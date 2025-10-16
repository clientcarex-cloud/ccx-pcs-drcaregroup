<?php defined('BASEPATH') or exit('No direct script access allowed'); 

?>
<?php init_head(); ?>
<style>
    .swal2-popup { font-size: 1.6rem !important; }
</style>
<style>
  .length-select-wrapper {
    display: flex;
    align-items: center;
  }

  .dt-buttons .btn {
    margin-right: 5px;
    margin-bottom: 0;
  }
</style>
<div id="wrapper">
<div class="content">
<div class="row">

<div class="col-md-12">
<div class="panel_s">
<div class="panel-body">

<h4 class="no-margin">
<?php echo _l($title); ?>
</h4>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
    <a href="<?= admin_url('client/doctor/add_doctor'); ?>" class="btn btn-primary me-auto">
        <i class="fa-regular fa-plus tw-mr-1"></i>
        <?= _l('new_doctor'); ?>
    </a>
</div>
<table id="my-custom-table">
<thead>
<tr>
<th><?php echo _l('id'); ?></th>
<th><?php echo _l('doctor_name'); ?></th>
<th><?php echo _l('email'); ?></th>
<th><?php echo _l('role'); ?></th>
<th><?php echo _l('phonenumber'); ?></th>
<th><?php echo _l('actions'); ?></th> <!-- Added column for action buttons -->
</tr>
</thead>
<tbody>
<?php if (!empty($doctors)) {
    foreach ($doctors as $r) {
?>
<tr>
    <td><?php echo e($r['staffid']); ?></td>
    <td><?php echo e($r['firstname'].' '.$r['lastname']); ?></td>
    <td><?php echo e($r['email']); ?></td>
    <td><?php echo e($r['name']); ?></td>
    <td><?php echo _d($r['phonenumber']); ?></td>
    <td>
        <!-- Edit Button -->
        <a href="<?= admin_url('client/doctor/edit_doctor/' . $r['staffid']); ?>" class="btn btn-primary btn-sm" style="color: white;">Edit</a>

    </td>
</tr>
<?php }
} else { ?>
<tr>
    <td colspan="6"><?php echo _l('no_records_found'); ?></td> <!-- Adjusted colspan for the new "actions" column -->
</tr>
<?php } ?>
</tbody>
</table>

</div>

</div>
</div>
</div>
</div>
<?php init_tail(); ?>

<!-- DataTables core -->
 <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Buttons extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">


<script>
  $(document).ready(function () {
    $('#my-custom-table').DataTable({
      dom:
        
        "<'row'<'col-md-12'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-5'i><'col-md-7'p>>",
      
    });
  });
</script>



</body>
</html>
