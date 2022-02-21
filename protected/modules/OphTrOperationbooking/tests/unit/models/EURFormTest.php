<?php
class EUREventResultsTest extends CDbTestCase
{
    protected $fixtures = array(
        'eur_answer_results' => 'EURAnswerResults',
        'eur_event_results' => 'EUREventResults',
        'event' => 'Event'
    );

    public function setUp()
    {
        parent::setUp();
    }

    public function getEventRes()
    {
        return array(
            'failed second eye eur' => array(
                'event_fixtureId' => 'eur_event_res_1',
                'questions_answers' => array(
                    'eur_answer_res_1_1',
                    'eur_answer_res_1_2',
                    'eur_answer_res_1_3'
                ),
                'event'
            ),
            'failed first eye eur' => array(
                'event_fixtureId' => 'eur_event_res_2',
                'questions_answers' => array(
                    'eur_answer_res_2_1',
                    'eur_answer_res_2_2',
                )
            ),
            'passed second eye eur' => array(
                'event_fixtureId' => 'eur_event_res_3',
                'questions_answers' => array(
                    'eur_answer_res_3_1',
                    'eur_answer_res_3_2',
                    'eur_answer_res_3_3'
                )
            ),
            'passed first eye eur' => array(
                'event_fixtureId' => 'eur_event_res_4',
                'questions_answers' => array(
                    'eur_answer_res_4_1',
                    'eur_answer_res_4_2',
                )
            ),
        );
    }

    /**
     * @param $event_fixtureId string|null
     * @param $questions_answers array|null
     * @dataProvider getEventRes
     * @covers EUREventResults
     * @covers EURAnswerResults
     * @throws CHttpException
     */
    public function testEURFormEventAnswers($event_fixtureId, $questions_answers)
    {
        $this->answerRes = array();
        $last_question = null;
        $this->eventRes = $this->eur_event_results($event_fixtureId);
        if (is_array($questions_answers)) {
            foreach ($questions_answers as $index => $val) {
                $answerRes = $this->eur_answer_results($val);
                if (!$last_question || $answerRes->question_id > $last_question->question_id) {
                    $last_question = $answerRes;
                }
                $this->assertEquals($this->eventRes->eurAnswerResults[$index]->id, $answerRes->id);
                $this->answerRes[$answerRes->res_id][] = $answerRes;
                $questions[] = $answerRes->question_id;
            }
            $this->assertEquals($this->eventRes->result, $last_question->answer->value);
        }
    }

    public function tearDown()
    {
        unset($this->answerRes);
        unset($this->eventRes);
        parent::tearDown();
    }
}
