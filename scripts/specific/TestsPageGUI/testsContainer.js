/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker, logDownloadTestData, logCreateCommands, noTestDescription, noTestName */

/* Dependencies and constants */
if (!window.TestsPageGUI) {
    window.TestsPageGUI = {}
}
if (!window.StateTracker) {
    console.throw("StateTracker is a dependency of TestsPageGUI")
}

/**
 * Updates and renders list of tests.
 * @param {type} window
 * @returns {undefined}
 */
(function (window) {
    'use strict'
    const TestsPageGUI = window.TestsPageGUI
    const logDownloadTestData = TestsPageGUI.logDownloadTestData || false
    const logCreateCommands = TestsPageGUI.logCreateCommands || false
    const noTestName = TestsPageGUI.noTestName || ""
    const noTestDescription = TestsPageGUI.noTestDescription || ""

    var testsContainer = document.querySelector("#testsContainer")
    var newTestButton = document.createElement("div")
    newTestButton.className = "newElementButton"

    /**
     * Data from server for tests, indexed by oid
     * @type object
     */
    const trackedTests = {}
    /**
     * Objects of references, indexed by oid. Contains
     * {cell, name, description}
     * @type object
     */
    const testNodes = {}

    /* Tracking */
    StateTracker.track("listTests", null, function (event) {
        event.result.forEach(trackTest)
        renderTests(event.result)
    })
    StateTracker.reloadTracker('listTests')

    function trackTest (oid) {
        if (Object.keys(trackedTests).indexOf(oid) === -1) {
            StateTracker.track('testData', {id: oid}, handleTestUpdate)
            trackedTests[oid] = null
        }
        StateTracker.reloadTracker('testData', {id: oid})
    }
    function untrackTest (oid) {
        StateTracker.untrack('testData', {id: oid}, handleTestUpdate)
    }
    function handleTestUpdate (e) {
        var test = e.result
        trackedTests[test._id] = test
        if (logDownloadTestData) {
            console.log("test", test._id, test)
        }
        if (testNodes[test._id]) {
            updateTestCell(test._id)
        } else {
            renderTests()
        }
    }

    /* Rendering */
    var lastTestOrder = []
    function renderTests (oids) {
        var list = oids || lastTestOrder
        lastTestOrder = list

        while (testsContainer.firstChild) {
            testsContainer.removeChild(testsContainer.firstChild)
        }

        list.forEach(function (oid) {
            if (testNodes[oid] === undefined) {
                testNodes[oid] = createTestCell()
                testNodes[oid].cell.addEventListener("click", function (e) {
                    return cellClickHandler(e, oid)
                })
            }
            updateTestCell(oid)
            testsContainer.appendChild(testNodes[oid].cell)
        })
        testsContainer.appendChild(newTestButton)
    }
    function createTestCell () {
        var parentNode = document.createElement("div")
        parentNode.className = "block"

        var nameNode = document.createElement("div")
        nameNode.className = "name"
        parentNode.appendChild(nameNode)

        var descriptionNode = document.createElement("div")
        descriptionNode.className = "description"
        parentNode.appendChild(descriptionNode)

        var references = {
            cell: parentNode,
            name: nameNode,
            description: descriptionNode
        }
        return references
    }
    function updateTestCell (oid) {
        var reference = testNodes[oid]
        var data = trackedTests[oid]
        if (!reference || !data) {
            return;
        }
        if (data.name) {
            reference.name.innerText = data.name
        } else {
            reference.name.innerHTML = noTestName
        }
        if (data.name) {
            reference.description.innerText = data.description
        } else {
            reference.description.innerHTML = noTestDescription
        }
    }

    /* Creating */
    newTestButton.addEventListener("click", function () {
        createTest()
    })
    async function createTest () {
        var e = await StateTracker.get("createTest")

        if (e.code !== "Success") {
            console.log("Couldn't create test");
            return;
        }

        var oid = e.result.id
        StateTracker.reloadTracker('listTests')

        if (logCreateCommands) {
            console.log("created test", oid)
        }
    }

    /* Context menu */
    function cellClickHandler (e, oid) {
        console.log("TODO: implement click handler", oid, e)
        TestsPageGUI.editTest(oid)
    }
})(window)
