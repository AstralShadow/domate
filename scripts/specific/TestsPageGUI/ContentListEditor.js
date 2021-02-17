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
TestsPageGUI.ContentListEditor = function (oid, options) {
    'use strict'
    var self = this
    const TestsPageGUI = window.TestsPageGUI
    const SwidingBoard = window.SwidingBoard

    const type = options.type
    const dataURL = options.dataURL
    const elementDataURL = options.elementDataURL
    const modifyURL = options.modifyURL
    const contentsQuery = options.contentsQuery
    const noContentName = options.noContentName || ""

    if (!type || !dataURL || !modifyURL || !elementDataURL || !contentsQuery) {
        throw ["Missing TestPageGUI.ContentListEditor option!", options]
    }

    /* Modifiers */
    this.addContent = async function (c_oid) {
        var object = {}
        object[c_oid] = 1
        var query = {
            id: oid,
            addContents: object
        }
        var result = await StateTracker.get(modifyURL, query)
        StateTracker.reloadTracker(dataURL, {id: oid})
    }
    this.removeContent = async function (c_oid) {
        var query = {
            id: oid,
            removeContents: [c_oid]
        }
        var result = await StateTracker.get(modifyURL, query)
        StateTracker.reloadTracker(dataURL, {id: oid})
    }

    /* Tracking subelements */
    var tracked = {}
    function track (oid) {
        if (Object.keys(tracked).indexOf(oid) === -1) {
            tracked[oid] = null
            StateTracker.track(elementDataURL, {id: oid}, handleUpdate)
        }
        StateTracker.reloadTracker(elementDataURL, {id: oid})
    }
    function untrack (oid) {
        StateTracker.untrack(elementDataURL, {id: oid}, handleUpdate)
        delete tracked[oid]
    }
    function handleUpdate (e) {
        if (e.code !== "Success") {
            console.log("Update failed for subelement of " + type, e)
            return;
        }
        var object = e.result
        tracked[object._id] = object
        updateNodes(object._id)
    }

    /* Rendering */
    var container = document.querySelector(contentsQuery)
    var lastContents
    var nodes = {}
    function renderContents (data) {
        if (JSON.stringify(data.contents) === JSON.stringify(lastContents)) {
            return;
        }

        /* Clear */
        while (container.firstChild) {
            container.removeChild(container.firstChild)
        }

        /* Create nodes */
        data.contents.forEach(function (entry) {
            for (var i = 0; i < entry.repeat; i++) {
                appendNode(entry)
            }
        })

        /* Track contents */
        data.contents.forEach(function (entry) {
            track(entry.id)
            updateNodes(entry.id)
        })

        /* Untrack old contents */
        Object.keys(tracked).forEach(function (oid) {
            var entry = data.contents.find(function (entry) {
                return entry.id === oid
            })
            if (!entry) {
                console.log("untrack", oid)
                untrack(oid)
            }
        })

        /* Remove old nodes */
        Object.keys(nodes).forEach(function (token) {
            var entry = data.contents.find(function (entry) {
                return entry.token === token
            })
            if (!entry) {
                removeNode(token)
            }
        })
        lastContents = data.contents
    }
    function appendNode (entry) {
        if (!nodes[entry.token]) {
            var node = createNode()
            node.entry = entry
            nodes[entry.token] = node
            node.remove.addEventListener("click", function () {
                self.removeContent(entry.token)
            })
        }
        container.appendChild(nodes[entry.token].base)
    }
    function createNode () {
        var container = document.createElement("div")
        container.className = "selectedElement"

        var moveKey = new Image()
        moveKey.className = "move"
        moveKey.src = "img/dragbutton.png"
        container.appendChild(moveKey)

        var nameContainer = document.createElement("div")
        nameContainer.className = "name"
        container.appendChild(nameContainer)

        var removeKey = new Image()
        removeKey.className = "remove"
        removeKey.src = "img/delete.png"
        container.appendChild(removeKey)

        var ref = {
            base: container,
            move: moveKey,
            name: nameContainer,
            remove: removeKey
        }
        return ref
    }
    function removeNode (token) {
        delete nodes[token]
    }
    function updateNodes (c_oid) {
        var data = tracked[c_oid]
        if (!data) {
            return;
        }
        Object.values(nodes).forEach(function (node) {
            if (node.entry.id === c_oid) {
                if (data.name) {
                    node.name.innerText = data.name
                } else {
                    node.name.innerHTML = noContentName
                }
            }
        })
    }

    options.contentsRenderer = renderContents
    TestsPageGUI.DefaultEditor.apply(this, [oid, options])
}
