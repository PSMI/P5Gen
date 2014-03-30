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
    public $discount_amount = '0.00';
    public $discount_percent = 0;
    public $discount_type;
    public $status;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('search','safe'),
            array('product_code, product_name, amount, discount_type','required'),
            array('discount_amount, discount_percent','safe'),
        );
    }
    
    public function attributeLabels() {
        return array(
            'search'=>'Search',
            'product_code'=>'Product Code',
            'product_name'=>'Product Name',
            'amount'=>'Amount',
            'discount_type'=>'Discount Type',
            'discount_amount'=>'Discount (Amt)',
            'discount_percent'=>'Discount (%)',
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
                    discount_amount,
                    concat(discount_percent,'%') AS discount_percent,
                    CASE discount_type 
                      WHEN 1 THEN 'By Percentage'
                      WHEN 2 THEN 'By fixed amount'
                    END discount_type,
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
                    discount_amount,
                    concat(discount_percent,'%') AS discount_percent,
                    CASE discount_type 
                      WHEN 1 THEN 'By Percentage'
                      WHEN 2 THEN 'By fixed amount'
                    END discount_type,
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
        
        $query = "INSERT INTO products (product_code, product_name, amount, discount_amount, discount_percent, discount_type, status)
                    VALUES (:product_code, :product_name, :amount, :discount_amount, :discount_percent, :discount_type, :status)";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':product_code', $this->product_code);
        $command->bindParam(':product_name', $this->product_name);
        $command->bindParam(':amount', $this->amount);
        $command->bindParam(':discount_amount', $this->discount_amount);
        $command->bindParam(':discount_percent', $this->discount_percent);
        $command->bindParam(':discount_type', $this->discount_type);
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
                    discount_amount = :discount_amount,
                    discount_percent = :discount_percent,
                    discount_type = :discount_type,
                    status = :status
                  WHERE product_id = :product_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':product_id', $this->product_id);
        $command->bindParam(':product_code', $this->product_code);
        $command->bindParam(':product_name', $this->product_name);
        $command->bindParam(':amount', $this->amount);
        $command->bindParam(':discount_amount', $this->discount_amount);
        $command->bindParam(':discount_percent', $this->discount_percent);
        $command->bindParam(':discount_type', $this->discount_type);
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
