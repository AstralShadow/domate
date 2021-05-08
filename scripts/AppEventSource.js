
(function (window) {
    "use strict"
    window.AppEventSource = new Promise(function (ready) {
        window.addEventListener("load", function () {
            var es = new EventSource("content")
            ready(es)
            es.onmessage = function (e) {
                console.log(e);
            }
        })
    })
})(window)
