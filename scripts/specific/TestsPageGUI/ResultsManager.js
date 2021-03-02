/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (window.TestsPageGUI === undefined) {
    window.TestsPageGUI = {}
}

window.addEventListener("load", function () {
    var mainContainer = document.querySelector("#examListContainer")
    var examContainer = document.querySelector("#singleExamResults")
    var taskContainer = document.querySelector("#d")

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
        if (taskContainer.style.display !== "none" && e !== lastEventInTask)
            taskContainer.style.display = "none"
        else if (examContainer.style.display !== "none" && e !== lastEventInExam)
            examContainer.style.display = "none"
        else if (mainContainer.style.display !== "none" && e !== lastEventInMain)
            mainContainer.style.display = "none"
    })

    window.TestsPageGUI.showAllExams = function (test_id) {
        mainContainer.style.display = "block"
    }
})
