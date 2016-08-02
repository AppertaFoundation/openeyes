<?xml version="1.0" encoding="utf-8" ?>
<linkparameters>
	<security><authentication><?=$authentication_hash?></authentication>
		<timestamp><?=$timestamp?></timestamp>
		<xmlid><?=$message_id?></xmlid>
	</security>
	<revision number="3.1" />
	<applicationid><?=$application_id?></applicationid>
	<user>
		<id><?=$username?></id>
		<name><?=$user_displayname?></name>
	</user>
	<primarylink><process>0</process>
		<uniquereference type="I"><?=$event_id?></uniquereference>
		<typeid><?=$windip_type_id?></typeid>
		<eventdate><?=$event_date?></eventdate>
		<eventtime><?=$event_time?></eventtime>
	</primarylink>
	<additionalindexes count="<?=count($additional_indexes)?>">
		<?php foreach ($additional_indexes as $i=>$index) {?>
		<index_<?=$i+1?>>
			<id><?=$index['id']?></id>
			<value>$index['value']?></value>
		</index_<?=$i+1?>>
		<?php } ?>
</additionalindexes>
<event><status>0</status><failuredescription></failuredescription></event>
</linkparameters>