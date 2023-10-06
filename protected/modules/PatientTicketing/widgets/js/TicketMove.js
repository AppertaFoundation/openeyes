(function () {

  function TicketMoveController(options) {
    this.options = $.extend(true, {}, TicketMoveController._defaultOptions, options);
    this.queueAssForms = {};
    this.patientId = $(this.options.patientAlertSelector).data('patient-id');
  }

  TicketMoveController._defaultOptions = {
    queueAssignmentFormURI: "/PatientTicketing/default/getQueueAssignmentForm/",
    reloadPatientAlertURI: "/PatientTicketing/default/getPatientAlert",
    formSelector: "#PatientTicketing-moveForm",
    formClass: '.PatientTicketing-moveTicket',
    queueAssignmentPlaceholderSelector: "#PatientTicketing-queue-assignment",
    ticketMoveURI: "/PatientTicketing/default/moveTicket/",
    ticketNavigateToEventURI: "/PatientTicketing/default/navigateToEvent/",
    patientAlertSelector: "#patient-alert-patientticketing",

    scratchpadButtonSelector: '#js-vc-scratchpad',
    scratchpadPopupSelector: '#oe-vc-scratchpad',
    scratchpadInputSelector: '#oe-vc-scratchpad textarea',
    currentDate: new Date().setHours(0, 0, 0, 0) / 1000,
  };

  /**
   * Retrieves the assignment form for the given queue id (caching for future use)
   *
   * @param {integer} id
   * @param {callback} success
   */
  TicketMoveController.prototype.getQueueAssForm = function (id, success) {
    let ticket_id = $(this.options.formClass).find('input[name="ticket_id"]').val();
    if (!this.queueAssForms[id]) {
      disableButtons();
      let self = this;
      $.ajax({
        url: this.options.queueAssignmentFormURI,
        data: {id: id, ticket_id: ticket_id},
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
   * @param {integer} id
   */
  TicketMoveController.prototype.setQueueAssForm = function (form, id) {
    if (id) {
      this.getQueueAssForm(id, function onSuccess(assForm) {
        form.find(this.options.queueAssignmentPlaceholderSelector).html(assForm)
      }.bind(this));
    }
    else {
      form.find(this.options.queueAssignmentPlaceholderSelector).html('');
    }
  };

  /**
   * Reload the patient alert banner
   */
  TicketMoveController.prototype.reloadPatientAlert = function () {
    $.ajax({
      url: this.options.reloadPatientAlertURI,
      data: {patient_id: this.patientId},
      success: function (response) {
        $(this.options.patientAlertSelector).replaceWith(response)
      }.bind(this),
      error: function (jqXHR, status, error) {
        new OpenEyes.UI.Dialog.Alert({content: 'An unexpected error occurred.'}).open();
      }.bind(this)
    });
  };

  TicketMoveController.prototype.navigateToEvent = function (form, href) {
    disableButtons(this.options.formSelector);
    var errors = form.find('.alert-box');

    if (!form.find('[name=to_queue_id]').val()) {
      errors.text('Please select a destination queue').show()
      return;
    }

    var ticket_id = form.find('input[name="ticket_id"]').val();
    var patient = encodeURIComponent($('#patient-alert-patientticketing').data('patient-id'));

    errors.hide();

    $.ajax({
      url: this.options.ticketNavigateToEventURI + ticket_id,
      data: form.serialize() + '&patient_id=' + patient + '&href=' + href,
      type: 'POST',
      dataType: 'json',

      success: function (response) {
        if (response.errors) {
          errors.text('');
          for (var i in response.errors) errors.append(response.errors[i] + "<br>");
          errors.show();
          enableButtons(this.options.formSelector);
        }
        else {
            // The form data is saved with the above 'form.serialize()'
            // success should mean this data is already saved, so we don't need to warn the users
            $(window).off('beforeunload');
            window.location.href = href;
        }
      }.bind(this),
      error: function (jqXHR, status, error) {
        //this.reloadPatientAlert();
        new OpenEyes.UI.Dialog.Alert({content: 'Could not move ticket'}).open();
      }.bind(this),
      complete: function () {
        enableButtons(this.options.formSelector);
      }.bind(this)
    })
  };


  /**
   * process the Ticket Move form
   */
  TicketMoveController.prototype.submitForm = function (form) {
    disableButtons(this.options.formSelector);
    var errors = form.find('.alert-box');

    if (!form.find('[name=to_queue_id]').val()) {
      errors.text('Please select a destination queue').show()
      return;
    }

    var ticket_id = form.find('input[name="ticket_id"]').val();

    errors.hide();
    $.ajax({
      url: this.options.ticketMoveURI + ticket_id,
      data: form.serialize(),
      type: 'POST',
      dataType: 'json',

      success: function (response) {
        if (response.errors) {
          errors.text('');
          for (var i in response.errors) errors.append(response.errors[i] + "<br>");
          errors.show();
        } else {
          //this.reloadPatientAlert();
          if (response.redirectURL) {
              window.onbeforeunload = null;
            window.patientTicketChanged = false;
            window.location = response.redirectURL;
          }
        }
      }.bind(this),
      error: function (jqXHR, status, error) {
        this.reloadPatientAlert();
        new OpenEyes.UI.Dialog.Alert({content: 'Could not move ticket'}).open();
      }.bind(this),
      complete: function () {
        enableButtons(this.options.formSelector);
      }.bind(this)
    });
  };


  TicketMoveController.prototype.showScratchpad = function () {
    this.toggleScratchpad(true);
  };

  TicketMoveController.prototype.hideScratchpad = function () {
    this.toggleScratchpad(false);
  };

  TicketMoveController.prototype.toggleScratchpad = function (showScratchpad) {
    var self = this;
    $(this.options.scratchpadPopupSelector).toggle(showScratchpad);
    var txt = showScratchpad ? 'Hide Scratchpad' : 'Scratchpad';
    $(this.options.scratchpadButtonSelector).text(txt);

    if (showScratchpad) {
      autosize($(this.options.scratchpadInputSelector));
      $(this.options.scratchpadPopupSelector).draggable({
        containment: "body",
        stop: function (event, ui) {
          self.saveScratchpadPosition(ui.position);
        }
      });

      var position = this.getSavedScratchpadPosition();
      if (position && position.top <= $(window).height() && position.left <= $(window).width()) {
        // do nothing as postion top and left are already set
      } else {
        position.top = 75;
        position.left = 250
      }

      $(this.options.scratchpadPopupSelector).css({
        top: position.top,
        left: position.left
      });
    }
  };

  // Basic JSON string check
  TicketMoveController.prototype.isJSONString = function (str) {
    try{
      JSON.parse(str);
    } catch (e) {
      return false;
    }
    return true;
  }

  TicketMoveController.prototype.loadScratchpadData = function () {
    var storageKey = this.getScratchpadStorageKey() + '-value';

    // get position key for removing old position data
    var positionMasterKey = this.getScratchpadStorageKey() + '-position';

    var oldScratchValue = window.localStorage.getItem(storageKey);

    var $scratchInput = $(this.options.scratchpadInputSelector);

    // In case the local storage data is modified to invalid JSON string
    if (oldScratchValue && this.isJSONString(oldScratchValue)) {

      oldScratchValue = JSON.parse(oldScratchValue);
      // further prevention
      if(typeof oldScratchValue !== 'object' || !oldScratchValue.date || oldScratchValue.date !== this.options.currentDate){
        // remove invalid or old data
        window.localStorage.removeItem(storageKey);
        window.localStorage.removeItem(positionMasterKey + '-top');
        window.localStorage.removeItem(positionMasterKey + '-left');
        return
      }

      $scratchInput.val(oldScratchValue.notes);
      this.showScratchpad();
    }
  };

  TicketMoveController.prototype.saveScratchpadData = function (data) {
    var storageKey = this.getScratchpadStorageKey() + '-value';
    window.localStorage.setItem(storageKey, JSON.stringify(data));
  };

  TicketMoveController.prototype.saveScratchpadPosition = function (position) {
    var storageKey = this.getScratchpadStorageKey() + '-position';
    window.localStorage.setItem(storageKey + '-top', position.top);
    window.localStorage.setItem(storageKey + '-left', position.left);
  };

  TicketMoveController.prototype.getSavedScratchpadPosition = function () {
    var storageKey = this.getScratchpadStorageKey() + '-position';
    return {
      top: parseInt(window.localStorage.getItem(storageKey + '-top')),
      left: parseInt(window.localStorage.getItem(storageKey + '-left'))
    };
  };

  TicketMoveController.prototype.getScratchpadStorageKey = function () {
    // getting ticket_id from hidden input
    var form = this.options.formClass;
    var ticket_id = $(form).find('input[name="ticket_id"]').val();

    return 'sratchpad_' + '_' + user_id + '_' + ticket_id;
  };

    //This is set when document is ready
    var initialContentHash = '';

    function getContentHash() {
        var result = '';
        $('main#event-content').children().each(function () {
            if (!$(this).hasClass('js-patient-messages')) {
                //Only keep a hash of the content to minimize the size. This takes <10ms
                result += hashCode($(this).serialize());
            }
        });
        return result;
    }

    var setOnBeforeUnload = function () {
        window.onbeforeunload = function (e) {
            //Check if this is a submit (don't stop users from moving if they are saving)
            if (e.target.activeElement.type === 'submit') {
                return null;
            }
            if (initialContentHash !== getContentHash()) {
                //This console log is used in warning-dialog-test.cy.js as I couldn't find another way to test
                //onbeforeunload behavior on cypress as cypress is doing user inputs and doesn't show the dialog then
                console.log("Show Changes you made may not be saved message.");
                return true;
            } else {
                return null;
            }
        };
    };

    function hashCode(s) {
        for(var i = 0, h = 0; i < s.length; i++)
            h = Math.imul(31, h) + s.charCodeAt(i) | 0;
        return h;
    }

    document.addEventListener("DOMContentLoaded", setOnBeforeUnload);
  $(document).ready(function () {
    autosize($('.autosize'));
    var ticketMoveController = new TicketMoveController();
    window.ticketMoveController = ticketMoveController;
    ticketMoveController.loadScratchpadData();

    initialContentHash = getContentHash();

    document.querySelectorAll('button[type="submit"]').forEach(function (submitButton) {
        submitButton.addEventListener('click', function () {
            window.onbeforeunload = null;
        });
    });

    $(document).on('click', ticketMoveController.options.formClass + ' .ok', function (e) {
      e.preventDefault();
      ticketMoveController.submitForm($(this).closest('form'));
    });

    $(document).on('click', ticketMoveController.options.formClass + ' .cancel', function () {
      var queueset_id = $(this).data('queue');
      var category_id = $(this).data('category');
      delete(window.changedTickets[queueset_id]);
      if (Object.keys(window.changedTickets).length == 0) window.patientTicketChanged = false;
      //$(this).parents('.alert-box').find('.js-toggle').trigger('click');
      window.location = "/PatientTicketing/default/?queueset_id=" + queueset_id + "&cat_id=" + category_id+"&unset_patientticketing=true";
    });

    $(document).on('click', '.js-auto-save', function (e) {
      e.preventDefault();
      ticketMoveController.navigateToEvent(($(this).closest('form')), $(this).attr('href'));
    });

    $(this).on('change', ticketMoveController.options.scratchpadInputSelector, function () {
      ticketMoveController.saveScratchpadData(
        {
          notes: $(this).val(),
          date: ticketMoveController.options.currentDate
        }
      );
    });

    $(this).on('click', ticketMoveController.options.scratchpadButtonSelector, function () {
      ticketMoveController.toggleScratchpad(!$(ticketMoveController.options.scratchpadPopupSelector).is(':visible'));
    });

    $('select[name="to_queue_id"]').on('change', function () {
      ticketMoveController.getQueueAssForm($(this).val(), function (response) {
        $('#PatientTicketing-queue-assignment').html(response);

        $(document).on('change', '.outcome-select', function() {
          let fup = $(this).find('option:selected').data('followup');
          let form_name = $(this).parents('tbody').data('formname');
          $('#' + form_name + '-followup').toggle(fup);
        });
      });
    });
  });
})();
