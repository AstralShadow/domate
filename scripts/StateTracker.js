
/**
 * The API to do communication with DomaTe server via AJAX
 * @param {object} window
 * @returns {undefined}
 */
(function (window) {
    "use strict"
    var URLmap = {
        "exerciseGroups": "?p=listExerciseGroups",
        "exercises": "?p=listExercises",
        "tests": "?p=listTests"
    }

    function RESTStateTracker () {
        var _tracking = []

        this.track = function (name, args, callback, refreshDelay) {
            if (!URLmap[name]) {
                console.log("Unknown tracked element:", name)
                return;
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
                    callback(query.lastData)
                }
            } else {
                var argsCopy
                Object.assign(argsCopy, args)
                query = {
                    name: name,
                    url: URLmap[name],
                    args: argsCopy,
                    callbacks: [callback],
                    lastTime: 0,
                    lastData: undefined,
                    refreshDelay: refreshDelay || 30000
                }
                _tracking.push(query)
            }
        }

        function pull (query) {
            var now = (new Date()).getTime()
            var url = query.url
            var args = query.args
            var callbacks = query.callbacks
            // TODO: insert ajax request to server
            //  for given url, type post, given args
            //  then, set lastData to answer object,
            //  modify lastTime to now and invoke all
            //  callbacks with {
            //   data: ajaxRequest,
            //   time: now,
            //   event: name,
            //   args: copy of args
            //  }
        }

        function checkForUpdates () {
            _tracking.forEach(function (data) {
                var now = (new Date()).getTime()
                var last = data.lastTime
                var delay = data.refreshDelay
                if (now - last > delay) {
                    pull(data)
                }
            })
        }

        setInterval(checkForUpdates, 3000)
        checkForUpdates();
    }

    window.StateTracker = new RESTStateTracker()

})(window)
