<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?php echo $this->lang->line('heading_title_coins_registry'); ?></h2>
        <ol class="breadcrumb">
            <li>
                <a href="<?php echo base_url() ?>home"><?php echo $this->lang->line('heading_home_coins_registry'); ?></a>
            </li>
            
            <li>
                <a href="<?php echo base_url() ?>coins"><?php echo $this->lang->line('heading_subtitle_coins_registry'); ?></a>
            </li>
            
            <li class="active">
                <strong><?php echo $this->lang->line('heading_info_coins_registry'); ?></strong>
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
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5><?php echo $this->lang->line('heading_info_coins_registry'); ?><small></small></h5>
					
				</div>
				<div class="ibox-content">
					<form id="form_monedas" method="post" accept-charset="utf-8" class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $this->lang->line('registry_description_coins'); ?> *</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="description" maxlength="250" id="description">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $this->lang->line('registry_abvr_coins'); ?> *</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="abbreviation" maxlength="5" id="abbreviation">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $this->lang->line('registry_symbols_coins'); ?> </label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="symbol" maxlength="5" id="symbol">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $this->lang->line('registry_decimals_coins'); ?>
							<i class="fa fa-info-circle fa-1.5x" style="color:#337AB7;cursor:pointer;" title="Indique en números la cantidad de decimales a utilizar en operaciones"></i> *
							</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="decimals" maxlength="2" id="decimals">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" ><?php echo $this->lang->line('registry_status_coins'); ?> *</label>
							<div class="col-sm-10">
								<select class="form-control m-b" name="status" id="status">
									<option value="1" selected=""><?php echo $this->lang->line('registry_status1_coins'); ?></option>
									<option value="0"><?php echo $this->lang->line('registry_status2_coins'); ?></option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-4 col-sm-offset-2">
								<button class="btn btn-white" id="volver" type="button"><?php echo $this->lang->line('registry_back_coins'); ?></button>
								<button class="btn btn-primary" id="registrar" type="submit"><?php echo $this->lang->line('registry_save_coins'); ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){

    $('input').on({
        keypress: function () {
            $(this).parent('div').removeClass('has-error');
        }
    });

    $('#volver').click(function () {
        url = '<?php echo base_url() ?>coins/';
        window.location = url;
    });

    $("#registrar").click(function (e) {

        e.preventDefault();  // Para evitar que se envíe por defecto

        if ($('#description').val().trim() === "") {
			swal("Disculpe,", "para continuar debe ingresar la descripción de la moneda");
			$('#description').parent('div').addClass('has-error');
			
        } else if ($('#abbreviation').val().trim() === "") {
			swal("Disculpe,", "para continuar debe ingresar la abreviación de la moneda");
			$('#abbreviation').parent('div').addClass('has-error');
			
        } else if ($('#decimals').val().trim() === "") {
			swal("Disculpe,", "para continuar debe ingresar el número de decimales a tener en cuenta durante las operaciones");
			$('#decimals').parent('div').addClass('has-error');
			
        } else {

            $.post('<?php echo base_url(); ?>CCoins/add', $('#form_monedas').serialize(), function (response) {
				if (response['response'] == 'error') {
                    swal("Disculpe,", "El registro no pudo ser guardado, por favor consulte a su administrador...");
                }else{
					swal({ 
						title: "Registro",
						 text: "Guardado con exito",
						  type: "success" 
						},
					function(){
					  window.location.href = '<?php echo base_url(); ?>coins';
					});
				}
            }, 'json');
            
        }
    });
});

</script>
