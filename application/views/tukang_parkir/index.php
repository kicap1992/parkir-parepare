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
						<h4 class="box-title">Peta Lahan Parkir Parepare</h4>
						<div class="card-content">
							<div id="map" style="width: 100%; height: 500px;"></div>
						</div>
					</div>
				</div>

				<div class="col-xs-12">
					<div class="box-content card white">
						<h4 class="box-title">Infomasi Parkir</h4>
						<div class="card-content">
							<form id="form_tambah">
								<div class="form-group">
									<label for="luas">Nama Tukang Parkir</label>
									<input type="text" id="nama" class="form-control" disabled>
								</div>

								<div class="form-group">
									<label for="luas">NIK Tukang Parkir</label>
									<input type="text" id="nik" class="form-control" disabled>
								</div>

								<div class="form-group">
									<label for="kecamatan_select">Kecamatan</label>
									<input type="text" id="kecamatan" class="form-control" disabled>
								</div>
								<div class="form-group">
									<label for="kelurahan_select">Kelurahan</label>
									<input type="text" id="kelurahan" class="form-control" disabled>
								</div>
								<div class="form-group">
									<label for="luas">Luas Area</label>
									<input type="text" id="luas_input" class="form-control" disabled>
								</div>

								<div class="form-group">
									<label for="alamat">Alamat Tempat Parkir</label>
									<textarea name="alamat" id="alamat" cols="30" rows="10" class="form-control"style="resize:none" disabled></textarea>
								</div>

								<div class="form-group">
									<label for="luas">Biaya Parkir Per Motor</label>
									<input type="text" id="biaya_motor" class="form-control" disabled>
								</div>

								<div class="form-group">
									<label for="luas">Biaya Parkir Per Mobil</label>
									<input type="text" id="biaya_mobil" class="form-control" disabled>
								</div>

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
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD7B9RynI4hQM_Y4BG9GYxsTLWwYkGASRo&libraries=drawing,places,geometry"></script>
	<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
	<!-- <script src="<?= base_url() ?>assets/cluster.js"></script> -->
	<script>
		// import { MarkerClusterer } from "https://cdn.skypack.dev/@googlemaps/markerclusterer@2.0.3";
		var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 12,
			center: {
				lat: -4.012433155366426,
				lng: 119.62193318059713
			},
		});

		var marker_parkir;
		var markers = [];
		var polygon_parkir;
		var polygons = [];
		var infowindow

		function get_kabupaten() {
			$.ajax({
				url: "<?php echo base_url('api/kabupaten_maps') ?>",
				type: "GET",
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
					// console.log(data.area_parkir.length)
					let map_data = data.data;
					// console.log(map_data[0])
					var bound = new google.maps.LatLngBounds();
					for (let i = 0; i < map_data.length; i++) {
						let map_1 = map_data[i].kordinat;
						let polygon = [];

						for (let i = 0; i < map_1.length; i++) {
							// map_1[i].lng value return like 119.62361145
							// map_1[i].lat value return like -4.00000000
							// push value to polygon array
							polygon.push({
								lat: parseFloat(map_1[i].lat),
								lng: parseFloat(map_1[i].lng)
							});
						}

						let show_polygon = new google.maps.Polygon({
							paths: polygon,
							strokeColor: '#FF0000',
							strokeOpacity: 0.8,
							strokeWeight: 0.8,
							// fillColor: '#FF0000',
							fillOpacity: 0.1
						});

						show_polygon.setMap(map);

						for (var j = 0; j < polygon.length; j++) {
							bound.extend(polygon[j]);
						}


					}

					map.fitBounds(bound);





				},
				error: function(jqXHR, textStatus, errorThrown) {
					$.unblockUI();
					alert('Error get data from ajax');
				}
			});
		}

		get_kabupaten();

		async function get_data_tukang_parkir() {
			try {
				const data = await $.ajax({
					url: "<?php echo base_url('api/data_tukang_parkir') ?>",
					type: "GET",
					dataType: "JSON",
					async: false,
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
					}
				}).responseJSON;
				// console.log(data);
				$.unblockUI();

				let detail = data.data;

				// add data to detail
				$('#nama').val(detail.nama);
				$('#nik').val(detail.nik);
				$('#kecamatan').val(data.kecamatan);
				$('#kelurahan').val(data.kelurahan);
				$('#luas_input').val(detail.luas + " m2");
				$('#alamat').val(detail.alamat);
				$('#biaya_motor').val("Rp. " + detail.biaya_motor);
				$('#biaya_mobil').val("Rp. " + detail.biaya_mobil);

				marker_parkir = JSON.parse(detail.center);
				polygon_parkir = JSON.parse(detail.kordinat);
				console.log(polygon_parkir);
				console.log(marker_parkir);
				
				marker = new google.maps.Marker({
					position: marker_parkir,
					map: map,
					title: 'Lahan Parkir'
				});
				markers.push(marker);

				infowindow = new google.maps.InfoWindow({
							content: "Alamat : " + detail.alamat + "<br>Luas : " + detail.luas + " m2<br>Biaya Parkir Motor : " + detail.biaya_motor + "<br>Biaya Parkir Mobil : " + detail.biaya_mobil 
						});
				
				marker.addListener("click", () => {

					infowindow.open(map, marker);
				});

				marker.setMap(map);
			} catch (error) {
				alert("Gagal mengambil data tukang parkir");
			}
		}
		get_data_tukang_parkir();

		google.maps.event.addListener(map, 'zoom_changed', function() {
			let zoomLevel = map.getZoom();
			console.log(zoomLevel);
			if(zoomLevel > 15){
				// clear all marker on map
				markers.forEach(function(marker) {
					marker.setMap(null);
				});
				markers = [];
				var polygon = new google.maps.Polygon({
					paths: polygon_parkir,
					strokeColor: '#FF0000',
					strokeOpacity: 0.8,
					strokeWeight: 2,
					fillColor: '#FF0000',
					fillOpacity: 0.35
				});

				polygons.push(polygon);

				polygon.addListener("click", () => {

					infowindow.open(map, polygon);
				});
				polygon.setMap(map);
			}else{
				// clear all polygon on map
				polygons.forEach(function(polygon) {
					polygon.setMap(null);
				});
				polygons = [];
				var marker = new google.maps.Marker({
					position: marker_parkir,
					map: map,
					title: 'Lahan Parkir'
				});
				markers.push(marker);
				marker.addListener("click", () => {

					infowindow.open(map, marker);
				});
			}
		});
	</script>
</body>

</html>