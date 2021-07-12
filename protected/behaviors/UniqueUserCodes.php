<?php

class UniqueUserCodes extends CBehavior
{
    public function getUniqueCodeForUser()
    {
        $userUniqueCode = UniqueCodeMapping::model()->findByAttributes(array('user_id' => Yii::app()->user->id));
        if($userUniqueCode)
        {
            return $userUniqueCode->unique_code_id;
        }else
        {
            $uniqueCode = $this->createNewUniqueCodeMapping(null, Yii::app()->user->id);
            return $uniqueCode->unique_code_id;
        }
    }
    
    /**
     * 
     * @param type $eventId
     * @param type $userId
     * @return \UniqueCodeMapping
     * @throws Exception
     */

    public function createNewUniqueCodeMapping($eventId=null, $userId=null)
    {
        /* LOCK */
        UniqueCodeMapping::lock();

        $newUniqueCode = new UniqueCodeMapping();
        $ucid = $this->getActiveUnusedUniqueCode();
        if(is_null($ucid)) {
            throw new Exception("Couldn't get new unique code. Please make sure new ones are generated.");
        }
        
        $newUniqueCode->unique_code_id = $ucid;
        
        if($eventId > 0)
        {
            $newUniqueCode->event_id = $eventId;
            $newUniqueCode->user_id = NULL;
        }
        else if($userId > 0)
        {
            $newUniqueCode->event_id = NULL;
            $newUniqueCode->user_id = $userId;
        }
        
        $newUniqueCode->save();
        
        /* UNLOCK */
        UniqueCodeMapping::unlock();
        
        return $newUniqueCode;
    }


    /**
     * Retrieves an unused active unique code id
     *
     * @return int
     */
    private function getActiveUnusedUniqueCode()
    {
        $record = Yii::app()->db->createCommand()
            ->select('unique_codes.id as id')
            ->from('unique_codes')
            ->leftJoin('unique_codes_mapping', 'unique_code_id=unique_codes.id')
            ->where('unique_codes_mapping.id is null')
            ->andWhere('active = 1')
            ->limit(1)
            ->queryRow();

        if($record){
            return $record["id"];
        }
        
        return null;
    }
}