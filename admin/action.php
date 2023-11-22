<?php
session_start();

// include event class 
include_once 'class/Event.php';

require_once __DIR__ . '/inc/flash.php';
require_once __DIR__ . '/inc/functions.php';

// create obj
$event = new Event();
// post method
$post = $_POST;
// define array
$json = array();	

function alert_msg($msg) {
  echo '<script type ="text/JavaScript">';  
  echo  'alert("'.$msg.'");';  
  echo  '</script>'; 
}

// var_dump($_FILES['files']);
// die();

$_SESSION['title'] = $post['title'];
$_SESSION['content'] = $post['content'];
$_SESSION['status'] = $post['status'];
$_SESSION['options'] = $post['options'];
$_SESSION['category'] = $post['category'];
$_SESSION['files'] = $_FILES['files'];

include "./file_upload.php";

// create record in database
if(!empty($post['action']) && $post['action']=="create") {
	$event->setTitle($post['title']);
	$event->setContent($post['content']);
  $event->setStatus($post['status']);
  if(isset($post['options']))
    $event->setOptions(implode(",", $post['options']));
  if (!isset($_POST['category'])) {
      $_SESSION['error_msg'] = "Please Select at least One Category";
      header("location: ./make_post.php");
      exit();
  }
	$status = $event->create();
	if(!empty($status)) {
		$json['msg'] = 'success';
		$json['task_id'] = $status;
    // alert_msg($status);
    include "./post_category.php";
    // include "./file_upload.php";
    if (!$errors) {
       {
        $post_id = $status;
        $store_files_base_name = "";
        $store_files_caption = "";
        
        // move the files
        for($i = 0; $i < $file_count; $i++) {
            $img_id = uniqid();
            $filename = $img_id . $files['name'][$i];
            $tmp = $files['tmp_name'][$i];
            $mime_type = get_mime_type($tmp);
            $caption = $_POST['caption'][$i];
            // set the filename as the basename + extension
            $uploaded_file = pathinfo($filename, PATHINFO_FILENAME) . '.' . ALLOWED_FILES[$mime_type];
            // new filepath
            // $filepath = UPLOAD_DIR . '/' . $uploaded_file;
            $filepath = UPLOAD_DIR . '/' . $status;
            mkdir($filepath);
            $filepath = UPLOAD_DIR . '/' . $status . '/' .$uploaded_file;
            
            // move the file to the upload dir
            
            $success = move_uploaded_file($tmp, $filepath);
            if(!$success) {
                $errors[$filename] = "The file $filename was failed to move.";
            } else {
                $store_files_base_name .= "('$filename', '$caption', $post_id),";
            }
        }
        
        function store_files($storeFilesBasename, $tableName) {
            global $db;
            if(!empty($storeFilesBasename))
            {
                $value = trim($storeFilesBasename, ',');
                
                $store="INSERT INTO files (name, caption, post_id) VALUES $value;";
                $exec= $db->query($store);
                if($exec){
                    $_SESSION['success_msg'] = "Post Successfully Created!";
                    echo "files are uploaded successfully";
                }else{
                    echo  "Error: " .  $store . "<br>" . $db->error;
                }
            }
        }
            store_files($store_files_base_name, "files");
        }
    } 
    if($file_count == 0){
      $_SESSION['success_msg'] = "Post Successfully Created!";
      echo "files are uploaded successfully";
  }
    header("location: ./make_post.php");
    exit();
	} else {
		$json['msg'] = 'failed';
		$json['task_id'] = '';
    alert_msg("Insertion Failed!");
	}
	// header('Content-Type: application/json');	
	// echo '<div class="card gedf-card" style="margin: 5px;" id="dyn-'.$status.'">
  //     <div class="card-body">
  //         <a class="card-link" href="#">
  //             <h5 class="card-title">'.$post['title'].'</h5>
  //         </a>
  //         <div class="text-muted h7 mb-2"> <i class="fas fa-map-marker-alt"></i> '.$post['location'].'</div>
  //         <p class="card-text">'.$post['content'].'</p>
  //         <hr>
  //         <p class="card-text float-right">
  //           <button type="submit" class="btn btn-sm btn-outline-secondary update-event" data-ueventid="'.$status.'">Edit</button>
  //           <button type="submit" class="btn btn-sm btn-outline-secondary delete-event" data-deventid="'.$status.'">Delete</button>
  //         </p>
  //     </div>                    
  //   </div>';
}
// update record in database
if(!empty($post['action']) && $post['action']=="fetch_event") {
	$event->setEventID($post['event_id']);
	$fetchEvent = $event->getEvent();
	header('Content-Type: application/json');
	echo '<form id="dynamic-post-'.$post['event_id'].'" class="dynamic-post">
	<input type="hidden" name="action" value="update">
		<input type="hidden" name="event_id" value="'.$fetchEvent['id'].'">
        <div class="row align-items-center">
          <div class="col-md-12 col-md-right">
           <div class="form-group">
              <div class="col-sm-12">          
                <input type="text" class="form-control" id="event-title" placeholder="Title" name="title" value="'.$fetchEvent['title'].'">
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-12">          
                <input type="text" class="form-control" id="event-location" placeholder="Location" name="location" value="'.$fetchEvent['location'].'">
              </div>
            </div> 
            <div class="form-group">
              <div class="col-sm-12">
                <textarea class="form-control" id="event-content'.$fetchEvent['id'].'" name="content">'.$fetchEvent['content'].'</textarea>
              </div>
            </div>
            <div class="form-group">        
              <div class="col-sm-offset-2 col-sm-12">
                <button type="button" class="btn btn-info float-right save-update" data-seventid="'.$fetchEvent['id'].'">Submit</button>
              </div>
            </div>
        </div>
      </div>
      </form>';
  }

// update record in database
if(!empty($post['action']) && $post['action']=="update") {
	$event->setEventID($post['event_id']);
	$event->setTitle($post['title']);
	$event->setContent($post['content']);
	$status = $event->update();
	if(!empty($status)){
		$json['msg'] = 'success';
	} else {
		$json['msg'] = 'failed';
	}
	header('Content-Type: application/json');	
	echo '<div class="card gedf-card" style="margin: 5px;" id="dyn-'.$post['event_id'].'">
      <div class="card-body">
          <a class="card-link" href="#">
              <h5 class="card-title">'.$post['title'].'</h5>
          </a>
          <div class="text-muted h7 mb-2"> <i class="fas fa-map-marker-alt"></i> '.$post['location'].'</div>
          <p class="card-text">'.$post['content'].'</p>
          <hr>
          <p class="card-text float-right">
            <button type="submit" class="btn btn-sm btn-outline-secondary update-event" data-ueventid="'.$post['event_id'].'">Edit</button>
            <button type="submit" class="btn btn-sm btn-outline-secondary delete-event" data-deventid="'.$post['event_id'].'">Delete</button>
          </p>
      </div>                    
    </div>';
}

// delete record from database
if(!empty($post['action']) && $post['action']=="delete") {
	$event->setEventID($post['event_id']);
	$status = $event->delete();
	if(!empty($status)){
		$json['msg'] = 'success';
	} else {
		$json['msg'] = 'failed';
	}
	header('Content-Type: application/json');	
	echo json_encode($json);	
}

?>