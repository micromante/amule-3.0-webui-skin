<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<title>aMule - Control Panel - Downloads and Uploads</title>
	<?php
		echo "<script>window._dloadRefresh = " . ($_SESSION["auto_refresh"] > 0 ? $_SESSION["auto_refresh"] : 0) . ";</script>";
	?>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="custom.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="i18n.js"></script>

	<script language="JavaScript" type="text/JavaScript">
	function formCommandSubmit(command) {
		if ( command == "cancel" ) {
			var boxchecked = document.querySelectorAll('input[type="checkbox"]:checked');
			var selectedFiles = Object.values(boxchecked).filter(selected => selected.name != 'selectAllFiles').length;
			if (selectedFiles == 0) return;
			var res = confirm("Delete selected " + (selectedFiles) + " files ?");
			if ( res == false ) return;
		}
		if ( command != "filter" ) {
			<?php
				if ($_SESSION["guest_login"] != 0) {
						echo 'alert("You logged in as guest - commands are disabled");';
						echo "return;";
				}
			?>
		}
		var frm = document.forms.mainform;
		frm.command.value = command;
		frm.submit();
	}
	function selectAll(check) {
		var checkboxes = document.querySelectorAll('#downloadsTable input[type="checkbox"]');
		checkboxes.forEach(function(checkbox) { checkbox.checked = check.checked; });
	}

	// Render the rounded download percentage under each progress bar
	// (amuleweb's template engine has no round(), so we do it client-side).
	function renderPercents() {
		document.querySelectorAll('.amule-pct').forEach(function(el) {
			var v = parseFloat(el.getAttribute('data-pct'));
			el.textContent = (isNaN(v) ? '' : Math.round(v) + '%');
		});
	}

	// Refresh only the Download/Upload tables via AJAX (no full page reload).
	function dloadPoll() {
		if (document.querySelectorAll('#downloadsTable input[type="checkbox"]:checked').length > 0) return;
		fetch('amuleweb-main-dload.php')
			.then(function(r) { return r.text(); })
			.then(function(html) {
				if (document.querySelectorAll('#downloadsTable input[type="checkbox"]:checked').length > 0) return;
				var d = new DOMParser().parseFromString(html, 'text/html');
				['downloadsTable', 'uploadsTable'].forEach(function(id) {
					var nb = d.querySelector('#' + id + ' tbody');
					var cb = document.querySelector('#' + id + ' tbody');
					if (nb && cb) cb.innerHTML = nb.innerHTML;
				});
				renderPercents();
			})
			.catch(function() {});
	}

	document.addEventListener('DOMContentLoaded', function() {
		renderPercents();
		if (window._dloadRefresh > 0) {
			setInterval(dloadPoll, 1000 * window._dloadRefresh);
		}
		window.addEventListener('scroll', function() {
			var s = document.getElementById('scroll');
			if (!s) return;
			s.style.display = (window.scrollY > 100) ? 'flex' : 'none';
		});
	});
	function scrollToTop() { window.scrollTo({ top: 0, behavior: 'smooth' }); }
	</script>
</head>

