/**
 * FilterContent.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

import Prism from './Prism';
import Utils from '../util/Utils';

const setup = function (editor) {
  const $ = editor.$;

  editor.on('PreProcess', function (e) {
    $('pre[contenteditable=false]', e.node).
      filter(Utils.trimArg(Utils.isCodeSample)).
      each(function (idx, elm) {
        const $elm = $(elm), code = elm.textContent;

        $elm.attr('class', $.trim($elm.attr('class')));
        $elm.removeAttr('contentEditable');

        $elm.empty().append($('<code></code>').each(function () {
          // Needs to be textContent since innerText produces BR:s
          this.textContent = code;
        }));
      });
  });

  editor.on('SetContent', function () {
    const unprocessedCodeSamples = $('pre').filter(Utils.trimArg(Utils.isCodeSample)).filter(function (idx, elm) {
      return elm.contentEditable !== 'false';
    });

    if (unprocessedCodeSamples.length) {
      editor.undoManager.transact(function () {
        unprocessedCodeSamples.each(function (idx, elm) {
          $(elm).find('br').each(function (idx, elm) {
            elm.parentNode.replaceChild(editor.getDoc().createTextNode('\n'), elm);
          });

          elm.contentEditable = false;
          elm.innerHTML = editor.dom.encode(elm.textContent);
          Prism.highlightElement(elm);
          elm.className = $.trim(elm.className);
        });
      });
    }
  });
};

export default {
  setup
};