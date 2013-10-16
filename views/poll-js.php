<script>
function vote(id) {
    if ($("input[name=poll-"+id+"]:checked").length == 0) {
        return;
    }
    $("#button-"+id).attr("disabled", true);
    $.ajax('<?php if (isset($url)) echo $url; ?>/index.php?c=index&a=vote', {
        data: {id: id, vote: $("input[name=poll-"+id+"]:checked").val()},
        dataType: 'json',
        error: function() {
            $("#button-"+id).attr("disabled", false)
            alert('Failed to cast vote!');
        },
        success: function(data) {
            if (data != true) {
                $("#result-text-"+id).html(data)
                $("#vote-"+id).hide()
                $("#result-"+id).show()
            } else {
                $("#vote-"+id).hide();
                $("#thanks-"+id).show();
            }
        }
    })
}
function vote_back(id) {
    $("#thanks-"+id).hide()
    $("#result-"+id).hide()
    $("#vote-"+id).show()
}
function showResults(id) {
    $.ajax('<?php if (isset($url)) echo $url; ?>/index.php?c=index&a=show-results&id='+id, {
        dataType: 'html',
        success: function(data) {
            $("#result-text-"+id).html(data)
            $("#vote-"+id).hide()
            $("#thanks-"+id).hide()
            $("#result-"+id).show()
        }
    })
}
</script>