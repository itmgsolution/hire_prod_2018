<?php

	include "db_connect.php";
	
	header('Content-Type: text/html; charset=utf-8');
	
	$the_invoice_id = $_GET[invoice_id]*1;
	
	$invoice_row = getFirstRow("
	
		select
			*
		from
			invoices
		where
			invoice_id = '$the_invoice_id'
				
	");
	

	$the_cid = $invoice_row[invoice_cid];
	$the_year = $invoice_row[invoice_lawful_year];
	

?>

<div align="center"> <strong>ใบชำระเงิน</strong> <br />
  <br />


<table border="1"  cellpadding="5" style="border-collapse:collapse;" >
                            
                              <?php if($sess_accesslevel !=4){?>
                              <?php } ?>
                              
                              <tr>
                                <td>สถานประกอบการ</td>
                                <td>
                                
                                
                                <strong><?php 
								
								
								
								$company_row = getFirstRow("select * from company where cid = '$the_cid'");
								
								echo formatCompanyName($company_row[CompanyNameThai],$company_row[CompanyTypeCode]);
								
								
								?></strong></td>
                              </tr>
                              <tr>
                                <td>เลขทะเบียนนายจ้าง</td>
                                <td><?php 
								
								echo $company_row[CompanyCode];
								
								?></td>
                              </tr>
                              <tr>
                                    <td>สำหรับปี</td>
                                    <td><strong><?php 
										//**toggle payment
										
										
										echo $the_year+543;
										
										
										// ddl_year_payments will only allow to add payment year 2015?></strong></td>
    </tr>
                              <tr>
                                <td>วันที่ออกใบชำระเงิน</td>
                                <td>
								
								<strong><?php 
								
								
									$this_date_time = date("Y-m-d");
								
									echo formatDateThai($this_date_time);?></strong>
                                
                                </td>
                              </tr>
                              
  </table>
<br />
<table border="1" cellpadding="5" style="border-collapse:collapse;" >
  
  <tr>
    <td>วันที่ชำระเงิน</td>
    <td colspan="3"><strong>
      <?php 
								
								
									
								
									echo formatDateThai($invoice_row[invoice_payment_date]);?>
    </strong></td>
    </tr>
  <tr>
    <td bgcolor="#FFF9F9">เงินต้นคงเหลือ</td>
    <td bgcolor="#FFF9F9"><?php 
	
		echo number_format($invoice_row[invoice_owned_principal],2);
		
	?>
      บาท</td>
    <td bgcolor="#FFF9F9">ดอกเบี้ยค้างชำระ</td>
    <td bgcolor="#FFF9F9"><?php 
	
		echo number_format($invoice_row[invoice_owned_interest],2);
		
	?>
      บาท</td>
    </tr>
  <tr>
    <td bgcolor="#E6F2FF">จำนวนเงินที่ต้องการจ่าย</td>
    <td bgcolor="#E6F2FF"> 
      <?php 
	
		echo number_format($invoice_row[invoice_amount],2);
		
	?> บาท</td>
    <td bgcolor="#E6F2FF">&nbsp;</td>
    <td bgcolor="#E6F2FF">&nbsp;</td>
    </tr>
  <tr>
    <td bgcolor="#E6F2FF">จ่ายเป็นเงินต้น</td>
    <td bgcolor="#E6F2FF"><?php 
	
		
		$owned_money = $invoice_row[invoice_owned_principal];
		$pay_for_start = $invoice_row[invoice_principal_amount];
		
		//จ่ายเกิน vs จ่ายขาด
									
		if($owned_money < $pay_for_start){
			
			echo number_format($owned_money,2);
			$extra_paid = $pay_for_start- $owned_money;
			
		}elseif($owned_money > $pay_for_start){
			
			echo number_format($pay_for_start,2);
			$missing_paid = $owned_money - $pay_for_start;
			
		}else{
		
			echo number_format($pay_for_start,2);
		
		}
		
		
		//echo number_format($invoice_row[invoice_principal_amount],2);
		
	?>
      บาท</td>
    <td bgcolor="#E6F2FF">จ่ายเป็นดอกเบี้ย</td>
    <td bgcolor="#E6F2FF"><?php 
	
		echo number_format($invoice_row[invoice_interest_amount],2);
		
	?>
      บาท</td>
    </tr>
  <tr>
    <td bgcolor="#E6F2FF"><?php 
	
	
		if($extra_paid){
			
			echo "จ่ายเงินเกิน";
			
		}
		
		if($missing_paid){
			
			echo "จ่ายเงินขาด";
			
		}
	
	?></td>
    <td bgcolor="#E6F2FF" colspan="3">
    
     <?php
								
		
		if($extra_paid){
			
			echo "<font color='green'>จ่ายเกิน ".number_format($extra_paid,2)." บาท</font>";	
			
		}
		
		if($missing_paid){
			
			echo "<font color='red'>จ่ายขาด ".number_format($missing_paid,2)." บาท</font>";	
			
		}
	
	?>		
    
    
    </td>
    
  </tr>
  <tr>
    <td height="34">หมายเหตุ</td>
    <td colspan="3">
    
    <?php 
	
		echo $invoice_row[invoice_remarks];
	?>
    
    </td>
    </tr>
  <tr>
    <td>เจ้าหน้าที่ผู้ออกใบชำระเงิน</td>
    <td colspan="3"> 
    <?php 
	
		echo $invoice_row[invoice_userid_text];
	?></td>
    </tr>
</table>
<br />
<table border="1"  cellpadding="5" style="border-collapse:collapse;"  >
  <?php if($sess_accesslevel !=4){?>
  <?php } ?>
  <tr>
    <td>เลขที่ใบชำระเงิน</td>
    <td><strong>
      <?php 
								
								
								echo $invoice_row[invoice_id].$invoice_row[invoice_cid];
								
								
								?>
    </strong></td>
  </tr>
  
  
  <?php if(1==1){?>
  <tr>
  	<td>
    </td>
    <td>
    <style>
	
		.barcode1px {
            border-left: 1px solid black;
            position: absolute;
            height: 1cm;
        }

        .barcode2px {
            border-left: 2px solid black;
            position: absolute;
            height: 1cm;
        }

        .barcode3px {
            border-left: 3px solid black;
            position: absolute;
            height: 1cm;
        }

        .barcode4px {
            border-left: 4px solid black;
            position: absolute;
            height: 1cm;
        }
	
	</style>
    <?php 
	
		require_once 'custom_tcpdf_barcodes_1d_02.php';
		
		
		//$barcode = "|$taxId$serviceCode\r$ref1\r$ref2\r$amountInBarcode";
		//$barcode = "|99900\r591002737389\r3504\r30162300\r";
		$barcodeObj = new CustomTCPDFBarcode($invoice_row[invoice_id].$invoice_row[invoice_cid], "C128");
		
		
	
	?>
    <div align="center">
    
    
    	<?php 
		
		echo $barcodeObj->getBarcodeCustomHTML();
		
		?>
        
        
    	<?php if(1==0){?>
    	<img src="decors/dummy_bar.jpg" />
        <?php }?>
    
    </div>    
    </td>
    </tr>
  <?php }?>
  
  
</table>

<br />


<?php if($_POST[do_print]){ ?>

	<script>
		window.print();
	</script>

<?php }else{ ?>
<form method="post" target="_blank">
	<input name="do_print" type="submit"  value="พิมพ์ใบชำระเงิน" />                                
</form>  
<?php }?>  
 
</div>
