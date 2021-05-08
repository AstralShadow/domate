/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global AppEventSource, logDownloadTestData, logCreateCommands, noTestDescription, noTestName, FancyContextMenu */

/* Dependencies */
if (!window.TestsPageGUI) {
    var TestsPageGUI = {}
}
if (!window.AppEventSource) {
    throw "AppEventSource is a dependency of TestsPageGUI"
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
    var functions = options.functions
    var endpoint = options.endpoint
    if (!type || !containerQuery || !functions || !endpoint) {
        throw ["Missing TestPageGUI.Container option!", options]
    }

    var evCreated = options.evCreated || "new_" + endpoint
    var evModified = options.evModified || "modified_" + endpoint
    var evDeleted = options.evDeleted || "deleted_" + endpoint

    function getToken () {
        return new Promise(function (resolve) {
            var request = new XMLHttpRequest()
            request.open("GET", endpoint + "/get-token")
            request.send()

            request.addEventListener("load", function () {
                var data = JSON.parse(request.response)
                if (data.token !== undefined) {
                    resolve(data.token)
                } else {
                    console.log("Couldn't accure token. Maybe your session expired")
                }
            })
        })
    }

    /* Functionality */
    this.create = async function () {
        var input = {
            token: await getToken()
        }
        var request = new XMLHttpRequest()
        request.open("POST", endpoint)
        request.setRequestHeader("Content-type", "application/json")
        request.send(JSON.stringify(input))

        request.addEventListener("load", function () {
            if (request.status !== 200) {
                console.log("Failed creating element at " + endpoint, request.status, request.response)
                return;
            }

            var data = JSON.parse(request.response)
            if (options.oncreate) {
                options.oncreate(data.id)
            }

            if (logCreateCommands) {
                console.log("created element at endpoint " + endpoint, data.id)
            }
        })
    }
    this.remove = async function (oid) {
        var input = {
            token: await getToken()
        }
        var request = new XMLHttpRequest()
        request.open("DELETE", endpoint + "/" + oid)
        request.setRequestHeader("Content-type", "application/json")
        request.send(JSON.stringify(input))

        request.addEventListener("load", function () {
            if (request.status !== 200) {
                console.log("Failed creating element at " + endpoint, request.status, request.response)
                return;
            }

            var data = JSON.parse(request.response)
            if (options.oncreate) {
                options.oncreate(data.id)
            }

            if (logCreateCommands) {
                console.log("deleted element at endpoint " + endpoint, data.id)
            }
        })
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

    window.AppEventSource.then(function (source) {
        source.addEventListener(evCreated, function (e) {
            var data = JSON.parse(e.data)
            download(data.id)
        })
        source.addEventListener(evDeleted, function (e) {
            var data = JSON.parse(e.data)
            delete tracked[data.id]
            render()
            setTimeout(function () {
                // workaround rapid deletion event delay
                delete tracked[data.id]
                render()
            }, 500)
        })
        source.addEventListener(evModified, function (e) {
            var data = JSON.parse(e.data)
            console.log("event data: ", data)
            tracked[data.id] = data.data;
            updateCellOrRender(data.id)
        })
        fetchContentList();
    })

    function download (oid) {
        return new Promise(function (resolve) {
            var request = new XMLHttpRequest()
            request.open("GET", endpoint + "/" + oid)
            request.send()

            request.addEventListener("load", function () {
                if (request.status !== 200) {
                    console.log("Failed downloading element at " + endpoint, request.status, request.response)
                    return;
                }

                var data = JSON.parse(request.response).data
                resolve(data)
                tracked[data._id] = data
                updateCellOrRender(data._id)


                if (logDownloadData) {
                    console.log("downloading element at endpoint " + endpoint, data)
                }
            })
        })
    }

    function fetchContentList () {
        var request = new XMLHttpRequest()
        request.open("GET", endpoint)
        request.send()

        request.addEventListener("load", function () {
            if (request.status !== 200) {
                console.log("Failed fetching content at " + endpoint, request.status, request.response)
                return;
            }

            var data = JSON.parse(request.response)
            updateContentList(data.data)

            if (logDownloadData) {
                console.log("fetched content list at endpoint " + endpoint, data.data)
            }
        })
    }
    async function updateContentList (newList) {
        var trackedKeys = Object.keys(tracked)
        trackedKeys.forEach(function (oid) {
            if (newList.indexOf(oid) === -1) {
                delete tracked[oid]
            }
        })
        var promises = []
        newList.forEach(async function (oid) {
            if (trackedKeys.indexOf(oid) === -1) {
                if (tracked[oid] === undefined) {
                    tracked[oid] = {}
                }
                tracked[oid] = await download(oid)
            }
        })
        render()
    }

    function updateCellOrRender (oid) {
        if (nodes[oid]) {
            updateCell(oid)
        } else {
            render()
        }
    }

    this.get = function (oid) {
        return tracked[oid]
    }

    /* Activation & Deactivation */
    var active = false;
    this.activate = function () {
        if (!active) {
            active = true
            render()
        }
    }
    this.deactivate = function () {
        if (active) {
            active = false
        }
    }

    /* Rendering */
    function render (oids) {
        var list = Object.keys(tracked)

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
            contextMenu: new FancyContextMenu(parentNode, ctxOptions, options.contextHint)
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
