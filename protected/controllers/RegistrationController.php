<?php

/*
 * @author : owliber
 * @date : 2014-02-01
 */

class RegistrationController extends Controller
{
    public $layout = 'column2';
    
    public $dialogTitle;
    public $dialogMessage;
    public $showDialog = false;
    public $showConfirm = false;
    public $alertType = 'info';
    public $errorCode;
    
    /* ------------------------------------------ IBO REGISTRATION ------------------------------------------ */
    public function actionIndex()
    {
        $model = new RegistrationForm();
        $model->member_id = Yii::app()->session['member_id'];
        
        if (isset($_POST['RegistrationForm']) && $_POST['hidden_flag'] != 1)
        {
            $model->attributes = $_POST['RegistrationForm'];
            Yii::app()->session['birth_date'] = $model->birth_date;
            
            // force required fields.

            $model->product_name = 'Default: P5 Water Purifier';

            if($model->validate())
            {
                $activation_code = $model->activation_code;
                
                $activation = new ActivationCodeModel();
                //Validate activation code
                $result = $activation->validateActivationCode($activation_code, 1);
                
                if(count($result) > 0)
                {
                    $retname = $model->validateMemberName();
                    
                    if (is_array($retname)) 
                    {
                        $this->dialogMessage = '<strong>Ooops!</strong> Member name already exist. Please use another name or append some characters you preferred to make it unique.';
                        $this->errorCode = 6;
                        $this->showDialog = true;
                    }
                    else 
                    {
                        $exist_member_code = $activation->checkUsedCodeByMembers($activation_code);
                        if ($exist_member_code > 0)
                        {
                            $this->dialogMessage = '<strong>Ooops!</strong> The activation code you have entered has already been used by another member. Please use another activation code.';
                            $this->errorCode = 6;
                            $this->showDialog = true;
                        }
                        else
                        {
                            $this->showConfirm = true;
                        }
                    }
                    
                }
                else
                {
                    $this->dialogMessage = '<strong>Ooops!</strong> The activation code entered is invalid. Please make sure you have entered the code correctly or the code given to you is valid.';
                    $this->errorCode = 6; //Activation code already in used.
                    $this->showDialog = true;
                }
                $this->dialogTitle = 'IBP Registration';
            }
        }
        else if ($_POST['hidden_flag'] == 1)
        {
            $model->attributes = $_POST['RegistrationForm'];
            $model->birth_date = Yii::app()->session['birth_date'];
            
            unset(Yii::app()->session['birth_date']);
            
            // process registration
            $retval = $model->register();                    
            if($retval['result_code'] == 0)
            {
                $model->triggerRunningAccountAfterInsert();
                // send email notification
                $param['member_id'] = $model->new_member_id;
                $param['plain_password'] = $model->plain_password;
                Mailer::sendVerificationLink($param);
                $param2['upline_id'] = $model->upline_id;
                $param2['new_member_id'] = $model->new_member_id;
                $param2['endorser_id'] = $model->member_id;                      
                Mailer::sendUplineNotification($param2);
                $this->dialogMessage = '<strong>Well done!</strong> You have successfully registered our new business partner.';
            }
            else
            {
                $this->dialogMessage = '<strong>Ooops!</strong> A problem encountered during the registration. Please contact P5 support.';
            }
            $this->errorCode = $retval['result_code'];
            $this->showDialog = true;
        }
        
        $this->render('index',array('model'=>$model));
    }
    
