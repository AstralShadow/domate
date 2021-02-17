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
if (!window.SwidingBoard) {
    throw "SwidingBoard is a dependency of TestsPageGUI"
}
if (!window.StateTracker) {
    throw "StateTracker is a dependency of TestsPageGUI"
}

/**
 * Handles similar parts between editors
 * @param {type} oid
 * @param {type} options
 * @returns {TestsPageGUI.DefaultEditor}
 */
TestsPageGUI.DefaultEditor = function (oid, options) {
    'use strict'
    var self = this
    const TestsPageGUI = window.TestsPageGUI
    const SwidingBoard = window.SwidingBoard

    const type = options.type
    const dataURL = options.dataURL
    const modifyURL = options.modifyURL
    const pageQuery = options.pageQuery
    const nameQuery = options.nameQuery
    const descriptionQuery = options.descriptionQuery
    const contentsRenderer = options.contentsRenderer
    const onclose = options.onclose

    if (!type || !dataURL || !modifyURL || !contentsRenderer) {
        throw ["Missing TestPageGUI.Container option!", options]
    }
    if (!pageQuery || !nameQuery || !descriptionQuery) {
        throw ["Missing TestPageGUI.Container option: query!", options]
    }

    /* Init */
    TestsPageGUI.activeEditor = this
    var container = document.querySelector(pageQuery)
    var nameInput = document.querySelector(nameQuery)
    var descriptionInput = document.querySelector(descriptionQuery)
    Object.defineProperty(this, "type", {
        get: function () {
            return type
        }
    })

    /* Tracking */
    var lastData = {}
    StateTracker.track(dataURL, {id: oid}, updateHandler)
    function update () {
        StateTracker.reloadTracker(dataURL, {id: oid})
    }
    function updateHandler (e) {
        if (e.code !== "Success") {
            // TODO: implement some kind of feedback
            console.log("Failed to load object", oid)
            return;
        }
        var object = e.result
        nameInput.innerText = object.name
        descriptionInput.innerText = object.description
        lastData = object
        renderContents()
    }

    /* Modifiers */
    this.setName = async function (name) {
        if (lastData.name === name) {
            return;
        }
        var query = {
            id: oid,
            name: name
        }
        var result = await StateTracker.get(modifyURL, query)
        StateTracker.reloadTracker(dataURL, {id: oid})
    }
    this.setDescription = async function (description) {
        if (lastData.description === description) {
            return;
        }
        var query = {
            id: oid,
            description: description
        }
        var result = await StateTracker.get(modifyURL, query)
        StateTracker.reloadTracker(dataURL, {id: oid})
    }

    /* Interface */
    var selfClick = null
    container.addEventListener("click", function (e) {
        selfClick = e
    })

    document.addEventListener("click", function (e) {
        if (TestsPageGUI.activeEditor === self && selfClick !== e) {
            TestsPageGUI.activeEditor.deactivate()
        }
    })
    nameInput.addEventListener("blur", function () {
        if (TestsPageGUI.activeEditor === self) {
            TestsPageGUI.activeEditor.setName(this.innerText)
        }
    })
    descriptionInput.addEventListener("blur", function () {
        if (TestsPageGUI.activeEditor === self) {
            TestsPageGUI.activeEditor.setDescription(this.innerText)
        }
    })

    /* Rendering */
    var swider = new SwidingBoard(container, TestsPageGUI.swidingDirection)
    var show = () => swider.show(TestsPageGUI.animationSpeed)
    var hide = () => swider.hide(TestsPageGUI.animationSpeed)
    show()
    function renderContents () {
        contentsRenderer(lastData)
    }


    this.deactivate = function () {
        StateTracker.untrack(dataURL, {id: oid}, updateHandler)
        TestsPageGUI.activeEditor = null

        var animationPromise = new Promise(function (resolve) {
            hide()
            setTimeout(function () {
                if (onclose) {
                    onclose()
                }
                resolve()
            }, TestsPageGUI.animationSpeed)
        })
        return animationPromise
    }
}
