<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class Element_OphCoCorrespondence_Esign
 *
 * @property int $id
 * @property int $event_id
 *
 * @property Event $event
 * @property OphCoCorrespondence_Signature[] $signatures
 */
use \OE\factories\models\traits\HasFactory;

class Element_OphCoCorrespondence_Esign extends BaseEsignElement
{
    use AutoSignTrait;
    use HasFactory;

    private $signature_class = \OphCoCorrespondence_Signature::class;
    private $pin_required_setting_name = 'require_pin_for_correspondence';
    private $auto_sign_role = self::PRIMARY_ROLE;

    public const PRIMARY_ROLE = "From";
    public const SECONDARY_ROLE = "Secondary Signatory";

    protected $widgetClass = CorrespondenceEsignElementWidget::class;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return static the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophcocorrespondence_esign';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id', 'safe'),
            array('id, event_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'event' => array(self::BELONGS_TO, Event::class, 'event_id'),
            'user' => array(self::BELONGS_TO, User::class, 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
            'signatures' => array(self::HAS_MANY, OphCoCorrespondence_Signature::class, 'element_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return OphCoCorrespondence_Signature[]
     */
    public function getSignatures(): array
    {
        $signatures = $this->signatures;
        foreach ([self::PRIMARY_ROLE, self::SECONDARY_ROLE] as $role) {
            $signature = $this->getSignatureByRole($role);
            if (is_null($signature)) {
                $signature = new OphCoCorrespondence_Signature();
                $signature->type = BaseSignature::TYPE_OTHER_USER;
                $signature->signatory_role = $role;
                if(Yii::app()->user) {
                    $user = User::model()->findByPk(Yii::app()->user->getId());
                    /** @var User $user */
                    if($user) {
                        if($user->signOffUser) {
                            $signature->signed_user_id = $user->signOffUser->id;
                            $signature->signatory_name = $user->signOffUser->getFullName();
                        }
                    }
                }
                $signatures[] = $signature;
            }
        }

        return $signatures;
    }

    private function getSignatureByRole(string $role_name) : ?OphCoCorrespondence_Signature
    {
        $sign_arr = array_filter(
            $this->signatures,
            function ($signature) use ($role_name) {
                return $signature->signatory_role === $role_name;
            }
        );
        return empty($sign_arr) ? null : array_pop($sign_arr);
    }

    /**
     * A correspondence is signed if at least one of the signatures is done
     *
     * @return bool
     */
    public function isSigned() : bool
    {
        return !empty(
            array_filter(
                $this->signatures,
                function ($signature) {
                    return $signature->isSigned();
                }
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getUnsignedMessage(): string
    {
        return "This correspondence must be signed before it can be sent.";
    }

    /**
     * @param array $elements
     */
    public function eventScopeValidation(array $elements)
    {
        $elements = array_filter(
            $elements,
            function ($element) {
                return $element instanceof ElementLetter;
            }
        );
        if (!empty($elements)) {
            $element_letter = $elements[0];
            /** @var ElementLetter $element_letter */
            if (!$this->isSigned() && !$element_letter->draft) {
                $this->addError(
                    "id",
                    "At least one signature must be provided to finalize this Correspondence."
                );
            }
        }
    }
}
