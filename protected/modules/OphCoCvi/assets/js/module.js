
/* Module-specific javascript can be placed here */

$(document).ready(function() {

    if (typeof(cvi_do_print) !== 'undefined' && cvi_do_print == 1) {
        doPrint();
    }

    handleButton($(".js-print-postal-form-btn"), function (e) {
        let role = $(".js-print-postal-form-btn").closest('tr').find('.js-signatory_role-field').val();
        let signatory = $(".js-print-postal-form-btn").closest('tr').find('.js-signatory_name-field').val();
        let _url = '/OphCoCvi/default/printQRSignature?event_id='
            +OE_event_id+'&role='+encodeURIComponent(role.replace(/'/g, '%27'))
            +'&signatory='+encodeURIComponent(signatory.replace(/'/g, '%27'));

        $frame = $("<iframe src='"+_url+"' style='position:fixed;left:-1000px;'></iframe>");
        $frame.appendTo("body");
        $frame.get(0).contentWindow.print();
        setTimeout(enableButtons, 2000);
    });

    handleButton($("#et_print_empty_consent"), function (e) {
        $frame = $("<iframe src='/OphCoCvi/default/printEmptyConsent?event_id="+OE_event_id+"' style='position:fixed;left:-1000px;'></iframe>");
        $frame.appendTo("body");
        $frame.get(0).contentWindow.print();
        setTimeout(enableButtons, 2000);
    });

    handleButton($("#et_print_info_sheet, #et_print_info_sheet_footer"), function (e) {
        $frame = $("<iframe src='/OphCoCvi/default/printInfoSheet' style='display: none;'></iframe>");
        $frame.appendTo("body");
        $frame.get(0).contentWindow.print();
        setTimeout(enableButtons, 2000);
    });

    handleButton($("#et_print_consent"), function (e) {
        $frame = $("<iframe src='/OphCoCvi/default/printConsent?event_id="+OE_event_id+"' style='position:fixed;left:-1000px;'></iframe>");
        $frame.appendTo("body");
        $frame.get(0).contentWindow.print();
        setTimeout(enableButtons, 2000);
    });

    handleButton($("#et_print_out"), function (e) {
        var element_id = $(e.target).data('element-id');
        var element_type_id = $(e.target).data('element-type-id');
        $frame = $("<iframe src='/OphCoCvi/default/printQRSignature?event_id="+OE_event_id+"&element_id="+element_id+"&element_type_id="+element_type_id+"' style='display: none;'></iframe>");
        $frame.appendTo("body");
        $frame.get(0).contentWindow.print();
        setTimeout(enableButtons, 2000);
    });

    handleButton($("#et_visually_impaired"), function (e) {
        $frame = $("<iframe src='/OphCoCvi/default/printVisualyImpaired?event_id="+OE_event_id+"' style='display: none;'></iframe>");
        $frame.appendTo("body");
        $frame.get(0).contentWindow.print();
        setTimeout(enableButtons, 2000);
    });

    $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_describe_ethnics').hide();
    openIfOtherEthnicity();


    $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_ethnic_group_id').change(function(){
        openIfOtherEthnicity();
    });

    $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_language_text').hide();

    $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_language_id').change(function(){
        var label_name = $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_language_id').find(":selected").text();
        if (label_name.toLowerCase().indexOf("other") >= 0) {
            $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_language_text').show();
        } else {
            $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_language_text').hide();
        }
    });


    $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_info_email').hide();

    $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_info_fmt_id').change(function(){
        var label_name = $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_info_fmt_id').find(":selected").text();
        if (label_name.toLowerCase().indexOf("email") >= 0) {
            $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_info_email').show();
        } else {
            $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_info_email').hide();
        }
    });

    handleButton($('#et_cancel'),function(e) {
        if (m = window.location.href.match(/\/update\/[0-9]+/)) {
            window.location.href = window.location.href.replace('/update/','/view/');
        } else {
            window.location.href = baseUrl+'/patient/episodes/'+OE_patient_id;
        }
        e.preventDefault();
    });

    handleButton($('#capture-patient-signature'), function(e) {

        $('#capture-patient-signature-instructions').show();
        $('#capture-patient-signature').parent().hide();
        // I honestly don't know wny this works, but it works and we have a demo to do:
        // FIXME: this seems ridiculous
        setTimeout(function() {e.preventDefault(); enableButtons();}, 100);
        return false;
    });

    $('#remove-patient-signature').on('click', function(e) {

        e.preventDefault();

        var confirmDialog = new OpenEyes.UI.Dialog.Confirm({
            title: "Remove Patient Signature",
            'content': 'Are you sure you want to delete the current Patient Signature?',
            'okButton': 'Remove'
        });
        confirmDialog.open();
        // suppress default ok behaviour
        confirmDialog.content.off('click', '.ok');
        // manage form submission and response
        confirmDialog.content.on('click', '.ok', function() {
            $('#remove-consent-signature-form').submit();
        });

        return false;
    });

    handleButton( $('#print-for-signature'),function(e) {
        var data = {'firstPage':'1'};
        printIFrameUrl($(e.target).data('print-url'), data);

        iframeId = 'print_content_iframe',
        $iframe = $('iframe#print_content_iframe');

        $iframe.load(function() {
            enableButtons();
            e.preventDefault();

            try{
                var PDF = document.getElementById(iframeId);
                PDF.focus();
                PDF.contentWindow.print();
            } catch (e) {
                alert("Exception thrown: " + e);
            }
        });
    });

    handleButton($('#et_print'),function(e) {
        doPrint(e);
    });

    handleButton($('#et_print_labels'),function() {

        var table = generateTable();
        var dialogContainer = '<div id="label-print-dialog">' +
                generateLabelInput() +
                table.outerHTML +
                '<button type="button" id="print-label">Print</button>' +
                '</div>';

        var labelDialog = new OpenEyes.UI.Dialog({
            content: dialogContainer,
            title: "Print Labels",
            autoOpen: false,
            onClose: function() { enableButtons(); }
        });

        labelDialog.open();

        const $label_print_btn = document.getElementById('print-label');
        OpenEyes.UI.DOM.addEventListener($label_print_btn, 'click', null, (e) => {
            const $label_input = document.getElementById('firstLabel');
            const num = $label_input.value;

            if (num > 0) {
                $label_input.style.backgroundColor = "#353333";
                const $error = document.getElementById('label-print-error');
                if ($error) {
                    $error.remove();
                }

                const data = { 'firstLabel': num };
                printIFrameUrl(label_print_url, data);

                const iframeId = 'print_content_iframe';
                const $iframe = $('iframe#print_content_iframe');

                $iframe.load(function() {
                    enableButtons();
                    e.preventDefault();

                    try {
                        const PDF = document.getElementById(iframeId);
                        PDF.focus();
                        PDF.contentWindow.print();

                    } catch (e) {
                        alert("Exception thrown: " + e);
                    }
                });
            } else {
                $label_input.style.backgroundColor = "#cd0000";
                const $error = OpenEyes.UI.DOM.createElement('span', { id: 'label-print-error', style: 'color:#ff6565; font-style:italic;margin-left:10px'});
                const $content = document.createTextNode("The value cannot be less than 1");
                $error.appendChild($content);

                $label_input.after($error);
            }
        });

        $('#printLabelPanel tr td').click(function(){
            $('#printLabelPanel tr td').removeClass('active-panel');
            $('#printLabelPanel tr td').text('Label');

            var tdID = $(this).attr('id').match(/\d+/);
            $('input#firstLabel').val(tdID);
            for(var i = 1; i<= tdID; i++ ){
                if(i == tdID){
                    $('#labelPanel_'+i).text('First empty label');
                } else {
                    $('#labelPanel_'+i).addClass('active-panel');
                    $('#labelPanel_'+i).text('Used label');
                }
            }
        });
        $('#firstLabel').keyup(function() {
            $('#printLabelPanel tr td').removeClass('active-panel');
            $('#printLabelPanel tr td').text('Label');

            var tdID = $(this).val();
            for(var i = 1; i <= tdID; i++ ){
                if(i == tdID){
                    $('#labelPanel_'+i).text('First empty label');
                } else {
                    $('#labelPanel_'+i).addClass('active-panel');
                    $('#labelPanel_'+i).text('Used label');
                }
            }
        });

    });

    handleButton($('#la-search-toggle'), function(e) {
        e.preventDefault();
        $('#local_authority_search_wrapper').show();
        setTimeout(function() {$(e.target).blur(); enableButtons(); $(e.target).addClass('disabled'); }, 100);
    });

    $('select.populate_textarea').unbind('change').change(function() {
        if ($(this).val() != '') {
            var cLass = $(this).parent().parent().parent().attr('class').match(/Element.*/);
            var el = $('#'+cLass+'_'+$(this).attr('id'));
            var currentText = el.text();
            var newText = $(this).children('option:selected').text();

            if (currentText.length == 0) {
                el.text(ucfirst(newText));
            } else {
                el.text(currentText+', '+newText);
            }
        }
    });

    // if a disorder is a main cause, it should be marked as "affected"
    $(document).on('change', '.disorder-main-cause', function(e) {
        if (e.target.checked) {
            $(this).closest('.column').find('.affected-selector[value="1"]').prop('checked', 'checked');
        }
    });

    $(document).on('change', 'input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature[is_patient]"][type="radio"]',function(e) {
        if ($(e.target).val() === '1') {
            $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature_representative_name').prop('disabled', 'disabled').closest('.field-row').hide();
        } else {
            $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature_representative_name').removeProp('disabled').closest('.field-row').show();
        }

    });

    $(document).on('change', '.collapse-group .js-right-eye, .collapse-group .js-left-eye', function(e) {
        const $td = e.target.closest('td');
        var current_group_id = $td.dataset.group_id;
        var data_id = $td.dataset.disorder_id;

        if (current_group_id) {
            $("input[data-group_id='" +current_group_id+ "'][type='radio']").each(function () {
                var current_data_id = $(this).attr('data-id');
                $(this).prop({checked: false});
                $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_right_disorders_'+current_data_id+'_main_cause').prop({checked: false});
                $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_right_disorders_'+current_data_id+'_main_cause').attr('disabled', 'disabled');
                $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_right_disorders_'+current_data_id+'_main_cause').removeAttr('data-active');
            });
            $(e.target).prop({checked: true});
        }

        const is_active = $td.querySelectorAll('.js-right-eye:checked, .js-left-eye:checked').length;
        const $main = document.getElementById(`OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_right_disorders_${data_id}_main_cause`);
        if (!is_active) {
            $main.checked = false;
        }

        $main.toggleAttribute('disabled', !is_active);
        $main.toggleAttribute('data-active', !is_active);
    });

    $(document).on('click', '.disorder-main-cause',function(e) {
        var current_checkbox = $(this);
        $('.disorder-main-cause').each(function() {
            if (current_checkbox.attr('id') != $(this).attr('id')) {
                if (current_checkbox.is(':checked')) {
                    $(this).prop({disabled: true, checked: false});
                } else {
                    if ($(this).attr('data-active') == 1) {
                        $(this).prop({disabled: false, checked: false});
                    }
                }
            } else {
                $(this).prop({ disabled: false});
            }
        });
    });

    $(document).on('click', '.js-unchecked-diagnosis-element',function(e) {
        e.preventDefault();
        var current_checkbox = $(this);
        var data_id = current_checkbox.attr('data-id');
        $('input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[disorders]['+data_id+'][affected]"][type="radio"]').each(function() {
            $(this).prop({checked: false});
            $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_right_disorders_'+data_id+'_main_cause').attr('disabled', 'disabled');
            $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_right_disorders_'+data_id+'_main_cause').removeAttr('data-active');
            $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_right_disorders_'+data_id+'_main_cause').prop({checked: false});
            $('.disorder-main-cause').each(function() {
                if ($(this).attr('data-active') == 1) {
                    $(this).prop({disabled: false, checked: false});
                }
            });
        });
    });

    $(document).on('change', 'input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[patient_type]"][type="radio"]',function(e) {
        var patient_type = this.value;

        var confirmDialogFirst = new OpenEyes.UI.Dialog.Confirm({
            title: "",
            'content': 'You are about to switch between adult and child diagnosis list. Are you sure you wish to continue?',
            'okButton': 'Continue'
        });
        confirmDialogFirst.open();
        confirmDialogFirst.content.off('click', '.ok');
        confirmDialogFirst.content.on('click', '.ok', function() {
            confirmDialogFirst.close();
            var confirmDialogSecond = new OpenEyes.UI.Dialog.Confirm({
                title: "",
                'content': 'Would you like to retain diagnoses you have selected?',
                'okButton': 'Yes',
                'cancelButton': 'No'
            });
            confirmDialogSecond.open();
            confirmDialogSecond.content.off('click', '.cancel');
            confirmDialogSecond.content.on('click', '.cancel', function() {

                var diagnosis_not_covered_list = [];
                $('#diagnosis_not_covered_table tr').each(function() {
                    var data_id = $(this).attr('data-id');
                    if (data_id !== '' || data_id !== 'undefined') {
                        var main_cause = $('input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered][' + data_id + '][main_cause]"][type="hidden"]').val();
                        var eyes = $('input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered][' + data_id + '][eyes]"][type="hidden"]').val();
                        diagnosis_not_covered_list[data_id] = {main_cause: main_cause, eyes: eyes};
                    }
                    $(this).remove();
                });
                $('#diagnosis_not_covered_table tbody').after('<tr data-id="1"></tr>');

                $.ajax({
                    'type': 'POST',
                    'url': baseUrl + '/OphCoCvi/Default/clinicalInfoDisorderList',
                    'data': {'patient_type': patient_type, 'diagnosis_not_covered_list': diagnosis_not_covered_list, YII_CSRF_TOKEN: YII_CSRF_TOKEN},
                    'success': function(data) {
                        $('#diagnosis_list').html(data);
                    }
                });
                confirmDialogSecond.close();
                confirmDialogFirst.close();
            });
            confirmDialogSecond.content.off('click', '.ok');
            confirmDialogSecond.content.on('click', '.ok', function() {

                var diagnosis_not_covered_list = [];
                $('#diagnosis_not_covered_table tr').each(function() {
                    var data_id = $(this).attr('data-id');
                    if (typeof data_id !== 'undefined' && data_id !== '') {
                        var main_cause = $('input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered][' + data_id + '][main_cause]"][type="hidden"]').val();
                        var eyes = $('input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered][' + data_id + '][eyes]"][type="hidden"]').val();
                        diagnosis_not_covered_list[data_id] = {main_cause: main_cause, eyes: eyes};
                        $('#diagnosis_not_covered_'+data_id).remove();
                    }
                });
                $.ajax({
                    'type': 'POST',
                    'url': baseUrl + '/OphCoCvi/Default/clinicalInfoDisorderList',
                    'data': {patient_type: patient_type, diagnosis_not_covered_list: diagnosis_not_covered_list, 'transfer_data':true, YII_CSRF_TOKEN: YII_CSRF_TOKEN},
                    'success': function(data) {

                        $('#diagnosis_list').html(data);
                    }
                });

                const getEye = function($tr) {
                    const $right = $tr.querySelector('.js-left-eye:checked');
                    const $left = $tr.querySelector('.js-right-eye:checked');

                    if ($right && $left) {
                        return 3;
                    }
                     return $left ? 1 : ($right ? 2 : null);
                };


                $('#diagnosis_list tr:has(input[type=checkbox]:checked) ').each(function() {
                    const $tr = $(this).closest('tr')[0];

                    var data_name = $tr.dataset.name;
                    var data_code = $tr.dataset.code;
                    var data_id = parseInt($tr.dataset.id);
                    var data_eye = getEye($tr);

                    var main_cause = '';
                    var main_cause_id = 0;

                    if ($tr.querySelector('input[name$="[main_cause]"]:checked')) {
                        main_cause = '(main cause)';
                        main_cause_id = 1;
                    }

                    var add_row_content = '<tr id="diagnosis_not_covered_'+data_id+'" data-id="'+data_id+'">\n' +
                            '                        <td>'+data_name+' '+main_cause+' - '+data_code+'</td>\n' +
                            '                        <td>\n' +
                            '                            <button class="button button-icon small js-remove-diagnosis-not-covered-element disabled" data-id="'+data_id+'" title="Delete Diagnosis">\n' +
                            '                                <span class="icon-button-small-mini-cross"></span>\n' +
                            '                                <span class="hide-offscreen">Remove element</span>\n' +
                            '                            </button>\n' +
                            '                        </td>\n' +
                            '<input type="hidden" name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+data_id+'][disorder_id]" value="'+data_id+'">' +
                            '<input type="hidden" name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+data_id+'][main_cause]" value="'+main_cause_id+'">' +
                            '<input type="hidden" name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+data_id+'][code]" value="'+data_code+'">' +
                            '<input type="hidden" name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+data_id+'][eyes]" value="'+data_eye+'">' +
                            '<input type="hidden" name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+data_id+'][disorder_type]" value="1">' +
                            '</tr>';
                    $('#diagnosis_not_covered_table tr:last').after(add_row_content);
                });

                confirmDialogSecond.close();
                confirmDialogFirst.close();
            });
        });
        confirmDialogFirst.content.off('click', '.cancel');
        confirmDialogFirst.content.on('click', '.cancel', function(e) {
            if (patient_type == 0) {
                $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_patient_type_0').attr('checked', false);
                $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_patient_type_1').attr('checked', true);
            } else if (patient_type == 1) {
                $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_patient_type_1').attr('checked', false);
                $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_patient_type_0').attr('checked', true);
            }
            confirmDialogFirst.close();
        });
    });

    $(document).on('change', 'input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo[patient_factors][24][is_factor]"][type="radio"]',function(e) {
        if ($(e.target).val() === '1') {
            $('#comment_15v1').show();
        } else {
            $('#comment_15v1').hide();
        }
    });

    $(document).on('click', '.js-remove-diagnosis-not-covered-element',function(e) {
        e.preventDefault();
        var data_id = $(this).attr('data-id');
        var confirmDialog = new OpenEyes.UI.Dialog.Confirm({
            title: "",
            'content': 'Are you sure you want to delete this diagnosis?',
            'okButton': 'Yes'
        });
        confirmDialog.open();
        confirmDialog.content.off('click', '.ok');
        confirmDialog.content.on('click', '.ok', function() {
            $('#diagnosis_not_covered_'+data_id).remove();
            $('input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+data_id+'][disorder_id]"]').remove();
            $('input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+data_id+'][main_cause]"]').remove();
            $('input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+data_id+'][code]"]').remove();
            $('input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+data_id+'][eyes]"]').remove();
            $('input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+data_id+'][disorder_type]"]').remove();
            confirmDialog.close();
        });
    });

    $(document).on('click', '#js-add-diagnosis-not-covered', function(e) {
        e.preventDefault();

        var disorder_id = $('#disorder_id').val();
        var disorder = $('#autocomplete_disorder_id');
        var main_cause = '';
        var main_cause_id = 0;
        var code = '';
        var eye_id = null;
        var eye_label = '';

        if (disorder === '') {
            disorder.css('border', '1px solid red');
            return false;
        }
        if (disorder_id === '') {
            disorder.css('border', '1px solid red');
            return false;
        }
        if ($('#dynamic_right_eye:checked, #dynamic_left_eye:checked').length === 0) {
            $('#dynamic_right_eye').closest('td').css('border', '1px solid red');
            return false;
        } else {
            $('#dynamic_right_eye').closest('td').css('border', '0px');
        }

        if($('#main_cause').prop("checked") === true){
            main_cause = '(main cause)';
            main_cause_id = 1;
        }

        if ($('#icd10').val() !== '') {
            code = ' - ' + $('#icd10').val();
        }

        if ($('#dynamic_right_eye:checked, #dynamic_left_eye:checked').length === 2) {
            eye_label = 'Bilateral';
            eye_id = 3;
        } else if ($('#dynamic_right_eye:checked').length) {
            eye_label = 'Right';
            eye_id = 2;
        } else if ($('#dynamic_left_eye:checked').length) {
            eye_label = 'Left';
            eye_id = 1;
        }

        var add_row_content = '<tr id="diagnosis_not_covered_'+disorder_id+'">\n' +
                '                        <td>'+eye_label+' '+disorder.val()+' '+main_cause+' '+code+'</td>\n' +
                '                        <td>\n' +
                '                            <button class="button button-icon small js-remove-diagnosis-not-covered-element" data-id="'+disorder_id+'" title="Delete Diagnosis">\n' +
                '                                <span class="icon-button-small-mini-cross"></span>\n' +
                '                                <span class="hide-offscreen">Remove element</span>\n' +
                '                            </button>\n' +
                '                        </td>\n' +
                '<input type="hidden" name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+disorder_id+'][disorder_id]" value="'+disorder_id+'">' +
                '<input type="hidden" name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+disorder_id+'][main_cause]" value="'+main_cause_id+'">' +
                '<input type="hidden" name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+disorder_id+'][code]" value="'+code+'">' +
                '<input type="hidden" name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+disorder_id+'][eyes]" value="'+eye_id+'">' +
                '<input type="hidden" name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[diagnosis_not_covered]['+disorder_id+'][disorder_type]" value="2">' +
                '</tr>';
        $('#diagnosis_not_covered_table tr:last').after(add_row_content);
        clearDiagnosisNotCoveredForm();
    });

    $(document).on('click', '#js-clear-diagnosis-not-covered',function(e) {
        e.preventDefault();
        clearDiagnosisNotCoveredForm();
    });

    function clearDiagnosisNotCoveredForm() {
        $('#autocomplete_disorder_id').val('');
        $('#disorder_id').val('');
        $('#main_cause').prop("checked", false);
        $('#icd10').val('');
        $('#dynamic_right_eye:checked, #dynamic_left_eye:checked').prop("checked", false);
    }


    $('#js-show_icd10_code').on('change',function(e) {
        if(this.checked) {
            $('.icd10code').show();
        } else {
            $('.icd10code').hide();
        }
    });


    $(document).on('click', '.disorder-main-cause',function(e) {
        var current_checkbox = $(this);
        $('.disorder-main-cause').each(function() {
            if (current_checkbox.attr('id') != $(this).attr('id')) {
                if (current_checkbox.is(':checked')) {
                    $(this).prop({disabled: true, checked: false});
                } else {
                    $(this).prop({disabled: false, checked: false});
                }
            } else {
                $(this).prop({ disabled: false});
            }
        });
    });

    $(document).on('click', '.js-unchecked-diagnosis-element',function(e) {
        e.preventDefault();
        var current_checkbox = $(this);
        var data_id = current_checkbox.attr('data-id');
        $('input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo[disorders]['+data_id+'][affected]"][type="radio"]').each(function() {
            $(this).prop({checked: false});
        });
    });

    $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_best_corrected_right_va_list').on('change',function(){
        updateVisualAcuityDropdown( $(this).val(), 'right');
        updateVisualAcuityDropdown( $(this).val(), 'left');
        updateVisualAcuityDropdown( $(this).val(), 'binocular');
        $('#best_corrected_left_va_list').val($(this).val());
        $('#best_corrected_binocular_va_list').val($(this).val());
    });
});

