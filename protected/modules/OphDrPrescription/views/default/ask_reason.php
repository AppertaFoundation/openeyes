<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php $this->beginContent('//patient/event_container', array('no_face'=>true)); ?>

<?php

    $this->event_actions[] = EventAction::link('Cancel', '/OphDrPrescription/default/view/'.$id, [], ['class'=>'button small cancel']);
    $this->event_tabs[] = array(
        'label' => 'View',
        'href' => '/OphDrPrescription/default/view/'.$id
    );

    $this->event_tabs[] = array(
        'label' => 'Edit',
        'href' => '#',
        'active' => true
    );
    ?>


<section class="element">
  <div class="element-fields">
<div style="padding-left: 25px;">

<h1><i class="oe-i triangle"></i> Reason required</h1>

    <?php
        $text = '';
    if (($draft == 0) && ($printed == 0)) {
        $text = 'finalised';
    } else {
        $text = 'printed';
    }
    ?>
    This prescription has been <?php echo $text; ?>. Changes to <?php echo $text; ?> prescriptions must
    only be made under specific circumstances. Please select a reason from the list below:


<?php

$reasons = OphDrPrescriptionEditReasons::model()->findAll(array('order'=>'display_order', 'condition'=>'active = 1'));

?>
<?=\CHtml::form('/OphDrPrescription/default/update/'.$id.'?reason=selected', 'get'); ?>
    <input type="hidden" name="do_not_save" value="1" />
    <input type="hidden" name="reason" id="reason" />


    <?php foreach ($reasons as $key=>$reason) : ?>
        <div>
            <button class="hint blue submit" data-value="<?php echo $reason->id; ?>" id="reason_<?php echo $reason->id; ?>" data-test="reason_<?php echo $reason->id; ?>" style="margin-bottom: 15px;"><?php echo htmlentities($reason->caption); ?></button>
        </div>
    <?php endforeach; ?>


  <div class="data-group">
        <div class="cols-6 column">
            <textarea rows="5" cols="40" readonly type="text" id="reason_other_text" name="reason_other"></textarea>
        </div>
        <div class="column cols-6">
            <div id="other_reason_controls" style="display: none;">
                <a href="javascript:void(-1);" id="submit_other" style="color: #3fa522;"><i class="oe-i tick large"></i></a>
                <a href="javascript:void(-1);" id="cancel_other" style="color: #eb5911;"><i class="oe-i remove large"></i></a>
            </div>
        </div>
    </div>

<?=\CHtml::endForm() ?>
<br/>
  <div class="alert-box warning">
    Any old paper copies of this prescription MUST BE DESTROYED.
  </div>
</div>

  </div>
</section>

<?php $this->endContent();?>



<script type="text/javascript">
    $(function(){
        window.onbeforeunload = null;
        $(document).on("click","button.submit", function(e){
            e.preventDefault();
            var value = $(this).data('value');
            if (value=='1')
            {
                $('#reason').val(1);
                $('#reason_other_text').removeAttr("readonly").focus();
                $('#other_reason_controls').show();
                var $buttons = $('button.submit').not(this);
                $buttons.attr("disabled", "disabled");
                $buttons.css("pointer-events", "none");
                $buttons.removeClass("blue");
            }
            else
            {
                $('#reason_other_text').val("").attr("readonly", "readonly");
                $('#other_reason_controls').hide();
                $('#reason').val(value);
                $(this).closest('form').submit();
            }
        });

        $("#cancel_other").click(function(e){
            $('#reason_other_text').val("").attr("readonly", "readonly");
            $('#other_reason_controls').hide();
            $('#reason').val('');
            var $buttons = $('button.submit').not(this);
            $buttons.removeAttr("disabled");
            $buttons.css("pointer-events", "");
            $buttons.addClass("blue");
        });

        $("#submit_other").click(function(e){
            $(this).closest('form').submit();
        });

    });
</script>