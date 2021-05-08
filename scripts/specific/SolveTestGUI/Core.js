/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker */

if (!window.SolveTestGUI) {
    throw "SolveTestGUI aint initialized"
}

window.SolveTestGUI.Core = function (data) {
    "use strict"
    var self = this
    this.data = data
    console.log(data)
    this.solution = data.solution
    this.task_keys = data.solution.tasks

    const timer = new SolveTestGUI.Timer(this)
    const progress = new SolveTestGUI.Progress(this)
    const contentsProcessor = new SolveTestGUI.Tasks(this)
    document.getElementById("testUI").style.display = "block"
    document.getElementById("testContents").style.display = "block"

    this.getTask = function (oid) {
        return tasks[oid]
    }

    this.onFinish = function () {
        contentsProcessor.disableInput()
    }

    function onTaskLoad (oid) {
        progress.onTaskLoad(oid)
        contentsProcessor.onTaskLoad(oid)
    }

    this.onAnswer = function (oid) {
        var request = new XMLHttpRequest()
        request.open("GET", window.SolveTestGUI.endpoint + '/' + oid)
        request.send()

        request.addEventListener("load", function () {
            var data = JSON.parse(request.response);
            tasks[oid] = data.data
            onTaskLoad(oid)
        })
    }

    /* Tracking */
    var tasks = {}
    var loadedTasks = 0
    this.task_keys.forEach(function (oid) {
        tasks[oid] = null
        var request = new XMLHttpRequest()
        request.open("GET", window.SolveTestGUI.endpoint + '/' + oid)
        request.send()

        request.addEventListener("load", function () {
            var data = JSON.parse(request.response);
            tasks[oid] = data.data
            onTaskLoad(oid)
            if (++loadedTasks === self.task_keys.length) {
                contentsProcessor.onLoad()
            }
        })
    })


}
