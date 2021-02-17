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
    new TestsPageGUI.Container({
        /* Constants */
        type: 'group',
        containerQuery: "#exerciseGroupsContainer",
        listURL: 'listExerciseGroups',
        dataURL: 'exerciseGroupData',
        createURL: 'createExerciseGroup',
        removeURL: 'removeExerciseGroup',
        noName: TestsPageGUI.noGroupName,
        noDescription: TestsPageGUI.noGroupDescription,

        /* Functionality */
        functions: [
            ["img/icon_231x234.png",
                function (oid) {
                    console.log("edit grouop not used yet")
                    //TestsPageGUI.editTest(oid)
                }
            ],
            ["img/delete_in_domate_95x100.png",
                function (oid) {
                    console.log("remove grouop not used yet")
                    // TODO: use some more nice askbox
                    //if (confirm("You sure want to remove?"))
                    //this.remove(oid)
                }
            ],
            ["img/icon_231x234.png",
                function (oid) {
                    var active = TestsPageGUI.activeEditor
                    if (active && active.type === "test") {
                        active.addContent(oid)
                    }
                }
            ]
        ]
    }
    )
})()