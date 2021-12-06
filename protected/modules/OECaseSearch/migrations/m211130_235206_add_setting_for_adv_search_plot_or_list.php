<?php

class m211130_235206_add_setting_for_adv_search_plot_or_list extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'field_type_id' => 3,
            'key' => 'oecasesearch_default_view',
            'name' => 'Default advanced search display',
            'default_value' => 'plot',
            'data' => serialize(array('plot' => 'Plot of results', 'list' => 'Paginated list of results'))
        ));

        $this->insert('setting_installation', array(
            'key' => 'oecasesearch_default_view',
            'value' => 'plot',
        ));
    }

    public function down()
    {
        $this->delete('setting_metadata', '`key` = "oecasesearch_default_view"');
        $this->delete('setting_installation', '`key` = "oecasesearch_default_view"');
    }
}
