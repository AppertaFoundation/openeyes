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

<!-- Dialog alert template -->
<script type="text/html" id="dialog-alert-template">
    <p>{{{content}}}</p>
    <div class="buttons">
        <button class="secondary small confirm ok" type="button" data-test="alert-ok">
            OK
        </button>
    </div>
</script>
<!-- Dialog confirm split-view template -->
<script type="text/html" id="dialog-confirm-splitview-template">
    <div class="flex-t">
        <div class="cols-5">{{{leftPanelContent}}}</div>
        <div class="cols-6">
            <p>{{{rightPanelContent}}}</p>
            <div class="popup-actions">
                <button class="{{okButtonClassList}}" type="button">
                    {{{okButton}}}
                </button>
                <button class="{{cancelButtonClassList}}" type="button">
                    {{{cancelButton}}}
                </button>
            </div>
        </div>
    </div>
</script>
<!-- Dialog confirm template -->
<script type="text/html" id="dialog-confirm-template">
    <p>{{{content}}}</p>
    <div class="buttons">
        <button class="{{okButtonClassList}}" type="button">
            {{{okButton}}}
        </button>
        <button class="{{cancelButtonClassList}}" type="button">
            {{{cancelButton}}}
        </button>
    </div>
</script>
<!-- Dialog with pathway step options template -->
<script type="text/html" id="path-step-options-template">
    <div class="block-layout js-step-options">
        {{#itemSets}}
            <div class="js-itemset block{{#display_options}} {{display_options}}{{/display_options}}" data-itemset-id="{{id}}">
                <h3>{{title}}</h3>
                {{#is_form}}
                <fieldset class="btn-list">
                    {{#items}}
                    <label>
                        <input type="radio" name="{{name}}" value="{{id}}"/>
                        <div class="li">{{label}}</div>
                    </label>
                    {{/items}}
                </fieldset>
                {{/is_form}}
                {{^is_form}}
                <table>
                    <tbody>
                    {{#items}}
                    <tr id="{{id}}">
                        <th>{{label}}</th>
                        <td>{{value}}</td>
                    </tr>
                    {{/items}}
                    </tbody>
                </table>
                {{/is_form}}
            </div>
        {{/itemSets}}
    </div>
    <div class="popup-actions flex-right">
        <button class="green hint js-add-pathway">Add to selected patients</button>
        <button class="red hint js-cancel-popup-steps">Cancel</button>
    </div>
</script>
<!-- Dialog with pathway step options template -->
<script type="text/html" id="new-path-step-template">
    <h4>Name</h4>
    <input name="taskname" type="text" maxlength="64" size="66" placeholder="Task name (maximum 64 characters)" data-test="path-step-task-name"/>
    <h4>Step pathway display name (restricted to 16 characters)</h4>
    <input name="shortname" type="text" maxlength="16" size="18" placeholder="Display name"/>
    {{#custom_options}}
    <h4>{{name}}</h4>
    <select class="js-custom-option" name="custom_option_{{id}}">
        {{#option_values}}
        <option value="{{id}}">{{name}}</option>
        {{/option_values}}
    </select>
    {{/custom_options}}
    <div class="popup-actions flex-right">
        <button class="green hint js-add-pathway" disabled="disabled" data-test="path-step-add-pathway">Add to selected patients</button>
        <button class="red hint js-cancel-popup-steps">Cancel</button>
    </div>
</script>
<script type="text/html" id="psd-drug-list-item">
<tr>
    <td>
        <div class="drug">{{drug_name}}</div>
    </td>
    <td>{{dose}}</td>
    <td>
        {{#route}}{{route}}{{/route}}
        {{#laterality}}
        <span class="oe-eye-lat-icons">
            {{#right_eye}}
            <i class="oe-i laterality R small pad"></i>
            {{/right_eye}}
            {{#left_eye}}
            <i class="oe-i laterality L small pad"></i>
            {{/left_eye}}
        </span>
        {{/laterality}}
    </td>
</tr>
</script>
<!-- COMPLog confirm dialog template -->
<script type="text/html" id="dialog-complog-template">
    <p>{{{content}}}</p>
    <table>
        <tbody>
        <tr>
            <td class="fade">Status:</td>
            <td><h4 id="js-complog-status"><i class="spinner as-icon"></i> Launching COMPLog...</h4></td>
        </tr>
        </tbody>
    </table>
    <div class="spacer"></div>
    <div class="buttons flex-layout">
        <button class="large blue hint ok" type="button" style="display: none">
            {{{okButton}}}
        </button>
        <button class="large red hint cancel" type="button">
            {{{cancelButton}}}
        </button>
    </div>
</script>