<?php

class DbRepo {
    protected $db;
    protected $prefix;
}

class DbPollRepository extends DbRepo implements PollRepository {
    public function __construct(DB $db, $prefix) {
        $this->db = $db;
        $this->prefix = $prefix;
    }

    public function Store(DomainPoll $poll) {
        if (!$poll->Id) {
            $rt = $this->db->Exec("INSERT INTO {$this->prefix}polls (question, close_date, vote_repeating, cookie_expire, show_hide_results, order_results, created, randomize_order, closed) VALUES (?,?,?,?,?,?,?,?,?)", array($poll->Question, $poll->CloseDate, $poll->VoteRepeating, $poll->CookieExpire, $poll->ShowHideResults, $poll->OrderResults, time(), $poll->RandomizeOrder, $poll->Closed));
            $poll->Id = $rt->LastInsertId();
            return $poll;
        } else {
            $this->db->Exec("UPDATE {$this->prefix}polls SET question = ?, close_date = ?, vote_repeating = ?, cookie_expire = ?, show_hide_results = ?, order_results = ?, randomize_order = ?, closed = ? WHERE id = ?", array($poll->Question, $poll->CloseDate, $poll->VoteRepeating, $poll->CookieExpire, $poll->ShowHideResults, $poll->OrderResults, $poll->RandomizeOrder, $poll->Closed, $poll->Id));
        }
    }

    public function StoreAnswer(DomainPollAnswer $answer) {
        if (!$answer->Id) {
            $rt = $this->db->Exec("INSERT INTO {$this->prefix}poll_answers (poll_id, answer, sort_order) VALUES (?,?,?)", array($answer->PollId, $answer->Answer, $answer->Order));
            $answer->Id = $rt->LastInsertId();
            return $answer;
        } else {
            if ($answer->Delete) {
                $this->db->Exec("DELETE FROM {$this->prefix}poll_votes WHERE answer_id = ? AND poll_id = ?", array($answer->Id, $answer->PollId));
                $this->db->Exec("DELETE FROM {$this->prefix}poll_answers WHERE id = ? AND poll_id = ?", array($answer->Id, $answer->PollId));
                return;
            }
            $tmp = $this->db->QueryRow("SELECT COUNT(id) AS total FROM {$this->prefix}poll_answers WHERE id = ? AND poll_id = ?", array($answer->Id, $answer->PollId))->Scan();
            if ($tmp['total'] == 0) {
                $answer->Id = 0;
                return $this->StoreAnswer($answer);
            } else {
                $this->db->Exec("UPDATE {$this->prefix}poll_answers SET answer = ?, sort_order = ? WHERE id = ?", array($answer->Answer, $answer->Order, $answer->Id));
            }
        }
    }

    public function StoreVote(DomainPollVote $vote) {
        $this->db->Exec("INSERT INTO {$this->prefix}poll_votes (poll_id, identifier, voted_date, voted_ip, answer_id) VALUES (?,?,?,?,?)", array($vote->PollId, $vote->Identifier, $vote->VotedDate, ip2long($vote->VotedIp), $vote->AnswerId));
    }

    public function GetAnswersById($id,$randomize) {
        $order = 'sort_order ASC';
        if ($randomize) {
            $order = 'RAND()';
        }
        $rows = $this->db->Query("SELECT id, poll_id, answer, sort_order FROM {$this->prefix}poll_answers WHERE poll_id = ? ORDER BY ".$order, array($id));
        $answers = array();
        while ($rows->Next()) {
            $result = $rows->Scan();
            $answer = new DomainPollAnswer();
            $answer->Id = $result['id'];
            $answer->PollId = $result['poll_id'];
            $answer->Answer = $result['answer'];
            $answer->Order = $result['sort_order'];
            $answers[] = $answer;
        }
        return $answers;
    }

    public function Delete($id) {
        $this->db->Exec("DELETE FROM {$this->prefix}polls WHERE id = ?", array($id));
    }

    public function DeleteVote($id, $pollId) {
        $this->db->Exec("DELETE FROM {$this->prefix}poll_votes WHERE id = ? AND poll_id = ?", array($id, $pollId));
    }

