    <!--/#wrapper -->
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="<?=base_url()?>assets/script/html5shiv.min.js"></script>
		<script src="<?=base_url()?>assets/script/respond.min.js"></script>
	<![endif]-->
	<!-- 
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="<?=base_url()?>assets/scripts/jquery.min.js"></script>
	<script src="<?=base_url()?>assets/scripts/modernizr.min.js"></script>
	<script src="<?=base_url()?>assets/plugin/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?=base_url()?>assets/plugin/mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="<?=base_url()?>assets/plugin/nprogress/nprogress.js"></script>
	<script src="<?=base_url()?>assets/plugin/sweet-alert/sweetalert.min.js"></script>
	<script src="<?=base_url()?>assets/plugin/waves/waves.min.js"></script>
	<!-- Full Screen Plugin -->
	<script src="<?=base_url()?>assets/plugin/fullscreen/jquery.fullscreen-min.js"></script>
	<script src="<?=base_url()?>assets/block/jquery.blockUI.js"></script>
	<script src="<?=base_url()?>assets/plugin/datatables/media/js/jquery.dataTables.min.js"></script>
	<script src="<?=base_url()?>assets/plugin/datatables/media/js/dataTables.bootstrap.min.js"></script>

	<script src="<?=base_url()?>assets/scripts/main.min.js"></script>
	<script src="<?=base_url()?>assets/color-switcher/color-switcher.min.js"></script>

	<script>
		function logout(){
			if(confirm("Apakah anda yakin ingin keluar?")){
				window.location.href = "<?=base_url()?>home/logout";
			}
			// window.location.href = "<?=base_url()?>home/logout";
		}
	</script>