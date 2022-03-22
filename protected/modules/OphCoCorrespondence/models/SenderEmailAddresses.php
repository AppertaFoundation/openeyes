<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "ophcocorrespondence_sender_email_addresses".
 *
 * The followings are the available columns in table 'ophcocorrespondence_sender_email_addresses':
 * @property integer $id
 * @property string $host
 * @property string $username
 * @property string $password
 * @property string $reply_to_address
 * @property integer $port
 * @property string $security
 * @property int $institution_id
 * @property int $site_id
 * @property string $domain
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Institution $institution
 * @property Site $site
 * @property User $created_user
 * @property User $last_modified_user
 */
class SenderEmailAddresses extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophcocorrespondence_sender_email_addresses';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('host, username, password, port, security, domain', 'required'),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('reply_to_address, institution_id, site_id', 'default', 'setOnEmpty' => true, 'value' => null),
            array('domain', 'match', 'pattern' => '/(?:\*|@.*\..*)$/', 'message' => '{attribute} should only contain either * or @domain.com'),
            array('domain', 'institutionSiteDomainValidator'),
            array('reply_to_address, institution_id, site_id, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array(
                'id, host, username, password, reply_to_address, port, security, institution_id, site_id, domain, last_modified_user_id, last_modified_date, created_user_id, created_date',
                'safe',
                'on'=>'search'
            ),
        );
    }

    public function institutionSiteDomainValidator($attribute, $params)
    {
        $op1 = ($this->institution_id != '' ? ' = ' : ' IS ' );
        $op2 = ($this->site_id != '' ? ' = ' : ' IS ' );

        $query = Yii::app()->db->createCommand()
            ->select('osea.id')
            ->from('ophcocorrespondence_sender_email_addresses osea')
            ->where(
                'osea.institution_id' . $op1 . ':institution_id and osea.site_id' . $op2 . ':site_id and LOWER(osea.domain) = LOWER(:domain) and osea.id != :sender_email_address_id',
                array(':institution_id' => $this->institution_id, ':site_id' => $this->site_id, ':domain' => $this->domain, ':sender_email_address_id' => $this->id)
            )
            ->queryAll();

        if (count($query) !== 0) {
            $this->addError($attribute, 'This combination of institution, site and domain already exists.');
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'created_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'last_modified_user' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return string[] customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'host' => 'Host',
            'username' => 'Username',
            'password' => 'Password',
            'reply_to_address' => 'Reply-To Address',
            'port' => 'Port',
            'security' => 'Security',
            'institution_id' => 'Institution',
            'site_id' => 'Site',
            'domain' => 'domain',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('institution_id', $this->institution_id, true);
        $criteria->compare('reply_to_address', $this->reply_to_address, true);
        $criteria->compare('site_id', $this->site_id, true);
        $criteria->compare('domain', $this->domain, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SenderEmailAddresses the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getSenderAddress($email, $institution_id, $site_id):? SenderEmailAddresses
    {
        $email_domain = substr($email, strpos($email, '@'));

        $criteria = new \CDbCriteria();
        $criteria->addCondition("t.institution_id = :institution_id OR t.institution_id IS NULL");
        $criteria->addCondition("t.site_id = :site_id OR t.site_id IS NULL");
        $criteria->addInCondition('t.domain', ['*', $email_domain]);
        $criteria->order = 't.institution_id IS NULL, t.site_id IS NULL, t.domain = "*"';
        $criteria->limit = 1;

        $criteria->params[':institution_id'] = $institution_id;
        $criteria->params[':site_id'] = $site_id;

        return SenderEmailAddresses::model()->find($criteria);
    }

    /**
     * Prepare global mailer
     *
     * @throws SodiumException
     */
    public function prepareMailer(): void
    {
        // Setting up the SMTP properties
        \Yii::app()->mailer->setSmtpHost($this->host);
        \Yii::app()->mailer->setSmtpUsername($this->username);

        // decrypt the password
        $encryptionDecryptionHelper = new EncryptionDecryptionHelper();
        $password = $encryptionDecryptionHelper->decryptData($this->password);
        \Yii::app()->mailer->setSmtpPassword($password);

        \Yii::app()->mailer->setSmtpPort($this->port);
        \Yii::app()->mailer->setSmtpSecurity($this->security);
    }
}
