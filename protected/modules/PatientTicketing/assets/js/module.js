/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * function to display pop up for moving a ticket to a new queue
 * @param id
 * @param outcomes
 */
(function () {
  /**
   * TicketController provides functions to control the Ticket Viewer page.
   *
   * @param options
   * @constructor
   */
  function TicketController(options) {
    this.options = $.extend(true, {}, TicketController._defaultOptions, options);
    this.queueAssForms = {};
    this.currentTicketId = null;

    /**
     * Appends the given html to the ticket table row for the given id.
     *
     * @param id
     * @param html
     */
    this.appendHistory = function (id, html) {
      var historyRows = $(this.getTicketRowSelector(id)).filter(this.options.ticketHistoryFilter);
      if ($(historyRows).length) {
        historyRows.remove();
      }
      $(this.getTicketRowSelector(id)).after(html);
    };

    this.initializeSortFunctions();
  }

  TicketController._defaultOptions = {
    queueAssignmentFormURI: "/PatientTicketing/default/getQueueAssignmentForm/",
    ticketMoveURI: "/PatientTicketing/default/moveTicket/",
    ticketRowSelectorPrefix: "tr[data-ticket-id='",
    ticketRowSelectorPostfix: "']",
    getTicketRowURI: "/PatientTicketing/default/getTicketTableRow/",
    queueTemplateSelector: '#ticketcontroller-queue-select-template',
    queueAssignmentPlaceholderSelector: '#queue-assignment-placeholder',
    ticketHistoryFilter: ".history",
    getTicketHistoryURI: "/PatientTicketing/default/getTicketTableRowHistory/",
    takeTicketURI: "/PatientTicketing/default/takeTicket/",
    releaseTicketURI: "/PatientTicketing/default/releaseTicket/"
  };

  /**
   * Convenience function to construct the table row selector for the given ticket id
   *
   * @param id
   * @returns {string}
   */
  TicketController.prototype.getTicketRowSelector = function (id) {
    if (!id) {
      id = this.currentTicketId;
    }
    return this.options.ticketRowSelectorPrefix + id + this.options.ticketRowSelectorPostfix;
  };

  TicketController.prototype.initializeSortFunctions = function () {
    const queueset_id = document.getElementById('queueset_id').value;
    const $wrapper = document.getElementById('table-sort-order');

    OpenEyes.UI.DOM.addEventListener($wrapper, 'input', 'select, .js-direction-up, .js-direction-down', () => {
      this.order($wrapper.querySelector('select').value, $wrapper.querySelector('input[type="radio"]:checked').value, queueset_id);
    });
  };

  TicketController.prototype.order = function (by, direction, queueset_id) {
    const domain = `${window.location.protocol}//${window.location.host}`;
    let url = `${domain}/PatientTicketing/default?reset_filters=1&sort_by=${by}&sort_by_order=${direction}&cat_id=1&queueset_id=${queueset_id}`;
    window.location.href = url;
  };

  /**
   * Convenience function to provide visual cue that an interaction is taking place on the ticket row.
   *
   * @param id
   */
  TicketController.prototype.maskTicketRow = function (id) {
    var rowSelector = this.getTicketRowSelector(id);
    disableButtons(rowSelector + ' button,.button');
    $(rowSelector).addClass('disabled');
  };

  /**
   * Convenience function to remove ticket row interaction visual cues.
   * (doesn't always need to be called as ticket reloading will reset the css on the reloaded row)
   *
   * @param id
   */
  TicketController.prototype.unmaskTicketRow = function (id) {
    var rowSelector = this.getTicketRowSelector(id);
    enableButtons(rowSelector + ' button,.button');
    $(rowSelector).removeClass('disabled');
  };

  /**
   * Creates the popup for the given Ticket definition to move it to a new Queue (based on the given outcomes)
   *
   * @param ticketInfo
   * @param outcomes
   */
  TicketController.prototype.moveTicket = function (ticketInfo, outcomes, event_types) {
    var template = $(this.options.queueTemplateSelector).html();

    if (!template) {
      throw new Error('Unable to compile queue selector template. Template not found: ' + this.options.queueTemplateSelector);
    }

    this.currentTicketId = ticketInfo.id;
    var templateVals = $.extend(true, {}, ticketInfo, {CSRF_TOKEN: YII_CSRF_TOKEN});
    templateVals.outcome_options = '';
    var firstSelect = '';
    var queue_id = false;

    if (outcomes.length == 1) {
      firstSelect = ' selected';
      queue_id = outcomes[0].id;
    }
    for (var i = 0; i < outcomes.length; i++) {
      templateVals.outcome_options += '<option value="' + outcomes[i].id + '"' + firstSelect + '>' + outcomes[i].name + '</option>';
    }

    templateVals.event_types = JSON.stringify(event_types);
    templateVals.ticketInfo = JSON.stringify(ticketInfo);
    templateVals.event_type_links = '';

    if (queue_id) {
      if (typeof(event_types[queue_id]) != 'undefined') {
        for (var i = 0; i < event_types[queue_id].length; i++) {
          templateVals.event_type_links += '<a href="' + baseUrl + '/' + event_types[queue_id][i]['class_name'] + '/default/create?patient_id=' +
            ticketInfo.patient_id + '" class="button blue hint">' + event_types[queue_id][i]['name'] + '</a>';
        }
      }
    }

    this.dialog = new OpenEyes.UI.Dialog({
      content: Mustache.render(template, templateVals)
    });
    this.dialog.content.on('click', '.ok', this.submitTicketMove.bind(this));
    this.dialog.content.on('click', '.cancel', this.dialog.close.bind(this.dialog));
    this.dialog.open();
    if (firstSelect.length) {
      this.dialog.content.find('#to_queue_id').trigger('change');
    }
  };

  /**
   * process the Ticket Move form
   */
  TicketController.prototype.submitTicketMove = function () {
    var form = $(this.dialog.content).find('form');
    var errors = form.find('.alert-box');

    if (!form.find('[name=to_queue_id]').val()) {
      errors.text('Please select a destination queue').show()
      return;
    }

    errors.hide();
    $.ajax({
      url: this.options.ticketMoveURI + this.currentTicketId,
      data: form.serialize(),
      type: 'POST',
      dataType: 'json',
      success: function (response) {
        if (response.errors) {
          errors.text('');
          for (var i in response.errors) errors.append(response.errors[i] + "<br>");
          errors.show();
        } else {
          this.dialog.close();
          this.maskTicketRow();
          this.reloadTicket(this.currentTicketId);
        }
      }.bind(this),
      error: function (jqXHR, status, error) {
        this.dialog.close();
        this.maskTicketRow();
        this.reloadTicket(this.currentTicketId);
        new OpenEyes.UI.Dialog.Alert({content: 'Could not move ticket'}).open();
      }.bind(this)
    })
  };

  /**
   * Reload the row for the current ticket (called after a ticket is updated)
   */
  TicketController.prototype.reloadTicket = function (id) {
    var rowSelector = this.getTicketRowSelector(id);
    $.ajax({
      url: this.options.getTicketRowURI + id,
      success: function (response) {
        if ($(rowSelector).filter(this.options.ticketHistoryFilter).length) {
          $(rowSelector).filter(this.options.ticketHistoryFilter).slideUp("slow", function () {
            $(rowSelector).fadeOut("slow", function () {
              $(rowSelector).replaceWith(response).fadeIn('slow')
            });
          }).remove();
        }
        else {
          $(rowSelector).fadeOut("slow", function () {
            $(rowSelector).replaceWith(response).fadeIn('slow')
          });
        }
      }.bind(this),
      error: function () {
        $(this.getTicketRowSelector(id)).remove();
        new OpenEyes.UI.Dialog.Alert("Unable to reload ticket").open();
      }
    });
  };

  /**
   * Retrieves the assignment form for the given queue id (caching for future use)
   *
   * @param integer id
   * @param callback success
   */
  TicketController.prototype.getQueueAssForm = function (id, success) {
    if (!this.queueAssForms[id]) {
      disableButtons();
      var self = this;
      var form = $.ajax({
        url: this.options.queueAssignmentFormURI,
        data: {id: id},
        success: function (response) {
          self.queueAssForms[id] = response;
          enableButtons();
          success(response);
        },
        error: function (jqXHR, status, error) {
          enableButtons();
          throw new Error("Unable to retrieve assignment form for queue with id " + id + ": " + error);
        }
      });
    }
    else {
      success(this.queueAssForms[id]);
    }
  };

  /**
   * Sets the Queue Assignment form in the Ticket Move popup.
   *
   * @param integer id
   */
  TicketController.prototype.setQueueAssForm = function (id) {
    if (id) {
      this.getQueueAssForm(id, function onSuccess(form) {
        $(this.dialog.content.html).find(this.options.queueAssignmentPlaceholderSelector).html(form)
      }.bind(this));
    }
    else {
      $(this.dialog.content.html).find(this.options.queueAssignmentPlaceholderSelector).html('');
    }
  };

  /**
   * Toggles the history rows for the given ticket.
   *
   * @param ticketInfo
   */
  TicketController.prototype.toggleHistory = function (ticketInfo) {
    var historyRows = $(this.getTicketRowSelector(ticketInfo.id)).filter(this.options.ticketHistoryFilter);
    if (historyRows.length) {
      if (historyRows.is(":visible")) {
        historyRows.slideUp();
      }
      else {
        historyRows.slideDown();
      }
    }
    else {
      this.maskTicketRow(ticketInfo.id);
      $.ajax({
        url: this.options.getTicketHistoryURI + ticketInfo.id,
        success: function (response) {
          this.appendHistory(ticketInfo.id, response);
          this.unmaskTicketRow(ticketInfo.id);
        }.bind(this),
        error: function () {
          new OpenEyes.UI.Dialog.Alert({content: "Could not load history for ticket."}).open();
          this.unmaskTicketRow(ticketInfo.id);
        }

      });
    }
  };

  /**
   * Have the currently logged in user take control of the given ticket and refreshes the table row for it.
   *
   * @param ticketInfo
   */
  TicketController.prototype.takeTicket = function (ticketInfo) {
    this.maskTicketRow(ticketInfo.id);
    $.ajax({
      url: this.options.takeTicketURI + ticketInfo.id,
      success: function (response) {
        if (response.message) {
          new OpenEyes.UI.Dialog.Alert(response.message).open();
        }
        this.reloadTicket(ticketInfo.id);
      }.bind(this),
      error: function () {
        new OpenEyes.UI.Dialog.Alert('Unable to take ticket').open();
        this.reloadTicket(ticketInfo.id);
      }
    });
  };

  /**
   * Release the ticket for the currently logged in user and refreshes the table row for it.
   *
   * @param ticketInfo
   */
  TicketController.prototype.releaseTicket = function (ticketInfo) {
    this.maskTicketRow(ticketInfo.id);
    $.ajax({
      url: this.options.releaseTicketURI + ticketInfo.id,
      success: function (response) {
        if (response.message) {
          new OpenEyes.UI.Dialog.Alert(response.message).open();
        }
        this.reloadTicket(ticketInfo.id);
      }.bind(this),
      error: function () {
        new OpenEyes.UI.Dialog.Alert('Unable to release ticket').open();
        this.reloadTicket(ticketInfo.id);
      }
    });
  };

  $(document).ready(function () {
    var ticketController = new TicketController();

    $(this).on('click', '.ticket-take', function () {
      var ticketInfo = $(this).closest('tr').data('ticket-info');
      ticketController.takeTicket(ticketInfo);
    });

    $(this).on('click', '.ticket-release', function () {
      var ticketInfo = $(this).closest('tr').data('ticket-info');
      ticketController.releaseTicket(ticketInfo);
    });

    $(this).on('click', '.js-ticket-history', function () {
      if (this.innerHTML === 'History') {
        this.innerHTML = 'Hide History';
      } else {
        this.innerHTML = 'History';
      }
      var ticketInfo = $(this).closest('tr').data('ticket-info');
      ticketController.toggleHistory(ticketInfo);
    });

    $(this).on('click', '.js-undo-last-queue-step', function () {
      var ticketInfo = $(this).closest('tr').data('ticket-info');

      fetch(`${baseUrl}/PatientTicketing/default/undoLastStep/${ticketInfo.id}`)
          .then(response => response.json())
          .then(data  => {
            if (data.success !== true) {
              alert("Something went wrong trying to undo the last step.  Please try again or contact support for assistance.");
            }
            window.location.reload();
          });
    });

    $(this).on('change', '#subspecialty-id', function () {
      var subspecialty_id = $(this).val();

      if (subspecialty_id === '') {
        $('#firm-id').find('options').remove();
        $('#firm-id').append($('<option value="All">').text("All Contexts"));
        $('#firm-id').attr('disabled', 'disabled');
      } else {
        $.ajax({
          'type': 'GET',
          'url': baseUrl + '/PatientTicketing/default/getFirmsForSubspecialty?subspecialty_id=' + subspecialty_id,
          'success': function (html) {
            $('#firm-id').replaceWith(html);
          }
        });
      }
    });
  });
}());
