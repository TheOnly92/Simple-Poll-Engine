<?php $active='result-poll'; include(dirname(__FILE__).'/result-poll-tabs.php'); ?>
<div style="float: right;" class="btn-group">
    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
        Export
        <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
        <li><a href="?c=admin&amp;a=export-poll&amp;format=csv&amp;id=<?php echo $poll->Poll->Id; ?>">CSV</a></li>
    </ul>
</div>
<p>Total Votes: <?php echo number_format($poll->NumOfVotes); ?></p>
<div class="row">
    <div class="span3">
        <div id="poll-result"></div>
    </div>
    <div class="span4">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th>Count</th>
                    <th>Percent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($poll->Results as $i => $result) {?>
                <tr>
                    <td><?php echo htmlentities($result->Answer); ?></td>
                    <td style="text-align: right;"><?php echo number_format($result->VoteCount); ?></td>
                    <td style="text-align: right;"><?php echo number_format($result->Percentage*100, 2); ?> %</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#poll-result").highcharts({
        title: {
            text: null
        },
        chart: {
            animation: false
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                animation: false,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            type: 'pie',
            name: 'Poll Result',
            data: [
            <?php foreach ($poll->Results as $i => $result) { ?>
                ['<?php echo addslashes($result->Answer); ?>', <?php echo $result->VoteCount; ?>]<?php if ($i < count($poll->Results)-1) {?>,<?php } ?>
            <?php } ?>
            ]
        }]
    })
})
</script>