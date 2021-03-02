/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global StateTracker */

if (!window.SolveTestGUI) {
    window.SolveTestGUI = {}
}

window.SolveTestGUI.Tasks = function (core) {
    "use strict"

    /* Tracking */
    StateTracker.track("getExamData", {id: core.oid}, examDataHandler)
    var lastData = undefined
    function examDataHandler (e) {
        lastData = e.result
        e.result.tasks.forEach(trackTask)
        container.style.display = "block"
    }
    var tasks = {}
    function trackTask (oid) {
        if (Object.keys(tasks).indexOf(oid) === -1) {
            tasks[oid] = undefined
            StateTracker.track("getExamQuestion", {id: oid}, taskUpdateHandler)
        }
    }
    function taskUpdateHandler (e) {
        tasks[e.args.id] = e.result
        updateTask(e.args.id)
    }

    /* Functionality */
    async function setAnswer (oid, answer) {
        if (answer !== tasks[oid].answer) {
            await StateTracker.get("answerExamQuestion", {id: oid, answer: answer})
            StateTracker.reloadTracker("getExamQuestion", {id: oid})
        }
    }

    /* Rendering */
    var container = document.querySelector("#testContents")
    function fillElements () {
        while (container.firstChild) {
            container.removeChild(container.firstChild)
        }
        lastData.tasks.forEach(function (oid) {
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

        if (!tasks[oid]) {
            return;
        }

        ptrs.question.innerText = tasks[oid].question
        ptrs.input.value = tasks[oid].answer
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
