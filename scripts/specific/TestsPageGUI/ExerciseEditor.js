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

TestsPageGUI.ExerciseEditor = function (oid, options) {
    'use strict'
    var self = this
    const TestsPageGUI = window.TestsPageGUI
    const SwidingBoard = window.SwidingBoard

    const type = options.type
    const dataURL = options.dataURL
    const modifyURL = options.modifyURL
    //const workspaceQuery = options.workspaceQuery
    const sideboardQuery = options.sideboardQuery
    const settingsQuery = options.settingsQuery
    const answerQuery = options.answerQuery
    const mathDisplayQuery = options.mathDisplayQuery
    const mathInputQuery = options.mathInputQuery

    if (!type || !dataURL || !modifyURL || !sideboardQuery || // !workspaceQuery ||
        !settingsQuery || !answerQuery || !mathDisplayQuery || !mathInputQuery) {
        throw ["Missing TestPageGUI.ContentListEditor option!", options]
    }

    /* onUpdate */
    var lastData
    function onUpdate (data) {
        lastData = data
        fillInputs(data)
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
    this.setAnswer = async function (answer, useAnswer) {
        var query = {
            id: oid
        }
        if (lastData.answer !== answer && answer !== undefined) {
            query.answer = answer
        }
        if (lastData.useAnswer !== useAnswer && useAnswer !== undefined) {
            query.useAnswer = useAnswer
        }
        if (Object.keys(query).length === 1) {
            return;
        }
        var result = await StateTracker.get(modifyURL, query)
        StateTracker.reloadTracker(dataURL, {id: oid})
    }


    /* Rendering */
    // var workspace = document.querySelector(workspaceQuery)
    var sideboard = document.querySelector(sideboardQuery)

    var settingsContainer = document.querySelector(settingsQuery)
    var answerContainer = document.querySelector(answerQuery)
    var answerCheckbox = answerContainer.parentElement.querySelector("input[type=checkbox]")

    var mathDisplay = document.querySelector(mathDisplayQuery)
    var mathInput = document.querySelector(mathInputQuery)


    function fillInputs (data) {
        answerContainer.innerText = data.answer || ""
        answerCheckbox.checked = Boolean(data.useAnswer)
        mathInput.innerText = data.question || ""
        renderMath()
        showHideAnswer();
    }

    mathInput.addEventListener("input", renderMath)
    mathInput.addEventListener("blur", function () {
        if (TestsPageGUI.activeEditor === self) {
            self.setQuestion(this.innerText)
        }
    })
    answerContainer.addEventListener("blur", function () {
        if (TestsPageGUI.activeEditor === self) {
            self.setAnswer(answerContainer.innerText, answerCheckbox.checked)
        }
    })
    answerCheckbox.addEventListener("input", function () {
        if (TestsPageGUI.activeEditor === self) {
            self.setAnswer(answerContainer.innerText, answerCheckbox.checked)
            showHideAnswer();
        }
    })

    function renderMath () {
        var math = mathInput.innerText
        mathDisplay.innerText = math
        if (MathJax.typeset) {
            MathJax.typeset()
        }
    }
    function showHideAnswer () {
        if (TestsPageGUI.activeEditor !== self) {
            return;
        }
        if (answerCheckbox.checked) {
            answerContainer.parentNode.className = "exerciseConfig on"
        } else {
            answerContainer.parentNode.className = "exerciseConfig off"
        }
        window.ExetendedDimensionParser.parse()
    }

    this.renderMath = renderMath

    /* Core initialization */
    TestsPageGUI.DefaultEditor.apply(this, [oid, options])
}
