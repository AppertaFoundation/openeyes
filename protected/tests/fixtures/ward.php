<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/


return array(
	'ward1' => array(
		'site_id' => 1,
		'name' => 'Male Childrens Ward',
		'restriction' => Ward::RESTRICTION_MALE + Ward::RESTRICTION_UNDER_16
	),
	'ward2' => array(
		'site_id' => 1,
		'name' => 'Female Childrens Ward',
		'restriction' => Ward::RESTRICTION_FEMALE + Ward::RESTRICTION_UNDER_16
	),
	'ward3' => array(
		'site_id' => 1,
		'name' => 'Male Adult Ward',
		'restriction' => Ward::RESTRICTION_MALE + Ward::RESTRICTION_ATLEAST_16
	),
	'ward4' => array(
		'site_id' => 1,
		'name' => 'Female Adult Ward',
		'restriction' => Ward::RESTRICTION_FEMALE + Ward::RESTRICTION_ATLEAST_16
	),
	'ward5' => array(
		'site_id' => 2,
		'name' => 'Other Site Ward',
		'restriction' => 0
	),
);