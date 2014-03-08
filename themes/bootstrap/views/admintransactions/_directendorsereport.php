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
        height: 105px;
        background: url(images/logo.png) 0 top center no-repeat #fff;
    }
    .address{font-size:8px;padding-top:4px;}
    #footer{padding-top:10px; position:absolute; float:bottom; bottom: 10px; border-top:1px solid #ccc}
</style>
<?php
/* Payee Information */
$payee_name = $payee['last_name'] . ', ' . $payee['middle_name'] . ' ' . $payee['first_name'];
$payee_username = $payee['username'];
$payee_email = $payee['email'];
$payee_mobile_no = $payee['mobile_no'];
$payee_tel_no = $payee['telephone_no'];

$endoser_name = $endorser['last_name'] . ', ' . $endorser['middle_name'] . ' ' . $endorser['first_name'];
$cutoff_date = $cutoff['cutoff_date'];
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
    <h4>Direct Endorsement Payout Summary</h4>
    <table id="tbl-summary">
        <tr>
            <th width="150">Name of Payee</th>
            <td width="575"><?php echo $payee_name; ?></td>
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
            <th>Cut-Off Date</th>
            <td><?php echo $cutoff_date; ?></td>
        </tr>
        <tr>
            <th>Total IBO</th>
            <td align="right"><?php echo number_format($total['total_ibo'], 0); ?></td>
        </tr>
        <tr>
            <th>Total Payout</th>
            <td align="right"><?php echo number_format($total['total_amount'], 2); ?></td>
        </tr>        
        <tr>
            <th colspan="2">Deductions</th>
        </tr>
        <tr>
            <th>Tax Withheld</th>
            <td align="right">(<?php echo number_format($total['tax_amount'], 2); ?>)</td>
        </tr>
        <tr>
            <th>Net Amount</th>
            <td align="right"><strong><?php echo number_format($total['net_amount'], 2); ?></strong></td>
        </tr>
    </table> 
    <div id="footer">
        <div class="slogan" align="center">�Finding ways in helping others is our top priority.�</div>
    </div>
</page>
<page>
    <h4>Direct Endorsement Payout </h4>
    <table>
        <tr>
            <th width="100">Name of Payee</th>
            <td width="250"><?php echo $payee_name; ?></td>
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
            <th>Cut-Off Date</th>
            <td><?php echo $cutoff_date; ?></td>
            <th>Date Generated</th>
            <td><?php echo $curdate; ?></td>
        </tr>
    </table> 
    <br />
    <table width="100%">
        <tr>
            <th>&nbsp;</th>
            <th width="250">Name of Endorsed IBO</th>
            <th width="250">Place Under</th>
            <th width="200">Date Joined</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($endorsee as $row) {
            ?>
            <tr>
                <td><?php echo $ctr; ?></td>
                <td><?php echo $row['member_name'] ?></td>
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
            <th>Total IBO</th>
            <td width="100" align="right"><?php echo number_format($total['total_ibo'], 0); ?></td>
        </tr>
        <tr>
            <th>Total Payout</th>
            <td width="100" align="right"><?php echo number_format($total['total_amount'], 2); ?></td>
        </tr>        
        <tr>
            <th colspan="2">Deductions</th>
        </tr>
        <tr>
            <th>Tax Withheld</th>
            <td width="100" align="right">(<?php echo number_format($total['tax_amount'], 2); ?>)</td>
        </tr>
        <tr>
            <th>Net Amount</th>
            <td width="100" align="right"><strong><?php echo number_format($total['net_amount'], 2); ?></strong></td>
        </tr>
    </table>
</page>
