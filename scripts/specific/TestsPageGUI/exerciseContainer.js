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
            ["img/m1.png:Редактирай",
                function (oid) {
                    TestsPageGUI.editExercise(oid)
                }
            ],
            ["img/delete.png:Изтрии",
                function (oid) {
                    // TODO: use some more nice askbox
                    if (confirm("You sure want to remove?"))
                        this.remove(oid)
                }
            ],
            ["img/plus1.png:Добави",
                function (oid) {
                    var active = TestsPageGUI.activeEditor
                    if (active && active.type === "group") {
                        active.addContent([oid])
                    }
                }
            ]
        ],
        //contextHint: "img/Firefox-OS-tan.png"
    })

    window.TestsPageGUI.activateExercisesContainer = function () {
        container.activate()
    }

    window.TestsPageGUI.deactivateExercisesContainer = function () {
        container.deactivate()
    }
})()