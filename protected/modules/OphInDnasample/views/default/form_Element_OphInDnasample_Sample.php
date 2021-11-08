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
<div class="element-fields full-width flex-layout flex-top col-gap">
    <div class="cols-6 data-group">
        <table class= "cols-full">
            <tbody>
            <tr>
                <td>
                    <?= $form->dropDownList(
                        $element,
                        'type_id',
                        CHtml::listData(OphInDnasample_Sample_Type::model()->findAll(array('order' => 'display_order asc')), 'id', 'name'),
                        ['empty' => '- Select -'],
                        false,
                        array('label' => 7, 'field' => 5, 'full_dropdown' => true, 'class' => 'oe-input-is-read-only', 'hidden' => true)
                    );


                    /* now way to hide the whole row using the widget : $form->activeWidget('TextField', $element, 'other_sample_type', array('class' => 'hidden')); */

?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    $form->datePicker(
                        $element,
                        'blood_date',
                        array('options' => array('maxDate' => 'today')),
                        array('style' => 'width: 100%'),
                        array('label' => 7, 'field' => 5)
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=$form->textField(
                        $element,
                        'volume',
                        array(
                            'style' => 'width: 100%',
                        ),
                        null,
                        array('label' => 7, 'field' => 5)
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=$form->textField(
                        $element,
                        'destination',
                        array(
                            'style' => 'width: 100%',
                        ),
                        null,
                        array('label' => 7, 'field' => 5)
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    $users = User::model()->findAllByRoles(['Genetics User', 'Genetics Clinical', 'Genetics Laboratory Technician', 'Genetics Admin'], true);
                    $form->dropDownList(
                        $element,
                        'consented_by',
                        CHtml::listData($users, 'id', function ($row) {
                            return $row->last_name.', '.$row->first_name;
                        }),
                        array('empty' => '- Select -', 'options'=>array(Yii::app()->user->id => array('selected' => true))),
                        false,
                        array('label' => 7, 'field' => 5, 'class' => 'oe-input-is-read-only cols-full', 'hidden' => true)
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group cols-full flex-layout">
                        <div class="cols-7 column">Study(s):</div>
                        <div class="cols-5 column">
                            <?php
                            $user = User::model()->findByPk(Yii::app()->user->id);

                            $form->multiSelectList(
                                $element,
                                CHtml::modelName($element) .'[studies]',
                                'studies',
                                'id',
                                CHtml::listData(GeneticsStudy::model()->findAll(), 'id', 'name'),
                                array(),
                                array('label' => 'Study(s)', 'empty' => '-- Add --', 'nowrapper' => true, 'full_dropdown' => true, 'class' => 'cols-full'),
                                false,
                                false,
                                null,
                                false,
                                false

                            );
                            ?>
                        </div>
                    </div>

                </td>
            </tr>
            <tr>
                <td>

                    <?=
                    //$user['first_name'].' '.$user['last_name'];
                        $form->textField(
                            $element,
                            'comments',
                            array(
                                'style' => 'width: 100%',
                            ),
                            null,
                            array('label' => 7, 'field' => 5)
                        );
?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    $hidden = $element->other_sample_type ? '' : 'hidden'; //hide if null
                    if ( $element->getError('other_sample_type') ) {
                        // show the field if there is an error
                        $hidden = '';
                    }
                    ?>
                    <div id="div_Element_OphInDnasample_Sample_other_sample_type" class="data-group <?php echo $hidden; ?>">
                        <div class="cols-5 column">
                            <label for="Element_OphInDnasample_Sample_other_sample_type"><?php echo $element->getAttributeLabel('other_sample_type'); ?></label>
                        </div>
                        <div class="cols-5 column end">
                            <?=\CHtml::textField('Element_OphInDnasample_Sample[other_sample_type]', $element->other_sample_type); ?>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>