<?php

class ImportIndicesOfDeprivationCommand extends CConsoleCommand
{
    public function run($args)
    {
        $errors = [];

        $path = $args[0];

        $transaction = Yii::app()->db->beginTransaction();

        $row = 0;

        $db = Yii::app()->db;

        if (($handle = fopen($path, "r")) !== false) {
            $named_indexes = array_flip(fgetcsv($handle));

            while (($data = fgetcsv($handle)) !== false) {
                $postcode = $data[$named_indexes['pcd7']];
                $lsoa = $data[$named_indexes['lsoa11cd']];
                $row++;

                echo "Processing row: $row with Postcode: $postcode => LSOA: $lsoa\r";
                $db->createCommand()->insert('postcode_to_lsoa_mapping', array('postcode' => $postcode, 'lsoa' => $lsoa));
            }
            fclose($handle);
            echo "\n";
        } else {
            $errors[] = "Unable to open file at $path";
        }

        $errors = array_merge($errors, $this->importIMDFromSpreadsheet($args[1], 'ENGLAND', 1, 2, 'A', 'E'));
        $errors = array_merge($errors, $this->importIMDFromSpreadsheet($args[2], 'WALES', 1, 5, 'A', 'D'));

        echo "\n";

        if (empty($errors)) {
            $transaction->commit();

            echo "Import complete\n";
        } else {
            $transaction->rollback();

            echo "Import aborted due to the following errors:\n";
            foreach ($errors as $error) {
                echo " - " . $error . "\n";
            }
        }
    }

    public function importIMDFromSpreadsheet($file_name, $country_name, $data_sheet_index, $starting_row, $lsoa_column, $score_column)
    {
        $errors = [];

        echo "\nImporting $country_name IMD:\n";

        $spreadsheet_score_path = $file_name;
        $imd_score_spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($spreadsheet_score_path);
        $imd_score_data_sheet  = $imd_score_spreadsheet->getSheet($data_sheet_index);

        $imd_import = new IMDImport();
        $imd_import->country = $country_name;
        $imd_import->file_name = $spreadsheet_score_path;

        if (!$imd_import->save()) {
            $errors[] = "IMD import model failed to save due to the following errors:";
            foreach ($imd_import->getErrors() as $attribute => $attribute_errors) {
                foreach ($attribute_errors as $attribute => $error) {
                    $errors[] = " - $attribute: $error";
                }
            }
        }

        $structured_data = [];

        $max_rows = $imd_score_data_sheet->getHighestRow();
        for ($i = $starting_row; $i <= $max_rows; $i++) {
            $lsoa = $imd_score_data_sheet->getCell("$lsoa_column$i")->getValue();
            $imd_score = $imd_score_data_sheet->getCell("$score_column$i")->getValue();

            $row_number = $i - $starting_row;

            echo "Processing row: $row_number with LSOA: $lsoa => Score: $imd_score\r";

            $structured_data[] = ['lsoa' => $lsoa, 'score' => $imd_score];
        }

        echo "\nSorting data\n";

        usort(
            $structured_data,
            function ($a, $b) {
                return  ($a['score'] == $b['score']) ?
                        ($a['lsoa'] > $b['lsoa'] ? 1 : -1) :
                        ($a['score'] <= $b['score'] ? 1 : -1);
            }
        );

        $structured_data_decile_ratio = count($structured_data) / 10;

        echo "\nAssigning ranks and deciles\n";

        $structured_data_with_ranks = [];

        foreach ($structured_data as $key => $value) {
            $rank = $key + 1;

            $structured_data_with_ranks[] = [
                'lsoa' => $value['lsoa'],
                'score' => $value['score'],
                'rank' => $rank,
                'decile' => ceil($rank / $structured_data_decile_ratio)
            ];
        }

        echo "\nCreating models:\n";

        foreach ($structured_data_with_ranks as $row_number => $data_point) {
            $model = new LSOAToIMDMapping();
            $model->lsoa = trim($data_point['lsoa']);
            $model->imd_score = floatval($data_point['score']);
            $model->imd_rank = $data_point['rank'];
            $model->imd_decile = $data_point['decile'];
            $model->imd_import_id = $imd_import->id;

            echo "Creating model: $row_number with LSOA: $model->lsoa => (Score: $model->imd_score, Rank: $model->imd_rank, Decile: $model->imd_decile)\r";

            if (!$model->save()) {
                $errors[] = "LSOA to IMD with LSOA $model->lsoa failed to save due to the following errors:";
                foreach ($model->getErrors() as $attribute => $attribute_errors) {
                    foreach ($attribute_errors as $attribute => $error) {
                        $errors[] = " - $attribute: $error";
                    }
                }
            }
        }

        echo "\nImport complete\n";

        return $errors;
    }
}
