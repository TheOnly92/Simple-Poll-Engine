<?php
/*
 * This file defines the domain of this application
 */

define('ALLOW_REPEAT', 0);
define('BLOCK_BY_COOKIE', 1);
define('BLOCK_BY_COOKIE_IP', 2);
define('BLOCK_BY_USERID', 3);

define('HIDE_RESULTS', 0);
define('PERCENTAGES_ONLY', 1);
define('SHOW_RESULTS_TO_VOTER', 2);

define('SORT_NORMAL', 0);
define('SORT_RESULT', 1);

class DomainPoll {
    public $Id;
    public $Question;
    public $CloseDate = 0;
    public $VoteRepeating = 1;
    public $CookieExpire = 0;
    public $ShowHideResults = 0;
    public $OrderResults = 0;
    public $Created;
    public $RandomizeOrder = false;
    public $Closed = false;
}

class DomainPollAnswer {
    public $Id;
    public $PollId;
    public $Answer;
    public $Order;
    public $Delete = false;
    public $Image;
}

class DomainPollVote {
    public $Id;
    public $PollId;
    public $Identifier = '';
    public $VotedDate;
    public $VotedIp;
    public $AnswerId;
}

class DomainPollImage {
    public $Id;
    public $UploadPath;
}

interface PollRepository {
    public function Store(DomainPoll $poll);
    public function GetById($id);
    public function GetAll($limit, $offset);
    public function GetAnswersById($id,$randomize);
    public function Delete($id);
    public function StoreAnswer(DomainPollAnswer $answer);
    public function GetNumofVotes($id);
    public function CheckIfVoted($id, $identifier);
    public function AnswerBelongsToVote($id, $answer);
    public function StoreVote(DomainPollVote $vote);
    public function GetVotes($id, $limit, $offset);
    public function DeleteVote($id, $pollId);
}

interface UserRepository {
    // Returns the identifier (username or user ID), retrieve it through $_SESSION, $_COOKIE or whatsoever
    public function GetIdentifier();
}

interface RequestRepository {
    // Overwrite with your own if you need to handle special cases (x-forwarded-for, etc...)
    public function GetIP();
}

interface PollInteractorInterface {
    public function GetAll($page);
    public function Create($poll);
    public function Delete($id);
    public function Edit($poll);
    public function GetLast5();
    public function CanVote($id);
    public function CastVote($id, $vote);
    public function Open($id);
    public function Close($id);
    public function GetIndividualVotes($id, $page);
    public function DeleteVote($id, $pollId);
}

interface AdminInteractorInterface {
    // Checks if authenticated for the admin interface, returns true if yes, false if no
    public function IsAuthenticated();

    // Authenticates with $username and $password, returns true if successful, false if failure
    public function Authenticate($username, $password);

    // Logs out the admin
    public function Unauthenticate();
}
