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

        if (!empty($_POST['single_document_rotate']) || !empty($_POST['left_document_rotate']) || !empty($_POST['right_document_rotate'])) {

            if (!empty($this->single_document)) {
                $rotate = $_POST['single_document_rotate'];
                $protected = ProtectedFile::model()->findByPk($this->single_document_id);
                $tmp_name = $protected->getFilePath().'/'.$protected->uid;
                $imageType = getimagesize($tmp_name)['mime'];

                if ($imageType == 'image/jpeg') {
                    $this->rotate($tmp_name, $rotate);
                }
            }
            if (!empty($this->left_document)) {
                $rotate = $_POST['left_document_rotate'];
                $protected = ProtectedFile::model()->findByPk($this->left_document_id);
                $tmp_name = $protected->getFilePath().'/'.$protected->uid;
                $imageType = getimagesize($tmp_name)['mime'];

                if ($imageType == 'image/jpeg') {
                    $this->rotate($tmp_name, $rotate);
                }
            }
            if (!empty($this->right_document)) {
                $rotate = $_POST['right_document_rotate'];
                $protected = ProtectedFile::model()->findByPk($this->right_document_id);
                $tmp_name = $protected->getFilePath().'/'.$protected->uid;
                $imageType = getimagesize($tmp_name)['mime'];

                if ($imageType == 'image/jpeg') {
                    $this->rotate($tmp_name, $rotate);
                }
            }

        }


        parent::afterSave();
    }


    public function rotate($tmp_name, $rotate = null) {
        $original = imagecreatefromjpeg($tmp_name);

        if (!empty($rotate)) {
            $rotated = imagerotate($original, $rotate, 0);
            imagejpeg($rotated, $tmp_name);

            return $tmp_name;
        }

        $exif = exif_read_data($tmp_name);
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
            imagejpeg($rotated, $tmp_name);

        }

        return $tmp_name;
    }
}
