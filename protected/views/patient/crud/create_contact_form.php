<div class="oe-popup-wrap" id="extra_gp_adding_form" style="display: none; z-index:100">
    <div class="oe-popup">
        <div class="form">
            <?php
            $extra_gp_form = $this->beginWidget('CActiveForm', array(
                'id' => 'extra-gp-form',
                'enableAjaxValidation' => true,
            ));
            ?>
            <?php echo $extra_gp_form->errorSummary($extra_gp_contact); ?>
            <div class="title">
                <div id="extra_gp_adding_title" data-type="">Add New Contact</div>
                <div class="close-icon-btn">
                    <i class="oe-i remove-circle pro-theme js-cancel-add-contact"></i>
                </div>
            </div>
            <div class="alert-box info" id="extra-gp-message" style="display:none;">
                <p></p>
            </div>
            <div class="alert-box warning" id="extra_gp_practitioner-alert-box" style="display:none;">
                <p id="extra_gp_errors"></p>
            </div>
            <table class="standard row">
                <tbody>
                <tr>
                    <td>Title:</td>
                    <td class="flex-layout">
                        <?php echo $extra_gp_form->textField($extra_gp_contact, 'title', array('size' => 60, 'maxlength' => 20)); ?>
                        <?php echo $extra_gp_form->error($extra_gp_contact, 'title'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_gp_form->labelEx($extra_gp_contact, 'first_name'); ?>
                    </td>
                    <td>
                        <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'Contact[first_name]', 'hide_no_result_msg' => true]); ?>
                        <?php echo $extra_gp_form->error($extra_gp_contact, 'first_name'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_gp_form->labelEx($extra_gp_contact, 'last_name'); ?>
                    </td>
                    <td>
                        <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'Contact[last_name]', 'hide_no_result_msg' => true]); ?>
                        <?php echo $extra_gp_form->error($extra_gp_contact, 'last_name'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_gp_form->labelEx($extra_gp_contact, 'primary_phone'); ?>
                    </td>
                    <td>
                        <?php echo $extra_gp_form->textField($extra_gp_contact, 'primary_phone', array('size' => 60, 'maxlength' => 20, 'autocomplete' => 'off')); ?>
                        <?php echo $extra_gp_form->error($extra_gp_contact, 'primary_phone'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_gp_form->labelEx($extra_practice_associate, 'provider_no'); ?>
                    </td>
                    <td>
                        <?php echo $extra_gp_form->textField($extra_practice_associate, 'provider_no', array('size' => 60, 'maxlength' => 20, 'autocomplete' => 'off')); ?>
                        <?php echo $extra_gp_form->error($extra_practice_associate, 'provider_no'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php echo $extra_gp_contact->getAttributeLabel('Role'); ?> <span class="required">*</span></label>
                    </td>
                    <td>
                        <?php echo $extra_gp_form->error($extra_gp_contact, 'contact_label_id'); ?>

                        <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'extra_gp_autocomplete_contact_label_id']); ?>

                    </td>
                </tr>
                <tr id="extra_gp_selected_contact_label_wrapper" style="display: <?php echo $extra_gp_contact->label ? '' : 'none' ?>">
                    <td></td>
                    <td>
                        <div>
                            <span class="js-name">
                                <?php echo isset($extra_gp_contact->label) ? $extra_gp_contact->label->name : ''; ?>
                            </span>
                            <?php echo CHtml::hiddenField('Contact[contact_label_id]', $extra_gp_contact->contact_label_id, array('class' => 'hidden_id js-extra-gp-contact-label-id')); ?>
                        </div>
                    </td>
                    <td>
                        <a href="javascript:void(0)" class="oe-i trash removeReading remove"></a>
                    </td>
                </tr>

                <tr id="extra_gp_no_contact_label_result" style="display:none">
                    <td></td>
                    <td>
                        <div>
                            <div class="selected_gp">No result</div>
                        </div>
                    </td>
                </tr>
                <?php echo CHtml::hiddenField('Gp[is_active]', 1); ?>
                <tr>
                    <td colspan="2" class="align-right">
                        <?php
                        echo CHtml::ajaxButton('Next',
                            Yii::app()->controller->createUrl('gp/create', array('context' => 'AJAX')),
                            array(
                                'type' => 'POST',
                                'success' => 'js:function(event){
                                    if (isJsonString(event)) {
                                        let response = JSON.parse(event);
                                        $(".js-contact-title").val(response.title);
                                        $(".js-contact-first-name").val(response.firstName);
                                        $(".js-contact-last-name").val(response.lastName);
                                        $(".js-contact-primary-phone").val(response.primaryPhone);
                                        $(".js-contact-label-id").val(response.labelId);
                                        $(".js-contact-practice-provider-no").val(response.providerNo);
                                        $("#extra-gp-form")[0].reset();
                                        $("#extra_gp_errors").text("");
                                        $("#extra_gp_practitioner-alert-box").css("display","none");
                                        $("#extra_gp_adding_form").css("display","none");
                                        $("#extra_practice_adding_existing_form").css("display","");

                                        // The gp variable has been defined globally.
                                        if (response.gpId !== undefined) {
                                            // Filled in when a practitioner with the same name
                                            // and role has been found  to avoid creating a
                                            // duplicate practitioner. (See CERA-514 for the criteria)
                                            gp.id = response.gpId;
                                        }

                                        if (gp.id === null) {
                                            // Setting the gpId to -1 as there is no elegant way in php to handle undefined values.
                                            gp.id = "-1";
                                        }

                                        gp.title = response.title;
                                        gp.firstName = response.firstName;
                                        gp.lastName = response.lastName;
                                        gp.phoneno =response.primaryPhone;
                                        gp.role = response.labelId;
                                        // Saving the data in the hidden field
                                        $(".gp_data_retrieved").val(gp.toString());
                                    } else {
                                        $("#extra_gp_errors").html(event);
                                        $("#extra_gp_practitioner-alert-box").css("display","");
                                    }
                                  }',
                                'error' => 'js:function(event){
                                    $("#extra_gp_errors").html("<div class=\"errorSummary\"><p>Unable to save Practitioner information, please contact your support.</p></div>");
                                    $("#extra_gp_practitioner-alert-box").css("display","");
                                }',
                            ),
                            array('class' => 'button hint green')
                        );
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>


