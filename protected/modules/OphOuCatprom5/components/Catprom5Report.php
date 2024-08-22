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

namespace OEModule\OphOuCatprom5\components;
/**
 *
 * @property string $searchTemplate
 * @property int $mode
 * @property int $eye
 * @property array $plotlyConfig
 */
class Catprom5Report extends \Report implements \ReportInterface
{
    protected $searchTemplate = 'application.modules.OphOuCatprom5.views.report.catprom5_search';
    protected $mode;
    protected $eye;

    protected $plotlyConfig = array(
        'type' => 'bar',
        'showlegend' => false,
        'paper_bgcolor' => 'rgba(0, 0, 0, 0)',
        'plot_bgcolor' => 'rgba(0, 0, 0, 0)',
        'title' => '',
        'font' => array(
            'family' => 'Roboto,Helvetica,Arial,sans-serif',
        ),
        'xaxis' => array(
            'title' => 'Rasch Score',
            'ticks' => 'outside',
            'tickvals' => [],
            'ticktext' => [],
            'tickmode' => 'linear',
            // 'tickangle' => -45,
        ),
        'yaxis' => array(
            'title' => 'Number of Patients',
            'showline' => true,
            'showgrid' => true,
            'ticks' => 'outside',
            'dtick'=>1,
            'dtickrange'=>['min',null],
        ),
    );

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->mode = $app->getRequest()->getQuery('mode', 0);
        $this->eye = $app->getRequest()->getQuery('eye', 0);

