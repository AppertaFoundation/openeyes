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
<div class="oe-worklists-panel" id="js-worklists-filter-panel">
    <table class="site-context">
        <tbody>
            <tr>
                <td>Institution</td>
                <td>
                    <?php $current_institution = Institution::model()->getCurrent(); ?>
                    <span>
                        <?= $current_institution->name ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>Site</td>
                <td>
                    <select class="cols-full js-worklists-sites">
                        <?php
                            $current_site = Site::model()->getCurrent();
                            $sites = Site::model()->getListForCurrentInstitution();

                        foreach ($sites as $id => $name) : ?>
                            <option value="<?= $id ?>" <?= $current_site->id == $id ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Context</td>
                <td>
                    <select class="cols-full js-worklists-contexts">
                        <option value="all">
                            Any context (Show all pathways)
                        </option>
                        <?php
                            $contexts = Firm::model()->getList($current_institution->id, null, null, true, null, true);

                        foreach ($contexts as $id => $name) : ?>
                            <option value="<?= $id ?>">
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Panel state -->
    <div class="worklist-mode flex-btns">
        <button data-subpanel="lists" class="selected">Lists</button>
        <button data-subpanel="starred"><i class="oe-i star small selected"></i></button>
        <button data-subpanel="recent">Recent</button>
    </div>

    <!-- Lists -->
    <div class="list-manager js-worklist-mode-panel" data-subpanel="lists">
        <div class="set-date-range">
            <div class="date-inputs">
                <?php $date_today = (new DateTime())->format('Y-m-d'); ?>
                <input type="text" size="11" class="date js-filter-date-from" placeholder="from" value="<?= $date_today ?>">
                <input type="text" size="11" class="date js-filter-date-to" placeholder="to">
            </div>
            <fieldset class="js-quick-date">
                <div class="selectors">
                    <label>
                        <input type="radio" value="yesterday" name="quick-selector">
                        <div class="btn">Yesterday</div>
                    </label>
                    <label>
                        <input type="radio" value="today" name="quick-selector" checked="">
                        <div class="btn">Today</div>
                    </label>
                    <label>
                        <input type="radio" value="tomorrow" name="quick-selector">
                        <div class="btn">Tomorrow</div>
                    </label>
                </div>
                <div class="selectors">
                    <label>
                        <input type="radio" value="this-week" name="quick-selector">
                        <div class="btn">This week</div>
                    </label>
                    <label>
                        <input type="radio" value="next-week" name="quick-selector">
                        <div class="btn">Next week</div>
                    </label>
                    <label>
                        <input type="radio" value="next-7-days" name="quick-selector">
                        <div class="btn">+ 7 days</div>
                    </label>
                </div>
            </fieldset>
        </div>

        <!-- List view (shown only if the search returns one or more lists) -->
        <div class="list-view js-worklist-lists-view">
            <button name="all" class="cols-full js-all-lists-btn selected">All</button>
            <div class="worklists">
                <fieldset class="js-list-set">
                </fieldset>
            </div>
        </div>

        <hr class="divider">

        <div class="search-filters">
            <table class="filters">
                <tbody class="js-filters-table">
                    <tr>
                        <th>Lists</th>
                        <td class="js-lists-value">All</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Sort by</th>
                        <td class="js-sort-by-value">Time</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div class="small-row flex-c">
                <button class="adder js-add-select-btn"></button>
            </div>

            <hr class="divider">

            <div class="small-row">
                <label class="highlight">
                    <input class="js-combine-lists-option" value="single-list" type="checkbox">
                    Show patients in combined single list
                </label>
            </div>

            <div class="button-stack">
                <button class="cols-full green hint js-apply-filter-btn">Show patient pathways</button>
            </div>
        </div>
    </div>

    <!-- Starred -->
    <div class="favourites js-worklist-mode-panel" style="display: none" data-subpanel="starred">
        <h3>Favourites will automatically change Site and Context</h3>
    </div>

    <!-- Recent -->
    <div class="favourites js-worklist-mode-panel" style="display: none" data-subpanel="recent">
        <h3>Recents will automatically change Site and Context</h3>
    </div>
</div>

<script type="text/javascript">
     $(document).ready(function() {
         const hidePanelWidth = 1890;
         const button = $('#js-nav-worklist-btn');

         button.click(function() {
             if (button.hasClass('open')) {
                 $('#js-hotlist-panel').show();
                 $('#js-worklists-filter-panel').hide();

                 button.removeClass('open');
             } else {
                 $('#js-hotlist-panel').hide();
                 $('#js-worklists-filter-panel').show();

                 button.addClass('open');
             }
         });

         $('#js-nav-hotlist-btn').click(function() {
             if (button.hasClass('open')) {
                 $('#js-hotlist-panel').show();
                 $('#js-worklists-filter-panel').hide();

                 button.removeClass('open');
             }
         });

         $(window).resize(function() {
             if (button.hasClass('open')) {
                 if ($(this).width() > hidePanelWidth) {
                     $('#js-worklists-filter-panel').show();
                 } else {
                     $('#js-worklists-filter-panel').hide();
                 }
             }
         });

         $('#js-hotlist-panel').hide();
         $('#js-worklists-filter-panel').show();
         button.addClass('open');
     });
</script>

<script type="text/template" id="js-worklist-filter-panel-template-worklist-entry">
    <label>
        <input type="checkbox" value="{{id}}">
        <span class="btn">{{title}}</span>
    </label>
</script>

<script type="text/template" id="js-worklist-filter-panel-template-removable-filter">
    <tr class="js-removeable-filter" data-filter-type="{{type}}">
        <th>{{name}}</th>
        <td>{{value}}</td>
        <td>
              <i class="oe-i remove-circle small js-remove-filter"></i>
        </td>
    </tr>
</script>

<script type="text/template" id="js-worklist-filter-panel-template-named-filter">
    <div class="fav" data-index="{{index}}">
        <div class="details">
            <div class="name">{{name}}</div>
            <div class="js-full-details" style="display:none">
                <div class="site">{{site}}</div>
                <div class="context">{{context}}</div>
                <ul class="lists">
                    {{#lists}}
                    <li>{{title}}</li>
                    {{/lists}}
                </ul>
                <div class="filters">
                    <span class="fade">Filters</span>
                    {{optional}}
                </div>
                <div class="date-range">
                    <span class="fade">For</span>
                    {{period}}
                    <small class="fade">{{periodIncludes}}</small>
                </div>
            </div>
        </div>

        <div class="expand-fav .js-toggle-expand-filter">
            <i class="oe-i expand small"></i>
        </div>

        <div class="remove-fav .js-remove-filter">
            <i class="oe-i remove-circle small"></i>
        </div>
    </div>
</script>

<script type="text/template" id="js-worklist-filter-panel-template-recent-filter">
     <div class="fav" data-index="{{index}}">
        <div class="details">
            <div class="site">{{site}}</div>
            <div class="context">{{context}}</div>
            <ul class="lists">
                {{#lists}}
                <li>{{title}}</li>
                {{/lists}}
            </ul>
            <div class="filters">
                <span class="fade">Filters</span>
                {{optional}}
            </div>
            <div class="date-range">
                <span class="fade">For</span>
                {{period}}
                <small class="fade">{{periodIncludes}}</small>
            </div>
        </div>
    </div>
</script>
