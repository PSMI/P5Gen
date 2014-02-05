<?php

/*
 * @author : owliber
 * @date : 2014-02-03
 */

class PurchasesModel extends CFormModel
{
    public $_connection;
        
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function insertPurchased($product)
    {
        $conn = $this->_connection;
        
        $member_id = $product['member_id'];
        $product_code = $product['product_code'];
        $product_name = $product['product_name'];
        $date_purchase = $product['date_purchased'];
        $payment_mode = $product['payment_mode_id'];
        
        /* Insert purchased products */
        $query = "INSERT INTO purchases (member_id, product_code, product_name, date_purchased, payment_type_id)
                    VALUES (:member_id, :product_code, :product_name, :date_purchased, :payment_mode_id)";

        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $command->bindParam(':product_code', $product_code);
        $command->bindParam(':product_name', $product_name);
        $command->bindParam(':date_purchased', $date_purchase);
        $command->bindParam(':payment_mode_id', $payment_mode);

        $result = $command->execute();
        
        return $result;
    }
}
?>
