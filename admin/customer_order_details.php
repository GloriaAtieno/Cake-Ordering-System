<?php
    session_start();
    error_reporting(0);
    include('includes/config.php');

    if(isset($_GET['cust'])==false || empty($_GET['cust'])==true)
    {
        header("Location: customer_reports.php");
        exit();
    }
    else
    {
        $sql00 ="SELECT `id` from `users` WHERE `id`=?";
        $stmt00= $con->prepare($sql00);
        $stmt00->bind_param('s',$_GET['cust']);
        $stmt00->execute();
        $result00 = $stmt00->get_result();
        if ($result00->num_rows !== 1){
            header("Location: customer_reports.php");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Glowreea's Confectionery Corner | Customer purchase Report</title>
		<!-- Font awesome -->
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<!-- Sandstone Bootstrap CSS -->
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<!-- Bootstrap Datatables -->
		<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
		<!-- Bootstrap social button library -->
		<link rel="stylesheet" href="css/bootstrap-social.css">
		<!-- Bootstrap select -->
		<link rel="stylesheet" href="css/bootstrap-select.css">
		<!-- Bootstrap file input -->
		<link rel="stylesheet" href="css/fileinput.min.css">
		<!-- Awesome Bootstrap checkbox -->
		<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
		<!-- Admin Stye -->
		<link rel="stylesheet" href="css/style.css">
		<style>
			.errorWrap
			{
				padding: 10px;
				margin: 0 0 20px 0;
				background: #fff;
				border-left: 4px solid #dd3d36;
				-webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
				box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
			}
			.succWrap
			{
				padding: 10px;
				margin: 0 0 20px 0;
				background: #fff;
				border-left: 4px solid #5cb85c;
				-webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
				box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
			}
		</style>
	</head>
	<body>
		<?php include('includes/header.php');?>
		<div class="">
			<div class="content-wrapper">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<h2 class="page-title">Customer purchase report</h2>
							<div class="panel panel-default">
								<div class="panel-heading">
									<caption>CUSTOMER PURCHASE REPORTS FOR 
                                        <?php
                                            // SQL SELECT statement with one parameter placeholders
                                            $sql01 = "SELECT `name` FROM `users` WHERE `id`=?";
                                            // Prepare the SQL SELECT statement
                                            $stmt01 = $con->prepare($sql01);
                                            // Bind the one parameter to the statement
                                            $stmt01->bind_param('i', $_GET['cust']);
                                            // Execute the statement
                                            $stmt01->execute();
                                            // Retrieve the result set
                                            $result01 = $stmt01->get_result();
                                            // Fetch data
                                            $rows01 = $result01->fetch_assoc();
                                            echo $rows01['name'];
                                        ?>
                                    </caption>
								</div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-primary" style="background-color: blue;" onclick="history.back()">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </button>
                                </div>
								<div class="panel-body">
									<table id="zctb" class="table table-striped table-responsive table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Date</th>
												<th>Product name</th>
												<th>Product price</th>
                                                <th>Quantity</th>
												<th>Total price</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sql02 = "SELECT * FROM `orders` WHERE `user_id`=?";
												// Prepare the SQL SELECT statement
												$stmt02 = $con->prepare($sql02);
                                                // Bind the one parameter to the statement
                                                $stmt02->bind_param('s',$_GET['cust']);
												// Execute the statement
												$stmt02->execute();
												// Retrieve the result set
												$result02 = $stmt02->get_result();
												// Fetch data
												while($row02 = $result02->fetch_assoc()){
											?>
                                                <tr>
                                                <td class="text-nowrap"><?php echo $row02['orderDate']; ?></td>    
                                                <td class="text-nowrap"><?php echo $row02['productName']; ?></td>
                                                <td class="text-nowrap"><?php echo number_format($row02['productPrice'],2); ?></td>
                                                <td class="text-nowrap"><?php echo $row02['quantity']; ?></td>
                                                <td class="text-nowrap"><?php echo number_format($row02['total'],2); ?></td>
                                                </tr>
											<?php } ?>
										</tbody>
										<tfoot>
											<tr>
												<th>Date</th>
												<th>Product name</th>
												<th>Product price</th>
                                                <th>Quantity</th>
												<th>Total price</th>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Loading Scripts -->
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap-select.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/dataTables.bootstrap.min.js"></script>
		<script src="js/Chart.min.js"></script>
		<script src="js/fileinput.js"></script>
		<script src="js/chartData.js"></script>
		<script src="js/main.js"></script>
	</body>
</html>