<?php

/**
 * Created by PhpStorm.
 * User: peter
 * Date: 19/02/16
 * Time: 16:39.
 */
class CataractComplicationsReport extends Report implements ReportInterface
{
    /**
     * @var array
     */
    protected $graphConfig = array(
        'chart' => array('renderTo' => '', 'type' => 'bar'),
        'legend' => array('enabled' => false),
        'title' => array('text' => 'Complication Profile'),
        'subtitle' => array('text' => 'Total Complications: '),
        'xAxis' => array(
            'categories' => array(),
            'title' => array('text' => 'Complication'),
            'labels' => array('style' => array('fontSize' => '0.5em')),
        ),
        'yAxis' => array(
            'title' => array('text' => 'Percent of cases'),
        ),
        'tooltip' => array(
            'headerFormat' => '<b>Cataract Complications</b><br>',
            'pointFormat' => '<i>Complication</i>: {point.category} <br /> <i>Percentage </i>: {point.y:.2f}% <br /> Total Operations: {point.total}',
        ),
    );

    protected $plotlyConfig = array(
      'type' => 'bar',
      'title' => '<b>Complication Profile</b><br>Total Complications: ',
      'showlegend' => false,
      'xaxis' => array(
        'title' => 'Percent of cases',
      ),
      'yaxis' => array(
        'title' => 'Complication',
        'tickvals' => array(),
        'ticktext' => array(),
        'autorange' => 'reversed',
        'tickfont' => array(
          'family' => 'Roboto,Helvetica,Arial,sans-serif',
          'size' => '10',
        ),
      ),
      'margin' => array(
        'l' => 150,
      ),
    );

    /**
     * @param $surgeon
     * @param $dateFrom
     * @param $dateTo
     *
     * @return array|CDbDataReader
     */
    protected function queryData($surgeon, $dateFrom, $dateTo)
    {
        $this->command->reset();
        $this->command->select('COUNT(cataract_id) as complication_count, ophtroperationnote_cataract_complications.name')
            ->from('ophtroperationnote_cataract_complication')
            ->join('et_ophtroperationnote_cataract', 'ophtroperationnote_cataract_complication.cataract_id = et_ophtroperationnote_cataract.id')
            ->join('event', 'et_ophtroperationnote_cataract.event_id = event.id')
            ->join('et_ophtroperationnote_surgeon', 'et_ophtroperationnote_surgeon.event_id = event.id')
            ->join('ophtroperationnote_cataract_complications',
                'ophtroperationnote_cataract_complication.complication_id = ophtroperationnote_cataract_complications.id'
            )
            ->where('surgeon_id = :surgeon', array('surgeon' => $surgeon))
            ->andWhere('ophtroperationnote_cataract_complications.name <> "None"')
            ->andWhere('event.deleted=0')
            ->group('complication_id');

        if ($dateFrom) {
            $this->command->andWhere('event.event_date >= :dateFrom', array('dateFrom' => $dateFrom));
        }

        if ($dateTo) {
            $this->command->andWhere('event.event_date <= :dateTo', array('dateTo' => $dateTo));
        }

        return $this->command->queryAll();
    }

    /**
     * @return array
     */
    public function dataSet()
    {
        $data = $this->queryData($this->surgeon, $this->from, $this->to);
        $seriesCount = array();
        $this->setComplicationCategories();
        $total = $this->getTotalComplications();

        foreach ($this->graphConfig['xAxis']['categories'] as $category) {
            foreach ($data as $complicationData) {
                if ($category === $complicationData['name']) {
                    $seriesCount[] = array(
                        'y' => (($complicationData['complication_count'] / $total) * 100),
                        'total' => $complicationData['complication_count'],
                    );

                    continue 2;
                }
            }
            $seriesCount[] = 0;
        }

        return $seriesCount;
    }

    /**
     * @return string
     */
    public function seriesJson()
    {
        $this->series = array(
            array(
                'name' => 'Complications',
                'data' => $this->dataSet(),
            ),
        );

        return json_encode($this->series);
    }


    public function tracesJson(){
      $data = $this->dataSet();
      $this->setyAxisCategories();

      $trace1 = array(
        'name' => 'Complications',
        'type' => 'bar',
        'orientation' => 'h',
        'x' => array_map(function($item){
          if (isset($item['y'])){
            return $item['y'];
          } else {
            return 0;
          }
        },$data),
        'y' => array_keys($data),
        'hovertext' => array_map(function($key, $item){
          if (isset($item['y'])){
            return '<b>Cataract Complications</b><br><i>Complication</i>:'. $this->plotlyConfig['yaxis']['ticktext'][$key].
              '<br><i>Percentage</i>: '.number_format($item['y'],2).
              '%<br>Total Operations: '.$item['total'];
          } else {
            return '';
          }
        },array_keys($data), $data),
        'hoverinfo' =>'text',
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
    public function graphConfig()
    {
        $this->setComplicationCategories();
        $this->graphConfig['chart']['renderTo'] = $this->graphId();
        $this->graphConfig['subtitle']['text'] .= $this->getTotalComplications();
        $this->graphConfig['subtitle']['text'] .= ' Total Operations: '.$this->getTotalOperations();

        return json_encode(array_merge_recursive($this->globalGraphConfig, $this->graphConfig));
    }

    public function plotlyConfig(){
      $this->setyAxisCategories();
      return json_encode($this->plotlyConfig);
    }

    /**
     * @return array|CDbDataReader
     */
    protected function allComplications()
    {
        $this->command->reset();

        return $this->command->select('name')
            ->from('ophtroperationnote_cataract_complications')
            ->where('ophtroperationnote_cataract_complications.name <> "None"')
            ->queryAll();
    }

    /**
     *
     */
    protected function setComplicationCategories()
    {
        if (!$this->graphConfig['xAxis']['categories']) {
            $complications = $this->allComplications();
            foreach ($complications as $complication) {
                $this->graphConfig['xAxis']['categories'][] = $complication['name'];
            }
        }
    }

    protected function setyAxisCategories(){
      if (!sizeof($this->plotlyConfig['yaxis']['ticktext'])) {
        $complications = $this->allComplications();
        $i = 0;
        foreach ($complications as $complication) {
          $this->plotlyConfig['yaxis']['tickvals'][] = $i;
          $i++;
          $this->plotlyConfig['yaxis']['ticktext'][] = $complication['name'];
        }
      }
    }
    /**
     * @return int
     */
    public function getTotalComplications()
    {
        $data = $this->queryData($this->surgeon, $this->from, $this->to);
        $total = 0;
        foreach ($data as $complication) {
            $total += $complication['complication_count'];
        }

        return $total;
    }

    public function getTotalOperations()
    {
        $this->command->reset();
        $this->command->select('COUNT(*) as total')
            ->from('et_ophtroperationnote_cataract')
            ->join('event', 'et_ophtroperationnote_cataract.event_id = event.id')
            ->join('et_ophtroperationnote_surgeon', 'et_ophtroperationnote_surgeon.event_id = event.id')
            ->where('surgeon_id = :surgeon', array('surgeon' => $this->surgeon))
            ->andWhere('event.deleted=0');

        if ($this->from) {
            $this->command->andWhere('event.event_date >= :dateFrom', array('dateFrom' => $this->from));
        }

        if ($this->to) {
            $this->command->andWhere('event.event_date <= :dateTo', array('dateTo' => $this->to));
        }

        $totalData = $this->command->queryAll();

        return $totalData[0]['total'];
    }
}
