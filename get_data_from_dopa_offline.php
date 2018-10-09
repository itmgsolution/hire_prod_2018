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

                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >ดึงข้อมูลจากบัตรประชาชน </h2>
										<i>** เสียบบัตรประชาชนที่เครื่องอ่านบัตร จากนั้นกด "อ่านข้อมูล"</i>


										<script type="text/javascript" src="js_ami.php"></script>
										<br/><input type="button" value="อ่านข้อมูล" id="readData" onClick="readData();"><br><br>
										<table>
											<tr><td bgcolor="#efefef">เลขประจำตัวประชาชน</td><td><span id="personal_id"></span></td></tr>
											<tr><td bgcolor="#efefef">ชื่อ-นามสกุล</td><td><span id="full_name"></span></td></tr>
											<tr><td bgcolor="#efefef">วัน เดือน ปีเกิด</td><td><span id="birth_date"></span></td></tr>
											<tr><td bgcolor="#efefef">เพศ</td><td><span id="gender"></span></td></tr>
											<tr><td bgcolor="#efefef">วัน เดือน ปีที่ออกบัตร</td><td><span id="issue_date"></span></td></tr>
											<tr><td bgcolor="#efefef">วัน เดือน ปีที่บัตรหมดอายุ</td><td><span id="expire_date"></span></td></tr>
											<tr><td bgcolor="#efefef">ที่อยู่ตามหน้าบัตร</td><td><span id="address"></span></td></tr>
										</table>
<script>
var listCheckReader = [
	{	func: "ListReader",		title: "ตรวจสอบเครื่องอ่านบัตร", return2: null},
	{	func: "OpenReader",		title: "ติดต่อเครื่องอ่านบัตร", return2: null},
	{	func: "SelectApplet",		title: "ติดต่อเครื่องอ่านบัตร", return2: null},
	{	func: "ReadData/"+objCardData.CardData[2].offset+"/"+objCardData.CardData[2].size,		title: "เลขประจำตัวประชาชน", return2: "personal_id"},
	{	func: "ReadData/"+objCardData.CardData[3].offset+"/"+objCardData.CardData[3].size,		title: "ชื่อ-นามสกุล", return2: "full_name"},
	{	func: "ReadData/"+objCardData.CardData[5].offset+"/"+objCardData.CardData[5].size,		title: "วัน เดือน ปีเกิด", return2: "birth_date"},
	{	func: "ReadData/"+objCardData.CardData[6].offset+"/"+objCardData.CardData[6].size,		title: "เพศ", return2: "gender"},
	{	func: "ReadData/"+objCardData.CardData[10].offset+"/"+objCardData.CardData[10].size,		title: "วัน เดือน ปีที่ออกบัตร", return2: "issue_date"},
	{	func: "ReadData/"+objCardData.CardData[11].offset+"/"+objCardData.CardData[11].size,		title: "วัน เดือน ปีที่บัตรหมดอายุ", return2: "expire_date"},
	{	func: "ReadData/"+objCardData.CardData[14].offset+"/"+objCardData.CardData[14].size,		title: "ที่อยู่ตามหน้าบัตรุ", return2: "address"},
	{	func: "CloseReader",		title: "Close Reader", return2: null},
];

	function readData(isDone=false){
			$("#dialog-message").dialog("open");
				doAMIMulti(listCheckReader);
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
