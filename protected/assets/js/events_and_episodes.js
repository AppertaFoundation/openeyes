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
    $(document).keydown(function (event) {
        if (event.keyCode == 13 && $(event.target).is(':not(textarea)')) {
            event.preventDefault();
            return false;
        }
    });

    function markElementDirty(element) {
        if (typeof element !== "undefined" && element) {
            $(element).find('input[name*="[element_dirty]"]').val(1);
        } else {
            $(this).closest('.element').find('input[name*="[element_dirty]"]').val(1);
        }
    }

    $(document).on('click', '.add-icon-btn', function () {
        markElementDirty($(this).closest('.element'));
    });
    $('#event-content').on('change', 'select, input, textarea', function () {
        markElementDirty($(this).closest('.element'));
    });

    $('.js-active-elements').on('mouseup', '.ed-widget', function () {
        markElementDirty($(this).closest('.element'));
    });

    $('label').die('click').live('click', function () {
        if ($(this).prev().is('input:radio')) {
            $(this).prev().click();
        }
    });

    $(this).undelegate('select.dropDownTextSelection', 'change').delegate('select.dropDownTextSelection', 'change', function () {
        if ($(this).val() != '') {
            var target = $('#' + $(this).attr('id').replace(/^dropDownTextSelection_/, ''));
            var currentVal = target.val();

            if ($(this).hasClass('delimited')) {
                var newText = $(this).val();
            } else {
                var newText = $(this).children('option:selected').text();
            }

            if (currentVal.length > 0 && !$(this).hasClass('delimited')) {
                if (newText.toUpperCase() == newText) {
                    newText = ', ' + newText;
                } else {
                    newText = ', ' + newText.charAt(0).toLowerCase() + newText.slice(1);
                }
            } else if (currentVal.length == 0 && $(this).hasClass('delimited')) {
                newText = newText.charAt(0).toUpperCase() + newText.slice(1);
            } else if ($(this).hasClass('delimited') && currentVal.slice(-1) != ' ') {
                newText = ' ' + newText;
            }

            target.val(currentVal + newText);
            target.trigger('autosize');

            $(this).val('');
        }
    });

    $(this).delegate('#js-event-audit-trail-btn', 'click', function () {
        $("#js-event-audit-trail").toggle();
        $(this).toggleClass("active");
    });

    // Handle form fields that have linked fields to show/hide
    $(this).on('change', 'select.linked-fields', function () {
        var fields = $(this).data('linked-fields').split(',');
        var values = $(this).data('linked-values').split(',');

        if ($(this).hasClass('MultiSelectList')) {
            var element_name = $(this).parent().prev('input').attr('name').replace(/\[.*$/, '');
        } else {
            var element_name = $(this).attr('name').replace(/\[.*$/, '');

            for (var i in fields) {
                hide_linked_field(element_name, fields[i]);
            }
        }

        if ($(this).hasClass('MultiSelectList')) {
            var selected = $(this).parents('.multi-select').find('.multi-select-selections').children('li');
            for (var j = 0; j < selected.length; j++) {
                var value = $(selected[j]).children('.multi-select-remove')[0];
                if ($(value).data('text') == $(selected[j]).find('.text').text()) {
                    show_linked_field(element_name, $(value).data('linked-fields'), true);
                }
            }
        } else if (inArray($(this).children('option:selected').text(), values)) {
            var vi = arrayIndex($(this).children('option:selected').text(), values);
            for (var j in fields) {
                if (values.length == 1 || j == vi) {
                    show_linked_field(element_name, fields[j], js == 0);
                }
            }
        }
    });

    $(this).on('click', 'input[type="radio"].linked-fields', function () {
        var element_name = $(this).attr('name').replace(/\[.*$/, '');

        var fields = $(this).data('linked-fields').split(',');
        var values = $(this).data('linked-values').split(',');

        if (inArray($(this).parent().text().trim(), values)) {
            for (var i in fields) {
                show_linked_field(element_name, fields[i], i == 0);
            }
        } else {
            for (var i in fields) {
                hide_linked_field(element_name, fields[i]);
            }
        }
    });

    $(this).on('click', 'input[type="checkbox"].linked-fields', function () {
        var element_name = $(this).attr('name').replace(/\[.*$/, '');

        var fields = $(this).data('linked-fields').split(',');

        if ($(this).is(':checked')) {
            for (var i in fields) {
                show_linked_field(element_name, fields[i], i == 0);
            }
        } else {
            for (var i in fields) {
                hide_linked_field(element_name, fields[i]);
            }
        }
    });

    $(this).on('click', '.js-remove-element', function (e) {
        e.preventDefault();
        var $parent = $(this).closest('.element');
        var class_name = $parent.data('element-type-class');
        if (element_close_warning_enabled === 'on' && $parent.find('input[name*="[element_dirty]"]').val() === "1") {
            let dialog = new OpenEyes.UI.Dialog.Confirm({
                content: "Are you sure that you wish to close the " +
                    $parent.data('element-type-name') +
                    " element? All data in this element will be lost"
            });
            dialog.on('ok', function () {
                $parent.trigger('element_removed');
                removeElement($parent);
                $(document).trigger('element_removed');
            }.bind(this));
            dialog.open();
        } else {
            $parent.trigger('element_removed');
            removeElement($parent);
        }
    });

    $(this).on('click', '.js-tiles-collapse-btn', function () {
        let $tileGroup = $(this).closest('.element-tile-group');
        let isCollapsedGroup = $tileGroup.hasClass('collapse');
        $tileGroup.toggleClass('collapse');
        $tileGroup.find('.element.tile .element-data, .element.tile .tile-more-data-flag').toggle(isCollapsedGroup);
        $(this).toggleClass('reduce-height increase-height');

        if (!isCollapsedGroup) {
            $tileGroup.find('.element').each(function () {
                let rowCount = $(this).find('tbody:first tr').length;
                let $countDisplay = $('<small />', {class: 'js-data-hidden-state'}).text(' [' + rowCount + ']');
                $(this).find('.element-title').append($countDisplay);
            });
        } else {
            $tileGroup.find('.js-data-hidden-state').remove();
        }
    });


    // this event handler should attach only to the elements. For the element sidebar, the collapse/expand
    // functionality is already defined in the OpenEyes.UI.PatientSidebar.js file
    $(this).find('section').on('click', '.collapse-group > .header-icon', function(e) {
        e.preventDefault();
        $(e.target).toggleClass('collapse');
        $(e.target).siblings('.collapse-group-content').toggle();
    });

    // Tile Data Overflow
    $('.element.tile').each(function () {
        let h = $(this).find('.data-value').height();

        // CSS is set to max-height:180px;
        if (h > 179) {
            // it's scrolling, so flag it
            let flag = $('<div/>', {class: "tile-more-data-flag"});
            let icon = $('<i/>', {class: "oe-i arrow-down-bold medium selected"});
            flag.append(icon);
            $(this).prepend(flag);

            let tileOverflow = $('.tile-data-overflow', this);

            flag.click(function () {
                tileOverflow.animate({
                    scrollTop: tileOverflow.height()
                }, 1000);

                flag.fadeOut();
            });

            tileOverflow.on('scroll', function () {
                flag.fadeOut();
            });

            if ($(this).find('tbody').length > 0) {
                // Assuming it's a table!...
                let trCount = $(this).find('tbody').get(0).childElementCount;
                // and then set the title to show total data count
                let title = $('.element-title', this);
                title.html(title.text() + ' <small>[' + trCount + ']</small>');
            }
        }
    });

    $('#js-get-cito-url').click(function(e) {
        e.preventDefault();
        $.ajax({
            'type': 'GET',
            'url': baseUrl+'/Patient/getCitoUrl',
            'data': $.param({hos_num: OE_patient_hosnum})+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(data) {
                if (data.success) {
                    window.open(data.url, 'newwindow', 'width=1200,height=800');
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: data.message
                    }).open();
                }
            }
        });
    });
});

