<div class="element-data">
    <div class="large-6 column">
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('title_surname') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->title_surname) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('other_names') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->other_names) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('date_of_birth') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->NHSDate('date_of_birth')) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('nhs_number') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->nhs_number) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('address') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= nl2br(CHtml::encode($element->address)) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('postcode') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->postcode) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('email') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->email) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('telephone') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->telephone) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('gender') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->gender ? $element->gender->name : '') ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('ethnic_group') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->ethnic_group ? $element->ethnic_group->name : '') ?></div>
            </div>
        </div>
    </div>
    <div class="large-6 column">
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('gp_name') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->gp_name) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('gp_address') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= nl2br(CHtml::encode($element->gp_address)) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('gp_telephone') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->gp_telephone) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('la_name') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->la_name) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('la_address') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= nl2br(CHtml::encode($element->la_address)) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('la_telephone') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->la_telephone) ?></div>
            </div>
        </div>
    </div>
</div>