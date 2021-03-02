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
        if (e === lastEventInTask)
            return;
        if (taskContainer.style.display !== "none") {
            taskContainer.style.display = "none"
            return;
        }
        if (e === lastEventInExam)
            return;
        if (examContainer.style.display !== "none") {
            examContainer.style.display = "none"
            return;
        }
        if (e === lastEventInMain)
            return;
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
            var request = await StateTracker.get("solutionData", {id: s_oid})
            var solution = request.result
            var row = container.insertRow()
            row.className = "tr"
            var name = row.insertCell()
            name.className = "td"
            name.innerText = solution.identification
            var start = row.insertCell()
            start.className = "td"
            start.innerText = (new Date(solution.created * 1000)).toLocaleString()
            var worktime = row.insertCell()
            worktime.className = "td"
            var delta = solution.finished - solution.created
            worktime.innerText = Math.floor(delta / 60) + " минут" + (delta === 1 ? "а" : "и")

            var tasks = row.insertCell()
            tasks.style.whiteSpace = "nowrap"
            tasks.style.textAlign = "center"
            var correctTasks = row.insertCell()
            correctTasks.className = "td"
            var nodes = {}
            Object.keys(solution.tasks).forEach(function (task_id) {
                var task = solution.tasks[task_id]
                console.log(task)
                var div = document.createElement("div")
                tasks.appendChild(div)
                div.className = "td"
                div.style.height = "20px"
                div.style.display = "inline-block"
                div.style.margin = "3px"
                div.style.marginTop = "5px"
                div.style.borderRadius = "10px"
                nodes[task_id] = div
            })
            parseCorrectness()
            function parseCorrectness () {
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
        })


    }
})
