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
    <div class="admin box">

        <h2><?php echo $admin->getModelDisplayName(); ?></h2>

        <form id="generic-admin-list">
		<?php

        if (is_array($admin->getFilterFields())) {
            foreach ($admin->getFilterFields() as $field => $params) { ?>
				<div class="large-2 column"><label
						for="<?php echo $params['dropDownName'] ?>"><?php echo $params['label']; ?>:</label></div>
				<div class="large-4 column">
					<?php
                    $searchParams = $this->request->getParam('search');
                if (isset($searchParams['filterid'][$params['dropDownName']]['value']) && $searchParams['filterid'][$params['dropDownName']]['value'] != '') {
                    $selectedValue[$params['dropDownName']] = $searchParams['filterid'][$params['dropDownName']]['value'];
                } else {
                    $selectedValue[$params['dropDownName']] = $params['defaultValue'];
                }
                if (!isset($params['emptyLabel'])) {
                    $params['emptyLabel'] = '-- Please select --';
                }
                if (isset($params['dependsOnFilterName'])) {
                    $filterQuery = array(
                            'condition' => $params['dependsOnDbFieldName'].'=:paramID',
                            'order' => $params['listDisplayField'],
                            'params' => array(':paramID' => $selectedValue[$params['dependsOnFilterName']]),
                        );
                    if (isset($params['dependsOnJoinedTable'])) {
                        $filterQuery = array_merge($filterQuery, array('with' => $params['dependsOnJoinedTable']));
                    }
                } else {
                    $filterQuery = array('order' => $params['listDisplayField']);
                }

                    // for some functions we need to exclude fields from search
                    if (isset($params['excludeSearch']) && $params['excludeSearch']) {
                        $fieldName = $params['dropDownName'];
                        $htmlClass = 'excluded';
                    } else {
                        $fieldName = 'search[filterid]['.$params['dropDownName'].'][value]';
                        $htmlClass = 'filterfieldselect';
                    }
                echo CHtml::dropDownList($fieldName,
                        $selectedValue[$params['dropDownName']],
                        CHtml::listData($params['listModel']->findAll($filterQuery),
                            $params['listIdField'],
                            $params['listDisplayField']),
                        array(
                            'class' => $htmlClass,
                            'empty' => $params['emptyLabel'],
                        ));
                ?>
				</div>
			<?php

            }
        }
        ?>
            <div class="row field-row">
                <table class="grid">
                    <thead>
                    <tr>
                        <?php foreach ($admin->getListFields() as $listItem): ?>
                            <th><?php echo $admin->getModel()->getAttributeLabel($listItem); ?></th>
                        <?php endforeach; ?>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($admin->getSearch()->retrieveResults() as $i => $row) { ?>
                        <tr>
                            <?php foreach ($admin->getListFields() as $listItem): ?>
                                <td>
                                    <?php
                                    if ($listItem == 'default') {
                                        if ($admin->attributeValue($row, $listItem)):
                                            ?><i class="fa fa-check"></i><?php
                                        else:
                                            ?><i class="fa fa-times"></i><?php
                                        endif;
                                    } else {
                                        if (gettype($admin->attributeValue($row, $listItem)) === 'boolean'):
                                            if ($admin->attributeValue($row, $listItem)):
                                                ?><i class="fa fa-check"></i><?php
                                            else:
                                                ?><i class="fa fa-times"></i><?php
                                            endif;
                                        else:
                                            echo $admin->attributeValue($row, $listItem);
                                        endif;
                                    }
                        ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <a OnCLick="deleteItem('<?php echo $row->id; ?>','<?php echo $admin->getCustomDeleteURL(); ?>')">Delete</a>
                                <?php if ($listItem == 'default') { ?>
                                    &nbsp;&nbsp;|&nbsp;&nbsp;<a
                                        OnCLick="setDefaultItem('<?php echo $row->id; ?>','<?php echo $admin->getCustomSetDefaultURL(); ?>')">Set
                                        Default</a>
                                    &nbsp;&nbsp;|&nbsp;&nbsp;<a
                                        OnCLick="removeDefaultItem('<?php echo $row->id; ?>','<?php echo $admin->getCustomRemoveDefaultURL(); ?>')">Remove
                                        Default</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot class="pagination-container">
                    <tr>
                        <td colspan="<?php echo count($admin->getListFields()) + 1; ?>">
                            <?php
                            $acFieldData = $admin->getAutocompleteField();
                            if ($acFieldData) {
                                if (isset($acFieldData['allowBlankSearch']) && $acFieldData['allowBlankSearch'] == 1) {
                                    $minLength = '0';
                                    $triggerSearch = "$('#autocomplete_".$acFieldData['fieldName']."').autocomplete('search','')";
                                } else {
                                    $minLength = 1;
                                    $triggerSearch = '';
                                }
                                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                                    'name' => $acFieldData['fieldName'],
                                    'id' => 'autocomplete_'.$acFieldData['fieldName'],
                                    'source' => "js:function(request, response) {
										$.getJSON('".$acFieldData['jsonURL']."', {
											term : request.term
										}, response);
									}",
                                    'options' => array(
                                        'select' => "js:function(event, ui) {
											addItem(ui.item.id, '".$admin->getCustomSaveURL()."');
											$(this).val('');
											return false;
										}",
                                        'minLength' => $minLength,
                                    ),
                                    'htmlOptions' => array(
                                        'placeholder' => $acFieldData['placeholder'],
                                        'onFocus' => $triggerSearch,
                                    ),
                                ));
                            }
                            ?>
                            <b>Select from list to add new</b>
                            <?php echo $this->renderPartial('//admin/_pagination', array(
                                'pagination' => $admin->getPagination(),
                            )) ?>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </form>
    </div>
<?php
Yii::app()->assetManager->registerScriptFile('js/oeadmin/listAutocomplete.js', CClientScript::POS_HEAD);
?>