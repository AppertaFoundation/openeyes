<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class DashboardController extends BaseDashboardController
{
    public $patient;

    protected $headerTemplate = 'header';

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'printSvg'),
                'expression' => 'Yii::app()->user->isSurgeon()',
            ),
            array('allow',
                'actions' => array('index'),
                'roles' => array('admin'),
            ),
            array('allow',
                'actions' => array('oescape'),
                'roles' => array('none'),
            ),
        );
    }

    public function actionIndex()
    {
        $this->render('//dashboard/index');
    }

    public function actionPrintSvg()
    {
        /*
         * This file is part of the exporting module for Highcharts JS.
         * www.highcharts.com/license
         *
         *
         * Available POST variables:
         *
         * $filename  string   The desired filename without extension
         * $type      string   The MIME type for export.
         * $width     int      The pixel width of the exported raster image. The height is calculated.
         * $svg       string   The SVG source code to convert.
         */

// Options
        define('BATIK_PATH', 'rasterizer');

///////////////////////////////////////////////////////////////////////////////
        ini_set('magic_quotes_gpc', 'off');

        $type = $_POST['type'];
        $svg = (string) $_POST['svg'];
        $filename = (string) $_POST['filename'];

// prepare variables
        if (!$filename or !preg_match('/^[A-Za-z0-9\-_ ]+$/', $filename)) {
            $filename = 'chart';
        }
        if (get_magic_quotes_gpc()) {
            $svg = stripslashes($svg);
        }

// check for malicious attack in SVG
        if (strpos($svg, '<!ENTITY') !== false || strpos($svg, '<!DOCTYPE') !== false) {
            throw new CHttpException(500, 'Malicious code detected in SVG');
        }

        $tempName = md5(rand());
        $highchartsDir = 'protected/runtime/highcharts';

        if (!is_dir($highchartsDir)) {
            mkdir($highchartsDir, 0777);
        }

// allow no other than predefined types
        if ($type == 'image/png') {
            $typeString = '-m image/png';
            $ext = 'png';
        } elseif ($type == 'image/jpeg') {
            $typeString = '-m image/jpeg';
            $ext = 'jpg';
        } elseif ($type == 'application/pdf') {
            $typeString = '-m application/pdf';
            $ext = 'pdf';
        } elseif ($type == 'image/svg+xml') {
            $ext = 'svg';
        } else { // prevent fallthrough from global variables
            $ext = 'txt';
        }

        $outfile = "$highchartsDir/$tempName.$ext";

        if (isset($typeString)) {

            // size
            $width = '';
            if ($_POST['width']) {
                $width = (int) $_POST['width'];
                if ($width) {
                    $width = "-w $width";
                }
            }

            // generate the temporary file
            if (!file_put_contents("$highchartsDir/$tempName.svg", $svg)) {
                throw new CHttpException(500, "Couldn't create temporary file. Check that the directory permissions for
      the /temp directory are set to 777.");
            }

            // Troubleshooting snippet

            /*$command = BATIK_PATH ." $typeString -d $outfile   protected/runtime/highcharts/$tempName.svg 2>&1";
            $output = shell_exec($command);
            echo "<pre>Command: $command <br>";
            echo "Output: $output</pre>";
            die;*/
            //

            // Do the conversion
            $output = shell_exec(BATIK_PATH." $typeString -d $outfile $highchartsDir/$tempName.svg");

            // catch error
            if (!is_file($outfile) || filesize($outfile) < 10) {
                throw new CHttpException(500, 'Error while converting SVG. ');
            }

            // stream it
            else {
                header("Content-Disposition: attachment; filename=\"$filename.$ext\"");
                header("Content-Type: $type");
                echo file_get_contents($outfile);
            }

            // delete it
            unlink("protected/runtime/highcharts/$tempName.svg");
            unlink($outfile);

            // SVG can be streamed directly back
        } elseif ($ext == 'svg') {
            header("Content-Disposition: attachment; filename=\"$filename.$ext\"");
            header("Content-Type: $type");
            echo $svg;
        } else {
            throw new CHttpException(400, 'Invalid Type');
        }
    }

    public function actionOEscape($id)
    {
        $this->headerTemplate = '//dashboard/header_oescape';

        $assetManager = Yii::app()->getAssetManager();
        Yii::app()->clientScript->registerScript('patientId', 'var patientId = '.$id.';', CClientScript::POS_HEAD);
        $assetManager->registerScriptFile('js/dashboard/initOEscape.js', null, null, AssetManager::OUTPUT_ALL, false);

        if ($id > 0) {
            $this->patient = Patient::model()->findByPk($id);
            $this->render('//dashboard/oescape');
        } else {
            throw new CHttpException(400, 'Patient ID not presented');
        }
    }
}
