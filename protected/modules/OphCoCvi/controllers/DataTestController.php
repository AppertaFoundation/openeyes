<?PHP
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoCvi\controllers;

use \components\odtTemplateManager\ODTImage;
use \components\odtTemplateManager\ODTTable;
use \components\odtTemplateManager\ODTRow;
use \components\odtTemplateManager\ODTCell;
use \components\odtTemplateManager\ODTSimpleText;

use \OEModule\OphCoCvi\components\ODTDataHandler;

class DataTestController extends \BaseController
{
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'roles' => array('admin'),
            ),
        );
    }

    public function actionTest()
    {
        $dataHandler = new ODTDataHandler();

        $myTable = $dataHandler->createTable('myTable');

        $myRow = $dataHandler->createRow();
        $dataHandler->setObjType($myRow, 'normal');

        $myCell = $dataHandler->createCell($myRow);
        $dataHandler->setAttribute($myCell, 'cell-type', 'normal');
        $dataHandler->setAttribute($myCell, array('colspan' => 2, 'rowspan' => 0));
        $dataHandler->addCell($myRow, $myCell);

        $myCell = $dataHandler->createCell($myRow);
        $dataHandler->setAttribute($myCell, 'cell-type', 'covered');
        $dataHandler->addCell($myRow, $myCell);
        $dataHandler->addRow($myTable, $myRow);

        $myRow = $dataHandler->createRow();
        $dataHandler->setObjType($myRow, 'hidden');
        $myCell = $dataHandler->createCell($myRow);
        $dataHandler->setAttribute($myCell, 'cell-type', 'normal');
        $dataHandler->setAttribute($myCell, array('colspan' => 0, 'rowspan' => 0));
        $dataHandler->addCell($myRow, $myCell);

        $row = 1;
        $col = 1;
        $cellData = 'kercerece';

        $myCell = $dataHandler->createCell($myRow);
        $dataHandler->setAttribute($myCell, 'cell-type', 'normal');
        $dataHandler->setAttribute($myCell, array('colspan' => 0, 'rowspan' => 0));
        $dataHandler->addCell($myRow, $myCell);
        $dataHandler->addRow($myTable, $myRow);
        $dataHandler->setTableCellData($myTable, $row, $col, $cellData);

        $row = 2;
        $rowData = array('Egy', 'Ketto');
        $dataHandler->setTableRowData($myTable, $row, $rowData);

        $tableData = array(array(1, 2), array(3, 4));


        $dataHandler->import($myTable);

        $myText = $dataHandler->createText('myName');
        $dataHandler->setAttribute($myText, 'data', 'Teszt message');
        $dataHandler->setAttribute($myText, array('style' => array('text-align' => 'center', 'color' => 'red')));

        $dataHandler->import($myText);

        $type = 'png';
        $binarySource = null;
        $myImage = $dataHandler->createImage('myImage', $type, $binarySource);
        $dataHandler->import($myImage);

        print '<pre>';
        print_r($dataHandler->dataSource);
    }
}
