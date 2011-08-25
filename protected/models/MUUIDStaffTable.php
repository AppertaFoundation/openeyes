<?php

/**
 * This is the model class for table "MUUID_Staff_Table".
 *
 * The followings are the available columns in table 'MUUID_Staff_Table':
 * @property integer $MUUID_Staff_UniqueID
 * @property string $MUUID_Staff_MUUID
 * @property string $MUUID_Staff_Title
 * @property string $MUUID_Staff_NameFirst
 * @property string $MUUID_Staff_NameMiddle
 * @property string $MUUID_Staff_NameLast
 * @property string $MUUID_Staff_KnownAs_NameFirst
 * @property string $MUUID_Staff_Gender
 * @property string $MUUID_Staff_JobTitle
 * @property integer $MUUID_Staff_NHSOrganisationNameID
 * @property integer $MUUID_Staff_MoorfieldsSiteID
 * @property integer $MUUID_Staff_DepartmentSduID
 * @property integer $MUUID_Staff_DepartmentID
 * @property string $MUUID_Staff_DepartmentSub
 * @property string $MUUID_Staff_Location
 * @property string $MUUID_Staff_Comments
 * @property string $MUUID_Staff_Home_Phone
 * @property string $MUUID_Staff_Mobile_Phone
 * @property string $MUUID_Staff_Internal_Phone
 * @property string $MUUID_Staff_Internal_Bleep
 * @property string $MUUID_Staff_PPsecretary_Phone
 * @property string $MUUID_Staff_NHSsecretary_Phone
 * @property string $MUUID_Staff_Clerk1_Phone
 * @property string $MUUID_Staff_Clerk2_Phone
 * @property string $MUUID_Staff_Notes_Phone
 * @property string $MUUID_Staff_MyBossMUUID
 * @property string $MUUID_Staff_MyAppraiserMUUID
 * @property string $MUUID_Staff_MyCoordinatorMUUID
 * @property string $MUUID_Staff_EmailAddress
 * @property string $MUUID_Staff_EmailAddressDelegate
 * @property string $MUUID_Staff_DomainUsername
 * @property integer $MUUID_Staff_EthnicityID
 * @property string $MUUID_Staff_EmployeeID
 * @property string $MUUID_Staff_DateOfStarting
 * @property boolean $MUUID_Staff_LeftMEH
 * @property string $MUUID_Staff_DateOfLeaving
 * @property string $MUUID_Staff_PersonnelID
 * @property string $MUUID_Staff_PhotoCardID
 * @property boolean $MUUID_Staff_IsPhotoCardIDDisplayPermitted
 * @property string $MUUID_Staff_CreatedBy
 * @property string $MUUID_Staff_CreatedDate
 * @property string $EPR_MedicalDegrees
 * @property string $EPR_JobType
 * @property string $EPR_JobDescription
 * @property string $EPR_LetterSignoff
 * @property integer $EPR_MedicalGrade
 * @property integer $EPR_Service_CodeID
 * @property integer $EPR_Firm_CodeID
 * @property string $EPR_ConsultantNameText
 * @property string $EPR_ConsultantCode
 * @property integer $EPR_DefaultWardID
 * @property integer $EPR_RoleID
 *
 * The followings are the available model relations:
 * @property MUUIDMEHClinicalUsersTable $mUUIDStaffDomainUsername
 * @property EPRServicesTable $ePRServiceCode
 * @property EPRWardsTable $ePRDefaultWard
 * @property EPRFirmsTable $ePRFirmCode
 * @property MUUIDDepartmentsTable $mUUIDStaffDepartment
 * @property MUUIDDepartmentSDUsTable $mUUIDStaffDepartmentSdu
 * @property MUUIDEthnicityTable $mUUIDStaffEthnicity
 * @property MUUIDGenderTable $mUUIDStaffGender
 * @property MUUIDNHSOrganisationNamesTable $mUUIDStaffNHSOrganisationName
 * @property MUUIDMoorfieldsSitesTable $mUUIDStaffMoorfieldsSite
 */
