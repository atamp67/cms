<?php
// session_start();
include_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/inc/flash.php';
require_once __DIR__ . '/inc/functions.php';

// $post_id = $status;

$database = new Database();
$db = $database->getConnection();

// $db = $conn;

const ALLOWED_FILES = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg'
];

const MAX_SIZE = 5 * 1024 * 1024; //  5MB

const UPLOAD_DIR = __DIR__ . '/uploads';


$is_post_request = strtolower($_SERVER['REQUEST_METHOD']) === 'post';
$has_files = isset($_FILES['files']);

// if (!$is_post_request || !$has_files) {
//     redirect_with_message('Invalid file upload operation', FLASH_ERROR);
// }

if (isset($_FILES['files'])) {
    $files = $_FILES['files'];
    // var_dump(strrpos($files['tmp_name'][0], "\\"));
    // die();
    $file_count = count(array_filter($files['name']));
} else {
    $file_count = 0;
}

// validation
$errors = [];
for ($i = 0; $i < $file_count; $i++) {
    // get the uploaded file info
    $status = $files['error'][$i];
    $filename = $files['name'][$i];
    $tmp = $files['tmp_name'][$i];

    // an error occurs
    if ($status !== UPLOAD_ERR_OK) {
        $errors[$filename] = MESSAGES[$status];
        continue;
        // break;
    }
    // validate the file size
        $filesize = filesize($tmp);

    if ($filesize > MAX_SIZE) {
        // construct an error message
        $message = sprintf("The file %s is %s which is greater than the allowed size %s",
            $filename,
            format_filesize($filesize),
            format_filesize(MAX_SIZE));

        $errors[$filesize] = $message;
        continue;
    }

    // validate the file type
    if (!in_array(get_mime_type($tmp), array_keys(ALLOWED_FILES))) {
        $errors[$filename] = "The file $filename is not allowed to upload";
    } 
    else {
        $mime_type = get_mime_type($tmp);
        $uploaded_file = pathinfo($filename, PATHINFO_FILENAME) . '.' . ALLOWED_FILES[$mime_type];
        $filepath = UPLOAD_DIR . '/' . 'temp' . '/' . $uploaded_file;
        $success = move_uploaded_file($tmp, $filepath);
        // if($success) {
        //     var_dump($uploaded_file);
        //     die();
        // } else {
            
        // }
    }
}

if ($errors) {
    redirect_with_message(format_messages('The following errors occurred:',$errors), FLASH_ERROR);
    header("location: ./make_post.php");
    exit();
} 

// $errors ?
//     redirect_with_message(format_messages('The following errors occurred:',$errors), FLASH_ERROR) :
//     redirect_with_message('All the files were uploaded successfully.', FLASH_SUCCESS);

