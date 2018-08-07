/**
 * CodeSample.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

import DOMUtils from 'tinymce/core/api/dom/DOMUtils';
import Prism from './Prism';
import Utils from '../util/Utils';

const getSelectedCodeSample = function (editor) {
  const node = editor.selection.getNode();

  if (Utils.isCodeSample(node)) {
    return node;
  }

  return null;
};

const insertCodeSample = function (editor, language, code) {
  editor.undoManager.transact(function () {
    const node = getSelectedCodeSample(editor);

    code = DOMUtils.DOM.encode(code);

    if (node) {
      editor.dom.setAttrib(node, 'class', 'language-' + language);
      node.innerHTML = code;
      Prism.highlightElement(node);
      editor.selection.select(node);
    } else {
      editor.insertContent('<pre id="__new" class="language-' + language + '">' + code + '</pre>');
      editor.selection.select(editor.$('#__new').removeAttr('id')[0]);
    }
  });
};

const getCurrentCode = function (editor) {
  const node = getSelectedCodeSample(editor);

  if (node) {
    return node.textContent;
  }

  return '';
};

export default {
  getSelectedCodeSample,
  insertCodeSample,
  getCurrentCode
};