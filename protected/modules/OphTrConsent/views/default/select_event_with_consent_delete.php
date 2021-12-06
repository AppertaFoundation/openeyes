<?=\CHtml::form(array('Default/delete/'.$old_consent_event[0]['id']), 'post', array('id' => 'deleteForm'))?>
<div class="oe-popup-wrap"><!-- generic oe-popup -->
    <div class="oe-popup">
        <div class="title">Delete &amp; Replace Consent Form</div>
        <div id="consent_delete_modal_cancel" class="close-icon-btn">
            <i class="oe-i remove-circle pro-theme"></i>
        </div>

        <!-- CSS hook: delete-event for specific styling -->
        <div class="oe-popup-content">

            <h3>Left Trabeculectomy</h3> 

                    <div class="alert-box warning">
                <strong>WARNING: This will permanently remove the consent event and replace it with a new event.<br>THIS ACTION CANNOT BE UNDONE.</strong>
            </div>

            <table class="large-text">
                <tbody>
                    <tr>
                        <th>Delete </th>
                        <td>
                            <i class="oe-i-e i-CoPatientConsent pad-right"></i> Consent Form <?php echo date('j M Y', strtotime($old_consent_event[0]['last_modified_date'])); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Create new</th>
                        <td>
                            <i class="oe-i-e i-CoPatientConsent pad-right"></i> Consent Form 30 Aug 2021
                        </td>
                    </tr>
                </tbody>
            </table>

            <div style="width:300px; margin-bottom: 0.6em;">
                <p>Reason for deletion:</p>
                <?=\CHtml::textArea('delete_reason', '')?>
            </div>

            <h3>Are you sure you want to proceed?</h3>

            <div class="popup-actions flex-right">
                <?php echo CHtml::hiddenField('event_id', $old_consent_event[0]['id']); ?>
                <button type="submit" id="et_deleteevent" name="et_deleteevent" class="green hint">Yes - Create new consent form</button>
                <button type="submit" id="consent_delete_modal_no" name="et_canceldelete" class="js-demo-cancel-btn">No, cancel</button>
            </div>

        </div><!-- oe-popup-content -->
    </div><!-- oe-popup -->
</div> 
<?=\CHtml::endForm(); ?>
