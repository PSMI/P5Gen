<link href="css/report.css" type="text/css" rel="stylesheet" />
<page>
    <h4>Direct Endorsement Payout Summary</h4>
    <h5>Cutoff Date: <?php echo $cutoff_direct; ?></h5>
    <table id="tbl-lists2">
        <tr>
            <th class="ctr">&nbsp;</th>
            <th class="name">Endorser Name</th>
            <th class="total">IBO Count</th>
            <th class="amount">Total Payout</th>
<!--            <th>Date Approved</th>
            <th>Approved By</th>
            <th>Date Claimed</th>
            <th>Processed By</th>-->
            <th class="status">Status</th>
        </tr>
        <?php
        $ctr = 1;
        foreach ($direct_details as $row) {
            ?>
            <tr>
                <td class="ctr"><?php echo $ctr; ?></td>
                <td class="name"><?php echo $row['endorser_name']; ?></td>
                <td class="total"><?php echo $row['ibo_count']; ?></td>
                <td class="amount"><?php echo $row['total_payout']; ?></td>
           <?php /*     <td><?php echo $row['date_approved']; ?></td>
                <td><?php echo $row['approved_by']; ?></td>
                <td><?php echo $row['date_claimed']; ?></td>
                <td><?php echo $row['claimed_by']; ?></td> */ ?>
                <td class="status"><?php echo AdmintransactionsController::getStatus($row['status'], 3); ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
        <tr>
            <th align="center" colspan="2">Total Payout</th>
            <td class="total"><strong><?php echo number_format($total_direct_ibo, 0); ?></strong></td>
            <td class="amount"><strong><?php echo AdmintransactionsController::numberFormat($total_direct); ?></strong></td>
            <td>&nbsp;</td>
        </tr>
    </table>
</page>