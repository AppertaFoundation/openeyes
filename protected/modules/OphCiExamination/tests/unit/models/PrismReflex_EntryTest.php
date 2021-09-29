<?php


namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\PrismReflex;
use OEModule\OphCiExamination\models\PrismReflex_Entry;
use OEModule\OphCiExamination\models\PrismReflex_Finding;
use OEModule\OphCiExamination\models\PrismReflex_PrismBase;
use OEModule\OphCiExamination\models\PrismReflex_PrismDioptre;
use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasCorrectionTypeAttributeToTest;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureAttributesToTest;

/**
 * Class PrismReflex_EntryTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\PrismReflex_Entry
 * @group sample-data
 * @group strabismus
 * @group prism-reflex
 */
class PrismReflex_EntryTest extends \ModelTestCase
{
    use \HasStandardRelationsTests;
    use \HasRelationOptionsToTest;
    use \InteractsWithEventTypeElements;
    use \WithFaker;
    use \WithTransactions;

    use HasCorrectionTypeAttributeToTest;
    use HasWithHeadPostureAttributesToTest;

    protected $element_cls = PrismReflex_Entry::class;


    /** @test */
    public function dioptreprism_relations_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertArrayHasKey('prismbase', $instance->relations());
        $this->assertArrayHasKey('prismdioptre', $instance->relations());
        $this->assertArrayHasKey('finding', $instance->relations());
    }

    /** @test */
    public function dioptreprism_finding_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, 'finding_id', PrismReflex_Finding::class);
        $this->assertContains('finding_id', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function dioptreprism_finding_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'finding', PrismReflex_Finding::class);
    }

    /** @test */
    public function dioptreprism_prismbase_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, 'prismbase_id', PrismReflex_PrismBase::class);
        $this->assertContains('prismbase_id', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function dioptreprism_prismbase_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'prismbase', PrismReflex_PrismBase::class);
    }

    /** @test */
    public function dioptreprism_prismdioptre_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, 'prismdioptre_id', PrismReflex_PrismDioptre::class);
        $this->assertContains('prismdioptre_id', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function dioptreprism_prismdioptre_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'prismdioptre', PrismReflex_PrismDioptre::class);
    }


    public function entry_validation_provider()
    {
        $finding = $this->getRandomLookup(PrismReflex_Finding::class);
        $prismbase = $this->getRandomLookup(PrismReflex_PrismBase::class);
        $prismdioptre = $this->getRandomLookup(PrismReflex_PrismDioptre::class);

        return [
            [
                [
                    'finding_id' => $finding->getPrimaryKey()
                ],
                false
            ],
            [
                [
                    'prismbase_id' => $prismbase->getPrimaryKey()
                ],
                false
            ],
            [
                [
                    'prismdioptre_id' => $prismdioptre->getPrimaryKey()
                ],
                false
            ],
            [
                [
                    'finding_id' => $finding->getPrimaryKey(),
                    'prismbase_id' => $prismbase->getPrimaryKey()
                ],
                false
            ],
            [
                [
                    'finding_id' => $finding->getPrimaryKey(),
                    'prismdioptre_id' => $prismdioptre->getPrimaryKey()
                ],
                false
            ],
            [
                [
                    'prismbase_id' => $prismbase->getPrimaryKey(),
                    'prismdioptre_id' => $prismdioptre->getPrimaryKey()
                ],
                false
            ],
            [
                [
                    'finding_id' => $finding->getPrimaryKey(),
                    'prismbase_id' => $prismbase->getPrimaryKey(),
                    'prismdioptre_id' => $prismdioptre->getPrimaryKey()
                ],
                true
            ],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @test
     * @dataProvider entry_validation_provider
     */
    public function entry_validation($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes($attrs);
        $this->assertEquals($expected, $instance->validate());
    }

    public function letter_string_provider()
    {
        $finding = $this->getRandomLookup(PrismReflex_Finding::class);
        $prismbase = $this->getRandomLookup(PrismReflex_PrismBase::class);
        $prismdioptre = $this->getRandomLookup(PrismReflex_PrismDioptre::class);
        $correctiontype = $this->getRandomLookup(CorrectionType::class);

        return [
            [
                [
                    'finding_id' => $finding->getPrimaryKey(),
                    'prismbase_id' => $prismbase->getPrimaryKey(),
                    'prismdioptre_id' => $prismdioptre->getPrimaryKey(),
                    'correctiontype_id' => $correctiontype->getPrimaryKey(),
                    'with_head_posture' => PrismReflex_Entry::$WITH_HEAD_POSTURE,
                ],
                "{$prismdioptre}: {$correctiontype->name}, CHP: "
                . PrismReflex_Entry::$DISPLAY_WITH_HEAD_POSTURE . ", {$prismbase} - {$finding}"
            ],
            [
                [
                    'finding_id' => $finding->getPrimaryKey(),
                    'prismbase_id' => $prismbase->getPrimaryKey(),
                    'prismdioptre_id' => $prismdioptre->getPrimaryKey(),
                    'with_head_posture' => PrismReflex_Entry::$WITH_HEAD_POSTURE,
                ],
                "{$prismdioptre}: CHP: "  . PrismReflex_Entry::$DISPLAY_WITH_HEAD_POSTURE . ", {$prismbase} - {$finding}"

            ],
            [
                [
                    'finding_id' => $finding->getPrimaryKey(),
                    'prismbase_id' => $prismbase->getPrimaryKey(),
                    'prismdioptre_id' => $prismdioptre->getPrimaryKey(),
                    'with_head_posture' => PrismReflex_Entry::$WITHOUT_HEAD_POSTURE,
                ],
                "{$prismdioptre}: CHP: "  . PrismReflex_Entry::$DISPLAY_WITHOUT_HEAD_POSTURE . ", {$prismbase} - {$finding}"
            ],
            [
                [
                    'finding_id' => $finding->getPrimaryKey(),
                    'prismbase_id' => $prismbase->getPrimaryKey(),
                    'prismdioptre_id' => $prismdioptre->getPrimaryKey(),
                    'correctiontype_id' => $correctiontype->getPrimaryKey(),
                    'with_head_posture' => PrismReflex_Entry::$WITHOUT_HEAD_POSTURE,
                ],
                "{$prismdioptre}: {$correctiontype->name}, CHP: "  . PrismReflex_Entry::$DISPLAY_WITHOUT_HEAD_POSTURE . ", {$prismbase} - {$finding}"
            ],
            [
                [
                    'finding_id' => $finding->getPrimaryKey(),
                    'prismbase_id' => $prismbase->getPrimaryKey(),
                    'prismdioptre_id' => $prismdioptre->getPrimaryKey(),
                    'correctiontype_id' => $correctiontype->getPrimaryKey(),
                ],
                "{$prismdioptre}: {$correctiontype->name}, {$prismbase} - {$finding}"
            ],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @test
     * @dataProvider letter_string_provider
     */
    public function to_string_for_letter($attrs, $expected)
    {

        $instance = $this->getElementInstance();
        $instance->setAttributes($attrs);
        $savedInstance = $this->saveEntry($instance);
        $this->assertEquals($expected, (string)$savedInstance); // explicit type casting may not be nesc, but added for readability
    }

    protected function saveEntry(PrismReflex_Entry $instance)
    {
        $element = new PrismReflex();
        $element->entries = [$instance];
        $this->saveElement($element);
        return PrismReflex_Entry::model()->findByPk($instance->getPrimaryKey());
    }
}
