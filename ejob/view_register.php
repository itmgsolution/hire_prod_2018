<?php

		
	include "db_connect.php";
	include "scrp_config.php";
	
	//yoes 20160402 --- try to do "auto login"
	
	if($_GET[p] && $_GET[n] && $_GET[s]){
		
		//echo "moo";
		
		$decoded_id = html_entity_decode(base64_decode($_GET[p]))*1;
		$decoded_name = doCleanInput(html_entity_decode(base64_decode($_GET[n])));
		$decoded_seed = html_entity_decode(base64_decode($_GET[s]))*1;
		
		/*echo $decoded_id;
		echo $decoded_name;
		echo $decoded_seed;
		*/
		
		$sqll = "select * from users where user_id = '$decoded_id' and user_name = '$decoded_name'";
		//echo $sqll;
		
		$login_row = getFirstRow($sqll);
				
		//yoes 20160427 -- check if sees is correct...				
		$correct_seed = $decoded_id+$login_row[user_meta]+7890;
		
		if($login_row && $correct_seed == $decoded_seed){
			//echo "do auto login";	
			
			//session_start();			
			
			$_SESSION['sess_userid'] = $login_row["user_id"];
			$_SESSION['sess_accesslevel'] = $login_row["AccessLevel"];
			$_SESSION['sess_meta'] = $login_row["user_meta"];
			
			//yoes 20160816 ---> also check if approved
			if($login_row["user_enabled"] == 1){
				$_SESSION['sess_user_enabled'] = 1;
			}else{
				$_SESSION['sess_user_enabled'] = 0;
			}
			
			
			//echo $login_row["user_id"]; exit();
			
			$sess_userid = $_SESSION['sess_userid'];
			$sess_accesslevel = $_SESSION['sess_accesslevel'];
			$sess_meta = $_SESSION['sess_meta'];
			$sess_user_enabled = $_SESSION['sess_user_enabled'];
			
			$sql = "update users set user_enabled = 0 where user_id = '$decoded_id'";
			mysql_query($sql);
			
		}
		
	}
	
	
	//echo 'sess_userid '. $_SESSION['sess_userid']; 
	
	//yoes 20160331
	//echo $sess_user_enabled; exit();
	if($sess_user_enabled == "0" || $sess_user_enabled == 2 || $sess_user_enabled == 9 || $sess_user_enabled == 1){
		
		//echo $sess_user_enabled; exit();
		
		$mode = "edit";	
		
		//echo $mode;
		
		$this_id = $sess_userid*1;
		
		$post_row = getFirstRow("select * 
								from 
									users
								where 
									user_id  = '$this_id'
								limit 0,1");
								
		//vars to use
		$output_fields = array(
						
						'AccessLevel'
						,'Department'
						,'FirstName'
						,'LastLoginDatetime'
						,'LastName'
						,'reopen_case_password'
						,'user_approved_by'
						,'user_approved_date'
						,'user_can_manage_user'
						,'user_commercial_code'
						,'user_created_date'
						,'user_email'
						,'user_enabled'
						,'user_id'
						,'user_ip_address'
						,'user_meta'
						,'user_name'
						,'user_password'
						,'user_position'
						,'user_telephone'

						//more items
						,'FirstName_2'						
						,'LastName_2'
						,'user_position_2'
						,'user_telephone_2'
						
						);
				//echo "asdasd";
				
				
				
				
		for($i = 0; $i < count($output_fields); $i++){
			//clean all inputs
			//echo $i;
			$register_values[$output_fields[$i]] .= doCleanOutput($post_row[$output_fields[$i]]);
		}				
		
		$registered_org_row = getFirstRow("select *  from company where cid = '".$register_values[user_meta]."'");
		
		//no password
		if(strlen($register_values[user_password])==0 || $_GET[r] == 1){
			$no_pass = 1;
		}
		
		//echo "np : ".$no_pass; 
		
	}else{
	
		//only has "ADD" mode for now
		$mode = "add";	
		$this_id = "new";
		
	
	}
	
?>
<?php 
	include "header_html.php";
	include "global.js.php";
?>
              <td valign="top">
                	
                    
                    
                <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                  
                  	<?php if($mode == "edit"){?>
                    ข้อมูลการสมัครเข้าใช้งาน
                    <?php }else{?>
                	สถานประกอบการสมัครเข้าใช้งาน
                    <?php }?>
                
                </h2>
                    
                    <div style="padding:5px 0 0px 2px">
                   
                    
                   
                    
                   
                <?php 
						if($_GET["user_added"]=="user_added"){
							
							$register_id = $_GET["id"];
							$register_row = getFirstRow("select * from users where user_id = '$register_id'");
							
							
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มข้อมูลการใช้งานเสร็จสิ้น</div>
                         
                         <table border="0">
                          <tr>
                            <td colspan="2"><hr /><strong>ข้อมูลการใช้งานระบบ</strong>
                            
                            <!--
                            <br />คุณสามารถ <a href="submit_forms.php">ส่งเอกสารการปฏิบัติตามกฏหมาย</a> ได้ด้วย user name และ password ด้านล่าง
                            -->
                            
                            <hr /></td>
                           </tr>
                          <tr>
                            <td>User name:</td>
                            <td><?php echo $register_row["user_name"];?></td>
                          </tr>
                         <tr>
                            <td>ชื่อสถานประกอบการ:</td>
                            <td><?php 
							
							$my_company_row = getFirstRow("select CompanyNameThai, CompanyTypeCode from company where CID = '".$register_row["user_meta"]."'");
							
							echo formatCompanyName($my_company_row["CompanyNameThai"],$my_company_row["CompanyTypeCode"]);
							
							?></td>
                          </tr>
                          
                           <tr>                            
                            <td colspan="2">
                            <hr />
                            
                            <span style="color:#369; ">
                           	กรุณาเช็ค email ของคุณ เพื่อทำการยืนยันตน <br />และทำการกรอกข้อมูลการปฏิบัติตามกฎหมายต่อไป (<?php echo $register_row["user_email"];?>)
                            </span>
                            
                            <hr />
                            </td>
                          </tr>
                          
                        </table>
                        
                        
                        

                         
                    <?php
						}					
					?>
                   
                   
                   
                
                  <div align="center">            
                            
                          
                <form 
                	method="post" 
                    id="view_user_form" 
                    name="view_user_form" 
                    action="scrp_update_register.php" 
                    onsubmit="return validate_register(this);"               
					
                    enctype="multipart/form-data"
                    
                
                >
                     <input name="register_id" type="hidden" value="<?php echo $this_id;?>" />
                     
                     <script>
					 
					
					 $().ready(function() {
						 
						 //alert("whaattt");
						 // validate signup form on keyup and submit
						$("#view_user_form").validate({
							
							
							rules: {
								register_contact_name: "required",
								register_contact_lastname: "required",
								register_contact_phone: {
									required: true,
									number: true
								},
								register_email: {
									required: true,
									email: true
								},
								register_position: {
									required: true
								},
								
								
								register_contact_name_2: "required",
								register_contact_lastname_2: "required",
								register_contact_phone_2: {
									required: true,
									number: true
								},								
								register_position_2: {
									required: true
								},
								
								
								/*register_employee_card: {
								  required: 0,
								  accept: "image/*"
								},
								register_id_card: {
								  required: 0,
								  accept: "image/*"
								},*/
								
								
								user_commercial_code: {
									required: true,
									number: true,
									maxlength: 13,
									minlength: 13
									
								}
								
								
								<?php if($no_pass){?>
								
								, register_password: {
									required: true
								}
								<?php }?>
								
							},
							messages: {
								register_contact_name: "กรุณาใส่ ชื่อผู้ติดต่อ",
								register_contact_lastname: "กรุณาใส่ นามสกุลผู้ติดต่อ",
								register_contact_phone: "กรุณาใส่ เบอร์โทรศัพท์ ที่เป็นตัวเลขเท่านั้น",
								register_email: "กรุณาใส่ email ให้ถูกต้อง",
								register_position: "กรุณาใส่ ตำแหน่ง ให้ถูกต้อง",
								
								
								register_contact_name_2: "กรุณาใส่ ชื่อกรรมการบริษัท",
								register_contact_lastname_2: "กรุณาใส่ นามสกุลกรรมการบริษัท",
								register_contact_phone_2: "กรุณาใส่ เบอร์โทรศัพท์ ที่เป็นตัวเลขเท่านั้น",
								register_position_2: "กรุณาใส่ ตำแหน่ง ให้ถูกต้อง",
								
								/*register_employee_card: "กรุณาแนบรูป เป็นไฟล์ jpg, gif หรือ png เท่านั้น",
								register_id_card: "กรุณาแนบรูป  เป็นไฟล์ jpg, gif หรือ png เท่านั้น",*/
								user_commercial_code: "กรุณาใส่ เลขทะเบียนนิติบุคคล เป็นตัวเลข 13 หลักเท่านั้น"
								
								<?php if($no_pass){?>
								
								, register_password: "กรุณาระบุรหัสผ่านที่ต้องการ"
								<?php }?>
							}
						});
						 
						 
						 
					 }); /**/
					 
					 </script>
                     
                     
                     <?php if($mode == "add"){?>
                  <div align="center" style="background-color: #fcfcfc; width:500px; padding: 5px; text-align: left;">
                   
                   	1) กรอกเลขที่บัญชีนายจ้าง 10 หลัก และกดปุ่ม "ตรวจสอบเลขที่บัญชีนายจ้าง"
                    
                    <br />
                     <br />
                    
                    2) หลังจากตรวจสอบเลขที่นายจ้างถูกต้องแล้ว ให้ใส่ชื่อผู้ใช้งานที่ต้องการ และอีเมล์ที่ใช้ในการติดต่อได้ 
                    <br />เพื่อรับ link ในการ activate บัญชีผู้ใช้งาน และทำการยื่นเอกสารยืนยันตน
                    
                  </div>
                  <?php }?>
                  
                  
                   <?php if($no_pass){?>
                   
                          <div align="center" style="background-color: #fcfcfc; width:500px; padding: 5px; text-align: left;">
                           
                            1) กรุณากรอก และยืนยัน รหัสผ่านที่ต้องการใช้ระบบ
                            
                           
                          </div>
                          
                   <?php }elseif($sess_user_enabled == "0"){?>
                   
                   
                           <div align="center" style="background-color: #F0FFF8; color:#006600; width:500px; padding: 5px; text-align: left;">
                           
                            1) ข้อมูลการใช้งานของท่าน ยังไม่ได้รับการอนุมัติโดยเจ้าหน้าที่
                            
                             <br />
                             <br />
                            
                            2) กรุณากรอกข้อมูล และแนบเอกสารยืนยันตนเองให้ครบถ้วน - เจ้าหน้าที่จะอนุมัติผู้ใช้งานที่ข้อมูลครบถ้วนเท่านั้น
                            
                            
                            <br />
                            <br />
                            
                            3) ท่านสามารถแก้ไขข้อมูล และเอกสารได้ตามแบบฟอร์มข้างล่าง
                            
                            <br />
                            <br />
                            
                            4) หลังจากกรอกข้อมูลครบถ้วน และเจ้าหน้าที่ได้ทำการอนุมัติการใช้งานแล้ว จะมีอีเมล์แจ้งไปหาท่านอีกครั้ง
                            
                            
                            
                            
                            
                           
                          </div>
                          
                          
                          <div align="center" id="register_top_message" style="padding: 10px 0 0 0;">                        	
                        </div>
                          
                  
                  <?php }?>
                  
                  
                  	<?php 
						if($_GET["updated"]=="updated"){
					?>							
               <div align="center" style="background-color: #F0FFF8; color:#006600; font-weight: bold; width:500px; padding: 5px; text-align: center;">แก้ไขข้อมูลเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["duped"]=="duped"){
					?>							
                    
                    <div align="center" style="background-color: #F0FFF8; color:#CC3300; font-weight: bold; width:500px; padding: 5px; text-align: center;">
                    
               User Name ที่ใช้สมัครมีอยู่ในระบบแล้ว กรุณาใช้ user name อื่นในการสมัคร - ลืมรหัสผ่าน <a href="view_register_password.php">คลิกที่นี่</a></div>
                    <?php
						}					
					?>
                    
                    <?php 
						if($_GET["mailed"]=="mailed"){
					?>							
               <div align="center" style="background-color: #F0FFF8; color:#CC3300; font-weight: bold; width:500px; padding: 5px; text-align: center;"> Email ที่ใช้สมัครมีอยู่ในระบบแล้ว กรุณาใช้ email อื่นในการสมัคร - ลืมรหัสผ่าน <a href="view_register_password.php">คลิกที่นี่</a></div>
                    <?php
						}					
					?>
                     
                  <table border="0" cellpadding="0">
                        <tr>
                          <td>
                          
                          <table border="0" style="padding:0px 0 0 50px;" >
                          
                          
                          	 <tr>
                                <td colspan="4"><hr />
                                <span style="font-weight: bold">ข้อมูลสถานประกอบการ</span></td>
                              </tr>
                              
                              
                              <tr id="tr_textbox">
                                <td >เลขที่บัญชีนายจ้าง<br />(เลขประกันสังคม 10 หลัก)</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_org_code" type="text" id="register_org_code" maxlength="10" value="<?php echo $registered_org_row["CompanyCode"];?>"  />
                                  
                                  <input name="register_org_name" type="hidden" id="register_org_name" value="<?php echo $register_values["register_org_name"];?>"  />
                                  <input name="register_cid" type="hidden" id="register_cid" value="<?php echo $register_values["register_cid"];?>"  />
                                  <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span><br />
                                  <input id="btn_get_data" type="button" value="ตรวจสอบเลขที่บัญชีนายจ้าง" onClick="return doGetData();" />
                                  
                                  
                                  <script>
											
												function doGetData(){
												
													var the_id = "";
													
													//
													the_id = the_id + document.getElementById('register_org_code').value;
												
													var checkOK = "1234567890";
												   var checkStr = the_id;
												   var allValid = true;
												   for (i = 0;  i < checkStr.length;  i++)
												   {
													 ch = checkStr.charAt(i);
													 for (j = 0;  j < checkOK.length;  j++)
													   if (ch == checkOK.charAt(j))
														 break;
													 if (j == checkOK.length)
													 {
													   allValid = false;
													   break;
													 }
												   }
												   if (!allValid)
												   {
													 alert("เลขที่บัญชีนายจ้างต้องเป็นเลข 10 หลักเท่านั้น");
													 document.getElementById('register_org_code').focus();
													 return (false);
												   }
													
													
													if(the_id.length != 10)
													{
														alert("เลขที่บัญชีนายจ้างต้องเป็นเลข 10 หลักเท่านั้น");
														document.getElementById('register_org_code').focus();
														return (false);
													}
												
													//alert("do get data");
													//document.getElementById('btn_get_data').style.display = 'none';
													//document.getElementById('img_get_data').style.display = '';
													
													var parameters = "the_id="+the_id;
													//alert(parameters);
													//return false;
													//send requests
													http_request = false;
													 if (window.XMLHttpRequest) { // Mozilla, Safari,...
														 http_request = new XMLHttpRequest();
														 if (http_request.overrideMimeType) {										
															http_request.overrideMimeType('text/html');
														 }
													  } else if (window.ActiveXObject) { // IE
														 try {
															http_request = new ActiveXObject("Msxml2.XMLHTTP");
														 } catch (e) {
															try {
															   http_request = new ActiveXObject("Microsoft.XMLHTTP");
															} catch (e) {}
														 }
													  }
													  if (!http_request) {
														 alert('Cannot create XMLHTTP instance');
														 return false;
													  }
													
													http_request.onreadystatechange = alertContents3;
													http_request.open('POST', "./ajax_get_company.php", true);
													http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded;");
													http_request.setRequestHeader("Content-length", parameters.length);
													http_request.setRequestHeader("Connection", "close");
													
													http_request.send(parameters);
													
													return true;
												
												}
												
												function alertContents3(){
													
													if (http_request.readyState == 4) {
													
														if (http_request.status == 200) {
															
															//alert("response recieved");
															//return false;
															
															if(http_request.responseText == "no_result"){
															
																alert("ไม่พบข้อมูลบัญชีนายจ้าง");
																//no result
																
															}else{
															
																var JSONFile = http_request.responseText;  
																eval(JSONFile); 	
																
																
																//document.getElementById('le_age').value = someVar.BIRTH_DATE;
																document.getElementById('tr_textbox').style.display = 'none';
																document.getElementById('tr_result').style.display = '';
																document.getElementById('tr_result2').style.display = '';
																
																document.getElementById('tr_result_2').style.display = '';
																
																document.getElementById('span_org_code').innerHTML = document.getElementById('register_org_code').value;
																document.getElementById('span_org_name').innerHTML = someVar.company_name_thai;
																
																document.getElementById('register_org_name').value = someVar.company_name_thai;
																document.getElementById('register_cid').value = someVar.company_cid;
															
															
																//alert(someVar.company_name_thai);
																if(someVar.user_count > 1){
																	document.getElementById('tr_result_2').style.display = 'none';
																	
																	$("#btn_new_register").prop("disabled",true);
																	$("#register_name").prop("disabled",true);
																	$("#register_email").prop("disabled",true);
																	
																	$("#tr_result_duped").show();
																	
																	
																	
																	alert("เลขบัญชีนายจ้างถูกต้อง แต่มีการสมัครเข้าใช้งานโดยผู้ใช้งานคนอื่นแล้ว");
																	
																}else{
																	alert("เลขบัญชีนายจ้างถูกต้อง");
																}
																
															
															}
															//
															
														} else {
															alert('การเชื่อมต่อผิดพลาด โปรดลองอีกครั้ง');
														}
													}
												
												}
											
											
											</script>
                                  
                                  
                                  
                                </span></td>
                                <td></td>
                                <td></td>
                              </tr>
                              
                              <tr id="tr_result" style="display: none;">
                                <td >เลขที่บัญชีนายจ้าง (เลขประกันสังคม 10 หลัก)</td>
                                <td colspan="3"><span id="span_org_code" style="font-weight: bold;"><?php echo $registered_org_row["CompanyCode"];?></span></td>
                                
                              </tr>
                              <tr id="tr_result2" style="display: none;">
                                <td>ชื่อบริษัท (ภาษาไทย)</td>
                                <td colspan="3"><span id="span_org_name" style="font-weight: bold;">
								
								<?php 
								
									echo formatCompanyName($registered_org_row["CompanyNameThai"], $registered_org_row["CompanyTypeCode"]);
									
									
									?>
                                
                                
                                
                                </span></td>
                                
                              </tr>
                              
                              
                              <tr id="tr_result_duped" style="display: none;">
                                <td colspan="2">
                                <div align="center" style="color:#F00;">
                               
                                	มีการสมัครใช้งานระบบของสถานประกอบการนี้แล้ว
                                    <br /> ระบบอนุญาตให้สามารถมีหนึ่งผู้ใช้งานต่อหนึ่งสถานประกอบการเท่านั้น
                                    <br /> ถ้าท่านเคยสมัครใช้งาน แต่ลืมรหัสผ่าน ให้ <a href="view_register_password.php">คลิกที่นี่</a> เพื่อขอรหัสผ่านใหม่
                                    <br /> กรุณาติดต่อเจ้าหน้าที่ถ้าต้องการข้อมูลเพิ่มเติม
                                    
                                                                        
                                </div>
                                </td>
                                
                              </tr>
                              
                              <tr id="tr_result_2" style="display: none;">
                                <td >เลขทะเบียนนิติบุคคลของกระทรวงพาณิชย์</td>
                                <td colspan="3"><span id="span_org_code" style="font-weight: bold;">
                                
                                
                                	<?php if($mode == "edit"){?>
                  					
                                    <?php echo $register_values["user_commercial_code"];?>
                                    
                                    <input name="user_commercial_code" type="hidden" id="user_commercial_code" value="<?php echo $register_values["user_commercial_code"];?>" maxlength="13"  />
                                               	   
                                   <?php }else{?>
                                   <input name="user_commercial_code" type="text" id="user_commercial_code" value="<?php echo $register_values["user_commercial_code"];?>" maxlength="13"  />
                                   <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span></span></td>
                                   <?php }?>
                                   
                                   
                             	   
                               
                              </tr>
              
             
                            <?php 
							
							//yoes 20160331
							if($mode == "edit"){?>
                            
								<script>
								
									$('#tr_textbox').hide();
									 
                                    $('#tr_result').show();
                                    $('#tr_result2').show();
                                    $('#tr_result_2').show();
									
                                </script>
                            
                            <?php }?>
                          
                          
                              <tr>
                                <td colspan="4">
                                	<hr />
                                	<span style="font-weight: bold">ข้อมูลการใช้งานระบบ</span>                                </td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ชื่อผู้ใช้งาน</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  
                                  		<?php if($mode == "edit"){?>
                                        
											<?php echo $register_values["user_name"];?> 
                                            
                                            <input type="hidden" name="user_id" value="<?php echo $register_values["user_id"];?>"  />
                                            
	                                   
                                        <?php }else{?>
	                                   <input 
                                       
                                       	name="register_name" type="text" id="register_name" value="<?php echo $output_values["user_name"];?>"
                                       
                                       	onchange="doCheckUserName();"
                                        
                                         />
                                         
                                         
                                         <input type="hidden" name="user_id" value="new"  />
                                         
	                                   <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>
                                       
                                       <br />
                                       
                                       <span class="style86" id="register_name_used" style="padding: 10px 0 10px 0; display: none;"><font color="red">user name นี้ถูกใช้งานแล้ว - กรุณาใช้ user name อื่น</font></span>
	                                   <?php }?>
                                  
                                </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>อีเมล์</td>
                                <td>
                                
                                
                                	<?php if($mode == "edit"){?>
                                  
                                  <?php echo $register_values["user_email"];?>
                                  
                                  <input name="register_email" type="hidden" id="register_email" value="<?php echo $register_values["user_email"];?>" 
                                   
                                   onchange="doCheckEmail();"
                                   
                                   />
                                  
                                    
                                    <?php }else{?>
                                  <input name="register_email" type="text" id="register_email" value="<?php echo $register_values["user_email"];?>" 
                                   
                                   onchange="doCheckEmail();"
                                   
                                   /> <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>
                                   <?php }?>
                                   
                                   
                                                                  </span></span>
                                 
                                 <br />
                                 <span class="style86" id="email_used" style="padding: 10px 0 10px 0; display: none;"><font color="red">email นี้ถูกใช้งานแล้ว - กรุณาใช้ email อื่น</font></span>
                                </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              
                              <script>
							  	
								function doCheckUserName(){
								
									//alert($('#register_name').val());	
									$.ajax({ url: './ajax_check_user_name.php',
										 data: {user_name: $('#register_name').val()},
										 type: 'post',
										 success: function(output) {
											 //alert(output);
											 if(output == 1){
												$('#register_name_used').css("display",""); 
											 }else{
												 $('#register_name_used').css("display","none"); 
											 }
											 //
										  }
									});
									
								}
							  
							  </script>
                              
                                <script>
							  	
								function doCheckEmail(){
								
									//alert($('#register_name').val());	
									$.ajax({ url: './ajax_check_email.php',
										 data: {email: $('#register_email').val(), cid: $('#register_org_code').val()},
										 type: 'post',
										 success: function(output) {
											 //alert(output);
											 if(output == 1){
												$('#email_used').css("display",""); 
											 }else{
												 $('#email_used').css("display","none"); 
											 }
											 //
										  }
									});
									
								}
							  
							  </script>
                              
                              
                             
                             
                            
                             
                             
                            <tr id="password_row_1">
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                
                               
                                รหัสผ่าน
                                
                                
                                </span></td>
                                <td colspan="3">
                                
                                
                                
                                <span class="style86" style="padding: 10px 0 10px 0;">
                                
                                <?php if(!$no_pass && $mode != "add"){?>
                                <a id="change_pass_link" href="#" onclick="doShowPass(); return false;">ต้องการเปลี่ยนรหัสผ่าน คลิกที่นี่</a>
                                <?php }?>
                                
                                  <input name="register_password" type="password" id="register_password"  value=""  />
                                 
                                 
                                 	<?php if($no_pass){?>
                                  	<font color="red">*</font>
                                    <?php }?>
                                  
                                  
                                </span></td>
                               
                              </tr>
                              
                              
                               <tr id="password_row_2">
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ยืนยัน Password</span></td>
                                <td colspan="3"><span class="style86" style="padding: 10px 0 10px 0;">
                                
                                  <input name="register_password_2" type="password" id="register_password_2"  value=""  />
                                  
                                  
                                 
                                  
                                  <span class="style86" style="padding: 10px 0 10px 0;">
                                  
                                  
                                 <?php if($no_pass){?>
                                 <font color="red">*</font>
                                  <?php }?>
                                  
                                  
                                  </span></span></td>
                               
                              </tr>
							  
							  
							  <?php if(!$no_pass && $mode != "add"){?>
                              
                              <script>
							  		$('#register_password').hide();
									$('#password_row_2').hide();
									
									function doShowPass(){
										$('#password_row_2').show();
										$('#register_password').show();
										$('#change_pass_link').hide();
										
									}
								
							  </script>
                              
                              <?php }?>
                              
                             
                              
                             
                              
                             
                              
                            
                             
                              <?php if($mode == "edit" && !$no_pass){ // only show this if have passwords?>
                              
                              <tr>
                                <td colspan="4"><hr />
                                <strong>ข้อมูลผู้ติดต่อ</strong></td>
                              </tr>
                              
                              <tr>
                                <td valign="top">ชื่อ</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_contact_name" type="text" id="register_contact_name" value="<?php echo $register_values["FirstName"];?>" />
                                <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                                <td valign="top">นามสกุล</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_contact_lastname" type="text" id="register_contact_lastname" value="<?php echo $register_values["LastName"];?>" />
                                <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                              </tr>
                              <tr>
                               <td valign="top">เบอร์โทรศัพท์</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_contact_phone" type="text" id="register_contact_phone" value="<?php echo $register_values["user_telephone"];?>" />
                                <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                                
                                <td valign="top">ตำแหน่ง</td>
                                 <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                 
                                 <span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                   <input name="register_position" type="text" id="register_position" value="<?php echo $register_values["user_position"];?>" />
                                 <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                 </span></span>
                                 
                                 </td>
                              </tr>


                              
                              
                              <tr>
                                <td colspan="4"><hr />
                                <strong>ข้อมูลกรรมการบริษัท (ผู้มีอำนาจ)</strong></td>
                              </tr>
                              
                              
                              <?php
							  if($sess_user_enabled == 1){
							  ?>
                              
                              	<tr>
                                <td valign="top">ชื่อ</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <?php echo $register_values["FirstName_2"];?>
                                                                </span></span></td>
                                <td valign="top">นามสกุล</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                 <?php echo $register_values["LastName_2"];?>
                                                                </span></span></td>
                              </tr>
                              <tr>
                               <td valign="top">เบอร์โทรศัพท์</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <?php echo $register_values["user_telephone_2"];?>
                                                                </span></span></td>
                                
                                <td valign="top">ตำแหน่ง</td>
                                 <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                 
                                 <span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                   <?php echo $register_values["user_position_2"];?>
                                                                  </span></span>
                                 
                                 </td>
                              </tr>
							  
							  
                              
                              <?php }else{?>
                              
                              	<tr>
                                    <td valign="top">ชื่อ</td>
                                    <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                      <input name="register_contact_name_2" type="text" id="register_contact_name_2" value="<?php echo $register_values["FirstName_2"];?>" />
                                    <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                                    <td valign="top">นามสกุล</td>
                                    <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                      <input name="register_contact_lastname_2" type="text" id="register_contact_lastname_2" value="<?php echo $register_values["LastName_2"];?>" />
                                    <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                                  </tr>
                                  <tr>
                                   <td valign="top">เบอร์โทรศัพท์</td>
                                    <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                      <input name="register_contact_phone_2" type="text" id="register_contact_phone_2" value="<?php echo $register_values["user_telephone_2"];?>" />
                                    <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                                    
                                    <td valign="top">ตำแหน่ง</td>
                                     <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                     
                                     <span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                       <input name="register_position_2" type="text" id="register_position_2" value="<?php echo $register_values["user_position_2"];?>" />
                                     <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                 </span></span>
                                     
                                     </td>
                                  </tr>
								  
								  
								  
								  <?php if(1==0){?>
								  <tr>
									<td colspan="4">
										<hr>
											<div align=center>
												<input type="button" value="เพิ่มข้อมูลกรรมการบริษัท" />
											</div>
										
									</td>
								  </tr>
								  <?php }?>
								  
								  
								  
								  
								  <?php for($i = 2; $i <= 3; $i++){ 
								  
								  
									$contact_row = getFirstRow("
													
													
												select
													*
												from
													users_contacts
												where
													seq = '$i'
													and
													user_id = '$this_id'
									
									
												");								  
								  
								  ?>
								  
								  <tr>
									<td colspan="4">
									
									<strong>ข้อมูลกรรมการบริษัท <?php echo $i;?></strong></td>
								  </tr>
								  
								  <tr>
                                    <td valign="top">ชื่อ</td>
                                    <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                      <input name="register_contact_name_2<?php echo $i;?>" type="text"  value="<?php echo $contact_row["FirstName"];?>" />
                                    <span class="style86" style="padding: 10px 0 10px 0;"></span>                                </span></span></td>
                                    <td valign="top">นามสกุล</td>
                                    <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                      <input name="register_contact_lastname_2<?php echo $i;?>" type="text"  value="<?php echo $contact_row["LastName"];?>" />
                                    <span class="style86" style="padding: 10px 0 10px 0;"></span>                                </span></span></td>
                                  </tr>
                                  <tr>
                                   <td valign="top">เบอร์โทรศัพท์</td>
                                    <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                      <input name="register_contact_phone_2<?php echo $i;?>" type="text" value="<?php echo $contact_row["user_telephone"];?>" />
                                    <span class="style86" style="padding: 10px 0 10px 0;"></span>                                </span></span></td>
                                    
                                    <td valign="top">ตำแหน่ง</td>
                                     <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                     
                                     <span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                       <input name="register_position_2<?php echo $i;?>" type="text"  value="<?php echo $contact_row["user_position"];?>" />
                                     <span class="style86" style="padding: 10px 0 10px 0;"></span>                                 </span></span>
                                     
                                     </td>
                                  </tr>
								  
								  <?php }?>
								  
								 
								  
                              
                              
                              <?php }?>
                              
                              
                              
                              
                             
                               
                               
                               <tr>
                                <td colspan="4"><hr />
                                <strong>แนบเอกสารยืนยันตัวเอง</strong> <font style="font-size:11px;">เป็นไฟล์ jpg, gif, png หรือ pdf เท่านั้น</font></td>
                              </tr>
                              
                               <tr bgcolor="#fcfcfc" >
                                 <td valign="top" style="padding:5px;">
                                 
                                 <img id="re01" src="exclaim_small.jpg" title="กรุณาแนบไฟล์"  height="15" />
                                 
                                 หนังสือแสดงความตกลงในการแจ้งรายงานการปฏิบัติตามกฎหมายจ้างงานคนพิการผ่านทางอิเล็กทรอนิกส์ 
                                 
                                 
                                 <?php if($sess_user_enabled != 1){?>
                                 <a href="create_pdf_5.php?the_cid=<?php echo $register_values[user_meta];?>" target="_blank" style="font-weight: normal">(download แบบฟอร์ม)</a>
                                 <?php }?>
                                 
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 
                                 <?php 
								 
								 			//yoes 20160512
											//if enabled then cant edit this
											if($sess_user_enabled == 1){
												$disable_delete = 1;	
											}
								 
								 			$required_doc++;
									
											$this_id = $this_id;
											
											$file_type = "register_doc_1";
											
											include "doc_file_links.php";
									
									?>
                                    <?php if($have_doc_file){ $required_doc--;?><br /><script>$('#re01').hide();</script><?php }?>
                                    
                                    <?php if($sess_user_enabled != 1){?>
                                    <input name="register_doc_1"  type="file"  />
                                    <?php }?>
                                 
                                 </td>
                               </tr>
                               
                               
                              
                               
                               <tr  >
                                 <td valign="top" style="padding:5px;">
                                 
                                  <img id="re022" src="exclaim_small.jpg" title="กรุณาแนบไฟล์"  height="15" />
                                 
                                แบบคำขอเปิดใช้งาน Username เพื่อนำส่งข้อมูลการปฏิบัติตามกฎหมายการจ้างงานผ่านทางอิเล็กทรอนิกส์
                                
                                <?php if($sess_user_enabled != 1){?>
                                <a href="create_pdf_6.php?the_cid=<?php echo $register_values[user_meta];?>&the_uid=<?php echo $sess_userid;?>" target="_blank" style="font-weight: normal">(download แบบฟอร์ม)</a>
                                 
                                 <?php }?>
                                 
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 
                                  <?php 
								  
								  			$required_doc++;
									
											$this_id = $this_id;
											
											$file_type = "register_doc_22";
											
											include "doc_file_links.php";
									
									?>
                                     <?php if($have_doc_file){ $required_doc--;?><br /><script>$('#re022').hide();</script><?php }?>
                                     <?php if($sess_user_enabled != 1){?>
                                    <input name="register_doc_22"  type="file"  />
                                    <?php }?>
                                 
                                 </td>
                               </tr>
                               
                               
                             
                               
                               
                                <tr bgcolor="#fcfcfc">
                                 <td valign="top" style="padding:5px;">
                                 
                                  <img id="re03" src="exclaim_small.jpg" title="กรุณาแนบไฟล์"  height="15" />
                                 
                                 สำเนาบัตรประชาชนของผู้มีอำนาจที่ลงชื่อในคำขอ
                                 
                                 
                                </td>
                                 <td colspan="3" valign="top" style="padding:5px;">
                                 
                                  <?php 
											$required_doc++;
											
											$this_id = $this_id;
											
											$file_type = "register_employee_card";
											
											include "doc_file_links.php";
									
									?>
                                     <?php if($have_doc_file){ $required_doc--;?><br /><script>$('#re03').hide();</script><?php }?>
                                     <?php if($sess_user_enabled != 1){?>
                                    <input name="register_employee_card"  type="file"  />
                                    <?php }?>
                                   
                                 </td>
                                
                               </tr>
                               
                               
                              
                               
                                <tr  >
                                 <td valign="top" style="padding:5px;">
                                 
                                  <img id="re05" src="exclaim_small.jpg" title="กรุณาแนบไฟล์"  height="15" />
                                 
                                หนังสือรับรองนิติบุคคลที่มีอายุไม่เกิน 90 วัน
                                 
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 
                                   <?php 
											$required_doc++;
											
											$this_id = $this_id;
											
											$file_type = "register_company_card";
											
											include "doc_file_links.php";
									
									?>
                                     <?php if($have_doc_file){ $required_doc--;?><br /><script>$('#re05').hide();</script><?php }?>
                                     <?php if($sess_user_enabled != 1){?>
                                    <input name="register_company_card"  type="file"  />
                                    <?php }?>
                                 
                                 </td>
                               </tr>
                               
                               
                               
                               
                               
                               
                               
                               <tr bgcolor="#fcfcfc" >
                                 <td valign="top" style="padding:5px;">
                                 
                                  <?php if(1==0){ //yoes 20161130 - remove this from requirments?><img id="re02" src="exclaim_small.jpg" title="กรุณาแนบไฟล์"  height="15" /><?php }?>
                                 
                                หนังสือมติที่ประชุมคณะกรรมการที่ให้รายงานการปฏิบัติตามกฎหมายจ้างงานคนพิการผ่านทางอิเล็กทรอนิกส์(ถ้ามี)
                                
                                <?php if($sess_user_enabled != 1){?>
                                 <a href="4_meeting_form.doc" target="_blank" style="font-weight: normal">(ตัวอย่าง)</a>
                                 <?php }?>
                                 
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 
                                  <?php 
								  
								  			//$required_doc++;
									
											$this_id = $this_id;
											
											$file_type = "register_doc_2";
											
											include "doc_file_links.php";
									
									?>
                                    
                                   <?php if(1==0){ //yoes 20161130 - remove this from requirments?>
                                     <?php if($have_doc_file){ $required_doc--;?><br /><script>$('#re02').hide();</script><?php }?>
                                   <?php }?>  
                                     
                                     
                                     <?php if($sess_user_enabled != 1){?>
                                    <input name="register_doc_2"  type="file"  />
                                    <?php }?>
                                 
                                 </td>
                               </tr>
                               
                               
                               
                               <tr  >
                                 <td valign="top" style="padding:5px;">
                                 
                                 
                                 
                                 หนังสือมอบอำนาจ (ถ้ามี)
                                 
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 
                                 
                                   <?php 
									
											$this_id = $this_id;
											
											$file_type = "register_doc_3";
											
											include "doc_file_links.php";
									
									?>
                                    <?php if($have_doc_file){?><br /><?php }?>
                                    <?php if($sess_user_enabled != 1){?>
                                    <input name="register_doc_3"  type="file"  />
                                    <?php }?>
                                 
                                </td>
                               </tr>
                               
                               
                              
                               
                                <tr bgcolor="#fcfcfc" >
                                 <td valign="top" style="padding:5px;">
                                 
                                สำเนาบัตรประชาชนของผู้รับมอบอำนาจ (ถ้ามี)
                                 
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 
                                   <?php 
									
											
											$this_id = $this_id;
											
											$file_type = "register_doc_4";
											
											include "doc_file_links.php";
									
									?>
                                    <?php if($have_doc_file){?><br /><?php }?>
                                    <?php if($sess_user_enabled != 1){?>
                                    <input name="register_doc_4"  type="file"  />
                                    <?php }?>
                                 
                                 </td>
                               </tr>
                               
                               
                              
                               
                               
                               <?php if(1==0){?>
                               <tr>
                                 <td valign="top" style="padding:5px;"> 
                                 
                                  <img id="re04" src="exclaim_small.jpg" title="กรุณาแนบไฟล์"  height="15" />
                                 
                                 แสดงต้นฉบับบัตรประชาชนของผู้รับมอบอำนาจหรือผู้ยื่นคำขอ
                                 
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 <?php 
									
											$required_doc++;
											
											$this_id = $this_id;
											
											$file_type = "register_id_card";
											
											include "doc_file_links.php";
									
									?>
                                     <?php if($have_doc_file){ $required_doc--;?><br /><script>$('#re04').hide();</script><?php }?>
									<?php if($sess_user_enabled != 1){?>
                                 <input name="register_id_card"  type="file" />
                                 <?php }?>
                                 
                                 </td>
                                
                               </tr>
                               <?php }?>
                               
                               
                               <?php if($sess_user_enabled != 1 && 1==0){?>
                               <tr>
                                <td colspan="4"><hr />
                                
                                <strong>
                                
                                ในกรณีที่ไม่สะดวกในการแนบไฟล์ได้ - 
                                
                                <a href="create_pdf_6.php?the_cid=<?php echo $register_values[user_meta];?>&the_uid=<?php echo $sess_userid;?>" target="_blank">คลิกที่นี่</a>
                                
                                 <br />เพื่อ download
                                 
                                  แบบฟอร์มแบบคําขอเปิดใช้งาน username แบบ offline
                                  <br />
                                                                                                
                                หลังจากทำการกรอกข้อมูล และแนบเอกสารครบถ้วนแล้ว ให้ส่งเอกสารมาที่ กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ
                                </strong>
                                
                                </td>
                              </tr>
                              
                              <?php }?>
                               
                               <?php }?>
                               
                               <script>
																			
																			
									function alertContents() {
										if (http_request.readyState == 4) {
											if (http_request.status == 200) {
												//alert(http_request.responseText.trim()); 
												document.getElementById("loading_"+http_request.responseText.trim()).style.display = 'none';
											} else {
												//alert('There was a problem with the request.');
											}
										}
									}
								  </script>
                              
                              
                              	
                              
                          </table></td>
                        </tr>
                        
                        
                        
                        
                        <tr>
                          <td><hr />
                              <div align="center">
                              
                              	<?php if($mode == "edit" ){?>
                                
									<?php if($required_doc  ){?>
                                    
                                     <div align="center" style="padding: 5px;" id="register_bottom_message">
                                      		<font color="#FF6600">กรุณากรอกข้อมูล และแนบเอกสารที่มีเครื่องหมาย <img id="re01" src="exclaim_small.jpg" title="กรุณาแนบไฟล์"  height="15" /> ให้ครบถ้วน</font> - เจ้าหน้าที่จะอนุมัติผู้ใช้งานที่ข้อมูลครบถ้วนเท่านั้น
                                       </div>
                                                            
                                      
                                    
                                    <?php }elseif(!$no_pass && !$required_doc && $sess_user_enabled == "0" ){?>
                                    
                                        <div align="center" style="padding: 5px;" id="register_bottom_message">
                                        		<font color="#009900">ระบบได้รับข้อมูลใบสมัครของท่านเรียบร้อยแล้ว</font> - กรุณารอการอนุมัติจากเจ้าหน้าที่ โดยจะมีอีเมล์แจ้งกลับไป สอบถามเพิ่มเติม 02-106-9300
												
												<br>
												<font color=red style="font-size: 22px;">** กรุณาส่งเอกสารยืนยันตัวเองฉบับจริงเข้ามาให้เจ้าหน้าที่ <br>เจ้าหน้าที่จะไม่ทำการอนุมัติการใช้งานจนกว่าจะมีการส่งเอกสารยืนยันตัวเองฉบับจริงเข้ามา **</font>
                                         </div>
                                    
                                    <?php }?>
									
									
									<?php if($sess_user_enabled == "9"){?>
                                    
                                        <div align="center" style="padding: 5px;" id="register_bottom_message">
                                        		
												<font color=red style="font-size: 22px;">** กรุณาทำการยืนยัน email จาก email ที่ทางระบบได้ส่งให้ท่าน<br>เจ้าหน้าที่จะไม่ทำการอนุมัติการใช้งานจนกว่าจะมีการทำการยืนยัน email **</font>
                                         </div>
                                    
                                    <?php }?>
									
									
                                    
                                    
                                    <script>									  	
										$("#register_top_message").html($("#register_bottom_message").html());										
									  </script>
                                
                                <input id="btn_new_register" type="submit" value="บันทึกข้อมูล" />
                                <?php }else{?>
                                <input id="btn_new_register" type="submit" value="สมัครเข้าใช้งาน" />
                                
                                
                                <script>
									
									/*$('#view_user_form').one('submit', function() {
										$(this).find('input[type="submit"]').attr('disabled','disabled');
									});*/
								</script>
                                
                   				 <?php }?>
                                
                          </div></td>
                        </tr>
                        
                        
                        
                        <?php if($sess_accesslevel == 1){?>
                        
                        <tr>
                          <td><hr />
                              <div align="center">
                              
                              <a href="report_20.php?mod_register_id=<?php echo $register_values["register_id"];?>" target="_blank">
                              ดูรายงานการบันทึกข้อมูลเจ้าหน้าที่ของสถานประกอบการ
                              </a>
                                
                          </div></td>
                        </tr>
                        
                         <tr>
                          <td>
                          
                          <hr />
                          <strong>เอกสารที่เคยส่งไปแล้ว</strong>
                          
                          </td>
                        </tr>
                        
                        <tr>
                            <td>
                            
                            	<table border="0" cellpadding="5" style="border-collapse:collapse;">
                                  <tr bgcolor="#9C9A9C" align="center" >
                                    <td><span class="column_header">สำหรับปี</span></td>
                                    <td><span class="column_header">ไฟล์</span></td>
                                    <td><span class="column_header">วันที่ส่งไฟล์</span></td>
                                    <td><span class="column_header"></span></td>
                                    <td>&nbsp;</td>
                                  </tr>
                                  
                                  <?php
								  
								  	$pay_sql = "select 
													* 
												from 
													modify_history_register 
												where 
													mod_register_id = '".$register_values["register_id"]."'
													and mod_type = 3
												order by mod_year desc
												";
												
									//echo $pay_sql;
								  
								  	$pay_result = mysql_query($pay_sql);
						
									while ($pay_row = mysql_fetch_array($pay_result)) {

								  
								  ?>
                                  <tr>
                                    <td><?php echo formatYear($pay_row["mod_year"]);?></td>
                                    <td><a href="register_doc/<?php echo $pay_row["mod_file"];?>"><?php echo $pay_row["mod_file"];?></a></td>
                                    <td><?php echo formatDateThai($pay_row["mod_date"]);?></td>
                                    <td><?php echo $pay_row["mod_desc"];?></td>
                                    <td></td>
                                  </tr>
                                  <?php }?>
                                  
                                </table>

                            
                            
                            </td>
                        </tr>
                        
                        
                        <?php }//$sess_accesslevel == 1?>
                        
                      </table>
                      
                </form>
                
                </div> <!--- div align =center -->
                
                
                <script language='javascript'>
						<!--
						function validate_register(frm) {
							
							
							
							<?php if($mode == "add"){?>
							if($('#register_name_used').css("display") != "none"){
								alert("กรุณาเลือกชื่อ user name ใหม่");
								frm.register_name.focus();
								return false;	
							}
							
							if(frm.register_org_code.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: เลขที่บัญชีนายจ้าง");
								frm.register_org_code.focus();
								return (false);
							}
							
							
							if(frm.register_name.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อ user name");
								frm.register_name.focus();
								return (false);
							}
							
							
							var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890.-_";
						   var checkStr = frm.register_name.value;
						   var allValid = true;
						   for (i = 0;  i < checkStr.length;  i++)
						   {
							 ch = checkStr.charAt(i);
							 for (j = 0;  j < checkOK.length;  j++)
							   if (ch == checkOK.charAt(j))
								 break;
							 if (j == checkOK.length)
							 {
							   allValid = false;
							   break;
							 }
						   }
						   if (!allValid)
						   {
							 alert("ชื่อ user name สามารถเป็นภาษาอังกฤษหรือตัวเลขเท่านั้น");
							 frm.register_name.focus();
							 return (false);
						   }
						   
						   //alert($('#register_org_name').val());
							if($('#register_org_name').val().length < 1)
							{
								alert("เลขที่บัญชีนายจ้างไม่ถูกต้อง กรุณาใส่เลขที่บัญชีนายจ้าง และทำการ 'ตรวจสอบเลขที่บัญชีนายจ้าง' อีกครั้ง");								
								return (false);
							}
							
							<?php }?>
							
							
							if($('#email_used').css("display") != "none"){
								alert("กรุณาเลือกชื่อ email ใหม่");
								frm.register_email.focus();
								return false;	
							}
							
							if(frm.register_email.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: อีเมล์");
								frm.register_email.focus();
								return (false);
							}
														
							
							<?php if($mode == "add"){ ?> 
							
							if(frm.register_password.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: รหัสผ่าน");
								frm.register_password.focus();
								return (false);
							}
							
							if(frm.register_password.value != frm.register_password_2.value)
							{
								alert("กรุณาใส่ข้อมูล: ยืนยัน password ใหม่ไม่ถูกต้อง");
								frm.register_password_2.focus();
								return (false);
							}
							
							<?php }?>
							
							
							
							<?php if($mode == "edit"){ ?> 
							
							
							<?php if($no_pass){?>
							if(frm.register_password.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: รหัสผ่าน");
								frm.register_password.focus();
								return (false);
							}
							<?php }?>

							if(frm.register_password.value != frm.register_password_2.value)
							{
								alert("กรุณาใส่ข้อมูล: ยืนยัน password ใหม่ไม่ถูกต้อง");
								frm.register_password_2.focus();
								return (false);
							}
							
							
							
							if(frm.register_contact_name.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อผู้ติดต่อ");
								frm.register_contact_name.focus();
								return (false);
							}
							
							if(frm.register_contact_lastname.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: นามสกุลผู้ติดต่อ");
								frm.register_contact_lastname.focus();
								return (false);
							}
														
							if(frm.register_contact_phone.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: เบอร์โทรศัพท์");
								frm.register_contact_phone.focus();
								return (false);
							}
							
							
							<?php }?>
							
							
							
							//----
							$( "#btn_new_register" ).attr('disabled','disabled');
							
							return(true);									
						
						}
						-->
					
					</script>
                        
                   
                   
                   
              </td>
            </tr>
            
             <tr>
                <td align="right" colspan="2">
                    <?php include "bottom_menu.php";?>
                </td>
            </tr>  
            
		</table>                            
        
        </td>
    </tr>
    
</table>    

<?php if($_GET["user_added"]=="user_added"){ ?>
                         <script>
                         document.getElementById("view_user_form").style.display = "none";
						 </script>
                    
<?php }?>

</body>
</html>