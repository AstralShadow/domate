/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/* global StateTracker */

if (!window.TestsPageGUI) {
    TestsPageGUI = {}
}
if (!window.TestsPageGUI.DefaultEditor) {
    throw "TestsPageGUI.DefaultEditor is a dependency of TestPageGUI.ContentListEditor"
}

(function () {
    'use strict'
    var input = document.querySelector('#exerciseInput')
    var display = document.querySelector('#a')

    function render () {
        var a = input.innerText
        while (a.indexOf('<') !== - 1)
            a = a.replace('<', '&lt;')
        display.innerText = a
        if (MathJax.typeset) {
            MathJax.typeset()
        }
    }

    input.addEventListener('input', function () {
        render();
    })
    render();
})();

(function () {
    var shortcuts = [
        'sqrt(x)',
        'root(x)(y)',
        'sum_(i=1)^n i^y',
        'int_i^a(b)',
        'log_i^a(b)'
    ]

    TestsPageGUI.ExerciseEditor = function (oid, options) {
        'use strict'
        var self = this
        const TestsPageGUI = window.TestsPageGUI
        const SwidingBoard = window.SwidingBoard

        const type = options.type
        const dataURL = options.dataURL
        const modifyURL = options.modifyURL
        const workspaceQuery = options.workspaceQuery
        const sideboardQuery = options.sideboardQuery
        const settingsQuery = options.settingsQuery
        const answerQuery = options.answerQuery

        if (!type || !dataURL || !modifyURL || !workspaceQuery) {
            throw ["Missing TestPageGUI.ContentListEditor option!", options]
        }

        /* onUpdate */
        var lastData
        function onUpdate (data) {
            lastData = data

        }
        options.contentsRenderer = onUpdate

        /* Modifiers */
        this.setQuestion = async function (question) {
            if (lastData.question === question) {
                return;
            }
            var query = {
                id: oid,
                question: question
            }
            var result = await StateTracker.get(modifyURL, query)
            StateTracker.reloadTracker(dataURL, {id: oid})
        }


        /* Rendering */
        var workspace = document.querySelector(workspaceQuery)
        var sideboard = document.querySelector(sideboardQuery)

        var settingsContainer = document.querySelector(settingsQuery)
        var answerContainer = document.querySelector(answerQuery)

        answerContainer.addEventListener("blur", function () {
            if (TestsPageGUI.activeEditor === self) {
                TestsPageGUI.activeEditor.setAnswer(this.innerText)
            }
        })

        /* Core initialization */
        TestsPageGUI.DefaultEditor.apply(this, [oid, options])
    }

})();