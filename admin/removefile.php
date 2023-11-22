<?php

    include("config.php");

    $id = $_POST['id'];
    $file_path = $_POST['path'];

    $query = "SELECT * FROM files WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $remove_img = "";
    if (mysqli_num_rows($result)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $remove_img = $row['name'];
        }
        $folder = "uploads/$file_path/";
        unlink($folder.$remove_img);
        $result = mysqli_query($conn, "DELETE from files WHERE id = $id");
        if ($result)
            echo 1;
        else 
            echo 0;
    }
?>