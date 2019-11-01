var docman = (function() {
    var data;        // holds the entire document data structure
    var macros;        // macro names and ids
    var dom_id;        // the ID of the div to contain the docman table
    var prefix;        // prefix added to all DOM elements created by docman
    var event_id;    // event id that this docset relates to
    var baseUrl;
    var allowAddDocument;
    var module_correspondence=0;

    return {

        init: function(){
            $('#docman_block').on("click", '#docman_add_new', function(){
                // insert new edit row after the dm_table last TR
                var lastrow = $('#dm_table tr:last');
                this.createNewEntry(lastrow);
            });
            $('#docman_block').on("click", '#docman_add_new_recipient', function(e){
                e.preventDefault();
                docman.createNewRecipientEntry('');
            });

            this.addHandlers();
        },

        //-------------------------------------------------------------
        //  Sets the id of the containing DOM object, and the prefix
        //  used for all DOM elements created within that.
        //-------------------------------------------------------------

        setDOMid: function(id,prefix,event_id) {
            this.dom_id = id;
            this.prefix = prefix;
            this.event_id = event_id;
            this.allowAddDocument = '1';

        },


        //-------------------------------------------------------------
        //  Sets the event id
        //-------------------------------------------------------------

        setEventId: function(event_id) {
            this.event_id = event_id;
        },


        //-------------------------------------------------------------
        //  Clears the DOM ready to create the new table
        //-------------------------------------------------------------

        clearDOM: function() {
            var myNode = document.getElementById(this.dom_id);
            var fc = myNode.firstChild;
            while(fc) {
                myNode.removeChild(fc);
                fc = myNode.firstChild;
            }
            $("#"+this.dom_id).append("<table id='"+this.prefix+"table'>" +
                "<tr id='"+this.prefix+"0'><th>Document Type</th><th>To/CC</th><th>Recipient</th>" + 
                "<th>Delivery Method(s)</th><th>Delivery Status</th></tr></table>");
        },


        changeSelectedMacro: function(macro_id, element) {
            if(macro_id !== undefined) {
                this.fetchDocumentRecipients(macro_id, element);
                if(this.module_correspondence == 1)
                {
                    updateCorrespondence( macro_id );
                }
            }
        },


        addMacroHandler: function(){
            $('#macro_id').on('change', function(){ 
                docman2.changeSelectedMacro($('#macro_id').val(), $('#macro_id'));
            });
        },

        addNewRecipientHandler: function()
        {
            $('#docman_block').on("change", '.docman_recipient', function(event){
                if(event.target.value){
                    docman.getRecipientData(event.target.value, event.target);
										// $.first('.autosize').trigger('autosize');
                }
            });
            $('#docman_block').on("change", '.docman_contact_type', function (){
                var rowindex = $(this).data("rowindex");
                docman.setDeliveryMethods(rowindex);
            });
        },
                
        addRemoveHandler: function(){
            $('#docman_block').on("click", '.remove_recipient', function(event)
            {
                event.target.closest('tr').remove();
            });
        },

        addHandlers: function(){
            this.addMacroHandler();
            this.addNewRecipientHandler();
            this.addRemoveHandler();
            this.addDocmanMethodMandatory();
            this.addContactNameUpdate();
        },
        
        addContactNameUpdate: function(){
            $('#dm_table').on('keyup', '#DocumentTarget_0_attributes_contact_name',function(){
                $('#ElementLetter_introduction').val( 'Dear ' + $(this).val() );
            });
        },

        addDocmanMethodMandatory: function()
        {
            $('#docman_block').on("click", '.docman_delivery', function(e){
                if(docman.checkRecipientType($(this).data("rowindex")) == 'Gp'){
                    $(this).prop('checked', true);
                }
            });
        },

        checkRecipientType: function(row){
            var contact_type;
            $('#dm_tabledm_tabledm_table tr').each(function() {
                if ($(this).data("rowindex") == row) {
                    contact_type = $(this).find('.docman_contact_type').val();
                }
            });
            return contact_type;
        },

        setDeliveryMethods: function(row)
        {
            var delivery_methods = '';
             
            $('#dm_table tr').each(function()
            {
                if($(this).data("rowindex") == row)
                {
                    var contact_type = $(this).find('.docman_contact_type').val();
                    if( contact_type == 'GP')
                    {
                        //electronic_sending_method_label is coming from config
                        electronic_sending_method_label = electronic_sending_method_label ? electronic_sending_method_label : 'Electronic';
                            
                        delivery_methods = '<div><label class="inline highlight"><input value="Docman" name="DocumentTarget_' + row + '_DocumentOutput_0_output_type" type="checkbox" disabled checked> ' + electronic_sending_method_label;
                        delivery_methods += '<input type="hidden" value="Docman" name="DocumentTarget[' + row + '][DocumentOutput][0][output_type]"></label></div>';

                        // if the print option is not set we will not display the button
                        if( $('button#et_saveprint').length ){
                            delivery_methods += '<div><label class="inline highlight"><input value="Print" name="DocumentTarget[' + row + '][DocumentOutput][1][output_type]" type="checkbox"> Print</label></div>';
                        }

                    }
                    else if(contact_type == 'INTERNALREFERRAL'){
                        internal_referral_method_label = internal_referral_method_label ? internal_referral_method_label : 'Electronic (Internal Referral)';

                        delivery_methods = '<div><label class="inline highlight"><input value="Internalreferral" name="DocumentTarget_' + row + '_DocumentOutput_0_output_type" type="checkbox" disabled checked> ' + internal_referral_method_label;
                        delivery_methods += '<input type="hidden" value="Internalreferral" name="DocumentTarget[' + row + '][DocumentOutput][0][output_type]"></label></div>';

                        // if the print option is not set we will not display the button
                        if( $('button#et_saveprint').length ){
                            delivery_methods += '<div><label class="inline highlight"><input value="Print" name="DocumentTarget[' + row + '][DocumentOutput][1][output_type]" type="checkbox"> Print</label></div>>';
                        }
                    }
                    else
                    {
                        // if the print option is not set we will not display the button
                        if( $('button#et_saveprint').length ){
                            delivery_methods = '<div><label class="inline highlight"><input value="Print" name="DocumentTarget[' + row + '][DocumentOutput][0][output_type]" type="checkbox" checked> Print</label></div>';
                        }
                    }
                    $(this).find('.docman_delivery_method').html(delivery_methods);
                }
            });
        },

        fetchDocumentRecipients: function (macroId, element) {
            // perform AJAX call to fetch recipients, target options and output statuses
            // write data to DOM
            if(macroId){
                $.ajax(
                    {
                        'type': 'GET',
                        'url': this.baseUrl + 'ajaxGetMacroTargets?macro_id='+macroId+'&patient_id='+OE_patient_id,
                        'data':  null,
                        context: this,
                        async: false,
                        'success': function(resp) {                       
                            $('#dm_table').replaceWith(resp);
                        }
                    }
                );
            }
        },

        getRowIndexByContactType: function(contact_type){
            var rowindex;
            $('.docman_contact_type').each(function(i,$element){
                if( $($element).val() === contact_type){
                    rowindex =  $($element).data('rowindex');
                }
            });

            return rowindex;
        },

        toggleRemoveLink: function(rowindex){
            $('#dm_table').find('tr.rowindex-' + rowindex + ' .remove_recipient').toggle();
        },


        //-------------------------------------------------------------
        //  Fetches all the data for the entire document set, in JSON
        //-------------------------------------------------------------

        getDocSet: function(eventId) {
            $('#dm_table .docman_loader').show();
            $.ajax({
                'type': 'GET',
                'url': this.baseUrl + 'ajaxGetDocSet?id='+eventId,
                'data':  null,
                context: this,
                'success': function(resp) {
                    this.data = resp;
                    $('#dm_table .docman_loader').hide();
                }
            });
        },

        //-------------------------------------------------------------
        //  Fetches all the data for the entire document set, in JSON
        //-------------------------------------------------------------

        getDocTable: function(event_id, macro_id) {
            var correspondence_mode = '';
            if(this.module_correspondence == 1)
            {
                correspondence_mode = '&in_correspondence=1';
            }
            $('#dm_table .docman_loader').show();
            $.ajax({
                'type': 'GET',
                'url': this.baseUrl + 'ajaxGetDocTable?id='+event_id+correspondence_mode,
                'data':  null,
                context: this,
                dataType: 'html',
                'success': function(resp) {
                    this.setDocTableToHTML(resp);
                    $('#dm_table .docman_loader').hide();
                }
                    });
        },

        setDocTableToHTML: function(data){
            $('#docman_block').html(data);

                    if(macro_id > 0){
                        $('#macro_id').val(macro_id).change();
                    }
        },

        getRecipientData: function(contact_id, element) {
            var document_set_id = '';
            var document_set_id_param = '';
            var rowindex = $(element).data("rowindex");
            var $tr = $('tr.rowindex-' + rowindex);
            if( $('#DocumentSet_id').length > 0 ){
                document_set_id = $('#DocumentSet_id').val();
                document_set_id_param = '&document_set_id=' + document_set_id;
            }

            var current_type =  $('#DocumentTarget_' + rowindex + '_attributes_contact_type option:selected').text();
            var selected_type = contact_id.match(/^([a-zA-Z]+)([0-9]+)$/);
            var other_rowindex;
            var other_id;

            if(selected_type){
                other_rowindex = $('#docman_block select option[value="' + selected_type[1].toUpperCase() + '"]:selected').closest('tr').data('rowindex');
        }

            if(contact_id != 'OTHER' ){
            	if(other_rowindex !== undefined){
                    current_type = current_type.toLowerCase();
                    type = current_type[0].toUpperCase() + current_type.slice(1);

                    if(type !== 'Other' && type !== undefined && type !== '- type -'){
                        other_id = $("#docman_recipient_" + other_rowindex + " option[value*='" + type + "']" ).val();
                    }

                    other_id = $("#docman_recipient_" + other_rowindex + " option[value*='" + type + "']" ).val();
                }

                this.updateRow(rowindex, contact_id, OE_patient_id, document_set_id_param);
                this.updateRow(other_rowindex, other_id, OE_patient_id, document_set_id_param);

            } else if(contact_id.toUpperCase() === 'OTHER'){
                $('#DocumentTarget_' + rowindex + '_attributes_contact_name').val('');
                $('#DocumentTarget_' + rowindex + '_attributes_contact_nickname').val('');
                $('#Document_Target_Address_' + rowindex ).val('');
								$('#Document_Target_Address_' + rowindex).trigger('autosize');
                $('#DocumentTarget_' + rowindex + '_attributes_contact_id').val('');
								$('#DocumentTarget_' + rowindex + '_attributes_contact_type').removeAttr('disabled');
                $('#DocumentTarget_' + rowindex + '_attributes_contact_type').val('OTHER');
                $('#yDocumentTarget_' + rowindex + '_attributes_contact_type').val('OTHER');
                $('#DocumentTarget_' + rowindex + '_attributes_contact_type').trigger('change');
                //set readonly
                //$('#DocumentTarget_' + rowindex + '_attributes_contact_name').attr('readonly', false);
                $('#Document_Target_Address_' + rowindex).attr('readonly', false);
                $('#ElementLetter_use_nickname').prop('checked','');
            }
        },
        
        isContactTypeAdded: function(type){
            var is_added = false;
            $('.docman_contact_type').each(function(i,$element){
                if( $($element).val() === type){
                    is_added = true;
                }
            });
            return is_added;
        },

        updateRow: function(rowindex, contact_id, OE_patient_id, document_set_id_param) {

            if(contact_id === undefined){
                return;
            }

            return $.ajax({
                'type': 'GET',
                'url': '/' + moduleName + '/default/getAddress?contact=' + contact_id + '&patient_id=' + OE_patient_id + document_set_id_param,
                context: this,
                dataType: 'json',
                'beforeSend': function(){
                    $('#dm_table .docman_loader').show();
                },
                'success': function (resp) {

                    $('#Document_Target_Address_' + rowindex).val(resp.address);
										$('#Document_Target_Address_' + rowindex).trigger('autosize');
                    $('#DocumentTarget_' + rowindex + '_attributes_contact_name').val(resp.contact_name);
                    $('#DocumentTarget_' + rowindex + '_attributes_contact_nickname').val(resp.contact_nickname);
                    $('#DocumentTarget_' + rowindex + '_attributes_contact_id').val(resp.contact_id);
                    $('#DocumentTarget_' + rowindex + '_attributes_contact_type').val(resp.contact_type.toUpperCase()).trigger('change');

                    $('#Document_Target_Address_' + rowindex).attr('readonly', (resp.contact_type.toUpperCase() === 'GP'));

                    //check if the contact_type is disabled - if it is we have to insert a hidden field
                    if( $('#DocumentTarget_' + rowindex + '_attributes_contact_type').is(':disabled') ){
                        $('#yDocumentTarget_' + rowindex + '_attributes_contact_type').val(resp.contact_type.toUpperCase());
                    }

                    if(rowindex === 0){
                        $('#ElementLetter_use_nickname').prop('checked','');
                        $("#ElementLetter_introduction").val( resp.text_ElementLetter_introduction );
                    }
                    $('#docman_recipient_' + rowindex).val('');
                    $('#dm_table .docman_loader').hide();
                    autosize.update($('.autosize'));
                }
            });
        },

        //------------------------------------------------------------
        //  Create a new entry row at the end of the table
        //------------------------------------------------------------
        createNewEntry: function(element) {
            $('#dm_table .docman_loader').show();
            $.ajax({
                'type': 'GET',
                'url': this.baseUrl + 'ajaxGetDocTableEditRow?patient_id='+OE_patient_id,
                'data':  null,
                context: this,
                'success': function(resp) {
                    //console.log(resp);
                    element.before(resp);
                    $('#docman_add_new').hide();
                    $('#dm_table .docman_loader').hdie();
                }
            });
        },

        //------------------------------------------------------------
        //  Create a new recipient entry row at the end of the table
        //------------------------------------------------------------
        createNewRecipientEntry: function(selected_contact_type)
        {
            last_row_index = this.getLastRowIndex();
            $('#dm_table .docman_loader').show();
            $.ajax({
                'type': 'GET',
                'url': this.baseUrl + 'ajaxGetDocTableRecipientRow',
                data: {
                    patient_id: OE_patient_id,
                    last_row_index: last_row_index,
                    selected_contact_type: selected_contact_type
                },
                'context': this,
                'success': function(resp) {
                    $('#dm_table tr:last').before(resp);
                    this.addRemoveHandler();
                    this.addDocmanMethodMandatory();
                    $('tr.rowindex-' + (++last_row_index) + ' .docman_recipient').trigger('change');
                    $('#dm_table .docman_loader').hide();
                    autosize($('#dm_table textarea'));
                }
            });
        },

        getLastRowIndex: function()
        {
            var last_row_index = -1;
            $('#dm_table tr').each(function(){
                if($(this).data('rowindex') > last_row_index)
                {
                    last_row_index = $(this).data('rowindex');
                }
            });
            return last_row_index;
        },

        //-------------------------------------------------------------
        //  Process change of letter macro select box
        //-------------------------------------------------------------


        //-------------------------------------------------------------
        //  Return the count of document instances defined
        //-------------------------------------------------------------

        getDocCount: function() {
            if(this.data) {
                return this.data.docinst.length;
            } else {
                return 0;
            }
        },


        //-------------------------------------------------------------
        //  Finds the last numbered row in the doc table
        //-------------------------------------------------------------

        getLastTableRowId: function() {
            var n = 0;
            while(1) {
                var x = this.prefix+"tablerow_"+n;
                if(!document.getElementById(x)) break;
            }
            return n;
        },
//
//
//        editAddress: function(addressId) {
//            $('#docman_edit_button_'+addressId).hide();
//            data = $('#docman_address_'+addressId).html();
//            $('#docman_address_'+addressId).html('<textarea rows="3" cols="10" id="docman_address_edit_'+addressId+'">'+data+'</textarea><button onclick="docman2.saveAddress(event, '+addressId+')" class="secondary small right" >Save</button>');
//        },

        saveAddress: function(event, addressId){
            event.preventDefault();
            $('#dm_table .docman_loader').show();
            $.ajax(
                {
                    // TODO: should be POST but we need the YII_TOKEN for that!!!
                    'type': 'GET',
                    'url': this.baseUrl + 'ajaxUpdateTargetAddress',
                    'data': {'doc_target_id':addressId,
                             'new_address':$('#docman_address_edit_'+addressId).val()},
                    'success': function(resp) {
                        $('#docman_address_'+addressId).html(resp);
                        $('#docman_edit_button_'+addressId).show();
                        $('#dm_table .docman_loader').hide();
                    }
                }
            );
        },

        //-------------------------------------------------------------
        //  test / junk
        //-------------------------------------------------------------

        getEventId: function() {
            return 1001;
            //return this.data.data.docset[0].event_id;
        },
                
        f1: function() {
          return 6;
        },

        f2: function() {
          var x = this.f1() /2;
          alert(x);
          return x;
        },

    };
})();

