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
class ReportController extends BaseReportController
{
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('injections', 'runReport', 'downloadReport', 'ArvoPresentation'),
                'expression' => array('ReportController', 'checkSurgonOrRole'),
            ),
            array('deny'),
        );
    }

    public function actionInjections()
    {
        Audit::add('Reports', 'view', print_r(['report-name' => 'Intravitreal Injection'], true));
        $this->pageTitle = 'Intravitreal Injections report';
        $this->render('injections');
    }

    public function actionArvoPresentation($startDate, $endDate)
    {
        // this is a fixed report for now, need to be updated

        $leftSummary = array('superior' => 0, 'inferior' => 0, 'neutral' => 0);
        $rightSummary = array('superior' => 0, 'inferior' => 0, 'neutral' => 0);

        $dataValues = Yii::app()->db->createCommand("SELECT left_eyedraw, right_eyedraw
														FROM et_ophtrintravitinjection_anteriorseg eoa
														JOIN event e ON e.id=eoa.event_id
														WHERE e.event_date>='".$startDate."'
																AND e.event_date<='".$endDate."'")->queryAll();
        foreach ($dataValues as $data) {
            $jsonDataLeft = json_decode($data['left_eyedraw']);
            $jsonDataRight = json_decode($data['right_eyedraw']);

            if (isset($jsonDataLeft) && isset($jsonDataLeft[count($jsonDataLeft) - 1]->rotation)) {
                ++$leftSummary[$this->getArvo($jsonDataLeft[count($jsonDataLeft) - 1]->rotation)];
            }
            if (isset($jsonDataRight) && isset($jsonDataRight[count($jsonDataRight) - 1]->rotation)) {
                ++$rightSummary[$this->getArvo($jsonDataRight[count($jsonDataRight) - 1]->rotation)];
            }
        }

        echo 'Dates: '.$startDate.' - '.$endDate;
        echo '<table>';
        echo '<tr><th></th><th>Left</th><th>Right</th></tr>';
        echo '<tr><td>Inferior:</td><td>'.$leftSummary['inferior'].'</td><td>'.$rightSummary['inferior'].'</td></tr>';
        echo '<tr><td>Superior:</td><td>'.$leftSummary['superior'].'</td><td>'.$rightSummary['superior'].'</td></tr>';
        echo '<tr><td>Neutral (90, 270):</td><td>'.$leftSummary['neutral'].'</td><td>'.$rightSummary['neutral'].'</td></tr>';
        echo '</table>';
    }

    private function getArvo($rotation)
    {
        if ($rotation > 90 && $rotation < 270) {
            return 'inferior';
        } elseif ($rotation > 270 || $rotation < 90) {
            return 'superior';
        } else {
            return 'neutral';
        }
    }
}
