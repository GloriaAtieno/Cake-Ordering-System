<?php
	// start session
	session_start();
	// Turn off error reporting
	error_reporting(0);
	// Code for Remove a Product from Cart
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['remove_code'])==true)
	{
		include('includes/config.php');

		foreach($_POST['remove_code'] as $key){
			
			// echo$key;
			// SQL DELETE statement with one parameter placeholders
			$sql = 'DELETE FROM `cart` WHERE `product_id`=?';
			// Prepare the SQL DELETE statement
			$stmt = $con->prepare($sql);
			// Bind the one parameter to the statement
			$stmt->bind_param('s',$key);
			// Execute the statement
			if ($stmt->execute())
			{
				header("Location: my-cart.php?success=true");
      			exit();
			}
			else
			{
				header("Location: my-cart.php?success=false");
      			exit();
			}
		}
	}

	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete']) == true)
	{
		include('includes/config.php');
		$product_Id = trim(mysqli_real_escape_string($con, $_POST['product_id']));
      	$product_Id = htmlspecialchars($product_Id,ENT_QUOTES,'UTF-8');

		// SQL DELETE statement with one parameter placeholders
		$sql = 'DELETE FROM `cart` WHERE `product_id`=?';
		// Prepare the SQL DELETE statement
		$stmt = $con->prepare($sql);
		// Bind the one parameter to the statement
		$stmt->bind_param('s',$product_Id);
		// Execute the statement
		if ($stmt->execute())
		{header("Location: my-cart.php?success=true");exit();}
		else
		{header("Location: my-cart.php?success=false");exit();}
	}

	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['saveQuantity']) == true)
	{
		include('includes/config.php');
		$product_Id = trim(mysqli_real_escape_string($con, $_POST['product_id']));
      	$product_Id = htmlspecialchars($product_Id,ENT_QUOTES,'UTF-8');

		$newQuantity = trim(mysqli_real_escape_string($con, $_POST['newQuantity']));
		$newQuantity = htmlspecialchars($newQuantity,ENT_QUOTES,'UTF-8');

		// SQL SELECT statement with one parameter placeholders
        $sql = "SELECT `productPrice`, `shippingCharge` FROM `products` WHERE `id`=?";
        // Prepare the SQL SELECT statement
        $stmt = $con->prepare($sql);
        // Bind the one parameter to the statement
        $stmt->bind_param('s', $product_Id);
        // Execute the statement
        $stmt->execute();
        // Retrieve the result set
        $result = $stmt->get_result();
        // Fetch data
        $row = $result->fetch_assoc();
        // Value of the productPrice row returned
        $productPriceRow = $row['productPrice'];
		// Value of the shippingCharge row returned
        $shippingChargeRow = $row['shippingCharge'];
		// Value of the shippingCharge row returned
		echo $newTotal = ((int)$productPriceRow*(int)$newQuantity)+(int)$shippingChargeRow;

		// SQL UPDATE statement with one parameter placeholders
		$sql = 'UPDATE `cart` SET `quantity`=?,`total`=? WHERE `product_id`=?';
		// Prepare the SQL UPDATE statement
		$stmt = $con->prepare($sql);
		// Bind the one parameter to the statement
		$stmt->bind_param('sss',$newQuantity,$newTotal,$product_Id);
		// Execute the statement
		if ($stmt->execute())
		{header("Location: my-cart.php?success=true");exit();}
		else
		{header("Location: my-cart.php?success=false");exit();}
	}

	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitOrder']) == true)
	{
		//payment-method.php
		header("Location: payment-method.php");
		exit();
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">
	    <meta name="keywords" content="MediaCenter, Template, eCommerce">
	    <meta name="robots" content="all">

	    <title>My Cart</title>
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/green.css">
	    <link rel="stylesheet" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" href="assets/css/owl.transitions.css">
		<!--<link rel="stylesheet" href="assets/css/owl.theme.css">-->
		<link href="assets/css/lightbox.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/animate.min.css">
		<link rel="stylesheet" href="assets/css/rateit.css">
		<link rel="stylesheet" href="assets/css/bootstrap-select.min.css">

		<!-- Demo Purpose Only. Should be removed in production -->
		<link rel="stylesheet" href="assets/css/config.css">

		<link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
		<link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
		<link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
		<link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
		<link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">
		<!-- Demo Purpose Only. Should be removed in production : END -->

		
		<!-- Icons/Glyphs -->
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">

        <!-- Fonts --> 
		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="assets/images/favicon.ico">

		<!-- HTML5 elements and media queries Support for IE8 : HTML5 shim and Respond.js -->
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->

	</head>
	<body class="cnt-home">
		<!-- ============================================== HEADER : START ========================================== -->
		<header class="header-style-1">
		<?php include('includes/top-header.php');?>
		<?php include('includes/main-header.php');?>
		<?php include('includes/menu-bar.php');?>
		</header>
		<!-- ============================================== HEADER : END============================================== -->

		<div class="breadcrumb">
			<div class="container">
				<div class="breadcrumb-inner">
					<ul class="list-inline list-unstyled">
						<li><a href="#">Home</a></li>
						<li class='active'>Shopping Cart</li>
					</ul>
				</div><!-- /.breadcrumb-inner -->
			</div><!-- /.container -->
		</div><!-- /.breadcrumb -->

		<div class="body-content outer-top-xs">
			<div class="container">
				<div class="row inner-bottom-sm">
					<div class="shopping-cart">
						<div class="col-md-12 col-sm-12 shopping-cart-table ">
							<form action="" method="post">
								<div class="table-responsive">
									<?php
										// SQL SELECT statement with two parameters placeholders
										$sql = "SELECT `product_id` FROM `cart` WHERE `user_id`=?";
										// Prepare the SQL SELECT statement
										$stmt = $con->prepare($sql);
										// Bind the two parameters to the statement
										$stmt->bind_param('s',$_SESSION['id']);
										// Execute the statement
										$stmt->execute();
										// Store the result set
										$stmt->store_result();
										// if number of rows is identical to zero, then add the new item to the cart
										if($stmt->num_rows===0){
									?>
											Your shopping Cart is empty
									<?php }else{ ?>
											<table class="table table-bordered">
												<thead>
													<tr>
														<!-- <th class="cart-romove item">Remove</th> -->
														<th class="cart-description item">Image</th>
														<th class="cart-product-name item">Cake Name</th>
														<th class="cart-qty item">Quantity</th>
														<th class="cart-sub-total item">Price Per Cake</th>
														<th class="cart-sub-total item">Delivery Cost</th>
														<th class="cart-total last-item">Grandtotal</th>
														<th class="cart-romove item">Action</th>
													</tr>
												</thead>
												<tfoot>
													<tr>
														<td colspan="7">
															<div class="shopping-cart-btn">
																<span class="">
																	<a href="index.php" class="btn btn-upper btn-primary outer-left-xs">Continue Shopping</a>
																	<!-- <button type="submit" name="submit" class="btn btn-upper btn-primary pull-right outer-right-xs">
																		Update shopping cart
																	</button> -->
																</span>
															</div><!-- /.shopping-cart-btn -->
														</td>
													</tr>
												</tfoot>
												<tbody>
													<?php
														// SQL SELECT statement with one parameter placeholders
														$sql = "SELECT * FROM `cart` WHERE `user_id`=?";
														// Prepare the SQL SELECT statement
														$stmt = $con->prepare($sql);
														// Bind the one parameter to the statement
														$stmt->bind_param('s',$_SESSION['id']);
														// Execute the statement
														$stmt->execute();
														// Retrieve the result set
														$result = $stmt->get_result();
														// Fetch data
														while ($row = $result->fetch_assoc()){
													?>
															<tr>
																	<?php
																		// SQL SELECT statement with one parameter placeholders
																		$SQL = "SELECT `productImage1`, `shippingCharge` FROM `products` WHERE `id` = ?";
																		// Prepare the SQL SELECT statement
																		$STMT = $con->prepare($SQL);
																		// Bind the one parameter to the statement
																		$STMT->bind_param('s', $row['product_id']);
																		// Execute the statement
																		$STMT->execute();
																		// Retrieve the result set
																		$STMTResult = $STMT->get_result();
																		// Fetch data
																		$STMTRow = $STMTResult->fetch_assoc();
																	?>
																<!-- <td class="romove-item">
																	<input type="checkbox" name="remove_code[]" id="remove_code[]" value="<?php echo htmlspecialchars($row['product_id'],ENT_QUOTES,'UTF-8'); ?>">
																</td> -->
																<td class="cart-image">
																	<a class="entry-thumbnail" href="product-details.php?pid=<?php echo htmlspecialchars($row['product_id'],ENT_QUOTES,'UTF-8'); ?>">
																		<img src="admin/productimages/<?php echo htmlspecialchars($row['product_id'],ENT_QUOTES,'UTF-8'); ?>/<?php echo htmlspecialchars($STMTRow["productImage1"],ENT_QUOTES,'UTF-8'); ?>" alt="" height="100px" width="100px" object-fit="contain">
																	</a>
																</td>
																<td><?php echo htmlspecialchars($row['productName'],ENT_QUOTES,'UTF-8'); ?></td>
																<td><?php echo htmlspecialchars($row['quantity'],ENT_QUOTES,'UTF-8'); ?></td>
																<td><?php echo htmlspecialchars(number_format($row['productPrice'],2),ENT_QUOTES,'UTF-8'); ?></td>
																<td><?php echo htmlspecialchars(number_format($STMTRow["shippingCharge"],2),ENT_QUOTES,'UTF-8'); ?></td>
																<td><?php echo htmlspecialchars(number_format($row['total'],2),ENT_QUOTES,'UTF-8'); ?></td>
																<td>
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#removeItem<?php echo $row['product_id']; ?>">Remove</button>
																		<button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#editQuantity<?php echo $row['product_id']; ?>">Edit</button>
																	</div>
																</td>
																<!-- Modal -->
																<div class="modal fade" id="removeItem<?php echo $row['product_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
																	<form action="" method="post">
																		<div class="modal-dialog modal-dialog-centered" role="document">
																			<div class="modal-content">
																				<div class="modal-header">
																					<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
																					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																					<span aria-hidden="true">&times;</span>
																					</button>
																				</div>
																				<div class="modal-body">
																					<p>Are you sure you want to remove <?php echo$row['productName']; ?> from the cart?</p>
																				</div>
																				<input type="tel" autocomplete="off" name="product_id" id="product_id" value="<?php echo $row['product_id']; ?>" hidden>
																				<div class="modal-footer">
																					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
																					<button type="submit" name="delete" id="delete" class="btn btn-primary">Yes</button>
																				</div>
																			</div>
																		</div>
																	</form>
																</div>

																<div class="modal fade" id="editQuantity<?php echo $row['product_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
																	<form action="" method="post">
																		<div class="modal-dialog modal-dialog-centered" role="document">
																			<div class="modal-content">
																				<div class="modal-header">
																					<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
																					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																					<span aria-hidden="true">&times;</span>
																					</button>
																				</div>
																				<div class="modal-body">
																					<label for="newQuantity">Quantity</label>
																					<input type="tel" autocomplete="off" name="newQuantity" id="newQuantity" class="form-control" placeholder="Quantity" value="<?php echo $row['quantity']; ?>">
																				</div>
																				<input type="tel" autocomplete="off" name="product_id" id="product_id" value="<?php echo $row['product_id']; ?>" hidden>
																				<div class="modal-footer">
																					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
																					<button type="submit" name="saveQuantity" id="saveQuantity" class="btn btn-primary">Yes</button>
																				</div>
																			</div>
																		</div>
																	</form>
																</div>
															</tr>
													<?php } ?>
												</tbody>
											</table>
									<?php } ?>
								</div>
							</form>
						</div>
						<div class="col-md-4 col-sm-12 estimate-ship-tax">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th><span class="estimate-title">Shipping Address</span></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<div class="form-group">
												<?php $qry=mysqli_query($con,"SELECT * FROM  `users` WHERE  id='".$_SESSION['id']."'");
													while ($rt=mysqli_fetch_array($qry)){
														echo htmlentities($rt['shippingAddress'])."<br>";
														echo htmlentities($rt['shippingCity'])."<br>";
														echo htmlentities($rt['shippingState'])."<br>";
														echo htmlentities($rt['shippingPincode'])."<br>";
													}
												?>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-md-4 col-sm-12 estimate-ship-tax">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th><span class="estimate-title">Billing Address</span></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<div class="form-group">
												<?php $qry=mysqli_query($con,"SELECT * FROM  `users` WHERE  id='".$_SESSION['id']."'");
													while ($rt=mysqli_fetch_array($qry)){
														echo htmlentities($rt['billingAddress'])."<br>";
														echo htmlentities($rt['billingCity'])."<br>";
														echo htmlentities($rt['billingState'])."<br>";
														echo htmlentities($rt['billingPincode'])."<br>";
													}
												?>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-md-4 col-sm-12 cart-shopping-total">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>
											<div class="cart-grand-total">
												Grand Total
												<span class="inner-left-md">
													<!-- <?php echo $_SESSION['tp']="$totalprice". ".00"; ?> -->
													<?php
														// SQL SELECT statement with one parameter placeholders
														$sql = "SELECT SUM(total) AS `total` FROM `cart` WHERE `user_id`=?";
														// Prepare the SQL SELECT statement
														$stmt = $con->prepare($sql);
														// Bind the one parameter to the statement
														$stmt->bind_param('s',$_SESSION['id']);
														// Execute the statement
														$stmt->execute();
														// Retrieve the result set
														$stmtResult = $stmt->get_result();
														// Fetch data
														$grandPriceRow = $stmtResult->fetch_assoc();
														echo number_format((int)$grandPriceRow['total'],2);
													?>
												</span>
											</div>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<form action="" method="post">
												<div class="cart-checkout-btn pull-right">
													<button type="submit" name="submitOrder" class="btn btn-primary">PROCCED TO CHEKOUT</button>
												</div>
											</form>
										</td>
									</tr>
								</tbody>
							</table>	 
						</div>
						
					</div>
				</div>
			</div>
		</div>

	<?php include('includes/footer.php');?>

	<script src="assets/js/jquery-1.11.1.min.js"></script>

	<script src="assets/js/bootstrap.min.js"></script>

	<script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
	<script src="assets/js/owl.carousel.min.js"></script>

	<script src="assets/js/echo.min.js"></script>
	<script src="assets/js/jquery.easing-1.3.min.js"></script>
	<script src="assets/js/bootstrap-slider.min.js"></script>
	<script src="assets/js/jquery.rateit.min.js"></script>
	<script type="text/javascript" src="assets/js/lightbox.min.js"></script>
	<script src="assets/js/bootstrap-select.min.js"></script>
	<script src="assets/js/wow.min.js"></script>
	<script src="assets/js/scripts.js"></script>

	<!-- For demo purposes – can be removed on production -->

	<script src="switchstylesheet/switchstylesheet.js"></script>

	<script>
		$(document).ready(function(){ 
			$(".changecolor").switchstylesheet( { seperator:"color"} );
			$('.show-theme-options').click(function(){
				$(this).parent().toggleClass('open');
				return false;
			});
		});

		$(window).bind("load", function() {
		$('.show-theme-options').delay(2000).trigger('click');
		});
	</script>
	<!-- For demo purposes – can be removed on production : End -->
	</body>
</html>