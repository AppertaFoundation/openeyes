<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

array_walk($elements, function ($e) {
    echo '<pre>' . print_r(get_class($e), true) . '</pre>';
});

?>

<!-- Page title -->
<div class="print-title text-c">
    <h1 class="highlighter">Certificate of Vision Impairment for people who are sight impaired (partially sighted) or
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
            <td><?= $patient->title ?> <?= $patient->first_name ?></td>
        </tr>
        <tr>
            <th>All other names (identify preferred name):</th>
            <td><?= $patient->first_name ?></td>
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
            <th>Gender</th>
            <td><?= $patient->genderString ?></td>
        </tr>
        </tbody>
    </table>
    <hr class="divider"/>
    <?php

    $clinical_info = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo_V1');
    echo '<pre>' . print_r($clinical_info->attributes, true) . '</pre>';
    ?>
    <div class="highlighter">To be completed by the Ophthalmologist</div>
    <h6>(Tick the box that applies)</h6><h4>I consider that this person is:</h4><span
            class="tickbox <?= !$clinical_info->is_considered_blind ? 'checked' : '' ?>"></span>
    <b>Slight impaired (partially sighted)</b><span
            class="tickbox <?= $clinical_info->is_considered_blind ? 'checked' : '' ?>"></span> <b>Severely sight
        impaired (blind)</b>
    <p>I have made the patient aware of the information booklet, “Sight Loss: What we needed to know”
        (www.rnib.org.uk/sightlossinfo)</p><span
            class="tickbox <?= $clinical_info->information_booklet ? 'checked' : '' ?>"></span> Yes<span
            class="tickbox <?= !$clinical_info->information_booklet ? 'checked' : '' ?>"></span> No
    <p>Has the patient seen an Eye Clinic Liaison Officer (ECLO)/Sight Loss Advisor?</p><span
            class="tickbox <?= $clinical_info->eclo === "1" ? 'checked' : '' ?>"></span> Yes<span
            class="tickbox <?= $clinical_info->eclo === "2" ? 'checked' : '' ?>"></span> Referred<span
            class="tickbox <?= ($clinical_info->eclo === "0" || !$clinical_info->eclo) ? 'checked' : '' ?>"></span> Not
    applicable
    <div class="box">
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Signed</div>
            </div>
            <div class="dotted-area">
                <div class="label">Date</div>
            </div>
        </div>
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Print name</div>
            </div>
        </div>
    </div>
    <div class="dotted-area">
        <div class="label">Hospital address</div>
    </div>
    <h6>NB: the date of examination is taken as the date from which any concessions are calculated</h6>
    <hr class="divider"/>
    <div class="box"><h4>For Hospital staff: Provide/send copies of this CVI as stated below</h4>
        <ul class="layout">
            <li><span class="tickbox"></span> An accessible signed copy of the CVI form to the patient (or
                parent/guardian if the patient is a child).
            </li>
            <li><span class="tickbox"></span> Parts 1-4 to the patient’s local council if the patient (or
                parent/guardian if the patient is a child)
                consents, <b>within 5 working days</b></li>
            <li><span class="tickbox"></span> Parts 1-4 to the patient’s GP, if the patient (or parent/guardian if the
                patient is a child) consents.
            </li>
            <li><span class="tickbox"></span> Parts 1-5 to The Royal College of Ophthalmologists, c/o Certifications
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
            <td><?= $clinical_info->getDisplayBestCorrectedRightVA() ?></td>
            <td><?= $clinical_info->getDisplayBestCorrectedLeftVA() ?></td>
            <td><?= $clinical_info->getDisplayBestCorrectedBinocularVA() ?></td>
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
    <h2>Part 2a: Diagnosis (for patients 18 years of age or over)</h2><h4>Tick each that applies. <b>Tick "Main" if this
            is the main cause for the impairment.</b></h4><h6>Please note that this is not intended to be a
        comprehensive list of all possible diagnoses.</h6>
    <!-- headers for all tables - must align correctly (uses same colgroup) -->
    <div class="flex"><h3 class="cols-3"><!----></h3>
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
    <?php foreach ($this->getDisorderSections_V1($clinical_info->patient_type) as $disorder_section) :?>
    <div class="flex"><h3 class="cols-3"><?=\CHtml::encode($disorder_section->name); ?></h3>
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
                    <td><span class="checkbox <?=$clinical_info->isCviDisorderMainCauseForSide($disorder, 'right') ? 'checked': ''?>"></span></td>
                    <td>H35.32</td>
                    <td><span class="tickbox"></span></td>
                    <td><span class="tickbox"></span></td>
                </tr>
            <?php endforeach ;?>

            <tr>
                <td>age-related macular degeneration – choroidal neovascularisation (wet)</td>
                <td><span class="checkbox" checked></span></td>
                <td>H35.32</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>age-related macular degeneration – atrophic/ geographic macular atrophy (dry)</td>
                <td><span class="checkbox"></span></td>
                <td>H35.31</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>age-related macular degeneration unspecified (mixed)</td>
                <td><span class="checkbox"></span></td>
                <td>H35.30</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>diabetic retinopathy</td>
                <td><span class="checkbox"></span></td>
                <td>E10.3-E14.3 H36.0</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>diabetic maculopathy</td>
                <td><span class="checkbox"></span></td>
                <td>H36.0A</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>hereditary retinal dystrophy</td>
                <td><span class="checkbox"></span></td>
                <td>H35.5</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>retinal vascular occlusions</td>
                <td><span class="checkbox"></span></td>
                <td>H34</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>other retinal (specify)</td>
                <td><span class="checkbox"></span></td>
                <td>H35</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>
    <div style="display:none" class="flex"><h3 class="cols-3">Glaucoma</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>primary open angle</td>
                <td><span class="checkbox"></span></td>
                <td>H40.1</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>primary angle closure</td>
                <td><span class="checkbox"></span></td>
                <td>H40.2</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>secondary</td>
                <td><span class="checkbox"></span></td>
                <td>H40.5</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>other glaucoma (specify)</td>
                <td><span class="checkbox"></span></td>
                <td>H40</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="display:none" class="flex"><h3 class="cols-3">Globe</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>degenerative myopia</td>
                <td><span class="checkbox"></span></td>
                <td>H44.2</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="display:none" class="flex"><h3 class="cols-3">Neurological</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>optic atrophy</td>
                <td><span class="checkbox"></span></td>
                <td>H47.2</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>visual cortex disorder</td>
                <td><span class="checkbox"></span></td>
                <td>H47.6</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>cerebrovascular disease</td>
                <td><span class="checkbox"></span></td>
                <td>I60-I69</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="display:none" class="flex"><h3 class="cols-3">Choroid</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>chorioretinitis</td>
                <td><span class="checkbox"></span></td>
                <td>H30.9</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>choroidal degeneration</td>
                <td><span class="checkbox"></span></td>
                <td>H31.1</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="display:none" class="flex"><h3 class="cols-3">Lens</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>cataract (excludes congenital)</td>
                <td><span class="checkbox"></span></td>
                <td>H25.9</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="display:none" class="flex"><h3 class="cols-3">Cornea</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>corneal scars and opacities</td>
                <td><span class="checkbox"></span></td>
                <td>H17</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>keratitis</td>
                <td><span class="checkbox"></span></td>
                <td>H16</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="display:none" class="flex"><h3 class="cols-3">Neoplasia</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>eye</td>
                <td><span class="checkbox"></span></td>
                <td>C69</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>brain & CNS</td>
                <td><span class="checkbox"></span></td>
                <td>C70-C72, D43-D44</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>other neoplasia (specify)</td>
                <td><span class="checkbox"></span></td>
                <td>C00-C68, C73-C97, D00-D42, D45-D48</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <h4>Diagnosis not covered in any of the above, specify, including ICD 10 code if known and indicating eye or
        eyes</h4>
    <div class="box">
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <hr class="divider"/>
    <h2>Part 2b: Diagnosis (for patients under the age of 18)</h2><h4>Tick each that applies. <b>Tick "Main" if this is
            the main cause for the impairment.</b></h4>
    <div class="flex"><h3 class="cols-3"><!----></h3>
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
    <div class="flex"><h3 class="cols-3">Central Visual Pathway Problems</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>cerebral/cortical pathology affecting mainly a) acuity b) fields c) visual perception (circle)</td>
                <td><span class="checkbox"></span></td>
                <td>H47.6</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>nystagmus</td>
                <td><span class="checkbox"></span></td>
                <td>H55</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>other (specify)</td>
                <td><span class="checkbox"></span></td>
                <td>H47.7</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="flex"><h3 class="cols-3">Whole Globe and Anterior Segment</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>anophthalmos/microphthalmos</td>
                <td><span class="checkbox"></span></td>
                <td>Q11</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>disorganised globe/phthisis</td>
                <td><span class="checkbox"></span></td>
                <td>H44</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>anterior segment anomaly</td>
                <td><span class="checkbox"></span></td>
                <td>Q13</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>primary congenital/infantile glaucoma</td>
                <td><span class="checkbox"></span></td>
                <td>Q15, H40.1-H40.2</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>other glaucoma</td>
                <td><span class="checkbox"></span></td>
                <td>H40.8-H40.9</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="flex"><h3 class="cols-3">Amblyopia</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>stimulus deprivation</td>
                <td><span class="checkbox"></span></td>
                <td>H53.0</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>strabismic</td>
                <td><span class="checkbox"></span></td>
                <td>H53.0</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>refractive</td>
                <td><span class="checkbox"></span></td>
                <td>H53.0</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="flex"><h3 class="cols-3">Cornea</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>opacity</td>
                <td><span class="checkbox"></span></td>
                <td>H17</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>dystrophy</td>
                <td><span class="checkbox"></span></td>
                <td>H18.4</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>other (specify)</td>
                <td><span class="checkbox"></span></td>
                <td>H18.8-H18.9</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="flex"><h3 class="cols-3">Cataract</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>congenital</td>
                <td><span class="checkbox"></span></td>
                <td>Q12.0</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>developmental</td>
                <td><span class="checkbox"></span></td>
                <td>H26.9</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>secondary</td>
                <td><span class="checkbox"></span></td>
                <td>H26.4</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="flex"><h3 class="cols-3">Uvea</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>aniridia</td>
                <td><span class="checkbox"></span></td>
                <td>Q13.1</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>coloboma</td>
                <td><span class="checkbox"></span></td>
                <td>Q12.2, Q13.0</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>uveitis</td>
                <td><span class="checkbox"></span></td>
                <td>H20</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>other (specify)</td>
                <td><span class="checkbox"></span></td>
                <td>H21</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="flex"><h3 class="cols-3">Retina</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>retinopathy of prematurity</td>
                <td><span class="checkbox"></span></td>
                <td>H35.1</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>retinal dystrophy</td>
                <td><span class="checkbox"></span></td>
                <td>H35.5</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>retinitis</td>
                <td><span class="checkbox"></span></td>
                <td>H30</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>other retinopathy</td>
                <td><span class="checkbox"></span></td>
                <td>H35.2</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>retinoblastoma</td>
                <td><span class="checkbox"></span></td>
                <td>C69.2</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>albinism</td>
                <td><span class="checkbox"></span></td>
                <td>E70.3</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>retinal detachment</td>
                <td><span class="checkbox"></span></td>
                <td>H33</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>other (specify)</td>
                <td><span class="checkbox"></span></td>
                <td>H35</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="flex"><h3 class="cols-3">Optic Nerve</h3>
        <table class="row-lines">
            <colgroup>
                <col class="cols-6">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup">
            <tbody>
            <tr>
                <td>hypoplasia</td>
                <td><span class="checkbox"></span></td>
                <td>Q11.2</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>other congenital anomaly</td>
                <td><span class="checkbox"></span></td>
                <td>Q14.2</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>optic atrophy</td>
                <td><span class="checkbox"></span></td>
                <td>H47.2</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>neuropathy</td>
                <td><span class="checkbox"></span></td>
                <td>H47.0</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            <tr>
                <td>other (specify)</td>
                <td><span class="checkbox"></span></td>
                <td>H47.0</td>
                <td><span class="tickbox"></span></td>
                <td><span class="tickbox"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <h4>Diagnosis not covered in any of the above, specify, including ICD 10 code if known and indicating eye or
        eyes</h4>
    <div class="box">
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <hr class="divider"/>
    <h2>Part 3: To be completed by the patient (or parent/guardian if the patient is a child) and eye clinic staff e.g.
        ECLO/Sight Loss Advisor</h2>
    <div class="highlighter">Additional information for the patient’s local council</div>
    <table class="row-lines">
        <colgroup>
            <col class="cols-6">
            <col class="cols-6">
        </colgroup">
        <tbody>
        <tr>
            <td>If you are an adult do you live alone?</td>
            <td><span class="tickbox"></span> Yes <span class="tickbox"></span> No</td>
        </tr>
        <tr>
            <td>Does someone support you with your care?</td>
            <td><span class="tickbox"></span> Yes <span class="tickbox"></span> No</td>
        </tr>
        <tr>
            <td>Do you have difficulties with your physical mobility?</td>
            <td><span class="tickbox"></span> Yes <span class="tickbox"></span> No</td>
        </tr>
        <tr>
            <td>Do you have difficulties with your hearing?</td>
            <td><span class="tickbox"></span> Yes <span class="tickbox"></span> No</td>
        </tr>
        <tr>
            <td>Do you have a learning disability?</td>
            <td><span class="tickbox"></span> Yes <span class="tickbox"></span> No</td>
        </tr>
        <tr>
            <td>Do you have a diagnosis of dementia?</td>
            <td><span class="tickbox"></span> Yes <span class="tickbox"></span> No</td>
        </tr>
        <tr>
            <td>Are you employed?</td>
            <td><span class="tickbox"></span> Yes <span class="tickbox"></span> No</td>
        </tr>
        <tr>
            <td>Are you in full-time education?</td>
            <td><span class="tickbox"></span> Yes <span class="tickbox"></span> No</td>
        </tr>
        </tbody>
    </table>
    <p>If the patient is a baby, child or young person, is your child/are you known to the specialist visual impairment
        education service?</p><span class="tickbox"></span> Yes<span class="tickbox"></span> No<span
            class="tickbox"></span> Don't know
    <div class="spacer"><!-- **** empty vertical spacer ***** --></div>
    <p>Record any further relevant information below e.g. medical conditions, emotional impact of sight loss, risk of
        falls, benefits of vision rehabilitation and/or if you think the patient requires urgent support and reasons
        why.</p>
    <div class="box">
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <div class="highlighter">Patient’s information and communication needs</div>
    <p>All providers of NHS and local authority social care services are legally required to identify, record and meet
        your individual information/communication needs (refer to Explanatory Notes paragraphs 9, 22 and 23).</p>
    <p>Preferred method of contact?</p><span class="tickbox"></span> Phone call<span class="tickbox"></span> Email<span
            class="tickbox"></span> Letter
    <div class="spacer"><!-- **** empty vertical spacer ***** --></div>
    <p>Preferred method of communication e.g. BSL, deafblind manual?</p>
    <div class="box">
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <div class="spacer"><!-- **** empty vertical spacer ***** --></div>
    <span class="tickbox"></span> Large print 18<span class="tickbox"></span> Large print 22<span
            class="tickbox"></span> Large print 26<span class="tickbox"></span> Easy-Read<span class="tickbox"></span>
    Audio CD<span class="tickbox"></span> Email<span class="tickbox"></span> Other (specify)
    <div class="box">
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <span class="tickbox"></span> I don’t know and need an assessment
    <div class="spacer"><!-- **** empty vertical spacer ***** --></div>
    <p>Preferred language (and identify if an interpreter is required).</p>
    <div class="box">
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <hr class="divider"/>
    <h2>Part 4: Consent to share information</h2>
    <div class="highlighter">I understand that by signing this form</div>
    <p>I give my permission for a copy to be sent to my GP to make them aware of this certificate.</p>
    <div class="box">
        <div class="dotted-area">
            <div class="label">My <b>GP</b> name/practice</div>
        </div>
        <div class="dotted-area">
            <div class="label">GP address</div>
        </div>
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Postcode</div>
            </div>
            <div class="dotted-area">
                <div class="label">Telephone number</div>
            </div>
        </div>
    </div>
    <p>I give my permission for a copy to be sent to my local council (or an organisation working on their behalf) who
        have a duty (under the Care Act 2014) to contact me to offer advice on living with sight loss and explain the
        benefits of being registered. When the council contacts me, I am aware that I do not have to accept any help, or
        be registered at that time, if I choose not to do so.</p>
    <div class="box">
        <div class="dotted-area">
            <div class="label">My <b>local council</b> name</div>
        </div>
        <div class="dotted-area">
            <div class="label">Address</div>
        </div>
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Postcode</div>
            </div>
            <div class="dotted-area">
                <div class="label">Telephone number</div>
            </div>
        </div>
    </div>
    <p>I give my permission for a copy to be sent to The Royal College of Ophthalmologists, Certifications Office at
        Moorfields Eye Hospital; where information about eye conditions is collected, and used to help to improve eye
        care and services in the future.</p>
    <p>I understand that I do not have to consent to sharing my information with my GP, local council or The Royal
        College of Ophthalmologists Certifications Office, or that I can withdraw my consent at any point by contacting
        them directly.</p>
    <p>I confirm that my attention has been drawn to the paragraph entitled ‘Driving’ and understand that I must not
        drive.</p><h4>Signed by the patient (or signature and name of parent/guardian or representative)</h4>
    <div class="box">
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Signed</div>
            </div>
        </div>
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Printed name</div>
            </div>
        </div>
    </div>
    <div class="break"><!-- **** page break ***** --></div>
    <hr class="divider"/>
    <h2>Part 5: Ethnicity</h2>
    <div class="highlighter">This information is needed for service and epidemiological monitoring</div>
    <div class="group"><h4>White</h4>
        <ul class="layout">
            <li><span class="tickbox"></span> 1. English/Northern Irish/Scottish/Welsh/British</li>
            <li><span class="tickbox"></span> 2. Irish</li>
            <li><span class="tickbox"></span> 3. Any other White background, describe below</li>
        </ul>
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <div class="group"><h4>Mixed/Multiple ethnic groups</h4>
        <ul class="layout">
            <li><span class="tickbox"></span> 4. White and Black Caribbean</li>
            <li><span class="tickbox"></span> 5. White and Black African</li>
            <li><span class="tickbox"></span> 6. White and Asian</li>
            <li><span class="tickbox"></span> 7. Any other Mixed/Multiple ethnic background, describe below</li>
        </ul>
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <div class="group"><h4>Asian/Asian British</h4>
        <ul class="layout">
            <li><span class="tickbox"></span> 8. White and Black Caribbean</li>
            <li><span class="tickbox"></span> 9. White and Black African</li>
            <li><span class="tickbox"></span> 10. White and Asian</li>
            <li><span class="tickbox"></span> 11. Any other Mixed/Multiple ethnic background, describe below</li>
        </ul>
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <div class="group"><h4>Black/African/Caribbean/Black British</h4>
        <ul class="layout">
            <li><span class="tickbox"></span> 12. African</li>
            <li><span class="tickbox"></span> 13. Caribbean</li>
            <li><span class="tickbox"></span> 14. Any other Black/African/Caribbean background, describe below</li>
        </ul>
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <div class="group"><h4>Chinese/Chinese British</h4>
        <ul class="layout">
            <li><span class="tickbox"></span> 15. Chinese</li>
            <li><span class="tickbox"></span> 16. Any other Chinese background, describe below</li>
        </ul>
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <div class="group"><h4>Other ethnic group</h4>
        <ul class="layout">
            <li><span class="tickbox"></span> 17. Other, describe below</li>
        </ul>
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <div class="break"><!-- **** page break ***** --></div>
    <hr class="divider"/>
    <h2>Information Sheet for patients (or parents/guardians if the patient is a child)</h2>
    <div class="highlighter">Certification</div>
    <div class="group"><h4>Keep your Certificate of Vision Impairment (CVI). It has three main functions:</h4>
        <p>1. It qualifies you to be registered with your local council as sight impaired (partially sighted) or
            severely sight impaired (blind).<br>
            2. It lets your local council know about your sight loss. They should contact you within two weeks to offer
            registration, and to identify any help you might need with day-to-day tasks.<br>
            3. The CVI records important information about the causes of sight loss. It helps in planning NHS eye care
            services and research about eye conditions.</p></div>
    <div class="highlighter">Registration and vision rehabilitiation/habilitation</div>
    <div class="group"><p>Councils have a duty to keep a register of people with sight loss. They will contact you to
            talk about the benefits of being registered. This is likely to be through the Social Services Local Sensory
            Team (or an organisation working on their behalf). Registration is often a positive step to help you to be
            as independent as possible. You can choose whether or not to be registered. Once registered, your local
            council should offer you a card confirming registration. If you are registered, you may find it easier to
            prove the degree of your sight loss and your eligibility for certain concessions. The Council should also
            talk to you about vision rehabilitation if you are an adult, and habilitation if you are a child or young
            person and any other support that might help. Vision rehabilitation/habilitation is support or training to
            help you to maximise your independence, such as moving around your home and getting out and about
            safely.</p></div>
    <div class="highlighter">Early Years Development, Children and Young People and Education</div>
    <div class="group"><p>Children (including babies) and young people who are vision impaired will require specialist
            support for their development and may receive special educational needs provision. An education, health and
            care (EHC) plan may be provided. You do not need to be certified or registered to receive this support or an
            EHC plan. This support is provided by the council’s specialist education vision impairment service.
            Additional support from a social care assessment may also be offered as a result of registration.
            Information about the support your council offers to children and young people can be found on the ‘Local
            Offer’ page of their website. If you or your child are not known to this service talk to the Ophthalmologist
            or ECLO/Sight Loss Advisor.</p></div>
    <div class="highlighter">Driving</div>
    <div class="group"><p>As a person certified as sight impaired or severely sight impaired <b>you must not drive</b>
            and you must inform the DVLA at the earliest opportunity. For more information, please contact: Drivers
            Medical Branch, DVLA, Swansea, SA99 1TU. Telephone 0300 790 6806. Email eftd@dvla.gsi.gov.uk</p></div>
    <div class="highlighter">Where to get further information, advice and support</div>
    <div class="group"><p>“Sight Loss: What we needed to know”, written by people with sight loss, contains lots of
            useful information including a list of other charities who may be able to help you. Visit
            www.rnib.org.uk/sightlossinfo</p>
        <p>‘Sightline’ is an online directory of people, services and organisations that help people with sight loss in
            your area. Visit www.sightlinedirectory.org.uk</p>
        <p>‘Starting Point’ signposts families to resources and professionals that can help with the first steps
            following your child’s diagnosis. Visit www.vision2020uk.org.uk/startingpoint</p>
        <p>Your local sight loss charity has lots of information, advice and practical solutions that can help you.
            Visit www.visionary.org.uk</p>
        <p>RNIB offers practical and emotional support for everyone affected by sight loss. Call the Helpline on 0303
            123 9999 or visit www.rnib.org.uk</p>
        <p>Guide Dogs provides a range of support services to people of all ages. Call 0800 953 0113 (adults) or 0800
            781 1444 (parents/guardians of children/young people) or visit www.guidedogs. org.uk</p>
        <p>Blind Veterans UK provides services and support to vision impaired veterans. Call 0800 389 7979 or visit
            www.noonealone.org.uk</p>
        <p>SeeAbility is a charity that acts to make eye care more accessible for people with learning disabilities and
            autism. Their easy read information can be found at www.seeability.org/looking- after-your-eyes or you can
            call 01372 755000.</p></div>
</main>

<?php echo $this->renderTiledElements([$this->getOpenElementByClassName('Element_OphCoCvi_Esign')], 'print'); ?>
