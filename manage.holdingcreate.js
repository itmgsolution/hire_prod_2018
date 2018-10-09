/**
 * For Manage Holding Create Page
 */
var c2xHolding = null;
(function () {
	var _interestRate;
	var _theCompanyWord;
	var _sequestrationType;
	var C2XFunctions = function () {
    };
    
    C2XFunctions.prototype.config = function (config) {
    	_interestRate = config.InterestRate;
    	_theCompanyWord = config.TheCompanyWord;
    	_sequestrationType = config.SequestrationType;
    };
    
    C2XFunctions.prototype.bindOrgDebtFrom = function (objSequester) {
    	// bind company detail
    	var cid = $("#HiddCID").val();
    	var companyID, companyCode, branchCode, calDate;
    	var dataItems = objSequester.SequesterPayments;
    	var textJson = "";
    	if((typeof(cid) != 'undefined') && (cid != "") && dataItems != null){    
    		
    		companyID = cid;
    		companyCode = $("#HiddCompanyCode").val();
    		branchCode = $("#HiddBranchCode").val();
    		calDate = $("#DocumentDate").val();
    		
    		renderOrgDebtData(dataItems);
    		reCheckOrgSelected(companyID);
    		//reSelecedDate(calDate);
    	}
    	
    	// bind sequestation detail 
    	dataItems = objSequester.SequesterTypeMoney; 	
    	dataItems = bindSequestrationDetailUID(dataItems);
    	if(dataItems != null){
    		$("#ChkSequesterTypeMoney").get(0).checked = true;
    		textJson = kendo.stringify(dataItems);
    		$("#HiddSequesterTypeMoneyData").val(textJson);  
    		$("#SequesterTypeMoneyContainer").show();
    		renderSequesterTypeMoneyGrid();    		
    	}
    	
    	dataItems = objSequester.SequesterTypeProperty; 	
    	dataItems = bindSequestrationDetailUID(dataItems);
    	if(dataItems != null){
    		$("#ChkSequesterTypeProperty").get(0).checked = true;
    		textJson = kendo.stringify(dataItems);
    		$("#HiddSequesterTypePropertyData").val(textJson);  
    		$("#SequesterTypePropertyContainer").show();
    		renderSequesterTypePropertyGrid();    		
    	} 
    	
    	dataItems = objSequester.SequesterTypeCar; 	
    	dataItems = bindSequestrationDetailUID(dataItems);
    	if(dataItems != null){
    		$("#ChkSequesterTypeCar").get(0).checked = true;
    		textJson = kendo.stringify(dataItems);
    		$("#HiddSequesterTypeCarData").val(textJson);  
    		$("#SequesterTypeCarContainer").show();
    		renderSequesterTypeCarGrid();    		
    	}    
    	
    	dataItems = objSequester.SequesterTypeOther; 	
    	dataItems = bindSequestrationDetailUID(dataItems);
    	if(dataItems != null){
    		$("#ChkSequesterTypeOther").get(0).checked = true;
    		textJson = kendo.stringify(dataItems);
    		$("#HiddSequesterTypeOtherData").val(textJson);  
    		$("#SequesterTypeOtherContainer").show();
    		renderSequesterTypeOtherGrid();    		
    	}    
    	
    };    
    
    
    C2XFunctions.prototype.setTask = function (taskName) {
    	$("#HiddTaskName").val(taskName);
    	return true;
    };
    
    C2XFunctions.prototype.handleSelecDate = function(){
		$("#DocumentDate_day").change(function(e){			
			c2xHolding.loadOrgDebt();
		});

		$("#DocumentDate_month").change(function(e){
			c2xHolding.loadOrgDebt();
		});

		$("#DocumentDate_year").change(function(e){
			c2xHolding.loadOrgDebt();
		});
	};
    
    C2XFunctions.prototype.onSelectedOrg = function(el){
		var isChecked = el.checked;
		
		if(isChecked){
			$(".org-checkbox").each(function(){
				this.checked = false;
			});	
			el.checked = true;	

			c2xHolding.loadOrgDebt();
		}else{
			c2xHolding.clearOrgDebt();
	    }
	};
		
	C2XFunctions.prototype.loadOrgDebt = function(obj){		
		clearMessage();
		var param = ((typeof(obj) != 'undefined') && (obj != null))? obj : getOrgDebtParam();
		c2xHolding.clearOrgDebt();

		if(param != null){
			$("#HiddCID").val(param.companyid);
			$("#HiddCompanyCode").val(param.companycode);
			$("#HiddBranchCode").val(param.branchcode);
			$("#NoticeDate").val(param.noticedate);
			$("#DocumentDate").val(param.caldate);
			
			$.post( "scrp_load_org_debt.php",param,  function(response) {
			    if((typeof(response.Status) != 'undefined') && (response.Status == 1)){
			    	renderOrgDebtData(response.Data);
			    }else if((typeof(response.Message) != 'undefined')){
			    	 alert( response.Message);
			    }				
			}, "json")			  
			  .fail(function() {
			    alert( "เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง" );
			  });
		}
		
		
		return false;
	};
	
	C2XFunctions.prototype.clearOrgDebt = function(){
		clearMessage();
		var container = $("#OrgDebtBlock");
		container.empty();		
		
		$("#HiddCID").val("");
		$("#HiddCompanyCode").val("");
		$("#HiddBranchCode").val("");
		$("#NoticeDate").val("");
		$("#DocumentDate").val("");
		$("#TotalAmount").val("");
		$("#SequestrationPayments").val("");
	};
	
	C2XFunctions.prototype.showSequesterType = function(el, typeName){
		var containerID = '';
		if(typeName == 'money'){
			containerID = "SequesterTypeMoneyContainer" ;			
		}else if(typeName == 'property'){
			containerID = "SequesterTypePropertyContainer";
		}else if(typeName == 'car'){
			containerID = "SequesterTypeCarContainer";
		}else{
			containerID = "SequesterTypeOtherContainer";
		}
		
		if(el.checked){
			$("#" + containerID).show();
		}else{
			$("#" + containerID).hide();
		}
	};

	C2XFunctions.prototype.saveHolding = function(){	
		clearMessage();
		var isValid = validateHolding();
		if(isValid){
			c2xHolding.setTask('save');
		}
		return isValid;
	};
	
	C2XFunctions.prototype.deleteHolding = function(){
		clearMessage();
		var isConfirm = confirm("ยืนยันการลบข้อมูล");
		if(isConfirm){
			c2xHolding.setTask('delete');
			$("form").submit();	
		}
		
		return isConfirm;
	}
	
	C2XFunctions.prototype.saveSequesterTypeMoney = function(){		
		clearMessage();
		var isValid = validateSequesterTypeMoney();
		var accountNo, accountType, accountTypeName, checkBank, bankName, branchName, checkBankName, uid;
		var dataItems, tempData, item;
		var gridID = "SequesterTypeMoneyGrid";
		var hiddID = "HiddSequesterTypeMoneyData";
		if(isValid){
			accountNo = $("#AccountNo").val();
			accountNo = $.trim(accountNo);
			
			accountType = $("#AccountType").val();
			accountTypeName = $("#AccountType option:selected").text();
			
			checkBank = $("select[name='check_bank']").val();	
			bankName = $("select[name='check_bank']  option:selected").text();	
			
			branchName = $("#BankBranch").val();
			branchName = $.trim(branchName);
			
			uid = $("#HiddSequesterTypeMoneyUID").val();
			
			tempData = $("#HiddSequesterTypeMoneyData").val();
			dataItems = (tempData != "")? $.parseJSON(tempData) : [];
			
			var item = {
					UID: "",
					SequestrationType: _sequestrationType.Money,
					DocumentNo: accountNo,
					AccountType: accountType,
					AccountTypeName: accountTypeName,
					BankID: checkBank,
					BankName: bankName,
					BankBranchName: branchName,
					SubDistrictCode: "",
					SubDistrictName: "",
					DistrictCode: "",
					DistrictName: "",
					ProvinceCode: "",
					ProvinceName: "",
					CarYear: 0,
					Other:""
				};
			
			if(uid != ""){
				item.UID = uid;				
				for(var i = 0; i < dataItems.length; i++){
					if(dataItems[i].UID == uid){
						dataItems[i] = item;
						break;
					}
				}				
				
			}else{
				item.UID = c2x.guid();
				dataItems.push(item);
			}
			
			var textJson = kendo.stringify(dataItems);
			$("#HiddSequesterTypeMoneyData").val(textJson);
			renderSequesterTypeMoneyGrid();
			c2xHolding.clearSequesterTypeMoneyForm();
		}
		return isValid;
	};
	
	C2XFunctions.prototype.editSequesterTypeMoney = function(uid){	
		$("#SequesterTypeMoneyGrid tr[uid='"+uid+"']").addClass('edit-row');
		var textJson = $("#HiddSequesterTypeMoneyData").val();
		var dataItems =  $.parseJSON(textJson);
		var editItem = null;
		
		for(var i = 0; i < dataItems.length; i++){
			if(dataItems[i].UID == uid){
				editItem = dataItems[i];
				break;
			}
		}	
		
		$("#AccountNo").val(editItem.DocumentNo);
		$("#AccountNo").focus();
		
		$("#AccountType").val(editItem.AccountType);
		
		$("select[name='check_bank']").val(editItem.BankID);
		$("#HiddSequesterTypeMoneyUID").val(uid);
		
		$("#BankBranch").val(editItem.BankBranchName);
		
	};
	
	C2XFunctions.prototype.delSequesterTypeMoney = function(uid){	
		$("#SequesterTypeMoneyGrid tr[uid='"+uid+"']").remove();
		var textJson = $("#HiddSequesterTypeMoneyData").val();
		var dataItems =  $.parseJSON(textJson);
		dataItems = $.grep(dataItems, function( item ) {
			  return item.UID !== uid;
		});
		
		if(dataItems.length > 0){
			textJson = kendo.stringify(dataItems);
		}else{
			textJson = "";
			$("#SequesterTypeMoneyGrid").hide();
		}
		
		
		$("#HiddSequesterTypeMoneyData").val(textJson);
	};
	
	C2XFunctions.prototype.saveSequesterTypeProperty = function(){		
		clearMessage();
		var isValid = validateSequesterTypeProperty();
		var documentNo, subDistrictCode, subDistrictName, districtCode, districtName, provinceCode, provinceName;
		
		if(isValid){
			documentNo = $("#DocumentNo").val();
			documentNo = $.trim(documentNo);
			
			subDistrictCode = $("#DdlSubDistrict").val();
			subDistrictName = $("#DdlSubDistrict option:selected").text();
			
			districtCode = $("#DdlDistrict").val();
			districtName = $("#DdlDistrict option:selected").text();
			
			provinceCode = $("#DdlProvince").val();
			provinceName = $("#DdlProvince option:selected").text();
						
			uid = $("#HiddSequesterTypePropertyUID").val();
			
			tempData = $("#HiddSequesterTypePropertyData").val();
			dataItems = (tempData != "")? $.parseJSON(tempData) : [];
			
			var item = {
					UID: "",
					SequestrationType: _sequestrationType.Property,
					DocumentNo: documentNo,
					AccountType: "",
					AccountTypeName: "",
					BankID: "",
					BankName: "",
					BankBranchName: "",
					SubDistrictCode: subDistrictCode,
					SubDistrictName: subDistrictName,
					DistrictCode: districtCode,
					DistrictName: districtName,
					ProvinceCode: provinceCode,
					ProvinceName: provinceName,
					CarYear: "",
					Other:""
				};
			
			if(uid != ""){
				item.UID = uid;				
				for(var i = 0; i < dataItems.length; i++){
					if(dataItems[i].UID == uid){
						dataItems[i] = item;
						break;
					}
				}	
				
			}else{
				item.UID = c2x.guid();
				dataItems.push(item);
			}
			
			var textJson = kendo.stringify(dataItems);
			$("#HiddSequesterTypePropertyData").val(textJson);
			renderSequesterTypePropertyGrid();
			c2xHolding.clearSequesterTypePropertyForm();
		}
		return isValid;
	};
	
	C2XFunctions.prototype.editSequesterTypeProperty = function(uid){	
		$("#SequesterTypePropertyGrid tr[uid='"+uid+"']").addClass('edit-row');
		var textJson = $("#HiddSequesterTypePropertyData").val();
		var dataItems =  $.parseJSON(textJson);
		var editItem = null;
		
		for(var i = 0; i < dataItems.length; i++){
			if(dataItems[i].UID == uid){
				editItem = dataItems[i];
				break;
			}
		}	
		
		$("#DocumentNo").val(editItem.DocumentNo);
		$("#DocumentNo").focus();
		
		$("#DdlProvince").val(editItem.ProvinceCode);	
		c2x.loadDdlCascadeProvince('district', editItem.DistrictCode, 'DdlProvince', 'DdlDistrict', 'DdlSubDistrict');	
		c2x.loadDdlCascadeSubDistrict(editItem.SubDistrictCode, editItem.DistrictCode, editItem.ProvinceCode, 'DdlDistrict', 'DdlSubDistrict');
				
	
		$("#HiddSequesterTypePropertyUID").val(uid);
	};
	
	C2XFunctions.prototype.delSequesterTypeProperty = function(uid){	
		$("#SequesterTypePropertyGrid tr[uid='"+uid+"']").remove();
		var textJson = $("#HiddSequesterTypePropertyData").val();
		var dataItems =  $.parseJSON(textJson);
		dataItems = $.grep(dataItems, function( item ) {
			  return item.UID !== uid;
		});
		
		if(dataItems.length > 0){
			textJson = kendo.stringify(dataItems);
		}else{
			textJson = "";
			$("#SequesterTypePropertyGrid").hide();
		}		
		
		$("#HiddSequesterTypePropertyData").val(textJson);
	};
	
	C2XFunctions.prototype.saveSequesterTypeCar = function(){		
		clearMessage();
		var isValid = validateSequesterTypeCar();
		var carNo, carYear, uid;
		var dataItems, tempData, item;
		var gridID = "SequesterTypeCarGrid";
		var hiddID = "HiddSequesterTypeCarData";
		if(isValid){
			carNo = $("#CarNo").val();
			carNo = $.trim(carNo);
			
			var carYearPicker =  $("#CarYear").data("kendoDatePicker");
			var selectedYear = carYearPicker.value();
			carYear = kendo.toString(selectedYear, "yyyy");
			carYear = (parseInt(carYear, 10) - 543);
				
			
			uid = $("#HiddSequesterTypeCarUID").val();
			
			tempData = $("#HiddSequesterTypeCarData").val();
			dataItems = (tempData != "")? $.parseJSON(tempData) : [];
			
			var item = {
					UID: "",
					SequestrationType: _sequestrationType.Car,
					DocumentNo: carNo,
					AccountType: "",
					AccountTypeName: "",
					BankID: "",
					BankName: "",
					BankBranchName: "",
					SubDistrictCode: "",
					SubDistrictName: "",
					DistrictCode: "",
					DistrictName: "",
					ProvinceCode: "",
					ProvinceName: "",
					CarYear: carYear,
					Other:""
				};
			
			if(uid != ""){
				item.UID = uid;				
				for(var i = 0; i < dataItems.length; i++){
					if(dataItems[i].UID == uid){
						dataItems[i] = item;
						break;
					}
				}				
				
			}else{
				item.UID = c2x.guid();
				dataItems.push(item);
			}
			
			var textJson = kendo.stringify(dataItems);
			$("#HiddSequesterTypeCarData").val(textJson);
			renderSequesterTypeCarGrid();
			c2xHolding.clearSequesterTypeCarForm();
		}
		return isValid;
	};
	
	C2XFunctions.prototype.editSequesterTypeCar = function(uid){	
		$("#SequesterTypeCarGrid tr[uid='"+uid+"']").addClass('edit-row');
		var textJson = $("#HiddSequesterTypeCarData").val();
		var dataItems =  $.parseJSON(textJson);
		var editItem = null;
		
		for(var i = 0; i < dataItems.length; i++){
			if(dataItems[i].UID == uid){
				editItem = dataItems[i];
				break;
			}
		}	
		
		$("#CarNo").val(editItem.DocumentNo);
		$("#CarNo").focus();
		
		var selectedYear = new Date(editItem.CarYear,0,1,0,0,0,0);
		var carYearPicker =  $("#CarYear").data("kendoDatePicker");
		carYearPicker.value(selectedYear);		
		
		$("#HiddSequesterTypeCarUID").val(uid);
		
	};
	
	C2XFunctions.prototype.delSequesterTypeCar = function(uid){	
		$("#SequesterTypeCarGrid tr[uid='"+uid+"']").remove();
		var textJson = $("#HiddSequesterTypeCarData").val();
		var dataItems =  $.parseJSON(textJson);
		dataItems = $.grep(dataItems, function( item ) {
			  return item.UID !== uid;
		});
		
		if(dataItems.length > 0){
			textJson = kendo.stringify(dataItems);
		}else{
			textJson = "";
			$("#SequesterTypeCarGrid").hide();
		}
		
		
		$("#HiddSequesterTypeCarData").val(textJson);
	};	
	
	C2XFunctions.prototype.saveSequesterTypeOther = function(){		
		clearMessage();
		var isValid = validateSequesterTypeOther();
		var other, uid;
		var dataItems, tempData, item;
		var gridID = "SequesterTypeOtherGrid";
		var hiddID = "HiddSequesterTypeOtherData";
		if(isValid){
			other = $("#Other").val();
			other = $.trim(other);	
			
			
			uid = $("#HiddSequesterTypeOtherUID").val();
			
			tempData = $("#HiddSequesterTypeOtherData").val();
			dataItems = (tempData != "")? $.parseJSON(tempData) : [];
			
			var item = {
					UID: "",
					SequestrationType: _sequestrationType.Other,
					DocumentNo: "",
					AccountType: "",
					AccountTypeName: "",
					BankID: "",
					BankName: "",
					BankBranchName: "",
					SubDistrictCode: "",
					SubDistrictName: "",
					DistrictCode: "",
					DistrictName: "",
					ProvinceCode: "",
					ProvinceName: "",
					CarYear: "",
					Other:other
				};
			
			if(uid != ""){
				item.UID = uid;				
				for(var i = 0; i < dataItems.length; i++){
					if(dataItems[i].UID == uid){
						dataItems[i] = item;
						break;
					}
				}				
				
			}else{
				item.UID = c2x.guid();
				dataItems.push(item);
			}
			
			var textJson = kendo.stringify(dataItems);
			$("#HiddSequesterTypeOtherData").val(textJson);
			renderSequesterTypeOtherGrid();
			c2xHolding.clearSequesterTypeOtherForm();
		}
		return isValid;
	};
	
	C2XFunctions.prototype.editSequesterTypeOther = function(uid){	
		$("#SequesterTypeOtherGrid tr[uid='"+uid+"']").addClass('edit-row');
		var textJson = $("#HiddSequesterTypeOtherData").val();
		var dataItems =  $.parseJSON(textJson);
		var editItem = null;
		
		for(var i = 0; i < dataItems.length; i++){
			if(dataItems[i].UID == uid){
				editItem = dataItems[i];
				break;
			}
		}	
		
		$("#Other").val(editItem.Other);
		$("#Other").focus();
		$("#HiddSequesterTypeOtherUID").val(uid);
		
	};
	
	C2XFunctions.prototype.delSequesterTypeOther = function(uid){	
		$("#SequesterTypeOtherGrid tr[uid='"+uid+"']").remove();
		var textJson = $("#HiddSequesterTypeOtherData").val();
		var dataItems =  $.parseJSON(textJson);
		dataItems = $.grep(dataItems, function( item ) {
			  return item.UID !== uid;
		});
		
		if(dataItems.length > 0){
			textJson = kendo.stringify(dataItems);
		}else{
			textJson = "";
			$("#SequesterTypeOtherGrid").hide();
		}
		
		
		$("#HiddSequesterTypeOtherData").val(textJson);
	};
	
	
	C2XFunctions.prototype.clearSequesterTypeMoneyForm = function(){
		$("#AccountNo").val("");		
		$("#AccountType").val("");		
		$("select[name='check_bank']").val("");
		$("#BankBranch").val("");
		var uid = $("#HiddSequesterTypeMoneyUID").val();
		$("#HiddSequesterTypeMoneyUID").val("");
		if(uid != ""){
			$("#SequesterTypeMoneyGrid tr[uid='"+uid+"']").removeClass('edit-row');
		}
		
	};
	
	C2XFunctions.prototype.clearSequesterTypePropertyForm = function(){
		$("#DocumentNo").val("");		
		$("#DdlSubDistrict").val("");
		$("#DdlDistrict").val("");
		$("#DdlProvince").val("");
		
		var uid = $("#HiddSequesterTypePropertyUID").val();
		$("#HiddSequesterTypePropertyUID").val("");
		if(uid != ""){
			$("#SequesterTypePropertyGrid tr[uid='"+uid+"']").removeClass('edit-row');
		}
	};
	
	C2XFunctions.prototype.clearSequesterTypeCarForm = function(){
		$("#CarNo").val("");	
		
		var carYearPicker =  $("#CarYear").data("kendoDatePicker");
		carYearPicker.value(null);	
		
		var uid = $("#HiddSequesterTypeCarUID").val();
		$("#HiddSequesterTypeCarUID").val("");
		if(uid != ""){
			$("#SequesterTypeCarGrid tr[uid='"+uid+"']").removeClass('edit-row');
		}
		
	};
	
	C2XFunctions.prototype.clearSequesterTypeOtherForm = function(){
		$("#Other").val("");		
		
		
		var uid = $("#HiddSequesterTypeOtherUID").val();
		$("#HiddSequesterTypeOtherUID").val("");
		if(uid != ""){
			$("#SequesterTypeOtherGrid tr[uid='"+uid+"']").removeClass('edit-row');
		}
		
	};
    
	C2XFunctions.prototype.changePagination = function (){
		$("#HiddTaskName").val("paginator");
		$("form").submit();		
	}
	
	function clearMessage(){
	   var updateBlock = $('div[message-for="sequestration-update"]').get(0);	
	   if(typeof(updateBlock) != "undefined" && (updateBlock != null)){
		   $(updateBlock).hide();
	   }
	   
	}
	
	function renderSequesterTypeMoneyGrid(){
		var textJson = $("#HiddSequesterTypeMoneyData").val();
		if(textJson != ""){				
			var dataItems = $.parseJSON(textJson);
			var grid = $("#SequesterTypeMoneyGrid");
			var tbody = $(grid).find("tbody");
			var tr, td, editA, delA, item;
			$(tbody).empty();
			
			for(var i = 0; i < dataItems.length; i++){
				item = dataItems[i];
				tr = document.createElement("tr");
				tr.setAttribute("uid", item.UID);
				
				//เลขบัญชี
				td = document.createElement("td");
				td.innerHTML = item.DocumentNo;
				tr.appendChild(td);
				
				//ประเภทบัญชี
				td = document.createElement("td");
				td.innerHTML = item.AccountTypeName;
				tr.appendChild(td);
				
				//ธนาคาร
				td = document.createElement("td");
				td.innerHTML = item.BankName;
				tr.appendChild(td);
				
				//สาขา
				td = document.createElement("td");
				td.innerHTML = item.BankBranchName;
				tr.appendChild(td);				
				
				
				//command
				td = document.createElement("td");
				editA = document.createElement("a");
				editA.setAttribute("href", "javascript:void(0)");
				editA.setAttribute("onclick", "c2xHolding.editSequesterTypeMoney('"+ item.UID +"')");
				editA.setAttribute("class", "icon icon-edit");
				editA.setAttribute("title", "แก้ไข");
				td.appendChild(editA); 
				
				delA = document.createElement("a");
				delA.setAttribute("href", "javascript:void(0)");
				delA.setAttribute("onclick", "c2xHolding.delSequesterTypeMoney('"+ item.UID +"')");
				delA.setAttribute("class", "icon icon-del");
				delA.setAttribute("title", "ลบ");
				td.appendChild(delA); 				
				
				tr.appendChild(td);
				
				$(tbody).append(tr);
			}
			
			
			$(grid).show();
		}else{
			$("#SequesterTypeMoneyGrid").hide();
		}
	}
	
	
	
	function renderSequesterTypePropertyGrid(){
		var textJson = $("#HiddSequesterTypePropertyData").val();
		if(textJson != ""){				
			var dataItems = $.parseJSON(textJson);
			var grid = $("#SequesterTypePropertyGrid");
			var tbody = $(grid).find("tbody");
			var tr, td, item;
			$(tbody).empty();
			
			for(var i = 0; i < dataItems.length; i++){
				item = dataItems[i];
				tr = document.createElement("tr");
				tr.setAttribute("uid", item.UID);
				
				//เลขที่โฉนด
				td = document.createElement("td");
				td.innerHTML = item.DocumentNo;
				tr.appendChild(td);
				//ตำบล/แขวง
				td = document.createElement("td");
				td.innerHTML = item.SubDistrictName;
				tr.appendChild(td);
				//อำเภอ/เขต
				td = document.createElement("td");
				td.innerHTML = item.DistrictName;
				tr.appendChild(td);
				//จังหวัด
				td = document.createElement("td");
				td.innerHTML = item.ProvinceName;
				tr.appendChild(td);							
				
				//command
				td = document.createElement("td");
				editA = document.createElement("a");
				editA.setAttribute("href", "javascript:void(0)");
				editA.setAttribute("onclick", "c2xHolding.editSequesterTypeProperty('"+ item.UID +"')");
				editA.setAttribute("class", "icon icon-edit");
				editA.setAttribute("title", "แก้ไข");
				td.appendChild(editA); 
				
				delA = document.createElement("a");
				delA.setAttribute("href", "javascript:void(0)");
				delA.setAttribute("onclick", "c2xHolding.delSequesterTypeProperty('"+ item.UID +"')");
				delA.setAttribute("class", "icon icon-del");
				delA.setAttribute("title", "ลบ");
				td.appendChild(delA); 	
				tr.appendChild(td);		
				
				$(tbody).append(tr);
			}
			
			
			$(grid).show();
		}else{
			$("#SequesterTypeMoneyGrid").hide();
		}
	}
	
	function renderSequesterTypeCarGrid(){
		var textJson = $("#HiddSequesterTypeCarData").val();
		if(textJson != ""){				
			var dataItems = $.parseJSON(textJson);
			var grid = $("#SequesterTypeCarGrid");
			var tbody = $(grid).find("tbody");
			var tr, td, editA, delA, item, year;
			$(tbody).empty();
			
			for(var i = 0; i < dataItems.length; i++){
				item = dataItems[i];
				tr = document.createElement("tr");
				tr.setAttribute("uid", item.UID);
				
				//ทะเบียนรถยนต์
				td = document.createElement("td");
				td.innerHTML = item.DocumentNo;
				tr.appendChild(td);
				
				//ปี
				year = parseInt(item.CarYear, 10) + 543;				
				td = document.createElement("td");
				td.innerHTML = year;
				tr.appendChild(td);
				
				//command
				td = document.createElement("td");
				editA = document.createElement("a");
				editA.setAttribute("href", "javascript:void(0)");
				editA.setAttribute("onclick", "c2xHolding.editSequesterTypeCar('"+ item.UID +"')");
				editA.setAttribute("class", "icon icon-edit");
				editA.setAttribute("title", "แก้ไข");
				td.appendChild(editA); 
				
				delA = document.createElement("a");
				delA.setAttribute("href", "javascript:void(0)");
				delA.setAttribute("onclick", "c2xHolding.delSequesterTypeCar('"+ item.UID +"')");
				delA.setAttribute("class", "icon icon-del");
				delA.setAttribute("title", "ลบ");
				td.appendChild(delA); 				
				
				tr.appendChild(td);
				
				$(tbody).append(tr);
			}
			
			
			$(grid).show();
		}else{
			$("#SequesterTypeCarGrid").hide();
		}
	}
	
	function renderSequesterTypeOtherGrid(){
		var textJson = $("#HiddSequesterTypeOtherData").val();
		if(textJson != ""){				
			var dataItems = $.parseJSON(textJson);
			var grid = $("#SequesterTypeOtherGrid");
			var tbody = $(grid).find("tbody");
			var tr, td, editA, delA, item;
			$(tbody).empty();
			
			for(var i = 0; i < dataItems.length; i++){
				item = dataItems[i];
				tr = document.createElement("tr");
				tr.setAttribute("uid", item.UID);
				
				//รายละเอียด
				td = document.createElement("td");
				td.innerHTML = item.Other;
				tr.appendChild(td);
							
				
				//command
				td = document.createElement("td");
				editA = document.createElement("a");
				editA.setAttribute("href", "javascript:void(0)");
				editA.setAttribute("onclick", "c2xHolding.editSequesterTypeOther('"+ item.UID +"')");
				editA.setAttribute("class", "icon icon-edit");
				editA.setAttribute("title", "แก้ไข");
				td.appendChild(editA); 
				
				delA = document.createElement("a");
				delA.setAttribute("href", "javascript:void(0)");
				delA.setAttribute("onclick", "c2xHolding.delSequesterTypeOther('"+ item.UID +"')");
				delA.setAttribute("class", "icon icon-del");
				delA.setAttribute("title", "ลบ");
				td.appendChild(delA); 				
				
				tr.appendChild(td);
				
				$(tbody).append(tr);
			}
			
			
			$(grid).show();
		}else{
			$("#SequesterTypeOtherGrid").hide();
		}
	}
	
	function renderOrgDebtData(data){		
		var dataItems = [];
		if($.isArray(data)){
			dataItems = data;
		}else{
			dataItems.push(data);
		}
		
		

		var container = $("#OrgDebtBlock");
		
		var table = document.createElement("table");
		var tr = document.createElement("tr");
		var th;
		var td;
		var item, year, interestRate;
		var totalPrincipleAmount = 0, totalInterestAmount = 0, totalAmount = 0, totalInterestAmountPerDay = 0; 
		var pAmount, iAmount, tAmount, iPerDayAmount;
		th = document.createElement("th");		
		th.innerHTML = "ปี";
		th.setAttribute("width", 45);
		tr.appendChild(th);

		th = document.createElement("th");
		th.innerHTML = "เงินต้น";
		th.setAttribute("width", 120);
		tr.appendChild(th);

		th = document.createElement("th");
		th.innerHTML = "ดอกเบี้ย";
		th.setAttribute("width", 100);
		tr.appendChild(th);
		
		th = document.createElement("th");
		th.innerHTML = "ดอกเบี้ยต่อวัน";
		th.setAttribute("width", 80);
		tr.appendChild(th);

		th = document.createElement("th");
		th.innerHTML = "อัตราดอกเบี้ย";		
		tr.appendChild(th);

		th = document.createElement("th");
		th.innerHTML = "รวม";
		th.setAttribute("width", 120);
		tr.appendChild(th);

		

		table.appendChild(tr);
		var payments = [], payment;
		for(var i =0; i < dataItems.length; i++){
			item = dataItems[i];
			
			year = parseInt(item.Year, 10);
			pAmount = parseFloat(item.PrincipleAmount);
			iAmount = parseFloat(item.InterestAmount);
			tAmount = parseFloat(item.TotalAmount);
			iPerDayAmount = parseFloat(item.InterestPerDay);
			
			totalPrincipleAmount += pAmount;
			totalInterestAmount += iAmount;
			totalAmount += tAmount;
			totalInterestAmountPerDay += iPerDayAmount;

			// ปี
			tr = document.createElement("tr");
			td = document.createElement("td");
			td.innerHTML =  year + 543;
			td.setAttribute("class", "text-center");
			tr.appendChild(td);

			// เงินต้น
			td = document.createElement("td");
			td.innerHTML = kendo.format("{0:n2}", pAmount);
			td.setAttribute("class", "text-right");
			tr.appendChild(td);

			//ดอกเบี้ย
			td = document.createElement("td");
			td.innerHTML =  kendo.format("{0:n2}", iAmount);
			td.setAttribute("class", "text-right");
			tr.appendChild(td);
			
			//ดอกเบี้ยต่อวัน
			td = document.createElement("td");
			td.innerHTML =  kendo.format("{0:n2}", iPerDayAmount);
			td.setAttribute("class", "text-right");
			tr.appendChild(td);

			//อัตราดอกเบี้ย
			td = document.createElement("td");
			interestRate = (year > 2011)? _interestRate : 0;
			td.innerHTML = kendo.format("{0:n1}",interestRate) + "%";
			td.setAttribute("class", "text-right");
			tr.appendChild(td);

			//รวม
			td = document.createElement("td");
			td.innerHTML = kendo.format("{0:n2}",tAmount);
			td.setAttribute("class", "text-right");
			tr.appendChild(td);			

			table.appendChild(tr);
			
			payment = {	SPID: 0, SID: 0, LID: 0, 
						Year: year, 
						PrincipleAmount: pAmount, 
						InterestAmount: iAmount, 
						InterestPerDay: iPerDayAmount,
						InterestRate: interestRate, 
						TotalAmount: tAmount};
			payments.push(payment);
		}

		tr = document.createElement("tr");
		
		td = document.createElement("td");
		td.innerHTML =  "รวม";
		td.setAttribute("class", "text-center");
		tr.appendChild(td);

		// เงินต้น
		td = document.createElement("td");
		td.innerHTML = kendo.format("{0:n2}", totalPrincipleAmount);
		td.setAttribute("class", "text-right");
		tr.appendChild(td);

		// ดอกเบี้ย
		td = document.createElement("td");
		td.innerHTML = kendo.format("{0:n2}", totalInterestAmount);
		td.setAttribute("class", "text-right");
		tr.appendChild(td);
		
		// ดอกเบี้ยต่อวัน
		td = document.createElement("td");
		td.innerHTML = kendo.format("{0:n2}", totalInterestAmountPerDay);
		td.setAttribute("class", "text-right");
		tr.appendChild(td);

		// อัตราดอกเบี้ย
		td = document.createElement("td");
		tr.appendChild(td);

		// รวม
		td = document.createElement("td");
		td.innerHTML = kendo.format("{0:n2}", totalAmount);
		td.setAttribute("class", "text-right");
		tr.appendChild(td);	
		
		table.appendChild(tr);

		table.setAttribute("class", "nep-grid");
		container.append(table);
		container.show();
		
		$("#TotalAmount").val(totalAmount);
		var textJson = kendo.stringify(payments);
		$("#SequestrationPayments").val(textJson);		
	}

	function getOrgDebtParam(){
		var param = null;
		var companycode = "", branchcode = "",  caldate = "", companyid = "", noticedate = "";
		if($(".org-checkbox").get(0) != null){
			$(".org-checkbox").each(function(){
				if(this.checked){
					companycode = $(this).attr("company-code");
					branchcode = $(this).attr("branch-code");
					companyid = $(this).attr("company-id");
					noticedate = $(this).attr("notice-date");
					return;
				}			
			});	
		}else{
			companycode =  $("#HiddCompanyCode").val();
			branchcode =  $("#HiddBranchCode").val();
			companyid =  $("#HiddCID").val();
			noticedate = $("#NoticeDate").val();
		}
		

		var day = $("#DocumentDate_day").val();
		var month = $("#DocumentDate_month").val();
		var year = $("#DocumentDate_year").val();

		if((day != "00") && (month != "00") && (year != "0000")){
			caldate = year +"-"+ month +"-"+ day;
		}

		if((companycode != "") && (branchcode != "") && (caldate != "")){
			param = {companyid: companyid, companycode: companycode, branchcode: branchcode, caldate: caldate, noticedate: noticedate};
		}
	
		
		return param;
	}
	
	function validateHolding(){
		var isValid = true;
		var isSelectedCompany = false;
		var isEntrySequesterTypeMoney = true;
		var isEntrySequesterTypeProperty = true;
		var isEntrySequesterTypeCar = true;
		var isEntrySequesterTypeOther = true;
		
		var cid = $("#HiddCID").val();
		isSelectedCompany = (cid != "");		
		
		var noticeDate = $("#NoticeDate").val();		
				
		var day = $("#DocumentDate_day").val();
		var month = $("#DocumentDate_month").val();
		var year = $("#DocumentDate_year").val();
		var gov = $("#GovDocumentNo").val();
		gov = $.trim(gov);
		var moneyTypeChecked = $("#ChkSequesterTypeMoney").get(0).checked;
		var propertyTypeChecked = $("#ChkSequesterTypeProperty").get(0).checked;
		var carTypeChecked = $("#ChkSequesterTypeCar").get(0).checked;
		var otherTypeChecked = $("#ChkSequesterTypeOther").get(0).checked;
		
		if(!isSelectedCompany){
			alert("กรุณาเลือก: "+ _theCompanyWord + "ที่ต้องการแจ้งอายัด");
		}else if(day == "00"){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: วัน");
			$("#DocumentDate_day").focus();
		}else if(month == "00"){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: เดือน");
			$("#DocumentDate_month").focus();
		}else if(year == "0000"){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ปี");
			$("#DocumentDate_year").focus();
		}else if(gov == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: หนังสือเลขที่");
			$("#GovDocumentNo").focus();
		}else if((!moneyTypeChecked) && (!propertyTypeChecked)){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ประเภทการอายัด");
			$("#ChkSequesterTypeMoney").focus();
		}else if((day != "00") && (month != "00") && (year != "0000") && (noticeDate != "")){
			day = parseInt(day, 10);
			month = parseInt(month, 10);
			
			var nYear = noticeDate.substring(0, 4);
			var nMonth = parseInt(noticeDate.substring(5, 7), 10);
			var nDay = parseInt(noticeDate.substring(8, 10), 10);
			
			var noticeTime = (new Date(nYear, nMonth -1, nDay)).getTime();
			var docDate = (new Date(year, month - 1, day)).getTime();
			
			if(docDate < noticeTime){
				isValid = false;
				alert("กรุณาใส่ข้อมูล: วันที่มากกว่าหรือเท่ากับวันแจ้งโนติส");
				$("#DocumentDate_day").focus();
			}
		}
		
		
		
		if(moneyTypeChecked){
			var text = $("#HiddSequesterTypeMoneyData").val();
			isEntrySequesterTypeMoney = (text != "");
		}		
		if(propertyTypeChecked){
			var text = $("#HiddSequesterTypePropertyData").val();
			isEntrySequesterTypeProperty = (text != "");
		}
		if(carTypeChecked){
			var text = $("#HiddSequesterTypeCarData").val();
			isEntrySequesterTypeCar = (text != "");
		}
		if(otherTypeChecked){
			var text = $("#HiddSequesterTypeOtherData").val();
			isEntrySequesterTypeOther = (text != "");
		}
		
		if((!isEntrySequesterTypeMoney) || (!isEntrySequesterTypeProperty) || (!isEntrySequesterTypeCar) || (!isEntrySequesterTypeOther)){
			alert("กรุณาใส่ข้อมูล: รายละเอียดประเภทการอายัด");
			if(!isEntrySequesterTypeMoney){
				$("#AccountNo").focus();
			}else if(!isEntrySequesterTypeProperty){
				$("#DocumentNo").focus();
			}else if(!isEntrySequesterTypeProperty){
				$("#CarNo").focus();
			}else{
				$("#Other").focus();
			}
		}
		
		return (isValid && isSelectedCompany && isEntrySequesterTypeMoney && isEntrySequesterTypeProperty && isEntrySequesterTypeCar && isEntrySequesterTypeOther);
	}
	
	function validateSequesterTypeMoney(){
		var isValid = true;
		
		var accountNo = $("#AccountNo").val();
		accountNo = $.trim(accountNo);
		
		var accountType = $("#AccountType").val();
		
		var checkBank = $("select[name='check_bank']").val();
		
		var branchName = $("#BankBranch").val();
		branchName = $.trim(branchName);
		
		var uid = $("#HiddSequesterTypeMoneyUID").val();
		var isAccountValid = checkDocumentNoDup(_sequestrationType.Money, accountNo, uid);
		
		if(accountNo == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: เลขบัญชี");
			$("#AccountNo").focus();
		}else if(accountType == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ประเภทบัญชี");
			$("#AccountType").focus();
		}else if(checkBank == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ธนาคาร");
			$("select[name='check_bank']").focus();
		}else if(branchName == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: สาขา");
			$("#BankBranch").focus();
		}else if(!isAccountValid){
			isValid = false;
			alert("ข้อมูลเลขที่บัญชีซ้ำ");
			$("#AccountNo").focus();
		}
		return isValid;
	}	
	
	function validateSequesterTypeProperty(){
		var isValid = true;
		var docmentNo = $("#DocumentNo").val();	
		docmentNo = $.trim(docmentNo);
		
		var subDistrict = $("#DdlSubDistrict").val();
		var district = $("#DdlDistrict").val();
		var province = $("#DdlProvince").val();
		var uid = $("#HiddSequesterTypePropertyUID").val();
		
		var isDocumentNoValid = checkDocumentNoDup(_sequestrationType.Property, docmentNo, uid);		
		
		if(docmentNo == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: เลขที่โฉนด");
			$("#DocumentNo").focus();
		}else if(subDistrict == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ตำบล/แขวง");
			$("#DdlSubDistrict").focus();
		}else if(district == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: อำเภอ/เขต");
			$("#DdlDistrict").focus();
		}else if(province == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: จังหวัด");
			$("#DdlProvince").focus();
		}else if(!isDocumentNoValid){
			isValid = false;
			alert("ข้อมูลเลขที่โฉนดซ้ำ");
			$("#DocumentNo").focus();
		}		
		
		return isValid;
	}
	
	function validateSequesterTypeCar(){
		var isValid = true;
		
		var carNo = $("#CarNo").val();
		carNo = $.trim(carNo);
		
		var carYearPicker =  $("#CarYear").data("kendoDatePicker");
	    var carYear = carYearPicker.value();
	    carYear = (carYear != null)? carYear.toString("yyyy") : null;
		
		var uid = $("#HiddSequesterTypeCarUID").val();
		var isCarNoValid = checkDocumentNoDup(_sequestrationType.Car, carNo, uid);
		
		if(carNo == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ทะเบียนรถยนต์");
			$("#CarNo").focus();
		}else if(carYear == null){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ปี");
			$("#CarYear").focus();		
		}else if(!isCarNoValid){
			isValid = false;
			alert("ข้อมูลทะเบียนรถยนต์ซ้ำ");
			$("#CarNo").focus();
		}
		return isValid;
	}
	
	function validateSequesterTypeOther(){
		var isValid = true;
		
		var other = $("#Other").val();
		other = $.trim(other);
		
	
		
		if(other == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: รายละเอียดทรัพย์สิน");
			$("#Other").focus();
		}else if(other.length > 500){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: รายละเอียดทรัพย์สินไม่เกิน 500 ตัวอักษร");
			$("#Other").focus();
		}
		return isValid;
	}
	

	function checkDocumentNoDup(sequestrationType, docNo, uid){
		var isValid = true;
		var dataItems, item, textJson;		
		
		var hiddID;
		if(sequestrationType == _sequestrationType.Money){
			hiddID = "HiddSequesterTypeMoneyData";
		}else if(sequestrationType == _sequestrationType.Property){
			hiddID = "HiddSequesterTypePropertyData";
		}else{
			hiddID = "HiddSequesterTypeCarData";
		}
		
		
		textJson = $("#" + hiddID).val();
		docNo = docNo.replace(/\s/g,'');
		if(textJson != ""){
			dataItems = $.parseJSON(textJson);
			dataItems = $.grep(dataItems, function( item ) {
				var itemDocNo = item.DocumentNo;
				itemDocNo = itemDocNo.replace(/\s/g,'');
				  return ((itemDocNo == docNo) && (item.UID != uid));
			});
			
			isValid = (dataItems.length == 0);
		}
		return isValid;
	}
	
	function reCheckOrgSelected(companyID){
		var chk = $("input[type='checkbox'][company-id='"+companyID+"']").get(0);
		if(typeof(chk) != 'undefined'){
				chk.checked = true;		
		}
		
	}
	
	function bindSequestrationDetailUID(details){
		if(details != null){
			for(var i = 0; i < details.length; i++){
				details[i].UID = c2x.guid();
			}
		}
		return details;
	}
	
	
    
    c2xHolding = new C2XFunctions();
})();