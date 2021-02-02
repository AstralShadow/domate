
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
        "listExercises": "?p=listExercises",
        "listTests": "?p=listTests",
        "inputTest": "?p=inputTest", // temporary
        "createTest": "?p=createTest",
        "modifyTest": "?p=modifyTest",
        "testData": "?p=testData",
        "logout": "?p=logout"
    }

    window.StateTracker = new RESTStateTracker()

    function RESTStateTracker () {
        var _tracking = []

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
                    url: URLmap[name],
                    args: argsCopy,
                    callbacks: [callback],
                    lastTime: 0,
                    lastData: undefined,
                    refreshDelay: refreshDelay || 30000,
                    waiting: false
                }
                _tracking.push(query)
            }
        }

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

        function checkForUpdates () {
            _tracking.forEach(function (data) {
                if (data.waiting) {
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
