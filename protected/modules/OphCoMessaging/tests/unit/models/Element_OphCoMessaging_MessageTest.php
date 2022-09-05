<?php

namespace OEModule\OphCoMessaging\tests\unit\models;

use ComponentStubGenerator;
use Event;
use EventSubtype;
use InteractsWithEventTypeElements;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;
use User;
use WithFaker;
use WithTransactions;

/**
 * @group sample-data
 * @group messaging
 */
class Element_OphCoMessaging_MessageTest extends \ModelTestCase
{
    use InteractsWithEventTypeElements;
    use WithFaker;
    use WithTransactions;

    protected $element_cls = Element_OphCoMessaging_Message::class;
    protected $instance;

    /** @test */
    public function event_subtype_is_set_from_message_type()
    {
        $this->instance = $this->getElementInstance();
        $message_type = $this->getMessageType(true);
        $this->instance->attributes = $this->generateElementAttributes([
            'message_type_id' => $message_type->id
        ]);

        $this->saveElement($this->instance);

        $this->assertEquals($message_type->event_subtype, $this->instance->event->firstEventSubtypeItem->event_subtype);
    }

    /** @test */
    public function event_subtype_is_removed_from_message_type()
    {
        $this->instance = $this->getElementInstance();
        $message_type = $this->getMessageType(true);
        $this->instance->attributes = $this->generateElementAttributes([
            'message_type_id' => $message_type->id
        ]);

        $this->saveElement($this->instance);

        // create new message type without a subtype
        $message_type2 = $this->getMessageType(false);
        $this->instance->message_type_id = $message_type2->id;

        $this->saveElement($this->instance);

        // reload event
        $event = Event::model()->findByPk($this->instance->event_id);
        $this->assertNull($event->firstEventSubtypeItem->event_subtype);
    }

    /** @test */
    public function event_subtype_is_updated_by_changed_message_type()
    {
        $this->instance = $this->getElementInstance();
        $message_type = $this->getMessageType(true);
        $this->instance->attributes = $this->generateElementAttributes([
            'message_type_id' => $message_type->id
        ]);

        $this->saveElement($this->instance);

        // create new message type with a subtype
        $message_type2 = $this->getMessageType(true);
        $this->instance->message_type_id = $message_type2->id;

        $this->saveElement($this->instance);

        // reload event
        $event = Event::model()->findByPk($this->instance->event_id);
        $this->assertEquals($message_type2->event_subtype, $this->instance->event->firstEventSubtypeItem->event_subtype);
    }

    public function getMessageType(bool $has_event_subtype = false)
    {
        $message_type = new OphCoMessaging_Message_MessageType();
        $message_type->name = $this->faker->unique()->word();
        $message_type->display_order = 1;

        if ($has_event_subtype) {
            $event_subtype = new EventSubtype();
            $event_subtype->event_subtype = $this->faker->unique()->word();
            $event_subtype->dicom_modality_code = "Foo";
            $event_subtype->icon_name = $this->faker->word();
            $event_subtype->display_name = $this->faker->word();
            $event_subtype->save();

            $message_type->event_subtype = $event_subtype->event_subtype;
        }
        $message_type->save();

        return $message_type;
    }

    public function generateElementAttributes(array $attributes = [])
    {
        return array_merge([
            'for_the_attention_of_user_id' => 1, // assumes presence of admin user
            'message_text' => $this->faker->paragraph()
        ], $attributes);
    }
}
