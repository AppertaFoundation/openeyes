<?php
use OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Maculopathy;
use OEModule\OphCiExamination\models\MaculopathyFeature;

/**
 * Class Element_OphCiExamination_DR_RetinopathyTest
 * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Maculopathy
 *
 * @property Element_OphCiExamination_DR_Maculopathy $element
 */
class Element_OphCiExamination_DR_MaculopathyTest extends CDbTestCase
{
    protected $element;
    protected $fixtures = array(
        'event_types' => EventType::class,
        'events' => Event::class,
        'maculopathy_elements' => Element_OphCiExamination_DR_Maculopathy::class,
        'maculopathy_features' => MaculopathyFeature::class,
    );

    public function setUp()
    {
        parent::setUp();
        $this->element = new Element_OphCiExamination_DR_Maculopathy();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->element);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return array(
            'Left eye single feature' => array(
                'side' => Eye::LEFT,
                'features' => array(
                    array(
                        'feature_id' => 20,
                    )
                ),
            ),
            'Right eye single feature' => array(
                'side' => Eye::RIGHT,
                'features' => array(
                    array(
                        'feature_id' => 20,
                    )
                ),
            ),
            'Left eye No MR' => array(
                'side' => Eye::LEFT,
                'features' => array(
                    array(
                        'feature_id' => 19,
                    )
                ),
            ),
            'Left eye replace existing feature' => array(
                'side' => Eye::LEFT,
                'features' => array(
                    array(
                        'feature_id' => 23,
                    ),
                ),
                'fixture' => 'element1',
            ),
        );
    }

    /**
     * @param $side int
     * @param $features array
     * @param $fixture string|null
     * @dataProvider getData
     * @throws Exception
     */
    public function testUpdateFeatures(int $side, array $features, string $fixture = null): void
    {
        $field = null;
        $feature_obj_list = array();

        if ($side === Eye::LEFT) {
            $field = 'left_maculopathy_features';
        } elseif ($side === Eye::RIGHT) {
            $field = 'right_maculopathy_features';
        }

        if (!$fixture) {
            $this->element->eye_id = $side;
            foreach ($features as $feature_data) {
                $feature = new MaculopathyFeature();
                $feature->eye_id = $side;
                $feature->attributes = $feature_data;
                $feature_obj_list[] = $feature;
            }
            $this->element->$field = $feature_obj_list;
        } else {
            $this->element = $this->maculopathy_elements($fixture);
        }
        $this->element->updateFeatures($side, $features);
        if (!$this->element->save()) {
            $error_msg = 'Unable to save maculopathy feature element.';
            self::fail($error_msg);
        }
        // First, ensure the field is not null. Then, ensure the array isn't empty,
        // then check that the feature_id of the first (and only) entry in the saved record is the same as the provided
        // feature's feature_id value.
        self::assertNotNull($this->element->$field);
        self::assertNotEmpty($this->element->$field);
        self::assertEquals($features[0]['feature_id'], $this->element->{$field}[0]->feature_id);
    }
}
