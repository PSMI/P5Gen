<?php

/**
 * @author Noel Antonio
 * @date 01-27-2014
 */

class CodesController extends Controller 
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
        
        $model = new ActivationCodeModel();
        
        $rawData = $model->selectAll();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('index', array('model'=>$model, 'dataProvider'=>$dataProvider));
    }
            
    public function actionCreate()
    {
        $model = new ActivationCodeModel();
        
        if (isset($_POST["ActivationCodeModel"]))
        { 
            $model->attributes = $_POST["ActivationCodeModel"];
            $quantity = $model->quantity;

            if ($model->validate())
            {
                if ($quantity == 0) {
                    $this->title = "NOTIFICATION";
                    $this->msg = "Zero value not accepted. Please try again.";
                    $this->showDialog = true;
                }
                else if ($quantity > 1000)
                {
                    $this->title = "NOTIFICATION";
                    $this->msg = "Maximum of 1000 codes are allowed to be generated.";
                    $this->showDialog = true;
                }
                else {
                    $this->title = "CONFIRMATION";
                    $this->msg = "Are you sure you want to generate " . $quantity . " activation code(s)?";
                    $this->showConfirm = true;
                }
            }
        }
        else if (isset($_POST["hiddenQty"]))
        {
            $quantity = $_POST["hiddenQty"];
            $aid = 1; // Yii::app()->session['AID'];
            $ipaddr = $_SERVER['REMOTE_ADDR'];

            $retval = $model->generateActivationCodes($quantity, $aid, $ipaddr);

            if ($retval) {
                $this->title = "SUCCESSFUL";
                $this->msg = "Activation code successfully generated!";
            }
            else {
                $this->title = "NOTIFICATION";
                $this->msg = $retval;
            }
            
            $this->showDialog = true;
        }

        $this->render('_create', array('model'=>$model));
    }
    
    public function actionCodes()
    {
        $model = new ActivationCodeModel();
        
        $batchId = $_GET['id'];
        
        $rawData = $model->selectAllCodesByBatchId($batchId);
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('_codes', array('model'=>$model, 'dataProvider'=>$dataProvider));
    }
}
?>