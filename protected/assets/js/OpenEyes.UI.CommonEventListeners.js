/**
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
(function(exports) {

    'use strict';

    /**
     * OpenEyes CommonEventListeners module
     * @namespace OpenEyes.UI.CommonEventListeners
     * @memberOf OpenEyes
     */
    const CommonEventListeners = {};

    CommonEventListeners.updateSitesByInstitution = async function ($institution_dropdown, $site_dropdown) {

        if (!$institution_dropdown || !$site_dropdown) {
            return false;
        }

        OpenEyes.UI.DOM.addEventListener($institution_dropdown, 'change', null, async function(e) {
            const empty_text = $site_dropdown.querySelector("option[value='']").textContent;
            const institution_id = this.value;
            try {
                let response;
                if (institution_id !== '') {
                    response = await fetch(`${baseUrl}/admin/getInstitutionSites?institution_id=${institution_id}`);
                }

                if (response && response.ok) {
                    const sites = await response.json();
                    const options = Object.entries(sites).map(([value, text]) => `<option value="${value}">${text}</option>`).join('');
                    $site_dropdown.innerHTML = `<option value="">${empty_text}</option>${options}`;
                } else {
                    $site_dropdown.innerHTML = `<option value="">${empty_text}</option>`;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    };

    CommonEventListeners.updateFirmByInstitution = async function ($institution_dropdown, $firm_dropdown) {

        if (!$institution_dropdown || !$firm_dropdown) {
            return false;
        }

        OpenEyes.UI.DOM.addEventListener($institution_dropdown, 'change', null, async function(e) {
            const empty_text = $firm_dropdown.querySelector("option[value='']").textContent;
            const institution_id = this.value;
            try {
                let response;
                if (institution_id !== '') {

                    response = await fetch(`/OphCiExamination/admin/getInstitutionFirms/${institution_id}`);
                }

                if (response && response.ok) {
                    const firms = await response.json();
                    const options = firms
                        .map((firm) => `<option value="${firm.id}">${firm.name}</option>`)
                        .join('');
                    $firm_dropdown.innerHTML = `<option value="">${empty_text}</option>${options}`;
                } else {
                    $firm_dropdown.innerHTML = `<option value="">${empty_text}</option>`;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    };

    CommonEventListeners.onClickTableRow = function (table) {
        OpenEyes.UI.DOM.addEventListener(table, 'click', 'tr', function() {
            window.location.href = this.dataset.redirectTo;
        });
    };

    exports.CommonEventListeners = CommonEventListeners;

}(this.OpenEyes.UI));
