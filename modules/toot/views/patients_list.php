<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .swal2-popup { font-size: 1.6rem !important; }
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin" style="display: inline-block"><?= _l('patient_list'); ?></h4>
						<hr class="hr-panel-heading" />
						<div class="clearfix"></div>

                      <?php echo render_datatable([
				_l('#'),
				_l('patient_name'),
				_l('mobile'),
				_l('city'),
				_l('state'),
			], 'patients'); ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
    initDataTable('.table-patients', '<?= admin_url('toot/get_patient_list'); ?>', [1], [1]);
});
</script>

</body>
</html>