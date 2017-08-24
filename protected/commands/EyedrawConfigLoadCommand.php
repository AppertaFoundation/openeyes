<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
/**
 * Class EyedrawConfigLoadCommand
 *
 * Loader command for handling the eyedraw doodle configuration for object persistence.
 * Initial implementation is using a basic json config file that is expected to be superceded
 * by an XML document to define this information.
 */
class EyedrawConfigLoadCommand extends CConsoleCommand
{
    public $defaultAction = 'load';
    const DOODLE_TBL        = 'eyedraw_doodle';
    const CANVS_TBL         = 'eyedraw_canvas';
    const CANVAS_DOODLE_TBL = 'eyedraw_canvas_doodle';
    public function getName()

    {
        return 'Load eyedraw configuration';
    }
    public function getHelp()
    {
        return "yiic eyedrawconfigload --filename=<filename>\n\n".
        "load/update the eyedraw configuration from the definition file <filename>";
    }
    /**
     * Abstraction to the db connection
     * @return mixed
     */
    protected function getDb()
    {
        // TODO: stop using the static Yii call here
        return Yii::app()->db;
    }
    /**
     * Default action to process the given configuration file.
     *
     * @param $filename
     */
    public function actionLoad($filename)
    {
        if (!$filename) {
            $this->usageError('Please supply the path to the eyedraw configuration file.');
        }

        if (file_exists($filename)) {
            $data = simplexml_load_file($filename);
        } else {
            $this->usageError($filename.' does not exist');
        }

        if ($data === null) {
            $this->usageError($filename . ' is not in a valid format');
        }


        // iterate through the data structure, performing update/insert statements as appropriate

        foreach ($data->CANVAS_LIST->CANVAS as $canvas){
            $this->processCanvasDefinition($canvas);
        }

        foreach ($data->DOODLE_LIST->DOODLE as $doodle){
            $this->processDoodleDefinition($doodle);
        }

        foreach ($data->DOODLE_USAGE_LIST->DOODLE_USAGE as $canvas_doodle){
            $this->processCanvasDoodleDefinition($canvas_doodle);
        }
        $this->refreshTuples();

			/**
        * Generates HTML file from the OE_ED_CONFIG.xml (INDEX_LIST).
        * XML may need to change when creating multiple HTML files
        * as only 1 INDEX_LIST is permitted and there is no way to
        * tell which HTML file to write to.
        */

        $this->updateIndexSearchHtml($data);
    }

    /**
    * Method uses INDEX_LIST section of xml to update
    * the IndexSearch_{{event_type}} view files
    * for the IndexSearch widget used in some events,
    * for examaple Examination events).
     */
     /**
      * @param $data
      */
    private function updateIndexSearchHtml($data)
    {
      $html_string =
      "<?php \$this->render('IndexSearch_header'); ?>"
      ."<div id=\"results\">"
      ."<a href=\"#\" id=\"big_cross\"></a>"
      ."<ul class='results_list'>";

      foreach ($data->INDEX_LIST->INDEX as $index) {
        //appends HTML for the index and all of its descendant
        $html_string .= $this->generateIndexHTML($index);
      }

      $file = 'widgets/views/IndexSearch_examination.php';
      $file_handle = fopen($file, 'w') or die('Cannot open file:  '.$file);
      fwrite($file_handle, $html_string."</ul></div>");
    }

    /**
     * Method to run after any changes to Eyedraw configuration to ensure the intersection tuples are defined
     * correctly for each doodle.
     */
    private function refreshTuples()
    {
        $query_string = $this->getRefreshTuplesQuery();
        Yii::app()->db->createCommand(
            $query_string
        )->query();
    }

