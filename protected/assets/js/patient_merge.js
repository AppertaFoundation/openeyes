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
        $section.after(patientMerge.patients[type]['all-episodes']);
        $section.next('section').removeClass('episodes');
        
    },
    
    swapPatients: function(){
        var tmpPatiens = {};
        tmpPatiens = patientMerge.patients.primary;
        patientMerge.patients.primary = patientMerge.patients.secondary;
        patientMerge.patients.secondary = tmpPatiens;
    }
};
                        
$(document).ready(function(){
    var dialog = $("#patient_merge_search").autocomplete({
        minLength: 0,
        source: function(request, response) {
            $.getJSON('/patientmerge/search', {
                    term : request.term,
                    ajax: 'ajax',
            }, response);

        },
        select: function (event, ui) {

console.log(ui);
            if(Object.keys(patientMerge.patients.secondary).length === 0){
                patientMerge.patients.secondary = ui.item;
                patientMerge.updateDOM('secondary');      
                                
            } else if (Object.keys(patientMerge.patients.primary).length === 0){
                patientMerge.patients.primary = ui.item;
                patientMerge.updateDOM('primary');
            } else {
                
                $('<h2 class="text-center">Do you want to set this patient as Primary or Secondary ?</h2>').data('ui', ui).dialog({
                    buttons: [
                        {
                            id: 'secondaryPatientBtn',
                            class: 'disabled patient-mrg-btn',
                            text: 'Secondary',
                            click: function(){
                                var ui = $(this).data('ui');
                                patientMerge.patients.secondary = ui.item;
                                patientMerge.updateDOM('secondary');
                                $( this ).dialog( "close" );
                            }
                        },
                        {
                            id: 'primaryPatientBtn',
                            class: 'primary patient-mrg-btn',
                            text: 'Primary',
                            click: function(){
                                var ui = $(this).data('ui');
                                patientMerge.patients.primary = ui.item;
                                patientMerge.updateDOM('primary');
                                $( this ).dialog( "close" );
                            }
                        }
                    ],
                    create: function () {
                      var buttons = $('.ui-dialog-buttonset').children('button');
                      buttons.removeClass("ui-widget ui-state-default ui-state-active ui-state-focus");
                    },
                    
                });
                
            }

            return false;
        },
        close: function (event, ui) {
            if ( Object.keys(patientMerge.patients.primary).length === 0 || Object.keys(patientMerge.patients.secondary).length === 0 ){
                $("ul.ui-autocomplete").show(); 
            }
        }
    }).data( "autocomplete" )._renderItem = function( ul, item ) {
        return $( "<li></li>" )
            .data( "item.autocomplete", item )
            .append( "<a><strong>" + item.first_name + " " + item.last_name + "</strong>" + " (" + item.age + ")" + "<span class='icon icon-alert icon-alert-" + item.gender.toLowerCase() +"_trans'>Male</span>" + "<div class='nhs-number'>" + item.nhsnum +"</div><br>Hospital No.: " + item.hos_num + "<br>Date of birth: " + item.dob + "</a>" )
            .appendTo( ul );
    };
    
    $('#swapPatients').on('click', function(){
        patientMerge.swapPatients();
        patientMerge.updateDOM('primary');
        patientMerge.updateDOM('secondary');
    });
});