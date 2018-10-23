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

(function(exports){
  function LetterMacroController(editor_id, options){
    var tinymce_options = Object.assign(
      {
        selector: '#'+editor_id,
				setup : function(ed) {
					ed.on('keydown', function(e) {
						console.debug('Key up event: ' + e.keyCode);
						if (e.keyCode === 9){
							ed.execCommand('mceInsertContent', false, '&emsp;');
							e.preventDefault();
							e.stopPropagation();
							return false;
						}
					});
				}
      },
      options
    );
    controller = this;
    tinyMCE.init(tinymce_options)
      .then(
        function(editors){
          controller.editor = editors[0];
          controller.postInit();
        }
      );
  }

  LetterMacroController.prototype.postInit = function(){
    controller = this;

    var typingTimer;
    var waitInterval = 500;
    //Find and replace shortcodes (square brackets surrounding 3 lower case letters: [abc])
    this.editor.on('keyup', function() {
      clearTimeout(typingTimer);
      typingTimer = setTimeout(expandShortcodes, waitInterval);
    });

    this.editor.on('keydown', function() {
      clearTimeout(typingTimer);
    });

    function expandShortcodes(){
      var m = controller.editor.getContent().match(/\[([a-z]{3})\]/i);
      if (m) {
        var text = controller.editor.getContent();
        $.ajax({
          'type': 'POST',
          'url': baseUrl+'/OphCoCorrespondence/Default/expandStrings',
          'data': {'patient_id':OE_patient_id, 'text':text, 'YII_CSRF_TOKEN': YII_CSRF_TOKEN},
          'success': function(resp) {
            if (resp) {
              controller.setContent(resp);
            }
          }
        });
      }
    }
  };

  LetterMacroController.prototype.connectDropdown = function(dropdown){
    dropdown.on('change', function(){
      if ($(this).val() !== '') {
        addAtCursor('['+$(this).val()+']');
        $(this).val('');
      }
    });
  };

  LetterMacroController.prototype.connectChildDropdowns = function(parent){
    parent.find(selector).each(function(){connectDropdown(this);});
  };

  LetterMacroController.prototype.addAtCursor = function(content){
    if (this.editor.getContent() === ''){
      content = htmlUpperFirst(content);
    }
    this.editor.execCommand('mceInsertContent', false, content);
  };

  LetterMacroController.prototype.setContent = function(content){
    if (this.editor.getContent() === ''){
      content = htmlUpperFirst(content);
    }
    this.editor.setContent(content);
  };

  function htmlUpperFirst(str){
    //Find the position of the fist char of text
    var match = str.match(/[^\s]/); //match first non white-space char
    var charPos = match.index;
    //Is this html or just a string?
    if (match[0] === '<'){
      //Find first char of text (not white-space, not tags)
      match = str.match(/>\s*[^<\s]/);
      //The char we want to change is at the end of this regex match
      charPos = match.index + match.length;
    }

    //Uppercase the first letter
    return str.substr(0, charPos) +
        str.charAt(charPos).toUpperCase() +
        str.substr(charPos + 1);
  }

  LetterMacroController.prototype.getContent = function(){
    return this.editor.getContent();
  };

  LetterMacroController.prototype.appendContent = function(content){
    this.setContent(this.getContent() + content);
  };

  exports.LetterMacroController = LetterMacroController;
})(OpenEyes.OphCoCorrespondence);