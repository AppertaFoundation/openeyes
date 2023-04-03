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
    $assigned_users = $team->users ? : $team->temp_users ? : array();
    $assigned_teams = $team->childTeams ? : $team->temp_child_teams ? : array();

    $assigned_user_ids = array_map(function ($assigned_user) {
        return $assigned_user->id;
    }, $assigned_users);
    $assigned_users = $assigned_user_ids ? $this->api->getInstitutionUserAuth(true, $assigned_user_ids) : array();
    $assigned_users = array_map(function ($assigned_user) {
        return $assigned_user->user->getUserPermissionDetails();
    }, $assigned_users);
    ?>
<h2><?=$title_action?> Team</h2>

<?php
    echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors));
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#name',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 4,
            ),
        ]
    );

    $can_change_activation = $team->isNewRecord
                           || $this->checkAccess('OprnSetTeamActivation', $team->id);
    ?>
<div class="row divider">
    <div class="cols-full">
        <table class="large">
            <tbody>
                <tr>
                    <td>Team Name*</td>
                    <td>
                        <?=
                            \CHtml::activeTextField(
                                $team,
                                'attributes[name]',
                                [
                                    'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                                    'class' => 'cols-full',
                                    'data-test' => 'team-name',
                                ]
                            )
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>
                        <?=
                            \CHtml::activeTextField(
                                $team->contact ? : new Contact(),
                                'attributes[email]',
                                [
                                    'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                                    'class' => 'cols-full',
                                    'data-test' => 'team-email',
                                ]
                            )
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Active*</td>
                    <td>
                        <?php
                        if (!$can_change_activation) {
                            echo CHtml::activeHiddenField($team, 'attributes[active]');
                        }
                        ?>
                        <?=
                            \CHtml::activeCheckBox(
                                $team,
                                'attributes[active]',
                                [
                                    'checked' => $team->isNewRecord ? true : $team->active,
                                    'data-test' => 'team-active',
                                    'disabled' => !$can_change_activation,
                                ]
                            )
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
    $this->renderPartial('/default/_user_team_assignment', array('team' => $team, 'super_user' => $super_user, 'users' => $users, 'assigned_users' => $assigned_users, 'prefix' => $prefix));
if ($team->is_childTeam) {
    ?>
<div class="row divider">
    <div class="cols-12">
        Cannot assign child teams to this team, as it is associated to the following teams: <?=$team->getParentTeamLinks()?>
    </div>
</div>
<?php } else {
        $this->renderPartial('/default/_team_assignment', array('teams' => $teams, 'assigned_teams' => $assigned_teams, 'prefix' => $prefix, 'current_team' => $team));
}
    echo \OEHtml::submitButton();
    echo \OEHtml::cancelButton(
        "Cancel",
        [
            'data-uri' => $cancel_url,
        ]
    );
    $this->endWidget();
    ?>
