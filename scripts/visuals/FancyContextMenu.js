/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * A nice visual menu that opens on left click.
 * @param {type} element
 * @param {type} options - array of objects of type FancyContextMenu.Option
 * @returns {undefined}
 */
function FancyContextMenu (element, options) {
    'use strict'
    Object.defineProperty(this, "element", {
        value: element,
        writable: false
    })

    element.addEventListener("click", function (e) {

    })
}

/**
 * A single option, contains circle icon and callback.
 * The callback is invoked with FancyContextMenu object as parameter
 * @param {type} image
 * @param {type} callback
 * @returns {undefined}
 */
FancyContextMenu.Option = function (imageSrc, callback) {
    'use strict'

}
