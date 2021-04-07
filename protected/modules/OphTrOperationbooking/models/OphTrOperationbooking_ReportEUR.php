<?php

class OphTrOperationbooking_ReportEUR extends BaseReport
{
    public $date_from;
    public $date_to;
    public $consultant_id;
    public $eurs;

    public function attributeNames()
    {
        return array(
            'date_from',
            'date_to',
            'consultant_id'
        );
    }

    public function attributeLabels()
    {
        return array(
            'date_from' => 'Search Booking Date from',
            'date_to' => 'Search Booking Date to',
            'consultant_id' => 'Choose Consultant',
        );
    }
    public function rules()
    {
        return array(
            array(implode(',', $this->attributeNames()), 'safe'),
        );
    }
    public function run()
    {
        /*
            * call a function to get the required data, and put the data into $eurs
            * in _eur loop through $this->eurs
        */
        $eurs = EUREventResults::model()->with('event')->findAll('result = 1');
        $this->eurs = array();
        foreach ($eurs as $eur) {
            if (!$eur->event) {
                continue;
            }
            $booking = Element_OphTrOperationbooking_Operation::model()->with('event')->find('event_id = ' . @$eur->event->id);
            $event_date = strtotime($eur->event->event_date);
            if ($this->date_from && $event_date < strtotime($this->date_from)) {
                continue;
            }
            if ($this->date_to && $event_date > strtotime($this->date_to)) {
                continue;
            }
            if ($this->consultant_id && $this->consultant_id != @$booking->named_consultant_id) {
                continue;
            }
            $deciding_question = max($eur->eurAnswerResults);
            $row = array();
            $primary_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(
                Yii::app()->params['display_primary_number_usage_code'],
                $eur->event->episode->patient->id,
                $eur->event->institution_id, $eur->event->site_id
            ));
            $row['District Number'] = $primary_identifier_value;
            $row['1st/2nd Eye'] = $eur->eye_num == 1 ? '1st Eye' : '2nd Eye';
            $row['EUR'] = ($eur->result == 1 ? 'PASSED' : 'FAILED') . ' -- ' . $deciding_question->question->question;
            $row['Date Submitted'] = date("d/m/Y", $event_date);
            $consultant = $booking->consultant ? $booking->consultant->first_name . ' ' . $booking->consultant->last_name : 'Not Selected';
            $row['Responsible Consultant'] = $consultant;
            $row['Requesting Doctor'] = $eur->event->user->first_name . ' ' . $eur->event->user->last_name;
            $this->eurs[] = $row;
        }
    }

    public function getColumns()
    {
        return array(
            'District Number',
            '1st/2nd Eye',
            'EUR',
            'Date Submitted',
            'Responsible Consultant',
            'Requesting Doctor'
        );
    }

    /**
     * Output the report in CSV format.
     *
     * @return string
     */
    public function toCSV()
    {
        $output = implode(',', $this->getColumns())."\n";
        $output .= $this->array2Csv($this->eurs);
        return $output;
    }
}
