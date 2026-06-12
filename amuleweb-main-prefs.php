<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<title>aMule Control Panel - Preferences</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="custom.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="i18n.js"></script>

	<script language="JavaScript" type="text/JavaScript">
	var initvals = new Object;

	<?php
		// apply new options before proceeding
		if ( ($HTTP_GET_VARS["Submit"] == "Apply") && ($_SESSION["guest_login"] == 0) ) {
			$file_opts = array("check_free_space", "extract_metadata",
				"ich_en","aich_trust", "preview_prio","save_sources", "resume_same_cat",
				"min_free_space", "new_files_paused", "alloc_full", "alloc_full_chunks",
				"new_files_auto_dl_prio", "new_files_auto_ul_prio"
			);
			$conn_opts = array("max_line_up_cap","max_up_limit",
				"max_line_down_cap","max_down_limit", "slot_alloc",
				"tcp_port","udp_port","udp_dis","max_file_src","max_conn_total","autoconn_en","reconn_en");
			$webserver_opts = array("use_gzip", "autorefresh_time");

			$all_opts;
			foreach ($conn_opts as $i) {
				$curr_value = $HTTP_GET_VARS[$i];
				if ( $curr_value == "on") $curr_value = 1;
				if ( $curr_value == "") $curr_value = 0;
				$all_opts["connection"][$i] = $curr_value;
			}
			foreach ($file_opts as $i) {
				$curr_value = $HTTP_GET_VARS[$i];
				if ( $curr_value == "on") $curr_value = 1;
				if ( $curr_value == "") $curr_value = 0;
				$all_opts["files"][$i] = $curr_value;
			}
			foreach ($webserver_opts as $i) {
				$curr_value = $HTTP_GET_VARS[$i];
				if ( $curr_value == "on") $curr_value = 1;
				if ( $curr_value == "") $curr_value = 0;
				$all_opts["webserver"][$i] = $curr_value;
			}
			amule_set_options($all_opts);
		}

		$opts = amule_get_options();
		$opt_groups = array("connection", "files", "webserver");
		foreach ($opt_groups as $group) {
			$curr_opts = $opts[$group];
			foreach ($curr_opts as $opt_name => $opt_val) {
				echo 'initvals["', $opt_name, '"] = "', $opt_val, '";';
			}
		}
	?>

	function init_data() {
		var frm = document.forms.mainform;
		if (!frm) return;
		var str_param_names = ["max_line_down_cap","max_line_up_cap","max_up_limit","max_down_limit",
			"max_file_src","slot_alloc","max_conn_total","tcp_port","udp_port","min_free_space","autorefresh_time"];
		for (var i = 0; i < str_param_names.length; i++) {
			if (frm[str_param_names[i]]) frm[str_param_names[i]].value = initvals[str_param_names[i]] || "";
		}
		var check_param_names = ["autoconn_en","reconn_en","udp_dis","new_files_paused","aich_trust",
			"alloc_full","alloc_full_chunks","check_free_space","extract_metadata","ich_en",
			"new_files_auto_dl_prio","new_files_auto_ul_prio","use_gzip"];
		for (var j = 0; j < check_param_names.length; j++) {
			if (frm[check_param_names[j]]) frm[check_param_names[j]].checked = (initvals[check_param_names[j]] == "1");
		}
		if (frm["min_free_space"] && frm["check_free_space"]) {
			frm["min_free_space"].disabled = (initvals["check_free_space"] != "1");
			frm["check_free_space"].addEventListener('change', function() {
				frm["min_free_space"].disabled = !frm["check_free_space"].checked;
			});
		}
	}
	document.addEventListener('DOMContentLoaded', init_data);
	</script>
</head>

