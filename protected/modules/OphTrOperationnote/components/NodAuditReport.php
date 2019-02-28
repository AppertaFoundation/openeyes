<?php

class NodAuditReport extends Report implements ReportInterface
{
    /**
     * @var string
     */
    protected $searchTemplate = 'application.modules.OphTrOperationnote.views.report.nod_audit_search';

    /**
     * @var int
     */
    protected $months;

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->months = $app->getRequest()->getQuery('months', 1);
        $this->months = ceil($this->months*30);

        parent::__construct($app);
    }

    protected $plotlyConfig = array(
        'title'=>'NOD Audit Report',
        'paper_bgcolor' => 'rgba(0, 0, 0, 0)',
        'plot_bgcolor' => 'rgba(0, 0, 0, 0)',
        'font' => array(
            'family' => 'Roboto,Helvetica,Arial,sans-serif',
        ),
        'xaxis' => array(
            'title' => 'Categories',
            'titlefont' => array(
                'size' => 11,
            ),
            'tickfont'=> array(
                'size' => 10,
            ),
            'showgrid' => false,
            'ticks' => 'outside',
        ),
        'yaxis' => array(
            'title' => 'Completeness',
            'ticks' => 'outside',
            'dtick' => 50,
            'tick0' => 0,
            'dtick'=>0.1,
            'tickformat'=>',.0%',
            'showgrid' => true,
        ),
        'legend' => array(
            'x' => 0.9,
            'y' => 1.1,
            'bordercolor' => '#fff',
            'borderwidth' => 1,
            'font' => array(
                'size' => 13
            )
        ),
        'barmode'=>'stack',
        'hovermode'=>'closest'
    );

    /**
     * @param $surgeon
     * @param $dateFrom
     * @param $dateTo
     *
     * @return CDbDataReader|mixed
     */
    protected function queryData($surgeon, $dateFrom, $dateTo,$type)
    {
        $this->command->reset();
        $this->command->from('et_ophtroperationnote_cataract eoc')
            ->join('event e1', 'eoc.event_id = e1.id')
            ->join('et_ophtroperationnote_surgeon', 'et_ophtroperationnote_surgeon.event_id = e1.id')
            ->leftJoin('episode ep1','ep1.id=e1.episode_id')
            ->leftJoin('episode ep2','ep2.patient_id = ep1.patient_id')
            ->leftJoin('event e2','e2.episode_id = ep2.id')
            ->where('surgeon_id = :surgeon', array('surgeon' => $surgeon))
            ->andWhere('e1.deleted=0')
            ->andWhere('e2.deleted=0');
        switch ($type){
            //visual
            case 'VA':
                $this->command->select('eoc.id as cataract_element_id, 
                                        eoc.event_id as cataract_event_id, 
                                        eov.id as va_element_id, 
                                        e1.event_date as cataract_date, 
                                        e2.event_date as other_date')
                    ->leftJoin('et_ophciexamination_visualacuity eov','eov.event_id = e2.id')
                    ->andWhere('(DATEDIFF(e1.event_date,e2.event_date) <= 30 AND TIMEDIFF(e1.event_date,e2.event_date)>0) 
                                OR (DATEDIFF(e2.event_date,e1.event_date) <= :days AND TIMEDIFF(e2.event_date, e1.event_date)>0)',array(':days'=>$this->months))
                    ->group('e2.id');
                break;
            //refraction
            case 'RF':
                $this->command->select('eoc.id as cataract_element_id,
                                        eoc.event_id as cataract_event_id, 
                                        eor.id as refraction_element_id, 
                                        e1.event_date as cataract_date, 
                                        e2.event_date as other_date')
                    ->leftJoin('et_ophciexamination_refraction eor','eor.event_id = e2.id')
                    ->andWhere('(DATEDIFF(e1.event_date,e2.event_date) <= 30 AND TIMEDIFF(e1.event_date,e2.event_date)>0) 
                                OR (DATEDIFF(e2.event_date,e1.event_date) <= :days AND TIMEDIFF(e2.event_date, e1.event_date)>0)',array(':days'=>$this->months))
                    ->group('e2.id');
                break;
            //biometry
            case 'BM':
                $this->command->select('eoc.id as cataract_element_id,
                                        eoc.event_id as cataract_event_id, 
                                        e1.event_date as cataract_date, 
                                        e2.event_date as other_date')
                    ->leftJoin('et_ophinbiometry_measurement eom','eom.event_id = e2.id')
                    ->leftJoin('ophinbiometry_imported_events oie','oie.event_id = e2.id')
                    ->leftJoin('et_ophinbiometry_selection eos','eos.event_id = e2.id')
                    ->leftJoin('et_ophinbiometry_calculation eoc2','eoc2.id = eos.formula_id_left')
                    ->andWhere('eom.deleted = 0')
                    ->andWhere('DATEDIFF(e1.event_date,e2.event_date) <= :days AND TIMEDIFF(e1.event_date, e2.event_date)>0',array(':days'=> $this->months));
                break;
            case 'CT':
                $this->command->select('eoc.id as cataract_element_id, 
                                        eoc.event_id as cataract_event_id')
                    ->group('cataract_event_id');
                break;
            case 'RISK':
                $this->command->select('eoc.id as cataract_element_id, eoc.event_id as cataract_event_id, eoc.pcr_risk as pcr_risk')
                    ->group('eoc.id');
                break;
            case 'COMPLICATION':
                $this->command->select('eoc.id as cataract_element_id,
                                        eoc.event_id as cataract_event_id, 
                                        eopc.id as post_op_complication_id, 
                                        e1.event_date as cataract_date, 
                                        e2.event_date as other_date')
                    ->leftJoin('et_ophciexamination_postop_complications eopc','eopc.event_id = e2.id')
                    ->andWhere('DATEDIFF(e2.event_date,e1.event_date) <= :days AND TIMEDIFF(e2.event_date, e1.event_date)>0',array(':days'=>$this->months))
                    ->group('e2.id');
                break;
            // indication for surgery
            case 'IS':
                $this->command->select('eoc.id as cataract_element_id, eoc.event_id as cataract_event_id, eod.id as diagnosis_id')
                    ->leftJoin('et_ophtroperationnote_procedurelist eop','eop.event_id = eoc.event_id')
                    ->leftJoin('et_ophtroperationbooking_diagnosis eod','eod.event_id = eop.booking_event_id')
                    ->group('eoc.id');
                break;
            case 'PRE-EXAM':
                $this->command->select('eoc.id as cataract_element_id,
                                        eoc.event_id as cataract_event_id, 
                                        e1.event_date as cataract_date, 
                                        e2.event_date as other_date')
                    ->andWhere('eoc.id IS NULL');
                break;
            case 'E/I':
                $this->command->select('eoc.id as cataract_element_id, eoc.event_id as cataract_event_id, ep1.patient_id as patient_id,')
                    ->leftJoin('et_ophtroperationnote_procedurelist eop','eop.event_id = eoc.event_id')
                    ->leftJoin('ophtroperationnote_procedurelist_procedure_assignment oppa','oppa.procedurelist_id = eop.id')
                    ->leftJoin('proc','proc.id = oppa.proc_id')
                    ->leftJoin('et_ophtroperationbooking_diagnosis eod','eod.event_id = eop.booking_event_id')
                    ->leftJoin('user','et_ophtroperationnote_surgeon.surgeon_id=user.id')
                    ->andWhere('proc.term LIKE "%phacoemulsification%"')
                    ->andWhere('e1.event_date IS NOT NULL')
                    ->andWhere('user.doctor_grade_id IS NOT NULL')
                    ->andWhere('eod.id IS NOT NULL')
                    ->andWhere('et_ophtroperationnote_surgeon.surgeon_id IS NOT NULL')
                    ->group('eoc.id');
                break;

        }

        if ($dateFrom) {
            $this->command->andWhere('e1.event_date >= :dateFrom', array('dateFrom' => $dateFrom));
        }

        if ($dateTo) {
            $this->command->andWhere('e1.event_date <= :dateTo', array('dateTo' => $dateTo));
        }

        return $this->command->queryAll();
    }

    protected function querySurgeonData()
    {
        $this->command->reset();
        $this->command->select('user.id as id')
            ->from('user')
            ->where('is_surgeon = 1');
        return $this->command->queryAll();
    }

    /**
     * @return array
     */
    public function dataSet()
    {
        $return_data = array();
        if ($this->allSurgeons) {
            $surgeon_id_list = $this->querySurgeonData();
        } else {
            $surgeon_id_list = array(array('id' => $this->surgeon));
        }

        foreach ($surgeon_id_list as $surgeon_id) {
            if(!isset($return_data['VA'])){
                $return_data['VA'] = $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'VA'),$surgeon_id['id']);
            }else{
                $return_data['VA'] = array_merge_recursive($return_data['VA'],$this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'VA'),$surgeon_id['id']));
            }

            if (!isset($return_data['RF'])){
                $return_data['RF'] = $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'RF'),$surgeon_id['id']);
            }else{
                $return_data['RF'] = array_merge_recursive($return_data['RF'],$this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'RF'),$surgeon_id['id']));
            }

            if (!isset($return_data['PCR_RISK'])){
                $return_data['PCR_RISK'] = $this->PCRRiskDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'RISK'));
            }else{
                $return_data['PCR_RISK'] = array_merge_recursive($return_data['PCR_RISK'],$this->PCRRiskDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'RISK')));
            }

            if (!isset($return_data['COMPLICATION'])){
                $return_data['COMPLICATION'] = $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'COMPLICATION'),$surgeon_id['id']);
            }else{
                $return_data['COMPLICATION'] = array_merge_recursive($return_data['COMPLICATION'],$this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'COMPLICATION'),$surgeon_id['id']));
            }

            if (!isset($return_data['INDICATION_FOR_SURGERY'])){
                $return_data['INDICATION_FOR_SURGERY'] = $this->IndicationForSurgeryDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'IS'));
            }else{
                $return_data['INDICATION_FOR_SURGERY'] = array_merge_recursive($return_data['INDICATION_FOR_SURGERY'],$this->IndicationForSurgeryDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'IS')));
            }

            if (!isset($return_data['BM'])){
                $return_data['BM'] = $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'BM'),$surgeon_id['id']);
            }else{
                $return_data['BM'] = array_merge_recursive($return_data['BM'],$this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'BM'),$surgeon_id['id']));
            }
            if (!isset($return_data['PRE-EXAM'])){
                $return_data['PRE-EXAM'] = $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'PRE-EXAM'),$surgeon_id['id']);
            }else{
                $return_data['PRE-EXAM'] = array_merge_recursive($return_data['PRE-EXAM'],$this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'PRE-EXAM'),$surgeon_id['id']));
            }
            if (!isset($return_data['E/I'])){
                $return_data['E/I'] = $this->NodEligibilityDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'E/I'),$surgeon_id['id']);
            }else{
                $return_data['E/I'] = array_merge_recursive($return_data['E/I'],$this->NodEligibilityDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to,'E/I'),$surgeon_id['id']));
            }
            if (!isset($return_data['total'])){
                $return_data['total'] = count($this->queryData($surgeon_id['id'],$this->from,$this->to,'CT'));
            }else{
                $return_data['total'] += count($this->queryData($surgeon_id['id'],$this->from,$this->to,'CT'));
            }
        }
        return $return_data;
    }

    public function InsertDataToArray($data,$surgeon_id){
        $return_data = array(
            'pre-complete'=>array(),
            'post-complete'=>array(),
        );
        $cataract_elements = $this->queryData($surgeon_id,$this->from,$this->to,'CT');
        $cataract_events = array();

        foreach ($cataract_elements as $row){
            array_push($cataract_events,$row['cataract_event_id']);
        }
        if (isset($data)) {
            foreach ($data as $case) {
                $cataract_date = Helper::mysqlDate2JsTimestamp($case['cataract_date']);
                $other_date = Helper::mysqlDate2JsTimestamp($case['other_date']);
                if ($other_date > $cataract_date) {
                    if (!in_array($case['cataract_event_id'], $return_data['post-complete'])) {
                        array_push($return_data['post-complete'], $case['cataract_event_id']);
                    }
                } elseif ($other_date < $cataract_date) {
                    if (!in_array($case['cataract_event_id'], $return_data['pre-complete'])) {
                        array_push($return_data['pre-complete'], $case['cataract_event_id']);
                    }
                }
            }
        }
        $return_data['post-incomplete'] = array_values(array_diff($cataract_events, array_values($return_data['post-complete'])));
        $return_data['pre-incomplete'] = array_values(array_diff($cataract_events, array_values($return_data['pre-complete'])));

        return $return_data;
    }

    public function PCRRiskDataToArray($data){
        $return_data = array(
            'known'=> array(),
            'not_known'=> array(),
        );
        foreach ($data as $case){
            if ($case['pcr_risk'] !== null){
                array_push($return_data['known'], $case['cataract_event_id']);
            }else{
                array_push($return_data['not_known'], $case['cataract_event_id']);
            }
        }
        return $return_data;
    }

    public function IndicationForSurgeryDataToArray($data){
        $return_data=array(
            'complete'=>array(),
            'incomplete'=>array(),
        );
        foreach ($data as $case){
            if ($case['diagnosis_id'] !== null){
                array_push($return_data['complete'], $case['cataract_event_id']);
            }else{
                array_push($return_data['incomplete'], $case['cataract_event_id']);
            }
        }
        return $return_data;
    }
    public function NodEligibilityDataToArray($data,$surgeon_id){
        $return_data=array(
            'eligible'=>array(),
            'ineligible'=>array(),
        );
        $cataract_elements = $this->queryData($surgeon_id,$this->from,$this->to,'CT');
        $cataract_events = array();

        foreach ($cataract_elements as $row){
            array_push($cataract_events,$row['cataract_event_id']);
        }
        foreach ($data as $case){
            $current_patient = Patient::model()->findByPk($case['patient_id']);
            if (isset($current_patient) && $current_patient->getAge() >= 18){
                array_push($return_data['eligible'], $case['cataract_event_id']);
            }
        }
        $return_data['ineligible'] = array_values(array_diff($cataract_events, array_values($return_data['eligible'])));

        return $return_data;
    }

    /**
     * @return string
     */

    public function tracesJson()
    {
        $dataset = $this->dataSet();
        $trace2 = array(
            'name'=>'Incomplete',
            'type' => 'bar',
            'x' => array(
                'VA Pre-op',
                'VA Post-op',
                'Refraction Pre-op',
                'Refraction Post-op',
                'Biometry Pre-op',
                'Comorbidities/History',
                'Pre-operative Risk Factors',
                'Post-op Complications',
                'Indication For Surgery',
                'Eligibility For NOD Audit'
            ),
            'y' => array(
                count($dataset['VA']['pre-incomplete'])/$dataset['total'],
                count($dataset['VA']['post-incomplete'])/$dataset['total'],
                count($dataset['RF']['pre-incomplete'])/$dataset['total'],
                count($dataset['RF']['post-incomplete'])/$dataset['total'],
                count($dataset['BM']['pre-incomplete'])/$dataset['total'],
                count($dataset['PRE-EXAM']['pre-incomplete'])/$dataset['total'],
                count($dataset['PCR_RISK']['not_known'])/$dataset['total'],
                count($dataset['COMPLICATION']['post-incomplete'])/$dataset['total'],
                count($dataset['INDICATION_FOR_SURGERY']['incomplete'])/$dataset['total'],
                count($dataset['E/I']['ineligible'])/$dataset['total'],
            ),
            'customdata'=>array(
                $dataset['VA']['pre-incomplete'],
                $dataset['VA']['post-incomplete'],
                $dataset['RF']['pre-incomplete'],
                $dataset['RF']['post-incomplete'],
                $dataset['BM']['pre-incomplete'],
                $dataset['PRE-EXAM']['pre-incomplete'],
                $dataset['PCR_RISK']['not_known'],
                $dataset['COMPLICATION']['post-incomplete'],
                $dataset['INDICATION_FOR_SURGERY']['incomplete'],
                $dataset['E/I']['ineligible'],
            ),
        );
        $trace1 = array(
            'name'=>'Complete',
            'type' => 'bar',
            'x' => array(
                'VA Pre-op',
                'VA Post-op',
                'Refraction Pre-op',
                'Refraction Post-op',
                'Biometry Pre-op',
                'Comorbidities/History',
                'Pre-operative Risk Factors',
                'Post-op Complications',
                'Indication For Surgery',
                'Eligibility For NOD Audit',
            ),
            'y' => array(
                count($dataset['VA']['pre-complete'])/$dataset['total'],
                count($dataset['VA']['post-complete'])/$dataset['total'],
                count($dataset['RF']['pre-complete'])/$dataset['total'],
                count($dataset['RF']['post-complete'])/$dataset['total'],
                count($dataset['BM']['pre-complete'])/$dataset['total'],
                count($dataset['PRE-EXAM']['pre-complete'])/$dataset['total'],
                count($dataset['PCR_RISK']['known'])/$dataset['total'],
                count($dataset['COMPLICATION']['post-complete'])/$dataset['total'],
                count($dataset['INDICATION_FOR_SURGERY']['complete'])/$dataset['total'],
                count($dataset['E/I']['eligible'])/$dataset['total'],
            ),
            'customdata'=>array(
                $dataset['VA']['pre-complete'],
                $dataset['VA']['post-complete'],
                $dataset['RF']['pre-complete'],
                $dataset['RF']['post-complete'],
                $dataset['BM']['pre-complete'],
                $dataset['PRE-EXAM']['pre-complete'],
                $dataset['PCR_RISK']['known'],
                $dataset['COMPLICATION']['post-complete'],
                $dataset['INDICATION_FOR_SURGERY']['complete'],
                $dataset['E/I']['eligible'],
            ),
        );

        return json_encode(array($trace1, $trace2));
    }

    public function plotlyConfig(){
        return json_encode($this->plotlyConfig);
    }

    /**
     * @return mixed|string
     */
    public function renderSearch($analytics = false)
    {
        if ($analytics) {
            $this->searchTemplate = 'application.modules.OphTrOperationnote.views.report.nod_audit_search_analytics';
        }

        return $this->app->controller->renderPartial($this->searchTemplate, array('report' => $this));
    }
}