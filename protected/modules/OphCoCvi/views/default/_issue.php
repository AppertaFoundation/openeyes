<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$logo_helper = new LogoHelper();
$esign_element = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_Esign');
$eventinfo_element = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_EventInfo');
$clinicalinfo_element = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo');
$consent_element = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_Consent');
$demographics_element = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics');
$clearical_info = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo');

$getSignatureSource = function (int $type) use ($eventinfo_element): string {
    $signature = $eventinfo_element->getSignatureByType($type);

    if (isset($signature->signatureFile)) {
        $signature_content = file_get_contents($signature->signatureFile->getPath());
        return 'data:' . $signature->signatureFile->mimetype . ';base64,' . base64_encode($signature_content);
    }

    return '';
};

if ($demographics_element->isNewRecord) {
    $gp_name = $patient->gp->contact->fullName ?? '';
    // getLetterAddress checks if the patient has Practice and returns that if he/she has
    $address = $patient->gp->getLetterAddress(['patient' => $patient]);
    $gp_postcode = $patient->gp->getGPPostcode(['patient' => $patient]) ?? '';
    $gp_telephone = $patient->practice->contact->primary_phone ?? $patient->gp->contact->primary_phone ?? '';

    $title_surname = $patient->title . " " . $patient->first_name;
    $other_names = '';
} else {
    $gp_name = $demographics_element->gp_name;
    $address = [$demographics_element->gp_address];
    $gp_postcode = $demographics_element->gp_postcode . " " . $demographics_element->gp_postcode_2nd;
    $gp_telephone = $demographics_element->gp_telephone;
    $title_surname = $demographics_element->title_surname;
    $other_names = $demographics_element->other_names;
    $email = $demographics_element->email;
    $postcode = $demographics_element->postcode . " " . $demographics_element->postcode_2nd;
    $telephone = $demographics_element->telephone;
    $patient_address = [$demographics_element->address];
}
?>

    <header class="print-header">
        <?= $logo_helper->render() ?>
    </header>

    <!-- Page title -->
    <div class="print-title text-c">
        <h1 class="highlighter">Certificate of Vision Impairment for people who are sight impaired (partially sighted)
            or
            severely sight impaired (blind)<br><small>updated September 2018</small></h1>
    </div>

    <hr class="divider"/>

    <!-- print main content, only 1, wraps all content -->
    <main class="print-main">
        <h2>Part 1: Certificate of Vision Impairment</h2>

        <h3>Patient details</h3>
        <table class="normal-text row-lines">
            <colgroup>
                <col class="cols-5">
                <col class="cols-7">
            </colgroup">
            <tbody>
            <tr>
                <th>Title and surname or family name:</th>
                <td><?= \CHtml::encode($title_surname) ?></td>
            </tr>
            <tr>
                <th>All other names (identify preferred name):</th>
                <td><?= \CHtml::encode($other_names) ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?= \CHtml::encode($patient_address ? implode(", ", $patient_address) : '') ?></td>
            </tr>
            <tr>
                <th>Postcode</th>
                <td><?= \CHtml::encode($postcode); ?></td>
            </tr>
            <tr>
                <th>Telephone number</th>
                <td><?= \CHtml::encode($telephone); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= \CHtml::encode($email); ?></td>
            </tr>
            <tr>
                <th>Date of birth</th>
                <td><?= Helper::convertMySQL2NHS($patient->dob) ?></td>
            </tr>
            <tr>
                <th>Hospital #</th>
                <td><?= $primary_identifier; ?></td>
            </tr>
            <tr>
                <th>NHS #</th>
                <td><?= $secondary_identifier; ?></td>
            </tr>
            <tr>
                <th>Sex</th>
                <td><?= $patient->genderString ?></td>
            </tr>
            </tbody>
        </table>
        <hr class="divider"/>
        <?php $clinical_info = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo'); ?>
        <div class="highlighter">To be completed by the Ophthalmologist</div>
        <h6>(Tick the box that applies)</h6><h4>I consider that this person is:</h4><span
                class="tickbox <?= !$clinical_info->is_considered_blind ? 'checked' : '' ?>"></span>
        <b>Sight impaired (partially sighted)</b><span
                class="tickbox <?= $clinical_info->is_considered_blind ? 'checked' : '' ?>"></span> <b>Severely sight
            impaired (blind)</b>
        <p>I have made the patient aware of the information booklet, “Sight Loss: What we needed to know”
            (www.rnib.org.uk/sightlossinfo)</p><span
                class="tickbox <?= $clinical_info->information_booklet ? 'checked' : '' ?>"></span> Yes<span
                class="tickbox <?= !$clinical_info->information_booklet ? 'checked' : '' ?>"></span> No
        <p>Has the patient seen an Eye Clinic Liaison Officer (ECLO)/Sight Loss Advisor?</p><span
                class="tickbox <?= $clinical_info->eclo === "1" ? 'checked' : '' ?>"></span> Yes<span
                class="tickbox <?= $clinical_info->eclo === "2" ? 'checked' : '' ?>"></span> Referred<span
                class="tickbox <?= ($clinical_info->eclo === "0" || !$clinical_info->eclo) ? 'checked' : '' ?>"></span>
        Not
        applicable
        <div class="box">
            <div class="flex">
                <div class="dotted-area">
                    <div class="label">Signed</div>
                    <img src="<?= $getSignatureSource(BaseSignature::TYPE_LOGGEDIN_USER); ?>" class="signature">
                </div>
                <div class="dotted-area">
                    <div class="label">Date</div>
                    <?= Helper::convertMySQL2NHS($clinicalinfo_element->examination_date); ?>
                </div>
            </div>
            <div class="flex">
                <div class="dotted-area">
                    <div class="label">Print name</div>
                    <?php $patient_signature = $eventinfo_element->getSignatureByType(BaseSignature::TYPE_LOGGEDIN_USER); ?>
                    <?= $patient_signature->signatory_name ?? ''; ?>
                </div>
            </div>
        </div>
        <div class="dotted-area">
            <div class="label">Hospital address</div>
            <?= $eventinfo_element->site->contact->getLetterAddress(['delimiter' => ', ']); ?>
        </div>
        <h6>NB: the date of examination is taken as the date from which any concessions are calculated</h6>
        <hr class="divider"/>
        <div class="box"><h4>For Hospital staff: Provide/send copies of this CVI as stated below</h4>
            <ul class="layout">
                <li><span class="tickbox"></span> An accessible signed copy of the CVI form to the patient (or
                    parent/guardian if the patient is a child).
                </li>
                <li><span class="tickbox<?= $consent_element->consented_to_la ? ' checked' : ''; ?>"></span> Parts 1-4
                    to the patient’s local council if the patient (or
                    parent/guardian if the patient is a child)
                    consents, <b>within 5 working days</b></li>
                <li><span class="tickbox<?= $consent_element->consented_to_gp ? ' checked' : ''; ?>"></span> Parts 1-4
                    to the patient’s GP, if the patient (or parent/guardian if the
                    patient is a child) consents.
                </li>
                <li><span class="tickbox<?= $consent_element->consented_to_rcop ? ' checked' : ''; ?>"></span> Parts 1-5
                    to The Royal College of Ophthalmologists, c/o Certifications
                    Office, Moorfields Eye Hospital, 162 City Road, London, EC1V 2PD, or by nhs.net secure email to
                    meh-tr.CVI@nhs.net if the patient (or parent/guardian if the patient is a child) consents.
                </li>
            </ul>
        </div>
        <hr class="divider"/>
        <h2>Part 2: To be completed by the Ophthalmologist</h2>
        <div class="highlighter">Visual function</div>
        <h4>Best corrected visual acuity</h4>
        <table class="borders">
            <thead>
            <tr>
                <th>Right eye</th>
                <th>Left eye</th>
                <th>Binocular (Habitual)</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?= $clinical_info->getDisplayBestCorrectedVA('right') ?></td>
                <td><?= $clinical_info->getDisplayBestCorrectedVA('left') ?></td>
                <td><?= $clinical_info->getDisplayBestCorrectedVA('binocular') ?></td>
            </tr>
            </tbody>
        </table>
        <p><b>Field of vision:</b> Extensive loss of peripheral visual field (including hemianopia)</p>
        <span class="tickbox <?= $clinical_info->field_of_vision === "1" ? 'checked' : '' ?>"></span> Yes
        <span class="tickbox <?= $clinical_info->field_of_vision === "2" ? 'checked' : '' ?>"></span> No
        <div class="spacer"><!-- **** empty vertical spacer ***** --></div>
        <p><b>Low vision service:</b> If appropriate, has a referral for the low vision service been made?</p>
        <span class="tickbox <?= $clinical_info->low_vision_service === "1" ? 'checked' : '' ?>"></span> Yes
        <span class="tickbox <?= $clinical_info->low_vision_service === "2" ? 'checked' : '' ?>"></span> No
        <span class="tickbox <?= $clinical_info->low_vision_service === "3" ? 'checked' : '' ?>"></span> Don't know
        <span class="tickbox <?= $clinical_info->low_vision_service === "4" ? 'checked' : '' ?>"></span> Not required
        <hr class="divider"/>
        <h2>Part 2a: Diagnosis (for patients <?= ($clinical_info->patient_type == 0) ? "18 years of age or over" : "under the age of 18" ?>)</h2><h4>Tick each that applies. <b>Tick "Main" if
                this
                is the main cause for the impairment.</b></h4><h6>Please note that this is not intended to be a
            comprehensive list of all possible diagnoses.</h6>
        <!-- headers for all tables - must align correctly (uses same colgroup) -->
        <div class="flex"><h3 class="cols-5"><!----></h3>
            <table>
                <colgroup>
                    <col class="cols-6">
                    <col class="cols-1">
                    <col class="cols-3">
                    <col class="cols-1">
                    <col class="cols-1">
                </colgroup">
                <thead>
                <tr>
                    <td><!----></td>
                    <th>Main</th>
                    <th>ICD 10 code</th>
                    <th>Right</th>
                    <th>Left</th>
                </tr>
                </thead>
            </table>
        </div>
        <?php foreach ($this->getDisorderSections($clinical_info->patient_type) as $disorder_section) : ?>
            <div class="flex">
                <h3 class="cols-3"><?= \CHtml::encode($disorder_section->name); ?></h3>
                <div class="cols-2"></div>
                <table class="row-lines">
                    <colgroup>
                        <col class="cols-6">
                        <col class="cols-1">
                        <col class="cols-3">
                        <col class="cols-1">
                        <col class="cols-1">
                    </colgroup">
                    <tbody>

                    <?php foreach ($disorder_section->disorders as $disorder) : ?>
                        <tr>
                            <td><?= \CHtml::encode($disorder->name); ?></td>
                            <td>
                                <span class="checkbox <?= $clinical_info->isCviDisorderMainCauseForSide($disorder, 'right') ? 'checked' : '' ?>"></span>
                            </td>
                            <td><?= \CHtml::encode($disorder->code) ?></td>
                            <td>
                                <span class="tickbox <?= in_array($clinical_info->getCviDisorderSide($disorder), [\Eye::RIGHT, \Eye::BOTH]) ? 'checked' : ''; ?>"></span>
                            </td>
                            <td>
                                <span class="tickbox <?= in_array($clinical_info->getCviDisorderSide($disorder), [\Eye::LEFT, \Eye::BOTH]) ? 'checked' : ''; ?>"></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
        <h4>Diagnosis not covered in any of the above, specify, including ICD 10 code if known and indicating eye or
            eyes</h4>
        <?php if (!count($clinical_info->diagnosis_not_covered)) : ?>
            <div class="box">
                <div class="dotted-write"></div>
            </div>
        <?php endif; ?>
        <?php foreach ($clinical_info->diagnosis_not_covered as $diagnosis) {
            if (isset($diagnosis->disorder) || isset($diagnosis->clinicinfo_disorder)) {
                switch ($diagnosis->eye_id) {
                    case 1:
                        $eye = 'Left';
                        break;
                    case 2:
                        $eye = 'Right';
                        break;
                    case 3:
                        $eye = 'Bilateral';
                        break;
                }
                if ($diagnosis->disorder_type == OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered::TYPE_CLINICINFO_DISORDER) {
                    $disorder_name = $diagnosis->clinicinfo_disorder->term_to_display;
                    $disorder_code = $diagnosis->clinicinfo_disorder->code;
                } else {
                    $disorder_name = $diagnosis->disorder->term;
                    $disorder_code = $diagnosis->code;
                }
                ?>

                <div class="box">
                    <div class="dotted-write">
                        <?= \CHtml::encode($eye) ?>
                        <?= \CHtml::encode($disorder_name) ?>
                        <?= $diagnosis->main_cause == 1 ? '(main cause)' : '' ?> -
                        <?= \CHtml::encode($disorder_code) ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        <hr class="divider"/>

        <h2>Part 3: To be completed by the patient (or parent/guardian if the patient is a child) and eye clinic staff
            e.g.
            ECLO/Sight Loss Advisor</h2>
        <div class="highlighter">Additional information for the patient’s local council</div>
        <?php foreach (array_chunk($this->getPatientFactors(), 4) as $chunk) : ?>
            <table class="row-lines">
                <colgroup>
                    <col class="cols-6">
                    <col class="cols-6">
                </colgroup">
                <tbody>
                <?php foreach ($chunk as $factor) : ?>
                    <?php
                    $answer = $clearical_info->getPatientFactorAnswer($factor);
                    $value = $answer ? $answer->is_factor : null;
                    ?>
                    <tr>
                        <td><?= \CHtml::encode($factor->name); ?></td>
                        <td>
                            <?php if (!$factor->comments_only) : ?>
                                <span class="tickbox <?= $value === '1' ? 'checked' : ''; ?> "></span> Yes <span
                                        class="tickbox <?= $value === '0' ? 'checked' : ''; ?>"></span> No

                                <?php if (!$factor->yes_no_only) : ?>
                                    <span class="tickbox <?= $value === '2' ? 'checked' : ''; ?> "></span> Don't know
                                <?php endif; ?>

                                <?= ($answer && $answer->comments) ? " | comments: " : ''; ?>
                            <?php endif; ?>

                            <?= ($answer && $answer->comments) ? \CHtml::encode($answer->comments) : ''; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <div class="spacer"><!-- **** empty vertical spacer ***** --></div>

        <div class="highlighter">Patient’s information and communication needs</div>
        <p>All providers of NHS and local authority social care services are legally required to identify, record and
            meet
            your individual information/communication needs (refer to Explanatory Notes paragraphs 9, 22 and 23).</p>
        <p>Preferred method of contact?</p>

        <?php foreach (OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt::model()->findAll(array("condition" => "version =  1", 'order' => 'display_order asc')) as $fmt) : ?>
            <span class="tickbox <?= $clearical_info->preferred_info_fmt_id == $fmt->id ? 'checked' : ''; ?>"></span> <?= $fmt->name; ?>
        <?php endforeach; ?>

        <div class="spacer"><!-- **** empty vertical spacer ***** --></div>
        <p>Preferred method of communication e.g. BSL, deafblind manual?</p>
        <div class="box">
            <div class="dotted-write"><?= \CHtml::encode($clearical_info->preferred_comm); ?></div>
        </div>

        <?php $preferred_format_ids = array_map(fn($e) => $e->preferred_format_id, $clearical_info->preferred_format_assignments); ?>

        <?php foreach (OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredFormat::model()->findAll(array("condition" => "version =  1", 'order' => 'display_order asc')) as $format) : ?>
            <span class="tickbox <?= in_array($format->id, $preferred_format_ids) ? 'checked' : ''; ?>"></span> <?= $format->name; ?>
        <?php endforeach; ?>

        <hr class="divider"/>
        <h2>Part 4: Consent to share information</h2>
        <div class="highlighter">I understand that by signing this form</div>
        <p>I give my permission for a copy to be sent to my GP to make them aware of this certificate.</p>

        <div class="box">
            <div class="dotted-area">
                <div class="label">My <b>GP</b> name/practice</div>
                <?= \CHtml::encode($gp_name); ?>
            </div>
            <div class="dotted-area">
                <div class="label">GP address</div>
                <?= \CHtml::encode($address ? implode(", ", $address) : '') ?>
            </div>
            <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
            <div class="flex">
                <div class="dotted-area">
                    <div class="label">Postcode</div>
                    <?= \CHtml::encode($gp_postcode); ?>
                </div>
                <div class="dotted-area">
                    <div class="label">Telephone number</div>
                    <?= \CHtml::encode($gp_telephone) ?>
                </div>
            </div>
        </div>
        <p>I give my permission for a copy to be sent to my local council (or an organisation working on their behalf)
            who
            have a duty (under the Care Act 2014) to contact me to offer advice on living with sight loss and explain
            the
            benefits of being registered. When the council contacts me, I am aware that I do not have to accept any
            help, or
            be registered at that time, if I choose not to do so.</p>


        <div class="box">
            <div class="dotted-area">
                <div class="label">My <b>local council</b> name</div>
                <?= \CHtml::encode($demographics_element->la_name ?? ''); ?>
            </div>
            <div class="dotted-area">
                <div class="label">Address</div>
                <?= \CHtml::encode($demographics_element->la_address ?? ''); ?>
            </div>
            <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
            <div class="flex">
                <div class="dotted-area">
                    <div class="label">Postcode</div>
                    <?= \CHtml::encode($demographics_element->la_postcode ?? ''); ?><?= \CHtml::encode($demographics_element->la_postcode_2nd ?? ''); ?>
                </div>
                <div class="dotted-area">
                    <div class="label">Telephone number</div>
                    <?= \CHtml::encode($demographics_element->la_telephone ?? ''); ?>
                </div>
            </div>
        </div>
        <p>I give my permission for a copy to be sent to The Royal College of Ophthalmologists, Certifications Office at
            Moorfields Eye Hospital; where information about eye conditions is collected, and used to help to improve
            eye
            care and services in the future.</p>
        <p>I understand that I do not have to consent to sharing my information with my GP, local council or The Royal
            College of Ophthalmologists Certifications Office, or that I can withdraw my consent at any point by
            contacting
            them directly.</p>
        <p>I confirm that my attention has been drawn to the paragraph entitled ‘Driving’ and understand that I must not
            drive.</p><h4>Signed by the patient (or signature and name of parent/guardian or representative)</h4>
        <div class="box">
            <div class="flex">
                <div class="dotted-area">
                    <div class="label">Signed</div>
                    <img src="<?= $getSignatureSource(BaseSignature::TYPE_PATIENT); ?>" class="signature">
                </div>
            </div>
            <div class="flex">
                <div class="dotted-area">
                    <div class="label">Printed name</div>
                    <?= $patient->fullName; ?>
                </div>
            </div>
        </div>
        <div class="break"><!-- **** page break ***** --></div>
        <hr class="divider"/>
        <h2>Part 5: Ethnicity</h2>
        <div class="highlighter">This information is needed for service and epidemiological monitoring</div>

        <?php $i=0; foreach (EthnicGroup::model()->findAllAndGroup() as $group_name => $group) : ?>
            <div class="group">
                <h4><?=$group_name;?></h4>
                <ul class="layout">
                    <?php foreach ($group as $k => $ethnic) : ?>
                        <li>
                            <span class="tickbox <?= $demographics_element->ethnic_group_id == $ethnic->id ? 'checked' : ''; ?>"></span>
                            <?= ++$i . ". " . $ethnic->cviName; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="dotted-write">
                    <?php if ($demographics_element->ethnic_group_id == $ethnic->id && $demographics_element->describe_ethnics) : ?>
                        <?= \CHtml::encode($demographics_element->describe_ethnics); ?>
                    <?php endif;?>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="break"><!-- **** page break ***** --></div>
        <hr class="divider"/>
        <h2>Information Sheet for patients (or parents/guardians if the patient is a child)</h2>
        <div class="highlighter">Certification</div>
        <div class="group"><h4>Keep your Certificate of Vision Impairment (CVI). It has three main functions:</h4>
            <p>1. It qualifies you to be registered with your local council as sight impaired (partially sighted) or
                severely sight impaired (blind).<br>
                2. It lets your local council know about your sight loss. They should contact you within two weeks to
                offer
                registration, and to identify any help you might need with day-to-day tasks.<br>
                3. The CVI records important information about the causes of sight loss. It helps in planning NHS eye
                care
                services and research about eye conditions.</p></div>
        <div class="highlighter">Registration and vision rehabilitiation/habilitation</div>
        <div class="group"><p>Councils have a duty to keep a register of people with sight loss. They will contact you
                to
                talk about the benefits of being registered. This is likely to be through the Social Services Local
                Sensory
                Team (or an organisation working on their behalf). Registration is often a positive step to help you to
                be
                as independent as possible. You can choose whether or not to be registered. Once registered, your local
                council should offer you a card confirming registration. If you are registered, you may find it easier
                to
                prove the degree of your sight loss and your eligibility for certain concessions. The Council should
                also
                talk to you about vision rehabilitation if you are an adult, and habilitation if you are a child or
                young
                person and any other support that might help. Vision rehabilitation/habilitation is support or training
                to
                help you to maximise your independence, such as moving around your home and getting out and about
                safely.</p></div>
        <div class="highlighter">Early Years Development, Children and Young People and Education</div>
        <div class="group"><p>Children (including babies) and young people who are vision impaired will require
                specialist
                support for their development and may receive special educational needs provision. An education, health
                and
                care (EHC) plan may be provided. You do not need to be certified or registered to receive this support
                or an
                EHC plan. This support is provided by the council’s specialist education vision impairment service.
                Additional support from a social care assessment may also be offered as a result of registration.
                Information about the support your council offers to children and young people can be found on the
                ‘Local
                Offer’ page of their website. If you or your child are not known to this service talk to the
                Ophthalmologist
                or ECLO/Sight Loss Advisor.</p></div>
        <div class="highlighter">Driving</div>
        <div class="group"><p>As a person certified as sight impaired or severely sight impaired <b>you must not
                    drive</b>
                and you must inform the DVLA at the earliest opportunity. For more information, please contact: Drivers
                Medical Branch, DVLA, Swansea, SA99 1TU. Telephone 0300 790 6806. Email eftd@dvla.gsi.gov.uk</p></div>
        <div class="highlighter">Where to get further information, advice and support</div>
        <div class="group"><p>“Sight Loss: What we needed to know”, written by people with sight loss, contains lots of
                useful information including a list of other charities who may be able to help you. Visit
                www.rnib.org.uk/sightlossinfo</p>
            <p>‘Sightline’ is an online directory of people, services and organisations that help people with sight loss
                in
                your area. Visit www.sightlinedirectory.org.uk</p>
            <p>‘Starting Point’ signposts families to resources and professionals that can help with the first steps
                following your child’s diagnosis. Visit www.vision2020uk.org.uk/startingpoint</p>
            <p>Your local sight loss charity has lots of information, advice and practical solutions that can help you.
                Visit www.visionary.org.uk</p>
            <p>RNIB offers practical and emotional support for everyone affected by sight loss. Call the Helpline on
                0303
                123 9999 or visit www.rnib.org.uk</p>
            <p>Guide Dogs provides a range of support services to people of all ages. Call 0800 953 0113 (adults) or
                0800
                781 1444 (parents/guardians of children/young people) or visit www.guidedogs. org.uk</p>
            <p>Blind Veterans UK provides services and support to vision impaired veterans. Call 0800 389 7979 or visit
                www.noonealone.org.uk</p>
            <p>SeeAbility is a charity that acts to make eye care more accessible for people with learning disabilities
                and
                autism. Their easy read information can be found at www.seeability.org/looking- after-your-eyes or you
                can
                call 01372 755000.</p></div>
    </main>

<?php
if (!isset($with_esign_element) || $with_esign_element) {
    echo $this->renderTiledElements([$esign_element], 'print');
}
