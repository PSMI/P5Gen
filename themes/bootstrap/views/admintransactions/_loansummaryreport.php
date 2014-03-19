<link href="css/report.css" type="text/css" rel="stylesheet" />
<page>
    <h4>Loan Payout Summary</h4>
    <table id="tbl-lists2">
        <tr>
            <th class="ctr">&nbsp;</th>
            <th class="name">Member Name</th>
            <th class="name">Type</th>
            <th class="ctr">Lvl</th>
            <th class="amount">Amount</th>
<!--            <th>Date Completed</th>
            <th>Date Approved</th>
            <th>Approved By</th>
            <th>Date Claimed</th>
            <th>Processed By</th>-->
            <th class="date">Status</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($loan_details as $row) {
            ?>
            <tr>
                <td class="ctr"><?php echo $ctr; ?></td>
                <td class="name"><?php echo $row['member_name']; ?></td>
                <td class="name"><?php echo $row['loan_type_id'] == 1 ? "Direct" : "Completion" ?></td>
                <td class="ctr"><?php echo $row['loan_type_id'] == 1 ? "" : $row['level_no']; ?></td>
                <td class="amount" align="right"><?php echo AdmintransactionsController::numberFormat($row['loan_amount']); ?></td>
<?php /*                <td><?php echo $row['date_completed']; ?></td>
                <td><?php echo $row['date_approved']; ?></td>
                <td><?php echo $row['approved_by']; ?></td>
                <td><?php echo $row['date_claimed']; ?></td>
                <td><?php echo $row['claimed_by']; ?></td> */ ?>
                <td class="date"><?php echo AdmintransactionsController::getStatus($row['status'], 1); ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
        <tr>
            <th align="center" colspan="4">Total Loans</th>
            <td align="right"><strong><?php echo AdmintransactionsController::numberFormat($total); ?></strong></td>
            <td>&nbsp;</td>
        </tr>
    </table>
</page>