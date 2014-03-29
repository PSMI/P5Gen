<link href="css/report.css" type="text/css" rel="stylesheet" />
<page>
    <h4>Direct Endorsement Payout Summary</h4>
    <h5>Member Name: <?php echo $member_name; ?></h5>
    <table id="tbl-lists2">
        <tr>
            <th class="ctr">&nbsp;</th>
            <th class="date">Date Endorsed</th>
            <th class="name">Member Name</th>
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
                <td class="date"><?php echo $row['date_created']; ?></td>
                <td class="name"><?php echo $row['member_name']; ?></td>
          <?php /*      <td><?php echo $row['date_approved']; ?></td>
                <td><?php echo $row['approved_by']; ?></td>
                <td><?php echo $row['date_claimed']; ?></td>
                <td><?php echo $row['claimed_by']; ?></td> */ ?>
                <td class="status"><?php echo TransactionController::getStatus($row['status'], 3); ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
    </table>
</page>