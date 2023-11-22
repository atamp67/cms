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


include('inc/header.php');
?>
<!-- <script src="js/jquery.js"></script> -->
<!-- <script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		 -->
<!-- <link rel="stylesheet" href="css/dataTables.bootstrap.min.css" /> -->
<script src="js/categories.js"></script>	

<!-- <script src="https://cdn.datatables.net/select/1.2.6/js/dataTables.select.js"></script> -->
<!-- <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/select/1.2.6/css/select.dataTables.css"/> -->

<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script> -->
    
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<script src='https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'></script>
<script src="js/dataTables.treeGrid.js"></script>
<script src="js/treegrid.js"></script>

<link href="css/style.css" rel="stylesheet" type="text/css" >  
</head>
<body>
<?php include "menus.php"; ?>

<header id="header">
	<div class="container">
		<div class="row">
			<div class="col-md-10">
				<h1><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Categories <small>Manage Your Site</small></h1>
			</div>
			<br>			
		</div>
	</div>
</header>
<br>

<section id="main">
	<d	iv class="container">
		<div class="row" style="margin:-20px 50px">
			<?php include "left_menus.php"; ?>
			<div class="col-md-9">
				<div class="panel panel-default">
					<div class="panel-heading" style="background-color:  #095f59;">
						<h3 class="panel-title" style="color: #fff !important;">Categories</h3>
					</div>
					<div class="panel-body">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-10">
									<h3 class="panel-title"></h3>
								</div>
								<div class="col-md-2" align="right">
									<a href="add_categories.php" class="btn btn-default btn-xs">Add New</a>				
								</div>
							</div>
						</div>
						<table id="categoryList" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Id</th>									
									<th>Category</th>																								
									<th></th>
									<th></th>	
									<th></th>
									<th></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-category-val="0"  data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Delete Category</h1>
        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
      </div>
      <div class="modal-body">
        <p class="txt">This category has child nodes select the option for child categories</p>
        <div class="form-check">
            <input class="form-check-input category_val" type="radio" name="category_val" id="flexRadioDefault1" value="1">
            <label class="form-check-label" for="flexRadioDefault1">
                Delete All Child Categories
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input category_val" type="radio" name="category_val" id="flexRadioDefault2" value="2">
            <label class="form-check-label" for="flexRadioDefault2">
                Select Parent For Child Categories And Delete 
            </label>
        </div>
        <div class="form-check mt-4">
            <div id="category_nodes">
            </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modal_close">Close</button>
        <button type="button" class="btn btn-primary" id="category_del" data-opp="">Select Option</button>
      </div>
    </div>
  </div>
</div>
</section>

<?php include('inc/footer.php');?>
<script>
	$(document).ready(function() {

		$(document).on("click", "#modal_close", function(event) {
			$("#staticBackdrop").modal("hide");
		});
		
		$(".category_val").change(function() {
            var val = $(".category_val:checked").val();
            if (val == 1) {
                $("#category_del").text("Delete All");
                $("#category_del").attr("data-opp", 1);
            } else {
                $("#category_del").text("Change Parent");
                $("#category_del").attr("data-opp", 2);
            }
            var id = $("#staticBackdrop").attr("data-category-val");
            $.ajax({
                url: "nodes.php",
                method: "POST",
                data: {
                    id,
                    val
                },
                success: function(data) {
                    $("#category_nodes").html(data);                    
                }
            });
        });

        $(document).on("click", "#category_del", function(event) {
            var opp = $("#category_del").attr("data-opp");
            if (opp == 1) {
                    $.ajax({
                        url: "delnodes.php",
                        method: "POST",
                        data: {
                            id
                        },
                        success: function(data) {
                            alert(data);
                            window.location.reload();
                        }
                    });
            } else if (opp == 2) {
                    var value = $("#category_node").val();
                    $.ajax({
                        url: "changeparent.php",
                        method: "POST",
                        data: {
                            id,
                            value
                        },
                        success: function(data) {
							alert(data);
                            window.location.reload();
                        }
                    });
            }
        });
        
		$(document).on("click", "[name='delete']", function(event) {
                id = $(this).attr("data-value");
				if (confirm("are you sure to delete this category"))
                	$.ajax({
						url: "delete.php",
						method: "POST",
						data: {
							id  
						},
						success: function(data) {
							if (data == 1) {
								document.location.reload();
							} else {
                                $("#staticBackdrop").attr("data-category-val", id);
                                $("#staticBackdrop").modal("show");
							}
						}
                	});
            });   
	});
</script>


<!-- $(document).on("click", "#toggle_4", function(event) {
			$("<tr role='row' class='even'><td>1122</td><td>Hey</td><td>test1</td><td>test2</td></tr>").insertAfter($("#toggle_4").parent().parent()); -->
		<!-- });	 -->

