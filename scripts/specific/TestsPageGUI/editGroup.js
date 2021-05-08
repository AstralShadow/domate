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
        type: "group",
        endpoint: "group",
        pageQuery: "#groupEditorPage",
        nameQuery: "#groupName",
        descriptionQuery: "#groupDescription",
        swidingDirection: "left",

        contentsQuery: "#groupContents",
        contentEndpoint: "exercise",
        noContentName: TestPageGUI.no_exercise_name,
        parseContentRealId: (e) => {
            return e
        },
        parseContentInListId: (e) => {
            return e
        },
        disableMove: true
    }

    function createEditor (oid) {
        var copyOptions = {}
        Object.assign(copyOptions, options)
        copyOptions.onclose = function () {
            if (TestPageGUI.lastFocusedTest) {
                TestsPageGUI.editTest(TestPageGUI.lastFocusedTest)
            } else {
                TestsPageGUI.showHelp("main")
            }
            window.TestsPageGUI.deactivateExercisesContainer()
        }
        TestPageGUI.lastFocusedGroup = oid
        return new TestsPageGUI.ContentListEditor(oid, copyOptions)
    }

    /* Initialization */
    TestsPageGUI.editGroup = async function (oid, callerTestId) {
        if (TestsPageGUI.activeEditor) {
            var type = TestsPageGUI.activeEditor.type
            var promise = TestsPageGUI.activeEditor.deactivate()
            if (type === "group") {
                await promise
            }
        }

        setTimeout(function () {
            TestsPageGUI.showHelp("groupEditor")
            TestsPageGUI.activeEditor = createEditor(oid, callerTestId)
            window.TestsPageGUI.activateExercisesContainer()
            window.ExetendedDimensionParser.parse()
        }, 0)
    }
})(window)
