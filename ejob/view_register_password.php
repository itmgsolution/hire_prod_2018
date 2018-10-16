<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	
?>
<?php 
	include "header_html.php";
	
?>          
                      
<td valign="top">
                	
                    
                    
                    
  <div align="center" style="padding: 25px 0 10px 0;">
      <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                      
                   ลืมรหัสผ่าน
                    
      </h2>
  </div>
                
  
  <div align="center">
  
  	<form method="post" id="email_form" name="email_form">
  
    <table style="width: 600px;" >
        <tr>
           <TD height="30" colspan="3" valign="top" class="text" style="PADDING-LEFT: 8px" align="left">
           
                      
           <div align="center">
           
           
           	<?php if($_POST[the_email]){
            
            	
				
				$the_user_row = getFirstRow("select * from users where user_email = '".doCleanInput($_POST[the_email])."' limit 0,1");            
				
				$the_user_id = $the_user_row[user_id];
				$the_user_name = $the_user_row[user_name];
				$the_user_meta = $the_user_row[user_meta];
				
				$the_access_level = $the_user_row[AccessLevel];
				$the_commercial_code = $the_user_row[user_commercial_code];

            ?>
           
           		<?php 
				
				//yoes 20151118 -- special for company users - must have correct commercial_code
				if($the_access_level == 4){
					
					if($the_commercial_code != $_POST[the_commercial_code]){
					
						//wrong commercial code
						//give back nothing
						$the_user_name = "";	
						
					}
					
				
				}
				
				
				if($the_user_name && $the_user_row[user_enabled] == 2){
				
					//yoes 20151122 user is rejected...
					?>
                    
                     <div style="padding: 10px; color:#900;">
                      	user name ของท่าน ไม่อนุญาตให้ใช้งานระบบ โปรดติดต่อสอบถามเจ้าหน้าที่ ที่เบอร์ 0 2106 9327-31 ในเวลาราชการ  
                     </div>
                    
                    
                    <?php
				
				}elseif($the_user_name && $the_user_row[user_enabled] == "0" && 1==0){ //yoes 20160404 -- no longer need this
				
					//yoes 20151122 user is rejected...
					?>
                    
                     <div style="padding: 10px; color:#900;">
                      	user name ของท่าน เจ้าหน้าที่กำลังตรวจสอบข้อมูล  โปรดติดต่อสอบถามเจ้าหน้าที่ ที่เบอร์ 0 2106 9327-31 ในเวลาราชการ    
                     </div>
                    
                    
                    <?php
				
				}elseif($the_user_name && $the_user_row[user_enabled] == "9"){
				
					//yoes 20160404 --- send "confirm password" link
					
					$this_id = $the_user_id;
					$this_register_name = $the_user_name;
					$this_seed = $the_user_id+$the_user_meta+7890;
					$this_cid = $the_user_meta;
					
					include "template_register_email.php";
					
					doSendMail(doCleanInput($_POST[the_email]), $the_header, $the_body);	
					
					$do_skip_form = 1;
					?>
                    
                    <div style="padding: 10px; color:#900;">
                      	user name ของท่าน ยังไม่ได้มีการกำหนดรหัสผ่าน วิธีกำหนดรหัสผ่านได้ส่งไปที่อีเมล์ <strong><?php echo $_POST[the_email];?></strong> อีกครั้งแล้ว
                     </div>
                    
                    <?php
			
				
				}elseif($the_user_name){
					
					
					$do_skip_form = 1;
					
					
					//yoes 20151102 --> sending email here
					$mail_address = doCleanInput($_POST[the_email]);
			
					$this_id = $the_user_id;
					$this_register_name = $the_user_name;
						
					$this_seed = $the_user_id+$the_user_meta+7890;
			
					$the_back_link = "http://122.155.197.65/ejob/view_register.php?p=".htmlentities(base64_encode($this_id))."&n=".htmlentities(base64_encode(doCleanInput($this_register_name)))."&r=1";
					
					$the_back_link .= "&s=".htmlentities(base64_encode($this_seed));
			
					$the_header = "รหัสผ่านสำหรับเข้าใช้ระบบรายงานผลการจ้างงานคนพิการ";
					
					$the_body = "<table><tr><td>เรียนคุณ ".doCleanInput($the_user_row[FirstName]). " " .doCleanInput($the_user_row[LastName])."<br><br>";
					
					//$the_body .= "คุณได้สมัครเข้าใช้งาน ระบบรายงานผลการจ้างงานคนพิการ สำหรับสถานประกอบการ เรียบร้อยแล้ว <br><br>";
					//$the_body .= "หลังจากผู้ดูแลระบบได้ทำการตรวจสอบข้อมูลและอนุมัติ user account ของคุณแล้ว <br>";
					$the_body .= "คุณจะสามารถเข้าใช้ระบบได้โดยสามารถเข้าไปกำหนดรหัสผ่านใหม่ โดย ";
					
					$the_body .= "<a href='$the_back_link'>click ที่นี่</a> หรือไปที่ url $the_back_link เพื่อใส่รหัสผ่านที่ต้องการใช้งานต่อไป<br><br>";

						

					$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
					
					
					doSendMail($mail_address, $the_header, $the_body);	
					
					
					
					?>
           
                     <div style="padding: 10px;">
                        ข้อมูล user name และ password สำหรับการเข้าสู่ระบบได้ส่งไปที่อีเมล์ <strong><?php echo $_POST[the_email];?></strong> แล้ว
                     </div>
                   
                    <input id="" type="reset" value="กลับสู่หน้าหลัก" onclick="window.location.href='index.php';"  />
           
           		<?php }else{?>
                
                		
                     <div style="padding: 10px; color:#900;">
                      	ไม่พบ email <strong><?php echo $_POST[the_email];?></strong> ที่ใช้กับเลขที่นิติบุคคลล <?php echo $_POST[the_commercial_code];?> ในระบบ - กรุณาลองอีกครั้ง
                     </div>
                    
                
                <?php }?>
          
           <?php } ?>
           
           
           <?php if(!$do_skip_form){?>
           
               1) กรุุณากรอกอีเมล์ ที่ใช้ในการสมัครเวปไซต์ เพื่อรับข้อมูล login
               
               <div style="padding: 10px;">
               <input name="the_email" id="the_email" type="text" required="required" />
               </div>
               
               
               2) กรุุณากรอกเลขที่นิติบุคคล 13 หลัก ที่ใช้ในการสมัครเวปไซต์
               
               <div style="padding: 10px;">
               <input name="the_commercial_code" id="the_commercial_code" type="text" maxlength="13"  />
               </div>
               
               <input name="" type="submit" value="ค้นหา" /> 
               <input id="" type="reset" value=" ยกเลิก" onclick="window.location.href='index.php';"  />
               
               <script>
					 
					
					 $().ready(function() {
						$("#email_form").validate({							
							
							rules: {								
								the_email: {
									required: true,
									email: true
								}/*,
								the_commercial_code: {
									required: true
									, rangelength: [13, 13]
									, digits: true
								}*/
							},
							messages: {
								
								the_email: "กรุณาใส่ email ให้ถูกต้อง"
								//,the_commercial_code: "กรุณาใส่ เลขที่นิติบุคคล 13 หลัก ให้ถูกต้อง"
							}
						});
						 
						 
					 });
					 
					 </script>
               
           <?php }?>
          
           
           </div>
           
           
									
					  </TD>
        </tr>
    </table>
    
    
    </form>
    
   </div>
  
  
            

  
  
  
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

