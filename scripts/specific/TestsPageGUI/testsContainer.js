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
    var startTestDiv, showStartTestDiv, hideStartTestDiv
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
                    //console.log("use not used yet")
                    // *use* function
                    showStartTestDiv(oid)
                }
            ]
        ],
        oncreate: TestsPageGUI.editTest
    })
    container.activate()

    window.addEventListener("load", function () {
        var lastClickEvent
        startTestDiv = document.querySelector("#startTest")
        startTestDiv.addEventListener("mousedown", function (e) {
            lastClickEvent = e
        })
        document.addEventListener("mousedown", function (e) {
            if (lastClickEvent !== e) {
                hideStartTestDiv()
            }
        })
        showStartTestDiv = function (oid) {
            startTestDiv.style.display = "block"
            fill(oid)
        }
        hideStartTestDiv = function () {
            startTestDiv.style.display = "none"
        }
        var data = null
        function fill (oid) {
            data = container.get(oid)
            if (data.name) {
                document.getElementById("ST_name")
                    .innerText = data.name
            } else {
                document.getElementById("ST_name")
                    .innerHTML = TestsPageGUI.noTestName
            }
            document.getElementById("ST_description")
                .innerText = data.description
            document.getElementById("ST_tasks")
                .innerText = data.contents.length
        }
        document.getElementById("ST_button")
            .addEventListener("click", async function () {
                var query = await StateTracker.get("scheduleTest", {
                    id: data["_id"],

                })
                document.getElementById("ST_feedback").innerHTML = query.result
                console.log(query)
            })
    })
})()
