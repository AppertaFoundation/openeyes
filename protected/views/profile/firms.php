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
    <h2><?php echo Firm::contextLabel()?>s you work in</h2>
    <form id="profile_firms" method="post" action="/profile/firms">
        <table class="standard">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkall" style="min-width: 20px"  /></th>
                    <th>Name</th>
                    <th>Subspecialty</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($user->firmSelections as $i => $firm) {?>
                <tr data-attr-id="<?php echo $firm->id?>" <?= !$firm->runtime_selectable ? 'class="fade"' : "" ?>>
                    <td><input type="checkbox" name="firms[]" value="<?php echo $firm->id?>" <?= !$firm->runtime_selectable ? "disabled=disabled" : ""; ?> /></td>
                    <td><?php echo $firm->name . (!$firm->runtime_selectable ? " <sup>*</sup>" : ""); ?></td>
                    <td><?php echo $firm->subspecialtyText?></td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </form>
    <p><sup>*</sup> This firm is not selectable.</p>
<div class="data-group">
  <label for="profile_firm_id" class="inline">Add <?php echo strtolower(Firm::contextLabel())?>:</label>
    <?=\CHtml::dropDownList('profile_firm_id', '', $user->getNotSelectedFirmList(), array('empty' => '- Select -'))?>
    <?=\CHtml::link('Add all', '#', array('id' => 'add_all', 'class' => 'field-info button green hint'))?>
</div>
<div class="profile-actions">
    <?php echo EventAction::button('Remove selected ' . strtolower(Firm::contextLabel()), 'delete', array(), array('class' => 'button large hint blue'))->toHtml()?>
</div>

<div class="profile-actions">
    <p>Note: you can also set the sites you work at, <?=\CHtml::link('click here', Yii::app()->createUrl('/profile/sites'))?> to do so.</p>
</div>

<script type="text/javascript">
    $('#profile_firm_id').change(function(e) {
        var firm_id = $(this).val();

        if (firm_id != '') {
            $.ajax({
                'type': 'POST',
                'url': baseUrl+'/profile/addfirm',
                'data': 'firm_id='+firm_id+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
                'success': function(html) {
                    if (html == "1") {
                        window.location.reload();
                    }
                }
            });
        }

        $(this).val('');
    });

    $('#checkall').click(function() {
        $('input[name="firms[]"]').attr('checked',$(this).is(':checked') ? 'checked' : false);
    });

    /**
     *Firm deletion from user profile
     */

    $('#et_delete').click(function(e) {
        e.preventDefault();
        if ($('input[type="checkbox"][name="firms[]"]:checked').length <1) {
            alert("Please select the firms you wish to delete.");
            return;
        }
        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/profile/deleteFirms',
            'data': $('#profile_firms').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(html) {
                if (html === "success") {
                    window.location.reload();
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "There was an unexpected error deleting the firms, please try again or contact support for assistance."
                    }).open();
                }
            },
            'error': function() {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Sorry, There was an unexpected error deleting the firms, please try again or contact support for assistance."
                }).open();
            }
        });
    });




    $('#add_all').click(function() {
        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/profile/addfirm',
            'data': 'firm_id=all&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
            'success': function(html) {
                if (html == "1") {
                    window.location.reload();
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Something went wrong trying to add the firms. Please try again or contact support for assistance."
                    }).open();
                }
            }
        });
    });
</script>
