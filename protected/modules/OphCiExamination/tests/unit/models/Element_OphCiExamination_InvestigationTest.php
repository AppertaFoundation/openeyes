<?php

namespace OEModule\OphCiExamination\tests\unit\models;

use ModelTestCase;
use OEModule\OphCiExamination\models\Element_OphCiExamination_Investigation;
use OEModule\OphCiExamination\models\OphCiExamination_Investigation_Entry;

class Element_OphCiExamination_InvestigationTest extends ModelTestCase
{
    public $fixtures = [
        'entries' => OphCiExamination_Investigation_Entry::class,
    ];
    protected $element_cls = Element_OphCiExamination_Investigation::class;


    /** @test */
    public function get_letter_string_no_entries()
    {
        $instance = $this->getElementInstance();
        $this->assertEquals('', $instance->getLetter_string());
    }

    /** @test */
    public function get_letter_string_one_entry_with_comment()
    {
        $instance = $this->getElementInstance();
        $entries = [];

        $entries[] = $this->entries('entry1');
        $instance->entries = $entries;
        $this->assertStringContainsString(
            '<tbody><tr><td>A/C tap intravitreal tap (Some comment)</td></tr>',
            $instance->getLetter_string());
    }


    /** @test */
    public function get_letter_string_one_entry_with_empty_string_comment()
    {
        $instance = $this->getElementInstance();
        $entries = [];

        $entries[] = $this->entries('entry2');
        $instance->entries = $entries;
        $this->assertStringContainsString(
            '<tbody><tr><td>A/C tap intravitreal tap</td></tr></tbody>',
            $instance->getLetter_string());
    }

    /** @test */
    public function get_letter_string_one_entry_with_null_comment()
    {
        $instance = $this->getElementInstance();
        $entries = [];

        $entries[] = $this->entries('entry3');
        $instance->entries = $entries;
        $this->assertStringContainsString(
            '<tbody><tr><td>A/C tap intravitreal tap</td></tr></tbody>',
            $instance->getLetter_string());
    }

    /** @test */
    public function get_letter_string_one_entry_with_multiple_comments()
    {
        $instance = $this->getElementInstance();
        $entries = [];

        $entries[] = $this->entries('entry3');
        $entries[] = $this->entries('entry2');
        $entries[] = $this->entries('entry1');
        $instance->entries = $entries;
        $this->assertStringContainsString(
            '<tbody><tr><td>A/C tap intravitreal tap</td></tr><tr><td>A/C tap intravitreal tap</td></tr><tr><td>A/C tap intravitreal tap (Some comment)</td></tr></tbody>',
            $instance->getLetter_string());
    }

    /** @test */
    public function get_letter_string_no_entry_with_element_comments()
    {
        $instance = $this->getElementInstance();
        $instance->description = 'test';

        $this->assertStringContainsString(
            '<tr><td>Comments:test</td></tr>',
            $instance->getLetter_string());
    }

    /** @test */
    public function get_letter_string_with_one_entry_with_element_comments()
    {
        $instance = $this->getElementInstance();
        $entries = [];

        $entries[] = $this->entries('entry3');
        $instance->entries = $entries;
        $instance->description = 'test';

        $this->assertStringContainsString(
            '<tbody><tr><td>A/C tap intravitreal tap</td></tr><tr><td>Comments:test</td></tr></tbody>',
            $instance->getLetter_string()
        );
    }
}
