/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!window.TestsPageGUI) {
    TestsPageGUI = {}
}
if (!window.TestsPageGUI.ContentListEditor) {
    throw "TestsPageGUI.ContentListEditor is a dependency of TestPageGUI.editTest"
}

/**
 * Addes TestsPageGUI.editTest(oid) function
 * @param {type} window
 * @returns {undefined}
 */
(function (window) {
    'use strict'
    const TestPageGUI = window.TestsPageGUI
    const ContentListEditor = TestPageGUI.ContentListEditor

    var options = {
        type: "exercise",
        dataURL: "exerciseData",
        modifyURL: "modifyExercise",
        pageQuery: "#exerciseExitorPage",
        nameQuery: "#exerciseName",
        descriptionQuery: "#exerciseDescription",
        swidingDirection: "right",

        workspaceQuery: "#exerciseWorkspace",
        sideboardQuery: "#exerciseSideboard"
    }

    function createEditor (oid) {
        var copyOptions = {}
        Object.assign(copyOptions, options)
        copyOptions.onclose = function () {
            if (TestPageGUI.lastFocusedGroup) {
                TestsPageGUI.editGroup(TestPageGUI.lastFocusedGroup)
                TestsPageGUI.lastFocusedGroup.onclose = TestsPageGUI.lastFocusedGroup.oncloseMemory
            }
        }
        TestPageGUI.lastFocusedExercise = oid
        return new TestsPageGUI.ExerciseEditor(oid, copyOptions)
    }

    /* Initialization */
    TestsPageGUI.editExercise = async function (oid) {
        if (TestsPageGUI.activeEditor) {
            var type = TestsPageGUI.activeEditor.type
            if (type === "group") {
                TestsPageGUI.activeEditor.oncloseMemory = TestsPageGUI.activeEditor.onclose
                TestsPageGUI.activeEditor.onclose = null
            }
            var promise = TestsPageGUI.activeEditor.deactivate()
            if (type === "exercise") {
                await promise
            }
        }

        setTimeout(function () {
            TestsPageGUI.activeEditor = createEditor(oid)
            window.ExetendedDimensionParser.parse()
        }, 0)
    }
})(window)
