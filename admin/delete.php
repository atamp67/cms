<?php

    include("config.php");

    $id = $_POST['id'];

    $query = "SELECT * FROM category WHERE id = $id";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $qur = "SELECT * FROM category WHERE pid = $id";
            $res = mysqli_query($conn, $qur);
            if (mysqli_num_rows($res) == 1) {
                echo 0;
            } 
            else if (mysqli_num_rows($res) > 1) {
                echo 0;
            } else if (mysqli_num_rows($res) < 1) {
                $delqur = "DELETE FROM category WHERE id = $id";
                if (mysqli_query($conn, $delqur)) {
                    echo 1;
                } else 
                    echo 0;
            }
        }
    }

?>