<?php

/**
 * @author Noel Antonio
 * @date 03-26-2014
 */

class ProductsForm extends CFormModel
{
    public $_connection;
    public $product_id;
    public $product_name;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function selectAll()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM products WHERE status = 1";
        $command = $conn->createCommand($query);
        $result = $command->queryAll();
        
        return $result;
    }
    
    public function selectProducts($filter)
    {
        $conn = $this->_connection;        
        $filter = "%".$filter."%";
        
        $query = "SELECT product_id, product_name, product_code 
                  FROM products 
                  WHERE (product_name like :filter
                    OR product_code like :filter)
                    AND status = 1";
        $command = $conn->createCommand($query);
        $command->bindParam(':filter', $filter);
        $result = $command->queryAll();        
        return $result;
    }
    
    public function selectProductById($product_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM products WHERE product_id = :product_id AND status = 1";
        $command = $conn->createCommand($query);
        $command->bindParam(':product_id', $product_id);
        $result = $command->queryRow();
        
        return $result;
    }
    
    public function listProducts()
    {
        $model = new ProductsForm();
        return CHtml::listData($model->selectAll(), 'product_id', 'product_name');
    }
    
    public function selectProductByPackageType($product_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM products a 
                WHERE a.product_id = :product_id 
                    AND a.is_package = 1
                    AND a.status = 1";
        $command = $conn->createCommand($query);
        $command->bindParam(':product_id', $product_id);
        $result = $command->queryAll();
        
        return $result;
    }
}
?>
