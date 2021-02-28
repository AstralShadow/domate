/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!window.SolveTestGUI) {
    window.SolveTestGUI = {}
}

window.SolveTestGUI.Core = function (oid) {
    "use strict"
    this.oid = oid
    console.log("Your id for this test is " + oid)
    alert("Working on this.")

    new SolveTestGUI.Timer(this)
    new SolveTestGUI.Progress(this)

}
