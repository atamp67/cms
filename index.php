<?php 
include_once 'config/Database.php';
include_once 'class/Articles.php';
include_once './config.php';

$database = new Database();
$db = $database->getConnection();

$article = new Articles($db);

$article->id = 0;

$result = $article->getArticles();

include('inc/header.php');

?>
<title>CMS : Demo Build Content Management System with PHP & MySQL</title>
<link href="css/style.css" rel="stylesheet" id="bootstrap-css">
	<style>
		input, select, textarea, th, td {
			font-size: 1em;
		}

		ol.tree {
			padding: 0 0 0 30px;
			width: 300px;
		}

		li {
			position: relative;
			margin-left: -15px;
			list-style: none;
		}

		li input {
			position: absolute;
			left: 0;
			margin-left: 0;
			opacity: 0;
			z-index: 2;
			cursor: pointer;
			height: 1em;
			width: 1em;
			top: 0;
		}

		li input + ol {
			background: url(toggle-small-expand.png) 40px 0 no-repeat;
			margin: -1.8em 0px 10px -46px;
			height: 1em;
		}

		li input + ol > li {
			display: none; 
			margin-left: -14px !important; 
			padding-left: 1px;
		}

		li label {
			background: url(folder-horizontal.png) 15px 1px no-repeat;
			cursor: poniter;
			display: block;
			padding-left: 37px;
		}

		li input:checked + ol {
			background: url(toggle-small.png) 40px 5px no-repeat;
			margin: -1.9em 0 0 -48px;
			padding: 1.563em 0 0 80px;
			height: auto;
		}

		li input:checked + ol > li {
			display: block;
			margin: 0 0 0.125em;
		}

		li input:checked + ol > li:last-child {
			margin: 0 0 0.063em;
		}

		input, select, textarea, th, td {
			font-size: 1em;
		}
	</style>
<?php include('inc/container.php');?>
<div class="container">	
		<div class="row">
			<div class="header">
				<a href="#default" class="logo">My DEMO CMS</a>
				<div class="header-right">
					<a href="index.php">Home</a>
					<a href="#contact">Contact</a>
					<a href="#about">About</a>
				</div>
			</div>
			</div>
			<div id="blog" class="row">
				<div class="col-sm-3 col-md-3">
					<aside style="margin-top: 40px !important;">
						<?php
							$output = "";
							$arr = array();
							$arrCategories = array();    

							function treeView() {
								global $conn, $arrCategories;
								$query = "SELECT * FROM category";
								$res = mysqli_query($conn, $query);
								while ($row = mysqli_fetch_assoc($res)) {
									$arrCategories[$row['id']] = array("parent_id" => $row["pid"], "name" => $row["name"]);
								}
							}

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
											".$category['name']."</label>
											<input type='checkbox' name='subfolder2'>";
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

							global $arrCategories;
							createTreeView($arrCategories, 0);
						?>
					</aside>
				</div>
				<div class="col-sm-9 col-md-9">
		
			<?php
				while ($post = $result->fetch_assoc()) {
					$date = date_create($post['created']);					
					$message = str_replace("\n\r", "<br><br>", $post['message']);
					$message = $article->formatMessage($message, 100);
			?>
				<div class="col-md-10 blogShort">
					<h3><a href="view.php?id=<?php echo $post['id']; ?>"><?php echo $post['title']; ?></a></h3>		
					<em><strong>Published on</strong>: <?php echo date_format($date, "d F Y");	?></em>
					<em><strong>Category:</strong> <a href="#" target="_blank"><?php echo $post['category']; ?></a></em>
					<br><br>
					<article>		
					<p><?php echo $message; ?></p>
					</article>
					<a class="btn btn-blog pull-right" href="view.php?id=<?php echo $post['id']; ?>">READ MORE</a> 
				</div>
			<?php } 
			?>   	
		</div>
	</div>
</div>
<?php include('inc/footer.php');?>