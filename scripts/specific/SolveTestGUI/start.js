/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (window.SolveTestGUI === undefined) {
    throw "SolveTestGUI aint initialized"
}

window.SolveTestGUI.start = async function (identification, cb) {
    "use strict"

    console.log("STARTIN")
    startOrResume()

    function startOrResume () {
        var request = new XMLHttpRequest()
        request.open("GET", window.SolveTestGUI.endpoint)
        request.send()

        request.addEventListener("load", function () {
            var data = JSON.parse(request.response)

            if (request.status === 200) {
                if (data.data.joined) {
                    handleTestData(request)
                } else {
                    start()
                }
            } else {
                cb(data)
            }
        })
    }
    async function start () {
        var input = {
            "token": await window.SolveTestGUI.getToken(),
            "identification": identification
        }

        var request = new XMLHttpRequest()
        request.open("PUT", window.SolveTestGUI.endpoint)
        request.setRequestHeader("Content-type", "application/json")
        request.send(JSON.stringify(input))

        request.addEventListener("load", function () {
            handleTestData(request)
        })
    }

    function handleTestData (request) {
        var data = JSON.parse(request.response);
        if (request.status === 200) {
            new window.SolveTestGUI.Core(data.data)
        }
        cb(data)
    }



}
