<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<title>aMule - Control Panel - Login</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="custom.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="i18n.js"></script>
	<script language="JavaScript" type="text/javascript">
		function login_init() {
			try { breakout_of_frame(); } catch (e) {}
			var p = document.querySelector('input[name="pass"]');
			if (p) p.focus();
		}
	</script>
</head>

<body class="amule-bs5" style="padding-top:0;" onload="login_init();">

	<div class="container" style="max-width:460px;">
		<div class="text-center" style="padding-top:48px;">
			<img class="img-fluid" style="max-width:220px;border-radius:24px;" src="logo-brax.png" alt="aMule">
			<h1 style="color:#4db6ac;margin-top:20px;">aMule Web Interface</h1>
			<p class="text-secondary">Welcome!<br>Please login to access the complete interface!</p>
		</div>

		<div class="card">
			<div class="card-body">
				<form role="form" name="login" action="main.php" method="post">
					<div class="input-group">
						<span class="input-group-text"><i class="bi bi-key-fill"></i></span>
						<input name="pass" type="password" class="form-control" placeholder="Password" required autofocus>
						<button class="btn btn-primary" type="submit" name="submit" value="Submit">
							<i class="bi bi-box-arrow-in-right"></i> Login
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</body>
</html>