<body class="amule-bs5">

	<!-- Navigation bar -->
	<nav class="navbar navbar-expand-lg fixed-top amule-navbar">
		<div class="container-fluid">
			<a class="navbar-brand" href="amuleweb-main-prefs.php">
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
					<li class="nav-item"><a class="nav-link" title="Kademlia" href="amuleweb-main-kad.php"><i class="bi bi-diagram-3"></i> Kad</a></li>
					<li class="nav-item"><a class="nav-link" title="Statistics" href="amuleweb-main-stats.php"><i class="bi bi-bar-chart-line"></i> Stats</a></li>
					<li class="nav-item"><a class="nav-link active" title="Configurations" href="amuleweb-main-prefs.php"><i class="bi bi-gear"></i> Settings</a></li>
					<li class="nav-item"><a class="nav-link" title="Log" href="amuleweb-main-log.php"><i class="bi bi-card-list"></i> Logs</a></li>
					<li class="nav-item"><a class="nav-link" title="Exit" href="login.php"><i class="bi bi-box-arrow-right"></i> Exit</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container-fluid" style="max-width:1200px;">
	<form name="mainform" action="amuleweb-main-prefs.php" method="get">

	<div class="card mt-3">
		<div class="card-body d-flex align-items-center gap-3">
			<?php
				// Use a <button> so the submitted value stays "Apply" even when
				// the visible label is translated (i18n only touches text content,
				// not the value attribute of <button>). An <input> would have its
				// submitted value translated too, breaking the Apply handler.
				if ($_SESSION["guest_login"] == 0) {
					echo '<button class="btn btn-primary" type="submit" name="Submit" value="Apply">Apply</button>';
				} else {
					echo '<button class="btn btn-primary" type="submit" name="Submit" value="Apply" disabled>Apply</button>';
					echo '<span class="badge text-bg-warning">You logged in as guest - commands are disabled</span>';
				}
			?>
		</div>
	</div>

	<div class="card">
		<div class="card-header">PREFERENCES</div>
		<div class="card-body">
			<!-- Field values are populated by init_data() from PHP-provided initvals -->

			<h6 class="text-info text-uppercase mt-1 mb-3 amule-prefhead">Webserver</h6>
			<div class="input-group mb-2"><span class="input-group-text flex-grow-1 justify-content-start">Page refresh interval</span><input type="text" class="form-control" style="max-width:130px;" name="autorefresh_time"></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="use_gzip" id="pf_use_gzip"><label class="form-check-label" for="pf_use_gzip">Use gzip compression</label></div>

			<h6 class="text-info text-uppercase mt-4 mb-3 amule-prefhead">Bandwidth limits</h6>
			<div class="input-group mb-2"><span class="input-group-text flex-grow-1 justify-content-start">Max download rate</span><input type="text" class="form-control" style="max-width:130px;" name="max_down_limit"></div>
			<div class="input-group mb-2"><span class="input-group-text flex-grow-1 justify-content-start">Max upload rate</span><input type="text" class="form-control" style="max-width:130px;" name="max_up_limit"></div>
			<div class="input-group mb-2"><span class="input-group-text flex-grow-1 justify-content-start">Slot allocation</span><input type="text" class="form-control" style="max-width:130px;" name="slot_alloc"></div>

			<h6 class="text-info text-uppercase mt-4 mb-3 amule-prefhead">Connection settings</h6>
			<div class="input-group mb-2"><span class="input-group-text flex-grow-1 justify-content-start">Max total connections</span><input type="text" class="form-control" style="max-width:130px;" name="max_conn_total"></div>
			<div class="input-group mb-2"><span class="input-group-text flex-grow-1 justify-content-start">Max sources per file</span><input type="text" class="form-control" style="max-width:130px;" name="max_file_src"></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="autoconn_en" id="pf_autoconn_en"><label class="form-check-label" for="pf_autoconn_en">Autoconnect at startup</label></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="reconn_en" id="pf_reconn_en"><label class="form-check-label" for="pf_reconn_en">Reconnect when connection lost</label></div>
			<div class="input-group mb-2"><span class="input-group-text flex-grow-1 justify-content-start">TCP Port</span><input type="text" class="form-control" style="max-width:130px;" name="tcp_port"></div>
			<div class="input-group mb-2"><span class="input-group-text flex-grow-1 justify-content-start">UDP Port</span><input type="text" class="form-control" style="max-width:130px;" name="udp_port"></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="udp_dis" id="pf_udp_dis"><label class="form-check-label" for="pf_udp_dis">Disable UDP connections</label></div>

			<h6 class="text-info text-uppercase mt-4 mb-3 amule-prefhead">Line capacity (statistics)</h6>
			<div class="input-group mb-2"><span class="input-group-text flex-grow-1 justify-content-start">Max download rate</span><input type="text" class="form-control" style="max-width:130px;" name="max_line_down_cap"></div>
			<div class="input-group mb-2"><span class="input-group-text flex-grow-1 justify-content-start">Max upload rate</span><input type="text" class="form-control" style="max-width:130px;" name="max_line_up_cap"></div>

			<h6 class="text-info text-uppercase mt-4 mb-3 amule-prefhead">File settings</h6>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="check_free_space" id="pf_check_free_space"><label class="form-check-label" for="pf_check_free_space">Check free minimum space</label></div>
			<div class="input-group mb-2"><span class="input-group-text flex-grow-1 justify-content-start">Minimum free space (MB)</span><input type="text" class="form-control" style="max-width:130px;" name="min_free_space"></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="new_files_auto_dl_prio" id="pf_new_files_auto_dl_prio"><label class="form-check-label" for="pf_new_files_auto_dl_prio">Added download with auto priority</label></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="new_files_auto_ul_prio" id="pf_new_files_auto_ul_prio"><label class="form-check-label" for="pf_new_files_auto_ul_prio">New shared files with auto priority</label></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="ich_en" id="pf_ich_en"><label class="form-check-label" for="pf_ich_en">I.C.H. active</label></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="aich_trust" id="pf_aich_trust"><label class="form-check-label" for="pf_aich_trust">AICH trusts every hash (not recommended)</label></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="alloc_full_chunks" id="pf_alloc_full_chunks"><label class="form-check-label" for="pf_alloc_full_chunks">Alloc full chunks for .part files</label></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="alloc_full" id="pf_alloc_full"><label class="form-check-label" for="pf_alloc_full">Alloc full disk space for .part files</label></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="new_files_paused" id="pf_new_files_paused"><label class="form-check-label" for="pf_new_files_paused">Add files to download in pause</label></div>
			<div class="form-check form-switch mb-2"><input type="checkbox" role="switch" class="form-check-input" name="extract_metadata" id="pf_extract_metadata"><label class="form-check-label" for="pf_extract_metadata">Extract MetaData tags</label></div>
		</div>
	</div>
	</form>

	<!-- Footer: ed2k link + connection status -->
	<div class="amule-footer">
		<div class="row g-3 align-items-center">
			<div class="col-12 col-lg-6">
				<form name="formlink" method="get" action="amuleweb-main-prefs.php" id="formed2link">
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
