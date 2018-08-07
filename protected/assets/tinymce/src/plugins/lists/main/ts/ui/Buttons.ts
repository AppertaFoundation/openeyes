/**
 * Buttons.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

import Tools from 'tinymce/core/api/util/Tools';
import NodeType from '../core/NodeType';
import Selection from '../core/Selection';

const findIndex = function (list, predicate) {
  for (let index = 0; index < list.length; index++) {
    const element = list[index];

    if (predicate(element)) {
      return index;
    }
  }
  return -1;
};
const listState = function (editor, listName) {
  return function (e) {
    const ctrl = e.control;

    editor.on('NodeChange', function (e) {
      const tableCellIndex = findIndex(e.parents, NodeType.isTableCellNode);
      const parents = tableCellIndex !== -1 ? e.parents.slice(0, tableCellIndex) : e.parents;
      const lists = Tools.grep(parents, NodeType.isListNode);
      ctrl.active(lists.length > 0 && lists[0].nodeName === listName);
    });
  };
};

const indentPostRender = function (editor) {
  return function (e) {
    const ctrl = e.control;

    editor.on('nodechange', function () {
      const listItemBlocks = Selection.getSelectedListItems(editor);
      const disable = listItemBlocks.length > 0 && NodeType.isFirstChild(listItemBlocks[0]);
      ctrl.disabled(disable);
    });
  };
};

const register = function (editor) {
  const hasPlugin = function (editor, plugin) {
    const plugins = editor.settings.plugins ? editor.settings.plugins : '';
    return Tools.inArray(plugins.split(/[ ,]/), plugin) !== -1;
  };

  if (!hasPlugin(editor, 'advlist')) {
    editor.addButton('numlist', {
      active: false,
      title: 'Numbered list',
      cmd: 'InsertOrderedList',
      onPostRender: listState(editor, 'OL')
    });

    editor.addButton('bullist', {
      active: false,
      title: 'Bullet list',
      cmd: 'InsertUnorderedList',
      onPostRender: listState(editor, 'UL')
    });
  }

  editor.addButton('indent', {
    icon: 'indent',
    title: 'Increase indent',
    cmd: 'Indent',
    onPostRender: indentPostRender(editor)
  });
};

export default {
  register
};