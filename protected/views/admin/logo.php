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
	<h2>Logo</h2>
	<?php
        
        $form = $this->beginWidget(
    'BaseEventTypeCActiveForm',
    array(
        'id' => 'upload-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    )
);
        
        
        
        echo $form->error($model, 'header_logo');
        echo $form->error($model, 'secondary_logo');
         @$headerLogo = $logoList[0]['header_logo'];
        @$secondaryLogo = $logoList[0]['secondary_logo'];
        ?>
        
   	
<table class="grid">
						<tbody>
				<tr>
					<td><?php echo $form->labelEx($model, 'Header Logo'); ?></td>
					<td><?php if(!empty($headerLogo)){?><img src="<?php echo Yii::app() -> basePath . '/images/logo/' .$headerLogo;?>" />
                                            
                                            <?php echo CHtml::link('Delete',"#", array("submit"=>array('admin/deleteLogo/', 'header_logo'=>$headerLogo), 'confirm' => 'Are you sure to delete header logo?', 'csrf'=>true)); ?>
                                            <?php }?><br/><?php echo $form->fileField($model, 'header_logo');?></td>
                                        
                                        
                                        
				</tr>
                                <tr>
					<td><?php echo $form->labelEx($model, 'Secondary Logo'); ?></td>
					<td><?php if(!empty($headerLogo)){?><img src="<?php echo Yii::app() -> basePath . '/images/logo/' .$secondaryLogo;?>">
                                            <?php echo CHtml::link('Delete',"#", array("submit"=>array('admin/deleteLogo/', 'secondary_logo'=>$secondaryLogo), 'confirm' => 'Are you sure to delete secondary logo?', 'csrf'=>true)); ?>
                                            <?php }?> 
                                            <br/><?php echo $form->fileField($model, 'secondary_logo');?>
                                            
                                        
                                        
		
                                        
                                        </td>
				</tr>

			</tbody>

 </table>                       

            
	<?php echo $form->formActions(array('cancel-uri' => '/admin/logo'));?>
	<?php $this->endWidget()?>
</div>
