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

    new SolveTestGUI.Timer(this)
    new SolveTestGUI.Progress(this)
    document.getElementById("testUI").style.display = "block"

    /* Tracking */
    StateTracker.track("getExamData", {id: oid}, examDataHandler)
    var lastData = undefined
    function examDataHandler (e) {
        lastData = e.result
        console.log(e.result)
    }

    this.onNearFinish = function () {

    }
    this.onFinish = function () {

    }

}
