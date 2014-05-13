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

<?php $array_count = count($own_purchase_comm_details_table) - 1; ?>
<?php if (count($own_purchase_comm_details_table) > 0) { ?>
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
            <th><?php echo $own_purchase_comm_details_table[0]['ipd_retention_rate'] ?>% RM</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($own_purchase_comm_details_table as $row) {
            ?>
            <tr>
                <td class="ctr"><?php echo $ctr; ?></td>
                <td><?php echo $row['date_purchased']; ?></td>
                <td><?php echo $row['product_name'] ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td style="text-align: right;"><?php echo number_format($row['total'], 2, '.', ','); ?></td>
                <td style="text-align: right;"><?php echo number_format($row['savings'], 2, '.', ','); ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
            <tr>
                <th></th><th></th><th></th>
                <th>Total</th>
                <td style="text-align: right;"><?php echo number_format($own_purchase_comm_details_table[$array_count]['grand_total'], 2, '.', ','); ?></td>
                <td style="text-align: right;"><?php echo number_format($own_purchase_comm_details_table[$array_count]['total_savings'], 2, '.', ','); ?></td>
            </tr>
    </table>
    <br />
</page>
<?php } ?>

<?php $array_count = count($ipd_direct_comm_table) - 1; ?>
<?php if (count($ipd_direct_comm_table) > 0) { ?>
<page>
    <h5><?php echo $ipd_direct_comm_table[0]['comm_ipd_rate'] ?>% Downline Commission</h5>
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
            <th><?php echo $ipd_direct_comm_table[0]['comm_ipd_rate'] ?>% RPC</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($ipd_direct_comm_table as $row) {
            ?>
            <tr>
                <td class="ctr"><?php echo $ctr; ?></td>
                <td class="name"><?php echo $row['member_name'] ?></td>
                <td><?php echo $row['account_type']; ?></td>
                <td><?php echo $row['date_purchased']; ?></td>
                <td><?php echo $row['product_name'] ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td style="text-align: right;"><?php echo number_format($row['total'], 2, '.', ','); ?></td>
                <td style="text-align: right;"><?php echo number_format($row['savings'], 2, '.', ','); ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
            <tr>
                <th></th><th></th><th></th><th></th><th></th>
                <th>Total</th>
                <td style="text-align: right;"><?php echo number_format($ipd_direct_comm_table[$array_count]['grand_total'], 2, '.', ','); ?></td>
                <td style="text-align: right;"><?php echo number_format($ipd_direct_comm_table[$array_count]['total_savings'], 2, '.', ','); ?></td>
            </tr>
    </table>
    <br />
</page>
<?php } ?>

<?php $array_count = count($ibo_direct_comm_table_table) - 1; ?>
<?php if (count($ibo_direct_comm_table_table) > 0) { ?>
<page>
    <h5><?php echo $ibo_direct_comm_table_table[0]['comm_ibo_rate']; ?>% Downline Commission</h5>
    <table id="tbl-lists">
        <tr>
            <th class="ctr">&nbsp;</th>
            <th class="name">Member Name</th>
            <th>Membership</th>
            <th>Date Purchased</th>
            <th>Product Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th><?php echo $ibo_direct_comm_table_table[0]['comm_ibo_rate'] ?>% RPC</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($ibo_direct_comm_table_table as $row) {
            ?>
            <tr>
                <td class="ctr"><?php echo $ctr; ?></td>
                <td class="name"><?php echo $row['member_name'] ?></td>
                <td><?php echo $row['account_type']; ?></td>
                <td><?php echo $row['date_purchased']; ?></td>
                <td><?php echo $row['product_name'] ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td style="text-align: right;"><?php echo number_format($row['total'], 2, '.', ','); ?></td>
                <td style="text-align: right;"><?php echo number_format($row['savings'], 2, '.', ','); ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
            <tr>
                <th></th><th></th><th></th><th></th><th></th>
                <th>Total</th>
                <td style="text-align: right;"><?php echo number_format($ibo_direct_comm_table_table[$array_count]['grand_total'], 2, '.', ','); ?></td>
                <td style="text-align: right;"><?php echo number_format($ibo_direct_comm_table_table[$array_count]['total_savings'], 2, '.', ','); ?></td>
            </tr>
    </table>
    <br />
</page>
<?php } ?>

<?php $array_count = count($indirect_endorse_table) - 1; ?>
<?php if (count($indirect_endorse_table) > 0) { ?>
<page>
    <h5><?php echo $indirect_endorse_table[0]['rpc_rate']; ?>% Downline Commission</h5>
    <table id="tbl-lists">
        <tr>
            <th class="ctr">&nbsp;</th>
            <th class="name">Member Name</th>
            <th>Membership</th>
            <th>Date Purchased</th>
            <th>Product Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th><?php echo $indirect_endorse_table[0]['rpc_rate'] ?>% RPC</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($indirect_endorse_table as $row) {
            ?>
            <tr>
                <td class="ctr"><?php echo $ctr; ?></td>
                <td class="name"><?php echo $row['member_name'] ?></td>
                <td><?php echo $row['account_type']; ?></td>
                <td><?php echo $row['date_purchased']; ?></td>
                <td><?php echo $row['product_name'] ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td style="text-align: right;"><?php echo number_format($row['total'], 2, '.', ','); ?></td>
                <td style="text-align: right;"><?php echo number_format($row['savings'], 2, '.', ','); ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
            <tr>
                <th></th><th></th><th></th><th></th><th></th>
                <th>Total</th>
                <td style="text-align: right;"><?php echo number_format($indirect_endorse_table[$array_count]['grand_total'], 2, '.', ','); ?></td>
                <td style="text-align: right;"><?php echo number_format($indirect_endorse_table[$array_count]['total_savings'], 2, '.', ','); ?></td>
            </tr>
    </table>
    <br />
</page>
<?php } ?>

