/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
this.OpenEyes = this.OpenEyes || {};

(function(exports) {

  var template =  '<h4 class="mdl-dialog__title">{{title}}</h4>' +
    '<div class="mdl-dialog__content">{{content}}</div>' +
    '<div class="mdl-dialog__actions">' +
    '<button type="button" class="mdl-button accept">{{agree}}</button>' +
    '<button type="button" class="mdl-button close">{{disagree}}</button>' +
    '</div>';
  var Dialog = {};

  function render(title, content){
    var dialog = document.createElement("dialog");
    dialog.className = 'mdl-dialog';
    dialog.id = Math.random().toString(36).slice(2);
    dialog.innerHTML = Mustache.render(template, {
      title: title,
      content: content,
      agree: Dialog.agree || "Continue",
      disagree: Dialog.disagree || "Cancel"
    });

    return dialog;
  }

  /**
   * Init a dialog
   *
   * @param container
   * @param trigger
   * @param title
   * @param content
   */
  Dialog.init = function(container, trigger, title, content){
    var dialog = render(title, content);
    var close;

    function handleTrigger(e){
      var target = e.target,
        className = 'modaled';

      if(target.className.indexOf(className) === -1){
        e.preventDefault();
        dialog.showModal();
        target.classList.add(className);
      }
    }

    container.appendChild(dialog);

    if(trigger instanceof HTMLElement){
      trigger.addEventListener('click', handleTrigger);
    }

    close = dialog.getElementsByClassName('close');
    for(var i = 0; i < close.length; i++){
      close[i].addEventListener('click', function(){
        dialog.close()
      });
    }

    accept = dialog.getElementsByClassName('accept');
    for(var i = 0; i < accept.length; i++){
      accept[i].addEventListener('click', function(){
        trigger.click();
      });
    }
  };

  exports.Dialog = Dialog;
}(this.OpenEyes));