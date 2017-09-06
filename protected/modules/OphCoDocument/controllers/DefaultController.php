<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class DefaultController extends BaseEventTypeController
{

    protected $show_element_sidebar = false;

    /**
     * @var OphCoDocument_Sub_Types
     */
    protected $sub_type;

    protected static $action_types = array(
        'fileUpload' => self::ACTION_TYPE_FORM,
        'fileRemove' => self::ACTION_TYPE_FORM,
    );

    /**
     *
     */
    protected function initActionView()
    {
        parent::initActionView();
        $el = Element_OphCoDocument_Document::model()->findByAttributes(array('event_id' => $this->event->id));
        $this->sub_type = $el->sub_type;
        $this->title = $el->sub_type->name;
    }

    /**
     * @param $files
     * @param $index
     * @return null|string
     */
    private function documentErrorHandler($files, $index )
    {
        $message = NULL;
      
        if($files['Document']['size'][$index] > 5120000){
            $message = 'The document\'s size is too large!' ;
            return $message;
        }
        
        switch ($files['Document']['error'][$index]) {
			case 'UPLOAD_ERR_OK':
			break;
			case 'UPLOAD_ERR_NO_FILE':
				$message = 'No file was uploaded!';
                return $message;
			break;
			case 'UPLOAD_ERR_INI_SIZE':
                $message = 'The document\'s size is too large!' ;
                return $message;
            break;
			case 'UPLOAD_ERR_FORM_SIZE':
				$message = 'The document\'s size is too large!' ;
                return $message;
			break;
			default:
				$message = 'Unknow error! Please try again!';
                return $message;
		}

		$finfo = new finfo(FILEINFO_MIME_TYPE);
		
        $ext = array(
            'pdf'   => 'application/pdf',
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'png'   => 'image/png',
        );
        
		if (false === $ext = array_search(	strtolower($finfo->file($files['Document']['tmp_name'][$index])), $ext, true )) {
            $message = 'File extension is not allowed! ('.$finfo->file($files['Document']['tmp_name'][$index]).')';
		}
        
        return $message;
    }

    /**
     * @param $original_name
     * @param $tmp_name
     * @param $type
     * @param $size
     * @return bool|int
     */
    private function uploadImage($original_name, $tmp_name, $type, $size)
    {
        $p_file = new ProtectedFile();
        if(!strpos($original_name, '.'))
        {
            if($type == 'image/jpeg' || $type='image/jpg'){
                $original_name .= ".jpg";
            }else if($type == 'image/png'){
                $original_name .= ".png";
            }else if($type == 'application/msword'){
                $original_name .= ".docx";
            }else if($type == 'application/pdf'){
                $original_name .= ".pdf";
            }
        }
        $p_file = $p_file->createForWriting($original_name);

        if(move_uploaded_file($tmp_name,  $p_file->getPath())) {
            $p_file->mimetype = $type;
            if($p_file->save()) {
                return $p_file->id;
            }else{
                return false;
            }
        }
    }

    /**
     * @return string
     */
    public function getHeaderBackgroundImage()
    {
        if ($this->sub_type) {
            if (in_array($this->sub_type->name, array('OCT', 'Photograph'))) {
                $asset_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $this->event->eventType->class_name . '.assets')) . '/';
                return $asset_path . 'img/medium' . $this->sub_type->name . '.png';
            }
        }
    }

    /**
     *
     */
    public function actionFileUpload()
    {
       
        foreach($_FILES as $file)
        {
            $return_data = array();
            if(isset($file["name"]["single_document_id"]) && strlen($file["name"]["single_document_id"])>0)
            {
                $handler = $this->documentErrorHandler($_FILES, 'single_document_id');
                if( $handler == NULL) {
                    $return_data["single_document_id"] = $this->uploadImage($file["name"]["single_document_id"], $file["tmp_name"]["single_document_id"], $file["type"]["single_document_id"],$file["size"]["single_document_id"]);  
                } else {
                    $return_data = array(
                        's'     => 0,
                        'msg'   => $handler,
                        'index' => 'single_document_id'
                    );
                }           
            }
            if(isset($file["name"]["left_document_id"]) && strlen($file["name"]["left_document_id"])>0)
            {
                $handler = $this->documentErrorHandler($_FILES, 'left_document_id');
                if($handler == NULL) {
                    $return_data["left_document_id"] = $this->uploadImage($file["name"]["left_document_id"], $file["tmp_name"]["left_document_id"], $file["type"]["left_document_id"],$file["size"]["left_document_id"]);
                } else {
                    $return_data = array(
                        's'     => 0,
                        'msg'   => $handler,
                        'index' => 'left_document_id'
                    );
                } 
            }
            if(isset($file["name"]["right_document_id"]) && strlen($file["name"]["right_document_id"])>0)
            {
                $handler = $this->documentErrorHandler($_FILES, 'right_document_id');
                if($handler == NULL) {
                    $return_data["right_document_id"] = $this->uploadImage($file["name"]["right_document_id"], $file["tmp_name"]["right_document_id"], $file["type"]["right_document_id"],$file["size"]["right_document_id"]);
                } else {
                    $return_data = array(
                        's'     => 0,
                        'msg'   => $handler,
                        'index' => 'right_document_id'
                    );
                }
            }

            echo json_encode($return_data);
        }
    }

    /**
     *
     */
    public function actionFileRemove()
    {
        if($fileId = Yii::app()->request->getQuery('imgID'))
        {
            $pFile = ProtectedFile::model()->findByPk($fileId);
            $pFile->delete();
            echo 1;
        }else{
            echo 0;
        }
    }
}
