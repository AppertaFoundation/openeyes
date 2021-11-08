<?php
/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-5">
    <div class="row divider">
        <h2><?= $ldap_config->isNewRecord ? 'Add' : 'Edit' ?> LDAP Configuration</h2>
    </div>
    <?php echo $this->renderPartial('_form_errors', ['errors' => $errors]) ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 5,
            ),
        ]
    ) ?>

    <table class="standard cols-full" id="ldap-conf-main-table">
        <colgroup>
            <col class="cols-3">
            <col class="cols-8">
        </colgroup>
        <?= \CHtml::activeHiddenField(
            $ldap_config,
            'id',
        ); ?>

        <tbody>
            <tr>
                <td><?= $ldap_config->getAttributeLabel('description'); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $ldap_config,
                        'description',
                        [ 'class' => 'cols-full' ]
                    ); ?>
                </td>
            </tr>
            <?php
            $this->renderPartial('/admin/ldap_config/_ldap_fields', [
                'ldap_config' => $ldap_config
            ]);
            ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
                <?= \CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large',
                        'name' => 'save',
                        'id' => 'et_save'
                    ]
                ); ?>
                <?= \CHtml::submitButton(
                    'Back',
                    [
                        'class' => 'button large',
                        'data-uri' => "/admin/ldapconfig",
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
    <?php $this->endWidget() ?>
</div>
<script type="x-tmpl-mustache" id="add-ldap-additional-param-template">
  <?php
    $this->renderPartial('/admin/ldap_config/_ldap_additional_param_row', [
        'key' => '{{key}}',
        'ldap_config' => $ldap_config,
    ]);
    ?>
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#ldap-conf-main-table').on('click', '#add-ldap-additional-param-btn', function () {
            let $table = $('#ldap-additional-params-table');
            let nextDataKey = OpenEyes.Util.getNextDataKey($table.find('tr'), 'key');
            let tr = Mustache.render($('#add-ldap-additional-param-template').text(), {key : nextDataKey});
            $table.append(tr);
        });

        $('#ldap-additional-params-table').on('click', '.js-remove-row', function () {
            $(this).closest('tr').remove();
        });
    });
</script>
