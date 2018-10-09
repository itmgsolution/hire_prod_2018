﻿<?php
require_once 'db_connect.php';
require_once 'session_handler.php';
require_once 'c2x_include.php';


if($_POST["report_format"] == "excel"){
	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_22.xls");

	$is_excel = 1;
}elseif($_POST["report_format"] == "words"){
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=report_22.doc");
}elseif($_POST["report_format"] == "pdf"){
	$is_pdf = 1;
}else{
	header ('Content-type: text/html; charset=utf-8');
}

$year = NULL;
$province = NULL;
$txDateFrom  = NULL;
$txDateTo = NULL;
$ref1 = NULL;
$ref2 = NULL;
$companyName = NULL;
$companyCode = NULL;
$bookReceiptNo = NULL;
$receiptNo = NULL;
$selectedPaymentStatus = NULL;
$bank = NULL;
$chequeNo = NULL;


$conditionSQL = '';

if (is_numeric($_POST['ddl_paymentstatus'])){
	$selectedPaymentStatus = intval($_POST['ddl_paymentstatus']);
}

if (is_numeric($_POST['ddl_year'])){
	$year = intval($_POST['ddl_year']);
}

if (is_numeric($_POST['Province'])){
	$province = intval($_POST['Province']);
}

if ($_POST['chk_from'] == '1'){
	// "date from" filter
	if ($_POST ['txDateFrom_year'] > 0 && $_POST['txDateFrom_month'] > 0 && $_POST['txDateFrom_day'] > 0) {
		$txDateFrom = DateTime::createFromFormat('Y-m-d', $_POST["txDateFrom_year"] . "-" . $_POST ["txDateFrom_month"] . "-" . $_POST ["txDateFrom_day"]);
	}
	
	// "date to" filter
	if ($_POST ['txDateTo_year'] > 0 && $_POST['txDateTo_month'] > 0 && $_POST['txDateTo_day'] > 0) {
		$txDateTo = DateTime::createFromFormat('Y-m-d', $_POST["txDateTo_year"] . "-" . $_POST ["txDateTo_month"] . "-" . $_POST ["txDateTo_day"]);
	}
}

if ($_POST['Ref1'] != ""){
	$ref1 = $_POST['Ref1'];
}

if ($_POST['Ref2'] != ""){
	$ref2 = $_POST['Ref2']; 
}

if ($_POST['CompanyCode'] != ""){
	$companyCode = $_POST['CompanyCode'];
}

if ($_POST['CompanyName'] != ""){
	$companyName = $_POST['CompanyName']; 
}

if ($_POST['BookReceiptNo'] != ""){
	$bookReceiptNo = $_POST['BookReceiptNo'];
}

if ($_POST['ReceiptNo'] != ""){
	$receiptNo = $_POST['ReceiptNo']; 
}

if ($_POST['check_bank'] != ""){
	$bank = $_POST['check_bank'];
}

if ($_POST['ChequeNo'] != ""){
	$chequeNo = $_POST['ChequeNo']; 
}

if (!is_null($selectedPaymentStatus)){
	$conditionSQL = "WHERE b.PaymentStatus=$selectedPaymentStatus";
}else{
	$conditionSQL = 'WHERE b.PaymentStatus <> 0';
}

if (!is_null($year)){
	$conditionSQL .= " AND law.Year=$year";
}

if (!is_null($txDateFrom)){
	$conditionSQL .= " AND b.PaymentDate >= '" . $txDateFrom->format('Y-m-d') . "'";
}

if (!is_null($txDateTo)){
	$conditionSQL .= " AND b.PaymentDate <= '" . $txDateTo->format('Y-m-d') . " 23:59:59.999'";
}

if (!is_null($ref1)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('b.ServiceRef1', $ref1);
}

if (!is_null($ref2)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('b.ServiceRef2', $ref2);
}

if (!is_null($companyName)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('c.CompanyNameThai', $companyName);
}

if (!is_null($companyCode)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('c.CompanyCode', $companyCode);
}

if (!is_null($bookReceiptNo)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('r.BookReceiptNo', $bookReceiptNo);
}

if (!is_null($receiptNo)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('r.ReceiptNo', $receiptNo);
}

if (!is_null($bank)){
	$conditionSQL .= " AND ba.bank_id=$bank";
}

if (!is_null($chequeNo)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('b.ChequeNo', $chequeNo);
}

if (!is_null($province)){
	$conditionSQL .= " AND c.Province = $province";
}

