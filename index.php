<?php 

require_once("include/initialize.php");

/* HIPANAO SOLUTIONS - Public Homepage.
   Dating diretsong ni-redirect ang guest (walang session) papunta sa
   login.php. Ngayon, ipinapakita muna sa kanila ang public homepage
   (homepage.php) - ang "Login" button doon ang siyang magdadala sa
   kanila sa login.php kapag admin/staff/student sila. */
if (!isset($_SESSION['UID'])){
      require_once("homepage.php");
      exit;
     }

/* HIPANAO SOLUTIONS - Student Login Accounts.
   Ang Student account ay may sarili na ngayong MODULE, module/studentmodule/
   (kaparehong-kapareho ng disenyo/layout ng Admin - theme/template.php,
   full AdminLTE) - dito na siya dinadala sa halip ng dating custom na
   theme/studentdashboard/ layout. */
if (isset($_SESSION['TYPE']) && $_SESSION['TYPE'] === 'Student') {
    redirect(WEB_ROOT."module/studentmodule/index.php");
    exit;
}

/* HIPANAO SOLUTIONS - Program Head Login Accounts.
   Kaparehong ideya ng Student redirect sa itaas - ang Program Head
   account ay may sarili niyang MODULE, module/programhead/, kaya
   dito na siya dinadala sa halip na makita ang buong Admin dashboard. */
if (isset($_SESSION['TYPE']) && $_SESSION['TYPE'] === 'Program Head') {
    redirect(WEB_ROOT."module/programhead/index.php");
    exit;
}

$title="Home"; 
$content='home.php';
$view = (isset($_GET['page']) && $_GET['page'] != '') ? $_GET['page'] : '';
switch ($view) {
  case '1' :
         $title="Home"; 
     $content='home.php'; 
    
    break;  
  default :
    $content    = 'home.php'; 
}
require_once("theme/template.php");
?>

<?php /* HIPANAO SOLUTIONS - Ang Chart.js init script ay dito inilalagay
   (PAGKATAPOS ng require_once theme/template.php sa itaas), hindi sa
   loob ng home.php, dahil ang home.php ay naka-require SA GITNA ng
   template.php (bago pa ma-load ang Chart.js na script na nasa footer
   ng template.php). Kung doon ilalagay, hindi pa umiiral ang Chart.js
   kaya magkakaroon ng JS error at hindi lalabas ang chart - eksaktong
   parehong dahilan/ayos ng ginawang fix sa module/programhead/index.php
   para sa DataTables. Lumalabas lang ito sa Home page (hindi lahat ng
   $view), dahil doon lang talaga na-render ang #homeMonthlyChart canvas. */
if ($content === 'home.php' && isset($_SESSION['UID']) && (!isset($_SESSION['TYPE']) || ($_SESSION['TYPE'] !== 'Student' && $_SESSION['TYPE'] !== 'Program Head'))): ?>
<script type="text/javascript">
$(document).ready(function() {
	var chartData = window.HIPANAO_HOME_CHART_DATA || { labels: [], registrations: [], collections: [] };
	var ctx = document.getElementById('homeMonthlyChart');
	if (ctx) {
		new Chart(ctx, {
			type: 'line',
			data: {
				labels: chartData.labels,
				datasets: [
					{
						label: 'New Registrations',
						data: chartData.registrations,
						borderColor: '#007bff',
						backgroundColor: 'rgba(0,123,255,0.1)',
						yAxisID: 'y',
						tension: 0.3,
						fill: true
					},
					{
						label: 'Collections (PHP)',
						data: chartData.collections,
						borderColor: '#28a745',
						backgroundColor: 'rgba(40,167,69,0.1)',
						yAxisID: 'y1',
						tension: 0.3,
						fill: true
					}
				]
			},
			options: {
				maintainAspectRatio: false,
				interaction: { mode: 'index', intersect: false },
				scales: {
					y:  { type: 'linear', position: 'left', beginAtZero: true, title: { display: true, text: 'Registrations' } },
					y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, title: { display: true, text: 'PHP' } }
				}
			}
		});
	}
});
</script>
<?php endif; ?>
