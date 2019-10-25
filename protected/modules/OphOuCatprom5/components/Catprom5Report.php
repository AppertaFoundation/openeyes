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

use Yii; //TODO: Remove this and all logging

class Catprom5Report extends \Report implements \ReportInterface
{
    /**
     * @var int
     */
    protected $months;

    /**
     * @var array
     */

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
        $this->months = $app->getRequest()->getQuery('months', 0);


        parent::__construct($app);
    }

    /**
     * @param $surgeon
     * @param $dateFrom
     * @param $dateTo
     *
     * @return array|\CDbDataReader
     */
    protected function queryData($dateFrom, $dateTo)
    {
        $this->getExaminationEvent();

        $this->command->reset();

        $test= Yii::app()->request->getParam('catprom5');
        switch($test){

          case 'pre':
          //catprom 5 events that are before operations
            $this->command->select(' 
            eoc.event_id as cataract_element_id,
            e1.event_date as cataract_date,
            e2.event_date as catprom5_date,
            cp5er.event_id as catprom5_element_id,
            cp5er.total_rasch_measure as rasch_score,
            cp5er.total_raw_score as raw_score')
                ->from('et_ophtroperationnote_cataract eoc')
                ->join('event e1', 'eoc.event_id = e1.id')
                ->join('episode ep1', 'ep1.id=e1.episode_id')
                ->join('episode ep2', 'ep2.patient_id = ep1.patient_id')
                ->join('event e2', 'e2.episode_id = ep2.id and e2.event_date <= e1.event_date')
                ->Join('cat_prom5_event_result cp5er','e2.id = cp5er.event_id');
          break;
        case 'post':
          //catprom 5 events that are after operations
            $this->command->select(' 
            eoc.event_id as cataract_element_id,
            e1.event_date as cataract_date,
            e2.event_date as catprom5_date,
            cp5er.event_id as catprom5_element_id,
            cp5er.total_rasch_measure as rasch_score,
            cp5er.total_raw_score as raw_score')
                ->from('et_ophtroperationnote_cataract eoc')
                ->join('event e1', 'eoc.event_id = e1.id')
                ->join('episode ep1', 'ep1.id=e1.episode_id')
                ->join('episode ep2', 'ep2.patient_id = ep1.patient_id')
                ->join('event e2', 'e2.episode_id = ep2.id and e2.event_date >= e1.event_date')
                ->Join('cat_prom5_event_result cp5er','e2.id = cp5er.event_id');
            break;
            case 'dif':
            default:
            //the diff between catprom 5 events before and after operations
                $this->command->select(' 
                eoc.event_id as cataract_element_id,

                cp5er2.event_id as C2_catprom5_element_id,
                cp5er2.total_rasch_measure as C2_rasch_score,
                cp5er2.total_raw_score as C2_raw_score,

                cp5er3.event_id as C3_catprom5_element_id,
                cp5er3.total_rasch_measure as C3_rasch_score,
                cp5er3.total_raw_score as C3_raw_score,
                (cp5er2.total_rasch_measure - cp5er3.total_rasch_measure) as rasch_score
                ')
                    ->from('et_ophtroperationnote_cataract eoc')
                    ->join('event e1', 'eoc.event_id = e1.id')
                    ->join('episode ep1', 'ep1.id=e1.episode_id')

                    ->join('episode ep2', 'ep2.patient_id = ep1.patient_id')
                    ->join('event e2', 'e2.episode_id = ep2.id')
                    ->Join('cat_prom5_event_result cp5er2','e2.id = cp5er2.event_id')
                    
                    ->join('episode ep3', 'ep3.patient_id = ep1.patient_id')
                    ->join('event e3', 'e3.episode_id = ep3.id
                    and e2.id != e3.id #Not the same event
                    and e2.event_date < e3.event_date  #e2 is earlier than e3')                    
                    ->Join('cat_prom5_event_result cp5er3','e3.id = cp5er3.event_id')
                    ;
            break;
        }

            if ($dateFrom) {
              $this->command->andWhere('note_event.event_date >= :dateFrom', array('dateFrom' => $dateFrom));
              // Yii::log($dateFrom);
          }
  
          if ($dateTo) {
              $this->command->andWhere('note_event.event_date <= :dateTo', array('dateTo' => $dateTo));
          }
        return $this->command->queryAll();
    }

    /**
     * @return array
     */

    public function dataset(){

        $data = $this->queryData($this->from, $this->to);
        $dataSet = array();
        foreach($data as $row) {
          $rash_score = strval($row['rasch_score']);
          $ret_ind = array_search($rash_score, array_keys($dataSet));
          if($ret_ind === false) {
            $dataSet[$rash_score]["count"] = 1;
            $dataSet[$rash_score]["ids"][] = $row["cataract_element_id"];
            // Yii::log(var_export($dataSet, true));
          } else {
            $dataSet[$rash_score]["count"]++;
            array_push($dataSet[$rash_score]["ids"],$row['cataract_element_id']);
          }
        }
        return $dataSet;
    }

    /**
     *
     */
    protected function padPlotlyCategories()
    {
        $this->plotlyConfig['xaxis']['range'] = [-9, 9];
    }


    /**
     * @return string
     */
    public function seriesJson()
    {
        $this->series = array(
            array(
                'data' => $this->dataSet(),
                'name' => 'Catprom5',
            ),
        );
        return json_encode($this->series);
    }

    public function tracesJson(){
        $dataset = $this->dataset();
        $temp = array_keys($dataset);
        $trace1 = array(
          'name' => 'Catprom5',
          'type' => 'bar',
          'marker' => array(
            'color' => '#7cb5ec',
          ),
          'x' => array_map(function($item){
            return $item;
          }, $temp),
          'y'=> array_map(function($item){
            // Yii::log(var_export($item, true));
            return $item['count'];
          }, array_values($dataset)),
          'width'=>array_map(function($item){
            return 1;
          }, $temp),
          'customdata' => array_map(function($item){
              return $item['ids'];
          }, array_values($dataset)),
          'hovertext' => array_map(function($item, $item2){
            return '<b>Catprom5</b><br><i>Diff Post: </i>'. $item .
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
    public function plotlyConfig(){
      
      $test= Yii::app()->request->getParam('catprom5');
      switch($test){
        case 'pre':
          $this->plotlyConfig['title'] = 'Catprom5: Pre-operation';
          $this->plotlyConfig['xaxis']['range'] = [-10,8];
        break;
        case 'post':
          $this->plotlyConfig['title'] = 'Catprom5: Post-operation';
          $this->plotlyConfig['xaxis']['range'] = [-10,8];          
        break;
        case 'diff':
        default:
          $this->plotlyConfig['title'] = 'Catprom5: Pre-op vs Post-op difference';
        break;
    }
        return json_encode($this->plotlyConfig);
    }
}
