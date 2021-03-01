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
    function examDataHandler (e) {
        lastData = e.result
        e.result.tasks.forEach(function (oid) {
            set(oid, false)
        })
        e.result.tasks.forEach(trackTask)
        container.style.display = "block"
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
        var solved = e.result.answer && e.result.answer.trim().length
        set(e.args.id, solved)
    }

    var container = document.querySelector("#progressDisplay")
    function clear () {
        while (container.firstChild) {
            container.removeChild(container.firstChild)
        }
        lastData.tasks.forEach(function (oid) {
            if (nodes[oid]) {
                container.appendChild(nodes[oid])
            }
        })
    }

    var nodes = {}
    function set (oid, solved) {
        if (nodes[oid] === undefined) {
            var circle = document.createElement("div")
            circle.className = "circle"
            circle.addEventListener("click", function () {
                scrollTo(oid)
            })
            nodes[oid] = circle
            clear()
        }
        if (!solved) {
            nodes[oid].style.backgroundImage = ""
        } else {
            nodes[oid].style.backgroundImage = 'url("img/tick.png")'
        }
    }
    function scrollTo (oid) {
        console.log("implement scroll", oid)
    }

}
