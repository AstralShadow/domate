/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker */

if (!window.SolveTestGUI) {
    window.SolveTestGUI = {}
}

window.SolveTestGUI.Timer = function (core) {
    "use strict"

    var endTime = core.data.solution.finished
    const timer = document.querySelector("#mainTimer > .timerFontHalf")
    progressStartTimer()

    var interval = setInterval(progressStartTimer, 1000)
    function progressStartTimer () {
        var delta = endTime - (new Date()).getTime() / 1000
        var difference = Math.max(0, Math.floor(delta))
        var hours = "00", minutes = "00", seconds = "00"
        if (Math.abs(difference) >= 3600) {
            hours = Math.floor(difference / 3600)
        }
        if (Math.abs(difference) % 3600 >= 60) {
            minutes = Math.floor(difference / 60) % 60
        }
        if (difference % 60) {
            seconds = difference % 60
        }

        hours = String(hours).padStart(2, '0')
        minutes = String(minutes).padStart(2, '0')
        seconds = String(seconds).padStart(2, '0')
        timer.innerText = (delta < 0 ? "-" : "") + hours + ':' + minutes + ':' + seconds

        if (delta <= 60) {
            timer.style.color = "rgb(255, 200, 0)"
        }

        if (delta <= 1) {
            core.onFinish()
            if (window.readyFunc()) {
                clearInterval(interval)
            }
        }
    }
}
