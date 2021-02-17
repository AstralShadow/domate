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
        dataURL: "exerciseGroupData",
        modifyURL: "modifyExerciseGroup",
        pageQuery: "#groupEditorPage",
        nameQuery: "#groupName",
        descriptionQuery: "#groupDescription",
        contentsQuery: "#groupContents",
        elementDataURL: "exerciseData",
        noContentName: TestPageGUI.noGroupName,
        parseContentRealId: (e) => e,
        parseContentInListId: (e) => e
    }

    function createEditor (oid) {
        var copyOptions = {}
        Object.assign(copyOptions, options)
        copyOptions.onclose = function () {
            if (TestPageGUI.lastFocusedTest) {
                TestsPageGUI.editTest(TestPageGUI.lastFocusedTest)
            }
        }
        TestPageGUI.lastFocusedGroup = oid
        return new TestsPageGUI.ContentListEditor(oid, copyOptions)
    }

    /* Initialization */
    TestsPageGUI.editGroup = async function (oid, callerTestId) {
        if (TestsPageGUI.activeEditor) {
            await TestsPageGUI.activeEditor.deactivate()
        }

        setTimeout(function () {
            TestsPageGUI.activeEditor = createEditor(oid, callerTestId)
            window.ExetendedDimensionParser.parse()
        }, 0)
    }
})(window)
