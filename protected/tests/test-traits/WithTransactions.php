<?php

/**
 * (C) Copyright Apperta Foundation 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

trait WithTransactions
{
    protected ?bool $originalEnableTransactionsState = null;

    public function setUpWithTransactions()
    {
        $this->beginDatabaseTransaction();
    }

    public function beginDatabaseTransaction()
    {
        $this->verifyTestsCanExistInTransaction();

        $this->originalEnableTransactionsState = \Yii::app()->params['enable_transactions'];

        // force transaction enabled to ensure we get one to wrap the test in
        \Yii::app()->setParams(['enable_transactions' => true]);

        $connection = $this->getDbConnection();
        $transaction = $connection->beginTransaction();

        // disable transaction calls that will fail inside the test transaction
        \Yii::app()->setParams(['enable_transactions' => false]);

        $this->tearDownCallbacks(function () use ($transaction) {
            \Yii::app()->setParams(['enable_transactions' => $this->originalEnableTransactionsState]);
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
