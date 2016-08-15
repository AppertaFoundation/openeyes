<?PHP
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
namespace OEModule\OphCoCvi\controllers;

use \OEModule\OphCoCvi\components\ODTImage;
use \OEModule\OphCoCvi\components\ODTTable;
use \OEModule\OphCoCvi\components\ODTRow;
use \OEModule\OphCoCvi\components\ODTCell;
use \OEModule\OphCoCvi\components\ODTSimpleText;
    
use \OEModule\OphCoCvi\components\ODTDataHandler;

class DataTestController extends \BaseController
{
    public function accessRules()
    {
        return array(
            array('allow',
                'roles'         => array('admin')
            ),
        );
    }    
    
    public function actionTest()
    {
	    $DH = new ODTDataHandler();
	    
	    $myTable   = $DH->createTable('myTable');
	    
	    $myRow     = $DH->createRow();
	    $DH       -> setObjType($myRow,'normal');
	    
	    $myCell    = $DH->createCell($myRow);
	    $DH       -> setAttribute($myCell, 'cell-type', 'normal' );
	    $DH       -> setAttribute($myCell, array( 'colspan'=>2, 'rowspan'=>0 ) );
	    $DH       -> addCell( $myRow, $myCell );    
	    
	    $myCell    = $DH->createCell($myRow);
	    $DH       -> setAttribute($myCell, 'cell-type', 'covered' );    
	    $DH       -> addCell( $myRow, $myCell );    
	    $DH       -> addRow( $myTable, $myRow );
	        
	    $myRow     = $DH->createRow();
	    $DH       -> setObjType($myRow,'hidden');
	    $myCell    = $DH->createCell($myRow);
	    $DH       -> setAttribute($myCell, 'cell-type', 'normal' );
	    $DH       -> setAttribute($myCell, array( 'colspan'=>0, 'rowspan'=>0 ) );
	    $DH       -> addCell( $myRow, $myCell );    
	    
	    $row = 1;
	    $col = 1;
	    $cellData = 'kercerece';
	    
	    $myCell    = $DH->createCell($myRow);
	    $DH       -> setAttribute( $myCell, 'cell-type', 'normal' );
	    $DH       -> setAttribute( $myCell, array( 'colspan'=>0, 'rowspan'=>0 ) );
	    $DH       -> addCell( $myRow, $myCell ); 
	    $DH       -> addRow( $myTable, $myRow );    
	    $DH       -> setTableCellData($myTable,$row,$col,$cellData);
	    
	    $row = 2;
	    $rowData = array('Egy','Ketto') ;
	    $DH       -> setTableRowData($myTable,$row,$rowData);
	    
	    $tableData = array(array(1,2), array(3,4));
	    
	    
	    $DH -> import($myTable);
	    
	    $myText = $DH -> createText('myName');
	    $DH -> setAttribute( $myText, 'data', 'Teszt message' );
	    $DH -> setAttribute( $myText, array( 'style' => array( 'text-align'=>'center', 'color' => 'red' ) ) );
	    
	    $DH -> import($myText); 
	
	    $type = 'png';
	    $binarySource = null;
	    $myImage = $DH -> createImage('myImage', $type, $binarySource );
	    $DH -> import($myImage);
        
        print '<pre>'; print_r( $DH->dataSource );
    }
}
?>