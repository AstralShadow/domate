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

/* Test interface */
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

    if (!type || !dataURL || !modifyURL || !workspaceQuery) {
        throw ["Missing TestPageGUI.ContentListEditor option!", options]
    }

    /* Modifiers */


    /* Rendering */
    var workspace = document.querySelector(workspaceQuery)
    var sideboard = document.querySelector(sideboardQuery)
    var lastContents
    var nodes = {}
    function renderContents (data) {
        if (JSON.stringify(data.contents) === JSON.stringify(lastContents)) {
            return;
        }

    }

    options.contentsRenderer = renderContents
    TestsPageGUI.DefaultEditor.apply(this, [oid, options])
}
