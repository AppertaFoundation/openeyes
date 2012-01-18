<?php
class OELog {
	static public function log($msg, $username=false) {
		if (Yii::app()->params['log_events']) {
			if (!$username) {
				if (Yii::app()->session['user']) {
					$username = Yii::app()->session['user']->username;
				}
			}

			$msg = "[useractivity] ".$msg." [".@$_SERVER['REMOTE_ADDR']."]";

			if ($username) {
				$msg .= " [$username]";
			}

			Yii::log($msg);
		}
	}
}
?>
