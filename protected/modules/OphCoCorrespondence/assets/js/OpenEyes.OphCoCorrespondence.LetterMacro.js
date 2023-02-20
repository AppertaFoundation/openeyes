/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = OpenEyes || {};
OpenEyes.OphCoCorrespondence = OpenEyes.OphCoCorrespondence || {};

(function (exports) {

    function LetterMacroController(editor_id, options) {
        var controller = this;

        var tinymce_options = Object.assign(
            options,
            {
                selector: '#' + editor_id,
                plugins: 'hr lists table paste code pagebreak',
                toolbar: 'undo redo | fontselect fontsizeselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | table | subtitle | labelitem | label-r-l | inputcheckbox | pagebreak hr code',
                setup: function (editor) {

                    editor.on('keydown', function (e) {
                        if (e.keyCode === 9) {
                            editor.execCommand('mceInsertContent', false, '&emsp;');
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }
                    });

                    /*
                    Sets up a common letter stucture using a table.
                    Layout can NOT be achieved using TABs.
                    note: style="width:100%" sets Tiny to use %!
                    */
                    editor.addButton('labelitem', {
                        text: 'Label - Item',
                        icon: false,
                        tooltip: "Use TAB to create new rows",
                        onpostrender: monitorNodeChange,
                        onclick: function () {
                            editor.insertContent('<table class="label-item" style="width:100%"><tbody><tr><th>Label</th><td>(use tab to add extra rows)</td></tr></tbody></table>');
                        }
                    });

                    /*
                    Sets up a common letter stucture using a table.
                    Layout can NOT be achieved using TABs.
                    */
                    editor.addButton('label-r-l', {
                        text: 'Label - R - L',
                        icon: false,
                        tooltip: "Right Left data",
                        onpostrender: monitorNodeChange,
                        onclick: function () {
                            editor.insertContent('<table class="label-r-l" style="width:100%"><tbody><tr><th>Label</th><td>Right</td><td>Left</td></tr></tbody></table>');
                        }
                    });

                    /*
                    Set up some table defaults.
                    Not using table matrix button so that i can control the DOM
                    note: style="width:100%" sets Tiny to use %!
                    */
                    editor.addButton('datatable', {
                        text: 'Table',
                        icon: false,
                        tooltip: "Insert a table",
                        onpostrender: monitorNodeChange,
                        onclick: function () {
                            editor.insertContent('<table class="borders" style="width:100%"><tbody><tr><td></td><td></td><td></td></tr></tbody></table>');
                        }
                    });

                    /*
                    Only allow 1 header style: <h4>
                    */
                    editor.addButton('subtitle', {
                        text: 'Subtitle',
                        icon: false,
                        onclick: function () {
                            editor.insertContent('<h4 class="subtitle">Subtitle</h4>');
                        }
                    });

                    /*
                    Inserts input of type checkbox into the editor.
                   */
                    editor.addButton('inputcheckbox', {
                        text: 'CheckBox',
                        icon: false,
                        tooltip: "",
                        onclick: function () {
                            editor.insertContent('<p><input type="checkbox"/>Input text here</p>');
                        }
                    });

                    /*
                    Disable custom table creation within tables.
                    Limited use. User can create a <p> inside <td> by Enter.
                    Then they can add another table
                    */
                    function monitorNodeChange() {
                        var btn = this;
                        editor.on('NodeChange', function (e) {
                            var nodeName = e.element.nodeName.toLowerCase();
                            btn.disabled(nodeName === 'td' || nodeName === 'th');
                        });
                    }

                    // This function works for checkboxes
                    editor.on('init', function() {
                        // Get the content of the
                        $(editor.getBody()).on("change", ":checkbox", function(el) {
                            if (el.target.checked) {
                                $(el.target).attr('checked', 'checked');
                            } else {
                                $(el.target).removeAttr('checked');
                            }
                        });
                        // Selects
                        $(editor.getBody()).on("change", "select", function(el) {
                            $(el.target).children('option').each(function() {
                                if(this.selected){
                                    $( this ).attr('selected','selected');
                                }else{
                                    $( this ).removeAttr('selected');
                                }
                            });
                        });
                        // Radio button
                        $(editor.getBody()).on("change", ":radio", function(el) {
                            // On changing the state of the radio button,
                            // First, remove the checked attribute from the radio group (i.e. all the radio buttons with the same name)
                            // then add it back only to the radio button that was clicked.
                            $(editor.dom.getRoot()).find('input[type=radio][name=' + el.target.name + ']').each(function() {
                                $(this).attr('checked', false);
                            });
                            $(el.target).attr('checked', true);
                        });
                    });
                }
            }
        );

        tinyMCE.init(tinymce_options)
            .then(
                function (editors) {
                    controller.editor = editors[0];

                    if(typeof OE_patient_id !== "undefined"){
                        controller.postInit();
                    }

                }
            );
    }

    LetterMacroController.prototype.postInit = function () {
        var controller = this;

        var typingTimer;
        var waitInterval = 500;
        //Find and replace shortcodes (square brackets surrounding 3 lower case letters: [abc])
        this.editor.on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(expandShortcodes, waitInterval);
        });

        this.editor.on('keydown', function () {
            clearTimeout(typingTimer);
        });

        function expandShortcodes() {
            var m = controller.editor.getContent().match(/\[([a-z]{3})\]/i);
            if (m) {
                var text = controller.editor.getContent();
                $.ajax({
                    'type': 'POST',
                    'url': baseUrl + '/OphCoCorrespondence/Default/expandStrings',
                    'data': {'patient_id': OE_patient_id, 'text': text, 'YII_CSRF_TOKEN': YII_CSRF_TOKEN},
                    'success': function (resp) {
                        if (resp) {
                            controller.setContent(resp);
                        }
                    }
                });
            }
        }
    };

    LetterMacroController.prototype.connectDropdown = function (dropdown) {
        dropdown.on('change', function () {
            if ($(this).val() !== '') {
                addAtCursor('[' + $(this).val() + ']');
                $(this).val('');
            }
        });
    };

    LetterMacroController.prototype.connectChildDropdowns = function (parent) {
        parent.find(selector).each(function () {
            connectDropdown(this);
        });
    };

    LetterMacroController.prototype.addAtCursor = function (content) {
        if (this.editor.getContent() === '') {
            content = htmlUpperFirst(content);
        }
        this.editor.execCommand('mceInsertContent', false, content);
    };

    LetterMacroController.prototype.setContent = function (content) {
        if (this.editor.getContent() === '') {
            content = htmlUpperFirst(content);
        }
        this.editor.setContent(content);
    };

    function htmlUpperFirst(str) {
        //Find the position of the fist char of text
        var match = str.match(/[^\s]/); //match first non white-space char
        var charPos = match.index;
        //Is this html or just a string?
        if (match[0] === '<') {
            //Find first char of text (not white-space, not tags)
            match = str.match(/>\s*[^<\s]/);
            if (match === null) {
                return str;
            }
            //The char we want to change is at the end of this regex match
            charPos = match.index + match.length;
        }

        //Uppercase the first letter
        return str.substr(0, charPos) +
            str.charAt(charPos).toUpperCase() +
            str.substr(charPos + 1);
    }

    LetterMacroController.prototype.getContent = function () {
        return this.editor.getContent();
    };

    LetterMacroController.prototype.appendContent = function (content) {
        this.setContent(this.getContent() + content);
    };

    exports.LetterMacroController = LetterMacroController;
})(OpenEyes.OphCoCorrespondence);
