<?php
    /**
     * @var string $form_css_class
     */
?>
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
                    <?= $this->patient->contact->address->address1 ?><br/>
                    <?= $this->patient->contact->address->address2 ?><?= $this->patient->contact->address->address2 ? '<br/>' : null ?>
                    <?= $this->patient->contact->address->city ?>
                </div>
                <div class="fpten-form-row">
                    <table>
                        <tbody>
                        <tr>
                            <td <?= ($form_css_class !== 'wpten') ? 'style="width: 70%"' : ''?>>
                                <?= $this->patient->contact->address->county ?>
                                <?= (!$this->patient->contact->address->address2 && $form_css_class !== 'wpten') ? '<br/><br/>' : null ?>
                            </td>
                            <td>
                                <?= $this->patient->contact->address->postcode ?>
                            </td>
                        </tr>
                        <?php if ($form_css_class === 'wpten') : ?>
                            <tr>
                                <td>NHS Number:</td>
                                <td><?= $this->patient->nhs_num ?></td>
                            </tr>
                        <?php else : ?>
                        <tr>
                            <td></td>
                            <td><?= $this->patient->nhs_num ?></td>
                        </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if ($form_css_class === 'wpten') : ?>
            <div class="fpten-form-row">
                <div class="fpten-form-column wpten-prescriber">
                    <!--HOSPITAL YSBYTY-->
                    <!--DOCTOR MEDDYG-->
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