    /**
     * @param $canvas
     * @param $element_type
     */
    private function insertOrUpdateCanvas($canvas, $element_type)
    {
        $current = $this->getDb()
        ->createCommand('SELECT count(*) FROM ' . static::CANVS_TBL . ' WHERE container_element_type_id = :eid')
        ->bindValue(':eid', $element_type->id)
        ->queryScalar();
        if ($current) {
            $cmd = $this->getDb()
            ->createCommand('UPDATE '
                . static::CANVS_TBL .
                ' SET canvas_mnemonic = :cvmn, canvas_name = :cvname where container_element_type_id = :eid');
        } else {
            $cmd = $this->getDb()
            ->createCommand('INSERT INTO ' . static::CANVS_TBL .
                '(canvas_mnemonic, canvas_name, container_element_type_id) VALUES (:cvmn, :cvname, :eid)');
        }
        $cmd->bindValue(':cvmn', $canvas->CANVAS_MNEMONIC)
            ->bindValue(':cvname', $canvas->CANVAS_NAME)
            ->bindValue(':eid', $element_type->id)
            ->query();
    }

    /**
     * Create or update a doodle definition
     *
     * @param $doodle
     */
    private function insertOrUpdateDoodle($doodle)
    {
        $current = $this->getDb()
            ->createCommand('SELECT count(*) FROM ' . static::DOODLE_TBL . ' WHERE eyedraw_class_mnemonic = :mnm')
            ->bindValue(':mnm', $doodle->EYEDRAW_CLASS_MNEMONIC)
            ->queryScalar();
        if ($current) {
            $cmd = $this->getDb()->createCommand('UPDATE ' . static::DOODLE_TBL . ' SET init_doodle_json = :init '
                . 'WHERE eyedraw_class_mnemonic = :mnm');
        } else {
            $cmd = $this->getDb()->createCommand('INSERT INTO '
                . static::DOODLE_TBL .
                '(eyedraw_class_mnemonic, init_doodle_json) VALUES (:mnm, :init)');
        }
        $cmd->bindValue(':mnm', $doodle->EYEDRAW_CLASS_MNEMONIC)
            ->bindValue(':init', $doodle->INIT_DOODLE_JSON)
            ->query();
    }

    /**
     * @param $mnemonic
     * @return bool
     */
    protected function isCanvasDefined($mnemonic)
    {
        return $this->getDb()
            ->createCommand('SELECT count(*) FROM ' . static::CANVS_TBL
                . ' WHERE canvas_mnemonic = :cvmn')
            ->bindValue(':cvmn', $mnemonic)
            ->queryScalar() > 0;
    }
    /**
     * @param $canvas_doodle
     */
    private function insertOrUpdateCanvasDoodle($canvas_doodle)
    {

        if (!$this->isCanvasDefined($canvas_doodle->CANVAS_MNEMONIC)) {
            // if the element is not part of the configuration (module not included)
            // then we don't load the canvas, and therefore don't load the canvas doodle
            return;
        }

        $current = $this->getDb()
            ->createCommand('SELECT count(*) FROM ' . static::CANVAS_DOODLE_TBL
                . ' WHERE eyedraw_class_mnemonic = :ecmm'
                . ' AND canvas_mnemonic = :cmm')
            ->bindValue(':ecmm', $canvas_doodle->EYEDRAW_CLASS_MNEMONIC)
            ->bindValue(':cmm', $canvas_doodle->CANVAS_MNEMONIC)
            ->queryScalar();
        if ($current) {
            $cmd = $this->getDb()
                ->createCommand('UPDATE ' . static::CANVAS_DOODLE_TBL
                    . ' SET eyedraw_on_canvas_toolbar_location = :eoctl, '
                    . 'eyedraw_on_canvas_toolbar_order = :eocto, '
                    . 'eyedraw_no_tuple_init_canvas_flag = :enticf, '
                    . 'eyedraw_carry_forward_canvas_flag = :ecfcf '
                    . 'WHERE eyedraw_class_mnemonic = :ecm '
                    . 'AND canvas_mnemonic = :cm');
        } else {
            $cmd = $this->getDb()
                ->createCommand('INSERT INTO ' . static::CANVAS_DOODLE_TBL . ' ('
                    . 'eyedraw_class_mnemonic, '
                    . 'canvas_mnemonic, '
                    . 'eyedraw_on_canvas_toolbar_location, '
                    . 'eyedraw_on_canvas_toolbar_order, '
                    . 'eyedraw_no_tuple_init_canvas_flag, '
                    . 'eyedraw_carry_forward_canvas_flag) '
                    . 'VALUES (:ecm, :cm, :eoctl, :eocto, :enticf, :ecfcf)');
        }

        $cmd->bindValue(':ecm', $canvas_doodle->EYEDRAW_CLASS_MNEMONIC)
            ->bindValue(':cm', $canvas_doodle->CANVAS_MNEMONIC)
            ->bindValue(':eoctl', $canvas_doodle->ON_TOOLBAR_LOCATION)
            ->bindValue(':eocto', $canvas_doodle->ON_TOOLBAR_ORDER)
            ->bindValue(':enticf', strtolower($canvas_doodle->NEW_EYE_INIT_FLAG) === 'true')
            ->bindValue(':ecfcf', strtolower($canvas_doodle->CARRY_FORWARD_FLAG) === 'true')
            ->query();
    }

