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
						
							"accNo" => "10000000001"
							, "accBran" => "000000"
							
							, "employStatusDesc" => "1"
							, "expStartDate" => "2017-01-01"
							, "empResignDate" => "2017-02-28"
						
						)
						,
						array(
						
							"accNo" => "10000000001"
							, "accBran" => "000000"
							
							, "employStatusDesc" => "0"
							, "expStartDate" => "2016-03-06"
							, "empResignDate" => "2016-10-18"
						
						)
					
					);

?>


<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; ">
		
			
            <?php if(1==0){ ?>
            <tr bgcolor="#9C9A9C" align="center">
			  <td align="center" colspan="7" style="color: #fff;">ข้อมูลการทำงานจากประกันสังคม</td>
			 
 		    </tr>
            <?php } ?>
            
            
			<tr bgcolor="#9C9A9C" align="center">
				<td align="center" style="color: #fff;">
					ลำดับที่
				</td>
                <td align="center" style="color: #fff;">
					เลขที่บัญชีนายจ้าง
				</td>
				<td align="center" style="color: #fff;">
					สาขา
				</td>
                <td align="center" style="color: #fff;">
					สถานะการจ้างงาน
				</td>
				<td align="center" style="color: #fff;">
					วันที่เข้างาน
				</td>
			
				<td align="center" style="color: #fff;">
					วันที่ลาออก
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
					
					echo $result_array[$i]["accNo"];
					
					?>
				</td>
				<td>
					<?php 
					
					echo $result_array[$i]["accBran"];
					
					?>
				</td>
				<td>
					<?php 
					
					echo formatEmployStatusDesc($result_array[$i]["employStatusDesc"]);
					
					?>
				</td>
                <td>
					<?php 
					
					echo formatDateThai($result_array[$i]["expStartDate"]);
					
					?>
                    <input id="sso_start_date_year_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["expStartDate"],0,4);?>" />
                    <input id="sso_start_date_month_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["expStartDate"],5,2);?>" />
                    <input id="sso_start_date_day_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["expStartDate"],8,2);?>" />
				</td>
					
				<td>
					<?php 
					
					echo formatDateThai($result_array[$i]["empResignDate"]);
					
					?>
                     <input id="sso_end_date_year_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["empResignDate"],0,4);?>" />
                    <input id="sso_end_date_month_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["empResignDate"],5,2);?>" />
                    <input id="sso_end_date_day_<?php echo $seq;?>" type="hidden" value="<?php echo substr($result_array[$i]["empResignDate"],8,2);?>" />
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
		
		//alert($("#sso_start_date_day_"+what).val()*1);
		
		$("#le_date_day").val($("#sso_start_date_day_"+what).val());
		$("#le_date_month").val($("#sso_start_date_month_"+what).val());
		$("#le_date_year").val($("#sso_start_date_year_"+what).val());
		
		
		$("#le_end_date_day").val($("#sso_end_date_day_"+what).val());
		$("#le_end_date_month").val($("#sso_end_date_month_"+what).val());
		$("#le_end_date_year").val($("#sso_end_date_year_"+what).val());
		
		$( "#sso_result" ).html('');
	}

</script>
	