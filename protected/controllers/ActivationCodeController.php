<?php

/**
 * @author Noel Antonio
 * @date 01-27-2014
 */

class ActivationCodeController extends Controller 
{
    public $msg = '';
    public $title = '';
    public $showDialog = false;
    public $showConfirm = false;
            
    public function actionIndex()
    {
        $model = new ActivationCodeModel();
        
        if (isset($_POST["ActivationCodeModel"]))
        {
            $model->attributes = $_POST["ActivationCodeModel"];
            
            if ($model->validate())
            {
                $quantity = $model->quantity;
                
                if ($quantity == 0) {
                    $this->title = "NOTIFICATION";
                    $this->msg = "Zero value not accepted. Please try again.";
                    $this->showDialog = true;
                }
                else {
                    $this->title = "CONFIRMATION";
                    $this->msg = "Are you sure you want to generate " . $quantity . " activation code(s)?";
                    $this->showConfirm = true;
                }
            }
            else
            {
                $this->title = "NOTIFICATION";
                $this->msg = "Quantity field is empty! Please enter a valid quantity.";
                $this->showDialog = true;
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

        $this->render('index', array('model'=>$model));
    }
    
    
    public function actionHistory()
    {
        $model = new ActivationCodeModel();
        
        $rawData = $model->selectAll();
        
        $dataProvider = new CArrayDataProvider($rawData, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 10,
                    ),
        ));
        
        $this->render('history', array('model'=>$model, 'dataProvider'=>$dataProvider));
    }
}
?>