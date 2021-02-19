/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


window.MathJax = {
    loader: {
        load: [
            'input/asciimath',
            'output/chtml'
        ]
    },
    options: {
        skipHtmlTags: [
            'script', 'noscript', 'input',
            'style', 'textarea', 'pre', 'code',
            'annotation', 'annotation-xml'
        ],
        processHtmlClass: "mathjax",
        ignoreHtmlClass: "nomathjax"
    },
    chtml: {
        minScale: 1
    }
};

(function () {
    'use strict'
    var input = document.querySelector('#exerciseInput')
    var display = document.querySelector('#a')

    function render () {
        var a = input.innerText
        while (a.indexOf('<') !== - 1)
            a = a.replace('<', '&lt;')
        display.innerText = a
        if (MathJax.typeset) {
            MathJax.typeset()
        }
    }

    input.addEventListener('input', function () {
        render();
    })
    render();
})();

(function () {
    var shortcuts = [
        'sqrt(x)',
        'root(x)(y)',
        'sum_(i=1)^n i^y',
        'int_i^a(b)',
        'log_i^a(b)'
    ]
})();
