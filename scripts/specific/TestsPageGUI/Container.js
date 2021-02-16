/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker, logDownloadTestData, logCreateCommands, noTestDescription, noTestName, FancyContextMenu */

/* Dependencies */
if (!window.TestsPageGUI) {
    var TestsPageGUI = {}
}
if (!window.StateTracker) {
    throw "StateTracker is a dependency of TestsPageGUI"
}
if (!window.FancyContextMenu) {
    throw "FancyContextMenu is a dependency of TestsPageGUI"
}

/**
 * Updates and renders list of tests/groups/exercises.
 * @param {object} options - type, containerQuery, listURL, dataURL, createURL
 * @returns {undefined}
 */
TestsPageGUI.Container = function (options) {
    'use strict'
    const TestsPageGUI = window.TestsPageGUI
    const logDownloadData = TestsPageGUI.logDownloadGroupData || false
    const logCreateCommands = TestsPageGUI.logCreateCommands || false
    const namePlaceholder = options.noName || TestsPageGUI.noName || ""
    const descriptionPlaceholder = options.noDescription || TestsPageGUI.noDescription || ""

    var type = options.type
    var containerQuery = options.containerQuery
    var listURL = options.listURL
    var dataURL = options.dataURL
    var createURL = options.createURL
    if (!type || !containerQuery || !listURL || !dataURL || !createURL) {
        throw "Missing TestPageGUI.Container option!"
    }
    /* Functionality */
    async function create () {
        var e = await StateTracker.get(createURL)

        if (e.code !== "Success") {
            // TODO: implement some kind of feedback
            console.log("Couldn't create " + type);
            return;
        }

        var oid = e.result.id
        StateTracker.reloadTracker(listURL)
        edit(oid)

        if (logCreateCommands) {
            console.log("created " + type, oid)
        }
    }
    async function edit (oid) {
        if (options.edit) {
            options.edit(oid)
        } else {
            console.log(`*${type}.edit* not implemented`)
        }
    }
    async function remove (oid) {
        console.log("*remove* not implemented")
    }
    async function use (oid) {
        if (options.use) {
            options.use(oid)
        } else {
            console.log(`*${type}.use* not implemented`)
        }
    }

    /* Containers */
    var container = document.querySelector(containerQuery)
    var newElementButton = document.createElement("div")
    newElementButton.className = "newElementButton"
    newElementButton.addEventListener("click", function () {
        create()
    })

    /**
     * Data from server, indexed by oid
     * @type object
     */
    const tracked = {}
    /**
     * Objects of references, indexed by oid. Contains
     * {cell, name, description}
     * @type object
     */
    const nodes = {}

    /* Tracking */
    StateTracker.track(listURL, null, function (event) {
        event.result.forEach(track)
        render(event.result)
    })
    StateTracker.reloadTracker(listURL)

    function track (oid) {
        if (Object.keys(tracked).indexOf(oid) === -1) {
            StateTracker.track(dataURL, {id: oid}, handleUpdate)
            tracked[oid] = null
        }
        StateTracker.reloadTracker(dataURL, {id: oid})
    }
    function untrack (oid) {
        StateTracker.untrack(dataURL, {id: oid}, handleUpdate)
    }
    function handleUpdate (e) {
        var object = e.result
        tracked[object._id] = object
        if (logDownloadData) {
            console.log(type, object._id, object)
        }
        if (nodes[object._id]) {
            updateCell(object._id)
        } else {
            render()
        }
    }

    /* Rendering */
    var lastOrder = []
    function render (oids) {
        var list = oids || lastOrder
        lastOrder = list

        while (container.firstChild) {
            container.removeChild(container.firstChild)
        }

        list.forEach(function (oid) {
            if (nodes[oid] === undefined) {
                nodes[oid] = createCell()
                nodes[oid].contextMenu.oid = oid
            }
            updateCell(oid)
            container.appendChild(nodes[oid].cell)
        })
        container.appendChild(newElementButton)
    }
    function createCell () {
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
            description: descriptionNode,
            contextMenu: new FancyContextMenu(parentNode, ctxOptions)
        }
        return references
    }
    function updateCell (oid) {
        var reference = nodes[oid]
        var data = tracked[oid]
        if (!reference || !data) {
            return;
        }
        if (data.name) {
            reference.name.innerText = data.name
        } else {
            reference.name.innerHTML = namePlaceholder
        }
        if (data.name) {
            reference.description.innerText = data.description
        } else {
            reference.description.innerHTML = descriptionPlaceholder
        }
    }

    /* Context menu */
    function optionFactory (img, cb) {
        return new FancyContextMenu.Option(img, function (menu) {
            cb(menu.oid)
        })
    }
    var ctxOptions = [
        optionFactory("img/icon_231x234.png", edit),
        optionFactory("img/icon_231x234.png", remove),
        optionFactory("img/icon_231x234.png", use)
    ]
}
