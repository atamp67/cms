<?php
    $id = $_GET['id'];
    $margin = 0;
    $border = 0;
    if (isset($_GET['req'])) {
        $margin = 10;
        $border = 1;
    }
    $output = "";
    $output .= "
    <div class='row border' id='append$id' style='margin-top: {$margin}px;'>
        <div class='col-md-4' >
            <input type='file' name='files[]' style='border: 1px solid grey; padding: 10px; border-top: {$border}px solid grey;'/>
        </div>
        <div class='col-md-4' >
        <input type='text' name='caption[]' placeholder='Enter Caption (optional)' style='border: 1px solid grey; padding: 11px; margin-left: 10px; border-top: {$border}px solid grey;'/>
        </div>
        <div class='col-md-2' >
            <a class='removefile' data-val='$id'><span class='rmfile' style=''>X</span></a>
        </div>
    </div>";
    echo $output;
?>