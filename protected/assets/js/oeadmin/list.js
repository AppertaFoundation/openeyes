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

$(document).ready(function () {
  var $genericLists = $('#generic-admin-list .sortable, #generic-admin-sublist .sortable');
  OpenEyes.Admin.cacheList($genericLists);
  $genericLists.sortable({
    stop: OpenEyes.Admin.saveSorted
  });

  $('table').on('click', 'tr.clickable', function (e) {

    var target = $(e.target);

    // If the user clicked on an input element, or if this cell contains an input
    // element then do nothing.
    if (target.is(':input') || (target.is('td') && target.find('input').length)) {
      return;
    }

    var uri = $(this).data('uri');

    if (uri) {
      var url = uri.split('/');
      url.unshift(baseUrl);
      window.location.href = url.join('/');
    }
  });

  $('#diagnosis-search').on('click', '#clear-diagnosis-widget', function() {
    $('#enteredDiagnosisText').hide();
    $('#savedDiagnosis').val('');
  });
});