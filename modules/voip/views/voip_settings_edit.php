<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin">
              <?= _l('voip_settings'); ?>
              
            </h4>
            <hr class="hr-panel-heading" />
            <form action="<?= admin_url('voip/edit/'.$voip['id']); ?>" method="POST" autocomplete="off" novalidate>
		  
		  <div class="modal-body">
			<?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
			
			<!-- Hidden ID for update -->
			<input type="hidden" name="id" value="<?= html_escape($voip['id']); ?>">

			<!-- Staff Dropdown -->
			<div class="form-group">
			  <?= render_select('staffid', $staff, ['staffid', ['firstname', 'lastname']], 'staff', $voip['staffid'], ['required' => true]); ?>
			</div>

			<!-- Username -->
			<?= render_input('username', 'username', html_escape($voip['username']), 'text', ['required' => true]); ?>

			<!-- Password (leave blank if unchanged) -->
			<?= render_input('password', 'password', $voip['password'], 'password', ['autocomplete' => 'new-password']); ?>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-success">Update</button>
		  </div>
		</form>


            

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>

<script>
  // Reset forms when modals close
  $('#voipModal, #editVoipModal').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
  });

 
</script>
</body>
</html>
