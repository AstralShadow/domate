/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global AppEventSource */

/* Dependencies */
if (!window.TestsPageGUI) {
    window.TestsPageGUI = {}
}
if (!window.SwidingBoard) {
    throw "SwidingBoard is a dependency of TestsPageGUI"
}
if (!window.AppEventSource) {
    throw "AppEventSource is a dependency of TestsPageGUI"
}

/**
 * Handles similar parts between editors
 * @param {type} oid
 * @param {type} options
 * @returns {TestsPageGUI.DefaultEditor}
 */
TestsPageGUI.DefaultEditor = function (oid, options) {
    'use strict'
    var self = this
    const TestsPageGUI = window.TestsPageGUI
    const SwidingBoard = window.SwidingBoard

    const type = options.type
    const endpoint = options.endpoint
    const pageQuery = options.pageQuery
    const nameQuery = options.nameQuery
    const descriptionQuery = options.descriptionQuery
    const contentsRenderer = options.contentsRenderer
    const swidingDirection = options.swidingDirection || "right"

    if (!type || !endpoint || !contentsRenderer) {
        throw ["Missing TestPageGUI.Container option!", options]
    }
    if (!pageQuery || !nameQuery || !descriptionQuery) {
        throw ["Missing TestPageGUI.Container option: query!", options]
    }

    this.onclose = options.onclose

    /* Init */
    TestsPageGUI.activeEditor = this
    var container = document.querySelector(pageQuery)
    var nameInput = document.querySelector(nameQuery)
    var descriptionInput = document.querySelector(descriptionQuery)
    Object.defineProperty(this, "type", {
        get: function () {
            return type
        }
    })

    /* Tracking */
    var lastData = {}
    window.AppEventSource.then(function (source) {
        source.addEventListener("deleted_" + endpoint, source_onDeleted)
        source.addEventListener("modified_" + endpoint, source_onModified)
        download()
    })
    function source_onDeleted (e) {
        var data = JSON.parse(e.data)
        if (data.id === oid) {
            console.log("closed", oid, "because deleted", data)
            self.deactivate()
        }
    }
    function source_onModified (e) {
        var data = JSON.parse(e.data)
        updateHandler(data.data)
    }
    function updateHandler (object) {
        nameInput.innerText = object.name
        descriptionInput.innerText = object.description
        lastData = object
        renderContents()
    }
    function download () {
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
                updateHandler(data)
            })
        })
    }

    /* Modifiers */
    this.setName = async function (name) {
        if (lastData.name === name) {
            return;
        }

        var input = {
            token: await getToken(),
            name: name
        }

        var request = new XMLHttpRequest()
        request.open("PUT", endpoint + "/" + oid)
        request.setRequestHeader("Content-type", "application/json")
        request.send(JSON.stringify(input))

        request.addEventListener("load", function () {
            if (request.status !== 200) {
                console.log("Failed updating at " + endpoint, request.status, request.response)
                return;
            }
        })
    }
    this.setDescription = async function (description) {
        if (lastData.description === description) {
            return;
        }

        var input = {
            token: await getToken(),
            description: description
        }

        var request = new XMLHttpRequest()
        request.open("PUT", endpoint + "/" + oid)
        request.setRequestHeader("Content-type", "application/json")
        request.send(JSON.stringify(input))

        request.addEventListener("load", function () {
            if (request.status !== 200) {
                console.log("Failed updating at " + endpoint, request.status, request.response)
                return;
            }
        })
    }

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

    /* Interface */
    var selfClick = null
    container.addEventListener("click", function (e) {
        selfClick = e
    })

    document.addEventListener("click", function (e) {
        if (TestsPageGUI.activeEditor === self && selfClick !== e) {
            TestsPageGUI.activeEditor.deactivate()
        }
    })
    nameInput.addEventListener("blur", function () {
        if (TestsPageGUI.activeEditor === self) {
            TestsPageGUI.activeEditor.setName(this.innerText)
        }
    })
    descriptionInput.addEventListener("blur", function () {
        if (TestsPageGUI.activeEditor === self) {
            TestsPageGUI.activeEditor.setDescription(this.innerText)
        }
    })

    /* Rendering */
    var swider = new SwidingBoard(container, swidingDirection)
    var show = function () {
        swider.show(TestsPageGUI.animationSpeed)
        window.ExetendedDimensionParser.parse()
    }
    var hide = () => swider.hide(TestsPageGUI.animationSpeed)
    show()
    function renderContents () {
        contentsRenderer(lastData)
    }


    this.deactivate = function () {
        window.AppEventSource.then(function (source) {
            source.removeEventListener("deleted_" + endpoint, source_onDeleted)
            source.removeEventListener("modified_" + endpoint, source_onModified)
        })
        TestsPageGUI.activeEditor = null

        var animationPromise = new Promise(function (resolve) {
            hide()
            setTimeout(function () {
                resolve()
            }, TestsPageGUI.animationSpeed)
        })
        if (this.onclose) {
            this.onclose(animationPromise)
        }
        return animationPromise
    }
}
