<?php
// session_start();

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
$req_id = $_REQUEST['id'];

function alert_msg($msg) {
  echo '<script type ="text/JavaScript">';  
  echo  'alert("'.$msg.'");';  
  echo  '</script>'; 
}

// var_dump($post['file'][0]);
// die();

include "./file_upload.php";


// update record in database
if(!empty($post['action']) && $post['action']=="update") {
	$event->setEventID($req_id);
	$event->setTitle($post['title']);
	$event->setContent($post['content']);
    $event->setStatus($post['status']);
    if(isset($post['options']))
        $event->setOptions(implode(",", $post['options']));
	$status = $event->update();
	if(true) {
        $json['msg'] = 'success';
        include "./post_category.php";
        $caption_count = count($post['file']);
        for ($i = 0; $i < $caption_count; $i++) {
            $caption = $post['caption'][$i];
            $file_id = (int)$post['file'][$i];
            $query = "UPDATE files SET caption = '$caption' WHERE id = $file_id";
            // var_dump($caption_count);
            // die();
            $db->query($query);
        }
        if (!$errors) {
            {
             $post_id = $req_id;
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
                 $filepath = UPLOAD_DIR . '/' . $req_id;
                 mkdir($filepath);
                 $filepath = UPLOAD_DIR . '/' . $req_id . '/' . $uploaded_file;
                 
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
                         $_SESSION['success_msg'] = "Post Successfully Updated!";
                         header("location: ./posts.php");
                     }else{
                         echo  "Error: " .  $store . "<br>" . $db->error;
                     }
                 }
             }
                 store_files($store_files_base_name, "files");
             }
             if($file_count == 0) {
                $_SESSION['success_msg'] = "Post Successfully Updated!";
                // echo "files are uploaded successfully";
            }
              header("location: ./make_post.php");
         }
	} 
}


?>