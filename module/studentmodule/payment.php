<?php

/* HIPANAO SOLUTIONS - Student Module > My Payments.
   Listahan ng lahat ng payment record ng estudyanteng ito
   (tblpayments) - Enrollment Fee man o Tuition Fee, kasama ang Print
   Receipt link (module/payment/receipt.php - UID login lang ang
   kailangan doon, kaya puwede itong buksan ng Student). May summary
   box rin sa itaas gamit ang parehong tuitionBreakdownForEnrollment() /
   paymentTotalsForEnrollment() helpers na ginagamit ng module/payment. */

global $mydb;

$smPayments = array();
if ($sid > 0) {
    $mydb->setQuery("SELECT p.*, u.DISPLAYNAME
        FROM `tblpayments` p
        LEFT JOIN `tblusers` u ON u.UID = p.RECEIVED_BY
        WHERE p.S_ID = '".$sid."'
        ORDER BY p.PAYMENT_ID DESC");
    $smPayments = $mydb->loadResultList();
}

$smBreakdown = array('total_amount' => 0);
$smPaidTotals = array('Enrollment Fee' => 0.0, 'Tuition Fee' => 0.0);
if ($currentEnrollment) {
    $smBreakdown  = tuitionBreakdownForEnrollment($currentEnrollment->ENROLLMENT_ID);
    $smPaidTotals = paymentTotalsForEnrollment($currentEnrollment->ENROLLMENT_ID);
}
$smTuitionBalance = max(0, $smBreakdown['total_amount'] - $smPaidTotals['Tuition Fee']);
$smEnrollFeeBalance = max(0, ENROLLMENT_FEE - $smPaidTotals['Enrollment Fee']);
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <?php if ($currentEnrollment): ?>
    <!-- ================= Summary (current term) ================= -->
    <div class="row">
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3>&#8369;<?php echo number_format($smPaidTotals['Enrollment Fee'], 2); ?></h3>
            <p>Enrollment Fee Paid</p>
          </div>
          <div class="icon"><i class="fa fa-file-invoice-dollar"></i></div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box <?php echo $smEnrollFeeBalance > 0 ? 'bg-warning' : 'bg-success'; ?>">
          <div class="inner">
            <h3>&#8369;<?php echo number_format($smEnrollFeeBalance, 2); ?></h3>
            <p>Enrollment Fee Balance</p>
          </div>
          <div class="icon"><i class="fa fa-hand-holding-usd"></i></div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
          <div class="inner">
            <h3>&#8369;<?php echo number_format($smPaidTotals['Tuition Fee'], 2); ?></h3>
            <p>Tuition Fee Paid</p>
          </div>
          <div class="icon"><i class="fa fa-money-check-alt"></i></div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box <?php echo $smTuitionBalance > 0 ? 'bg-danger' : 'bg-success'; ?>">
          <div class="inner">
            <h3>&#8369;<?php echo number_format($smTuitionBalance, 2); ?></h3>
            <p>Tuition Fee Balance</p>
          </div>
          <div class="icon"><i class="fa fa-balance-scale"></i></div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ================= Payment History ================= -->
    <div class="row">
      <div class="col-12">
        <div class="card card-success card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-money-check-alt mr-1 text-muted"></i> Payment History</h3>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>OR No.</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Date Paid</th>
                  <th>Received By</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($smPayments) < 1): ?>
                  <tr><td colspan="6" class="text-muted text-center">No payment record yet.</td></tr>
                <?php else: $i = 1; foreach ($smPayments as $p): ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($p->OR_NO ? $p->OR_NO : '-'); ?></td>
                    <td><?php echo ($p->PAYMENT_TYPE == 'Tuition Fee') ? '<span class="badge badge-subject-major">Tuition Fee</span>' : '<span class="badge badge-status-active">Enrollment Fee</span>'; ?></td>
                    <td>&#8369;<?php echo number_format($p->AMOUNT, 2); ?></td>
                    <td><?php echo htmlspecialchars($p->DATE_PAID); ?></td>
                    <td><?php echo htmlspecialchars($p->DISPLAYNAME ? $p->DISPLAYNAME : '-'); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
