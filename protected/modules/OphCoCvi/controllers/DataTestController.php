<?PHP
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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

?>