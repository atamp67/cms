<?php

include_once 'config/Database.php';
include_once 'class/User.php';
include_once 'class/Post.php';
include_once 'class/Category.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$post = new Post($db);
$category = new Category($db);

if(!$user->loggedIn()) {
	header("location: index.php");
}

$category = new Category($db);

// $category->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';

// $catID = isset($_GET['catID']) ? 1 : 0;
// $message = "";

$saveMessage = '';
// if(!empty($_POST["categorySave"]) && $_POST["categoryName"]!='') {
	
// 	$category->name = $_POST["categoryName"];	
// 	if($category->id) {			
// 		if($category->update()) {
// 			$saveMessage = "Category updated successfully!";
// 		}
// 	} else {			
// 		$lastInserId = $category->insert();
// 		if($lastInserId) {
// 			$category->id = $lastInserId;
// 			$saveMessage = "Category saved successfully!";
// 		}
// 	}
// }

// $categoryDetails = $category->getCategory();
 
    include("./config.php");
    if (isset($_POST['submit'])) {
        $category_name = $_POST['name'];
        $parent_id = $_POST['category'];
        $desc = $_POST['desc'];
		$status = $_POST['status'];
        $query = "INSERT INTO category(name, pid, description, is_active) VALUES ('$category_name', $parent_id, '$desc', '$status');";
        $res = mysqli_query($conn, $query);
        if ($res) {
            alert_msg();
            header("location: ./categories.php");
        }
        else
            echo "Unsuccess".$res;
    }
    function alert_msg() {
        echo '<script type ="text/JavaScript">';  
        echo 'alert("Category Successfully Added!");';  
        echo '</script>';  
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
					<h3 class="panel-title" style="color: #000;">Add Category</h3>
				  </div>
				  <div class="panel-body">
				  
					<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" id="postForm">							
						<?php if ($saveMessage != '') { ?>
							<div id="login-alert" class="alert alert-success col-sm-12"><?php echo $saveMessage; ?></div>                            
						<?php } ?>
						
						<div class="form-group">
							<label for="name" class="control-label">Category Name</label>
							<input type="name" class="form-control" required minlength="1" pattern="[a-zA-Z]{1,}[0-9\W+\S+]{0,}" title="Invalid Name" id="name" placeholder="Category Name" name="name">							
						</div>	
						
						<div class="form-group">
    						<label for="category">Select Parent Category</label>
							<?php 
        						$result = mysqli_query($conn, "SELECT * from category");
       						?>
							    <select class="form-control" name="category">
									<option value="0">none</option>
									<?php
										$output     = "";
										$spaceSep   = '&nbsp;';
										function traverse($id = 0, $level = 1) {
											global $conn, $output, $spaceSep;
											
											$blank  = '';

											$query = "SELECT * FROM category where pid=$id";
											$res = mysqli_query($conn, $query);
											while ($data = mysqli_fetch_array($res)) {
												if ($data['pid'] == 0) {
													$blank  = '';
												} else {
													$blank  = str_repeat($spaceSep, $level*4);
												}
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
										<input name="status" id="input-status-active" type="radio" value="1"/>Active
									</label>
							</div>
							<div class="col-sm-3">
									<label class="radio-inline">
										<input name="status" id="input-status-inactive" type="radio" value="0"/>In-Active
									</label>
							</div>
						</div>
						<br>
						<div class="form-group">
   						   <label for="desc">Description</label>
						   <textarea name="desc" required minlength="4" maxlength="200" class="form-control" placeholder="Decription here" id="desc" style="height: 100px"></textarea>
						</div>
						
						<input type="submit" name="submit" class="btn btn-primary" id="categorySave" value="Save" />											
					</form>				
				  </div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include('inc/footer.php');?>
<script>
	$('.list-group').find(".active").removeClass("active main-color-bg ");
	// $('.list-group').find(".categories").addClass("active main-color-bg ");
</script>
