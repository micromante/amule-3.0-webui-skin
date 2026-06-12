/*
 * Lightweight client-side i18n for the aMule WebUI.
 *
 * Strategy: the server always renders English. On every page load, if the
 * saved language is Spanish, we translate the interface chrome on top of it
 * (navbar, headings, table headers, labels, buttons, titles, placeholders,
 * footer status). Switching language just flips the saved value and reloads,
 * so going back to English needs no stored originals.
 *
 * Search results, download and upload rows (table bodies) are NEVER translated.
 * Option values and form-submitted values are NEVER translated either, since
 * the daemon matches them server-side (Local/Global/Kad, Byte/KByte, etc.).
 */
(function () {
	var STORE_KEY = 'amule_lang';

	var DICT = {
		// --- Navbar labels ---
		"Transfer": "Transferencias",
		"Shared": "Compartido",
		"Search": "Buscar",
		"Server": "Servidor",
		"Stats": "Estadísticas",
		"Settings": "Ajustes",
		"Logs": "Registros",
		"Exit": "Salir",
		"Refresh": "Refrescar",
		"Pause": "Pausar",
		"Resume": "Reanudar",
		"Remove": "Eliminar",
		"Filter": "Filtrar",
		"High Priority": "Subir prioridad",
		"Lower Priority": "Bajar prioridad",

		// --- Nav link titles ---
		"Downloads and Uploads": "Descargas y subidas",
		"Sharing": "Compartición",
		"Servers": "Servidores",
		"Kademlia": "Kademlia",
		"Statistics": "Estadísticas",
		"Configurations": "Configuración",
		"Log": "Registro",
		"Download this file": "Descargar este archivo",
		"Download": "Descargar",
		"Lower priority": "Bajar prioridad",
		"Higher priority": "Subir prioridad",
		"Refresh to see the results": "Refresca para ver los resultados",
		"Segments": "Segmentos",

		// --- Panel headings ---
		"DOWNLOAD": "DESCARGAS",
		"UPLOAD": "SUBIDAS",
		"SEARCH RESULTS": "RESULTADOS",
		"SERVERS": "SERVIDORES",
		"SHARED FILES": "ARCHIVOS COMPARTIDOS",
		"KAD STATUS": "ESTADO KAD",
		"AMULE LOG": "REGISTRO DE AMULE",
		"SERVER LOG": "REGISTRO DEL SERVIDOR",
		"PREFERENCES": "PREFERENCIAS",
		"STATISTICS": "ESTADÍSTICAS",
		"CONNECTION SETTINGS": "AJUSTES DE CONEXIÓN",
		"BANDWIDTH LIMITS": "LÍMITES DE ANCHO DE BANDA",
		"FILE SETTINGS": "AJUSTES DE ARCHIVOS",
		"WEBSERVER": "SERVIDOR WEB",

		// --- Table headers ---
		"File name": "Nombre del archivo",
		"File Name": "Nombre del archivo",
		"Size": "Tamaño",
		"Completed": "Completado",
		"Speed": "Velocidad",
		"Progress": "Progreso",
		"Sources": "Fuentes",
		"Network": "Red",
		"Status": "Estado",
		"Priority": "Prioridad",
		"Username": "Usuario",
		"Up": "Subida",
		"Down": "Bajada",
		"Transferred": "Transferido",
		"Requested": "Solicitado",
		"Accepted Rqst": "Solic. aceptadas",
		"Server name": "Nombre del servidor",
		"Description": "Descripción",
		"Address": "Dirección",
		"Users": "Usuarios",
		"Files": "Archivos",
		"Parameter": "Parámetro",

		// --- Labels / buttons / misc ---
		"For each element selected": "Para cada elemento seleccionado",
		"in category": "en categoría",
		"Availability": "Disponibilidad",
		"Min size": "Tamaño mín.",
		"Max size": "Tamaño máx.",
		"Download link": "Descargar enlace",
		"Apply": "Aplicar",
		"Connect": "Conectar",
		"Connections Count": "Número de conexiones",
		"KAD Nodes": "Nodos KAD",
		"Downloads": "Descargas",
		"Uploads": "Subidas",

		// --- Footer / status ---
		"Not connected": "No conectado",
		"Disconnected": "Desconectado",
		"Connecting ...": "Conectando ...",
		"Connected": "Conectado",

		// --- Login page ---
		"aMule Web Interface": "Interfaz web de aMule",
		"Welcome!": "¡Bienvenido!",
		"Please login to access the complete interface!": "¡Inicia sesión para acceder a la interfaz completa!",

		// --- Placeholders ---
		"Text query...": "Texto a buscar...",
		"Filter results (e.g. 1080 mkv)": "Filtrar resultados (ej. 1080 mkv)",
		"ed2k:// - Insert link": "ed2k:// - Introduce enlace",
		"Password": "Contraseña"
	};

	function getLang() {
		try { return localStorage.getItem(STORE_KEY) || 'en'; } catch (e) { return 'en'; }
	}

	function setLang(lang) {
		try { localStorage.setItem(STORE_KEY, lang); } catch (e) {}
	}

	function toggleLang() {
		setLang(getLang() === 'es' ? 'en' : 'es');
		location.reload();
	}
	window.amuleToggleLang = toggleLang;

	// Exposed so dynamically-generated messages (e.g. the search status) can
	// localize themselves at the moment they are built.
	window.amuleLang = getLang;

	// Selector for the data rows we must never translate
	var SKIP = '#searchResultsTable tbody, #downloadsTable tbody, #uploadsTable tbody, script, style';

	function inSkip(el) {
		return el && el.closest && el.closest(SKIP);
	}

	function translateTextNodes() {
		var walker = document.createTreeWalker(
			document.body,
			NodeFilter.SHOW_TEXT,
			{
				acceptNode: function (node) {
					var el = node.parentElement;
					if (!el || inSkip(el)) return NodeFilter.FILTER_REJECT;
					var txt = node.nodeValue.trim();
					if (txt && DICT.hasOwnProperty(txt)) return NodeFilter.FILTER_ACCEPT;
					return NodeFilter.FILTER_REJECT;
				}
			}
		);
		var nodes = [];
		while (walker.nextNode()) nodes.push(walker.currentNode);
		nodes.forEach(function (n) {
			var txt = n.nodeValue.trim();
			n.nodeValue = n.nodeValue.replace(txt, DICT[txt]);
		});
	}

	function translateAttrs() {
		['title', 'placeholder'].forEach(function (attr) {
			var els = document.querySelectorAll('[' + attr + ']');
			for (var i = 0; i < els.length; i++) {
				if (inSkip(els[i])) continue;
				var v = els[i].getAttribute(attr);
				if (v && DICT.hasOwnProperty(v.trim())) {
					els[i].setAttribute(attr, DICT[v.trim()]);
				}
			}
		});
		// Button / submit labels (display only — never the option/select values)
		var btns = document.querySelectorAll('input[type="submit"], input[type="button"]');
		for (var j = 0; j < btns.length; j++) {
			var bv = btns[j].value;
			if (bv && DICT.hasOwnProperty(bv.trim())) btns[j].value = DICT[bv.trim()];
		}
	}

	function applyTranslation() {
		if (getLang() !== 'es') return;
		try { translateTextNodes(); } catch (e) {}
		try { translateAttrs(); } catch (e) {}
	}

	// Inject the language toggle button into the navbar.
	// Supports both the Bootstrap 5 navbar (ul.navbar-nav with bi icons) and
	// the legacy Bootstrap 3 navbar (.btn-group with glyphicons), so it keeps
	// working while pages are migrated from BS3 to BS5.
	function injectToggle() {
		var lang = getLang();
		var label = (lang === 'es' ? 'EN' : 'ES');

		// Bootstrap 5 navbar
		var navUl = document.querySelector('.navbar-nav');
		if (navUl) {
			var li = document.createElement('li');
			li.className = 'nav-item';
			var a = document.createElement('a');
			a.className = 'nav-link';
			a.href = 'javascript:void(0);';
			a.title = 'Language / Idioma';
			a.setAttribute('onclick', 'amuleToggleLang();');
			a.innerHTML = '<i class="bi bi-globe2"></i> ' + label;
			li.appendChild(a);
			navUl.appendChild(li);
			return;
		}

		// Bootstrap 3 navbar (legacy)
		var group = document.querySelector('.navbar .btn-group');
		if (!group) return; // login page has no nav group
		var btn = document.createElement('a');
		btn.className = 'btn navbar-link';
		btn.href = 'javascript:void(0);';
		btn.title = 'Language / Idioma';
		btn.setAttribute('onclick', 'amuleToggleLang();');
		btn.innerHTML =
			'<span class="glyphicon glyphicon-globe"></span>' +
			'<div style="font-size:13px"><br>' + label + '</div>';
		group.insertBefore(btn, group.firstChild);
	}

	function init() {
		injectToggle();
		applyTranslation();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
