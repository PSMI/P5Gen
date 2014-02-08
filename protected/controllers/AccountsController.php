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
            $logged_in_id = Yii::app()->user->getId();
            $model->member_id = $id;
            $model->attributes = $_POST["MemberDetailsModel"];
             
            if ($model->validate())
            {
                $exist = $model->checkExistingEmail($model->email);
                if (count($exist) > 0 && $logged_in_id != $id)
                {
                    $this->title = "NOTIFICATION";
                    $this->msg = "Email already exist.";
                    $this->showDialog = "true";
                }
                else
                {
                    $this->title = "CONFIRMATION";
                    $this->msg = "Are you sure you want to modify this information?";
                    $this->showConfirm = true;
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
                $exist = $model->checkExistingEmail($model->email);
                if (count($exist) > 0)
                {
                    $this->title = "NOTIFICATION";
                    $this->msg = "Email already exist.";
                    $this->showDialog = "true";
                }
                else
                {
                    $this->title = "CONFIRMATION";
                    $this->msg = "Are you sure you want to modify this information?";
                    $this->showConfirm = true;
                }
            }
            else
            {
                $this->title = "NOTIFICATION";
                $this->msg = "Please fill-up the required fields.";
                $this->showDialog = true;
            }
        }
        
        $this->render('_create', array('model'=>$model, 'membersModel'=>$membersModel, 'accountList'=>$accountTypeList, 'maxId'=>$maxId));
    }
    
    public function actionUpdateSuccess()
    {
        $model = new MemberDetailsModel();
        
        $model->attributes = $_POST["MemberDetailsModel"];
        
        $retval = $model->updateMemberDetails();

        if ($retval)
        {
            $msg = "Member information successfully modified.";
        }
        else
        {
            $msg = "No changes made on the member's info.";
        }      
        
        echo $msg;
    }
    
    public function actionCreateSuccess()
    {
        $model = new MemberDetailsModel();
        $membersModel = new MembersModel();
        
        $membersModel->attributes = $_POST["MembersModel"];
        $membersModel->status = 1; // set default status as ACTIVE.
        $model->attributes = $_POST["MemberDetailsModel"];
        
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
        $telephone_no = $model->telephone_no;
        $email = $model->email;
        $tin_no = $model->tin_no;
        $company = $model->company;
        $occupation_id = $model->occupation_id;
        $spouse_name = $model->spouse_name;
        $spouse_contact_no = $model->spouse_contact_no;
        $beneficiary_name = $model->beneficiary_name;
        $relationship_id = $model->relationship_id;
        
        $retval = $membersModel->insertNewMemberAccount($account_type_id, $username, $password,
            $last_name, $first_name, $middle_name, $address1, $address2, $address3,
            $zip_code, $gender, $civil_status, $birth_date, $mobile_no, $telephone_no,
            $email, $tin_no, $company, $occupation_id, $spouse_name, $spouse_contact_no,
            $beneficiary_name, $relationship_id);

        if ($retval)
        {
            $msg = "Member information successfully created.";
        }
        else
        {
            $msg = "Error in creating admin account.";
        }
        
        echo $msg;
    }
    
    public function actionAjaxUser()
    {
        $member_id = $_POST["id"];
        $firstname = $_POST["first"];
        $lastname = $_POST["last"];

        $username = Helpers::generate($member_id, $firstname, $lastname);

        echo json_encode($username);
    }
}
?>
