<!--Bootstrap -->

<link rel="stylesheet" href="assets2/css/style.css" type="text/css">
<link rel="stylesheet" href="assets2/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets2/css/owl.transitions.css" type="text/css">
<link href="assets2/css/slick.css" rel="stylesheet">
<link href="assets2/css/bootstrap-slider.min.css" rel="stylesheet">
<link href="assets2/css/font-awesome.min.css" rel="stylesheet">
<?php
if(isset($_POST['emailsubscibe']))
{
$subscriberemail=$_POST['subscriberemail'];
$sql ="SELECT SubscriberEmail FROM subscribers WHERE SubscriberEmail=:subscriberemail";
$query= $dbh -> prepare($sql);
$query-> bindParam(':subscriberemail', $subscriberemail, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query -> rowCount() > 0)
{
echo "<script>alert('Already Subscribed.');</script>";
}
else{
$sql="INSERT INTO  subscribers(SubscriberEmail) VALUES(:subscriberemail)";
$query = $dbh->prepare($sql);
$query->bindParam(':subscriberemail',$subscriberemail,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
echo "<script>alert('Subscribed successfully.');</script>";
}
else
{
echo "<script>alert('Something went wrong. Please try again');</script>";
}
}
}
?>

<footer>
  <div class="footer-top">
    <div class="container">
      <div class="row">

        <div class="col-md-6">
          <h6>About Us</h6>
          <!-- <ul>


            <li><a href="#">About Us</a></li>
            <li><a href="#">FAQs</a></li>
            <li><a href="#">Privacy</a></li>
            <li><a href="#">Terms of use</a></li>
            <li><a href="admin/">Admin Login</a></li>
          </ul> -->

          <p>"Welcome to Glowreea's Confectionary Corner, where every slice tells a story and every bite is an experience. Nestled in the heart of Nyeri, we are more than just a bakery – we're creators of moments, crafting memories with each delicious creation. From our humble beginnings steeped in a passion for baking, we've grown into a beloved destination for those who appreciate the artistry and taste of finely crafted desserts. At Glowreea's, we believe in infusing every cake, cupcake, and confection with love, care, and a touch of magic. Join us on a journey of sweetness, where every celebration is made extraordinary."</p>

        </div>

        <div class="col-md-3 col-sm-6">
          <h6>Subscribe Newsletter</h6>
          <div class="newsletter-form">
            <form method="post">
              <div class="form-group">
                <input type="email" name="subscriberemail" class="form-control newsletter-input" required placeholder="Enter Email Address" />
              </div>
              <button type="submit" name="emailsubscibe" class="btn btn-block">Subscribe <span class="angle_arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span></button>
            </form>
            <p class="subscribed-text">*We send great deals and product updates to our subscribed users very week.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-md-6 col-md-push-6 text-right">
          <div class="footer_widget">
            <p>Connect with Us:</p>
            <ul>
              <li><a href="#"><i class="fa fa-faceOrder-square" aria-hidden="true"></i></a></li>
              <li><a href="#"><i class="fa fa-twitter-square" aria-hidden="true"></i></a></li>
              <li><a href="#"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a></li>
              <li><a href="#"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a></li>
              <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 col-md-pull-6">
          <p class="copy-right">Copyright &copy; 2024 Glowreea's Confectionery Corner. All Rights Reserved</p>
        </div>
      </div>
    </div>
  </div>
</footer>
