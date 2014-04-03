<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */

class PurchaseController extends Controller
{
    public $layout = 'column2';
    
    public function actionIndex()
    {
        
        $model = new PurchasesModel();
        $model2 = new DistributorForm();
        unset(Yii::app()->session['distributor_id']);
        $totals = array();
        
        if(isset($_POST['PurchasesModel']))
        {            
            $model->attributes = $_POST['PurchasesModel'];
            
            $distributor_id = $_POST['distributor_id'];
            Yii::app()->session['distributor_id'] = $distributor_id;
            
            $distributor = $model2->selectDistributorName($distributor_id);
        
            $purchases = $model->selectAll();
            $totals = $model->getItemTotal();
        }
        
        $dataProvider = new CArrayDataProvider($purchases, array(
                        'keyField' => false,
                        'pagination' => array(
                            'pageSize' => 25,
                        ),

        ));
        
        $this->render('index',array('model'=>$model,'dataProvider'=>$dataProvider,'distributor'=>$distributor,'totals'=>$totals));
    }
    
    public function actionAddItem()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PurchasesModel();
            
            $model->product_id = $_GET['product_id'];
            $model->quantity = $_GET['quantity'];
            $model->distributor_id = $_GET['distributor_id'];
            $model->payment_type_id = $_GET['payment_type_id'];
                              
            $retval = $model->is_item_exist();
            if($retval === false)
            {
                ($model->is_repeat_purchase() === true) ? $model->is_repeat = true : $model->is_repeat = false;
                $model->add_purchased_item();
            }
            else
            {
                $model->purchase_id = $retval[0]['purchase_id'];
                $model->append_purchased_item();
            }
            
            if(!$model->hasErrors())
            {
                echo CJSON::encode(array('result_code'=>0,'result_msg'=>'Add item successful'));
            }
            else
                echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Add item failed'));
        }
    }
    
    public function actionUpdateItem()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PurchasesModel();
            
            $model->purchase_id = $_GET['purchase_id'];
            $model->product_id = $_GET['product_id'];
            $model->quantity = $_GET['quantity'];
            $model->distributor_id = $_GET['distributor_id'];
            $model->payment_type_id = $_GET['payment_type_id'];
            
            $model->update_purchased_item();
            
            if(!$model->hasErrors())
            {
                echo CJSON::encode(array('result_code'=>0,'result_msg'=>'Update item successful'));
            }
            else
                echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Update item failed'));
        }
    }
    
    
    public function actionCheckout()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PurchasesModel();
            
            $model->distributor_id = $_GET['distributor_id'];
            
            $model->checkout_items();
            
            if(!$model->hasErrors())
            {
                echo CJSON::encode(array('result_code'=>0,'result_msg'=>'Purchase is successful'));
            }
            else
                echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Purchase failed'));
        }
    }
    
    public function actionRemoveItem()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PurchasesModel();
            
            $model->purchase_id = $_GET['id'];
            
            $model->remove_item();
            
            if(!$model->hasErrors())
            {
                echo CJSON::encode(array('result_code'=>0,'result_msg'=>'Add item successful'));
            }
            else
                echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Add item failed'));
        }
    }
    
    public function actionGetValues()
    {
        if(Yii::app()->request->isAjaxRequest)
        {   
            $model = new PurchasesModel();
            
            $model->purchase_id = $_GET['id'];
            $result = $model->get_purchase_by_id();
            
            $values[] = $result;
            echo CJSON::encode($values);
        }
    }
    
}
?>
