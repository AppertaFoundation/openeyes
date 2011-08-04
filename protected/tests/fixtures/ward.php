<?php

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
	)
);