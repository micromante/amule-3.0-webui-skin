<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<title>aMule Control Panel - Shared Files</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="custom.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="i18n.js"></script>
	<script language="JavaScript" type="text/JavaScript">
	function formCommandSubmit(command) {
		<?php
			if ($_SESSION["guest_login"] != 0) {
					echo 'if (command != "reload" && command != "setprio") { alert("You logged in as guest - commands are disabled"); return; }';
			}
		?>
		var frm = document.forms.mainform;
		frm.command.value = command;
		frm.submit();
	}
	function selectAll(check) {
		document.querySelectorAll('#sharedTable input[type="checkbox"]').forEach(function(cb){ cb.checked = check.checked; });
	}
	</script>
</head>

<body class="amule-bs5">

	<!-- Navigation bar -->
	<nav class="navbar navbar-expand-lg fixed-top amule-navbar">
		<div class="container-fluid">
			<a class="navbar-brand" href="amuleweb-main-shared.php">
				<img src="logo-nav-brax.png" class="logo-nav" alt="aMule">
				aMule <span class="amule-brand-rest">WebUI</span>
				<small style="font-size:11px;">powered by micromante <span style="color:#e74c3c;">&#10084;</span></small>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="mainNav">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link" title="Downloads and Uploads" href="amuleweb-main-dload.php"><i class="bi bi-arrow-down-up"></i> Transfer</a></li>
					<li class="nav-item"><a class="nav-link active" title="Sharing" href="amuleweb-main-shared.php"><i class="bi bi-share-fill"></i> Shared</a></li>
					<li class="nav-item"><a class="nav-link" title="Search" href="amuleweb-main-search.php"><i class="bi bi-search"></i> Search</a></li>
					<li class="nav-item"><a class="nav-link" title="Servers" href="amuleweb-main-servers.php"><i class="bi bi-hdd-network"></i> Server</a></li>
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

	<!-- Commands -->
	<form name="mainform" action="amuleweb-main-shared.php" method="get">
	<input type="hidden" name="command">
	<div class="card mt-3">
		<div class="card-body">
			<div class="d-flex flex-wrap gap-2 align-items-center amule-toolbar">
				<div class="btn-group">
					<a class="btn btn-secondary" href="javascript:formCommandSubmit('priodown');" title="Lower priority"><i class="bi bi-arrow-down"></i> Lower Priority</a>
					<a class="btn btn-secondary" href="javascript:formCommandSubmit('reload');" title="Refresh"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
					<a class="btn btn-secondary" href="javascript:formCommandSubmit('prioup');" title="Higher priority"><i class="bi bi-arrow-up"></i> High Priority</a>
				</div>
				<div class="input-group" style="width:auto;">
					<select name="select" id="filter" class="form-select">
						<option selected>All</option>
						<option>Low</option>
						<option>Normal</option>
						<option>High</option>
						<option>Release</option>
					</select>
					<a class="btn btn-primary" href="javascript:formCommandSubmit('setprio');" title="Filter"><i class="bi bi-funnel"></i> Filter</a>
				</div>
				<?php
					if ($_SESSION["guest_login"] != 0) {
						echo '<span class="badge text-bg-warning">You logged in as guest - commands are disabled</span>';
					}
				?>
			</div>
		</div>
	</div>

	<!-- Shared files table -->
	<div class="card">
		<div class="card-header">SHARED FILES</div>
		<table class="table table-hover align-middle mb-0 amule-cards" id="sharedTable">
			<thead>
				<tr>
					<th style="width:42px;"><input type="checkbox" class="form-check-input" name="selectAllFiles" onclick='selectAll(this);'></th>
					<th>File name</th>
					<th class="d-none d-lg-table-cell">Transferred (Total)</th>
					<th class="d-none d-lg-table-cell">Requested (Total)</th>
					<th class="d-none d-lg-table-cell">Accepted Rqst (Total)</th>
					<th style="width:90px;">Size</th>
					<th style="width:90px;">Priority</th>
				</tr>
			</thead>
			<tbody>
				<?php
					function CastToXBytes($size) {
						if ( $size < 1024 ) {
							$result = $size . " b";
						} elseif ( $size < 1048576 ) {
							$result = ($size / 1024.0) . " kb";
						} elseif ( $size < 1073741824 ) {
							$result = ($size / 1048576.0) . " mb";
						} else {
							$result = ($size / 1073741824.0) . " gb";
						}
						return $result;
					}

					function PrioString($file) {
						$prionames = array(0 => "Low", 1 => "Normal", 2 => "High",
							3 => "Very high", 4 => "Very low", 5=> "Auto", 6 => "Release");
						$result = $prionames[$file->prio];
						if ( $file->prio_auto == 1) {
							$result = $result . " (auto)";
						}
						return $result;
					}

					function PrioStringSorter($file) {
						$prionames = array(0 => "Low", 1 => "Normal", 2 => "High",
							3 => "High", 4 => "Low", 5=> "Normal", 6 => "Release");
						$result = $prionames[$file->prio];
						return $result;
					}

					$sort_order;$sort_reverse;

					function my_cmp($a, $b)	{
						global $sort_order, $sort_reverse;
						switch ( $sort_order) {
							case "size": $result = $a->size > $b->size; break;
							case "name": $result = $a->name > $b->name; break;
							case "xfer": $result = $a->xfer > $b->xfer; break;
							case "xfer_all": $result = $a->xfer_all > $b->xfer_all; break;
							case "acc": $result = $a->accept > $b->accept; break;
							case "acc_all": $result = $a->accept_all > $b->accept_all; break;
							case "req": $result = $a->req > $b->req; break;
							case "req_all": $result = $a->req_all > $b->req_all; break;
						}
						if ( $sort_reverse ) {
							$result = !$result;
						}
						return $result;
					}

					// perform command before processing content
					if (($HTTP_GET_VARS["command"] != "") && ($_SESSION["guest_login"] == 0)) {
						foreach ( $HTTP_GET_VARS as $name => $val) {
							if ( (strlen($name) == 32) and ($val == "on") ) {
								amule_do_shared_cmd($name, $HTTP_GET_VARS["command"]);
							}
						}
						if ($HTTP_GET_VARS["command"] == "reload") {
							amule_do_reload_shared_cmd();
						}
					}
					$shared = amule_load_vars("shared");

					$sort_order = $HTTP_GET_VARS["sort"];
					if ( $sort_order == "" ) {
						$sort_order = $_SESSION["shared_sort"];
					} else {
						if ( $_SESSION["sort_reverse"] == "" ) {
							$_SESSION["sort_reverse"] = 0;
						} else {
							$_SESSION["sort_reverse"] = !$_SESSION["sort_reverse"];
						}
					}
					$sort_reverse = $_SESSION["sort_reverse"];
					if ( $sort_order != "" ) {
						$_SESSION["shared_sort"] = $sort_order;
						usort($shared, "my_cmp");
					}

					function print_shared_row($file) {
						echo '<tr>';
						echo '<td><input type="checkbox" class="form-check-input" name="', $file->hash, '"></td>';
						echo '<td style="font-size:12px;"><label><b>', $file->name, '</b></label></td>';
						echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', CastToXBytes($file->xfer), ' (', CastToXBytes($file->xfer_all), ')</td>';
						echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', $file->req, ' (', $file->req_all, ')</td>';
						echo '<td data-label="Accepted" class="d-none d-lg-table-cell" style="font-size:12px;">', $file->accept, ' (', $file->accept_all, ')</td>';
						echo '<td data-label="Size" style="font-size:12px;">', CastToXBytes($file->size), '</td>';
						echo '<td data-label="Priority" style="font-size:12px;">', PrioString($file), '</td>';
						echo '</tr>';
					}

					if ($HTTP_GET_VARS["select"] == "All" || $HTTP_GET_VARS["select"] == "") {
						foreach ($shared as $file) {
							print_shared_row($file);
						}
					} else {
						foreach ($shared as $file) {
							if ($HTTP_GET_VARS["select"] == PrioStringSorter($file)) {
								print_shared_row($file);
							}
						}
					}
				?>
			</tbody>
		</table>
	</div>
	</form>

	<!-- Footer: ed2k link + connection status -->
	<div class="amule-footer">
		<div class="row g-3 align-items-center">
			<div class="col-12 col-lg-6">
				<form name="formlink" method="get" action="amuleweb-main-shared.php" id="formed2link">
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
