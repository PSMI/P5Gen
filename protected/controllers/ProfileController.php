<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ProfileController extends Controller
{
    public $msg = '';
    public $title = '';
    public $showDialog = false;
    public $showConfirm = false;
    public $showRedirect = false;
    public $reOpenDialog = false;
    
    public $layout = "column2";
    
    public function actionIndex()
    {
        $model = new NetworksModel();
        $members = new MembersModel();
        
        $member_id = Yii::app()->user->getId();
        
        $rawData = $model->getProfileInfo($member_id);
        
        $uplineInfo = $members->selectMemberDetailsStatus($rawData["upline_id"]);
        $endorserInfo = $members->selectMemberDetailsStatus($rawData["endorser_id"]);
        
        if (isset($_POST["btnChange"]))
        {
            $db_pass = $rawData["password"];
            $curr_pass = $_POST["txtCurrentPass"];
            $new_pass = $_POST["txtNewPass"];
            $confirm_pass = $_POST["txtConfirmPass"];

            if ($curr_pass != "" && $new_pass != "" && $confirm_pass != "")
            {
                if ($new_pass == $confirm_pass)
                {
                    if ($db_pass == md5($curr_pass))
                    {
                        $retval = $members->changePassword($member_id, $new_pass);
                        
                        if ($retval)
                        {
                            $this->title = "SUCCESSFUL";
                            $this->msg = "Member's password successfully modified.";
                            $this->showRedirect = true;
                        }
                        else
                        {
                            $this->title = "NOTIFICATION";
                            $this->msg = "Change password failed.";
                            $this->showDialog = true;
                        }
                    }
                    else
                    {
                        $this->title = "NOTIFICATION";
                        $this->msg = "Invalid current password. Please try again.";
                        $this->reOpenDialog = true;
                    }
                }
                else
                {
                    $this->title = "NOTIFICATION";
                    $this->msg = "Your new password and confim password did not match.";
                    $this->reOpenDialog = true;
                }
            }
            else
            {
                $this->title = "NOTIFICATION";
                $this->msg = "Please fill-up the required fields.";
                $this->reOpenDialog = true;
            }
        }
        
        $this->render('index', array('model'=>$model, 'data'=>$rawData, 'upline'=>$uplineInfo, 'endorser'=>$endorserInfo));
    }
}
?>
