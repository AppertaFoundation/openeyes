<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2022, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var $institution Institution
 * @var $site Site|null
 * @var $subspecialty Subspecialty|null
 */
?>

<div>
    <div class="row divider">
        <form method="GET">
            <table>
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-8">
                </colgroup>
                <tr>
                    <td>Institution</td>
                    <td>
                    <?php
                    if ($this->checkAccess('admin')) {
                        echo \CHtml::dropDownList('institution_id', $institution->id, \Institution::model()->getList(false), ['class' => 'cols-8']);
                    } else {
                        echo $institution->name;
                    }
                    ?>
                    </td>
                </tr>
                <tr>
                    <td>Site / Location</td>
                    <td>
                    <?php
                    if (count($institution->sites) > 0) {
                        $sites = \CHtml::listData($institution->sites, 'id', 'name');

                        echo \CHtml::dropDownList('site_id', $site->id ?? null, $sites, ['class' => 'cols-8']);
                    } else {
                        echo 'No sites available for the selected institution';
                    }
                    ?>
                    </td>
                </tr>
                <tr>
                    <td>Service / Subspecialty</td>
                    <td><?= \CHtml::dropDownList('subspecialty_id', $subspecialty->id ?? null, \Subspecialty::model()->getList(), ['class' => 'cols-8', 'empty' => 'All']) ?></td>
                </tr>
            </table>
        </form>
    </div>
    <?php if (!empty($site)) : ?>
        <form method="POST">
            <input type="hidden" class="no-clear" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
            <?= \CHtml::hiddenField('institution_id', $institution->id) ?>
            <?= \CHtml::hiddenField('site_id', $site->id) ?>
            <?= \CHtml::hiddenField('subspecialty_id', $subspecialty->id ?? null) ?>
            <?php
                $command = Yii::app()->db->createCommand()
                                         ->select('f.id, f.name, s.name AS subspecialty, sfm.id AS context_location')
                                         ->from('firm f')
                                         ->join('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
                                         ->join('subspecialty s', 'ssa.subspecialty_id = s.id')
                                         ->leftJoin('ophcocorrespondence_internal_referral_site_firm_mapping sfm', 'sfm.firm_id = f.id')
                                         ->where('f.active = 1')
                                         ->andWhere('sfm.site_id IS NULL OR sfm.site_id = :site_id', [':site_id' => $site->id]);

            if ($subspecialty) {
                $command->andWhere('s.id = :subspecialty_id', [':subspecialty_id' => $subspecialty->id]);
            }

                $entries = $command->order('f.name, s.name')->queryAll();

                $already_included = [];
                $excluded = [];

            foreach ($entries as $entry) {
                $display = $entry['name'];

                if ($entry['subspecialty']) {
                    $display .= ' (' . $entry['subspecialty'] . ')';
                }

                if ($entry['context_location']) {
                    $already_included[$entry['id']] = $display;
                } else {
                    $excluded[$entry['id']] = $display;
                }
            }
            ?>
            <select id='js-firms-selection' class='cols-8'>
                <option value="">-- Add --</option>
                <?php foreach ($excluded as $id => $display) : ?>
                    <option value="<?= $id ?>"><?= $display ?></option>
                <?php endforeach; ?>
            </select>
            <ul id='js-firms-list'>
                <?php
                    $index = 0;
                foreach ($already_included as $id => $display) :
                    ?>
                    <li>
                    <?= \CHtml::hiddenField("InternalReferralSiteFirmMapping_firm_id[$index]", $id) ?>
                    <?= $display ?>
                        <i class="oe-i remove-circle small-icon pad-left js-remove-firm-entry"></i>
                    </li>
                    <?php
                    $index = $index + 1;
                endforeach;
                ?>
            </ul>
            <input type="submit" value="Save" />
        </form>
    <?php else : ?>
        <div>No site selected</div>
    <?php endif ?>
</div>
<script type='text/template' id='js-firms-list-row-template'>
    <li>
        <?= \CHtml::hiddenField('InternalReferralSiteFirmMapping_firm_id[{{index}}]', '{{id}}') ?>
        {{display}}
        <i class="oe-i remove-circle small-icon pad-left js-remove-firm-entry"></i>
    </li>
</script>
<script>
    $(document).ready(function() {
        $('#institution_id, #site_id, #subspecialty_id').on('change', function() {
            $(this).closest('form').submit();
        });

        let nextIndex = $('#js-firms-list li').length;

        function removeListEntry() {
            const id = $(this).closest('input').val();
            const display = $(this).parent().text().trim();

            const selection = $('#js-firms-selection');

            selection.append($(`<option value="${id}">${display}</option>`));

            $(this).parent().remove();
        }

        $('#js-firms-selection').on('change', function () {
            const selection = $('#js-firms-selection');
            const choice = selection.val();

            if (choice !== '') {
                const list = $('#js-firms-list');
                const display = selection.children(`option[value=${choice}]`).text().trim();

                selection.children(`option[value="${choice}"]`).remove();

                const template = $('#js-firms-list-row-template').text();
                const newListItem = Mustache.render(template, {index: nextIndex, id: choice, display: display});

                list.append(newListItem);
                nextIndex = nextIndex + 1;

                $('#js-firms-list .js-remove-firm-entry').off('click').on('click', removeListEntry);
            }
        });

        $('#js-firms-list .js-remove-firm-entry').on('click', removeListEntry);
    });
</script>
