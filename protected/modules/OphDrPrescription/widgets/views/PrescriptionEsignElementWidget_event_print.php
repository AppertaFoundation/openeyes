<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
    $signatures = $this->element->getViewSignatures();
    $print_mode = Yii::app()->request->getParam('print_mode');
?>


<?php if (count($signatures) > 0 && ($print_mode !== 'WP10' && $print_mode !== 'FP10')) { ?>
    <?php foreach (array_filter($signatures, fn($sig) => (int)$sig->type !== \BaseSignature::TYPE_LOGGEDIN_USER) as $signatory) { ?>
        <table class="borders done_bys">
            <tr>
                <th><?=$signatory->signatory_role?></th>
                <td><?=$signatory->signatory_name ?><?php if (isset($signatory->signedUser->registration_code)) {
                        echo ' (' . $signatory->signedUser->registration_code . ')';
                    } ?>
                </td>
                <th>Date</th>
                <td><?= $signatory->isSigned() ? $this->element->NHSDate('created_date') : '' ?></td>
            </tr>
            <tr class="handWritten">
                <th>Signature</th>
                <td>
                    <div class="dotted-write" style="text-align: center">
                        <?= str_replace("<img", "<img style='width:90px' ", $signatory->getPrintout()); ?>
                    </div>
                </td>
                <th>Contact Number</th>
                <td>
                    <div class="dotted-write"></div>
                </td>
            </tr>
        </table>
    <?php } ?>

    <?php if ($medication_management_element = $this->element->detailsElement->isSignedByMedication()) {
        $readonly_signatures = $medication_management_element->getSignatures(true);

        foreach ($readonly_signatures as $signature) :?>
            <table class="borders done_bys">
            <tr>
                <th><?=$signature->signatory_role?></th>
                <td><?=$signature->signatory_name ?><?php if (isset($signature->signedUser->registration_code)) {
                        echo ' (' . $signature->signedUser->registration_code . ')';
                    } ?>
                </td>
                <th>Date</th>
                <td><?= $this->element->NHSDate('created_date') ?>
                </td>
            </tr>
            <tr class="handWritten">
                <th>Signature</th>
                <td>
                    <div class="dotted-write" style="text-align: center">
                        <?= str_replace("<img", "<img style='width:90px' ", $signature->getPrintout()); ?>
                    </div>
                </td>
                <th>Contact Number</th>
                <td>
                    <div class="dotted-write"></div>
                </td>
            </tr>
        </table>
        <?php endforeach;?>

    <?php } else {
        foreach (array_filter($signatures, fn($sig) => (int)$sig->type === \BaseSignature::TYPE_LOGGEDIN_USER) as $signature) { ?>
            <table class="borders done_bys">
                <tr>
                    <th><?=$signature->signatory_role?></th>
                    <td><?=$signature->signatory_name ?><?php if (isset($signature->signedUser->registration_code)) {
                            echo ' (' . $signature->signedUser->registration_code . ')';
                        } ?>
                    </td>
                    <th>Date</th>
                    <td><?= $this->element->NHSDate('created_date') ?>
                    </td>
                </tr>
                <tr class="handWritten">
                    <th>Signature</th>
                    <td>
                        <div class="dotted-write" style="text-align: center">
                            <?= str_replace("<img", "<img style='width:90px' ", $signature->getPrintout()); ?>
                        </div>
                    </td>
                    <th>Contact Number</th>
                    <td>
                        <div class="dotted-write"></div>
                    </td>
                </tr>
            </table>
        <?php } ?>
    <?php } ?>
<?php } ?>
