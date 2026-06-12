<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<title>aMule Control Panel - Server List</title>

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
			<a class="navbar-brand" href="amuleweb-main-servers.php">
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
					<li class="nav-item"><a class="nav-link active" title="Servers" href="amuleweb-main-servers.php"><i class="bi bi-hdd-network"></i> Server</a></li>
					<li class="nav-item"><a class="nav-link" title="Kademlia" href="amuleweb-main-kad.php"><i class="bi bi-diagram-3"></i> Kad</a></li>
					<li class="nav-item"><a class="nav-link" title="Statistics" href="amuleweb-main-stats.php"><i class="bi bi-bar-chart-line"></i> Stats</a></li>
					<li class="nav-item"><a class="nav-link" title="Configurations" href="amuleweb-main-prefs.php"><i class="bi bi-gear"></i> Settings</a></li>
					<li class="nav-item"><a class="nav-link" title="Log" href="amuleweb-main-log.php"><i class="bi bi-card-list"></i> Logs</a></li>
					<li class="nav-item"><a class="nav-link" title="Exit" href="login.php"><i class="bi bi-box-arrow-right"></i> Exit</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container-fluid" style="max-width:1200px;">

	<div class="card mt-3">
		<div class="card-header">SERVERS</div>
		<table class="table table-hover align-middle mb-0">
			<thead>
				<tr>
					<th style="width:70px;"></th>
					<th>Server name</th>
					<th class="d-none d-lg-table-cell">Description</th>
					<th class="d-none d-md-table-cell">Address</th>
					<th style="width:80px;">Users</th>
					<th style="width:80px;">Files</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$sort_order;$sort_reverse;

					function my_cmp($a, $b) {
						global $sort_order, $sort_reverse;
						switch ( $sort_order) {
							case "name": $result = $a->name > $b->name; break;
							case "desc": $result = $a->desc > $b->desc; break;
							case "users": $result = $a->users > $b->users; break;
							case "files":$result = $a->files > $b->files; break;
						}
						if ( $sort_reverse ) {
							$result = !$result;
						}
						return $result;
					}

					$servers = amule_load_vars("servers");
					$sort_order = $HTTP_GET_VARS["sort"];

					if ( ($HTTP_GET_VARS["cmd"] != "") and ($HTTP_GET_VARS["ip"] != "") and ($HTTP_GET_VARS["port"] != "")) {
						if ($_SESSION["guest_login"] == 0) {
							amule_do_server_cmd($HTTP_GET_VARS["ip"], $HTTP_GET_VARS["port"], $HTTP_GET_VARS["cmd"]);
						}
					}

					if ( $sort_order == "" ) {
						$sort_order = $_SESSION["servers_sort"];
					} else {
						if ( $_SESSION["sort_reverse"] == "" ) {
							$_SESSION["sort_reverse"] = 0;
						} else {
							$_SESSION["sort_reverse"] = !$_SESSION["sort_reverse"];
						}
					}

					$sort_reverse = $_SESSION["sort_reverse"];
					if ( $sort_order != "" ) {
						$_SESSION["servers_sort"] = $sort_order;
						usort($servers, "my_cmp");
					}
					foreach ($servers as $srv) {
						echo "<tr>";
						if ($_SESSION["guest_login"] != 0) {
							echo "<td></td>";
						} else {
							echo '<td style="white-space:nowrap;">',
									'<a href="amuleweb-main-servers.php?cmd=connect&ip=', $srv->ip, '&port=', $srv->port, '" title="Connect">',
										'<i class="bi bi-plus-circle-fill" style="color:#4db6ac;font-size:16px;"></i>',
									'</a> ',
									'<a href="amuleweb-main-servers.php?cmd=remove&ip=', $srv->ip, '&port=', $srv->port, '" title="Remove">',
										'<i class="bi bi-dash-circle-fill" style="color:#ef5350;font-size:16px;"></i>',
									'</a>',
								'</td>';
						}
						echo '<td style="font-size:12px;"><b style="color:#cfd8dc;">', $srv->name, '</b></td>';
						echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', $srv->desc, '</td>';
						echo '<td class="d-none d-md-table-cell" style="font-size:12px;color:#cfd8dc;">', $srv->addr, '</td>';
						echo '<td style="font-size:12px;color:#cfd8dc;">', $srv->users, '</td>';
						echo '<td style="font-size:12px;color:#cfd8dc;">', $srv->files, '</td>';
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>

	<!-- Footer: ed2k link + connection status -->
	<div class="amule-footer">
		<div class="row g-3 align-items-center">
			<div class="col-12 col-lg-6">
				<form name="formlink" method="get" action="amuleweb-main-servers.php" id="formed2link">
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
