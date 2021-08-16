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
                <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>"  data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>"}'></i>
                <span class="user-comment"><?php echo nl2br(CHtml::encode($element->reason_for_procedure ?: "-"))?></span>
            </div>
        </div>
        <div class="cols-5">
            <?php if($element->treatment_cannot_wait_reason): ?>
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("treatment_cannot_wait_reason_view")).':'?>
                </div>
                <div class="row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->treatment_cannot_wait_reason ?: "-"))?></span>
                </div>
            <?php endif; ?>
            <?php if ($element->wishes) : ?>
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("wishes")).':'?>
                </div>
                <div class="row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->wishes ?: "-"))?></span>
                </div>
            <?php endif; ?>
            <?php if (isset($element->deputy_granted)) : ?>
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("deputy_granted")).':'?>
                </div>
                <div class="row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>"}'></i>
                    <span class="user-comment"><?php echo $element->deputy_granted ? "Yes" : "No" ?></span>
                </div>
            <?php endif; ?>
            <?php if (isset($element->circumstances)) : ?>
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("circumstances")).':'?>
                </div>
                <div class="row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->circumstances ?: "-")) ?></span>
                </div>
            <?php endif; ?>
            <?php if (isset($element->imca_view)) : ?>
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("imca_view")).':'?>
                </div>
                <div class="row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->imca_view ?: "-")) ?></span>
                </div>
            <?php endif; ?>
            <?php if (isset($element->options_less_restrictive)) : ?>
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("options_less_restrictive")).':'?>
                </div>
                <div class="row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->options_less_restrictive ?: "-")) ?></span>
                </div>
            <?php endif; ?>
            <?php if (isset($element->views_of_colleagues)) : ?>
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("views_of_colleagues")).':'?>
                </div>
                <div class="row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->views_of_colleagues ?: "-")) ?></span>
                </div>
            <?php endif; ?>
            <?php if (isset($element->decision)) : ?>
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("decision")).':'?>
                </div>
                <div class="row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->decision ?: "-")) ?></span>
                </div>
            <?php endif; ?>
            <?php if (isset($element->protected_file_id)) : ?>
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("protected_file_id")).':'?>
                </div>
                <div class="row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->getFullName() ?>"}'></i>
                    <span class="user-comment"><a href="/protectedFile/download/<?=$element->protected_file_id?>"><?=CHtml::encode($element->file->name)?></a></span>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>