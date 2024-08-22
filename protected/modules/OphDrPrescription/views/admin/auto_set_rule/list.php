<?php
/**
 * (C) OpenEyes Foundation, 2019
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
if (!isset($displayOrder)) {
    $displayOrder = 0;
}
if (!isset($uniqueid)) {
    $uniqueid = $this->uniqueid;
}
?>
<?php $this->renderPartial('//base/_messages'); ?>
<div class='<?= $admin->div_wrapper_class ?>'>
    <?php if (!$admin->isSubList() || $admin->isForceTitleDisplay()) : ?>
    <h2><?php echo $admin->getModelDisplayName(); ?></h2>
    <?php endif; ?>
    <?php $this->widget('GenericSearch', array('search' => $admin->getSearch(), 'subList' => $admin->isSubList())); ?>

    <?php
    $returnUri = '';
    if ($admin->isSubList()) : ?>
    <div id="generic-admin-sublist">

        <?php if ($admin->isForceFormDisplay()) : ?>
        <form id="generic-admin-list">
        <?php endif; ?>

            <?php
            if ($admin->getSubListParent() && is_array($admin->getSubListParent())) :
                foreach ($admin->getSubListParent() as $key => $value) :
                    ?>
                    <input type="hidden" name="default[<?= $key ?>]" value="<?= $value ?>"/>
                    <?php
                endforeach;
            endif;
            $returnUri = $admin->generateReturnUrl(Yii::app()->request->requestUri);
            ?>
            <input type="hidden" name="returnUri" id="returnUri" value="<?= $returnUri ?>"/>

    <?php else : ?>
            <form id="generic-admin-list">
    <?php endif; ?>
                <input type="hidden" name="page" value="<?php echo Yii::app()->request->getParam('page', 1) ?>"/>
                <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>

                <table class="standard">
                    <thead>
                    <tr>
                        <th><input type="checkbox" name="selectall" id="selectall"/></th>
                        <?php
                        foreach ($admin->getListFields() as $listItem) :
                            if ($listItem !== 'attribute_elements.id') :?>
                                <th>
                                    <?php if ($admin->isSortableColumn($listItem)) : ?>
                                    <a href="/<?php echo $uniqueid ?>/list?<?php echo $admin->sortQuery($listItem, $displayOrder,
                                        Yii::app()->request->getQueryString()) ?>">
                                    <?php endif;
                                    ?>
                                        <?php echo $admin->getModel()->getAttributeLabel($listItem); ?>
                                        <?php if ($admin->isSortableColumn($listItem)) : ?>
                                    </a>
                                        <?php endif;
                                        ?>
                                </th>
                            <?php else : ?>
                                <th>Action</th>
                            <?php endif;
                        endforeach; ?>
                    </tr>
                    </thead>
                    <tbody <?php if (in_array('display_order', $admin->getListFields())) :
                        echo 'class="sortable"';
                           endif; ?>>
                    <?php
                    $retrieveResults = $admin->getSearch()->retrieveResults();
                    foreach ($retrieveResults as $i => $row) { ?>
                        <tr class="clickable" data-id="<?php echo $row->id ?>"
                            data-uri="<?php echo $uniqueid ?>/<?php echo $admin->getListFieldsAction() ?>/<?php echo $row->id ?>?returnUri=<?= $returnUri ?>">
                            <td>
                                <input type="checkbox" name="<?php echo $admin->getModelName(); ?>[id][]"
                                       value="<?php echo $row->id ?>"/>
                            </td>
                            <?php foreach ($admin->getListFields() as $listItem) :
                                if ($listItem !== 'attribute_elements.id') : ?>
                                    <td>
                                        <?php
                                        $attr_val = $admin->attributeValue($row, $listItem);
                                        if (gettype($attr_val) === 'boolean') :
                                            if ($admin->attributeValue($row, $listItem)) :
                                                ?><i class="oe-i tick small"></i><?php
                                            else :
                                                ?><i class="oe-i remove small"></i><?php
                                            endif;
                                        elseif (gettype($attr_val) === 'array') :
                                                echo implode(',', $admin->attributeValue($row, $listItem));
                                        elseif ($listItem === 'display_order') :
                                            ?>
                                            &uarr;&darr;<input type="hidden"
                                                               name="<?php echo $admin->getModelName(); ?>[display_order][]"
                                                               value="<?php echo $row->id ?>">
                                                <?php
                                        else :
                                                echo $attr_val;
                                        endif
                                        ?>
                                    </td>
                                <?php endif;

                                if ($listItem === 'attribute_elements.id') :
                                    $mappingId = $admin->attributeValue($row, $listItem);
                                endif;

                                if ($listItem === 'attribute_element_types.name') :?>
                                    <td>
                                        <?php if (($mappingId > 0)) : ?>
                                            <a onMouseOver="this.style.color='#AFEEEE'"
                                               onMouseOut="this.style.color='#00F'"
                                               href="../../OphCiExamination/admin/manageElementAttributes?attribute_element_id=<?php echo $mappingId ?>">Manage
                                                Options</a>
                                        <?php endif; ?>
                                    </td>
                                    <?php
                                endif;
                            endforeach; ?>

                        </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot class="pagination-container">
                    <tr>
                        <td colspan="<?php echo count($admin->getListFields()) + 1; ?>">
                            <?php if (isset($buttons) && ($buttons == true)) { ?>
                                <?= \CHtml::button(
                                    'Add',
                                    [
                                        'class' => 'button large',
                                        'data-uri' => $admin->getCustomAddUrl() == '' ? '/' . $uniqueid . '/edit' : $admin->getCustomAddUrl(),
                                        'formmethod' => 'get',
                                        'name' => 'add',
                                        'id' => 'et_add'
                                    ]
                                ); ?>
                                <?= \CHtml::button(
                                    'Delete',
                                    [
                                        'class' => 'button large',
                                        'name' => 'delete',
                                        'data-uri' => '/' . $uniqueid . '/delete',
                                        'data-object' => $admin->getModelName(),
                                        'id' => 'et_delete'
                                    ]
                                ); ?>
                                <?= \CHtml::linkButton('Rebuild all sets now',
                                    array('href' => '/OphDrPrescription/medicationSetAutoRulesAdmin/populateAll',
                                        'class' => 'button large')); ?>
                            <?php } ?>
                            <?php echo EventAction::button(
                                'Sort',
                                'sort',
                                array(),
                                array(
                                    'class' => 'button large',
                                    'style' => 'display:none;',
                                    'data-uri' => '/' . $uniqueid . '/sort',
                                    'data-object' => $admin->getModelName(),
                                )
                            )->toHtml() ?>
                            <?php echo $this->renderPartial('//admin/_pagination', array(
                                'pagination' => $admin->getPagination(),
                                'hide_links' => (!$admin->getSearch()->isDefaultResults() && !$admin->getSearch()->isSearching())
                            )) ?>
                            <?php
                            if (!empty($_GET) && count($retrieveResults) < 1) {
                                echo "No results found. Total of " . $admin->getModel()->count() . " items.";
                            }
                            ?>
                        </td>
                    </tr>
                    </tfoot>
                </table>
                <?php if ($admin->isSubList()) : ?>
                    <?php if ($admin->isForceFormDisplay()) : ?>
            </form>
                    <?php endif; ?>

    </div>
                <?php else : ?>
    </form>
                <?php endif; ?>
</div>