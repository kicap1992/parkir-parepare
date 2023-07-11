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
                        <h4 class="box-title">List Area Parkir</h4>
                        <div class="card-content">
                            <div style="overflow-x: auto;">
                                <table id="table_kritik" class="table table-striped table-bordered display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Nama</th>
                                            <th>Aksi</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->load->view('tukang_parkir/footer'); ?>
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
                        <label for="alamat">Waktu</label>
                        <input type="text" name="waktu" id="waktu" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Kritik Dan Komen</label>
                        <textarea name="kritik" id="kritik" cols="30" rows="10" class="form-control"  style="resize:none" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm waves-effect waves-light" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="send_kritik()">Send</button>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('tukang_parkir/scripts'); ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD7B9RynI4hQM_Y4BG9GYxsTLWwYkGASRo&libraries=drawing,places,geometry"></script>
    <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
    <!-- <script src="<?= base_url() ?>assets/cluster.js"></script> -->
    <script>
        function datatables() {
            table = $('#table_kritik').DataTable({
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
                    "url": "<?php echo base_url('tukang_parkir/kritik'); ?>",
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

        function lihat_kritik(id_kritik){
            console.log(id_kritik)
            try {
                $.ajax({
					url: "<?php echo base_url('api/kritik') ?>?id_kritik="+id_kritik,
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
                        console.log(data)
						$.unblockUI();
						// show modal
                        $('#boostrapModal-1').modal('show');
                        $('#waktu').val(data.data.created_at)
                        $('#nama').val(data.data.nama)
                        $('#kritik').val(data.data.kritik)
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$.unblockUI();
						alert('Error Loading Data');
					}
				});
            } catch (error) {
                alert(error)
            }
        }
    </script>
</body>

</html>