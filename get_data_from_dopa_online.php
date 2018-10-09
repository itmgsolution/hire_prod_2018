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
<!--
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >ดึงข้อมูลจากกรมการปกครอง </h2>
										<i>เสียบบัตรของเจ้าหน้าที่ที่จะทำการดึงข้อมูล จากนั้นกด "ตรวจสอบสิทธิ์ผู้ขอข้อมูล" ระบบจะสอบถาม PIN <br>ถ้า PIN ถูกต้องจะสามารดึงข้อมูลจากหมายเลขบัตรประชาชนที่ต้องการได้</i>
										<br>
										<script type="text/javascript" src="js_ami.php"></script>
										<br/><input type="button" value="ตรวจสอบสิทธิ์ผู้ขอข้อมูล" id="verifyUser" onClick="verifyUser();"> <span id="agent_id"></span><br><br>
-->										
										
										หมายเลขบัตรประชาชนที่ต้องการข้อมูล: <input type="text" name="personal_id" id="personal_id" maxlength="13"><input type="button" value="ดึงข้อมูล" id="readData" onClick="readData();">
										<table width="500px" >
											<tr><td bgcolor="#efefef">ข้อมูลที่ได้รับ</td><td><textarea id="ret_data" style="width: 300px; height: 200px"></textarea></td></tr>
										</table>
<script>
	var listCheckReader = [
		{	func: "ListReader",		title: "ตรวจสอบเครื่องอ่านบัตร", return2: null},
		{	func: "OpenReader",		title: "ติดต่อเครื่องอ่านบัตร", return2: null},
		{	func: "GetPID",		title: "ตรวจสอบข้อมูลเจ้าหน้าที่", return2: null},
		{	func: "GetCID",		title: "ตรวจสอบข้อมูลเจ้าหน้าที่", return2: null},
		{	func: "RequestRandom",		title: "ตรวจสอบข้อมูลเจ้าหน้าที่", return2: null},
		{	func: "GetAuthorize",		title: "ตรวจสอบข้อมูลเจ้าหน้าที่", return2: null},
		{	func: "VerifyPIN",		title: "ตรวจสอบข้อมูลเจ้าหน้าที่", return2: null},
		{	func: "GetMatchStatus",		title: "ตรวจสอบข้อมูลเจ้าหน้าที่", return2: null},
		{	func: "InternalAuthen",		title: "ตรวจสอบข้อมูลเจ้าหน้าที่", return2: null},
		{	func: "CloseReader",		title: "ตรวจสอบข้อมูลเจ้าหน้าที่", return2: null},
		{	func: "SearchByPID/"+$("#personal_id").val(),		title: "ดึงข้อมูลจากกรมการปกครอง", return2: "ret_data"},
	];

	return_code = 0;
	//$( "#readData" ).prop( "disabled", true );
	function verifyUser(isDone=false){
			$("#dialog-message").dialog("open");
				doAMIMulti(listCheckReader,getSuccess, getError);
	}

	function readData() {
			$("#dialog-message").dialog("open");
			if(return_code == 0)
				doAMI("SearchByPID/"+$("#personal_id").val(),"ดึงข้อมูลจากกรมการปกครอง","ret_data");
			else
				doAMIMulti(listCheckReader,getSuccess, getError);
	}

	function getSuccess() {
		//$("#agent_id").html("<font color='green'>สามารถดึกข้อมูลได้</font>");
		return_code = 0;
	}

	function getError() {
		//$("#agent_id").html("<font color='red'>ไม่มีสิทธิ์ในการเข้าถึงข้อมูล</font>");
		return_code = 9999;
	}

	$( function() {
		$( "#dialog-message" ).dialog({
			modal: true,
			autoOpen: false,
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	} );

</script>
<div id="dialog-message" title="ระบบกำลังทำการอ่านข้อมูล">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 100px 0;"></span>
    <span id="info_status"></span>
  </p>
  <p><span id="info_msg"></span></p>
</div>


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
