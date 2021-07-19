<?php
use OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Retinopathy;
use OEModule\OphCiExamination\models\RetinopathyFeature;

/**
 * Class Element_OphCiExamination_DR_RetinopathyTest
 * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Retinopathy
 *
 * @property Element_OphCiExamination_DR_Retinopathy $element
 */
class Element_OphCiExamination_DR_RetinopathyTest extends CDbTestCase
{
    protected $element;
    protected $fixtures = array(
        'event_types' => EventType::class,
        'events' => Event::class,
        'retinopathy_elements' => Element_OphCiExamination_DR_Retinopathy::class,
        'retinopathy_features' => RetinopathyFeature::class,
    );

    public function setUp()
    {
        parent::setUp();
        $this->element = new Element_OphCiExamination_DR_Retinopathy();
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
                        'feature_id' => 4,
                    )
                ),
            ),
            'Right eye single feature' => array(
                'side' => Eye::RIGHT,
                'features' => array(
                    array(
                        'feature_id' => 4,
                    )
                ),
            ),
            'Left eye No DR' => array(
                'side' => Eye::LEFT,
                'features' => array(
                    array(
                        'feature_id' => 1,
                    )
                ),
            ),
            'Left eye multiple MA single feature' => array(
                'side' => Eye::LEFT,
                'features' => array(
                    array(
                        'feature_id' => 2,
                        'feature_count' => '5+',
                    )
                ),
            ),
            'Left eye multiple features from different grades' => array(
                'side' => Eye::LEFT,
                'features' => array(
                    array(
                        'feature_id' => 3,
                    ),
                    array(
                        'feature_id' => 5,
                    ),
                    array(
                        'feature_id' => 10,
                    ),
                    array(
                        'feature_id' => 14,
                    ),
                    array(
                        'feature_id' => 18,
                    ),
                ),
            ),
            'Left eye multiple features from different grades over existing element' => array(
                'side' => Eye::LEFT,
                'features' => array(
                    array(
                        'feature_id' => 3,
                    ),
                    array(
                        'feature_id' => 5,
                    ),
                    array(
                        'feature_id' => 10,
                    ),
                    array(
                        'feature_id' => 14,
                    ),
                    array(
                        'feature_id' => 18,
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
            $field = 'left_retinopathy_features';
        } elseif ($side === Eye::RIGHT) {
            $field = 'right_retinopathy_features';
        }
        if (!$fixture) {
            $this->element->eye_id = $side;
            foreach ($features as $feature_data) {
                $feature = new RetinopathyFeature();
                $feature->eye_id = $side;
                $feature->attributes = $feature_data;
                $feature_obj_list[] = $feature;
            }
            $this->element->$field = $feature_obj_list;
        } else {
            $this->element = $this->retinopathy_elements($fixture);
        }
        $this->element->updateFeatures($side, $features);
        if (!$this->element->save()) {
            self::fail('Unable to save DR retinopathy element.');
        }

        // First, ensure the field is not null. Then, ensure the array isn't empty,
        // then check that the number of entries saved matches the number of entries provided for testing.
        // Cannot test individual entries due to the generic nature of this test, so relying on the number of
        // entries being accurate.
        self::assertNotNull($this->element->$field);
        self::assertNotEmpty($this->element->$field);
        self::assertCount(count($features), $this->element->$field);
    }
}
