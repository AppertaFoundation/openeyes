<?php
return array(
	'params'=>array(
		// application-level parameters that can be accessed
		// using Yii::app()->params['paramName']
		'auth_source' => 'BASIC', // Options are BASIC or LDAP.
		'use_pas' => false, // If false there are no referrals, and patient details are from the OE DB only.
		// this is used in contact page
		'adminEmail' => 'webmaster@example.com',
		'ldap_server' => '',
		'ldap_port' => '',
		'ldap_admin_dn' => 'uid=admin,ou=system',
		'ldap_password' => '',
		'ldap_dn' => 'ou=people,o=openeyes',
		'adminEmail'=>'webmaster@example.com',
		// if true or not present, patient details are pseudonymised. This must be present and set to false to store real patient data
		'pseudonymise_patient_details' => true,
		'environment' => 'live', // can be dev, training, live etc
		'google_analytics_account' => '',
		'watermark' => '',
		'helpdesk_email' => '',
		'helpdesk_phone' => ''
	)
);
