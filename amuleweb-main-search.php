<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>aMule - Control Panel - Search</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<a href="#" id="scroll" style="display: none;"><span></span></a>

	<?php
		echo "<script>window._amuleRefresh = " . ($_SESSION["auto_refresh"] > 0 ? $_SESSION["auto_refresh"] : 0) . ";</script>";
	?>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<!-- Inclusion of bootstrap css -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" integrity="sha384-/Gm+ur33q/W+9ANGYwB2Q4V0ZWApToOzRuA8md/1p9xMMxpqnlguMvk8QuEFWA1B" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="custom.css">
	<script src="i18n.js"></script>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" integrity="sha384-7tY7Dc2Q8WQTKGz2Fa0vC4dWQo07N4mJjKvHfIGnxuC4vPqFGFQppd9b3NWpf18/" crossorigin="anonymous">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css" integrity="sha384-BD3p+z3TqIhBK2OaMBRzK4Nz02t4OQcwrEkJxy3PAqU2Rwm1giS6RCgvBDk6+iPH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js" integrity="sha384-oFMgcGzKX7GaHtF4hx14KbxdsGjyfHK6m1comHjI1FH6g4m6qYre+4cnZbwaYbHD" crossorigin="anonymous"></script>

	<script type="text/Javascript">
		$(function () { $("[data-toggle='tooltip']").tooltip(); });
		$(function () { $("[data-toggle='popover']").popover(); });
	</script>

	<!-- Style for navigation bar -->
	<style type="text/css">
		body {
			padding-top: 102px;
			background-color: #39425f;
		}
		.logo-nav {
			height: 40px;
			width: 40px;
		}
		.navbar-brand {
			padding-top: 5px;
		}
		.navbar-link:hover {
			color: white !important;
		}
	</style>

	<!-- Tasks panel -->
	<style type="text/css">
		.panel-tasks {
			width: 95%;
			margin-left: auto;
			margin-right: auto;
		}
		.panel-center {
			text-align: center;
			margin: auto;
		}
		#filter {
			width: 125px;
			height: 28px;
			border-top-right-radius: 0px;
			border-bottom-right-radius: 0px;
		}
		#category {
			width: 125px;
			height: 28px;
			border-radius: 0px;
		}
		.btn-filter {
			border-top-left-radius: 0px;
			border-bottom-left-radius: 0px;
		}
		.form-inline {
			margin-top: 1px;
			margin-bottom: 1px;
		}
	</style>

	<!-- Tables -->
	<style type="css/text">
		.panel-tr {
			width: 95%;
			margin-left: auto;
			margin-right: auto;
			margin-top: 10px;
		}
	</style>

        <!-- Styling for footer -->
        <style text="css/text">
                #footer {
                        position: fixed;
			left:0;
                        bottom: 0;
                        width: 100%;
                        /* Set the fixed height of the footer here */
                        height:auto;
                        background-color: #2f303d;
                }
                #ed2link {
                        margin-right: 5px;
                        width: 120px;
                }
                #selectcat {
                        border-radius: 0px;
                        width: 100px;
                }
                #formed2link {
                        margin: 5px;
                }
        </style>

<!-- /* Styling for Brax AmuleWebUI Material Theme */ -->
        <style text="css/text">

		.navbar {
                background-color:#2f303d;
                }
                .label-success {
                        background-color:#319a9b;
                }
                .label-default {
                        background-color:#ffffff;
                        color:#319a9b;
                }
                .panel {
                	background:transparent;
	            }
                .panel-heading{
                 	background-color:#319a9b;
                	border: 0;
                }
                .table > thead > tr > th, .table > thead > tr > td {
                	border: 1;
                }
                .glyphicon {
                 	color:#319a9b;
                }
		.btn:hover .glyphicon{
 		  	color:#fff;
	 	  }
		  a:hover {
                        color:#fff;
                       }
		.badge {
			background-color:#319a9b;
			color:#ffffff;
		}

                a {
                	color:#4db6ac;
                }
                h4 {
                	color:#cfd8dc;
                }
                 td {
                	color:#cfd8dc;
                }
        </style>

 <!-- /* Styling for Brax AmuleWebUI Material Theme ScrollTOP */-->
