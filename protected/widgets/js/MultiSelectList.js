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

$(document).ready(function () {

    // make Selects disabled if None is selected
    var $multi_selects = $('.multi-select-list');
    $.each($multi_selects, function(index, multi_select){

        var $multi_select = $(multi_select);
        var $selection = $multi_select.find('.multi-select-selections');

        var lis = $($selection).find('li');

        if( lis.length === 1 && $( lis[0]).find('a').data('text') === 'None'){
            $multi_select.find('select').prop('disabled', true);
        }
    });

  $(this).on('init', '.multi-select', function () {
    $('.multi-select-selections.sortable').sortable();
  });

    $('.multi-select-selections.sortable').sortable();

    // Prevent the events from being bound multiple times.
    if ($(this).data('multi-select-events')) {
        return;
    }
    $(this).data('multi-select-events', true);

    $(this).on('click', '.multi-select .remove-all', function (e) {
        e.preventDefault();
        var container = $(this).closest('.multi-select');
        container.find('.remove-one').trigger('click');
    });

  $(this).on('change', 'select.MultiSelectList', function () {

    var select = $(this);
    var selected = select.children('option:selected');

    if (selected.val().length > 0) {

      var container = select.closest('.multi-select');
      var selections = container.find('.multi-select-selections');
      var inputField = container.find('.multi-select-list-name');
      var fieldName = inputField.attr('name').match(/\[MultiSelectList_(.*?)\]$/)[1];
      var noSelectionsMsg = container.find('.no-selections-msg');
      var removeAll = container.find('.remove-all');
      var options = container.data('options');
      var throughOptions = container.data('statuses');

      $('#' + fieldName.replace('[', '_').replace(']', '_') + 'empty_hidden').remove();

      var attrs = {};
      $(selected[0].attributes).each(function () {
        attrs[this.nodeName] = this.nodeValue;
      });

      var inp_str = '<input type="hidden" name="' + fieldName + '[]"';
      for (var key in attrs) {
        if (attrs.hasOwnProperty(key)) {
          inp_str += ' ' + key + '="' + attrs[key] + '"';
        }
      }
      inp_str += ' />';

      var input = $(inp_str);

      var remote_data = {
        'class': 'multi-select-remove remove-one ' + selected.val(),
        'data-name': fieldName + '[]',
        'data-text': selected.text()
      };

      if ($(this).hasClass('linked-fields')) {
        remote_data['class'] += ' linked-fields';
        remote_data['data-linked-fields'] = $(this).data('linked-fields');
        remote_data['data-linked-values'] = $(this).data('linked-values');
      }

      var remove = $('<span></span>', remote_data);
      var remove_icon = $('<i class="oe-i remove-circle small "></i>');
      remove.append(remove_icon);

      var item = $('<li><span class="text">' + selected.text() + '</span></li>');
      item.append(remove);
      item.append(input);

      if(throughOptions) {
        var $throughSelect = $('<select name="' + fieldName.replace(/\[([^\]]+)\]/g, '[$1_through]') +'[' + selected.val() + '][status_id]"></select>');
        $.each(throughOptions, function (key, value) {
          $throughSelect
            .append($('<option>', {value: key})
              .text(value));
        });
        item.append($throughSelect);
      }

      selections
        .append(item)
        .show();

      noSelectionsMsg.hide();
      removeAll.show();

      if (!select.data('searchable')) {
        selected.remove();
        select.val('');
      } else {
        var chosenId = '#' + select.attr('id') + '_chosen';
        $(chosenId).find('.chosen-single').addClass('chosen-default').find('span').text(select.data('placeholder'));
      }

      if (options.sorted) {
        selections.append(selections.find('li').sort(function (a, b) {
          return $.trim($(a).find('.text').text()) > $.trim($(b).find('.text').text());
        }));
      }
    }

      //if 'None' selected we do no allow more options
    var selected_text = selected.text().trim();

    if(selected_text === 'None'){
        $(this).prop('disabled', true);

          //remove other options
          $.each(selections.find('li'), function( index, $item ) {
            var $anchor = $($item).find('.multi-select-remove');

            if($anchor.data('text').trim() !== 'None'){
                $anchor.trigger('click');
            }
          });
    }

    select.trigger('MultiSelectChanged');
    return false;
  });

  $(this).on('click', '.multi-select-remove', 'click', function (e) {
    e.preventDefault();
    var anchor = $(this);
    var container = anchor.closest('.multi-select');
    var selections = container.find('.multi-select-selections');
    var noSelectionsMsg = container.find('.no-selections-msg');
    var removeAll = container.find('.remove-all');
    var input = anchor.closest('li').find('input');

    var attrs = {};
    $(input[0].attributes).each(function () {
      if (this.nodeName !== 'type' && this.nodeName !== 'name') {
        attrs[this.nodeName] = this.nodeValue;
      }
    });

    var text = anchor.data('text').toString();
    var select = container.find('select');

    var attr_str = '';
    for (var key in attrs) {
      if (attrs.hasOwnProperty(key)) {
        attr_str += ' ' + key + '="' + attrs[key] + '"';
      }
    }

    if (!select.data('searchable')) {
      select.append('<option' + attr_str + '>' + text + '</option>');
      if(text.trim() === 'None'){
          select.prop('disabled', false);
      }

      sort_selectbox(select);
    }

    anchor.closest('li').remove();
    input.remove();

    if (!selections.children().length) {
      selections.add(removeAll).hide();
      noSelectionsMsg.show();
      var container = select.closest('.multi-select');
      var inputField = container.find('.multi-select-list-name');
      var fieldName = inputField.attr('name').match(/\[MultiSelectList_(.*?)\]$/)[1];
      var inp_str = '<input id="' + fieldName.replace('[', '_').replace(']', '_') + 'empty_hidden" type="hidden" name="' + fieldName + '"/>';
      container.append(inp_str);
    }

    if ($(this).hasClass('linked-fields')) {
      if (inArray($(this).data('text'), $(this).data('linked-values').split(',')) !== -1) {
        var element_name = container.children('input[type="hidden"]').attr('name').replace(/\[.*$/, '');
        var fields = $(this).data('linked-fields').split(',');
        var values = $(this).data('linked-values').split(',');
        for (var i = 0; i < fields.length; i++) {
          if (values.length === 1 || i === arrayIndex($(this).data('text'), values)) {
            hide_linked_field(element_name, fields[i]);
          }
        }
      }
    }

    select.trigger('MultiSelectChanged');

    return false;
  });

  if ($('select.MultiSelectList').data('searchable')) {
    $('select.MultiSelectList').chosen();
  }

});
