<?php

class CloseHotlistItemsCommand extends CConsoleCommand
{
    public function getHelp()
    {
        return 'Closes all currently open user activity hotlist items.';
    }

    public function run($args)
    {
        UserHotlistItem::model()->updateAll(array('is_open' => 0), 'is_open = 1');
    }
}
