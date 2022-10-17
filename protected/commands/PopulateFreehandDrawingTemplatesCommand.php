<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2022, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class PopulateFreehandDrawingTemplatesCommand extends CConsoleCommand
{
    private $lock_name = "PopulateFreehandDrawingTemplatesCommand_lock";

    public function getHelp()
    {
        return "Re-populate any freehand drawing templates from the sample database that are missing from the protected files directories\n";
    }

    public function run($args)
    {
        $t = microtime(true);
        echo "[" . (date("Y-m-d H:i:s")) . "] PopulateFreehandDrawingTemplates ... ";

        if ($this->isRunning() || !$this->acquireLock()) {
            echo "Another process is already being run." . PHP_EOL;
            exit(1);
        }

        register_shutdown_function(function () {
            $this->releaseLock();
        });

        $path = Yii::getPathOfAlias('application') . '/migrations/data/freehand_templates/';

        echo "\nRe-importing freehand drawing templates from $path\n";

        $templates = DrawingTemplate::model()->findAll();
        $files = array_diff(scandir($path), array('.', '..'));

        $templates = array_filter($templates, static function ($template) {
            return !$template->protected_file->fileExists();
        });

        $templates = array_reduce(
            $templates,
            static function ($mapped, $template) {
                $mapped[$template->name] = $template;

                return $mapped;
            },
            []
        );

        $data = [];

        foreach ($files as $file_name) {
            echo "... $file_name - ";
            $name = ucfirst(str_replace('_', ' ', str_replace("_background", "", $file_name)));
            $template_name = substr($name, 0, (strrpos($name, ".")));

            if (array_key_exists($template_name, $templates)) {
                $old_file = $templates[$template_name]->protected_file;

                $new_file = ProtectedFile::createFromFile($path . "/$file_name");
                $new_file->name = $name;
                $new_file->title = $name;

                if ($new_file->save()) {
                    $templates[$template_name]->protected_file_id = $new_file->id;

                    if ($templates[$template_name]->save()) {
                        echo "re-imported\n";
                    } else {
                        echo "failed to save template\n";

                        $new_file->delete();
                    }
                } else {
                    echo "failed to save protected file\n";
                }
            } else {
                echo "file exists, did not re-import\n";
            }
        }

        echo "OK - took: " . (microtime(true) - $t) . "\n";
        exit(0);
    }

    private function isRunning()
    {
        return (bool) Yii::app()->db->createCommand(
            'SELECT IS_USED_LOCK(:name)'
        )->bindValue(':name', $this->lock_name)
        ->queryScalar();
    }

    /**
     * @return bool acquiring result
     */
    private function acquireLock()
    {
        return (bool) Yii::app()->db->createCommand(
            'SELECT GET_LOCK(:name, :timeout)',
        )->bindValue(':name', $this->lock_name)
        ->bindValue(':timeout', 0) // timeout = 0 means that the method will return false immediately in case lock is used
        ->queryScalar();
    }

    /**
     * @return bool release result
     */
    private function releaseLock()
    {
        return (bool) Yii::app()->db->createCommand(
            'SELECT RELEASE_LOCK(:name)'
        )->bindValue(':name', $this->lock_name)
        ->queryScalar();
    }
}
