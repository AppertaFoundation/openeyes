<?php
    /**
     * @var string $form_css_class
     */
?>
<div class="fpten-form-row">
    <div class="fpten-form-column">
        <div class="fpten-form-row">
            <div id="<?= $form_css_class ?>-age" class="fpten-form-column">
                <?= $this->patient->getAge() . 'y' ?>
            </div>
        </div>
        <div class="fpten-form-row">
            <div id="<?= $form_css_class ?>-dob" class="fpten-form-column">
                <?= Helper::convertDate2FullYear($this->patient->dob) ?>
            </div>
        </div>
    </div>
    <div class="fpten-form-column">
        <div class="fpten-form-row">
            <div id="<?= $form_css_class ?>-patient-details" class="fpten-form-column">
                <?= $this->patient->fullname ?><br/>
                <?= $this->patient->contact->address->address1 ?>
                <?= $this->patient->contact->address->address2 ? '<br/>' : null ?>
                <?= $this->patient->contact->address->address2 ?><br/>
                <?= $this->patient->contact->address->city ?>
                <?= $this->patient->contact->address->county ? '<br/>' : null ?>
                <?= $this->patient->contact->address->county ?>
                <span id="fpten-postcode"><?= str_replace(' ', '', $this->patient->contact->address->postcode) ?></span>
            </div>
        </div>
        <div class="fpten-form-row">
            <div id="fpten-nhs" class="fpten-form-column">
                <span id="<?= $form_css_class ?>-nhs-text">
                    <?= ($form_css_class === 'wpten') ? 'NHS Number: ' . $this->patient->nhs_num : $this->patient->nhs_num ?>
                </span>
            </div>
        </div>
        <?php if ($form_css_class === 'wpten') : ?>
            <div class="fpten-form-row">
                <div id="wpten-prescriber" class="fpten-form-column">
                    <!--HOSPITAL YSBYTY-->
                    <!--DOCTOR MEDDYG-->
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
