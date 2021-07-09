<div class="element-data">
    <div class="large-6 column">
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('title_surname')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->title_surname) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('other_names')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->other_names) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('date_of_birth')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->NHSDate('date_of_birth')) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('nhs_number')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->nhs_number) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('address')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= nl2br(CHtml::encode($element->address)) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('postcode')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->postcode) ?> <?= CHtml::encode($element->postcode_2nd) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('email')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->email) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('telephone')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->telephone) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('gender')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->gender ? $element->gender->name : '') ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('ethnic_group')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->ethnic_group ? $element->ethnic_group->name : '') ?></div>
            </div>
        </div>
        <?php if($element->describe_ethnics) { ?>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('describe_ethnics')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->describe_ethnics ? $element->describe_ethnics : '') ?></div>
            </div>
        </div>
        <?php } ?>
        
    </div>
    <div class="large-6 column">
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('gp_name')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->gp_name) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('gp_address')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= nl2br(CHtml::encode($element->gp_address)) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('gp_postcode')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->gp_postcode) ?> <?= CHtml::encode($element->gp_postcode_2nd) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('gp_telephone')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->gp_telephone) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('la_name')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->la_name) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('la_address')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= nl2br(CHtml::encode($element->la_address)) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('la_postcode')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->la_postcode) ?> <?= CHtml::encode($element->la_postcode_2nd) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('la_telephone')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->la_telephone) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('la_email')) ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->la_email) ?></div>
            </div>
        </div>
    </div>
</div>