function WidgetSlider() {
    if (this.init) this.init.apply(this, arguments);
}

WidgetSlider.prototype = {
    init: function (params) {
        for (var i in params) {
            this[i] = params[i];
        }

        var thiz = this;

        $(document).ready(function () {
            thiz.bindElement();
        });
    },

    bindElement: function () {
        var thiz = this;

        $('#' + this.range_id).change(function () {
            thiz.handleChange($(this));
        });
    },

    handleChange: function (element) {
        var val = element.val();

        for (var value in this.remap) {
            if (val == value) {
                val = this.remap[value];
            }
        }

        if (this.null.length > 0) {
            if (val.match(/\./)) {
                val = String(parseFloat(val) - 1);
            } else {
                val = String(parseInt(val) - 1);
            }
        }

        var min = $('#' + this.range_id).attr('min');

        if (min.match(/\./)) {
            min = parseFloat(min);
        } else {
            min = parseInt(min);
        }

        if (val < min && this.null.length > 0) {
            val = this.null;
        } else {
            if (this.force_dp) {
                if (!val.match(/\./)) {
                    val += '.';
                    for (var i in this.force_dp) {
                        val += '00';
                    }
                } else {
                    var dp = val.replace(/^.*?\./, '');

                    while (dp.length < this.force_dp) {
                        dp += '0';
                        val += '0';
                    }

                    while (dp.length > this.force_dp) {
                        dp = dp.replace(/.$/, '');
                        val = val.replace(/.$/, '');
                    }
                }
            }

            if (this.prefix_positive && parseFloat(val) > 0) {
                var val = this.prefix_positive + val;
            }
        }

        $('#' + this.range_id + '_value_span').text(val + this.append);
    }
}

function WidgetSliderTable() {
    if (this.init) this.init.apply(this, arguments);
}

