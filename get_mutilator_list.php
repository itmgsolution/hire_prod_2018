<?php

	include "db_connect.php";
	include "session_handler.php";

	if($_GET["mode"]=="search"){
		$mode = "search";

	}elseif($_GET["mode"]=="letters"){
		$mode = "letters";
	}

?>
<?php



//manag minimum wage
if($_POST["var_value"]){

	$sql = "replace into vars values('wage_".$_POST["ddl_year"]."','".($_POST["var_value"]*1)."')";
	mysql_query($sql) or die (mysql_error());

}





?>



<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
									<h2>ตำแหน่งงานที่เปิดรับ</h2>
									<br>
									<div>
									ชื่อผู้พิการ: <input type="text" name="employee_name" id="employee_name">
									จังหวัด: <input type="text" name="job_province" id="job_province">
									<input type="button" value="ค้นหา" onClick="getJobList();">
									</div>
									<hr>

									<div id="job_result"></div>

<script>
function getJobList(){
	var employee_name = $("#employee_name").val();	
	var job_province = $("#job_province").val();
	$.ajax({
	 	method: "POST",
	 	url: "http://203.155.46.118/ajax_list_mutilator.php",
	 	data: { employee_name: employee_name, province_name: job_province }
 	})
	 .done(function( html ) {
				 //alert( "Data Saved: " + msg );
				 $( "#job_result" ).html( html);
	 });
}

getJobList();

</script>


</div><!--end page cell-->
</td>
</tr>
</table>

<script language="javascript">

function checkOrUncheck(){
	if(document.getElementById('chk_all').checked == true){
		checkAll();
	}else{
		uncheckAll();
	}
}

function checkAll(){
	<?php echo $js_do_check; ?>
}

function uncheckAll(){
	<?php echo $js_do_uncheck; ?>
}
</script>
</body>
</html>
