<div class="element-data">
    <div class="large-6 column">
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('name') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->name) ?></div>
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
            <div class="large-8 column">
                <div class="data-label"><?= $element->getAttributeLabel('nhs_number') ?>:</div>
            </div>
            <div class="large-10 column end">
                <div class="data-value"><?= CHtml::encode($element->nhs_number) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?= $element->getAttributeLabel('address') ?>:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"><?= CHtml::encode($element->address) ?></div>
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
                <div class="data-value"><?= CHtml::encode($element->gp_address) ?></div>
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
    </div>
</div>