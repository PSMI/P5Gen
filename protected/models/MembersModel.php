<?php

/**
 * @author Noel Antonio
 * @date 01-30-2014
 */
class MembersModel extends CFormModel
{
    public $_connection;
    public $member_id;
    public $last_name;
    public $first_name;
    public $middle_name;
    public $address1;
    public $address2;
    public $address3;
    public $zip_code;
    public $gender;
    public $civil_status;
    public $birth_date;
    public $mobile_no;
    public $telephone_fax_no;
    public $email;
    public $tin_number;
    public $company;
    public $occupation_id;
    public $spouse_name;
    public $spouse_contact_no;
    public $beneficiary;
    public $relationship;
    
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
                array('member_id, last_name, first_name, middle_name
                        address1, address2, address3, zip_code, civil_status
                        mobile_no, telephone_fax_no, email, tin_number
                        company, occupation_id, spouse_name, spouse_contact_no
                        beneficiary, relationship, gender', 'required'),
            
                array('birth_date', 'safe')
            );
    }
    
    public function attributeLabels()
    {
        return array(
                'member_id' => 'ID',
                'last_name' => 'Last Name',
                'first_name' => 'First Name',
                'address1' => 'Address 1',
                'address2' => 'Address 2',
                'address3' => 'Address 3',
                'zip_code' => 'Zip Code',
                'gender'=>'Gender',
                'civil_status'=>'Civil Status',
                'birth_date'=>'Birth Date',
                'mobile_no'=>'Mobile Number',
                'telephone_fax_no'=>'Telephone Number',
                'email'=>'Email',
                'tin_number'=>'TIN',
                'company'=>'Company',
                'occupation_id'=>'Occupation ID',
                'spouse_name'=>'Spouse Name',
                'spouse_contact_no'=>'Spouse Contact Number',
                'beneficiary'=>'Beneficiary',
                'relationship'=>'Relationship',
                'status'=>'Status'
        );
    }
    
    public function selectAllAdminDetails()
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.last_name, a.first_name, a.middle_name,
                a.birth_date, a.mobile_no, a.email
                FROM member_details a
                INNER JOIN members b ON a.member_id = b.member_id
                WHERE b.account_type_id = 2";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function selectAllMemberDetails()
    {
        $connection = $this->_connection;
        
        $sql = "SELECT a.member_id, a.last_name, a.first_name, a.middle_name,
                a.birth_date, a.mobile_no, a.email
                FROM member_details a
                INNER JOIN members b ON a.member_id = b.member_id
                WHERE b.account_type_id = 3";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function selectMemberById($id)
    {
        $connection = $this->_connection;
        
        $sql = "SELECT * FROM member_details WHERE member_id = :member_id";
        $command = $connection->createCommand($sql);
        $command->bindParam(":member_id", $id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function updateMemberDetails()
    {
        $connection = $this->_connection;
        $beginTrans = $connection->beginTransaction();
        
        try
        {
            $sql = "UPDATE member_details SET last_name = :last_name, first_name = :first_name,
                        middle_name = :middle_name, address1 = :address1, address2 = :address2,
                        address3 = :address3, zip_code = :zip_code, gender = :gender, civil_status = :civil_status,
                        birth_date = :birth_date, mobile_no = :mobile_no, telephone_fax_no = :telephone_fax_no,
                        email = :email, tin_number = :tin_number, company = :company, occupation_id = :occupation_id,
                        spouse_name = :spouse_name, spouse_contact_no = :spouse_contact_no, beneficiary = :beneficiary,
                        relationship = :relationship
                    WHERE member_id = :member_id";
            $command = $connection->createCommand($sql);
            $command->bindValue(':member_id', $this->member_id);
            $command->bindValue(':last_name', $this->last_name);
            $command->bindValue(':first_name', $this->first_name);
            $command->bindValue(':middle_name', $this->middle_name);
            $command->bindValue(':address1', $this->address1);
            $command->bindValue(':address2', $this->address2);
            $command->bindValue(':address3', $this->address3);
            $command->bindValue(':zip_code', $this->zip_code);
            $command->bindValue(':gender', $this->gender);
            $command->bindValue(':civil_status', $this->civil_status);
            $command->bindValue(':birth_date', $this->birth_date);
            $command->bindValue(':mobile_no', $this->mobile_no);
            $command->bindValue(':telephone_fax_no', $this->telephone_fax_no);
            $command->bindValue(':email', $this->email);
            $command->bindValue(':tin_number', $this->tin_number);
            $command->bindValue(':company', $this->company);
            $command->bindValue(':occupation_id', $this->occupation_id);
            $command->bindValue(':spouse_name', $this->spouse_name);
            $command->bindValue(':spouse_contact_no', $this->spouse_contact_no);
            $command->bindValue(':beneficiary', $this->beneficiary);
            $command->bindValue(':relationship', $this->relationship);
            $rowCount = $command->execute();
            
            if ($rowCount > 0) {
                    $beginTrans->commit();
                    return true;
            } else {
                $beginTrans->rollback();  
                return false;
            }
        }
        catch (CDbException $e)
        {
            $beginTrans->rollback();  
            return false;
        }
    }
}
?>
