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
?>
<?php

$model_name = CHtml::modelName($element);
$element_errors = $element->getErrors();
?>
<div class="element-data full-width">	
    <div class="flex-t">
        <div class="cols-6">
            <div class="row">
            Where the patient has authorised an attorney to make decisions about the procedure in question under a Lasting Power of Attorney or a Court Appointed Deputy has been authorised to make decisions about the procedure in question, the attorney or deputy will have the final responsibility for determining whether a procedure is in the patient's best interests
            </div>
        </div>
        <?php if ($element->comments) { ?>
            <div class="cols-5">
                <div class="fade">Other comments</div>
                <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?= $element->lastModifiedUser->first_name . ' ' . $element->lastModifiedUser->last_name ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?= $element->lastModifiedUser->first_name . ' ' . $element->lastModifiedUser->last_name ?>"}'></i>
                <span class="user-comment"><?= nl2br(CHtml::encode($element->comments ?: "-")) ?></span>
            </div>
        <?php } ?>
    </div>
        
    <hr class="divider" />
    
    <!-- Power of Attorney contacts -->
    <table class="cols-full last-left">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
            <col class="cols-5">
        </colgroup>
        <tbody>
            <?php
            foreach ($this->contact_assignments as $contact_assignment) { ?>
                <?php $contact = $contact_assignment->contact; ?>
                <?php $authorised_decision = $contact_assignment->authorisedDecision; ?>
                <?php $considered_decision = $contact_assignment->consideredDecision; ?>
                <tr>
                    <th>Patient's attorney or deputy</th>
                    <td><?= $contact->getFullName() ?></td>
                    <td><!-- empty --></td>
                </tr>
                <tr>
                    <th>Statement</th>
                    <td>I have been authorised to make decisions about the procedure in question:</td>
                    <td><span class="highlighter"><?= $authorised_decision->name ?></span></td>
                </tr>
                <tr>
                    <th>Statement</th>
                    <td>I have considered the relevant circumstances relating to the decision in question and believe the procedure to be in the patients best interests:</td>
                    <td><span class="highlighter"><?= $considered_decision->name ?></span></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>