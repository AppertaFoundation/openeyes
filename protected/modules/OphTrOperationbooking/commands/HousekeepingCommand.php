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
class HousekeepingCommand extends CConsoleCommand
{
    public function getName()
    {
        return 'Housekeeping Command.';
    }

    public function getHelp()
    {
        return "Cancels operations for deceased patients.\n";
    }

    public function run($args)
    {
        $this->deceasedPatients();
    }

    // Check for operations where patient is deceased and cancel them
    protected function deceasedPatients()
    {
        echo 'Cancelling operations for deceased patients: ';

        // TODO: This needs to be made more robust
        $cancellation_reason = OphTrOperationbooking_Operation_Cancellation_Reason::model()->find("text = 'Patient has died'");
        if (!$cancellation_reason) {
            throw new CException('Cannot find cancellation code for "patient has died"');
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('`t`.status_id', array(1, 2, 3, 4));
        $criteria->addCondition('is_deceased = 1 and session_date > :dateNow');
        $criteria->params[':dateNow'] = date('Y-m-d');

        foreach (Element_OphTrOperationbooking_Operation::model()
            ->with(array(
                'event' => array(
                    'with' => array(
                        'episode' => array(
                            'with' => 'patient',
                        ),
                    ),
                ),
                'booking',
            ))
            ->findAll($criteria) as $operation) {
            $operation = Element_OphTrOperationbooking_Operation::model()->findByPk($operation['id']);
            $operation->cancel($cancellation_reason->id, 'Booking cancelled automatically', 1);

            echo '.';
        }

        echo "\n";
    }
}
