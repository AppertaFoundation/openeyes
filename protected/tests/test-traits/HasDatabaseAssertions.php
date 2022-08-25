<?php

trait HasDatabaseAssertions
{
    protected function assertDatabaseHas(string $table, array $attributes)
    {
        $cmd = $this->generateDatabaseCountQuery($table, $attributes);

        $this->assertGreaterThanOrEqual(1, $cmd->queryScalar());
    }

    protected function assertDatabaseDoesntHave(string $table, array $attributes)
    {
        $cmd = $this->generateDatabaseCountQuery($table, $attributes);

        $this->assertEquals(0, $cmd->queryScalar());
    }

    private function generateDatabaseCountQuery(string $table, array $attributes)
    {
        $db = $this->getFixtureManager()->dbConnection;

        $wheres = [];
        $params = [];
        foreach ($attributes as $col => $val) {
            $wheres[] = "$col = :_$col";
            $params[":_$col"] = $val;
        }

        return $db->createCommand()
            ->select('COUNT(*)')
            ->from($table)
            ->where(implode(' AND ', $wheres), $params);
    }
}
