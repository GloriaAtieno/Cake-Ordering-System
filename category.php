<?php
	// Start a new session
    session_start();

    // Turns off error reporting
    error_reporting(0);

    // Include the configuration file
    include('includes/config.php');
	
	if(strlen($_SESSION["login"])==0)
    {
		header("Location: login.php");
		exit();
	}
	elseif (isset($_GET["cid"])==false || empty($_GET["cid"])==true || $_GET["cid"]<="0")
	{
		header("Location: index.php");
		exit();
	}
	else
	{
		// Gets the category ID from the GET request and converts it to an integer
		$cid = intval($_GET["cid"]);

		if(isset($_POST['addProductIdToWishlist']))
		{
			if (isset($_SESSION['id'])==false) {
				header("Location: index.php?login=login");
				exit();
			}
			else
			{
				$productId = trim(mysqli_real_escape_string($con, $_POST['productId']));
				$productId = htmlspecialchars($productId,ENT_QUOTES,'UTF-8');

				$userId = trim(mysqli_real_escape_string($con, $_SESSION['id']));
				$userId = htmlspecialchars($_SESSION['id'],ENT_QUOTES,'UTF-8');

				// Set default timezone to Nairobi,Kenya.
				date_default_timezone_set('Africa/Nairobi');
				$postingDate = date('Y-m-d h:i:s', time());

				// SQL SELECT statement with one parameter placeholder
				$sql = 'SELECT `productId` FROM `wishlist` WHERE `productId`=? AND `userId`=?';
				// Prepare the SQL SELECT statement
				$stmt = $con->prepare($sql);
				// Bind the one parameter to the statement
				$stmt->bind_param('ss',$productId, $userId);
				// Execute the statement
				$stmt->execute();
				// Store the result set
				$stmt->store_result();
				// if number of rows is identical to zero, then redirect to index.php webpage
				if($stmt->num_rows===0)
				{
					// SQL INSERT statement with five parameters placeholders
					$sql = "INSERT INTO `wishlist`(`userId`, `productId`, `postingDate`) VALUES (?,?,?)";
					// Prepare the SQL INSERT statement
					$stmt = $con->prepare($sql);
					// Bind the two parameters to the statement
					$stmt->bind_param('sss',$userId,$productId,$postingDate);
					// if the statement is executed successfully, then redirect to my-wishlist.php webpage with success message
					if ($stmt->execute())
					{
						header("Location: my-wishlist.php?success=true");
						exit();
					}
					// if the statement is not executed successfully, then redirect to my-wishlist.php webpage with error message
					else
					{
						header("Location: my-wishlist.php?success=false");
						exit();
					}
				}
				else
				{
					header("Location: my-wishlist.php");
					exit();
				}
			}
		}

		if(isset($_POST['addProductIdToCart']))
		{
			if (isset($_SESSION['id'])==false) {
			header("Location: index.php?login=login");
			exit();
			}
			else
			{
				$productId = trim(mysqli_real_escape_string($con, $_POST['productId']));
				$productId = htmlspecialchars($productId,ENT_QUOTES,'UTF-8');

				$userId = trim(mysqli_real_escape_string($con, $_SESSION['id']));
				$userId = htmlspecialchars($_SESSION['id'],ENT_QUOTES,'UTF-8');

				// SQL SELECT statement with one parameter placeholder
				$sql = 'SELECT `id` FROM `products` WHERE `id`=?';
				// Prepare the SQL SELECT statement
				$stmt = $con->prepare($sql);
				// Bind the one parameter to the statement
				$stmt->bind_param('s',$productId);
				// Execute the statement
				$stmt->execute();
				// Store the result set
				$stmt->store_result();
				// if number of rows is identical to zero, then redirect to index.php webpage
				if($stmt->num_rows===0)
				{
					header("Location: index.php");
					exit();
				}
				// if number of rows is not identical to zero
				else
				{
					// SQL SELECT statement with one parameter placeholders
					$sql = "SELECT `id`,`productName`,`productPrice`, `shippingCharge` FROM `products` WHERE `id`=?";
					// Prepare the SQL SELECT statement
					$stmt = $con->prepare($sql);
					// Bind the one parameter to the statement
					$stmt->bind_param('s', $productId);
					// Execute the statement
					$stmt->execute();
					// Retrieve the result set
					$result = $stmt->get_result();
					// Fetch data
					$row = $result->fetch_assoc();
					// Value of the id row returned
					$idRow = $row['id'];
					// Value of the productName row returned
					$productNameRow = $row['productName'];
					// Value of the productPrice row returned
					$productPriceRow = $row['productPrice'];
					// the default quantity to add is string 1
					$quantityRow = "1";
					// total by multipliying price and quantity
					$totalRow = ((int)$productPriceRow*(int)$quantityRow)+(int)$row['shippingCharge'];

					// if either idRow, productNameRow, productPriceRow or quantityRow is empty, then redirect to index.php webpage
					if (empty($idRow)===true || empty($productNameRow)===true || empty($productPriceRow)===true || empty($quantityRow)===true)
					{
						header("Location: index.php");
						exit();
					}
					else
					{
						// SQL SELECT statement with two parameters placeholders
						$sql = "SELECT `product_id` FROM `cart` WHERE `user_id`=? AND `product_id`=?";
						// Prepare the SQL SELECT statement
						$stmt = $con->prepare($sql);
						// Bind the two parameters to the statement
						$stmt->bind_param('ss',$userId,$idRow);
						// Execute the statement
						$stmt->execute();
						// Store the result set
						$stmt->store_result();
						// if number of rows is identical to zero, then add the new item to the cart
						if($stmt->num_rows===0)
						{
							// SQL with parameters
							$sql00 = 'SELECT `cart_id` FROM `cart` WHERE `user_id`=?';
							// Prepare the SQL statement
							$stmt00 = $con->prepare($sql00);
							// Bind $Stockcode valiable to the parameter
							$stmt00->bind_param('s',$userId);
							// Execute the SQL statement
							$stmt00->execute();
							// Get the result
							$result = $stmt00->get_result();
							while ($row = $result->fetch_assoc())
							{$cartIdRow = $row['cart_id'];}

							if
							(empty($cartIdRow)==false)
							{
								$cart_id = $cartIdRow;
							}
							else
							{
								// get cryptographically secure random bytes (4 characters)
								$genCartId = random_bytes(2);
								// genCartId all in uppercase
								$genCartId = strtoupper(bin2hex($genCartId));

								$sql001 = 'SELECT `cart_id` FROM `cart` WHERE `cart_id`=?';
								$stmt01 = $con->prepare($sql001);
								$stmt01->bind_param('s',$genCartId);
								$stmt01->execute();
								$stmt01->store_result();

								$sql02 = 'SELECT `cart_id` FROM `orders` WHERE `cart_id`=?';
								$stmt02 = $con->prepare($sql02);
								$stmt02->bind_param('s',$genCartId);
								$stmt02->execute();
								$stmt02->store_result();

								do
								{
								// get cryptographically secure random bytes (4 characters)
								$genCartId = random_bytes(2);
								// genCartId all in uppercase
								$genCartId = strtoupper(bin2hex($genCartId));
								}
								while ($stmt01->num_rows()>0 || $stmt02->num_rows()>0);
								$cart_id = $genCartId;
							}

							// SQL INSERT statement with five parameters placeholders
							$sql = "INSERT INTO `cart`(`user_id`, `product_id`, `productName`, `productPrice`, `quantity`, `total`, `cart_id`) VALUES (?,?,?,?,?,?,?)";
							// Prepare the SQL INSERT statement
							$stmt = $con->prepare($sql);
							// Bind the two parameters to the statement
							$stmt->bind_param('sssssss',$userId,$idRow,$productNameRow,$productPriceRow,$quantityRow,$totalRow,$cart_id);
							// if the statement is executed successfully, then redirect to index.php webpage with success message
							if ($stmt->execute())
							{
								header("Location: index.php?success=true");
								exit();
							}
							// if the statement is not executed successfully, then redirect to index.php webpage with error message
							else
							{
								header("Location: index.php?success=false");
								exit();
							}
						}
						// if number of rows is not identical to zero, then add one to the current quantity of the new item in the cart
						else
						{
							// SQL SELECT statement with one parameter placeholders
							$sql = "SELECT `shippingCharge` FROM `products` WHERE `id`=?";
							// Prepare the SQL SELECT statement
							$stmt = $con->prepare($sql);
							// Bind the one parameter to the statement
							$stmt->bind_param('s', $productId);
							// Execute the statement
							$stmt->execute();
							// Retrieve the result set
							$result = $stmt->get_result();
							// Fetch data
							$row = $result->fetch_assoc();
							// Value of the shippingCharge row returned
							$shippingChargeRow = $row['shippingCharge'];

							// SQL SELECT statement with three parameter placeholders
							$sql = "SELECT `quantity` FROM `cart` WHERE `user_id`=? AND `product_id`=? AND `productName`=?";
							// Prepare the SQL SELECT statement
							$stmt = $con->prepare($sql);
							// Bind the three parameters to the statement
							$stmt->bind_param('sss',$userId,$idRow,$productNameRow);
							// Execute the statement
							$stmt->execute();
							// Retrieve the result set
							$result = $stmt->get_result();
							// Fetch data
							$row = $result->fetch_assoc();
							// Value of the id row returned
							$quantityRow = $row['quantity'];
							// Add integer one to $quantityRow as an integer value
							$newQuantity = (int)$quantityRow+1;
							// total by multipliying price and new quantity
							$totalRow = ((int)$productPriceRow*(int)$newQuantity)+$shippingChargeRow;

							// SQL UPDATE statement with four parameter placeholders
							$sql = "UPDATE `cart` SET `quantity`=?, `total`=? WHERE `user_id`=? AND `product_id`=? AND `productName`=?";
							// Prepare the SQL UPDATE statement
							$stmt = $con->prepare($sql);
							// Bind the four parameters to the statement
							$stmt->bind_param('sssss',$newQuantity,$totalRow,$userId,$idRow,$productNameRow);
							// if the statement is executed successfully, then redirect to index.php webpage with success message
							if ($stmt->execute())
							{
								header("Location: index.php?success=true");
								exit();
							}
							// if the statement is not executed successfully, then redirect to index.php webpage with error message
							else
							{
								header("Location: index.php?success=false");
								exit();
							}
						}
					}
				}
			}
		}
?>
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<!-- The 'charset' attribute in the 'meta' tag specifies the character encoding for the HTML document. -->
			<meta charset="utf-8">
			<!-- The 'Content-Type' meta tag is used to specify the document's mediatype (in this case, text/html) and its character set (UTF-8). -->
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<!-- The 'viewport' meta tag is used to control the viewport size and scaling of the web page. -->
			<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
			<!-- The 'description' meta tag provides a brief description of the web page. -->
			<meta name="description" content="">
			<!-- The 'author' meta tag specifies the author of the web page. -->
			<meta name="author" content="">
			<!-- The 'keywords' meta tag contains a list of keywords that describe the web page. -->
			<meta name="keywords" content="MediaCenter, Template, eCommerce">
			<!-- The 'robots' meta tag provides instructions to web crawlers and indexing bots on how to crawl and index the web page. -->
			<meta name="robots" content="all">
			<!-- The title of the web page -->
			<title>Product Category</title>
			<!-- Bootstrap Core CSS -->
			<link rel="stylesheet" href="assets/css/bootstrap.min.css">
			<!-- Customizable CSS -->
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
		</head>
		<body class="cnt-home">
			<header class="header-style-1">
				<?php include('includes/top-header.php');?>
				<?php include('includes/main-header.php');?>
				<?php include('includes/menu-bar.php');?>
			</header>
		<div class="body-content outer-top-xs">
			<div class='container'>
				<div class='row outer-bottom-sm'>
					<div class='col-md-3 sidebar'>
							<div class="side-menu animate-dropdown outer-bottom-xs">
								<div class="side-menu animate-dropdown outer-bottom-xs">
									<div class="head"><i class="icon fa fa-align-justify fa-fw"></i>Child Categories</div>        
										<nav class="yamm megamenu-horizontal" role="navigation">
											<ul class="nav">
												<li class="dropdown menu-item">
													<?php $sql=mysqli_query($con,"select id,subcategory  from subcategory where categoryid='$cid'");
													while($row=mysqli_fetch_array($sql)) { ?>
														<a href="sub-category.php?scid=<?php echo $row['id'];?>" class="dropdown-toggle"><i class="icon fa fa-spoon fa-fw"></i>
														<?php echo $row['subcategory'];?></a>
													<?php }?>	
												</li>
											</ul>
										</nav>
									</div>
								</div>
								<div class="sidebar-module-container">
									<h3 class="section-title">shop by</h3>
									<div class="sidebar-filter">
										<div class="sidebar-widget wow fadeInUp outer-bottom-xs ">
											<div class="widget-header m-t-20">
												<h4 class="widget-title">Category</h4>
											</div>
											<div class="sidebar-widget-body m-t-10">
												<?php $sql=mysqli_query($con,"select id,categoryName  from category");
												while($row=mysqli_fetch_array($sql)) { ?>
													<div class="accordion">
														<div class="accordion-group">
															<div class="accordion-heading">
																<a href="category.php?cid=<?php echo $row['id'];?>"  class="accordion-toggle collapsed">
																	<?php echo $row['categoryName'];?>
																</a>
															</div>  
														</div>
													</div>
												<?php } ?>
											</div><!-- /.sidebar-widget-body -->
										</div><!-- /.sidebar-widget -->
									</div><!-- /.sidebar-filter -->
								</div><!-- /.sidebar-module-container -->
					</div><!-- /.sidebar -->

					<div class='col-md-9'>
						<div id="category" class="category-carousel hidden-xs">
							<div class="item">	
								<div class="image">
									<img src="assets/images/banners/cat-banner-1.jpg" alt="" class="img-responsive">
								</div>
								<div class="container-fluid">
									<div class="caption vertical-top text-left">
										<div class="big-text"><br /></div>
											<?php $sql=mysqli_query($con,"select categoryName  from category where id='$cid'");
											while($row=mysqli_fetch_array($sql)){ ?>
												<div class="excerpt hidden-sm hidden-md">
													<?php echo htmlentities($row['categoryName']);?>
												</div>
											<?php } ?>
									</div><!-- /.caption -->
								</div><!-- /.container-fluid -->
							</div>
						</div>

						<div class="search-result-container" style="margin-top:70px;">
							<div id="myTabContent" class="tab-content">
								<div class="tab-pane active " id="grid-container">
										<div class="category-product  inner-top-vs">
											<div class="row">
												<?php
													$ret=mysqli_query($con,"select * from products where category='$cid'");
													$num=mysqli_num_rows($ret);
													if($num>0){
													while ($row=mysqli_fetch_array($ret)) {
												?>
													<div class="col-sm-6 col-md-4 wow fadeInUp">
														<div class="products">
															<div class="product">		
																<div class="product-image">
																	<div class="image">
																		<a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><img  src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" alt="" height="200px" width="100%" object-fit="contain"></a>
																	</div><!-- /.image -->			                      		   
																</div><!-- /.product-image -->
																<div class="product-info text-left" style="margin-top:30px;">
																	<h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($row['productName']);?></a></h3>
																	<div class="rating rateit-small"></div>
																	<div class="description"></div>
																	<div class="product-price">	
																		<span class="price">
																			Ksh. <?php echo htmlentities($row['productPrice']);?>
																		</span>
																		<span class="price-before-discount">
																			Ksh. <?php echo htmlentities($row['productPriceBeforeDiscount']);?>
																		</span>
																	</div><!-- /.product-price -->
																</div><!-- /.product-info -->
																<div class="cart clearfix animate-effect">
																	<form action="" method="post">
																		<input type="text" name="productId" id="productId" value="<?php echo $row['id']; ?>" hidden>
																		<div class="button-group">
																			<button type="submit" name="addProductIdToCart" id="addProductIdToCart" class="btn btn-primary btn-block" style="background-color:#428bca;">Add to Cart</button>
																			<button type="submit" name="addProductIdToWishlist" id="addProductIdToWishlist" class="btn btn-primary btn-block" style="background-color:#428bca;">
																				<i class="icon fa fa-heart"></i>
																			</button>
																		</div>
																	</form>
																</div>
															</div>
														</div>
													</div>
												<?php }} else { ?>
													<div class="col-sm-6 col-md-4 wow fadeInUp">
														<h3>No Cake Found</h3>
													</div>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div><!-- /.col -->
				</div>
				<?php include('includes/Ksh.-slider.php');?>
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
		</body>
	</html>
<?php } ?>