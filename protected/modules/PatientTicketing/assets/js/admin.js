(function () {
  function QueueAdmin(options) {
    this.options = $.extend(true, {}, QueueAdmin._defaultOptions, options);
    this.init();
    this.currentDisplayedQueueId = null;

    /**
     * POSTs the given data to the given URI. if a removeNavId is provided, will strip that item from the queue nav,
     * otherwise will call reloadQueue to redisplay the current queue.
     *
     * @param uri
     * @param data
     * @param description
     * @param removeNavId
     */
    this.ajaxPostAndReload = function (uri, data, description, message, removeNavId) {
      data.YII_CSRF_TOKEN = YII_CSRF_TOKEN;
      $.ajax({
        url: uri,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (resp) {
          if (removeNavId) {
            $('#chart').html('');
            this.currentDisplayedQueueId = null;
            $('#queue-nav-' + removeNavId).remove();
          }
          else {
            this.reloadQueue();
          }
          if (message) {
            this.displayMessage(message);
          }
        }.bind(this),
        error: function (jqXHR, status, error) {
          new OpenEyes.UI.Dialog.Alert({content: 'There was a problem ' + description}).open();
        }
      });
    }
  }

  QueueAdmin._defaultOptions = {
    'setSelector': '.queue-set',
    'parentSelector': '.initial-queue',
    'childSelector': '.child-queue',
    'addQueueSetURI': '/PatientTicketing/admin/addQueueSet',
    'editQueueSetURI': '/PatientTicketing/admin/updateQueueSet',
    'editQueueSetPermissionsURI': '/PatientTicketing/admin/queueSetPermissions',
    'addQueueURI': '/PatientTicketing/admin/addQueue',
    'editQueueURI': '/PatientTicketing/admin/updateQueue',
    'loadQueueURI': '/PatientTicketing/admin/loadQueueNav',
    'deactivateQueueURI': '/PatientTicketing/admin/deactivateQueue',
    'activateQueueURI': '/PatientTicketing/admin/activateQueue',
    'deleteQueueURI': '/PatientTicketing/admin/deleteQueue',
    'ticketStatusURI': '/PatientTicketing/admin/getQueueTicketStatus'
  };

  QueueAdmin.prototype.init = function () {
  }

  QueueAdmin.prototype.displayMessage = function (message) {
    $('#message-box').text(message).slideDown().fadeTo(300, 1.0, function () {
      $(this).delay(2000).fadeTo(1000, 0.0, function () {
        $(this).slideUp()
      });
    });
  }

  QueueAdmin.prototype.displayQueue = function (queueId) {
    var selector = '#queue-container-' + queueId;
    $('#chart').html('');
    $(selector).jOrgChart({
      chartElement: '#chart',
      dragAndDrop: false,
      showSelector: '.show-children',
      hideSelector: '.hide-children',
      expansionSelector: '.expansion-controls'
    });
    this.currentDisplayedQueueId = queueId;
  }

  QueueAdmin.prototype.reloadQueue = function (queueId) {
    $('#chart').html('');
    if (!queueId) {
      queueId = this.currentDisplayedQueueId;
    }
    $.ajax({
      url: this.options.loadQueueURI,
      data: {id: queueId},
      dataType: "json",
      success: function (resp) {
        if ($('#queue-nav-' + resp.rootid).replaceWith(resp.nav).length) {
          $('#queue-nav-' + resp.rootid).replaceWith(resp.nav);
        }
        else {
          $('#queue-nav').append(resp.nav);
        }
        this.displayQueue(resp.rootid)
      }.bind(this),
      error: function (jqXHR, status, error) {
        new OpenEyes.UI.Dialog.Alert({content: 'There was a problem reloading the queue data'}).open();
      }
    });
  }

  QueueAdmin.prototype.addQueueSet = function () {
    $.ajax({
      url: this.options.addQueueSetURI,
      success: function (content) {
        var formDialog = new OpenEyes.UI.Dialog.Confirm({
          title: "Add Queue Set",
          'content': content,
          'okButton': 'Save'
        });
        formDialog.open();
        // suppress default ok behaviour
        formDialog.content.off('click', '.ok');
        // manage form submission and response
        formDialog.content.on('click', '.ok', function () {
          this.submitQueueSetForm(formDialog, this.options.addQueueSetURI)
        }.bind(this));
      }.bind(this)
    })
  }

  QueueAdmin.prototype.editQueueSet = function (queueSetId) {
    $.ajax({
      url: this.options.editQueueSetURI,
      data: {id: queueSetId},
      dataType: 'html',
      success: function (content) {
        var formDialog = new OpenEyes.UI.Dialog.Confirm({
          title: "Edit Queue Set",
          content: content,
          okButton: 'Save'
        });
        formDialog.open();
        // suppress default ok behaviour
        formDialog.content.off('click', '.ok');
        // manage form submission and response
        formDialog.content.on('click', '.ok', function () {
          this.submitQueueSetForm(formDialog, this.options.editQueueSetURI + '?id=' + queueSetId)
        }.bind(this));
      }.bind(this)
    });
  }

  QueueAdmin.prototype.editQueueSetPermissions = function (queueSetId) {
    var formDialog = new OpenEyes.UI.Dialog.Confirm({
      title: "Edit Queue Set Permissions",
      okButton: 'Save',
      url: this.options.editQueueSetPermissionsURI,
      data: {id: queueSetId}
    });
    formDialog.open();
    // suppress default ok behaviour
    formDialog.content.off('click', '.ok');
    // manage form submission and response
    formDialog.content.on('click', '.ok', function () {
      this.submitQueueSetForm(formDialog, this.options.editQueueSetPermissionsURI + '?id=' + queueSetId)
    }.bind(this));

  }

  QueueAdmin.prototype.addQueue = function (queueId) {
    $.ajax({
      url: this.options.addQueueURI,
      data: {parent_id: queueId},
      success: function (content) {
        var formDialog = new OpenEyes.UI.Dialog.Confirm({
          title: "Add Queue",
          content: content,
          okButton: 'Save'
        });
        formDialog.on('open', function () {
          formDialog.content.find('.multi-select').trigger('init');
        });
        formDialog.open();
        // suppress default ok behaviour
        formDialog.content.off('click', '.ok');
        // manage form submission and response
        formDialog.content.on('click', '.ok', function () {
          this.submitQueueForm(formDialog, this.options.addQueueURI)
        }.bind(this));
      }.bind(this)
    });
  }

  QueueAdmin.prototype.editQueue = function (queueId) {
    $.ajax({
      url: this.options.editQueueURI,
      data: {id: queueId},
      success: function (content) {
        var formDialog = new OpenEyes.UI.Dialog.Confirm({
          title: "Edit Queue",
          content: content,
          okButton: 'Save'
        });
        formDialog.on('open', function () {
          formDialog.content.find('.multi-select').trigger('init');
        });
        formDialog.open();
        // suppress default ok behaviour
        formDialog.content.off('click', '.ok');
        // manage form submission and response
        formDialog.content.on('click', '.ok', function () {
          this.submitQueueForm(formDialog, this.options.editQueueURI + '?id=' + queueId);
        }.bind(this));
      }.bind(this)
    });
  }

  QueueAdmin.prototype.submitQueueForm = function (formDialog, submitURI) {
    $.ajax({
      url: submitURI,
      data: formDialog.content.find('form').serialize(),
      type: 'POST',
      dataType: 'json',
      success: function (resp) {
        if (resp.success) {
          formDialog.close();
          this.reloadQueue(resp.queueId);
        }
        else {
          formDialog.setContent(resp.form);
        }
      }.bind(this),
      error: function (jqXHR, status, error) {
        formDialog.close();
        new OpenEyes.UI.Dialog.Alert({content: 'There was a problem saving the queue'}).open();
      }
    });
  }

  QueueAdmin.prototype.submitQueueSetForm = function (formDialog, submitURI) {
    $.ajax({
      url: submitURI,
      data: formDialog.content.find('form').serialize(),
      type: 'POST',
      dataType: 'json',
      success: function (resp) {
        if (resp.success) {
          formDialog.close();
          this.reloadQueue(resp.initialQueueId);
          if (resp.message) {
            this.displayMessage(resp.message);
          }
          else {
            this.displayMessage("Queue set saved. Please wait...reloading queue sets");
          }
          location.reload();
        }
        else {
          formDialog.removeContent();
          formDialog.setContent(resp.form);
        }
      }.bind(this),
      error: function (jqXHR, status, error) {
        formDialog.close();
        new OpenEyes.UI.Dialog.Alert({content: 'There was a problem saving the queue set'}).open();
      }
    });
  }

  QueueAdmin.prototype.activeToggleQueue = function (queueId, active) {
    if (active) {
      $.ajax({
        url: this.options.ticketStatusURI,
        data: {id: queueId},
        dataType: 'json',
        success: function (resp) {
          if (resp.can_delete) {
            var deleteDialog = new OpenEyes.UI.Dialog.Confirm({
              content: "There are no tickets assigned to this queue or its children, you may delete or deactive it.",
              okButton: "Delete",
              cancelButton: "Deactivate"
            });
            deleteDialog.open();
            // manage form submission and response
            var removeId = null;
            if (this.currentDisplayedQueueId == queueId) {
              // we're removing the root queue, so we need to remove it from the nav when successful
              removeId = queueId;
            }
            deleteDialog.on('ok', function () {
              this.ajaxPostAndReload(this.options.deleteQueueURI, {id: queueId}, 'deleting queue', 'Queue deleted', removeId)
            }.bind(this));
            deleteDialog.on('cancel', function () {
              this.ajaxPostAndReload(this.options.deactivateQueueURI, {id: queueId}, 'changing queue state', 'Queue deactivated')
            }.bind(this));
          }
          else if (resp.current_count > 0) {
            var deactivateDialog = new OpenEyes.UI.Dialog.Confirm({
              title: "Queue has current tickets",
              content: "There are currently tickets assigned to this queue. Are you sure want to deactivate it?"
            });
            deactivateDialog.on('ok', function () {
              this.ajaxPostAndReload(this.options.deactivateQueueURI, {id: queueId}, 'changing queue state', 'Queue deactivated')
            }.bind(this));
            deactivateDialog.open();
          }
          else {
            this.ajaxPostAndReload(this.options.deactivateQueueURI, {id: queueId}, 'changing queue state', 'Queue deactivated');
          }
        }.bind(this)
      });
    }
    else {
      this.ajaxPostAndReload(this.options.activateQueueURI, {id: queueId}, 'changing queue state', 'Queue activated');
    }
  }

  $(document).ready(function () {
    var queueAdmin = new QueueAdmin();

    $(this).on('click', '#add-queueset', function () {
      queueAdmin.addQueueSet();
    });

    $(this).on('click', '.queueset-link', function () {
      queueAdmin.displayQueue($(this).parents('tr').data('initial-queue-id'));
    });

    $(this).on('click', '.queueset-admin .permissions', function (e) {
      e.preventDefault();
      var queueSetId = $(this).parents('tr').data('queueset-id');
      queueAdmin.editQueueSetPermissions(queueSetId);
      queueAdmin.displayQueue($(this).parents('tr').data('initial-queue-id'));
    });

    $(this).on('click', '.add-child', function (e) {
      var queueId = $(this).parent('div').data('queue-id');
      queueAdmin.addQueue(queueId);
    });

    $(this).on('click', '.queueset-admin .edit', function (e) {
      e.preventDefault();
      var queueSetId = $(this).parents('tr').data('queueset-id');
      queueAdmin.editQueueSet(queueSetId);
      queueAdmin.displayQueue($(this).parents('tr').data('initial-queue-id'));
    });

    $(this).on('click', '.queue .edit', function () {
      var queueId = $(this).parent('div').data('queue-id');
      queueAdmin.editQueue(queueId);
    });

    $(this).on('click', '.active-toggle', function () {
      var queueId = $(this).parent('div').data('queue-id');
      var active = !$(this).closest('div.node').hasClass('inactive');
      queueAdmin.activeToggleQueue(queueId, active);
    });

    $(this).on('click', '#current-users-list .remove', function () {
      $(this).parents('li').remove();
    });

  });
}());