<div class="oe-popup-wrap" id="extra_practice_adding_existing_form" style="display: none; z-index:100">
    <div class="oe-popup">
        <div class="title">
            Add Existing Practice
            <div class="close-icon-btn">
                <i class="oe-i remove-circle pro-theme js-cancel-add-contact"></i>
            </div>
        </div>
        <div class="alert-box warning" id="extra-existing-practice-alert-box" style="display:none;">
            <p id="extra-existing-practice-errors"></p>
        </div>
        <div class="form">
            <?php $extra_existing_practice_form = $this->beginWidget('CActiveForm', array(
                'id' => 'extra-adding-existing-practice-form',
                'enableAjaxValidation' => true,
            )); ?>

            <p class="note text-right">Fields with <span class="required">*</span> are required.</p>
            <?php echo $extra_existing_practice_form->errorSummary($extra_practice_associate); ?>
            <table class="standard">
                <tbody>
                <?php echo CHtml::hiddenField('gp_data_retrieved', '', array('class' => 'hidden_id gp_data_retrieved')); ?>
                <?php echo CHtml::hiddenField('Contact[title]', '', array('class' => 'hidden_id js-contact-title')); ?>
                <?php echo CHtml::hiddenField('Contact[first_name]', '', array('class' => 'hidden_id js-contact-first-name')); ?>
                <?php echo CHtml::hiddenField('Contact[last_name]', '', array('class' => 'hidden_id js-contact-last-name')); ?>
                <?php echo CHtml::hiddenField('Contact[primary_phone]', '', array('class' => 'hidden_id js-contact-primary-phone')); ?>
                <?php echo CHtml::hiddenField('Contact[contact_label_id]', '', array('class' => 'hidden_id js-contact-label-id')); ?>
                <?php echo CHtml::hiddenField('ContactPracticeAssociate[provider_no]', '', array('class' => 'hidden_id js-contact-practice-provider-no')); ?>
                <tr>
                    <td>
                        <?php echo $extra_existing_practice_form->labelEx($extra_practice_associate, 'practice_id'); ?>
                    </td>
                    <td>
                        <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'autocomplete_extra_practice_id']); ?>
                        <div id="selected_practice_associate_wrapper">
                            <ul class="oe-multi-select js-selected-practice-associate">
                            </ul>
                            <?= CHtml::hiddenField('PracticeAssociate[practice_id]', $extra_practice_associate->practice_id,
                                array('class' => 'hidden_id')); ?>
                        </div>
                        <div id="no_practice_associate_result" style="display: none;">
                            <div>No result</div>
                        </div>
                        <a id="js-add-extra-practice-btn" href="#">Add Practice</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="align-right">
                        <?php echo CHtml::ajaxButton('Add',
                             Yii::app()->controller->createUrl('practiceAssociate/create'),
                             [
                                 'type' => 'POST',
                                 'success' => 'js:function(event) {
                                    let response = JSON.parse(event);
                                    if ("error" in response) {
                                        $("#extra-existing-practice-errors").html(response.error);
                                        $("#extra-existing-practice-alert-box").css("display","");
                                    } else {
                                        if ($("#extra_gp_adding_title").text() === "Add Referring Practitioner") {
                                            addExtraGp("js-selected_gp", response.gp_id, response.practice_id);
                                        } else {
                                            addExtraGp("js-selected_extra_gps", response.gp_id, response.practice_id);
                                        }

                                        extraContactFormCleaning();
                                        $(".js-extra-practice-gp-id").val("");

                                        // clearing practice_id value (stored in the hidden field) from the HTML
                                        // DOM after the contact/gp has been successfully added
                                        $("#PracticeAssociate_practice_id").val("");

                                        // Cleaning the data from the hidden fields as it might cause problems for the
                                        // non-mandatory fields when trying to add new contact or gp
                                        $(".js-contact-title").val("");
                                        $(".js-contact-first-name").val("");
                                        $(".js-contact-last-name").val("");
                                        $(".js-contact-primary-phone").val("");
                                        $(".js-contact-label-id").val("");
                                        $(".js-contact-practice-provider-no").val("");

                                        // Cleaning the contact label id after the contact/gp has been added successfully,
                                        // otherwise the same label gets saved for the contact (if left blank) on adding the contact/gp for the next time
                                        $(".js-extra-gp-contact-label-id").val("");

                                        // Hide the message once the existing practice is associated successfully.
                                        $("#extra-gp-message").hide();

                                        // enabling title and phone number on closing the popup.
                                        $("#extra-gp-form #Contact_title").prop("readonly", false);
                                        $("#extra-gp-form #Contact_primary_phone").prop("readonly", false);

                                        // remove data from hidden fields.
                                        $(".gp_data_retrieved").val("");

                                        // unsetting the variable (defined in create_contact_form inside the onselect function of autocompletesearch widget - firstname and lastname field)
                                        gp = new Gp();
                                    }
                                }',
                             ],
                             array('class' => 'button hint green')
                        ); ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php $this->endWidget(); ?>
        </div><!-- form -->
    </div>
