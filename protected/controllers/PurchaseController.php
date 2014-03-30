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
        
        if(isset($_POST['PurchasesModel']))
        {            
            $model->attributes = $_POST['PurchasesModel'];
            
            $distributor_id = $_POST['distributor_id'];
            Yii::app()->session['distributor_id'] = $distributor_id;
            
            $distributor = $model2->selectDistributorName($distributor_id);
        
            $purchases = $model->selectAll();
        }
        $dataProvider = new CArrayDataProvider($purchases, array(
                        'keyField' => false,
                        'pagination' => array(
                            'pageSize' => 25,
                        ),

        ));
        $this->render('index',array('model'=>$model,'dataProvider'=>$dataProvider,'distributor'=>$distributor));
//        }
//        else
//        {
//            $this->render('index',array('model'=>$model));
//        }
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
            
            $model->add_purchased_item();
            
            if(!$model->hasErrors())
            {
                echo CJSON::encode(array('result_code'=>0,'result_msg'=>'Purchase successful'));
            }
            else
                echo CJSON::encode(array('result_code'=>1,'result_msg'=>'Purchase failed'));
        }
    }
}
?>
