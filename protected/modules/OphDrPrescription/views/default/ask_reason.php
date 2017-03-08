<?php $this->beginContent('//patient/event_container'); ?>

<h1><i class="fa fa-exclamation-triangle" style="color: #eb5911;"></i> Reason required</h1>
<p>
    This prescription has been printed. Changes to printed prescriptions must
    only be made under specific circumstances. Please select a reason from the list below:
</p>

<?php

$reasons = OphDrPrescriptionEditReasons::model()->findAll(array('order'=>'display_order', 'condition'=>'active = 1'));

?>

<?php foreach ($reasons as $key=>$reason): ?>
    <div>
        <input type="radio" value="<?php echo $reason->id; ?>" name="reason" id="reason_<?php echo $reason->id; ?>" onclick="window.location.href='/OphDrPrescription/default/update/<?php echo $id; ?>?reason=<?php echo $reason->id; ?>';" />
        <label style="display: inline" for="reason_<?php echo $reason->id; ?>" onclick="window.location.href='/OphDrPrescription/default/update/<?php echo $id; ?>?reason=<?php echo $reason->id; ?>';"><?php echo htmlentities($reason->caption); ?></label>
    </div>
<?php endforeach; ?>

<br/>
<p style="color: red;">Any old paper copies of this prescription MUST BE DESTROYED.</p>

<?php $this->endContent();?>

<script type="text/javascript">
    $(function(){
        window.onbeforeunload = null;
    });
</script>