<?php

    $rootdir = '/home/iolmaster/test/testsubdir';
if (!is_dir($rootdir)) {
    mkdir($rootdir);
}
for ($i = 0; $i < 6000; ++$i) {
    $dirname = rand(100000, 999999);
    mkdir($rootdir . '/' . $dirname);
    for ($f = 0; $f < 2; ++$f) {
        $fname = rand(999999, 9999999);
        $testfile = fopen($rootdir . '/' . $dirname . '/' . $fname, 'w');
        fwrite($testfile, $fname);
        fclose($testfile);
    }
}
