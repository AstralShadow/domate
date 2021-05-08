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
        var req = await listActiveExams(test_id)

        while (mainContainer.firstChild)
            mainContainer.removeChild(mainContainer.firstChild)
        req.reverse().forEach(async function (id) {
            var exam = await getExamData(test_id, id)
            var div = document.createElement("div")
            div.className = "examListElement"
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
                window.TestsPageGUI.showExam(test_id, id)
            })
        })
        setTimeout(function () {
            mainContainer.style.display = "block"
        }, 20)
    }

    function listActiveExams (test_id) {
        return new Promise(function (resolve) {
            var request = new XMLHttpRequest()
            request.open("GET", "exam/" + test_id + "/active")
            request.send()

            request.addEventListener("load", function () {
                if (request.status !== 200) {
                    console.log("failed to fetch active exams list")
                    return;
                }
                var data = JSON.parse(request.response)
                resolve(data.data)
            })
        })
    }

    function getExamData (test_id, exam_id) {
        return new Promise(function (resolve) {
            var request = new XMLHttpRequest()
            request.open("GET", "exam/" + test_id + "/active/" + exam_id)
            request.send()

            request.addEventListener("load", function () {
                if (request.status !== 200) {
                    console.log("failed to fetch active exams list")
                    return;
                }
                var data = JSON.parse(request.response)
                resolve(data.data)
            })
        })
    }

    var tracked_active_exam = undefined;

    var trackedSolutions = {}
    var updateCommands = {}

    function downloadSolution (id) {
        var request = new XMLHttpRequest()
        request.open("GET", "solution/" + id)
        request.send()

        request.addEventListener("load", function () {
            if (request.status !== 200) {
                console.log("couldnt load solutions")
                return;
            }

            var data = JSON.parse(request.response).data

            trackedSolutions[id] = data
            if (updateCommands[id]) {
                updateCommands[id](data)
            }
        })
    }

    window.AppEventSource.then(function (source) {
        source.addEventListener("joined", function (e) {
            var input = JSON.parse(e.data)
            console.log("joined", input)

            if (input.active_exam === tracked_active_exam) {
                var id = input.solution
                trackedSolutions[id] = input.data
                if (updateCommands[id]) {
                    updateCommands[id](input.data)
                }
            }
        })
        source.addEventListener("marked", questionUpdate)
        source.addEventListener("answered", questionUpdate)
        function questionUpdate (e) {
            var input = JSON.parse(e.data)
            var s_id = input.solution
            var q_id = input.id
            var question = input.data
            question["_id"] = q_id
            trackedSolutions[s_id].tasks.forEach(function (task, i) {
                if (task._id === q_id) {
                    trackedSolutions[s_id].tasks[i] = question
                }
            })
            if (updateCommands[s_id]) {
                updateCommands[s_id](trackedSolutions[s_id])
            }

        }
    })

    function untrackAllSolutions () {
        trackedSolutions = {}
        updateCommands = {}
    }

    window.TestsPageGUI.showExam = async function (exam_id, active_exam_id) {
        var request = getExamData(exam_id, active_exam_id)
        tracked_active_exam = active_exam_id
        setTimeout(function () {
            examContainer.style.display = "block"
        }, 0)
        var container = examContainer.querySelector("tbody")
        while (container.firstChild)
            container.removeChild(container.firstChild)
        var exam = await request
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
            downloadSolution(s_oid)
        })


    }
    function showTaskMenu (s_oid, task_index) {
        var solution = trackedSolutions[s_oid]
        var task = solution.tasks[task_index]

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
                decide(s_oid, task["_id"], true)
                taskContainer.style.display = "none"
            }
        taskContainer.querySelector("#badAnswer")
            .onclick = async function () {
                await decide(s_oid, task["_id"], false)
                taskContainer.style.display = "none"
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

    async function decide (solution_id, task_id, isCorrect) {
        var token_request = new XMLHttpRequest()
        token_request.open("GET", "solution/get-token")
        token_request.send()
        var token = new Promise(function (resolve) {
            token_request.addEventListener("load", function () {
                if (token_request.status !== 200) {
                    console.log("couldnt accure token! maybe session expired")
                    return;
                }
                var data = JSON.parse(token_request.response)
                resolve(data.token)
            })
        })
        var input = {
            token: await token,
            "true": isCorrect
        }
        var request = new XMLHttpRequest()
        request.open("PUT", "solution/" + solution_id + "/" + task_id)
        request.setRequestHeader("Content-type", "application/json")
        request.send(JSON.stringify(input))
        request.addEventListener("load", function () {
            if (token_request.status !== 200) {
                console.log("couldnt set mark.")
                return;
            }
        })
    }
})
