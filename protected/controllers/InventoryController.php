<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */

class InventoryController extends Controller
{
    public $layout = 'column2';    
    public $dialog_message;
    public $show_dialog = false;
    public $error_code;
    
    public function actionIndex()
    {
        $model = new Inventory();        
        
        if(isset($_POST['Inventory']))
        {
            $model->attributes = $_POST['Inventory'];
            
            if(empty($model->search))
                $products = $model->get_products();
            else
                $products = $model->filter_products();
        }
        else
        {
            if(isset($_POST['product_id']))
            {

                $model->product_id = $_POST['product_id'];
                $model->product_code = $_POST['product_code'];
                $model->product_name = $_POST['product_name'];
                $model->amount = $_POST['amount'];
                $model->ibo_discount = $_POST['ibo_discount'];
                $model->ipd_discount = $_POST['ipd_discount'];
                $model->status = $_POST['status'];

                $model->update_product();

                if(!$model->hasErrors())
                {
                    $this->show_dialog = true;
                    $this->dialog_message = 'Product update was successful.';
                }
                else
                {
                    $this->show_dialog = true;
                    $this->dialog_message = 'Product update has failed.';
                }
                
                $products = $model->get_products();

            }
            else
            {
                $products = $model->get_products();
            }
        }
        
        $dataProvider = new CArrayDataProvider($products, array(
                        'keyField' => false,
                        'pagination' => array(
                        'pageSize' => 25,
                    ),
        ));
        
        $this->render('index',array('dataProvider'=>$dataProvider,'model'=>$model));
    }
    
    public function actionAddProduct()
    {
        $model = new Inventory();
        
        if(isset($_POST['Inventory']))
        {
            
            $model->attributes = $_POST['Inventory'];
            
            if($model->validate())
            {
                $model->add_product();
                
                if(!$model->hasErrors())
                {
                    $this->dialog_message = 'New product was successfully added.';
                    $this->show_dialog = true;
                }
                else
                {
                    $this->dialog_message = 'An error encountered while adding new product.';
                    $this->show_dialog = true;
                }
                
            }
        }
        $this->render('_form',array('model'=>$model));
    }
       
    public function actionGetValues()
    {
        if(Yii::app()->request->isAjaxRequest)
        {   
            $model = new Inventory();
            
            $product_id = $_GET['id'];
            $model->product_id = $product_id;
            $result = $model->get_product_by_id();
            
            $values[] = $result;
            echo CJSON::encode($values);
        }
    }
      
}
?>
