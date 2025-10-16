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
        <h4 class="no-margin">
        <?php echo $title; ?> <a  class="btn btn-info mbot30 pull-right" data-toggle="modal" data-target="#myModal">
        Add Template </a>
        </h4>
        
        <hr class="hr-panel-heading" />
        <div class="clearfix"></div>
        <?php render_datatable(array(
        'Template Id',
        'Template Name',
        'Message',
        'Created At',
        _l('Action'),
        ),'template'); ?> 
        
        </div>
        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
        <form action="<?php echo admin_url().'template/Templates/add_template';?>" method="POST">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Template</h4>
        </div>
        
        <div class="modal-body">
        <input type="hidden" class="txt_csrfname" id="txt_csrfname" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        
        <label for="template_id"><b> Template ID</b></label>
        <input type="text" id="template_id" value="" name="template_id" class="form-control">
        <br>
        <label for="template_name"><b> Template Name</b></label>
        <input type="text" id="template_name" value="" name="template_name" class="form-control">
        <br>
        <label for="template_body"> <b>Message</b></label>
        <textarea id="template_body"  rows="10" value="" name="template_body" class="form-control"></textarea>
        <br>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit"  value="save" class="btn btn-success ">
        </div>
        </form>	
        </div>
        
        </div>
        </div>
        <!---Edit Modal--->
           <div id="myeditModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
        <form action="<?php echo admin_url().'template/Templates/edit_template';?>" method="POST">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Template</h4>
        </div>
        <div class="modal-body">
        <input type="hidden" class="txt_csrfname" id="txt_csrfname" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" id="edit_id" name="id">
        <label for="edittemplate_id"><b> Template ID</b></label>
        <input type="text" id="edittemplate_id" value="" name="template_id" class="form-control">
        <br>
        <label for="edittemplate_name"><b> Template Name</b></label>
        <input type="text" id="edittemplate_name" value="" name="template_name" class="form-control">
        <br>
        <label for="edittemplate_body"> <b>Message</b></label>
        <textarea id="edittemplate_body" rows="10" value="" name="template_body" class="form-control"></textarea>
        <br>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit"  value="save" class="btn btn-success ">
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
      
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">   </script>
        <script>
        function open_modal(id,template_id,template_name){
            
            
               $.ajax({
    url: "<?php echo admin_url().'template/sms/ajax_data_edit';?>",
    type: "POST",
    data: {id : id},
    dataType: "json",
    success: function(resp){
  //  $('#message').html(resp.template_body);
    
        
            $('#myeditModal').modal('show');
            
            $('#edit_id').val(id);
            $('#edittemplate_id').val(template_id);
            $('#edittemplate_name').val(template_name);
            $('#edittemplate_body').val(resp.template_body);
    }
    });
            
        
            
        }
        
        
        <?php if($_SESSION['delete_templates']==1){ ?>
        Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Template Deleted !',
        showConfirmButton: false,
        timer: 1500
        })
        <?php  }?>
        <?php if($_SESSION['edit_templates']==1){ ?>
        Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Template Updated Successfully',
        showConfirmButton: false,
        timer: 1500
        })
        <?php  }?>
        <?php if($_SESSION['add_template']==1){ ?>
        Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'New Template Created !',
        showConfirmButton: false,
        timer: 1500
        })
        <?php  }
        $this->session->unset_userdata('add_template');
        $this->session->unset_userdata('edit_templates');
        $this->session->unset_userdata('delete_templates');
        ?>
        </script>
        
        <script>
     

        $(function(){
        initDataTable('.table-template', window.location.href, [1], [1]);
        });
        
       
        function delete_template(url){
        Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
        if (result.isConfirmed) {
        window.location.href = url;
        }
        })
        
        }
        
        </script>
        </body>
        </html>
