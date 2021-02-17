/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* Constants and dependencies */
/* global StateTracker */

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
 * Addes TestsPageGUI.editTest(oid) function
 * @param {type} window
 * @returns {undefined}
 */
(function (window) {
    'use strict'
    const TestsPageGUI = window.TestsPageGUI
    const SwidingBoard = window.SwidingBoard
    var activeEditor

    var container = document.getElementById("testEditorPage")
    var nameInput = document.getElementById("testName")
    var descriptionInput = document.getElementById("testDescription")
    var contentsInput = document.getElementById("testContents")

    var swider = new SwidingBoard(container, TestsPageGUI.swidingDirection)
    var show = () => swider.show(TestsPageGUI.animationSpeed)
    var hide = () => swider.hide(TestsPageGUI.animationSpeed)

    var selfClick = null
    container.addEventListener("click", function (e) {
        selfClick = e
    })

    document.addEventListener("click", function (e) {
        if (activeEditor && selfClick !== e) {
            activeEditor.deactivate()
        }
    })

    nameInput.addEventListener("blur", function () {
        if (activeEditor) {
            activeEditor.setName(nameInput.innerText)
        }
    })
    descriptionInput.addEventListener("blur", function () {
        if (activeEditor) {
            activeEditor.setDescription(descriptionInput.innerText)
        }
    })


    /* Test interface */
    function TestEditor (oid) {
        'use strict'
        activeEditor = this
        show()

        var lastData = {}
        StateTracker.track('testData', {id: oid}, updateHandler)
        function update () {
            StateTracker.reloadTracker('testData', {id: oid})
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

        this.setName = async function (name) {
            if (lastData.name === name) {
                return;
            }
            var query = {
                id: oid,
                name: name
            }
            var result = await StateTracker.get('modifyTest', query)
            StateTracker.reloadTracker('testData', {id: oid})
        }

        this.setDescription = async function (description) {
            if (lastData.description === description) {
                return;
            }
            var query = {
                id: oid,
                description: description
            }
            var result = await StateTracker.get('modifyTest', query)
            StateTracker.reloadTracker('testData', {id: oid})
        }

        this.deactivate = function () {
            StateTracker.untrack('testData', {id: oid}, updateHandler)
            activeEditor = null

            var animationPromise = new Promise(function (resolve) {
                hide()
                setTimeout(function () {
                    resolve()
                }, TestsPageGUI.animationSpeed)
            })
            return animationPromise
        }

        this.addContent = async function (c_oid) {
            console.log("Add content " + c_oid)
            var object = {}
            object[c_oid] = 1
            var query = {
                id: oid,
                addExerciseGroups: object
            }
            var result = await StateTracker.get('modifyTest', query)
            console.log(result)
            StateTracker.reloadTracker('testData', {id: oid})
        }
        this.removeContent = async function (c_oid) {
            console.log("Remove content " + c_oid)
            var query = {
                id: oid,
                addExerciseGroups: [c_oid]
            }
            var result = await StateTracker.get('modifyTest', query)
            console.log(result)
            StateTracker.reloadTracker('testData', {id: oid})
        }

        function renderContents () {
            console.log("contents: ", lastData)
        }

    }

    /* Initialization */
    TestsPageGUI.editTest = async function (oid) {
        if (activeEditor) {
            await activeEditor.deactivate()
        }

        setTimeout(function () {
            activeEditor = new TestEditor(oid)
            window.ExetendedDimensionParser.parse()
        }, 0)
    }

    Object.defineProperty(TestsPageGUI, "activeTestEditor", {
        get: function () {
            return activeEditor
        }
    })

})(window)
