/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Executes expressions within data-dimensions attribute
 * Modifies element.style.<...>
 * You can use substraction and addition.
 * Also selectin attributes with <[simple css selector]>.attribute
 * @returns {undefined}
 */
(function () {
    "use strict"
    function loop () {
        var elements = document.querySelectorAll('[data-dimensions]')
        elements.forEach(function (element) {
            var query = element.getAttribute("data-dimensions")
            var data = parseDimensionData(query)
            Object.keys(data).forEach(function (key) {
                var value = data[key]
                var chain = key.split('.')
                var object = element.style
                while (chain.length > 1) {
                    object = object[chain.shift()]
                }
                object[chain.shift()] = value + "px"
            })
        })
    }

    function parseDimensionData (query) {
        var input = query.trim().split(';')
        var data = {}
        input.forEach(function (line) {
            line = line.trim()
            if (line.length === 0) {
                return;
            }
            var property = line.split(':')[0].trim()
            var value = line.split(':')[1].trim()

            data[property] = parseValue(value)
        })
        return data
    }

    function parseValue (command) {
        var elementQueries = command.match(/(?<=\<)([-_#\.a-zA-Z0-9]*?)(?=\>)/g)
        var elements = {}
        var variables = {}
        elementQueries.forEach(function (query) {
            var tag = "<" + query + ">"
            if (query.length === 0 || elements[tag] !== undefined) {
                return;
            }
            elements[tag] = document.querySelector(query)
            var regex = new RegExp("(?<=\\<" + query + "\\>\\.)([a-zA-Z0-9\.]+)", "g");
            var attributes = command.match(regex)
            attributes.forEach(function (attribute) {
                var key = tag + '.' + attribute
                var value = elements[tag]
                var chain = attribute.split('.')
                while (chain.length) {
                    value = value[chain.shift()]
                }
                variables[key] = value
            })
        })
        var expression = command
        Object.keys(variables).forEach(function (key) {
            expression = expression.replaceAll(key, variables[key])
        })
        expression = expression.replaceAll(/\s/g, "")
        expression = expression.replaceAll(/\+\-/g, "-")
        expression = expression.replaceAll(/\-\-/g, "+")

        return sum(expression)
    }

    function sum (s) {
        // https://stackoverflow.com/questions/2276021/evaluating-a-string-as-a-mathematical-expression-in-javascript

        var total = 0
        var s = s.match(/[+\-]*(\.\d+|\d+(\.\d+)?)/g) || []

        while (s.length) {
            total += parseFloat(s.shift())
        }
        return total
    }

    window.addEventListener("resize", loop)
    window.ExetendedDimensionParser = {
        parse: loop
    }
    setInterval(loop, 1500)
    loop()
})()
