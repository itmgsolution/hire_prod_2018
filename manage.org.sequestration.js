/**
 * จัดการข้อมูล แท็บประวัติการดำเนินตามกฎหมาย ในหน้า organization.php
 */

var c2xOrgSeq = null;
(function () {
	var _manageUrl = "";	
	var _sequestrationTitle = "";
	var _sequestrationCss = "icon-locksequestration";
	var _cancelledSequestrationTitle = "";
	var _cancelledSequestrationCss = "icon-unlocksequestration";
	var _proceedingsAttachments = null;
	var _hireDocfileRelatePath = "";
	var _cid;
	var TASK_NAME = {
			"SaveCollectionCompany": 0, 
			"DelCollectionCompany": 1,
			
			"SaveNoticeDocument": 2, 
			"DelNoticeDocument": 3,				
			
			"DelSequestration": 4, 
			"SaveCancelledSequestration": 5, 
			"DelCancelledSequestration": 6,			
			
			"DelProceedings": 7,
			
			"CheckCanCreateCollection": 8, 
			"CheckCanCreateNotice": 9, 
			"CheckCanCreateSequest": 10,
			"CheckCanCreateProceeds": 11,};
	var C2XFunctions = function () {
    };
    
    C2XFunctions.prototype.config = function (config) {
    	_manageUrl = config.ManageUrl;
    	_sequestrationTitle = config.SequestrationTitle;
    	_cancelledSequestrationTitle = config.CancelledSequestrationTitle;
    	_hireDocfileRelatePath = config.HireDocfileRelatePath;
    	_cid = config.CID;
    	
    	var proceedingsAttachmentResult = config.ProceedingsAttachments;
    	if(proceedingsAttachmentResult.IsComplete){
    		_proceedingsAttachments = proceedingsAttachmentResult.Data;
    	}    	
    };
    
    C2XFunctions.prototype.saveCollectionCompany = function (ccid) {
    	var collectionReceiverNo = $("#collectionReceiverNo"+ccid).val();
    	collectionReceiverNo = $.trim(collectionReceiverNo);    	
    	
    	var collectionDatepicker = $("#collectionDatepicker"+ccid).data("kendoDatePicker");
    	var collectionDate = collectionDatepicker.value();   
    	var collectionDateFormat = (collectionDate == null)? null : kendo.toString(collectionDate, "yyyy-MM-dd");
    	
    	var collectionReceiver = $("#collectionReceiver"+ccid).val();
    	collectionReceiver = $.trim(collectionReceiver);
    	
    	var param = {
    			receiverno: collectionReceiverNo,
    			receiverdate: collectionDateFormat,
    			receiver:collectionReceiver,
    			task: TASK_NAME.SaveCollectionCompany,
    			id:ccid};
    	startRequest();
    	$.post(_manageUrl,param,  function(response) {   
		    if((typeof(response.IsComplete) != 'undefined') && (response.IsComplete == true)){
		    	showSuccessMessage("การแก้ไขข้อมูลเสร็จสิ้น");
		    }else if((typeof(response.Message) != 'undefined')){		    	
		    	 showErrorMessage(response.Message);
		    }	
		    endRequest();
		}, "json")			  
		  .fail(function() {
			  endRequest();
			  showErrorMessage("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");	    
		  });
    	return false;
    };
    
    C2XFunctions.prototype.deleteCollectionCompany = function (ccid) {  	
    	var isConfirm = confirm("คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือว่าเป็นการสิ้นสุดจะไม่สามารถเรียกข้อมูลกลับมาได้");
    	if(isConfirm){
    		var param = {    			
        			task: TASK_NAME.DelCollectionCompany,
        			id:ccid};
    		startRequest();
        	$.post(_manageUrl,param,  function(response) {    		
    		    if((typeof(response.IsComplete) != 'undefined') && (response.IsComplete == true)){
    		    	showSuccessMessage("การลบข้อมูลเสร็จสิ้น");
    		    	deleteCollectionCompanyRow(ccid);
    		    	checkCanCreateCollection(ccid, _cid);
    		    }else if((typeof(response.Message) != 'undefined')){
    		    	showErrorMessage(response.Message);
    		    	endRequest();
    		    }	
    		   
    		}, "json")			  
    		  .fail(function() {
    			  endRequest();
    			  showErrorMessage("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");    			      
    		  });
    	}
    	
    	return false;
    };
    
    C2XFunctions.prototype.saveNoticeDocument = function (noticeID) {
    	var noticeReceived = $("#noticeReceived"+noticeID).val();
    	noticeReceived = $.trim(noticeReceived);    	
    	
    	var noticeDatepicker = $("#noticeDatepicker"+noticeID).data("kendoDatePicker");
    	var noticeDate = noticeDatepicker.value();   
    	var noticeDateFormat = (noticeDate == null)? null : kendo.toString(noticeDate, "yyyy-MM-dd");
    	
    	var noticeRequestNo = $("#noticeRequestNo"+noticeID).val();
    	noticeRequestNo = $.trim(noticeRequestNo);  
    	
    	var param = {
    			received: noticeReceived,
    			receivedate: noticeDateFormat,    
    			requestno:noticeRequestNo,
    			task: TASK_NAME.SaveNoticeDocument,
    			id:noticeID};
    	startRequest();
    	$.post(_manageUrl,param,  function(response) {   
		    if((typeof(response.IsComplete) != 'undefined') && (response.IsComplete == true)){
		    	showSuccessMessage("การแก้ไขข้อมูลเสร็จสิ้น");
		    }else if((typeof(response.Message) != 'undefined')){		    	
		    	 showErrorMessage(response.Message);
		    }	
		    endRequest();
		}, "json")			  
		  .fail(function() {
			  endRequest();
			  showErrorMessage("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");	    
		  });
    	return false;
    };
    
    C2XFunctions.prototype.deleteNoticeDocument = function (noticeID, cid) {  	
    	var isConfirm = confirm("คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือว่าเป็นการสิ้นสุดจะไม่สามารถเรียกข้อมูลกลับมาได้");
    	if(isConfirm){
    		var param = {    			
        			task: TASK_NAME.DelNoticeDocument,
        			id:noticeID,
        			cid:cid};
    		startRequest();
        	$.post(_manageUrl,param,  function(response) {    		
    		    if((typeof(response.IsComplete) != 'undefined') && (response.IsComplete == true)){
    		    	showSuccessMessage("การลบข้อมูลเสร็จสิ้น");
    		    	deleteNoticeDocumentRow(noticeID);
    		    	checkCanCreateSequestration(TASK_NAME.CheckCanCreateNotice, noticeID, cid);
    		    }else if((typeof(response.Message) != 'undefined')){
    		    	showErrorMessage(response.Message);
    		    	endRequest();
    		    }	
    		    
    		}, "json")			  
    		  .fail(function() {
    			  endRequest();
    			  showErrorMessage("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");    			      
    		  });
    	}
    	
    	return false;
    };
    
    C2XFunctions.prototype.deleteSequestration = function (sid, cid) {  	
    	var isConfirm = confirm("คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือว่าเป็นการสิ้นสุดจะไม่สามารถเรียกข้อมูลกลับมาได้");
    	if(isConfirm){
    		var param = {    			
        			task: TASK_NAME.DelSequestration,
        			id:sid,
        			cid:cid};
    		startRequest();
        	$.post(_manageUrl,param,  function(response) {    		
    		    if((typeof(response.IsComplete) != 'undefined') && (response.IsComplete == true)){
    		    	showSuccessMessage("การลบข้อมูลเสร็จสิ้น");
    		    	deleteSequestrationRow(sid);
    		    	checkCanCreateSequestration(TASK_NAME.CheckCanCreateSequest, sid, cid);
    		    }else if((typeof(response.Message) != 'undefined')){
    		    	showErrorMessage(response.Message);
    		    	endRequest();
    		    }	
    		    
    		}, "json")			  
    		  .fail(function() {
    			  endRequest();
    			  showErrorMessage("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");    			      
    		  });
    	}
    	
    	return false;
    };
    
    C2XFunctions.prototype.deleteProceeds = function (pid, cid) {  	
    	var isConfirm = confirm("คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือว่าเป็นการสิ้นสุดจะไม่สามารถเรียกข้อมูลกลับมาได้");
    	if(isConfirm){
    		var param = {    			
        			task: TASK_NAME.DelProceedings,
        			id:pid,
        			cid:cid};
    		startRequest();
        	$.post(_manageUrl,param,  function(response) {    		
    		    if((typeof(response.IsComplete) != 'undefined') && (response.IsComplete == true)){
    		    	showSuccessMessage("การลบข้อมูลเสร็จสิ้น");
    		    	deleteProceedsRow(pid);
    		    	checkCanCreateSequestration(TASK_NAME.CheckCanCreateProceeds, pid, cid);
    		    }else if((typeof(response.Message) != 'undefined')){
    		    	showErrorMessage(response.Message);
    		    	endRequest();
    		    }	
    		    
    		}, "json")			  
    		  .fail(function() {
    			  endRequest();
    			  showErrorMessage("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");    			      
    		  });
    	}
    	
    	return false;
    };
    
    C2XFunctions.prototype.displayDeleteCancelSequestrationMessage = function (sid) { 
    	var messageBlock = $('div[class~="success-message"][message-for="sequestration-update"]');
    	$(messageBlock).text("การลบข้อมูลเสร็จสิ้น");
    	$(messageBlock).show();
    	
    	var alink = $("tr[row-id='"+ sid +"'][row-data='sequestration']").find("a[class~='"+ _cancelledSequestrationCss +"']");
    	if(alink != null){
    		$(alink).removeClass(_cancelledSequestrationCss);
    		$(alink).addClass(_sequestrationCss);
    		$(alink).attr("title", _sequestrationTitle);
    		$(alink).click(function(){
    			openCancelSequestration(sid);
    		});
    	} 
    	
    };
    
    C2XFunctions.prototype.updateSequestrationToUnlock = function (sid, csid) { 
    	var alink = $("tr[row-id='"+ sid +"'][row-data='sequestration']").find("a[class~='"+ _sequestrationCss +"']");
    	if(alink != null){
    		$(alink).removeClass(_sequestrationCss);
    		$(alink).addClass(_cancelledSequestrationCss);
    		$(alink).attr("title", _cancelledSequestrationTitle);
    		$(alink).click(function(){
    			openCancelSequestration(sid, csid);
    		});
    	}    	
    };
    
    C2XFunctions.prototype.openProceedAttachment = function (pid) { 
    	var govDocumentNo = $("a[link-id='ProceedingsLink"+ pid +"']").text();
    	$("#ProceedingsRequestNo").text(govDocumentNo);
    	
    	if(_proceedingsAttachments != null){
    		var newAttach = jQuery.grep(_proceedingsAttachments, function(item) {
    			  return ( item.PID == pid);
    		});
    		
    		var a, url, item;
    		var container = $("#ProceedingsAttachment");
    		$(container).empty();
    		//<a href="<?php echo "$path_to_use/".$post_row["file_name"];?>" target="_blank"><?php echo getAttachFileName($post_row["file_name"]);?></a> 
    		for(var i = 0; i < newAttach.length; i++){
    			item = newAttach[i];
    			url = _hireDocfileRelatePath + "/" + item.FileName;
    			a = document.createElement("a");
    			a.setAttribute("href", url);
    			a.setAttribute("target", "_blank");
    			a.setAttribute("class", "file-line");
    			a.innerHTML = item.FileName;
    			container.append(a);
    			
    		}    		
    	}
    };
    
    
    function deleteCollectionCompanyRow(ccid){
    	$('tr[row-id="'+ ccid +'"][row-data="collectioncompany"]').remove();    	
    }
    
    function deleteNoticeDocumentRow(noticeID){    
    	$('tr[row-id="'+ noticeID +'"][row-data="noticedocument"]').remove(); 
    }
    
    function deleteSequestrationRow(sid){    
    	$('tr[row-id="'+ sid +'"][row-data="sequestration"]').remove(); 
    }
    
    function deleteProceedsRow(pid){    
    	$('tr[row-id="'+ pid +'"][row-data="proceeds"]').remove(); 
    }
    
    function checkCanCreateSequestration(task, id, cid){
    	var param = {    			
    			task: task, 
    			id: id,
    			cid:cid};		
		
    	$.post(_manageUrl,param,  function(response) {    		
		    if((typeof(response.IsComplete) != 'undefined') && (response.IsComplete == true)){	
		    	
		    	
		    	var link = null;
		    	if(task == TASK_NAME.CheckCanCreateNotice){
		    		link = $("#LinkCreateNotice");
		    	}else if(task == TASK_NAME.CheckCanCreateSequest){
		    		link = $("#LinkCreateSequest");
		    	}else if(task == TASK_NAME.CheckCanCreateProceeds){
		    		link = $("#LinkCreateProceeds");
		    	}
		    	
		    	var year = response.Data;	
		    	if(link != null){
		    		if(year != null){
			    		$(link).removeClass("hide-block").addClass("show-line");
			    	}else{
			    		$(link).removeClass("show-block").addClass("hide-line");
			    	}
		    	}
		    	
		    	
		    }else if((typeof(response.Message) != 'undefined')){
		    	showErrorMessage(response.Message);
		    }	
		    endRequest();
		}, "json")			  
		  .fail(function() {
			  endRequest();
			  showErrorMessage("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");    			      
		  });
    }
    
    function checkCanCreateCollection(ccid, cid){
    	var param = {    			
    			task: TASK_NAME.CheckCanCreateCollection, 
    			id: ccid,
    			cid:cid};		
		
    	$.post(_manageUrl,param,  function(response) {    		
		    if((typeof(response.IsComplete) != 'undefined') && (response.IsComplete == true)){		    	
		    	var year = response.Data;
		    	if(year != null){
		    		var href = $("#LinkCreateCollection").attr("href");
		    		var yearStartIndex = href.indexOf("for_year");
		    		href = href.substring(0, yearStartIndex);
		    		href = href + 'for_year=' + year;
		    		$("#LinkCreateCollection").removeClass("hide-block").addClass("show-line");
		    		$("#LinkCreateCollection").attr("href", href);
		    		console.log(href);
		    	}else{
		    		$("#LinkCreateCollection").removeClass("show-block").addClass("hide-line");
		    	}
		    	
		    }else if((typeof(response.Message) != 'undefined')){
		    	showErrorMessage(response.Message);
		    }	
		    endRequest();
		}, "json")			  
		  .fail(function() {
			  endRequest();
			  showErrorMessage("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");    			      
		  });
    }
    
    function showSuccessMessage(message){
    	var messageBlock = $('div[message-for="sequestration-update"]').get(0);
    	$(messageBlock).text(message);
    	$(messageBlock).show();
    }
      
    function showErrorMessage(message){
    	var messageBlock = $('div[message-for="sequestration-error"]').get(0);
    	$(messageBlock).text(message);
    	$(messageBlock).show();
    }
    
    function clearMessage(){
    	var updateBlock = $('div[message-for="sequestration-update"]').get(0);
    	var errorBlock = $('div[message-for="sequestration-error"]').get(0);
    	
    	$(updateBlock).hide();
    	$(errorBlock).hide();
    }
    
    function startRequest(){
    	clearMessage();
    	c2x.showLoader();
    }
    
    function endRequest(){
    	c2x.closeLoader();
    }
    
    c2xOrgSeq = new C2XFunctions();
})();