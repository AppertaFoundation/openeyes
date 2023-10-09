<?php

/**
 * (C) OpenEyes Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2022, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div>
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
);
$mailbox_html_name = CHtml::modelName($mailbox);
?>
    <div>
        <div class="row divider">
            <h2><?php echo $mailbox->id ? 'Edit' : 'Add' ?> shared mailbox</h2>
        </div>

        <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>

        <table class="standard cols-full">
            <colgroup>
                <col class="cols-5">
                <col class="cols-8">
            </colgroup>
            <tbody>
                <tr>
                    <td>Name</td>
                    <td>
                        <?= \CHtml::activeTextField($mailbox, 'name', ['class' => 'cols-full', 'data-test' => 'mailbox-name']) ?>
                    </td>
                </tr>
                <tr>
                    <td>Users</td>
                    <td>
                        <?php $this->widget(
                            'application.widgets.AutoCompleteSearch',
                            ['field_name' => 'user-search', 'htmlOptions' => ['placeholder' => 'Search for user', 'data-test' => 'mailbox-user-search']]
                        ); ?>
                        <div id="users-field">
                            <?php foreach ($selected_users as $user) : ?>
                            <ul class="oe-multi-select inline">
                                <input type="hidden" name="<?= $mailbox_html_name?>[mailboxUsers][]" value="<?= $user->id ?>" />
                                <li>
                                    <?= $user->getFullName() ?>
                                    <i class="oe-i remove-circle small-icon pad-left"></i>
                                </li>
                            </ul>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Teams</td>
                    <td>
                        <?php $this->widget(
                            'application.widgets.AutoCompleteSearch',
                            ['field_name' => 'team-search', 'htmlOptions' => ['placeholder' => 'Search for team', 'data-test' => 'mailbox-team-search']]
                        ); ?>
                        <div id="teams-field">
                            <?php foreach ($selected_teams as $team) : ?>
                            <ul class="oe-multi-select inline">
                                <input type="hidden" name="<?= $mailbox_html_name ?>[mailboxTeams][]" value="<?= $team->id ?>" />
                                <li>
                                    <?= $team->name ?>
                                    <i class="oe-i remove-circle small-icon pad-left"></i>
                                </li>
                            </ul>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Active</td>
                    <td>
                        <?= \CHtml::activeCheckBox($mailbox, 'active', ['data-test' => 'mailbox-active-checkbox']) ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <?= \CHtml::submitButton(
            'Save',
            [
                'class' => 'button large',
                'name' => 'save',
                'id' => 'et_save',
                'data-test' => 'mailbox-save-button'
            ]
        ); ?>
        <?= \CHtml::button(
            'Cancel',
            [
                'data-uri' => '/OphCoMessaging/SharedMailboxSettings',
                'class' => 'button large',
                'name' => 'cancel',
                'id' => 'et_cancel',
                'data-test' => 'mailbox-cancel-button'
            ]
        ); ?>
    </div>
    <?php $this->endWidget() ?>
</div>
<script>
$(document).ready(function() {
    function makeEntry(id, name, type) {
        if (type === 'mailboxUsers' || type === 'mailboxTeams') {
            const field = `<input type="hidden" name="<?= $mailbox_html_name ?>[${type}][]" value="${id}" />`;
            const label = `<li>${name}<i class="oe-i remove-circle small-icon pad-left"></i></li>`;

            return $(`<ul class="oe-multi-select inline">${field}${label}</ul>`);
        }

        return '';
    }

    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#user-search'),
        url: '/user/autocomplete',
        onSelect: function () {
            const AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            const usersField = $('#users-field');

            usersField.append(makeEntry(AutoCompleteResponse.id, AutoCompleteResponse.label, 'mailboxUsers'));

            return false;
        }
    });

    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#team-search'),
        url: '/oeadmin/team/autocomplete',
        onSelect: function () {
            const AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            const teamsField = $('#teams-field');

            teamsField.append(makeEntry(AutoCompleteResponse.id, AutoCompleteResponse.label, 'mailboxTeams'));

            return false;
        }
    });

    $(document).on('click', '.oe-i.remove-circle.small-icon.pad-left', function (e) {
        e.preventDefault();

        const field = $(this).closest('ul');

        field.remove();
    });
});
</script>
