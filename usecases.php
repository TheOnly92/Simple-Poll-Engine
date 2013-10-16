<?php
/*
 * This file defines the usecases of this application.
 */

class UsecasesPoll {
    public $Poll;
    public $Answers = array();
    public $OrderAnswers = array();
    public $Votes = array();
    public $Results = array();
    public $OrderResults = array();
    public $NumOfVotes = 0;

    public function Closed() {
        return $this->Poll->Closed || $this->Poll->CloseDate > 0 && $this->Poll->CloseDate < time();
    }
}

class UsecasesPollResult {
    public $AnswerId;
    public $Answer;
    public $VoteCount;
    public $Percentage;
}

class UsecasesPollVote {
    public $Vote;
    public $Answer;
}

class PollInteractor implements PollInteractorInterface {
    private $pollRepository;
    private $userRepository;
    private $requestRepository;

    public function __construct(PollRepository $pollRepository, RequestRepository $requestRepository, $userRepository = null) {
        $this->pollRepository = $pollRepository;
        $this->requestRepository = $requestRepository;
        if ($userRepository != null) {
            if (!($userRepository instanceof UserRepository)) {
                throw new Exception("User repository must implement the interface UserRepository!");
            } else {
                $this->userRepository = $userRepository;
            }
        }
    }

    public function GetAll($page) {
        $polls = $this->pollRepository->GetAll(25, ($page-1)*25);
        $rt = array();
        foreach ($polls as $poll) {
            $rt1 = new UsecasesPoll();
            $rt1->Poll = $poll;
            $rt1->NumOfVotes = $this->pollRepository->GetNumofVotes($poll->Id);
            $rt[] = $rt1;
        }
        return $rt;
    }

    public function GetLast5() {
        $polls = $this->pollRepository->GetAll(5, 0);
        $rt = array();
        foreach ($polls as $poll) {
            $rt1 = new UsecasesPoll();
            $rt1->Poll = $poll;
            $rt1->Answers = $this->pollRepository->GetAnswersById($poll->Id, $poll->RandomizeOrder);
            $rt1->OrderAnswers = $this->pollRepository->GetAnswersById($poll->Id, false);
            $rt1->NumOfVotes = $this->pollRepository->GetNumofVotes($poll->Id);
            $rt1Results = $this->pollRepository->GetPollResults($poll->Id);
            foreach ($rt1Results as $result) {
                $tmp = new UsecasesPollResult();
                $tmp->AnswerId = $result['answer_id'];
                foreach ($rt1->Answers as $answer) {
                    if ($answer->Id == $result['answer_id']) {
                        $tmp->Answer = $answer->Answer;
                        break;
                    }
                }
                $tmp->VoteCount = $result['total'];
                $tmp->Percentage = $tmp->VoteCount / $rt1->NumOfVotes;
                $rt1->Results[] = $tmp;
            }
            foreach ($rt1->OrderAnswers as $answer) {
                $tmp = new UsecasesPollResult();
                $tmp->AnswerId = $answer->Id;
                $tmp->Answer = $answer->Answer;
                foreach ($rt1Results as $result) {
                    if ($result['answer_id'] == $answer->Id) {
                        $tmp->VoteCount = $result['total'];
                        $tmp->Percentage = $tmp->VoteCount / $rt1->NumOfVotes;
                        break;
                    }
                }
                $rt1->OrderResults[] = $tmp;
            }
            $rt[] = $rt1;
        }
        return $rt;
    }

    public function GetById($id) {
        $poll = new UsecasesPoll();
        $poll->Poll = $this->pollRepository->GetById($id);
        $poll->Answers = $this->pollRepository->GetAnswersById($id, $poll->Poll->RandomizeOrder);
        $poll->OrderAnswers = $this->pollRepository->GetAnswersById($id, false);
        $poll->NumOfVotes = $this->pollRepository->GetNumofVotes($id);
        $pollResults = $this->pollRepository->GetPollResults($id);
        foreach ($pollResults as $result) {
            $rt = new UsecasesPollResult();
            $rt->AnswerId = $result['answer_id'];
            foreach ($poll->Answers as $answer) {
                if ($answer->Id == $result['answer_id']) {
                    $rt->Answer = $answer->Answer;
                    break;
                }
            }
            $rt->VoteCount = $result['total'];
            $rt->Percentage = $rt->VoteCount / $poll->NumOfVotes;
            $poll->Results[] = $rt;
        }
        foreach ($poll->OrderAnswers as $answer) {
            $rt = new UsecasesPollResult();
            $rt->AnswerId = $answer->Id;
            $rt->Answer = $answer->Answer;
            foreach ($pollResults as $result) {
                if ($result['answer_id'] == $answer->Id) {
                    $rt->VoteCount = $result['total'];
                    $rt->Percentage = $rt->VoteCount / $poll->NumOfVotes;
                    break;
                }
            }
            $poll->OrderResults[] = $rt;
        }
        return $poll;
    }

