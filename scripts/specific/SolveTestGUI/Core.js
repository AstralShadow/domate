/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker */

if (!window.SolveTestGUI) {
    window.SolveTestGUI = {}
}

window.SolveTestGUI.Core = function (oid) {
    "use strict"
    this.oid = oid

    const timer = new SolveTestGUI.Timer(this)
    const progress = new SolveTestGUI.Progress(this)
    const contentsProcessor = new SolveTestGUI.Tasks(this)
    document.getElementById("testUI").style.display = "block"
    document.getElementById("testContents").style.display = "block"

    /* Tracking */
    StateTracker.track("getExamData", {id: oid}, examDataHandler)
    var lastData = undefined
    function examDataHandler (e) {
        lastData = e.result
        console.log(e.result)
        e.result.tasks.forEach(trackTask)
    }
    var tasks = {}
    function trackTask (oid) {
        if (Object.keys(tasks).indexOf(oid) === -1) {
            tasks[oid] = undefined
            StateTracker.track("getExamQuestion", {id: oid}, taskUpdateHandler)
        }
    }
    function taskUpdateHandler (e) {
        tasks[e.args.id] = e.result
    }

    /* Modules events */
    this.onFinish = function () {
        contentsProcessor.disableInput()
    }

}
