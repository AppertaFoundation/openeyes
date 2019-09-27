<?php
/**
 * Created by PhpStorm.
 * User: fivium-isaac
 * Date: 12/1/17
 * Time: 2:49 PM
 */

class Csv
{
    public function hasErrors()
    {

    }

    public static function parseCSV($csv_path)
		{
				$file_data = file($csv_path);

				$return_data = array();
				$column_names = explode(',', $file_data[0]);

				//Index starts at 1 to avoid interpreting header as data
				for($i = 1; $i < count($file_data); $i++) {
						$raw_data = explode(',', $file_data[$i]);
						$formatted_data = array();

						for($ii = 0; $ii < count($raw_data); $ii++) {
								$formatted_data[$column_names[$ii]] = $raw_data[$ii];
						}

						$return_data[] = $formatted_data;
				}

				return $return_data;
		}
}