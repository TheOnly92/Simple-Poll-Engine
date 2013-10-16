<form method="post">
    <fieldset>
        <legend><?php echo $formTitle; ?></legend>
        <?php if (count($errors) > 0) foreach ($errors as $error) { ?>
        <div class="alert alert-error">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Error!</strong> <?php echo $error; ?>
        </div>
        <?php } ?>
        <label for="question">Question</label>
        <input type="text" placeholder="Question for your poll" id="question" name="question" class="input-block-level" value="<?php echo htmlentities($poll->Poll->Question); ?>">
        <div class="well">
            <div id="answers">
                <?php if (count($poll->Answers) > 0) {?>
                <?php foreach ($poll->OrderAnswers as $i => $answer) { ?>
                <div id="answers-<?php echo ($answer->Id) ? $answer->Id : $i; ?>"><input type="text" placeholder="Put your answers here" name="answers[<?php echo ($answer->Id) ? $answer->Id : $i; ?>]" class="input-xxlarge" value="<?php echo htmlentities($answer->Answer); ?>"> <button type="button" onclick="removeAnswer(<?php echo ($answer->Id) ? $answer->Id : $i; ?>);"><i class="icon-remove"></i></button></div>
                <?php } ?>
                <?php } else { ?>
                <div id="answers-0"><input type="text" placeholder="Put your answers here" name="answers[0]" class="input-xxlarge"> <button type="button" onclick="removeAnswer(0);"><i class="icon-remove"></i></button></div>
                <div id="answers-1"><input type="text" placeholder="Put your answers here" name="answers[1]" class="input-xxlarge"> <button type="button" onclick="removeAnswer(1);"><i class="icon-remove"></i></button></div>
                <div id="answers-2"><input type="text" placeholder="Put your answers here" name="answers[2]" class="input-xxlarge"> <button type="button" onclick="removeAnswer(2);"><i class="icon-remove"></i></button></div>
                <div id="answers-3"><input type="text" placeholder="Put your answers here" name="answers[3]" class="input-xxlarge"> <button type="button" onclick="removeAnswer(3);"><i class="icon-remove"></i></button></div>
                <?php } ?>
            </div>
            <button type="button" class="btn" onclick="addAnswer();">Add Answer</button>
        </div>
        <label class="checkbox">
            <input type="checkbox" name="randomize_order" value="yes"<?php if ($poll->Poll->RandomizeOrder) echo ' checked'; ?>>
            Randomize answer order
        </label>
        <label for="close_poll">Close Poll</label>
        <select name="close_poll" id="close_poll" onchange="checkPoll();">
            <option value="0"<?php if ($poll->Poll->CloseDate == 0) echo ' selected'; ?>>Keep Open</option>
            <option value="1"<?php if ($poll->Poll->CloseDate > 0) echo ' selected'; ?>>Close After</option>
        </select>
        <div id="datetimepicker" class="input-append date" style="display: none;">
            <input type="text" class="input-large" name="close_date" value="<?php echo date('Y-m-d H:i:s', $poll->Poll->CloseDate); ?>">
            <span class="add-on">
                <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
            </span>
        </div>
        <label>Block Repeating Votes By</label>
        <label class="radio">
            <input type="radio" name="vote_repeating" value="0"<?php if ($poll->Poll->VoteRepeating == 0) echo ' checked'; ?> onclick="checkShow();">
            Don't block
        </label>
        <label class="radio">
            <input type="radio" name="vote_repeating" value="1"<?php if ($poll->Poll->VoteRepeating == 1) echo ' checked'; ?> onclick="checkShow();">
            By cookie
        </label>
        <!-- <label class="radio">
            <input type="radio" name="vote_repeating" value="2"<?php if ($poll->Poll->VoteRepeating == 2) echo ' checked'; ?> onclick="checkShow();">
            By cookie and IP
        </label> -->
        <label class="radio">
            <input type="radio" name="vote_repeating" value="3"<?php if ($poll->Poll->VoteRepeating == 3) echo ' checked'; ?> onclick="checkShow();">
            By user ID
        </label>
        <span class="help-inline">If you choose user ID, you would have to configure it first</span>
        <div id="expire_cookie">
            <label for="cookie_expire">Cookie Expires</label>
            <select name="cookie_expire" id="cookie_expire">
                <option value="0"<?php if ($poll->Poll->CookieExpire == 0) echo ' selected'; ?>>Never</option>
                <option value="3600"<?php if ($poll->Poll->CookieExpire == 3600) echo ' selected'; ?>>1 hour</option>
                <option value="10800"<?php if ($poll->Poll->CookieExpire == 10800) echo ' selected'; ?>>3 hours</option>
                <option value="21600"<?php if ($poll->Poll->CookieExpire == 21600) echo ' selected'; ?>>6 hours</option>
                <option value="43200"<?php if ($poll->Poll->CookieExpire == 43200) echo ' selected'; ?>>12 hours</option>
                <option value="86400"<?php if ($poll->Poll->CookieExpire == 86400) echo ' selected'; ?>>1 day</option>
                <option value="604800"<?php if ($poll->Poll->CookieExpire == 604800) echo ' selected'; ?>>1 week</option>
                <option value="2419200"<?php if ($poll->Poll->CookieExpire == 2419200) echo ' selected'; ?>>1 month</option>
            </select>
        </div>
        <label>Show Results</label>
        <label class="radio">
            <input type="radio" name="show_hide_results" value="0"<?php if ($poll->Poll->ShowHideResults == 0) echo ' checked'; ?> onclick="checkShow();">
            Hide all results
        </label>
        <label class="radio">
            <input type="radio" name="show_hide_results" value="1"<?php if ($poll->Poll->ShowHideResults == 1) echo ' checked'; ?> onclick="checkShow();">
            Percentages only
        </label>
        <label class="radio">
            <input type="radio" name="show_hide_results" value="2"<?php if ($poll->Poll->ShowHideResults == 2) echo ' checked'; ?> onclick="checkShow();">
            Show results to voter
        </label>
        <div id="result_orders">
            <label for="order_results">Order results by</label>
            <select name="order_results" id="order_results">
                <option value="0"<?php if ($poll->Poll->OrderResults == 0) echo ' selected'; ?>>Position</option>
                <option value="1"<?php if ($poll->Poll->OrderResults == 1) echo ' selected'; ?>>Votes</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <a href="?c=admin&amp;a=index" class="btn">Cancel</a>
        </div>
    </fieldset>
</form>

<script>
function addAnswer() {
    answers = $("#answers input").length
    $("#answers").append('<div id="answers-'+answers+'"><input type="text" placeholder="Put your answers here" name="answers['+answers+']" class="input-xxlarge">  <button type="button" onclick="removeAnswer('+answers+');"><i class="icon-remove"></i></button></div>')
}
function checkShow() {
    if ($("input[name=show_hide_results]:checked").val() == 0) {
        $("#result_orders").hide();
    } else {
        $("#result_orders").show();
    }
    if ($("input[name=vote_repeating]:checked").val() == 1) {
        $("#expire_cookie").show();
    } else {
        $("#expire_cookie").hide();
    }
}
function removeAnswer(id) {
    $("#answers-"+id).remove();
}
function checkPoll() {
    if ($("#close_poll option:selected").val() == 0) {
        $("#datetimepicker").hide()
    } else {
        $("#datetimepicker").show()
    }
}
$(document).ready(function() {
    $("#datetimepicker").datetimepicker({
        format: 'yyyy-MM-dd hh:mm:ss'
    });
    checkShow()
})
</script>