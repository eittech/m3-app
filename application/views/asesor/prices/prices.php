<style>
.fa-2x {
    font-size: 1.2em !important;
}

.a-actions {
	padding-top: 8px !important;
	padding-right: 8px !important;
	padding-bottom: 8px !important;
	padding-left: 8px !important;
}

.select2-container {
	z-index: 99999;
}
</style>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?php echo $this->lang->line('heading_title_prices'); ?></h2>
        <ol class="breadcrumb">
            <li>
                <a href="<?php echo base_url() ?>home"><?php echo $this->lang->line('heading_home_prices'); ?></a>
            </li>
            <li class="active">
                <strong><?php echo $this->lang->line('heading_subtitle_prices'); ?></strong>
            </li>
        </ol>
    </div>
</div>

<!-- Campos ocultos que almacenan los nombres del menú y el submenú de la vista actual -->
<input type="hidden" id="ident" value="<?php echo $ident; ?>">
<input type="hidden" id="ident_sub" value="<?php echo $ident_sub; ?>">

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
			<!-- Botón de generación de listado -->
            <a href="#">
				<button class="btn btn-outline btn-primary dim" type="button" id="show_categories1"><i class="fa fa-plus"></i> <?php echo $this->lang->line('btn_generation'); ?></button>
            </a>
            <!-- Botón de actualización de precios -->
            <a href="#">
				<button class="btn btn-outline btn-primary dim" type="button" id="show_categories2"><i class="fa fa-refresh"></i> <?php echo $this->lang->line('btn_update'); ?></button>
            </a>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?php echo $this->lang->line('list_title_prices'); ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="tab_transactions" class="table table-striped table-bordered dt-responsive table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Id Product</th>
                                    <th><?php echo $this->lang->line('list_parent_category_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_category_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_reference_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_name_product_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_id_combination_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_combination_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_price_min_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_fixed_costs_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_variable_costs_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_price_cost_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_price_wholesale_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_price_retail_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_edit_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_view_prices'); ?></th>
                                    <th><?php echo $this->lang->line('list_download_prices'); ?></th>
                                </tr>
                            </thead>
                            <!--<tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($listar as $producto) { ?>
                                    <tr style="text-align: center">
                                        <td>
                                            <?php echo $producto->id_product; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->category_name_parent; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->category_name; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->reference; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->product_name; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->id_product_attribute; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->attribute_name; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->price_min; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->costs_fixed; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->costs_variable; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->price_cost; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->price_wholesale; ?>
                                        </td>
                                        <td>
                                            <?php echo $producto->price_retail; ?>
                                        </td>
                                        <td>
											<a target="_blank" href="<?php echo base_url(); ?>products/catalogue/<?php echo $producto->id_product; ?>"><i class="fa fa-search fa-2x"></i></a>
                                        </td>
                                        <td style='text-align: center'>
											<?php if($this->session->userdata('logged_in')['profile_id'] == 1){ ?>
												<a href="<?php echo base_url() ?>prices/edit/<?= $producto->id_product; ?>" title="<?php echo $this->lang->line('list_edit_prices'); ?>"><i class="fa fa-edit fa-2x"></i></a>
                                            <?php }else{ ?>
												<a ><i class="fa fa-ban fa-2x" style='color:#D33333;'></i></a>
                                            <?php } ?>
                                        </td>
                                        <td style='text-align: center'>
											<a target="_blank" href="<?php echo base_url(); ?>products/catalogue/<?php echo $producto->id_product; ?>"><i class="fa fa-download fa-2x"></i></a>
                                        </td>
                                    </tr>
                                    <?php $i++ ?>
                                <?php } ?>
                            </tbody>-->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de selección de categorías -->
