<div class="element-fields full-width">
    <div class="flex-layout flex-top col-gap">

        <div class="cols-6">

            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-5">
                    <col class="cols-7">
                </colgroup>
                <tbody>
                <tr>
                    <td>Title and Surname</td>
                    <td>
                        <?= CHtml::activeTextField($element, 'title_surname', ['class' => 'cols-full']); ?>
                    </td>
                </tr>
                <tr>
                    <td>Other names</td>
                    <td><?= CHtml::activeTextField($element, 'other_names', ['class' => 'cols-full']); ?></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td><?= CHtml::activeTextArea($element, 'address', ["class" => "cols-full"]); ?></td>
                </tr>
                <tr>
                    <td>Post Code</td>
                    <td>
                        <?= CHtml::activeTextField($element, 'postcode', ['class' => 'cols-5', "maxlength" => 4]); ?>
                        &nbsp;
                        <?= CHtml::activeTextField($element, 'postcode_2nd', ['class' => 'cols-5', "maxlength" => 3]); ?>
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><?= CHtml::activeEmailField($element, 'email', ['class' => 'cols-full']); ?></td>
                </tr>
                <tr>
                    <td>Telephone</td>
                    <td><?= CHtml::activeTelField($element, 'telephone', ['class' => 'cols-full']); ?></td>
                </tr>
                <tr>
                    <td>Date of Birth</td>
                    <td><?= CHtml::activeTelField($element, 'date_of_birth', ['class' => 'cols-full']); ?></td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td>
                        <?= CHtml::activeDropDownList($element, 'gender_id', CHtml::listData(Gender::model()->findAll(), 'id', 'name'), [
                            'class' => 'cols-full'
                        ]); ?>
                    </td>
                </tr>
                <tr>
                    <td>Ethnic Group</td>
                    <td>
                        <?= CHtml::activeDropDownList($element, 'ethnic_group_id', CHtml::listData(EthnicGroup::model()->findAll(), 'id', 'name'), [
                            'class' => 'cols-full'
                        ]); ?>
                    </td>
                </tr>
                </tr>
                <tr>
                    <td>Describe other ethnic group</td>
                    <td>
                        <?= CHtml::activeTextArea($element, 'describe_ethnics', ['class' => 'cols-full']); ?>
                    </td>
                </tr>
                </tbody>
            </table>

        </div><!-- left -->

        <div class="cols-6">

            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-5">
                    <col class="cols-7">
                </colgroup>

                <tbody>
                <?php if (false): ?>
                    <tr>
                        <td><?= PatientIdentifierHelper::getIdentifierPrompt($this->patient->globalIdentifier); ?></td>
                        <td><?= PatientIdentifierHelper::getIdentifierValue($this->patient->globalIdentifier) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>NHS Number</td>
                    <td><?= CHtml::activeTextField($element, 'nhs_number', ['class' => 'cols-full']); ?></td>
                </tr>
                <tr>
                    <td>GP's Name</td>
                    <td><?= CHtml::activeTextArea($element, 'gp_name', ["class" => "cols-full"]); ?></td>
                </tr>
                <tr>
                    <td>GP's Address</td>
                    <td><?= CHtml::activeTextArea($element, 'gp_address', ["class" => "cols-full"]); ?></td>
                </tr>
                <tr>
                    <td>GP's Post Code</td>
                    <td>
                        <?= CHtml::activeTextField($element, 'gp_postcode', ['class' => 'cols-5', "maxlength" => 4]); ?>
                        &nbsp;
                        <?= CHtml::activeTextField($element, 'gp_postcode_2nd', ['class' => 'cols-5', "maxlength" => 3]); ?>
                    </td>
                </tr>
                <tr>
                    <td>GP's Telephone</td>
                    <td>
                        <?= CHtml::activeTextField($element, 'gp_telephone', ['class' => 'cols-full']); ?>
                    </td>
                </tr>
                </tbody>
            </table>

            <hr class="divider">
            <div class="row field-row">
                <div class="small-push-6 column-5">
                    <a href="#" id="la-search-toggle" class="button secondary small">
                        Find Local Authority Details
                    </a>
                </div>
            </div>
            <?php $this->renderPartial('localauthority_search', array('hidden' => true)); ?>

            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-5">
                    <col class="cols-7">
                </colgroup>
                <tbody>
                <tr>
                    <td>Local Authority Name:</td>
                    <td>Local Authority Name:</td>
                </tr>
                <tr>
                    <td>Local Authority Address:</td>
                    <td>Local Authority Address</td>
                </tr>
                <tr>
                    <td>Local Authority Post Code:</td>
                    <td>
                        <?= CHtml::activeTextField($element, 'postcode', ['class' => 'cols-5', "maxlength" => 4]); ?>
                        &nbsp;
                        <?= CHtml::activeTextField($element, 'postcode_2nd', ['class' => 'cols-5', "maxlength" => 3]); ?>
                    </td>
                </tr>
                <tr>
                    <td>Local Authority Telephone:</td>
                    <td>
                        Local Authority Telephone:
                    </td>
                </tr>
                <tr>
                    <td>Local Authority Email:</td>
                    <td>
                        Local Authority Email:
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
