<?php

/*
 * @author : owliber
 * @date : 2014-03-10
 */

class AdminController extends Controller
{
    public $showDialog = false;
    public $dialogTitle;
    public $dialogMessage;
    public $errorCode;
    public $layout = "column2";
    
    public function actionScheduler()
    {
        $model = new TransactionQueue();
        $reference = new ReferenceModel();
        
        if(isset($_POST['status']))
        {
            $status = $_POST['status'];
            ($status == 1) ? $new_status = 2 : $new_status = 1;
            $reference->toggle_job_scheduler($new_status);
        }
        
        $rawData = $model->get_pending_transactions();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 25,
                    ),
        ));
        
        $job_status = $reference->get_variable_value('JOB_SCHEDULER');
        
        $this->render('scheduler',array('dataProvider'=>$dataProvider,'job_status'=>$job_status));
    }
    
    public function actionOptions()
    {
        $reference = new ReferenceModel();
        
        $schedule_options = $reference->get_schedule_variables();
        $rate_options = $reference->get_rates_variables();
        $payout_rates = $reference->get_payout_rates();
        
        $dataProvider = new CArrayDataProvider($schedule_options, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $dataProvider2 = new CArrayDataProvider($rate_options, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $dataProvider3 = new CArrayDataProvider($payout_rates, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('options',array(
            'dataProvider'=>$dataProvider, 
            'dataProvider2'=>$dataProvider2,
            'dataProvider3'=>$dataProvider3,
        ));
    }
    
    public function actionGetVariableOptions()
    {
        if(Yii::app()->request->isAjaxRequest)
        {   
            $reference = new ReferenceModel();
            
            $variable_id = $_GET['id'];
            $result = $reference->get_variables_by_id($variable_id);
            
            $options[] = array('id'=>$variable_id,'value'=>$result['variable_value'],'text'=>$result['variable_text']);
            echo CJSON::encode($options);
        }
    }
}
?>
