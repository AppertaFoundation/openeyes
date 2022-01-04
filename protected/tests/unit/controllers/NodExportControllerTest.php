<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class NodExportControllerTest
 *
 * Very basic test that verifies outputs against a known good snapshot
 * of output. Crude, but good for stopping regressions.
 *
 * Relies on the sample database being loaded, which has known useful data
 * between March 2016 and March 2018
 *
 * @covers NodExportController
 * @group sample-data
 */
class NodExportControllerTest extends \OEDbTestCase
{
    protected static $expected_export_files = [
        'EpisodeVisualAcuity.csv'
    ];

    // some files contain randomised data so cannot be consistently compared
    protected const INCONSISTENT_FILES = ['Patient.csv'];

    public function test_export()
    {
        $this->markTestSkipped('The nod export report cannot be generated, skip the test for now.');
        $controller = new NodExportController('foo');
        $tmpdir = "/tmp/nodexporttest/" . date('Ymdhis');
        mkdir($tmpdir, 0777, true);
        $controller->setExportPath($tmpdir);
        $tmpFilename = date('Ymdhis');
        $controller->setZipName($tmpFilename);
        $controller->setStartDate('2016-03-04');
        $controller->setEndDate('2018-03-04');

        $controller->generateExport();
        $createdFiles = $this->getFilesInDirectory($tmpdir);

        $expectedFilesPath = __DIR__ . "/NodExport/expected/";

        foreach ($this->getFilesInDirectory($expectedFilesPath) as $expectedFile) {
            $this->assertContains($expectedFile, $createdFiles);
            if (in_array($expectedFile, self::INCONSISTENT_FILES)) {
                continue;
            }
            $expectedFileContentString = file_get_contents("$expectedFilesPath/$expectedFile");
            $expectedContentArray = explode(PHP_EOL, $expectedFileContentString);

            $createdFileContentString = file_get_contents("$tmpdir/$expectedFile");
            $createdContentArray = explode(PHP_EOL, $createdFileContentString);

            $compare_res = !array_diff($createdContentArray, $expectedContentArray) && !array_diff($expectedContentArray, $createdContentArray);

            $this->assertTrue($compare_res, "Export for $expectedFile did not match");
        }
    }

    protected function getFilesInDirectory($directory)
    {
        $dh = opendir($directory);
        $result = [];
        while (false !== ($exportFile = readdir($dh))) {
            if (!in_array($exportFile, ['.', '..'])) {
                $result[] = $exportFile;
            }
        }
        closedir($dh);
        return $result;
    }
}
