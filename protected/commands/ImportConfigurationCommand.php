<?php

/**
 * Class ImportConfigurationCommand
 */
class ImportConfigurationCommand extends CConsoleCommand
{
    private $spreadsheet;
    private $institution_id;
    /**
     * @return string
     */
    public function getName()
    {
        return 'Import Configuration Command.';
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return <<<EOH
        
Import Configuration
        
This command is able to import OpenEyes configuration from an XLSX files

The file should be named the name of the institution as it appears in the database

The file should have the following tabs (remove any tabs if you do not want them to be imported):
|Context|Workflows|Workflow Rules|Allergy Reaction|History Macros|Element Attributes|Element Mapping|Drug Sets|

The context tab should have the following headers:
|PAS Code|Context Name|Subspecialty|Consultant|Cost Code|Service Enabled|Context Enabled|Active|
|Code    |String      |String      |String    |Code     |Yes/No         |Yes/No         |Yes/No|

The workflow tab should have the following headers:
|Workflow Name|Step  |Order |Element|Mandatory|
|String       |String|Number|String |Yes/No   |

The workflow rules tab should have the following headers:
|Subspecialty|Context|Episode    |Workflow|
|String      |String |String/All |String  |

The allergy reaction tab should have the following headers:
|Name  |
|String|

The history macros tab should have the following headers:
|Subspecialty|Name  |Body  |
|String      |String|String|

The element attributes tab should have the following headers:
|Attribute Name|Attribute Label|Attribute Element|Multiselect|
|String        |String         |String           |Yes/No     |

The element mapping tab should have the following headers:
|Attribute Label|Order |Value |Subspecialty|Exclusion    |
|String         |Number|String|String      |CSV String   |

The drug sets tab should have the following headers:
|Name  |Hidden|Site       |Usage Code|Subspecialty|Medicine Name|Default Dose|Default Route|Default Frequency|Default Duration|
|String|Yes/No|String/None|String    |String/None |String       |Number      |String       |String           |String          |

If you want a value to be Null please include 'Blank' in the cell
If you want all values then please include 'All' in the cell

Make sure all the values you put in the excel document exist in the database,
check the corresponding model to check for dependencies

USAGE
  php yiic importconfiguration --filename=[filename.xlsx]
         
EOH;
    }

    /**
     * @param $filename
     */
    public function actionIndex($filename)
    {
        $t = microtime(true);
        echo "\n[" . (date("Y-m-d H:i:s")) . "] Import Configuration started ... \n";

        $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);

        $institution = basename($filename, ".xlsx");
        if (Institution::model()->findByAttributes(array('name' => $institution)) == null) {
            echo "\033[0;31mError: \033[0m".$institution." is not a valid Institution\n";
            exit(8);
        }
        $this->institution_id = Institution::model()->findByAttributes(array('name' => $institution))->id;

        $this->contextImport();

        $this->workflowImport();

        $this->workflowRulesImport();

        $this->allergyReactionImport();

        $this->historyMacrosImport();

        $this->elementAttributesImport();

        $this->elementMappingImport();

        $this->drugSetImport();

