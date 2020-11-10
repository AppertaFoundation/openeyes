/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

(function (exports) {
    function HTMLSettingEditorController(editor_id, options, substitutions) {
        let editor_ref = null;
        this.substitutions = substitutions;
        this.editor_id = editor_id;

        options.toolbar = options.toolbar + ' | styleselect';
        options.object_resizing = true;

        let lineSpacingSelectors = [
            'p','h1','h2','h3','h4','h5','h6','div','ul','ol','td'
        ];

        let lineSpacingSelectorString = lineSpacingSelectors.join(',');

        // Utility function to apply a function to a node and all of its children, recursively
        function forEachNodeRecursively(node, f) {
            f(node);
            for (let i = 0; i < node.children.length; i++) {
                forEachNodeRecursively(node.children[i], f);
            }
        }

        // Commands and formats for which we need to remove the contenteditable attribute during BeforeExecCommand
        // and restore it during ExecCommand
        let restoreEditableCommands = [
            'FontName',
            'FontSize',
            'Bold',
            'Italic',
            'Underline'
        ];
        let lineSpacingFormats = [
            'smalllinespacing',
            'smallmidlinespacing',
            'midlinespacing',
            'midlargelinespacing',
            'largelinespacing',
        ];
        let restoreEditableFormats = [
            'bold',
            'italic',
            'underline',
        ].concat(lineSpacingFormats);

        tinymce.init(Object.assign(
            {
                selector: '#' + editor_id,
                table_default_styles: {
                    'border-collapse': 'separate',
                    'width': '100%',
                    'margin-bottom': '0px',
                    'margin-top': '0px',
                },
                formats: {
                    smalllinespacing: {selector: lineSpacingSelectorString, styles: {'line-height': '0.5'}},
                    smallmidlinespacing: {selector: lineSpacingSelectorString, styles: {'line-height': '0.75'}},
                    midlinespacing: {selector: lineSpacingSelectorString, styles: {'line-height': '1'}},
                    midlargelinespacing: {selector: lineSpacingSelectorString, styles: {'line-height': '1.25'}},
                    largelinespacing: {selector: lineSpacingSelectorString, styles: {'line-height': '1.5'}},
                },
                style_formats: [
                    {title: 'Line Spacing 0.5', format: 'smalllinespacing'},
                    {title: 'Line Spacing 0.75', format: 'smallmidlinespacing'},
                    {title: 'Line Spacing 1.0', format: 'midlinespacing'},
                    {title: 'Line Spacing 1.25', format: 'midlargelinespacing'},
                    {title: 'Line Spacing 1.5', format: 'largelinespacing'},
                ],
                setup: function (editor) {
                    editor.on('keydown', function (e) {
                        if (e.keyCode === 9) {
                            editor.execCommand('mceInsertContent', false, '&emsp;');
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }
                    });

                    editor.on('BeforeExecCommand', function(command) {
                        let selectedNode = editor.selection.getNode();

                        if(command.command === 'mceToggleFormat' && _.includes(lineSpacingFormats, command.value))
                        {
                            let nextSelected = selectedNode;

                            while (nextSelected) {
                                if(nextSelected.tagName === "TD"){
                                    let p = document.createElement('p');
                                    p.innerHTML = nextSelected.innerHTML;
                                    nextSelected.innerHTML = '';
                                    nextSelected.appendChild(p);
                                    editor.selection.select(p);
                                    break;
                                }

                                if (_.includes(lineSpacingSelectors, nextSelected.tagName.toLowerCase())){
                                    break;
                                }

                                nextSelected = nextSelected.parentNode;
                            }
                        }

                        if (_.includes(restoreEditableCommands, command.command)
                            || (command.command === 'mceToggleFormat' && _.includes(restoreEditableFormats, command.value))){
                            forEachNodeRecursively(selectedNode, function(node) {
                                if(node.tagName === 'SPAN' && node.firstChild.tagName !== 'IMG')
                                {
                                    let attribute = node.attributes.getNamedItem("contenteditable");

                                    if (attribute !== null)
                                    {
                                        node.removeAttribute("contenteditable");
                                        node.setAttribute("contentwaseditable", "true");
                                    }
                                }
                            });
                        }
                    });

                    editor.on('ExecCommand', function(command) {
                        let selectedNode = editor.selection.getNode();

                        if (_.includes(restoreEditableCommands, command.command) ||
                            (command.command === 'mceToggleFormat' && _.includes(restoreEditableFormats, command.value))){
                            forEachNodeRecursively(selectedNode, function(node) {
                                if(node.tagName === 'SPAN'){
                                    let attribute = node.attributes.getNamedItem("contentwaseditable");

                                    if (attribute !== null){
                                        node.setAttribute("contenteditable", "false");
                                        node.removeAttribute("contentwaseditable");
                                    }
                                }
                            });
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
            }, options)).then(
            function (editors) {
                editor_ref = editors[0];
            }
        );

        let that = this;
        if (substitutions) {
            $('button.quick-insert').off('click');
            $('button.quick-insert').on('click', function() {
                let key = $(this).attr('data-insert');
                if (key !== '' && key !== 'none_selected') {
                    let value = that.getSubstitution(key);
                    editor_ref.insertContent('<span style="" data-mce-style="" contenteditable="false" data-substitution="' + key + '">' + value + '</span>');
                }
            });
        }
    }

    HTMLSettingEditorController.prototype.getSubstitution = function(key) {
        let value = null;

        if (this.substitutions && this.substitutions[key] != null) {
            value = this.substitutions[key].value;
        }

        if (value === null || value === '') {
            value = '<span>[' + key + ']</span>';
        }

        return value;
    };

    exports.HTMLSettingEditorController = HTMLSettingEditorController;
})(OpenEyes);