    /**
     *
     * @param $canvas
     */
    protected function processCanvasDefinition($canvas)
    {
        // verify that the element type exists for this definition
        if ($element_type = ElementType::model()->findByAttributes(array('class_name' => $canvas->OE_ELEMENT_CLASS_NAME))) {
            $this->insertOrUpdateCanvas($canvas, $element_type);
        }
    }

    /**
     * @param $doodle
     */
    protected function processDoodleDefinition($doodle)
    {
        $this->insertOrUpdateDoodle($doodle);
    }

    /**
     * @param $canvas_doodle
     */
    protected function processCanvasDoodleDefinition($canvas_doodle) //must be run last to prevent error
    {
        //Use the canvas mnemonic to confirm whether or not it should be setup in the db.
        $this->insertOrUpdateCanvasDoodle($canvas_doodle);
    }

    /**
     * @return string
     */
    private function getRefreshTuplesQuery()
    {
        $doodle_tbl = static::DOODLE_TBL;
        $doodle_canvas_tbl = static::CANVAS_DOODLE_TBL;

        return <<<EOSQL
-- Update the Doodle Tuples
UPDATE $doodle_tbl ed
SET ed.processed_canvas_intersection_tuple = (
    SELECT GROUP_CONCAT(DISTINCT ecd.canvas_mnemonic ORDER BY ecd.canvas_mnemonic) -- canvas_mnenonic
  FROM $doodle_canvas_tbl ecd
  WHERE ecd.eyedraw_class_mnemonic = ed.eyedraw_class_mnemonic
    AND EXISTS (
        SELECT 1
    FROM $doodle_canvas_tbl in_ecd
    WHERE in_ecd.canvas_mnemonic = ecd.canvas_mnemonic
    AND in_ecd.eyedraw_carry_forward_canvas_flag = 1
  )
  GROUP BY ecd.eyedraw_class_mnemonic
  HAVING SUM(ecd.eyedraw_carry_forward_canvas_flag) != 0
)
WHERE ed.eyedraw_class_mnemonic != "*" -- Unsafe mode workaround
EOSQL;
    }

private function get_element_id($open_element_class_name){
  $element_type = ElementType::model()->findByAttributes(array('class_name' => $open_element_class_name));
  $element_id = $element_type->id;
  return $element_id;
}

private function get_element_name($open_element_class_name){
  $element_type = ElementType::model()->findByAttributes(array('class_name' => $open_element_class_name));
  $element_name = $element_type->name;
  return $element_name;
}

