<style type="text/css">
    page {font-family:Courier; font-size:11px; width:100%;}
    #tbl-body, #tbl-body td, #tbl-body th {border:1px solid #ccc; border-collapse: collapse; padding: 2px}
    #tbl-body th {background-color: #ccc;}
    .tborder {width:100%; border-bottom: 1px solid #ccc;}
    
</style>
<?php
//Get Payee Details
$payee_username = $payee[0]['username'];
$date_joined = $payee[0]['date_created'];
$payee_email = $payee[0]['email'];
$payee_mobile_no = $payee[0]['mobile_no'];
$payee_tel_no = $payee[0]['telephone_no'];
$endorser_name = $payee[0]['endorser_name'];
$curdate = date('M d, Y h:ia');                    
?>

<page>
    <h4>Group Override Commission Payout </h4>
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
            <td><?php echo $endorser_name; ?></td>
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
        foreach($downlines as $row)
        {
        ?>
        <tr>
            <td><?php echo $ctr; ?></td>
            <td><?php echo $row['member_name'] ?></td>
            <td><?php echo $row['level']; ?></td>
            <td><?php echo $row['upline_name']; ?></td>
            <td><?php echo $row['date_joined']; ?></td>
        </tr>
        <?php
        $ctr++;
        }?>
    </table>
    <br />
    <table id="tbl-body">
        <tr>
            <th>Total Loan Amount</th>
            <td width="100" align="right"><?php echo number_format($loan_amount,2); ?></td>
        </tr>
        <tr>
            <th align="right">Cash (80%)</th>
            <td align="right"><?php echo number_format($pct['cash'],2); ?></td>
        </tr>
        <tr>
            <th align="right">Check (20%)</th>
            <td align="right"><?php echo number_format($pct['check'],2); ?></td>
        </tr>
    </table>
</page>


