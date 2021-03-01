/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker */

if (!window.SolveTestGUI) {
    window.SolveTestGUI = {}
}

window.SolveTestGUI.Progress = function (core) {
    "use strict"

    /* Tracking */
    StateTracker.track("getExamData", {id: core.oid}, examDataHandler)
    var lastData = undefined
    var tasks = []
    function examDataHandler (e) {
        lastData = e.result
        tasks = e.result.tasks
    }

    var container = document.querySelector("progressDeisplay")
    function clear () {
        while (container.firstChild) {
            container.removeChild(container.firstChild)
        }
    }

    var elements = {}
    function add (oid, color) {
        if (elements[oid] === undefined) {
            var circle = document.createElement("div")
            circle.className = "circle"
            circle.style.backgroundColor = color || "black"
            circle.addEventListener("click", function () {
                scrollTo(oid)
            })
            elements[oid] = circle
        }
        container.appendChild(circle)
    }
    function scrollTo (oid) {
        console.log("implement scroll", oid)
    }

}
