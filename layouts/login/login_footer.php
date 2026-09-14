<!-- ================================================= -->
<!-- COPYRIGHT -->
<!-- ================================================= -->

<div class="text-center tc-copyright py-3">
  Copyright &copy; 2026 <a href="http://localhost/hipanao-class/index.php#">HIPANAO</a>. All rights reserved.
</div>


<!-- jQuery -->
<script src="<?php echo WEB_ROOT; ?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo WEB_ROOT; ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo WEB_ROOT; ?>dist/js/adminlte.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const eye = document.getElementById("eye");
  const password = document.getElementById("password");
  if (eye && password) {
    eye.addEventListener("click", function () {
      if (password.type === "password") {
        password.type = "text";
        eye.classList.remove("fa-eye");
        eye.classList.add("fa-eye-slash");
      } else {
        password.type = "password";
        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");
      }
    });
  }
});
</script>

</body>
</html>
