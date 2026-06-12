<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<title>aMule Control Panel - Kademlia</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="custom.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="i18n.js"></script>
</head>

<body class="amule-bs5">

	<!-- Navigation bar -->
	<nav class="navbar navbar-expand-lg fixed-top amule-navbar">
		<div class="container-fluid">
			<a class="navbar-brand" href="amuleweb-main-kad.php">
				<img src="logo-nav-brax.png" class="logo-nav" alt="aMule">
				aMule <span class="amule-brand-rest">WebUI</span>
				<small style="font-size:11px;">powered by micromante <span style="color:#e74c3c;">&#10084;</span></small>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="mainNav">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link" title="Downloads and Uploads" href="amuleweb-main-dload.php"><i class="bi bi-arrow-down-up"></i> Transfer</a></li>
					<li class="nav-item"><a class="nav-link" title="Sharing" href="amuleweb-main-shared.php"><i class="bi bi-share-fill"></i> Shared</a></li>
					<li class="nav-item"><a class="nav-link" title="Search" href="amuleweb-main-search.php"><i class="bi bi-search"></i> Search</a></li>
					<li class="nav-item"><a class="nav-link" title="Servers" href="amuleweb-main-servers.php"><i class="bi bi-hdd-network"></i> Server</a></li>
					<li class="nav-item"><a class="nav-link active" title="Kademlia" href="amuleweb-main-kad.php"><i class="bi bi-diagram-3"></i> Kad</a></li>
					<li class="nav-item"><a class="nav-link" title="Statistics" href="amuleweb-main-stats.php"><i class="bi bi-bar-chart-line"></i> Stats</a></li>
					<li class="nav-item"><a class="nav-link" title="Configurations" href="amuleweb-main-prefs.php"><i class="bi bi-gear"></i> Settings</a></li>
					<li class="nav-item"><a class="nav-link" title="Log" href="amuleweb-main-log.php"><i class="bi bi-card-list"></i> Logs</a></li>
					<li class="nav-item"><a class="nav-link" title="Exit" href="login.php"><i class="bi bi-box-arrow-right"></i> Exit</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container-fluid" style="max-width:1000px;">

	<!-- Bootstrap from node -->
	<form name="mainform" action="amuleweb-main-kad.php" method="get" role="form">
	<div class="card mt-3">
		<div class="card-body">
			<div class="row g-2 align-items-center">
				<div class="col-12 col-md-auto"><b style="color:#4db6ac;">Bootstrap from Node</b></div>
				<div class="col-12 col-md-auto">
					<div class="input-group" style="max-width:340px;">
						<input class="form-control text-center px-1" type="text" name="ip3" maxlength="3" placeholder="255">
						<span class="input-group-text px-1">.</span>
						<input class="form-control text-center px-1" type="text" name="ip2" maxlength="3" placeholder="255">
						<span class="input-group-text px-1">.</span>
						<input class="form-control text-center px-1" type="text" name="ip1" maxlength="3" placeholder="255">
						<span class="input-group-text px-1">.</span>
						<input class="form-control text-center px-1" type="text" name="ip0" maxlength="3" placeholder="255">
						<span class="input-group-text px-1">:</span>
						<input class="form-control text-center px-1" type="text" name="port" maxlength="5" placeholder="Port" style="max-width:64px;">
					</div>
				</div>
				<div class="col-12 col-md-auto">
					<input class="btn btn-warning w-100" type="submit" name="Submit" value="Connect">
				</div>
			</div>
		</div>
	</div>
	</form>

	<!-- KAD status -->
	<div class="card">
		<div class="card-header">KAD STATUS</div>
		<div class="card-body">
			<?php
				function print_table($kad1, $kad2) {
					echo '<table class="table align-middle" style="width:auto;margin:0 auto;">
					<thead>
						<tr><th>Parameter</th><th>Status</th></tr>
					</thead>
					<tbody>
						<tr><td>Connection</td><td>'.$kad1.'</td></tr>
						<tr><td>Firewall</td><td>'.$kad2.'</td></tr>
						<tr><td colspan="2" style="text-align:center;">
							<img src="amule_stats_kad.png" class="img-fluid" border="0" alt="" title="" />
						</td></tr>
					</tbody>
				</table>';
				}

				function print_kad1($status) {
					if ($status["kad_connected"] == 1) {
						if ($status["kad_firewalled"] == 0) { return '<span class="badge text-bg-success">OK</span>'; }
						else { return '<span class="badge text-bg-warning">OK</span>'; }
					} else {
						return '<span class="badge text-bg-danger">ERROR</span>';
					}
				}
				function print_kad2($status) {
					if ($status["kad_firewalled"] == 0) { return '<span class="badge text-bg-success">OK</span>'; }
					else { return '<span class="badge text-bg-danger">ERROR</span>'; }
				}

				$stats = amule_get_stats();
				$kad1 = print_kad1($stats);
				$kad2 = print_kad2($stats);
				print_table($kad1, $kad2);
			?>
		</div>
	</div>

	<!-- Footer: ed2k link + connection status -->
	<div class="amule-footer">
		<div class="row g-3 align-items-center">
			<div class="col-12 col-lg-6">
				<form name="formlink" method="get" action="amuleweb-main-kad.php" id="formed2link">
					<div class="input-group">
						<input class="form-control" name="ed2klink" type="text" id="ed2klink" placeholder="ed2k:// - Insert link">
						<select class="form-select" name="selectcat" id="selectcat" style="max-width:130px;">
						<?php
							$cats = amule_get_categories();
							if ( $HTTP_GET_VARS["Submit"] != "" ) {
								$link = $HTTP_GET_VARS["ed2klink"];
								$target_cat = $HTTP_GET_VARS["selectcat"];
								$target_cat_idx = 0;
								foreach($cats as $i => $c) {
									if ( $target_cat == $c) $target_cat_idx = $i;
								}
								if ( strlen($link) > 0 ) {
									$links = split("ed2k://", $link);
									foreach($links as $linkn) {
										if (strlen($linkn) > 0){
											amule_do_ed2k_download_cmd("ed2k://" . $linkn, $target_cat_idx);
										}
									}
								}
							}
							foreach($cats as $c) {
								echo  '<option>', $c, '</option>';
							}
						?>
						</select>
						<input class="btn btn-primary" type="submit" name="Submit" value="Download link">
					</div>
				</form>
			</div>
			<div class="col-12 col-lg-6 text-lg-end">
				<?php
					$stats = amule_get_stats();
					if ( $stats["id"] == 0 ) {
						$ed2k = "Not connected";
						$ed2k_status = "danger";
					} elseif ( $stats["id"] == 0xffffffff ) {
						$ed2k = "Connecting ...";
						$ed2k_status = "info";
					} else {
						$ed2k = "Connected " . (($stats["id"] < 16777216) ? "(low)" : "(high)") . " " . $stats["serv_name"] . " " . $stats["serv_addr"];
						$ed2k_status = (($stats["id"] < 16777216) ? "warning" : "success");
					}
					if ( $stats["kad_connected"] == 1 ) {
						$kad1 = "Connected";
						if ( $stats["kad_firewalled"] == 1 ) {
							$kad2 = "(FW)";
							$kad_status = "warning";
						} else {
							$kad2 = "(OK)";
							$kad_status = "success";
						}
					} else {
						$kad1 = "Disconnected";
						$kad2 = "";
						$kad_status = "danger";
					}
					echo '<span class="badge text-bg-secondary">ED2k:</span> ';
					echo '<span class="badge text-bg-', $ed2k_status, '">', $ed2k, '</span> ';
					echo '<span class="badge text-bg-secondary">KAD:</span> ';
					echo '<span class="badge text-bg-', $kad_status, '">', $kad1, ' ', $kad2, '</span>';
				?>
			</div>
		</div>
	</div>

	</div><!-- /container -->
</body>
</html>
