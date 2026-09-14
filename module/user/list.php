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
                    <h3 class="card-title mb-0"><i class="fa fa-users mr-1 text-muted"></i> User Directory</h3>
                  </div>
                  <div class="hipanao-toolbar-actions">
                    <button type="button" class="btn btn-primary btn-add-new" data-toggle="modal" data-target="#AddNewEntry"><i class="fa fa-user-plus mr-1"></i>New user</button>
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="tbluser" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Username</th>
                    <th>User Type</th>
                    <th>Date Added</th>
                    <th>Date Modified</th>
                    <th width="12%">Action</th>
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
<div class="modal fade" id="AddNewEntry">
        <div class="modal-dialog">
        <form action="controller.php?action=add" enctype="multipart/form-data" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"><i class="fa fa-user-plus"></i> &nbsp;Add New User</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                         
              <!-- /.card-header -->
              <div class="row">

                    <!-- Photo upload -->
                    <div class="col-sm-12 mb-2">
                      <div class="hipanao-photo-upload">
                        <img id="img-upload-user-add" src="<?php echo WEB_ROOT; ?>module/user/images/default.png" alt="User photo">
                        <div class="custom-file">
                          <input type="file" name="PICTURE" id="USER_PICTURE" class="custom-file-input user-photo-input" data-preview="#img-upload-user-add" accept="image/*">
                          <label class="custom-file-label" for="USER_PICTURE">Choose photo...</label>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="name"  class="col-form-label col-form-label-sm">Name</label>
                        <input type="text" class="form-control form-control-sm" name="DISPLAYNAME"
                        id="Fullname" placeholder="Enter Name" required>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="name"  class="col-form-label col-form-label-sm">Username</label>
                        <input type="text" class="form-control form-control-sm" name="USERNAME"
                        id="Fullname" placeholder="Enter UserName" required>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="name"  class="col-form-label col-form-label-sm">Password</label>
                        <input type="password" class="form-control form-control-sm" name="PASSWORD"
                        id="Fullname" placeholder="Enter Password" required>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="TYPE"  class="col-form-label col-form-label-sm">User Type</label>
                         <select class="form-control form-control-sm" name="TYPE" id="TYPE">
                         <?php
                            $Usertype = new Usertype();
                            $cur = $Usertype->listOfUserTypes();
                            foreach ($cur as $m) {
                              echo '<option value="'. $m->USERTYPE.'">'.$m->USERTYPE .'</option>';
                            }

                            ?>
                         
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
<!-----START of Add Form---->
     <div class="modal fade" id="editEntry">
        <div class="modal-dialog">
        <form action="controller.php?action=edit" enctype="multipart/form-data" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"><i class="fa fa-user-edit"></i> &nbsp;Modify User Account</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                         
              <!-- /.card-header -->
              <div class="row">
                    <input type="hidden" name="UID" id="UID">

                    <!-- Photo upload -->
                    <div class="col-sm-12 mb-2">
                      <div class="hipanao-photo-upload">
                        <img id="img-upload-user-edit" src="<?php echo WEB_ROOT; ?>module/user/images/default.png" alt="User photo">
                        <div class="custom-file">
                          <input type="file" name="PICTURE" id="USER_PICTURE1" class="custom-file-input user-photo-input" data-preview="#img-upload-user-edit" accept="image/*">
                          <label class="custom-file-label" for="USER_PICTURE1">Choose new photo...</label>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="name"  class="col-form-label col-form-label-sm">Display Name</label>
                        <input type="text" class="form-control form-control-sm" name="FULLNAME"
                        id="FULLNAME" placeholder="Enter Complete Name" readonly>
                      </div>
                    </div>
                   
                  </div>
                               
                  <div class="row">
                 
                      <div class="col-sm-6">
                        <div class="form-group" >
                           <label for="username" class="col-form-label col-form-label-sm">Username</label>
                           <input type="text" class="form-control form-control-sm" id="USERNAME" name="USERNAME" placeholder="Enter Username" required>
                        </div>
                      </div>
                     <div class="col-sm-6">
                        <div class="form-group" >
                           <label for="Password" class="col-form-label col-form-label-sm">Password</label>
                           <input type="password" name="PASSWORD" class="form-control form-control-sm" id="Password" readonly>
                        </div>
                      </div>
                     
                   </div>
                   
                  <div class="row">
                   
                    <div class="col-sm-6">
                       <label for="TYPE"  class="col-form-label col-form-label-sm">User Type</label>
                         <select class="form-control form-control-sm" name="TYPE" id="TYPE">
                          <?php
                            $Usertype = new Usertype();
                            $cur = $Usertype->listOfUserTypes();
                            foreach ($cur as $m) {
                              echo '<option value="'. $m->USERTYPE .'">'. $m->USERTYPE .'</option>';
                            }

                            ?>
                        </select> 
                     </div>
                     <div class="col-sm-6">
                         <label for="customSwitch3"  class="col-form-label col-form-label-sm">Status</label>
                          <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">

                            <input type="checkbox" name="status" class="custom-control-input" id="customSwitch3">
                            <label class="custom-control-label" for="customSwitch3">Slide to Change Status</label>
                          </div> 
                       
                     </div>
                    
                   </div>
                   
                <!-- /.card-body -->
               
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

      <!-----Start of edit password Form---->
    <div class="modal fade" id="changepass">
        <div class="modal-dialog">
        <form action="controller.php?action=editpass" enctype="multipart/form-data" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"><i class="fa fa-key"></i> &nbsp;Change User Password</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                         
              <!-- /.card-header -->
              <div class="row">
                    <input type="hidden" name="UID" id="UIDpas">
                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="name"  class="col-form-label col-form-label-sm">Display Name</label>
                        <input type="text" class="form-control form-control-sm" name="FULLNAME"
                        id="dNAME" placeholder="Enter Complete Name" readonly>
                      </div>
                    </div>
                   
                  </div>
                               
                  <div class="row">
                 
                      <div class="col-sm-6">
                        <div class="form-group" >
                           <label for="username" class="col-form-label col-form-label-sm">Username</label>
                           <input type="text" class="form-control form-control-sm" id="UNAME" name="USERNAME" placeholder="Enter Username" readonly>
                        </div>
                      </div>
                     <div class="col-sm-6">
                        <div class="form-group" >
                           <label for="Password" class="col-form-label col-form-label-sm">Password</label>
                           <input type="password" name="PASSWORD" class="form-control form-control-sm" id="Password" required>
                        </div>
                      </div>
                     
                   </div>
                   
                  <div class="row">
                   
                    <div class="col-sm-6">
                       <label for="TYPE"  class="col-form-label col-form-label-sm">User Type</label>
                         <select class="form-control form-control-sm" name="TYPE" id="TYPE" disabled>
                          <?php
                            $Usertype = new Usertype();
                            $cur = $Usertype->listOfUserTypes();
                            foreach ($cur as $m) {
                              echo '<option value="'. $m->USERTYPE .'">'. $m->USERTYPE .'</option>';
                            }

                            ?>
                        </select> 
                     </div>
                     <div class="col-sm-6">
                         <label for="customSwitch3"  class="col-form-label col-form-label-sm">Status</label>
                          <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">

                            <input type="checkbox" name="status" class="custom-control-input" id="customSwitch3" disabled>
                            <label class="custom-control-label" for="customSwitch3">Slide to Change Status</label>
                          </div> 
                       
                     </div>
                    
                   </div>
                   
                <!-- /.card-body -->
               
              <!-- /.card-body -->
           
            </div>
            <div class="modal-footer justify-content-between">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary swalDefaultSuccesss" name="editpass" type="submit">Save changes</button>
             
            </div>
          </div>
          </form>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

<?php

?>