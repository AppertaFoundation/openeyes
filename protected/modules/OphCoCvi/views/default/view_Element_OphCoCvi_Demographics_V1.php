<div class="element-data full-width">
    <div class="flex-layout flex-top col-gap">

        <div class="cols-half">

            <table class="label-value">
                <tbody>
                <tr>
                    <td>
                        <div class="data-label">Full name</div>
                    </td>
                    <td>
                        <div class="data-value"><?= CHtml::encode($this->patient->fullName) ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">Born</div>
                    </td>
                    <td>
                        <div class="data-value"><?= CHtml::encode(($this->patient->dob) ? $this->patient->NHSDate('dob') : 'Unknown') ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label"><?= PatientIdentifierHelper::getIdentifierPrompt($this->patient->globalIdentifier); ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?= PatientIdentifierHelper::getIdentifierValue($this->patient->globalIdentifier) ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('address')) ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?= nl2br(CHtml::encode($element->address)) ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">Post Code (1st half)</div>
                    </td>
                    <td>
                        <div class="data-value"><?= substr(\CHtml::encode($element->postcode),0, 4) ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('email'))?></div>
                    </td>
                    <td>
                        <div class="data-value"><?= \CHtml::encode($element->email); ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('telephone')); ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?=\CHtml::encode($element->telephone); ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('gender')); ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?= CHtml::encode($element->gender->name ?? '') ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('ethnic_group')) ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?= \CHtml::encode($element->ethnic_group->name ?? '') ?></div>
                    </td>
                </tr>
                </tbody>
            </table>

        </div> <!-- cols -->

        <div class="cols-half">

            <table class="label-value">
                <tbody>
                <tr>
                    <td>
                        <div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('gp_name')) ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?= \CHtml::encode($element->gp_name) ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('gp_address')) ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?= nl2br(\CHtml::encode($element->gp_address)) ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('gp_postcode')) ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?= CHtml::encode($element->gp_postcode) ?> <?= CHtml::encode($element->gp_postcode_2nd) ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('gp_telephone')) ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?= CHtml::encode($element->gp_telephone) ?></div>
                    </td>
                </tr>
                </tbody>
            </table>
            <hr class="divider">
            <table class="label-value">
                <tbody>
                    <tr>
                        <td><div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('la_name')) ?></div></td>
                        <td><div class="data-value"><?= \CHtml::encode($element->la_name) ?></div></td>
                    </tr><tr>
                        <td><div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('la_address')) ?></div></td>
                        <td><div class="data-value"><?= \CHtml::encode($element->la_address) ?></div></td>
                    </tr><tr>
                        <td><div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('la_postcode')) ?></div></td>
                        <td><div class="data-value"><?= CHtml::encode($element->la_postcode) ?> <?= CHtml::encode($element->la_postcode_2nd) ?></div></td>
                    </tr><tr>
                        <td><div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('la_telephone')) ?></div></td>
                        <td><div class="data-value"><?= \CHtml::encode($element->la_telephone) ?></div></td>
                    </tr><tr>
                        <td><div class="data-label"><?= \CHtml::encode($element->getAttributeLabel('la_email')) ?></div></td>
                        <td><div class="data-value"><?= \CHtml::encode($element->la_email) ?></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
