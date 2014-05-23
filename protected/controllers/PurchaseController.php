<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */

class PurchaseController extends Controller
{
    //public $layout = 'column2';
//    public $input_disabled = '';
      
    public function actionIndex()
    {
        if(!Yii::app()->user->hasUserAccess() && !Yii::app()->user->isSuperAdmin()) 
                $this->redirect(array('site/404'));
        
        $model = new PurchasesModel();
        $model2 = new DistributorForm(); 
            
        if(isset($_POST['PurchasesModel']))
        {   
            /*if (isset(Yii::app()->session['purchaser_id']))
            {
                unset(Yii::app()->session['purchaser_id']);
                unset(Yii::app()->session['purchase_summary_id']);
            }*/
            
            $model->attributes = $_POST['PurchasesModel'];
            
            $member_id = $_POST['purchaser_id'];
            $model->member_id = $member_id;
            Yii::app()->session['purchaser_id'] = $model->member_id;
            
            $purchase_summary_id = $_POST['purchase_summary_id'];
            if(!empty($purchase_summary_id))
                $model->purchase_summary_id = $purchase_summary_id;
            
        }
        else
        {
            $model->member_id = Yii::app()->session['purchaser_id'];
            $model->purchase_summary_id = Yii::app()->session['purchase_summary_id'];
        }
        
        $member = $model2->selectDistributorName($member_id);
        $purchases = $model->selectAll();
        
        if(empty($model->purchase_summary_id))
        {
            $model->purchase_summary_id = $purchases[0]['purchase_summary_id'];
            Yii::app()->session['purchase_summary_id'] = $purchases[0]['purchase_summary_id'];

        }
        
        $totals = $model->getItemTotal();
            
        $dataProvider = new CArrayDataProvider($purchases, array(
                        'keyField' => false,
                        'pagination' => array(
                            'pageSize' => 25,
                        ),

        ));
        
        $this->render('index',array('model'=>$model,'dataProvider'=>$dataProvider,'member'=>$member,'totals'=>$totals));
    }
    
    public function actionClearSession()
    {
        if(isset(Yii::app()->session['purchaser_id']))
            unset(Yii::app()->session['purchaser_id']);
        if(isset(Yii::app()->session['purchase_summary_id']))
            unset(Yii::app()->session['purchase_summary_id']);
        
        $this->redirect(Yii::app()->createUrl('purchase/index'));
    }
    
    public function actionAddToCart()
    {
       
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PurchasesModel();
            
            if(isset($_GET['product_id']) && !empty($_GET['product_id']))
            {
                $model->product_id = $_GET['product_id'];
                $model->member_id = $_GET['purchaser_id'];
                $model->payment_type_id = $_GET['payment_type_id'];
                
                if($_GET['quantity'] > 0)
                {
                    $model->quantity = $_GET['quantity'];
                }
                else
                {
                    echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Quantity should NOT be zero.'));
                    Yii::app()->end();
                }
                
                if(isset($_GET['purchase_summary_id']) && !empty($_GET['purchase_summary_id']))
                {
                    $model->purchase_summary_id = $_GET['purchase_summary_id'];
                    $retval = $model->is_item_exist();

                    if($retval === false)
                    {
                        $model->add_new_item();
                    }
                    else
                    {
                        $model->purchase_id = $retval[0]['purchase_id'];
                        $model->update_item();
                    }
                }
                else
                {
                    
                   $retval = $model->add_new_purchase();
                   if(!isset(Yii::app()->session['purchase_summary_id']))
                       Yii::app()->session['purchase_summary_id'] = $retval;
                    
                }
                  
                if(!$model->hasErrors())
                    echo CJSON::encode(array(
                        'result_code'=>0,
                        'result_msg'=>'Add item successful'
                    ));
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
            $model->member_id = $_GET['purchaser_id'];
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
            
            $model->member_id = $_GET['purchaser_id'];
            $model->purchase_summary_id = $_GET['purchase_summary_id'];            
            $model->payment_type_id = $_GET['payment_type_id'];
            
            if(empty($_GET['receipt_no']) || $_GET['receipt_no'] == "")
            {
                echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Please enter a valid receipt number.'));
                Yii::app()->end();
            }
            
            $model->receipt_no = $_GET['receipt_no'];
            
            if($model->receipt_is_used())
            {
                echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Receipt number is already in used. 
Please enter a valid receipt no.'));
                Yii::app()->end();
            }
            
            $model->checkout_items();
            
            if(!$model->hasErrors())
            {
                echo CJSON::encode(array('result_code'=>0,'result_msg'=>'Purchase is successful'));
                unset(Yii::app()->session['purchase_summary_id']);
                unset(Yii::app()->session['purchaser_id']);
                Yii::app()->end();
            }
            else
            {
                echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Purchase failed'));
                Yii::app()->end();
            }
        }
    }
    
    public function actionRemoveItem()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PurchasesModel();
            
            $model->purchase_id = $_GET['id'];
            $model->purchase_summary_id = $_GET['sid'];
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
            
            $model->member_id = $_GET['purchaser_id'];
            $model->purchase_summary_id = $_GET['purchase_summary_id'];
            
            $model->cancel_items();
            
            if(!$model->hasErrors())
            {
                unset(Yii::app()->session['purchase_summary_id']);
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
        
        if(!Yii::app()->user->hasUserAccess() && !Yii::app()->user->isSuperAdmin()) 
                $this->redirect(array('site/404'));
        
        $model = new PurchasesModel();
        
        if(isset($_GET['id']))
        {
            $distributor = new MemberDetailsModel();
            $model->member_id = $_GET['id'];
            
            $purchase_history = $model->selectByID();
            $view = '_ipdhistory';
            $info = $distributor->selectMemberById($_GET['id']);
            $total = $model->selectByIDTotal();
        }
        else
        {
            $this->layout = 'column2';
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
    
    public function actionCancel()
    {
        if(Yii::app()->request->isAjaxRequest)
        {   
            $values['purchase_summary_id'] = $_GET['id'];
            $values['member_id'] = $_GET['mid'];
            $values['receipt_no'] = $_GET['receipt_no'];
            $values['name'] = $_GET['name'];
            $values['date_purchased'] = $_GET['date_purchased'];
            echo CJSON::encode($values);
        }
    }
    
    public function actionCancelPurchase()
    {
        if(Yii::app()->request->isAjaxRequest)
        {   
            $model = new PurchasesModel();
            
            $model->purchase_summary_id = $_POST['purchase_summary_id'];
            $model->member_id = $_POST['purchaser_id'];
            $model->receipt_no = $_POST['receipt_no'];
            
            if(!empty($_POST['cancellation_reason']) || $_POST['cancellation_reason'] != "")
            {
               $model->cancel_reason = $_POST['cancellation_reason'];
                
               $model->cancel_purchase();
            
                if(!$model->hasErrors())
                {
                    $result_code = 0;
                    $result_msg = 'Receipt# '.$_POST['receipt_no'].' was successfully cancelled.';
                }
                else
                {
                    $result_code = 1;
                    $result_msg = 'Receipt# '.$_POST['receipt_no'].' cancellation failed.';
                } 
                
            }
            else
            {
               $result_code = 1;
               $result_msg = 'Please provide a reason for cancellation.';
            }
            
            echo CJSON::encode(array('result_code'=>$result_code,'result_msg'=>$result_msg));
        }
    }
    
}
?>
