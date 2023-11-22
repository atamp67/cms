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

$req_id = $_REQUEST['id'];
$res = mysqli_query($conn, "SELECT * FROM posts WHERE id = $req_id");
if (!mysqli_num_rows($res))
header("location: ./index.php");

$res = mysqli_query($conn, "SELECT * FROM post_category WHERE post_id = $req_id");
$checked_categories = array();
while ($data = mysqli_fetch_assoc($res))
    $checked_categories[] = $data['category_id'];

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

// $postdetails = $post->getPost();
 
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
        global $checked_categories;
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
					if (isset($_SESSION['err_msg'])) {
						$msg = $_SESSION['err_msg'];
						echo "<div class='alert alert-danger alert-dismissible  fade in	' role='alert'>
						<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
						<strong>$msg</strong>
						</div>";
						unset($_SESSION['err_msg']);
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
				  
                  <form id="dynamic-post" class="dynamic-post" action="action_update.php?id=<?php echo $req_id; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update">
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
					createTreeView($arrCategories, 0);
    		  ?>
            </div>
            </div>
		  </div> 
            <br><br>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM posts WHERE id = $req_id"); 
            while($data = mysqli_fetch_assoc($res)) {?>
           <div class="form-group">
              <div class="col-sm-12">          
                <input type="text" required minlength="1" pattern="[a-zA-Z]{1,}[0-9\W+\S+]{0,}" class="form-control" id="event-title" placeholder="Title" name="title" value="<?php echo $data['title']; ?>">
              </div>
            </div> 
            <br><br>
            <div class="form-group">
              <div class="col-sm-12">
                <textarea class="form-control" id="event-content" name="content"><?php echo $data['content']; ?></textarea>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-12">
                <br>
                <label>Post Options</label>
                <?php 
                    $options = explode(",", $data['options']);
                ?>
                <br>
                <label><input type="checkbox"  name="options[]" value="Make it Featured" <?php if (in_array("Make it Featured", $options)) echo "checked";?>>&nbsp;<span><i class="fa-solid fa-star fa-sm"></i> Make it Featured</span></label>
                <label style="margin-left: 10px;"><input type="checkbox" name="options[]" value="Make it Visible" <?php if (in_array("Make it Visible", $options)) echo "checked";?>>&nbsp;<span><i class="fa-solid fa-eye fa-sm"></i> Make it Visible</span></label>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-12">
                <br>
                <label>Attach File</label>
                <br>
                <?php 
				    $cnt = 0;
                    $resl = mysqli_query($conn, "SELECT * FROM files WHERE post_id = $req_id");
					$total_record = mysqli_num_rows($resl);
					$record = $total_record;
                    while ($file = mysqli_fetch_assoc($resl)) {
						$total_record--;
                ?>
                <div class="row" id="attachfile<?php echo $cnt; ?>">
                    <div class="col-md-2" >
						<img src="uploads/<?php echo $data['id']; ?>/<?php echo $file['name'];?>" width="80px" height="40px"/>
					</div>
					<!-- <div class="col-md-4" >
						<input type="file" name="files[]" style="border: 1px solid grey; padding: 10px;"/>
					</div> -->
					<input type="hidden" name="file[]" value="<?php echo $file['id']; ?>" />
					<div class="col-md-4" >
						<input type="text" name="caption[]" value="<?php echo $file['caption']; ?>"  placeholder="Enter Caption (optional)" style="border: 1px solid grey; padding: 11px; margin-left: 10px;"/>
					</div>
					<div class='col-md-2' >
            			<a class='remove_prev_file' data-val='attachfile<?php echo $cnt; ?>' data-fileid='<?php echo $file['id']; ?>' data-path='<?php echo $req_id; ?>'><span class='rmfile'>X</span></a>
        			</div>
					<br>
				</div>
				<br>
                <?php 
					$cnt++;
                  }
                ?>
				<div class="row" id="attachfile" style="display: hidden;" data-record='<?php echo $record; ?>'></div>
				<div>
					<a class="btn btn-sm btn-primary" id="addfile" style="margin-top: 4px;">Add More</a>
				</div>
              </div>
            </div>
			<label for="name" class="control-label"><br>Status</label>
						<div class="form-group"> 
							<?php if ($data["status"] != "published") { ?>
								<div class="col-sm-3">
										<label class="radio-inline">
											<input name="status" id="input-status-draft" type="radio" value="draft" <?php if ($data['status'] == "draft") echo "checked"; ?>/>Draft
										</label>
								</div>
							<?php } ?>
							<div class="col-sm-3">
									<label class="radio-inline">
										<input name="status" id="input-status-publish" type="radio" value="published" <?php if ($data['status'] == "published") echo "checked"; ?>/>Published 
									</label>
							</div>
						</div>
						<br>
            <div class="form-group">        
              <div class="col-sm-12">
                <br>
                <button type="submit" class="btn btn-info" id="save-event121">Update</button>
              </div>
            </div>
        </div>
      </div>
      <?php }?>
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
	var record;
	$(document).ready(function() {
		append_file_count = -1;
		record = $("#attachfile").attr("data-record");
		$(document).on("click", "#addfile", function() {
			$.ajax({
				url: "addfile.php",
				type: "GET",
				data: {
					id: count,
					req: 'update'
				},
				success: function(data) {
					append = "#append" + append_file_count;
					if (append_file_count == -1|| count != 0)
						$(data).insertAfter("#attachfile");
					else {
						$(data).insertAfter(append);
						console.log(append);
					}
					count++;
					append_file_count++;
				} 	
			});
		});
		if (record == 0) {
			$("#addfile").click();
		}
		$(document).on("click", ".removefile", function() {
			id = $(this).attr("data-val");
			removefile = "#append" + id;
			$(removefile).remove();
			append_file_count--;
		});

		$(document).on("click", ".remove_prev_file", function() {
			id = $(this).attr("data-val");
			file_id = $(this).attr("data-fileid");
			file_path = $(this).attr("data-path");
			removefile = "#" + id;
			$.ajax({
				url: 'removefile.php',
				type: "POST",
				data: {
					id: file_id,
					path: file_path
				},
				success: function(data) {
					if (data == "1") {
						$(removefile).remove();
						record--;
						if (record == 0)
							$("#addfile").click();
					} else 
						console.log(data);
				}
			});
		});

	});
</script>