        echo "\n[" . (date("Y-m-d H:i:s")) . "] Import Configuration finished ... OK - took: " . (microtime(true) - $t) . "s\n";
    }

    public function contextImport()
    {
        if ($this->spreadsheet->getSheetByName('Context') == null) {
            echo "\n\t Skipping Context Import ... \n";
            return;
        }
        $t = microtime(true);
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Context Import started ... \n";

        foreach ($this->spreadsheet->getSheetByName('Context')->toArray() as $index => $row) {
            // Skipping header
            if ($index == 0) {
                continue;
            }

            $pas_code = $row[0];
            $context_name = $row[1];
            $subspecialty = $row[2];
            $consultant = $row[3];
            $cost_code = $row[4];
            $service_enabled = $row[5];
            $context_enabled = $row[6];
            $active = $row[7];

            if (Subspecialty::model()->findByAttributes(array('name' => $subspecialty)) == null) {
                echo "\033[0;31mError: \033[0m".$subspecialty." is not a valid Subspecialty\n";
                exit(8);
            }
            $subspecialty_id = Subspecialty::model()->findByAttributes(array('name' => $subspecialty))->id;

            if (ServiceSubspecialtyAssignment::model()->findByAttributes(array('subspecialty_id' => $subspecialty_id)) == null) {
                echo "\033[0;31mError: \033[0mThere is no service assigned to ".$subspecialty."\n";
                exit(8);
            }
            $service_subspecialty_assignment_id = ServiceSubspecialtyAssignment::model()->findByAttributes(array('subspecialty_id' => $subspecialty_id))->id;

            if ($consultant != "Blank") {
                if (User::model()->findByAttributes(array('first_name' => $consultant)) == null) {
                    echo "\033[0;31mError: \033[0m".$user." is not a valid consultant\n";
                    exit(8);
                }
                $consultant_id = User::model()->findByAttributes(array('first_name' => $consultant))->id;
            }

            $firm = null;

            (\Firm::model()->findByAttributes(array('name'=>$context_name)) == null) ? $firm = new \Firm : $firm = \Firm::model()->findByAttributes(array('name'=>$context_name));

            $firm->pas_code = ($pas_code == "Blank") ? null : $pas_code;
            $firm->name = ($context_name == "Blank") ? null : $context_name;
            $firm->institution_id = $this->institution_id;
            $firm->service_subspecialty_assignment_id = $service_subspecialty_assignment_id;
            $firm->consultant_id = ($consultant == "Blank") ? null : $consultant_id;
            $firm->cost_code = ($cost_code == "Blank") ? null : $cost_code;
            $firm->can_own_an_episode = ($service_enabled == "No") ? 0 : 1;
            $firm->runtime_selectable = ($context_enabled == "No") ? 0 : 1;
            $firm->active = ($active == "No") ? 0 : 1;
            $firm->save(false);
        }
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Context Import finished ... OK - took: " . (microtime(true) - $t) . "s\n";
    }

    public function workflowImport()
    {
        if ($this->spreadsheet->getSheetByName('Workflows') == null) {
            echo "\n\t Skipping Workflow Import ... \n";
            return;
        }

        $t = microtime(true);
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Workflow Import started ... \n";

        // Removing all workflows in current institution
        $old_workflows = Yii::app()->db->createCommand()->select('id')->from('ophciexamination_workflow')->where('institution_id=:institution_id', array(':institution_id'=>$this->institution_id))->queryColumn();
        $workflow_criteria = new CDbCriteria();
        $workflow_criteria->addInCondition('workflow_id', $old_workflows);
        $step_ids = array();
        foreach (OEModule\OphCiExamination\models\OphCiExamination_ElementSet::model()->findAll($workflow_criteria) as $step) {
            $step_ids[] = $step->id;
        }
        if (!empty($step_ids)) {
            $setitem_criteria = new CDbCriteria();
            $setitem_criteria->addInCondition('set_id', $step_ids);

            OEModule\OphCiExamination\models\OphCiExamination_ElementSetItem::model()->deleteAll($setitem_criteria);
            $event_stepitem_criteria = new CDbCriteria();
            $event_stepitem_criteria->addInCondition('step_id', $step_ids);
            OEModule\OphCiExamination\models\OphCiExamination_Event_ElementSet_Assignment::model()->deleteAll($event_stepitem_criteria);
        }
        OEModule\OphCiExamination\models\OphCiExamination_ElementSet::model()->deleteAll($workflow_criteria);
        OEModule\OphCiExamination\models\OphCiExamination_Workflow_Rule::model()->deleteAll($workflow_criteria);
        $workflow = new CDbCriteria();
        $workflow->addInCondition('id', $old_workflows);
        OEModule\OphCiExamination\models\OphCiExamination_Workflow::model()->deleteAll($workflow);

        foreach ($this->spreadsheet->getSheetByName('Workflows')->toArray() as $index => $row) {
            // Skipping header
            if ($index == 0) {
                continue;
            }

            $workflow_name = $row[0];
            $step = $row[1];
            $order = $row[2];
            $element = $row[3];
            $mandatory = $row[4];

            // Adding workflow
            $workflow_id = null;
            if (OEModule\OphCiExamination\models\OphCiExamination_Workflow::model()->findByAttributes(array('name' => $workflow_name, 'institution_id' => $this->institution_id)) == null) {
                $workflow = new OEModule\OphCiExamination\models\OphCiExamination_Workflow;
                $workflow->name = ($workflow_name == "Blank") ? null : $workflow_name;
                $workflow->institution_id = $this->institution_id;
                $workflow->save(false);
                $workflow_id = $workflow->id;
            } else {
                $workflow_id = OEModule\OphCiExamination\models\OphCiExamination_Workflow::model()->findByAttributes(array('name' => $workflow_name, 'institution_id' => $this->institution_id))->id;
            }

            // Adding element set
            $set_id = null;
            if (OEModule\OphCiExamination\models\OphCiExamination_ElementSet::model()->findByAttributes(array('name'=>$step, 'workflow_id'=>$workflow_id)) == null) {
                $element_set = new OEModule\OphCiExamination\models\OphCiExamination_ElementSet;
                $element_set->name = ($step == "Blank") ? null : $step;
                $element_set->workflow_id = $workflow_id;
                $element_set->save(false);
                $set_id = $element_set->id;
            } else {
                $set_id = OEModule\OphCiExamination\models\OphCiExamination_ElementSet::model()->findByAttributes(array('name'=>$step, 'workflow_id'=>$workflow_id))->id;
            }

            // These worklfows should only be added to examination
            $event_type_id = \EventType::model()->findByAttributes(array('name' => 'Examination'))->id;

            if (\ElementType::model()->findByAttributes(array('name' => $element, 'event_type_id' => $event_type_id)) == null) {
                echo "\033[0;31mError: \033[0m".$element." is not a valid element type\n";
                exit(8);
            }
            $element_id = \ElementType::model()->findByAttributes(array('name' => $element, 'event_type_id' => $event_type_id))->id;

            // Adding element set item
            $element_set_item = new OEModule\OphCiExamination\models\OphCiExamination_ElementSetItem;
            $element_set_item->set_id = ($set_id == "Blank") ? null : $set_id;
            $element_set_item->element_type_id = $element_id;
            $element_set_item->display_order = ($order == "Blank") ? null : $order;
            $element_set_item->is_mandatory = ($mandatory == "Yes") ? 1 : 0;
            $element_set_item->save(false);
        }
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Workflow Import finished ... OK - took: " . (microtime(true) - $t) . "s\n";
    }

    public function workflowRulesImport()
    {
        if ($this->spreadsheet->getSheetByName('Workflow Rules') == null) {
            echo "\n\t Skipping Workflow Rules Import ... \n";
            return;
        }

        $t = microtime(true);
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Workflow Rules Import started ... \n";

        foreach ($this->spreadsheet->getSheetByName('Workflow Rules')->toArray() as $index => $row) {
            // Skipping header
            if ($index == 0) {
                continue;
            }

            $subspecialty = $row[0];
            $context = $row[1];
            $episode = $row[2];
            $workflow = $row[3];

            if (\Subspecialty::model()->findByAttributes(array('name' => $subspecialty)) == null) {
                echo "\033[0;31mError: \033[0m".$subspecialty." is not a valid subspecialty\n";
                exit(8);
            }
            $subspecialty_id = ($subspecialty == "All") ? null : \Subspecialty::model()->findByAttributes(array('name' => $subspecialty))->id;

            if (OEModule\OphCiExamination\models\OphCiExamination_Workflow::model()->findByAttributes(array('name' => $workflow)) == null) {
                echo "\033[0;31mError: \033[0m".$workflow." is not a valid workflow\n";
                exit(8);
            }
            $workflow_id = OEModule\OphCiExamination\models\OphCiExamination_Workflow::model()->findByAttributes(array('name' => $workflow))->id;

            if ($episode != "All" && \EpisodeStatus::model()->findByAttributes(array('name' => $episode)) == null) {
                echo "\033[0;31mError: \033[0m".$episode." is not a valid episode status\n";
                exit(8);
            }
            $episode_status_id = ($episode == "All") ? null : \EpisodeStatus::model()->findByAttributes(array('name' => $episode))->id;

            if ($context != "All" &&\Firm::model()->findByAttributes(array('name' => $context)) == null) {
                echo "\033[0;31mError: \033[0m".$context." is not a valid context\n";
                exit(8);
            }
            $firm_id = ($context == "All") ? null : \Firm::model()->findByAttributes(array('name' => $context))->id;

            $workflow_rule = new OEModule\OphCiExamination\models\OphCiExamination_Workflow_Rule;
            $workflow_rule->workflow_id = $workflow_id;
            $workflow_rule->subspecialty_id = $subspecialty_id;
            $workflow_rule->firm_id = $firm_id;
            $workflow_rule->episode_status_id = $episode_status_id;
            $workflow_rule->save(false);
        }
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Workflow Rules Import finished ... OK - took: " . (microtime(true) - $t) . "s\n";
    }

    public function allergyReactionImport()
    {
        if ($this->spreadsheet->getSheetByName('Allergy Reaction') == null) {
            echo "\n\t Skipping Allergy Reaction Import ... \n";
            return;
        }

        $t = microtime(true);
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Allergy Reaction Import started ... \n";

        foreach ($this->spreadsheet->getSheetByName('Allergy Reaction')->toArray() as $index => $row) {
            // Skipping header
            if ($index == 0) {
                continue;
            }

            $name = $row[0];

            if (OphCiExaminationAllergyReaction::model()->findByAttributes(array('name'=>$name)) == null) {
                $allergy_reaction = new OphCiExaminationAllergyReaction;
                $allergy_reaction->name = $name;
                $allergy_reaction->save(false);
            }
        }
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Allergy Reaction Import finished ... OK - took: " . (microtime(true) - $t) . "s\n";
    }

    public function historyMacrosImport()
    {
        if ($this->spreadsheet->getSheetByName('History Macros') == null) {
            echo "\n\t Skipping History Macros Import ... \n";
            return;
        }

        $t = microtime(true);
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] History Macros Import started ... \n";

        foreach ($this->spreadsheet->getSheetByName('History Macros')->toArray() as $index => $row) {
            // Skipping header
            if ($index == 0) {
                continue;
            }

            $subspecialty = $row[0];
            $name = $row[1];
            $body = $row[2];

            if (\Subspecialty::model()->findByAttributes(array('name' => $subspecialty)) == null) {
                echo "\033[0;31mError: \033[0m".$subspecialty." is not a valid subspecialty\n";
                exit(8);
            }
            $subspecialty_id = \Subspecialty::model()->findByAttributes(array('name' => $subspecialty))->id;

            $history_macro = null;

            (OEModule\OphCiExamination\models\HistoryMacro::model()->findByAttributes(array('name'=>$name)) == null) ? $history_macro = new OEModule\OphCiExamination\models\HistoryMacro : $history_macro = OEModule\OphCiExamination\models\HistoryMacro::model()->findByAttributes(array('name'=>$name));

            $history_macro->name = $name;
            $history_macro->body = $body;
            $history_macro->active = 1;
            $history_macro->save(false);

            $history_macro_id = $history_macro->id;

            if (OEModule\OphCiExamination\models\HistoryMacro_Subspecialty::model()->findByAttributes(array('history_macro_id' => $history_macro_id, 'subspecialty_id' => $subspecialty_id)) == null) {
                $history_macro_subspecialty = new OEModule\OphCiExamination\models\HistoryMacro_Subspecialty;
                $history_macro_subspecialty->history_macro_id = $history_macro_id;
                $history_macro_subspecialty->subspecialty_id = $subspecialty_id;
                $history_macro_subspecialty->save(false);
            }
        }
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] History Macros Import finished ... OK - took: " . (microtime(true) - $t) . "s\n";
    }

    public function elementAttributesImport()
    {
        if ($this->spreadsheet->getSheetByName('Element Attributes') == null) {
            echo "\n\t Skipping Element Attributes Import ... \n";
            return;
        }

        $t = microtime(true);
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Element Attributes Import started ... \n";

        foreach ($this->spreadsheet->getSheetByName('Element Attributes')->toArray() as $index => $row) {
            // Skipping header
            if ($index == 0) {
                continue;
            }

            $name = $row[0];
            $label = $row[1];
            $element = $row[2];
            $multiselect = $row[3];

            if (\ElementType::model()->findByAttributes(array('name' => $element)) == null) {
                echo "\033[0;31mError: \033[0m".$element." is not a valid element type\n";
                exit(8);
            }
            $element_id = \ElementType::model()->findByAttributes(array('name' => $element))->id;

            $attribute_id = null;

            if (OEModule\OphCiExamination\models\OphCiExamination_Attribute::model()->findByAttributes(array('name'=>$name, 'label'=>$label, 'institution_id'=>$this->institution_id)) == null) {
                $attribute = new OEModule\OphCiExamination\models\OphCiExamination_Attribute;
                $attribute->name = $name;
                $attribute->label = $label;
                $attribute->is_multiselect = ($multiselect == "Yes") ? 1 : 0;
                $attribute->institution_id = $this->institution_id;
                $attribute->save(false);
                $attribute_id = $attribute->id;
            } else {
                $attribute_id = OEModule\OphCiExamination\models\OphCiExamination_Attribute::model()->findByAttributes(array('name'=>$name, 'label'=>$label, 'institution_id'=>$this->institution_id))->id;
            }

            if (OEModule\OphCiExamination\models\OphCiExamination_AttributeElement::model()->findByAttributes(array('attribute_id'=>$attribute_id, 'element_type_id'=>$element_id)) == null) {
                $attribute_element = new OEModule\OphCiExamination\models\OphCiExamination_AttributeElement;
                $attribute_element->attribute_id = $attribute_id;
                $attribute_element->element_type_id = $element_id;
                $attribute_element->save(false);
            }
        }
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Element Attributes Import finished ... OK - took: " . (microtime(true) - $t) . "s\n";
    }

    public function elementMappingImport()
    {
        if ($this->spreadsheet->getSheetByName('Element Mapping') == null) {
            echo "\n\t Skipping Element Mapping Import ... \n";
            return;
        }

        $t = microtime(true);
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Element Mapping Import started ... \n";

        foreach ($this->spreadsheet->getSheetByName('Element Mapping')->toArray() as $index => $row) {
            // Skipping header
            if ($index == 0) {
                continue;
            }

            $label = $row[0];
            $order = $row[1];
            $value = $row[2];
            $subspecialty = $row[3];
            $exclusions = explode(",", $row[4]);

            if (OEModule\OphCiExamination\models\OphCiExamination_Attribute::model()->findByAttributes(array('label' => $label, 'institution_id' => $this->institution_id)) == null) {
                echo "\033[0;31mError: \033[0m".$label." is not a valid attribute label\n";
                exit(8);
            }
            $attribute_element_id = OEModule\OphCiExamination\models\OphCiExamination_Attribute::model()->findByAttributes(array('label' => $label, 'institution_id' => $this->institution_id))->id;

            if ($subspecialty != "All" && \Subspecialty::model()->findByAttributes(array('name' => $subspecialty)) == null) {
                echo "\033[0;31mError: \033[0m".$subspecialty." is not a valid subspecialty\n";
                exit(8);
            }
            $subspecialty_id = ($subspecialty == "All") ? null : \Subspecialty::model()->findByAttributes(array('name' => $subspecialty))->id;

            $element_mapping = null;

            (OEModule\OphCiExamination\models\OphCiExamination_AttributeOption::model()->findByAttributes(array('subspecialty_id'=>$subspecialty_id, 'attribute_element_id'=>$attribute_element_id, 'display_order'=>$order)) == null) ? $element_mapping = new OEModule\OphCiExamination\models\OphCiExamination_AttributeOption : $element_mapping = OEModule\OphCiExamination\models\OphCiExamination_AttributeOption::model()->findByAttributes(array('subspecialty_id'=>$subspecialty_id, 'attribute_element_id'=>$attribute_element_id, 'display_order'=>$order));

            $element_mapping->value = $value;
            $element_mapping->subspecialty_id = $subspecialty_id;
            $element_mapping->attribute_element_id = $attribute_element_id;
            $element_mapping->display_order = $order;
            $element_mapping->save(false);

            $option_id = $element_mapping->id;

            foreach ($exclusions as $indexx => $exclusion) {
                if ($exclusion == "None") {
                    break;
                }
                if (\Subspecialty::model()->findByAttributes(array('name' => $exclusion)) == null) {
                    echo "\033[0;31mError: \033[0m".$exclusion." is not a valid subspecialty\n";
                    exit(8);
                }
                $exclusion_id = \Subspecialty::model()->findByAttributes(array('name' => $exclusion))->id;

                if (Yii::app()->db->createCommand()->select()->from('ophciexamination_attribute_option_exclude')->where('option_id=:option_id', array(':option_id'=>$option_id))->andWhere('subspecialty_id=:subspecialty_id', array(':subspecialty_id'=>$exclusion_id))->queryRow() == null) {
                    Yii::app()->db->createCommand()->insert('ophciexamination_attribute_option_exclude', array('option_id'=>$option_id, 'subspecialty_id'=>$exclusion_id));
                }
            }
        }
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Element Mapping Import finished ... OK - took: " . (microtime(true) - $t) . "s\n";
    }

    public function drugSetImport()
    {
        if ($this->spreadsheet->getSheetByName('Drug Sets') == null) {
            echo "\n\t Skipping Drug Set Import ... \n";
            return;
        }
        $t = microtime(true);
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Drug Set Import started ... \n";

        foreach ($this->spreadsheet->getSheetByName('Drug Sets')->toArray() as $index => $row) {
            // Skipping header
            if ($index == 0) {
                continue;
            }

            $name = $row[0];
            $hidden = $row[1];
            $site = $row[2];
            $usage_code = $row[3];
            $subspecialty = $row[4];
            $medicine_name = $row[5];
            $default_dose = $row[6];
            $default_route = $row[7];
            $default_frequency = $row[8];
            $default_duration = $row[9];

            $drug_set = null;

            (\MedicationSet::model()->findByAttributes(array('name'=>$name)) == null) ? $drug_set = new \MedicationSet : $drug_set = \MedicationSet::model()->findByAttributes(array('name'=>$name));

            $drug_set->name = $name;
            $drug_set->hidden = ($hidden == "Yes") ? 1 : 0;
            $drug_set->automatic = 1;
            $drug_set->save(false);

            $drug_set_id = $drug_set->id;

            if ($site != "None" && Site::model()->findByAttributes(array('name' => $site)) == null) {
                echo "\033[0;31mError: \033[0m".$site." is not a valid Site\n";
                exit(8);
            }
            $site_id = ($site == "None") ? null : \Site::model()->findByAttributes(array('name' => $site))->id;

            if ($subspecialty != "None" && \Subspecialty::model()->findByAttributes(array('name' => $subspecialty)) == null) {
                echo "\033[0;31mError: \033[0m".$subspecialty." is not a valid Subspecialty\n";
                exit(8);
            }
            $subspecialty_id = ($subspecialty == "None") ? null : \Subspecialty::model()->findByAttributes(array('name' => $subspecialty))->id;

            if ($usage_code != "None" && \MedicationUsageCode::model()->findByAttributes(array('name' => $usage_code)) == null) {
                echo "\033[0;31mError: \033[0m".$usage_code." is not a valid Usage Code\n";
                exit(8);
            }
            $usage_code_id = ($usage_code == "None") ? null : \MedicationUsageCode::model()->findByAttributes(array('name' => $usage_code))->id;

            $drug_set_rule = null;

            (\MedicationSetRule::model()->findByAttributes(array('medication_set_id'=>$drug_set_id, 'subspecialty_id'=>$subspecialty_id, 'site_id'=>$site_id, 'usage_code_id'=>$usage_code_id)) == null) ? $drug_set_rule = new \MedicationSetRule : $drug_set_rule = \MedicationSetRule::model()->findByAttributes(array('medication_set_id'=>$drug_set_id, 'subspecialty_id'=>$subspecialty_id, 'site_id'=>$site_id, 'usage_code_id'=>$usage_code_id));

            $drug_set_rule->medication_set_id = $drug_set_id;
            $drug_set_rule->subspecialty_id = $subspecialty_id;
            $drug_set_rule->site_id = $site_id;
            $drug_set_rule->usage_code_id = $usage_code_id;
            $drug_set_rule->save(false);

            if (\Medication::model()->findByAttributes(array('short_term' => $medicine_name)) == null) {
                echo "\033[0;31mError: \033[0m".$medicine_name." is not a valid Medicine\n";
                exit(8);
            }
            $medication_id = \Medication::model()->findByAttributes(array('short_term' => $medicine_name))->id;
            $default_dose_unit_term = \Medication::model()->findByAttributes(array('short_term' => $medicine_name))->default_dose_unit_term;

            if (\MedicationRoute::model()->findByAttributes(array('term' => $default_route)) == null) {
                echo "\033[0;31mError: \033[0m".$default_route." is not a valid Route\n";
                exit(8);
            }
            $default_route_id = \MedicationRoute::model()->findByAttributes(array('term' => $default_route))->id;

            if (\MedicationDuration::model()->findByAttributes(array('name' => $default_duration)) == null) {
                echo "\033[0;31mError: \033[0m".$default_duration." is not a valid Duration\n";
                exit(8);
            }
            $default_duration_id = \MedicationDuration::model()->findByAttributes(array('name' => $default_duration))->id;

            if (\MedicationFrequency::model()->findByAttributes(array('term' => $default_frequency)) == null) {
                echo "\033[0;31mError: \033[0m".$default_frequency." is not a valid Frequency\n";
                exit(8);
            }
            $default_frequency_id = \MedicationFrequency::model()->findByAttributes(array('term' => $default_frequency))->id;

            if (\MedicationSetAutoRuleMedication::model()->findByAttributes(array('medication_set_id'=>$drug_set_id, 'medication_id'=>$medication_id, 'default_dose'=>$default_dose, 'default_route_id'=>$default_route_id, 'default_frequency_id'=>$default_frequency_id, 'default_dose_unit_term'=>$default_dose_unit_term, 'default_duration_id'=>$default_duration_id)) == null) {
                $medication_set_auto_rule_medication = new \MedicationSetAutoRuleMedication;
                $medication_set_auto_rule_medication->medication_set_id = $drug_set_id;
                $medication_set_auto_rule_medication->medication_id = $medication_id;
                $medication_set_auto_rule_medication->default_dose = $default_dose;
                $medication_set_auto_rule_medication->default_route_id = $default_route_id;
                $medication_set_auto_rule_medication->default_dose_unit_term = $default_dose_unit_term;
                $medication_set_auto_rule_medication->default_duration_id = $default_duration_id;
                $medication_set_auto_rule_medication->default_frequency_id = $default_frequency_id;
                $medication_set_auto_rule_medication->save(false);
            }
        }
        // Rebuilding all sets
        \MedicationSet::populateAutoSets();
        echo "\n\t[" . (date("Y-m-d H:i:s")) . "] Drug Set Import finished ... OK - took: " . (microtime(true) - $t) . "s\n";
    }
}
