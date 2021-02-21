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
    const getId = options.parseContentRealId
    const getToken = options.parseContentInListId
    const moving = !(options.disableMove || false)
    // TODO: implament count when design is ready

    if (!type || !dataURL || !modifyURL || !elementDataURL || !contentsQuery) {
        throw ["Missing TestPageGUI.ContentListEditor option!", options]
    }
    if (!getId || !getToken) {
        throw ["Missing TestPageGUI.ContentListEditor parser!", options]
    }

    /* Modifiers */
    this.addContent = async function (object) {
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
    if (moving) {
        this.move = async function (token, index) {
            var query = {
                id: oid,
                move: token,
                position: index
            }
            var result = await StateTracker.get(modifyURL, query)
            StateTracker.reloadTracker(dataURL, {id: oid})
        }
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
            appendNode(entry)
        })
        resetPositions()

        /* Track contents */
        data.contents.forEach(function (entry) {
            if (!getId(entry)) {
                console.log("err", entry)
            }
            track(getId(entry))
            updateNodes(getId(entry))
        })

        /* Untrack old contents */
        Object.keys(tracked).forEach(function (oid) {
            var entry = data.contents.find(function (entry) {
                return getId(entry) === oid
            })
            if (!entry) {
                untrack(oid)
            }
        })

        /* Remove old nodes */
        Object.keys(nodes).forEach(function (token) {
            var entry = data.contents.find(function (entry) {
                return getToken(entry) === token
            })
            if (!entry) {
                removeNode(token)
            }
        })
        lastContents = data.contents
    }
    function appendNode (entry) {
        if (!nodes[getToken(entry)]) {
            var node = createNode(entry)
            nodes[getToken(entry)] = node
        }
        container.appendChild(nodes[getToken(entry)].base)
    }
    function createNode (entry) {
        var container = document.createElement("div")
        container.className = "selectedElement"

        if (moving) {
            var moveKey = document.createElement("div")
            moveKey.className = "move contentButton"
            container.appendChild(moveKey)
        }

        var nameContainer = document.createElement("div")
        nameContainer.className = "name"
        container.appendChild(nameContainer)

        var removeKey = document.createElement("div")
        removeKey.className = "remove contentButton"
        container.appendChild(removeKey)

        var ref = {
            base: container,
            move: moving ? moveKey : null,
            name: nameContainer,
            remove: removeKey,
            entry: entry
        }

        if (moving) {
            moveKey.addEventListener("mousedown", function (e) {
                mousedownHandler(e, ref)
                return false
            })
        }
        removeKey.addEventListener("click", function () {
            self.removeContent(getToken(entry))
        })

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
            if (getId(node.entry) === c_oid) {
                if (data.name) {
                    node.name.innerText = data.name
                } else {
                    node.name.innerHTML = noContentName
                }
            }
        })
    }

    /* Init core */
    options.contentsRenderer = renderContents
    TestsPageGUI.DefaultEditor.apply(this, [oid, options])

    /* Drag to order */
    var dragging = null
    var baseY = 0
    var firstTop = 0
    var deltaY = 0
    var lastTop = 0

    if (moving) {
        document.addEventListener("mouseup", function (e) {
            if (dragging) {
                mouseupHandler(e)
                return false
            }
        })
        document.addEventListener("mousemove", function (e) {
            if (dragging) {
                deltaY += e.movementY
            }
        })
    }
    function mousedownHandler (e, nodeRef) {
        requestAnimationFrame(dragAnimation)
        dragging = nodeRef
        baseY = nodeRef.base.offsetTop
        deltaY = 0
        lastTop = container.lastChild.offsetTop
        firstTop = container.firstChild.offsetTop
        document.body.style.userSelect = "none"
    }
    function mouseupHandler (e) {
        document.body.style.userSelect = "auto"

        var localDelta = Math.max(firstTop - baseY, deltaY)
        localDelta = Math.min(lastTop - baseY, localDelta)
        var margins = 10

        resetPositions()
        var baseI = Array.prototype.indexOf.call(container.childNodes, dragging.base)
        var array = []
        container.childNodes.forEach(function (node, i) {
            if (dragging.base === node) {
                return;
            }
            var baseDelta = node.offsetTop - baseY
            var height = node.offsetHeight + margins
            var cursorDelta = baseDelta - localDelta
            var y = 0
            if (i < baseI) {
                y = Math.max(0, Math.min(height, cursorDelta + height))
            } else {
                y = Math.max(-height, Math.min(0, cursorDelta - height))
            }
            y = Math.round(y / height)
            array[i + y] = getToken(lastContents[i])
            node.style.transform = "translate(0px, " + y * height + "px)"
        })
        var height = dragging.base.offsetHeight + margins
        dragging.base.style.top = Math.round(localDelta / height) * height + "px"

        var token = getToken(dragging.entry)
        dragging = null
        for (var i = 0; i < container.childNodes.length; i++) {
            if (array[i] === undefined) {
                var pos = i
                setTimeout(function () {
                    self.move(token, pos)
                }, 0)
                break;
            }
        }
    }
    function dragAnimation () {
        if (!dragging) {
            return;
        }
        requestAnimationFrame(dragAnimation)
        var localDelta = Math.max(firstTop - baseY, deltaY)
        localDelta = Math.min(lastTop - baseY, localDelta)
        var margins = 10

        resetPositions()
        var baseI = Array.prototype.indexOf.call(container.childNodes, dragging.base)
        container.childNodes.forEach(function (node, i) {
            if (dragging.base === node) {
                return;
            }
            var baseDelta = node.offsetTop - baseY
            var height = node.offsetHeight + margins
            var cursorDelta = baseDelta - localDelta
            var y = 0
            if (i < baseI) {
                y = Math.max(0, Math.min(height, cursorDelta + height))
            } else {
                y = Math.max(-height, Math.min(0, cursorDelta - height))
            }
            node.style.transform = "translate(0px, " + y + "px)"
        })

        dragging.base.style.top = localDelta + "px"
    }

    function resetPositions () {
        container.childNodes.forEach(function (node, i) {
            node.style.top = "0px"
            node.style.transform = "translate(0px, 0px)"
        })
    }
}
