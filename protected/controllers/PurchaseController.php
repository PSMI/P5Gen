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
        $totals = array();
        
        if(isset($_POST['PurchasesModel']))
        {      
            if(isset(Yii::app()->session['distributor_id']))
                unset(Yii::app()->session['distributor_id']);

            $model->attributes = $_POST['PurchasesModel'];
            
            $distributor_id = $_POST['distributor_id'];
            $model->distributor_id = $distributor_id;
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
    
    public function actionAddToCart()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PurchasesModel();
            
            if(isset($_GET['product_id']) && !empty($_GET['product_id']))
            {
                $model->product_id = $_GET['product_id'];
                
                if($_GET['quantity'] > 0)
                {
                    $model->quantity = $_GET['quantity'];
                }
                else
                {
                    echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Quantity should NOT be zero.'));
                    Yii::app()->end();
                }
                
                $model->distributor_id = $_GET['distributor_id'];
                $model->payment_type_id = $_GET['payment_type_id'];

                $retval = $model->is_item_exist();

                if($retval === false)
                {
                    $model->add_purchased_item();
                }
                else
                {
                    $model->purchase_id = $retval[0]['purchase_id'];
                    $model->append_purchased_item();
                }

                if(!$model->hasErrors())
                    echo CJSON::encode(array('result_code'=>0,'result_msg'=>'Add item successful'));
                else
                    echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Add item failed'));
            }
            else
            {
                echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Please type the product code or name.'));
            }
        }
    }
    
    public function actionUpdateCart()
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
    
    public function actionCancelCart()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PurchasesModel();
            
            $model->distributor_id = $_GET['distributor_id'];
            
            $model->cancel_items();
            
            if(!$model->hasErrors())
            {
                echo CJSON::encode(array('result_code'=>0,'result_msg'=>'Purchase was cancelled successfully.'));
            }
            else
                echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Purchase cancellation failed.'));
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
    
    public function actionHistory()
    {
        $model = new PurchasesModel();
        
        if(isset($_GET['id']))
        {
            $distributor = new MemberDetailsModel();
            $model->distributor_id = $_GET['id'];
            
            $purchase_history = $model->selectByID();
            $view = '_ipdhistory';
            $info = $distributor->selectMemberById($_GET['id']);
            $total = $model->selectByIDTotal();
        }
        else
        {
            $info = array();
            if(isset($_POST['PurchasesModel']))
            {
                if(isset(Yii::app()->session['history']))
                    unset(Yii::app()->session['history']);

                $model->attributes = $_POST['PurchasesModel'];

                Yii::app()->session['history'] = $model->attributes;
            }
            else
            {
                $model->date_from = date('Y-m-d');
                $model->date_to = $model->date_from;

                if(isset(Yii::app()->session['history']))
                    $model->attributes = Yii::app()->session['history'];
            }
            
            $view = '_history';
            $purchase_history = $model->selectByDate();
            $total = $model->selectByDateTotal();
        }
        
        $dataProvider = new CArrayDataProvider($purchase_history, array(
                        'keyField' => false,
                        'pagination' => array(
                            'pageSize' => 25,
                        ),

        ));

        $this->render($view,array(
            'model'=>$model,
            'dataProvider'=>$dataProvider,
            'info'=>$info,
            'total'=>$total,
        ));
    }
    
    public function actionProducts()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
        {
            $model = new ProductsForm();

            $result = $model->selectProducts($_GET['term']);

            if(count($result)>0)
            {
                foreach($result as $row)
                {
                    $arr[] = array(
                        'id'=>$row['product_id'],
                        'value'=>$row['product_name'],
                        'label'=>$row['product_name'],
                    );
                }

                echo CJSON::encode($arr);
                Yii::app()->end();
            }
            
        }
    }
    
}
?>