    public function actionDownlines()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
        {
            $model = new RegistrationForm();

            $result = $model->selectDownlines($_GET['term']);

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
    
    public function actionConfirm()
    {
        $info = array();
        
        if (isset($_POST)) {
            $info[0]["member_name"] = strtoupper($_POST["last_name"] . ", " . $_POST["first_name"] . " " . $_POST["middle_name"]);
            $info[0]["upline_name"] = Networks::getMemberName($_POST["upline_id"]);
            $info[0]["endorser_name"] = Networks::getMemberName(Yii::app()->user->getId());
        }
        
        $dataProvider = new CArrayDataProvider($info, array(
                        'keyField' => false,
                        'pagination' => false
        ));
        
        $this->renderPartial('_position', array('dataProvider'=>$dataProvider));
    }
    
    /* ------------------------------------------ IPD REGISTRATION ------------------------------------------ */
    public function actionIpdIndex()
    {
        $model = new RegistrationForm();
        $model->member_id = Yii::app()->session['member_id'];
        if (isset($_POST['RegistrationForm']) && $_POST['hidden_flag'] != 1)
        {
            $model->attributes = $_POST['RegistrationForm'];
            
            // force required fields
            $model->product_name = 'Default: P5 Water Purifier';
            
            if ($model->validate())
            {
                $activation_code = $model->activation_code;
                
                $activation = new ActivationCodeModel();
                $result = $activation->validateActivationCode($activation_code, 2);
                if(count($result) > 0)
                {
                    $retname = $model->validateMemberName();
                    if (is_array($retname)) 
                    {
                        $this->dialogMessage = '<strong>Ooops!</strong> Member name already exist. Please use another name or append some characters you preferred to make it unique.';
                        $this->errorCode = 6;
                        $this->showDialog = true;
                    }
                    else 
                    {
                        $exist_member_code = $activation->checkUsedCodeByMembers($activation_code);
                        if ($exist_member_code > 0)
                        {
                            $this->dialogMessage = '<strong>Ooops!</strong> The activation code you have entered has already been used by another member. Please use another activation code.';
                            $this->errorCode = 6;
                            $this->showDialog = true;
                        }
                        else
                        {
                            $this->showConfirm = true;
                        }
                    }
                }
                else
                {
                    $this->dialogMessage = '<strong>Ooops!</strong> The activation code entered is invalid. Please make sure you have entered the code correctly or the code given to you is valid.';
                    $this->errorCode = 6;
                    $this->showDialog = true;
                }
                $this->dialogTitle = 'IBP Registration';
            }
        }
        else if ($_POST['hidden_flag'] == 1)
        {
            $model->attributes = $_POST['RegistrationForm'];
            $retval = $model->registerIPD();                    
            if($retval['result_code'] == 0)
            {
                $param['distributor_id'] = $model->new_member_id;
                $param['plain_password'] = $model->plain_password;
                Mailer::sendIPDVerificationLink($param);
                $param2['new_member_id'] = $model->new_member_id;
                $param2['endorser_id'] = $model->member_id;
                Mailer::sendIPDEndorserNotification($param2);
                $this->dialogMessage = '<strong>Well done!</strong> You have successfully registered our new business distributor.';
            }
            else
            {
                $this->dialogMessage = '<strong>Ooops!</strong> A problem encountered during the registration. Please contact P5 support.';
            }
            $this->errorCode = $retval['result_code'];
            $this->showDialog = true;
        }
        $this->render('_ipdindex',array('model'=>$model));
    }
    
    
    public function actionIpdConfirm()
    {
        $info = array();
        if (isset($_POST)) {
            $info[0]["member_name"] = strtoupper($_POST["last_name"] . ", " . $_POST["first_name"] . " " . $_POST["middle_name"]);
            $info[0]["endorser_name"] = Networks::getMemberName(Yii::app()->user->getId());
        }
        $dataProvider = new CArrayDataProvider($info, array(
                        'keyField' => false,
                        'pagination' => false
        ));
        $this->renderPartial('_ipdposition', array('dataProvider'=>$dataProvider));
    }
    
    
    public function actionPlaceUnderIPD()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
        {
            $model = new RegistrationForm();

            $result = $model->selectIPDDownlines($_GET['term']);

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
    
    
    /* ------------------------------------------ IPD TO IBO REGISTRATION (ADMIN) ------------------------------------------ */
    public function actionNew()
    {
        $model = new DistributorForm();
        $model_attr = $_POST['DistributorForm'];
        $hidden_flag = $model_attr['hidden_flag'];
        if ($hidden_flag == 1)
        {
            $member_id = $model_attr['distributor_id'];
            $activation_code = $model_attr['activation_code'];
            $upline_id = $model_attr['upline_id'];
            $ibo_endorser_id = $model_attr['ibo_endorser_id'];
            
            $activation = new ActivationCodeModel();
            $result = $activation->validateActivationCode($activation_code, 1);
            
            if ($result > 0)
            {
                $exist_member_code = $activation->checkUsedCodeByMembers($activation_code);
                if ($exist_member_code > 0)
                {
                    $this->dialogTitle = 'ERROR!';
                    $this->dialogMessage = '<strong>Ooops!</strong> The activation code you have entered has already been used by another member. Please use another activation code.';
                }
                else
                {
                    $registration = new RegistrationForm();
                    $retval = $registration->registerIPDtoIBO($member_id, $upline_id, $ibo_endorser_id, $activation_code);
                    if ($retval)
                    {
                        $param['member_id'] = $member_id;
                        Mailer::sendIPDtoIBONotification($param);

                        $param2['upline_id'] = $upline_id;
                        $param2['new_member_id'] = $member_id;
                        $param2['endorser_id'] = $ibo_endorser_id;                      
                        Mailer::sendUplineNotification($param2);

                        $this->dialogTitle = 'SUCCESSFUL!';
                        $this->dialogMessage = '<strong>Well done!</strong> You have successfully registered our distributor as a new member.';
                    }
                    else
                    {
                        $this->dialogTitle = 'ERROR!';
                        $this->dialogMessage = '<strong>Ooops!</strong> A problem encountered during the registration. Please contact P5 support.';
                    }
                }
            }
            else
            {
                $this->dialogTitle = 'ERROR!';
                $this->dialogMessage = '<strong>Ooops!</strong> The activation code entered is invalid. Please make sure you have entered the code correctly or the code given to you is valid.';
            }
            
            $this->showDialog = true;
        }
        $this->render('_newibo', array('model'=>$model));
    }
    /**
     * This function is used to view the profile of the 
     * particular distributor selected.
     */
    public function actionViewProfile()
    {
        $networksModel = new NetworksModel();
        $member_id = $_POST["member_id"];
        $rawData = $networksModel->getProfileInfo($member_id);
        $fullname = $rawData["last_name"] . ", " . $rawData["first_name"] . " " . $rawData["middle_name"];
        $endorser = Networks::getMemberName($rawData["ipd_endorser_id"]);
        $immediate_ibo_id = Networks::getImmediateIBOEndorser($rawData["ipd_endorser_id"]);
        $immediate_ibo_endorser = Networks::getMemberName($immediate_ibo_id);
        $content .= '<style type="text/css">
                        table#summary{font-size:14px; width:100%;}
                        table#summary, table#summary th, table#summary td{border:1px solid #e1e1e1; border-collapse: collapse; padding: 2px 10px 2px 10px}
                        table#summary td.data{color:#0088cc}
                    </style>';
        $content .= '<table with="100%" id="summary">
                        <tr>
                            <td width="20%" align="right">Username</td>
                            <td width="75%" class="data">'.$rawData["username"].'</td>
                        </tr>
                        <tr>
                            <td width="20%" align="right">Full Name</td>
                            <td width="75%" class="data">'.$fullname.'</td>
                        </tr>
                        <tr>
                            <td width="20%" align="right">Date Joined</td>
                            <td width="75%" class="data">'.date("F d, Y ", strtotime($rawData["date_joined"])).'</td>
                        </tr>
                        <tr>
                            <td width="20%" align="right">Endorsed By</td>
                            <td width="75%" class="data">'.$endorser.'</td>
                        </tr>
                        <tr>
                            <td width="20%" align="right">Immediate IBO Endorser</td>
                            <td width="75%" class="data">'.$immediate_ibo_endorser.'</td>
                        </tr>
                        <tr>
                            <td width="20%" align="right">Address</td>
                            <td width="75%" class="data">'.$rawData["address1"].'</td>
                        </tr>
                        <tr>
                            <td width="20%" align="right">Birth Date</td>
                            <td width="75%" class="data">'.date("F d, Y ", strtotime($rawData["birth_date"])).'</td>
                        </tr>
                        <tr>
                            <td width="20%" align="right">Contact Number</td>
                            <td width="75%" class="data">'.$rawData["mobile_no"].'</td>
                        </tr>
                        <tr>
                            <td width="20%" align="right">Email</td>
                            <td width="75%" class="data">'.$rawData["email"].'</td>
                        </tr>
                        <tr>
                            <td width="20%" align="right">Beneficiary Name</td>
                            <td width="75%" class="data">'.$rawData["beneficiary_name"].'</td>
                        </tr>
                    </table>
                    <input type="hidden" id="DistributorForm_ibo_endorser_id" name="DistributorForm[ibo_endorser_id]" value="'.$immediate_ibo_id.'" />';
        echo $content;
    }
    /**
     * This function is used for autocomplete search of 
     * all qualified distributors
     */
    public function actionDistributor()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
        {
            $model = new RegistrationForm();
            $result = $model->selectDistributors($_GET['term']);
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
    /**
     * This function is used to retrieve the immediate IBO of 
     * the particular IPD distributor selected.
     */
    public function actionDownlinesOfImmediateIBO()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
        {
            $model = new RegistrationForm();
            $result = $model->selectDownlinesOfImmediateIBO($_GET['term'], $_GET['ibo']);
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
    /**
     * This function is used for registration of IPD to IBO
     */
    public function actionAjaxRegister()
    {
        if (isset($_POST['DistributorForm']))
        {
            $model_attr = $_POST['DistributorForm'];
            $activation_code = $model_attr['activation_code'];
            $activation = new ActivationCodeModel();
            $result = $activation->validateActivationCode($activation_code, 1);
            if(count($result) > 0)
            {
                $retval = array('code'=>0, 'message'=>'');
            }
            else
            {
                $retval = array('code'=>1, 'message'=>'<strong>Ooops!</strong> The activation code entered is invalid. Please make sure you have entered the code correctly or the code given to you is valid.');
            }
            echo json_encode($retval);
        }
    }
    /**
     * This function is used by admin registration only for confirmation 
     * of possible network before registration.
     */
    public function actionConfirm2()
    {
        $info = array();
        if (isset($_POST)) {
            $info[0]["member_name"] = strtoupper($_POST["distributor_name"]);
            $info[0]["upline_name"] = Networks::getMemberName($_POST["upline_id"]);
            $info[0]["endorser_name"] = Networks::getMemberName($_POST["ibo_endorser_id"]);
        }
        $dataProvider = new CArrayDataProvider($info, array(
                        'keyField' => false,
                        'pagination' => false
        ));
        $this->renderPartial('_position', array('dataProvider'=>$dataProvider));
    }
}
?>
