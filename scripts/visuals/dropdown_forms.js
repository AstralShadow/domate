/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Allows drop-down form effect.
 * @param string coontainerQuery
 * @param string formQuery
 * @param string subimtQuery
 * @returns void
 */
(function (containerQuery, formQuery, feedbackQuery, submitQuery) {
    "use strict"

    window.addEventListener("load", function () {
        document.querySelectorAll(containerQuery)
            .forEach(function (container) {
                var form = container.querySelector(formQuery)
                var submitKey = container.querySelector(submitQuery)
                var feedbackContainer = container.querySelector(feedbackQuery)
                new DropDownForm(container, form, submitKey, feedbackContainer)
            })

    })

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

    DropDownForm.prototype.submit = function () {
        var self = this

        var data = new FormData(this.contents)
        if (self.token !== undefined) {
            data.set("token", self.token)
        }
        clearContents()

        var request = new XMLHttpRequest()
        request.addEventListener("load", function () {
            var response = JSON.parse(request.response)
            self.feedbackContainer.innerText = response.msg

            if (response.newToken) {
                updateToken(response.newToken)

                if (response.code === "invalid_token") {
                    self.submit()
                }
            }

            if (response.reload)
                location.reload()
        })
        request.open("post", this.contents.action)
        request.send(data)

        function updateToken (newToken) {
            self.token = newToken
        }

        function clearContents () {
            var elements = self.container.querySelectorAll("input, textarea")
            Array.prototype.forEach.call(elements, function (element) {
                element.value = ""
            })
        }

    }


})
    (".dropdown_form_container", ".dropdown_form_contents", ".dropdown_form_feedback", ".dropdown_form_submit");
