/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker, logDownloadTestData, logCreateCommands, noTestDescription, noTestName, FancyContextMenu */

/* Dependencies and constants */
if (!window.TestsPageGUI) {
    TestsPageGUI = {}
}
if (!window.TestsPageGUI.Container) {
    throw "TestsPageGUI.Container is a dependency of displaying groups"
}

(function () {
    'use strict'
    var startTestDiv, showStartTestDiv, hideStartTestDiv
    var container = new TestsPageGUI.Container({
        /* Constants */
        type: 'test',
        containerQuery: "#testsContainer",
        endpoint: "exam",
        evCreated: "new_exam",
        evModified: "modified_exam",
        evDeleted: "deleted_exam",
        noName: TestsPageGUI.no_test_name,
        noDescription: TestsPageGUI.no_test_description,

        /* Functionality */
        functions: [
            ["img/m1.png:Редактирай",
                function (oid) {
                    TestsPageGUI.editTest(oid)
                }],
            ["img/delete.png:Изтрии",
                function (oid) {
                    // TODO: use some more nice askbox
                    var div = document.getElementById("delNotification")
                    var del = document.getElementById("delButton")
                    var nodel = document.getElementById("noDelButton")
                    var self = this
                    div.style.display = "block"

                    del.onclick = function (e) {
                        self.remove(oid)
                        div.style.display = "none"
                        e.stopPropagation()
                    }
                    nodel.onclick = function (e) {
                        div.style.display = "none"
                        e.stopPropagation()
                    }
                }
            ],
            ["img/list.png:Раздай",
                function (oid) {
                    showStartTestDiv(oid)
                }
            ],
            ["img/tick.png:Провери",
                function (oid) {
                    //console.log("use not used yet")
                    // *use* function
                    window.TestsPageGUI.showAllExams(oid)
                }
            ]
        ],
        oncreate: TestsPageGUI.editTest,
        //contextHint: "img/Firefox-OS-tan.png"
    })
    container.activate()

    window.addEventListener("load", function () {
        var lastClickEvent
        startTestDiv = document.querySelector("#startTest")
        startTestDiv.addEventListener("mousedown", function (e) {
            lastClickEvent = e
        })
        document.addEventListener("mousedown", function (e) {
            if (lastClickEvent !== e) {
                hideStartTestDiv()
            }
        })
        showStartTestDiv = function (oid) {
            startTestDiv.style.display = "block"
            fill(oid)
        }
        hideStartTestDiv = function () {
            startTestDiv.style.display = "none"
        }
        var data = null
        function fill (oid) {
            data = container.get(oid)
            if (data.name) {
                document.getElementById("ST_name")
                    .innerText = data.name
            } else {
                document.getElementById("ST_name")
                    .innerHTML = TestsPageGUI.noTestName
            }
            document.getElementById("ST_description")
                .innerText = data.description
            document.getElementById("ST_tasks")
                .innerText = data.contents.length
            document.getElementById("ST_feedback").innerText = ""
            startE.value = now()
        }
        var startE = startTestDiv.querySelector("[name=start]")
        var endE = startTestDiv.querySelector("[name=end]")
        var worktimeE = startTestDiv.querySelector("[name=worktime]")
        var noteE = startTestDiv.querySelector("#ST_note")
        var idE = startTestDiv.querySelector("#ST_identification")
        document.getElementById("ST_button")
            .addEventListener("click", async function () {
                var id = data["_id"];
                var input = {
                    token: await getToken(),
                    start: (new Date(startE.value)).getTime() / 1000,
                    end: (new Date(endE.value)).getTime() / 1000,
                    worktime: worktimeE.value || def_worktime(),
                    note: noteE.value,
                    question: idE.value || "Име на ученик:"
                }

                var request = new XMLHttpRequest()
                request.open("POST", "exam/" + id + "/active")
                request.setRequestHeader("Content-type", "application/json")
                request.send(JSON.stringify(input))

                request.addEventListener("load", function () {
                    if (request.status !== 200) {
                        console.log("Failed activating test ", request.status, request.response)
                        return;
                    }

                    var data = JSON.parse(request.response)
                    console.log(data.key, data.id)

                    var msg = TestsPageGUI.successful_test_schedule
                    var url = location.href.replace(/([^/\\]*\.php)?(\?.*)?$/, "")
                    var link = url + data.key
                    document.getElementById("ST_feedback").innerHTML = msg.replace(/\$link/g, link)
                    console.log(data)
                })
            })
        function getToken () {
            return new Promise(function (resolve) {
                var request = new XMLHttpRequest()
                request.open("GET", "exam/get-token")
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
        startE.addEventListener("input", function () {
            if (endE.valueAsNumber)
                startE.valueAsNumber = Math.min(startE.valueAsNumber, endE.valueAsNumber - 60000)
            worktimeE.placeholder = def_worktime()
            worktimeE.max = def_worktime()
        })
        endE.addEventListener("input", function () {
            if (startE.valueAsNumber)
                endE.valueAsNumber = Math.max(endE.valueAsNumber, startE.valueAsNumber + 60000)
            worktimeE.placeholder = def_worktime()
            worktimeE.max = def_worktime()
        })

        function setmin () {
            var timestamp = now()
            startE.min = timestamp
            endE.min = timestamp
        }
        setInterval(setmin, 30000)
        setmin()

        function def_worktime () {
            var start = startE.valueAsNumber
            var end = endE.valueAsNumber
            return Math.floor((end - start) / 60000) || 40
        }
        function now () {
            // https://stackoverflow.com/questions/30166338/setting-value-of-datetime-local-from-date#61082536
            var dateVal = new Date()
            var day = dateVal.getDate().toString().padStart(2, "0")
            var month = (1 + dateVal.getMonth()).toString().padStart(2, "0")
            var hour = dateVal.getHours().toString().padStart(2, "0")
            var minute = dateVal.getMinutes().toString().padStart(2, "0")
            var sec = dateVal.getSeconds().toString().padStart(2, "0")
            var ms = dateVal.getMilliseconds().toString().padStart(3, "0")
            var inputDate = dateVal.getFullYear() + "-" + (month) + "-" + (day)
                + "T" + (hour) + ":" + (minute) + ":00" //+ (sec) + "." + (ms)

            return inputDate
        }

    })
})()
