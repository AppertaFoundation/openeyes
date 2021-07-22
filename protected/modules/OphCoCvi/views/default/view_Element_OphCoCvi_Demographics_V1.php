<div class="element-data full-width">
    <div class="flex-layout flex-top col-gap">

        <div class="cols-half">

            <table class="label-value">
                <tbody>
                <tr>
                    <td>
                        <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('title_surname')) ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?= CHtml::encode($element->title_surname) ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('other_names')) ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?= CHtml::encode($element->other_names) ?></div>
                    </td>
                </tr><tr>

                    <td>
                        <div class="data-label">Born</div>
                    </td>
                    <td>
                        <div class="data-value">17 Nov 1940</div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">NHS Number</div>
                    </td>
                    <td>
                        <div class="data-value">385 999 8817</div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">Address (incl. Post Code)</div>
                    </td>
                    <td>
                        <div class="data-value">53 Appleby Crescent,<br>
                            Brighouse Bay,<br>
                            Cheshire,<br>
                            FA39 4WZ</div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">Post Code (1st half)</div>
                    </td>
                    <td>
                        <div class="data-value">FA39</div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">Email:</div>
                    </td>
                    <td>
                        <div class="data-value">Pauline.Conant@hotmail.com</div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">Telephone</div>
                    </td>
                    <td>
                        <div class="data-value">01234567890</div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">Gender</div>
                    </td>
                    <td>
                        <div class="data-value">Male</div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">Ethnic Group</div>
                    </td>
                    <td>
                        <div class="data-value">White – Any other background</div>
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
                        <div class="data-label">GP's Name</div>
                    </td>
                    <td>
                        <div class="data-value">Dr James Kildare</div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">GP's Address</div>
                    </td>
                    <td>
                        <div class="data-value">Elm Practice,<br>
                            1a Fountayne Road,<br>
                            London,<br>
                            UB1 2TU</div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">GP's Telephone</div>
                    </td>
                    <td>
                        <div class="data-value">020 722620000</div>
                    </td>
                </tr>
                </tbody>
            </table>

        </div> <!-- cols -->

    </div><!-- .flex -->
    <?php echo '<pre>' . print_r($element->attributes, true) . '</pre>'; ?>
</div>

<div class="element-data" style="display:none">
    <div class="large-6 column">
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label">:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label">:</div>
            </div>
            <div class="large-8 column end">
                <div class="data-value"></div>
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