<link href="css/report.css" type="text/css" rel="stylesheet" />
<?php
//Get Payee Details
$payee_username = $payee[0]['username'];
$date_joined = $payee[0]['date_joined'];
$payee_email = $payee[0]['email'];
$payee_mobile_no = $payee[0]['mobile_no'];
$payee_tel_no = $payee[0]['telephone_no'];
$endorser_name = $payee[0]['endorser_name'];
$curdate = date('M d, Y h:ia');
?>
<page>
    <div id="header" align="center">
        <div class="logo">&nbsp;</div>
        <p class="address">Unit 6 2nd Flr. Maclane Centre, Nat’l Hi-way<br />
        San Antonio, San Pedro, Laguna<br />
        www.p5partners.com<br />
        (02)553-68-19
        </p>
    </div>
    <h4>Group Override Commission Payout Summary</h4>
    <table id="tbl-summary">
        <tr>
            <th>Name of Payee</th>
            <td><?php echo $member_name; ?></td>
        </tr>
        <tr>
            <th>Endorser Name</th>
            <td><?php echo $endorser_name; ?></td>
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
            <th>Total IBO</th>
            <td align="right"><?php echo number_format($ibo_count, 0); ?></td>
        </tr>
        <tr>
            <th>Total GOC Amount</th>
            <td align="right"><?php echo number_format($amount['total_commission'], 2); ?></td>
        </tr>
        <tr>
            <th colspan="2">Deductions</th>
        </tr>
        <tr>
            <th>Loan Grant</th>
            <td align="right">(<?php echo number_format($amount['previous_loan'], 2); ?>)</td>
        </tr>
        <tr>
            <th>Loan Balance</th>
            <td align="right">(<?php echo number_format($loan_balance, 2); ?>)</td>
        </tr>
        <tr>
            <th>Total Tax Withheld</th>
            <td  align="right">(<?php echo number_format($amount['tax'], 2); ?>)</td>
        </tr>
        <tr>
            <th>Total Cash (<?php echo $amount['cash_pct']; ?>%)</th>
            <td align="right"><?php echo number_format($amount['cash'], 2); ?></td>
        </tr>
        <tr>
            <th>Total Check (<?php echo $amount['check_pct']; ?>%)</th>
            <td align="right"><?php echo number_format($amount['check'], 2); ?></td>
        </tr>
        <tr>
            <th>Total Net Commission</th>
            <td align="right"><strong><?php echo number_format($amount['net_commission'], 2); ?></strong></td>
        </tr>
    </table> 
    <br />
    <table id="tbl-signature">
        <tr>
            <th>Released By</th>
            <td>&nbsp;</td>
            <th>Received By</th>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <th>Date Released</th>
            <td>&nbsp;</td>
            <th>Date Received</th>
            <td>&nbsp;</td>
        </tr>
    </table>
    <div id="footer">
        <div class="slogan" align="center">“Finding ways in helping others is our top priority.”</div>
    </div>
</page>
<page>
    <h4>Group Override Commission Payout </h4>
    <table id="tbl-details">
        <tr>
            <th>Name of Payee</th>
            <td><?php echo $member_name; ?></td>
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
    <table id="tbl-lists3">
        <tr>
            <th class="ctr">&nbsp;</th>
            <th class="ctr">Lvl</th>
            <th class="name">Name of Endorsed IBO</th>
            <th class="name">Place Under</th>
            <th class="date">Date Joined</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($downlines as $row) {
            ?>
            <tr>
                <td class="ctr"><?php echo $ctr; ?></td>
                <td class="ctr"><?php echo $row['level']; ?></td>
                <td class="name"><?php echo $row['member_name'] ?></td>
                <td class="name"><?php echo $row['upline_name']; ?></td>
                <td class="date"><?php echo $row['date_joined']; ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
    </table>
    <br />
    <table id="tbl-details">
        <tr>
            <th>Total IBO</th>
            <td width="100" align="right"><?php echo number_format($ibo_count, 0); ?></td>
        </tr>
        <tr>
            <th>Total GOC Amount</th>
            <td width="100" align="right"><?php echo number_format($amount['total_commission'], 2); ?></td>
        </tr>
        <tr>
            <th colspan="2">Deductions</th>
        </tr>
        <tr>
            <th>Loan Grant</th>
            <td width="100" align="right">(<?php echo number_format($amount['previous_loan'], 2); ?>)</td>
        </tr>
        <tr>
            <th>Loan Balance</th>
            <td align="right">(<?php echo number_format($loan_balance, 2); ?>)</td>
        </tr>
        <tr>
            <th>Total Tax Withheld</th>
            <td width="100" align="right">(<?php echo number_format($amount['tax'], 2); ?>)</td>
        </tr>
        <tr>
            <th>Total Cash (<?php echo $amount['cash_pct']; ?>%)</th>
            <td align="right"><?php echo number_format($amount['cash'], 2); ?></td>
        </tr>
        <tr>
            <th>Total Check (<?php echo $amount['check_pct']; ?>%)</th>
            <td align="right"><?php echo number_format($amount['check'], 2); ?></td>
        </tr>
        <tr>
            <th>Total Net Commission</th>
            <td align="right"><strong><?php echo number_format($amount['net_commission'], 2); ?></strong></td>
        </tr>
    </table>
</page>


