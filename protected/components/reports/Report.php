<?php
/**
 * OpenEyes.
 *
 * 
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class Report
{
    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $to;
    /**
     * @var boolean
     */
    public $allSurgeons;

    /**
     * Template to render.
     *
     * @var string
     */
    protected $template = '//report/plotly_report';

    /**
     * @var CWebApplication
     */
    protected $app;

    /**
     * @var CDbCommand
     */
    protected $command;

    /**
     * @var string
     */
    protected $searchTemplate;

    /**
     * @var int
     */
    protected $surgeon;

    /**
     * @var array
     */
    protected $examinationEvent;

    /**
     * @var array
     */
    protected $series = array();

    /**
     * @var array
     */
    protected $globalGraphConfig = array(
        'credits' => array('enabled' => false),
        'chart' => array('style' => array('fontFamily' => 'Roboto,Helvetica,Arial,sans-serif')),
        'exporting' => array(
            'buttons' => array('contextButton' => array('align' => 'left')),
        ),
    );

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return CWebApplication
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return CDbCommand
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param CDbCommand $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        if ($app->getRequest()->getQuery('from', '')) {
            $from = new DateTime($app->getRequest()->getQuery('from', ''));
            $this->from = $from->format('Y-m-d');
        }

        if ($app->getRequest()->getQuery('to', '')) {
            $to = new DateTime($app->getRequest()->getQuery('to', ''));
            $this->to = $to->format('Y-m-d');
        }

        if ($app->getRequest()->getQuery('allsurgeons','')){
            $this->allSurgeons = true;
        }

        $this->command = $app->db->createCommand();
        $this->surgeon = $app->user->id;
    }

    /**
     * @return array|CDbDataReader|mixed
     */
    protected function getExaminationEvent()
    {
        if (!$this->examinationEvent) {
            $this->examinationEvent = $this->command->select('id')->from('event_type')->where('`name` = "Examination"')->queryRow();
            $this->command->reset();
        }

        return $this->examinationEvent;
    }

    /**
     * @return string
     */
    public function graphId()
    {
        return str_replace('\\', '_', get_called_class());
    }

    /**
     * @return string
     */
    public function getReportTitle()
    {
        return null;
    }
}