</div>


<?php
$extra_practice_countries = CHtml::listData(Country::model()->findAll(), 'id', 'name');
$extra_practice_address_type_ids = CHtml::listData(AddressType::model()->findAll(), 'id', 'name');
?>

<div class="oe-popup-wrap" id="extra_practice_adding_new_form" style="display: none; z-index:100">
    <div class="oe-popup">
        <div class="title">
            Add Practice
            <div class="close-icon-btn">
                <i class="oe-i remove-circle pro-theme js-cancel-add-contact"></i>
            </div>
        </div>
        <div id="extra-practice-practice-alert-box" class="alert-box warning" style="display:none;">
            <p id="extra-practice-errors"></p>
        </div>
        <div class="form">
            <?php $extra_practice_form = $this->beginWidget('CActiveForm', array(
                'id' => 'extra-adding-practice-form',
                'enableAjaxValidation' => true,
            )); ?>

            <p class="note text-right">Fields with <span class="required">*</span> are required.</p>
            <?php echo $extra_practice_form->errorSummary($extra_practice); ?>
            <table class="standard">
                <?php echo CHtml::hiddenField('gp_data_retrieved', '', array('class' => 'hidden_id gp_data_retrieved')); ?>
                <?php echo CHtml::hiddenField('Contact[contact_title]', '', array('class' => 'hidden_id js-contact-title')); ?>
                <?php echo CHtml::hiddenField('Contact[contact_first_name]', '', array('class' => 'hidden_id js-contact-first-name')); ?>
                <?php echo CHtml::hiddenField('Contact[contact_last_name]', '', array('class' => 'hidden_id js-contact-last-name')); ?>
                <?php echo CHtml::hiddenField('Contact[contact_primary_phone]', '', array('class' => 'hidden_id js-contact-primary-phone')); ?>
                <?php echo CHtml::hiddenField('Contact[contact_label_id]', '', array('class' => 'hidden_id js-contact-label-id')); ?>
                <?php echo CHtml::hiddenField('ContactPracticeAssociate[provider_no]', '', array('class' => 'hidden_id js-contact-practice-provider-no')); ?>
                <tbody>
                <tr>
                    <td>
                        <?php echo $extra_practice_form->labelEx($extra_practice_contact, 'first_name'); ?>
                    </td>
                    <td>
                        <?php echo $extra_practice_form->textArea($extra_practice_contact, 'first_name', array('maxlength' => 300, 'cols' => 40, 'class' => 'cols-10')); ?>
                        <?php echo $extra_practice_form->error($extra_practice_contact, 'first_name'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_practice_form->labelEx($extra_practice, 'code'); ?>
                    </td>
                    <td>
                        <?php echo $extra_practice_form->textField($extra_practice, 'code', array('size' => 15, 'maxlength' => 20, 'class' => 'cols-10')); ?>
                        <?php echo $extra_practice_form->error($extra_practice, 'code'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_practice_form->labelEx($extra_practice, 'phone'); ?>
                        <?php echo $extra_practice_form->error($extra_practice, 'phone'); ?>
                    </td>
                    <td>
                        <?php echo $extra_practice_form->telField($extra_practice, 'phone', array('size' => 15, 'maxlength' => 20, 'class' => 'cols-10')); ?>
                    </td>
                </tr>
                <tr>
                    <?php $this->renderPartial('../practice/_form_address', array('form' => $extra_practice_form, 'address' => $extra_practice_address, 'countries' => $extra_practice_countries, 'address_type_ids' => $extra_practice_address_type_ids)); ?>
                </tr>
                <tr>
                    <td colspan="2" class="align-right">
                        <?php echo CHtml::ajaxButton('Add',
                             Yii::app()->controller->createUrl('practice/createAssociate'),
                             [
                                 'type' => 'POST',
                                 'success' => 'js:function(event) {
                                    let response = JSON.parse(event);
                                    if ("error" in response) {
                                        $("#extra-practice-errors").html(response.error);
                                        $("#extra-practice-practice-alert-box").css("display","");
                                    } else {
                                        if($("#extra_gp_adding_title").text() === "Add Referring Practitioner"){
                                            addExtraGp("js-selected_gp", response.gp_id, response.practice_id);
                                        } else {
                                            addExtraGp("js-selected_extra_gps", response.gp_id, response.practice_id);
                                        }
                                        extraContactFormCleaning();
                                        // clearing practice_id value (stored in the hidden field) from the HTML
                                        // DOM after the contact/gp has been successfully added
                                        $("#PracticeAssociate_practice_id").val("");

                                        // Cleaning the data from the hidden fields as it might cause problems for the
                                        // non-mandatory fields when trying to add new contact or gp
                                        $(".js-contact-title").val("");
                                        $(".js-contact-first-name").val("");
                                        $(".js-contact-last-name").val("");
                                        $(".js-contact-primary-phone").val("");
                                        $(".js-contact-label-id").val("");
                                        $(".js-contact-practice-provider-no").val("");

                                        // Cleaning the contact label id after the contact/gp has been added successfully,
                                        // otherwise the same label gets saved for the contact (if left blank) on adding the contact/gp for the next time
                                        $(".js-extra-gp-contact-label-id").val("");

                                        // Hide the message once the existing practice is associated successfully.
                                        $("#extra-gp-message").hide();

                                        // enabling title and phone number on closing the popup.
                                        $("#extra-gp-form #Contact_title").prop("readonly", false);
                                        $("#extra-gp-form #Contact_primary_phone").prop("readonly", false);

                                        // remove data from hidden fields.
                                        $(".gp_data_retrieved").val("");

                                        // unsetting the variable (defined in create_contact_form inside the onselect function of autocompletesearch widget - firstname and lastname field)
                                        gp = new Gp();
                                    }
                                }',
                             ],
                             array('class' => 'button hint green')
                        ); ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php $this->endWidget(); ?>
        </div><!-- form -->
    </div>
</div>

<script>
    // This function checks if the string is a valid JSON.
    function isJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#extra_gp_autocomplete_contact_label_id'),
        url: '/gp/contactLabelList',
        maxHeight: '200px',
        onSelect: function(){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            removeSelectedContactLabel();
            addItem('extra_gp_selected_contact_label_wrapper', {item: AutoCompleteResponse});
            $('#extra_gp_autocomplete_contact_label_id').val('');
        }
    });

    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#autocomplete_extra_practice_id'),
        url: '/patient/practiceList',
        maxHeight: '200px',
        onSelect: function(){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            $('.js-selected-practice-associate').find('li').remove();
            $('.js-selected-practice-associate').append('<li><span class="js-name" style="text-align:justify">'+AutoCompleteResponse.label+'</span><i id="js-remove-extra-practice-'+AutoCompleteResponse.value+'" class="oe-i remove-circle small-icon pad-left"></i></li>');
            $('#PracticeAssociate_practice_id').val(AutoCompleteResponse.value);
            $('#js-remove-extra-practice-'+AutoCompleteResponse.value).click(function () {
                $(this).parent('li').remove();
                $('#PracticeAssociate_practice_id').val("");
            });
        }
    });
    $('#extra_gp_selected_contact_label_wrapper').find('.removeReading').click(function () {
        $('#extra_gp_selected_contact_label_wrapper').css('display','none');
        // clearing the selected gp role id when user removes the role of the gp/contact.
        $('.js-extra-gp-contact-label-id').val('');
        onChangeFirstLastRoleGpFields();
    });

    // Initializing the AutoComplete Search widget for the first Name field in the pop-up on adding gp or contact.
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#Contact\\[first_name\\]' ),
        url: '/patient/gpList',
        maxHeight: '200px',
        onSelect: function(){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            searchGps(AutoCompleteResponse);
        }
    });

    // Initializing the AutoComplete Search widget for the last Name field in the pop-up on adding gp or contact.
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#Contact\\[last_name\\]' ),
        url: '/patient/gpList',
        maxHeight: '200px',
        onSelect: function(){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            searchGps(AutoCompleteResponse);
        }
    });

    // Creating Gp Constructor
    function Gp(id, title, firstName, lastName, phoneno, role) {
        this.id = id;
        this.title = title;
        this.firstName = firstName;
        this.lastName = lastName;
        this.phoneno = phoneno;
        this.role = role;
        this.toString = function() {
            return '{"gpId": "' + this.id + '", "title": "' + this.title + '", "firstName": "' + this.firstName + '", "lastName": "' + this.lastName + '", "phoneno": "' + this.phoneno + '", "roleId": "' + this.role +'"}';
        }
    }

    // global gp variable to keep track of the gp data.
    var gp = new Gp();

    var enablePulsateEffect = true;

    function searchGps(response) {
        $('#extra-gp-form #Contact_title').val(response.gpTitle);
        $('#Contact\\[first_name\\]').val(response.gpFirstName);
        $('#Contact\\[last_name\\]').val(response.gpLastName);
        $('#extra-gp-form #Contact_primary_phone').val(response.gpPhoneno);
        autoCompleteContactLabel('extra_gp_selected_contact_label_wrapper', JSON.parse(response.gpRole), '#extra_gp_autocomplete_contact_label_id');

        // Setting the title, phone no. and provider no. to read only if user has select an existing gp.
        $("#extra-gp-form #Contact_title").prop("readonly", true);
        $("#extra-gp-form #Contact_primary_phone").prop("readonly", true);
        $('#extra-gp-message').hide();
        // Setting the property gpId.
        gp.id = response.value;
        // Show the message to the user.
        if($("#extra_gp_adding_title").text() === "Add Referring Practitioner"){
            notifyUserExistingorNewGpRecord(false, true);
        } else {
            notifyUserExistingorNewGpRecord(false, false);
        }
        pulsateEffect();
        enablePulsateEffect = true;
    }

    /**
     * On selecting an item from the search results, this  function adds the item (@param item) to the wrapper (@wrapperId)
     * @param wrapperId The id of the wrapper which contains the contact label (or role).
     * @param item The item that we want to add to the wrapper.
     * @param labelElementId id of the element from where the text is deleted (on selecting the item).
     */
    function autoCompleteContactLabel(wrapperId, item, labelElementId) {
        removeSelectedContactLabel();
        addItem(wrapperId, {item: item});
        $(labelElementId).val('');
    }

    $('#extra-gp-form #Contact\\[first_name\\]').on('input',function(e){
        onChangeFirstLastRoleGpFields();
    });

    $('#extra-gp-form #Contact\\[last_name\\]').on('input',function(e){
        onChangeFirstLastRoleGpFields();
    });

    function onChangeFirstLastRoleGpFields() {
        gp.id = '-1'; // Setting the gpId to -1 as there is no elegant way in php to handle undefined values.
        $("#extra-gp-form #Contact_title").prop("readonly", false);
        $("#extra-gp-form #Contact_primary_phone").prop("readonly", false);
        $('#extra-gp-message').hide();
        if($("#extra_gp_adding_title").text() === "Add Referring Practitioner"){
            notifyUserExistingorNewGpRecord(true, true);
        } else {
            notifyUserExistingorNewGpRecord(true, false);
        }

        if (enablePulsateEffect) {
            pulsateEffect();
            enablePulsateEffect = false;
        }
    }

    /**
     * This function display the message to let the user know whether they are associating an existing gp or creating a new one.
     * @param isNewRecord User has selected the existing practitioner or a new one will be created.
     * @param isGp To check whether user is adding a gp or gp contact.
     */
    function notifyUserExistingorNewGpRecord(isNewRecord, isGp) {
        $('#extra-gp-message').show();

        var msg = "";

        if(!isNewRecord && isGp) {
            msg = "You have selected an existing practitioner. <br/>Please click 'Next' to associate the existing practitioner with a practice.";
        }
        if(isNewRecord && isGp) {
            msg = "This action will create a new/find an existing practitioner. <br/>Please click 'Next' to associate the practitioner with a practice.";
        }
        if(!isNewRecord && !isGp) {
            msg = "You have selected an existing practitioner contact. <br/>Please click 'Next' to associate the existing practitioner contact with a practice.";
        }
        if(isNewRecord && !isGp) {
            msg = "This action will create a new/find an existing practitioner contact. <br/>Please click 'Next' to associate the practitioner contact with a practice.";
        }

        $('#extra-gp-message p').html(msg);
    }

    function pulsateEffect() {
        $('#extra-gp-message p').effect('pulsate', {
            times: 2
        }, 600);
    }

</script>
