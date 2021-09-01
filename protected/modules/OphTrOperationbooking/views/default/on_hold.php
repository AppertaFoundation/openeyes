<div class="oe-popup-wrap" id="js-put-operation-on-hold" style="display:none; z-index:100">
    <div class="oe-popup">
        <?= \CHtml::form(array('default/putOnHold/' . $this->event->id), 'post', array('id' => 'putOnHoldForm')) ?>
        <div class="title">
            Put Operation Booking On Hold
        </div>
        <div class="oe-popup-content delete-event">
            <div class="alert-box warning js-error-on-hold-errors-box" style="display:none">
                <p id="on_hold_errors"></p>
            </div>
            <table class="row">
                <tbody>

                <tr>
                    <td>Reason for putting on hold:</td>
                    <td><?= \CHtml::dropDownList(
                        'on_hold_reason',
                        false,
                        CHtml::listData(OphTrOperationBooking_Operation_On_Hold_Reason::model()->findAll(), 'reason', 'reason') + ['Other' => 'Other'],
                        ['empty' => 'Please Select']
                    ) ?></textarea></td>
                </tr>
                <tr class="js-other-reason-box" style="display:none;">
                    <td>Other reason:</td>
                    <td>
                        <?= \CHtml::textArea('other_reason', '', array('cols' => 40, 'id' => 'js-on_hold-other-reason-area')) ?>
                    </td>
                </tr>
                <tr>
                    <td>Comments:</td>
                    <td>
                        <?= \CHtml::textArea('on_hold_comments', '', array('cols' => 40, 'id' => 'js-on_hold-comment-area')) ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="flex-layout row">
                <h4>Are you sure you want to proceed? </h4>
                <?php
                echo CHtml::hiddenField('event_id', $this->event->id); ?>
                <button type="submit" class="large red hint" id="et_put_on_hold" name="et_put_on_hold">
                    Put on Hold
                </button>
                <button type="submit" class="large blue hint cancel-icon-btn" id="et_cancel_put_on_hold"
                        name="et_cancel_put_on_hold">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    <?= \CHtml::endForm(); ?>
</div>

<script>
    $(document).ready(function () {
        $('#on_hold_reason').on('change', function () {
            $('.js-other-reason-box').toggle( $(this).val() === "Other");
        });

        $('#js-put-on-hold, #js-put-on-hold_footer').click(function (event) {
            event.preventDefault();
            $('#on_hold_errors').text("");
            $('#js-put-operation-on-hold').css('display', 'flex');
        });

        $('#et_put_on_hold').click(function (event) {
            let reason = $('#on_hold_reason option:selected');
            if (reason.val() === "" || reason.val() === "Other" && $('#js-on_hold-other-reason-area').val().trim() === "") {
                $('.js-error-on-hold-errors-box').show();
                $('#on_hold_errors').text("Please enter the reason for putting the booking on hold");
                event.preventDefault();
            } else {
                $('#on_hold_errors').text("");
                $('.js-error-on-hold-errors-box').hide();
            }
        });

        $('#et_cancel_put_on_hold').click(function (event) {
            event.preventDefault();
            $('#on_hold_errors').text("");
            $('.js-error-on-hold-errors-box').hide();
            $('#js-put-operation-on-hold').css('display', 'none');
        });

        $('#js-put-operation-on-hold').submit(function () {
            $('#et_put_on_hold').attr('disabled', 'disabled');
            $('#et_cancel_put_on_hold').attr('disabled', 'disabled');
        });
    })
</script>