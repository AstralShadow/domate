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
        type: "test",
        endpoint: "exam",
        pageQuery: "#testEditorPage",
        nameQuery: "#testName",
        descriptionQuery: "#testDescription",
        swidingDirection: "right",

        contentsQuery: "#testContents",
        contentEndpoint: "group",
        noContentName: TestPageGUI.no_group_name,
        parseContentRealId: (e) => e.id,
        parseContentInListId: (e) => e.token,

        onclose: function () {
            window.TestsPageGUI.deactivateGroupsContainer()
            TestsPageGUI.showHelp("main")
        }
    }

    function createEditor (oid) {
        TestPageGUI.lastFocusedTest = oid
        return new TestsPageGUI.ContentListEditor(oid, options)
    }

    /* Initialization */
    TestsPageGUI.editTest = async function (oid) {
        if (TestsPageGUI.activeEditor) {
            await TestsPageGUI.activeEditor.deactivate()
        }

        setTimeout(function () {
            TestsPageGUI.showHelp("testEditor")
            TestsPageGUI.activeEditor = createEditor(oid)
            window.TestsPageGUI.activateGroupsContainer()
            window.ExetendedDimensionParser.parse()
        }, 0)
    }
})(window)
