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
					
						
						"accNo" => "10000000001"
						, "EmployeeNo" => '512'
						
					
					);
	

?>


<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; ">
		
			
          
            
			<tr bgcolor="#9C9A9C" align="center">
				
                <td align="center" style="color: #fff;">
					เลขที่บัญชีนายจ้าง
				</td>
                
                <td align="center" style="color: #fff;">
					ข้อมูล ณ วันที่
				</td>
				
                <td align="center" style="color: #fff;">
					จำนวนลูกจ้างรวมทุกสาขา
				</td>
				
			
                
                <td align="center" style="color: #fff;">
					
				</td>
				
				
			</tr>
            
         
        	<tr bgcolor="#ffffff" align="center">
				
                <td>
					<?php 
					
					echo $result_array["accNo"];
					
					?>
				</td>
				
                  <td>
					<?php echo formatDateThai(date("Y-m-d"));?>
				</td>
				<td >
                	<div align="right">
					<?php 
					
					echo number_format($result_array["EmployeeNo"],0);
					
					?>
                    </div>
				</td>
              
					
				
                
                <td>
                
                <a href="#" onclick="populateSSOEmployees(<?php 
					
					echo number_format($result_array["EmployeeNo"],0);
					
					?>); return false;">เลือกข้อมูล</a>
                
                </td>
			
			
			</tr>
            
           
            
</table>
<script>
	
	function populateSSOEmployees(what){
		
		
		$("#update_employees_01").val(what);
		$("#sso_employee_result" ).html('');
	}

</script>
	