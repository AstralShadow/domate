/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker, logDownloadTestData, logCreateCommands, noTestDescription, noTestName, FancyContextMenu */

/* Dependencies and constants */
if (!window.TestsPageGUI) {
    TestsPageGUI = {}
}
if (!window.TestsPageGUI.Container) {
    throw "TestsPageGUI.Container is a dependency of displaying groups"
}

(function () {
    'use strict'
    var container = new TestsPageGUI.Container({
        /* Constants */
        type: 'test',
        containerQuery: "#testsContainer",
        listURL: 'listTests',
        dataURL: 'testData',
        createURL: 'createTest',
        removeURL: 'removeTest',
        noName: TestsPageGUI.noTestName,
        noDescription: TestsPageGUI.noTestDescription,

        /* Functionality */
        functions: [
            ["img/icon_231x234.png",
                function (oid) {
                    TestsPageGUI.editTest(oid)
                }],
            ["img/delete_in_domate_95x100.png",
                function (oid) {
                    // TODO: use some more nice askbox
                    if (confirm("Are you sure you\nwant to remove this?"))
                        this.remove(oid)
                }
            ],
            ["img/icon_231x234.png",
                function (oid) {
                    console.log("use not used yet")
                    // *use* function
                }
            ]
        ],
        oncreate: TestsPageGUI.editTest
    })

    container.activate()
})()
