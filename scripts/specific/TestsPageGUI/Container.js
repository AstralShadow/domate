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
    var self = this

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
    var removeURL = options.removeURL
    var functions = options.functions
    if (!type || !containerQuery || !listURL || !dataURL || !createURL || !functions) {
        throw ["Missing TestPageGUI.Container option!", options]
    }
    /* Functionality */
    this.create = async function () {
        var e = await StateTracker.get(createURL)

        if (e.code !== "Success") {
            // TODO: implement some kind of feedback
            console.log("Couldn't create " + type, e);
            return;
        }

        var oid = e.result.id
        StateTracker.reloadTracker(listURL)
        if (options.oncreate) {
            options.oncreate(oid)
        }

        if (logCreateCommands) {
            console.log("created " + type, oid)
        }
    }
    this.remove = async function (oid) {
        var e = await StateTracker.get(removeURL, {id: oid})

        if (e.code !== "Success") {
            // TODO: implement some kind of feedback
            console.log("Couldn't remove " + type, e);
            return;
        }
        untrack(oid)

        StateTracker.reloadTracker(listURL)

        if (logCreateCommands) {
            console.log("removed " + type, oid)
        }
    }

    /* Containers */
    var container = document.querySelector(containerQuery)
    var newElementButton = document.createElement("div")
    newElementButton.className = "newElementButton"
    newElementButton.addEventListener("click", function () {
        self.create()
    })

    /**
     * Objects of references, indexed by oid. Contains
     * {cell, name, description}
     * @type object
     */
    const nodes = {}

    /* Tracking */
    /**
     * Data from server, indexed by oid
     * @type object
     */
    const tracked = {}

    function contentUpdateHandler (event) {
        Object.keys(tracked).forEach(function (oid) {
            if (event.result.indexOf(oid) === -1) {
                untrack(oid)
            }
        })
        event.result.forEach(track)
        render(event.result)

    }
    function track (oid) {
        if (Object.keys(tracked).indexOf(oid) === -1) {
            tracked[oid] = null
            StateTracker.track(dataURL, {id: oid}, handleUpdate)
        }
    }
    function untrack (oid) {
        StateTracker.untrack(dataURL, {id: oid}, handleUpdate)
        delete tracked[oid]
    }
    function handleUpdate (e) {
        if (e.code !== "Success") {
            console.log("Update not successful")
            return;
        }
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

    /* Activation & Deactivation */
    var active = false;
    this.activate = function () {
        if (!active) {
            active = true
            StateTracker.track(listURL, null, contentUpdateHandler)
        }
    }
    this.deactivate = function () {
        if (active) {
            active = false
            StateTracker.untrack(listURL, null, contentUpdateHandler)
            Object.keys(tracked).forEach(function (oid) {
                untrack(oid)
            })
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
        descriptionNode.className = "description mathjax"
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
        if (data.description || data.question) {
            reference.description.innerText = data.description || data.question
        } else {
            reference.description.innerHTML = descriptionPlaceholder
        }
        if (MathJax.typeset) {
            MathJax.typeset()
        }
    }

    /* Context menu */
    function optionFactory (img, cb) {
        return new FancyContextMenu.Option(img, function (menu) {
            cb.apply(self, [menu.oid])
        })
    }
    var ctxOptions = []
    functions.forEach(function (e) {
        ctxOptions.push(optionFactory(e[0], e[1]))
    })
}
