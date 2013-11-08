<?php
$config = array(
	'commandMap' => array(),
);

$dh = opendir(dirname(__FILE__)."/../commands");

while ($file = readdir($dh)) {
	if (preg_match('/^(.*?)Command\.php$/',$file,$m)) {
		$config['commandMap'][strtolower($m[1])] = array(
			'class' => "application.modules.Reports.commands.{$m[1]}Command",
		);
	}
}

return $config;