<style text="css/text">
	#scroll {
	    position:fixed;
	    right:10px;
	    bottom:50px;
	    cursor:pointer;
	    width:50px;
	    height:50px;
	    background-color:#1565c0;
	    text-indent:-9999px;
	    display:none;
	    -webkit-border-radius:60px;
	    -moz-border-radius:60px;
	    border-radius:60px
}
	#scroll span {
	    position:absolute;
	    top:50%;
	    left:50%;
	    margin-left:-8px;
	    margin-top:-12px;
	    height:0;
	    width:0;
	    border:8px solid transparent;
	    border-bottom-color:#ffffff;
}
	#scroll:hover {
	    background-color:#319a9b;
	    opacity:1;filter:"alpha(opacity=100)";
	    -ms-filter:"alpha(opacity=100)";
}
</style>
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

	// Hash of a row = the checkbox name (32-char ed2k hash)
	function amuleRowHash(tr) {
		var cb = tr.querySelector('input[type="checkbox"]');
		return cb ? cb.getAttribute('name') : null;
	}

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
		for (var i = 0; i < rows.length; i++) {
			cb.appendChild(rows[i].cloneNode(true));
		}
		amuleApplySort();   // keep the current sort order after re-render
		amuleApplyFilter(); // keep the text filter applied after re-render
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
			if (trs[i].querySelector('input[type="checkbox"]')) rows.push(trs[i]);
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
			var rowCb = rows[i].querySelector('input[type="checkbox"]');
			if (!rowCb) { continue; } // skip message rows ("Searching...", etc.)
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

	function amulePoll() {
		if (window._amuleSearchPending) return;
		if (document.querySelectorAll('#searchResultsTable input[type="checkbox"]:checked').length > 0) return;
		fetch('amuleweb-main-search.php')
			.then(function(r) { return r.text(); })
			.then(function(html) {
				if (window._amuleSearchPending) return;
				var d = new DOMParser().parseFromString(html, 'text/html');
				var nb = d.querySelector('#searchResultsTable tbody');
				if (nb) amuleRenderFiltered(nb, false);
			})
			.catch(function() {});
	}

	// Send a single search result to downloads (same effect as ticking its
	// checkbox and pressing Download), via AJAX so the result list is kept.
	// Reads the hash from the row's checkbox to avoid template-engine quoting issues.
	function amuleDownloadOne(el) {
		var tr = el.closest ? el.closest('tr') : null;
		var cb = tr ? tr.querySelector('input[type="checkbox"]') : null;
		var hash = cb ? cb.getAttribute('name') : null;
		if (!hash) return;

		var catSel = document.querySelector('select[name="targetcat"]');
		var cat = catSel ? catSel.value : '';
		var icon = el ? el.querySelector('.glyphicon') : null;

		var body = new URLSearchParams();
		body.append('command', 'download');
		body.append(hash, 'on');
		body.append('targetcat', cat);

		fetch('amuleweb-main-search.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body.toString()
			})
			.then(function() {
				if (icon) {
					icon.className = 'glyphicon glyphicon-ok';
					icon.style.color = '#66bb6a';
				}
				if (el) {
					el.style.pointerEvents = 'none';
					el.title = 'Added to downloads';
				}
			})
			.catch(function() {
				if (icon) {
					icon.className = 'glyphicon glyphicon-remove';
					icon.style.color = '#ef5350';
				}
			});
	}

	document.addEventListener('DOMContentLoaded', function() {
		// Apply baseline filter to the server-rendered (full) result list on load
		var initial = document.querySelector('#searchResultsTable tbody');
		if (initial) {
			var snapshot = initial.cloneNode(true);
			amuleRenderFiltered(snapshot, true);
		}

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
	});

	function formCommandSubmit(command)
		{
			<?php
				if ($_SESSION["guest_login"] != 0) {
						echo 'alert("You logged in as guest - commands are disabled");';
						echo "return;";
				}
			?>
			if ( command == "download" ) {
				var boxchecked = document.querySelectorAll('input[type="checkbox"]:checked');
				var selectedFiles = Object.values(boxchecked).filter(selected => selected.name != 'selectAllFiles').length;
				if (selectedFiles == 0)
					return;
				var res = confirm("Download selected " + (selectedFiles) + " files ?")
				if ( res == false ) {
					return;
				}
			}
			var frm=document.forms.mainform
			frm.command.value=command
			frm.submit()
		}
	function selectAll(check)
		{
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			if (check.checked)
			{
				checkboxes.forEach(function(checkbox) {
					checkbox.checked = true;
				});
			}
			else
			{
				checkboxes.forEach(function(checkbox) {
					checkbox.checked = false;
				});
			}
		}
	</script>

