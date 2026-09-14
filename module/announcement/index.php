<?php

require_once("../../include/initialize.php");

$title   = "Announcement Module";
$content = 'list.php';

require_once ("../../theme/template.php");

?>

<script type="text/javascript">
    $(document).ready(function() {
        var t = $('#tblannouncement').DataTable( {
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                url: "<?php echo WEB_ROOT; ?>module/announcement/announcement_ajax.php",
                type: "POST"
            },
            "columnDefs": [ {
                "searchable": true,
                "orderable": true,
                "targets": 1
            } ],
            "order": [[ 4, 'desc' ]]
        } );

        t.on( 'order.dt search.dt', function () {
            t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
    });
</script>

<script type="text/javascript">
    // Picture preview - ipinapakita agad ang napiling litrato bago pa i-submit
    $(document).ready( function() {
        $(document).on('change', '.announcement-photo-input', function() {
            var input = this;
            var $input = $(this);
            var fileName = $input.val() ? $input.val().replace(/\\/g, '/').replace(/.*\//, '') : '';
            $input.next('.custom-file-label').text(fileName ? fileName : 'Choose picture...');

            var preview = $input.data('preview');
            if (preview && input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(preview).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    });
</script>

<script type="text/javascript">
    // Video preview - katulad ng picture preview sa taas, pero gamit ang
    // <video> element (URL.createObjectURL, mas mabilis kaysa FileReader
    // para sa malalaking video file).
    $(document).ready( function() {
        $(document).on('change', '.announcement-video-input', function() {
            var input = this;
            var $input = $(this);
            var fileName = $input.val() ? $input.val().replace(/\\/g, '/').replace(/.*\//, '') : '';
            $input.next('.custom-file-label').text(fileName ? fileName : 'Choose video...');

            var preview = $input.data('preview');
            if (preview && input.files && input.files[0]) {
                var url = URL.createObjectURL(input.files[0]);
                $(preview).attr('src', url).show();
            }
        });
    });
</script>

<script type="text/javascript">
    // Reset Add modal - laging blangko ang laman pag bubukas
    $('#AddNewAnnouncement').on('show.bs.modal', function () {
        $('#form-add-announcement')[0].reset();
        $('#ANN_PICTURE').next('.custom-file-label').text('Choose picture...');
        $('#img-upload-ann-add').attr('src', '<?php echo WEB_ROOT; ?>no.png');
        $('#ANN_VIDEO').next('.custom-file-label').text('Choose video...');
        $('#vid-upload-ann-add').removeAttr('src').hide();
        $('#ADD_DATE_POSTED').val('<?php echo date("Y-m-d"); ?>');
    });
</script>

<script type="text/javascript">
    $(document).on('click', '.editEntry', function(){
        var id = $(this).attr("ANNOUNCEMENT_ID");
        $.ajax({
            url: "<?php echo WEB_ROOT; ?>module/announcement/announcement_ajax.php",
            method: "POST",
            data: { ANNOUNCEMENT_ID: id },
            dataType: "json",
            success: function(data) {
                $('#editEntry').modal('show');
                $('#EDIT_ANNOUNCEMENT_ID').val(data.ANNOUNCEMENT_ID);
                $('#EDIT_TITLE').val(data.TITLE);
                $('#EDIT_CONTENT').val(data.CONTENT);
                $('#EDIT_DATE_POSTED').val(data.DATE_POSTED);
                $("#EDIT_STATUS option[value='" + data.STATUS + "']").prop("selected", true);

                $('#ANN_PICTURE1').next('.custom-file-label').text('Choose new picture...');
                $('#img-upload-ann-edit').attr('src', data.PICTURE_URL);

                $('#ANN_VIDEO1').next('.custom-file-label').text('Choose new video...');
                if (data.VIDEO_URL) {
                    $('#vid-upload-ann-edit').attr('src', data.VIDEO_URL).show();
                } else {
                    $('#vid-upload-ann-edit').removeAttr('src').hide();
                }
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).on('click', '.deleteEntry', function(){
        var id = $(this).attr("ANNOUNCEMENT_ID");
        Swal.fire({
            title: 'Delete Announcement?',
            text: "This action cannot be undone. Are you sure you want to delete this announcement?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.value) {
                window.location.href = "<?php echo WEB_ROOT; ?>module/announcement/controller.php?action=delete&id=" + id;
            }
        });
    });
</script>
