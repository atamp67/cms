<?php

include_once 'config/Database.php';
include_once 'class/User.php';
include_once 'class/Post.php';
include_once 'class/Category.php';

include_once 'class/Event.php';
// create obj
$event = new Event();
$eventInfo = $event->getList();

$database = new Database();
$db = $database->getConnection();
$conn = $db;
$user = new User($db);
$post = new Post($db);
$category = new Category($db);

if(!$user->loggedIn()) {
	header("location: index.php");
}



// if (isset($_REQUEST['msg']))
// 	echo "msg";

$post = new Post($db);

$categories = $post->getCategories();

$post->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';

$label = isset($_GET['post']) ? 1 : 0;

$saveMessage = '';
if(!empty($_POST["savePost"]) && $_POST["title"]!=''&& $_POST["message"]!='') {
	
	$post->title = $_POST["title"];
	$post->message = $_POST["message"];
	$post->category = $_POST["category"];
	$post->status = $_POST["status"];	 	
	if($post->id) {	
		$post->updated = date('Y-m-d H:i:s');
		if($post->update()) {
			$saveMessage = "Post updated successfully!";
		}
	} else {
		$post->userid = $_SESSION["userid"];
		$post->created = date('Y-m-d H:i:s'); 
		$post->updated = date('Y-m-d H:i:s'); 	
		$lastInserId = $post->insert();
		if($lastInserId) {
			$post->id = $lastInserId;
			$saveMessage = "Post saved successfully!";
		}
	}
}

$postdetails = $post->getPost();
 
include('inc/header.php');
?>
<script src="assets/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/posts.js"></script>	
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="css/style.css" rel="stylesheet" type="text/css" >  
</head>
<body>
<?php include "menus.php"; ?>
<?php 
	$output = "";
	$arr = array();
	$arrCategories = array();    
	
	include("./utils.php");
	
	treeView();

	function createTreeView($array, $currentParent, $currLevel = 0, $prevLevel = -1) {
		foreach ($array as $categoryId => $category) {
			if ($currentParent == $category["parent_id"]) {
				if ($currLevel > $prevLevel) 
					echo "<ol class='tree'>"; 
				if ($currLevel == $prevLevel) 
					echo " ";
				echo "
					<li>
					<label for='subfolder2'>
					<input type='checkbox' name='category[]' value='$categoryId'>
					".$category['name']."</label>
					<input type='checkbox' class='checkinput' name='subfolder2'>";
					if ($currLevel > $prevLevel)
						$prevLevel = $currLevel;
					$currLevel++;
					createTreeView($array, $categoryId, $currLevel, $prevLevel);
					$currLevel--;
				}
			}
			
			if ($currLevel == $prevLevel)
			echo "</li>
			</ol>
			";
			
		}

		function createTreeView2($array, $currentParent, $currLevel = 0, $prevLevel = -1) {
			$checked_categories = $_SESSION['category'];
			$checked = "";
			foreach ($array as $categoryId => $category) {
				if ($currentParent == $category["parent_id"]) {
					if ($currLevel > $prevLevel) 
						echo "<ol class='tree'>"; 
					if ($currLevel == $prevLevel) 
						echo " ";
					if (in_array($categoryId, $checked_categories))
						$checked = "checked";
					else 
						$checked = "";
					echo "
						<li>
						<label for='subfolder2'>
						<input type='checkbox' name='category[]' value='$categoryId' $checked>
						".$category['name']."</label>
						<input type='checkbox' class='checkinput' name='subfolder2'>";
						if ($currLevel > $prevLevel)
							$prevLevel = $currLevel;
						$currLevel++;
						createTreeView($array, $categoryId, $currLevel, $prevLevel);
						$currLevel--;
					}
				}
				
				if ($currLevel == $prevLevel)
				echo "</li>
				</ol>
				";
				
			}
