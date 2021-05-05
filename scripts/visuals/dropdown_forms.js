
/**
 * Allows drop-down form effect.
 * It sends the selected forms without
 *  reloading and returns feedback
 * 
 * @param {string} containerQuery
 * @param {string} formQuery
 * @param {string} feedbackQuery
 * @param {string} submitQuery
 * @returns {undefined}
 */
(function (containerQuery, formQuery, feedbackQuery, submitQuery) {
    "use strict"

    function loadDropDownFunctionality () {
        document.querySelectorAll(containerQuery)
            .forEach(function (container) {
                var form = container.querySelector(formQuery)
                var submitKey = container.querySelector(submitQuery)
                var feedbackContainer = container.querySelector(feedbackQuery)
                new DropDownForm(container, form, submitKey, feedbackContainer)
            })
    }

    if (document.readyState === "complete")
        loadDropDownFunctionality();
    else
        window.addEventListener("load", loadDropDownFunctionality)

    function DropDownForm (container, hiddenForm, submitKey, feedbackContainer) {
        var self = this

        this.container = container
        this.contents = hiddenForm
        this.button = submitKey
        this.feedbackContainer = feedbackContainer
        this.shown = false
        this.animationPhrase = 1
        this.animationTimer = 0

        var mouseOut = false
        this.container.addEventListener("mouseout", function () {
            mouseOut = true
        })
        this.container.addEventListener("mouseleave", function () {
            mouseOut = true
            scheduleHide()
        })
        this.container.addEventListener("mouseover", function () {
            mouseOut = false
        })
        this.button.addEventListener("click", function (e) {
            e.stopPropagation()
            self.clickHandler()
        })
        document.addEventListener("click", function () {
            mouseOut = true
            scheduleHide()
        })
        function scheduleHide () {
            setTimeout(function () {
                var focused = Array.prototype.indexOf.call(self.contents, document.activeElement)
                if (mouseOut && focused === -1)
                    self.hide()
            }, 500)
        }

        function animation () {
            requestAnimationFrame(animation)

            if (self.animationPhrase !== 1) {
                var now = (new Date()).getTime()
                var time = now - self.animationTimer
                self.animationTimer = now

                self.progressAnimation(time)
            }
        }
        requestAnimationFrame(animation)

        this.contents.style.display = "block"
        this.container.style.top = -this.contents.offsetHeight + "px"
    }

    DropDownForm.prototype.token = undefined

    DropDownForm.prototype.clickHandler = function () {
        if (this.shown)
            this.submit()
        else
            this.show()
    }

    DropDownForm.prototype.show = function () {
        if (this.shown)
            return;
        this.animationTimer = (new Date()).getTime()
        this.animationPhrase = 0
        this.shown = true
    }
    DropDownForm.prototype.hide = function () {
        if (!this.shown)
            return;
        this.animationTimer = (new Date()).getTime()
        this.animationPhrase = 0
        this.shown = false
    }
    DropDownForm.prototype.progressAnimation = function (passedTime) {
        var distance = this.contents.offsetHeight
        this.animationPhrase += passedTime / (distance * 2)
        if (this.animationPhrase > 1)
            this.animationPhrase = 1

        var top = (this.animationPhrase - 1) * distance
        if (!this.shown)
            top = -top - distance
        this.container.style.top = top + "px"
    }

    DropDownForm.prototype.submit = async function () {
        var self = this

        var token_promise = new Promise(function (finish) {
            var token_request = new XMLHttpRequest()
            token_request.addEventListener("load", function () {
                finish(token_request)
            })
            token_request.open("post", "./profile/get-token")
            token_request.send()
        })

        var token_request = await token_promise;

        var token_requset_result = JSON.parse(token_request.response);
        var token = token_requset_result["token"] || undefined
        if (token_request.status !== 200 || token === undefined) {
            sendFeedback(token_requset_result.message)
            return;
        }

        var data = new FormData(this.contents)
        data.set("token", token)

        var request_promise = new Promise(function (finish) {
            var request = new XMLHttpRequest()
            request.addEventListener("load", function () {
                finish(request)
            })
            request.open("post", self.contents.action)
            request.send(data)
        })

        clearContents()
        var request = await request_promise;
        var response = JSON.parse(request.response)
        console.log(this.contents.action)
        if (request.status === 200 && this.contents.action.indexOf("login") !== -1) {
            location.reload()
        }
        sendFeedback(response.message)


        function clearContents () {
            var elements = self.container.querySelectorAll("input, textarea")
            Array.prototype.forEach.call(elements, function (element) {
                element.value = ""
            })
        }

        function sendFeedback (str) {
            self.feedbackContainer.innerText = str
        }

    }


})(".dropdown_form_container", ".dropdown_form_contents", ".dropdown_form_feedback", ".dropdown_form_submit")
