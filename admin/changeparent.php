<?php
    include("./config.php");
    $id = $_POST['id'];
    $val = $_POST['value'];
    $query = "SELECT * FROM category WHERE pid = $id";
    $result = mysqli_query($conn, $query);
    $output = "Parent Category Updated!";
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rowid = $row['id'];
            $qur = "UPDATE category SET pid = $val WHERE id = $rowid";
            if (!mysqli_query($conn, $qur)) {
                $output = "";  
            } 
        }    
    }
    if (!mysqli_query($conn, "DELETE FROM category WHERE id = $id")) 
        $output = "";
    echo $output;
?>