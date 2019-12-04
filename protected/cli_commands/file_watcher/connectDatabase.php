<?php
    include_once '../../config/local/common.php';

function connectDatabase()
{
    global $config;
    // $config array defined in Yii config file!
    $connectionData = explode(';', str_replace('mysql:', '', $config['components']['db']['connectionString']));

    $mysqli = new mysqli(str_replace('host=', '', $connectionData[0]), $config['components']['db']['username'], $config['components']['db']['password'], str_replace('dbname=', '', $connectionData[2]), str_replace('port=', '', $connectionData[1]));

    if ($mysqli->connect_errno) {
        echo 'Error connecting to database with the following options: '.str_replace('host=', '', $connectionData[0]).' : '.$config['components']['db']['username'].' : '.$config['components']['db']['password'].' : '.str_replace('dbname=', '', $connectionData[2]).' : '.str_replace('port=', '', $connectionData[1]);
        exit(-1);
    }

    return $mysqli;
}
