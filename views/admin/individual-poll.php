<?php $active='individual-poll'; include(dirname(__FILE__).'/result-poll-tabs.php'); ?>
<table class="table">
    <thead>
        <tr>
            <th>IP Address / Identifier</th>
            <th>Date</th>
            <th>Answer</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($votes as $vote) { ?>
            <tr>
                <td><?php echo $vote->Vote->VotedIp; ?><?php if ($vote->Vote->Identifier) { echo ' ('.htmlentities($vote->Vote->Identifier).')'; } ?></td>
                <td><?php echo date('Y-m-d H:i:s', $vote->Vote->VotedDate); ?></td>
                <td><?php echo htmlentities($vote->Answer); ?></td>
                <td><a href="#" onclick="if (confirm('Are you sure you want to delete this vote?')) { window.location='?c=admin&a=delete-vote&id=<?php echo $vote->Vote->Id; ?>&pollId=<?php echo $vote->Vote->PollId; ?>&page=<?php echo $_GET['page']; ?>' }; return false;" title="Delete Vote"><i class="icon-remove"></i></a></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<div class="pagination">
    <ul>
        <li<?php if ($_GET['page'] <= 1) { ?> class="disabled"<?php } ?>><a href="<?php if ($_GET['page'] <= 1) {?>#<?php } else { ?>?c=admin&amp;a=individual-poll&amp;id=<?php echo $poll->Poll->Id; ?>&amp;page=<?php echo $_GET['page']-1; ?><?php } ?>">&laquo; Previous</a></li>
        <li<?php if (count($votes) < 25) {?> class="disabled"<?php } ?>><a href="<?php if (count($votes) < 25) {?>#<?php } else { ?>?c=admin&amp;a=individual-poll&amp;id=<?php echo $poll->Poll->Id; ?>&amp;page=<?php echo $_GET['page']+1; ?><?php } ?>">Next &raquo;</a></li>
    </ul>
</div>