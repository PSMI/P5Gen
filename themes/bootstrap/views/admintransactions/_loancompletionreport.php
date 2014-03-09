<style type="text/css">
    page {font-family:Courier; font-size:10px; width:100%;}
    table#tbl-summary{font-family:Courier; font-size:12px; width:100%;}
    table, table th, table td{border:1px solid #ccc; border-collapse: collapse; padding: 2px}
    table th {background-color: #ccc;}
    .logo{
        margin-top:10%;
        margin-left:50%;
        left: -70px;
        margin-bottom: -10px;
        position: relative;
        width:140px;
        height: 137px;
        background: url(images/sagip_logo.png) 0 top center no-repeat #fff;
    }
    .address{font-size:8px;padding-top:4px;}
    #footer{padding-top:10px; position:absolute; float:bottom; bottom: 10px; border-top:1px solid #ccc}
</style>
<?php
/* Payee Information */
$payee_username = $payee[0]['username'];
$date_joined = $payee[0]['date_joined'];
$payee_email = $payee[0]['email'];
$payee_mobile_no = $payee[0]['mobile_no'];
$payee_tel_no = $payee[0]['telephone_no'];
$endoser_name = $payee[0]['endorser_name'];
$curdate = date('M d, Y h:ia');
?>
<page>
    <div id="header" align="center">
        <div class="logo">&nbsp;</div>
    </div>
    <h4>Loan Completion Payout </h4>
    <table id="tbl-summary">
        <tr>
            <th width="150">Name of Payee</th>
            <td width="575"><?php echo $member_name; ?></td>
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
            <th>Date Joined</th>
            <td><?php echo $date_joined; ?></td>
        </tr>
        <tr>
            <th>Date Generated</th>
            <td><?php echo $curdate; ?></td>
        </tr>
        <tr>
            <th>Total Loan Amount</th>
            <td width="100" align="right"><?php echo number_format($amount['total_loan'], 2); ?></td>
        </tr>
        <tr>
            <th colspan="2">Deductions</th>
        </tr>
        <tr>
            <th>Total Tax Amount</th>
            <td width="100" align="right">(<?php echo number_format($amount['tax_amount'], 2); ?>)</td>
        </tr>       
        <tr>
            <th>Total Cash (80%)</th>
            <td align="right"><?php echo number_format($amount['cash'], 2); ?></td>
        </tr>
        <tr>
            <th>Total Check (20%)</th>
            <td align="right"><?php echo number_format($amount['check'], 2); ?></td>
        </tr>
         <tr>
            <th>Total Net Loan</th>
            <td width="100" align="right"><strong><?php echo number_format($amount['net_loan'], 2); ?></strong></td>
        </tr>
    </table> 
</page>
<page>
    <h4>Loan Completion Payout </h4>
    <table id="tbl-head">
        <tr>
            <th width="100">Name of Payee</th>
            <td width="250"><?php echo $member_name; ?></td>
            <th width="100">Email</th>
            <td width="250"><?php echo $payee_email; ?></td>
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
        <tr>
            <th>Date Joined</th>
            <td><?php echo $date_joined; ?></td>
            <th>Date Generated</th>
            <td><?php echo $curdate; ?></td>
        </tr>
    </table> 
    <br />
    <table width="100%" id="tbl-body">
        <tr>
            <th>&nbsp;</th>
            <th width="250">Name of Endorsed IBO</th>
            <th>Level No.</th>
            <th width="250">Place Under</th>
            <th width="200">Date Joined</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($downlines as $row) {
            ?>
            <tr>
                <td><?php echo $ctr; ?></td>
                <td><?php echo $row['member_name'] ?></td>
                <td><?php echo $level_no; ?></td>
                <td><?php echo $row['upline_name']; ?></td>
                <td><?php echo $row['date_joined']; ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
    </table>
    <br />
    <table>
        <tr>
            <th>Total Loan Amount</th>
            <td width="100" align="right"><?php echo number_format($amount['total_loan'], 2); ?></td>
        </tr>
        <tr>
            <th colspan="2">Deductions</th>
        </tr>
        <tr>
            <th>Total Tax Amount</th>
            <td width="100" align="right">(<?php echo number_format($amount['tax_amount'], 2); ?>)</td>
        </tr>       
        <tr>
            <th>Total Cash (80%)</th>
            <td align="right"><?php echo number_format($amount['cash'], 2); ?></td>
        </tr>
        <tr>
            <th>Total Check (20%)</th>
            <td align="right"><?php echo number_format($amount['check'], 2); ?></td>
        </tr>
         <tr>
            <th>Total Net Loan</th>
            <td width="100" align="right"><strong><?php echo number_format($amount['net_loan'], 2); ?></strong></td>
        </tr>
    </table>
</page>
