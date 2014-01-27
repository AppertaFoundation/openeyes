<?php

/*
 * This proxy exists to allow us to reference protected assets without
 * publishing them through YII.
 */

$path = ltrim($_SERVER['PATH_INFO'], '/');
$ext = end(explode('.', $path));

if (!in_array($ext, array('css','js','jpg','gif','png'))) {
	exit(1);
}

$loc = array(__DIR__, '..', '..', '..', 'protected');

$parts = explode('/', $path);
if ($parts[0] !== 'modules') {
	$loc[] = 'assets';
}

$loc[] = $path;
$path = implode(DIRECTORY_SEPARATOR, $loc);
$path = realpath($path);

if (!$path) {
	exit(1);
}

$contents = file_get_contents($path);
$mime = null;

switch($ext) {
	case 'css':
		$mime = 'text/css';
		break;
	case 'js':
		$mime = 'application/javascript';
		break;
	default:
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $path);
		finfo_close($finfo);
		break;
}

header('Content-Type: '.$mime);
header('Content-Length: ' . strlen($contents));
ob_clean();
flush();
print $contents;
exit;
