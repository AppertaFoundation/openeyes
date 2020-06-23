<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class GenerateUniqueCodeCommand extends CConsoleCommand
{
    protected $unique_code;

    protected $allowable_character_length;

    protected $limit = 1000;

    const UNIQUE_CODE_LENGTH = 6;

    public function getHelp()
    {
        return "Generate 6 character unique codes to be used in letters.\n";
    }

    /**
     * Generate six characters unique code, and store them into the db. Only generate
     * new codes such that the prescribed number of codes are free upon completion. This
     * is to allow the command to be run every day and only generate codes when necessary.
     */
    public function run($args)
    {
        $this->setAlphabet(
            implode(range('A', 'Z'))
                .implode(range(2, 9))
        );
        if (!empty($args[0])) {
            $this->limit = (int) $args[0];
        }

        $unusedCount = Yii::app()->db->createCommand()
            ->select('count(*)')
            ->from('unique_codes')
            ->leftJoin('unique_codes_mapping', 'unique_code_id=unique_codes.id')
            ->where('unique_codes_mapping.id is null')
            ->andWhere('active = 1')
            ->queryScalar();

        for ($generation = 1; $generation <= ($this->limit - $unusedCount); ++$generation) {
            $values = $this->generate(self::UNIQUE_CODE_LENGTH);
            $this->insert($values);
        }
    }

    /**
     * @param string $alphabet
     */
    public function setAlphabet($alphabet)
    {
        $this->unique_code = $alphabet;
        $this->allowable_character_length = strlen($alphabet);
    }

    public function generate($length)
    {
        $token = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomKey = $this->getRandomInteger(0, $this->allowable_character_length);
            $token .= $this->unique_code[$randomKey];
        }

        return $token;
    }

    /**
     * @param int $min
     * @param int $max
     *
     * @return int
     */
    protected function getRandomInteger($min, $max)
    {
        $range = ($max - $min);

        if ($range < 0) {
            // Not so random...
            return $min;
        }

        $log = log($range, 2);

        // Length in bytes.
        $bytes = (int) ($log / 8) + 1;

        // Length in bits.
        $bits = (int) $log + 1;

        // Set all lower bits to 1.
        $filter = (int) (1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));

            // Discard irrelevant bits.
            $rnd = $rnd & $filter;
        } while ($rnd >= $range);

        return $min + $rnd;
    }

    protected function insert($record)
    {
        try {
            $db = Yii::app()->db;
            $query = 'INSERT INTO '.$db->quoteTableName('unique_codes')." (code) VALUES ('".$record."')";
            $db->createCommand($query)->execute();
        } catch (Exception $e) {
        }
    }
}
