<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?php echo '<?xml version="1.0" encoding="utf-8" ?>'; ?>
<linkparameters>
    <security>
        <authentication><?=$authentication_hash?></authentication>
        <timestamp><?=$timestamp?></timestamp>
        <xmlid><?=$message_id?></xmlid>
    </security>
    <revision number="4.2" />
    <applicationid><?=$application_id?></applicationid>
    <user><id>LLOYD</id>
        <name>Lloyd Gilbert</name>
    </user>
    <primarylink>
        <process>0</process>
        <uniquereference type="I">2222222</uniquereference>
        <typeid></typeid>
        <eventdate><?=$event_time?></eventdate>
        <eventtime><?=$event_time?></eventtime>
        <OUID>DID1</OUID></primarylink>
    <additionalindexes count="1">
        <index_1><id>DepartmentID</id>
            <value>DID1</value>
        </index_1></additionalindexes>
    <event>
        <status>0</status>
        <failuredescription></failuredescription>
    </event>
    <automation>
        <application>
            <sb value="1" />
            <tb value="1" />
            <ub value="1" />
            <db value="1" />
            <mb value="0" />
            <rm value="0" />
            <cm value="0" />
            <em value="0" />
        </application>
        <formstyle>
            <caption value="1" />
        </formstyle>
        <viewer>
            <showdetail value="1" />
            <icons value="0" />
            <position value="0" />
            <buttons value="111111111111" />
        </viewer>
    </automation>
</linkparameters>