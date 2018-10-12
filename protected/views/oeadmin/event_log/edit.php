<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-7">

    <div class="row divider">
        <h2>Examination Event Logs</h2>
    </div>

    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'focus' => '#contactname',
    )) ?>

    <table class="standard">
        <tbody>
        <tr>
            <td>Event Id</td>
            <td>
                <?php if ($event) : ?>
                    <?php echo $event->id; ?>
                    <?= \CHtml::hiddenField('eventId', $event->id, array('id' => 'hiddenInput')); ?>
                <?php else : ?>
                    Event Deleted
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Status</td>
            <td>
                <?php echo $status; ?>
            </td>
        </tr>
        <tr>
            <td>Unique Code</td>
            <td><?php echo $unique_code; ?></td>
        </tr>
        <tr>
            <td>Patient Identifier</td>
            <td><?php echo $data['patient']['unique_identifier']; ?></td>
        </tr>
        <tr>
            <td>Date of birth</td>
            <td><?php echo date('d M Y', strtotime($data['patient']['dob'])); ?></td>
        </tr>
        <?php
        $exams = array($data);
        if ($status === 'Duplicate Event') : ?>
            <?php $exams = array($previous, $data); ?>
            <tr>
                <th>&nbsp;</th>
                <th>Existing - <?= date('d M Y', strtotime($data['examination_date'])); ?></th>
                <th>New - <?= date('d M Y', strtotime($previous['examination_date'])); ?></th>
            </tr>
        <?php endif; ?>
        <tr>
            <td>Eye Readings</td>
            <?php foreach ($exams as $exam) : ?>
                <td>
                    <?php foreach ($exam['patient']['eyes'] as $eyes) : ?>
                        <?php echo $eyes['label']; ?>
                        <br/> Refraction ( Sphere-<?php echo $eyes['reading'][0]['refraction']['sphere']; ?>, Cylinder-<?php echo $eyes['reading'][0]['refraction']['cylinder']; ?>, Axis-<?php echo $eyes['reading'][0]['refraction']['axis']; ?> )
                        <br/>IOP ( <?php echo $eyes['reading'][0]['iop']['mm_hg']; ?> mmhg, <?php echo $eyes['reading'][0]['iop']['instrument']; ?>)
                        <br/>
                        <br/>
                    <?php endforeach; ?>
                </td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <td>
                OpTom Details
            </td>
            <?php foreach ($exams as $exam) : ?>
                <td>
                    Name : <?php echo $exam['op_tom']['name']; ?>
                    <br/>
                    Address : <?php echo $exam['op_tom']['address']; ?>
                </td>
            <?php endforeach; ?>
        </tr>
        <?php if ($status === 'Unfound Event') : ?>
            <tr>
                <td><label for="patient-search">Assign To Patient</label></td>
                <td>
                    <div class="large-9 column">
                        <input type="text"
                               name="search"
                               id="patient-search"
                               class="form panel search large ui-autocomplete-input"
                               placeholder="Enter search..."
                               autocomplete="off">
                        <div style="display:none" class="no-result-patients warning alert-box">
                            <div class="small-12 column text-center">
                                No results found.
                            </div>
                        </div>
                        <div id="patient-result" style="display: none">

                        </div>
                        <input type="hidden" name="patient_id" id="patient-result-id">
                    </div>
                    <div class="large-3 column text-right">
                        <img class="loader"
                             src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
                             alt="loading..." style="margin-right: 10px; display: none;"/>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
        <tfoot class="pagination-container">
        <tr>
            <td colspan="2">
                <?php
                if (in_array($status, ['Success Event', 'Dismissed Event', 'Import Success'])) {
                    echo CHtml::submitButton('Ok', [
                        'class' => 'button large',
                        'data-uri' => '/oeadmin/eventLog/list',
                    ]);
                } elseif ($status == 'Duplicate Event') {
                    echo CHtml::submitButton('Accept New', [
                            'class' => 'button large',
                            'name' => 'cancel',
                            'id' => 'et_save',
                        ]) . ' ' .
                        CHtml::submitButton('Dismiss New', [
                            'class' => 'button large',
                            'data-uri' => '/oeadmin/eventLog/dismiss/' . $log_id,
                            'name' => 'cancel',
                            'id' => 'et_cancel',
                        ]);
                } else {
                    echo CHtml::submitButton('Save', [
                            'class' => 'button large',
                            'id' => 'et_save',
                        ]) . ' ' .
                        CHtml::submitButton('Cancel', [
                            'class' => 'button large',
                            'data-uri' => '/oeadmin/eventLog/list',
                            'name' => 'cancel',
                            'id' => 'et_cancel',
                        ]);
                }
                ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        OpenEyes.UI.Search.init($('#patient-search'));
        OpenEyes.UI.Search.getElement().autocomplete('option', 'select', function (event, uid) {
            $('#patient-search').hide();
            $('#patient-result').html('<span>' + uid.item.first_name + ' ' + uid.item.last_name + '</span>').show();
            $('#patient-result-id').val(uid.item.id);
        });
    });
</script>
