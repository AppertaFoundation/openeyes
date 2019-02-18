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

<div class="cols-5">
    <?php
    if (!isset($title)) {
        $title = 'Commissioning body services';
    }
    // some base initialisation
    $url_query = '';
    if (!isset($return_url)) {
        $return_url = '';
    }
    if (!isset($base_data_url)) {
        $base_data_url = 'admin/';
    }
    ?>

    <form id="admin_commissioning_body_services">
        <table class="grid standard">
            <thead>
            <tr>
                <th><input type="checkbox" id="checkall" class="commissioning_body_services"/></th>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Commissioning body</th>
            </tr>
            </thead>

            <tbody>
            <?php
            $criteria = new CDbCriteria();
            $criteria->with = array('commissioning_body');
            $criteria->order = 'LOWER(t.name) asc';

            if (isset($commissioning_bt)) {
                $criteria->addColumnCondition(array('commissioning_body.commissioning_body_type_id' => $commissioning_bt->id));
                $url_query = 'commissioning_body_type_id=' . $commissioning_bt->id;
            }
            if (isset($service_type)) {
                $url_query .= '&service_type_id=' . $service_type->id . '&return_url=' . $return_url;
            }

            foreach (CommissioningBodyService::model()->findAll($criteria) as $i => $cbs) { ?>
                <tr class="clickable" data-id="<?php echo $cbs->id ?>"
                    data-uri="<?php echo $base_data_url ?>editCommissioningBodyService?commissioning_body_service_id=<?php echo $cbs->id;
                    if (isset($data["returnUrl"])) {
                        echo "&return_url=" . $data["returnUrl"];
                    } ?>">
                    <td><input type="checkbox" name="commissioning_body_service[]" value="<?php echo $cbs->id ?>"
                               class="wards"/></td>
                    <td><?php echo $cbs->code ?></td>
                    <td><?php echo $cbs->name ?></td>
                    <td><?php echo $cbs->type->name ?></td>
                    <td><?php echo $cbs->commissioning_body ? $cbs->commissioning_body->name : 'None' ?></td>
                </tr>
            <?php } ?>
            </tbody>

            <tfoot>
            <tr>
                <td colspan="5">
                    <?= \CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'name' => 'add_commissioning_body_service',
                            'id' => 'et_add_commissioning_body_service',
                            'data-uri' => $url_query,
                        ]
                    ); ?>
                    <?= \CHtml::button(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete_commissioning_body_service',
                            'id' => 'et_delete_commissioning_body_service',
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>

    <div id="confirm_delete_commissioning_body_services"
         title="Confirm delete commissioning_body_service"
         style="display: none;">
        <div>
            <div id="delete_commissioning_body_services">
                <div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
                    <strong>WARNING: This will remove the commissioning body service from the system.<br/>
                        This action cannot be undone.</strong>
                </div>
                <p>
                    <strong>Are you sure you want to proceed?</strong>
                </p>
                <div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
                    <button type="submit" class="classy red venti btn_remove_commissioning_body_services">
                        <span class="button-span button-span-red">Remove commissioning body services</span></button>
                    <button type="submit" class="classy green venti btn_cancel_remove_commissioning_body_services">
                        <span class="button-span button-span-green">Cancel</span></button>
                    <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
                         alt="loading..." style="display: none;"/>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('li.even .column_code, li.even .column_name, li.even .column_type, li.even .column_address, li.odd .column_code, li.odd .column_name, li.odd .column_type, li.odd .column_address').click(function (e) {
        e.preventDefault();
        window.location.href = baseUrl + '/<?php echo $base_data_url?>editCommissioningBodyService?commissioning_body_service_id=' + $(this).parent().attr('data-attr-id');
    });

    $('#et_add_commissioning_body_service').click(function (e) {
        e.preventDefault();
        window.location.href = baseUrl + '/<?php echo $base_data_url?>addCommissioningBodyService?' + $(this).data('uri');
    });

    $('#checkall').click(function (e) {
        $('input[name="commissioning_body_service[]"]').attr('checked', $(this).is(':checked') ? 'checked' : false);
    });

    $('#et_delete_commissioning_body_service').click(function (e) {
        e.preventDefault();

        if ($('input[type="checkbox"][name="commissioning_body_service[]"]:checked').length < 1) {
            alert("Please select the commissioning body services you wish to delete.");
            enableButtons();
            return;
        }

        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/<?php echo $base_data_url?>verifyDeleteCommissioningBodyServices',
            'data': $('#admin_commissioning_bodies').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'success': function (resp) {
                var mention = ($('input[type="checkbox"][name="commissioning_body_service[]"]:checked').length == 1) ? 'commissioning body service' : 'commissioning body services';

                if (resp == "1") {
                    enableButtons();

                    $('#confirm_delete_commissioning_body_services').attr('title', 'Confirm delete ' + mention);
                    $('#delete_commissioning_body_services').children('div').children('strong').html("WARNING: This will remove the " + mention + " from the system.<br/><br/>This action cannot be undone.");
                    $('button.btn_remove_commissioning_body_services').children('span').text('Remove ' + mention);

                    $('#confirm_delete_commissioning_body_services').dialog({
                        resizable: false,
                        modal: true,
                        width: 560
                    });
                } else {
                    alert("One or more of the selected commissioning body services are in use and so cannot be deleted.");
                    enableButtons();
                }
            }
        });
    });

    $('button.btn_cancel_remove_commissioning_body_services').click(function (e) {
        e.preventDefault();
        $('#confirm_delete_commissioning_body_services').dialog('close');
    });

    handleButton($('button.btn_remove_commissioning_body_services'), function (e) {
        e.preventDefault();

        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/<?php echo $base_data_url?>deleteCommissioningBodyServices',
            'data': $('#admin_commissioning_body_services').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'success': function (resp) {
                if (resp == "1") {
                    window.location.reload();
                } else {
                    alert("There was an unexpected error deleting the commissioning body services, please try again or contact support for assistance");
                    enableButtons();
                    $('#confirm_delete_commissioning_body_services').dialog('close');
                }
            }
        });
    });
</script>