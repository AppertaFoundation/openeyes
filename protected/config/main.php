<?php

$config = CMap::mergeArray(
	require(dirname(__FILE__).'/common.php'),
	array(
	)
);

// Check for local main config
$local_main = dirname(__FILE__).'/local/main.php';
if(file_exists($local_main)) {
	$config = CMap::mergeArray(
		$config,
		require($local_main)
	);
}

return $config;