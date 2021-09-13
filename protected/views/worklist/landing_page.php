<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="oe-full-header">
    <div class="title wordcaps">Setup worklists</div>
</div>
<main class="oe-full-content oe-clinic" id="js-clinic-manager-landing">
    <div class="oec-help">
        Setup your lists and filter the patients as required. <br>
        Once setup all your settings can be saved as a favourite to allow quick access to your most common list use configurations.

        <div class="help-note" style="left:182px; top:110px;">
            <i class="oe-i direction-up medium pad selected"></i>
            Use the <i class="oe-i starline small pad no-click"></i> icon to save a configuration in your favourites
        </div>
        <div class="help-note" style="top:240px; right:42vw">
            Set list dates as relative or specific.
            <i class="oe-i direction-right medium pad selected"></i>
        </div>
        <div class="help-note" style="top:320px; right:42vw">
            Apply any extra search filters before loading lists
            <i class="oe-i direction-right medium pad selected"></i>
        </div>
        <div class="help-note" style="top:394px; right:42vw">
            Multiple patient lists can be displayed as a single list
            <i class="oe-i direction-right medium pad selected"></i>
        </div>
    </div>
</main>

<script type="text/javascript">
    $(document).ready(function () {
        // Set up a dummy client side controller for filters
        // Used until the initial filter is applied, where it switches to the index page proper
        const worklists = <?= json_encode(array_map(static function ($worklist) {
            return ['id' => $worklist->id, 'title' => $worklist->name];
                          }, $worklists)) ?>;

        const usersList = <?= json_encode(array_map(static function ($user) {
            return ['id' => $user->id, 'label' => $user->getFullName() . ' (' . $user->getInitials() .')'];
                          }, User::model()->findAll())) ?>;

        const stepsList = <?= json_encode(array_map(static function ($step_type) {
            return ['id' => $step_type->id, 'label' => $step_type->long_name];
                          }, PathwayStepType::model()->findAll())) ?>;

        const controllerOptions = {
            worklistFilterPanelSelector: '#js-worklists-filter-panel',
            saveFilterPopupSelector: '#js-worklist-save-filter-popup',
            saveFilterPopupButtonSelector: '.oe-header .js-favourite',

            worklists: worklists,
            users: usersList,
            steps: stepsList,

            applyFilter: function() { window.location.reload(); },
        };

        new OpenEyes.WorklistFiltersController(controllerOptions);
    });
</script>
