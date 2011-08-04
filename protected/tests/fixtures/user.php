<?php

return array(
	'user1' => array(
		'username' => 'JoeBloggs',
		'first_name' => 'Joe',
		'last_name' => 'Bloggs',
		'email' => 'joe@bloggs.com',
		'active' => 1,
		'salt' => 'qWQJaOT4Kz',
		'password' => '4a3de11333d5814d90270c27116f1bdc', // pw: secret,
		'global_firm_rights' => 1
	),
	'user2' => array(
		'username' => 'JaneBloggs',
		'first_name' => 'Jane',
		'last_name' => 'Bloggs',
		'email' => 'jane@bloggs.com',
		'active' => 1,
		'salt' => '4d36ed1c4a',
		'password' => '3f3819bcd2ed9d433e2dc26c5da82ae9', // pw: password
		'global_firm_rights' => 0
	),
	'user3' => array(
		'username' => 'icabod',
		'first_name' => 'icabod',
		'last_name' => 'icabod',
		'email' => 'icabod@icabod.com',
		'active' => 0,
		'salt' => '4d36f32441',
		'password' => '19187c5d5985482d352a9d6ffa1d6759', // pw: 12345
		'global_firm_rights' => 1
	),
	'admin' => array(
		'username' => 'admin',
		'first_name' => 'Admin',
		'last_name' => 'User',
		'email' => 'admin@mail.com',
		'active' => 1,
		'salt' => 'FbYJis0YG3',
		'password' => '44e327c6e513ecd64d050e29678bf8a6', // pw: 54321
		'global_firm_rights' => 0
	),
);
