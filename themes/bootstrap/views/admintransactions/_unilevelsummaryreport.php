<link href="css/report.css" type="text/css" rel="stylesheet" />
<page>
    <div id="header" align="center">
        <div class="logo2">&nbsp;</div>
    </div>
    <h4>Unilevel Payout Summary</h4>
    <h5>Cutoff Date: <?php echo $cutoff_unilvl; ?></h5>
    <table id="tbl-lists2">
        <tr>
            <th>&nbsp;</th>
            <th>Member Name</th>
            <th>IBO Count</th>
            <th>Amount</th>
            <th>Date Approved</th>
            <th>Approved By</th>
            <th>Date Claimed</th>
            <th>Processed By</th>
            <th>Status</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($unilvl_details as $row) {
            ?>
            <tr>
                <td><?php echo $ctr; ?></td>
                <td><?php echo $row['member_name']; ?></td>
                <td><?php echo $row['ibo_count']; ?></td>
                <td><?php echo AdmintransactionsController::numberFormat($row['amount']); ?></td>
                <td><?php echo $row['date_approved']; ?></td>
                <td><?php echo $row['approved_by']; ?></td>
                <td><?php echo $row['date_claimed']; ?></td>
                <td><?php echo $row['claimed_by']; ?></td>
                <td><?php echo AdmintransactionsController::getStatus($row['status'], 2); ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
        <tr>
            <td></td>
            <th>Total Payout</th>
            <td><strong><?php echo number_format($total_unilvl_ibo, 0); ?></strong></td>
            <td><strong><?php echo AdmintransactionsController::numberFormat($total_unilvl); ?></strong></td>
        </tr>
    </table>
</page>