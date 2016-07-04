/**
 * OpenEyes
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @package OpenEyes
* @link http://www.openeyes.org.uk
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
*/

function addItem(item){
    var $table = $('#report-drug-list'),
        $tr = $("<tr>",{"id": item.id});
        $td = $("<td>");
        $span_name = $("<span>",{"class": "drug-name"});
        $span_remove = $("<a>",{"class": "remove right"}).text("remove");
        $hidden = $("<input>",{"type":"hidden", "name":"OphDrPrescription_ReportPrescribedDrugs[attributes][drugs][]","value": item.id});
        
        $td.append($span_name.text(item.label));
        $td.append($span_remove);
        $td.append($hidden);
        
        $table.append( $tr.append($td) );
}

$(document).ready(function(){
    $('#report-drug-list').on('click', '.remove',function(){
        $(this).closest('tr').remove();
    });
});