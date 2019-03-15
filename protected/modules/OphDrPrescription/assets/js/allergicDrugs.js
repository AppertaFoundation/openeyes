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

    function AllergicDrugsController( patientAllergies) {
        this.patientAllergies = patientAllergies;
        this.allergicDrugs = [];
    }

    AllergicDrugsController.prototype.addEntries = function(drugs){
        if (this.patientAllergies) {
            for (let index = 0; index < drugs.length; index++) {
                this.fillAllergicDrugsAndAllergiesArray(drugs[index]);
            }
        }
    };

    AllergicDrugsController.prototype.fillAllergicDrugsAndAllergiesArray = function (drug) {
        if(drug['allergies'] === "") {
            return;
        }
        let controller = this;
        let allergies = [];
        if(typeof drug['allergies'] === "string") {
            allergies = drug['allergies'].split(",");
        }
        else {
            allergies = drug['allergies'];
        }
        allergies.forEach(function (allergy) {
            if (controller.patientAllergies.includes(allergy) && !controller.allergicDrugs.includes(drug['label'])) {
                let nextAllergicDrugIndex = controller.allergicDrugs.length;
                controller.allergicDrugs[nextAllergicDrugIndex] = drug['label'];
            }
        });
    };

    AllergicDrugsController.prototype.getAllergicDrugs = function(){
        if(this.allergicDrugs.length !== 0) {
            return this.allergicDrugs;
        } else {
            return false;
        }
    }

    exports.AllergicDrugsController = AllergicDrugsController;
})(OpenEyes.OphDrPrescription);
