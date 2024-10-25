<?php
    // start session
	session_start();
	// Turn off error reporting
	error_reporting(0);
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submit']) == true)
    {
        include('includes/config.php');

        $sql00 = "SELECT `cart_id` FROM `cart` WHERE `user_id`=?";
        // Prepare the SQL SELECT statement
        $stmt00 = $con->prepare($sql00);
        // Bind the one parameter to the statement
        $stmt00->bind_param('s', $_SESSION['id']);
        // Execute the statement
        $stmt00->execute();
        // Retrieve the result set
        $result = $stmt00->get_result();
        // Fetch data
        $row = $result->fetch_assoc();
        // Value of the cart_id row returned
        $cartIdRow = $row['cart_id'];

        if (empty($cartIdRow)==true){
            header("Location: index.php");
            exit();
        }
        else
        {
            $sql01 = "INSERT INTO `orders`(`user_id`, `product_id`, `productName`, `productPrice`, `quantity`, `total`, `cart_id`) SELECT `user_id`, `product_id`, `productName`, `productPrice`, `quantity`, `total`, `cart_id` FROM `cart` WHERE `user_id`=? AND `cart_id`=?";
            $stmt01 = $con->prepare($sql01);
            $stmt01->bind_param('ss',$_SESSION['id'],$cartIdRow);
            if ($stmt01->execute())
            {
                $sql02 = "SELECT MAX(invoice_number) AS `max_invoice_number` FROM `orders`";
                $stmt02 = $con->prepare($sql02);
                if ($stmt02->execute())
                {
                    // Retrieve the result set
                    $result = $stmt02->get_result();
                    // Fetch data
                    $row = $result->fetch_assoc();
                    // Value of the cart_id row returned
                    $maxInvoiceNumber = $row['max_invoice_number'];

                    if(empty($maxInvoiceNumber)==true)
                    {
                        // Set default timezone to Nairobi,Kenya.
                        date_default_timezone_set('Africa/Nairobi');
                        $date = date('Y-m-d h:i:s', time());

                        $newInvoiceNumber = '01';

                        $orderDate= $date;
                        $paymentMethod= 'COD';
                        $orderStatus= 'We are processing your order';
                        // SQL UPDATE statement with five parameter placeholders
                        $sql03 = "UPDATE `orders` SET `invoice_number`=?, `orderDate`=?, `paymentMethod`=?, `orderStatus`=? WHERE `user_id`=? AND `cart_id`=?";
                        // Prepare the SQL UPDATE statement
                        $stmt03 = $con->prepare($sql03);
                        // Bind the five parameters to the statement
                        $stmt03->bind_param('ssssss',$newInvoiceNumber,$orderDate,$paymentMethod,$orderStatus,$_SESSION['id'],$cartIdRow);
                        if ($stmt03->execute()){
                            // SQL DELETE statement with two parameter placeholders
                            $sql04 = "DELETE FROM `cart` WHERE `user_id`=? AND `cart_id`=?";
                            // Prepare the SQL DELETE statement
                            $stmt04 = $con->prepare($sql04);
                            // Bind the two parameters to the statement
                            $stmt04->bind_param('ss',$_SESSION['id'],$cartIdRow);
                            if ($stmt04->execute()){
                                header("Location: index.php?status=orderComplete");
                                exit();
                            }
                        }
                    }
                    else
                    {
                        // Set default timezone to Nairobi,Kenya.
                        date_default_timezone_set('Africa/Nairobi');
                        $date = date('Y-m-d h:i:s', time());

                        // $newInvoiceNumber = (int)$maxInvoiceNumber+1;
                        $number = (int)$maxInvoiceNumber+1;
                        if ($number<10) {
                            $newInvoiceNumber = '0'.$number;
                        }
                        else
                        {
                            $newInvoiceNumber = $number;
                        }
                        $orderDate= $date;
                        $paymentMethod= 'COD';
                        $orderStatus= 'We are processing your order';
                        // SQL UPDATE statement with five parameter placeholders
                        $sql03 = "UPDATE `orders` SET `invoice_number`=?, `orderDate`=?, `paymentMethod`=?, `orderStatus`=? WHERE `user_id`=? AND `cart_id`=?";
                        // Prepare the SQL UPDATE statement
                        $stmt03 = $con->prepare($sql03);
                        // Bind the five parameters to the statement
                        $stmt03->bind_param('ssssss',$newInvoiceNumber,$orderDate,$paymentMethod,$orderStatus,$_SESSION['id'],$cartIdRow);
                        if ($stmt03->execute()){
                            // SQL DELETE statement with two parameter placeholders
                            $sql04 = "DELETE FROM `cart` WHERE `user_id`=? AND `cart_id`=?";
                            // Prepare the SQL DELETE statement
                            $stmt04 = $con->prepare($sql04);
                            // Bind the two parameters to the statement
                            $stmt04->bind_param('ss',$_SESSION['id'],$cartIdRow);
                            if ($stmt04->execute()){
                                header("Location: index.php?status=orderComplete");
                                exit();
                            }
                        }
                    }
                }
            }
        }
    }
?>
<!DOCTYPE html
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

            <title>Shopping Portal | Payment Method</title>
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
            <link rel="stylesheet" href="assets/css/config.css">
            <link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
            <link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
            <link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
            <link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
            <link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">
            <link rel="stylesheet" href="assets/css/font-awesome.min.css">
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
                            <li class='active'>Payment Method</li>
                        </ul>
                    </div><!-- /.breadcrumb-inner -->
                </div><!-- /.container -->
            </div><!-- /.breadcrumb -->

            <div class="body-content outer-top-bd">
                <div class="container">
                    <div class="checkout-box faq-page inner-bottom-sm">
                        <div class="row">
                            <div class="col-md-12">
                                <h2>Choose Payment Method</h2>
                                <div class="panel-group checkout-steps" id="accordion">
                                    <div class="panel panel-default checkout-step-01">
                                        <div class="panel-heading">
                                            <h4 class="unicase-checkout-title">
                                                <a data-toggle="collapse" class="" data-parent="#accordion" href="#collapseOne">
                                                Select Prefered payment Method
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne" class="panel-collapse collapse in">
                                            <div class="panel-body">
                                                <form name="payment" method="post">
                                                    <input type="radio" name="paymethod" value="COD" checked="checked"> COD
                                                    <!-- <input type="radio" name="paymethod" value="Internet Banking"> Internet Banking -->
                                                    <!-- <input type="radio" name="paymethod" value="Debit / Credit card"> Debit / Credit card -->
                                                    <br/><br/>
                                                    <input type="submit" value="submit" name="submit" class="btn btn-primary">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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