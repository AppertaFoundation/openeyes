<?php
class m190731_053530_event_type_OphOuCatprom5 extends OEMigration
{
    public function safeup()
    {
        if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->
        where('class_name=:class_name',
        array(':class_name'=>'OphOuCatprom5'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name'=>'Outcomes'))->queryRow();
            $this->insert('event_type', array('class_name' => 'OphOuCatprom5', 'name' => 'CatProm5','event_group_id' => $group['id']));
        }
        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphOuCatprom5'))->queryRow();

//        $this->addCatprom5Element($event_type['id'],'Questionare', array('class_name'=> 'CatProm5AnswerResult','display_order'=>1,'default'=>true,'required' =>true));
                $this->addCatprom5Element($event_type['id'], 'Questionare', array('class_name'=> 'CatProm5EventResult','display_order'=>1,'default'=>true,'required' =>true));

    }

    function addCatprom5Element($event_type, $element_name, $params)
    {
        $row = array(
            'name' => $element_name,
            'class_name' => isset($params['class_name']) ? $params['class_name'] : "Element_{$event_type}_" . str_replace(' ', '', $element_name),
            'event_type_id' => isset($event_type) ? $event_type : 0,
            'display_order' => isset($params['display_order']) ? $params['display_order'] : 1,
            'default' => isset($params['default']) ? $params['default'] : false,
            'required' => isset($params['required']) ? $params['required'] : false,
        );

        $this->insert('element_type', $row);
    }


    public function safedown()
    {

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphOuCatprom5'))->queryRow();

        foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where('event_type_id=:event_type_id', array(':event_type_id'=>$event_type['id']))->queryAll() as $row) {
            $this->delete('audit', 'event_id='.$row['id']);
            $this->delete('event', 'id='.$row['id']);
        }

        $this->delete('element_type', 'event_type_id='.$event_type['id']);
        $this->delete('event_type', 'id='.$event_type['id']);
    }
}
