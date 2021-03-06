<?php

/*
 * @author : owliber
 * @date : 2014-02-03
 */

class PurchasesModel extends CFormModel
{
    public $_connection;
    public $autocomplete_name;
    public $member_id;
    public $endorser_id;
    public $product_id;
    public $product_code;
    public $product_name;
    public $quantity;
    public $payment_type_id;
    public $purchase_summary_id;
    public $purchase_id;
    public $srp;
    public $rp_commission;
    public $discount;
    public $net_price;
    public $savings;
    public $is_repeat;
    public $cutoff_id;
    public $commission;
    public $date_from;
    public $date_to;
    public $receipt_no;
    public $repeat_purchase_id;
    public $cancel_reason;
        
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('autocomplete_name','required'),
            array('member_id,purchase_summary_id,date_from,date_to','safe'),
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
        $member_id = $product['member_id'];
        $product_id = $product['product_id'];
        $amount = $product['amount'];
        $date_purchase = $product['date_purchased'];
        $payment_mode = $product['payment_mode_id'];
        /* Insert purchased products */
        $query = "INSERT INTO purchased_items (member_id, product_id, srp, date_purchased, quantity, payment_type_id, status)
                    VALUES (:member_id, :product_id, :amount, :date_purchased, 1, :payment_mode_id, 1)";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
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
        $query = "SELECT
                    ps.purchase_summary_id,
                    pi.purchase_id,
                    p.product_code,
                    p.product_name,
                    DATE_FORMAT(ps.date_purchased, '%b %d, %Y') AS date_purchased,
                    FORMAT(pi.srp, 2) AS srp,
                    pi.discount,
                    FORMAT(pi.net_price, 2) AS net_price,
                    FORMAT(pi.savings, 2) AS savings,
                    pi.quantity,
                    FORMAT(pi.total, 2) AS total
                  FROM purchased_items pi
                    INNER JOIN purchased_summary ps
                      ON pi.purchase_summary_id = ps.purchase_summary_id
                    INNER JOIN products p
                      ON pi.product_id = p.product_id
                  WHERE ps.member_id = :member_id
                  AND ps.status = 0;";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $result = $command->queryAll();
        return $result;
    }
    
    public function selectByDate()
    {
        $conn = $this->_connection;
        $query = "SELECT
                    ps.purchase_summary_id,
                    ps.member_id,
                    CONCAT(md.last_name, ', ', md.first_name) AS member,
                    CASE m.account_type_id WHEN 3 THEN 'IBO' WHEN 5 THEN 'IPD' END account_type,
                    pi.purchase_id,
                    p.product_code,
                    ps.receipt_no,
                    p.product_name,
                    DATE_FORMAT(pi.date_created, '%b %d, %Y') AS date_purchased,
                    pi.quantity,
                    FORMAT(pi.discount, 2) AS discount,
                    FORMAT(pi.srp, 2) AS SRP,
                    FORMAT(pi.net_price, 2) AS net_price,
                    FORMAT(pi.total, 2) AS total
                  --  SUM(pi.quantity) AS quantity,
                  --  FORMAT(SUM(pi.total), 2) AS total
                  FROM purchased_items pi
                    INNER JOIN products p
                      ON pi.product_id = p.product_id
                    INNER JOIN purchased_summary ps
                      ON pi.purchase_summary_id = ps.purchase_summary_id
                    INNER JOIN member_details md ON ps.member_id = md.member_id
                    INNER JOIN members m ON md.member_id = m.member_id
                  WHERE pi.date_created >= :date_from
                  AND pi.date_created <= :date_to
                  AND ps.status = 1
               --   GROUP BY DATE(pi.date_created),
               --            pi.product_id
                  ORDER BY pi.date_created;";
        $command = $conn->createCommand($query);
        $command->bindParam(':date_from', $this->date_from);
        $command->bindParam(':date_to', $this->date_to);
        $result = $command->queryAll();
        return $result;
    }
    
    public function selectByDateTotal()
    {
        $conn = $this->_connection;
        $query = "SELECT sum(pi.quantity) AS total_quantity,
                         sum(pi.total) as total_amount
                    FROM purchased_items pi
                        INNER JOIN purchased_summary ps ON pi.purchase_summary_id = ps.purchase_summary_id
                    WHERE pi.date_created >= :date_from 
                        AND pi.date_created <= :date_to
                        AND ps.status = 1;";
        $command = $conn->createCommand($query);
        $command->bindParam(':date_from', $this->date_from);
        $command->bindParam(':date_to', $this->date_to);
        $result = $command->queryRow();
        return $result;
    }
    
    public function selectByID()
    {
        $conn = $this->_connection;
        $query = "SELECT pi.purchase_id,
                         p.product_code,
                         p.product_name,                         
                         date_format(pi.date_created,'%b %d, %Y') AS date_purchased,
                         format(pi.srp,2) as srp,
                         pi.discount,
                         format(pi.net_price,2) as net_price,
                         format(sum(pi.savings),2) as savings,
                         sum(pi.quantity) as quantity,
                         format(sum(pi.total),2) as total
                    FROM purchased_items pi
                        INNER JOIN products p ON pi.product_id = p.product_id
                        INNER JOIN purchased_summary ps ON pi.purchase_summary_id = ps.purchase_summary_id
                    WHERE ps.member_id = :member_id
                        AND ps.status = 1
                    GROUP BY date(pi.date_created), pi.product_id
                    ORDER BY pi.date_created;";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $result = $command->queryAll();
        return $result;
    }
    
    public function selectByIDTotal()
    {
        $conn = $this->_connection;
        $query = "SELECT sum(pi.savings) as total_savings,
                         sum(pi.quantity) as total_quantity,
                         sum(pi.total) as total_amount
                    FROM purchased_items pi
                        INNER JOIN purchased_summary ps ON pi.purchase_summary_id = ps.purchase_summary_id
                    WHERE ps.member_id = :member_id
                        AND ps.status = 1;";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $result = $command->queryRow();
        return $result;
    }
    
        
    public function getItemTotal()
    {
        $conn = $this->_connection;
        $query = "SELECT
                ps.total AS total_amount,
                ps.quantity AS total_quantity,
                ps.savings AS total_savings
              FROM purchased_summary ps
              WHERE ps.member_id = :member_id
              AND ps.purchase_summary_id = :purchase_summary_id
              AND ps.status = 0;";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $command->bindParam(':purchase_summary_id', $this->purchase_summary_id);
        $result = $command->queryRow();
        if(!empty($result))
            return $result;
        else
            return array('total_amount'=>'0.00','total_savings'=>'0.00','total_quantity'=>0);
    }
    
    
    public function add_new_purchase()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $model = new ProductsForm();
        $reference = new ReferenceModel();
        
        $product = $model->selectProductById($this->product_id);
        $srp = $product['amount'];
              
        if(Members::getMembershipType($this->member_id) == 'distributor')
        {
            $discount = $product['ipd_discount']; 
            $rp_commission = $reference->get_variable_value('IPD_REPEAT_PURCHASE_COMMISSION');
            
        }
        
        if(Members::getMembershipType($this->member_id) == 'member')
        {
            $discount = $product['ibo_discount'];
            $rp_commission = $reference->get_variable_value('IBO_REPEAT_PURCHASE_COMMISSION');
        }
        
        $discount_price = $srp * ($discount / 100);
        $net_price = $srp - $discount_price;
        $total_net_price = $this->quantity * $net_price;
        $savings = $total_net_price * ($rp_commission / 100);
        
        $query = "INSERT INTO purchased_summary (member_id, quantity, total, savings, payment_type_id)
                    VALUES (:member_id, :quantity, :total, :savings, :payment_type_id)";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $command->bindParam(':quantity', $this->quantity);
        $command->bindParam(':savings', $savings);
        $command->bindParam(':total', $total_net_price);
        $command->bindParam(':payment_type_id', $this->payment_type_id);
        $command->execute();
        $purchase_summary_id = $conn->lastInsertID;
        
        if(!$this->hasErrors())
        {
            $query1 = "INSERT INTO purchased_items (purchase_summary_id,product_id, srp, discount, net_price, total, savings, quantity)
                    VALUES (:purchase_summary_id, :product_id, :srp, :discount, :net_price, :total, :savings, :quantity)";
        
            $command1 = $conn->createCommand($query1);
            $command1->bindParam(':purchase_summary_id', $purchase_summary_id);
            $command1->bindParam(':product_id', $this->product_id);
            $command1->bindParam(':srp', $srp);
            $command1->bindParam(':discount', $discount);
            $command1->bindParam(':net_price', $net_price);
            $command1->bindParam(':total', $total_net_price);
            $command1->bindParam(':savings', $savings);
            $command1->bindParam(':quantity', $this->quantity);
            $command1->execute();
            try
            {
                $trx->commit();
                return $purchase_summary_id;
            }
            catch(PDOException $e)
            {
                $trx->rollback();
                return false;
            }
        }
        
    }
    
    
    public function add_new_item()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $model = new ProductsForm();
        $reference = new ReferenceModel();
        
        $product = $model->selectProductById($this->product_id);
        $srp = $product['amount'];
              
        if(Members::getMembershipType($this->member_id) == 'distributor')
        {
            $discount = $product['ipd_discount']; 
            $rp_commission = $reference->get_variable_value('IPD_REPEAT_PURCHASE_COMMISSION');
            
        }
        
        if(Members::getMembershipType($this->member_id) == 'member')
        {
            $discount = $product['ibo_discount'];
            $rp_commission = $reference->get_variable_value('IBO_REPEAT_PURCHASE_COMMISSION');
        }
        
        $discount_price = $srp * ($discount / 100);
        $net_price = $srp - $discount_price;
        $total_net_price = $this->quantity * $net_price;
        $savings = $total_net_price * ($rp_commission / 100);
        
        $query = "INSERT INTO purchased_items (purchase_summary_id,product_id, srp, discount, net_price, total, savings, quantity)
                VALUES (:purchase_summary_id, :product_id, :srp, :discount, :net_price, :total, :savings, :quantity)";

        $command = $conn->createCommand($query);
        $command->bindParam(':purchase_summary_id', $this->purchase_summary_id);
        $command->bindParam(':product_id', $this->product_id);
        $command->bindParam(':srp', $srp);
        $command->bindParam(':discount', $discount);
        $command->bindParam(':net_price', $net_price);
        $command->bindParam(':total', $total_net_price);
        $command->bindParam(':savings', $savings);
        $command->bindParam(':quantity', $this->quantity);
        $command->execute();
        if(!$this->hasErrors())
        {
            $query2 = "UPDATE purchased_summary 
                        SET quantity = quantity + :quantity, total = total + :total, savings = savings + :savings
                       WHERE purchase_summary_id = :purchase_summary_id
                        AND status = 0";
            $command1 = $conn->createCommand($query2);
            $command1->bindParam(':purchase_summary_id', $this->purchase_summary_id);
            $command1->bindParam(':quantity', $this->quantity);
            $command1->bindParam(':total', $total_net_price);
            $command1->bindParam(':savings', $savings);
            $command1->execute();
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
    
    public function update_item()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        $model = new ProductsForm();
        $reference = new ReferenceModel();
        
        $product = $model->selectProductById($this->product_id);
        $srp = $product['amount'];
                      
        if(Members::getMembershipType($this->member_id) == 'distributor')
        {
            $discount = $product['ipd_discount']; 
            $rp_commission = $reference->get_variable_value('IPD_REPEAT_PURCHASE_COMMISSION');
            
        }
        
        if(Members::getMembershipType($this->member_id) == 'member')
        {
            $discount = $product['ibo_discount'];
            $rp_commission = $reference->get_variable_value('IBO_REPEAT_PURCHASE_COMMISSION');
        }
        
        $discount_price = $srp * ($discount / 100);
        $net_price = $srp - $discount_price;
        $total_net_price = $this->quantity * $net_price;
        $savings = $total_net_price * ($rp_commission / 100);
        
        $query = "UPDATE purchased_items 
                    SET quantity = quantity + :quantity,
                        srp = :srp,
                        discount = :discount,
                        total = total + :total,
                        savings = savings + :savings,
                        date_updated = now()
                   WHERE purchase_summary_id = :purchase_summary_id
                    AND purchase_id = :purchase_id
                    AND product_id = :product_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':purchase_summary_id', $this->purchase_summary_id);
        $command->bindParam(':purchase_id', $this->purchase_id);
        $command->bindParam(':product_id', $this->product_id);
        $command->bindParam(':srp', $srp);
        $command->bindParam(':discount', $discount);
        $command->bindParam(':savings', $savings);
        $command->bindParam(':total', $total_net_price);
        $command->bindParam(':quantity', $this->quantity);
        $command->execute();
        if(!$this->hasErrors())
        {
            $query2 = "UPDATE purchased_summary 
                        SET quantity = quantity + :quantity, total = total + :total, savings = savings + :savings
                       WHERE purchase_summary_id = :purchase_summary_id
                        AND status = 0";
            $command1 = $conn->createCommand($query2);
            $command1->bindParam(':purchase_summary_id', $this->purchase_summary_id);
            $command1->bindParam(':quantity', $this->quantity);
            $command1->bindParam(':total', $total_net_price);
            $command1->bindParam(':savings', $savings);
            $command1->execute();
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
    
    public function update_purchased_item()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        $model = new ProductsForm();
        $reference = new ReferenceModel();
        
        $product = $model->selectProductById($this->product_id);
        $srp = $product['amount'];
        
        if(Members::getMembershipType($this->member_id) == 'distributor')
        {
            $rp_commission = $reference->get_variable_value('IPD_REPEAT_PURCHASE_COMMISSION');
            
        }
        
        if(Members::getMembershipType($this->member_id) == 'member')
        {
            $rp_commission = $reference->get_variable_value('IBO_REPEAT_PURCHASE_COMMISSION');
        }
        
        $query = "UPDATE purchased_items 
                    SET quantity = :quantity,
                        payment_type_id = :payment_type_id,
                        savings = (:quantity * (srp - (srp * discount / 100))) * (:rp_commission / 100),
                        total = :quantity * (srp - ((srp * discount) / 100))
                   WHERE member_id = :member_id
                    AND purchase_id = :purchase_id
                    AND product_id = :product_id";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
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
    
    public function receipt_is_used()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM purchased_summary
                  WHERE receipt_no = :receipt_no";
        $command = $conn->createCommand($query);
        $command->bindParam(':receipt_no', $this->receipt_no);
        $result = $command->queryAll();
        if(count($result)>0)
            return true;
        else
            return false;
    }
    
    public function checkout_items()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $model = new PurchasesModel();
        $reference = new ReferenceModel();
        
        $ipd_retention_rate = $reference->get_variable_value('IPD_RETENTION_MONEY');
        $comm_ipd_rate = $reference->get_variable_value('IPD_REPEAT_PURCHASE_COMMISSION');
        $comm_ibo_rate = $reference->get_variable_value('IBO_REPEAT_PURCHASE_COMMISSION');
        $rpc_rate = $reference->get_variable_value('RPC_DEFAULT_RATE');
            
        $result = $model->get_retention($this->member_id, $this->purchase_summary_id);
                
        $query = "UPDATE purchased_summary 
                    SET status = 1, 
                        receipt_no = :receipt_no,
                        ipd_retention_rate = :ipd_retention_rate,
                        rpc_rate = :rpc_rate,
                        comm_ipd_rate = :comm_ipd_rate,
                        comm_ibo_rate = :comm_ibo_rate
                    WHERE member_id = :member_id
                    AND purchase_summary_id = :purchase_summary_id
                            AND status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $command->bindParam(':purchase_summary_id', $this->purchase_summary_id);
        $command->bindParam(':receipt_no', $this->receipt_no);
        $command->bindParam(':ipd_retention_rate', $ipd_retention_rate);
        $command->bindParam(':rpc_rate', $rpc_rate);
        $command->bindParam(':comm_ipd_rate', $comm_ipd_rate);
        $command->bindParam(':comm_ibo_rate', $comm_ibo_rate);
        $command->execute();
        
        if(!$this->hasErrors())
        {
            
            $retention = $result['savings'];
            
            if($model->has_ipd_retention($this->member_id))
            {
                 $query2 = "UPDATE distributor_retentions
                            SET purchase_retention = purchase_retention + :purchase_retention
                            WHERE member_id = :member_id
                                AND status = 0";
            }
            else
            {
                $query2 = "INSERT INTO distributor_retentions (member_id, purchase_retention)
                            VALUES (:member_id, :purchase_retention)";
            }
            
            $command2 = $conn->createCommand($query2);
            $command2->bindParam(':member_id', $this->member_id);
            $command2->bindParam(':purchase_retention', $retention);
            $command2->execute();
            
            if(!$this->hasErrors())
            {
                $query3 = "INSERT INTO repeat_purchases (purchase_summary_id, member_id, total)
                           SELECT purchase_summary_id, member_id, total 
                           FROM purchased_summary 
                           WHERE purchase_summary_id = :purchase_summary_id
                            AND member_id = :member_id;";
                $command3 = $conn->createCommand($query3);
                $command3->bindParam(':member_id', $this->member_id);
                $command3->bindParam(':purchase_summary_id', $this->purchase_summary_id);
                $command3->execute();
                
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
        
    }
    
    public function get_retention($member_id, $purchase_summary_id)
    {
        $conn = $this->_connection;
        
        $query1 = "SELECT * FROM purchased_summary 
                        WHERE member_id = :member_id
                        AND purchase_summary_id = :purchase_summary_id 
                        AND status = 0";
            
        $command1 = $conn->createCommand($query1);
        $command1->bindParam(':member_id', $member_id);
        $command1->bindParam(':purchase_summary_id', $purchase_summary_id);
        $result = $command1->queryRow();
        
        return $result;
    }
    
    public function remove_item()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $query = "SELECT * FROM purchased_summary"
                . " WHERE purchase_summary_id = :purchase_summary_id ";
        $command = $conn->createCommand($query);
        $command->bindParam(':purchase_summary_id', $this->purchase_summary_id);
        $result = $command->queryRow();
        
        $item_quantity = $result['quantity'];
        $item_total = $result['total'];
        $item_savings = $result['savings'];
        
        $query1 = "DELETE FROM purchased_items 
                    WHERE purchase_summary_id = :purchase_summary_id 
                    AND purchase_id = :purchase_id";
        $command1 = $conn->createCommand($query1);
        $command1->bindParam(':purchase_id', $this->purchase_id);
        $command1->bindParam(':purchase_summary_id', $this->purchase_summary_id);
        $command1->execute();
        
        if(!$this->hasErrors())
        {
            $query2 = "UPDATE purchased_summary
                        SET quantity = quantity - :old_quantity,
                            total = total - :old_total,
                            savings = savings - :old_savings
                        WHERE purchase_summary_id = :purchase_summary_id;";
            $command2 = $conn->createCommand($query2);
            $command2->bindParam(':purchase_summary_id', $this->purchase_summary_id);
            $command2->bindParam(':old_quantity', $item_quantity);
            $command2->bindParam(':old_total', $item_total);
            $command2->bindParam(':old_savings', $item_savings);
            $command2->execute();
            
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
    
    public function cancel_items()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE purchased_summary SET status = 2
                  WHERE purchase_summary_id = :purchase_summary_id
                    AND member_id = :member_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $command->bindParam(':purchase_summary_id', $this->purchase_summary_id);
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
                         pi.member_id,
                         pi.payment_type_id
                    FROM purchased_items pi
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
        
        $query = "SELECT
                    *
                  FROM purchased_items pi
                      INNER JOIN purchased_summary ps ON pi.purchase_summary_id = ps.purchase_summary_id
                  WHERE ps.member_id = :member_id
                  AND ps.purchase_summary_id = :purchase_summary_id
                  AND pi.product_id = :product_id
                  AND ps.status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':purchase_summary_id', $this->purchase_summary_id);
        $command->bindParam(':product_id', $this->product_id);
        $command->bindParam(':member_id', $this->member_id);
        $result = $command->queryAll();
        
        if(count($result) > 0)
            return $result;
        else
            return false;
    }
    
    public function is_repeat_purchase()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM purchased_items
                   WHERE member_id = :member_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $result = $command->queryAll();
        
        if(count($result)>0)
            return true;
        else
            return false;
    }
    
    public function get_repeat_purchases()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM repeat_purchases
                   WHERE member_id = :member_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->member_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function delete_processed_purchases()
    {
        $conn = $this->_connection;
        
        $query = "DELETE FROM repeat_purchases WHERE repeat_purchase_id = :repeat_purchase_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':repeat_purchase_id', $this->repeat_purchase_id);
        $command->execute();
        
        //Replacement for the DB trigger
        if(!$this->hasErrors())
        {
            $query2 = "INSERT INTO repeat_purchase_logs (purchase_summary_id, member_id, total, date_purchased)"
                    . " SELECT purchase_summary_id, member_id, total, date_purchased FROM repeat_purchases"
                    . " WHERE repeat_purchase_id = :repeat_purchase_id ;";
            $command2 = $conn->createCommand($query2);
            $command2->bindParam(':repeat_purchase_id', $this->repeat_purchase_id);
            $command2->execute();
        }
        
    }
    
    public function get_unprocessed_purchases()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM repeat_purchases
                    WHERE status = 0
                    LIMIT 25";
        $command = $conn->createCommand($query);
        $result = $command->queryAll();
        return $result;
    }

    public function has_transaction()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM distributor_commissions
                    WHERE cutoff_id = :cutoff_id
                        AND member_id = :member_id AND status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':cutoff_id', $this->cutoff_id);
        $command->bindParam(':member_id', $this->endorser_id);
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
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE repeat_purchases
                    SET status = 1
                    WHERE purchase_id = :purchase_id";
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
    
    /**
     * @author Noel Antonio
     * @date 04-12-2014
     */
    public function insertPurchasedItem($params)
    {
        $conn = $this->_connection;
        
        $member_id = $params['member_id'];
        $product_id = $params['product_code'];
        // $date_purchase = $params['date_purchased'];
        $payment_mode = $params['payment_mode_id'];
        
        $product_info = ProductsForm::selectProductById($product_id);
        $product_amount = $product_info['amount'];
        
        /* Insert purchased summary */
        $query = "INSERT INTO purchased_summary (member_id, quantity, total, payment_type_id, status)
                VALUES (:member_id, 1, :total, :payment_mode_id, 1)";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $member_id);
        $command->bindParam(':total', $product_amount);
        $command->bindParam(':payment_mode_id', $payment_mode);
        $result = $command->execute();
            
        try
        {
            if ($result > 0)
            {
                $last_inserted_id = $conn->getLastInsertID();
                
                /* Insert purchased items */
                $query2 = "INSERT INTO purchased_items (purchase_summary_id, product_id, quantity, total)
                    VALUES (:purchased_summary_id, :product_id, 1, :total)";
                $command2 = $conn->createCommand($query2);
                $command2->bindParam(':purchased_summary_id', $last_inserted_id);
                $command2->bindParam(':product_id', $product_id);
                $command2->bindParam(':total', $product_amount);
                $result2 = $command2->execute();
                
                if ($result2 > 0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        catch (PDOException $e)
        {
            return false;
        }
    }
    
    public function has_retention()
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM distributor_retentions
                    WHERE member_id = :member_id 
                        AND status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->endorser_id);
        $result = $command->queryAll();
        if(count($result)>0)
            return true;
        else
            return false;
    }
    
    public function has_ipd_retention($endorser_id)
    {
        $conn = $this->_connection;
        
        $query = "SELECT * FROM distributor_retentions
                    WHERE member_id = :member_id 
                        AND status = 0";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $endorser_id);
        $result = $command->queryAll();
        if(count($result)>0)
            return true;
        else
            return false;
    }
    
    public function update_ipd_retention()
    {
        $conn = $this->_connection;
        
        $query = "UPDATE distributor_retentions
                    SET other_retention = other_retention + :other_retention
                    WHERE member_id = :member_id
                        AND status = 0";
        
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->endorser_id);
        $command->bindParam(':other_retention', $this->commission);
        $command->execute();
        
    }
    
    public function insert_ipd_retention()
    {
        $conn = $this->_connection;
        
        $query = "INSERT INTO distributor_retentions (member_id, other_retention)
                    VALUES (:member_id, :other_retention)";
        $command = $conn->createCommand($query);
        $command->bindParam(':member_id', $this->endorser_id);
        $command->bindParam(':other_retention', $this->commission);
        $command->execute();
        
    }
    
    public function cancel_purchase()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $query = "UPDATE purchased_summary
                    SET status = 2,
                        cancelled_by_mid = :logged_user_id,
                        cancellation_reason = :reason,
                        date_cancelled = now()
                    WHERE purchase_summary_id = :purchase_summary_id
                        AND member_id = :member_id
                        AND receipt_no = :receipt_no";
        
        $logged_user_id = Yii::app()->user->getId();
        
        $command = $conn->createCommand($query);
        $command->bindParam(':logged_user_id', $logged_user_id);
        $command->bindParam(':reason', $this->cancel_reason);
        $command->bindParam(':purchase_summary_id', $this->purchase_summary_id);
        $command->bindParam(':member_id', $this->member_id);
        $command->bindParam(':receipt_no', $this->receipt_no);
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
