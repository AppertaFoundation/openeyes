<?php
class GenerateSessionsCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'Generate Session Data Command.';
	}
	public function getHelp()
	{
		$help = "A script to generate session data based on sequences in the database for future dates.\n
Optional parameter to specify the end date for the script.\n";
		
		return $help;
	}

	public function run($args)
	{
		$today = date('Y-m-d');
		$endDate = empty($args) ? strtotime('+13 months') : strtotime($args[0]);
		$sequences = Sequence::model()->findAll(
			'start_date <= :end_date AND end_date IS NULL or end_date > :today', 
			array(':end_date'=>date('Y-m-d', $endDate), ':today'=>$today));
		
		foreach ($sequences as $sequence) {
			$session = Yii::app()->db->createCommand()
				->select('date')
				->from('session')
				->where('sequence_id=:id', array(':id'=>$sequence->id))
				->order('date DESC')
				->queryRow();
			
			$startDate = empty($session) ? strtotime($today) : strtotime($session['date']) + (60 * 60 * 24);
			$sequenceEnd = empty($sequence->end_date) ? $endDate : strtotime($sequence->end_date);
			
			if ($endDate > $sequenceEnd) {
				$endDate = $sequenceEnd;
			}
			
			$dateList = array();
			if (empty($sequence->week_selection)) {
				$interval = $sequence->getFrequencyInteger($sequence['repeat_interval'], $endDate);
				$days = $interval / 24 / 60 / 60;
				
				$date = date('Y-m-d', $startDate);
				$time = $startDate;
				// get the next occurrence of the sequence on/after the start date
				while (date('N', $time) != date('N', strtotime($sequence->start_date))) {
					$date = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) + 1, date('Y', $time)));
					$time = strtotime($date);
				}
				
				while ($time <= $endDate) {
					$dateList[] = $date;
					
					$date = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) + $days, date('Y', $time)));
					$time = strtotime($date);
				}
			} else {
				$date = date('Y-m-d', $startDate);
				$time = $startDate;
				// get the next occurrence of the sequence on/after the start date
				while (date('N', $time) != date('N', strtotime($sequence->start_date))) {
					$date = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) + 1, date('Y', $time)));
					$time = strtotime($date);
				}				
				
				$dateList = $sequence->getWeekOccurrences($sequence->weekday, $sequence->week_selection, $time, $endDate, $date, date('Y-m-d', $endDate));
			}
			
			if (!empty($dateList)) {
				$insert = 'INSERT IGNORE INTO session (sequence_id, date, start_time, end_time) VALUES ';
				foreach ($dateList as $date) {
					$insert .= "({$sequence->id}, '$date', '{$sequence->start_time}', '{$sequence->end_time}')";
					if ($date != end($dateList)) {
						$insert .= ', ';
					}
					$insert .= "\n";
				}

				echo "\nSequence ID {$sequence->id}: Created " . count($dateList) . " session(s).\n";
				
				$command = Yii::app()->db->createCommand($insert);
				$command->execute();
			}
		}
	}
}
?>
