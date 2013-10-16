<h3><?php echo htmlentities($poll->Poll->Question); ?></h3>
<p><a href="?c=admin">Back to index</a></p>
<ul class="nav nav-tabs">
    <li<?php if ($active == 'result-poll') { ?> class="active"<?php } ?>><a href="?c=admin&amp;a=result-poll&amp;id=<?php echo $poll->Poll->Id; ?>"><i class="icon-list"></i> Results</a></li>
    <li<?php if ($active == 'individual-poll') { ?> class="active"<?php } ?>><a href="?c=admin&amp;a=individual-poll&amp;id=<?php echo $poll->Poll->Id; ?>"><i class="icon-user"></i> Individual Votes</a></li>
</ul>