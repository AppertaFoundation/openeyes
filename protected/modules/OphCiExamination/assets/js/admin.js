$(document).ready(function() {

  let workflow_edited = false;

  let bindActionWithWorkflowWarning = function (selector, actionType, actionFunction) {
    $(selector).on(actionType, function(e) {
      e.preventDefault();
      let currentElement = $(this);

      if (workflow_edited) {
        let confirmationDialog = new OpenEyes.UI.Dialog.Confirm({
          title: 'Warning',
          content: 'The current workflow order has unsaved changes, are you sure you want to continue?'
        });

        confirmationDialog.content.on('click', '.ok', () => {
          workflow_edited = false;
          actionFunction(e, currentElement);
        });

        confirmationDialog.open();
      } else {
        actionFunction(e, currentElement);
      }
    });
  };

    if (typeof(OphCiExamination_sort_url) !== 'undefined') {
        $('.sortable').sortable({
            update: function (event, ui) {
                var ids = [];
                $('div.sortable').children('li').map(function () {
                    ids.push($(this).attr('data-attr-id'));
                });
                $.ajax({
                    'type': 'POST',
                    'url': OphCiExamination_sort_url,
                    'data': {
                        order: ids,
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN
                    },
                    'success': function (data) {
                        new OpenEyes.UI.Dialog.Alert({
                            content: 'Questions reordered'
                        }).open();
                    }
                });
            }
        });
    }

	$('#question_disorder').bind('change', function() {
		var did = $(this).val(),
        	url;

		if (did) {
			url = '/' + OE_module_name + '/admin/ViewOphCiExamination_InjectionManagementComplex_Question?disorder_id=';
            console.log(url + did);
            window.location.href = url + did;
		}
	});

	$('input.model_enabled').bind('change', function() {
		var model_id = $(this).parents('tr').data('attr-id');
		var model_name = $(this).parents('tr').data('attr-name');

		var enabled = 0;
		if ($(this).attr('checked')) {
			enabled = 1;
		}
		$.ajax({
			type: 'POST',
			url: OphCiExamination_model_status_url,
			data: {
				id: model_id,
				enabled: enabled,
				YII_CSRF_TOKEN: YII_CSRF_TOKEN
			},
			'success': function() {
				if (enabled) {
					new OpenEyes.UI.Dialog.Alert({
						content: model_name + ' enabled'
					}).open();
				}
				else {
					new OpenEyes.UI.Dialog.Alert({
						content: model_name + ' disabled'
					}).open();
				}
			}
		});
	});

	window.editWorkflowStepAjaxInProgress = false;
  bindActionWithWorkflowWarning('#admin_workflow_steps tbody tr', 'click', function(e, currentElement) {
		$('#admin_workflow_steps tbody tr').removeClass('selected');
		currentElement.addClass('selected');

		var id = currentElement.data('id');

        $.ajax({
            'type': 'GET',
            'url': baseUrl+'/OphCiExamination/admin/editWorkflowStep?step_id='+id,
            'beforeSend': function() {
                editWorkflowStepAjaxInProgress = true;
            },
            'success': function(html) {
                $('#step_element_types').html(html);
                bindWorkflowEditEventListeners();
            },
            'complete': function() {
                editWorkflowStepAjaxInProgress = false;
            }
		});
	});

	$('#admin_workflow_steps tbody').sortable();
	$('#admin_workflow_steps tbody').on('sortupdate',function(event, ui){
			var i = 1;

			var ids = {};

			$('#admin_workflow_steps tbody tr').map(function() {
				$(this).children('td:first').text(i);
				ids[$(this).data('id')] = i;
				i += 1;
			});

			$.ajax({
				'type': 'POST',
				'data': $.param(ids)+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'url': baseUrl+'/OphCiExamination/admin/reorderWorkflowSteps',
				'success': function(resp) {
					if (resp != "1") {
						alert("Something went wrong trying to save the new order.  Please refresh the page and try again or contact support for assistance.");
					}

					if (typeof ui === 'undefined') {
						//ui is undefined when .trigger('sortupdate') is called
						window.location.reload();
					}
				}
			});
	});

  let workflowFlash = (message='Workflow saved.', duration=3000) => {
    $('#workflow-flash').html(message);
    $('#workflow-flash').fadeIn();
    setTimeout(()=>$('#workflow-flash').fadeOut(), duration);
  };

  let changeWorkflowButton = () => {
    let $workflow_contextual_button = $('#et_workflow_contextual_button');
    $workflow_contextual_button.fadeOut( function () {
      if (workflow_edited) {
        if ($('#display_order_edited').val() === '1') {
          $(this).html('Save new element order');
        } else {
          $(this).html('Save element order');
        }
        $(this).attr('name', 'save_workflow');
      } else {
        $(this).html('Reset element order to default');
        $(this).attr('name', 'reset_workflow');
      }
      $(this).prop("disabled", false);
    }).fadeIn();
  };

  let workflowResetHandler = e => {
    e.preventDefault();
    workflow_edited = false;
    $('.spinner').css('display', 'block');

    $.ajax({
      'type': 'GET',
      'url' : baseUrl + '/OphCiExamination/admin/setWorkflowToDefault?element_set_id=' + e.target.dataset['element_set_id'],
      'success': function() {
        workflowFlash('Worflow reset.');


        $.ajax({
          'type': 'GET',
          'url' : baseUrl + '/OphCiExamination/admin/editWorkflowStep?step_id=' + e.target.dataset['element_set_id'],
          'success': function (html) {
            $('#step_element_types').html(html);
            bindWorkflowEditEventListeners();
            $('.spinner').css('display', 'none');
          },
          'error': function () {
            workflowFlash('Workflow reset, please refresh.');
          }
        });
      },
      'error': function (jqXHR, status) {
        workflowFlash('Failed to reset workflow.');
        alert(jqXHR.responseText);
      }
    }).then(setWorkflowDisplayOrderEditStatus(0));
  };

  let workflowSaveHandler = e => {
    e.preventDefault();

    $('#et_workflow_contextual_button').prop("disabled", true);
    $('.spinner').css('display', 'block');
    let $form = $('#et_sort').closest('form');
    $.ajax({
      'type': 'POST',
      'url': $('#et_sort').data('uri'),
      'data': $form.serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
      'success': function(){
        workflowFlash();
        $('.spinner').css('display', 'none');
        workflow_edited = false;
        changeWorkflowButton();
      },
      'error': function (jqXHR, status) {
        workflowFlash('Failed to save workflow.');
        alert(jqXHR.responseText);
      }
    }).then(setWorkflowDisplayOrderEditStatus(1));
  };

  let setWorkflowDisplayOrderEditStatus = status => {
    $('#display_order_edited').val(status);
    return $.ajax({
      'type': 'POST',
      'data': 'workflow_id='+$('#OEModule_OphCiExamination_models_OphCiExamination_Workflow_id').val()+'&element_set_id='+$('#admin_workflow_steps tbody tr.selected').data('id')+'&display_order_edited='+ status +'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
      'url': baseUrl+'/OphCiExamination/admin/saveWorkflowDisplayOrderEditStatus',
      'success': function(resp) {
        if (resp != "1") {
          alert("Something went wrong trying to save the element display order.  Please try again or contact support for assistance.");
        }
      }
    });
  };

  let workflowAddHandler = e => {
    if ($('#element_type_id').val() === '') {
      alert("Please select an element type to add");
      return;
    }

    let data = {
      'element_type_id': $('#element_type_id').val(),
      'step_id': $('#admin_workflow_steps tr.selected').data('id'),
      'YII_CSRF_TOKEN': YII_CSRF_TOKEN
    };

    $.ajax({
      'type': 'POST',
      'data': data,
      'url': baseUrl+'/OphCiExamination/admin/addElementTypeToWorkflowStep',
      'success': function(resp) {
        if (resp !== "1") {
          alert("Something went wrong trying to add the element type.  Please try again or contact support for assistance.");
        } else {
            $('#admin_workflow_steps tr.selected').click();
            const display_order_edited = document.getElementById("display_order_edited").value === '1';
            const checker = () => {
                if (window.editWorkflowStepAjaxInProgress === false) {
                    if (display_order_edited) {
                        workflowSaveHandler(e);
                    }
                    clearInterval(interval);
                }
            };

            const interval = setInterval(checker, 250);
        }
      }
    });
  };

  let workflowSaveNameHandler = e => {
    if ($('#step_name').val() == '') {
      alert("Name cannot be blank");
      return;
    }

    $.ajax({
      'type': 'POST',
      'data': 'workflow_id='+$('#OEModule_OphCiExamination_models_OphCiExamination_Workflow_id').val()+'&element_set_id='+$('#admin_workflow_steps tbody tr.selected').data('id')+'&step_name='+$('#step_name').val()+'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
      'url': baseUrl+'/OphCiExamination/admin/saveWorkflowStepName',
      'success': function(resp) {
        if (resp != "1") {
          alert("Something went wrong trying to set the name for the step.  Please try again or contact support for assistance.");
        } else {
          $('#admin_workflow_steps tr.selected td:nth-child(2)').text($('#step_name').val());
        }
      }
    });
  };

  let workflowRemoveElementHandler = function(e) {
    e.preventDefault();

    let row = $(this).parent().parent();
    let element_type_name = row.children('td:nth-child(2)').text();
    let element_type_id = $(this).data('element-type-id');

    let data = {
      'element_type_item_id': $(this).attr('rel'),
      'step_id': $('#admin_workflow_steps tr.selected').data('id'),
      'YII_CSRF_TOKEN': YII_CSRF_TOKEN,
    };

    $.ajax({
      'type': 'POST',
      'data': data,
      'url': baseUrl+'/OphCiExamination/admin/removeElementTypeFromWorkflowStep',
      'success': function(resp) {
        if (resp !== "1") {
          alert("Something went wrong trying to remove the element type.	Please try again or contact support for assistance.");
        } else {
          row.remove();
          $('#element_type_id').append('<option value="'+element_type_id+'">'+element_type_name+'</option>');
          sort_selectbox($('#element_type_id'));
        }
      }
    });
  };

  let bindWorkflowEditEventListeners = () => {
    // Activate edit mode when a change is made
    $('#step_element_types tbody').sortable({
      stop: (event, ui) => {
        if (!workflow_edited) {
          workflow_edited = true;
          changeWorkflowButton();
        }
      }
    });

    $('#et_workflow_contextual_button').click( e => {
      e.preventDefault();
      if (e.target.name === 'save_workflow') {
        workflowSaveHandler(e)
      } else if (e.target.name === 'reset_workflow') {
        workflowResetHandler(e);
      }
    });

    // Bind Add element button to current flow.
    bindActionWithWorkflowWarning('#et_add_element_type', 'click', workflowAddHandler);

	  bindActionWithWorkflowWarning('#et_save_step_name', 'click', workflowSaveNameHandler);

    // Bind Remove element action to current flow rows.
    $('a.removeElementType').click(workflowRemoveElementHandler);
  }

    $('#step_element_types').on('click', 'tr.clickable .workflow-item-attr',  function() {
        var item = this,
            $itemTd = $(this).parent(),
            $itemTr = $itemTd.parent(),
            itemId = $itemTr.data('id'),
			itemObj = {};

        if(($itemTr.find(':checkbox:checked[id*="is_hidden"]').length && $itemTr.find(':checkbox:checked[id*="is_mandatory"]').length)){
            alert('An element cannot be both Mandatory and Hidden. Please correct this error before saving.');
            item.checked = false;
            //return;
        }

        $itemTd.append('<img src="'+baseUrl+OE_core_asset_path+'/img/ajax-loader.gif" class="loader" />');
        if(this.type === "number") {
        	if(this.value === "0" || this.value.trim() === "") {
        		itemObj[this.name] = null;
			} else {
				itemObj[this.name] = this.value;
			}
		} else {
			itemObj[this.name] = this.checked ? 1 : 0;
		}
		itemObj['YII_CSRF_TOKEN'] = YII_CSRF_TOKEN;

		$.ajax({
			'type': 'POST',
			'data': itemObj,
			'url': baseUrl+'/OphCiExamination/admin/updateElementAttribute/' + itemId,
			'success': function(resp) {
                $itemTd.find('.loader').remove();
			},
            error: function(resp){
                item.checked = false;
                $itemTd.find('.loader').remove();
                alert('An issue occurred trying to save the attribute, please try again');
            }
		});
	});

  bindActionWithWorkflowWarning('#et_add_step', 'click', function (e) {
		$.ajax({
			'type': 'POST',
			'dataType': 'json',
			'data': 'workflow_id='+$('#OEModule_OphCiExamination_models_OphCiExamination_Workflow_id').val()+'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'url': baseUrl+'/OphCiExamination/admin/addWorkflowStep',
			'success': function(data) {
				if (typeof(data['id']) == 'undefined') {
					alert("Something went wrong trying to add the workflow step.	Please try again or contact support for assistance.");
				} else {
					window.location.reload();
				}
			}
		});
	});

	$('a.removeElementSet').click(function(e) {
		e.preventDefault();

		var element_set_id = $(this).attr('rel');

		$.ajax({
			'type': 'POST',
			'data': 'workflow_id='+$('#OEModule_OphCiExamination_models_OphCiExamination_Workflow_id').val()+'&element_set_id='+element_set_id+'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'url': baseUrl+'/OphCiExamination/admin/removeWorkflowStep',
			'success': function(resp) {
				if (resp != "1") {
					alert("Something went wrong trying to remove the workflow step.  Please try again or contact support for assistance.");
				} else {
					$("#admin_workflow_steps tbody > tr.selectable").filter("[data-id='"+element_set_id+"']").remove();
					$('#admin_workflow_steps tbody').trigger('sortupdate');
				}
			}
		});
	});

	$('.js-elementSetActiveStatus').click(function(){
		let element = $(this);
		let element_set_id = element.data('stepid');
		let element_status = element.text();
		$.ajax({
			'type': 'POST',
			'data': 'workflow_id='+$('#OEModule_OphCiExamination_models_OphCiExamination_Workflow_id').val()+'&element_set_id='+element_set_id+'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'url': baseUrl+'/OphCiExamination/admin/changeWorkflowStepActiveStatus',
			'success': function(resp){
				if (resp !== "1") {
					alert("Something went wrong trying to "+element_status+" the workflow step.  Please try again or contact support for assistance.");
				} else {
					element.text((element_status == 'Disable' ? 'Enable' : 'Disable'));
				}
			}
		});
	});
});
