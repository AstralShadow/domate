/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker */

(function () {
    "use strict"
    var testsBaseContainer = document.querySelector("#testsContainer")
    var testsContainer = testsBaseContainer.querySelector(".contents")
    var newTestButton = testsBaseContainer.querySelector(".newElementButton")
    var editTestMenu = document.querySelector("#editTest")

    StateTracker.track("listTests", null, renderTests)

    function renderTests (event) {
        event.result.forEach(downloadTest)
    }

    async function downloadTest (oid) {
        var a = await StateTracker.get('testData', {id: oid})
        console.log(a)
    }

})()

