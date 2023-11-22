<?php
include_once __DIR__ . '/config/Database.php';
$database = new Database();
$db = $database->getConnection();
if (isset($req_id))
    $post_id = $req_id;
else 
    $post_id = $status;
$is_post_request = strtolower($_SERVER['REQUEST_METHOD']) === 'post';
$has_categories = isset($_POST['category']); 
$category_count = count($_POST['category']);
$store_categories = "";
if ($has_categories) {
    for($i = 0; $i < $category_count; $i++) {
        $catid = $_POST['category'][$i];
        $store_categories .= "($catid, $post_id),";
    }
    if ($post["action"] == "update") {
        $delete = "DELETE FROM post_category WHERE post_id = $post_id";
        $db->query($delete);
    }
    // var_dump($category_count);    
    store_categories($store_categories, "");
    // die();
}

function store_categories($storeCategories) {
    global $db;
    if(!empty($storeCategories))
    {
        $value = trim($storeCategories, ',');
        
        $store="INSERT INTO post_category (category_id, post_id) VALUES $value;";
        $exec= $db->query($store);
    }
}
?>