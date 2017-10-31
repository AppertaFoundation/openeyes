<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class TreeCrawlerCommand extends CConsoleCommand
{
    public function getName()
    {
        return 'Tree Crawler Command.';
    }

    public function getHelp()
    {
        return "treecrawler <model_class> <snomeds>\n\nfor a comma separated list of snomed codes in the provided model_class, will display child and parent disorders\n";
    }

    public function run($args)
    {
        $kls = $args[0];
        $snomeds = explode(',', $args[1]);

        $test_class = new $kls();

        try {
            $behaviour = $test_class->treeStart();
        } catch (Exception $e) {
            echo "class '$kls' does not implement 'treeBehaviour', exiting ...\n";
            exit();
        }

        // Initialise db
        $db = Yii::app()->db;

        foreach ($snomeds as $snomed) {
            if ($d = $kls::model()->findByPk($snomed)) {
                echo 'Tree of '.$snomed.' - '.$d->term.":\n\n";
                $children = $d->childIds();
                echo 'children ('.count($children)."):\n";
                foreach ($children as $child) {
                    echo $child.': '.$kls::model()->findByPk($child)->term."\n";
                }

                $parents = $d->parentIds();
                echo "------\n\nparents (".count($parents)."):\n";
                foreach ($parents as $parent) {
                    if ($p = $kls::model()->findByPk($parent)) {
                        echo $parent.': '.$p->term."\n";
                    } else {
                        echo 'unknown parent snomed '.$parent."\n";
                    }
                }
                echo "\n";
            } else {
                echo "snomed '".$snomed."' not found in class '".$kls."'. Skipping ...\n";
            }
        }
    }
}
