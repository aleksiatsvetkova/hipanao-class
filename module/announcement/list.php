<section class="content">

<!-- HIPANAO SOLUTIONS - Announcements -->

    <div class="container-fluid">
        <?php check_message(); ?>
        <div class="row">
            <div class="col-12">

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <div class="hipanao-toolbar">
                            <div>
                                <h3 class="card-title mb-0"><i class="fa fa-bullhorn mr-1 text-muted"></i> Announcements</h3>
                            </div>
                            <div class="hipanao-toolbar-actions">
                                <button type="button" class="btn btn-primary btn-add-new" data-toggle="modal" data-target="#AddNewAnnouncement"><i class="fa fa-plus mr-1"></i>New announcement</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="tblannouncement" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Picture</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Date Posted</th>
                                <th width="12%">Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
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

<!-- Add Modal -->
<div class="modal fade" id="AddNewAnnouncement">
    <div class="modal-dialog">
        <form id="form-add-announcement" action="controller.php?action=add" enctype="multipart/form-data" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-bullhorn"></i> &nbsp;New Announcement</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <!-- Picture upload -->
                        <div class="col-sm-12 mb-2">
                            <div class="hipanao-photo-upload">
                                <img id="img-upload-ann-add" src="<?php echo WEB_ROOT; ?>no.png" alt="Announcement picture" style="max-height:140px;object-fit:cover;">
                                <div class="custom-file">
                                    <input type="file" name="PICTURE" id="ANN_PICTURE" class="custom-file-input announcement-photo-input" data-preview="#img-upload-ann-add" accept="image/*">
                                    <label class="custom-file-label" for="ANN_PICTURE">Choose picture...</label>
                                </div>
                                <small class="text-muted">Optional. JPG/PNG/GIF/WEBP.</small>
                            </div>
                        </div>

                        <!-- Video upload -->
                        <div class="col-sm-12 mb-2">
                            <div class="hipanao-photo-upload">
                                <video id="vid-upload-ann-add" controls style="display:none;max-height:180px;width:100%;object-fit:contain;background:#000;margin-bottom:.6rem;"></video>
                                <div class="custom-file">
                                    <input type="file" name="VIDEO" id="ANN_VIDEO" class="custom-file-input announcement-video-input" data-preview="#vid-upload-ann-add" accept="video/*">
                                    <label class="custom-file-label" for="ANN_VIDEO">Choose video...</label>
                                </div>
                                <small class="text-muted">Optional. MP4/WEBM/OGG/MOV.</small>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label col-form-label-sm">Title</label>
                                <input type="text" class="form-control form-control-sm" name="TITLE" placeholder="Enter announcement title" required maxlength="200">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label col-form-label-sm">Content</label>
                                <textarea class="form-control form-control-sm" name="CONTENT" rows="5" placeholder="Enter the full announcement content" required></textarea>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label col-form-label-sm">Date Posted</label>
                                <input type="date" class="form-control form-control-sm" name="DATE_POSTED" id="ADD_DATE_POSTED" required>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label col-form-label-sm">Status</label>
                                <select class="form-control form-control-sm" name="STATUS">
                                    <option value="Published">Published (shown on homepage)</option>
                                    <option value="Draft">Draft (not yet visible)</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="save" type="submit">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editEntry">
    <div class="modal-dialog">
        <form action="controller.php?action=edit" enctype="multipart/form-data" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-edit"></i> &nbsp;Modify Announcement</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="ANNOUNCEMENT_ID" id="EDIT_ANNOUNCEMENT_ID">

                        <!-- Picture upload -->
                        <div class="col-sm-12 mb-2">
                            <div class="hipanao-photo-upload">
                                <img id="img-upload-ann-edit" src="<?php echo WEB_ROOT; ?>no.png" alt="Announcement picture" style="max-height:140px;object-fit:cover;">
                                <div class="custom-file">
                                    <input type="file" name="PICTURE" id="ANN_PICTURE1" class="custom-file-input announcement-photo-input" data-preview="#img-upload-ann-edit" accept="image/*">
                                    <label class="custom-file-label" for="ANN_PICTURE1">Choose new picture...</label>
                                </div>
                                <small class="text-muted">Optional. Leave blank to keep the current picture.</small>
                            </div>
                        </div>

                        <!-- Video upload -->
                        <div class="col-sm-12 mb-2">
                            <div class="hipanao-photo-upload">
                                <video id="vid-upload-ann-edit" controls style="display:none;max-height:180px;width:100%;object-fit:contain;background:#000;margin-bottom:.6rem;"></video>
                                <div class="custom-file">
                                    <input type="file" name="VIDEO" id="ANN_VIDEO1" class="custom-file-input announcement-video-input" data-preview="#vid-upload-ann-edit" accept="video/*">
                                    <label class="custom-file-label" for="ANN_VIDEO1">Choose new video...</label>
                                </div>
                                <small class="text-muted">Optional. Leave blank to keep the current video.</small>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label col-form-label-sm">Title</label>
                                <input type="text" class="form-control form-control-sm" name="TITLE" id="EDIT_TITLE" required maxlength="200">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label col-form-label-sm">Content</label>
                                <textarea class="form-control form-control-sm" name="CONTENT" id="EDIT_CONTENT" rows="5" required></textarea>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label col-form-label-sm">Date Posted</label>
                                <input type="date" class="form-control form-control-sm" name="DATE_POSTED" id="EDIT_DATE_POSTED" required>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label col-form-label-sm">Status</label>
                                <select class="form-control form-control-sm" name="STATUS" id="EDIT_STATUS">
                                    <option value="Published">Published (shown on homepage)</option>
                                    <option value="Draft">Draft (not yet visible)</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary swalDefaultSuccesss" name="edit" type="submit">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
