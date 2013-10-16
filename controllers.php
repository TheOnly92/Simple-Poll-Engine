<?php

class IndexHandler {
    private $pollInteractor;

    public function __construct(PollInteractorInterface $pollInteractor) {
        $this->pollInteractor = $pollInteractor;
    }

    public function Route($action) {
        switch ($action) {
            case 'vote':
                $this->Vote();
                break;
            case 'show-results':
                $this->ShowResults();
                break;
            case 'index':
            default:
                $this->Index();
                break;
        }
    }

    public function Vote() {
        if (!$this->pollInteractor->CastVote($_GET['id'], $_GET['vote'])) {
            echo json_encode('You have already voted on this poll!');
            return;
        }
        echo json_encode(true);
    }

    public function Index() {
        if (!isset($_GET['id']) || !$_GET['id']) {
            $polls = $this->pollInteractor->GetLast5();
            $templateTitle = 'Polls';
            $templateFile = 'index/index.php';
            require_once(POLL_SCRIPT_ROOT.'/views/layout.php');
        } else {
            $poll = $this->pollInteractor->GetById($_GET['id']);
            include(POLL_SCRIPT_ROOT.'/views/poll-include.php');
            include(POLL_SCRIPT_ROOT.'/views/poll.php');
            if (!$poll->Closed()) {
                include(POLL_SCRIPT_ROOT.'/views/poll-js.php');
            }
        }
    }

    public function ShowResults() {
        $poll = $this->pollInteractor->GetById($_GET['id']);
        if ($poll->Poll->ShowHideResults == HIDE_RESULTS) {
            return false;
        }
        require_once(POLL_SCRIPT_ROOT.'/views/poll-results.php');
    }
}

class AdminHandler {
    private $pollInteractor;
    private $adminInteractor;

    public function __construct(PollInteractorInterface $pollInteractor, AdminInteractorInterface $adminInteractor) {
        $this->pollInteractor = $pollInteractor;
        $this->adminInteractor = $adminInteractor;
    }

    public function Route($action) {
        if ($action != 'login') {
            if (!$this->adminInteractor->IsAuthenticated()) {
                header('Location: index.php?c=admin&a=login');
                return;
            }
        } elseif ($action == 'login') {
            if ($this->adminInteractor->IsAuthenticated()) {
                header('Location: index.php?c=admin&a=index');
                return;
            }
        }
        switch ($action) {
            case 'login':
                $this->Login();
                break;
            case 'new-poll':
                $this->NewPoll();
                break;
            case 'delete-poll':
                $this->DeletePoll();
                break;
            case 'edit-poll':
                $this->EditPoll();
                break;
            case 'result-poll':
                $this->ResultPoll();
                break;
            case 'export-poll':
                $this->ExportPoll();
                break;
            case 'close-poll':
                $this->ClosePoll();
                break;
            case 'open-poll':
                $this->OpenPoll();
                break;
            case 'individual-poll':
                $this->IndividualPoll();
                break;
            case 'delete-vote':
                $this->DeleteVote();
                break;
            case 'index':
            default:
                $this->Index();
                break;
        }
    }

