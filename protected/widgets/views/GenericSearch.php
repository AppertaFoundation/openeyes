<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<?php
if ($search->getSearchItems() && is_array($search->getSearchItems())):
    if (isset($subList) && $subList):?>
        <div id="generic-search-form">
    <?php
    else:
        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'generic-search-form',
            'enableAjaxValidation' => false,
            'method' => 'get',
            'action' => '#',
        ));
    endif; ?>
    <div>
        <?php
        foreach ($search->getSearchItems() as $key => $value):
            $name = 'search[' . $key . ']';
            if (is_array($value)):
                $type = isset($value['type']) ? $value['type'] : 'compare';
                $default = isset($value['default']) ? $value['default'] : '';
                switch ($type) {
                    case 'compare':
                        $comparePlaceholder = $search->getModel()->getAttributeLabel($key);
                        foreach ($value as $searchKey => $searchValue):
                            if ($searchKey === 'compare_to'):
                                foreach ($searchValue as $compareTo):
                                    $comparePlaceholder .= ', ' . $search->getModel()->getAttributeLabel($compareTo);
                                    echo CHtml::hiddenField('search[' . $key . '][compare_to][' . $compareTo . ']', $compareTo);
                                endforeach;
                            endif;
                        endforeach; ?>
                        <div class="single-search-field">
                            <?php
                            $name .= '[value]';
                            echo CHtml::textField($name, $search->getSearchTermForAttribute($key), array(
                                'autocomplete' => Yii::app()->params['html_autocomplete'],
                                'placeholder' => $comparePlaceholder,
                            )); ?>
                        </div>
                        <?php break;
                    case 'boolean':
                        ?>
                        <div>
                            <?php
                            echo CHtml::dropDownList($name, $search->getSearchTermForAttribute($key), array(
                                '' => 'All',
                                '1' => 'Only ' . $search->getModel()->getAttributeLabel($key),
                                '0' => 'Exclude ' . $search->getModel()->getAttributeLabel($key),
                            ));
                            ?>
                        </div>
                        <?php
                        break;
                    case 'dropdown':
                        ?>
                        <div>
                            <?php
                            echo CHtml::dropDownList(
                                $name,
                                $search->getSearchTermForAttribute($key, $default),
                                $value['options'],
                                array('empty' => $value['empty'] ? $value['empty'] : '- Select-')
                            );
                            echo CHtml::hiddenField('search[exact][' . $key . ']', true)
                            ?>
                        </div>
                        <?php
                        break;
                    case 'disorder':
                        ?>
                        <div id="diagnosis-search">
                            <span id="enteredDiagnosisText" style="display: none;">&nbsp;</span>
                            <?php
                                $this->controller->renderPartial('//disorder/disorderAutoComplete', array(
                                    'class' => 'search',
                                    'name' => 'disorder_id',
                                    'code' => '',
                                    'value' => Yii::app()->request->getQuery('search[disorder_id]', ''),
                                    'clear_diagnosis' => '<i class="fa fa-times" aria-hidden="true" id="clear-diagnosis-widget"></i>',
                                    'placeholder' => 'Search for a diagnosis',
                                ));
                            ?>
                        </div>
                        <?php
                        break;
                }
            else: ?>
                <div>
                    <?php
                    echo CHtml::textField($name, $search->getSearchTermForAttribute($key), array(
                        'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'placeholder' => $search->getModel()->getAttributeLabel($key),
                    ));
                    ?>
                </div>
                <?php
            endif;
        endforeach;
        ?>
        <div class="submit-row">
            <button class="button small primary event-action" name="save" formmethod="get" type="submit">Search</button>
        </div>
    </div>

    <?php
    if (isset($subList) && $subList):
        ?></div><?php
    else:
        $this->endWidget();
    endif;
endif;
?>