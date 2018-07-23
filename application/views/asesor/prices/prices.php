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
            <a href="<?php echo base_url() ?>prices/generate">
				<button class="btn btn-outline btn-primary dim" type="button"><i class="fa fa-plus"></i> <?php echo $this->lang->line('btn_generation'); ?></button>
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
             
    // Validacion para borrar
    $("table#tab_transactions").on('click', 'a.borrar', function (e) {
        e.preventDefault();
        var id = this.getAttribute('id');

        swal({
            title: "Borrar registro",
            text: "¿Está seguro de borrar el registro?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Eliminar",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false,
            closeOnCancel: true
          },
        function(isConfirm){
            if (isConfirm) {
             
                $.post('<?php echo base_url(); ?>transactions/delete/' + id + '', function (response) {

                    if (response[0] == "e") {
                       
                         swal({ 
                           title: "Disculpe,",
                            text: "No se puede eliminar se encuentra asociado a un usuario",
                             type: "warning" 
                           },
                           function(){
                             
                         });
                    }else{
                         swal({ 
                           title: "Eliminar",
                            text: "Registro eliminado con exito",
                             type: "success" 
                           },
                           function(){
                             window.location.href = '<?php echo base_url(); ?>transactions';
                         });
                    }
                });
            } 
        });
    });
    
    
    // Función para validar transacción
    $("table#tab_transactions").on('click', 'a.validar', function (e) {
        e.preventDefault();
        var id = this.getAttribute('id');
        
        var account_id = id.split(';');
        account_id = account_id[1];

        var amount = id.split(';');
        amount = amount[2];

        var tipo = id.split(';');
        tipo = tipo[3];

        var id = id.split(';');
        id = id[0];

        swal({
            title: "Validar transacción",
            text: "¿Está seguro de valdiar la transacción?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Validar",
            cancelButtonText: "Denegar",
            closeOnConfirm: false,
            closeOnCancel: true
          },
        function(isConfirm){
            if (isConfirm) {
             
                $.post('<?php echo base_url(); ?>transactions/validar/', {'id': id, 'account_id': account_id, 'amount': amount, 'tipo': tipo, 'status': 'approved'}, function (response) {

                    if (response['response'] == 'error') {
                       
                         swal({ 
                           title: "Disculpe,",
                            text: "No se pudo validar la transacción, por favor consulte con su administrador",
                             type: "warning" 
                           },
                           function(){
                             
                         });
                    }else{
                         swal({ 
                           title: "Validado",
                            text: "Transacción validada con exito",
                             type: "success" 
                           },
                           function(){
                             window.location.href = '<?php echo base_url(); ?>transactions';
                         });
                    }
                    
                }, 'json');
                
            }else{
				
				$.post('<?php echo base_url(); ?>transactions/validar/', {'id': id, 'account_id': account_id, 'amount': amount, 'tipo': tipo, 'status': 'denied'}, function (response) {

                    if (response['response'] == 'error') {
                       
                         swal({ 
                           title: "Disculpe,",
                            text: "No se pudo negar la transacción, por favor consulte con su administrador",
                             type: "warning" 
                           },
                           function(){
                             
                         });
                    }else{
                         swal({ 
                           title: "Negada",
                            text: "Transacción negada con exito",
                             type: "success" 
                           },
                           function(){
                             window.location.href = '<?php echo base_url(); ?>transactions';
                         });
                    }
                    
                }, 'json');
				
			}
        });
    });
    
});
        
</script>
