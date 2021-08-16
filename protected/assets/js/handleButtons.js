$(document).ready(function(){
  handleButton($('#et_save'), function (e) {
  });

  handleButton($('#et_cancel'), function (e) {
    e.preventDefault();
    var hrefArray,
      page;

    if ($(e.target).data('uri')) {
      window.location.href = $(e.target).data('uri');
    } else {
      hrefArray = window.location.href.split('/');
      page = false;

      if (parseInt(hrefArray[hrefArray.length - 1])) {
        page = Math.ceil(parseInt(hrefArray[hrefArray.length - 1]) / items_per_page);
      }

      for (var i = 0; i < hrefArray.length; i++) {
        if (hrefArray[i] === 'admin') {
          object = ucfirst(hrefArray[parseInt(i) + 1].replace(/ies$/, 'y'));
          if((object === 'EditUser') || (object === 'AddUser')) {
            window.location.href = baseUrl + '/admin/users';
          }else {
            var object = e[parseInt(i) + 1].replace(/^[a-z]+/, '').toLowerCase() + 's';
            window.location.href = baseUrl + '/admin/' + object + (page ? '/' + page : '');
          }
        }
      }
    }
  });

  handleButton($('#et_contact_cancel'), function (e) {
    e.preventDefault();
    history.back();
  });

  handleButton($('#et_add'), function (e) {
    e.preventDefault();
    var object,
      hrefArray;

    if ($(e.target).data('uri')) {
      window.location.href = baseUrl + $(e.target).data('uri');
    } else {
      hrefArray = window.location.href.split('?')[0].split('/');

      for (var i = 0; i < hrefArray.length; i++) {
        if (hrefArray[i] === 'admin') {
          if (hrefArray[parseInt(i) + 1].match(/ies$/)) {
            object = ucfirst(hrefArray[parseInt(i) + 1].replace(/ies$/, 'y'));
          } else {
            object = ucfirst(hrefArray[parseInt(i) + 1].replace(/s$/, ''));
          }
          window.location.href = baseUrl + '/admin/add' + object;
        }
      }
    }
  });

  handleButton($('#et_delete'), function (e) {
    e.preventDefault();
    var object,
      hrefArray,
      uri,
      serializedForm,
      $form;

    if ($(e.target).data('object')) {
      object = $(e.target).data('object');
      if (object.charAt(object.length - 1) !== 's') {
        object = object + 's';
      }
    } else {
      hrefArray = window.location.href.split('?')[0].split('/');
      for (var i = 0; i < hrefArray.length; i++) {
        if (hrefArray[i] === 'admin') {
          object = hrefArray[parseInt(i) + 1].replace(/s$/, '');
        }
      }
    }

    $form = $('#admin_' + object);
    if($('#generic-admin-list, #generic-admin-form').length){
      $form = $('#generic-admin-list, #generic-admin-form');
    }
    serializedForm = $form.serialize();

    if ( $form.find('table.standard tbody input[type="checkbox"]:checked').length === 0 ) {
      new OpenEyes.UI.Dialog.Alert({
        content: "Please select one or more items to delete.",
        closeCallback: function () {
            enableButtons();
        }
      }).open();
      return;
    }

    if ($(e.target).data('uri')) {
      uri = baseUrl + $(e.target).data('uri');
    } else {
      uri = baseUrl + '/admin/delete' + ucfirst(object);
    }

    $.when(et_delete_ajax_call(uri, serializedForm)).done(function(html){
      if (html === '1') {
        // CERA-573 inactivate user should not delete that user from the front-end. It should update the active status.
        if(object==='users' || object.toLowerCase() === 'teams' || object.toLowerCase() === 'pgdpsds'){
          updateActiveIcon($form);
        } else {
          $form.find('table.standard tbody input[type="checkbox"]:checked').closest('tr').remove();
        }
        enableButtons();
      } else {
        if(html.indexOf('Attribute Element is in use') !== -1){
          new OpenEyes.UI.Dialog.Confirm({
            content: "One or more Element attributes has sub-options. Continue to delete?",
            closeCallback: function () {
                enableButtons();
            }
          }).on('ok', function () {
            et_delete_ajax_call(uri, serializedForm,1);
            $form.find('table.standard tbody input[type="checkbox"]:checked').closest('tr').remove();
            enableButtons();
          }).open();
        } else {
          let reponse = JSON.parse(html);
          let msg = "One or more Element attributes could not be deleted as they are in use.";
          if (reponse.message) {
            msg = reponse.message;
          }
          new OpenEyes.UI.Dialog.Alert({
            content: msg,
            closeCallback: function () {
                enableButtons();
            }
          }).open();
        }
      }
    });
  });

  handleButton($('#et_add_label'), function (e) {
    e.preventDefault();
    /* TODO */
  });

  handleButton($('#admin_event_deletion_requests #et_approve'), function (e) {
    e.preventDefault();

    var id = $(e.target).parent().parent().data('id');

    $.ajax({
      'type': 'GET',
      'url': baseUrl + '/admin/approveEventDeletionRequest/' + id,
      'success': function (resp) {
        if (resp == "1") {
          window.location.reload();
        } else {
          alert("Something went wrong trying to approve the deletion request.  Please try again or contact support for assistance.");
        }
      }
    });
  });

  handleButton($('#admin_event_deletion_requests #et_reject'), function (e) {
    e.preventDefault();

    var id = $(e.target).parent().parent().data('id');

    $.ajax({
      'type': 'GET',
      'url': baseUrl + '/admin/rejectEventDeletionRequest/' + id,
      'success': function (resp) {
        if (resp === "1") {
          window.location.reload();
        } else {
          alert("Something went wrong trying to reject the deletion request.  Please try again or contact support for assistance.");
        }
      }
    });
  });

  function et_delete_ajax_call(uri, serializedForm, delete_subs_also = 0){
    return $.ajax({
      'type': 'POST',
      'url': uri,
      'data': serializedForm + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN + (delete_subs_also ? "&DELETE_SUBS_ALSO=1" : ""),
    });
  }
});