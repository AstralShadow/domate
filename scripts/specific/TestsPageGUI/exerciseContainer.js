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
        type: 'exercise',
        containerQuery: "#exercisesContainer",
        listURL: 'listExercises',
        dataURL: 'exerciseData',
        createURL: 'createExercise',
        removeURL: 'removeExercise',
        noName: TestsPageGUI.noExerciseName,
        noDescription: TestsPageGUI.noExerciseDescription,

        /* Functionality */
        functions: [
            ["img/icon_231x234.png",
                function (oid) {
                    TestsPageGUI.editExercise(oid)
                }
            ],
            ["img/delete_in_domate_95x100.png",
                function (oid) {
                    // TODO: use some more nice askbox
                    if (confirm("You sure want to remove?"))
                        this.remove(oid)
                }
            ],
            ["img/icon_231x234.png",
                function (oid) {
                    var active = TestsPageGUI.activeEditor
                    if (active && active.type === "group") {
                        active.addContent([oid])
                    }
                }
            ]
        ]
    }
    )
})()