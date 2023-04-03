<?php

/**
 * Class OwnedByReferenceDataTest
 * @covers OwnedByReferenceData
 */
class OwnedByReferenceDataTest extends OEDbTestCase
{
    use HasModelAssertions;
    use MocksSession;
    use \WithFaker;

    /**
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->createTestTable('test_owned_by_reference_data_test_fake', [
            'name' => 'varchar(30)',
            'institution_id' => 'int(10) unsigned'
        ]);
    }

    /**
     *
     */
    public function tearDown(): void
    {
        unset($this->instance, $this->softdelete_instance);
        parent::tearDown();
    }

    public function testAllInstancesReturnedByFindAllAtLevelWhenNoMappingDefined()
    {
        $this->mockCurrentInstitution();
        $expectedCount = rand(1, 5);
        $this->createFakes($expectedCount);

        $this->assertCount($expectedCount, OwnedByReferenceDataTestFake::model()->findAllAtLevels(ReferenceData::LEVEL_ALL));
    }

    public function testAllReturnedByFindAllAtLevel()
    {
        $institution = Institution::model()->findAll(new CDbCriteria(['order' => 'rand()']))[0];
        $installationFakes = $this->createFakes(rand(1, 5));
        $institutionFakes = $this->createFakesMappedToInstitution(rand(1, 5), $institution);
        $expectedFakes = array_merge($installationFakes, $institutionFakes);
        $this->mockCurrentInstitution($institution);

        $this->assertModelArraysMatch($installationFakes, OwnedByReferenceDataTestFake::model()->findAllAtLevels(ReferenceData::LEVEL_INSTALLATION, null, $institution));
        $this->assertModelArraysMatch($institutionFakes, OwnedByReferenceDataTestFake::model()->findAllAtLevels(ReferenceData::LEVEL_INSTITUTION, null, $institution));
        $this->assertModelArraysMatch($expectedFakes, OwnedByReferenceDataTestFake::model()->findAllAtLevels(ReferenceData::LEVEL_ALL, null, $institution));
    }

    public function testCriteriaAreMergedWithFindAllAtLevel()
    {
        $institution = Institution::model()->findAll(new CDbCriteria(['order' => 'rand()']))[0];
        $expected = $this->createFakesMappedToInstitution(1, $institution);
        $this->createFakesMappedToInstitution(rand(1, 5), $institution);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :expectedId');
        $criteria->params[':expectedId'] = $expected[0]->id;
        $this->mockCurrentInstitution($institution);

        $this->assertModelArraysMatch($expected, OwnedByReferenceDataTestFake::model()->findAllAtLevels(ReferenceData::LEVEL_ALL, $criteria, $institution));
    }

    protected function createFakes($count = 1)
    {
        return array_map(
            function () {
                $fake = new OwnedByReferenceDataTestFake();
                $fake->name = $this->faker->word();
                $fake->save();
                return $fake;
            },
            array_fill(0, $count, null)
        );
    }

    protected function createFakesMappedToInstitution($count, $institution)
    {
        return array_map(
            function ($fake) use ($institution) {
                $column = $fake->levelColumns()[ReferenceData::LEVEL_INSTITUTION];
                $fake->$column = $institution->id;
                $fake->save();
                return $fake;
            },
            $this->createFakes($count)
        );
    }
}

class OwnedByReferenceDataTestFake extends \BaseActiveRecord
{
    use OwnedByReferenceData;

    public function tableName()
    {
        return 'test_owned_by_reference_data_test_fake';
    }

    /**
     * Gets all supported levels.
     *
     * @return int a Bitwise value representing the supported mapping levels.
     */
    protected function getSupportedLevelMask(): int
    {
        return ReferenceData::LEVEL_INSTALLATION | ReferenceData::LEVEL_INSTITUTION;
    }
}
