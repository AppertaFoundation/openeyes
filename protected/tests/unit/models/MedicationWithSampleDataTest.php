<?php

/**
 * Class MedicationWithSampleDataTest
 *
 * This test class was added to allow testing of changes to the Medication class using
 * sample data/transactions, and is a partner to the original MedicationTest
 *
 * @group sample-data
 * @group medication
 */
class MedicationWithSampleDataTest extends OEDbTestCase
{
    use InteractsWithMedication;
    use MocksSession;
    use WithTransactions;
    use WithFaker;

    /**
     * @test
     * @group mapped-reference-data
     */
    public function find_all_at_level_only_applies_to_local_medications()
    {
        $institution = $this->mockCurrentInstitution();
        // one that should not be returned
        $unexpected = $this->createLocalMedication();
        $expected = [$this->createDMDMedication(), $this->createLocalMedication()];
        $expected[1]->createMapping(ReferenceData::LEVEL_INSTITUTION, $institution->id);

        $resultIds = array_map(
            function ($medication) {
                return $medication->id;
            },
            Medication::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION)
        );

        $this->assertContains($expected[0]->id, $resultIds);
        $this->assertContains($expected[1]->id, $resultIds);
        $this->assertNotContains($unexpected->id, $resultIds);
    }

    /** @test */
    public function label_displays_preservative_free_for_medication_with_attribute()
    {
        $medication = Medication::factory()->preservativeFree()->create();
        $this->assertTrue($medication->isPreservativeFree(), 'factory did not generate a preservative free medication.');

        $this->assertStringEndsWith('(No Preservative)', $medication->getLabel());
    }

    /** @test */
    public function label_does_not_display_preservative_free_for_medication_without_attribute()
    {
        $medication = Medication::factory()->create();
        $this->assertFalse($medication->isPreservativeFree(), 'factory erroneously generated a preservative free medication.');

        $this->assertStringNotContainsString('(No Preservative)', $medication->getLabel());
    }
}
