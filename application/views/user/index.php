<!DOCTYPE html>
<html lang="en">

<head>
	<?php $this->load->view('user/header'); ?>
</head>

<body>

	<?php $this->load->view('user/sidebar'); ?>


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
						<h4 class="box-title">List Area Parkir</h4>
						<div class="card-content">
							<div style="overflow-x: auto;">
								<table id="table_area_parkir" class="table table-striped table-bordered display" style="width:100%">
									<thead>
										<tr>
											<th>No</th>
											<th>Alamat</th>
											<th>Kecamatan</th>
											<th>Kelurahan</th>
											<th>Luas Area</th>
											<th>Biaya Parkir Motor</th>
											<th>Biaya Parkir Mobil</th>
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
			<?php $this->load->view('user/footer'); ?>
		</div>
		<!-- /.main-content -->
	</div>

	<div class="modal fade" id="boostrapModal-1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Kritik Dan Komen</h4>
				</div>
				<div class="modal-body">
				<div class="form-group">
						<label for="alamat">Nama</label>
						<input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan Nama">
					</div>
					<div class="form-group">
						<label for="alamat">Kritik Dan Komen</label>
						<textarea name="kritik" id="kritik" cols="30" rows="10" class="form-control" placeholder="Masukkan Kritik Dan Komen" style="resize:none"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-sm waves-effect waves-light" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="send_kritik()">Send</button>
				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view('user/scripts'); ?>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD7B9RynI4hQM_Y4BG9GYxsTLWwYkGASRo&libraries=drawing,places,geometry"></script>
	<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
	<!-- <script src="<?= base_url() ?>assets/cluster.js"></script> -->
	<script>
		// import { MarkerClusterer } from "https://cdn.skypack.dev/@googlemaps/markerclusterer@2.0.3";
		var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 12,
			center: {
				lat: -5.041461,
				lng: 121.628891
			},
		});
		var markers = [];
		var markers1 = [];
		var polygon_parkir = [];
		var polygon_parkir1 = [];
		var cluster;
		var infowindow_data = [];

		var id_parkir_global;


		function kritik(id_parkir) {
			console.log(id_parkir)
			id_parkir_global = id_parkir;
			// open the modal
			
		
			$('#boostrapModal-1').modal('show');
		}


		function send_kritik(){
			console.log(id_parkir_global)
			const kritik = $('#kritik').val();
			const nama = $('#nama').val();

			if(nama == ''){
				alert('Nama Tidak Boleh Kosong')
				return false;
			}

			if(kritik == ''){
				alert('Kritik dan Komen Tidak Boleh Kosong')
				return false;
			}

			// create a yes/no confirm box
			if (confirm('Apakah Anda Yakin Ingin Mengirim Kritik Dan Komen?')) {
				$.ajax({
					url: "<?php echo base_url('api/kritik') ?>",
					type: "POST",
					dataType: "JSON",
					data: {
						id_parkir: id_parkir_global,
						nama: nama,
						kritik: kritik
					},
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
						alert(data.message)
						// close the modal
						$('#boostrapModal-1').modal('hide');
						
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$.unblockUI();
						alert('Error Adding / Update Data');
					}
				});
			}

		}



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


		async function get_marker_parkir() {
			try {
				const data = await $.ajax({
					url: "<?php echo base_url('api/area_parkir') ?>",
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
				$.unblockUI();
				// console.log(data.area_parkir.length)
				let area_parkir = data.data;

				// console.log(area_parkir.length)
				if (area_parkir.length > 0) {


					for (let i = 0; i < area_parkir.length; i++) {
						let area_parkir_1 = area_parkir[i];
						// console.log(JSON.parse(area_parkir_1.center))
						let coordinate = JSON.parse(area_parkir_1.center);
						let marker = new google.maps.Marker({
							position: {
								lat: parseFloat(coordinate.lat),
								lng: parseFloat(coordinate.lng)
							},
							map: map,
							title: area_parkir_1.alamat,

						});

						markers1.push(marker);
						var biaya_motor = area_parkir_1.biaya_motor;
						var biaya_mobil = area_parkir_1.biaya_mobil;
						let infowindow = new google.maps.InfoWindow({
							content: "Alamat : " + area_parkir_1.alamat + "<br>Luas : " + area_parkir_1.luas + " m2<br>Biaya Parkir Motor : " + biaya_motor + "<br>Biaya Parkir Mobil : " + biaya_mobil + "<br><br><center><a onclick='kritik(" + area_parkir_1.id_parkir + ")' class='btn btn-primary btn-xs'>Kritik Dan Komen</a></center>"
						});
						infowindow_data.push(infowindow);
						marker.addListener('click', function() {
							// console.log(area_parkir_1)






							infowindow.open(map, marker);
						});
						markers.push(marker);
					}

					// marker cluster
					cluster = new markerClusterer.MarkerClusterer({
						map,
						markers
					});

				}
			} catch (error) {
				$.unblockUI();
				console.log(error)
				alert('Error get data from ajax');
			}

		}

		get_marker_parkir();

		async function get_polygon_parkir() {
			try {
				const data = await $.ajax({
					url: "<?php echo base_url('api/area_parkir') ?>",
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
				$.unblockUI();
				// console.log(data)
				let area_parkir = data.data;

				// // console.log(area_parkir)
				if (area_parkir.length > 0) {
					if (polygon_parkir.length == 0) {
						for (let i = 0; i < area_parkir.length; i++) {
							let the_polygon = JSON.parse(area_parkir[i].kordinat);
							// console.log(the_polygon)

							let polygon = [];
							for (let i = 0; i < the_polygon.length; i++) {
								// map_1[i].lng value return like 119.62361145
								// map_1[i].lat value return like -4.00000000
								// push value to polygon array
								polygon.push({
									lat: parseFloat(the_polygon[i].lat),
									lng: parseFloat(the_polygon[i].lng)
								});
							}
							polygon_parkir.push(polygon);
							let show_polygon = new google.maps.Polygon({
								paths: polygon,
								strokeColor: '#FF0000',
								strokeOpacity: 0.8,
								strokeWeight: 0.8,
								// fillColor: '#FF0000',
								fillOpacity: 0.1
							});

							polygon_parkir1.push(show_polygon);
							show_polygon.setMap(map);


							// google.





						}
					}


				}



			} catch (error) {
				$.unblockUI();
				alert('Error get data from ajax');
			}

		}

		get_polygon_parkir();

		google.maps.event.addListener(map, 'zoom_changed', function() {
			var zoomLevel = map.getZoom();
			console.log(zoomLevel);
			if (zoomLevel >= 16) {
				// console.log('zoom in')
				console.log(markers.length)
				for (let i = 0; i < markers.length; i++) {
					markers[i].setMap(null);
				}
				markers = [];
				// clear all marker
				// get_polygon_parkir();
				// for (let i = 0; i < polygon_parkir.length; i++) {
				// 	polygon_parkir1[i].setMap(map);
				// }
				// console.log(polygon_parkir)
				for (let i = 0; i < polygon_parkir.length; i++) {
					let show_polygon = new google.maps.Polygon({
						paths: polygon_parkir[i],
						strokeColor: '#FF0000',
						strokeOpacity: 0.8,
						strokeWeight: 0.8,
						// fillColor: '#FF0000',
						fillOpacity: 0.1
					});

					show_polygon.setMap(map);
					polygon_parkir1.push(show_polygon);

					google.maps.event.addListener(show_polygon, 'click', function(event) {
						// console.log("click polygon")
						// console.log(event.latLng.lng())
						console.log(infowindow_data)
						let infowindow = infowindow_data[i];
						// console.log(infowindow)

						// get the lat and lng of the point
						var latLng = event.latLng;
						// console.log(latLng)
						infowindow.setPosition(latLng);
						infowindow.open(map);
						// infowindow_data.push(infowindow);

						// infowindow.open(map, show_polygon);
					});


				}

				// remove cluster
				cluster.clearMarkers();


			}
			if (zoomLevel < 16) {
				console.log('zoom out')
				console.log(markers.length)
				console.log(markers1.length)
				if (polygon_parkir1.length > 0) {
					// clear all polygon
					for (let i = 0; i < polygon_parkir1.length; i++) {
						polygon_parkir1[i].setMap(null);
					}
				}

				markers = markers1;
				for (let i = 0; i < markers.length; i++) {
					markers[i].setMap(map);
				}
				// add cluster
				cluster.addMarkers(markers);

			}
		});


		function datatables() {
			table = $('#table_area_parkir').DataTable({
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
					"url": "<?php echo base_url('user/parkir_index'); ?>",
					"type": "POST",
					data: {
						proses: 'table_area_parkir'
					},

				},

				"columnDefs": [{
					"targets": [0],
					"orderable": false,
				}, ],
			});
		}
		datatables()
	</script>
</body>

</html>