        parent::__construct($app);
    }

    /**
     * @param $dateFrom
     * @param $dateTo
     *
     * @return array|\CDbDataReader
     */
    protected function queryData($dateFrom, $dateTo)
    {
        $this->getExaminationEvent();

        $this->command->reset();

        $this->mode = $this->app->getRequest()->getQuery('mode', 0);
        $this->eye = $this->app->getRequest()->getQuery('eye', 0);

        if (empty($this->mode)) {
            $this->mode =0;
        }
        if (empty($this->eye)) {
            $this->eye =0;
        }
        switch ($this->mode) {
            case 1:
                $this->command->select(' 
                    cataract_element_id AS cataract_element_id,
                    C1_rasch_measure AS rasch_measure');
                break;
            case 2:
                $this->command->select('
                    cataract_element_id AS cataract_element_id,
                    C3_rasch_measure AS rasch_measure');
                break;
            default: // includes and designed for case 0. both
                $this->command->select(' 
                    cataract_element_id AS cataract_element_id,
                    (C1_rasch_measure - C3_rasch_measure) AS rasch_measure');
                break;
        }

        switch ($this->eye) {
            case 1: //Eye1
            // This will require events in the format C->O->C->O-C or C->O->C and any patients with variations to this will not display, or will potentially display incorrectly.
                $this->command ->from('(
                    SELECT DISTINCT
                    ep1.patient_id patient
                    ,eoc2.event_id AS cataract_element_id
                    ,cp5er1.total_rasch_measure AS C1_rasch_measure
                    , e1.event_date C1_date
                    , e2.event_date O2_date
                    ,cp5er3.total_rasch_measure AS C3_rasch_measure
                    , e3.event_date C3_date

                    FROM episode ep1
                    JOIN event e1 on e1.episode_id = ep1.id
                    JOIN cat_prom5_event_result cp5er1 on e1.id = cp5er1.event_id    

                    JOIN event e2 on e2.episode_id = ep1.id
                        AND e1.id != e2.id 
                        AND e1.event_date < e2.event_date  
                    JOIN et_ophtroperationnote_cataract eoc2 on eoc2.event_id = e2.id

                    JOIN event e3 on e3.episode_id = ep1.id
                        AND e2.id != e3.id 
                        AND e2.event_date < e3.event_date  
                    JOIN cat_prom5_event_result cp5er3 on e3.id = cp5er3.event_id
                
                    LEFT JOIN event e4 on e4.episode_id = ep1.id 
                        AND e1.id != e4.id 
                        AND e3.id != e4.id 
                        AND e3.event_date < e4.event_date  
                    LEFT JOIN et_ophtroperationnote_cataract eoc4 on eoc4.event_id = e4.id

                    LEFT JOIN event e5 on e5.episode_id = ep1.id 
                        AND e4.id != e5.id 
                        AND e4.event_date < e5.event_date  

                    LEFT JOIN cat_prom5_event_result cp5er5 on e5.id = cp5er5.event_id
                    ORDER BY C1_date, C3_date) wrapper')->group('patient');
                break;

            case 2: //Eye2
             // This will require events in the format C->O->C->O-C and any patients with variations to this will not display, or will potentially display incorrectly.
                $this->command ->from('(
                    SELECT DISTINCT
                    ep1.patient_id patient
                    ,eoc2.event_id AS cataract_element_id
                    ,cp5er3.total_rasch_measure AS C1_rasch_measure
                    , e3.event_date C1_date
                    , e2.event_date O2_date
                    ,cp5er5.total_rasch_measure AS C3_rasch_measure
                    , e5.event_date C3_date

                    FROM episode ep1
                    JOIN event e1 on e1.episode_id = ep1.id
                    JOIN cat_prom5_event_result cp5er1 on e1.id = cp5er1.event_id    

                    JOIN event e2 on e2.episode_id = ep1.id
                        AND e1.id != e2.id 
                        AND e1.event_date < e2.event_date  
                    JOIN et_ophtroperationnote_cataract eoc2 on eoc2.event_id = e2.id

                    JOIN event e3 on e3.episode_id = ep1.id
                        AND e2.id != e3.id 
                        AND e2.event_date < e3.event_date  
                    JOIN cat_prom5_event_result cp5er3 on e3.id = cp5er3.event_id

                    JOIN event e4 on e4.episode_id = ep1.id 
                        AND e1.id != e4.id 
                        AND e3.id != e4.id 
                        AND e3.event_date < e4.event_date  
                    JOIN et_ophtroperationnote_cataract eoc4 on eoc4.event_id = e4.id

                    JOIN event e5 on e5.episode_id = ep1.id 
                        AND e4.id != e5.id 
                        AND e4.event_date < e5.event_date  
                    JOIN cat_prom5_event_result cp5er5 on e5.id = cp5er5.event_id
                    ORDER BY C1_date desc, C3_date desc) wrapper')->group('patient');
                break;
            case 0:// includes and designed for case 0. All Eyes -  I do not like the repitition in this SQL query, but this was one of the only ways I could see this working under MariaDb 10.1 as Window functions are only availible from 10.2
            // This will require events in the format C->O->C->O-C or C->O->C and any patients with variations to this will not display, or will potentially display incorrectly.
                $this->command ->from('
                    ( SELECT * FROM(
                        SELECT * FROM(
                            SELECT DISTINCT
                            ep1.patient_id patient
                            ,eoc2.event_id AS cataract_element_id
                            ,cp5er1.total_rasch_measure AS C1_rasch_measure
                            , e1.event_date C1_date
                            , e2.event_date O2_date
                            ,cp5er3.total_rasch_measure AS C3_rasch_measure
                            , e3.event_date C3_date
                    
                            FROM episode ep1
                            JOIN event e1 on e1.episode_id = ep1.id
                            JOIN cat_prom5_event_result cp5er1 on e1.id = cp5er1.event_id    
                    
                            JOIN event e2 on e2.episode_id = ep1.id
                                AND e1.id != e2.id 
                                AND e1.event_date < e2.event_date  
                            JOIN et_ophtroperationnote_cataract eoc2 on eoc2.event_id = e2.id
                    
                            JOIN event e3 on e3.episode_id = ep1.id
                                AND e2.id != e3.id 
                                AND e2.event_date < e3.event_date  
                            JOIN cat_prom5_event_result cp5er3 on e3.id = cp5er3.event_id
                        
                            LEFT JOIN event e4 on e4.episode_id = ep1.id 
                                AND e1.id != e4.id 
                                AND e3.id != e4.id 
                                AND e3.event_date < e4.event_date  
                            LEFT JOIN et_ophtroperationnote_cataract eoc4 on eoc4.event_id = e4.id
                    
                            LEFT JOIN event e5 on e5.episode_id = ep1.id 
                                AND e4.id != e5.id 
                                AND e4.event_date < e5.event_date  
                    
                            LEFT JOIN cat_prom5_event_result cp5er5 on e5.id = cp5er5.event_id
                            ORDER BY C1_date, C3_date
                        ) Eye1sub GROUP BY patient
                    )Eye1
                    UNION
                    SELECT * FROM(
                    SELECT * FROM(
                        SELECT DISTINCT
                        ep1.patient_id patient
                        ,eoc2.event_id AS cataract_element_id
                        ,cp5er3.total_rasch_measure AS C1_rasch_measure
                        , e3.event_date C1_date
                        , e2.event_date O2_date
                        ,cp5er5.total_rasch_measure AS C3_rasch_measure
                        , e5.event_date C3_date
                
                        FROM episode ep1
                        JOIN event e1 on e1.episode_id = ep1.id
                        JOIN cat_prom5_event_result cp5er1 on e1.id = cp5er1.event_id    
                
                        JOIN event e2 on e2.episode_id = ep1.id
                            AND e1.id != e2.id 
                            AND e1.event_date < e2.event_date  
                        JOIN et_ophtroperationnote_cataract eoc2 on eoc2.event_id = e2.id
                
                        JOIN event e3 on e3.episode_id = ep1.id
                            AND e2.id != e3.id 
                            AND e2.event_date < e3.event_date  
                        JOIN cat_prom5_event_result cp5er3 on e3.id = cp5er3.event_id
                
                        JOIN event e4 on e4.episode_id = ep1.id 
                            AND e1.id != e4.id 
                            AND e3.id != e4.id 
                            AND e3.event_date < e4.event_date  
                        JOIN et_ophtroperationnote_cataract eoc4 on eoc4.event_id = e4.id
                
                        JOIN event e5 on e5.episode_id = ep1.id 
                            AND e4.id != e5.id 
                            AND e4.event_date < e5.event_date  
                
                        JOIN cat_prom5_event_result cp5er5 on e5.id = cp5er5.event_id
                        ORDER BY C1_date desc, C3_date desc
                    ) Eye2sub GROUP BY patient
                    ) Eye2
                ) wrapper');
                break;
        }
        if ($dateFrom) {
            $this->command->andWhere('C1_date >= :dateFrom', array('dateFrom' => $dateFrom));
            $this->command->andWhere('O2_date >= :dateFrom', array('dateFrom' => $dateFrom));
            $this->command->andWhere('C3_date >= :dateFrom', array('dateFrom' => $dateFrom));
        }
        if ($dateTo) {
            $this->command->andWhere('C1_date <= :dateTo', array('dateTo' => $dateTo));
            $this->command->andWhere('O2_date <= :dateTo', array('dateTo' => $dateTo));
            $this->command->andWhere('C3_date <= :dateTo', array('dateTo' => $dateTo));
        }
        return $this->command->queryAll();
    }

    /**
     * @return array
     */

    public function dataset()
    {

        $data = $this->queryData($this->from, $this->to);
        $dataSet = array();
        foreach ($data as $row) {
            $rash_score = strval($row['rasch_measure']);
            $ret_ind = array_search($rash_score, array_keys($dataSet));
            if ($ret_ind === false) {
                $dataSet[$rash_score]["count"] = 1;
                $dataSet[$rash_score]["ids"][] = $row["cataract_element_id"];
            } else {
                $dataSet[$rash_score]["count"]++;
                array_push($dataSet[$rash_score]["ids"], $row['cataract_element_id']);
            }
        }
        return $dataSet;
    }

    /**
     * @return string
     */
    public function seriesJson()
    {
        $this->series = array(
            array(
                'data' => $this->dataSet(),
                'name' => 'Cat-PROM5',
            ),
        );
        return json_encode($this->series);
    }

    /**
     * @return string
     */
    public function tracesJson()
    {
        $dataset = $this->dataset();
        $temp = array_keys($dataset);
        $trace1 = array(
            'name' => 'Cat-PROM5',
            'type' => 'bar',
            'marker' => array(
                'color' => '#7cb5ec',
            ),
            'x' => array_map(function ($item) {
                return $item;
            }, $temp),
            'y'=> array_map(function ($item) {
                return $item['count'];
            }, array_values($dataset)),
            'width'=>array_map(function ($item) {
                return 1;
            }, $temp),
            'customdata' => array_map(function ($item) {
                return $item['ids'];
            }, array_values($dataset)),
            'hovertext' => array_map(function ($item, $item2) {
                return '<b>Cat-PROM5</b><br><i>Score: </i>'. $item .
                '<br><i>Num results:</i> '. $item2['count'];
            }, $temp, $dataset),
            'hoverinfo' => 'text',
            'hoverlabel' => array(
                'bgcolor' => '#fff',
                'bordercolor' => '#7cb5ec',
                'font' => array(
                    'color' => '#000',
                ),
            ),
        );

        $traces = array($trace1);
        return json_encode($traces);
    }

    /**
     * @return string
     */
    public function plotlyConfig()
    {
        $this->plotlyConfig['title'] = 'Cat-PROM5: Pre-op vs Post-op difference - All Eyes <br><sub> (All Events) </sub>';
        return json_encode($this->plotlyConfig);
    }

    /**
     * @return mixed|string
     */
    public function renderSearch($analytics = false)
    {
        if ($analytics) {
            $this->searchTemplate = 'application.modules.OphOuCatprom5.views.report.catprom5_search_analytics';
        }

        $displayModes = array(array('id' => '0', 'name' => 'Pre-op vs Post-op difference'), array('id' => '1', 'name' => 'Pre-op'), array('id' => '2', 'name' => 'Post-op'));

        $displayEyes = array(array('id' => '0', 'name' => 'All Eyes'), array('id' => '1', 'name' => 'Eye 1'), array('id' => '2', 'name' => 'Eye 2'));

        return $this->app->controller->renderPartial($this->searchTemplate, array('report' => $this, 'modes' => $displayModes,'eyes'=>$displayEyes));
    }
}
