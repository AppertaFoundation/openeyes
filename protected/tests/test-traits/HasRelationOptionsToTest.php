<?php

trait HasRelationOptionsToTest
{

    /**
     * checks the instance provides the options for the given relation and expected_cls.
     * expected_cls should implement an active scope
     *
     * Optionally pass in the expected PKs for the options if they are not going to be all the
     * currently active entries for the relation lookup
     *
     * @param $instance
     * @param $relation
     * @param $expected_cls
     * @param array|null $expected_pks
     */
    public function assertOptionsAreRetrievable($instance, $relation, $expected_cls, $expected_pks = null)
    {
        if ($expected_pks === null) {
            $expected_pks = array_map(function($related_obj) {
                return $related_obj->getPrimaryKey();
            }, $expected_cls::model()->active()->findAll());
        }

        if (!count($expected_pks)) {
            $this->fail('Cannot check option relations when no data loaded for related cls ' . $expected_cls);
        }

        $options = $instance->{"{$relation}_options"};

        $this->assertCount(count($expected_pks), $options);
        $this->assertInstanceOf($expected_cls, $options[0]);

        $option_pks = array_map(function($option) {
            return $option->getPrimaryKey();
        }, $options);

        $this->assertEmpty(array_diff($expected_pks, $option_pks));
        $this->assertEmpty(array_diff($option_pks, $expected_pks));
    }
}