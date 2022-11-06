<?php

trait HasDatabaseAssertions
{
    protected function assertDatabaseHas(string $table, array $attributes)
    {
        $cmd = $this->generateDatabaseCountQuery($table, $attributes);

        $this->assertGreaterThanOrEqual(1, $cmd->queryScalar(), "$table does not contain " . print_r($attributes, true));
    }

    protected function assertDatabaseDoesntHave(string $table, array $attributes)
    {
        $cmd = $this->generateDatabaseCountQuery($table, $attributes);

        $count = $cmd->queryScalar();
        $this->assertEquals(0, $count, "$table contains $count entries matching: " . print_r($attributes, true));
    }

    protected function generateDatabaseCountQuery(string $table, array $attributes)
    {
        $wheres = [];
        $params = [];
        foreach ($attributes as $col => $val) {
            if (is_null($val)) {
                $wheres[] = "($table.$col IS NULL or $table.$col = '')";
            } else {
                $wheres[] = "$table.$col = :_$col";
                $params[":_$col"] = $val;
            }
        }

        return $this->getDbConnection()
            ->createCommand()
            ->select('COUNT(*)')
            ->from($table)
            ->where(implode(' AND ', $wheres), $params);
    }
}
