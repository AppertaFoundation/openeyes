/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var patientMerge = {
    patients: {
        primary: {},
        secondary: {}
    },
    
    updateDOM: function(type){
        $section = $('section.' + type);
        $section.find('input[type=hidden]').val('');
        $section.find('.data-value').each(function(i, dom){
            var $dom = $(dom),
                defaultVal = $dom.data('default');
            $dom.text( defaultVal ? defaultVal : '' );
            $dom.val( defaultVal ? defaultVal : '' );
        });
        
        Object.keys(this.patients[type]).forEach(function (key) {
            $section.find('.' + key).html(patientMerge.patients[type][key]);
            $section.find('.' + key + '-input').val(patientMerge.patients[type][key]);
        });
        $section.next('section').remove();
        $section.after(this.patients[type]['all-episodes']);
        $section.after(this.patients[type]['genetics-panel']);
        $section.next('section').removeClass('episodes');
        
    },
    
    swapPatients: function(){
        var tmpPatiens = {};
        tmpPatiens = this.patients.primary;
        this.patients.primary = this.patients.secondary;
        this.patients.secondary = tmpPatiens;     
    },
        
    validatePatientsData: function(callback_true, callback_false){
        
        var isValid = false;
        
        if( this.patients.primary.dob && this.patients.secondary.dob && (this.patients.primary.dob === this.patients.secondary.dob) ){
            isValid = true;
        } else {
            isValid = false;
        }
        
        if( this.patients.primary.gender && this.patients.secondary.gender && (this.patients.primary.gender === this.patients.secondary.gender) ){
            isValid = isValid && true;
        } else {
            isValid = false;
        }
        
        if( isValid && typeof callback_true === "function" ){
            callback_true();
        } else if(!isValid && typeof callback_false === "function" ){
            callback_false();
        }
      
        return isValid;
    }
};


function displayConflictMessage(){
        
    var $patientDataConflictConfirmation = $('#patientDataConflictConfirmation'),
        $input = $patientDataConflictConfirmation.find('input'),
        
        $row = $('<div>', {'class':'row'}),
        $column = $('<div>',{'class':'large-12 column'}),
        $dob = $('<div>',{'class':'alert-box with-icon warning','id':'flash-merge_error_dob'}).text('Patients have different personal details : dob'),
        $gender = $('<div>',{'class':'alert-box with-icon warning','id':'flash-merge_error_gender'}).text('Patients have different personal details : gender');
        

    // Display DOB warning msg
    $('#flash-merge_error_dob').remove();
    if( patientMerge.patients.primary.dob !== patientMerge.patients.secondary.dob && $('#flash-merge_error_dob').length < 1){
        $column.append($dob);
    }

    // Display Gender warning msg
    $('#flash-merge_error_gender').remove();
    if( patientMerge.patients.primary.gender !== patientMerge.patients.secondary.gender && $('#flash-merge_error_gender').length < 1 ){
        $column.append($gender);
    }
    
    $row.append( $column );
    $('#patientDataConflictConfirmation').before($row);
    
    // Show the warning with the checkbox
    $patientDataConflictConfirmation.show();
    $input.attr('name', $input.data('name') );

}