<div class="modal fade" id="modal_categories">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h5 class="modal-title">Seleccionar categoría</h5>
            </div>
            <div class="modal-body" >
                <form id="modal_pass" method="post" accept-charset="utf-8" class="form-horizontal">
					<div class="form-group">
						<div class="col-lg-12">
							<div class="form-group">
								<label>Categoría</label>
								<select class="form-control" style="width:100%;" id="categoria">
									<option value="0">Seleccione</option>
									<?php foreach($categories as $categorie){ ?>
									<option value="<?php echo $categorie->id_category?>"><?php echo $categorie->name?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
                </form>
            </div>
            <div class="modal-footer" >
                <button class="btn btn-primary" type="button" id="save_list">
                    Generar
                </button>
                <button class="btn btn-primary" type="button" id="update_list">
                    Actualizar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Fin de modal de selección de categorías -->


 <!-- Page-Level Scripts -->
<script>
$(document).ready(function(){
     $('#tab_transactions').DataTable({
        "paging": true,
        "lengthChange": true,
        "autoWidth": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "iDisplayLength": 50,
        "iDisplayStart": 0,
        "sPaginationType": "full_numbers",
        "aLengthMenu": [10, 50, 100, 150],
        // Nuevo
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
			"method":"POST",
			"url": "<?= base_url() ?>prices_json"
		},
		"columnDefs": [
			{
				//~ "target": [0, 3, 4],
				"orderable":false,
			}
		],
		//Nuevo
        "oLanguage": {"sUrl": "<?= assets_url() ?>js/es.txt"},
        "aoColumns": [
            {"sClass": "registro center", "sWidth": "5%"},
            {"sClass": "registro center", "sWidth": "10%"},
            {"sClass": "registro center", "sWidth": "10%"},
            {"sClass": "registro center", "sWidth": "10%"},
            {"sClass": "registro center", "sWidth": "10%"},
            {"sClass": "none", "sWidth": "30%"},
            {"sClass": "registro center", "sWidth": "10%"},
            {"sClass": "none", "sWidth": "30%"},
            {"sClass": "none", "sWidth": "30%"},
            {"sClass": "none", "sWidth": "30%"},
            {"sClass": "registro center", "sWidth": "10%", "bSortable": false},
            {"sClass": "none", "sWidth": "30%"},
            {"sClass": "none", "sWidth": "30%"},
            {"sWidth": "3%", "bSortable": false, "sClass": "center sorting_false", "bSearchable": false},
            {"sWidth": "3%", "bSortable": false, "sClass": "center sorting_false", "bSearchable": false},
            {"sWidth": "3%", "bSortable": false, "sClass": "center sorting_false", "bSearchable": false}
        ]
    });
    
    // Activar la modal (en caso de guardado de lista)
    $("#show_categories1").click(function (e) {
		
		e.preventDefault();  // Para evitar que se envíe por defecto
		$("#modal_categories").modal('show');
		//~ var id = this.getAttribute('id');
		$("#save_list").css('display', 'block');
		$("#update_list").css('display', 'none');

	});
    
    // Activar la modal (en caso de actualización de precios)
    $("#show_categories2").click(function (e) {
		
		e.preventDefault();  // Para evitar que se envíe por defecto
		$("#modal_categories").modal('show');
		//~ var id = this.getAttribute('id');
		$("#save_list").css('display', 'none');
		$("#update_list").css('display', 'block');

	});
    
    
    // Función para guardar el listado de precios generado
    $("#save_list").on('click', function (e) {
        e.preventDefault();
        var id_category = $("#categoria").val();
        
        if (id_category == 0) {
			
			swal("Disculpe", "No ha seleccionado ninguna categoría de la lista");
			
		}else{

			swal({
				title: "Guardar lista",
				text: "¿Está seguro de guardar el listado actual?",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Guardar",
				cancelButtonText: "Cancelar",
				closeOnConfirm: false,
				closeOnCancel: true
			  },
			function(isConfirm){
				if (isConfirm) {
				 
					$.post('<?php echo base_url(); ?>prices/save/', {'id_category': id_category}, function (response) {

						if (response['response'] == 'error') {
						   
							 swal({ 
							   title: "Disculpe,",
								text: "Ocurrieron errores en el guardado, por favor consulte con su administrador",
								 type: "warning" 
							   },
							   function(){
								 
							 });
						}else{
							 swal({ 
							   title: "Guardado",
								text: response['response'],
								 type: "success" 
							   },
							   function(){
								 window.location.href = '<?php echo base_url(); ?>prices';
							 });
						}
						
					}, 'json');
					
				}
				
			});
		
		}
        
    });
    
    
    // Función para validar transacción
    $("#update_list").on('click', function (e) {
        e.preventDefault();
        var id_category = $("#categoria").val();
        
        if (id_category == 0) {
			
			swal("Disculpe", "No ha seleccionado ninguna categoría de la lista");
			
		}else{

			swal({
				title: "Actualizar precios",
				text: "¿Está seguro de actualizar los precios?",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Actualizar",
				cancelButtonText: "Cancelar",
				closeOnConfirm: false,
				closeOnCancel: true
			  },
			function(isConfirm){
				if (isConfirm) {
				 
					$.post('<?php echo base_url(); ?>prices/update/', {'id_category': id_category}, function (response) {

						if (response['response'] == 'error') {
						   
							 swal({ 
							   title: "Disculpe,",
								text: "Ocurrieron errores en la actualziación, por favor consulte con su administrador",
								 type: "warning" 
							   },
							   function(){
								 
							 });
						}else{
							 swal({ 
							   title: "Actualizado",
								text: response['response'],
								 type: "success" 
							   },
							   function(){
								 window.location.href = '<?php echo base_url(); ?>prices';
							 });
						}
						
					}, 'json');
					
				}
				
			});
		
		}
        
    });
    
});
        
</script>
