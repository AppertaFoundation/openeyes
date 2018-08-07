/**
 * Buttons.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

const toggleActiveState = function (editor, enabledState) {
  return function (e) {
    const ctrl = e.control;

    ctrl.active(enabledState.get());

    editor.on('VisualBlocks', function (e) {
      ctrl.active(e.state);
    });
  };
};

const register = function (editor, enabledState) {
  editor.addButton('visualblocks', {
    active: false,
    title: 'Show blocks',
    cmd: 'mceVisualBlocks',
    onPostRender: toggleActiveState(editor, enabledState)
  });

  editor.addMenuItem('visualblocks', {
    text: 'Show blocks',
    cmd: 'mceVisualBlocks',
    onPostRender: toggleActiveState(editor, enabledState),
    selectable: true,
    context: 'view',
    prependToContext: true
  });
};

export default {
  register
};