$(document).ready(function(){
    
    OpenEyes.UI.Search.init($('#patient_merge_search'));
    
    OpenEyes.UI.Search.getElement().autocomplete('option', 'source', function(request, response){
        $.ajax({
            type: 'GET',
            url: baseUrl + '/patientMergeRequest/search',
            data: {
                    term: request.term,
                    ajax: 'ajax'
            },
            timeout: 4000,
            success: function (data) {
                response(JSON.parse(data));
            },
            beforeSend: function(){
                $('.no-result-patients').slideUp();
            },
            error: function(jqXHR, textStatus, errorThrown ){
                $('.loader').hide();
                if(textStatus === 'timeout'){
                    $('.timeout').slideDown();
                }
            }
        });
    });
    
    OpenEyes.UI.Search.getElement().autocomplete('option', 'search', function(event, ui){
        $('.loader').show();
        $('#patient1-search-form').find('button').prop('disabled', true);
    });
    
    OpenEyes.UI.Search.getElement().autocomplete('option', 'select', function(event, ui){
        
        // if there is a warning about the patient is alredy in the request list than it cannot be selected

        if( ui.item.warning.length > 0 ){
            return false;
        }

        if(Object.keys(patientMerge.patients.secondary).length === 0){
            // check if the secondary and primary patient ids are the same
            if ( patientMerge.patients.primary.id != ui.item.id ){
                patientMerge.patients.secondary = ui.item;
                patientMerge.updateDOM('secondary');
                if ( patientMerge.patients.primary.id){
                    patientMerge.validatePatientsData(null, displayConflictMessage);
                }

            } else {
                // secondary and primary patient ids are the same - ALERT
                new OpenEyes.UI.Dialog.Alert({
                    content: "Primary and Secondary patient cannot be the same record."
              }).open();
            }
                                  
        } else if (Object.keys(patientMerge.patients.primary).length === 0){

            if ( (patientMerge.patients.secondary.id != ui.item.id) && !(patientMerge.patients.secondary.is_local == 0 && ui.item.is_local == 1) ){
                patientMerge.patients.primary = ui.item;
                patientMerge.updateDOM('primary');

                if ( patientMerge.patients.secondary.id){
                    patientMerge.validatePatientsData(null, displayConflictMessage);
                }

            } else if (patientMerge.patients.secondary.id == ui.item.id) {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Primary and Secondary patient cannot be the same record."
              }).open();
            } else if (patientMerge.patients.secondary.is_local == 0 && ui.item.is_local == 1) {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Non local patient cannot be merged into local patient."
              }).open();
            }

        } else {

            $('<h2 class="text-center">Do you want to set this patient as Primary or Secondary ?</h2>').data('ui', ui).dialog({
                buttons: [
                    {
                        id: 'secondaryPatientBtn',
                        class: 'disabled patient-mrg-btn',
                        text: 'Secondary',
                        click: function(){
                            var ui = $(this).data('ui');

                            // cannot be the same patient and the primary patient is not local than we can merge local and non-local patient into primary
                            if ( (patientMerge.patients.primary.id != ui.item.id && patientMerge.patients.primary.is_local == 0) ||
                                    // if primary patient is local we only merge local pation into it
                                 (ui.item.is_local == patientMerge.patients.primary.is_local) ){
                                patientMerge.patients.secondary = ui.item;
                                patientMerge.updateDOM('secondary');
                                patientMerge.validatePatientsData(null, displayConflictMessage);
                                $( this ).dialog( "close" );

                            }else if (patientMerge.patients.primary.id == ui.item.id){
                                $( this ).dialog( "close" );
                                new OpenEyes.UI.Dialog.Alert({
                                    content: "Primary and Secondary patient cannot be the same record."
                                }).open();
                            } else if(ui.item.is_local != patientMerge.patients.primary.is_local){
                                //if primary is local then we can only merge local patient into it
                                new OpenEyes.UI.Dialog.Alert({
                                    content: "Non local patient cannot be merged into local patient."
                                }).open();
                                
                            }
                        }
                    },
                    {
                        id: 'primaryPatientBtn',
                        class: 'primary patient-mrg-btn',
                        text: 'Primary',
                        click: function(){
                            var ui = $(this).data('ui');
                            if ( (patientMerge.patients.secondary.id != ui.item.id && patientMerge.patients.secondary.is_local == 1) ||
                                 (patientMerge.patients.secondary.is_local == 0 && patientMerge.patients.primary.is_local == 0) ){
                                patientMerge.patients.primary = ui.item;
                                patientMerge.updateDOM('primary');
                                patientMerge.validatePatientsData(null, displayConflictMessage);
                                $( this ).dialog( "close" );
                            }else{
                                $( this ).dialog( "close" );
                                new OpenEyes.UI.Dialog.Alert({
                                    content: "Primary and Secondary patient cannot be the same record."
                                  }).open();
                            }
                        }
                    }
                ],
                create: function () {
                  var buttons = $('.ui-dialog-buttonset').children('button');
                  buttons.removeClass("ui-widget ui-state-default ui-state-active ui-state-focus");
                },
            });
        }

        $('#patient_merge_search').val("");
        return false;
    });
    
    OpenEyes.UI.Search.getElement().autocomplete('option', 'close', function(event, ui){
        $('#patient1-search-form').find('button').prop('disabled', false);
    });
    
    if( OpenEyes.UI.Search.getElement().data('autocomplete') ){
        OpenEyes.UI.Search.getElement().data('autocomplete')._renderItem = function (ul, item) {
            var warningHTML = '';
                ul.addClass("z-index-1000 patient-ajax-list");

            if(item.warning.length > 0){
                for(i=0; i<item.warning.length;i++){
                    warningHTML += '<div class="warning text-center" style="padding:3px;color:#fff;background-color:red;font-weight:900">' + item.warning[i] + '</div>';
                }
            }

            if(item.notice){
                for(i=0; i<item.notice.length;i++){
                    warningHTML += '<div class="warning text-center" style="padding:3px;color:#fff;background-color:red;font-weight:900">' + item.notice[i] + '</div>';
                }
            }
            
            return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a><strong>"
                    + item.first_name + " "
                    + item.last_name + "</strong>"
                    + " (" + item.age + ")"
                    + "<span class='icon icon-alert icon-alert-" + item.gender.toLowerCase() +"_trans'>Male</span>"
                    + "<div class='nhs-number'>" + item.nhsnum +"</div>"
                    + "<br>Hospital No.: " + item.hos_num
                    + "<br>Date of birth: " + item.dob
                    + (item.subject_id ? "<br>Subject Id: " + item.subject_id : '' ) +
                    + warningHTML + "</a>" )
                .appendTo( ul );
        };
    }
    
    $('#swapPatients').on('click', function(){
        if( (patientMerge.patients.secondary.is_local == patientMerge.patients.primary.is_local) || patientMerge.patients.secondary.is_local == 0){
            patientMerge.swapPatients();
            patientMerge.updateDOM('primary');
            patientMerge.updateDOM('secondary');
        } else {
            new OpenEyes.UI.Dialog.Alert({
                content: "Non local patient cannot be merged into local patient."
            }).open();
        }
    });
    
    $('#patient1-search-form').on('click', 'button', function(){
        $("#patient_merge_search").autocomplete('search', $('#patient_merge_search').val());
    });
    
    // form validation before sending
    $('#patientMergeWrapper').on('submit', '#grid_header_form', function(e){
               
        var primary_id = $('#PatientMergeRequest_primary_id').val(),
            secondary_id = $('#PatientMergeRequest_secondary_id').val();
            isValid = false;
            
        if( !primary_id || !secondary_id ){
            new OpenEyes.UI.Dialog.Alert({
                content: "Both Primary and Secondary patients have to be selected."
              }).open();
        } else if( primary_id == secondary_id ){
            new OpenEyes.UI.Dialog.Alert({
                content: "Primary and Secondary patient cannot be the same record."
              }).open();
        } else {
            isValid = true;
        }
        
        if(!isValid){
            e.preventDefault();
        }

        if( $('#patientDataConflictConfirmation').is(':visible') && !$('#PatientMergeRequest_personal_details_conflict_confirm').is(':checked') ){
            e.preventDefault();
            $('#PatientMergeRequest_personal_details_conflict_confirm').closest('label').css({"border":'3px solid red',"padding":"5px"});
        }
            
        if( $('#PatientMergeRequest_confirm').length > 0 && !$('#PatientMergeRequest_confirm').is(':checked') ){
            e.preventDefault();
            $('#PatientMergeRequest_confirm').closest('label').css({"border":'3px solid red',"padding":"5px"});
        }
        
        if( $('#patientDataConflictConfirmation').length > 0 && $('#patientDataConflictConfirmation').is(':visible') && $('#patientDataConflictConfirmation').find('input').is(':not(:checked)') ){
            var $row = $('<div>', {'class':'row check-warning'}),
                $column = $('<div>',{'class':'large-12 column'}),
                $checkbox = $('<div>',{'class':'alert-box with-icon warning'}).text('Please tick the checkboxes.');
                
                if( $('#patientMergeWrapper').find('.row.check-warning').length < 1 ){
                    $row.append( $column.append($checkbox) );
                    $('#patientDataConflictConfirmation').before($row);
                }
        }
    });
    
    $('#patientMergeWrapper').on('click', '#selectall', function(){
        $(this).closest('table').find('input[type="checkbox"]:not(:disabled)').attr('checked', this.checked);
    });
    
    $('#patientMergeWrapper table').on('click', 'tr', function(e){
        var target = $(e.target),
            uri = $(this).data('uri');

        // If the user clicked on an input element, or if this cell contains an input
        // element then do nothing.
        if (target.is(':input') || (target.is('td') && target.find('input').length)) {
            return;
        }

        if (uri) {
            var url = uri.split('/');
            url.unshift(baseUrl);
            window.location.href = url.join('/');
        }
   });
   
   $('#patientMergeWrapper').on('click', '#rq_delete', function(e){
        e.preventDefault();
        
        if( $('#patientMergeRequestList').find('td input[type=checkbox]:checked').length > 0 ){
           
            var serializedForm = $(this).closest('form').serialize();
                    
            $.post( "/patientMergeRequest/delete", serializedForm, function( data ) {
                window.location.reload();
            });
        } else {
            new OpenEyes.UI.Dialog.Alert({
                    content: "Please select one or more items to delete."
            }).open();
        }
        
   });
   
    
   $('.filter').on('click', 'button.filter',function(event){
	
        event.preventDefault();

        $.ajax({
                url: "",
                type: "POST",
                data: $(this).closest('form').serialize(),
                beforeSend: function() {
                    $('.filter .loader').show();
                },
                success:function(data){
                        var nodes = $(data);
                        
                        $('#patientMergeRequestList').html( nodes.find('#patientMergeRequestList').html() );

                        $('.filter .loader').hide();
                }
        });
    });
    
    $('#patientMergeWrapper').on('keypress', '#secondary_hos_num_filter, #primary_hos_num_filter',function(e){
        var val = $(this).val(),
            id = $(this).attr('id');
            
        if (e.which === 13) { //is enter
            $.ajax({
                    url: "",
                    type: "POST",
                    data: $(this).closest('form').serialize(),
                    beforeSend: function() {
                        $('#patientMergeRequestList .loader').show();
                    },
                    success:function(data){
                            var nodes = $(data);

                            $('#patientMergeRequestList tbody').html( nodes.find('#patientMergeRequestList tbody').html() );
                            $('#patientMergeRequestList .loader').hide();
                    }
            });
        }
    });
    
});