function updateVisualAcuityDropdown(unit_id, type){

    jQuery.ajax({
        'url': baseUrl + '/OphCoCvi/Default/getVisualAcuityDatas',
        data: {"unit_id": unit_id },
        dataType: "json",
        success: function(data){
            var options = [];

            //remove old options
            $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_best_corrected_'+type+'_va option:gt(0)').remove();

            //create js array from obj to sort
            for(item in data){
                options.push([item,data[item]]);
            }

            //append new option to the dropdown
            $.each(options, function(key, value) {
                $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_best_corrected_'+type+'_va').append($("<option></option>")
                        .attr("value", value[0]).text(value[1]));
            });
        }
    });
}

function ucfirst(str) { str += ''; var f = str.charAt(0).toUpperCase(); return f + str.substr(1); }

function eDparameterListener(_drawing) {
    if (_drawing.selectedDoodle != null) {
        // handle event
    }
}

function updateLAFields(item) {
    if (item.service) {
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_name').val(item.service.name);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_address').val(item.service.address);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_telephone').val(item.service.telephone);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_postcode').val(item.service.postcode_1);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_postcode_2nd').val(item.service.postcode_2);
    } else if (item.body) {
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_name').val(item.body.name);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_address').val(item.body.address);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_telephone').val(item.body.telephone);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_postcode').val(item.body.postcode_1);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_postcode_2nd').val(item.body.postcode_2);
    }

    if(item.body) {
      $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_email').val(item.body.email);
    }

    $('#local_authority_search_wrapper').hide();
    $('#la-search-toggle').removeClass('disabled');
}

