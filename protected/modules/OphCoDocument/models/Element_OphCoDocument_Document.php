<?php


class Element_OphCoDocument_Document extends BaseEventTypeElement
{
    /**
     * @return string
     */
    public function tableName()
    {
        return 'et_ophcodocument_document';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            //array('single_document_id', 'required'),
            array('event_id, single_document_id, left_document_id, right_document_id, event_sub_type, comment', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'single_document' => array(self::BELONGS_TO, 'ProtectedFile', 'single_document_id'),
            'left_document' => array(self::BELONGS_TO, 'ProtectedFile', 'left_document_id'),
            'right_document' => array(self::BELONGS_TO, 'ProtectedFile', 'right_document_id'),
            'sub_type' => array(self::BELONGS_TO, 'OphCoDocument_Sub_Types', 'event_sub_type'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'document_id' => 'Document',
            'comment' => 'Comments'
        );
    }

    /**
     * @return string
     */
    public function logoPostfix() {
        if ($this->sub_type) {
            if ($this->sub_type->name == 'OCT') {
                return 'OCT';
            } else if ($this->sub_type->name == 'Photograph') {
                return 'Photograph';
            }
        }
        return '';
    }

    public function afterSave()
    {
        foreach(array('single_document', 'left_document', 'right_document') as $document){
            $document_id = $document.'_id';
            $document_rotate = $document.'_rotate';
            $file_name = $this->getImageFileNameForRotation($this->$document_id);
            if($file_name){
                $file = $this->rotate($file_name, $_POST[$document_rotate]);
                $protected = ProtectedFile::model()->findByPk($this->$document_id);
                $protected->size = filesize($file);
                $protected->save();

            }
        }

        parent::afterSave();
    }

    protected function getImageFileNameForRotation($image_id){
        $protected = ProtectedFile::model()->findByPk($image_id);
        if($protected){
            $file_name = $protected->getFilePath().'/'.$protected->uid;
            $imageType = getimagesize($file_name)['mime'];
            if ($imageType == 'image/jpeg') {
                return $file_name;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    public function rotate($file_name, $rotate = null) {

        $original = imagecreatefromjpeg($file_name);

        if (!empty($rotate)) {
            $rotated = imagerotate($original, $rotate, 0);
            imagejpeg($rotated, $file_name);

            return $file_name;
        }

        $exif = @exif_read_data($file_name);
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 1:
                    $rotate = 0;
                    break;
                case 3:
                    $rotate = 180;
                    break;
                case 6:
                    $rotate = -90;
                    break;
                case 8:
                    $rotate = 90;
                    break;
            }

            $rotated = imagerotate($original, $rotate, 0);
            imagejpeg($rotated, $file_name);
        }

        return $file_name;
    }
}
