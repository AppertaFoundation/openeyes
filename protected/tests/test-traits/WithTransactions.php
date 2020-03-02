<?php

trait WithTransactions
{
    public function beginDatabaseTransaction()
    {
        $connection = $this->getFixtureManager()->dbConnection;

        $transaction = $connection->beginTransaction();

        $this->tearDownCallbacks(function() use ($transaction) {
            $transaction->rollback();
        });
    }
}