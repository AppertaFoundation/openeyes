/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = OpenEyes || {};
OpenEyes.OphDrPrescription = OpenEyes.OphDrPrescription || {};

(function (exports) {

    function AllergicDrugsController(drugs, patientAllergies) {
        this.drugs = drugs;
        this.patientAllergies = patientAllergies;
        this.init();
    }


    AllergicDrugsController.prototype.init = function () {
        let controller = this;
        let allergyCounter = 0;
        this.allergicDrugs = [];
        this.allergiesFromAllergicDrugs = [];
        if (this.patientAllergies) {
            for (let index = 0; index < this.drugs.length; index++) {
                controller.fillAlergicDrugsAndAllergiesArray(index, controller, allergyCounter);
            }
        }
    };

    AllergicDrugsController.prototype.fillAlergicDrugsAndAllergiesArray = function (index, controller, allergyCounter) {
        this.drugs[index]['allergies'].forEach(function (allergy) {
            if (patientAllergies.includes(allergy)) {
                controller.allergicDrugs[index] = controller.drugs[index]['label'];
                if (controller.allergiesFromAllergicDrugs.indexOf(allergy) < 0) {
                    controller.allergiesFromAllergicDrugs[allergyCounter] = allergy;
                    allergyCounter++;
                }
            }
        });
    };

    AllergicDrugsController.prototype.getAllergicDrugsWithAllergies = function () {
        return {
            'drugs': this.allergicDrugs,
            'allergies': this.allergiesFromAllergicDrugs
        };
    };

    exports.AllergicDrugsController = AllergicDrugsController;
})(OpenEyes.OphDrPrescription);