class MUUIDStaffTable extends MultiActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return MUUIDStaffTable the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

        /**
         * @return string the associated db connection name
         */
        public function connectionId()
        {
                return 'db_muu';
        }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'MUUID_Staff_Table';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('MUUID_Staff_MUUID, MUUID_Staff_Title, MUUID_Staff_NameFirst, MUUID_Staff_NameMiddle, MUUID_Staff_NameLast, MUUID_Staff_KnownAs_NameFirst, MUUID_Staff_Gender, MUUID_Staff_JobTitle, MUUID_Staff_NHSOrganisationNameID, MUUID_Staff_MoorfieldsSiteID, MUUID_Staff_DepartmentSduID, MUUID_Staff_DepartmentID, MUUID_Staff_Location, MUUID_Staff_EthnicityID, MUUID_Staff_DateOfStarting, MUUID_Staff_LeftMEH, MUUID_Staff_IsPhotoCardIDDisplayPermitted, MUUID_Staff_CreatedBy, MUUID_Staff_CreatedDate, EPR_Service_CodeID, EPR_DefaultWardID', 'required'),
			array('MUUID_Staff_NHSOrganisationNameID, MUUID_Staff_MoorfieldsSiteID, MUUID_Staff_DepartmentSduID, MUUID_Staff_DepartmentID, MUUID_Staff_EthnicityID, EPR_MedicalGrade, EPR_Service_CodeID, EPR_Firm_CodeID, EPR_DefaultWardID, EPR_RoleID', 'numerical', 'integerOnly'=>true),
			array('MUUID_Staff_MUUID, MUUID_Staff_MyBossMUUID, MUUID_Staff_MyAppraiserMUUID, MUUID_Staff_MyCoordinatorMUUID', 'length', 'max'=>11),
			array('MUUID_Staff_Title, MUUID_Staff_NameFirst, MUUID_Staff_NameMiddle, MUUID_Staff_NameLast, MUUID_Staff_KnownAs_NameFirst, MUUID_Staff_JobTitle, MUUID_Staff_DepartmentSub, MUUID_Staff_Location, EPR_LetterSignoff', 'length', 'max'=>100),
			array('MUUID_Staff_Gender', 'length', 'max'=>10),
			array('MUUID_Staff_Home_Phone, MUUID_Staff_Mobile_Phone, MUUID_Staff_Internal_Phone, MUUID_Staff_Internal_Bleep, MUUID_Staff_PPsecretary_Phone, MUUID_Staff_NHSsecretary_Phone, MUUID_Staff_Clerk1_Phone, MUUID_Staff_Clerk2_Phone, MUUID_Staff_DomainUsername, MUUID_Staff_EmployeeID, MUUID_Staff_CreatedBy, EPR_MedicalDegrees, EPR_JobType, EPR_ConsultantNameText', 'length', 'max'=>50),
			array('MUUID_Staff_EmailAddress, MUUID_Staff_EmailAddressDelegate, EPR_JobDescription', 'length', 'max'=>200),
			array('MUUID_Staff_PersonnelID, MUUID_Staff_PhotoCardID', 'length', 'max'=>20),
			array('EPR_ConsultantCode', 'length', 'max'=>5),
			array('MUUID_Staff_Comments, MUUID_Staff_Notes_Phone, MUUID_Staff_DateOfLeaving', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('MUUID_Staff_UniqueID, MUUID_Staff_MUUID, MUUID_Staff_Title, MUUID_Staff_NameFirst, MUUID_Staff_NameMiddle, MUUID_Staff_NameLast, MUUID_Staff_KnownAs_NameFirst, MUUID_Staff_Gender, MUUID_Staff_JobTitle, MUUID_Staff_NHSOrganisationNameID, MUUID_Staff_MoorfieldsSiteID, MUUID_Staff_DepartmentSduID, MUUID_Staff_DepartmentID, MUUID_Staff_DepartmentSub, MUUID_Staff_Location, MUUID_Staff_Comments, MUUID_Staff_Home_Phone, MUUID_Staff_Mobile_Phone, MUUID_Staff_Internal_Phone, MUUID_Staff_Internal_Bleep, MUUID_Staff_PPsecretary_Phone, MUUID_Staff_NHSsecretary_Phone, MUUID_Staff_Clerk1_Phone, MUUID_Staff_Clerk2_Phone, MUUID_Staff_Notes_Phone, MUUID_Staff_MyBossMUUID, MUUID_Staff_MyAppraiserMUUID, MUUID_Staff_MyCoordinatorMUUID, MUUID_Staff_EmailAddress, MUUID_Staff_EmailAddressDelegate, MUUID_Staff_DomainUsername, MUUID_Staff_EthnicityID, MUUID_Staff_EmployeeID, MUUID_Staff_DateOfStarting, MUUID_Staff_LeftMEH, MUUID_Staff_DateOfLeaving, MUUID_Staff_PersonnelID, MUUID_Staff_PhotoCardID, MUUID_Staff_IsPhotoCardIDDisplayPermitted, MUUID_Staff_CreatedBy, MUUID_Staff_CreatedDate, EPR_MedicalDegrees, EPR_JobType, EPR_JobDescription, EPR_LetterSignoff, EPR_MedicalGrade, EPR_Service_CodeID, EPR_Firm_CodeID, EPR_ConsultantNameText, EPR_ConsultantCode, EPR_DefaultWardID, EPR_RoleID', 'safe', 'on'=>'search'),
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
			'mUUIDStaffDomainUsername' => array(self::BELONGS_TO, 'MUUIDMEHClinicalUsersTable', 'MUUID_Staff_DomainUsername'),
			'ePRServiceCode' => array(self::BELONGS_TO, 'EPRServicesTable', 'EPR_Service_CodeID'),
			'ePRDefaultWard' => array(self::BELONGS_TO, 'EPRWardsTable', 'EPR_DefaultWardID'),
			'ePRFirmCode' => array(self::BELONGS_TO, 'EPRFirmsTable', 'EPR_Firm_CodeID'),
			'mUUIDStaffDepartment' => array(self::BELONGS_TO, 'MUUIDDepartmentsTable', 'MUUID_Staff_DepartmentID'),
			'mUUIDStaffDepartmentSdu' => array(self::BELONGS_TO, 'MUUIDDepartmentSDUsTable', 'MUUID_Staff_DepartmentSduID'),
			'mUUIDStaffEthnicity' => array(self::BELONGS_TO, 'MUUIDEthnicityTable', 'MUUID_Staff_EthnicityID'),
			'mUUIDStaffGender' => array(self::BELONGS_TO, 'MUUIDGenderTable', 'MUUID_Staff_Gender'),
			'mUUIDStaffNHSOrganisationName' => array(self::BELONGS_TO, 'MUUIDNHSOrganisationNamesTable', 'MUUID_Staff_NHSOrganisationNameID'),
			'mUUIDStaffMoorfieldsSite' => array(self::BELONGS_TO, 'MUUIDMoorfieldsSitesTable', 'MUUID_Staff_MoorfieldsSiteID'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'MUUID_Staff_UniqueID' => 'Muuid Staff Unique',
			'MUUID_Staff_MUUID' => 'Muuid Staff Muuid',
			'MUUID_Staff_Title' => 'Muuid Staff Title',
			'MUUID_Staff_NameFirst' => 'Muuid Staff Name First',
			'MUUID_Staff_NameMiddle' => 'Muuid Staff Name Middle',
			'MUUID_Staff_NameLast' => 'Muuid Staff Name Last',
			'MUUID_Staff_KnownAs_NameFirst' => 'Muuid Staff Known As Name First',
			'MUUID_Staff_Gender' => 'Muuid Staff Gender',
			'MUUID_Staff_JobTitle' => 'Muuid Staff Job Title',
			'MUUID_Staff_NHSOrganisationNameID' => 'Muuid Staff Nhsorganisation Name',
			'MUUID_Staff_MoorfieldsSiteID' => 'Muuid Staff Moorfields Site',
			'MUUID_Staff_DepartmentSduID' => 'Muuid Staff Department Sdu',
			'MUUID_Staff_DepartmentID' => 'Muuid Staff Department',
			'MUUID_Staff_DepartmentSub' => 'Muuid Staff Department Sub',
			'MUUID_Staff_Location' => 'Muuid Staff Location',
			'MUUID_Staff_Comments' => 'Muuid Staff Comments',
			'MUUID_Staff_Home_Phone' => 'Muuid Staff Home Phone',
			'MUUID_Staff_Mobile_Phone' => 'Muuid Staff Mobile Phone',
			'MUUID_Staff_Internal_Phone' => 'Muuid Staff Internal Phone',
			'MUUID_Staff_Internal_Bleep' => 'Muuid Staff Internal Bleep',
			'MUUID_Staff_PPsecretary_Phone' => 'Muuid Staff Ppsecretary Phone',
			'MUUID_Staff_NHSsecretary_Phone' => 'Muuid Staff Nhssecretary Phone',
			'MUUID_Staff_Clerk1_Phone' => 'Muuid Staff Clerk1 Phone',
			'MUUID_Staff_Clerk2_Phone' => 'Muuid Staff Clerk2 Phone',
			'MUUID_Staff_Notes_Phone' => 'Muuid Staff Notes Phone',
			'MUUID_Staff_MyBossMUUID' => 'Muuid Staff My Boss Muuid',
			'MUUID_Staff_MyAppraiserMUUID' => 'Muuid Staff My Appraiser Muuid',
			'MUUID_Staff_MyCoordinatorMUUID' => 'Muuid Staff My Coordinator Muuid',
			'MUUID_Staff_EmailAddress' => 'Muuid Staff Email Address',
			'MUUID_Staff_EmailAddressDelegate' => 'Muuid Staff Email Address Delegate',
			'MUUID_Staff_DomainUsername' => 'Muuid Staff Domain Username',
			'MUUID_Staff_EthnicityID' => 'Muuid Staff Ethnicity',
			'MUUID_Staff_EmployeeID' => 'Muuid Staff Employee',
			'MUUID_Staff_DateOfStarting' => 'Muuid Staff Date Of Starting',
			'MUUID_Staff_LeftMEH' => 'Muuid Staff Left Meh',
			'MUUID_Staff_DateOfLeaving' => 'Muuid Staff Date Of Leaving',
			'MUUID_Staff_PersonnelID' => 'Muuid Staff Personnel',
			'MUUID_Staff_PhotoCardID' => 'Muuid Staff Photo Card',
			'MUUID_Staff_IsPhotoCardIDDisplayPermitted' => 'Muuid Staff Is Photo Card Iddisplay Permitted',
			'MUUID_Staff_CreatedBy' => 'Muuid Staff Created By',
			'MUUID_Staff_CreatedDate' => 'Muuid Staff Created Date',
			'EPR_MedicalDegrees' => 'Epr Medical Degrees',
			'EPR_JobType' => 'Epr Job Type',
			'EPR_JobDescription' => 'Epr Job Description',
			'EPR_LetterSignoff' => 'Epr Letter Signoff',
			'EPR_MedicalGrade' => 'Epr Medical Grade',
			'EPR_Service_CodeID' => 'Epr Service Code',
			'EPR_Firm_CodeID' => 'Epr Firm Code',
			'EPR_ConsultantNameText' => 'Epr Consultant Name Text',
			'EPR_ConsultantCode' => 'Epr Consultant Code',
			'EPR_DefaultWardID' => 'Epr Default Ward',
			'EPR_RoleID' => 'Epr Role',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('MUUID_Staff_UniqueID',$this->MUUID_Staff_UniqueID);
		$criteria->compare('MUUID_Staff_MUUID',$this->MUUID_Staff_MUUID,true);
		$criteria->compare('MUUID_Staff_Title',$this->MUUID_Staff_Title,true);
		$criteria->compare('MUUID_Staff_NameFirst',$this->MUUID_Staff_NameFirst,true);
		$criteria->compare('MUUID_Staff_NameMiddle',$this->MUUID_Staff_NameMiddle,true);
		$criteria->compare('MUUID_Staff_NameLast',$this->MUUID_Staff_NameLast,true);
		$criteria->compare('MUUID_Staff_KnownAs_NameFirst',$this->MUUID_Staff_KnownAs_NameFirst,true);
		$criteria->compare('MUUID_Staff_Gender',$this->MUUID_Staff_Gender,true);
		$criteria->compare('MUUID_Staff_JobTitle',$this->MUUID_Staff_JobTitle,true);
		$criteria->compare('MUUID_Staff_NHSOrganisationNameID',$this->MUUID_Staff_NHSOrganisationNameID);
		$criteria->compare('MUUID_Staff_MoorfieldsSiteID',$this->MUUID_Staff_MoorfieldsSiteID);
		$criteria->compare('MUUID_Staff_DepartmentSduID',$this->MUUID_Staff_DepartmentSduID);
		$criteria->compare('MUUID_Staff_DepartmentID',$this->MUUID_Staff_DepartmentID);
		$criteria->compare('MUUID_Staff_DepartmentSub',$this->MUUID_Staff_DepartmentSub,true);
		$criteria->compare('MUUID_Staff_Location',$this->MUUID_Staff_Location,true);
		$criteria->compare('MUUID_Staff_Comments',$this->MUUID_Staff_Comments,true);
		$criteria->compare('MUUID_Staff_Home_Phone',$this->MUUID_Staff_Home_Phone,true);
		$criteria->compare('MUUID_Staff_Mobile_Phone',$this->MUUID_Staff_Mobile_Phone,true);
		$criteria->compare('MUUID_Staff_Internal_Phone',$this->MUUID_Staff_Internal_Phone,true);
		$criteria->compare('MUUID_Staff_Internal_Bleep',$this->MUUID_Staff_Internal_Bleep,true);
		$criteria->compare('MUUID_Staff_PPsecretary_Phone',$this->MUUID_Staff_PPsecretary_Phone,true);
		$criteria->compare('MUUID_Staff_NHSsecretary_Phone',$this->MUUID_Staff_NHSsecretary_Phone,true);
		$criteria->compare('MUUID_Staff_Clerk1_Phone',$this->MUUID_Staff_Clerk1_Phone,true);
		$criteria->compare('MUUID_Staff_Clerk2_Phone',$this->MUUID_Staff_Clerk2_Phone,true);
		$criteria->compare('MUUID_Staff_Notes_Phone',$this->MUUID_Staff_Notes_Phone,true);
		$criteria->compare('MUUID_Staff_MyBossMUUID',$this->MUUID_Staff_MyBossMUUID,true);
		$criteria->compare('MUUID_Staff_MyAppraiserMUUID',$this->MUUID_Staff_MyAppraiserMUUID,true);
		$criteria->compare('MUUID_Staff_MyCoordinatorMUUID',$this->MUUID_Staff_MyCoordinatorMUUID,true);
		$criteria->compare('MUUID_Staff_EmailAddress',$this->MUUID_Staff_EmailAddress,true);
		$criteria->compare('MUUID_Staff_EmailAddressDelegate',$this->MUUID_Staff_EmailAddressDelegate,true);
		$criteria->compare('MUUID_Staff_DomainUsername',$this->MUUID_Staff_DomainUsername,true);
		$criteria->compare('MUUID_Staff_EthnicityID',$this->MUUID_Staff_EthnicityID);
		$criteria->compare('MUUID_Staff_EmployeeID',$this->MUUID_Staff_EmployeeID,true);
		$criteria->compare('MUUID_Staff_DateOfStarting',$this->MUUID_Staff_DateOfStarting,true);
		$criteria->compare('MUUID_Staff_LeftMEH',$this->MUUID_Staff_LeftMEH);
		$criteria->compare('MUUID_Staff_DateOfLeaving',$this->MUUID_Staff_DateOfLeaving,true);
		$criteria->compare('MUUID_Staff_PersonnelID',$this->MUUID_Staff_PersonnelID,true);
		$criteria->compare('MUUID_Staff_PhotoCardID',$this->MUUID_Staff_PhotoCardID,true);
		$criteria->compare('MUUID_Staff_IsPhotoCardIDDisplayPermitted',$this->MUUID_Staff_IsPhotoCardIDDisplayPermitted);
		$criteria->compare('MUUID_Staff_CreatedBy',$this->MUUID_Staff_CreatedBy,true);
		$criteria->compare('MUUID_Staff_CreatedDate',$this->MUUID_Staff_CreatedDate,true);
		$criteria->compare('EPR_MedicalDegrees',$this->EPR_MedicalDegrees,true);
		$criteria->compare('EPR_JobType',$this->EPR_JobType,true);
		$criteria->compare('EPR_JobDescription',$this->EPR_JobDescription,true);
		$criteria->compare('EPR_LetterSignoff',$this->EPR_LetterSignoff,true);
		$criteria->compare('EPR_MedicalGrade',$this->EPR_MedicalGrade);
		$criteria->compare('EPR_Service_CodeID',$this->EPR_Service_CodeID);
		$criteria->compare('EPR_Firm_CodeID',$this->EPR_Firm_CodeID);
		$criteria->compare('EPR_ConsultantNameText',$this->EPR_ConsultantNameText,true);
		$criteria->compare('EPR_ConsultantCode',$this->EPR_ConsultantCode,true);
		$criteria->compare('EPR_DefaultWardID',$this->EPR_DefaultWardID);
		$criteria->compare('EPR_RoleID',$this->EPR_RoleID);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
