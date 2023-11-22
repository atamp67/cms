<?php

include_once 'config/Database.php';
include_once 'class/User.php';
include_once 'class/Post.php';
include_once 'class/Category.php';
include './config.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$post = new Post($db);
$category = new Category($db);

if(!$user->loggedIn()) {
	header("location: index.php");
}

$req_id = $_REQUEST['id'];
$res = mysqli_query($conn, "SELECT * FROM category WHERE id = $req_id");
if (!mysqli_num_rows($res))
header("location: ./index.php");


$saveMessage = '';
    if (isset($_POST['submit']) && $_POST["name"]!=''&& $_POST["desc"]!='') {
        $id = $_POST['id'];
        $category_name = $_POST['name'];
        $parent_id = $_POST['category'];
        $desc = $_POST['desc'];
        $status = $_POST['status'];
        $query = "UPDATE category SET name = '$category_name', pid = $parent_id, description = '$desc', is_active = '$status' WHERE id = $id";
        $res = mysqli_query($conn, $query);
        if ($res) {
			$saveMessage = "Category updated successfully!";
            echo '<script type ="text/JavaScript">';  
            echo 'alert("Category Successfully Added!");';  
            echo '</script>'; 
            header("location: ./index.php");
        }
        else
            echo mysqli_error($conn);
    }


 
include('inc/header.php');
?>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<!-- <script src="js/posts.js"></script>	 -->
<script src="js/categories.js"></script>	
<link href="css/style.css" rel="stylesheet" type="text/css" >  
</head>
<body>
<?php include "menus.php"; ?>
<?php
    include("./config.php");
    $id =  $_REQUEST['id'];
    $record = array();
    if (isset($_REQUEST['id'])) {
        $qur = "SELECT * FROM category WHERE id = $id";
        $result = mysqli_query($conn, $qur);
        if (mysqli_num_rows($result)) {
            while($row = mysqli_fetch_assoc($result)) {
                $record = $row;    
            }
        }
    }
?>
<header id="header">
	<div class="container">
		<div class="row">
			<div class="col-md-10">
				<h1><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Dashboard <small>Manage Your Site</small></h1>
			</div>
			<br>			
		</div>
	</div>
</header>
<br>
<section id="main">
	<div class="container">
		<div class="row">	
			<?php include "left_menus.php"; ?>
			<div class="col-md-9">
				<div class="panel panel-default">
				  <div class="panel-heading">
					<h3 class="panel-title" style="color: #000 !important;">Update Category</h3>
				  </div>
				  <div class="panel-body">
				  
					<form method="post" id="postForm" action="<?php echo $_SERVER['PHP_SELF'] ?>">							
						<?php if ($saveMessage != '') { ?>
							<div id="login-alert" class="alert alert-success col-sm-12"><?php echo $saveMessage; ?></div>                            
						<?php } ?>
						<div class="form-group">
							<label for="name" class="control-label">Category Name</label>
							<input type="name" required minlength="1" pattern="[a-zA-Z]{1,}[0-9\W+\S+]{0,}" class="form-control" id="name" value="<?php echo $record['name'] ?>" placeholder="Category Name" name="name">							
						</div>		
						<div class="form-group">
							<label for="category">Select Parent Category</label>
                            <?php 
                                $result = mysqli_query($conn, "SELECT * from category");
                                
                                ?>
							<select class="form-control" name="category">
        <option value="<?php if ($record['pid'] == 0) echo 0; else echo $record['pid']; ?>"><?php if($record['pid'] == 0) echo "root"; else {$res = mysqli_query($conn, "Select * from category where id = ".$record['pid']);while ($data = mysqli_fetch_assoc($res)) echo $data['name'];} ?></option>
        <?php
            $output     = "";
            $spaceSep   = '&nbsp;';
            function traverse($id = 0, $level = 1) {
                global $conn, $output, $spaceSep, $record;
                
                // init
                $blank  = '';

                $query = "SELECT * FROM category where pid=$id";
                $res = mysqli_query($conn, $query);
                while ($data = mysqli_fetch_array($res)) {
                    if ($data['pid'] == 0) {
                        $blank  = '';
                    } else {
                        $blank  = str_repeat($spaceSep, $level*4);
                    }
                    if ($record['pid'] == $data['pid'] && $record['id'] == $data['id'])
                        continue;
                    $status = "";
                    if ($data['is_active'] == 0)
                        $status = "disabled";
                    $output .= "<option value='{$data['id']}' $status>$blank {$data['name']}</option>";
                    traverse($data['id'], $level+1);
                }
                $count = 0;
                return $output;
            } 
            echo traverse();
        ?>
    </select>
						</div>	
                        <label for="name" class="control-label">Category Status</label>
						<div class="form-group"> 
							<div class="col-sm-3">
									<label class="radio-inline">
										<input name="status" id="input-status-active" type="radio" value="1" <?php echo ($record['is_active'] == 1) ?  "checked" : "" ;  ?>/>Active
									</label>
							</div>
							<div class="col-sm-3">
									<label class="radio-inline">
										<input name="status" id="input-status-inactive" type="radio" value="0" <?php echo ($record['is_active'] == 0) ?  "checked" : "" ;  ?>/>In-Active
									</label>
							</div>
						</div>
						<br>					
						<div class="form-group">
							<label for="desc">Description</label>							
                            <textarea name="desc" required minlength="4" maxlength="200" class="form-control"  placeholder="Decription here" id="desc" style="height: 100px"><?php echo $record['description']; ?></textarea>
						</div>	
                    <input type="hidden" name="id" value="<?php echo $record['id']; ?>" />

						<input type="submit" name="submit" id="savePost" class="btn btn-info" value="Update" />											
					</form>				
				  </div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include('inc/footer.php');?>
