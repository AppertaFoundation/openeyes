<?php
//namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class IndexSearch extends BaseCWidget
{
    public $event_type = "Examination";
    private $searchable_terms = [];

    public function init()
    {
        parent::init();
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

    public function run()
    {
        try {
            $render_content = $this->processEventDefinition($this->event_type);
            return eval("?>$render_content");
        } catch (Exception $e) {
            //view does not exist in DB
        }
    }

    /**
     * @param $event
     */

    public function processEventDefinition($event)
    {
        $cmdEventID = $this->getDb()->createCommand('SELECT id FROM event_type WHERE name=:eventname ');
        $cmdEventID->bindValue(':eventname', $event);
        $event_id = $cmdEventID->queryScalar();
        $cmd = $this->getDb()->createCommand('SELECT id, parent, primary_term, secondary_term_list, description, general_note, open_element_class_name, goto_id, goto_tag, goto_text, img_url, goto_subcontainer_class, goto_doodle_class_name, goto_property, warning_note FROM index_search WHERE event_type_id=:eventtype AND parent IS NULL');
        $cmd->bindValue(':eventtype', $event_id);
        $index_list = $cmd->queryAll();
        $this->searchable_terms["$event"] = [];
        return $this->updateEventIndexSearchHTML($index_list, $event);
    }


    /**
     * Method uses INDEX_LIST section of xml to update
     * the IndexSearch_{{event_type_name}} view files
     * for the IndexSearch widget used in some events,
     * for examaple Examination events).
     * @param $index_list
     * @param $event_name
     */
    private function updateEventIndexSearchHTML($index_list, $event_name)
    {
        $html_string =
            $this->getIndexSearchHeader()
            . $this->getIndexSearchResultsHTML($index_list, $event_name)
            . $this->getIndexSearchHiddenTerms($event_name);
        $html_string = $this->formatHTML($html_string);
        return $html_string;
    }

    private function getIndexSearchHeader()
    {
        return
            "<?php \$this->render('IndexSearch_header'); ?>"
            . "<div id=\"elements-search-results\" class=\"elements-search-results search-results\" style=\"display:none;\">"
            . "<div class=\"close-icon-btn\"><i class=\"oe-i remove-circle\"></i></div>"
            . "<ul class='results_list'>";
    }

    private function getIndexSearchHiddenTerms($event_name)
    {
        $searchable_terms_JSON = '[';
        $unique = array_unique($this->searchable_terms["$event_name"]);
        foreach ($unique as $search_term) {
            $words = explode(" ", $search_term);
            //inserts full term and each word in appropriate searchable_terms array
            if (sizeof($words) > 1) {
                $searchable_terms_JSON .= "\"" . strtolower($search_term) . "\",";
            }
            foreach ($words as $word) {
                $searchable_terms_JSON .= "\"" . strtolower($word) . "\",";
            }
        }
        $searchable_terms_JSON = rtrim($searchable_terms_JSON, ", ");
        $searchable_terms_JSON .= ']';
        return "<input id=\"searchable_terms\" hidden data-searchable-terms='$searchable_terms_JSON'/>";
    }

    public function formatHTML($html)
    {
        return $this->HTML($html);
    }

    private function getIndexSearchResultsHTML($index_list, $event_name)
    {
        $results = "";
        foreach ($index_list as $index) {
            //appends HTML for the index and all of its descendants
            $results .= $this->generateIndexHTML($index, $event_name);
        }
        $results .= "</ul></div></div>";
        return $results;
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
    private function generateIndexHTML($index, $event_name, $lvl = 1)
    {
        $this->addEventSearchableTerms($event_name, $index['primary_term'], $index['secondary_term_list']);
        return
            "<li style>"
            . $this->generateIndexMainDiv($index, $lvl)
            . $this->generateAdditionalInfoDiv($index, $lvl)
            . $this->generateChildren($index, $event_name, $lvl)
            . "</li>";
    }

    private function addEventSearchableTerms($event_name, $primary_term, $secondary_term_list)
    {
        $secondary_term_array = explode(",", $secondary_term_list);
        array_push($this->searchable_terms["$event_name"], $primary_term);
        foreach ($secondary_term_array as $term) {
            array_push($this->searchable_terms["$event_name"], $term);
        }
    }

    public function generateIndexMainDiv($index, $lvl)
    {
        $div_attr = $this->getMainDivDivAttr($index);
        $span_attr = $this->getMainDivSpanAttr($index, $lvl);
        $primary_term = $index['primary_term'];
        return $this->getIndexMainDiv($div_attr, $span_attr, $primary_term);
    }

    public function generateAdditionalInfoDiv($index, $lvl)
    {
        $result = "";
        $secondary_term_list = $index['secondary_term_list'];
        $description = $index['description'];
        $warning = $index['warning_note'];
        $info = $index['general_note'];
        if ($secondary_term_list || $description || $warning || $info) {
            $result .= $this->getAdditionalInfoLeftCol($secondary_term_list, $lvl); //opens row tag
            $result .= $this->getAdditionalInfoRightCol($description, $warning, $info); //closes row tag
        }
        return $result;
    }

    private function generateChildren($index, $event_name, $lvl)
    {
        $result = "";
        $cmd = $this->getDb()->createCommand('SELECT id, parent, primary_term, secondary_term_list, description, general_note, open_element_class_name, goto_id, goto_tag, goto_text, img_url, goto_subcontainer_class, goto_doodle_class_name, goto_property, warning_note FROM index_search WHERE parent =:parent_key');
        $cmd->bindValue(':parent_key', $index['id']);
        $children = $cmd->queryAll();
        if ($children) {
            $result .= "<ul class='results_list'>";
            foreach ($children as $child) {
                $result .= $this->generateIndexHTML($child, $event_name, $lvl + 1);
            }
            $result .= "</ul>";
        }
        return $result;
    }

    private function getMainDivDivAttr($index)
    {
        return "{$this->getMainDivDivImage($index)} {$this->getMainDivDivClass($index)} {$this->getMainDivDivData($index)}";
    }

    private function getMainDivSpanAttr($index, $lvl)
    {
        $primary_term = $index['primary_term'];
        $secondary_terms_string = $index['secondary_term_list'];
        $complete_term_list = $secondary_terms_string ? $primary_term . "," . $secondary_terms_string : $primary_term;
        return "data-alias=\"{$complete_term_list}\" class=\"lvl{$lvl}\"";
    }

    private function getIndexMainDiv($div_attr, $span_attr, $primary_term)
    {
        return
            "<div $div_attr>"
            . "<span $span_attr>"
            . "$primary_term"
            . "</span>"
            . "</div>";
    }

    private function getAdditionalInfoLeftCol($secondary_term_list, $lvl)
    {
        return
            "<div class=\"index_row row\">"
            . "<div class=\"index_col_left" . "_lvl{$lvl}\">"
            . "<span class=\"alias\">"
            . ($secondary_term_list !== null ? ($secondary_term_list) : (""))
            . "</span>"
            . "</div>";
    }

    private function getAdditionalInfoRightCol($description, $warning, $info)
    {
        return
            "<div class=\"index_col_right\">"
            . $this->generateIndexDescription($description, $warning, $info)
            . $this->generateIndexWarning($warning)
            . $this->generateIndexInfo($info)
            . "</div></div>";
    }

    private function getMainDivDivImage($index)
    {
        $result = "";
        if ($index['img_url'] !== null) {
            $path = $index['img_url'];
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
        if ($index['img_url'] !== null) {
            return "class=\"result_item result_item_with_icon\"";
        } else {
            return "class=\"result_item\"";
        }
    }

    public function getMainDivDivData($index)
    {
        $data_map_attrs = array(
            'goto-id' => 'goto_id',
            'goto-subcontainer' => 'goto_subcontainer_class',
            'goto-tag' => 'goto_tag',
            'goto-text' => 'goto_text',
            'element-class-name' => 'open_element_class_name',
            'doodle-class-name' => 'goto_doodle_class_name',
            'property' => 'goto_property'
        );
        $computed_data = "";
        $open_element_class_name = $index['open_element_class_name'];
        if ($open_element_class_name) {
            $computed_data =
                "data-element-id=\"{$this->getElementId($open_element_class_name)}\" "
                . "data-element-name=\"{$this->getElementName($open_element_class_name)}\" ";
        }
        return $computed_data . $this->getDataAttributes($data_map_attrs, $index);
    }

    private function generateIndexDescription($description, $warning, $info)
    {
        $result = "";
        if ($description) {
            $result = "<p class=\"description_note note\">{$description}</p>";
        }
        return ($warning || $info) ? "{$result}<br>" : $result;
    }

    private function generateIndexWarning($warning)
    {
        $result = "";
        if ($warning) {
            $result =
                "<span class=\"warning_icon\"></span>"
                . "<span class=\"warning_note\">{$warning}</span>";
        }
        return $result;
    }

    private function generateIndexInfo($info)
    {
        $result = "";
        if ($info) {
            $result =
                "<span class=\"info_icon\"></span>"
                . "<p class=\"info_note\">{$info}</p>";
        }
        return $result;
    }

    /**
     * @param $open_element_class_name
     * @return mixed
     * @throws SystemException
     */
    private function getElementId($open_element_class_name)
    {
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
    public function getElementName($open_element_class_name)
    {
        if ($element_type = ElementType::model()->findByAttributes(array('class_name' => $open_element_class_name))) {
            return $element_type->name;
        } else {
            throw new SystemException("unable to find element type for {$open_element_class_name}.");
        }
    }

    private function getDataAttributes($map, $index)
    {
        $attrs = array();
        foreach ($map as $data_key => $index_attr) {
            if ($index[$index_attr] !== null) {
                $attrs[$data_key] = $index[$index_attr];
            }
        }
        return implode(' ', array_map(function ($k, $v) {
            return "data-{$k}='{$v}'";
        }, array_keys($attrs), $attrs));
    }

// Begin Format library
    private $input = '';
    private $output = '';
    private $tabs = 0;
    private $in_tag = false;
    private $in_comment = false;
    private $in_content = false;
    private $inline_tag = false;
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
                    if (!$this->is_inline_tag()) {
                        $this->in_content = false;
                    }
                    $this->parse_tag();
                } elseif (!$this->in_content) {
                    if (!$this->inline_tag) {
                        $this->output .= "\n" . str_repeat("\t", $this->tabs);
                    }
                    $this->in_content = true;
                }
                $this->output .= $this->input[$this->input_index];
            }
        }

        return $this->output;
    }

    private function parse_comment()
    {
        if ($this->is_end_comment()) {
            $this->in_comment = false;
            $this->output .= '-->';
            $this->input_index += 3;
        } else {
            $this->output .= $this->input[$this->input_index];
        }
    }

    private function parse_inner_tag()
    {
        if ($this->input[$this->input_index] == '>') {
            $this->in_tag = false;
            $this->output .= '>';
        } else {
            $this->output .= $this->input[$this->input_index];
        }
    }

    private function parse_inner_inline_tag()
    {
        if ($this->input[$this->input_index] == '>') {
            $this->inline_tag = false;
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
            $this->in_comment = true;
        } elseif ($this->is_end_tag()) {
            $this->in_tag = true;
            $this->inline_tag = false;
            $this->decrement_tabs();
            if (!$this->is_inline_tag() and !$this->is_tag_empty()) {
                $this->output .= "\n" . str_repeat("\t", $this->tabs);
            }
        } else {
            $this->in_tag = true;
            if (!$this->in_content and !$this->inline_tag) {
                $this->output .= "\n" . str_repeat("\t", $this->tabs);
            }
            if (!$this->is_closed_tag()) {
                $this->tabs++;
            }
            if ($this->is_inline_tag()) {
                $this->inline_tag = true;
            }
        }
    }

    private function is_end_tag()
    {
        for ($input_index = $this->input_index; $input_index < strlen($this->input); $input_index++) {
            if ($this->input[$input_index] == '<' and $this->input[$input_index + 1] == '/') {
                return true;
            } elseif ($this->input[$input_index] == '<' and $this->input[$input_index + 1] == '!') {
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
            and $this->input[$this->input_index + 1] == '!'
            and $this->input[$this->input_index + 2] == '-'
            and $this->input[$this->input_index + 3] == '-') {
            return true;
        } else {
            return false;
        }
    }

    private function is_end_comment()
    {
        if ($this->input[$this->input_index] == '-'
            and $this->input[$this->input_index + 1] == '-'
            and $this->input[$this->input_index + 2] == '>') {
            return true;
        } else {
            return false;
        }
    }

    private function is_tag_empty()
    {
        $current_tag = $this->get_current_tag($this->input_index + 2);
        $in_tag = false;

        for ($input_index = $this->input_index - 1; $input_index >= 0; $input_index--) {
            if (!$in_tag) {
                if ($this->input[$input_index] == '>') {
                    $in_tag = true;
                } elseif (!preg_match('/\s/', $this->input[$input_index])) {
                    return false;
                }
            } else {
                if ($this->input[$input_index] == '<') {
                    if ($current_tag == $this->get_current_tag($input_index + 1)) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function get_current_tag($input_index)
    {
        $current_tag = '';

        for ($input_index; $input_index < strlen($this->input); $input_index++) {
            if ($this->input[$input_index] == '<') {
                continue;
            } elseif ($this->input[$input_index] == '>' or preg_match('/\s/', $this->input[$input_index])) {
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
            if ($this->input[$input_index] == '<' or $this->input[$input_index] == '/') {
                continue;
            } elseif (preg_match('/\s/', $this->input[$input_index]) or $this->input[$input_index] == '>') {
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
//End of Format library
}
