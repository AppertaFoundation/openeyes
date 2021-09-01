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

<?php $this->renderPartial('//base/_messages') ?>

<div class="alert-box error with-icon js-admin-errors" style="display:none">
    <p>Could not be deleted:</p>
    <div class="js-admin-error-container"></div>
</div>

<div class="cols-12">
    <div class="row divider">
        <form id="template-search-form" action="/OphTrConsent/oeadmin/Template/list" method="get">
            <table class="standard">
                <colgroup>
                    <col class="cols-4">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">      
                </colgroup>
                <tr>
                    <td>
                        <?= CHtml::textField('searchQuery', $query, [
                            'placeholder' => 'Search Name',
                            'class' => 'cols-full',
                        ]); ?>
                    </td>
                    <td>
                        <?=\CHtml::dropDownList(
                            'institution',
                            isset($_GET['institution']) ? $_GET['institution'] :'',
                            CHtml::listData(
                                Institution::model()->findAll(
                                    ['order' => 'name']
                                ),
                                'id',
                                'name'
                            ) + ['None' => 'None'],
                            ['empty' => 'All institutions']
                        ) ?>
                    </td>
                    <td>
                        <?=\CHtml::dropDownList(
                            'site',
                            isset($_GET['site']) ? $_GET['site'] :'',
                            CHtml::listData(
                                Site::model()->findAll(
                                    ['order' => 'name']
                                ),
                                'id',
                                'name'
                            ) + ['None' => 'None'],
                            ['empty' => 'All sites']
                        ) ?>
                    </td>
                    <td>
                        <?=\CHtml::dropDownList(
                            'subspecialty',
                            isset($_GET['subspecialty']) ? $_GET['subspecialty'] :'',
                            CHtml::listData(
                                Subspecialty::model()->findAll(
                                    ['order' => 'name']
                                ),
                                'id',
                                'name'
                            ) + ['None' => 'None'],
                            ['empty' => 'All subspecialties']
                        ) ?>
                    </td>
                    <td>
                        <button class="blue hint" id="search-button" type="submit">Search</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <form id="admin_list_templates">
        <table class="standard cols-full">
            <colgroup>
                <col>
                <col class="cols-1">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-3">
            </colgroup>
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Id</th>
                <th>Name</th>
                <th>Institution</th>
                <th>Site</th>
                <th>Subspecialty</th>
                <th>Type</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) { ?>
                <tr class="clickable" data-id="<?php echo $model->id ?>"
                    data-uri="OphTrConsent/oeadmin/Template/edit/<?php echo $model->id ?>">
                    <td><input type="checkbox" name="templates[]" value="<?php echo $model->id ?>"/></td>
                    <td><?php echo $model->id ?></td>
                    <td><?php echo $model->name ?></td>
                    <td><?= isset($model->institution) ? $model->institution->name : ""  ?></td>
                    <td><?= isset($model->site) ? $model->site->name : "" ?></td>
                    <td><?= isset($model->subspecialty) ? $model->subspecialty->name : ""  ?></td>
                    <td><?= isset($model->type) ? $model->type->name : ""  ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="2">
                    <?= \CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'type' => 'button',
                            'name' => 'add',
                            'data-uri' => '/OphTrConsent/oeadmin/Template/add',
                            'id' => 'et_add'
                        ]
                    ); ?>
                    <?= \CHtml::submitButton(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete_template',
                            'data-object' => 'template',
                            'id' => 'et_delete_template'
                        ]
                    ); ?>
                </td>
                <td colspan="4">
                    <?php $this->widget('LinkPager', ['pages' => $pagination]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<script type = "text/javascript" >
    $('#et_delete_template').click(function (e) {
        e.preventDefault();

        let $checked = $('input[name="templates[]"]:checked');
        if ($checked.length === 0) {
            alert('Please select one or more generic procedure data to delete.');
            return;
        }

        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/OphTrConsent/oeadmin/Template/delete',
            'data': $checked.serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'success': function (response) {
                if (response['status'] === 1) {
                    window.location.reload();
                } else {
                    $('.js-admin-errors').show();
                    let $errorContainer = $('.js-admin-error-container');
                    $errorContainer.html("");

                    response['errors'].forEach(function (error) {
                        $errorContainer.append('<p class="js-admin-errors">' + error + '</p>');
                    });
                }
            }
        });
    });
</script>