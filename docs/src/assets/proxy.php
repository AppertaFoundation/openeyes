<?php

/*
 * This proxy exists to allow us to reference protected assets without
 * publishing them through YII. This proxy should only be used for development
 * purposes, and will only run if on localhost.
 */

if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '10.0.2.2') {
	echo "Can only be run on localhost";
	exit(1);
}

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
	echo "File not found";
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
