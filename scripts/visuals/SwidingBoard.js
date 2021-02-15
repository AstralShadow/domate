/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Allows sliding from side of screen of given element.
 * The given direction will be between 2 and -element.offsetWidth
 * This makes the element behave as css {
 *  position: fixed;
 *  display: block;
 * }
 * @constructor
 * @param {DOMElement} element
 * @param {"right"|"left"} direction
 * @returns {SwidingBoard}
 */
function SwidingBoard (element, direction) {
    'use strict'
    var self = this
    var hidden = false
    var visible = 1
    var animationStart = -1
    var animationDuration = 0
    direction = direction === "right" ? "right" : "left"

    init()

    function init () {
        element.style.position = "fixed"
        console.log(element.style.display)
        if (window.getComputedStyle(element).display === "none") {
            hidden = true
            visible = 0
        }
        window.requestAnimationFrame(animation)
    }

    this.show = function (delay) {
        delay = Math.abs(delay || 0)
        if (hidden) {
            hidden = false
            animationStart = (new Date()).getTime() - delay * visible
            animationDuration = delay * (1 - visible)
            element.style.display = "block"
        }
    }

    this.hide = function (delay) {
        delay = Math.abs(delay || 0)
        if (!hidden) {
            hidden = true
            animationStart = (new Date()).getTime() - delay * (1 - visible)
            animationDuration = delay * visible
        }
    }

    function animation () {
        window.requestAnimationFrame(animation)
        if (animationStart === -1) {
            return;
        }

        var now = (new Date()).getTime()

        if (animationDuration) {
            if (hidden) {
                visible = 1 - (now - animationStart) / animationDuration
            } else {
                visible = (now - animationStart) / animationDuration
            }
        } else {
            visible = !hidden
        }

        if (visible > 1 || visible < 0) {
            visible = visible >= 1
            animationStart = -1
            if (visible) {
                element.style[direction] = "2px"
            } else {
                element.style.display = "none"
            }
        } else {
            var p = visible
            var progress = Math.sin(p * Math.PI / 2) ** 3
            var distance = (progress - 1) * window.innerWidth
            element.style[direction] = distance + "px"
        }
    }

}
