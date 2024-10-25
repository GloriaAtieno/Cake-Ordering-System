<div class="main-header">
		<div class="container">
			<div class="row">
				<!-- ============================================================= LOGO START ============================================================= -->
				<div class="col-xs-12 col-sm-12 col-md-3 logo-holder">
					<div class="logo" style="width: 200px; height: 100px;" >
						<a href="index.php">
							<img src="img/tutsy.jpg" alt="" class="img-responsive" width="200px" height="200px">
						</a>
					</div>
				</div>
				<!-- ============================================================= LOGO END ============================================================= -->

				<!-- ============================================================= SEARCH START ============================================================= -->
				<div class="col-xs-12 col-sm-12 col-md-6 top-search-holder">
					<div class="search-area">
						<form name="search" method="post" action="search-result.php">
							<div class="control-group">

								<input class="search-field" placeholder="Search here..." name="product" required="required" />

								<button class="search-button" type="submit" name="search"></button>    

							</div>
						</form>
					</div>
				</div>
				<!-- ============================================================= SEARCH END ============================================================= -->

				<!-- ============================================================= SHOPPING CART DROPDOWN START============================================================= -->
				<div class="col-xs-12 col-sm-12 col-md-3 animate-dropdown top-cart-row">
					<?php
						if(isset($_SESSION['id'])){
					?>
						<div class="dropdown dropdown-cart">
							<a href="my-cart.php" class="dropdown-toggle lnk-cart">
								<div class="items-cart-inner">
									<div class="total-price-basket">
										<span class="lbl">cart -</span>
										<span class="total-price">
											<span class="sign">Ksh.</span>
											<span class="value">
												<?php
													include('includes/config.php');
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
										</span>
									</div>
									<div class="basket">
										<i class="glyphicon glyphicon-shopping-cart"></i>
									</div>
									<div class="basket-item-count">
										<span class="count">
											<?php
												include('includes/config.php');
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
												// number of rows returned
												echo $stmt->num_rows;
											?>
										</span>
									</div>
								</div>
							</a>
						</div>
					<?php } ?>
				</div>
				<!-- ============================================================= SHOPPING CART DROPDOWN END============================================================= -->
			</div>
		</div>
	</div>
</div>