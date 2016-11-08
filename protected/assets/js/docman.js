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
                docman.createNewRecipientEntry();
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
            $('#macro_id').on('change', function(){ docman2.changeSelectedMacro($('#macro_id').val(), $('#macro_id'));});
        },

        addNewRecipientHandler: function()
        {
            $('#docman_block').on("change", '.docman_recipient', function(event){
                if(event.target.value){
                   docman.getRecipientData(event.target.value, event.target);
                }
            });
            $('#docman_block').on("change", '.docman_contact_type', function (){
                var rowindex = $(this).data("rowindex");
                if($(this).val() == 'Other'){

                   $(this).hide();
                   $('.docman_recipient').each(function(){
                        if($(this).data("rowindex") == rowindex){
                            $(this).parent().html('<input type="text" name="contact_id[]"><textarea rows="4" cols="10" name="address[]" id="address_"'+rowindex+' data-rowindex="'+rowindex+'"></textarea>');
                        }
                   });
                }
                docman.setDeliveryMethods(rowindex);
            });
        },

        addRemoveHandler: function(){
            $('#docman_block').on("click", '.remove_recipient', function(event)
            {
                event.target.closest('tr').remove();
                //$('#dm_table tr:has(td[rowspan])').attr("rowspan", $(this).attr("rowspan")-1);
            });
        },

        addHandlers: function(){
            this.addMacroHandler();
            this.addNewRecipientHandler();
            this.addRemoveHandler();
            this.addDocmanMethodMandatory();
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
            $('#dm_table tr').each(function()
            {
                if($(this).data("rowindex") == row)
                {
                    if($(this).find('.docman_contact_type').val() == 'Gp')
                    {
                        var delivery_methods = '<label><input type="checkbox" name="print[]">Print</label><br><label><input type="checkbox" name="docman[]" data-rowindex="0" checked="">DocMan</label>';
                    }else
                    {
                        var delivery_methods = '<label><input type="checkbox" name="print[]" checked>Print</label>';
                    }
                    $(this).find('.docman_delivery_method').html(delivery_methods);
                }
            });
        },

        fetchDocumentRecipients: function (macroId, element) {
            // perform AJAX call to fetch recipients, target options and output statuses
            // write data to DOM
            $.ajax(
                {
                    'type': 'GET',
                    'url': this.baseUrl + 'ajaxGetMacroTargets?macro_id='+macroId+'&patient_id='+OE_patient_id,
                    'data':  null,
                    context: this,
                    async: false,
                    'success': function(resp) {
                        $('.new_entry_row').remove();
                        $('#dm_table tr:first').after(resp);
                    }
                }
            );
        },


        //-------------------------------------------------------------
        //  Fetches all the data for the entire document set, in JSON
        //-------------------------------------------------------------

        getDocSet: function(eventId) {
            $.ajax({
                'type': 'GET',
                'url': this.baseUrl + 'ajaxGetDocSet?id='+eventId,
                'data':  null,
                context: this,
                'success': function(resp) {
                    this.data = resp;
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

            $.ajax({
                'type': 'GET',
                'url': this.baseUrl + 'ajaxGetDocTable?id='+event_id+correspondence_mode,
                'data':  null,
                context: this,
                dataType: 'html',
                'success': function(resp) {
                    this.setDocTableToHTML(resp);
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
            $.ajax({
                'type': 'GET',
                'url': this.baseUrl + 'ajaxGetContactData?contact_id='+contact_id+'&patient_id='+OE_patient_id,
                context: this,
                dataType: 'json',
                'success': function(resp) {
                    var rowindex = $(element).data("rowindex");
                    var this_recipient = $(element).data('previous');

                    var this_address = $('#address_'+rowindex).val();
                    var this_contact_type = $('#contact_type_'+rowindex).val();
                    var other_rowindex = $('#docman_block select option[value="' + resp.contact_type + '"]:selected').closest('tr').data('rowindex');
                    var $other_docman_recipient = $('tr.rowindex-' + other_rowindex + ' .docman_recipient');

                    $('#address_'+rowindex).val(resp.address);
                    $('#contact_type_'+rowindex).val(resp.contact_type).trigger('change');

                    if(resp.contact_type === 'Gp' || resp.contact_type === 'Patient'){
                        $other_docman_recipient.val(this_recipient);
                        $('#address_' + other_rowindex).val(this_address);
                        $('#contact_type_' + other_rowindex).val(this_contact_type).trigger('change');
                    }
                    $(element).data('previous', $(element).val());
                    $other_docman_recipient.data('previous', $other_docman_recipient.val());
                }
            });
        },
        
        swapGpPatent: function(rowindex, contact_type){
            
            var gp_rowindex = $('select option[value="Gp"]:selected').closest('tr').data('rowindex');
            var patient_rowindex = $('select option[value="Patient"]:selected').closest('tr').data('rowindex');
            this.swapRecipients(gp_rowindex, patient_rowindex);

        },
       /* 
        swapRecipients: function(from_rowindex, to_rowindex){
            //change recipient dropdown
            var $from = $('#docman_block').find('tr.rowindex-' + from_rowindex + ' .docman_recipient');
            var $to = $('#docman_block').find('tr.rowindex-' + to_rowindex + ' .docman_recipient');
            var from_val = $('#docman_block').find('tr.rowindex-' + from_rowindex + ' .docman_recipient').val();
            var to_val = $('#docman_block').find('tr.rowindex-' + to_rowindex + ' .docman_recipient').val();
            
            $from.val(to_val);
            $to.val(from_val);
            
        },*/

        //------------------------------------------------------------
        //  Create a new entry row at the end of the table
        //------------------------------------------------------------
        createNewEntry: function(element) {
            $.ajax({
                'type': 'GET',
                'url': this.baseUrl + 'ajaxGetDocTableEditRow?patient_id='+OE_patient_id,
                'data':  null,
                context: this,
                'success': function(resp) {
                    //console.log(resp);
                    element.before(resp);
                    $('#docman_add_new').hide();
                }
            });
        },

        //------------------------------------------------------------
        //  Create a new recipient entry row at the end of the table
        //------------------------------------------------------------
        createNewRecipientEntry: function()
        {
            last_row_index = this.getLastRowIndex();
            $.ajax({
                'type': 'GET',
                'url': this.baseUrl + 'ajaxGetDocTableRecipientRow?patient_id='+OE_patient_id+'&last_row_index='+last_row_index,
                'context': this,
                'success': function(resp) {
                    $('#dm_table tr:last').before(resp);
                    this.addRemoveHandler();
                    this.addDocmanMethodMandatory();
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


        editAddress: function(addressId) {
            $('#docman_edit_button_'+addressId).hide();
            data = $('#docman_address_'+addressId).html();
            $('#docman_address_'+addressId).html('<textarea rows="3" cols="10" id="docman_address_edit_'+addressId+'">'+data+'</textarea><button onclick="docman2.saveAddress(event, '+addressId+')" class="secondary small right" >Save</button>');
        },

        saveAddress: function(event, addressId){
            event.preventDefault();
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

