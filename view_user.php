<?php

	include "db_connect.php";
	include "scrp_config.php";
	
		//current mode
	if(is_numeric($_GET["id"])){
		
		//
		if($sess_userid == $_GET["id"]){
			//this user is an owner of this page
			$is_owner = 1;
		
		
		}elseif($sess_accesslevel == 3){
			
			//yoes 20161201 --> allow user 3 to use this page
			
			
		}elseif($sess_accesslevel != 1 && $sess_accesslevel != 2 && !$sess_can_manage_user){  
		
			//yoes 20160118 -- now allow user พมจ to edit "approve" users
			// yoes 20141007 ---> if is not admin AND didnt have permisison...
		
			//if not, check if this user is an admin
			header("location: index.php");
			exit();
		}
		
				
		$this_id = $_GET["id"]*1;
		
		$post_row = getFirstRow("select * 
								from 
									users
								where 
									user_id  = '$this_id'
								limit 0,1");
								
		//vars to use
		$output_fields = array(
						
						'user_id'
						,'user_name'
						,'user_password'
						,'AccessLevel'
						,'Department'
						,'FirstName'
						,'LastName'
						,'LastLoginDatetime' 
						,'user_meta' 
						
						,'user_email' 
						,'user_position' 
						,'user_telephone' 
						
						, 'user_enabled'
						, 'user_can_manage_user'
						
						, 'user_commercial_code'
						
						, 'user_created_date'
						, 'user_approved_date'
						, 'user_approved_by'
						, 'user_ip_address' 
						
						, 'reopen_case_password'
						
						, 'FirstName_2'
						, 'LastName_2'
						, 'user_telephone_2'
						, 'user_position_2'
					
						
						
						
						);
				
		for($i = 0; $i < count($output_fields); $i++){
			//clean all inputs
			$output_values[$output_fields[$i]] .= doCleanOutput($post_row[$output_fields[$i]]);
		}
		
		
		//yoes 20141007 --> also special session for พมจ users
		
		
		//yoes 20160118 -- allow พก to edit users
		//yoes 20161201 -- allow pmj to edit users -> but only in own's province only
		
		$respond_province = getFirstItem("select province from company where cid = '".$output_values["user_meta"]."'");
		
		if(($sess_can_manage_user && $sess_meta == $output_values["user_meta"]) || $sess_accesslevel == 1 || $sess_accesslevel == 2 || ($sess_accesslevel == 3 && $sess_meta == $respond_province )){ 
		
			
			$can_edit_user = 1;
			
		}elseif($output_values["AccessLevel"] == 4 && $sess_can_manage_user){
				
			//if this user is company -> also check if it's company in responsible's province or not
			$respond_province = getFirstItem("select province from company where cid = '".$output_values["user_meta"]."'");
			
			if($respond_province == $sess_meta){
				$can_edit_user = 1;
			}
			
		}
		
		
		if(!$can_edit_user && !$is_owner){
			//cant edit this	
			header("location: index.php");
			exit();
		}
		
		
	}else if($_GET["mode"] == "add"){
	
		$mode = "add";
		$this_id = "new";
		
		if(is_numeric($_GET["cid"])){
			$this_cid = $_GET["cid"];
		}
	
		//yoes 20141007
		if($sess_can_manage_user){
			$can_edit_user = 1;			
		}
	
	}else{
		header("location: index.php");
	}	

?>
<?php 
	include "header_html.php";
	include "global.js.php";
?>
              <td valign="top">
                	
                    
                    
                <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                  
                <?php if($mode == "add"){ ?>  
	                เพิ่ม User ใหม่
                <?php }else{ ?>
                
                	 ข้อมูล User: <font color="#006699"><?php echo $output_values["user_name"];?></font>
                
                <?php } ?>
                
                </h2>
                    
                    <div style="padding:5px 0 0px 2px">
                    <?php if($sess_accesslevel == 1){ ?>
                    <a href="user_list.php">users ทั้งหมด</a> > 
                    <?php }?>
                    
                    
                    <?php if($is_owner == 1){ ?>
                    เปลี่ยนรหัสผ่าน >
                    <?php }?>
                    
                    <?php if($mode == "add"){ ?>  
                    	เพิ่ม User ใหม่
                    <?php }else{?>
	                    user name: <?php echo $output_values["user_name"];?>
                    <?php }?>
                    </div>
                    
                   
                    <?php 
						if($_GET["user_added"]=="user_added"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มข้อมูลเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["updated"]=="updated"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["oldpass"]=="oldpass"){
					?>							
                         <div style="color:#CC3300; padding:5px 0 0 0; font-weight: bold;">* Password เดิมไม่ถูกต้อง!</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["duped"]=="duped"){
					?>							
                         <div style="color:#CC3300; padding:5px 0 0 0; font-weight: bold;">* User Name มีอยู่ในระบบแล้ว</div>
                    <?php
						}					
					?>
                    
                   
                                      
                    <form method="post" action="scrp_update_user.php" enctype="multipart/form-data" onsubmit="return validate_user(this);">
                    <input name="user_id" type="hidden" value="<?php echo $this_id;?>" />
                      <table border="0" cellpadding="0" >
                        <tr>
                          <td> <hr /><table border="0" style="padding:0px 0 0 50px;" >
                              <tr>
                                <td colspan="2">
                                	<span style="font-weight: bold">ข้อมูล user</span>
                                </td>
                               
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">User Name</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <?php if($mode == "add"){ ?> 
	                                   <input name="user_name" type="text" id="user_name" value="<?php echo $output_values["user_name"];?>"  />
                                  <?php }else{ ?>
                                  	  <strong><?php echo $output_values["user_name"];?></strong>
                                      <input name="user_name_origin" type="hidden" id="user_name_origin" value="<?php echo $output_values["user_name"];?>"  />
                                  <?php } ?>
                                </span></td>
                              </tr>
                              
                              
                              
                              
                              <?php if($is_owner){ ?>
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">Password เดิม</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_password_old" type="password"  value=""  />
                                </span></td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">Password ใหม่</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_password_new_1" type="password"  value=""  />
                                </span></td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ยืนยัน Password ใหม่</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_password_new_2" type="password"  value=""  />
                                </span></td>
                              </tr>
                              
                              <?php }else{ ?>
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">Password </span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_password" type="password" id="user_password" value="<?php echo $output_values["user_password"];?>"  />
                                </span></td>
                              </tr>
                              
                              <?php } ?>
                              
                              
                              <?php if($sess_accesslevel == 1 || $can_edit_user){ ?>
                              
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">เปิดใช้งาน</span></td>
                                <td>
                                                                	
                                    
                                    <?php include "ddl_user_enabled.php";?>
                                    
                                    <input name="user_enabled_origin" type="hidden" value="<?php echo $output_values["user_enabled"];?>" />
                                
                                </td>
                              </tr>
                              
                              
                              <tr>
                                <td>ชื่อ</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="FirstName" type="text" id="FirstName" value="<?php echo $output_values["FirstName"];?>" />
                                </span></span></td>
                              </tr>
                              <tr>
                                <td>นามสกุล</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="LastName" type="text" id="LastName" value="<?php echo $output_values["LastName"];?>"  />
                                </span> </td>
                              </tr>
                              
                              <?php if($output_values["AccessLevel"] != 4){ //yoes  20151122 -- company user wont see this?>
                              <tr>
                                <td>หน่วยงาน</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="Department" type="text" id="Department" value="<?php echo $output_values["Department"];?>"  />
                                </span></td>
                              </tr>
                              <?php }?>
                              
                               <tr>
                                <td>เบอร์โทรศัพท์</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_telephone" type="text" id="user_telephone" value="<?php echo $output_values["user_telephone"];?>"  />
                                </span></td>
                              </tr>
                              
                               <tr>
                                <td>email</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_email" type="text" id="user_email" value="<?php echo $output_values["user_email"];?>"  />
                                </span>
								
								
								
								</td>
                              </tr>
                              
                               <tr>
                                <td>ตำแหน่ง</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_position" type="text" id="user_position" value="<?php echo $output_values["user_position"];?>"  />
                                </span></td>
                              </tr>
                              
                              
							<?php if(isset($this_cid) || $output_values["AccessLevel"]=="4"){
								  
								  		if($output_values["AccessLevel"]=="4"){
								  			$this_cid = $output_values["user_meta"];
										}
								  ?>
                                  <tr>
                                    <td><hr />ชนิดของ User</td>
                                    <td><hr /> <strong style="color:#006699">เจ้าหน้าที่สถานประกอบการ: <?php 
										$company_row = getFirstRow("select CompanyNameThai,CompanyTypeCode from company where cid ='$this_cid'");
										echo formatCompanyName($company_row["CompanyNameThai"],$company_row["CompanyTypeCode"]);
										?></strong></td>
                                  </tr>
                                  
                                  
                                  
                                   <tr>
                                    <td>เลขทะเบียนนิติบุคคลของกระทรวงพาณิชย์ </td>
                                    <td>
									<strong ><?php echo $output_values["user_commercial_code"];?></strong></td>
                                  </tr>
                                  
                                  
                                  <input name="AccessLevel" type="hidden" value="4" />
                                  <input name="cid" type="hidden" value="<?php echo $this_cid;?>" />
                                 
                                 
                                 
                                 
                                 <?php if($output_values[user_enabled] == 1){?>
                                 
                                 
                                 <tr>
                                    <td>ชื่อกรรมการบริษัท</td>
                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                    <?php echo $output_values["FirstName_2"];?>
                                      <input name="FirstName_2" type="hidden" id="FirstName_2" value="<?php echo $output_values["FirstName_2"];?>"  />
                                    </span></td>
                                  </tr>
                                  
                                      <tr>
                                    <td>นามสกุลกรรมการบริษัท</td>
                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                    <?php echo $output_values["LastName_2"];?>
                                      <input name="LastName_2" type="hidden" id="LastName_2" value="<?php echo $output_values["LastName_2"];?>"  />
                                    </span></td>
                                  </tr>
                                  
                                     <tr>
                                    <td>เบอร์โทรศัพท์กรรมการบริษัท</td>
                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                    <?php echo $output_values["user_telephone_2"];?>
                                      <input name="user_telephone_2" type="hidden" id="user_telephone_2" value="<?php echo $output_values["user_telephone_2"];?>"  />
                                    </span></td>
                                  </tr>
                                   <tr>
                                    <td>ตำแหน่งกรรมการบริษัท <hr /></td>
                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                    	<?php echo $output_values["user_position_2"];?>
                                      <input name="user_position_2" type="hidden" id="user_position_2" value="<?php echo $output_values["user_position_2"];?>"  />
                                    </span>
                                    
                                    <hr />
                                    </td>
                                  </tr>
                                 
                                 
                                 
                                 <?php }else{?>
                                  <tr>
                                    <td>ชื่อกรรมการบริษัท</td>
                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                      <input name="FirstName_2" type="text" id="FirstName_2" value="<?php echo $output_values["FirstName_2"];?>"  />
                                    </span></td>
                                  </tr>
                                  
                                      <tr>
                                    <td>นามสกุลกรรมการบริษัท</td>
                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                      <input name="LastName_2" type="text" id="LastName_2" value="<?php echo $output_values["LastName_2"];?>"  />
                                    </span></td>
                                  </tr>
                                  
                                     <tr>
                                    <td>เบอร์โทรศัพท์กรรมการบริษัท</td>
                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                      <input name="user_telephone_2" type="text" id="user_telephone_2" value="<?php echo $output_values["user_telephone_2"];?>"  />
                                    </span></td>
                                  </tr>
                                   <tr>
                                    <td>ตำแหน่งกรรมการบริษัท <hr /></td>
                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                      <input name="user_position_2" type="text" id="user_position_2" value="<?php echo $output_values["user_position_2"];?>"  />
                                    </span>
                                    
                                    <hr />
                                    </td>
                                  </tr>
                              
                              <?php }?>
                                 
                                 
                                 
                                 
                                  <tr bgcolor="#fcfcfc" >
                                 <td valign="top" style="padding:5px;">
                                 
                                 <?php if(1==0){?>แบบคำขอรับการใช้งาน<br />ซึ่งลงชื่อโดยผู้มีอำนาจของนิติบุคคล<br />ตามที่ได้จดทะเบียนไว้กับกรมฯ<?php }?>
                                 
                                 หนังสือแสดงความตกลงในการแจ้งข้อมูลการจ้างงานผ่านทางอิเล็กทรอนิกส์ 
                                 
                                 
                                 <a href="3_sign_form.pdf" target="_blank" style="font-weight: normal"></a>
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 
                                 <?php 
									
											if($output_values[user_enabled] == 1){
												$disable_delete = 1;
											}
									
											$this_id = $this_id;
											
											$file_type = "register_doc_1";
											
											include "doc_file_links_ejob.php";
									
									?>
                                    <?php if($have_doc_file){?><br /><?php }?>
                                    <?php if($output_values[user_enabled] != 1){?>
                                    <input name="register_doc_1"  type="file"  />
                                    <?php }?>
                                 
                                 </td>
                               </tr>
                               
                               
                               <tr  >
                                 <td valign="top" style="padding:5px;">
                                 
                                 แบบคำขอเปิดใช้งาน Username เพื่อนำส่งข้อมูลการปฏิบัติตามกฎหมายการจ้างงานผ่านทางอิเล็กทรอนิกส์
                                
                                 <a href="4_meeting_form.doc" target="_blank" style="font-weight: normal"></a>
                                 
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 
                                  <?php 
									
											$this_id = $this_id;
											
											$file_type = "register_doc_22";
											
											include "doc_file_links_ejob.php";
									
									?>
                                    <?php if($have_doc_file){?><br /><?php }?>
                                    <?php if($output_values[user_enabled] != 1){?>
                                    <input name="register_doc_22"  type="file"  />
                                    <?php }?>
                                 
                                 </td>
                               </tr>
                               
                               
                                 <tr  >
                                 <td valign="top" style="padding:5px;">
                                 
                                หนังสือแสดงความตกลงในการนำส่งงบการเงินผ่านทางอิเล็กทรอนิกส์
                                <br />
                                หรือมติที่ประชุมคณะกรรมการที่ให้นำส่งงบการเงินผ่านทางอิเล็กทรอนิกส์
                                
                                 <a href="4_meeting_form.doc" target="_blank" style="font-weight: normal"></a>
                                 
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 
                                  <?php 
									
											$this_id = $this_id;
											
											$file_type = "register_doc_2";
											
											include "doc_file_links_ejob.php";
									
									?>
                                    <?php if($have_doc_file){?><br /><?php }?>
                                    <?php if($output_values[user_enabled] != 1){?>
                                    <input name="register_doc_2"  type="file"  />
                                    <?php }?>
                                 
                                 </td>
                               </tr>
                               
                               
                               <tr bgcolor="#fcfcfc" >
                                 <td valign="top" style="padding:5px;">
                                 
                                 หนังสือมอบอำนาจ (ถ้ามี)
                                 
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 
                                 
                                   <?php 
									
											$this_id = $this_id;
											
											$file_type = "register_doc_3";
											
											include "doc_file_links_ejob.php";
									
									?>
                                    <?php if($have_doc_file){?><br /><?php }?>
                                    <?php if($output_values[user_enabled] != 1){?>
                                    <input name="register_doc_3"  type="file"  />
                                    <?php }?>
                                 
                                </td>
                               </tr>
                               
                               
                               <tr >
                                 <td valign="top" style="padding:5px;">
                                 
                                 
                                 สำเนาบัตรประชาชนของผู้มีอำนาจที่ลงชื่อในคำขอ
                                 
                                 
                                </td>
                                 <td colspan="3" valign="top" style="padding:5px;">
                                 
                                  <?php 
									
											$this_id = $this_id;
											
											$file_type = "register_employee_card";
											
											include "doc_file_links_ejob.php";
									
									?>
                                    <?php if($have_doc_file){?><br /><?php }?>
                                    <?php if($output_values[user_enabled] != 1){?>
                                    <input name="register_employee_card"  type="file"  />
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
											
											include "doc_file_links_ejob.php";
									
									?>
                                    <?php if($have_doc_file){?><br /><?php }?>
                                    <?php if($output_values[user_enabled] != 1){?>
                                    <input name="register_doc_4"  type="file"  />
                                    <?php }?>
                                 
                                 </td>
                               </tr>
                               
                              
                               
                               
                                <tr >
                                 <td valign="top" style="padding:5px;">
                                 
                                 หนังสือรับรองนิติบุคคล
                                 
                                </td>
                                 <td colspan="3" valign="top" style="padding:5px;">
                                 
                                  <?php 
									
											$this_id = $this_id;
											
											$file_type = "register_company_card";
											
											include "doc_file_links_ejob.php";
									
									?>
                                    <?php if($have_doc_file){?><br /><?php }?>
                                    <?php if($output_values[user_enabled] != 1){?>
                                    <input name="register_company_card"  type="file"  />
                                    <?php }?>
                                   
                                 </td>
                                
                               </tr>
                               
                               
                                <tr>
                                 <td valign="top" style="padding:5px;"> 
                                 
                                 
                                 
                                 แสดงต้นฉบับบัตรประชาชนของผู้รับมอบอำนาจหรือผู้ยื่นคำขอ <br />(เป็นข้อมูลจาก e-service ระบบเก่า)
                                 
                                 </td>
                                 <td valign="top" colspan="3" style="padding:5px;">
                                 <?php 
									
											$this_id = $this_id;
											
											$file_type = "register_id_card";
											
											include "doc_file_links_ejob.php";
									
									?>
                                    <?php if($have_doc_file){?><br /><?php }?>
                                    <?php if($output_values[user_enabled] != 1){?>
                                 <input name="register_id_card"  type="file" />
                                 <?php }?>
                                 
                                 </td>
                                
                               </tr>
                                 
                                 
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
                                 
                                  
                                  <?php }else{ ?>
                                  
                                  <tr>
                                    <td>ชนิดของ User</td>
                                    <td>
                                    
                                    	<?php if($sess_accesslevel == 1){ //admin can manage roles?>
	                                      <?php include "ddl_access_level.php";?>
                                        <?php }elseif($sess_accesslevel == 2){ ?>
                                        	
                                            เจ้าหน้าที่ พก.
                                            <input name="AccessLevel" value="2" type="hidden" />
                                        
                                        <?php }elseif($sess_accesslevel == 3){ // พมจ cant manage row?>
                                        	เจ้าหน้าที่ พมจ.
                                            <input name="AccessLevel" value="3" type="hidden" />
                                        <?php }?>
                                      
                                      
                                    </td>
                                  </tr>
                                  <tr id="the_province">
                                    <td>จังหวัด</td>
                                    <td><?php include "ddl_org_province.php";?></td>
                                  </tr>
                                  
                                  
                                 
                                  
                                  <tr id="can_edit_user">
                                    <td>สามารถจัดการ user ในจังหวัดได้?</td>
                                    <td><input name="user_can_manage_user" type="checkbox" value="1" <?php if($output_values["user_can_manage_user"]){?>checked="checked"<?php }?> /></td>
                                  </tr>                                  
                                 
                                  
                                  <?php } ?>
                              
                             
                              
                              <?php } ?>
                              
                              
                              
							  <?php 
							  	if($output_values["AccessLevel"]=="2" ){ //|| $output_values["AccessLevel"]=="3"
									//yoes 20160118 --> user พก and พมจ can have password to unlock jobs
								  
								  ?>
                               <tr id="reopen_case_password">
                                <td>รหัสในการเปิดงาน<br />การปฏิบัติตามกฏหมาย</td>
                                <td><input name="reopen_case_password" type="password" style="width: 150px;" value="<?php echo $output_values[reopen_case_password] ?>"/></td>
                              </tr>
                              <?php }?>
                              
                              
                                <script>
                                                                
									function doToggleLevel(){
									
										//AccessLevel = document.getElementById("AccessLevel").value;
									
										AccessLevel = $('#AccessLevel').val();
										
										//alert(AccessLevel);
										document.getElementById("the_province").style.display = "none";
										
										
										document.getElementById("can_edit_user").style.display = "none";
										
										
										 <?php 
											if($output_values["AccessLevel"]=="2" ){ //|| $output_values["AccessLevel"]=="3"
											  
											  ?>
												document.getElementById("reopen_case_password").style.display = "none";
									   
										<?php }?>
										
										if(AccessLevel == "3"){
											
											document.getElementById("the_province").style.display = "";
											
											document.getElementById("can_edit_user").style.display = "";
											
										}
										
										
										if(AccessLevel == "2"){ // || AccessLevel == "3"
											
											
										   document.getElementById("reopen_case_password").style.display = "";
										}
									}	
									
									doToggleLevel();							
								
								
								</script>
                              
                              
                              <?php //yoes 20151122 
							  
							  if($output_values["user_approved_date"]){
							  ?>
                              
                              <tr>
                                    <td><hr />วันที่สมัครใช้งาน</td>
                                    <td><hr /><?php echo formatDateThai($output_values["user_created_date"],1,1);?></td>
                                  </tr>
                                  
                                  
                                  <tr>
                                    <td>วันที่อนุมัติให้ใช้งาน </td>
                                    <td>
									<?php echo formatDateThai($output_values["user_approved_date"],1,1);?></td>
                                  </tr>
                                  
                                  
                                  <tr>
                                    <td>IP ที่ใช้สมัคร </td>
                                    <td>
									<?php echo str_replace("-----","", $output_values["user_ip_address"]);?></td>
                                  </tr>
                                  
                                   <tr>
                                    <td>ผู้อนุมัติให้ใช้งาน <hr /></td>
                                    <td>
									<?php echo getFirstItem("select user_name from users where user_id = '".$output_values["user_approved_by"]."'");?> <hr /></td>
                                  </tr>
                                  
                                  
                               <?php }?>   
                               
                              
                          </table></td>
                        </tr>
                        
                        
                        
                        <tr>
                          <td><hr />
                              
                              
                              
                              <?php if($output_values["user_enabled"] && $sess_accesslevel == 3){ //yoes 20161201 -- disable allow changes after approved?>
                                  
                                   <div align="center">
                                   
                                    <input type="submit" value="ปรับปรุงข้อมูล" />
                                </div>
                                
                              <?php }else{?>
                              
                              <div align="center">
                                <input type="submit" value="ปรับปรุงข้อมูล" />
                            </div>
                            
                            <?php }?>
                            
                            
                            
                            </td>
                        </tr>
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                      </table>
                      
                </form>
                   <script language='javascript'>
						<!--
						function validate_user(frm) {
							<?php if($mode == "add"){ ?> 
							if(frm.user_name.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อ user name");
								frm.user_name.focus();
								return (false);
							}
							
							
							var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890.-_";
						   var checkStr = frm.user_name.value;
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
							 frm.user_name.focus();
							 return (false);
						   }

							
							<?php } ?>
							
							<?php if($is_owner){ ?>
							
							if(frm.user_password_old.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: password เดิม");
								frm.user_password_old.focus();
								return (false);
							}
							if(frm.user_password_new_1.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: password ใหม่");
								frm.user_password_new_1.focus();
								return (false);
							}
							if(frm.user_password_new_1.value != frm.user_password_new_2.value)
							{
								alert("กรุณาใส่ข้อมูล: ยืนยัน password ใหม่ไม่ถูกต้อง");
								frm.user_password_new_2.focus();
								return (false);
							}
							
							<?php }else{?>
							
							if(frm.user_password.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: password");
								frm.user_password.focus();
								return (false);
							}
							
							<?php } ?>
							
							//----
							if(frm.AccessLevel.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: ชนิดของ User");
								frm.AccessLevel.focus();
								return (false);
							}
							
							//----
							return(true);									
						
						}
						-->
					
					</script>
					
					
					
					
					<?php if($output_values["AccessLevel"]=="4" && $output_values[user_enabled] == 9){?>
					<form method=post action="scrp_resend_confirm_email.php">
						<hr><hr>
						
						
						<?php if($_GET["mailed"] == "mailed"){ ?>
					
							<div align=center>
								
								<font color=green>
									ได้ทำการส่ง email ยืนยันการใช้งานให้กับผู้ใช้งานสถานประกอบการอีกครั้งแล้ว
								</font>
							
							</div>
						
						<?php }?>
						
						
						<div align=center>
							<input type="submit" value="ส่ง email ยืนยันการใช้งานให้กับผู้ใช้งานสถานประกอบการอีกครั้ง" />
							<input name="user_id" type="hidden" value="<?php echo $this_id;?>" />
						</div>
					</form>
					<?php }?>
                    
                    
                    <?php if($sess_accesslevel == 1 && ($output_values["AccessLevel"]=="2" || $output_values["AccessLevel"]=="3")){ //admin can manage zones?>
                      <table border="0" cellpadding="0" >
                        
                        
                         <tr>
                            <td >
                            	<hr />
                                <span style="font-weight: bold">กำหนดพื้นที่การทำงาน</span>
                            </td>
                           
                          </tr>
                        
                        
                        <tr>
                              <td><hr />
                                  <div align="center">
                                     <form method="post" action="scrp_update_zone_user.php" >
                        
                                      <table border="0" cellpadding="0" >
                                         <tr id="the_zone">
                                                    <td>พื้นที่การทำงาน</td>
                                                    <td>
                                                    
                                                    <select name="zone_id" id="zone_id">
                                                        
                                                        
                                                        <option value="">-- ไม่ระบุ --</option>
                                                        <?php
                                                        
														
														//also see if this user own this zone
														$my_zone = getFirstItem("select zone_id from zone_user where user_id = $this_id");
														
														
														//also check on province zone only
														if($output_values["AccessLevel"]=="2"){ //เจ้าหน้าที่ พก
														
																$province_sql = " and zone_province_code = 10";
															
														}elseif($output_values["AccessLevel"]=="3"){
															
																$province_sql = " and zone_province_code = '"
																	.
																	
																	
																	getFirstItem("select province_code from provinces where province_id = '".$output_values["user_meta"]."'")
																	
																	."'";
															
														}
														
                                                         $get_zone_sql = "
														 		select 
																	*
                                                                from 
																	zones
																where
																	zone_id not in (
																	
																		select zone_id from zone_user
																		where zone_id != '$my_zone'
																	
																	)
																	
																	$province_sql
																	
                                                                order by 
																	zone_name asc
                                                                ";                                        
                                                      
													  	
													  
                                                        $zone_result = mysql_query($get_zone_sql);                                        
                                                        
                                                        while ($zone_row = mysql_fetch_array($zone_result)) {                                        
                                                        
                                                        ?>              
                                                            <option <?php if($my_zone == $zone_row["zone_id"]){echo "selected='selected'";}?> value="<?php echo $zone_row["zone_id"];?>"><?php echo $zone_row["zone_name"];?></option>
                                                        
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    
                                                    <input name="user_id" type="hidden" value="<?php echo $this_id;?>" />
                                                    
                                                    
                                                     <input type="submit" value="กำหนด" />
                
                                                    
                                                    </td>
                                                  </tr>
                                        </table>
                                        
                                    </form>
                                </div></td>
                            </tr>
                    
                    </table>
                    <?php }?>
                        
                   
              </td>
            </tr>
            
            
              <tr>
                <td align="right" colspan="2">
                    <?php include "bottom_menu.php";?>
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

</body>
</html>
