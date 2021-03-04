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
            ["img/m1.png:Редактирай",
                function (oid) {
                    TestsPageGUI.editGroup(oid)
                }
            ],
            ["img/delete.png:Изтрии",
                function (oid) {
                    // TODO: use some more nice askbox
                    var div = document.getElementById("delNotification")
                    var del = document.getElementById("delButton")
                    var nodel = document.getElementById("noDelButton")
                    var self = this
                    div.style.display = "block"

                    del.onclick = function () {
                        self.remove(oid)
                        div.style.display = "none"
                    }
                    nodel.onclick = function () {
                        div.style.display = "none"
                    }
                }
            ],
            ["img/plus1.png:Добави",
                function (oid) {
                    var active = TestsPageGUI.activeEditor
                    if (active && active.type === "test") {
                        var object = {}
                        object[oid] = 1
                        active.addContent(object)
                    }
                }
            ]
        ],
        //contextHint: "img/Firefox-OS-tan.png"
    })

    window.TestsPageGUI.activateGroupsContainer = function () {
        container.activate()
    }

    window.TestsPageGUI.deactivateGroupsContainer = function () {
        container.deactivate()
    }
})()