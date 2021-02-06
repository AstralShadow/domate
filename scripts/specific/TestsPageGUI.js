/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker */

(function () {
    "use strict"
    /* Flags */
    const logCreateCommands = false
    const logDownloadData = true

    var testsBaseContainer = document.querySelector("#testsContainer")
    var testsContainer = testsBaseContainer.querySelector(".contents")
    var newTestButton = testsBaseContainer.querySelector(".newElementButton")

    var exerciseGroupsBaseContainer = document.querySelector("#exerciseGroupsContainer")
    var exerciseGroupsContainer = exerciseGroupsBaseContainer.querySelector(".contents")
    var newExerciseGroupButton = exerciseGroupsBaseContainer.querySelector(".newElementButton")

    var exercisesBaseContainer = document.querySelector("#exercisesContainer")
    var exercisesContainer = exercisesBaseContainer.querySelector(".contents")
    var newExerciseButton = exercisesBaseContainer.querySelector(".newElementButton")

    var editTestMenu = document.querySelector("#editTest")
    var editExerciseGroupMenu = document.querySelector("#editExerciseGroup")
    var editExerciseMenu = document.querySelector("#editExercise")

    StateTracker.track("listTests", null, function (event) {
        event.result.forEach(trackTest)
    })
    StateTracker.track("listExerciseGroups", null, function (event) {
        event.result.forEach(trackExerciseGroup)
    })
    StateTracker.track("listExercises", null, function (event) {
        event.result.forEach(trackExercise)
    })

    /* Tracking elements */
    var trackedTests = {}
    var trackedExerciseGroups = {}
    var trackedExercises = {}
    function trackTest (oid) {
        if (Object.keys(trackedTests).indexOf(oid) === -1) {
            StateTracker.track('testData', {id: oid}, function (e) {
                var test = e.result
                trackedTests[test._id] = test
                if (logDownloadData) {
                    console.log("test", test._id, test)
                }
            })
            trackedTests[oid] = null
        } else {
            StateTracker.reloadTracker('testData', {id: oid})
        }
    }
    function trackExerciseGroup (oid) {
        if (Object.keys(trackedExerciseGroups).indexOf(oid) === -1) {
            StateTracker.track('exerciseGroupData', {id: oid}, function (e) {
                var group = e.result
                trackedExerciseGroups[group._id] = group
                if (logDownloadData) {
                    console.log("exerciseGroup", group._id, group)
                }
            })
            trackedExerciseGroups[oid] = null
        } else {
            StateTracker.reloadTracker('exerciseGroupData', {id: oid})
        }
    }
    function trackExercise (oid) {
        if (Object.keys(trackedExercises).indexOf(oid) === -1) {
            StateTracker.track('exerciseData', {id: oid}, function (e) {
                var exercise = e.result
                trackedExercises[exercise._id] = exercise
                if (logDownloadData) {
                    console.log("exerciseData", exercise._id, exercise)
                }
            })
            trackedExercises[oid] = null
        } else {
            StateTracker.reloadTracker('exerciseData', {id: oid})
        }
    }

    /* Creating */
    async function createTest () {
        var e = await StateTracker.get("createTest")
        if (e.code !== "Success") {
            throw "Couldn't create test";
        }
        var oid = e.result.id
        if (logCreateCommands) {
            console.log("created test", oid)
        }
        trackTest(oid)
    }
    async function createExerciseGroup () {
        var e = await StateTracker.get("createExerciseGroup")
        if (e.code !== "Success") {
            throw "Couldn't create exercise group";
        }
        var oid = e.result.id
        if (logCreateCommands) {
            console.log("created exercise group", oid)
        }
        trackExerciseGroup(oid)
    }
    async function createExercise () {
        var e = await StateTracker.get("createExercise")
        if (e.code !== "Success") {
            throw "Couldn't create exercise";
        }
        var oid = e.result.id
        if (logCreateCommands) {
            console.log("created exercise", oid)
        }
        trackExercise(oid)
    }

    newTestButton.addEventListener("click", function () {
        createTest()
    })
    newExerciseGroupButton.addEventListener("click", function () {
        createExerciseGroup()
    })
    newExerciseButton.addEventListener("click", function () {
        createExercise()
    })
})()

