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
    throw "TestsPageGUI.Container is a dependency for displaying groups"
}

(function () {
    'use strict'
    new TestsPageGUI.Container({
        /* Constants */
        type: 'test',
        containerQuery: "#testsContainer",
        listURL: 'listTests',
        dataURL: 'testData',
        createURL: 'createTest',
        noName: TestsPageGUI.noTestName,
        noDescription: TestsPageGUI.noTestDescription,

        /* Functionality */
        edit: TestsPageGUI.editTest,
        use: undefined,
        editIcon: "img/icon_231x234.png",
        removeIcon: "img/icon_231x234.png",
        useIcon: "img/icon_231x234.png"
    })
})()
