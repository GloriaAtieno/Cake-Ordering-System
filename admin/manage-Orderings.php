<?php
	session_start();
	error_reporting(0);
	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitOrderStatus']) == true)
	{
		include('includes/config.php');
		$orderId = trim(mysqli_real_escape_string($con, $_POST['orderId']));
      	$orderId = htmlspecialchars($orderId,ENT_QUOTES,'UTF-8');

		$orderstatus = trim(mysqli_real_escape_string($con, $_POST['orderstatus']));
		$orderstatus = htmlspecialchars($orderstatus,ENT_QUOTES,'UTF-8');

		// SQL UPDATE statement with one parameter placeholders
		$sql00 = 'UPDATE `orders` SET `orderStatus`=? WHERE `cart_id`=?';
		// Prepare the SQL UPDATE statement
		$stmt00 = $con->prepare($sql00);
		// Bind the one parameter to the statement
		$stmt00->bind_param('ss',$orderstatus,$orderId);
		// Execute the statement
		if ($stmt00->execute())
		{header("Location: manage-Orderings.php?success=true");exit();}
		else
		{header("Location: manage-Orderings.php?success=false");exit();}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Glowreea's Confectionery Corner | Orders</title>
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
		<div class="ts-main-content">
			<?php include('includes/leftbar.php');?>
			<div class="content-wrapper">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<h2 class="page-title">Manage Orders</h2>
							<div class="panel panel-default">
								<button onClick="window.print()">Print Orders</button>
								<div class="panel-heading">Orders Info</div>
								<div class="panel-body">
									<!-- <?php if($error){?>
										<div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?></div>
									<?php } elseif($msg){?>
										<div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?></div>
									<?php }?> -->
									<table id="zctb" class="table table-striped table-responsive table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>No.</th>
												<th>Name</th>
												<th>Phone</th>
												<th>Quantity</th>
												<th>Cost</th>
												<th>Payment Method</th>
												<th>Status</th>
												<th>Order date</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												include('includes/config.php');
												$sql00 = "SELECT * FROM `orders` GROUP BY `invoice_number`";
												// Prepare the SQL SELECT statement
												$stmt00 = $con->prepare($sql00);
												// Execute the statement
												$stmt00->execute();
												// Retrieve the result set
												$result00 = $stmt00->get_result();
												// Fetch data
												while($row00 = $result00->fetch_assoc()){
												$cnt=$cnt+1;
											?>
												<tr>
													<td><?php echo $cnt; ?></td>
													<?php
														// SQL SELECT statement with one parameter placeholders
														$sql01 = "SELECT `name`, `contactno` FROM `users` WHERE `id`=?";
														// Prepare the SQL SELECT statement
														$stmt01 = $con->prepare($sql01);
														// Bind the one parameter to the statement
														$stmt01->bind_param('i', $row00['user_id']);
														// Execute the statement
														$stmt01->execute();
														// Retrieve the result set
														$result01 = $stmt01->get_result();
														// Fetch data
														while($rows01 = $result01->fetch_assoc())
														{
													?>
													<td class="text-nowrap"><?php echo $rows01['name']; ?></td>
													<td><a href="tel:<?php echo $rows01['contactno']; ?>" style="color: #3E3F3A !important;"><?php echo $rows01['contactno']; ?></a></td>
													<?php } ?>
													<td><?php echo $row00['quantity']; ?></td>
													<td>
														<?php
															// SQL SELECT statement with one parameter placeholders
															$sql02 = "SELECT SUM(total) AS `total` FROM `orders` WHERE `invoice_number`=? AND `cart_id`=?";
															// Prepare the SQL SELECT statement
															$stmt02 = $con->prepare($sql02);
															// Bind the one parameter to the statement
															$stmt02->bind_param('ss',$row00['invoice_number'],$row00['cart_id']);
															// Execute the statement
															$stmt02->execute();
															// Retrieve the result set
															$result02 = $stmt02->get_result();
															// Fetch data
															while($row02 = $result02->fetch_assoc())
															{echo number_format((int)$row02['total'],2);}
														?>
													</td>
													<td><?php echo $row00['paymentMethod']; ?></td>
													<td><?php echo $row00['orderStatus']; ?></td>
													<td><?php echo $row00['orderDate']; ?></td>
													<td>
														<div class="btn-group">
															<button type="button" style="background-color:blue" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#<?php echo $row00['cart_id']; ?>">
																View
															</button>
															<button type="button" style="background-color:blue" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#update<?php echo $row00['cart_id']; ?>">
																Update
															</button>
														</div>

														<div class="modal fade" id="<?php echo $row00['cart_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="title" aria-hidden="true">
															<div class="modal-dialog" role="document">
																<div class="modal-content">
																<div class="modal-header">
																	<h5 class="modal-title" id="title">Order <?php echo $row00['cart_id']; ?></h5>
																	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																	<span aria-hidden="true">&times;</span>
																	</button>
																</div>
																<div class="modal-body">
																	<?php
																		$sql03 = "SELECT * FROM `orders` WHERE `invoice_number`=? AND `user_id`=? AND `cart_id`=?";
																		// Prepare the SQL SELECT statement
																		$stmt03 = $con->prepare($sql03);
																		// Bind the three parameters to the statement
																		$stmt03->bind_param('sss',$row00['invoice_number'],$row00['user_id'],$row00['cart_id']);
																		// Execute the statement
																		$stmt03->execute();
																		// Retrieve the result set
																		$result03 = $stmt03->get_result();
																		// Fetch data
																		while($row03 = $result03->fetch_assoc())
																		{
																	?>
																		<dl>
																			<dt>Product name: <?php echo $row03['productName'];?></dt>	
																			<dd>Product price: <?php echo number_format($row03['productPrice'],2);?></dd>
																			<dd>Quantity: <?php echo number_format($row03['quantity'],2);?></dd>
																		</dl>
																		<hr>
																	<?php } ?>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
																	<button type="button" class="btn btn-primary">Save changes</button>
																</div>
																</div>
															</div>
														</div>

														<div class="modal fade" id="update<?php echo $row00['cart_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateTitle" aria-hidden="true">
															<div class="modal-dialog" role="document">
																<div class="modal-content">
																	<form action="" method="post">
																		<div class="modal-header">
																			<h5 class="modal-title" id="updateTitle">Update order <?php echo $row00['cart_id']; ?></h5>
																			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																			<span aria-hidden="true">&times;</span>
																			</button>
																		</div>
																		<div class="modal-body">
																				<input type="text" name="orderId" id="orderId" hidden value="<?php echo $row00['cart_id']; ?>">
																				<label for="orderstatus">Order status</label><br>
																				<textarea name="orderstatus" id="orderstatus" class="form-control" cols="50" rows="5"></textarea>
																		</div>
																		<div class="modal-footer">
																			<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
																			<button type="submit" style="background-color:blue;" name="submitOrderStatus" id="submitOrderStatus" class="btn btn-primary">Save changes</button>
																		</div>
																	</form>
																</div>
															</div>
														</div>
													</td>
												</tr>
											<?php } ?>
										</tbody>
										<tfoot>
											<tr>
												<th>No.</th>
												<th>Name</th>
												<th>Phone</th>
												<th>Quantity</th>
												<th>Cost</th>
												<th>Payment Method</th>
												<th>Status</th>
												<th>Order date</th>
												<th>Action</th>
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