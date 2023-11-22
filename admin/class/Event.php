<?php
/**
 * @package Event class
 *
 * @author TechArise Team
 *
 * @email  info@techarise.com
 *   
 */

// include connection class
include("DBConnection.php");
// Event
class Event 
{
    protected $db;
    private $_eventID;
    private $_title;
    private $_content;
    private $_options;
    private $_files;
    private $_status;

    public function setEventID($eventID) {
        $this->_eventID = $eventID;
    }
    public function setTitle($title) {
        $this->_title = $title;
    }
    public function setContent($content) {
        $this->_content = htmlentities($content);
    }

    public function setOptions($options) {
        $this->_options = $options;
    }

    public function setFiles($files) {
        $this->_files = $files;
    }

    public function setStatus($status) {
        $this->_status = $status;
    }
    
    // __construct
    public function __construct() {
        $this->db = new DBConnection();
        $this->db = $this->db->returnConnection();
    }

    // create record in database
    public function create() {
		try {
    		$sql = 'INSERT INTO posts (title, content, options, status)  VALUES (:title, :content, :options, :status)';
    		$data = [
			    'title' => $this->_title,
			    'content' => $this->_content,
                'options' => $this->_options,
                'status' => $this->_status
			];
	    	$stmt = $this->db->prepare($sql);
	    	$stmt->execute($data);
			$status = $this->db->lastInsertId();
            return $status;

		} catch (Exception $err) {
    		die("Oh noes! There's an error in the query! ".$err);
		}

    }

    // update record in database
    public function update() {
        try {
		    $sql = "UPDATE posts SET title=:title, content=:content, options=:options, status=:status WHERE id=:event_id";
		    $data = [
			    'title' => $this->_title,
                'content' => $this->_content,
                'options' => $this->_options,
                'status' => $this->_status,
                'event_id' => $this->_eventID,
			];
			$stmt = $this->db->prepare($sql);
			$stmt->execute($data);
			$status = $stmt->rowCount();
            return $status;
		} catch (Exception $err) {
			die("Oh noes! There's an error in the query! " . $err);
		}
    }
   
    // get records from database
    public function getList() {
    	try {
    		$sql = "SELECT id, title, content, created_date FROM posts";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
		    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $err) {
		    die("Oh noes! There's an error in the query! " . $err);
		}
    }
    // 
    public function getEvent() {
        try {
            $sql = "SELECT id, title, content, created_date FROM posts WHERE id=:event_id";
            $stmt = $this->db->prepare($sql);
            $data = [
                'event_id' => $this->_eventID
            ];
            $stmt->execute($data);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            die("Oh noes! There's an error in the query!");
        }
    }

    // delete record from database
    public function delete() {
    	try {
	    	$sql = "DELETE FROM posts WHERE id=:event_id";
		    $stmt = $this->db->prepare($sql);
		    $data = [
		    	'event_id' => $this->_eventID
			];
	    	$stmt->execute($data);
            $status = $stmt->rowCount();
            return $status;
	    } catch (Exception $err) {
		    die("Oh noes! There's an error in the query! " . $err);
		}
    }


}
?>