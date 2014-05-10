<link href="css/report.css" type="text/css" rel="stylesheet" />
<?php
/* Payee Information */
$payee_name = $payee['last_name'] . ', ' . $payee['middle_name'] . ' ' . $payee['first_name'];
$payee_username = $payee['username'];
$payee_email = $payee['email'];
$payee_mobile_no = $payee['mobile_no'];
$payee_tel_no = $payee['telephone_no'];

$endoser_name = $endorser['last_name'] . ', ' . $endorser['middle_name'] . ' ' . $endorser['first_name'];
$curdate = date('M d, Y h:ia');
?>
<page>
    <div id="header" align="center">
        <div class="logo">&nbsp;</div>
        <p class="address">Unit 6 2nd Flr. Maclane Centre, Nat�l Hi-way<br />
        San Antonio, San Pedro, Laguna<br />
        www.p5partners.com<br />
        (02)553-68-19
        </p>
    </div>
    <h4>Distributor Retention Money</h4>
    <table id="tbl-summary">
        <tr>
            <th>Name of Payee</th>
            <td><?php echo $payee_name; ?></td>
        </tr>
        <tr>
            <th>Endorser Name</th>
            <td><?php echo $endoser_name; ?></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><?php echo $payee_username; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $payee_email; ?></td>
        </tr>

        <tr>
            <th>Mobile No</th>
            <td><?php echo $payee_mobile_no; ?></td>
        </tr>

        <tr>
            <th>Telephone No</th>
            <td><?php echo $payee_tel_no; ?></td>
        </tr>        
        <tr>
            <th>Date Generated</th>
            <td><?php echo $curdate; ?></td>
        </tr>
        <tr>
            <th>Total Retention</th>
            <td align="right"><?php echo number_format($total_retention, 2); ?></td>
        </tr>
        <tr>
            <th colspan="2">Deductions</th>
        </tr>
        <tr>
            <th>Tax Withheld</th>
            <td align="right">(<?php echo number_format($payout['tax_amount'], 2); ?>)</td>
        </tr>
        <tr>
            <th>Net Amount</th>
            <td align="right"><strong><?php echo number_format($payout['net_amount'], 2); ?></strong></td>
        </tr>
    </table>
    <br />
    <table id="tbl-signature">
        <tr>
            <th>Processed By</th>
            <td>&nbsp;</td>
            <th>Received By</th>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <th>Date Processed</th>
            <td>&nbsp;</td>
            <th>Date Received</th>
            <td>&nbsp;</td>
        </tr>
    </table>
    <div id="footer">
        <div class="slogan" align="center">�Finding ways in helping others is our top priority.�</div>
    </div>
</page>

