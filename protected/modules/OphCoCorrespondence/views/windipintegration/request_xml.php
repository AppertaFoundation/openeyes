<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?php echo '<?xml version="1.0" encoding="utf-8" ?>'; ?>
<linkparameters>
    <security><authentication><?=$authentication_hash?></authentication>
        <timestamp><?=$timestamp?></timestamp>
        <xmlid><?=$message_id?></xmlid>
    </security>
    <revision number="4.2" />
    <applicationid><?=$application_id?></applicationid>
    <user>
        <id><?=$username?></id>
        <name><?=$user_displayname?></name>
    </user>
    <primarylink>
        <process>2</process>
        <uniquereference type="I"><?=$event_id?></uniquereference>
        <?php if (!$is_new_event): ?>
            <uniquereference2></uniquereference2>
            <typeid></typeid>
        <?php else: ?>
            <typeid><?=$windip_type_id?></typeid>
        <?php endif; ?>
        <eventdate><?=$event_date?></eventdate>
        <eventtime><?=$event_time?></eventtime>
    </primarylink>
    <additionalindexes count="<?=count($additional_indexes)?>">
        <?php foreach ($additional_indexes as $i=>$index) {?>
            <index_<?=$i+1?>>
                <id><?=$index['id']?></id>
                <value><?=$index['value']?></value>
            </index_<?=$i+1?>>
        <?php } ?>
    </additionalindexes>
    <event><status>0</status><failuredescription></failuredescription></event>
</linkparameters>