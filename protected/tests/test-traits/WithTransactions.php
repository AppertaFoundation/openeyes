<?php

trait WithTransactions
{
    public function setUpWithTransactions()
    {
        $this->beginDatabaseTransaction();
    }

    public function beginDatabaseTransaction()
    {
        $this->verifyTestsCanExistInTransaction();

        $connection = $this->getFixtureManager()->getDbConnection();
        $transaction = $connection->beginTransaction();

        $this->tearDownCallbacks(function() use ($transaction) {
            $transaction->rollback();
        });
    }

    protected function verifyTestsCanExistInTransaction()
    {
        if ($this instanceof CDbTestCase) {
            if ($this->fixtures && count($this->fixtures)) {
                $this->fail("Cannot use transaction wrapper with fixtures. Fixtures cause implicit commits with sequence resets");
            }
        }
    }
}
