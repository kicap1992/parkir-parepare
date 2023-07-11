<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>Sistem Parkir Parepare - Halaman Login</title>
	<link rel="stylesheet" href="<?=base_url()?>assets/styles/style.min.css">

	<!-- Waves Effect -->
	<link rel="stylesheet" href="<?=base_url()?>assets/plugin/waves/waves.min.css">

</head>

<body>

<div id="single-wrapper">
	<form onsubmit="login(event)" class="frm-single">
		<div class="inside">
			<div class="title"><strong>Sistem Parkir </strong>Parepare</div>
			<!-- /.title -->
			<div class="frm-title">Form Login Tukang Parkir</div>
			<!-- /.frm-title -->
			<div class="frm-input"><input type="text" placeholder="Username" id="username"  name="username" class="frm-inp" required><i class="fa fa-user frm-ico"></i></div>
			<!-- /.frm-input -->
			<div class="frm-input"><input type="password" placeholder="Password" class="frm-inp" id="password" name="password" required><i class="fa fa-lock frm-ico"></i></div>
			
			<button type="submit" class="frm-submit">Login<i class="fa fa-arrow-circle-right"></i></button>

			<a href="<?=base_url()?>user" class="a-link">Kembali Ke Halaman Utama</a>
			
			<!-- <a href="page-register.html" class="a-link"><i class="fa fa-key"></i>New to NinjaAdmin? Register.</a> -->
			<div class="frm-footer">Kicap Karan © 2023.</div>
			<!-- /.footer -->
		</div>
		<!-- .inside -->
	</form>
	<!-- /.frm-single -->
</div><!--/#single-wrapper -->

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="assets/script/html5shiv.min.js"></script>
		<script src="assets/script/respond.min.js"></script>
	<![endif]-->
	<!-- 
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="<?=base_url()?>assets/scripts/jquery.min.js"></script>
	<script src="<?=base_url()?>assets/scripts/modernizr.min.js"></script>
	<script src="<?=base_url()?>assets/plugin/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?=base_url()?>assets/plugin/nprogress/nprogress.js"></script>
	<script src="<?=base_url()?>assets/plugin/waves/waves.min.js"></script>
	<script src="<?=base_url()?>assets/block/jquery.blockUI.js"></script>
	<script src="<?=base_url()?>assets/scripts/main.min.js"></script>
	<script>
		function login(e){
			e.preventDefault();
			var username = $('#username').val();
			var password = $('#password').val();
			
			// ajax
			$.ajax({
				url: '<?= base_url('api/login_tukang_parkir') ?>?username='+username+'&password='+password,
				type: 'GET',
				dataType: 'JSON',
				
				beforeSend: function(){
					// loading
					$.blockUI({
						message: 'Login',
						css: {
							border: 'none',
							padding: '15px',
							backgroundColor: '#000',
							'-webkit-border-radius': '10px',
							'-moz-border-radius': '10px',
							opacity: .5,
							color: '#fff'
						}
					});
				},
				success: function(data){
					console.log(data);
					$.unblockUI();
					window.location.href = '<?= base_url('tukang_parkir') ?>';
				},
				error: function( XMLHttpRequest, textStatus, errorThrown ){
					console.log( errorThrown );
					console.log( textStatus );
					console.log( XMLHttpRequest );
					$.unblockUI();
					alert('Username atau Password Salah');
				}
			});
		}
	</script>
</body>
</html>