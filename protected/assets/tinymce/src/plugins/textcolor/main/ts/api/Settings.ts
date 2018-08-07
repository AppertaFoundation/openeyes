/**
 * Settings.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

const defaultColorMap = [
  '000000', 'Black',
  '993300', 'Burnt orange',
  '333300', 'Dark olive',
  '003300', 'Dark green',
  '003366', 'Dark azure',
  '000080', 'Navy Blue',
  '333399', 'Indigo',
  '333333', 'Very dark gray',
  '800000', 'Maroon',
  'FF6600', 'Orange',
  '808000', 'Olive',
  '008000', 'Green',
  '008080', 'Teal',
  '0000FF', 'Blue',
  '666699', 'Grayish blue',
  '808080', 'Gray',
  'FF0000', 'Red',
  'FF9900', 'Amber',
  '99CC00', 'Yellow green',
  '339966', 'Sea green',
  '33CCCC', 'Turquoise',
  '3366FF', 'Royal blue',
  '800080', 'Purple',
  '999999', 'Medium gray',
  'FF00FF', 'Magenta',
  'FFCC00', 'Gold',
  'FFFF00', 'Yellow',
  '00FF00', 'Lime',
  '00FFFF', 'Aqua',
  '00CCFF', 'Sky blue',
  '993366', 'Red violet',
  'FFFFFF', 'White',
  'FF99CC', 'Pink',
  'FFCC99', 'Peach',
  'FFFF99', 'Light yellow',
  'CCFFCC', 'Pale green',
  'CCFFFF', 'Pale cyan',
  '99CCFF', 'Light sky blue',
  'CC99FF', 'Plum'
];

const getTextColorMap = function (editor) {
  return editor.getParam('textcolor_map', defaultColorMap);
};

const getForeColorMap = function (editor) {
  return editor.getParam('forecolor_map', getTextColorMap(editor));
};

const getBackColorMap = function (editor) {
  return editor.getParam('backcolor_map', getTextColorMap(editor));
};

const getTextColorRows = function (editor) {
  return editor.getParam('textcolor_rows', 5);
};

const getTextColorCols = function (editor) {
  return editor.getParam('textcolor_cols', 8);
};

const getForeColorRows = function (editor) {
  return editor.getParam('forecolor_rows', getTextColorRows(editor));
};

const getBackColorRows = function (editor) {
  return editor.getParam('backcolor_rows', getTextColorRows(editor));
};

const getForeColorCols = function (editor) {
  return editor.getParam('forecolor_cols', getTextColorCols(editor));
};

const getBackColorCols = function (editor) {
  return editor.getParam('backcolor_cols', getTextColorCols(editor));
};

const getColorPickerCallback = function (editor) {
  return editor.getParam('color_picker_callback', null);
};

const hasColorPicker = function (editor) {
  return typeof getColorPickerCallback(editor) === 'function';
};

export default {
  getForeColorMap,
  getBackColorMap,
  getForeColorRows,
  getBackColorRows,
  getForeColorCols,
  getBackColorCols,
  getColorPickerCallback,
  hasColorPicker
};