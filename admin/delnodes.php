<?php
    include("./config.php");
    $id = $_POST['id'];
    function deleteSub($cat_id) {
        global $conn;
        $request = "SELECT * FROM category WHERE pid = $cat_id";
        $result = mysqli_query($conn, $request);
        while ($child = mysqli_fetch_array($result)) {
            deleteSub($child["id"]);
        }
        $request = "DELETE FROM category WHERE id = $cat_id";
        return mysqli_query($conn, $request);
    }
    deleteSub($id);
    
    echo "Record Deleted Successfully!";
?>