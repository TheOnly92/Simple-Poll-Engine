<?php if ($poll->Poll->ShowHideResults > 0) { ?>
<ul>
    <?php if ($poll->Poll->OrderResults == 0) { $loop = $poll->OrderResults; } else { $loop = $poll->Results; } ?>
    <?php foreach ($loop as $result) {?>
    <li>
        <?php echo htmlentities($result->Answer); ?> <?php echo number_format($result->Percentage*100, 2); ?>% <?php if ($poll->Poll->ShowHideResults == SHOW_RESULTS_TO_VOTER) { echo '('.number_format($result->VoteCount).' votes)'; } ?><br />
        <div class="progress">
            <div class="bar" style="width: <?php echo number_format($result->Percentage*100, 2); ?>%"></div>
        </div>
    </li>
    <?php } ?>
</ul>
<?php } else { ?>
<p>This poll has been closed</p>
<?php } ?>