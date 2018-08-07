/**
 * ColorPickerHtml.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

import DOMUtils from 'tinymce/core/api/dom/DOMUtils';
import I18n from 'tinymce/core/api/util/I18n';
import TextColor from '../core/TextColor';

const getHtml = function (cols, rows, colorMap, hasColorPicker) {
  let colors, color, html, last, x, y, i, count = 0;
  const id = DOMUtils.DOM.uniqueId('mcearia');

  const getColorCellHtml = function (color, title) {
    const isNoColor = color === 'transparent';

    return (
      '<td class="mce-grid-cell' + (isNoColor ? ' mce-colorbtn-trans' : '') + '">' +
      '<div id="' + id + '-' + (count++) + '"' +
      ' data-mce-color="' + (color ? color : '') + '"' +
      ' role="option"' +
      ' tabIndex="-1"' +
      ' style="' + (color ? 'background-color: ' + color : '') + '"' +
      ' title="' + I18n.translate(title) + '">' +
      (isNoColor ? '&#215;' : '') +
      '</div>' +
      '</td>'
    );
  };

  colors = TextColor.mapColors(colorMap);
  colors.push({
    text: I18n.translate('No color'),
    color: 'transparent'
  });

  html = '<table class="mce-grid mce-grid-border mce-colorbutton-grid" role="list" cellspacing="0"><tbody>';
  last = colors.length - 1;

  for (y = 0; y < rows; y++) {
    html += '<tr>';

    for (x = 0; x < cols; x++) {
      i = y * cols + x;

      if (i > last) {
        html += '<td></td>';
      } else {
        color = colors[i];
        html += getColorCellHtml(color.color, color.text);
      }
    }

    html += '</tr>';
  }

  if (hasColorPicker) {
    html += (
      '<tr>' +
      '<td colspan="' + cols + '" class="mce-custom-color-btn">' +
      '<div id="' + id + '-c" class="mce-widget mce-btn mce-btn-small mce-btn-flat" ' +
      'role="button" tabindex="-1" aria-labelledby="' + id + '-c" style="width: 100%">' +
      '<button type="button" role="presentation" tabindex="-1">' + I18n.translate('Custom...') + '</button>' +
      '</div>' +
      '</td>' +
      '</tr>'
    );

    html += '<tr>';

    for (x = 0; x < cols; x++) {
      html += getColorCellHtml('', 'Custom color');
    }

    html += '</tr>';
  }

  html += '</tbody></table>';

  return html;
};

export default {
  getHtml
};