?>
<header id="header">
	<div class="container">
		<div class="row">
			<div class="col-md-10">
				<h1><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Make Post <small>Manage Your Site</small></h1>
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
				<?php
					$error = false;
					if (isset($_SESSION['err_msg'])) {
						$msg = $_SESSION['err_msg'];
						echo "<div class='alert alert-danger alert-dismissible  fade in	' role='alert'>
						<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
						<strong>$msg</strong>
						</div>";
						$error = true;
						unset($_SESSION['err_msg']);
					}
					if (isset($_SESSION['error_msg'])) {
						$msg = $_SESSION['error_msg'];
						echo "<div class='alert alert-danger alert-dismissible  fade in	' role='alert'>
						<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
						<strong>$msg</strong>
						</div>";
						unset($_SESSION['error_msg']);
					}
					if (isset($_SESSION['success_msg'])) {
						$msg = $_SESSION['success_msg'];
						echo "<div class='alert alert-success alert-dismissible  fade in	' role='alert'>
						<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
						<strong>$msg</strong>
						</div>";
						unset($_SESSION['success_msg']); 
					}
				?>
				<div class="panel panel-default">
				  <div class="panel-heading" style="background-color:  #095f59;">
					<h3 class="panel-title" style="color: #fff !important;">Add Post</h3>
				  </div>
				  <div class="panel-body">
				  
                  <form id="dynamic-post" class="dynamic-post" action="action.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <div class="row align-items-center">
          <div class="col-md-12 col-md-right">
		  <div class="form-group">
              <div class="col-sm-12"> 
					<label>Categories Selection: Choose Categories for Post</label>
              </div>
          </div>
		  <div class="overflow-auto" style="border: 1px solid grey; overflow: scroll; height: 200px;">
		  <div class="form-group">
              <div class="col-sm-12"> 
			  <?php
					global $arrCategories;
					if ($error) {
						createTreeView2($arrCategories, 0);
						unset($_SESSION['category']);
						}
					else
						createTreeView($arrCategories, 0);
    		  ?>
            </div>
            </div>
		  </div> 
            <br><br>
           <div class="form-group">
              <div class="col-sm-12">          
                <input type="text" required minlength="1" pattern="[a-zA-Z]{1,}[0-9\W+\S+]{0,}" class="form-control" id="event-title" placeholder="Title" name="title" value="<?php if ($error) echo $_SESSION['title']; else '';?>">
              </div>
            </div> 
            <br><br>
            <div class="form-group">
              <div class="col-sm-12">
                <textarea class="form-control" id="event-content" name="content">
					<?php
						if ($error)
							echo $_SESSION['content'];
						else
							echo "";
					?>
				</textarea>
              </div>
            </div>
			<?php if ($error) {?>
				<div class="form-group">
              <div class="col-sm-12">
                <br>
                <label>Post Options</label>
                <?php 
                    $options = $_SESSION['options'];
                ?>
                <br>
                <label><input type="checkbox"  name="options[]" value="Make it Featured" <?php if (in_array("Make it Featured", $options)) echo "checked";?>>&nbsp;<span><i class="fa-solid fa-star fa-sm"></i> Make it Featured</span></label>
                <label style="margin-left: 10px;"><input type="checkbox" name="options[]" value="Make it Visible" <?php if (in_array("Make it Visible", $options)) echo "checked";?>>&nbsp;<span><i class="fa-solid fa-eye fa-sm"></i> Make it Visible</span></label>
              </div>
            </div>
			<?php } else { ?>
            <div class="form-group">
              <div class="col-sm-12">
                <br>
                <label>Post Options</label>
                <br>
                <label><input type="checkbox"  name="options[]" value="Make it Featured">&nbsp;<span><i class="fa-solid fa-star fa-sm"></i> Make it Featured</span></label>
                <label style="margin-left: 10px;"><input type="checkbox" name="options[]" value="Make it Visible">&nbsp;<span><i class="fa-solid fa-eye fa-sm"></i> Make it Visible</span></label>
              </div>
            </div>
			<?php } ?>
            <div class="form-group">
              <div class="col-sm-12">
                <br>
                <label>Attach File</label>
                <br>
				<?php if ($error) { ?>
					<?php 
						$cnt = count($_SESSION['files']); 
						$rec_files = $_SESSION['files'];
						$res = scandir("uploads/temp/");
						for ($i = 0; $i < $cnt; $i++) {
							if (in_array($rec_files["name"][$i], $res)) {
							
					?>
					<div class="row" id="attachfile<?php echo $i; ?>">
                    <div class="col-md-2" >
						<img src="uploads/temp/<?php echo $rec_files['name'][$i];?>" width="80px" height="40px"/>
					</div>
					<input type="hidden" name="file[]" value="<?php echo $i; ?>" />
					<div class="col-md-4" >
						<input type="text" name="caption[]" value="<?php echo $rec_files['name'][$i]; ?>"  placeholder="Enter Caption (optional)" style="border: 1px solid grey; padding: 11px; margin-left: 10px;"/>
					</div>
					<div class='col-md-2' >
            			<a class='remove_prev_file' data-val='attachfile<?php echo $i; ?>' data-fileid='<?php echo $i; ?>' data-path='<?php echo "req_id"; ?>'><span class='rmfile'>X</span></a>
        			</div>
					<br>
				</div>
				<?php 
				}
			}
			} else { ?>
                <div class="row" id="attachfile">
					<div class="col-md-4" >
						<input type="file" name="files[]" style="border: 1px solid grey; padding: 10px;"/>
					</div>
					<div class="col-md-4" >
					<input type="text" name="caption[]" placeholder="Enter Caption (optional)" style="border: 1px solid grey; padding: 11px; margin-left: 10px;"/>
					</div>
					<br>
				</div>
				<?php } ?>
				<div>
					<a class="btn btn-sm btn-primary" id="addfile" style="margin-top: 4px;">Add More</a>
				</div>
              </div>
            </div>
			<label for="name" class="control-label"><br>Status</label>
						<?php if ($error) {
							$data = $_SESSION['status']; ?>
							<div class="form-group"> 
								<div class="col-sm-3">
										<label class="radio-inline">
											<input name="status" id="input-status-draft" type="radio" value="draft" <?php if ($data == "draft") echo "checked"; ?>/>Draft
										</label>
								</div>
							<div class="col-sm-3">
									<label class="radio-inline">
										<input name="status" id="input-status-publish" type="radio" value="published" <?php if ($data == "published") echo "checked"; ?>/>Published 
									</label>
							</div>
						</div>
						<?php } else { ?>
						<div class="form-group"> 
							<div class="col-sm-3">
									<label class="radio-inline">
										<input name="status" id="input-status-draft" type="radio" value="draft" checked/>Draft
									</label>
							</div>
							<div class="col-sm-3">
									<label class="radio-inline">
										<input name="status" id="input-status-publish" type="radio" value="published"/>Publish
									</label>
							</div>
						</div>
						<?php } ?>
						<br>
            <div class="form-group">        
              <div class="col-sm-12">
                <br>
                <button type="submit" class="btn btn-info" id="save-event121">Submit</button>
              </div>
            </div>
        </div>
      </div>
     </form>			
				  </div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include('inc/footer.php');?>
<script src="assets/tinymce/custom.tinymce.js"></script>
<script>
	var count = 0;
	$(document).ready(function() {
		var append_file_count = -1;
		$(document).on("click", "#addfile", function() {
			$.ajax({
				url: "addfile.php",
				type: "GET",
				data: {
					id: count
				},
				success: function(data) {
					append = "#append" + append_file_count;
					if (append_file_count == -1 || count != 0) {
						$(data).insertAfter("#attachfile");
					}
					else {
						$(data).insertAfter(append);
						// console.log(append);
					}
					count++;
					append_file_count++;
				} 	
			});
		});

		$(document).on("click", ".removefile", function() {
			id = $(this).attr("data-val");
			removefile = "#append" + id;
			$(removefile).remove();
			append_file_count--;
		});

	});
</script>
