<?php

/**
 * Class MappedReferenceDataTest
 * @covers MappedReferenceData
 */
class MappedReferenceDataTest extends CTestCase
{
    private MappedReferenceDataMock $instance;
    private MappedReferenceDataSoftDeleteMock $softdelete_instance;

    /**
     */
    public function setUp()
    {
        $this->instance = new MappedReferenceDataMock();
        $this->softdelete_instance = new MappedReferenceDataSoftDeleteMock();
        $this->instance->id = 1;
        $this->softdelete_instance->id = 1;
    }

    /**
     *
     */
    public function tearDown()
    {
        unset($this->instance, $this->softdelete_instance);
    }

    public function getScenarios()
    {
        return array(
            'Soft deletable reference data' => array(
                'soft_delete' => true,
                'mapping_class' => MockObject_Institution_SoftDelete::class,
                'mappings' => array(
                    array('test_id' => 1, 'institution_id' => 1),
                    array('test_id' => 1, 'institution_id' => 2),
                    array('test_id' => 1, 'institution_id' => 3),
                    array('test_id' => 1, 'institution_id' => 4),
                ),

            ),
            'Standard reference data' => array(
                'soft_delete' => false,
                'mapping_class' => MockObject_Institution::class,
                'mappings' => array(
                    array('test_id' => 1, 'institution_id' => 1),
                    array('test_id' => 1, 'institution_id' => 2),
                    array('test_id' => 1, 'institution_id' => 3),
                    array('test_id' => 1, 'institution_id' => 4),
                ),
            )
        );
    }

    public function getModelInstance($soft_delete)
    {
        return $soft_delete ? $this->softdelete_instance : $this->instance;
    }

    /**
     * @dataProvider getScenarios
     * @param bool $soft_delete
     * @param string $mapping_class
     * @param array $mappings
     */
    public function testDeleteMappings(bool $soft_delete, string $mapping_class, array $mappings): void
    {
        $model = $this->getModelInstance($soft_delete);
        foreach ($mappings as $mapping) {
            $new_mapping = new $mapping_class();
            $new_mapping->test_id = $mapping['test_id'];
            $new_mapping->institution_id = $mapping['institution_id'];
            $model->institutions[] = $new_mapping;
        }
        self::assertCount(count($mappings), $model->institutions);
        self::assertTrue($model->deleteMappings(ReferenceData::LEVEL_INSTITUTION));
    }

    /**
     * @dataProvider getScenarios
     * @param bool $soft_delete
     */
    public function testSoftDeleteMappings(bool $soft_delete): void
    {
        $model = $this->getModelInstance($soft_delete);
        self::assertEquals($soft_delete, $model->softDeleteMappings());
    }

    /**
     * @dataProvider getScenarios
     * @param bool $soft_delete
     * @param string $mapping_class
     * @param array $mappings
     */
    public function testHasMapping(bool $soft_delete, string $mapping_class, array $mappings): void
    {
        $model = $this->getModelInstance($soft_delete);
        foreach ($mappings as $mapping) {
            $new_mapping = new $mapping_class();
            $new_mapping->test_id = $mapping['test_id'];
            $new_mapping->institution_id = $mapping['institution_id'];
            $model->institutions[] = $new_mapping;
        }
        self::assertCount(count($mappings), $model->institutions);
        self::assertTrue($model->hasMapping(ReferenceData::LEVEL_INSTITUTION, $mappings[0]['institution_id']));
        self::assertFalse($model->hasMapping(ReferenceData::LEVEL_INSTITUTION, 999));
    }

    /**
     * @dataProvider getScenarios
     * @param bool $soft_delete
     * @param string $mapping_class
     * @param array $mappings
     */
    public function testCreateMappings(bool $soft_delete, string $mapping_class, array $mappings): void
    {
        $model = $this->getModelInstance($soft_delete);
        $ids = array_map(static function ($item) {
            return $item['institution_id'];
        }, $mappings);
        self::assertTrue($model->createMappings(ReferenceData::LEVEL_INSTITUTION, $ids));
    }

    /**
     * @dataProvider getScenarios
     * @param bool $soft_delete
     * @param string $mapping_class
     * @param array $mappings
     */
    public function testDeleteMapping(bool $soft_delete, string $mapping_class, array $mappings): void
    {
        $model = $this->getModelInstance($soft_delete);
        foreach ($mappings as $mapping) {
            $new_mapping = new $mapping_class();
            $new_mapping->test_id = $mapping['test_id'];
            $new_mapping->institution_id = $mapping['institution_id'];
            $model->institutions[] = $new_mapping;
        }
        self::assertCount(count($mappings), $model->institutions);
        self::assertTrue($model->deleteMapping(ReferenceData::LEVEL_INSTITUTION, $mappings[0]['institution_id']));
    }

