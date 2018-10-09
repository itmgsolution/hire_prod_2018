<?php

	include "db_connect.php";

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	
	
	$the_id = "5200501031625";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}
	
	$the_id = addslashes(substr($the_id,0,13));
	
	$the_count = 0;
	
	
	
	
	//yoes 20170909 -- instead of showing output -> show seleting table insteae
					
	//echo $the_output; exit();					
			
	//if($the_count > 0){			
	
	$result_array = array(
					
					
						array(
						
							"EmployerName" => "บริษัท เดอะบาร์บีคิวพลาซ่า จำกัด"
							, "ContractNo" => "02/1009/2560/00001"
							
							, "ContractStartDate" => "2018-01-01"
							, "ContractEndDate" => "2018-12-31"
							
							, "RequestTypeName" => "การให้ความช่วยเหลืออื่นใด"
							
							, "ContractAmount" => "109500"
						
						)
					
					
					);

?>


<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; ">
		
			
         
            
            
			<tr bgcolor="#9C9A9C" align="center">
				<td align="center" style="color: #fff;">
					ลำดับที่
				</td>
                <td align="center" style="color: #fff;">
					ชื่อสถานประกอบการ
				</td>
				<td align="center" style="color: #fff;">
					เลขที่สัญญา
				</td>
               
				<td align="center" style="color: #fff;">
					วันที่เริ่มต้นสัญญา
				</td>
			
				<td align="center" style="color: #fff;">
					วันที่สิ้นสุดสัญญา
				</td>
                
                <td align="center" style="color: #fff;">
					กิจกรรมตามมาตรา35
				</td>
                <td align="center" style="color: #fff;">
					มูลค่า
				</td>
				
                
                <td align="center" style="color: #fff;">
					
				</td>
				
				
			</tr>
            
            <?php 
			
				for($i=0;$i < count($result_array);$i++){
					
					$seq++;
			?>
		
        	<tr bgcolor="#ffffff" align="center">
				<td>
					<div align="center">
						<?php echo $seq; ?>
					</div>
				</td>
                <td>
					<?php 
					
					echo $result_array[$i]["EmployerName"];
					
					?>
				</td>
				<td>
					<?php 
					
					echo $result_array[$i]["ContractNo"];
					
					?>
                    
                     <input id="doe_contract_number_<?php echo $seq;?>" type="hidden" value="<?php echo $result_array[$i]["ContractNo"];?>" />
				</td>
				
                <td>
					<?php 
					
					echo formatDateThai($result_array[$i]["ContractStartDate"]);
					
					?>
                    <input id="doe_start_date_year_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["ContractStartDate"],0,4);?>" />
                    <input id="doe_start_date_month_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["ContractStartDate"],5,2);?>" />
                    <input id="doe_start_date_day_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["ContractStartDate"],8,2);?>" />
				</td>
					
				<td>
					<?php 
					
					echo formatDateThai($result_array[$i]["ContractEndDate"]);
					
					?>
                     <input id="doe_end_date_year_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["ContractEndDate"],0,4);?>" />
                    <input id="doe_end_date_month_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["ContractEndDate"],5,2);?>" />
                    <input id="doe_end_date_day_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["ContractEndDate"],8,2);?>" />
				</td>	
                
                <td>
					<?php 
					
					echo $result_array[$i]["RequestTypeName"];
					
					?>
                    
                     <input id="doe_request_type_<?php echo $seq;?>" type="hidden" value="<?php echo $result_array[$i]["RequestTypeName"];?>" />
				</td>
                
                
                 <td>
					<?php 
					
					echo number_format($result_array[$i]["ContractAmount"],0);
					
					?>
                    
                     <input id="doe_contract_amount_<?php echo $seq;?>" type="hidden" value="<?php echo $result_array[$i]["ContractAmount"];?>" />
				</td>
                
                <td>
                
                <a href="#" onclick="populateSSODates(<?php echo $seq;?>); return false;">เลือกข้อมูล</a>
                
                </td>
			
			
			</tr>
            
            <?php }?>
            
</table>
<script>
	
	function populateSSODates(what){
		
		//alert('what');
		
		//alert($("#doe_start_date_day_"+what).val()*1);
		
		$("#curator_start_date_day").val($("#doe_start_date_day_"+what).val());
		$("#curator_start_date_month").val($("#doe_start_date_month_"+what).val());
		$("#curator_start_date_year").val($("#doe_start_date_year_"+what).val());
		
		
		$("#curator_end_date_day").val($("#doe_end_date_day_"+what).val());
		$("#curator_end_date_month").val($("#doe_end_date_month_"+what).val());
		$("#curator_end_date_year").val($("#doe_end_date_year_"+what).val());
		
		$("#curator_contract_number").val($("#doe_contract_number_"+what).val());
		
		$("#curator_value").val($("#doe_contract_amount_"+what).val());
		
		$("#curator_event").val($("#doe_request_type_"+what).val());
		
		$( "#doe_result" ).html('');
	}

</script>
	