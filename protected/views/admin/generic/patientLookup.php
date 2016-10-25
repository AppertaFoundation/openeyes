<div class="row field-row">
    <div class="large-2 column">
        <label for="patient-search">Patient Lookup</label>
    </div>
    <div class="large-5 column end">
        <input type="hidden" name="<?=get_class($model)?>[patient_lookup_gender]" />
        <input type="hidden" name="<?=get_class($model)?>[patient_lookup_deceased]" />
        <input type="text" name="search" id="patient-search" class="form panel search large ui-autocomplete-input" placeholder="Enter search..." autocomplete="off">
        <div style="display:none" class="no-result-patients warning alert-box">
            <div class="small-12 column text-center">
                No results found.
            </div>
        </div>
        <?php if(!$model->patient_id):?>
            <div id="patient-result" style="display: none">

            </div>
            <input type="hidden" name="<?=get_class($model)?>[patient_id]" id="patient-result-id">
        <?php else:?>
            <div id="patient-result">
                <?= $model->patient->fullName?>
            </div>
            <input type="hidden" name="<?=get_class($model)?>[patient_id]" id="patient-result-id" value="<?=$model->patient_id?>">
        <?php endif;?>
    </div>
    <div class="large-3 column text-left">
        <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('/img/ajax-loader.gif') ?>"  alt="loading..." style="margin-right: 10px; display: none;"/>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        OpenEyes.UI.Search.init($('#patient-search'));
        OpenEyes.UI.Search.getElement().autocomplete('option', 'select', function(event, uid){
            $('#patient-search').hide();
            $('#patient-result').html('<span>'+ uid.item.first_name + ' ' + uid.item.last_name +'</span>').show();
            $('#patient-result-id').val(uid.item.id);
            $('input[name="<?=get_class($model)?>\[patient_lookup_gender\]"]').val(uid.item.gender).trigger('change');
            $('input[name="<?=get_class($model)?>\[patient_lookup_deceased\]"]').val(uid.item.is_deceased).trigger('change');
        });
    });
</script>
