/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!window.TestsPageGUI) {
    TestsPageGUI = {}
}
if (!window.TestsPageGUI.ContentListEditor) {
    throw "TestsPageGUI.ContentListEditor is a dependency of TestPageGUI.editTest"
}

/**
 * Addes TestsPageGUI.editTest(oid) function
 * @param {type} window
 * @returns {undefined}
 */
(function (window) {
    'use strict'
    const TestPageGUI = window.TestsPageGUI
    const ContentListEditor = TestPageGUI.ContentListEditor
    const shortcuts = [
        '(a)/(b)',
        'sqrt(x)',
        'root(x)(y)',
        'sum_(i=1)^n i',
        'int_i^a(b)',
        'log_i^a(b)'
    ]

    var options = {
        type: "exercise",
        dataURL: "exerciseData",
        modifyURL: "modifyExercise",
        pageQuery: "#exerciseExitorPage",
        nameQuery: "#exerciseName",
        descriptionQuery: "#exerciseDescription",
        swidingDirection: "right",

        //workspaceQuery: "#exerciseWorkspace",
        mathDisplayQuery: '#exerciseDisplay',
        mathInputQuery: '#exerciseInput',

        settingsQuery: "#exerciseSettings",
        answerQuery: "#exerciseAnswer",
        sideboardQuery: "#exerciseSideboard"
    }

    function createEditor (oid) {
        var copyOptions = {}
        Object.assign(copyOptions, options)
        copyOptions.onclose = function () {
            if (TestPageGUI.lastFocusedGroup) {
                TestsPageGUI.editGroup(TestPageGUI.lastFocusedGroup)
            } else {
                TestsPageGUI.showHelp("main")
            }
        }
        TestPageGUI.lastFocusedExercise = oid
        return new TestsPageGUI.ExerciseEditor(oid, copyOptions)
    }

    /* Initialization */
    TestsPageGUI.editExercise = async function (oid) {
        if (TestsPageGUI.activeEditor) {
            var type = TestsPageGUI.activeEditor.type
            if (type === "group") {
                TestsPageGUI.activeEditor.onclose = null
            }
            var promise = TestsPageGUI.activeEditor.deactivate()
            if (type === "exercise") {
                await promise
            }
        }

        setTimeout(function () {
            TestsPageGUI.showHelp("exerciseEditor")
            TestsPageGUI.activeEditor = createEditor(oid)
            window.ExetendedDimensionParser.parse()
        }, 0)
    }

    window.addEventListener("load", function () {
        const sideboard = document.querySelector(options.sideboardQuery)
        const input = document.querySelector(options.mathInputQuery)
        shortcuts.forEach(function (content) {
            var box = document.createElement("div")
            box.className = "mathjax box"
            sideboard.appendChild(box)
            box.innerText = "`" + content + "`"
            box.addEventListener("mousedown", function () {
                input.innerText += '`' + content + '`'
                if (TestsPageGUI.activeEditor) {
                    if (TestsPageGUI.activeEditor.type === "exercise") {
                        TestsPageGUI.activeEditor.renderMath()
                    }
                }
            })
        })
        if (MathJax.typeset) {
            MathJax.typeset()
        }

        function insertTextAtCaret (text) {
            //https://stackoverflow.com/questions/2920150/insert-text-at-cursor-in-a-content-editable-div
            var sel, range;
            if (window.getSelection) {
                sel = window.getSelection();
                if (!sel.baseNode || input !== sel.baseNode.parentNode) {
                    return;
                }
                if (sel.getRangeAt && sel.rangeCount) {
                    range = sel.getRangeAt(0);
                    range.deleteContents();
                    range.insertNode(document.createTextNode(text));
                }
            } else if (document.selection && document.selection.createRange) {
                // document.selection.createRange().text = text;
            }
        }
    })

})(window)

// TestsPageGUI.editExercise("6019231b0d33b44216509485")
