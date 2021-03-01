
/**
 * The API to do communication with DomaTe server via AJAX
 * @param {object} window
 * @returns {undefined}
 */
(function (window) {
    "use strict"
    var JSON = window.JSON
    var console = window.console
    var Object = window.Object
    var URLmap = {
        "listExerciseGroups": "?p=listExerciseGroups",
        "createExerciseGroup": "?p=createExerciseGroup",
        "modifyExerciseGroup": "?p=modifyExerciseGroup",
        "removeExerciseGroup": "?p=removeExerciseGroup",
        "exerciseGroupData": "?p=exerciseGroupData",

        "listExercises": "?p=listExercises",
        "createExercise": "?p=createExercise",
        "modifyExercise": "?p=modifyExercise",
        "removeExercise": "?p=removeExercise",
        "exerciseData": "?p=exerciseData",

        "listTests": "?p=listTests",
        "createTest": "?p=createTest",
        "modifyTest": "?p=modifyTest",
        "removeTest": "?p=removeTest",
        "testData": "?p=testData",

        "logout": "?p=logout",
        "scheduleTest": "?p=scheduleTest",

        "beginTest": "?p=beginTest",
        "getExamData": "?p=getExamData",
        "getExamQuestion": "?p=getExamQuestion",
        "answerExamQuestion": "?p=answerExamQuestion"

    }

    // TODO: Modifying can return status, use this.
    //       Also schedule force-reloads for at least
    //       one second after last reload request.

    window.StateTracker = new RESTStateTracker()

    function RESTStateTracker () {
        var self = this
        var _tracking = []

        /**
         * Asynchronously loads a single request once.
         * @param {string} name
         * @param {object} args
         * @param {type} callback
         * @returns {Promise|undefined}
         */
        this.get = function (name, args, callback) {
            var url = URLmap[name]
            if (url === undefined) {
                console.log("Unknown tracked element:", name)
                return;
            }

            var p = new Promise(function (resolve, reject) {
                var request = new XMLHttpRequest()

                request.addEventListener("load", function () {
                    if (request.status !== 200) {
                        reject(request)
                        return;
                    }

                    var result = {
                        event: name,
                        args: args,
                        timestamp: (new Date()).getTime()
                    }
                    Object.assign(result, JSON.parse(request.response))
                    resolve(result)
                })

                request.open("post", url)
                request.setRequestHeader("Content-type", "application/json")
                request.send(JSON.stringify(args))
            })
            if (callback === undefined) {
                return p
            } else {
                p.then(callback)
            }
        }

        /**
         * Asynchronously reloads a given request once per given delay (30s default)
         * Callback is invoked if there is difference in the contents of the request
         * @param {string} name
         * @param {object} args
         * @param {function} callback
         * @param {int} refreshDelay
         * @returns {undefined}
         */
        this.track = function (name, args, callback, refreshDelay) {
            if (!URLmap[name]) {
                console.log("Unknown tracked element:", name)
                return;
            }
            if (!args) {
                args = {}
            }
            var query = _tracking.find(function (q) {
                if (q.name !== name) {
                    return false;
                }
                var keys = Object.keys(q.args)
                    .concat(Object.keys(args))
                for (var i in keys) {
                    var key = keys[i]
                    if (args[key] !== q.args[key]) {
                        return false;
                    }
                }
                return true;
            })

            if (query) {
                query.callbacks.push(callback)
                query.active = true
                if (refreshDelay && refreshDelay < query.refreshDelay) {
                    query.refreshDelay = refreshDelay
                }
                if (query.lastData !== undefined) {
                    callback(JSON.parse(query.lastData))
                }
            } else {
                var argsCopy = {}
                Object.assign(argsCopy, args)
                query = {
                    name: name,
                    active: true,
                    url: URLmap[name],
                    args: argsCopy,
                    callbacks: [callback],
                    lastTime: 0,
                    lastData: undefined,
                    refreshDelay: refreshDelay || 30000,
                    waiting: false
                }
                _tracking.push(query)
                self.reloadTracker(name, argsCopy)
            }
        }

        /**
         * Removes a single tracker.
         * If none are present for the event,
         * removes the event itself
         * @param {string} name
         * @param {object} args
         * @param {function} callback
         * @returns {undefined}
         */
        this.untrack = function (name, args, callback) {
            if (!URLmap[name]) {
                console.log("Unknown tracked element:", name)
                return;
            }
            if (!args) {
                args = {}
            }
            var query = _tracking.find(function (q) {
                if (q.name !== name) {
                    return false;
                }
                var keys = Object.keys(q.args)
                    .concat(Object.keys(args))
                for (var i in keys) {
                    var key = keys[i]
                    if (args[key] !== q.args[key]) {
                        return false;
                    }
                }
                return true;
            })

            if (query) {
                var callbackIndex = query.callbacks.indexOf(callback)
                if (callbackIndex !== -1) {
                    query.callbacks.splice(callbackIndex, 1)
                }
                if (query.callbacks.length < 1) {
                    query.active = false
                }
            }
        }

        /**
         * Reloads a tracker without care of delay.
         * @param {string} name
         * @param {object} args
         * @returns {undefined}
         */
        this.reloadTracker = function (name, args) {
            if (!URLmap[name]) {
                console.log("Unknown tracked element:", name)
                return;
            }
            if (!args) {
                args = {}
            }
            var query = _tracking.find(function (q) {
                if (q.name !== name) {
                    return false;
                }
                var keys = Object.keys(q.args)
                    .concat(Object.keys(args))
                for (var i in keys) {
                    var key = keys[i]
                    if (args[key] !== q.args[key]) {
                        return false;
                    }
                }
                return true;
            })
            if (query) {
                pull(query)
            }
        }

        /**
         * Loads a request with XMLHttpRequest
         * @param {type} query
         * @returns {undefined}
         */
        function pull (query) {
            query.waiting = true
            var name = query.name
            var url = query.url
            var args = query.args
            var callbacks = query.callbacks

            var request = new XMLHttpRequest()

            request.addEventListener("load", function () {
                query.waiting = false
                if (request.status === 200) {
                    var argsCopy = {}
                    Object.assign(argsCopy, args)
                    query.lastTime = (new Date()).getTime()

                    if (request.response !== query.lastData) {
                        callbacks.forEach(function (callback) {
                            var result = {
                                event: name,
                                args: argsCopy,
                                timestamp: query.lastTime
                            }
                            Object.assign(result, JSON.parse(request.response))
                            callback(result)
                        })
                    }

                    query.lastData = request.response
                }
            })

            request.open("post", url)
            request.setRequestHeader("Content-type", "application/json")
            request.send(JSON.stringify(args))
        }

        /**
         * Invokes pull for all requests that have reached time for reload
         * @returns {undefined}
         */
        function checkForUpdates () {
            _tracking.forEach(function (data) {
                if (data.waiting || !data.active) {
                    return;
                }
                var now = (new Date()).getTime()
                var last = data.lastTime
                var delay = data.refreshDelay
                if (now - last > delay) {
                    pull(data)
                }
            })
        }

        setInterval(checkForUpdates, 1000)
        checkForUpdates();
    }

})(window)
