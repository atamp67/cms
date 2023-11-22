<?php
    include("./config.php");

    $id = $_POST["id"];
    $val = $_POST["val"];

    $output = "";

    if ($val == 1) {
        
    } else {
        $output .= "<select class='form-control' name='category_node' id='category_node'>
        <option value='0'>none</option>";
            $spaceSep   = '&nbsp;';
            
            function traverse($idi = 0, $level = 1) {
                global $conn, $output, $spaceSep, $id;
                
                $blank  = '';

                $query = "SELECT * FROM category where pid=$idi";
                $res = mysqli_query($conn, $query);
                $disabled = "";
                while ($data = mysqli_fetch_array($res)) {
                    if ($data['pid'] == 0) {
                        $blank  = '';
                    } else {
                        $blank  = str_repeat($spaceSep, $level*4);
                    }
                    if ($data['pid'] == $id || $data['id'] == $id) {
                            $disabled = "disabled";
                    }
                    else
                        $disabled = "";
                    
                    $output .= "<option value='{$data['id']}' $disabled>$blank {$data['name']}</option>";
                    traverse($data['id'], $level+1);
                }
            } 
            traverse();
        
        $output .= "</select>"; 
        echo $output;
    } 
?>