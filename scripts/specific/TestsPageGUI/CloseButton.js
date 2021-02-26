/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker */

/* Dependencies */
if (!window.TestsPageGUI) {
    window.TestsPageGUI = {}
}

window.addEventListener("load", function () {
    document.querySelectorAll(".pageCloseButton")
        .forEach(function (button) {
            button.addEventListener("click", function () {
                TestsPageGUI.activeEditor.deactivate()
            })
        })
})