// $countSQL = "
// 		SELECT COUNT(*)
// 		FROM bill_payment b
// 		LEFT JOIN lawfulness law ON law.LID = b.LID
// 		LEFT JOIN company c ON law.CID = c.CID
// 		LEFT JOIN bank ba ON ba.BankCode = b.ChequeBankCode
//         LEFT JOIN receipt r ON r.RID = b.ReceiptID
// 		$conditionSQL";

// $record_count_all = getFirstItem ($countSQL);

// $per_page = 20;
// $num_page = ceil ( $record_count_all / $per_page );

// $cur_page = 1;
// if (is_numeric ( $_POST ["start_page"] ) && $_POST ["start_page"] <= $num_page && $_POST ["start_page"] > 0) {
// 	$cur_page = $_POST ["start_page"];
// }

// $starting_index = 0;
// if ($cur_page > 1) {
// 	$starting_index = ($cur_page - 1) * $per_page;
// }

$the_limit = '';//"limit $starting_index, $per_page";

// ///////////////


$querySQL = "


		select
			*
		from
			provinces aa
			 
			 left join
			 
			 (
			

		
				SELECT
					pv.province_id,
				  
				   law.year, 
				  
				   sum(b.transactionprincipalamount) as TransactionPrincipalAmount,  
				   sum(b.transactioninterestamount) as TransactionInterestAmount, 
				   sum(b.transactiontotalamount) as TransactionTotalAmount, 
				   sum(b.paidtotalamount)
				  
					
				FROM bill_payment b
				LEFT JOIN lawfulness law ON law.LID = b.LID
				LEFT JOIN company c ON law.CID = c.CID
				LEFT JOIN bank ba ON ba.BankCode = b.ChequeBankCode
				LEFT JOIN receipt r ON r.RID = b.ReceiptID
				LEFT JOIN cancelled_payment cp
					 ON cp.NEPFundPaymentID = b.NEPFundPaymentID AND b.ReceiptID IS NULL AND b.NEPFundPaymentID IS NOT NULL
				
						and 
						 (nepfundcanceldate is null or nepfundcanceldate = '')
				join
					provinces pv
						on
						pv.province_id = c.province
				
						
				$conditionSQL
				
				
				and 
				concat(b.ServiceRef1, b.ServiceRef2) in (
					
					select
						concat(Ref1, Ref2)
					from
						webservice_log_ktb
				
				)
		
		
		group by
			pv.province_name
			,  law.year
			
		) mmmm
		
		on aa.province_id = mmmm.province_id
		
		order by
			aa.province_name asc
	
	";

//echo $querySQL;

$queryResult = mysql_query($querySQL);

$paymentStatusMapping = getPaymentStatusMapping();
unset($paymentStatusMapping[0]);

$paymentMethodMapping = getPaymentMethodMapping();

// total records
$seq = 0;

function writeDate($dateValue){
	if (is_null($dateValue)){
		echo "-";
	}else{
		echo formatDateThai($dateValue);
	}
}
?>
	<div align="center">
	  <strong>รายละเอียดของการชำระเงินผ่านระบบธนาคารกรุงไทย Onlineแบ่งรายจังหวัด<br />กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</strong>
		<br />
	</div>
	<table border="1" align="center" cellpadding="5" cellspacing="0" style="border-collapse: collapse;font-size:<?php echo !$is_pdf ? 14:28; ?>px">
		<thead>
			<tr bgcolor="#9C9A9C" align="center">
				<td align="center">
					ลำดับที่
				</td>
				<td align="center">จังหวัด</td>
				
				<td align="center">
					เงินต้น
				</td>
				<td align="center">
					ดอกเบี้ย
				</td>
				<td align="center">
					รวม
				</td>
			</tr>
		</thead>
		<tbody>
		<?php
		// main loop
		while ($row = mysql_fetch_array($queryResult)) {
			$seq ++;
		?>
			<tr bgcolor="#ffffff" align="center">
				<td>
					<div align="center">
						<?php echo $seq; ?>
					</div>
				</td>
				<td><div align="left"> <?php echo $row["province_name"]; ?> </div></td>	
				
				<td>
					<div align="right"><?php echo formatNumber($row["TransactionPrincipalAmount"]) ;?></div>
				</td>
				<td>
					<div align="right"><?php echo formatNumber($row["TransactionInterestAmount"]);?></div>
				</td>
				<td>
					<div align="right"><?php echo formatNumber($row["TransactionTotalAmount"]);?></div>
				</td>
			</tr>
		<?php } //end loop to generate rows?>
		</tbody>
	</table>
	<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?></div>