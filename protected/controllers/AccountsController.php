<?php

/**
 * @author Noel Antonio
 * @date 01-28-2014
 */

class AccountsController extends Controller 
{
    public $msg = '';
    public $title = '';
    public $showDialog = false;
    public $showConfirm = false;
    public $showRedirect = false;
    
    public $layout = 'column2';
    
    public function actionIndex()
    {
//        if(!Yii::app()->user->hasUserAccess() && !Yii::app()->user->isSuperAdmin()) 
//                $this->redirect(array('site/404'));
        
        $model = new MemberDetailsModel();
        
        if (isset($_POST["txtSearch"]) && $_POST["txtSearch"] != "")
        {
            $searchField = $_POST["txtSearch"];
            $rawData = $model->selectAdminDetailsBySearchField($searchField);
        }
        else
        {
            $rawData = $model->selectAllAdminDetails();
        }
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('index', array('dataProvider'=>$dataProvider));
    }
    
    public function actionUpdate()
    {
        $model = new MemberDetailsModel();
        
        if (!isset($_GET["id"])) {
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }
        
        $id = $_GET["id"];
        $rawData = $model->selectMemberById($id);
        $model->attributes = $rawData;
        
        if (isset($_POST["MemberDetailsModel"])) 
        {
            $model->member_id = $id;
            $model->attributes = $_POST["MemberDetailsModel"];
             
            if ($model->validate())
            {
                $retval = $model->updateMemberDetails();
                
                if ($retval)
                {
                    $this->title = "SUCCESSFUL";
                    $this->msg = "Member information successfully modified.";
                    $this->showRedirect = true;
                }
                else
                {
                    $this->title = "NOTIFICATION";
                    $this->msg = "No changes made on the member's info.";
                    $this->showRedirect = true;
                }
            }
            else
            {
                $this->title = "NOTIFICATION";
                $this->msg = "Please fill-up the required fields.";
                $this->showDialog = true;
            }
        }
        
        $this->render('_update', array('model'=>$model));
    }
    
    public function actionCreate()
    {
        $model = new MemberDetailsModel();
        $membersModel = new MembersModel();
        $accountTypeModel = new AccountTypes();
        
        $accountTypeList = $accountTypeModel->selectAllAccountTypes();
        $maxId = $membersModel->selectMemberMaxId();
        
        if (isset($_POST["MembersModel"]))
        {
            $membersModel->attributes = $_POST["MembersModel"];
            $membersModel->status = 1; // set default status as ACTIVE.
            $model->attributes = $_POST["MemberDetailsModel"];
            
            if ($model->validate() && $membersModel->validate())
            {
                $account_type_id = $membersModel->account_type_id;
                $username = $membersModel->username;
                $password = $membersModel->password;
                $last_name = $model->last_name;
                $first_name = $model->first_name;
                $middle_name = $model->middle_name;
                $address1 = $model->address1;
                $address2 = $model->address2;
                $address3 = $model->address3;
                $zip_code = $model->zip_code;
                $gender = $model->gender;
                $civil_status = $model->civil_status;
                $birth_date = $model->birth_date;
                $mobile_no = $model->mobile_no;
                $telephone_fax_no = $model->telephone_fax_no;
                $email = $model->email;
                $tin_number = $model->tin_number;
                $company = $model->company;
                $occupation_id = $model->occupation_id;
                $spouse_name = $model->spouse_name;
                $spouse_contact_no = $model->spouse_contact_no;
                $beneficiary = $model->beneficiary;
                $relationship = $model->relationship;
                
                $retval = $membersModel->insertNewMemberAccount($account_type_id, $username, $password,
                        $last_name, $first_name, $middle_name, $address1, $address2, $address3,
                        $zip_code, $gender, $civil_status, $birth_date, $mobile_no, $telephone_fax_no,
                        $email, $tin_number, $company, $occupation_id, $spouse_name, $spouse_contact_no,
                        $beneficiary, $relationship);
                
                if ($retval)
                {
                    $this->title = "SUCCESSFUL";
                    $this->msg = "Member status successfully created.";
                    $this->showRedirect = true;
                }
                else
                {
                    $this->title = "NOTIFICATION";
                    $this->msg = "Error in creating account.";
                    $this->showRedirect = true;
                }
            }
        }
        
        $this->render('_create', array('model'=>$model, 'membersModel'=>$membersModel, 'accountList'=>$accountTypeList, 'maxId'=>$maxId));
    }
}
?>
