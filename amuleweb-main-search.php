<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<title>aMule - Control Panel - Search</title>

	<?php
		echo "<script>window._amuleRefresh = " . ($_SESSION["auto_refresh"] > 0 ? $_SESSION["auto_refresh"] : 0) . ";</script>";
	?>

	<!-- Bootstrap 5 + Bootstrap Icons -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="custom.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="i18n.js"></script>

	<script language="JavaScript" type="text/JavaScript">
	/*
	 * aMule 3.0.0 has a bug: starting a new search does NOT clear previous
	 * results on the daemon (RemoveResults(0xffffffff) matches no search id),
	 * so the EC result list accumulates across searches forever.
	 * Workaround: snapshot the hashes already present when a new search starts
	 * ("baseline") and hide them, showing only results from the current search.
	 */
	window._amuleSearchPending = window._amuleSearchPending || false;

	function amuleGetBaseline() {
		try {
			var raw = sessionStorage.getItem('amule_baseline');
			return raw ? JSON.parse(raw) : {};
		} catch (e) { return {}; }
	}

	function amuleSetBaseline(obj) {
		try { sessionStorage.setItem('amule_baseline', JSON.stringify(obj)); } catch (e) {}
	}

	// Hash of a row. Every result row carries data-hash; "present" rows have no
	// checkbox, so we read the attribute first and fall back to the checkbox.
	function amuleRowHash(tr) {
		var h = tr.getAttribute('data-hash');
		if (h) return h;
		var cb = tr.querySelector('input[type="checkbox"]');
		return cb ? cb.getAttribute('name') : null;
	}
	// A real result row is any row with a data-hash (not the message rows).
	function amuleIsResultRow(tr) { return tr.hasAttribute('data-hash'); }

	// Returns array of <tr> from a tbody that are NOT in the baseline
	function amuleFilteredRows(tbody, baseline) {
		var out = [];
		var rows = tbody.querySelectorAll('tr');
		for (var i = 0; i < rows.length; i++) {
			var h = amuleRowHash(rows[i]);
			if (h && !baseline[h]) out.push(rows[i]);
		}
		return out;
	}

	function amuleShowSearching(val) {
		var tbody = document.querySelector('#searchResultsTable tbody');
		if (!tbody) return;
		var es = (window.amuleLang && window.amuleLang() === 'es');
		var q = val ? ' "' + val + '"' : '';
		var line1 = es ? ('Buscando' + q + '...') : ('Searching' + (val ? ' for "' + val + '"' : '') + '...');
		var line2 = es ? 'Los resultados aparecerán en breve' : 'Results will appear shortly';
		tbody.innerHTML =
			'<tr><td colspan="5" style="text-align:center;padding:28px 0;color:#4db6ac;font-size:14px;">' +
			'<span class="amule-spinner"></span>' +
			'<div style="margin-top:12px;">' + line1 + '</div>' +
			'<small style="color:#90a4ae;font-size:12px;">' + line2 + '</small>' +
			'</td></tr>';
	}

	// Render only current-search rows into the live table.
	// replaceEmpty=true  -> always replace (used on full page load)
	// replaceEmpty=false -> if no new rows, keep current view (used while polling)
	function amuleRenderFiltered(srcTbody, replaceEmpty) {
		var baseline = amuleGetBaseline();
		var cb = document.querySelector('#searchResultsTable tbody');
		if (!cb || !srcTbody) return;
		var rows = amuleFilteredRows(srcTbody, baseline);
		if (rows.length === 0 && !replaceEmpty) {
			return; // nothing new yet — keep "Searching..." / current view
		}
		cb.innerHTML = '';
		if (rows.length === 0) {
			var esE = (window.amuleLang && window.amuleLang() === 'es');
			cb.innerHTML =
				'<tr><td colspan="5" style="text-align:center;padding:24px 0;color:#90a4ae;font-size:13px;">' +
				(esE ? 'Aún no hay resultados para la búsqueda actual.' : 'No results for the current search yet.') +
				'</td></tr>';
			return;
		}
		// Deduplicate by hash: the same file (same ed2k hash) is often returned
		// several times, so show each unique file only once.
		var seen = {};
		for (var i = 0; i < rows.length; i++) {
			var rh = rows[i].getAttribute('data-hash');
			if (rh && seen[rh]) continue;
			if (rh) seen[rh] = 1;
			cb.appendChild(rows[i].cloneNode(true));
		}
		amuleApplyMarks();   // reconcile "in downloads" marks (downloaded/forgotten)
		amuleApplySort();    // keep the current sort order after re-render
		amuleApplyFilter();  // keep the text filter applied after re-render
	}

	// Localize the "already in downloads" badge (result rows aren't touched by
	// the generic i18n pass, so we set the text here based on the language).
	function amuleLocalizeHave() {
		var es = (window.amuleLang && window.amuleLang() === 'es');
		var txt = es ? 'En descargas' : 'In downloads';
		var bs = document.querySelectorAll('#searchResultsTable .amule-have-badge');
		for (var i = 0; i < bs.length; i++) bs[i].textContent = txt;
	}

	// --- Client-side column sorting (toggles asc/desc on each click) ---
	window._amuleSort = { col: null, dir: 1 };

	function amuleSortValue(tr, col) {
		if (col === 'size')    return parseFloat(tr.getAttribute('data-size')) || 0;
		if (col === 'sources') return parseFloat(tr.getAttribute('data-sources')) || 0;
		// name: use the filename label text
		var lbl = tr.querySelector('td label');
		return (lbl ? lbl.textContent : tr.textContent).toLowerCase();
	}

	function amuleApplySort() {
		var s = window._amuleSort;
		if (!s.col) return;
		var cb = document.querySelector('#searchResultsTable tbody');
		if (!cb) return;
		var rows = [];
		var trs = cb.querySelectorAll('tr');
		for (var i = 0; i < trs.length; i++) {
			if (amuleIsResultRow(trs[i])) rows.push(trs[i]);
		}
		if (rows.length < 2) { updateSortArrows(); return; }
		rows.sort(function (a, b) {
			var va = amuleSortValue(a, s.col), vb = amuleSortValue(b, s.col);
			if (va < vb) return -1 * s.dir;
			if (va > vb) return  1 * s.dir;
			return 0;
		});
		for (var j = 0; j < rows.length; j++) cb.appendChild(rows[j]); // re-append in order
		updateSortArrows();
	}

	function amuleSortBy(col) {
		var s = window._amuleSort;
		if (s.col === col) { s.dir = -s.dir; }   // same column -> flip direction
		else { s.col = col; s.dir = 1; }          // new column -> ascending
		amuleApplySort();
	}

	function updateSortArrows() {
		var s = window._amuleSort;
		var arrows = document.querySelectorAll('#searchResultsTable .sort-arrow');
		for (var i = 0; i < arrows.length; i++) {
			var col = arrows[i].getAttribute('data-col');
			arrows[i].textContent = (col === s.col) ? (s.dir === 1 ? '▲' : '▼') : '';
		}
	}

	// Dynamic text filter over loaded results. Space-separated terms are ANDed,
	// so "1080 mkv" shows only rows whose name contains both. Case-insensitive.
	function amuleApplyFilter() {
		var input = document.getElementById('resultFilter');
		var cb = document.querySelector('#searchResultsTable tbody');
		if (!cb) return;
		var terms = input && input.value ? input.value.toLowerCase().split(/\s+/).filter(Boolean) : [];
		var rows = cb.querySelectorAll('tr');
		var shown = 0, total = 0;
		for (var i = 0; i < rows.length; i++) {
			if (!amuleIsResultRow(rows[i])) { continue; } // skip message rows
			total++;
			var text = rows[i].textContent.toLowerCase();
			var match = true;
			for (var t = 0; t < terms.length; t++) {
				if (text.indexOf(terms[t]) === -1) { match = false; break; }
			}
			rows[i].style.display = match ? '' : 'none';
			if (match) shown++;
		}
		var counter = document.getElementById('resultFilterCount');
		if (counter) {
			var esC = (window.amuleLang && window.amuleLang() === 'es');
			var word = esC ? 'resultados' : 'results';
			counter.textContent = terms.length ? (shown + ' / ' + total) : (total ? total + ' ' + word : '');
		}
	}

	// How long to keep showing "Searching..." before giving up with a
	// "no results" message, when a search returns nothing.
	var AMULE_NO_RESULTS_MS = 30000;

	function amuleShowNoResults(val) {
		var tbody = document.querySelector('#searchResultsTable tbody');
		if (!tbody) return;
		var es = (window.amuleLang && window.amuleLang() === 'es');
		var q = val ? ' "' + val + '"' : '';
		var msg = es ? ('No se han encontrado resultados para' + q + '.')
		             : ('No results found for' + (val ? ' "' + val + '"' : '') + '.');
		tbody.innerHTML =
			'<tr><td colspan="5" style="text-align:center;padding:28px 0;color:#90a4ae;font-size:14px;">' +
			'<i class="bi bi-search" style="font-size:22px;opacity:0.5;"></i>' +
			'<div style="margin-top:10px;">' + msg + '</div>' +
			'</td></tr>';
	}

	function amulePoll() {
		if (window._amuleSearchPending) return;
		if (document.querySelectorAll('#searchResultsTable input[type="checkbox"]:checked').length > 0) return;
		fetch('amuleweb-main-search.php')
			.then(function(r) { return r.text(); })
			.then(function(html) {
				if (window._amuleSearchPending) return;
				var d = new DOMParser().parseFromString(html, 'text/html');
				var nb = d.querySelector('#searchResultsTable tbody');
				if (!nb) return;
				var filtered = amuleFilteredRows(nb, amuleGetBaseline());
				if (filtered.length > 0) {
					window._amuleSearchActive = false; // got results
					amuleRenderFiltered(nb, false);
				} else if (window._amuleSearchActive &&
						(Date.now() - window._amuleSearchStartedAt) > AMULE_NO_RESULTS_MS) {
					// Search has had enough time and still nothing: stop waiting.
					window._amuleSearchActive = false;
					amuleShowNoResults(window._amuleSearchQuery);
				}
				// else: still within the wait window, keep the spinner shown
			})
			.catch(function() {});
	}

	// Two session-scoped hash sets reconcile the "in downloads" mark:
	// The "in downloads" mark reflects the ACTUAL current download list (not the
	// sticky server "present"/AlreadyHave flag, which also covers shared/known
	// files). window._amuleDLHashes holds the hashes currently being downloaded;
	// it is refreshed from the downloads page, so removing a download un-marks it.
	window._amuleDLHashes = window._amuleDLHashes || {};

	function amuleRefreshDownloadHashes(done) {
		fetch('amuleweb-main-dload.php')
			.then(function(r) { return r.text(); })
			.then(function(html) {
				var d = new DOMParser().parseFromString(html, 'text/html');
				var set = {};
				var cbs = d.querySelectorAll('#downloadsTable input[type="checkbox"]');
				for (var i = 0; i < cbs.length; i++) {
					var n = cbs[i].getAttribute('name');
					if (n && n.length === 32) set[n] = 1;
				}
				window._amuleDLHashes = set;
				if (done) done();
			})
			.catch(function() { if (done) done(); });
	}

	// Reconcile every result row's mark against the current download list.
	function amuleApplyMarks() {
		var dl = window._amuleDLHashes || {};
		var rows = document.querySelectorAll('#searchResultsTable tbody tr[data-hash]');
		for (var i = 0; i < rows.length; i++) {
			var tr = rows[i];
			var h = tr.getAttribute('data-hash');
			var marked = tr.classList.contains('amule-have-row');
			var shouldMark = !!dl[h];
			if (shouldMark && !marked) amuleMarkRowAsHave(tr);
			else if (!shouldMark && marked) amuleUnmarkRow(tr);
		}
		amuleLocalizeHave();
	}

	// Mark a row as "in downloads" (badge + tint). The checkbox and download
	// button are kept so the file can be re-downloaded on purpose. DOM only.
	function amuleMarkRowAsHave(tr) {
		if (!tr) return;
		tr.classList.add('amule-have-row');
		tr.setAttribute('data-present', '1');
		var lbl = tr.querySelector('td label');
		if (lbl && !lbl.querySelector('.amule-have-badge')) {
			var b = document.createElement('span');
			b.className = 'amule-have-badge badge text-bg-success';
			b.style.cssText = 'font-size:10px;vertical-align:middle;margin-left:6px;';
			lbl.appendChild(b);
		}
	}

	// Remove the "in downloads" mark (badge + tint). Checkbox/button stay.
	function amuleUnmarkRow(tr) {
		if (!tr) return;
		tr.classList.remove('amule-have-row');
		tr.removeAttribute('data-present');
		var badge = tr.querySelector('.amule-have-badge');
		if (badge) badge.remove();
	}

	// Send a single search result to downloads. aMule 3.0.0's
	// amule_do_search_download_cmd does not actually queue the file, so we build
	// an ed2k link from the row (name|size|hash) and use the ed2k-link handler,
	// which works reliably. The row is then marked as "in downloads".
	function amuleDownloadOne(el) {
		var tr = el.closest ? el.closest('tr') : null;
		if (!tr) return;
		var hash = tr.getAttribute('data-hash');
		var size = tr.getAttribute('data-size');
		var lbl = tr.querySelector('td label');
		var name = '';
		if (lbl) {
			var c = lbl.cloneNode(true);
			var badge = c.querySelector('.amule-have-badge');
			if (badge) badge.remove();
			name = c.textContent.trim();
		}
		if (!hash || !size || !name) return;

		// Category for the ed2k handler. It rejects an empty category, so read the
		// footer's #selectcat select and fall back to "all".
		var catSel = document.getElementById('selectcat');
		var cat = (catSel && catSel.value) ? catSel.value : 'all';
		var icon = el ? el.querySelector('i') : null;

		var link = 'ed2k://|file|' + name + '|' + size + '|' + hash + '|/';
		var body = new URLSearchParams();
		body.append('Submit', 'x');
		body.append('ed2klink', link);
		body.append('selectcat', cat);

		fetch('amuleweb-main-search.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body.toString()
			})
			.then(function() {
				window._amuleDLHashes[hash] = 1; // optimistic; confirmed on next refresh
				amuleMarkRowAsHave(tr);
			})
			.catch(function() {
				if (icon) { icon.className = 'bi bi-x-lg'; icon.style.color = '#ef5350'; }
			});
	}

	document.addEventListener('DOMContentLoaded', function() {
		// Apply baseline filter to the server-rendered (full) result list on load
		var initial = document.querySelector('#searchResultsTable tbody');
		if (initial) {
			var snapshot = initial.cloneNode(true);
			amuleRenderFiltered(snapshot, true);
		}

		// Load the current download list, then mark matching results; keep it
		// fresh so removed/finished downloads stop being marked.
		amuleRefreshDownloadHashes(amuleApplyMarks);
		setInterval(function() { amuleRefreshDownloadHashes(amuleApplyMarks); },
			1000 * (window._amuleRefresh > 0 ? window._amuleRefresh : 10));

		var mainform = document.forms['mainform'];
		if (mainform) {
			mainform.addEventListener('submit', function(e) {
				var cmd = mainform.command ? mainform.command.value : '';
				if (cmd !== 'search') return; // download: let page reload normally

				e.preventDefault();

				var val = mainform.searchval ? mainform.searchval.value : '';
				localStorage.setItem('amule_last_search', val || '');
				localStorage.setItem('amule_last_searchtype', mainform.searchtype ? mainform.searchtype.value : 'Global');

				// Snapshot ALL hashes currently in the daemon's accumulated list
				// so they get hidden — only this search's new results will show.
				var baseline = {};
				fetch('amuleweb-main-search.php')
					.then(function(r) { return r.text(); })
					.then(function(html) {
						var d = new DOMParser().parseFromString(html, 'text/html');
						var rows = d.querySelectorAll('#searchResultsTable tbody tr');
						for (var i = 0; i < rows.length; i++) {
							var h = amuleRowHash(rows[i]);
							if (h) baseline[h] = 1;
						}
						amuleSetBaseline(baseline);

						// Now launch the actual search (urlencoded — amuleweb can't parse multipart)
						window._amuleSearchPending = true;
						window._amuleSearchActive = true;
						window._amuleSearchStartedAt = Date.now();
						window._amuleSearchQuery = val;
						amuleShowSearching(val);
						var body = new URLSearchParams(new FormData(mainform)).toString();
						return fetch('amuleweb-main-search.php', {
							method: 'POST',
							headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
							body: body
						});
					})
					.then(function() {
						setTimeout(function() { window._amuleSearchPending = false; }, 6000);
					})
					.catch(function() { window._amuleSearchPending = false; });
			});
		}

		// Restore last search term and type in the form
		var lastSearch = localStorage.getItem('amule_last_search');
		var lastType = localStorage.getItem('amule_last_searchtype');
		if (lastSearch) {
			var input = document.querySelector('input[name="searchval"]');
			if (input) input.value = lastSearch;
		}
		if (lastType) {
			var sel = document.querySelector('select[name="searchtype"]');
			if (sel) {
				for (var i = 0; i < sel.options.length; i++) {
					if (sel.options[i].value === lastType || sel.options[i].text === lastType) {
						sel.selectedIndex = i;
						break;
					}
				}
			}
		}

		// Start auto-refresh polling (results-only, no full page reload)
		if (window._amuleRefresh > 0) {
			setInterval(amulePoll, 1000 * window._amuleRefresh);
		}

		// Scroll-to-top button
		window.addEventListener('scroll', function() {
			var s = document.getElementById('scroll');
			if (!s) return;
			s.style.display = (window.scrollY > 100) ? 'flex' : 'none';
		});
	});

	function scrollToTop() { window.scrollTo({ top: 0, behavior: 'smooth' }); }

	function formCommandSubmit(command)
		{
			<?php
				if ($_SESSION["guest_login"] != 0) {
						echo 'alert("You logged in as guest - commands are disabled");';
						echo "return;";
				}
			?>
			if ( command == "download" ) {
				// Download each checked result via the ed2k-link method (the daemon's
				// search-download command is broken in aMule 3.0.0).
				var checked = document.querySelectorAll('#searchResultsTable tbody input[type="checkbox"]:checked');
				var rows = [];
				checked.forEach(function(cb) {
					if (cb.name === 'selectAllFiles') return;
					var tr = cb.closest('tr');
					if (tr) rows.push(tr);
				});
				if (rows.length === 0) return;
				if (!confirm("Download selected " + rows.length + " files ?")) return;
				rows.forEach(function(tr) {
					var a = tr.querySelector('a[onclick*="amuleDownloadOne"]');
					if (a) amuleDownloadOne(a);
				});
				return;
			}
			var frm=document.forms.mainform
			frm.command.value=command
			frm.submit()
		}
	function selectAll(check)
		{
			var checkboxes = document.querySelectorAll('#searchResultsTable input[type="checkbox"]');
			checkboxes.forEach(function(checkbox) { checkbox.checked = check.checked; });
		}
	</script>
