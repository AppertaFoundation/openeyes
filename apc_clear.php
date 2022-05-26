<?php

// Clears the APC cache - used by upgrade scripts.
// To use, call curl http://localhost/apc_clear.php from the command line on the web host
if (in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
    apc_clear_cache();
    apc_clear_cache('user');
    apc_clear_cache('opcode');
    echo ("success\n");
} else {
    header('HTTP/1.0 403 Forbidden');
}
