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
        $this->months = $app->getRequest()->getQuery('months', 4);

        parent::__construct($app);
    }

    public function getReportTitle()
    {
        return 'NOD Audit Report';
    }

    protected $plotlyConfig = array(
        'xaxis' => array(
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
            'ticks' => 'outside',
            'dtick' => 50,
            'tick0' => 0,
            'dtick'=>0.1,
            'tickformat'=>',.0%',
            'showgrid' => true,
            'automargin' => true,
            'range' => [0,1],
        ),
        'barmode'=>'stack',
        'oePlotly'=> array(
            'colors' => 'twoPosNeg',
            'legend' => false,
            'numTicksX' => 20,
            'numTicksY' => 20,
        ),
    );

    /**
     * @param $surgeon
     * @param $dateFrom
     * @param $dateTo
     *
     * @return CDbDataReader|mixed
     */
    protected function queryData($surgeon, $dateFrom, $dateTo, $type)
    {
        $unit = 'MONTH';
        $num = $this->months;
        $this->command->reset();
        $this->command->from('et_ophtroperationnote_cataract eoc')
            ->join('event e1', 'eoc.event_id = e1.id')
            ->join('et_ophtroperationnote_surgeon', 'et_ophtroperationnote_surgeon.event_id = e1.id')
            ->leftJoin('episode ep1', 'ep1.id=e1.episode_id')
            ->leftJoin('episode ep2', 'ep2.patient_id = ep1.patient_id')
            ->leftJoin('event e2', 'e2.episode_id = ep2.id')
            ->where('surgeon_id = :surgeon', array('surgeon' => $surgeon))
            ->andWhere('e1.deleted=0')
            ->andWhere('e2.deleted=0');
        switch ($type) {
            //visual
            case 'VA':
                $this->command->select('eoc.id as cataract_element_id, 
                                        eoc.event_id as cataract_event_id, 
                                        eov.id as va_element_id, 
                                        e1.event_date as cataract_date, 
                                        e2.event_date as other_date')
                    ->join('et_ophciexamination_visualacuity eov', 'eov.event_id = e2.id')
                    ->andWhere("ABS(date_diff('MONTH',e2.event_date,e1.event_date)) <= :month", array(':month' => 6))
                    ->group('e2.id, e1.id');
                break;
            //refraction
            case 'RF':
                $this->command->select('eoc.id as cataract_element_id,
                                        eoc.event_id as cataract_event_id, 
                                        eor.id as refraction_element_id, 
                                        e1.event_date as cataract_date, 
                                        e2.event_date as other_date')
                    ->join('et_ophciexamination_refraction eor', 'eor.event_id = e2.id')
                    ->andWhere("ABS(date_diff('$unit',e2.event_date,e1.event_date)) <= :month", array(':month' => $num))
                    ->group('e2.id, e1.id');
                break;
            //biometry
            case 'BM':
                $this->command->select('eoc.id as cataract_element_id,
                                        eoc.event_id as cataract_event_id, 
                                        e1.event_date as cataract_date, 
                                        e2.event_date as other_date')
                    ->join('et_ophinbiometry_measurement eom', 'eom.event_id = e2.id')
                    ->andWhere('eom.deleted = 0')
                    ->group('e2.id, e1.id');
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
                    ->join('et_ophciexamination_postop_complications eopc', 'eopc.event_id = e2.id')
                    ->andWhere("ABS(date_diff('$unit',e2.event_date,e1.event_date)) <= :month", array(':month' => $num))
                    ->group('e2.id, e1.id');
                break;
            // indication for surgery
            case 'IS':
                $this->command->select('eoc.id as cataract_element_id, eoc.event_id as cataract_event_id, eod.id as diagnosis_id')
                    ->leftJoin('et_ophtroperationnote_procedurelist eop', 'eop.event_id = eoc.event_id')
                    ->leftJoin('et_ophtroperationbooking_diagnosis eod', 'eod.event_id = eop.booking_event_id')
                    ->group('eoc.id');
                break;
            case 'PRE-EXAM':
                $this->command->select('eoc.id as cataract_element_id,
                                        eoc.event_id as cataract_event_id, 
                                        e1.event_date as cataract_date, 
                                        e2.event_date as other_date')
                    ->join('et_ophtroperationnote_procedurelist eop', 'eoc.event_id = eop.event_id')
                    ->join('et_ophciexamination_cataractsurgicalmanagement eocsc', 'eocsc.event_id = e2.id')
                    ->andWhere('IF(eop.eye_id = 1, eocsc.left_guarded_prognosis, eocsc.right_guarded_prognosis) = 1')
                    ->group('e2.id, e1.id');
                break;
            case 'E/I':
                $this->command->select('eoc.id as cataract_element_id, eoc.event_id as cataract_event_id, ep1.patient_id as patient_id,')
                    ->leftJoin('et_ophtroperationnote_procedurelist eop', 'eop.event_id = eoc.event_id')
                    ->leftJoin('ophtroperationnote_procedurelist_procedure_assignment oppa', 'oppa.procedurelist_id = eop.id')
                    ->leftJoin('proc', 'proc.id = oppa.proc_id')
                    ->leftJoin('et_ophtroperationbooking_diagnosis eod', 'eod.event_id = eop.booking_event_id')
                    ->leftJoin('user', 'et_ophtroperationnote_surgeon.surgeon_id=user.id')
                    ->andWhere('proc.term LIKE "%phacoemulsification%"')
                    ->andWhere('e1.event_date IS NOT NULL')
                    ->andWhere('user.doctor_grade_id IS NOT NULL')
                    ->andWhere('eod.id IS NOT NULL')
                    ->andWhere('et_ophtroperationnote_surgeon.surgeon_id IS NOT NULL')
                    ->group('eoc.id');
                break;
            case 'CATPROM5':
                if (isset(Yii::app()->modules['OphOuCatprom5'])) {
                    $this->command->select('eoc.id as cataract_element_id,
                                                eoc.event_id as cataract_event_id,
                                                e1.event_date as cataract_date,
                                                e2.event_date as other_date,
                                                cp5er.event_id as catprom5_element_id,
                                                cp5er.total_rasch_measure as rasch_score,
                                                cp5er.total_raw_score as raw_score')
                        ->leftJoin('cat_prom5_event_result cp5er', 'e2.id = cp5er.event_id')
                        ->andWhere('cp5er.event_id is not null')
                        ->group('e2.id, e1.id');
                }
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
            if (!isset($return_data['VA'])) {
                $return_data['VA'] = $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'VA'), $surgeon_id['id']);
            } else {
                $return_data['VA'] = array_merge_recursive($return_data['VA'], $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'VA'), $surgeon_id['id']));
            }

            if (!isset($return_data['RF'])) {
                $return_data['RF'] = $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'RF'), $surgeon_id['id']);
            } else {
                $return_data['RF'] = array_merge_recursive($return_data['RF'], $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'RF'), $surgeon_id['id']));
            }

            if (!isset($return_data['PCR_RISK'])) {
                $return_data['PCR_RISK'] = $this->PCRRiskDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'RISK'));
            } else {
                $return_data['PCR_RISK'] = array_merge_recursive($return_data['PCR_RISK'], $this->PCRRiskDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'RISK')));
            }

            if (!isset($return_data['COMPLICATION'])) {
                $return_data['COMPLICATION'] = $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'COMPLICATION'), $surgeon_id['id']);
            } else {
                $return_data['COMPLICATION'] = array_merge_recursive($return_data['COMPLICATION'], $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'COMPLICATION'), $surgeon_id['id']));
            }

            if (!isset($return_data['INDICATION_FOR_SURGERY'])) {
                $return_data['INDICATION_FOR_SURGERY'] = $this->IndicationForSurgeryDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'IS'));
            } else {
                $return_data['INDICATION_FOR_SURGERY'] = array_merge_recursive($return_data['INDICATION_FOR_SURGERY'], $this->IndicationForSurgeryDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'IS')));
            }

            if (!isset($return_data['BM'])) {
                $return_data['BM'] = $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'BM'), $surgeon_id['id']);
            } else {
                $return_data['BM'] = array_merge_recursive($return_data['BM'], $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'BM'), $surgeon_id['id']));
            }
            if (!isset($return_data['PRE-EXAM'])) {
                $return_data['PRE-EXAM'] = $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'PRE-EXAM'), $surgeon_id['id']);
            } else {
                $return_data['PRE-EXAM'] = array_merge_recursive($return_data['PRE-EXAM'], $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'PRE-EXAM'), $surgeon_id['id']));
            }
            if (!isset($return_data['E/I'])) {
                $return_data['E/I'] = $this->NodEligibilityDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'E/I'), $surgeon_id['id']);
            } else {
                $return_data['E/I'] = array_merge_recursive($return_data['E/I'], $this->NodEligibilityDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'E/I'), $surgeon_id['id']));
            }
            if (!isset($return_data['total'])) {
                $return_data['total'] = count($this->queryData($surgeon_id['id'], $this->from, $this->to, 'CT'));
            } else {
                $return_data['total'] += count($this->queryData($surgeon_id['id'], $this->from, $this->to, 'CT'));
            }
            if (isset(Yii::app()->modules['OphOuCatprom5'])) {
                if (!isset($return_data['CATPROM5'])) {
                    $return_data['CATPROM5'] = $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'CATPROM5'), $surgeon_id['id']);
                } else {
                    $return_data['CATPROM5'] = array_merge_recursive($return_data['CATPROM5'], $this->InsertDataToArray($this->queryData($surgeon_id['id'], $this->from, $this->to, 'CATPROM5'), $surgeon_id['id']));
                }
            }
        }
        return $return_data;
    }

    public function InsertDataToArray($data, $surgeon_id)
    {
        $return_data = array(
            'pre-complete'=>array(),
            'post-complete'=>array(),
        );
        $cataract_elements = $this->queryData($surgeon_id, $this->from, $this->to, 'CT');
        $cataract_events = array();

        foreach ($cataract_elements as $row) {
            array_push($cataract_events, $row['cataract_event_id']);
        }
        if (isset($data)) {
            foreach ($data as $case) {
                $cataract_date = Helper::mysqlDate2JsTimestamp($case['cataract_date']);
                $other_date = Helper::mysqlDate2JsTimestamp($case['other_date']);
                if ($other_date > $cataract_date) {
                    if (!in_array($case['cataract_event_id'], $return_data['post-complete'])) {
                        array_push($return_data['post-complete'], $case['cataract_event_id']);
                    }
                } elseif ($other_date <= $cataract_date) {
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

    public function PCRRiskDataToArray($data)
    {
        $return_data = array(
            'known'=> array(),
            'not_known'=> array(),
        );
        foreach ($data as $case) {
            if ($case['pcr_risk'] !== null) {
                array_push($return_data['known'], $case['cataract_event_id']);
            } else {
                array_push($return_data['not_known'], $case['cataract_event_id']);
            }
        }
        return $return_data;
    }

    public function IndicationForSurgeryDataToArray($data)
    {
        $return_data=array(
            'complete'=>array(),
            'incomplete'=>array(),
        );
        foreach ($data as $case) {
            if ($case['diagnosis_id'] !== null) {
                array_push($return_data['complete'], $case['cataract_event_id']);
            } else {
                array_push($return_data['incomplete'], $case['cataract_event_id']);
            }
        }
        return $return_data;
    }
    public function NodEligibilityDataToArray($data, $surgeon_id)
    {
        $return_data=array(
            'eligible'=>array(),
            'ineligible'=>array(),
        );
        $cataract_elements = $this->queryData($surgeon_id, $this->from, $this->to, 'CT');
        $cataract_events = array();

        foreach ($cataract_elements as $row) {
            array_push($cataract_events, $row['cataract_event_id']);
        }
        foreach ($data as $case) {
            $current_patient = Patient::model()->findByPk($case['patient_id']);
            if (isset($current_patient) && $current_patient->getAge() >= 18) {
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
        $incomplete_y = array();
        $complete_y = array();
        $hovertemplate = '%{x} %{y} of Total ' . $dataset['total'] . ' Ops';
        if ($dataset['total'] !== 0) {
            $incomplete_y = array(
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
            );
            $complete_y = array(
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
            );
            if (isset(Yii::app()->modules['OphOuCatprom5'])) {
                array_push(
                    $incomplete_y,
                    count($dataset['CATPROM5']['pre-incomplete'])/$dataset['total'],
                    count($dataset['CATPROM5']['post-incomplete'])/$dataset['total']
                );
                array_push(
                    $complete_y,
                    count($dataset['CATPROM5']['pre-complete'])/$dataset['total'],
                    count($dataset['CATPROM5']['post-complete'])/$dataset['total']
                );
            }
        }
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
                'Eligibility For NOD Audit',
            ),
            'y' => $incomplete_y,
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
            'hovertemplate' => $hovertemplate,
        );

        if (isset(Yii::app()->modules['OphOuCatprom5'])) {
            array_push($trace2['x'],
                'Cat-PROM5 Pre-op',
                'Cat-PROM5 Post-op'
            );
            array_push(
                $trace2['customdata'],
                $dataset['CATPROM5']['pre-incomplete'],
                $dataset['CATPROM5']['post-incomplete']
            );
        }
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
            'y' => $complete_y,
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
            'hovertemplate' => $hovertemplate,
        );
        if (isset(Yii::app()->modules['OphOuCatprom5'])) {
            array_push($trace1['x'],
                'Cat-PROM5 Pre-op',
                'Cat-PROM5 Post-op'
            );
            array_push(
                $trace1['customdata'],
                $dataset['CATPROM5']['pre-complete'],
                $dataset['CATPROM5']['post-complete']
            );
        }

        return json_encode(array($trace1, $trace2));
    }

    public function plotlyConfig()
    {
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
