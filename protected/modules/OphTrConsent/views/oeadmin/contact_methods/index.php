<?php
/**
 * (C) OpenEyes Foundation, 2018
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
$form = $this->beginWidget(
    'BaseEventTypeCActiveForm',
    [
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#username',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 4,
        ),
    ]
) ?>

<form id="contact_methods" method="POST">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
    <div class="cols-11">
        <table class="standard cols-full" id="finding-table">
            <colgroup>
                <col class="cols-1">
                <col class="cols-4">
                <col class="cols-1">
                <col class="cols-5">
            </colgroup>

            <thead>
            <tr>
                <th>Order</th>
                <th>Name</th>
                <th>Active</th>
                <th>Need Signature</th>
            </tr>
            </thead>

            <tbody class="sortable">

            <?php foreach ($contact_methods as $key => $contact_method) :
                $data = [
                    'contact_method' => $contact_method,
                    'key' => $key
                ];
                $this->renderPartial('/oeadmin/contact_methods/_row', ['data' => $data]);
            endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="6">
                    <?=\CHtml::htmlButton(
                        'Add',
                        [
                            'class' => 'button large',
                            'name' => 'admin-add',
                            'type' => 'button',
                            'data-model' => 'OphTrConsent_contact_method',
                            'id' => 'et_admin-add'
                        ]
                    ); ?>
                    <?=\CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large',
                            'name' => 'admin-save',
                            'id' => 'et_admin-save'
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</form>
<?php $this->endWidget() ?>

<script type="text/template" id="finding-row-template" style="display:none">
    <?php
    $data = [
        'contact_method' => new OphTrConsent_PatientContactMethod(),
        'key' => '{{key}}'
    ];
    $this->renderPartial('/oeadmin/contact_methods/_row', ['data' => $data]);
    ?>
</script>

<script
type = "text/javascript" >
    $(document).ready(function () {
        $('.sortable').sortable({
            stop: function (e, ui) {
                $('#finding-table tbody tr').each(function (index, tr) {
                    $(tr).find('.js-display-order').val(index);
                });
            }
        });
    });

$('#et_sort').on('click', function () {
    $('#definition-list').attr('action', $(this).data('uri')).submit();
})

// Add a new row to the table using the template
$('#et_admin-add').on('click', function () {
    $('#definition-list').attr('action', $(this).data('uri')).submit();
    let $table = $('#finding-table tbody');
    let $data = {
        key: $table.find('tr').length,
    };
    let tr = Mustache.render($('#finding-row-template').text(), $data);
    $table.append(tr);
})
</script>
