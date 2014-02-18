<?php

/**
 * @author Noel Antonio
 * @date 01-29-2014
 */
class MembersController extends Controller
{
    public $msg = '';
    public $title = '';
    public $showDialog = false;
    public $showConfirm = false;
    
    public $layout = 'column2';
    
    public function actionIndex()
    {   
        if(!Yii::app()->user->hasUserAccess() && !Yii::app()->user->isSuperAdmin()) 
                $this->redirect(array('site/404'));
        
        $model = new MemberDetailsModel();
        $members = new MembersModel();
        
        if (isset($_POST["txtSearch"]) && $_POST["txtSearch"] != "")
        {
            $searchField = $_POST["txtSearch"];
            $rawData = $model->selectMemberDetailsBySearchField($searchField);
        }
        else
        {
            $rawData = $model->selectAllMemberDetails(); // In future, this will no longer be used if the data is too many.
        }
        
        // get upline and endorser
        foreach ($rawData as $key => $value) {
            $uplineInfo = $members->selectMemberName($value["upline_id"]);
            $endorserInfo = $members->selectMemberName($value["endorser_id"]);
            
            if (is_array($endorserInfo) && count($endorserInfo) > 0) {
                $endorser = $endorserInfo["last_name"] . ", " . $endorserInfo["first_name"] . " " . $endorserInfo["middle_name"];
            }
            else {
                $endorser = '';
            }
            $rawData[$key]["endorser"] = $endorser;
            
            if (is_array($uplineInfo) && count($uplineInfo) > 0) {
                $upline = $uplineInfo["last_name"] . ", " . $uplineInfo["first_name"] . " " . $uplineInfo["middle_name"];
            }
            else {
                $upline = '';
            }
            $rawData[$key]["upline"] = $upline;
        }
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('index', array('model'=>$model,'dataProvider'=>$dataProvider));
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
                /*$exist = $model->checkExistingEmail($model->email);
                
                if (count($exist) > 0 && $logged_in_id != $id)
                {
                    $this->title = "NOTIFICATION";
                    $this->msg = "Email already exist.";
                    $this->showDialog = "true";
                }
                else
                {*/
                    $this->title = "CONFIRMATION";
                    $this->msg = "Are you sure you want to modify this information?";
                    $this->showConfirm = true;
                //}
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
    
    public function actionTerminate()
    {
        $model = new MembersModel();
        
        if (!isset($_GET["id"])) {
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }
        
        $id = $_GET["id"];
        $rawData = $model->selectMemberName($id);
        $model->attributes = $rawData;
        
        $fullName = $rawData["first_name"] . " " . $rawData["middle_name"] . " " . $rawData["last_name"];

        $status_list = array ('1'=>'Active', '2'=>'Inactive', '3'=>'Terminated', '4'=>'Banned');
        $currentStatus = $status_list[$rawData["status"]]; // get current status
        unset($status_list[$rawData["status"]]); // remove the current status from the status list
        
        if (isset($_POST["MembersModel"]))
        {
            $model->member_id = $id;
            $model->attributes = $_POST["MembersModel"];
            
            if ($model->status == "")
            {
                $this->title = "NOTIFICATION";
                $this->msg = "Please select a status.";
                $this->showDialog = true;
            }
            else
            {
                $this->title = "CONFIRMATION";
                $this->msg = "Are you sure you want to change the status of this account?";
                $this->showConfirm = true;
            }
        }

        $this->render('_terminate', array('model'=>$model, 'fullName'=>$fullName, 'status'=>$currentStatus, 'list'=>$status_list));
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
    
    public function actionTerminateSuccess()
    {
        $model = new MembersModel();
        
        $model->attributes = $_POST["MembersModel"]; 
        $retval = $model->updateMemberStatus();
        if ($retval)
        {
            $msg = "Member status successfully modified.";
        }
        else
        {
            $msg = "No changes made on the member's info.";
        }
        
        echo $msg;
    }
}
?>
