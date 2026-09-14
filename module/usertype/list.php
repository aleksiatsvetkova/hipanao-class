<section class="content">

   <!-- HIPANAO SOLUTIONS  -->
   
      <div class="container-fluid">
         <?php check_message(); ?>
        <div class="row">
          <div class="col-12">
          
            <div class="card card-primary card-outline">
              <div class="card-header">
                <div class="hipanao-toolbar">
                  <div>
                    <h3 class="card-title mb-0"><i class="fa fa-key mr-1 text-muted"></i> List of User Types</h3>
                  </div>
                  <div class="hipanao-toolbar-actions">
                    <button type="button" class="btn btn-primary btn-add-new" data-toggle="modal" data-target="#AddNewEntry"><i class="fa fa-plus mr-1"></i>Add New</button>
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="tbluser" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th width="10%">#</th>
                    <th width="60%">Name</th>
                    <th>Status</th>
                    <th width="10%">Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  
                  </tbody>
                  <tfoot>
                  
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>

<!-----START of Add Form---->
     <div class="modal fade" id="AddNewEntry">
        <div class="modal-dialog">
        <form action="controller.php?action=add" enctype="multipart/form-data" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add User Type</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                         
              <!-- /.card-header -->
              <div class="row">
                    
                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="name"  class="col-form-label col-form-label-sm">User Type</label>
                        <input type="text" class="form-control form-control-sm" name="usertype"
                        id="Fullname" placeholder="Enter User Type" required>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="STATUS" class="col-form-label col-form-label-sm">Status</label>
                        <select class="form-control form-control-sm" name="STATUS" required>
                          <option value="Active">Active</option>
                          <option value="In-Active">In-Active</option>
                        </select>
                      </div>
                    </div>
                   
                  </div>
                                
                <!-- /.card-body -->
           
              <!-- /.card-body -->
           
            </div>
            <div class="modal-footer justify-content-between">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary" name="save" type="submit">Save changes</button>
             
            </div>
          </div>
          </form>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

<!-----End of Add Form---->
<!-----Start of edit Form---->
<div class="modal fade" id="editEntry">
        <div class="modal-dialog">
        <form action="controller.php?action=edit" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Modify User Account</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                         
              <!-- /.card-header -->
              <div class="row">
                    <input type="hidden" name="TYPEID" id="TYPEID">
                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="name"  class="col-form-label col-form-label-sm">User Type</label>
                        <input type="text" class="form-control form-control-sm" name="USERTYPE"
                        id="USERTYPE" placeholder="Enter User Type">
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="STATUS" class="col-form-label col-form-label-sm">Status</label>
                        <select class="form-control form-control-sm" name="STATUS" id="STATUS">
                          <option value="Active">Active</option>
                          <option value="In-Active">In-Active</option>
                        </select>
                      </div>
                    </div>
                   
                  </div>
                    
              <!-- /.card-body -->
           
            </div>
            <div class="modal-footer justify-content-between">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary swalDefaultSuccesss" name="edit" type="submit">Save changes</button>
             
            </div>
          </div>
          </form>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

<?php

?>