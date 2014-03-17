<link href="css/report.css" type="text/css" rel="stylesheet" />
<page>
    <div id="header" align="center">
        <div class="logo2">&nbsp;</div>
    </div>
    <h4>Loan Completion Payout </h4>
    <table id="tbl-lists2">
        <tr>
            <th>&nbsp;</th>
            <th>Member Name</th>
            <th>Type</th>
            <th>Level</th>
            <th>Amount</th>
            <th>Date Completed</th>
            <th>Date Approved</th>
            <th>Approved By</th>
            <th>Date Claimed</th>
            <th>Processed By</th>
            <th>Status</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($loan_details as $row) {
            ?>
            <tr>
                <td><?php echo $ctr; ?></td>
                <td><?php echo $row['member_name']; ?></td>
                <td><?php echo $row['loan_type_id'] == 1 ? "Direct" : "Completion" ?></td>
                <td><?php echo $row['loan_type_id'] == 1 ? "" : $row['level_no']; ?></td>
                <td><?php echo AdmintransactionsController::numberFormat($row['loan_amount']); ?></td>
                <td><?php echo $row['date_completed']; ?></td>
                <td><?php echo $row['date_approved']; ?></td>
                <td><?php echo $row['approved_by']; ?></td>
                <td><?php echo $row['date_claimed']; ?></td>
                <td><?php echo $row['claimed_by']; ?></td>
                <td><?php echo AdmintransactionsController::getStatus($row['status'], 1); ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
        <tr>
            <th>Total</th>
            <td><?php echo AdmintransactionsController::numberFormat($total); ?></td>
        </tr>
    </table>
</page>