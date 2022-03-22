<?php

class PincodeHelper
{
    public static function generatePincode()
    {
        $temp_pincode = sprintf("%06d", mt_rand(0, 999999));
        
        while(!self::validatePincode($temp_pincode))
        {
            $temp_pincode = sprintf("%06d", mt_rand(0, 999999));
        }
        return $temp_pincode;
    }

    private static function validatePincode($temp_pincode)
    {
        return !self::isPreviouslyUsed($temp_pincode) && !self::isSequentialNum($temp_pincode) && !self::isRepeatNum($temp_pincode) && !self::isSecretaryPin($temp_pincode);
    }

    private static function isPreviouslyUsed($pin)
    {
        $pincodes = Yii::app()->db->createCommand()
            ->select('*')
            ->from('v_unavailable_pincodes')
            ->where('pincode = :pincode', array(':pincode' => $pin))
            ->queryAll();
        return count($pincodes) > 0;
    }

    private static function isSequentialNum($pin) {
		$count = 1;

		for ($i = 0; $i < strlen($pin); $i++) {
			if ((substr($pin, $i, 1) + 1) == substr($pin, $i + 1, 1)) {
				$count++;
			}
		}

        return $count === strlen($pin);
	}

	private static function isRepeatNum($pin) {
		return preg_match('/(\d)\1{5}/', $pin);
	}

    private static function isSecretaryPin($pin){
        if(Yii::app()->params["secretary_pin"]){
            return $pin === Yii::app()->params["secretary_pin"];
        }
        return false;
    }
}