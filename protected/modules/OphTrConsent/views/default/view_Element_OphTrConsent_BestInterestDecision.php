<?php /** @var \OEModule\OphTrConsent\models\Element_OphTrConsent_BestInterestDecision $element */ ?>
<?php
    $model_name = CHtml::modelName($element);
?>
<div class="element-data flex-layout flex-top col-gap">
    <?php if (!$element->patient_has_not_refused) : ?>
        Patient has refused
    <?php else : ?>
        <div class="cols-6">
            <div class="row">
                <?= str_replace('\n', '<br>', CHtml::encode($element->getAttributeLabel("patient_has_not_refused_view")))?>
            </div>
            <div class="row">
                <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->first_name . ' ' . $element->lastModifiedUser->last_name ?>"  data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->first_name . ' ' . $element->lastModifiedUser->last_name ?>"}'></i>
                <span class="user-comment"><?php echo nl2br(CHtml::encode($element->reason_for_procedure ?: "-"))?></span>
            </div>
        </div>
        <div class="cols-5">
            <?php if($element->treatment_cannot_wait_reason): ?>
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("treatment_cannot_wait_reason_view")).':'?>
                </div>
                <div class="row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->first_name . ' ' . $element->lastModifiedUser->last_name ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->first_name . ' ' . $element->lastModifiedUser->last_name ?>"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->treatment_cannot_wait_reason ?: "-"))?></span>
                </div>
            <?php endif; ?>
            <?php if ($element->wishes) : ?>
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("wishes")).':'?>
                </div>
                <div class="row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->first_name . ' ' . $element->lastModifiedUser->last_name ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->first_name . ' ' . $element->lastModifiedUser->last_name ?>"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->wishes ?: "-"))?></span>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>