<?xml version="1.0" encoding="utf-8" ?><linkparameters> <security><authentication>#AuthenticationHash#</authentication> <timestamp>2007-11-13 15:44:36</timestamp><xmlid>5E6B4D07-636C-4e23- 94C6802E9D1842B9</xmlid></security><revision number="3.1" /><applicationid>FE6164CC-3EDA-405c-8A59- 2F6358B21AF9</applicationid><user><id>PCN</id><name>Norton, Paul</name></user><primarylink><process>0</process> <uniquereference type="I">UQ17357239946</uniquereference><typeid>REF01</typeid> <eventdate>2007-03-29</eventdate><eventtime>13:27:21</eventtime></primarylink> <additionalindexes count="2"><index_1><id>DocumentID</id><value>DOC12345678</value> </index_1><index_2><id>Client Name</id><value>Gateway Computing Ltd</value></index_2> </additionalindexes><event><status>0</status><failuredescription></failuredescription></event> </linkparameters>

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