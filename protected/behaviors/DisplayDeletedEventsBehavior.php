<?php
class DisplayDeletedEventsBehavior extends CActiveRecordBehavior
{
    private const HIDDEN = 0;
    private const ALL_USER_GROUPED = 2;
    private const ADMIN_TIMELINE = 3;
    private const ADMIN_GROUPED = 4;
    private function canViewDeletedEvents()
    {
        $enabled = intval(SettingMetadata::model()->getSetting('show_deleted_events', null, false, ['SettingInstitution', 'SettingInstallation']));
        $is_admin = (Yii::app() instanceof CConsoleApplication) ? true : (Yii::app()->user->checkAccess('admin') ? true : (Yii::app()->user->checkAccess('Institution Admin') ? true : false));
        if($enabled === self::HIDDEN || (in_array($enabled, array(self::ADMIN_TIMELINE, self::ADMIN_GROUPED)) && (!$is_admin))){
            return 0;
        }
        return $enabled;
    }
    /**
     * according to the setting, to disable the default scope or not
     *
     * @return void
     */
    public function displayDeletedEvents()
    {
        if(!$this->canViewDeletedEvents()){
            return;
        }
        $this->owner->disableDefaultScope();
    }

    /**
     * if the setting is on, return display mode, otherwise, return 0
     *
     * 1 for "Deleted Events" category, 2 for "Timeline"
     *
     * @return int
     */
    public function displayDeletedEventsIn()
    {
        $enabled = $this->canViewDeletedEvents();
        if(!$enabled){
            return 0;
        }
        return in_array($enabled, array(self::ADMIN_GROUPED, self::ALL_USER_GROUPED)) ? 1 : 2;
    }
}
