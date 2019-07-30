<?php
class CataractComplicationsReport extends Report implements ReportInterface
{
    /**
     * @var array
     */

    protected $plotlyConfig = array(
      'type' => 'bar',
      'title' => '',
      'showlegend' => false,
      'paper_bgcolor' => 'rgba(0, 0, 0, 0)',
      'plot_bgcolor' => 'rgba(0, 0, 0, 0)',
      'font' => array(
        'family' => 'Roboto,Helvetica,Arial,sans-serif',
      ),
      'xaxis' => array(
        'title' => 'Percent of cases',
        'showline' => true,
        'showgrid'=> true,
        'ticks' => 'outside',
      ),
      'yaxis' => array(
        'title' => 'Complication',
        'tickvals' => array(),
        'ticktext' => array(),
        'autorange' => 'reversed',
        'automargin' => 'true',
        'showline' => true,
        'showgrid'=> false,
        'tickfont' => array(
          'size' => '9.5',
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
        $this->command->select('COUNT(cataract_id) as complication_count, ophtroperationnote_cataract_complications.name, et_ophtroperationnote_cataract.event_id as event_id')
            ->from('ophtroperationnote_cataract_complication')
            ->join('et_ophtroperationnote_cataract', 'ophtroperationnote_cataract_complication.cataract_id = et_ophtroperationnote_cataract.id')
            ->join('event', 'et_ophtroperationnote_cataract.event_id = event.id')
            ->join('et_ophtroperationnote_surgeon', 'et_ophtroperationnote_surgeon.event_id = event.id')
            ->join('ophtroperationnote_cataract_complications',
                'ophtroperationnote_cataract_complication.complication_id = ophtroperationnote_cataract_complications.id'
            )
            ->where('ophtroperationnote_cataract_complications.name <> "None"')
            ->andWhere('event.deleted=0')
            ->group('complication_id');

        if ($surgeon !== 'all') {
            $this->command->andWhere('surgeon_id = :surgeon', array('surgeon' => $this->surgeon));
        }
        if ($dateFrom) {
            $this->command->andWhere('event.event_date >= :dateFrom', array('dateFrom' => $dateFrom));
        }

        if ($dateTo) {
            $this->command->andWhere('event.event_date <= :dateTo', array('dateTo' => $dateTo));
        }

        return $this->command->queryAll();
    }

    protected function queryDatas($surgeon, $dateFrom, $dateTo)
    {
        $this->command->reset();
        $this->command->select('ophtroperationnote_cataract_complication.cataract_id as cataract_id, ophtroperationnote_cataract_complications.name, et_ophtroperationnote_cataract.event_id as event_id')
            ->from('ophtroperationnote_cataract_complication')
            ->join('et_ophtroperationnote_cataract', 'ophtroperationnote_cataract_complication.cataract_id = et_ophtroperationnote_cataract.id')
            ->join('event', 'et_ophtroperationnote_cataract.event_id = event.id')
            ->join('et_ophtroperationnote_surgeon', 'et_ophtroperationnote_surgeon.event_id = event.id')
            ->join('ophtroperationnote_cataract_complications',
                'ophtroperationnote_cataract_complication.complication_id = ophtroperationnote_cataract_complications.id'
            )
            ->where('ophtroperationnote_cataract_complications.name <> "None"')
            ->andWhere('event.deleted=0');

        if ($surgeon !== 'all') {
            $this->command->andWhere('surgeon_id = :surgeon', array('surgeon' => $this->surgeon));
        }
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
        if ($this->allSurgeons) {
            $surgeon = 'all';
        } else {
            $surgeon = $this->surgeon;
        }
        $dataset = $this->queryDatas($surgeon, $this->from, $this->to);
        $seriesCount = array();
        $data = array();
        $this->setyAxisCategories();
        $total = $this->getTotalComplications($surgeon);
        foreach ($dataset as $row) {
            if (!in_array($row['name'], array_keys($data))) {
                $data[$row['name']] = array(
                    'complication_count' => 0,
                    'name' => $row['name'],
                    'event'=>array()
                );
            }

            $data[$row['name']]['complication_count'] += 1;
            if (!in_array($row['event_id'], $data[$row['name']]['event'])) {
                array_push($data[$row['name']]['event'], $row['event_id']);
            }
        }

        $data = array_values($data);

        foreach ($this->plotlyConfig['yaxis']['ticktext'] as $category) {
            foreach ($data as $complicationData) {
                if ($category === $complicationData['name']) {
                    $seriesCount[] = array(
                        'y' => (($complicationData['complication_count'] / $total) * 100),
                        'total' => $complicationData['complication_count'],
                        'event_list'=>$complicationData['event']
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
    public function tracesJson(){
        $data = $this->dataSet();
        $trace1 = array(
        'name' => 'Complications',
        'type' => 'bar',
        'orientation' => 'h',
        'marker' => array(
          'color' => '#7cb5ec',
        ),
        'x' => array_map(function($item){
            if (isset($item['total'])) {
                return $item['total'];
            } else {
                return 0;
            }
        }, $data),
        'y' => array_keys($data),
        'customdata'=>array_map(function($item){
            if (isset($item['event_list'])) {
                return $item['event_list'];
            } else {
                return null;
            }
        }, $data),
        'hovertext' => array_map(function($key, $item){
            if (isset($item['y'])) {
                return '<b>Cataract Complications</b><br><i>Complication</i>:'
                . $this->plotlyConfig['yaxis']['ticktext'][$key]
                . '<br><i>Percentage</i>: '.number_format($item['y'], 2)
                . '%<br>Total Operations: '.$item['total'];
            } else {
                return '';
            }
        }, array_keys($data), $data),
        'hoverinfo' =>'text',
        'hoverlabel' => array(
          'bgcolor' => '#fff',
          'bordercolor' => '#7cb5ec',
          'font' => array(
            'color' => '#000',
          ),
        ),
        );
        $this->plotlyConfig['xaxis']['range'] =[0, max($trace1['x'])];
        $traces = array($trace1);
        return json_encode($traces);
    }

    /**
     * @return string
     */
    public function plotlyConfig(){
        if ($this->allSurgeons) {
            $surgeon = 'all';
        } else {
            $surgeon = $this->surgeon;
        }
        $this->setyAxisCategories();
        $this->plotlyConfig['title'] = 'Complication Profile<br>'
        . '<sub>Total Complications: ' .$this->getTotalComplications($surgeon)
        . ' Total Operations: '.$this->getTotalOperations($surgeon).'</sub>';
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
    public function getTotalComplications($surgeon)
    {

        $data = $this->queryDatas($surgeon, $this->from, $this->to);
        $total = count($data);
        return $total;
    }

    public function getTotalOperations($surgeon)
    {
        $this->command->reset();
        $this->command->select('COUNT(*) as total')
            ->from('et_ophtroperationnote_cataract')
            ->join('event', 'et_ophtroperationnote_cataract.event_id = event.id')
            ->join('et_ophtroperationnote_surgeon', 'et_ophtroperationnote_surgeon.event_id = event.id')
            ->where('event.deleted=0');

        if ($surgeon !== 'all') {
            $this->command->where('surgeon_id = :surgeon', array('surgeon' => $this->surgeon));
        }
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
