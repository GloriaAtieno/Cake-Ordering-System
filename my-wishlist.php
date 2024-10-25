<?php
	session_start();
	error_reporting(0);
	include('includes/config.php');
	if(strlen($_SESSION['login'])==0)
	{   
		header('location:index.php');
	}
	
	if(isset($_GET['del']))
	{
		if (isset($_SESSION['id'])==false)
		{
			header("Location: index.php?login=login");
			exit();
		}
		else
		{
			$productId = trim(mysqli_real_escape_string($con, $_POST['productId']));
			$productId = htmlspecialchars($productId,ENT_QUOTES,'UTF-8');

			$userId = trim(mysqli_real_escape_string($con, $_SESSION['id']));
			$userId = htmlspecialchars($_SESSION['id'],ENT_QUOTES,'UTF-8');

			// SQL DELETE statement with one parameter placeholder
			$sql = 'DELETE FROM `wishlist` WHERE `userId`=? AND `productId`=?';
			// Prepare the SQL DELETE statement
			$stmt = $con->prepare($sql);
			// Bind the one parameter to the statement
			$stmt->bind_param('ss',$userId, $productId);
			// Execute the statement
			if ($stmt->execute())
			{
				header("Location: my-wishlist.php");
				exit();
			}
			else
			{
				header("Location: my-wishlist.php?error");
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
		<!-- Meta -->
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">
	    <meta name="keywords" content="MediaCenter, Template, eCommerce">
	    <meta name="robots" content="all">

	    <title>My Wishlist</title>
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
		<link rel="shortcut icon" href="assets/images/favicon.ico">
	</head>
	<body class="cnt-home">
		<header class="header-style-1">
			<?php include('includes/top-header.php');?>
			<?php include('includes/main-header.php');?>
			<?php include('includes/menu-bar.php');?>
		</header>

		<div class="breadcrumb">
			<div class="container">
				<div class="breadcrumb-inner">
					<ul class="list-inline list-unstyled">
						<li><a href="home.html">Home</a></li>
						<li class='active'>Wishlish</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="body-content outer-top-bd">
			<div class="container">
				<div class="my-wishlist-page inner-bottom-sm">
					<div class="row">
						<div class="col-md-12 my-wishlist">
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th colspan="4">my wishlist</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$ret=mysqli_query($con,"select products.productName as pname,products.productName as proid,products.productImage1 as pimage,products.productPrice as pprice,wishlist.productId as pid,wishlist.id as wid from wishlist join products on products.id=wishlist.productId where wishlist.userId='".$_SESSION['id']."'");
											$num=mysqli_num_rows($ret);
											if($num>0){
											while ($row=mysqli_fetch_array($ret)) {
										?>
											<tr>
												<td class="col-md-2"><img src="admin/productimages/<?php echo htmlentities($row['pid']);?>/<?php echo htmlentities($row['pimage']);?>" alt="<?php echo htmlentities($row['pname']);?>" width="120"></td>
												<td class="col-md-6">
													<div class="product-name"><a href="product-details.php?pid=<?php echo htmlentities($pd=$row['pid']);?>"><?php echo htmlentities($row['pname']);?></a></div>
													<?php $rt=mysqli_query($con,"select * from productreviews where productId='$pd'");
													$num=mysqli_num_rows($rt);
{
													?>
													<div class="rating">
														<i class="fa fa-star rate"></i>
														<i class="fa fa-star rate"></i>
														<i class="fa fa-star rate"></i>
														<i class="fa fa-star rate"></i>
														<i class="fa fa-star non-rate"></i>
														<span class="review">( <?php echo htmlentities($num);?> Reviews )</span>
													</div>
													<?php } ?>
													<div class="price">Ksh. 
														<?php echo htmlentities($row['pprice']);?>.00
														<span><!--price before --></span>
													</div>
												</td>
												<td class="col-md-2">
													<form action="" method="post">
														<input type="text" name="productId" id="productId" value="<?php echo $row['id']; ?>" hidden>
														<button type="submit" name="addProductIdToCart" id="addProductIdToCart" class="btn btn-primary btn-block" style="background-color:#428bca;">Add to Cart</button>
													</form>
												</td>
												<td class="col-md-2 close-btn">
													<a href="my-wishlist.php?del=<?php echo htmlentities($row['productId']);?>" onClick="return confirm('Are you sure you want to delete?')" class=""><i class="fa fa-times"></i></a>
												</td>
											</tr>
										<?php }}else{ ?>
										<tr>
											<td style="font-size: 18px; font-weight:bold ">Your Wishlist is Empty</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
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
	</body>
</html>