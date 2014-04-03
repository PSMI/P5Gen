<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */

class Inventory extends CFormModel
{
    public $_connection;
    public $search;
    public $product_id;
    public $product_code;
    public $product_name;
    public $amount = '0.00';
    public $ibo_discount = '0.00';
    public $ipd_discount = '0.00';
    public $status;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('search','safe'),
            array('product_code, product_name, amount','required'),
            array('ibo_discount, ipd_discount','safe'),
        );
    }
    
    public function attributeLabels() {
        return array(
            'search'=>'Search',
            'product_code'=>'Product Code',
            'product_name'=>'Product Name',
            'amount'=>'Amount',
            'ibo_discount'=>'IBO Discount (%)',
            'ipd_discount'=>'IPD Discount (%)',
        );
    }
    
    public function get_products()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    product_id,
                    product_code,
                    product_name,
                    FORMAT(amount,2) AS amount,
                    ibo_discount,
                    ipd_discount,
                    CASE `status`
                      WHEN 1 THEN 'Active'
                      WHEN 2 THEN 'Inactive'
                    END `status`
                  FROM products;";
        $command = $conn->createCommand($query);
        $result = $command->queryAll();
        return $result;
    }
    
    public function filter_products()
    {
        $conn = $this->_connection;
        
        $query = "SELECT
                    product_id,
                    product_code,
                    product_name,
                    FORMAT(amount,2) AS amount,
                    ibo_discount,
                    ipd_discount,
                    CASE `status`
                      WHEN 1 THEN 'Active'
                      WHEN 2 THEN 'Inactive'
                    END `status`
                  FROM products                    
                    WHERE product_code LIKE concat('%',:filter,'%')
                    OR product_name LIKE concat('%',:filter,'%')";
        $command = $conn->createCommand($query);
        $command->bindParam(':filter', $this->search);
        $result = $command->queryAll();
        return $result;
    }
    
    public function add_product()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $query = "INSERT INTO products (product_code, product_name, amount, ibo_discount, ipd_discount)
                    VALUES (:product_code, :product_name, :amount, :ibo_discount, :ipd_discount)";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':product_code', $this->product_code);
        $command->bindParam(':product_name', $this->product_name);
        $command->bindParam(':amount', $this->amount);
        $command->bindParam(':ibo_discount', $this->ibo_discount);
        $command->bindParam(':ipd_discount', $this->ipd_discount);
        $command->execute();
        
        try
        {
            $trx->commit();
        }
        catch(PDOException $e)
        {
            $trx->rollback();
        }
        
    }
    
    public function get_product_by_id()
    {
        $conn = $this->_connection;
        
        $query = "SELECT *
                  FROM products
                    WHERE product_id = :product_id;";
        $command = $conn->createCommand($query);
        $command->bindParam(':product_id', $this->product_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function update_product()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE products SET 
                    product_code = :product_code,
                    product_name = :product_name,
                    amount = :amount,
                    ibo_discount = :ibo_discount,
                    ipd_discount = :ipd_discount,
                    status = :status
                  WHERE product_id = :product_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':product_id', $this->product_id);
        $command->bindParam(':product_code', $this->product_code);
        $command->bindParam(':product_name', $this->product_name);
        $command->bindParam(':amount', $this->amount);
        $command->bindParam(':ibo_discount', $this->ibo_discount);
        $command->bindParam(':ipd_discount', $this->ipd_discount);
        $command->bindParam(':status', $this->status);
        $command->execute();
        
        try
        {
            $trx->commit();
        }
        catch(PDOException $e)
        {
            $trx->rollback();
        }
    }
}
?>
