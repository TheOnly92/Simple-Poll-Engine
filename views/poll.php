<div class="well well-poll-<?php echo $poll->Poll->Id; ?>">
    <h4><?php echo htmlentities($poll->Poll->Question); ?></h4>
    <?php if ($poll->Closed()) {
        include(dirname(__FILE__).'/poll-results.php');
    } else { ?>
    <div id="vote-<?php echo $poll->Poll->Id; ?>">
        <?php foreach ($poll->Answers as $answer) { ?>
        <label class="radio">
            <input type="radio" name="poll-<?php echo $poll->Poll->Id; ?>" id="poll-<?php echo $poll->Poll->Id; ?>-<?php echo $answer->Id ?>" value="<?php echo $answer->Id; ?>">
            <?php echo htmlentities($answer->Answer); ?>
        </label>
        <?php } ?>
        <button type="button" class="btn btn-primary" onclick="vote(<?php echo $poll->Poll->Id ?>);" id="button-<?php echo $poll->Poll->Id ?>">Vote</button>
        <?php if ($poll->Poll->ShowHideResults > 0) { ?>
        <button type="button" class="btn" onclick="showResults(<?php echo $poll->Poll->Id; ?>);">Results</button>
        <?php } ?>
    </div>
    <div id="result-<?php echo $poll->Poll->Id; ?>" style="display: none;">
        <div id="result-text-<?php echo $poll->Poll->Id; ?>"></div>
        <button type="button" class="btn" onclick="vote_back(<?php echo $poll->Poll->Id; ?>)">Back</button>
    </div>
    <div id="thanks-<?php echo $poll->Poll->Id; ?>" style="display: none;">
        <p>Thanks for voting!</p>
        <button type="button" class="btn" onclick="vote_back(<?php echo $poll->Poll->Id; ?>)">Back</button>
        <?php if ($poll->Poll->ShowHideResults > 0) { ?>
        <button type="button" class="btn" onclick="showResults(<?php echo $poll->Poll->Id; ?>);">Results</button>
        <?php } ?>
    </div>
    <?php } ?>
</div>