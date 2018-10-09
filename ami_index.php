
<html>
<head>
	<script type="text/javascript" src="../jquery-1.11.1.min.js"></script>
</head>
<body>
										หมายเลขบัตรประชาชนที่ต้องการข้อมูล: <input type="text" name="personal_id" id="personal_id" maxlength="13"><input type="button" value="ดึงข้อมูล" id="readData" onClick="readData();">
										<table width="500px" >
											<tr><td bgcolor="#efefef">ข้อมูลที่ได้รับ</td><td><textarea id="ret_data" style="width: 300px; height: 200px"></textarea></td></tr>
										</table>
<script>
function readData() {
	  var PID = $("#personal_id").val();
		console.log(PID);
		$.ajax({
			url: "http://localhost:8000/SearchByPID/"+PID,
			type: 'GET',
			success: function(data,status){
	  			if(data.return_code == 0) {
						 $("#ret_data").val(JSON.stringify(data));
							console.log(data);
	  			} else {
						console.log(data);
					}
			},
			error: function(){
				alert("Error :: AmiBridge is running?");
			}
		});
}
</script>
</body>
</html>
