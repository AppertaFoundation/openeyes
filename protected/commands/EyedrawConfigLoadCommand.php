<?php
/**
* OpenEyes
*
* (C) OpenEyes Foundation, 2019
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @package OpenEyes
* @link http://www.openeyes.org.uk
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2019, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/
/**
* Class EyedrawConfigLoadCommand
*
* Loader command for handling the eyedraw doodle configuration for object persistence.
*/

// TODO: Change IMG_URL to IMAGE_CLASS and place in DOODLE_USAGE_LIST????

class EyedrawConfigLoadCommand extends CConsoleCommand {
  public $defaultAction = 'load';
  const DOODLE_TBL        = 'eyedraw_doodle';
  const CANVS_TBL         = 'eyedraw_canvas';
  const CANVAS_DOODLE_TBL = 'eyedraw_canvas_doodle';
  private $searchable_terms = [];

  public function getName() {
    return 'Load eyedraw configuration';
  }

  public function getHelp() {
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


    // iterate through the data structure, updating (IndexSearch_{{event}})view files as appropriate

    foreach ($data->EVENT_LIST->EVENT as $event){
      $this->processEventDefinition($event);
    }

  }

  /**
  * Method to run after any changes to Eyedraw configuration to ensure the intersection tuples are defined
  * correctly for each doodle.
  */
  private function refreshTuples() {
    $query_string = $this->getRefreshTuplesQuery();
    Yii::app()->db->createCommand(
      $query_string
      )->query();
    }

    /**
    * @param $canvas
    * @param $element_type
    */
    private function insertOrUpdateCanvas($canvas, $element_type) {
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
    private function insertOrUpdateDoodle($doodle) {
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
    protected function isCanvasDefined($mnemonic) {
      return $this->getDb()
      ->createCommand('SELECT count(*) FROM ' . static::CANVS_TBL
      . ' WHERE canvas_mnemonic = :cvmn')
      ->bindValue(':cvmn', $mnemonic)
      ->queryScalar() > 0;
    }
    /**
    * @param $canvas_doodle
    */
    private function insertOrUpdateCanvasDoodle($canvas_doodle) {
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
        . 'eyedraw_carry_forward_canvas_flag = :ecfcf, '
        . 'eyedraw_always_init_canvas_flag = :eaicf '
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
        . 'eyedraw_carry_forward_canvas_flag, '
        . 'eyedraw_always_init_canvas_flag)'
        . 'VALUES (:ecm, :cm, :eoctl, :eocto, :enticf, :ecfcf, :eaicf)');
      }

      $cmd->bindValue(':ecm', $canvas_doodle->EYEDRAW_CLASS_MNEMONIC)
      ->bindValue(':cm', $canvas_doodle->CANVAS_MNEMONIC)
      ->bindValue(':eoctl', $canvas_doodle->ON_TOOLBAR_LOCATION)
      ->bindValue(':eocto', $canvas_doodle->ON_TOOLBAR_ORDER)
      ->bindValue(':enticf', strtolower($canvas_doodle->NEW_EYE_INIT_FLAG) === 'true')
      ->bindValue(':ecfcf', strtolower($canvas_doodle->CARRY_FORWARD_FLAG) === 'true')
      ->bindValue(':eaicf', (!empty($canvas_doodle->INIT_ALWAYS_FLAG) && strtolower($canvas_doodle->INIT_ALWAYS_FLAG) === 'true'))
      ->query();
    }

    /**
    *
    * @param $canvas
    */
    protected function processCanvasDefinition($canvas) {
      // verify that the element type exists for this definition
      if ($element_type = ElementType::model()->findByAttributes(array('class_name' => $canvas->OE_ELEMENT_CLASS_NAME))) {
        $this->insertOrUpdateCanvas($canvas, $element_type);
      }
    }

    /**
    * @param $doodle
    */
    protected function processDoodleDefinition($doodle) {
      $this->insertOrUpdateDoodle($doodle);
    }

    /**
    * @param $canvas_doodle
    */
    protected function processCanvasDoodleDefinition($canvas_doodle) {
      //Use the canvas mnemonic to confirm whether or not it should be setup in the db.
      $this->insertOrUpdateCanvasDoodle($canvas_doodle);
    }

