<?php 
include_once 'config/Database.php';
include_once 'class/Articles.php';
include_once 'admin/class/Comments.php';

$database = new Database();
$db = $database->getConnection();

$article = new Articles($db);
$comment = new Comments($db);

$article->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';

if(!empty($_POST["commentSave"])) {
	
	$comment->user = $_POST["title"];	
	$comment->comment = $_POST["message"];	
	$lastInserId = $comment->insert();
	if($lastInserId) {
			$comment->id = $lastInserId;
			$saveMessage = "comment saved successfully!";
		}
	}

$result = $article->getArticles();

include('inc/header.php');

?>
<title>CMS : Demo Build Content Management System with PHP & MySQL</title>
<link href="css/style.css" rel="stylesheet" id="bootstrap-css">
<?php include('inc/container.php');?>
<div class="container">		
	<div id="blog" class="row">     
		<div id="blog" class="row">
			<div class="header">
			<a href="#default" class="logo">My DEMO CMS</a>
			<div class="header-right">
				<a href="index.php">Home</a>
				<a href="#contact">Contact</a>
				<a href="#about">About</a>
			</div>
		</div>		
		<?php 
		while ($post = $result->fetch_assoc()) {
			$date = date_create($post['created']);
			$message = str_replace("\n\r", "<br><br>", $post['message']);
		?>
			<div class="col-md-10 blogShort">
			<h2><?php echo $post['title']; ?></h2>
			<em><strong>Published on</strong>: <?php echo date_format($date, "d F Y");	?></em>
			<!-- <em><strong>comment:</strong> <a href="#" target="_blank"><?php echo $post['comment']; ?></a></em> -->
			<br><br>
			<article>
				<p><?php echo $message; ?> 	</p>
			</article>		
			</div>
		<?php } ?>   
		
		<div class="col-md-12 gap10"></div>
		<section id="main">
	<div class="container">
		<div class="row">	
			<div class="col-md-9">
				<div class="panel panel-default">
				  <div class="panel-heading">
					<h3 class="panel-title"></h3>
				  </div>
				  <div class="panel-body">
				  <form method="post" id="postForm">							
						
						<div class="form-group">
							<label for="title" class="control-label">User Name</label>
							<input type="text" class="form-control" id="title" name="title" value="" placeholder="Post title..">							
						</div>
						
						<div class="form-group">
							<label for="lastname" class="control-label">Comment</label>							
							<textarea class="form-control" rows="5" id="message" name="message" placeholder="Post message.."></textarea>					
						</div>	

						
						
						<input type="submit" name="commentSave" id="commentSave" class="btn btn-info" value="Save" />											
					</form>	
								
				  </div>
				</div>
			</div>
		</div>
	</div>
</section>
		
	</div>
</div>
<?php include('inc/footer.php');?>