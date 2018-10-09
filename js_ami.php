<?php header("Content-type: application/x-javascript; charset=utf-8"); ?>
// Ofline Data
var CardData = '{"CardData": [' +
	'{"id":0,"detail":"",				"offset":0,		"size":0},'+
	'{"id":1,"detail":"Version",				"offset":0,		"size":4},'+
	'{"id":2,"detail":"เลขประจำตัวประชาชน",			"offset":4,		"size":13},'+
	'{"id":3,"detail":"ชื่อ-นามสกุล",				"offset":17,	"size":100},'+
	'{"id":4,"detail":"ชื่อ-นามสกุล (Eng)",			"offset":117,	"size":100},'+
	'{"id":5,"detail":"วัน เดือน ปีเกิด",				"offset":217,	"size":8},'+
	'{"id":6,"detail":"เพศ",						"offset":225,	"size":1},'+
	'{"id":7,"detail":"หมายเลข บัตร/คำร้อง",			"offset":226,	"size":20},'+
	'{"id":8,"detail":"สถานที่/หน่วยงานที่ออกบัตร",		"offset":246,	"size":100},'+
	'{"id":9,"detail":"รหัสผู้ออกบัตร (Issuer code)",	"offset":346,	"size":13},'+
	'{"id":10,"detail":"วัน เดือน ปีที่ออกบัตร",			"offset":359,	"size":8},'+
	'{"id":11,"detail":"วัน เดือน ปีที่บัตรหมดอายุ",		"offset":367,	"size":8},'+
	'{"id":12,"detail":"รหัสประเภทบัตร",				"offset":375,	"size":2},'+
	'{"id":13,"detail":"ภาพใบหน้า",				"offset":377,	"size":5120},'+
	'{"id":14,"detail":"ที่อยู่ตามหน้าบัตร",			"offset":5497,	"size":160},'+
	'{"id":15,"detail":"เลขรหัสกำกับใต้รูป",			"offset":5657,	"size":14},'+
	'{"id":16,"detail":"ลายเซ็นนายทะเบียน บัตรประจำตัวประชาชน","offset":5671,"size":256}]}';

var objCardData = JSON.parse(CardData);
var offline_offset = 0;
var offline_size = 0;
var return_code = 0;
var reader_status = false;

function doAMI(cmd,info_status,return2) {
	  $("#info_status").text(info_status);
		return $.ajax({
			url: "http://localhost:8000/"+cmd,
			type: 'GET',
			success: function(data,status){
	        return_code = data.return_code;
	  			if(data.return_code == 0) {
	  				$("#info_msg").text(data.msg);
	  			} else {
	  				$("#info_msg").text("Error: " + data.msg);
	  			}

	  			if(data.return_data) {
	  				if(offline_offset == 377 || offline_offset == 5671) {
	  					$("#"+return2).html('<img src="data:image/jpg;base64,'+data.return_data+'"></img>');
	  				} else {
	  					$("#"+return2).html(data.return_data);
	  				}
	  			}
			},
			error: function(){
				$("#info_msg").html("<div class=\"alert alert-danger\" role=\"alert\">Error :: AmiBridge is running?</div>");
			}
		});
}

function doAMIMulti(f, getSuccess = function() {}, getError = function() {}){
	var isSuccess = true;
	looper = $.Deferred().resolve();
	$.each(f, function(i, data) {
		looper = looper.then(function() {
			if(!isSuccess){
				getError(); return;
			}
			else
			return doAMI(data.func, data.title, data.return2).done(function(resp){
				//console.log(resp);
				if(resp.return_code != 0) isSuccess = false;
				getSuccess();
			});

		});

	});
}


$(document).ready(function() {
    //

	$("#btn1").click(function() { doAMI("ListReader"); });
	$("#btn2").click(function() { doAMI("OpenReader"); });
	$("#btn3").click(function() { doAMI("GetPID"); });
	$("#btn4").click(function() { doAMI("GetCID"); });
	$("#btn5").click(function() { doAMI("RequestRandom"); });
	$("#btn6").click(function() { doAMI("GetAuthorize"); });
	$("#btn7").click(function() { doAMI("VerifyPIN"); });
	$("#btn8").click(function() { doAMI("GetMatchStatus"); });
	$("#btn9").click(function() { doAMI("InternalAuthen"); });
	$("#btn10").click(function() { doAMI("CloseReader"); });
	$("#btn13").click(function() { doAMI("SearchByPID/"+$("#txtPID").val()); });

	//Offline
	$("#btn11").click(function() { doAMI("SelectApplet"); });
	$("#btn12").click(function() { doAMI("ReadData/"+offline_offset+"/"+offline_size); });

	$("#listBox").change(function() {
		var id = $(this).val();
		offline_offset = objCardData.CardData[id].offset;
		offline_size = objCardData.CardData[id].size;
		$("#infoBox").html("Offset: "+offline_offset+", Size: "+offline_size);

	});

	var opt = "";
	for(var i=1, len=objCardData.CardData.length;i < len; i++) {
		opt += ('<option value="'+objCardData.CardData[i].id+'">'+objCardData.CardData[i].detail+'</option>');
		//console.log(opt);
	}
	$("#listBox").append(opt);



});
