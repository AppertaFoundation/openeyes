<?php
    /**
     * @var string $form_css_class
     */
?>
<?php $patient_has_address = isset($this->patient->contact->address); ?>
<div class="fpten-form-row">
    <div class="fpten-form-column">
        <div class="fpten-form-row">
            <div class="fpten-form-column <?= $form_css_class ?>-age">
                <?= $this->patient->getAge() . 'y' ?>
            </div>
        </div>
        <div class="fpten-form-row">
            <div  class="fpten-form-column <?= $form_css_class ?>-dob">
                <?= Helper::convertDate2FullYear($this->patient->dob) ?>
            </div>
        </div>
    </div>
    <div class="fpten-form-column">
        <div class="fpten-form-row">
            <div class="fpten-form-column <?= $form_css_class ?>-patient-details">
                <div class="fpten-form-row">
                    <?= $this->patient->fullname ?><br/>
                    <br/>
                    <?php if ($patient_has_address) { ?>
                        <?= $this->patient->contact->address->address1 ?><br/>
                        <?= $this->patient->contact->address->address2 ?><?= $this->patient->contact->address->address2 ? '<br/>' : null ?>
                    <?php } ?>
                </div>
                <div class="fpten-form-row">
                    <table>
                        <tbody>
                        <tr>
                            <?php if ($address) { ?>
                            <td <?= ($form_css_class !== 'wpten') ? 'style="width: 70%"' : ''?>>
                                <?php if ($patient_has_address) { ?>
                                    <?= $this->patient->contact->address->city ?><br/>
                                    <?= $this->patient->contact->address->county ?><br/><?= $form_css_class !== 'wpten' ? '<br/>' : null ?>
                                    <?= (!$this->patient->contact->address->address2 && $form_css_class !== 'wpten') ? '<br/>' : null ?>
                                    <?= (!$this->patient->contact->address->county && $form_css_class !== 'wpten') ? '<br/>' : null ?>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($patient_has_address) { ?>
                                    <?= $this->patient->contact->address->county ? '<br/>' : null ?>
                                    <?= $this->patient->contact->address->postcode ?>
                                <?php } ?>
                            </td>
                            <?php } else { ?>
                                <br/>
                                <td<?= ($form_css_class !== 'wpten') ? 'style="width: 70%"' : ''?>>
                                    Patient's address is unknown<br/>
                                </td>
                            <?php } ?>
                        </tr>
                        <?php
                        $secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_secondary_number_usage_code'], $this->patient->id, Institution::model()->getCurrent()->id, Yii::app()->session['selected_site_id']);
                        if ($form_css_class === 'wpten') { ?>
                            <tr>
                                <td><?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) ?>:</td>
                                <td><?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier) ?></td>
                            </tr>
                        <?php } else { ?>
                        <tr>
                            <td></td>
                            <td><?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier) ?></td>
                        </tr>
                        <?php }; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if ($form_css_class === 'wpten') { ?>
            <div class="fpten-form-row">
                <div class="fpten-form-column wpten-prescriber">
                    <!--HOSPITAL YSBYTY-->
                    <!--DOCTOR MEDDYG-->
                </div>
            </div>
        <?php } ?>
    </div>
</div>