<?php if (count($comm_details_own) > 0) { ?>
<page>
    <h4>Distributor Retention Money</h4>
    <h5>Purchase Retention</h5>
    <table id="tbl-details">
        <tr>
            <th>Name of Payee</th>
            <td><?php echo $payee_name; ?></td>
            <th>Email</th>
            <td><?php echo $payee_email; ?></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><?php echo $payee_username; ?></td>
            <th>Mobile No</th>
            <td><?php echo $payee_mobile_no; ?></td>
        </tr>
        <tr>
            <th>Endorser Name</th>
            <td><?php echo $endoser_name; ?></td>
            <th>Telephone No</th>
            <td><?php echo $payee_tel_no; ?></td>
        </tr>
    </table> 
    <br />
    <table id="tbl-lists">
        <tr>
            <th class="ctr">&nbsp;</th>
            <th>Date Purchased</th>
            <th>Product Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
            <th>5% RM</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($comm_details_own as $row) {
            ?>
            <tr>
                <td class="ctr"><?php echo $ctr; ?></td>
                <td><?php echo $row['date_purchased']; ?></td>
                <td><?php echo $row['product_name'] ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['srp']; ?></td>
                <td><?php echo $row['total']; ?></td>
                <td><?php echo $row['savings']; ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
            <tr>
                <th></th><th></th><th></th><th></th>
                <th>Total</th>
                <td><?php echo $comm_details_own_total[0]['total_price'] ?></td>
                <td><?php echo $comm_details_own_total[0]['total_savings'] ?></td>
            </tr>
    </table>
    <br />
</page>
<?php } ?>

<?php if (count($comm_details_downlines_five_prcnt) > 0) { ?>
<page>
    <h5>5% Downline Commission</h5>
    <table id="tbl-details">
        <tr>
            <th>Name of Payee</th>
            <td><?php echo $payee_name; ?></td>
            <th>Email</th>
            <td><?php echo $payee_email; ?></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><?php echo $payee_username; ?></td>
            <th>Mobile No</th>
            <td><?php echo $payee_mobile_no; ?></td>
        </tr>
        <tr>
            <th>Endorser Name</th>
            <td><?php echo $endoser_name; ?></td>
            <th>Telephone No</th>
            <td><?php echo $payee_tel_no; ?></td>
        </tr>
    </table> 
    <br />
    <table id="tbl-lists">
        <tr>
            <th class="ctr">&nbsp;</th>
            <th class="name">Member Name</th>
            <th>Membership</th>
            <th>Date Purchased</th>
            <th>Product Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
            <th>5% RPC</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($comm_details_downlines_five_prcnt as $row) {
            ?>
            <tr>
                <td class="ctr"><?php echo $ctr; ?></td>
                <td class="name"><?php echo $row['member_name'] ?></td>
                <td><?php echo AdmintransactionsController::getMemberType($row['account_type_id']); ?></td>
                <td><?php echo $row['date_purchased']; ?></td>
                <td><?php echo $row['product_name'] ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['srp']; ?></td>
                <td><?php echo $row['total']; ?></td>
                <td><?php echo $row['savings']; ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
            <tr>
                <th></th><th></th><th></th><th></th><th></th><th></th>
                <th>Total</th>
                <td><?php echo $comm_details_downlines_five_prcnt_total[0]['total_price'] ?></td>
                <td><?php echo $comm_details_downlines_five_prcnt_total[0]['total_savings'] ?></td>
            </tr>
    </table>
    <br />
</page>
<?php } ?>

<?php if (count($comm_details_downlines_three_prcnt) > 0) { ?>
<page>
    <h5>3% Downline Commission</h5>
    <table id="tbl-lists">
        <tr>
            <th class="ctr">&nbsp;</th>
            <th class="name">Member Name</th>
            <th>Membership</th>
            <th>Date Purchased</th>
            <th>Product Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
            <th>3% RPC</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($comm_details_downlines_three_prcnt as $row) {
            ?>
            <tr>
                <td class="ctr"><?php echo $ctr; ?></td>
                <td class="name"><?php echo $row['member_name'] ?></td>
                <td><?php echo AdmintransactionsController::getMemberType($row['account_type_id']); ?></td>
                <td><?php echo $row['date_purchased']; ?></td>
                <td><?php echo $row['product_name'] ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['srp']; ?></td>
                <td><?php echo $row['total']; ?></td>
                <td><?php echo $row['savings']; ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
            <tr>
                <th></th><th></th><th></th><th></th><th></th><th></th>
                <th>Total</th>
                <td><?php echo $comm_details_downlines_three_prcnt_total[0]['total_price'] ?></td>
                <td><?php echo $comm_details_downlines_three_prcnt_total[0]['total_savings'] ?></td>
            </tr>
    </table>
    <br />
</page>
<?php } ?>

<?php if (count($comm_details_downlines_one_prcnt) > 0) { ?>
<page>
    <h5>1% Downline Commission</h5>
    <table id="tbl-lists">
        <tr>
            <th class="ctr">&nbsp;</th>
            <th class="name">Member Name</th>
            <th>Membership</th>
            <th>Date Purchased</th>
            <th>Product Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
            <th>1% RPC</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($comm_details_downlines_one_prcnt as $row) {
            ?>
            <tr>
                <td class="ctr"><?php echo $ctr; ?></td>
                <td class="name"><?php echo $row['member_name'] ?></td>
                <td><?php echo AdmintransactionsController::getMemberType($row['account_type_id']); ?></td>
                <td><?php echo $row['date_purchased']; ?></td>
                <td><?php echo $row['product_name'] ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['srp']; ?></td>
                <td><?php echo $row['total']; ?></td>
                <td><?php echo $row['savings']; ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
            <tr>
                <th></th><th></th><th></th><th></th><th></th><th></th>
                <th>Total</th>
                <td><?php echo $comm_details_downlines_one_prcnt_total[0]['total_price'] ?></td>
                <td><?php echo $comm_details_downlines_one_prcnt_total[0]['total_savings'] ?></td>
            </tr>
    </table>
    <br />
</page>
<?php } ?>

