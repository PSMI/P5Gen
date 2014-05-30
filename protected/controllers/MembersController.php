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

        if (isset($_POST['btnSearch']) && $_POST['txtSearchCode'] != '')
        {
            $searchField = $_POST["txtSearchCode"];
            $rawData = $model->selectMemberDetailsBySearchField($searchField);
        }
        
        /*if (isset($_POST['btnSearch']) && $_POST['member_id'] != '')
        {
            $searchField = $_POST["member_id"];
            $rawData = $model->selectMemberDetailsBySearchField($searchField);
        }
        else if (isset($_POST['btnSearchCode']) && $_POST['txtSearchCode'] != '')
        {
            $activation_code = $_POST['txtSearchCode'];
            $rawData = $model->selectMemberDetailsByActivationCode($activation_code);
        }
        else
        {
            $rawData = $model->selectAllMemberDetails(); // In future, this will no longer be used if the data is too many.
        }*/
        
        // get upline and endorser
        foreach ($rawData as $key => $value) {
            $uplineInfo = $members->selectMemberName($value["upline_id"]);
            $endorserInfo = $members->selectMemberName($value["endorser_id"]);
            
            if (is_array($endorserInfo) && count($endorserInfo) > 0) {
                $endorser = $endorserInfo["last_name"] . ", " . $endorserInfo["first_name"] . " " . $endorserInfo["middle_name"];
            } else {
                $endorser = '';
            }
            $rawData[$key]["endorser"] = $endorser;

            if (is_array($uplineInfo) && count($uplineInfo) > 0) {
                $upline = $uplineInfo["last_name"] . ", " . $uplineInfo["first_name"] . " " . $uplineInfo["middle_name"];
            } else {
                $upline = '';
            }
            $rawData[$key]["upline"] = $upline;
        }
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 25,
                    ),
        ));
        
        $this->render('index', array('model'=>$model,'dataProvider'=>$dataProvider));
    }
    
    public function actionUpdate()
    {
        $model = new MemberDetailsModel();
        $membersModel = new MembersModel();
        
        if (!isset($_GET["id"])) {
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }
        
        $id = $_GET["id"];
        $rawData = $model->selectMemberById($id);
        $activationCode = $membersModel->getActivationCode($id);
        $member = $membersModel->selectMemberDetails($id);
        $model->date_joined = $member['date_joined'];
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
                
        $this->render('_update', array('model'=>$model, 'activationCode'=>$activationCode));
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
    
    public function actionSearch()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
        {
            $model = new MemberDetailsModel();

            $result = $model->autoCompleteSearch($_GET['term']);

            if(count($result)>0)
            {
                foreach($result as $row)
                {
                    $arr[] = array(
                        'id'=>$row['member_id'],
                        'value'=>$row['member_name'],
                        'label'=>$row['member_name'],
                    );
                }

                echo CJSON::encode($arr);
                Yii::app()->end();
            }
            
        }
    }
    
    public function actionGenealogy()
    {
        if (isset($_POST["hidden_member_id"])) {
            $member_id = $_POST["hidden_member_id"];
        }
        else 
        {
            $member_id = $_GET["id"];
        }
        
        $member_name = Networks::getMemberName($member_id);
        
        $rawData = Networks::getDownlines($member_id);
        $final = Networks::arrangeLevel($rawData);
        $count = $final['total'];       
        
        $dataProvider = new CArrayDataProvider($final['network'], array(
                        'keyField' => false,
                        'pagination' => array(
                            'pageSize' => 1000,
                        ),

        ));
        
        $this->render('_genealogy', array('dataProvider'=>$dataProvider, 'member_name'=>$member_name, 'counter'=>$count));
    }
    
    public function actionGenealogyDownlines()
    {
        if (isset($_POST["postData"])) 
        {
            $member_ids = $_POST["postData"];
            Yii::app()->session['ids'] = $member_ids;
        }
        else if (Yii::app()->request->isAjaxRequest) {
            $member_ids = Yii::app()->session['ids'];
        }
        
        $array = Networks::getGenealogyDownlines($member_ids);

        $dataProvider = new CArrayDataProvider($array, array(
                        'keyField' => false,
                        'pagination' => array(
                            'pageSize' => 25,
                        ),
        ));

        $this->renderPartial('_downlines', array('dataProvider'=>$dataProvider));
    }
    
    public function actionPendingPlacements()
    {
        $model = new PlacementModel();
        
        $rawData = $model->selectAllPendingPlacements();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 25,
                    ),
        ));
        
        $this->render('_pendingplacements', array('dataProvider'=>$dataProvider));
    }
}
?>
