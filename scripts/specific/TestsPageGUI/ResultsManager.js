/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker */

if (window.TestsPageGUI === undefined) {
    window.TestsPageGUI = {}
}

window.addEventListener("load", function () {
    "use strict"
    var mainContainer = document.querySelector("#examListContainer")
    var examContainer = document.querySelector("#singleExamResults")
    var taskContainer = document.querySelector("#taskCheck")

    var lastEventInMain
    var lastEventInExam
    var lastEventInTask
    mainContainer.addEventListener("click", function (e) {
        lastEventInMain = e
    })
    examContainer.addEventListener("click", function (e) {
        lastEventInExam = e
    })
    taskContainer.addEventListener("click", function (e) {
        lastEventInTask = e
    })
    document.addEventListener("click", function (e) {
        if (e === lastEventInTask) {
            return;
        }
        if (taskContainer.style.display !== "none") {
            taskContainer.style.display = "none"
            return;
        }
        if (e === lastEventInExam) {
            return;
        }
        if (examContainer.style.display !== "none") {
            examContainer.style.display = "none"
            untrackAllSolutions()
            return;
        }
        if (e === lastEventInMain) {
            return;
        }
        if (mainContainer.style.display !== "none") {
            mainContainer.style.display = "none"
            return;
        }
    })

    window.TestsPageGUI.showAllExams = async function (test_id) {
        var req = await StateTracker.get("listExams", {id: test_id})

        while (mainContainer.firstChild)
            mainContainer.removeChild(mainContainer.firstChild)
        req.result.reverse().forEach(async function (id) {
            var result = await StateTracker.get("examData", {id: id})
            var div = document.createElement("div")
            div.className = "examListElement"
            var exam = result.result
            div.innerHTML = "Начало: " + (new Date(exam.start * 1000)).toLocaleString()
            div.innerHTML += "<br />";
            div.innerHTML += "Край: " + (new Date(exam.end * 1000)).toLocaleString()
            div.innerHTML += "<br />";
            var minutes = exam.worktime
            div.innerHTML += "Време за работа: " + minutes + " минут" + (minutes === 1 ? "а" : "и")
            div.innerHTML += "<br />";
            div.innerHTML += "Участници: " + exam.solutions.length
            if (exam.note) {
                div.innerHTML += "<br />Бележка:<br />";
                div.append(exam.note)
            }
            mainContainer.appendChild(div)
            div.addEventListener("click", function () {
                window.TestsPageGUI.showExam(id)
            })
        })
        setTimeout(function () {
            mainContainer.style.display = "block"
        }, 20)
    }

    var trackedSolutions = {}
    var updateCommands = {}
    function trackSolution (oid) {
        if (Object.keys(trackedSolutions).indexOf(oid) === -1) {
            trackedSolutions[oid] = null
            StateTracker.track("solutionData", {id: oid}, solutionUpdate)
        }
    }
    function reloadSolution (oid) {
        StateTracker.reloadTracker("solutionData", {id: oid})
    }
    function solutionUpdate (e) {
        trackedSolutions[e.args.id] = e.result
        if (updateCommands[e.args.id]) {
            updateCommands[e.args.id](e.result)
        }
    }
    function untrackAllSolutions () {
        Object.keys(trackedSolutions).forEach(function (oid) {
            StateTracker.untrack("solutionData", {id: oid})
            delete trackedSolutions[oid]
        })
    }
    window.TestsPageGUI.showExam = async function (exam_id) {
        var request = StateTracker.get("examData", {id: exam_id})
        setTimeout(function () {
            examContainer.style.display = "block"
        }, 0)
        var container = examContainer.querySelector("tbody")
        while (container.firstChild)
            container.removeChild(container.firstChild)
        var result = await request
        var exam = result.result
        examContainer.querySelector("#identificationQuestion")
            .innerText = exam.question || ""
        exam.solutions.forEach(async function (s_oid) {
            //var solution = trackedSolutions[s_oid]
            var row = container.insertRow()
            row.className = "tr"
            var name = row.insertCell()
            name.className = "td"
            var start = row.insertCell()
            start.className = "td"
            var worktime = row.insertCell()
            worktime.className = "td"
            var tasks = row.insertCell()
            tasks.style.whiteSpace = "nowrap"
            tasks.style.textAlign = "center"
            var correctTasks = row.insertCell()
            correctTasks.className = "td"
            var nodes = {}

            updateCommands[s_oid] = function (solution) {
                name.innerText = solution.identification
                start.innerText = (new Date(solution.created * 1000)).toLocaleString()
                var delta = solution.finished - solution.created
                worktime.innerText = Math.floor(delta / 60) + " минут" + (delta === 1 ? "а" : "и")
                while (tasks.firstChild) {
                    tasks.removeChild(tasks.firstChild)
                }
                Object.keys(solution.tasks).forEach(function (task_id) {
                    var task = solution.tasks[task_id]
                    var div = document.createElement("div")
                    tasks.appendChild(div)
                    div.className = "td td_cell"
                    nodes[task_id] = div
                    div.onclick = function () {
                        showTaskMenu(s_oid, task_id)
                    }
                })

                var right = 0
                Object.keys(solution.tasks).forEach(function (task_id) {
                    var task = solution.tasks[task_id]
                    var div = nodes[task_id]
                    div.style.backgroundColor = task.color
                    if (task.isCorrect)
                        right += task.isCorrect
                })
                correctTasks.innerText = right + "/" + Object.keys(solution.tasks).length
            }
            trackSolution(s_oid)
        })


    }
    function showTaskMenu (s_oid, task_index) {
        var solution = trackedSolutions[s_oid]
        var task = solution.tasks[task_index]
        var reload = () => reloadSolution(s_oid)
        setTimeout(function () {
            taskContainer.style.display = "block"
        }, 0)
        taskContainer.querySelector("#studentIdentification")
            .innerText = solution.identification
        taskContainer.querySelector("#taskQuestion")
            .innerText = task.question
        var ansT = ""
        if (task.answerTime) {
            ansT = " (" + (new Date(task.answerTime * 1000)).toLocaleString() + ")"
        }
        taskContainer.querySelector("#studentAnswer")
            .innerHTML = "Отговор" + ansT + ":<hr />"
        taskContainer.querySelector("#studentAnswer")
            .append(task.answer || "")
        taskContainer.querySelector("#goodAnswer")
            .onclick = async function () {
                decide(task["_id"], true)
                taskContainer.style.display = "none"
                reload()
            }
        taskContainer.querySelector("#badAnswer")
            .onclick = async function () {
                await decide(task["_id"], false)
                taskContainer.style.display = "none"
                reload()
            }
        if (task.correctMarker === true) {
            taskContainer.querySelector("#goodAnswer")
                .className = "button button_green marked"
            taskContainer.querySelector("#badAnswer")
                .className = "button button_red"
        } else if (task.correctMarker === false) {
            taskContainer.querySelector("#goodAnswer")
                .className = "button button_green"
            taskContainer.querySelector("#badAnswer")
                .className = "button button_red marked"
        } else {
            taskContainer.querySelector("#goodAnswer")
                .className = "button button_green"
            taskContainer.querySelector("#badAnswer")
                .className = "button button_red"
        }
    }
    async function decide (task_id, isCorrect) {
        console.log(await StateTracker.get("submitTaskCheck", {id: task_id, "true": isCorrect}))

    }
})
