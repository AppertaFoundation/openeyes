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

function addItem(item){
    var $table = $('#report-drug-list'),
        $tr = $("<tr>",{"id": item.id});
        $td = $("<td>");
        $td_action = $("<td>");
        $span_name = $("<span>",{"class": "drug-name"});
        $a_remove = $("<a>",{"class": "remove"}).append($("<i>", {"class": "oe-i trash"}));
        $hidden = $("<input>",{"type":"hidden", "name":"OphDrPrescription_ReportPrescribedDrugs[drugs][]","value": item.id});
        
        $td.append($span_name.text(item.label));
        $td_action.append($a_remove);
        $td.append($hidden);
        
        $table.find('tbody').append( $tr.append($td).append($td_action) );
}

function getDrugs(subspecialty_id){
    $.ajax({
        'type': 'GET',
        'url': baseUrl+'/OphDrPrescription/report/getDrugsBySubspecialty',
        'data': 'subspecialtyId='+subspecialty_id+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
        'beforeSend': function(){
            $('.select-loader').show();
        },    
        'success': function(response) {
            var data = JSON.parse(response);
            
            $.each(data, function(i, item) {
                addItem(item);
            });
            $('.select-loader').hide();
        }
    });
}

$(document).ready(function(){
    $('#report-drug-list').on('click', '.remove',function(){
        $(this).closest('tr').remove();
        
        if( $('#report-drug-list tbody').find('tr:visible').length < 1){
            $('.no-drugs').show();
        }
        
    });
    
    $('#drug_id').on('change', function(){
        var item,
            value = $(this).val(),
            text = $(this).find('option:selected').text();
        
        if(text !== '-- Select --' && value !== '' && $('#report-drug-list').find('tr#'+value).length == 0){
            $('.no-drugs').hide();
            item = {
                id: $(this).val(),
                label: text
            }; 
            addItem(item);
        }
       
        
    });
    
});