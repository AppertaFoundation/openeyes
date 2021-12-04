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

/**
 * This is the model class for table "ophcotherapya_filecoll".
 *
 * A File Collection points to one or more PDF that can be attached to a therapy application
 *
 * @property int $id The file collection id
 * @property string $name The collection name
 * @property string $summary The summary text for this file collection
 * @property int $zipfile_id - The id of the protected file that is compressed file
 * @property ProtectedFile[] $files - The files in the collection
 * @property ProtectedFile $compressed_file - A zipfile of the files in this collection
 **/
class OphCoTherapyapplication_FileCollection extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * function to check if a file is the right type to become part of a collection.
     *
     * @param string $file
     *
     * @return bool
     */
    public static function checkMimeType($file)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        return in_array($finfo->file($file), array('application/pdf'));
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophcotherapya_filecoll';
    }

    /**
     * set the ordering based on the name of the collections.
     *
     * (non-PHPdoc)
     *
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        $alias = $this->getTableAlias(false, false);

        return array(
            'order' => 'UPPER('.$alias.'.name) ASC',
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
                'file_assignments' => array(self::HAS_MANY, 'OphCoTherapyapplication_FileCollectionAssignment', 'collection_id'),
                'files' => array(self::HAS_MANY, 'ProtectedFile', 'file_id', 'through' => 'file_assignments'),
                // Do NOT use this relation directly, use getZipFile instead
                'compressed_file' => array(self::BELONGS_TO, 'ProtectedFile', 'zipfile_id'),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('name, summary, institution_id', 'safe'),
                array('name, summary, institution_id', 'required'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, summary, name, institution_id', 'safe', 'on' => 'search'),
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * get a compressed zip file containing all the files in the collection.
     *
     * @throws Exception
     *
     * @return ProtectedFile
     */
    public function getZipFile()
    {
        if (!$this->compressed_file) {
            // need to generate zip file, and store it with this collection
            $pfile = ProtectedFile::createForWriting($this->name.'.zip');

            $zip = new ZipArchive();
            if (!$zip->open($pfile->getPath(), ZIPARCHIVE::OVERWRITE)) {
                throw new Exception('cannot create zip file');
            }

            foreach ($this->files as $pf) {
                $zip->addFile($pf->getPath(), $pf->name);
            }

            $zip->close();
            $pfile->save();

            // set up relation
            $this->compressed_file = $pfile;
            $this->zipfile_id = $pfile->id;

            $this->save();
        }

        return $this->compressed_file;
    }

    /**
     * return the download url for the compressed file of this collection.
     *
     * @return string URL
     */
    public function getDownloadURL()
    {
        if ($this->compressed_file != null) {
            return $this->compressed_file->getDownloadURL();
        } else {
            return Yii::app()->createURL('OphCoTherapyapplication/Default/DownloadFileCollection', array('id' => $this->id));
        }
    }

    /**
     * removes the compressed file association for this collection. Should be called when the files for it are
     * changed so that a new compressed file is created when needed.
     *
     * will also call the delete function on the protected file itself to remove orphaned files
     */
    protected function cleanCompressedFile()
    {
        // note we have to work on the file id not the relation
        if ($id = $this->zipfile_id) {
            $this->zipfile_id = null;
            if ($this->save()) {
                $pf = ProtectedFile::model()->findByPk($id);
                try {
                    $pf->delete();
                } catch (Exception $e) {
                    // ignore this exception as it's because the compressed file is referenced elsewhere
                }
            }
        }
    }

    /**
     * update the files for this collection.
     *
     * @param int[] $file_ids - array of ProtectedFile ids to assign to the collection
     */
    public function updateFiles($file_ids)
    {
        $current_files = array();
        $save_files = array();

        $current_files = $this->file_assignments;

        // go through each update file id, if it isn't assigned for this element,
        // create assignment and store for saving
        // if there is, remove from the current files array
        // anything left in current files at the end is ripe for deleting
        foreach ($file_ids as $file_id) {
            if (!array_key_exists($file_id, $current_files)) {
                $fa = new OphCoTherapyapplication_FileCollectionAssignment();
                $fa->attributes = array('collection_id' => $this->id, 'file_id' => $file_id);
                $save_files[] = $fa;
            } else {
                // don't want to delete later
                unset($current_files[$file_id]);
            }
        }
        // save what needs saving
        foreach ($save_files as $save) {
            $save->save();
        }
        // delete the rest
        foreach ($current_files as $curr) {
            $curr->delete();
        }

        $this->cleanCompressedFile();
    }

    /**
     * removes a protected file associated with the collection.
     *
     * @param $file_id
     *
     * @return bool
     */
    public function removeFileById($file_id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('collection_id = :cid');
        $criteria->addCondition('file_id = :fid');
        $criteria->params = array(':cid' => $this->id, ':fid' => $file_id);
        if ($assoc = OphCoTherapyapplication_FileCollectionAssignment::model()->find($criteria)) {
            if ($assoc->delete()) {
                $this->cleanCompressedFile();

                return true;
            }
        } else {
            return false;
        }
    }
}
