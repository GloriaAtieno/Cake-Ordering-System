<?php
  session_start();
  error_reporting(0);
  include('includes/config.php');

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

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="keywords" content="">
<meta name="description" content="">
<title>Glowreea's Confectionery Corner</title>
<?php
  if (isset($_GET["login"])==true && $_GET["login"] == "login")
  {
    echo "<script>alert('Please login to place your order');</script>";
  }
?>
<!--Bootstrap -->
<link rel="stylesheet" href="assets2/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets2/css/style.css" type="text/css">
<link rel="stylesheet" href="assets2/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets2/css/owl.transitions.css" type="text/css">
<link href="assets2/css/slick.css" rel="stylesheet">
<link href="assets2/css/bootstrap-slider.min.css" rel="stylesheet">
<link href="assets2/css/font-awesome.min.css" rel="stylesheet">

<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets2/images/favicon-icon/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets2/images/favicon-icon/apple-touch-icon-114-precomposed.html">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets2/images/favicon-icon/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="assets2/images/favicon-icon/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="assets2/images/favicon-icon/favicon.png">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
<!-- CSS -->
<link href="style.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
<link href='files/dist/themes/fontawesome-stars.css' rel='stylesheet' type='text/css'>
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
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>

<!-- Script -->
<script src="jquery-3.0.0.js" type="text/javascript"></script>
<script src="files/dist/jquery.barrating.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(function() {
    $('.rating').barrating({
        theme: 'fontawesome-stars',
        onSelect: function(value, text, event) {

            // Get element id by data-id attribute
            var el = this;
            var el_id = el.$elem.data('id');

            // rating was selected by a user
            if (typeof(event) !== 'undefined') {

                var split_id = el_id.split("_");

                var postid = split_id[1];  // postid

                // AJAX Request
                $.ajax({
                    url: 'rating_ajax.php',
                    type: 'post',
                    data: {postid:postid,rating:value},
                    dataType: 'json',
                    success: function(data){
                        // Update average
                        var average = data['averageRating'];
                        $('#avgrating_'+postid).text(average);
                    }
                });
            }
        }
    });
});

</script>
</head>
<body>


<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>

<!-- Banners -->
<section id="banner" class="banner-section">
  <div class="container">
    <div class="div_zindex">
      <div class="row">
        <div class="col-md-5 col-md-push-7">
          <div class="banner_content">
            <h1>Ever Tasty Ever Delicious.</h1>
            <p>We have more than twenty cake flavours for you to choose. </p>
            <a href="cake-listing.php" class="btn">Order Now <span class="angle_arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span></a> </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /Banners -->


<!-- Resent Cat-->
<section class="section-padding gray-bg">
  <div class="container">
    <div class="section-header text-center">
      <h2>What we have in wait <span> For You</span></h2>

    <div class="row">


      <!-- Recently Listed New Cakes -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="resentnewCake">

      <?php
        $ret=mysqli_query($con,"select * from products ORDER BY id DESC");
        while ($row=mysqli_fetch_array($ret)) { ?>

        <div class="col-list-3">
          <div class="recent-Cake-list">
            <div class="car-info-box">
                <a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>">
                  <img src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" style="width:200px; height:200px;" alt="image">
                </a>
            </div>
            <div class="Cake-title-m" style="color:black;">
              <h4><a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($row['productName']);?></a></h4>
              <span class="price">Ksh <?php echo htmlentities($row['productPrice']);?> <s>  Ksh <?php echo htmlentities($row['productPriceBeforeDiscount']);?></s></span>
              </div>
              <div class="inventory_info_m">
              <form action="" method="post">
                <input type="text" name="productId" id="productId" value="<?php echo $row['id']; ?>" hidden>
                <button type="submit" name="addProductIdToCart" id="addProductIdToCart" class="btn btn-primary btn-block" style="background-color:#428bca;">Add to Cart</button>
                <button type="submit" name="addProductIdToWishlist" id="addProductIdToWishlist" class="btn btn-primary btn-block" style="background-color:#428bca;">
                  <i class="icon fa fa-heart"></i>
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php }?>

      </div>
    </div>
  </div>
</section>
<!-- /Resent Cat -->


<?php
include('includes/config2.php'); ?>
<!--Testimonial -->
<section class="section-padding testimonial-section parallex-bg">
  <div class="container div_zindex">
    <div class="section-header white-text text-center">
      <h2>Our Satisfied Customers</h2>
    </div>
    <div class="row">
      <div id="testimonial-slider">

<?php

$tid=1;
// $sql = "SELECT testimonial.Testimonial,users.FullName from testimonial join users on testimonial.UserEmail=users.EmailId where testimonial.status=:tid";

$sql = "SELECT * FROM `testimonial` WHERE `status`=:tid";
$query = $dbh -> prepare($sql);
$query->bindParam(':tid',$tid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{  ?>


        <div class="testimonial-m">

          <div class="testimonial-content">
            <div class="testimonial-heading">
              <h5><?php echo htmlentities($result->Y);?></h5>
            <p><?php echo htmlentities($result->Testimonial);?></p>
          <!--   <iframe name="Framename" src="rating/index.php" width="100" height="120" frameborder="0" scrolling="no" style="width:100%;"> </iframe> -->

          </div>

        </div>
        </div>

        <?php }} ?>



      </div>
    </div>
  </div>
  <!-- Dark Overlay-->
  <div class="dark-overlay"></div>
  <?php include('includes/footer.php');?>
</section>
<!-- /Testimonial-->


<!--Footer -->

<!-- /Footer-->

<!--Back to top-->
<div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
<!--/Back to top-->

<!--Login-Form -->

<!--/Forgot-password-Form -->

<!-- Scripts -->
<script src="assets2/js/jquery.min.js"></script>
<script src="assets2/js/bootstrap.min.js"></script>
<script src="assets2/js/interface.js"></script>
<!--bootstrap-slider-JS-->
<script src="assets2/js/bootstrap-slider.min.js"></script>
<!--Slider-JS-->
<script src="assets2/js/slick.min.js"></script>
<script src="assets2/js/owl.carousel.min.js"></script>

</body>

</html>