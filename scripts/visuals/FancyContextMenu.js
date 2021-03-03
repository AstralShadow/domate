/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * A nice visual menu that opens on left click.
 * @param {type} element
 * @param {type} optionsList - array of objects of type FancyContextMenu.Option
 * @returns {undefined}
 */
function FancyContextMenu (element, optionsList, infoImgSrc) {
    'use strict'
    var self = this
    this.options = optionsList || []
    var defaultDelay = 200

    /* Target */
    if (element !== undefined) {
        Object.defineProperty(this, "element", {
            value: element,
            writable: false
        })

        element.addEventListener("click", function (e) {
            var x = e.clientX
            var y = e.clientY
            var clickedIcon = self.options.find(function (a) {
                return a.icon === e.target
            })
            if (!visible && !clickedIcon) {
                setTimeout(function () {
                    self.open(x, y)
                }, 0)
            }
        })

        element.addEventListener("scroll", function () {
            self.close()
        })
    }
    document.addEventListener("click", function () {
        self.close()
    })
    document.addEventListener("scroll", function () {
        self.close()
    })

    /* Helper */
    var infoimg = null
    if (infoImgSrc) {
        infoimg = document.createElement("img")
        infoimg.src = infoImgSrc
        infoimg.className = "FancyContextMenu_Help"
    }

    /* Animation */
    var visible = false
    var progress = 1
    var animationStart = -1
    var animationDuration = 0
    var xPos = 0
    var yPos = 0
    var radius = 50
    var onAnimationEnd = null
    var lastOpenAnimation = null
    var lastCloseAnimation = null
    this.open = async function (x, y, duration) {
        if (duration === undefined) {
            duration = defaultDelay
        }
        if (lastCloseAnimation) {
            await lastCloseAnimation
        }
        if (!visible && onAnimationEnd === null) {
            visible = true
            xPos = x
            yPos = y

            for (var i = 0; i < self.options.length; i++) {
                var option = self.options[i]
                if (option.lastContextMenu) {
                    await option.lastContextMenu.close()
                }
                (element || document.body).append(option.icon)
                option.lastContextMenu = self
            }
            if (infoimg) {
                (element || document.body).append(infoimg)
                infoimg.style.left = radius * 3 + "px"
                infoimg.style.top = 0 + "px"
            }

            var now = (new Date()).getTime()
            animationDuration = duration * progress
            progress = 0
            animationStart = now
            requestAnimationFrame(animation)

            lastOpenAnimation = new Promise(function (resolve) {
                onAnimationEnd = function () {
                    resolve()
                    onAnimationEnd = null
                }
            })
            await lastOpenAnimation
        }
    }
    this.close = async function (duration) {
        if (duration === undefined) {
            duration = defaultDelay
        }
        if (lastOpenAnimation) {
            await lastOpenAnimation
        }
        if (visible && onAnimationEnd === null) {
            visible = false

            var now = (new Date()).getTime()
            animationDuration = duration * progress
            progress = 0
            animationStart = now
            requestAnimationFrame(animation)
            lastCloseAnimation = new Promise(function (resolve) {
                onAnimationEnd = function () {
                    resolve()
                    onAnimationEnd = null
                }
            })
            await lastCloseAnimation
        }
    }

    function animation () {
        if (animationStart === -1) {
            return;
        }
        requestAnimationFrame(animation)

        var now = (new Date()).getTime()
        progress = (now - animationStart) / animationDuration
        if (progress > 1) {
            progress = 1
            animationStart = -1;
            if (!visible) {
                self.options.forEach(function (option) {
                    if (option.lastContextMenu === self) {
                        (element || document.body).removeChild(option.icon)
                        option.lastContextMenu = null
                    }
                });
                if (infoimg)
                    (element || document.body).removeChild(infoimg)
            } else {
                self.options.forEach(function (option) {
                    if (option.lastContextMenu === self) {
                        option.icon.style.opacity = 1
                    }
                })
            }
            if (onAnimationEnd !== null) {
                onAnimationEnd()
            }
        }

        var count = self.options.length
        var p = visible ? progress : 1 - progress
        self.options.forEach(function (option, i) {
            if (option.lastContextMenu === self) {
                var icon = option.icon
                var direction = Math.PI * (2 * i * p / count - p / 2)
                var x = xPos + Math.cos(direction) * radius * p
                var y = yPos + Math.sin(direction) * radius * p
                icon.style.top = Math.round(y) + "px"
                icon.style.left = Math.round(x) + "px"
                option.icon.style.opacity = p
            }
        })
        if (infoimg) {
            infoimg.style.opacity = Math.max(0, p * 1.2 - .1)
            infoimg.style.left = xPos + radius + "px"
            infoimg.style.top = yPos - radius + "px"
            if (xPos + radius + infoimg.offsetWidth > window.innerWidth) {
                infoimg.style.left = xPos - radius - infoimg.offsetWidth - self.options[0].icon.offsetWidth + "px"
            }
        }
    }

}

FancyContextMenu.canvas = document.createElement("canvas")

/**
 * A single option, contains circle icon and callback.
 * The callback is invoked with FancyContextMenu object as parameter
 * @param {type} imageSrc
 * @param {type} callback
 * @returns {undefined}
 */
FancyContextMenu.Option = function (imageSrc, callback) {
    'use strict'
    this.icon = document.createElement("div")
    this.icon.className = "FancyContextMenu_Icon"

    if (imageSrc.indexOf(':') !== -1) {
        var content = imageSrc.split(':')[1]
        imageSrc = imageSrc.split(':')[0]
        var span = document.createElement("span")
        this.icon.appendChild(span)
        span.innerText = content
        span.style.position = "absolute"
        span.style.textIndent = "0px"
        span.style.textShadow = "2px 2px 2px black, -2px 2px 2px black, -2px -2px 2px black, 2px -2px 2px black"
        span.style.transform = "translate(-" + FancyContextMenu.getTextWidth(content) / 2 + "px, -23px)"
        this.icon.style.overflow = "hidden"
        var icon = this.icon
        this.icon.addEventListener("mouseover", function () {
            this.style.overflow = "visible"
        })
        this.icon.addEventListener("mouseout", function () {
            this.style.overflow = "hidden"
        })
    }

    this.icon.style.backgroundImage = `url("${imageSrc}")`

    var lastContextMenu = null

    Object.defineProperty(this, "lastContextMenu", {
        get: function () {
            return lastContextMenu
        },
        set: function (v) {
            lastContextMenu = v
        }
    })

    this.icon.addEventListener("click", function () {
        lastContextMenu.close()
        callback(lastContextMenu)
    })

}

FancyContextMenu.getTextWidth = function (text) {
    // re-use canvas object for better performance
    var canvas = FancyContextMenu.canvas;
    var context = canvas.getContext("2d");
    context.font = "14pt Liberation Serif";
    var metrics = context.measureText(text);
    return metrics.width - 7;
}
