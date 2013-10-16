<a href="?c=admin&amp;a=new-poll" class="btn">Create Poll</a>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Poll</th>
            <th>Votes</th>
            <th>Created</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($polls)) { ?>
        <?php foreach ($polls as $poll) { ?>
        <tr>
            <td><?php echo htmlentities($poll->Poll->Question); ?><?php if ($poll->Closed()) { echo ' <small>Closed</small>'; } ?></td>
            <td><?php echo $poll->NumOfVotes; ?> Votes</td>
            <td><?php echo date('Y-m-d', $poll->Poll->Created); ?></td>
            <td>
                <a href="#" onclick="embed(<?php echo $poll->Poll->Id; ?>)" title="Embed Poll"><i class="icon-bullhorn"></i></a>
                <a href="?c=admin&amp;a=edit-poll&amp;id=<?php echo $poll->Poll->Id; ?>" title="Edit Poll"><i class="icon-pencil"></i></a>
                <a href="#" onclick="if (confirm('Are you sure you want to remove this poll?')) {window.location='?c=admin&amp;a=delete-poll&amp;id=<?php echo $poll->Poll->Id; ?>'} return false;" title="Delete Poll"><i class="icon-remove"></i></a>
                <a href="?c=admin&amp;a=result-poll&amp;id=<?php echo $poll->Poll->Id; ?>" title="View Poll Result"><i class="icon-eye-open"></i></a>
                <?php if ($poll->Closed()) { ?>
                <a href="?c=admin&amp;a=open-poll&amp;id=<?php echo $poll->Poll->Id; ?>" title="Open Poll"><i class="icon-ok-circle"></i></a>
                <?php } else { ?>
                <a href="?c=admin&amp;a=close-poll&amp;id=<?php echo $poll->Poll->Id; ?>" title="Close Poll"><i class="icon-ban-circle"></i></a>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr>
            <td colspan="3">No polls created yet</td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<div class="modal hide" role="dialog" id="embed-modal">
    <div class="modal-body">
        <ul class="nav nav-tabs" id="embed-tab">
            <li class="active"><a data-toggle="tab" href="#php">PHP</a></li>
            <li><a data-toggle="tab" href="#direct">Direct Link</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="php">
                <input type="text" id="embed-php" class="input-block-level">
            </div>
            <div class="tab-pane" id="direct">
                <input type="text" id="embed-direct" class="input-block-level">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" onclick="$('#embed-modal').modal('hide')">Close</button>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#embed-tab a").click(function(e) {
        e.preventDefault()
        $(this).tab('show');
    })
})

function embed(id) {
    path = '<?php echo realpath(POLL_SCRIPT_ROOT.'/external/').'/include.php'; ?>';
    url = window.location.protocol + "//" + window.location.host + window.location.pathname.replace(/index\.php/, '')
    $("#embed-php").val("require_once('"+path+"'); echo SPE_Poll("+id+", '"+url+"');")
    $("#embed-direct").val(url+"?id="+id)
    $("#embed-modal").modal('show')
}
</script>