WidgetSliderTable.prototype = {
    init: function (params) {
        for (var i in params) {
            this[i] = params[i];
        }

        var thiz = this;

        $(document).ready(function () {
            thiz.bindElement();
        });
    },

    bindElement: function () {
        var thiz = this;

        $('#' + this.range_id).change(function () {
            thiz.handleChange($(this));
        });
    },

    handleChange: function (element) {
        var val = element.val();

        $('#' + this.range_id + '_value_span').text(this.data[val]);
    }
}

function show_linked_field(element_name, field_name, focus) {
    $('fieldset#' + element_name + '_' + field_name).show();
    $('#div_' + element_name + '_' + field_name).show();
    if (focus) {
        $('#' + element_name + '_' + field_name).focus();
    }
}

function hide_linked_field(element_name, field_name) {
    $('fieldset#' + element_name + '_' + field_name).hide();
    $('#div_' + element_name + '_' + field_name).hide();

    $('input[name="' + element_name + '[' + field_name + ']"][type="radio"]').removeAttr('checked');
    $('input[name="' + element_name + '[' + field_name + ']"][type="text"]').val('');
    $('select[name="' + element_name + '[' + field_name + ']"]').val('');

    if ($('#' + field_name).hasClass('MultiSelectList')) {
        $('.multi-select-remove[data-name="' + field_name + '[]"]').map(function () {
            $(this).click();
        });
    }
}

function scrollToElement(element) {
    var $container = $('main.main-event');
    $container.scrollTop(
        $(element).offset().top - $container.offset().top + $container.scrollTop() - 130
    );

    var $title = $(element).closest('.element').find('.element-title');
    $title.effect('pulsate', {
        times: 2
    }, 600);
}

/**
 * Sets ups all the event listeners for adders
 * @param adderDiv div(or any other element) that is the adder
 * @param selectMode can more than one item be selected per list
 *    - multi: Multiple elements in a list can be selected simultaneously
 *    - single: When a second item in a list is selected the rest are deselected (in that one list)
 *    - return: Immediately return and call the callback with selected item
 * @param callback function to call when the adder exits
 * @param openButtons divs that on click will open the adderDiv
 * @param addButtons divs that on click will close the adder and call the callback
 * @param closeButtons divs that on click will close the adderDiv without callback
 */
function setUpAdder(adderDiv = null, selectMode = 'single', callback = null, openButtons = null, addButtons = null, closeButtons = null) {
    if (adderDiv === null) {
        console.warn('no div sent to setUpAdder');
        return;
    }

    if (openButtons !== null) {
        openButtons.click(function showAdder() {
            positionFixedPopup(openButtons, adderDiv);
            adderDiv.show();

            if (adderDiv.offset().top < 0) {
                positionFixedPopup(openButtons, adderDiv);
            }
        });
    }

    if (addButtons !== null) {
        addButtons.click(function closeAndAdd() {
            var added = true;

            if (selectMode == 'single' && typeof callback === 'function') {
                ($(this).parent()).find('ul').each(function () {
                    if ($(this).children('li.selected').length == 0) {
                        added = false;
                        return false;
                    }
                });
            }
            if (added) {
                adderDiv.hide();
                if (typeof callback === 'function') {
                    callback();
                }
            }
        });
    }

    if (closeButtons !== null) {
        closeButtons.click(function closeAdder() {
            adderDiv.find('.selected').removeClass('selected');
            adderDiv.hide();
        });
    }

    //set up select class on clicks
    if (selectMode === 'return') {
        adderDiv.find('li').click(function () {
            $(this).addClass('selected');
            adderDiv.hide();
            callback($(this));
        });
    } else {
        adderDiv.on('click', 'li', function () {
            if (!$(this).hasClass('selected')) {
                if (selectMode !== 'multi') {
                    $(this).parent('ul').find('li').removeClass('selected');
                }
                $(this).addClass('selected');
            } else {
                $(this).removeClass('selected');
            }
        });
    }
}

function positionFixedPopup($btn, adderDiv = null) {
    /*
    Popup is FIXED positioned
    work out offset position
    setup events to close it on resize or scroll.
    */
    var elem = $btn[0];

    // js vanilla:
    var btnPos = elem.getBoundingClientRect();
    var w = document.documentElement.clientWidth;
    var h = document.documentElement.clientHeight;
    var right = (w - btnPos.right);
    var bottom = (h - btnPos.bottom);
    // set CSS Fixed position
    adderDiv.css({
        "bottom": bottom,
        "right": right
    });

    if (adderDiv.offset().top < 0) {
        adderDiv.css({"bottom": Math.floor(bottom + adderDiv.offset().top)});
    }

    /*
    Close popup on...
    as scroll event fires on assignment.
    check against scroll position
    */
    var scrollPos = $(".main-event").scrollTop();
    document.addEventListener("scroll", function () {
        if (scrollPos != $(this).scrollTop()) {
            // Remove scroll event:
            $(".main-event").off("scroll");
            closeCancel(adderDiv);
        }
    });
}

// Close and reset
function closeCancel(adderDiv = null) {
    adderDiv.hide();
}
