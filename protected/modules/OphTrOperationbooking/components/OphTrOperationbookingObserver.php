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
class OphTrOperationbookingObserver
{
    /**
     * Reset search forms when site / firm changes.
     *
     * @param array $params
     */
    public function resetSearch($params)
    {
        $so = Yii::app()->session['theatre_searchoptions'];
        if (isset($so['firm-id'])) {
            unset($so['firm-id']);
        }
        if (isset($so['specialty-id'])) {
            unset($so['specialty-id']);
        }
        if (isset($so['site-id'])) {
            unset($so['site-id']);
        }
        if (isset($so['date-filter'])) {
            unset($so['date-filter']);
        }
        if (isset($so['date-start'])) {
            unset($so['date-start']);
        }
        if (isset($so['date-end'])) {
            unset($so['date-end']);
        }
        Yii::app()->session['theatre_searchoptions'] = $so;
        Yii::app()->session['waitinglist_searchoptions'] = null;
    }
}
