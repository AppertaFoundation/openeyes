<?php

    // we try to open the generic configuration file from /etc/openeyes, and if it's not found there than we will use the values from here!
if (file_exists('/etc/openeyes/file_watcher.conf') && !$dicomConfig = parse_ini_file('/etc/openeyes/file_watcher.conf', true)) {
    $dicomConfig = array(
        'general' => array('PHPdir' => '/var/www/openeyes/protected/cli_commands/file_watcher',
                    'javaCommandDir' => '../../javamodules/IOLMasterImport', ),
        'biometry' => array(
                'inputFolder' => '/home/vagrant/test_watch',
                'importerCommand' => 'cd ../../javamodules/IOLMasterImport && java -cp ./lib/antlr-2.7.7.jar:./lib/dcm4che-core-3.3.7.jar:./lib/dom4j-1.6.1.jar:./lib/geronimo-jta_1.1_spec-1.1.1.jar:./lib/hibernate-commons-annotations-5.0.0.Final.jar:./lib/hibernate-core-5.0.0.Final.jar:./lib/hibernate-jpa-2.1-api-1.0.0.Final.jar:./lib/jandex-1.2.2.Final.jar:./lib/javassist-3.18.1-GA.jar:./lib/jboss-logging-3.3.0.Final.jar:./lib/log4j-1.2.17.jar:./lib/slf4j-api-1.7.5.jar:./lib/slf4j-log4j12-1.7.5.jar:./lib/mysql-connector-java-5.1.23-bin.jar:./lib/json-simple-1.1.1.jar:./lib/commons-cli-1.3.1.jar:./lib/jaxen-1.1.6.jar:./lib/ini4j-0.5.4.jar:./OE_IOLMasterImport.jar uk.org.openeyes.OE_IOLMasterImport -c /etc/openeyes/db.conf -d -f ',
            ),
    );
}
