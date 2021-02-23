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
