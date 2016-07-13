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
        Object.keys(this.patients[type]).forEach(function (key) {
            $section.find('.' + key).html(patientMerge.patients[type][key]);
            $section.find('.' + key + '-input').val(patientMerge.patients[type][key]);
        });
        $section.next('section').remove();
        $.ajax({
          url: "/patientMergeRequest/episodes/"+this.patients[type]['id'],
          type: "GET",
          success:function(data){
            $section.after(data);
          }
        });

        $section.next('section').removeClass('episodes');
        
    },
    
    swapPatients: function(){
        tmpPatients = this.patients.primary;
        this.patients.primary = this.patients.secondary;
        this.patients.secondary = tmpPatients;
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
        $gender = $('<div>',{'class':'alert-box with-icon warning','id':'flash-merge_error_dob'}).text('Patients have different personal details : gender');
        

    // Display DOB warning msg
    if( patientMerge.patients.primary.dob !== patientMerge.patients.secondary.dob && $('#flash-merge_error_dob').length < 1){
        $column.append($dob);
    }

    // Display Gender warning msg
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
    OpenEyes.UI.Search.setMergeSelect();
    OpenEyes.UI.Search.init($('#patient_merge_search'));

    $('#swapPatients').on('click', function(){
        patientMerge.swapPatients();
        patientMerge.updateDOM('primary');
        patientMerge.updateDOM('secondary');
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
            $('<h2 title="Alert" class="text-center"></h2>').dialog();
            new OpenEyes.UI.Dialog.Alert({
                content: "Primary and Secondary patient cannot be the same record."
              }).open();
        } else {
            isValid = true;
        }
        
        if(!isValid){
            e.preventDefault();
        }
        
        if( $('#patientDataConflictConfirmation').is(':visible') && !$('#PatientMergeRequest_personalDetailsConflictConfirm').is(':checked') ){
            e.preventDefault();
            $('#PatientMergeRequest_personalDetailsConflictConfirm').closest('label').css({"border":'3px solid red',"padding":"5px"});
        }
            
        if( $('#PatientMergeRequest_confirm').length > 0 && !$('#PatientMergeRequest_confirm').is(':checked') ){
            e.preventDefault();
            $('#PatientMergeRequest_confirm').closest('label').css({"border":'3px solid red',"padding":"5px"});
        }
        
        if( $('#patientDataConflictConfirmation').length > 0 && $('#patientDataConflictConfirmation').find('input').is(':not(:checked)') ){
            var $row = $('<div>', {'class':'row'}),
                $column = $('<div>',{'class':'large-12 column'}),
                $checkbox = $('<div>',{'class':'alert-box with-icon warning'}).text('Please tick the checkboxes.');
                
                $row.append( $column.append($checkbox) );
                $('#patientDataConflictConfirmation').before($row);
        }
        
        
    });
    
    $('#patientMergeWrapper').on('click', '#selectall', function(){
        $(this).closest('table').find('input[type="checkbox"]:not(:disabled)').attr('checked', this.checked);
    });
    
    $('#patientMergeWrapper table').on('click', 'tr', function(e){
        var target = $(e.target);

        // If the user clicked on an input element, or if this cell contains an input
        // element then do nothing.
        if (target.is(':input') || (target.is('td') && target.find('input').length)) {
            return;
        }

        var uri = $(this).data('uri');

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
            
        if (e.which === 13) {
            
           
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