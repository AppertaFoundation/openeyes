<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ResendTherapyapplicationsCommand extends CConsoleCommand
{
    public $defaultAction = 'resend';

    public function getName()
    {
        return 'resendtherapyapplications';
    }

    public function getHelp()
    {
        return $this->getName().":\n\n". implode(' ', $this->getOptionHelp())."\n\n".<<<EOH
Will archive therapy application emails that have been sent since the given date, and generate 
URLs for a logged in user to visit to trigger the re-sending of the email.

EOH;
    }

    /**
     * @var DefaultController
     */
    private $controller;

    protected function getController()
    {
        if (!$this->controller) {
            $this->controller = new DefaultController("Default");
        }
        return $this->controller;
    }

    /**
     * @param $since
     * @return Event[]
     */
    protected function getSubmittedApplications($since)
    {
        return TherapyApplicationEvents::getEventsByStatus(
            OphCoTherapyapplication_Processor::STATUS_SENT,
            $since
        );
    }

    /**
     * @param $events
     */
    protected function reprocessApplications($events, $baseurl)
    {
        foreach ($events as $event) {
            // archive the originally sent email
            OphCoTherapyapplication_Email::model()->archiveForEvent($event);
            // generate link for resending the application
            echo $baseurl . "/OphCoTherapyapplication/default/processApplication/event_id/" . $event->id . "\n";
        }
    }


    /**
     * @param $since
     * @return string
     */
    protected function formatDate($since)
    {
        if (preg_match('/^\d\d\d\d-\d\d-\d\d$/', $since) !== 1) {
            $this->usageError('Incorrect format for date ' . $since . ' should yyyy-mm-dd');
        }
        return $since . ' 00:00:00';
    }

    public function actionResend($since, $baseurl = 'http://openeyes.dev', $dryrun = false)
    {

        $events = $this->getSubmittedApplications($this->formatDate($since));
        echo "There are " . count($events) . " applications that have been submitted since " . $since . "\n";
        if (!$dryrun) {
            echo "Resetting status for applications, please click on the following links:\n";
            $this->reprocessApplications($events, $baseurl);
        } else {
            echo "I have not reset their status\n";
        }
    }

    public function beforeAction($action, $params)
    {
        Yii::import('application.modules.OphCoTherapyapplication.components.*');
        Yii::import('application.modules.OphCoTherapyapplication.services.*');
        Yii::import('application.modules.OphCoTherapyapplication.controllers.DefaultController');
        return parent::beforeAction($action, $params);
    }
}
