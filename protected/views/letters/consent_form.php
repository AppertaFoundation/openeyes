<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="banner compact">
	<div class="logo"><img src="<?php echo Yii::app()->createUrl('img/_print/letterhead_Moorfields_NHS.jpg')?>" alt="letterhead_Moorfields_NHS" /></div>
</div>
<h1>Consent Form</h1>

<h2>Patient Details</h2>
<p><strong>Surname:</strong> <?php echo $patient->last_name; ?></p>
<p><strong>First name:</strong> <?php echo $patient->first_name; ?></p>
<p><strong>Date of birth:</strong> <?php echo $patient->NHSDate('dob'); ?></p>
<p><strong>Hospital number:</strong> <?php echo $patient->hos_num; ?></p>
<p><strong>Gender:</strong> <?php echo ($patient->gender == 'M') ? 'Male' : 'Female'; ?></p>

<h2>Statement of patient</h2>
<p>Please read this form carefully. If your treatment has been planned in advance, you should already have your
own copy which describes the benefits and risks of the proposed treatment. If not, you will be offered a copy
now. If you have any further questions, do ask - we are here to help you. You have the right to change your
mind at any time, including after you have signed the form.</p>
<ul>
	<li><strong>I agree</strong> to the procedure or course of treatment described on this form.</li>
	<li><strong>I understand</strong> that you cannot give me a guarantee that a particular person will perform the procedure. The
		person will, however, have appropriate experience.</li>
	<li><strong>I understand</strong> that I will have the opportunity ti discuss the details of anaesthesia with an anaesthetist before
		the procedure, unless the urgency of my situation prevents this. (This only applies to patients having general
		or regional anesthesia.)</li>
	<li><strong>I understand</strong> that any procedure in addition to those described on this form will only be carried out if it is
		necessary to save my life or to prevent serious harm to my health.</li>
	<li><strong>I have been told</strong> about additional procedures which may become neccessary during my treatment. I have
		listed below any procedures which I do no wish to be carried out without further discussion</li>
</ul>

<hr>

<table class="borders">
	<tr>
		<td>Patient signature</td>
		<td></td>
		<td>Date</td>
		<td></td>
	</tr>
	<tr>
		<td>Name</td>
		<td colspan=3><?php echo $patient->fullname; ?></td>
	</tr>
</table>

<p><strong>A witness should sign below if the patient is unable to sign but has indicated his or her
consent. Young people/children may also like a parent to sign here (see notes).</strong></p>

<table class="borders">
	<tr>
		<td>Witness signature</td>
		<td></td>
		<td>Date</td>
		<td></td>
	</tr>
	<tr>
		<td>Name (PRINT)</td>
		<td colspan=3></td>
	</tr>
</table>

<h2>Statement of interpreter <span>(where appropriate)</span></h2>
<p>I have interpreted the information above to the patient to the best of my ability and in a way
in which I believe s/he can understand.</p>
<table class="borders">
	<tr>
		<td>Interpreter signature</td>
		<td></td>
		<td>Date</td>
		<td></td>
	</tr>
	<tr>
		<td>Name (PRINT)</td>
		<td></td>
		<td>Position</td>
		<td></td>
	</tr>
</table>

<h2>Confirmation of consent <span>(to be completed by a health professional when the patient
is admitted for the procedure, if the patient has signed the form in advance)</span></h2>
<p>On behalf of the team treating the patient, I have confirmed with the patient that s/he has
no further questions and wishes the procedure to go ahead.</p>
<table class="borders">
	<tr>
		<td>Health professional signature</td>
		<td></td>
		<td>Date</td>
		<td></td>
	</tr>
	<tr>
		<td>Name (PRINT)</td>
		<td></td>
		<td>Position</td>
		<td></td>
	</tr>
</table>
