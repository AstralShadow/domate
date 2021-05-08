/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (window.SolveTestGUI === undefined) {
    throw "SolveTestGUI aint initialized"
}

window.SolveTestGUI.getToken = function () {
    return new Promise(function (resolve) {
        var request = new XMLHttpRequest()
        request.open("GET", "solve/get-token")
        request.send()

        request.addEventListener("load", function () {
            var data = JSON.parse(request.response)
            if (data.token !== undefined) {
                resolve(data.token)
            } else {
                console.log("Couldn't accure token. Maybe your session expired")
            }
        })
    })
}