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

    var container = document.querySelector("#progressDisplay")
    var nodes = {}

    core.task_keys.forEach(function (oid) {
        set(oid, false)
    })
    container.style.display = "block"

    this.onTaskLoad = function (key) {
        var task = core.getTask(key)
        var solved = task.answer && task.answer.trim().length
        set(key, solved)
    }

    /* Display */

    function clear () {
        while (container.firstChild) {
            container.removeChild(container.firstChild)
        }
        core.task_keys.forEach(function (oid) {
            if (nodes[oid]) {
                container.appendChild(nodes[oid])
            }
        })
    }

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
