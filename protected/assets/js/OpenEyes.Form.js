/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
(function(exports) {

    /**
     * OpenEyes Form module
     * @namespace OpenEyes.Util
     * @memberOf OpenEyes
     */
    var Form = {};
    Form.editedForms = [];

    /**
     * Get the count of forms currently being edited
     *
     * @returns {number}
     */
    Form.editedCount = function(){
        var count = 0;
        for(var i in Form.editedForms){
            if(Form.editedForms.hasOwnProperty(i)){
                count++;
            }
        }
        return count;
    };

    /**
     * Reset the form. Editing has been cancelled.
     *
     * @param $resetForm
     */
    Form.reset = function($resetForm){
        for(var i in Form.editedForms){
            if(Form.editedForms.hasOwnProperty(i) && i === $resetForm.attr('id')){
                delete Form.editedForms[i];
            }
        }
        $resetForm[0].reset();
        if(Form.editedCount() === 0){
            window.formHasChanged = false;
        }
    };

    /**
     * Mark the form as being edited.
     *
     * @param $resetForm
     */
    Form.edit = function($resetForm){
        Form.editedForms[$resetForm.attr('id')] = $resetForm.attr('id');
        window.formHasChanged = true;
    };

    exports.Form = Form;

}(this.OpenEyes));