function doPrint(e) {
    printIFrameUrl(cvi_print_url, null);

    iframeId = 'print_content_iframe',
            $iframe = $('iframe#print_content_iframe');

    $iframe.load(function() {
        if (e != undefined) {
            enableButtons();
            e.preventDefault();
        }

        try{
            var PDF = document.getElementById(iframeId);
            PDF.focus();
            PDF.contentWindow.print();
        } catch (e) {
            alert("Exception thrown: " + e);
        }
    });
}

function generateLabelInput(){
    var inputField = '<div class="large-8 column">'
            +'<label>Please enter the start label number:</label>'
            +'</div>'
            +'<div class="large-4 column">'
            +'<input type="text" name="firstLabel" id="firstLabel" value=""/>'
            +'</div>';

    return inputField;
}

function generateTable(){
    var tbl     = document.createElement("table");
    tbl.setAttribute("id", "printLabelPanel");
    var tblBody = document.createElement("tbody");
    var counter = 1;
    for (let j = 1; j <= 8; j++) {
        // table row creation
        let row = document.createElement("tr");

        for (let i = 1; i <= 3; i++) {

            let cell = document.createElement("td");
            cell.setAttribute("id", 'labelPanel_'+counter);
            let cellText = document.createTextNode("Label");

            cell.appendChild(cellText);
            row.appendChild(cell);
            counter++;
        }

        //row added to end of table body
        tblBody.appendChild(row);
    }


    tbl.appendChild(tblBody);

    return tbl;
}

function openIfOtherEthnicity() {

    const selected = $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_ethnic_group_id').find(":selected");
    const dataAttr = selected.data('describe');
    const $describe_ethnics = $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_describe_ethnics');

    if(dataAttr === 1) {
        $describe_ethnics.show();
    } else {
        $describe_ethnics.hide().find('textarea').val('');
    }
}
