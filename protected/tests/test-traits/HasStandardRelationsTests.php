<?php


trait HasStandardRelationsTests
{
    public function test_user_relations()
    {
        $instance = $this->getElementInstance();

        $relations = $instance->relations();

        foreach (['user', 'usermodified'] as $relation) {
            $this->assertArrayHasKey($relation, $relations);
        }
    }
}
