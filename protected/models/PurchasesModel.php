<?php

/*
 * @author : owliber
 * @date : 2014-02-03
 */

class PurchasesModel extends CFormModel
{
    public $_connection;
    public $autocomplete_name;
    public $distributor_id;
    public $endorser_id;
    public $product_id;
    public $product_code;
    public $product_name;
    public $quantity;
    public $payment_type_id;
    public $purchase_id;
    public $srp;
    public $rp_commission;
    public $discount;
    public $net_price;
    public $savings;
    public $is_repeat;
    public $cutoff_id;
    public $commission;
        
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('autocomplete_name','required'),
            array('distributor_id','safe'),
        );
    }
    public function insertPurchased($product)
    {
        $conn = $this->_connection;
        
        $member_id = $product['member_id'];
        $product_code = $product['product_code'];
        //$product_name = $product['product_name'];
        $date_purchase = $product['date_purchased'];
        $payment_mode = $product['payment_mode_id'];
        
        /* Insert purchased products */
        $query = "INSERT INTO purchases (member_id, product_id, date_purchased, payment_type_id)
                    VALUES (:member_id, :product_code, :date_purchased, :payment_mode_id)";

        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $command->bindParam(':product_code', $product_code);
        //$command->bindParam(':product_name', $product_name);
        $command->bindParam(':date_purchased', $date_purchase);
        $command->bindParam(':payment_mode_id', $payment_mode);
        $result = $command->execute();
        return $result;
    }
    public function insertIPDPurchased($product)
    {
        $conn = $this->_connection;
        $distributor_id = $product['member_id'];
        $product_id = $product['product_id'];
        $amount = $product['amount'];
        $date_purchase = $product['date_purchased'];
        $payment_mode = $product['payment_mode_id'];
        /* Insert purchased products */
        $query = "INSERT INTO distributor_purchased_items (distributor_id, product_id, srp, date_purchased, payment_type_id)
                    VALUES (:distributor_id, :product_id, :amount, :date_purchased, :payment_mode_id)";
        $command = $conn->createCommand($query);
        $command->bindParam(':distributor_id', $distributor_id);
        $command->bindParam(':product_id', $product_id);
        $command->bindParam(':amount', $amount);
        $command->bindParam(':date_purchased', $date_purchase);
        $command->bindParam(':payment_mode_id', $payment_mode);

        $result = $command->execute();
        
        return $result;
    }
    public function selectAll()
    {
        $conn = $this->_connection;
        $query = "SELECT pi.purchase_id,
                         p.product_code,
                         p.product_name,                         
                         date_format(pi.date_purchased,'%b %d, %Y') AS date_purchased,
                         format(pi.srp,2) as srp,
                         pi.discount,
                         format(pi.net_price,2) as net_price,
                         format(pi.savings,2) as savings,
                         pi.quantity,
                         format(total,2) as total
                    FROM distributor_purchased_items pi
                    INNER JOIN products p ON pi.product_id = p.product_id
                        WHERE pi.status = 0;";
        $command = $conn->createCommand($query);
        $result = $command->queryAll();
        return $result;
    }
    
    public function getItemTotal()
    {
        $conn = $this->_connection;
        $query = "SELECT 
                         format(sum(pi.total),2) as total_amount,
                         format(sum(pi.savings),2) as total_savings,
                         sum(pi.quantity) as total_quantity
                    FROM distributor_purchased_items pi
                    INNER JOIN products p ON pi.product_id = p.product_id
                        WHERE pi.status = 0
                    GROUP BY pi.distributor_id;";
        $command = $conn->createCommand($query);
        $result = $command->queryRow();
        if(!empty($result))
            return $result;
        else
            return array('total_amount'=>'0.00','total_savings'=>'0.00','total_quantity'=>0);
    }
    
    
    public function add_purchased_item()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $model = new ProductsForm();
        $reference = new ReferenceModel();
        
        $product = $model->selectProductById($this->product_id);
        $srp = $product['amount'];
              
        if(Members::getMembershipType($this->distributor_id) == 'distributor')
        {
            $discount = $product['ipd_discount']; 
            $rp_commission = $reference->get_variable_value('IPD_REPEAT_PURCHASE_COMMISSION');
            
        }
        
        if(Members::getMembershipType($this->distributor_id) == 'member')
        {
            $discount = $product['ibo_discount'];
            $rp_commission = $reference->get_variable_value('IBO_REPEAT_PURCHASE_COMMISSION');
        }
        
        if($this->is_repeat)
        {
            $discount_price = ($srp * $discount) / 100;
            $net_price = $srp - $discount_price;
            $total_net_price = $this->quantity * $net_price;
            $savings =  ($srp - $net_price) * $this->quantity; //($total_net_price * $rp_commission) / 100;
        }
        else
        {
            $discount = 0;
            $net_price = 0;
            $savings = 0;
            $total_net_price = $srp;
        }
          
        $query = "INSERT INTO distributor_purchased_items (distributor_id, product_id, srp, discount, net_price, total, savings, date_purchased, quantity, payment_type_id)
                    VALUES (:distributor_id, :product_id, :srp, :discount, :net_price, :total, :savings,  now(), :quantity, :payment_type_id)";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':distributor_id', $this->distributor_id);
        $command->bindParam(':product_id', $this->product_id);
        $command->bindParam(':srp', $srp);
        $command->bindParam(':discount', $discount);
        $command->bindParam(':net_price', $net_price);
        $command->bindParam(':total', $total_net_price);
        $command->bindParam(':savings', $savings);
        $command->bindParam(':quantity', $this->quantity);
        $command->bindParam(':payment_type_id', $this->payment_type_id);
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
    
    public function append_purchased_item()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        $model = new ProductsForm();
        $reference = new ReferenceModel();
        
        $product = $model->selectProductById($this->product_id);
        $srp = $product['amount'];
                      
        if(Members::getMembershipType($this->distributor_id) == 'distributor')
        {
            $discount = $product['ipd_discount']; 
            $rp_commission = $reference->get_variable_value('IPD_REPEAT_PURCHASE_COMMISSION');
            
        }
        
        if(Members::getMembershipType($this->distributor_id) == 'member')
        {
            $discount = $product['ibo_discount'];
            $rp_commission = $reference->get_variable_value('IBO_REPEAT_PURCHASE_COMMISSION');
        }
        
        $discount_price = ($srp * $discount) / 100;
        $net_price = $srp - $discount_price;
        $total_net_price = $this->quantity * $net_price;
        $savings = ($total_net_price * $rp_commission) / 100;
        
        $query = "UPDATE distributor_purchased_items 
                    SET quantity = quantity + :quantity,
                        payment_type_id = :payment_type_id,
                        srp = :srp,
                        discount = :discount,
                        net_price = srp - ((srp * discount) / 100),
                        total = total + :total,
                        savings = quantity  * ((srp * discount) / 100)
                   WHERE distributor_id = :distributor_id
                    AND purchase_id = :purchase_id
                    AND product_id = :product_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':distributor_id', $this->distributor_id);
        $command->bindParam(':purchase_id', $this->purchase_id);
        $command->bindParam(':product_id', $this->product_id);
        $command->bindParam(':srp', $srp);
        $command->bindParam(':discount', $discount);
        $command->bindParam(':total', $total_net_price);
        $command->bindParam(':quantity', $this->quantity);
        $command->bindParam(':payment_type_id', $this->payment_type_id);
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
    
    public function update_purchased_item()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        $model = new ProductsForm();
        $reference = new ReferenceModel();
        
        $product = $model->selectProductById($this->product_id);
        $srp = $product['amount'];
        
        if(Members::getMembershipType($this->distributor_id) == 'distributor')
        {
            $rp_commission = $reference->get_variable_value('IPD_REPEAT_PURCHASE_COMMISSION');
            
        }
        
        if(Members::getMembershipType($this->distributor_id) == 'member')
        {
            $rp_commission = $reference->get_variable_value('IBO_REPEAT_PURCHASE_COMMISSION');
        }
        
        $query = "UPDATE distributor_purchased_items 
                    SET quantity = :quantity,
                        payment_type_id = :payment_type_id,
                        srp = :srp,
                        net_price = srp - ((srp * discount) / 100),
                        savings = :quantity * ((srp * discount) / 100),
                        total = :quantity * (srp - ((srp * discount) / 100))
                   WHERE distributor_id = :distributor_id
                    AND purchase_id = :purchase_id
                    AND product_id = :product_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':distributor_id', $this->distributor_id);
        $command->bindParam(':purchase_id', $this->purchase_id);
        $command->bindParam(':product_id', $this->product_id);
        $command->bindParam(':srp', $srp);
        $command->bindParam(':rp_commission', $rp_commission);
        $command->bindParam(':quantity', $this->quantity);
        $command->bindParam(':payment_type_id', $this->payment_type_id);
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
    
    public function checkout_items()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE distributor_purchased_items SET status = 1
                    WHERE distributor_id = :distributor_id
                            AND status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':distributor_id', $this->distributor_id);
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
    
    public function remove_item()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $query = "DELETE FROM distributor_purchased_items WHERE purchase_id = :purchase_id AND status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':purchase_id', $this->purchase_id);
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
    
    public function get_purchase_by_id()
    {
        $conn = $this->_connection;
        
        $query = "SELECT pi.purchase_id,
                         p.product_id,
                         pi.quantity,
                         pi.distributor_id,
                         pi.payment_type_id
                    FROM distributor_purchased_items pi
                    INNER JOIN products p ON pi.product_id = p.product_id
                        WHERE pi.status = 0 AND pi.purchase_id = :purchase_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':purchase_id', $this->purchase_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function is_item_exist()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM distributor_purchased_items
                    WHERE distributor_id = :distributor_id
                        AND product_id = :product_id
                        AND status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':product_id', $this->product_id);
        $command->bindParam(':distributor_id', $this->distributor_id);
        $result = $command->queryAll();
        
        if(count($result) > 0)
            return $result;
        else
            return false;
    }
    
    public function is_repeat_purchase()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM distributor_purchased_items
                   WHERE distributor_id = :distributor_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':distributor_id', $this->distributor_id);
        $result = $command->queryAll();
        
        if(count($result)>0)
            return true;
        else
            return false;
    }
    
    public function get_repeat_purchases()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM distributor_repeat_purchases
                   WHERE distributor_id = :distributor_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':distributor_id', $this->distributor_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function delete_processed_purchases()
    {
        $conn = $this->_connection;
        $query = "DELETE FROM distributor_repeat_purchases WHERE purchase_id = :purchase_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':purchase_id', $this->purchase_id);
        $command->execute();
   
    }
    
    public function get_unprocessed_purchases()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM distributor_repeat_purchases
                    WHERE status = 0
                    LIMIT 25";
        $command = $conn->createCommand($query);
        $command->bindParam(':distributor_id', $this->distributor_id);
        $result = $command->queryAll();
        return $result;
    }

    public function has_transaction()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM distributor_commissions
                    WHERE cutoff_id = :cutoff_id
                        AND member_id AND status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $result = $command->queryAll();
        if(count($result)>0)
            return true;
        else
            return false;
    }
    
    public function insert_commission_transaction()
    {
        $conn = $this->_connection;
        
        $query = "INSERT INTO distributor_commissions (member_id, commission_amount, cutoff_id)
                    VALUES (:member_id, :commission_amount, :cutoff_id)";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->endorser_id);
        $command->bindParam(':commission_amount', $this->commission);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->execute();
    }
    
    public function update_commission_transaction()
    {
        $conn = $this->_connection;
        
        $query = "UPDATE distributor_commissions
                    SET commission_amount = commission_amount + :commission_amount
                    WHERE cutoff_id = :cutoff_id
                        AND member_id = :member_id
                        AND status = 0";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->endorser_id);
        $command->bindParam(':commission_amount', $this->commission);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->execute();
    }
    
    public function update_repeat_purchase()
    {
        $conn = $this->_connection;
        
        $query = "UPDATE distributor_repeat_purchases
                    SET status = 1
                    WHERE purchase_id = :purchase_id)";
        $command = $conn->createCommand($query);
        $command->bindParam(':purchase_id', $this->purchase_id);
        $command->execute();
    }
}
?>
