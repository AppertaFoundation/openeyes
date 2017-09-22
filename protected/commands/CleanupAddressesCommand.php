<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class CleanupAddressesCommand extends CConsoleCommand
{
    const DUMP_FILE = 'orphaned_addresses.csv';
    const BATCH_SIZE = 128;

    public function getHelp()
    {
        return "Remove orphaned entries from the address table.\nThe data removed is dumped to a file named ".self::DUMP_FILE.".\n";
    }

    public function run($args)
    {
        $f = fopen(self::DUMP_FILE, 'a+');
        if (!$f) {
            die('Failed to open '.self::DUMP_FILE." for writing\n");
        }

        $db = Yii::app()->db;

        $cmd = $db->createCommand()->select('a.*')
            ->from('address a')->leftJoin('contact c', "a.parent_class = 'Contact' and a.parent_id = c.id")
            ->where('c.id is null')->limit(self::BATCH_SIZE);

        while (1) {
            $tx = $db->beginTransaction();

            if (!($rows = $cmd->queryAll())) {
                $tx->commit();
                break;
            }

            echo 'Deleting '.count($rows)." rows...\n";

            $ids = array();
            foreach ($rows as $row) {
                if (!fputcsv($f, $row)) {
                    die("Failed to write CSV row\n");
                }

                $ids[] = $row['id'];
            }
            if (!fflush($f)) {
                die('Flush failed');
            }

            $db->createCommand()->delete('address', array('in', 'id', $ids));

            $tx->commit();
            sleep(1);
        }

        fclose($f);

        echo "Done\n";
    }
}