    /**
     */
    public function testRemapMappings(): void
    {
        self::assertTrue($this->instance->remapMappings(ReferenceData::LEVEL_INSTITUTION, $this->softdelete_instance->id));
    }

    /**
     * @dataProvider getScenarios
     * @param bool $soft_delete
     * @param string $mapping_class
     * @param array $mappings
     */
    public function testCreateMapping(bool $soft_delete, string $mapping_class, array $mappings): void
    {
        $model = $this->getModelInstance($soft_delete);
        self::assertTrue($model->createMapping(ReferenceData::LEVEL_INSTITUTION, $mappings[0]['institution_id']));
    }
}

class MappedReferenceDataMock extends BaseActiveRecordVersioned
{
    use MappedReferenceData;
    public $institutions = array();
    public $id;

    public function __construct($scenario = 'insert')
    {
    }

    public function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'test_id';
    }

    public function addErrors($errors)
    {
    }

    protected function mappingModelName(int $level): string
    {
        return 'MockObject_Institution';
    }
}

class MappedReferenceDataSoftDeleteMock extends BaseActiveRecordVersionedSoftDelete
{
    use MappedReferenceData;
    public $id;
    public $notDeletedField = 'active';
    public $institutions = array();

    public function __construct($scenario = 'insert')
    {
    }

    public function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    public function softDeleteMappings(): bool
    {
        return true;
    }

    protected function mappingColumn(int $level): string
    {
        return 'test_id';
    }

    public function addErrors($errors)
    {
    }

    protected function mappingModelName(int $level): string
    {
        return 'MockObject_Institution_SoftDelete';
    }
}

class MockObject_Institution extends BaseActiveRecordVersionedSoftDelete
{
    public $id = 1;
    public $test_id;
    public $institution_id;
    public $active = true;
    public $notDeletedField = 'active';

    public function __construct($scenario = 'insert')
    {
    }

    public function save($runValidation = true, $attributes = null, $allow_overriding = false)
    {
        return true;
    }

    public function delete()
    {
        if (parent::delete()) {
            $this->id = null;
            return true;
        }

        return false;
    }

    public function findAll($condition = '', $params = array())
    {
        $all_mappings = array();
        foreach (array(1, 2, 3, 4) as $mapping) {
            $new_mapping = new self();
            $new_mapping->test_id = $params[':reference_data_id'];
            $new_mapping->institution_id = $mapping;
            $new_mapping->id = 1;
            $all_mappings[] = $new_mapping;
        }
        return $all_mappings;
    }

    public function find($condition = '', $params = array())
    {
        $new_mapping = new self();
        $new_mapping->test_id = $params[':reference_data_id'];
        $new_mapping->institution_id = $params[':level_id'];
        $new_mapping->id = 1;
        return $new_mapping;
    }

    public function count($condition = '', $params = array())
    {
        return 0;
    }

    public function exists($condition = '', $params = array())
    {
        return false;
    }

    public function deleteAll($condition = '', $params = array())
    {
        return true;
    }

    public function getErrors($attribute = null)
    {
        return array();
    }
}

class MockObject_Institution_SoftDelete extends BaseActiveRecordVersionedSoftDelete
{
    public $id = 1;
    public $test_id;
    public $institution_id;
    public $active = true;
    public $notDeletedField = 'active';

    public function __construct($scenario = 'insert')
    {
    }

    public function save($runValidation = true, $attributes = null, $allow_overriding = false)
    {
        return true;
    }

    public function findAll($condition = '', $params = array())
    {
        $all_mappings = array();
        foreach (array(1, 2, 3, 4) as $mapping) {
            $new_mapping = new self();
            $new_mapping->test_id = 1;
            $new_mapping->institution_id = $mapping;
            $new_mapping->id = 1;
            $all_mappings[] = $new_mapping;
        }
        return $all_mappings;
    }

    public function find($condition = '', $params = array())
    {
        $new_mapping = new self();
        $new_mapping->test_id = $params[':reference_data_id'];
        $new_mapping->institution_id = $params[':level_id'];
        $new_mapping->id = 1;
        return $new_mapping;
    }

    public function count($condition = '', $params = array())
    {
        return 0;
    }

    public function exists($condition = '', $params = array())
    {
        return false;
    }

    public function deleteAll($condition = '', $params = array())
    {
        return true;
    }

    public function getErrors($attribute = null)
    {
        return array();
    }
}
