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
              <a class="btn btn-info mbot30 pull-right" data-toggle="modal" data-target="#voipModal">
                <?= _l('add_new'); ?>
              </a>
            </h4>
            <hr class="hr-panel-heading" />
            <table class="table table-bordered table-voip">
              <thead>
                <tr>
                  <th><?= _l('staff'); ?></th>
                  <th><?= _l('username'); ?></th>
                  <th><?= _l('password'); ?></th>
                  <th><?= _l('options'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($voip_settings)): ?>
                  <?php foreach ($voip_settings as $voip): ?>
                    <tr>
                      <td><?= html_escape($voip['firstname'] .' '. $voip['lastname']); ?></td>
                      <td><?= html_escape($voip['username']); ?></td>
                      <td><?= $voip['password']; ?></td>
                      <td>
                        <a href="<?= admin_url('voip/edit/' . $voip['id']); ?>"><button class="btn btn-warning btn-sm">
                          <?= _l('edit'); ?>
                        </button></a>
                        <a href="<?= admin_url('voip/delete/' . $voip['id']); ?>"><button
                          class="btn btn-danger btn-sm" onclick="return confirm('Are you ure you want to delete this record?');">
                          <?= _l('delete'); ?>
                        </button></a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center"><?= _l('no_voip_settings_found'); ?></td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>

            <!-- Add Modal -->
            <div id="voipModal" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="<?= admin_url('voip/add'); ?>" method="POST" autocomplete="off" novalidate>
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title"><?= _l('add_voip_setting'); ?></h4>
                    </div>
                    <div class="modal-body">
                      <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                      <div class="form-group">
                        <?= render_select('staffid', $staff, ['staffid', ['firstname', 'lastname']], 'staff', '', ['required' => true]) ?>
                      </div>
                      <?= render_input('username', 'username', '', 'text', ['required' => true]); ?>
                      <?= render_input('password', 'password', '', 'password', ['autocomplete' => 'new-password']); ?>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-success">Save</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            

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
