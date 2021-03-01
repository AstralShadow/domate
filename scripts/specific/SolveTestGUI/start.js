/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker */

if (!window.SolveTestGUI) {
    window.SolveTestGUI = {}
}

window.SolveTestGUI.start = function (key, identification, cb) {
    "use strict"

    StateTracker.get("beginTest", {
        "test": key,
        "identification": identification
    }, function (e) {
        if (e.code === "Success") {
            var oid = e.result
            new window.SolveTestGUI.Core(oid)
        }
        cb(e)
    })

}