<body class="amule-bs5">

	<!-- Navigation bar -->
	<nav class="navbar navbar-expand-lg fixed-top amule-navbar">
		<div class="container-fluid">
			<a class="navbar-brand" href="amuleweb-main-dload.php">
				<img src="logo-nav-brax.png" class="logo-nav" alt="aMule">
				aMule <span class="amule-brand-rest">WebUI</span>
				<small style="font-size:11px;">powered by micromante <span style="color:#e74c3c;">&#10084;</span></small>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="mainNav">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link active" title="Downloads and Uploads" href="amuleweb-main-dload.php"><i class="bi bi-arrow-down-up"></i> Transfer</a></li>
					<li class="nav-item"><a class="nav-link" title="Sharing" href="amuleweb-main-shared.php"><i class="bi bi-share-fill"></i> Shared</a></li>
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
	<form action="amuleweb-main-dload.php" method="get" name="mainform">
	<input type="hidden" name="command">

	<div class="card mt-3">
		<div class="card-body">
			<div class="d-flex flex-wrap gap-2 align-items-center amule-toolbar">
				<div class="btn-group">
					<a class="btn btn-secondary" href="javascript:formCommandSubmit('pause');" title="Pause"><i class="bi bi-pause-fill"></i> Pause</a>
					<a class="btn btn-secondary" href="javascript:formCommandSubmit('resume');" title="Resume"><i class="bi bi-play-fill"></i> Resume</a>
				</div>
				<div class="btn-group">
					<a class="btn btn-secondary" href="javascript:formCommandSubmit('priodown');" title="Lower priority"><i class="bi bi-arrow-down"></i> Lower Priority</a>
					<a class="btn btn-danger" href="javascript:formCommandSubmit('cancel');" title="Remove"><i class="bi bi-x-circle"></i> Remove</a>
					<a class="btn btn-secondary" href="javascript:formCommandSubmit('prioup');" title="Higher priority"><i class="bi bi-arrow-up"></i> High Priority</a>
				</div>
				<div class="input-group" style="width:auto;">
				<?php
					$all_status = array("all", "Waiting", "Paused", "Downloading");
					if ( $HTTP_GET_VARS["command"] == "filter") {
						$_SESSION["filter_status"] = $HTTP_GET_VARS["status"];
						$_SESSION["filter_cat"] = $HTTP_GET_VARS["category"];
					}
					if ( $_SESSION["filter_status"] == '') $_SESSION["filter_status"] = 'all';
					if ( $_SESSION["filter_cat"] == '') $_SESSION["filter_cat"] = 'all';

					echo '<select name="status" id="filter" class="form-select"> ';
					foreach ($all_status as $s) {
						echo (($s == $_SESSION["filter_status"]) ? '<option selected>' : '<option>'), $s, '</option>';
					}
					echo '</select>';

					echo '<select name="category" id="category" class="form-select">';
					$cats = amule_get_categories();
					foreach($cats as $c) {
						echo (($c == $_SESSION["filter_cat"]) ? '<option selected>' : '<option>'), $c, '</option>';
					}
					echo '</select>';
				?>
					<a class="btn btn-primary" href="javascript:formCommandSubmit('filter');" title="Filter"><i class="bi bi-funnel"></i> Filter</a>
				</div>
				<?php
					if ($_SESSION["guest_login"] != 0) {
						echo '<span class="badge text-bg-warning">You logged in as guest - commands are disabled</span>';
					}
				?>
			</div>
		</div>
	</div>

	<!-- Downloads table -->
	<div class="card">
		<div class="card-header">DOWNLOAD</div>
		<table class="table table-hover align-middle mb-0 amule-cards" id="downloadsTable">
			<thead>
				<tr>
					<th style="width:42px;"><input type="checkbox" class="form-check-input" name="selectAllFiles" onclick='selectAll(this);'></th>
					<th>File name</th>
					<th class="d-none d-lg-table-cell" style="width:80px;">Size</th>
					<th class="d-none d-lg-table-cell" style="width:120px;">Completed</th>
					<th style="width:90px;">Speed</th>
					<th style="width:160px;">Progress</th>
					<th class="d-none d-lg-table-cell" style="width:110px;">Sources</th>
					<th style="width:110px;">Status</th>
					<th class="d-none d-lg-table-cell" style="width:90px;">Priority</th>
				</tr>
			</thead>
			<tbody>
				<?php
					function CastToXBytes($size, &$count) {
						$count += $size;
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

					function StatusClass($file) {
						if ( $file->status == 7 ) {
							return '<span class="badge text-bg-info">Paused</span>';
						} elseif ( $file->src_count_xfer > 0 ) {
							return '<span class="badge text-bg-success">Downloading</span>';
						} else {
							return '<span class="badge text-bg-warning">Waiting</span>';
						}
					}

					function StatusString($file) {
						if ( $file->status == 7 ) {
							return 'Paused';
						} elseif ( $file->src_count_xfer > 0 ) {
							return 'Downloading';
						} else {
							return 'Waiting';
						}
					}

					function StatusCode($file) {
						if ( $file->status == 7 ) {
							return 1; // Paused
						} elseif ( $file->src_count_xfer > 0 ) {
							return 0; // downloading
						} else {
							return -1; // waiting
						}
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

					$sort_order; $sort_reverse;

					function my_cmp($a, $b)	{
						global $sort_order, $sort_reverse;
						switch ( $sort_order) {
							case "size": $result = $a->size > $b->size; break;
							case "size_done": $result = $a->size_done > $b->size_done; break;
							case "progress": $result = (((float)$a->size_done)/((float)$a->size)) > (((float)$b->size_done)/((float)$b->size)); break;
							case "name": $result = $a->name > $b->name; break;
							case "speed": $result = $a->speed > $b->speed; break;
							case "scrcount": $result = $a->src_count > $b->src_count; break;
							case "status": $result = StatusClass($a) > StatusClass($b); break;
							case "prio": $result = $a->prio < $b->prio; break;
						}
						if ( $sort_reverse ) {
							$result = !$result;
						}
						return $result;
					}

					function create_prg_bar($file) {
						$done = ((float)$file->size_done*100)/((float)$file->size);
						switch (StatusCode($file)) {
							case -1: $cls = 'bg-warning'; break;   // waiting
							case 0:  $cls = 'bg-info'; break;      // downloading
							default: $cls = 'bg-secondary'; break; // paused
						}
						// amuleweb's engine has no round()/number_format(); the raw
						// percentage goes in data-pct and JS renders it rounded below.
						echo '<div class="progress" style="height:8px;background:#11131c;">',
							'<div class="progress-bar ', $cls, '" role="progressbar" style="width:', $done, '%;"></div>',
							'</div>',
							'<div class="amule-pct" data-pct="', $done, '" style="font-size:11px;text-align:center;margin-top:3px;color:#cfd8dc;"></div>';
					}

					function name_cell($name) {
						echo '<label style="font-size:12px;color:#f5f5f5;" title="', $name, '">', $name, '</label>';
					}

					// perform command before processing content
					if ( ($HTTP_GET_VARS["command"] != "") && ($_SESSION["guest_login"] == 0) ) {
						foreach ( $HTTP_GET_VARS as $name => $val) {
							if ( (strlen($name) == 32) and ($val == "on") ) {
								amule_do_download_cmd($name, $HTTP_GET_VARS["command"]);
							}
						}
						if ( $HTTP_GET_VARS["command"] == "filter") {
							$_SESSION["filter_status"] = $HTTP_GET_VARS["status"];
							$_SESSION["filter_cat"] = $HTTP_GET_VARS["category"];
						}
					}
					if ( $_SESSION["filter_status"] == "") $_SESSION["filter_status"] = "all";
					if ( $_SESSION["filter_cat"] == "") $_SESSION["filter_cat"] = "all";
					$countSize = 0;
					$countCompleted = 0;
					$countSpeed = 0;
					$downloads = amule_load_vars("downloads");
					$fakevar=0;
					$sort_order = $HTTP_GET_VARS["sort"];

					if ( $sort_order == "" ) {
						$sort_order = $_SESSION["download_sort"];
					} else {
						if ( $_SESSION["download_sort_reverse"] == "" ) {
							$_SESSION["download_sort_reverse"] = 0;
						} else {
							if ( $HTTP_GET_VARS["sort"] != '') {
								$_SESSION["download_sort_reverse"] = !$_SESSION["download_sort_reverse"];
							}
						}
					}
					$sort_reverse = $_SESSION["download_sort_reverse"];
					if ( $sort_order != "" ) {
						$_SESSION["download_sort"] = $sort_order;
						usort($downloads, "my_cmp");
					}
					$cats = amule_get_categories();
					foreach($cats as $i => $c) {
						$cat_idx[$c] = $i;
					}

					foreach ($downloads as $file) {
						$filter_status_result = ($_SESSION["filter_status"] == "all") or
							($_SESSION["filter_status"] == StatusString($file));
						$filter_cat_result = ($_SESSION["filter_cat"] == "all") or
							($cat_idx[ $_SESSION["filter_cat"] ] == $file->category);

						if ( $filter_status_result and $filter_cat_result) {
							echo '<tr>';
							echo '<td><input type="checkbox" class="form-check-input" name="', $file->hash, '"></td>';
							echo '<td>'; name_cell($file->name); echo '</td>';
							echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', CastToXBytes($file->size, $countSize), '</td>';
							echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', CastToXBytes($file->size_done, $countCompleted), ' (',
								number_format(((float)$file->size_done*100)/((float)$file->size), 1), '%)</td>';
							echo '<td data-label="Speed" style="font-size:12px;">', ($file->speed > 0) ? (CastToXBytes($file->speed, $countSpeed) . "/s") : "-", '</td>';
							echo '<td data-label="Progress">'; create_prg_bar($file); echo '</td>';
							echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">';
							if ( $file->src_count_not_curr != 0 ) {
								echo $file->src_count - $file->src_count_not_curr, " / ";
							}
							echo $file->src_count, " ( ", $file->src_count_xfer, " ) ";
							if ( $file->src_count_a4af != 0 ) {
								echo "+ ", $file->src_count_a4af;
							}
							echo '</td>';
							echo '<td data-label="Status">', StatusClass($file), '</td>';
							echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', PrioString($file), '</td>';
							echo '</tr>';
						}
					}
					if (count($downloads) > 0 and $countSize > 0) {
						echo '<tr style="color:#c9c9c9;">';
						echo '<td></td>';
						echo '<td style="font-size:12px;text-align:right;">Total</td>';
						echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', CastToXBytes($countSize, $fakevar), '</td>';
						echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', CastToXBytes($countCompleted, $fakevar), ' (',
							($countSize > 0) ? number_format((float)$countCompleted*100/((float)$countSize), 1) : "0", '%)</td>';
						echo '<td style="font-size:12px;">', ($countSpeed > 0) ? (CastToXBytes($countSpeed, $fakevar) . "/s" ) : "", '</td>';
						echo '<td></td>';
						echo '<td class="d-none d-lg-table-cell"></td>';
						echo '<td></td>';
						echo '<td class="d-none d-lg-table-cell"></td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</div>
	</form>

	<!-- Uploads table -->
	<div class="card">
		<div class="card-header">UPLOAD</div>
		<table class="table table-hover align-middle mb-0 amule-cards" id="uploadsTable">
			<thead>
				<tr>
					<th>File Name</th>
					<th style="width:160px;">Username</th>
					<th class="d-none d-lg-table-cell" style="width:90px;">Up</th>
					<th class="d-none d-lg-table-cell" style="width:90px;">Down</th>
					<th style="width:90px;">Speed</th>
				</tr>
			</thead>
			<tbody>
				<?php
					// amuleweb scopes each <?php block separately, so CastToXBytes
					// (defined in the downloads block above) must be redefined here.
					function CastToXBytes($size, &$count) {
						$count += $size;
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
					function name_cell_up($name) {
						echo '<label style="font-size:12px;color:#f5f5f5;" title="', $name, '">', $name, '</label>';
					}
					$countUploadDimension = 0;
					$countDownloadDimension = 0;
					$countSpeedUp = 0;
					$uploads = amule_load_vars("uploads");
					$fv = 0;

					foreach ($uploads as $file) {
						echo '<tr>';
						echo '<td>'; name_cell_up($file->name); echo '</td>';
						echo '<td data-label="User" style="font-size:12px;">', $file->user_name, '</td>';
						echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', CastToXBytes($file->xfer_up, $countUploadDimension), '</td>';
						echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', CastToXBytes($file->xfer_down, $countDownloadDimension), '</td>';
						echo '<td data-label="Speed" style="font-size:12px;">', ($file->xfer_speed > 0) ? (CastToXBytes($file->xfer_speed, $countSpeedUp) . "/s") : "", '</td>';
						echo '</tr>';
					}
					if (count($uploads)>0) {
						echo '<tr style="color:#c9c9c9;">';
						echo '<td style="font-size:12px;text-align:right;">Total</td>';
						echo '<td></td>';
						echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', CastToXBytes($countUploadDimension, $fv), '</td>';
						echo '<td class="d-none d-lg-table-cell" style="font-size:12px;">', CastToXBytes($countDownloadDimension, $fv), '</td>';
						echo '<td style="font-size:12px;">', CastToXBytes($countSpeedUp, $fv), '/s</td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</div>

	<!-- Footer: ed2k link + connection status -->
	<div class="amule-footer">
		<div class="row g-3 align-items-center">
			<div class="col-12 col-lg-6">
				<form name="formlink" method="get" action="amuleweb-main-dload.php" id="formed2link">
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

	<a href="javascript:void(0);" id="scroll" onclick="scrollToTop();" title="Top"
	   style="display:none;position:fixed;right:16px;bottom:24px;width:46px;height:46px;border-radius:50%;background:#319a9b;color:#fff;align-items:center;justify-content:center;z-index:1030;box-shadow:0 2px 8px rgba(0,0,0,0.4);">
		<i class="bi bi-chevron-up" style="font-size:20px;"></i>
	</a>

</body>
</html>
