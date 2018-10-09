<?php 
if($total_records == 1){ // see if we have to draw headers
?>

    <tr bgcolor="#efefef">
      <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
      <td><div align="center">ชื่อ</div></td>
      <td><div align="center">เพศ</div></td>
      <td><div align="center">อายุ</div></td>
      <td><div align="center">เลขที่บัตรประชาชน</div></td>
      <td width="140px"><div align="center">ลักษณะความพิการ</div></td>
      <td><div align="center">เริ่มบรรจุงาน </div></td>
      <td><div align="center">ค่าจ้าง </div></td>
      <td ><div align="center">ตำแหน่งงาน</div></td>
      <td ><div align="center">การศึกษา</div></td>
      <td ><div align="center">ไฟล์แนบ</div></td>
      <?php if($sess_accesslevel != 5 && !$is_read_only && !$case_closed ){?>
      <td><div align="center">ลบข้อมูล</div></td>
      <td><div align="center">แก้ไขข้อมูล</div></td>
      <?php }?>
    </tr>

<?php

}			//ends $total_records == 1				

?>


  <tr>
                                      <td valign="top"><div align="center"><?php echo $total_records;?></div></td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_name"]);?></td>
                                      <td valign="top"><?php echo formatGender($post_row["le_gender"]);?></td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_age"]);?></td>
                                      <td valign="top">
									  <?php echo doCleanOutput($post_row["le_code"]);?>
                                      
                                      
                                      
                                      	<?php if($sess_accesslevel != 4){ // yoes 20140910 ---- show status whether it's in oracle or not?>
                                        
                                        
                                        		<?php if($post_row["le_is_dummy_row"]){?>
                                                
                                                	                                                    <div style="color:#F60">
                                                    <strong>! เป็นข้อมูลชั่วคราว</strong>
                                                  </div>

                                        
												<?php }elseif(!$post_row["le_from_oracle"]){?>
                                                    <div style="color:#660">
                                                    <strong>! ไม่พบข้อมูลในฐานข้อมูลการออกบัตร</strong>
                                                  </div>
                                                  
                                                  <?php }else{?>
                                                  <div style="color:#6C0">
                                                    พบข้อมูลในฐานข้อมูลการออกบัตร
                                                  </div>
                                                  
                                                  <?php }?>
                                      
                                     	 <?php }?>
                                      
                                      
                                      	<?php if($post_row["is_extra_row"]){ // yoes 20150118?>
                                        	  <div style="color:#F60">
                                                    <strong>! เป็นข้อมูลที่ถูกเพิ่มมาหลังจากมีการปิดงาน<br />และจะไม่ถูกนำไปใช้ในการคิดการปฏิบัติตามกฏหมาย</strong>
                                                  </div>
                                        <?php }?>
                                      
                                      
										 <?php 
										 
										 	//yoes 20151201 -- move this to other file instead
										 	include "widget_check_33-33_duped.php";
										 
										 ?>
                                          
                                          
                                           <?php 
										 
										 	//yoes 20151201 -- move this to other file instead
										 	include "widget_check_33-35_duped.php";
										 
										 ?>
                                      
                                      </td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_disable_desc"]);?></td>
                                      <td valign="top">
									  	<?php 
														
														
														echo formatDateThai($post_row["le_start_date"],0);
														
														
														if($post_row["le_end_date"]){
															echo "-".formatDateThai($post_row["le_end_date"],0);
														}
														
														
														?></td>
                                      
                                      <td valign="top"><div align="right">
									  
									  <?php echo formatNumber($post_row["le_wage"]);?>
                                      
                                      
                                      <?php echo getWageUnit($post_row["le_wage_unit"]);?>
                                      
                                      </div></td>
                                      
                                      <td valign="top"><?php 
									  
									  	if(is_numeric($post_row["le_position"])){
									  		echo formatPositionGroup($post_row["le_position"]);									  
										}else{
									  		echo doCleanInput($post_row["le_position"]);
										}
										
										?></td>
                                      
                                      <td valign="top"><?php echo formatEducationLevel(doCleanOutput($post_row["le_education"]));?></td>
										<td valign="top">
									  
										  <?php 
                                          	//file แนบ here
											
											//doc count
											$required_doc_33_1 = 1;
											$required_doc_33_2 = 1;
											
											//yoes 20160427 -->
											//also see if there are any attached files											 
											$curator_file_path = mysql_query("select 
																					* 
																			   from 
																					 files 
																				where 
																					file_for = '".$post_row["le_id"]."'
																					and
																					(
																					
																						file_type = 'docfile_33_1'																						
																						or
																						file_type = 'docfile_33_2'
																					)
																					");
											
											$file_count_33 = 0;
																		
											while ($file_row = mysql_fetch_array($curator_file_path)) {
											
												$file_count_33++;
												
												if($file_count_33 > 1){echo "<br>";}
											?>
                                            	
                                                
                                            
                                            
                                            	<?php 
												
													//echo substr($file_row["file_name"],0,4);
													if(substr($file_row["file_name"],0,4)=="ejob"){
												?>
													<a href="http://ejob.dep.go.th/ejob/hire_docfile/<?php echo substr($file_row["file_name"],5);?>" target="_blank">
												<?php	
													}else{
												?>
													<a href="hire_docfile/<?php echo $file_row["file_name"];?>" target="_blank">
                                                 <?php }?>
                                                
                                                <?php 
													if($file_row["file_type"] == "docfile_33_1"){
														echo "สำเนาสัญญาจ้าง";
														$required_doc_33_1--;
													}elseif($file_row["file_type"] == "docfile_33_2"){
														echo "สำเนาบัตรประจำตัวคนพิการ/ผู้ดูแลคนพิการ";																												
														$required_doc_33_2--;
														
													}else{
														echo "ไฟล์แนบ";	
													}
													
												?>
                                                
                                                </a>
												
												<?php if(!$read_only && !$case_closed){ //yoes 20160816 --> add this?>
												<a href="scrp_delete_curator_file.php?id=<?php echo $file_row["file_id"];?>&curator_id=<?php echo $post_row["le_id"];?>&return_id=<?php echo $this_id;?>" title="ลบไฟล์แนบ" onClick="return confirm('คุณแน่ใจหรือว่าจะลบไฟล์แนบ? การลบไฟล์ถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบไฟล์ได้');"><img src="decors/cross_icon.gif" alt="" height="10"  border="0" /></a>
                                                <?php }?>
												
										
												<!--<a href="force_load_file.php?file_for=<?php echo $file_row["curator_id"];?>&file_type=curator_docfile" target="_blank">ไฟล์แนบ</a>-->
											<?php
											
											
											}
											
											
                                          ?>
                                          
                                          <?php 
										  if($required_doc_33_1){
											
											$required_doc++;
											
											$file_count_33++;
											if($file_count_33 > 1){echo "<br>";}
											echo "<font color='red'>กรุณาแนบไฟล์สำเนาสัญญาจ้าง</font>";  
											
										  }
										  if($required_doc_33_2){
											  
											$required_doc++;
											  
											$file_count_33++;
											if($file_count_33 > 1){echo "<br>";}
											echo "<font color='red'>กรุณาแนบไฟล์สำเนาบัตรประจำตัวคนพิการ/ผู้ดูแลคนพิการ</font>";  
											
										  }
										  ?>
                                          
                                          <?php if($required_doc_33_1 || $required_doc_33_2){?>
                                          
                                          	<script>
                                            	
												$("#alert_33_files").show();
												
												$("#submit_doc").hide();
												
												$("#js_doc_warning").show();
                                            
                                            </script>
                                          
                                          <?php										  
											  
										  }?>
                                      
                                      </td>
                                     
                                     <?php if($sess_accesslevel != 5 && !$is_read_only && !$case_closed){?>
                                         
                                         
                                         
                                         <?php if($post_row["is_extra_row"]){?>
                                         
                                              <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>&is_extra_row=1" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                                          
                                          
                                          <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leidex=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล"><img src="decors/create_user.gif" alt="" border="0" /></a></div></td>
                                         
                                          <?php }else{?>
                                          
                                          
                                              <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                                              
                                              
                                              <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leid=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล"><img src="decors/create_user.gif" alt="" border="0" /></a></div></td>
                                          
                                          <?php }?>
                                          
                                      <?php }elseif($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only &&  $post_row["is_extra_row"]){ //extra row allow edit no matter what?>
                                         
                                         
                                          <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>&is_extra_row=1" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                                          
                                          
                                          <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leidex=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล"><img src="decors/create_user.gif" alt="" border="0" /></a></div></td>
                                          
                                          
                                      <?php }?>
                                      
                                      
                                      
                                      
                                    </tr>