</head>

<body class="amule-bs5">

	<!-- Navigation bar :: common to all pages -->
	<nav class="navbar navbar-expand-lg fixed-top amule-navbar">
		<div class="container-fluid">
			<a class="navbar-brand" href="amuleweb-main-search.php">
				<img src="logo-nav-brax.png" class="logo-nav" alt="aMule">
				aMule <span class="amule-brand-rest">WebUI</span>
				<small style="font-size:11px;">powered by micromante <span style="color:#e74c3c;">&#10084;</span></small>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="mainNav">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link" title="Downloads and Uploads" href="amuleweb-main-dload.php"><i class="bi bi-arrow-down-up"></i> Transfer</a></li>
					<li class="nav-item"><a class="nav-link" title="Sharing" href="amuleweb-main-shared.php"><i class="bi bi-share-fill"></i> Shared</a></li>
					<li class="nav-item"><a class="nav-link active" title="Search" href="amuleweb-main-search.php"><i class="bi bi-search"></i> Search</a></li>
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
	<form action="amuleweb-main-search.php" method="post" name="mainform">
	<input type="hidden" name="command" value="search">

	<!-- Search form card -->
	<div class="card mt-3">
		<div class="card-body">
			<div class="row g-2 align-items-center">
				<div class="col-12 col-md">
					<div class="input-group amule-search-bar">
						<a class="btn btn-primary" href="amuleweb-main-search.php" title="Refresh to see the results"><i class="bi bi-arrow-clockwise"></i></a>
						<input type="text" placeholder="Text query..." name="searchval" class="form-control" autofocus>
						<select class="form-select" style="max-width:130px;" name="searchtype">
							<option>Local</option>
							<option selected>Global</option>
							<option>Kad</option>
						</select>
						<input class="btn btn-primary" name="Search" type="submit" value="Search" style="min-width:110px;">
					</div>
				</div>
			</div>
			<div class="row g-2 mt-1">
				<div class="col-12 col-sm-4">
					<div class="input-group">
						<span class="input-group-text">Availability</span>
						<input type="text" class="form-control" name="avail">
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="input-group">
						<span class="input-group-text">Min size</span>
						<input type="text" class="form-control" name="minsize">
						<select class="form-select" name="minsizeu" style="max-width:90px;">
							<option>Byte</option>
							<option>KByte</option>
							<option selected>MByte</option>
							<option>GByte</option>
						</select>
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="input-group">
						<span class="input-group-text">Max size</span>
						<input type="text" class="form-control" name="maxsize">
						<select class="form-select" name="maxsizeu" style="max-width:90px;">
							<option>Byte</option>
							<option>KByte</option>
							<option selected>MByte</option>
							<option>GByte</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Download-selected bar -->
	<div class="card">
		<div class="card-body">
			<div class="row g-2 align-items-center">
				<div class="col-auto"><span class="text-secondary">For each element selected</span></div>
				<div class="col-auto">
					<a class="btn btn-success" title="Download" href="javascript:formCommandSubmit('download');"><i class="bi bi-download"></i> Download</a>
				</div>
				<div class="col-auto"><span class="text-secondary">in category</span></div>
				<div class="col-auto">
					<select class="form-select" name="targetcat">
					<?php
						$cats = amule_get_categories();
						foreach($cats as $c) {
							echo "<option>", $c, "</option>";
						}
					?>
					</select>
				</div>
			</div>
		</div>
	</div>

	<!-- Results card -->
	<div class="card">
		<div class="card-header d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
			<span>SEARCH RESULTS <span id="resultFilterCount" class="ms-2 small text-light opacity-75"></span></span>
			<input type="text" id="resultFilter" class="form-control form-control-sm" style="max-width:280px;" placeholder="Filter results (e.g. 1080 mkv)" autocomplete="off" oninput="amuleApplyFilter();">
		</div>
		<div>
			<table class="table table-hover align-middle mb-0" id="searchResultsTable">
				<thead>
					<tr>
						<th style="width:64px;"><input type="checkbox" class="form-check-input" name="selectAllFiles" onclick='selectAll(this);'></th>
						<th><a onclick="amuleSortBy('name');">File name <span class="sort-arrow" data-col="name"></span></a></th>
						<th style="width:84px;"><a onclick="amuleSortBy('size');">Size <span class="sort-arrow" data-col="size"></span></a></th>
						<th style="width:74px;" class="d-none d-md-table-cell"><a onclick="amuleSortBy('sources');">Sources <span class="sort-arrow" data-col="sources"></span></a></th>
						<th style="width:120px;" class="d-none d-md-table-cell">Network</th>
					</tr>
				</thead>
				<tbody>

				<?php
					function CastToXBytes($size) {
						if ( $size < 1024 ) {
							$result = $size . " b";
						} elseif ( $size < 1048576 ) {
							$result = ($size / 1024.0) . "kb";
						} elseif ( $size < 1073741824 ) {
							$result = ($size / 1048576.0) . "mb";
						} else {
							$result = ($size / 1073741824.0) . "gb";
						}
						return $result;
					}

					$sort_order;$sort_reverse;

					function my_cmp($a, $b) {
						global $sort_order, $sort_reverse;

						switch ( $sort_order) {
							case "size": $result = $a->size > $b->size; break;
							case "name": $result = $a->name > $b->name; break;
							case "sources": $result = $a->sources > $b->sources; break;
						}

						if ( $sort_reverse ) {
							$result = !$result;
						}

						return $result;
					}

					function str2mult($str) {
						$result = 1;
						switch($str) {
							case "Byte":	$result = 1; break;
							case "KByte":	$result = 1024; break;
							case "MByte":	$result = 1012*1024; break;
							case "GByte":	$result = 1012*1024*1024; break;
						}
						return $result;
					}

					function cat2idx($cat) {
					        	$cats = amule_get_categories();
					        	$result = 0;
					        	foreach($cats as $i => $c) {
					        		if ( $cat == $c) $result = $i;
					        	}
					    		return $result;
					}

					if ($_SESSION["guest_login"] == 0) {
						if ( $HTTP_GET_VARS["command"] == "search") {
							$search_type = -1;
							switch($HTTP_GET_VARS["searchtype"]) {
								case "Local": $search_type = 0; break;
								case "Global": $search_type = 1; break;
								case "Kad": $search_type = 2; break;
							}
							$_SESSION["last_searchtype"] = $HTTP_GET_VARS["searchtype"];
							$min_size = $HTTP_GET_VARS["minsize"] == "" ? 0 : $HTTP_GET_VARS["minsize"];
							$max_size = $HTTP_GET_VARS["maxsize"] == "" ? 0 : $HTTP_GET_VARS["maxsize"];

							$min_size *= str2mult($HTTP_GET_VARS["minsizeu"]);
							$max_size *= str2mult($HTTP_GET_VARS["maxsizeu"]);

							amule_do_search_start_cmd($HTTP_GET_VARS["searchval"],
								"", "",
								$search_type, $HTTP_GET_VARS["avail"], $min_size, $max_size);
						} elseif ( $HTTP_GET_VARS["command"] == "download") {
							foreach ( $HTTP_GET_VARS as $name => $val) {
								// this is file checkboxes
								if ( (strlen($name) == 32) and ($val == "on") ) {
									$cat = $HTTP_GET_VARS["targetcat"];
									$cat_idx = cat2idx($cat);
									amule_do_search_download_cmd($name, $cat_idx);
								}
							}
						}
					}
					$search = amule_load_vars("searchresult");

					$sort_order = $HTTP_GET_VARS["sort"];

					if ( $sort_order == "" ) {
						$sort_order = $_SESSION["search_sort"];
					} else {
						if ( $_SESSION["search_sort_reverse"] == "" ) {
							$_SESSION["search_sort_reverse"] = 0;
						} else {
							$_SESSION["search_sort_reverse"] = !$_SESSION["search_sort_reverse"];
						}
					}

					$sort_reverse = $_SESSION["search_sort_reverse"];
					if ( $sort_order != "" ) {
						$_SESSION["search_sort"] = $sort_order;
						usort($search, "my_cmp");
					}

					// Network/source label for the current search (aMule does not
					// track a per-result server, so it's the network we searched on).
					$stype = $_SESSION["last_searchtype"];
					if ( $stype == "Kad" ) {
						$network_label = "Kad";
					} else {
						$st = amule_get_stats();
						$srv = $st["serv_name"];
						if ( $stype == "Global" ) {
							$network_label = "eD2k (global)";
						} elseif ( $srv != "" ) {
							$network_label = "eD2k: " . $srv;
						} else {
							$network_label = "eD2k";
						}
					}

					foreach ($search as $file) {
						// Rows are rendered uniformly. The "in downloads" mark is added
						// client-side by reconciling against the actual current download
						// list, so removing a download un-marks it again.
						echo '<tr data-size="', $file->size, '" data-sources="', $file->sources, '" data-hash="', $file->hash, '">';
						echo '<td style="white-space:nowrap;">',
							'<input type="checkbox" class="form-check-input" name="', $file->hash, '"> ',
							'<a href="javascript:void(0);" title="Download this file" ',
							'onclick="amuleDownloadOne(this);" class="ms-1 text-decoration-none">',
							'<i class="bi bi-download" style="font-size:16px;color:#4db6ac;"></i>',
							'</a>',
							'</td>';
						echo '<td><label style="font-size:13px;">', $file->name, '</label></td>';
						echo '<td style="font-size:13px;">', CastToXBytes($file->size), '</td>';
						echo '<td style="font-size:13px;" class="d-none d-md-table-cell"><span class="badge bg-amule">', $file->sources, '</span></td>';
						echo '<td style="font-size:13px;color:#90a4ae;" class="d-none d-md-table-cell">', $network_label, '</td>';
						echo '</tr>';
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
	</form>

	<!-- Footer: ed2k link + connection status -->
	<div class="amule-footer">
		<div class="row g-3 align-items-center">
			<div class="col-12 col-lg-6">
				<form name="formlink" method="post" action="amuleweb-main-search.php" id="formed2link">
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
