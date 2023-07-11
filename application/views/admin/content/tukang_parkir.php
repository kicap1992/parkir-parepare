<!DOCTYPE html>
<html lang="en">

<head>
	<?php $this->load->view('admin/header'); ?>
	<link rel="stylesheet" href="<?= base_url() ?>assets/fonts/material-design-iconic-font/css/material-design-iconic-font.min.css">
</head>

<body>

	<?php $this->load->view('admin/sidebar'); ?>


	<div id="wrapper">
		<div class="main-content">
			<div class="row small-spacing">
				

				<div class="col-xs-12">
					<div class="box-content card white">
						<h4 class="box-title">List Area Parkir</h4>
						<div class="card-content">
							<div style="overflow-x: auto;">
								<table id="table_tukang_parkir" class="table table-striped table-bordered display" style="width:100%">
									<thead>
										<tr>
											<th>No</th>
											<th>NIK</th>
											<th>Nama</th>
											<th>Kecamatan</th>
											<th>Kelurahan</th>
											<th>Alamat Parkir</th>
											
										</tr>
									</thead>
									<tbody>

									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

			</div>
			<?php $this->load->view('admin/footer'); ?>
		</div>
		<!-- /.main-content -->
	</div>
	<?php $this->load->view('admin/scripts'); ?>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD7B9RynI4hQM_Y4BG9GYxsTLWwYkGASRo&libraries=drawing,places,geometry"></script>
	<script>
		

		function datatables() {
			table = $('#table_tukang_parkir').DataTable({
				// "searching": false,
				"lengthMenu": [
					[5, 10, 15, -1],
					[5, 10, 15, "All"]
				],
				"pageLength": 10,
				"ordering": true,
				"processing": true,
				"serverSide": true,
				// "order": [[ 4, 'desc' ]], 

				"ajax": {
					"url": "<?php echo base_url('home/tukang_parkir'); ?>",
					"type": "POST",
					data: {
						proses: 'table_tukang_parkir'
					},

				},

				"columnDefs": [{
					"targets": [0,3,4,5],
					"orderable": false,
				}, ],
			});
		}
		datatables()

	</script>
</body>

</html>