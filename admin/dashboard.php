<?php
	ob_start(); // output puffering start, before session
	session_start();
	if (isset($_SESSION['username'])) {
		$pageTitle = 'Dashboard';
		include 'init.php';
		?>
		<div class="container text-center dash-stats">
			<h1>Dash Board</h1>
			<div class="row">
				<div class="col-3">
					<div class="dash-stat st-members">
						Total Members
						<span><a href="members.php"><?php echo countItems('userID', 'users'); ?></a></span>
					</div>
				</div>
				<div class="col-3">
					<div class="dash-stat st-pending">
						Peding Members
						<span><a href="members.php?do=manage&page=pending"><?php echo checkItem('regstatus', 'users', 0) ?></a></span>
					</div>
				</div>
				<div class="col-3">
					<div class="dash-stat st-items">
						Total Items
						<span><a href="items.php"><?php echo countItems('item_id', 'items'); ?></a></span>
					</div>
				</div>
				<div class="col-3">
					<div class="dash-stat st-comments">
						Total Comments
						<span>12</span>
					</div>
				</div>
			</div>
		</div>
		<div class="container mt-5  latest">
			<div class="row">
				<div class="col-6">
					<div class="card">
						<?php $latestusers = 5; ?>
						<div class="card-header text-center">
							<i class="fa fa-users mr-2"></i>Latest <?php echo $latestusers; ?> Registered Users
						</div>
						<div class="card-body">
							<ul class="list-group list-group-flush">
								<?php
									$latest = getLatest('*', 'users', 'userID', $latestusers);
									foreach ($latest as $user) {
										echo '<li class="list-group-item">' . $user['username'] .
											'<a href="members.php?do=edit&userid=' . $user['userID'] . '"> <span class="btn btn-success float-right ">
										<i class="fa fa-edit"></i>Edit</span></a>';
										if ($user['regstatus'] == 0) {
											echo '<a href="members.php?do=activate&userid=' . $user['userID'] . '" class="btn btn-secondary float-right mr-1"><i class="fa fa-edit"></i>Active</a>';
										}
										echo '</li>';
									}
									?>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-6">
					<div class="card">
						<div class="card-header">
							<i class="fa fa-tag mr-2"></i>Latest Added Items
						</div>
						<div class="card-body">
							Test
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		include $tpl . 'footer.php';
	} else {
		//  echo'You are not Autharized ';
		header('Location: index.php');
		exit;
	}
	ob_end_flush();
?>