  /**
    * This method generates a string that represents the HTML of
    * an INDEX and all of its descendant.
    * Due to the recursive definition of INDEX a recursive approach has been taken
    * as the method returns the string HTML of INDEXES nested inside of it.
    */
    /**
    * @param $index
    * @param $lvl
    * @return string
    */
    private function generateIndexHTML($index, $lvl=1){
      $description = $index->DESCRIPTION;
      $warning = $index->WARNING_NOTE;
      $info = $index->GENERAL_NOTE;
      $fake_array = (array)$index->TERM_LIST->TERM;
      $alias = implode(",",$fake_array);
      $name = array_shift($fake_array);

      $primary_term = $index->PRIMARY_TERM;
      $secondary_term_list_array = (array)$index->SECONDARY_TERM_LIST->TERM;
      $secondary_term_list = implode(",",$secondary_term_list_array);
      $complete_term_list = $secondary_term_list_array ? $primary_term.",".$secondary_term_list : $primary_term;

      $alias_minus_name = implode(",",$fake_array);
      $img = $index->IMG_URL;
      $children = $index->INDEX_LIST;
      $open_element_class_name = $index->OPEN_ELEMENT_CLASS_NAME;
      $goto_doodle_class_name = $index->GOTO_DOODLE_CLASS_NAME;
      $goto_property = $index->GOTO_PROPERTY;
      $goto_subcontainer = $index->GOTO_SUBCONTAINER_CLASS;
      $goto_id = $index->GOTO_ID;
      $goto_tag = $index->GOTO_TAG;
      $goto_text = $index->GOTO_TEXT;
      $result =
      "<li style>"
      ."<div class=\"result_item"
      //changed from IMG_URL to class name with image property
      .($img ? (" result_item_with_icon is_".$goto_doodle_class_name."\"") : (""))
      ."\" "
      .($goto_id ? ("data-goto-id=\"".$goto_id."\" ") : (""))
      .($goto_subcontainer ? ("data-goto-subcontainer=\"".$goto_subcontainer."\" ") : (""))
      .($goto_tag ? ("data-goto-tag=\"".$goto_tag."\" ") : (""))
      .($goto_text ? ("data-goto-text=\"".$goto_text."\" ") : (""))
      .($open_element_class_name ? ("data-element-class-name=\"".$open_element_class_name."\" ") : (""))
      .($open_element_class_name ? ("data-element-id=\"".$this->get_element_id($open_element_class_name)."\" ") : (""))
      .($open_element_class_name ? ("data-element-name=\"".$this->get_element_name($open_element_class_name)."\" ") : (""))
      .($goto_doodle_class_name ? ("data-doodle-class-name=\"".$goto_doodle_class_name."\" ") : (""))
      .($goto_property ? ("data-property=\"".$goto_property."\"") : (""))
      .">"
      ."<span data-alias='".$complete_term_list."' "
      ."class='lvl".$lvl."'>"
      .$primary_term."</span>"
      ."</div>"
      .(($secondary_term_list || $description || $warning || $info) ? ( //TODO allow for description only
        "<div class=\"index_row\">"
        ."<div class=\"index_col_left\">"
        ."<span class=\"alias\">"
        .($secondary_term_list ? ($secondary_term_list) : (""))
        ."</span>"
        ."</div>"
        ."<div class=\"index_col_right\">"
        .($description ? (
          "<span class=\"description_icon\">Description:</span>"
          ."<span class=\"description_note\">"
          .$description
          ."<br></span>"
          ) : (""))
        .($warning ? (
          "<span class=\"warning_icon\"></span>"
          ."<span class=\"warning_note\">"
          .$warning
          ."</span>"
          ) : (""))
        .($info ? (
          "<span class=\"info_icon\"></span>"
          ."<span class=\"info_note\">"
          .$info
          ."</span>"
          ) : (""))
        ."</div>"
        ."</div>"
        ) : (""));
      if ($children) {
        $result .= "<ul class='results_list'>";
        foreach ($children->INDEX as $child) {
          $result .= $this->generateIndexHTML($child,$lvl+1);
        }
        $result .= "</ul>";
      }
      $result .= "</li>";
      return $result;
    }
}