    public function GetById($id) {
        $result = $this->db->QueryRow("SELECT id, question, close_date, vote_repeating, cookie_expire, show_hide_results, order_results, created, randomize_order, closed FROM {$this->prefix}polls WHERE id = ?", array($id))->Scan();
        $poll = new DomainPoll();
        $poll->Id = $result['id'];
        $poll->Question = $result['question'];
        $poll->CloseDate = $result['close_date'];
        $poll->VoteRepeating = $result['vote_repeating'];
        $poll->CookieExpire = $result['cookie_expire'];
        $poll->ShowHideResults = $result['show_hide_results'];
        $poll->RandomizeOrder = $result['randomize_order'];
        $poll->OrderResults = $result['order_results'];
        $poll->Created = $result['created'];
        $poll->Closed = $result['closed'];
        return $poll;
    }

    public function GetAll($limit, $offset) {
        $rows = $this->db->Query("SELECT id, question, close_date, vote_repeating, cookie_expire, show_hide_results, order_results, created, randomize_order, closed FROM {$this->prefix}polls ORDER BY created DESC LIMIT ? OFFSET ?", array($limit, $offset));
        $polls = array();
        while ($rows->Next()) {
            $result = $rows->Scan();
            $poll = new DomainPoll();
            $poll->Id = $result['id'];
            $poll->Question = $result['question'];
            $poll->CloseDate = $result['close_date'];
            $poll->VoteRepeating = $result['vote_repeating'];
            $poll->CookieExpire = $result['cookie_expire'];
            $poll->ShowHideResults = $result['show_hide_results'];
            $poll->RandomizeOrder = $result['randomize_order'];
            $poll->Created = $result['created'];
            $poll->Closed = $result['closed'];
            $polls[] = $poll;
        }
        return $polls;
    }

    public function GetNumofVotes($id) {
        $result = $this->db->QueryRow("SELECT COUNT(id) AS total FROM {$this->prefix}poll_votes WHERE poll_id = ?", array($id))->Scan();
        return $result['total'];
    }

    public function CheckIfVoted($id, $identifier) {
        $result = $this->db->QueryRow("SELECT COUNT(id) AS total FROM {$this->prefix}poll_votes WHERE poll_id = ? AND identifier = ?", array($id, $identifier))->Scan();
        return ($result['total'] > 0);
    }

    public function AnswerBelongsToVote($id, $answer) {
        $result = $this->db->QueryRow("SELECT COUNT(id) AS total FROM {$this->prefix}poll_answers WHERE poll_id = ? AND id = ?", array($id, $answer))->Scan();
        return ($result['total'] > 0);
    }

    public function GetPollResults($id) {
        $rows = $this->db->Query("SELECT answer_id, COUNT(id) AS total FROM {$this->prefix}poll_votes WHERE poll_id = ? GROUP BY answer_id ORDER BY total DESC", array($id));
        $results = array();
        while ($rows->Next()) {
            $result = $rows->Scan();
            $results[] = array('answer_id' => $result['answer_id'], 'total' => $result['total']);
        }
        return $results;
    }

    public function GetVotes($id, $limit, $offset) {
        $rows = $this->db->Query("SELECT id, poll_id, identifier, voted_date, voted_ip, answer_id FROM {$this->prefix}poll_votes WHERE poll_id = ? ORDER BY voted_date DESC LIMIT ? OFFSET ?", array($id, $limit, $offset));
        $results = array();
        while ($rows->Next()) {
            $result = $rows->Scan();
            $vote = new DomainPollVote();
            $vote->Id = $result['id'];
            $vote->PollId = $result['poll_id'];
            $vote->Identifier = $result['identifier'];
            $vote->VotedDate = $result['voted_date'];
            $vote->VotedIp = long2ip($result['voted_ip']);
            $vote->AnswerId = $result['answer_id'];
            $results[] = $vote;
        }
        return $results;
    }
}

class DefaultRequestRepository implements RequestRepository {
    public function GetIP() {
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }
}
