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
class PendingApplications
{
    /**
     * Gets all pending applications from the database.
     *
     * @return mixed
     */
    protected function getPendingApplications()
    {
        $command = Yii::app()->db->createCommand();

        return $command->select('patient.hos_num as HosNum,
                            event.id as EventID,
                            firm.name as FirmName,
                            CONCAT_WS(" ", user.first_name, user.last_name) as Username,
                            event.created_date as Created,
                            event.last_modified_date as Modified')
            ->from('event')
            ->join('episode', 'event.episode_id = episode.id')
            ->join('patient', 'episode.patient_id = patient.id')
            ->join('firm', 'episode.firm_id = firm.id')
            ->join('user', 'event.created_user_id = user.id')
            ->join('event_type', 'event.event_type_id = event_type.id AND event_type.name = "Therapy Application"')
            ->leftJoin('ophcotherapya_email', 'event.id = ophcotherapya_email.event_id')
            ->where('ophcotherapya_email.event_id IS NULL')
            ->queryAll();
    }

    /**
     * Generates and returns a CSV containing all pending therapy applications.
     *
     * @return string
     */
    protected function pendingApplicationsCSV()
    {
        $lines = $this->getPendingApplications();
        if (!count($lines)) {
            return '';
        }
        $handle = fopen('php://memory', 'w');
        $header = false;

        foreach ($lines as $line) {
            if (!$header) {
                fputcsv($handle, array_keys($line));
                $header = true;
            }
            fputcsv($handle, $line);
        }

        fseek($handle, 0);

        return stream_get_contents($handle);
    }

    /**
     * Email the CSV to the given recipients.
     *
     * @param $recipients
     *
     * @return array|string array of recipients or ; delimited list
     */
    public function emailCsvFile($recipients)
    {
        if (!is_array($recipients)) {
            $recipients = explode(';', $recipients);
        }
        $csv = $this->pendingApplicationsCSV();

        $message = Yii::app()->mailer->newMessage();
        $message->setFrom(array('noreply@openeyes.org.uk' => 'OpenEyes Reports'));
        $message->setTo($recipients);
        $message->setSubject('Pending Therapy Applications Report');
        $message->setBody('Your report on currently pending Therapy Applications');
        $message->attach(Swift_Attachment::newInstance($csv, 'PendingApplications.csv', 'text/csv'));

        return Yii::app()->mailer->sendMessage($message);
    }

    /**
     * Generate the CSV and send it to the browser.
     */
    public function downloadCsvFile()
    {
        $csv = $this->pendingApplicationsCSV();

        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename='PendingApplications.csv'");
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $csv;
    }
}
