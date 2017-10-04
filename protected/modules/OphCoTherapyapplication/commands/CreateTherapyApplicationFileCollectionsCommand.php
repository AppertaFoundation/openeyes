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
 * Class CreateTherapyApplicationFileCollections.
 *
 * command script to find PDF files in a nested directory structure and create appropriate file collections for use in
 * the Therapy application module
 */
class CreateTherapyApplicationFileCollectionsCommand extends CConsoleCommand
{
    protected $file_extensions = array('pdf');
    protected $summary_text_default = '-';
    protected $summary_filename = 'summary.txt';

    public function getName()
    {
        return 'CreateTherapyApplicationFileCollections';
    }

    public function getHelp()
    {
        return $this->getName().":\n\n".<<<EOH
Will import a directory structure as multiple file collections - A file collection will be created for each leaf
directory containing files with the configured file extension. The file collection name is a concatenation of the
directory names the files are found in.

For now the file summary is simply a "-".

EOH;
    }

    /**
     * main method to run the command for file collection creation.
     *
     * @TODO: look for a summary text file to include.
     * @TODO: search for existing file collections and update instead of adding.
     *
     * @param array $args
     *
     * @return int|void
     */
    public function run($args)
    {
        if (!count($args) == 1) {
            $this->usageError('missing source path argument');
        }

        if (!is_readable($args[0])) {
            $this->usageError('cannot read specified source path '.$args[0]);
        }

        $base_path = $args[0];

        // read directory structure into data
        $file_list = $this->buildFileList($base_path, './');
        $file_ext_regexp = implode('|', $this->file_extensions);

        $sets = array();

        // determine the file collections to be created
        foreach ($file_list as $fname => $details) {
            if (preg_match('/'.$file_ext_regexp.'$/', $fname)) {
                $path = str_replace(DIRECTORY_SEPARATOR, ' - ', dirname($fname));
                if (!@$sets[$path]) {
                    $summary_text = $this->summary_text_default;
                    $summary_filepath = $base_path.dirname($fname).DIRECTORY_SEPARATOR.$this->summary_filename;

                    if ($this->summary_filename
                        && file_exists($summary_filepath)) {
                        // read the summary text in from the file
                        $summary_text = file_get_contents($summary_filepath);
                    }
                    $sets[$path] = array(
                        'summary' => $summary_text,
                        'files' => array($details), );
                } else {
                    $sets[$path]['files'][] = $details;
                }
            }
        }

        $created = 0;
        $modified = 0;
        // iterate through and create the file collections.
        foreach ($sets as $set_name => $set_details) {
            $created_flag = false;
            $transaction = Yii::app()->getDb()->beginTransaction();
            $pf_list = array();
            $pf_ids = array();

            try {
                foreach ($set_details['files'] as $details) {
                    $pf = ProtectedFile::createFromFile($details['source']);
                    if ($pf->save()) {
                        $pf_ids[] = $pf->id;
                        $pf_list[] = $pf;
                    } else {
                        foreach ($pf_list as $pf) {
                            $pf->delete();
                        }
                        break;
                    }
                }

                // update the existing file collection if there is one
                $criteria = new CDbCriteria();
                $criteria->addCondition('name = :nm');
                $criteria->params = array(':nm' => $set_name);
                if (!$fc = OphCoTherapyapplication_FileCollection::model()->find($criteria)) {
                    $fc = new OphCoTherapyapplication_FileCollection();
                    $fc->name = $set_name;
                    $created_flag = true;
                }
                $fc->summary = $set_details['summary'];

                if (!$fc->validate()) {
                    echo "unexpected validation error with file collection\n";
                    var_dump($fc->getErrors());
                    $transaction->rollback();
                } else {
                    if ($fc->save()) {
                        $fc->updateFiles($pf_ids);
                        Audit::add('admin', 'create', $fc->id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_FileCollection'));
                        $transaction->commit();
                        $created_flag ? $created++ : $modified++;
                    } else {
                        foreach ($pf_list as $pf) {
                            $pf->delete();
                        }
                        $transaction->rollback();
                    }
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                foreach ($pf_list as $pf) {
                    $pf->delete();
                }
                $transaction->rollback();
            }
        }

        echo 'Processing complete, '.$created.' collections created, '.$modified." collections updated\n";
    }
}
