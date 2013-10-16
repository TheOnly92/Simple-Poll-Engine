<?php

/*
 * Use this function to display a poll
 * Example: ``require_once('path to this file include.php'); echo SPE_Poll(poll ID, URL to the poll directory)``
 * Keep in mind that the URL to the poll directory is one level above this external directory.
 */
function SPE_Poll($pollId, $url, $userInteractor = null) {
    require_once(dirname(__FILE__).'/../init.php');
    $pollRepository = new DbPollRepository($pollDb, $config['database']['prefix']);
    $requestRepository = new DefaultRequestRepository();
    $pollInteractor = new PollInteractor($pollRepository, $requestRepository, $userInteractor);
    $poll = $pollInteractor->GetById($pollId);
    ob_start();
    include(dirname(__FILE__).'/../views/poll-include.php');
    include(dirname(__FILE__).'/../views/poll.php');
    if (!$poll->Closed()) {
        include(dirname(__FILE__).'/../views/poll-js.php');
    }
    $contents = ob_get_contents();
}