</head>

<body class="animated fadeIn" style="animation-duration: 1.5s">

        <script type="text/JavaScript">

$(document).ready(function(){
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('#scroll').fadeIn();
        } else {
            $('#scroll').fadeOut();
        }
    });
    $('#scroll').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });
});


	</script>
	<!-- Navigation bar :: This part will be common in all the scripts -->
	<nav class="navbar navbar-fixed-top" role="navigation">
	    <div class="container">
	    	<a class="navbar-brand" href="#"><img src="logo-nav-brax.png" class="logo-nav"> aMule WebUI <small style="font-weight:normal;font-size:12px;opacity:0.9;">powered by micromante <span style="color:#e74c3c;">&#10084;</span></small></a>
	    	<form class="navbar-form navbar-right" role="form" name="login">
				<div class="collapse navbar-collapse">
									<div class="btn-group">
                                                <!-- Downloads -->
                                                <a class="btn  navbar-link" title="Downloads and Uploads" href="amuleweb-main-dload.php">
                                                                <span class="glyphicon glyphicon-transfer"></span>
                                                                <div style="font-size:13px"><br>Transfer</div>
                                                </a>
                                                <!-- Shared -->
                                                <a class="btn  navbar-link" title="Sharing" href="amuleweb-main-shared.php">
                                                                <span class="glyphicon glyphicon-share"></span>
                                                                <div style="font-size:13px"><br>Shared</div>

                                                                </a>
                                                <!-- Search -->
                                                <a class="btn  navbar-link" title="Search" href="amuleweb-main-search.php">
                                                                <span class="glyphicon glyphicon-search"></span>
                                                                <div style="font-size:13px"><br>Search</div>

                                                </a>
                                                <!-- Servers -->
                                                <a class="btn  navbar-link" title="Servers" href="amuleweb-main-servers.php">
                                                                <span class="glyphicon glyphicon-tasks"></span>
                                                                <div style="font-size:13px"><br>Server</div>

                                                </a>
                                                <!-- Kad -->
                                                <a class="btn  navbar-link" title="Kademlia" href="amuleweb-main-kad.php">
                                                                <span class="glyphicon glyphicon-asterisk"></span>
                                                                <div style="font-size:13px"><br>Kad</div>

                                                </a>
                                                <!-- Stats -->
                                                <a class="btn  navbar-link" title="Statistics" href="amuleweb-main-stats.php">
                                                                <span class="glyphicon glyphicon-stats"></span>
                                                                <div style="font-size:13px"><br>Stats</div>

                                                </a>
                                        </div>
                                        <div class="btn-group">
                                                <!-- Configuration -->
                                                <a class="btn navbar-link" title="Configurations" href="amuleweb-main-prefs.php">
                                                                <span class="glyphicon glyphicon-cog"></span>
                                                                <div style="font-size:13px"><br>Settings</div>

                                                </a>
                                                <!-- Log -->
                                                <a class="btn  navbar-link" title="Log" href="amuleweb-main-log.php">
                                                                <span class="glyphicon glyphicon-flag"></span>
                                                                <div style="font-size:13px"><br>Logs</div>

                                                </a>
                                                <!-- Exit -->
                                                <a class="btn navbar-link" title="Exit" href="login.php">
                                                                <span class="glyphicon glyphicon-off"></span>
                                                                <div style="font-size:13px"><br>Exit</div>

                                                </a>
				   	</div>
		    	</div>
    		</form>
    		</div><!--/.navbar-collapse -->
    	</div>
    </nav>


    <!-- Commands -->
    <form action="amuleweb-main-search.php" method="post" name="mainform">
    <input type="hidden" name="command" value="search">
    <div class="panel  panel-tasks">
  		<div class="panel-body container panel-center">
    		<div class="form-inline form-tasks">
    		<p><div class="btn-group">
    			<a class="btn btn-info btn-group" href="amuleweb-main-search.php?search_sort=<?php echo($HTTP_GET_VARS["sort"]);?>" title="Refresh to see the results" style="height:34px;">
    				<span class="glyphicon glyphicon-refresh" style="color:white"></span>
    			</a>
    			<input type="text" placeholder="Text query..." name="searchval" class="form-control btn-group" style="border-radius:0px; z-index:1;" size="70">
    			<select class="form-control btn-group" style="border-radius:0px; background-color:#eee;" name="searchtype">
    				<option>Local</option>
    				<option selected>Global</option>
    				<option>Kad</option>
			</select>
    			<input class="btn btn-info btn-group" name="Search" type="submit" value="Search" style="width:140px;">
    		</div></p><p>
    		<div class="btn-group">
    			<label class="form-control btn-group" style="border-top-right-radius:0px; border-bottom-right-radius:0px; background-color:#eee;">Availability</label>
    			<input type="text" class="form-control btn-group" name="avail" style="border-top-left-radius:0px; border-bottom-left-radius:0px; z-index:1;" size="10">
    		</div>
    		<div class="btn-group">
    			<label class="form-control btn-group" style="border-top-right-radius:0px; border-bottom-right-radius:0px; background-color:#eee;">Min size</label>
    			<input type="text" class="form-control btn-group" name="minsize" style="border-radius: 0px; z-index:1;" size="5">
    			<select class="form-control btn-group" style="border-radius:0px; background-color:#eee;" name="minsizeu">
    				<option>Byte</option>
					<option>KByte</option>
					<option selected>MByte</option>
					<option>GByte</option>
    			</select>
    			<label class="form-control btn-group" style="border-radius:0px; background-color:#eee;">Max size</label>
    			<input type="text" class="form-control btn-group" name="maxsize" style="border-radius: 0px; z-index:1;" size="5">
    			<select class="form-control btn-group" style="border-top-left-radius: 0px; border-bottom-left-radius:0px; background-color:#eee;" name="maxsizeu">
    				<option>Byte</option>
					<option>KByte</option>
					<option selected>MByte</option>
					<option>GByte</option>
    			</select>
    		</div>
    		</p>
  			</div>
  		</div>
	</div>

    <div class="panel  panel-tasks">
  		<div class="panel-body container panel-center">
    		<div class="form-inline form-tasks">
    		<div class="btn-group">
    			<label class="form-control btn-group" style="border-top-right-radius:0px; border-bottom-right-radius:0px; background-color:#eee;">For each element selected</label>
			<a class="btn btn-success btn-group" title="Download" href="javascript:formCommandSubmit('download');" style="border-radius:0px;">Download</a>
    			<label class="form-control btn-group" style="border-radius:0px; background-color:#eee;"> in category </label>
    			<select class="form-control btn-group" name="targetcat" style="border-top-left-radius:0px; border-bottom-left-radius:0px; background-color:#eee;">
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

	<!-- BEGIN OF CENTRAL BODY -->
	<div class="container-fluid panel-tr">
		<!-- Table Download -->
		<div class="panel" style="margin-bottom:60px;">
		<div class="panel-heading" style="display:flex;align-items:center;justify-content:center;position:relative;">
			<h4 style="margin:0;">SEARCH RESULTS</h4>
			<input type="text" id="resultFilter" placeholder="Filter results (e.g. 1080 mkv)" autocomplete="off"
				oninput="amuleApplyFilter();"
				style="position:absolute;right:12px;width:260px;height:30px;padding:4px 10px;font-size:13px;border:0;border-radius:4px;background:#2a2f42;color:#cfd8dc;">
			<span id="resultFilterCount" style="position:absolute;right:282px;font-size:12px;color:#e0f2f1;"></span>
		</div>
			<table class="table" id="searchResultsTable">
				<thead>
					<tr>
						<th><input type="checkbox" name="selectAllFiles" onclick='selectAll(this);'></th>
						<th><a href="javascript:void(0);" onclick="amuleSortBy('name');" style="cursor:pointer;">File name <span class="sort-arrow" data-col="name"></span></a></th>
						<th><a href="javascript:void(0);" onclick="amuleSortBy('size');" style="cursor:pointer;">Size <span class="sort-arrow" data-col="size"></span></a></th>
						<th><a href="javascript:void(0);" onclick="amuleSortBy('sources');" style="cursor:pointer;">Sources <span class="sort-arrow" data-col="sources"></span></a></th>
						<th>Network</th>
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
						echo '<tr data-size="', $file->size, '" data-sources="', $file->sources, '">';
						echo '<td style="white-space:nowrap;">',
							'<input type="checkbox" name="', $file->hash, '" style="vertical-align:middle;"> ',
							'<a href="javascript:void(0);" title="Download this file" ',
							'onclick="amuleDownloadOne(this);" ',
							'style="margin-left:6px;vertical-align:middle;">',
							'<span class="glyphicon glyphicon-download-alt" style="font-size:15px;color:#4db6ac;"></span>',
							'</a>',
							'</td>';
						echo '<td><label style="font-size:13px;">',$file->name,'</label></td>';
						echo '<td style="font-size:13px;">', CastToXBytes($file->size), '</td>';
						echo '<td style="font-size:13px;"><span class="badge badge-default">', $file->sources, '</span></td>';
						echo '<td style="font-size:13px;color:#90a4ae;white-space:nowrap;">', $network_label, '</td>';
						echo '</tr>';
					}


				?>
				</tbody>
			</table>
		</div>
	</form>

 	<!-- Footer -->
	<div id="footer">
		<div class="col-md-1"></div>
		<div class="col-md-5">
			<form name="formlink" method="post" class="form-inline" action="amuleweb-main-search.php" role="form" id="formed2link">
    			<div class="btn-group">
        			<input class="form-control btn-group" name="ed2klink" type="text" id="ed2klink" placeholder="ed2k:// - Insert link" style="border-top-right-radius: 0px; border-bottom-right-radius: 0px; height: 30px;" size="25">
        			<select class="form-control btn-group" name="selectcat" id="selectcat" style="height: 30px;">

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
        		<input class="btn btn-default btn-group" type="submit" name="Submit" value="Download link" onClick="amuleweb-main-dload.php" style="height: 30px;">
    		</div>
    </form>
		</div>
		<div class="col-md-5">
			<div class="form-inline" style="margin-top:10px;">
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

			    	echo '<span class="label label-default">ED2k:</span> ';
			    	echo '<span class="label label-', $ed2k_status, '">', $ed2k, '</span> ';
			    	echo '<span class="label label-default">KAD:</span> ';
			    	echo '<span class="label label-', $kad_status, '">', $kad1, ' ', $kad2, '</span>';
			    ?>
			</div>
		</div>
		<div class="col-md-1"></div>
	</div>
</form>
</body>
</html>