    public function Login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->adminInteractor->Authenticate($_POST['username'], $_POST['password'])) {
                header('Location: index.php?c=admin&a=index');
                return;
            }
        }
        $templateTitle = 'Login';
        $templateFile = 'admin/login.php';
        require_once(POLL_SCRIPT_ROOT.'/views/layout.php');
    }

    public function Index() {
        if (!isset($_GET['page'])) $_GET['page'] = 1;
        $_GET['page'] = (int) $_GET['page'];
        $polls = $this->pollInteractor->GetAll($_GET['page']);
        $templateTitle = 'Polls';
        $templateFile = 'admin/index.php';
        $page = $_GET['page'];
        require_once(POLL_SCRIPT_ROOT.'/views/layout.php');
    }

    public function NewPoll() {
        $templateTitle = 'Polls';
        $templateFile = 'admin/new-poll.php';
        $poll = new UsecasesPoll();
        $poll->Poll = new DomainPoll();
        $errors = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['question'] == '') {
                $errors[] = 'Please enter your question';
            }
            $poll->Poll->Question = $_POST['question'];
            foreach ($_POST['answers'] as $i => $answer) {
                if (trim($answer) != '') {
                    $pollAnswer = new DomainPollAnswer();
                    $pollAnswer->Order = $i;
                    $pollAnswer->Answer = $answer;
                    $poll->Answers[] = $pollAnswer;
                }
            }
            if (!count($poll->Answers)) {
                $errors[] = 'Please enter your answers';
            }
            $poll->Poll->RandomizeOrder = (isset($_POST['randomize_order']) && $_POST['randomize_order'] == 'yes');
            if ($_POST['close_poll'] != 0) {
                $poll->Poll->CloseDate = strtotime($_POST['close_date']);
            } else {
                $poll->Poll->CloseDate = 0;
            }
            $poll->Poll->VoteRepeating = $_POST['vote_repeating'];
            $poll->Poll->CookieExpire = $_POST['cookie_expire'];
            $poll->Poll->ShowHideResults = $_POST['show_hide_results'];
            $poll->Poll->OrderResults = $_POST['order_results'];
            if (count($errors) == 0) {
                $errors = $this->pollInteractor->Create($poll);
                if (count($errors) == 0) {
                    header('Location: index.php?c=admin&a=index');
                    return;
                }
            }
        }
        require_once(POLL_SCRIPT_ROOT.'/views/layout.php');
    }

    public function ResultPoll() {
        $poll = $this->pollInteractor->GetById($_GET['id']);
        $templateTitle = 'Polls';
        $templateFile = 'admin/result-poll.php';
        require_once(POLL_SCRIPT_ROOT.'/views/layout.php');
    }

    public function IndividualPoll() {
        if (!isset($_GET['page']) || !$_GET['page']) {
            $_GET['page'] = 1;
        }
        $_GET['page'] = (int) $_GET['page'];
        $poll = $this->pollInteractor->GetById($_GET['id']);
        $votes = $this->pollInteractor->GetIndividualVotes($_GET['id'], $_GET['page']);
        $templateTitle = 'Polls';
        $templateFile = 'admin/individual-poll.php';
        require_once(POLL_SCRIPT_ROOT.'/views/layout.php');
    }

    public function ExportPoll() {
        $poll = $this->pollInteractor->GetById($_GET['id']);
        header("Content-Disposition: attachment; filename=\"export.csv\"");
        header("Content-Type: text/csv");
        echo "answer,votes,percent\r\n";
        foreach ($poll->Results as $result) {
            echo '"'.addslashes($result->Answer).'",'.$result->VoteCount.','.($result->Percentage*100)."\r\n";
        }
    }

    public function EditPoll() {
        $poll = $this->pollInteractor->GetById($_GET['id']);
        $errors = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['question'] == '') {
                $errors[] = 'Please enter your question';
            }
            $poll->Poll->Question = $_POST['question'];
            foreach ($_POST['answers'] as $i => $answer) {
                if (trim($answer) != '') {
                    $found = false;
                    foreach ($poll->Answers as $j => $ans) {
                        if ($ans->Id == $i) {
                            $poll->Answers[$j]->Answer = $answer;
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $pollAnswer = new DomainPollAnswer();
                        $pollAnswer->Order = $i;
                        $pollAnswer->Answer = $answer;
                        $pollAnswer->PollId = $poll->Poll->Id;
                        $poll->Answers[] = $pollAnswer;
                    }
                }
            }
            foreach ($poll->Answers as $i=>$answer) {
                if ($answer->Id) {
                    if (!isset($_POST['answers'][$answer->Id])) {
                        $poll->Answers[$i]->Delete = true;
                    }
                }
            }
            if (!count($poll->Answers)) {
                $errors[] = 'Please enter your answers';
            }
            $poll->Poll->RandomizeOrder = (isset($_POST['randomize_order']) && $_POST['randomize_order'] == 'yes');
            if ($_POST['close_poll'] != 0) {
                $poll->Poll->CloseDate = strtotime($_POST['close_date']);
            } else {
                $poll->Poll->CloseDate = 0;
            }
            $poll->Poll->VoteRepeating = $_POST['vote_repeating'];
            $poll->Poll->CookieExpire = $_POST['cookie_expire'];
            $poll->Poll->ShowHideResults = $_POST['show_hide_results'];
            $poll->Poll->OrderResults = $_POST['order_results'];
            if (count($errors) == 0) {
                $errors = $this->pollInteractor->Edit($poll);
                if (count($errors) == 0) {
                    header('Location: index.php?c=admin&a=index');
                    return;
                }
            }
        }
        $templateTitle = 'Polls';
        $templateFile = 'admin/edit-poll.php';
        require_once(POLL_SCRIPT_ROOT.'/views/layout.php');
    }

    public function DeletePoll() {
        $this->pollInteractor->Delete($_GET['id']);
        header('Location: index.php?c=admin&a=index');
        return;
    }

    public function DeleteVote() {
        $this->pollInteractor->DeleteVote($_GET['id'], $_GET['pollId']);
        header('Location: index.php?c=admin&a=individual-poll&id='.$_GET['pollId'].'&page='.$_GET['page']);
        return;
    }

    public function ClosePoll() {
        $this->pollInteractor->Close($_GET['id']);
        header('Location: index.php?c=admin&a=index');
        return;
    }

    public function OpenPoll() {
        $this->pollInteractor->Open($_GET['id']);
        header('Location: index.php?c=admin&a=index');
        return;
    }
}

