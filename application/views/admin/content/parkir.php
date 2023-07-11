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
						<h4 class="box-title">Form Tambah Area Parkir</h4>
						<div class="card-content">
							<form id="form_tambah">
								<div class="form-group">
									<label for="luas">Nama Tukang Parkir</label>
									<input type="text" id="nama" class="form-control" placeholder="Masukkan Nama Tukang Parkir">
								</div>

								<div class="form-group">
									<label for="luas">NIK Tukang Parkir</label>
									<input type="text" id="nik" class="form-control" placeholder="Masukkan NIK Tukang Parkir" maxlength="16">
								</div>

								<div class="form-group">
									<label for="kecamatan_select">Kecamatan</label>
									<select name="kecamatan_select" id="kecamatan_select" class="form-control" onchange="get_kelurahan(this.value)">

									</select>
								</div>
								<div class="form-group">
									<label for="kelurahan_select">Kelurahan</label>
									<select name="kelurahan_select" id="kelurahan_select" class="form-control" onchange="get_kelurahan_maps(this.value)">
										<option value="" selected disabled>-Pilih Kecamatan Terlebih Dahulu</option>
									</select>
								</div>
								<div class="form-group">
									<label for="luas">Luas Area</label>
									<input type="text" id="luas_input" class="form-control" placeholder="Sila Tanda Area Parkir" disabled>
								</div>

								<div class="form-group">
									<label for="alamat">Alamat Tempat Parkir</label>
									<textarea name="alamat" id="alamat" cols="30" rows="10" class="form-control" placeholder="Masukkan Alamat" style="resize:none"></textarea>
								</div>

								<div class="form-group">
									<label for="luas">Biaya Parkir Per Motor</label>
									<input type="text" id="biaya_motor" class="form-control" placeholder="Masukkan Biaya Parkir Per Motor" maxlength="5">
								</div>

								<div class="form-group">
									<label for="luas">Biaya Parkir Per Mobil</label>
									<input type="text" id="biaya_mobil" class="form-control" placeholder="Masukkan Biaya Parkir Per Mobil" maxlength="5">
								</div>


								<center><button type="button" class="btn btn-primary btn-sm waves-effect waves-light text" onclick="tambah_parkir()">Tambah Area Parkir</button></center>
							</form>
							<br>
							<div id="div_map" style="display: none;">

								<div id="map" style="width: 100%; height: 500px;"></div>
							</div>
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
											<th>Aksi</th>
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
		const input_array = ['nik', 'biaya_motor', 'biaya_mobil'];
		for (let i = 0; i < input_array.length; i++) {
		const numericInput = document.getElementById(input_array[i]);
			numericInput.addEventListener('input', function(event) {
			const value = event.target.value;
			if (isNaN(value)) {
				event.target.value = '';
			}
		});
		}

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
					"url": "<?php echo base_url('home/parkir'); ?>",
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


		var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 12,
			center: {
				lat: -5.041461,
				lng: 121.628891
			},
			streetViewControl: false,
		});

		var polygon = new google.maps.Polygon();
		var parkir_polygon = new google.maps.Polygon();
		var parkir_polygon_coord = null;
		var center_parkir;
		var kecamatan_id;
		var kelurahan_id;
		var luas_parkirnya;

		// know the zoom level
		google.maps.event.addListener(map, 'zoom_changed', function() {
			var zoomLevel = map.getZoom();
			console.log(zoomLevel);
		});

		// drawing manager set the hand tool as default
		var drawingManager = new google.maps.drawing.DrawingManager({
			drawingMode: google.maps.drawing.OverlayType.POLYGON,
			drawingControl: true,
			drawingControlOptions: {
				position: google.maps.ControlPosition.TOP_CENTER,
				drawingModes: ['polygon']
			},
			polygonOptions: {
				fillColor: '#ffff00',
				fillOpacity: 0.5,
				strokeWeight: 1,
				clickable: false,
				editable: true,
				zIndex: 1
			}
		});

		// get the polygon coordinates



		google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
			if (event.type == google.maps.drawing.OverlayType.POLYGON) {
				var newShape = event.overlay;
				// console.log(newShape);
				var newShape_polygon_coords = (newShape.getPath().getArray());

				setSelection(newShape);
				// polygon onchange event

			}
		});


		var addListenerOnPolygon = function(the_polygon) {
			google.maps.event.addListener(the_polygon, 'click', function(event) {
				// var parkir_polygon_coords_array = [];
				// setSelection(the_polygon);
				console.log("this is the polygon");
				google.maps.event.addListener(the_polygon.getPath(), 'set_at', function() {
					console.log("this is the polygon set_at");
					var polygon_coords = (the_polygon.getPath().getArray());
					// console.log(polygon_coords);

					// change the polygon_coords to [{"lat" : lat, "lng" : lng}, {"lat" : lat, "lng" : lng}, {"lat" : lat, "lng" : lng}]
					var polygon_coords_array = [];
					for (var i = 0; i < polygon_coords.length; i++) {
						var polygon_coords_obj = {
							"lat": polygon_coords[i].lat(),
							"lng": polygon_coords[i].lng()
						};
						polygon_coords_array.push(polygon_coords_obj);
					}
					console.log(polygon_coords_array);

					// get the center of the polygon_coords by bounds
					var bounds = new google.maps.LatLngBounds();
					for (var i = 0; i < polygon_coords.length; i++) {
						bounds.extend(polygon_coords[i]);
					}
					var center = bounds.getCenter();
					// console.log(center);
					// chnage the center to {"lat" : lat, "lng" : lng}
					center_parkir = {
						"lat": center.lat(),
						"lng": center.lng()
					};
					console.log(center_parkir);

					// get luas 
					var luas = google.maps.geometry.spherical.computeArea(polygon_coords);
					// 2 digit after the decimal point
					luas = luas.toFixed(2);
					console.log(luas);
					$("#luas_input").val(luas + " m2");
					luas_parkirnya = luas;





				});

				google.maps.event.addListener(the_polygon.getPath(), 'insert_at', function() {
					console.log("this is the polygon insert_at");
					var polygon_coords = (the_polygon.getPath().getArray());
					// console.log(polygon_coords);

					// change the polygon_coords to [{"lat" : lat, "lng" : lng}, {"lat" : lat, "lng" : lng}, {"lat" : lat, "lng" : lng}]
					var polygon_coords_array = [];
					for (var i = 0; i < polygon_coords.length; i++) {
						var polygon_coords_obj = {
							"lat": polygon_coords[i].lat(),
							"lng": polygon_coords[i].lng()
						};
						polygon_coords_array.push(polygon_coords_obj);
					}
					console.log(polygon_coords_array);

					// get the center of the polygon_coords by bounds
					var bounds = new google.maps.LatLngBounds();
					for (var i = 0; i < polygon_coords.length; i++) {
						bounds.extend(polygon_coords[i]);
					}
					var center = bounds.getCenter();
					// console.log(center);
					// chnage the center to {"lat" : lat, "lng" : lng}
					center_parkir = {
						"lat": center.lat(),
						"lng": center.lng()
					};
					console.log(center_parkir);

					// get luas
					var luas = google.maps.geometry.spherical.computeArea(polygon_coords);
					// 2 digit after the decimal point
					luas = luas.toFixed(2);
					console.log(luas);
					$("#luas_input").val(luas + " m2");
					luas_parkirnya = luas;


				});
			});
		};



		function setSelection(shape) {
			clearSelection();
			var the_polygon = shape;
			parkir_polygon = the_polygon;

			var parkir_polygon_coords = (parkir_polygon.getPath().getArray());
			// var polygon_coords = (polygon.getPath().getArray());

			// if parkir_polygon_coords is outside polygon_coords
			if (google.maps.geometry.poly.containsLocation(parkir_polygon_coords[0], polygon) == false) {
				alert('Area Parkir Tidak Boleh Diluar Area Parkir');
				parkir_polygon.setMap(null);
			} else {
				// change the parkir_polygon_coords to [{"lat" : lat, "lng" : lng}, {"lat" : lat, "lng" : lng}, {"lat" : lat, "lng" : lng}]
				var parkir_polygon_coords_array = [];
				for (var i = 0; i < parkir_polygon_coords.length; i++) {
					var parkir_polygon_coords_obj = {
						"lat": parkir_polygon_coords[i].lat(),
						"lng": parkir_polygon_coords[i].lng()
					};
					parkir_polygon_coords_array.push(parkir_polygon_coords_obj);
				}
				// console.log(parkir_polygon_coords_array);

				parkir_polygon_coord = parkir_polygon_coords_array;
				console.log(parkir_polygon_coord);

				// get the center of the parkir_polygon_coords by bounds
				var bounds = new google.maps.LatLngBounds();
				for (var i = 0; i < parkir_polygon_coords.length; i++) {
					bounds.extend(parkir_polygon_coords[i]);
				}
				var center = bounds.getCenter();
				// chnage the center to {"lat" : lat, "lng" : lng}
				center_parkir = {
					"lat": center.lat(),
					"lng": center.lng()
				};

				console.log(center_parkir);

				// get luas
				var luas = google.maps.geometry.spherical.computeArea(parkir_polygon_coords);
				// 2 digit after the decimal point
				luas = luas.toFixed(2);
				console.log(luas);
				$("#luas_input").val(luas + " m2");
				luas_parkirnya = luas;
				addListenerOnPolygon(parkir_polygon);

			}






		}



		function clearSelection() {
			if (parkir_polygon) {
				parkir_polygon.setMap(null);
				parkir_polygon_coord = null;
				center_parkir = null;

			}
		}






		function get_all_kecamatan() {
			$.ajax({
				url: "<?php echo base_url('api/all_kecamatan') ?>",
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
					let all_kecamatan = data.data;
					// console.log(all_kecamatan);
					$("#kecamatan_select").append('<option value="" selected disabled>Pilih Kecamatan</option>');

					$.each(all_kecamatan, function(index, value) {
						$("#kecamatan_select").append('<option value="' + value.no + '">' + value.kecamatan + '</option>');
					});




				},
				error: function(jqXHR, textStatus, errorThrown) {
					$.unblockUI();
					alert('Error get data from ajax');
				}
			});
		}
		get_all_kecamatan();


		function get_kelurahan(id_kecamatan) {
			kecamatan_id = id_kecamatan;
			$.ajax({
				url: "<?php echo base_url('api/all_kelurahan') ?>?id_kecamatan=" + id_kecamatan,
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
					// console.log(data)
					let data_kelurahan = data.data;

					$("#kelurahan_select").empty();
					$("#kelurahan_select").append('<option value="" selected disabled>Pilih Kelurahan</option>');

					$.each(data_kelurahan, function(index, value) {
						$("#kelurahan_select").append('<option value="' + value.no + '">' + value.kelurahan + '</option>');
					});

					let maps = data.maps[0].kordinat;
					polygon.setMap(null);
					// console.log(maps)

					the_polygon = []

					for (let i = 0; i < maps.length; i++) {
						the_polygon.push({
							lat: parseFloat(maps[i].lat),
							lng: parseFloat(maps[i].lng)
						});
					}

					polygon = new google.maps.Polygon({
						paths: the_polygon,
						strokeColor: '#FF0000',
						strokeOpacity: 0.8,
						strokeWeight: 0.8,
						// fillColor: '#FF0000',
						fillOpacity: 0.1
					});

					polygon.setMap(map);

					var bounds = new google.maps.LatLngBounds();
					for (var i = 0; i < polygon.getPath().getLength(); i++) {
						bounds.extend(polygon.getPath().getAt(i));
					}
					map.fitBounds(bounds);

					// drawing manager 	
					drawingManager.setMap(null);

					$("#div_map").attr('style', 'display:block;');


				},
				error: function(jqXHR, textStatus, errorThrown) {
					$.unblockUI();
					alert('Error get data from ajax');
				}
			});
		}

		function get_kelurahan_maps(id_kelurahan) {
			kelurahan_id = id_kelurahan;
			// console.log(id_kelurahan)
			$.ajax({
				url: "<?php echo base_url('api/kelurahan_maps') ?>?id_kelurahan=" + id_kelurahan,
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
					// console.log(data)

					let maps = data.data[0].kordinat;
					polygon.setMap(null);

					the_polygon = []

					for (let i = 0; i < maps.length; i++) {
						the_polygon.push({
							lat: parseFloat(maps[i].lat),
							lng: parseFloat(maps[i].lng)
						});
					}

					polygon = new google.maps.Polygon({
						paths: the_polygon,
						strokeColor: '#FF0000',
						strokeOpacity: 0.8,
						strokeWeight: 0.8,
						// fillColor: '#FF0000',
						fillOpacity: 0.1,
						// editable: true
					});

					polygon.setMap(map);

					var bounds = new google.maps.LatLngBounds();
					for (var i = 0; i < polygon.getPath().getLength(); i++) {
						bounds.extend(polygon.getPath().getAt(i));
					}

					map.fitBounds(bounds);

					drawingManager.setMap(map);



				},
				error: function(jqXHR, textStatus, errorThrown) {
					$.unblockUI();
					alert('Error get data from ajax');
				}
			});
		}


		function tambah_parkir() {
			if (parkir_polygon_coord == null) {
				alert('Silahkan tanda lokasi area parkir');
				return false;
			}

			const alamat = $("#alamat").val();
			const nik = $("#nik").val();
			const nama = $("#nama").val();
			const biaya_motor = $("#biaya_motor").val();
			const biaya_mobil = $("#biaya_mobil").val();

			if (alamat == '') {
				alert('Silahkan isi alamat parkir');
				return false;
			}

			if (nik == '') {
				alert('Silahkan isi NIK');
				return false;
			}

			if(nik.length != 16){
				alert('NIK harus 16 digit');
				return false;
			}

			if (confirm("Area parkir baru akan ditambahkan ke sistem")) {
				$.ajax({
					url: "<?php echo base_url('api/tambah_parkir') ?>",
					type: "POST",
					dataType: "JSON",
					data: {
						kecamatan_id: kecamatan_id,
						kelurahan_id: kelurahan_id,
						kordinat: JSON.stringify(parkir_polygon_coord),
						center: JSON.stringify(center_parkir),
						luas: luas_parkirnya,
						alamat: alamat,
						nik: nik,
						nama: nama,
						biaya_motor: biaya_motor,
						biaya_mobil: biaya_mobil
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
						console.log(data)

						parkir_polygon_coord = null;
						center_parkir = null;
						luas_parkirnya = null;
						$("#alamat").val('');
						$("#luas_input").val('');

						// parkir_polygon.setMap(null);
						clearSelection();
						$('#table_area_parkir').dataTable().fnDestroy();
						datatables()
						alert('Area parkir berhasil ditambahkan');

					},
					error: function(jqXHR, textStatus, errorThrown) {
						$.unblockUI();
						alert('Error get data from ajax');
						console.log(jqXHR)
						console.log(textStatus)
						console.log(errorThrown)
					}
				});
			} else {
				return false;
			}

		}

		function hapus_area_parkir(id_parkir) {
			// console.log(id_parkir)
			if (confirm("Area parkir akan dihapus dari sistem")) {
				$.ajax({
					url: "<?php echo base_url('api/area_parkir') ?>",
					type: "DELETE",
					dataType: "JSON",
					data: {
						id: id_parkir
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
						console.log(data)

						$('#table_area_parkir').dataTable().fnDestroy();
						datatables()
						alert('Area parkir berhasil dihapus');

					},
					error: function(jqXHR, textStatus, errorThrown) {
						$.unblockUI();
						alert('Error get data from ajax');
						console.log(jqXHR)
						console.log(textStatus)
						console.log(errorThrown)
					}
				});
			} else {
				return false;
			}
		}
	</script>
</body>

</html>