<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<div class="box admin">
	<h2>Examination Event Log(s)</h2>
   <?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id'=>'adminform',
                'focus'=>'#contactname',
	))?>     
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>Event Id</td>   
        <td><?php echo $eventId;?>
        
        <?php echo CHtml::hiddenField('logId' , $logId, array('id' => 'hiddenInput')); 
        echo CHtml::hiddenField('eventId' , $eventId, array('id' => 'hiddenInput')); ?>
        
        </td>   
    </tr>
    
    <tr>
        <td>Unique Code </td>   
        <td><?php echo $unique_code;?></td>   
    </tr>
     <tr>
        <td>Examination Date</td>   
        <td><?php echo date("d M Y",strtotime($data['examination_date'])) ;?></td>   
    </tr>
    
    <tr>
        <td>Patient Identifier</td>   
        <td><?php echo $data['patient']['unique_identifier'];?></td>   
    </tr>
     <tr>
        <td>Date of birth</td>   
        <td><?php echo date("d M Y",strtotime($data['patient']['dob'])) ;?></td>   
    </tr>
  
    
    
    <tr >
        <td>Eye Readings</td>
        
        
        <td>
    <?php foreach($data['patient']['eyes'] as $eyes)
    {
     
        
        ?><?php echo $eyes['label'];?> 
            <br/> Refraction ( Sphere-<?php echo $eyes['reading'][0]['refraction']['sphere'];?>, Cylinder-<?php echo $eyes['reading'][0]['refraction']['cylinder'];?>, Axis-<?php echo $eyes['reading'][0]['refraction']['axis'];?> )
            <br/>IOP ( <?php echo $eyes['reading'][0]['iop']['mm_hg'];?> mmhg, <?php echo $eyes['reading'][0]['iop']['instrument'];?>)
        <br/>
        <br/>
        <?php
    
    }
    ?>
        
        </td>   
           
    </tr>
    
    <tr>
        <td>
            
            OpTom Details
        </td>    
        <td>
                 Name :     
                 <?php echo $data['op_tom']['name'];?>
                <br/>
                 Address : 
                     <?php echo $data['op_tom']['address'];?></td>
    </tr>
    
    <?php if($data['examination_date']==3){?>
    <tr><td>Active</td><td><?php echo CHtml::radioButton('status', false, array(
    'value'=>'1',
    'name'=>'btnname'
));?> Yes
       
        
    </td></tr> <?php }?>
    </table>
      
        	<?php echo $form->formActions(array('cancel-uri' => '/oeadmin/eventLog/list'));?>

	<?php $this->endWidget()?>
</div>