    /**
    * @param $event
    */
    protected function processEventDefinition($event) {
      $index_list = $event->INDEX_LIST;
      $event_name = $event->EVENT_NAME;
      $this->searchable_terms["$event_name"] = [];
      $this->updateEventIndexSearchHTML($index_list,$event_name);
    }

    /**
    * @return string
    */
    private function getRefreshTuplesQuery() {
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

    /**
     * @param $open_element_class_name
     * @return mixed
     * @throws SystemException
     */
    private function getElementId($open_element_class_name) {
        if ($element_type = ElementType::model()->findByAttributes(array('class_name' => $open_element_class_name))) {
            return $element_type->id;
        } else {
            throw new SystemException("Unable to find element type for {$open_element_class_name}. Have you fully migrated your database?");
        }
    }

    /**
     * @param $open_element_class_name
     * @return mixed
     * @throws SystemException
     */
    private function getElementName($open_element_class_name) {
        if ($element_type = ElementType::model()->findByAttributes(array('class_name' => $open_element_class_name))) {
            return $element_type->name;
        } else {
            throw new SystemException("unable to find element type for {$open_element_class_name}.");
        }
    }

    /**
    * This method generates a string that represents the HTML (with hidden data) of
    * an INDEX and all of its descendant.
    * Due to the recursive definition of INDEX a recursive approach has been taken
    * as the method returns the string HTML of INDEXES nested inside of it.
    * @param $index
    * @param $lvl
    * @return string
    */
    private function generateIndexHTML($index, $event_name, $lvl=1) {
      $this->addEventSearchableTerms($event_name,$index->PRIMARY_TERM,$index->SECONDARY_TERM_LIST->TERM);
      return
      "<li style>"
      .$this->generateIndexMainDiv($index,$lvl)
      .$this->generateAdditionalInfoDiv($index,$lvl)
      .$this->generateChildren($index,$event_name,$lvl)
      ."</li>";
    }

    private function addEventSearchableTerms($event_name, $primary_term, $secondary_term_list) {
      $secondary_term_array = (array) $secondary_term_list;
      array_push($this->searchable_terms["$event_name"],$primary_term);
      foreach ($secondary_term_array as $term) {
        array_push($this->searchable_terms["$event_name"],$term);
      }
    }

    private function getIndexMainDiv($div_attr,$span_attr,$primary_term) {
      return
      "<div $div_attr>"
      ."<span $span_attr>"
      ."$primary_term"
      ."</span>"
      ."</div>";
    }

    private function getDataAttributes($map, $index) {
      $attrs = array();
      foreach ($map as $data_key => $index_attr) {
        if ($index->$index_attr) {
          $attrs[$data_key] = $index->$index_attr;
        }
      }
      return implode(' ', array_map(function($k, $v) { return "data-{$k}='{$v}'"; },array_keys($attrs),$attrs));
    }

    private function getMainDivDivData($index) {
      $data_map_attrs = array(
        'goto-id' => 'GOTO_ID',
        'goto-subcontainer' => 'GOTO_SUBCONTAINER_CLASS',
        'goto-tag' => 'GOTO_TAG',
        'goto-text' => 'GOTO_TEXT',
        'element-class-name' => 'OPEN_ELEMENT_CLASS_NAME',
        'doodle-class-name' => 'GOTO_DOODLE_CLASS_NAME',
        'property' => 'GOTO_PROPERTY'
      );
      $computed_data = "";
      $open_element_class_name = $index->OPEN_ELEMENT_CLASS_NAME;
      if ($open_element_class_name){
        $computed_data =
        "data-element-id=\"{$this->getElementId($open_element_class_name)}\" "
        ."data-element-name=\"{$this->getElementName($open_element_class_name)}\" ";
      }
      return $computed_data.$this->getDataAttributes($data_map_attrs, $index);
    }

    private function getMainDivDivAttr($index){
      return "{$this->getMainDivDivImage($index)} {$this->getMainDivDivClass($index)} {$this->getMainDivDivData($index)}";
    }

    private function getMainDivDivImage($index)
    {
        $result = "";
        if ($index->IMG_URL) {
            $path = $index->IMG_URL;
            $image_URL = '<?php
        if (file_exists(\'' . $path . '\')){
          echo Yii::app()->getAssetManager()->publish(\'' . $path . '\');
        } else {
          echo "";
        }
        ?>';
            $result = "style=\"background-image: url({$image_URL}); background-repeat: no-repeat; padding-left: 5px; background-size: 16px 16px;\"";
        }

        return $result;
    }

    private function getMainDivDivClass($index)
    {
        if ($index->IMG_URL) {
            return "class=\"result_item result_item_with_icon\"";
        } else {
            return "class=\"result_item\"";
        }
    }

    private function getMainDivSpanAttr($index,$lvl) {
      $primary_term = $index->PRIMARY_TERM;
      $secondary_terms_array = (array) $index->SECONDARY_TERM_LIST->TERM;
      $secondary_terms_string = implode(",",$secondary_terms_array);
      $complete_term_list = $secondary_terms_string ? $primary_term.",".$secondary_terms_string : $primary_term;
      return "data-alias=\"{$complete_term_list}\" class=\"lvl{$lvl}\"";
    }

    private function generateIndexMainDiv($index,$lvl) {
      $div_attr = $this->getMainDivDivAttr($index);
      $span_attr = $this->getMainDivSpanAttr($index,$lvl);
      $primary_term = $index->PRIMARY_TERM;
      return $this->getIndexMainDiv($div_attr,$span_attr,$primary_term);
    }

    private function getAdditionalInfoLeftCol($secondary_term_list,$lvl) {
      $secondary_terms = (array)$secondary_term_list;
      return
      "<div class=\"index_row row\">"
      ."<div class=\"index_col_left"."_lvl{$lvl}\">"
      ."<span class=\"alias\">"
      .($secondary_terms ? (implode(", ",$secondary_terms)) : (""))
      ."</span>"
      ."</div>";
    }

    private function getAdditionalInfoRightCol($description,$warning,$info) {
      return
      "<div class=\"index_col_right\">"
      .$this->generateIndexDescription($description,$warning,$info)
      .$this->generateIndexWarning($warning)
      .$this->generateIndexInfo($info)
      ."</div></div>";
    }

    private function generateIndexDescription($description,$warning,$info) {
      $result="";
      if ($description){
        $result = "<p class=\"description_note note\">{$description}</p>";
      }
      return ($warning || $info) ? "{$result}<br>" : $result;
    }

    private function generateIndexWarning($warning) {
      $result = "";
      if ($warning){
        $result =
        "<span class=\"warning_icon\"></span>"
        ."<span class=\"warning_note\">{$warning}</span>";
      }
      return $result;
    }

    private function generateIndexInfo($info) {
      $result = "";
      if ($info){
        $result =
        "<span class=\"info_icon\"></span>"
        ."<p class=\"info_note\">{$info}</p>";
      }
      return $result;
    }

    private function generateAdditionalInfoDiv($index,$lvl) {
      $result = "";
      $secondary_term_list = $index->SECONDARY_TERM_LIST->TERM;
      $description = $index->DESCRIPTION;
      $warning = $index->WARNING_NOTE;
      $info = $index->GENERAL_NOTE;
      if ($secondary_term_list || $description || $warning || $info){
        $result .= $this->getAdditionalInfoLeftCol($secondary_term_list,$lvl); //opens row tag
        $result .= $this->getAdditionalInfoRightCol($description,$warning,$info); //closes row tag
      }
      return $result;
    }

    private function generateChildren($index,$event_name,$lvl) {
      $result = "";
      $children = $index->INDEX_LIST;
      if ($children) {
        $result .= "<ul class='results_list'>";
        foreach ($children->INDEX as $child) {
          $result .= $this->generateIndexHTML($child,$event_name,$lvl+1);
        }
        $result .= "</ul>";
      }
      return $result;
    }

    /**
    * Method uses INDEX_LIST section of xml to update
    * the IndexSearch_{{event_type_name}} view files
    * for the IndexSearch widget used in some events,
    * for examaple Examination events).
    * @param $index_list
    * @param $event_name
    */
    private function updateEventIndexSearchHTML($index_list,$event_name) {
      $html_string =
      $this->getIndexSearchHeader()
      .$this->getIndexSearchResultsHTML($index_list,$event_name)
      .$this->getIndexSearchHiddenTerms($event_name);
      $html_string = $this->formatHTML($html_string);
      $this->saveHTMLToFile($html_string, Yii::getPathOfAlias('application') . "/widgets/views/IndexSearch_{$event_name}.php");
    }

    private function getIndexSearchHeader() {
      return
      "<?php \$this->render('IndexSearch_header'); ?>"
      ."<div id=\"elements-search-results\" class=\"elements-search-results search-results\" style=\"display:none;\">"
      ."<div class=\"close-icon-btn\"><i class=\"oe-i remove-circle\"></i></div>"
      ."<ul class='results_list'>";
    }

    private function getIndexSearchHiddenTerms($event_name) {
      $searchable_terms_JSON = '[';
      $unique = array_unique($this->searchable_terms["$event_name"]);
      foreach ($unique as $search_term) {
        $words = explode(" ",$search_term);
        //inserts full term and each word in appropriate searchable_terms array
        if (sizeof($words) > 1) {
          $searchable_terms_JSON .= "\"".strtolower($search_term)."\",";
        }
        foreach ($words as $word) {
          $searchable_terms_JSON .= "\"".strtolower($word)."\",";
        }
      }
      $searchable_terms_JSON=rtrim($searchable_terms_JSON,", ");
      $searchable_terms_JSON .= ']';
      return"<input id=\"searchable_terms\" hidden data-searchable-terms='$searchable_terms_JSON'/>";
    }

    private function formatHTML($html) {
      $format = new Format;
      return $format->HTML($html);
    }

    private function saveHTMLToFile($html,$filename) {
      $file = Yii::getPathOfAlias('application') . '/widgets/views/IndexSearch_Examination.php';
      $file_handle = fopen($file, 'w') or die('Cannot open file:  '.$file);
      fwrite($file_handle, $html);
      fclose($file_handle);
    }

    private function getIndexSearchResultsHTML($index_list,$event_name) {
      $results = "";
      foreach ($index_list->INDEX as $index) {
        //appends HTML for the index and all of its descendants
        $results .= $this->generateIndexHTML($index,$event_name);
      }
      $results .= "</ul></div></div>";
      return $results;
    }

  }


//Format library (Not my code)
class Format
{
    private $input = '';
    private $output = '';
    private $tabs = 0;
    private $in_tag = FALSE;
    private $in_comment = FALSE;
    private $in_content = FALSE;
    private $inline_tag = FALSE;
    private $input_index = 0;

    public function HTML($input)
    {
        $this->input = $input;
        $this->output = '';

        $starting_index = 0;

        if (preg_match('/<\!doctype/i', $this->input)) {
            $starting_index = strpos($this->input, '>') + 1;
            $this->output .= substr($this->input, 0, $starting_index);
        }

        for ($this->input_index = $starting_index; $this->input_index < strlen($this->input); $this->input_index++) {
            if ($this->in_comment) {
                $this->parse_comment();
            } elseif ($this->in_tag) {
                $this->parse_inner_tag();
            } elseif ($this->inline_tag) {
                $this->parse_inner_inline_tag();
            } else {
                if (preg_match('/[\r\n\t]/', $this->input[$this->input_index])) {
                    continue;
                } elseif ($this->input[$this->input_index] == '<') {
                    if ( ! $this->is_inline_tag()) {
                        $this->in_content = FALSE;
                    }
                    $this->parse_tag();
                } elseif ( ! $this->in_content) {
                    if ( ! $this->inline_tag) {
                        $this->output .= "\n" . str_repeat("\t", $this->tabs);
                    }
                    $this->in_content = TRUE;
                }
                $this->output .= $this->input[$this->input_index];
            }
        }

        return $this->output;
    }

    private function parse_comment()
    {
        if ($this->is_end_comment()) {
            $this->in_comment = FALSE;
            $this->output .= '-->';
            $this->input_index += 3;
        } else {
            $this->output .= $this->input[$this->input_index];
        }
    }

    private function parse_inner_tag()
    {
        if ($this->input[$this->input_index] == '>') {
            $this->in_tag = FALSE;
            $this->output .= '>';
        } else {
            $this->output .= $this->input[$this->input_index];
        }
    }

    private function parse_inner_inline_tag()
    {
        if ($this->input[$this->input_index] == '>') {
            $this->inline_tag = FALSE;
            $this->decrement_tabs();
            $this->output .= '>';
        } else {
            $this->output .= $this->input[$this->input_index];
        }
    }

    private function parse_tag()
    {
        if ($this->is_comment()) {
            $this->output .= "\n" . str_repeat("\t", $this->tabs);
            $this->in_comment = TRUE;
        } elseif ($this->is_end_tag()) {
            $this->in_tag = TRUE;
            $this->inline_tag = FALSE;
            $this->decrement_tabs();
            if ( ! $this->is_inline_tag() AND ! $this->is_tag_empty()) {
                $this->output .= "\n" . str_repeat("\t", $this->tabs);
            }
        } else {
            $this->in_tag = TRUE;
            if ( ! $this->in_content AND ! $this->inline_tag) {
                $this->output .= "\n" . str_repeat("\t", $this->tabs);
            }
            if ( ! $this->is_closed_tag()) {
                $this->tabs++;
            }
            if ($this->is_inline_tag()) {
                $this->inline_tag = TRUE;
            }
        }
    }

    private function is_end_tag()
    {
        for ($input_index = $this->input_index; $input_index < strlen($this->input); $input_index++) {
            if ($this->input[$input_index] == '<' AND $this->input[$input_index + 1] == '/') {
                return true;
            } elseif ($this->input[$input_index] == '<' AND $this->input[$input_index + 1] == '!') {
                return true;
            } elseif ($this->input[$input_index] == '>') {
                return false;
            }
        }
        return false;
    }

    private function decrement_tabs()
    {
        $this->tabs--;
        if ($this->tabs < 0) {
            $this->tabs = 0;
        }
    }

    private function is_comment()
    {
        if ($this->input[$this->input_index] == '<'
        AND $this->input[$this->input_index + 1] == '!'
        AND $this->input[$this->input_index + 2] == '-'
        AND $this->input[$this->input_index + 3] == '-') {
            return true;
        } else {
            return false;
        }
    }

    private function is_end_comment()
    {
        if ($this->input[$this->input_index] == '-'
        AND $this->input[$this->input_index + 1] == '-'
        AND $this->input[$this->input_index + 2] == '>') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function is_tag_empty()
    {
        $current_tag = $this->get_current_tag($this->input_index + 2);
        $in_tag = FALSE;

        for ($input_index = $this->input_index - 1; $input_index >= 0; $input_index--) {
            if ( ! $in_tag) {
                if ($this->input[$input_index] == '>') {
                    $in_tag = TRUE;
                } elseif ( ! preg_match('/\s/', $this->input[$input_index])) {
                    return FALSE;
                }
            } else {
                if ($this->input[$input_index] == '<') {
                    if ($current_tag == $this->get_current_tag($input_index + 1)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                }
            }
        }
        return TRUE;
    }

    private function get_current_tag($input_index)
    {
        $current_tag = '';

        for ($input_index; $input_index < strlen($this->input); $input_index++) {
            if ($this->input[$input_index] == '<') {
                continue;
            } elseif ($this->input[$input_index] == '>' OR preg_match('/\s/', $this->input[$input_index])) {
                return $current_tag;
            } else {
                $current_tag .= $this->input[$input_index];
            }
        }

        return $current_tag;
    }

    private function is_closed_tag()
    {
        $closed_tags = array(
            'meta', 'link', 'img', 'hr', 'br', 'input',
        );

        $current_tag = '';

        for ($input_index = $this->input_index; $input_index < strlen($this->input); $input_index++) {
            if ($this->input[$input_index] == '<') {
                continue;
            } elseif (preg_match('/\s/', $this->input[$input_index])) {
                break;
            } else {
                $current_tag .= $this->input[$input_index];
            }
        }

        if (in_array($current_tag, $closed_tags)) {
            return true;
        } else {
            return false;
        }
    }

    private function is_inline_tag()
    {
        $inline_tags = array(
            'title', 'a', 'span', 'abbr', 'acronym', 'b', 'basefont', 'bdo', 'big', 'cite', 'code', 'dfn', 'em', 'font', 'i', 'kbd', 'q', 's', 'samp', 'small', 'strike', 'strong', 'sub', 'sup', 'textarea', 'tt', 'u', 'var', 'del', 'pre',
        );

        $current_tag = '';

        for ($input_index = $this->input_index; $input_index < strlen($this->input); $input_index++) {
            if ($this->input[$input_index] == '<' OR $this->input[$input_index] == '/') {
                continue;
            } elseif (preg_match('/\s/', $this->input[$input_index]) OR $this->input[$input_index] == '>') {
                break;
            } else {
                $current_tag .= $this->input[$input_index];
            }
        }

        if (in_array($current_tag, $inline_tags)) {
            return true;
        } else {
            return false;
        }
    }
}
//End of Format library
