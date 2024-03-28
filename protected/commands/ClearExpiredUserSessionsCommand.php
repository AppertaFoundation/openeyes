<?php

class ClearExpiredUserSessionsCommand extends CConsoleCommand
{
    public function actionDeleteExpired()
    {
        // Set a buffer time equal to the max session lifetime to ensure that we don't delete sessions that are still active
        $bufferTime = (int)ini_get('session.gc_maxlifetime');

        // check that the user session table exists (it may not if db is not being used for sessions)
        if (!Yii::app()->db->schema->getTable('user_session')) {
            echo "user_session table does not exist.\n";
            return;
        }

        // Current time minus a reasonable buffer
        $thresholdTime = time() - $bufferTime;

        echo "Deleting expired rows from user_session table that expired before " . date('Y-m-d H:i:s', $thresholdTime) . "\n";

        // delete any sessions rows that expired before the threshold time
        $command = Yii::app()->db->createCommand();
        $command->delete('user_session', 'expire < :threshold', array(':threshold' => $thresholdTime));

        echo "Expired rows have been deleted.\n";
    }
}