    public function GetIndividualVotes($id, $page) {
        $tmp = $this->pollRepository->GetAnswersById($id, false);
        $answers = array();
        foreach ($tmp as $answer) {
            $answers[$answer->Id] = $answer->Answer;
        }
        $votes = $this->pollRepository->GetVotes($id, 25, ($page-1)*25);
        $rt = array();
        foreach ($votes as $vote) {
            $t = new UsecasesPollVote();
            $t->Vote = $vote;
            $t->Answer = $answers[$vote->AnswerId];
            $rt[] = $t;
        }
        return $rt;
    }

    public function CanVote($poll) {
        if ($poll->Closed || $poll->CloseDate > 0 && $poll->CloseDate < time()) {
            return false;
        }
        if ($poll->VoteRepeating == ALLOW_REPEAT) {
            return true;
        }
        if ($poll->VoteRepeating == BLOCK_BY_COOKIE) {
            if (isset($_COOKIE['poll'+$poll->Id])) {
                return false;
            }
        }
        if ($poll->VoteRepeating == BLOCK_BY_USERID) {
            if (!$this->userRepository) {
                throw new Exception('User Repository not configured yet!');
            }
            if ($this->pollRepository->CheckIfVoted($poll->Id, $this->userRepository->GetIdentifier())) {
                return false;
            }
        }
        return true;
    }

    public function CastVote($id, $vote) {
        $poll = $this->pollRepository->GetById($id);
        if (!$this->CanVote($poll)) {
            return false;
        }
        if (!$this->pollRepository->AnswerBelongsToVote($id, $vote)) {
            return false;
        }
        $saveVote = new DomainPollVote();
        $saveVote->PollId = $poll->Id;
        $saveVote->AnswerId = $vote;
        if ($this->userRepository) {
            $saveVote->Identifier = $this->userRepository->GetIdentifier();
        }
        $saveVote->VotedDate = time();
        $saveVote->VotedIp = $this->requestRepository->GetIP();
        $this->pollRepository->StoreVote($saveVote);
        if ($poll->VoteRepeating == BLOCK_BY_COOKIE) {
            setcookie('poll'+$poll->Id, 'voted', ($poll->CookieExpire > 0) ? time() + $poll->CookieExpire : time() + 10*365*24*60*60);
        }
        return true;
    }

    public function Create($poll) {
        $poll->Poll = $this->pollRepository->Store($poll->Poll);
        foreach ($poll->Answers as $answer) {
            $answer->PollId = $poll->Poll->Id;
            $this->pollRepository->StoreAnswer($answer);
        }
        return array();
    }

    public function Open($id) {
        $poll = $this->pollRepository->GetById($id);
        $poll->Closed = false;
        $poll->CloseDate = 0;
        $this->pollRepository->Store($poll);
    }

    public function Close($id) {
        $poll = $this->pollRepository->GetById($id);
        $poll->Closed = true;
        $this->pollRepository->Store($poll);
    }

    public function Delete($id) {
        $this->pollRepository->Delete($id);
    }

    public function DeleteVote($id, $pollId) {
        $this->pollRepository->DeleteVote($id, $pollId);
    }

    public function Edit($poll) {
        $this->pollRepository->Store($poll->Poll);
        foreach ($poll->Answers as $answer) {
            $this->pollRepository->StoreAnswer($answer);
        }
        return array();
    }
}

class AdminInteractor implements AdminInteractorInterface {
    private $config;

    public function __construct($config) {
        session_start();
        $this->config = $config;
    }

    public function IsAuthenticated() {
        if (isset($_SESSION['poll_admin_authenticated']) && $_SESSION['poll_admin_authenticated'] == 'yes') {
            return true;
        }
        return false;
    }

    public function Authenticate($username, $password) {
        if ($username == $this->config['username'] && $password == $this->config['password']) {
            $_SESSION['poll_admin_authenticated'] = 'yes';
            return true;
        }
        return false;
    }

    public function Unauthenticate() {
        $_SESSION['poll_admin_authenticated'] = false;
    }
}
