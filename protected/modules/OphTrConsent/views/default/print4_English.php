<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$institution_id = Institution::model()->getCurrent()->id;
$site_id = Yii::app()->session['selected_site_id'];
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $this->patient->id, $institution_id, $site_id);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_secondary_number_usage_code'], $this->patient->id, $institution_id, $site_id);
?>
<main class="print-main">
    <?php $this->renderPartial('_consent_header') ?>
    <h1 class="print-title">
        Consent form 4<br />
        Form for adults who are unable to consent to investigation or treatment
    </h1>
    <strong>Patient details (or pre-printed label)</strong>
    <table class="large">
        <colgroup>
            <col class="cols-5">
            <col class="cols-7">
        </colgroup>
        <tr>
            <th>Patient's surname/family name</th>
            <td><?= $this->patient->last_name ?></td>
        </tr>
        <tr>
            <th>Patient's first names</th>
            <td><?= $this->patient->first_name ?></td>
        </tr>
        <tr>
            <th>Date of birth</th>
            <td><?= $this->patient->NHSDate('dob') ?></td>
        </tr>
        <tr>
            <th><?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) ?></th>
            <td><?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?></td>
        </tr>
        <tr>
            <th><?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) ?></th>
            <td><?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier) ?></td>
        </tr>
        <tr>
            <th>Gender</th>
            <td><?= $this->patient->genderString ?></td>
        </tr>
        <tr>
            <th>&nbsp;<br />Special requirements</th>
            <td>&nbsp;<div class="dotted-write"></div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>(e.g. other language/other communication method)</td>
        </tr>
        <?php if (isset($elements['Element_OphTrConsent_Other'])) { ?>
            <tr>
                <th>Witness required</th>
                <td><?= $elements['Element_OphTrConsent_Other']->witness_required ? 'Yes' : 'No' ?></td>
            </tr>
            <?php if ($elements['Element_OphTrConsent_Other']->witness_required) { ?>
                <tr>
                    <th>Witness name</th>
                    <td><?= $elements['Element_OphTrConsent_Other']->witness_name ?></td>
                </tr>
            <?php } ?>
            <tr>
                <th>Interpreter required</th>
                <td><?= $elements['Element_OphTrConsent_Other']->interpreter_required ? 'Yes' : 'No' ?></td>
            </tr>
            <?php if ($elements['Element_OphTrConsent_Other']->interpreter_required) { ?>
                <tr>
                    <th>Interpreter name</th>
                    <td><?= $elements['Element_OphTrConsent_Other']->interpreter_name ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
        <tr>
            <th>Procedure(s)</th>
            <td>
            <?php foreach ($elements['Element_OphTrConsent_Procedure']->procedure_assignments as $i => $procedure) {
                if ($i > 0) {
                    echo ', ';
                }
                echo \CHtml::encode($procedure->eye->adjective) . ' - ' . \CHtml::encode($procedure->proc->term);
            } ?>
            </td>
        </tr>
        <?php if (isset($elements['Element_OphTrConsent_AdvancedDecision']->description)) { ?>
            <tr>
                <th>Advanced Decision</th>
                <td><?= $elements['Element_OphTrConsent_AdvancedDecision']->description ?></td>
            </tr>
        <?php } ?>
        <tr>
            <th>&nbsp;<br />Consent date</th>
            <td>
                <div class="dotted-write"></div>
            </td>
        </tr>
    </table>
    <h4>To be retained in patient's notes</h4>
    <div class="break"></div>
    <h3>All sections to be completed by health professional proposing the procedure</h3>
    <div class="spacer"></div>
    <h3>A. Details of procedure or course of treatment proposed</h3>
    <?= $this->renderPartial('_proposed_procedures', array('css_class' => 'large', 'procedures' => $elements['Element_OphTrConsent_Procedure']->procedure_assignments)) ?>
    <p>
        (NB see guidance to health professionals overleaf for details of situations where court approval must first be sought)
    </p>
    <h3>B. Assessment of patient's capacity</h3>
    <p>
        <strong>I confirm that the patient lacks capacity to give or withhold consent to this procedure or course of treatment because:</strong>
    </p>
    <p>
        The patient has an impairment of, or disturbance in the functioning of, the mind or brain; <em>and</em>
    </p>
    <p>
        The patient is unable to comprehend understand and retain information material relevant to the decision; <em>and/or</em>
    </p>
    <p>
        The patient is unable to use and or weigh this information in the decision-making process; <em>or</em>
    </p>
    <p>
        The patient is unconscious
    </p>
    <p>
        Further details (excluding where patient unconscious): for example how above judgements reached; what attempts made to assist the patient make his or her own decision and why these were not successful; which colleagues consulted; what attempts made to assist the patient make his or her own decision and why these were not successful.
    </p>
    <h3>C. Assessment of patient's best interests</h3>
    <p>
        <strong>To the best of my knowledge, the patient has not refused this procedure in a valid advance directive. Where possible and appropriate, I have encouraged the patient to participate in the decision and I have consulted with those close to the patient and with colleagues and those close to the patient. In the case of a patient who does not have anyone close enough to help in the decision-making process and for whom serious medical treatment is proposed, I have consulted an Independent Medical Capacity Advocate. and I believe the procedure to be in the patient’s best interests because:</strong>
    </p>
    <p>
        <strong>[set out how the decision about the patient’s best interests was reached; what the reasons for reaching the decision were; who was consulted to help work out best interests; and what particular factors were taken into account.]</strong>
    </p>
    <p>
        <?php if ($css_class == 'impaired') { ?>
    <div class="dotted-write"></div>
<?php } else { ?>
    <div class="dotted-write"></div>
<?php } ?>
</p>
<p>
    (Where incapacity is likely to be temporary, for example if patient unconscious, or where patient has fluctuating capacity)
</p>
<p>
    <strong>The treatment cannot wait until the patient recovers capacity because:</strong>
</p>
<div class="spacer"></div>
<p>
    <strong>The purpose for which the treatment is needed cannot be as effectively achieved in a way that is less restrictive of the patient’s rights and freedom of action because:</strong>
</p>
<h3>D. Involvement of the patient's family and others close to the patient</h3>
<p>
    Where there is no registered lasting power of attorney for personal welfare, The final responsibility for determining whether a procedure is in an incapacitated patient’s best interests lies with the health professional performing the procedure. However, it is good practice and necessary to consult with those close to the patient and anyone engaged in caring for the patient or interested in his welfare (eg spouse/partner, family and friends, carer, supporter or advocate) unless you have good reason to believe that the patient would not have wished particular individuals to be consulted, or unless the urgency of their situation prevents this. “Best interests” go far wider than “best medical interests”, and include factors such as the patient’s wishes and beliefs when competent, their current wishes, their general well-being and their spiritual and religious welfare, past and present wishes and feelings (and any relevant written statement made by the patient when the patient had capacity; the beliefs and values that would be likely to influence the patient’s decision if the patient had capacity and the other factors that the patient would be likely to consider if the patient were able to do so.
</p>
<p>
    (to be signed by a person or persons close to the patient, if they wish)
</p>
<p>
    <strong>I/We have been involved in a discussion with the relevant health professionals over the treatment of……………………………(patient’s name). I/We understand that he/she is unable to give his/her own consent, based on the criteria set out in this form. I/We also understand that treatment can lawfully be provided if it is in his/her best interests to receive it.</strong>
</p>
<span>
    Any other comments (including any concerns about decision)
</span>
<br />
<div class="dotted-write"></div>
<?= $this->renderPartial('signature_table2', array('vi' => ($css_class == 'impaired'), 'address' => true)) ?>
<p>
    If a person close to the patient was not available in person, has this matter been discussed in any other way (eg over the telephone?) Yes&nbsp;&nbsp;&nbsp;No<br /><br />
    <?php if ($css_class == 'impaired') { ?>
        Details:
<div class="dotted-write"></div>
<?php } else { ?>
    Details:<div class="dotted-write"></div>
<?php } ?>
</p>
<p>
    <strong>Signature of health professional proposing treatment</strong>
</p>
<p>
    The above procedure is, in my clinical judgement, in the best interests of the patient, who lacks capacity to consent for himself or herself. Where possible and appropriate I have discussed the patient’s condition with those close to him or her, and taken their knowledge of the patient’s past and present wishes, feelings, views and beliefs and values into account in determining his or her best interests.
</p>
<div class="spacer"></div>
<?php if (isset($elements['Element_OphTrConsent_Other'])) { ?>
    <?= $this->renderPartial('signature_table1', array('vi' => ($css_class == 'impaired'), 'consultant' => $elements['Element_OphTrConsent_Other']->consultant)) ?>
    <div class="spacer"></div>
    <p>
        Where second opinion sought, s/he should sign below to confirm agreement:
    </p>
    <?= $this->renderPartial('signature_table1', array(
        'vi' => ($css_class == 'impaired'),
        'consultant' => $elements['Element_OphTrConsent_Other']->consultant,
        'mask_consultant' => true
    )) ?>
<?php } ?>
<div class="break"></div>
<p>
    <strong>Guidance to health professionals</strong> (to be read in conjunction with consent policy)
</p>
<p>
    <strong>This form should only be used where it would be usual to seek written consent but an adult patient (18 or over) lacks capacity to give or withhold consent to treatment. If an adult has capacity to accept or refuse treatment, you should use the standard consent form and respect any refusal. Where treatment is very urgent (for example if the patient is critically ill), it may not be feasible to fill in a form at the time, but you should document your clinical decisions appropriately afterwards. If treatment is being provided under the authority of Part IV of the Mental Health Act 1983, different legal provisions apply and you are required to fill in more specialised forms (although in some circumstances you may find it helpful to use this form as well). If the patient has made a lasting power of attorney for personal welfare and there is evidence that this has been registered with the Office of the Public Guardian, then the attorney can consent to treatment on behalf of the patient and the healthcare professional does not need to make an assessment of best interests. If the adult now lacks capacity, but has clearly refused particular treatment in advance of their loss of capacity (for example in an advance directive or ‘living will’), then you must abide by that refusal if it was validly made and is applicable to the circumstances. For further information on the law on consent for those lacking capacity to consent for themselves, see the Department of Health’s Reference guide to consent for examination or treatment (www.doh.gov.uk/consent) Mental Capacity Act 2005 and the supporting Code of Conduct.</strong>
</p>
<p>
    <strong>When treatment can be given to a patient who is unable to consent For treatment to be given to a patient who is unable to consent, the following must apply:</strong>
</p>
<ul class="large">
    <li><strong>the patient must lack the capacity (‘competence’) to give or withhold consent to this procedure AND</strong></li>
    <li><strong>the procedure must be in the patient’s best interests.</strong></li>
</ul>
<p>
    <strong><br />
        A patient will lack capacity to consent to a particular intervention if he or she is:
</p>
<ul class="large">
    <li><strong>has an impairment of, or disturbance in the functioning of, the mind or brain; and</strong></li>
    <li><strong>he or she is unable to understand and retain information relevant to the decision; and/or</strong></li>
    <li><strong>he or she is unable to use or weigh this information in the decision-making process; or</strong></li>
    <li><strong>the patient is unconsciousunable to comprehend and retain information material to the decision, especially as to the consequences of having, or not having, the intervention in question; and/or</strong></li>
    <li><strong>unable to use and weigh this information in the decision-making process.</strong></li>
</ul>
<p>
    <strong>Before making a judgement that a patient lacks capacity you must take all steps reasonable in the circumstances to assist the patient in taking their own decisions (this will clearly not apply if the patient is unconscious). This may involve explaining what is involved in very simple language, using pictures and communication and decision-aids as appropriate. People close to the patient (spouse/partner, family, friends and carers) may often be able tomust be consulted as to how best to help, as may specialist colleagues such as speech and language therapists or learning disability teams, and independent advocates or supporters.</strong>
</p>
<p>
    <strong>Capacity is ‘decision-specific’: a patient may lack capacity to take a particular complex decision, but be quite able to take other more straight-forward decisions or parts of decisions.</strong>
</p>
<p>
    <strong>Best interests<br />
        A patient’s best interests are not limited to their best medical interests. Other factors which form part of the best interests decision include:</strong>
</p>
<p>
    <strong>The patient’s past and present wishes and feelings (and any relevant written statement made by the patient when he/she had capacity; and</strong>
</p>
<p>
    <strong>the beliefs and values that would be likely to influence the patient’s decision if he/she had capacity; and</strong>
</p>
<p>
    <strong>the other factors that the patient would be likely to consider if he/she were able to do so</strong>
</p>
<ul class="large">
    <li><strong>the wishes and beliefs of the patient when competent</strong></li>
    <li><strong>their current wishes</strong></li>
    <li><strong>their general well-being</strong></li>
    <li><strong>their spiritual and religious welfare</strong></li>
</ul>
<p>
    <strong>Two incapacitated patients, whose physical condition is identical, may therefore have different best interests.</strong>
</p>
<p>
    <strong>Unless the patient has clearly indicated that particular individuals should not be involved in their care, or unless the urgency of their situation prevents it, you should attempt to must involve people close to the patient (spouse/partner, family and friends, carer, supporter or advocate) in the decision-making process. Those close to the patient cannot require you to provide particular treatment which you do not believe to be clinically appropriate. However they will know the patient much better than you do, and therefore are likely to be able to provide valuable information about the patient’s wishes, feelings, beliefs and values. In the case of a patient who does not have anyone close enough to help in the decision-making process (other than paid care staff) and for whom serious medical treatment is proposed, you must consult an Independent Medical Capacity Advocate for an assessment of the patient’s best interests.</strong>
</p>
<p>
    <strong>Second opinions and court involvement<br />
        Where treatment is complex and/or people close to the patient express doubts about the proposed treatment, a second opinion should be sought, unless the urgency of the patient’s condition prevents this. Donation of regenerative tissue such as bone marrow, sterilisation for contraceptive purposes and withdrawal of artificial nutrition or hydration from a patient in PVS must never be undertaken without prior High Court approval. High Court approval can also be sought where there are doubts about the patient’s capacity or best interests.</strong>
</p>
</main>