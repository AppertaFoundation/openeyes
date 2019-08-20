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
$operationNoteList = $element->getOperationNoteList();
$operation_note_id = \Yii::app()->request->getParam('OphCiExamination_postop_complication_operation_note_id',
    (is_array($operationNoteList) ? key($operationNoteList) : null));
?>

<?php if (isset($operation_note_id)) : ?>
    <div class="element-data element-eyes">
        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
            <div class="js-element-eye <?= $eye_side ?>-eye column">
                <div class="data-group">
                    <?php if ($element->hasEye($eye_side)) :
                        $eye_abbr = $eye_side == 'right' ? 'R' : 'L';
                        $eye_macro = $eye_side == 'right' ? \Eye::RIGHT : \Eye::LEFT;
                        ?>
                        <table id="right-complication-list" class="recorded-postop-complications"
                               data-sideletter="<?= $eye_abbr ?>">
                            <tr>
                                <td colspan="2">
                                    <?php if ($eye_side === 'right') {
                                        echo $operationNoteList[$operation_note_id];
                                    } ?>
                                </td>
                            </tr>
                            <?php foreach ($element->getFullComplicationList($eye_macro) as $value) : ?>
                                <tr>
                                    <td class=postop-complication-name><?php echo $value['name']; ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else : ?>
                        <div class="data-value not-recorded">
                            Not assessed in this examination
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
		<div class="element-data element-eyes">
			<div class="js-element-eye right-eye">
				<div class="data-value not-recorded">
					There are no recorded operations for this patient
				</div>
			</div>
			<div class="js-element-eye left-eye">
				<div class="data-value not-recorded">
					There are no recorded operations for this patient
				</div>
			</div>
		</div>
<?php endif; ?>
