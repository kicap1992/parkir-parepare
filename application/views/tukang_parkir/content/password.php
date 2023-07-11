<!DOCTYPE html>
<html lang="en">

<head>
	<?php $this->load->view('tukang_parkir/header'); ?>
</head>

<body>

	<?php $this->load->view('tukang_parkir/sidebar'); ?>


	<div id="wrapper">
		<div class="main-content">
			<div class="row small-spacing">
				

				<div class="col-xs-12">
					<div class="box-content card white">
						<h4 class="box-title">Ganti Password</h4>
						<div class="card-content">
							<form id="form_tambah">
								<div class="form-group">
									<label for="luas">Passowrd Lama</label>
									<input type="password" id="password_lama" class="form-control" >
								</div>

								<div class="form-group">
									<label for="luas">Password Baru</label>
									<input type="password" id="password_baru" class="form-control" >
								</div>

								<div class="form-group">
									<label for="luas">Konfirmasi Password Baru</label>
									<input type="password" id="konfirmasi_password_baru" class="form-control" >
								</div>

                                <center><button type="button" class="btn btn-primary btn-sm waves-effect waves-light text" onclick="ganti_password()">Ganti Password</button></center>
							</form>
							<br>
							<div id="div_map" style="display: none;">

								<div id="map" style="width: 100%; height: 500px;"></div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<?php $this->load->view('tukang_parkir/footer'); ?>
		</div>
		<!-- /.main-content -->
	</div>
	<?php $this->load->view('tukang_parkir/scripts'); ?>
	<script>
        function ganti_password(){
            var password_lama = $('#password_lama').val();
            var password_baru = $('#password_baru').val();
            var konfirmasi_password_baru = $('#konfirmasi_password_baru').val();

            if(password_baru.length < 8){
                alert('Password Baru Minimal 8 Karakter');
                return false;
            }

            if(password_baru != konfirmasi_password_baru){
                alert('Password Baru dan Konfirmasi Password Baru Tidak Sama');
            }else{
                $.ajax({
                    url: "<?php echo base_url('api/ganti_password') ?>",
                    type: "POST",
                    data: {
                        password_lama: password_lama,
                        password_baru: password_baru
                    },
                    dataType: "JSON",
                    beforeSend: function() {
                        $.blockUI({
							message: 'Loading...',
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
                    success: function(data) {
                        $.unblockUI();
                        console.log(data);
                        if(data.status == true){
                            alert('Password Berhasil Diganti');
                            // clear all input
                            $('#password_lama').val('');
                            $('#password_baru').val('');
                            $('#konfirmasi_password_baru').val('');
                        }else{
                            alert('Password Lama Salah');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $.unblockUI();
                        alert('Error');
                    }
                });
            }
        }


	</script>
</body>

</html>