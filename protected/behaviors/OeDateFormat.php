<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Model behavior for OpenEyes standard dates.
 */
class OeDateFormat extends CActiveRecordBehavior
{
    public $date_columns = array();
    public $fuzzy_date_field = 'date';

    /**
     * Converts OE (e.g. 5-Dec-2011) dates to ISO 9075 before save.
     */
    public function beforeSave($event)
    {
        foreach ($this->date_columns as $date_column) {
            $date = $this->Owner->{$date_column};
            if (preg_match(Helper::NHS_DATE_REGEX, $date) && strtotime($date)) {
                $this->Owner->{$date_column} = date('Y-m-d', strtotime($date));
            }
        }
    }

    /**
     * Converts ISO 9075 dates to OE (e.g. 5-Dec-2011) after read from database.
     */
    public function afterFind($event)
    {
        foreach ($this->date_columns as $date_column) {
            $date = $this->Owner->{$date_column};
            // Don't convert blank dates
            if ($date && $date != '0000-00-00') {
                $this->Owner->{$date_column} = date(Helper::NHS_DATE_FORMAT, strtotime($date));
            } else {
                $this->Owner->{$date_column} = '';
            }
        }
    }

    public function getHTMLformatedDate()
    {
        return $this->formatFuzzyDateToHtml();
    }

    /**
     * Converts ISO 9075 dates to an html.
     * @return string
     */
    private function formatFuzzyDateToHtml()
    {
        // get date from model
        $date = $this->Owner->{$this->fuzzy_date_field};

        // check date format
        if (!strtotime($date)) {
            return '';
        }

        // get month and day
        preg_match_all('/\b\d{2}\b/', $date, $matches);
        $month = sizeof($matches[0]) > 0 ? $matches[0][0] : '00';
        $day = sizeof($matches[0]) > 1 ? $matches[0][1] : '00';

        // get year
        preg_match('/\b\d{4}\b/', $date, $matches);
        $year = isset($matches[0]) ? $matches[0] : '00';

        return ($day   !== '00' ? '<span class="day">'.$day.'</span>'   : '') .
               ($month !== '00' ? '<span class="mth">'.date("M", mktime(0, 0, 0, $month, 1)).'</span>' : '') .
               ($year  !== '0000' ? '<span class="yr">'.$year.'</span>' : '');

    }
}
