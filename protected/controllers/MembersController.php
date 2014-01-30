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
    public $showRedirect = false;
    
    public function actionIndex()
    {
        $model = new MembersModel();
        
        $rawData = $model->selectAllMemberDetails();
        
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
        $model = new MembersModel();
        
        if (!isset($_GET["id"])) {
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }
        
        $id = $_GET["id"];
        $rawData = $model->selectMemberById($id);
        $model->attributes = $rawData;
        
        if (isset($_POST["MembersModel"])) 
        {
           
            $model->attributes = $_POST["MembersModel"];
             
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
    
    public function actionTerminate()
    {
        $model = new MemberDetailsModel();
        
        if (!isset($_GET["id"])) {
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }
        
        $id = $_GET["id"];
        $rawData = $model->selectMemberDetailsStatus($id);
        $model->attributes = $rawData;
        
        $fullName = $rawData["first_name"] . " " . $rawData["middle_name"] . " " . $rawData["last_name"];

        $status_list = array ('1'=>'Active', '2'=>'Inactive', '3'=>'Terminated', '4'=>'Banned');
        $currentStatus = $status_list[$rawData["status"]]; // get current status
        unset($status_list[$rawData["status"]]); // remove the current status from the status list
        
        if (isset($_POST["MemberDetailsModel"]))
        {
            $model->attributes = $_POST["MemberDetailsModel"];
            
            if ($model->validate())
            {
                $retval = $model->updateMemberStatus();
                
                if ($retval)
                {
                    $this->title = "SUCCESSFUL";
                    $this->msg = "Member status successfully modified.";
                    $this->showRedirect = true;
                }
                else
                {
                    $this->title = "NOTIFICATION";
                    $this->msg = "No changes made on the member's info.";
                    $this->showRedirect = true;
                }
            }
        }

        $this->render('_terminate', array('model'=>$model, 'fullName'=>$fullName, 'status'=>$currentStatus, 'list'=>$status_list));
    }
}
?>
