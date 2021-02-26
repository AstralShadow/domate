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

TestsPageGUI.showHelp = function (type) {
    const self = window.TestsPageGUI
    const texts = self.instructions
    const container = document.querySelector("#header > .alignedTextContainer")
    const title = container.querySelector(".topic")
    const contents = container.querySelector("#description")
    console.log(contents, title)
    if (texts[type] !== undefined) {
        title.innerHTML = texts[type][0]
        contents.innerHTML = texts[type][1]
    }
}

window.addEventListener("load", function () {
    TestsPageGUI.showHelp("main")
})
