/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

describe('Behaviour of worklist filters', () => {
    it('does not display duplicates of worklist names in the filter panel quick selector or lists section in the adder', () => {
        cy.login();

        cy.runSeeder('', 'WorklistFilteringSeeder').then((seederData) => {
            cy.login(seederData.user.username, seederData.user.password, seederData.site.id, seederData.institution.id);

            cy.visitWorklist();
            cy.openWorklistNavBar();

            cy.getBySel('worklist-filter-period-quick-selector-button', '[data-period="next-7-days"]').click();

            cy.intercept('/worklist/AutoRefresh').as('AutoRefresh');

            cy.getBySel('show-patient-pathways').scrollIntoView().click();

            cy.wait('@AutoRefresh').then(() => {
                // Hard refresh of page to avoid issues with DOM updates taking place after the tests below
                cy.visitWorklist();
                cy.openWorklistNavBar();

                cy.getBySel('worklist-filter-worklist-quick-selector-button').should('have.length', seederData.definitions.length);
                cy.getBySel('add-options', '[data-id="js-wfp-lists"]').find('li[data-id!="all"]').should('have.length', seederData.definitions.length);
            });
        });
    });
});
