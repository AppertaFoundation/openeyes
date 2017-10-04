/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
this.OpenEyes = this.OpenEyes || {};

(function (exports) {

  var template =  '<h4 class="mdl-dialog__title">{{title}}</h4>' +
    '<div class="mdl-dialog__content">{{content}}</div>' +
    '<div class="mdl-dialog__actions">' +
    '<button type="button" class="mdl-button accept">{{agree}}</button>' +
    '<button type="button" class="mdl-button close">{{disagree}}</button>' +
    '</div>';
  var Dialog = {};
  var clickEvents = ['click', 'touchstart'];

  function render(title, content) {
    var dialog = document.createElement("dialog");
    dialog.className = 'mdl-dialog';
    dialog.id = Math.random().toString(36).slice(2);
    dialog.innerHTML = Mustache.render(template, {
      title: title,
      content: content,
      agree: Dialog.agree || "Continue",
      disagree: Dialog.disagree || "Cancel"
    });

    //if the polyfil is available use it
    if (window.hasOwnProperty('dialogPolyfill')) {
      dialogPolyfill.registerDialog(dialog);
    }

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
  Dialog.init = function (container, trigger, title, content) {
    var dialog = render(title, content),
      close,
      accept,
      i,
      className = 'modaled';

    function handleTrigger(e) {
      var target = e.target;

      if (target.className.indexOf(className) === -1) {
        e.preventDefault();
        dialog.showModal();
        target.classList.add(className);
      }
    }

    container.appendChild(dialog);

    if (trigger instanceof HTMLElement) {
      trigger.addEventListener('click', handleTrigger);
    }

    close = dialog.getElementsByClassName('close');
    for (i = 0; i < close.length; i++) {
      clickEvents.forEach(function (eventType) {
        close[i].addEventListener(eventType, function () {
          trigger.classList.remove(className);
          dialog.close();
        });
      });
    }

    accept = dialog.getElementsByClassName('accept');
    for (i = 0; i < accept.length; i++) {
      clickEvents.forEach(function(eventType) {
        accept[i].addEventListener(eventType, function () {
          trigger.click();
        });
      });
    }
  };

  exports.Dialog = Dialog;
}(this.OpenEyes));