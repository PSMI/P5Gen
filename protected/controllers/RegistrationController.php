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
    public $alertType = 'info';
    public $errorCode;
    
    public function actionIndex()
    {
        $model = new RegistrationForm();
        $model->member_id = Yii::app()->session['member_id'];
        
        if(isset($_POST['RegistrationForm']))
        {
            $model->attributes = $_POST['RegistrationForm'];
            
            if($model->validate())
            {
                $activation = new ActivationCodeModel();
                //Validate activation code
                $result = $activation->validateActivationCode($model->activation_code);
               
                if(count($result) > 0)
                {
                    //process registration
                    $retval = $model->register();                    
                    echo CJSON::encode($retval);
                    if($retval['result_code'] == 0)
                    {
                        //send email notification
                        $this->dialogMessage = '<strong>Well done!</strong> You have successfully registered our new business partner.';
                        
                    }
                    else
                    {
                        $this->dialogMessage = '<strong>Ooops!</strong> A problem encountered during the registration. Please contact P5 support.';
                        
                    }
                    
                    $this->errorCode = $retval['result_code'];
                    
                }
                else
                {
                    $this->dialogMessage = '<strong>Ooops!</strong> The activation code entered is invalid. Please make sure you have entered the code correctly.';
                    $this->errorCode = 6; //Activation code already in used.
                    
                }                
                
                $this->dialogTitle = 'IBP Registration';
                $this->showDialog = true;
                
            }
        }
        
        $this->render('index',array('model'=>$model));
    }
    
    public function actionDownlines()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
        {
            $model = new RegistrationForm();

            $result = $model->filterDownlines($_GET['term']);

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
}
?>
