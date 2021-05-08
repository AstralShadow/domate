/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!window.SolveTestGUI) {
    throw "SolveTestGUI aint initialized"
}

window.SolveTestGUI.Tasks = function (core) {
    "use strict"

    /* Tracking */
    this.onLoad = function () {
        container.style.display = "block"
    }
    this.onTaskLoad = function (key) {
        updateTask(key)
    }

    /* Functionality */
    async function setAnswer (oid, answer) {
        var task = core.getTask(oid)
        if (answer !== task.answer) {
            var token = await window.SolveTestGUI.getToken()
            var input = {
                token: token,
                answer: answer
            }

            var request = new XMLHttpRequest()
            request.open("PUT", window.SolveTestGUI.endpoint + "/" + oid)
            request.setRequestHeader("Content-type", "application/json")
            request.send(JSON.stringify(input))

            request.addEventListener("load", function () {
                if (request.status !== 200) {
                    console.log("Failed answering ", oid, request.status, request.response)
                    return;
                }
                core.onAnswer(oid)
            })
        }
    }

    /* Rendering */
    var container = document.querySelector("#testContents")
    function fillElements () {
        while (container.firstChild) {
            container.removeChild(container.firstChild)
        }
        core.task_keys.forEach(function (oid) {
            if (nodes[oid]) {
                container.appendChild(nodes[oid].base)
            }
        })
    }


    var nodes = {}
    function createNode () {
        var base = document.createElement("div")
        var label = document.createElement("label")
        var question = document.createElement("div")
        var answerWrap = document.createElement("div")
        //var workspace = document.createElement("div")
        var input = document.createElement("textarea")

        base.className = "task"
        question.className = "question mathjax"
        answerWrap.className = "textarea answer"
        //workspace.className = "workarea mathjax"
        input.className = "textarea answerInput"

        //answerWrap.appendChild(workspace)
        answerWrap.appendChild(input)
        input.addEventListener("input", function () {
            // workspace.innerText = input.value
            if (MathJax.typeset) {
                MathJax.typeset()
            }
        })
        label.appendChild(question)
        label.appendChild(answerWrap)
        base.appendChild(label)
        var ptrs = {
            base: base,
            question: question,
            //displat: workspace,
            input: input
        }
        return ptrs
    }
    function updateTask (oid) {
        var ptrs = nodes[oid]
        if (ptrs === undefined) {
            ptrs = createNode()
            ptrs.input.addEventListener("blur", function () {
                setAnswer(oid, ptrs.input.value)
            })
            ptrs.oid = oid
            nodes[oid] = ptrs
            fillElements()
        }

        if (!core.getTask(oid)) {
            return;
        }
        var task = core.getTask(oid)

        ptrs.question.innerText = task.question
        ptrs.input.value = task.answer
//        ptrs.display.innerText = tasks[oid].answer
        if (MathJax.typeset) {
            MathJax.typeset()
        }
    }

    this.disableInput = function () {
        Object.values(nodes).forEach(function (ptr) {
            ptr.input.disabled = "true"
            if (ptr.input.value !== "") {
                setAnswer(ptr.oid, ptr.input.value)
            }
        })
    }
}
