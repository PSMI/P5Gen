<link href="css/report.css" type="text/css" rel="stylesheet" />
<page>
    <h4>Distributor Retention Money Summary</h4>
    <h5>Member Name: <?php echo $member_name; ?></h5>
    <table id="tbl-lists2">
        <tr>
            <th class="ctr">&nbsp;</th>
            <th class="name">Distributor Name</th>
            <th>Total Retention</th>           
        </tr>
        <?php
        $ctr = 1;
        foreach ($direct_details as $row) {
            ?>
            <tr>
                <td><?php echo $ctr; ?></td>
                <td><?php echo $row['member_name']; ?></td>
                <td><?php echo $row['savings']; ?></td>
            </tr>
            <?php
            $ctr++;
        }
        ?>
        <tr>
            <th class="date" colspan="2">Total</th>
            <td><?php echo $total['total_savings']; ?></td>
        </tr>
    </table>
</page>