<?php

if($sess_accesslevel == 1){
	//$starttime = microtime(true);
}?>


<table id="organization_35_details_table<?php echo $is_extra_table;?>" width="750" border="1" cellspacing="0" cellpadding="3" style="border-collapse:collapse; 

<?php if(1==1){?>display:none;<?php }?>" align="center">                        
                        
                        
                         <tr bgcolor="#efefef">
                             <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
                              <td><div align="center">ชื่อ-นามสกุล</div></td>
                              <td><div align="center">เพศ</div></td>
                              <td><div align="center">อายุ</div></td>
                              <td><div align="center">เลขที่บัตรประชาชน</div></td>
                              <td><div align="center">ผู้ใช้สิทธิเป็น</div></td>
                              <td><div align="center">วันเริ่มต้นสัญญา</div></td>
                              <td><div align="center">วันสิ้นสุดสัญญา</div></td>
                              <td><div align="center">ระยะเวลา</div></td>
                              <td><div align="center">กิจกรรม</div></td>
                              <td><div align="center">มูลค่า (บาท)</div></td>
                              <td><div align="center">รายละเอียด</div></td>
                              
                               <?php 
							   
							   
							   //echo "$submitted_company_lawful && $sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only && (!$case_closed || $is_extra_table";
							   
							   //yoes 20160318 -- fix this condition for compnay
							   if($sess_accesslevel != 4){ // non-company never submit company lawfu...
								   $company_lawful_submitted_for_m35 = 0;
							   }else{
									$company_lawful_submitted_for_m35 = $submitted_company_lawful;   
							   }
							   
							   if(!$company_lawful_submitted_for_m35  && $sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only && (!$case_closed || $is_extra_table)){
							   ?>
                             
                              <td><div align="center">ลบข้อมูล</div></td>
                              <td><div align="center">แก้ไขข้อมูล</div></td>
                              <?php }?>
                              
                        </tr> 
                        
                                    
                        <?php
                       
                            //get main curator
                            $sql = "select * from $curator_table_name where curator_lid = '".$lawful_values["LID"]."' and curator_parent = 0 order by curator_id asc";
                            //echo $sql;
							

                            
                            $org_result = mysql_query($sql);
                            $total_records = 0;
							
                            while ($post_row = mysql_fetch_array($org_result)) {			
                                
                                $total_records++;
                        
								if($the_bg == "bgcolor='#ffffff'"){
									$the_bg = "bgcolor='#F8F8F8'";
								}else{
									$the_bg = "bgcolor='#ffffff'";
								}
						
                        ?>
                             <tr <?php echo $the_bg;?>>
                              <td style="border-top:1px solid #999999; "><div align="center"><strong><?php echo $total_records;?></strong></div></td>
                              <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_name"]);?></td>
                              <td style="border-top:1px solid #999999;"><?php echo formatGender($post_row["curator_gender"]);?></td>
                              <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_age"]);?></td>
                              
                              <td style="border-top:1px solid #999999;">
                              
                              
                              <?php echo doCleanOutput($post_row["curator_idcard"]);?>
                              
                              
						  <?php 							  
                                //yoes 20160301 only check dupe on non-dummy rows
                                if(!$post_row["curator_is_dummy_row"]){								
                          ?>
                              
                               <?php 
							   
							   
                                                      
                                                        //see if this le_id already in another ID
                                                        
                                                        $this_curator_idcard = $post_row["curator_idcard"];
                                                        $this_curator_id = $post_row["curator_id"];
                                                        $this_le_cid = $post_row["le_cid"];
                                                        $this_le_year = $post_row["le_year"];
                                                        
                                                        $sql = "select * from lawful_employees_company
															 	where 
																le_code = '$this_curator_idcard'
                                                                and 
																le_year = '$this_lawful_year'
																and
																le_is_dummy_row = 0
                                                                ";
                                                      
                                                        //echo $sql;
                                                      
                                                        $le_result = mysql_query($sql);
                                                        
                                                        while ($le_row = mysql_fetch_array($le_result)) {
                                                    
                                                      
                                                      ?>
                                                      
                                                      
                                                      	 <?php 
												
															//yoes 20151118 -- make it so company can see link
															if($sess_accesslevel == 4){
															
															?>
															
															
															
                                                                <span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
                                                                ! คนพิการนี้มีการทำมาตรา 33 แล้ว <br />
                                                                </span>
														  
															
															
															<?php }else{ ?>
														  
                                                              <div>
                                                                <a href="organization.php?id=<?php echo $le_row["le_cid"];?>&le=le&focus=lawful&year=<?php echo $le_row["le_year"];?>" style="color:#990000; text-decoration:underline;" target="_blank">! พบในมาตรา 33</a>
                                                              </div>
														  
														  
														  <?php }?>
                                                      
                                                      <?php }?>
                                                      
                                                      
                                                      <?php 
                                                        
                                                        $sql = "select 
                                                            * 
                                                            from 
                                                            curator_company a, lawfulness_company b
                                                            
                                                            where 
                                                            a.curator_lid 	= b.LID
                                                            and
                                                            curator_idcard = '$this_curator_idcard'
                                                            and
                                                            curator_id != '$this_curator_id'
                                                            and
                                                            year = '$this_lawful_year'
															and
															curator_is_dummy_row = 0
                                                        ";
                                                      
                                                      
                                                      //echo $sql;
                                                        $le_result = mysql_query($sql);
                                                        
                                                        while ($le_row = mysql_fetch_array($le_result)) {
                                                    
                                                        $lawfulness_row = getFirstRow("select CID,Year from lawfulness where lid = '".$le_row["curator_lid"]."'");
                                                        
                                                        $this_company_id = $lawfulness_row["CID"];
                                                        $this_the_year = $lawfulness_row["Year"];
                                                      
                                                      ?>
                                                      
                                                      		   <?php 
												
															//yoes 20151118 -- make it so company can see link
															if($sess_accesslevel == 4){
															
															?>
															
															
															
                                                                    <span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
                                                                    ! พบในสถานประกอบการอื่น <br />
                                                                    </span>
														  
															
															
															<?php }else{ ?>
														  
                                                      
                                                      
                                                      
                                                                  <div>
                                                                    <a href="organization.php?id=<?php echo $this_company_id;?>&curate=curate&focus=lawful&year=<?php echo $this_the_year;?>" style="color:#006600; text-decoration:underline;" target="_blank">! พบในมาตรา 35 ของสถานประกอบการอื่น</a>
                                                                  </div>
                                                      
                                                      
		                                                      <?php }?>
                                                      
                                                      <?php }?>
                                                      
                              
                               <?php 							  
									//yoes 20160301 only check dupe on non-dummy rows
									} //end if(!$post_row["curator_is_dummy_row"]){								
							  ?>                
                                                      
                              
                              </td>
                              
                              
                              
                              <td style="border-top:1px solid #999999;">
                              <?php if($post_row["curator_is_disable"] == 1){
                                
                                    echo "<font color='green'>คนพิการ : " . $post_row["curator_disable_desc"]. "</font>";
                                    
                                }else{
                                
                                    echo "<font color='blue'>ผู้ดูแลคนพิกา</font>ร";
                                    
                                }?>
                              
                              </td>
                              
                              
                              <td style="border-top:1px solid #999999;"><?php echo formatDateThai($post_row["curator_start_date"]);?></td>
                                <td style="border-top:1px solid #999999;"><?php echo formatDateThai($post_row["curator_end_date"]);?></td>
                                
                                <td style="border-top:1px solid #999999;"><?php 
                                
                                
                                //echo $post_row["curator_start_date"];
                                //echo $post_row["curator_end_date"];
                                echo number_format(dateDiffTs(strtotime($post_row["curator_start_date"]), strtotime($post_row["curator_end_date"])),0);
                                
                                ?> วัน</td>
                                
                               
                              
                               <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_event"]);?></td>
                               
                               <td style="border-top:1px solid #999999;"><div align="right"><?php echo formatNumber($post_row["curator_value"]);?></div></td>
                               
                                <td style="border-top:1px solid #999999;"><?php 
								
								
										$required_doc_35_1 = 1;
										$required_doc_35_2 = 1;
										$required_doc_35_3 = 1;
                                
                                        echo doCleanOutput($post_row["curator_event_desc"]);
                                        
                                        //also see if there are any attached files
										//yoes 20160120 --> add "extra" suffix here
                                        $curator_file_path = mysql_query("select 
                                                                                * 
                                                                           from 
                                                                                 files 
                                                                            where 
                                                                                file_for = '".$post_row["curator_id"]."'
                                                                               
																				and
																					(
																					
																						file_type = 'curator_docfile'																						
																						or
																						file_type = 'curator_docfile_2'
																						or
																						file_type = 'curator_docfile_3'
																					)
                                                                                ");
                                        
										$file_count_35 = 0;
										                     
                                        while ($file_row = mysql_fetch_array($curator_file_path)) {
											
											$file_count_35++;
												
											if($file_count_35 > 1){echo "<br>";}
                                        
                                        ?>
                                            <a href="hire_docfile/<?php echo $file_row["file_name"];?>" target="_blank">
                                            
												 <?php 
                                                        if($file_row["file_type"] == "curator_docfile"){
                                                            echo "สำเนาหนังสือแจ้งขอใช้สิทธิ";
                                                            $required_doc_35_1--;
                                                        }elseif($file_row["file_type"] == "curator_docfile_2"){
                                                            echo "สำเนาหนังสือแจ้งผลการดำเนินการ";																												
                                                            $required_doc_35_2--;
                                                            
                                                        }elseif($file_row["file_type"] == "curator_docfile_3"){
                                                            echo "สำเนาสัญญาสัมปทาน";																												
                                                            $required_doc_35_3--;
                                                            
                                                        }else{
                                                            echo "ไฟล์แนบ";	
                                                        }
                                                        
                                                    ?>
                                            
                                            </a>
                                            
                                            
                                            <a href="scrp_delete_curator_file.php?id=<?php echo $file_row["file_id"];?>&curator_id=<?php echo $post_row["curator_id"];?>&return_id=<?php echo $this_id;?>" title="ลบไฟล์แนบ" onClick="return confirm('คุณแน่ใจหรือว่าจะลบไฟล์แนบ? การลบไฟล์ถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบไฟล์ได้');"><img src="decors/cross_icon.gif" alt="" height="10"  border="0" /></a>
                                            
        
                                            <!--<a href="force_load_file.php?file_for=<?php echo $file_row["curator_id"];?>&file_type=curator_docfile" target="_blank">ไฟล์แนบ</a>-->
                                        <?php
                                        
                                        
                                        }
										
                                        
                                        ?>
                                        
                                        
                                         <?php 
										  if($required_doc_35_1){
											
											$required_doc++;
											
											$file_count_35++;
											if($file_count_35 > 1){echo "<br>";}
											echo "<font color='red'>กรุณาแนบไฟล์สำเนาหนังสือแจ้งขอใช้สิทธิ</font>";  
											
										  }
										  if($required_doc_35_2){
											  
											$required_doc++;
											  
											$file_count_35++;
											if($file_count_35> 1){echo "<br>";}
											echo "<font color='red'>กรุณาแนบไฟล์สำเนาหนังสือแจ้งผลการดำเนินการ</font>";  
											
										  }
										  if($required_doc_35_3){
											  
											$required_doc++;
											  
											$file_count_35++;
											if($file_count_35> 1){echo "<br>";}
											echo "<font color='red'>กรุณาแนบไฟล์สำเนาสัญญาสัมปทาน</font>";  
											
										  }
										  ?>
                                        
                                        
                                        
                                        </td>
                                
                                <?php 
								
								
								if(!$company_lawful_submitted_for_m35 && $sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only && (!$case_closed || $is_extra_table)){
								
								?>
                                
                                      <td>
                                        <div align="center">
                                            <a href="scrp_delete_curator_new.php?id=<?php echo doCleanOutput($post_row["curator_id"]);?>&cid=<?php echo $this_cid;?>&year=<?php echo $this_lawful_year;?><?php if( $is_extra_table){echo "&extra=1";}?>" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูลผู้ใช้สิทธิ? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a>
                                        </div>
                                        
                                        </td>
                                  
                                 <?php }?>  
                                  
                                  
                                <?php 
								
								if(!$company_lawful_submitted_for_m35 && $sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only && (!$case_closed || $is_extra_table)){
								
								?>
                                  <td>
                                      <div align="center">
                                      
                                      <a href="organization.php?id=<?php echo $this_id;?>&focus=lawful&year=<?php echo $this_lawful_year;?>&curator_id=<?php echo doCleanOutput($post_row["curator_id"]);?><?php if( $is_extra_table){echo "&extra=1";}?>">
                                        <img src="decors/create_user.gif" alt="" border="0" />
                                      </a>
                                      </div>	
                                  </td>
                                 
								<?php }?>
                                  
                                  
                                  
                             
                             
                            </tr>
                             
                             
                             <?php 
							 	
								/*if($sess_accesslevel == 1){						
									$endtime = microtime(true);
									$timediff = $endtime - $starttime; echo $timediff;
								}*/
							 ?>
                            
                            
                            <?php 
							
							
							//for parent -> get child
							
							
							if(!$post_row["curator_is_disable"]){
								$count_usee = getFirstItem("select count(*) from $curator_table_name where curator_parent = '".doCleanOutput($post_row["curator_id"])."'");
							}else{
								$count_usee = 0;
							}
							//$count_usee = 0;
							
							if($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only && !$case_closed && $count_usee > 1){
							?>
                            
                            <tr <?php echo $the_bg;?>>
                               <td colspan="14" style="border-top:1px solid #999999; color:#F00;  ">ข้อมูลผู้ใช้สิทธิ มีผู้ถูกใช้สิทธิมากกว่า 1 คน กรุณาเลือกผู้ถูกใช้สิทธิที่ต้องการจากรายชื่อด้านล่าง</td>
                             </tr>
                             
                             <?php }?>
                            
                            <?php
							
							//get sub-curator
							$sql = "select 
										* 
									from 
										$curator_table_name 
									where curator_parent = '".doCleanOutput($post_row["curator_id"])."'";
							//echo $sql;
							
							$sub_result = mysql_query($sql);
							$total_sub = 0;
							while ($sub_row = mysql_fetch_array($sub_result)) {	
							
								
							?>
                            
                            
                             <tr <?php echo $the_bg;?> >
                               
                               <td valign="top">
                               
                               
							<?php if($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only && !$case_closed && $count_usee > 1){?>
                             
                                <div align="center">
                                    
                                    
                                    	 <a href="scrp_select_curator_new.php?id=<?php echo doCleanOutput($sub_row["curator_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>" onClick="return confirm('ยืนยันเลือกผู้ถูกใช้สิทธิ?');" style="font-weight: normal;">
                                    	คลิกที่นี่เพื่อเลือกผู้ถูกใช้สิทธิ
                                        
                                        </a>
                                                  
                                    
                                   
                                </div>
                             
                              <?php }?>
                               
                               </td>
                               
                               
                               
                                  <td valign="top" ><?php echo doCleanOutput($sub_row["curator_name"]);?></td>
                                  <td valign="top" ><?php echo formatGender($sub_row["curator_gender"]);?></td>
                                  <td valign="top" ><?php echo doCleanOutput($sub_row["curator_age"]);?></td>
                                  <td valign="top" >
								  
								  <?php echo doCleanOutput($sub_row["curator_idcard"]);?>
                                  
                                  
                             <?php 							  
								//yoes 20160301 only check dupe on non-dummy rows
								if(!$sub_row["curator_is_dummy_row"]){								
						    ?>
                                  
                                   <?php 
                                          
                                            //see if this le_id already in another ID
                                            
                                            $this_curator_idcard = $sub_row["curator_idcard"];
                                            $this_curator_id = $sub_row["curator_id"];
                                            $this_le_cid = $sub_row["le_cid"];
                                            $this_le_year = $sub_row["le_year"];
                                            
                                            $sql = "select * from lawful_employees where le_code = '$this_curator_idcard'
													and le_year = '$this_lawful_year'
													";
                                          
                                          	//echo $sql;
                                          
                                            $le_result = mysql_query($sql);
                                            
                                            while ($le_row = mysql_fetch_array($le_result)) {
										
                                          
                                          ?>
                                          
                                          		 <?php 
												
												//yoes 20151118 -- make it so company can see link
												if($sess_accesslevel == 4){
												
												?>
												
												
												
													<span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
													! คนพิการนี้มีการทำมาตรา 33 แล้ว <br />
													</span>
											  
												
												
												<?php }else{ ?>
                                          
                                          
                                                      <div>
                                                        <a href="organization.php?id=<?php echo $le_row["le_cid"];?>&le=le&focus=lawful&year=<?php echo $le_row["le_year"];?>" style="color:#990000; text-decoration:underline;" target="_blank">! พบในมาตรา 33</a>
                                                      </div>
                                                      
                                                  <?php }?>
                                          
                                          <?php }?>
                                          
                                          
                                          <?php 
                                            
                                             $sql = "select 
												* 
												from 
												curator a, lawfulness b
												
												where 
												a.curator_lid 	= b.LID
												and
												curator_idcard = '$this_curator_idcard'
												and
												curator_id != '$this_curator_id'
												and
												year = '$this_lawful_year'
											";
                                          
                                          
                                          
                                            $le_result = mysql_query($sql);
                                            
                                            while ($le_row = mysql_fetch_array($le_result)) {
                                        
                                            $lawfulness_row = getFirstRow("select CID, Year from lawfulness where lid = '".$le_row["curator_lid"]."'");
                                            
                                            $this_company_id = $lawfulness_row["CID"];
                                            $this_the_year = $lawfulness_row["Year"];
                                          
                                          ?>
                                          
                                         		 <?php 
												
												//yoes 20151118 -- make it so company can see link
												if($sess_accesslevel == 4){
												
												?>
												
												
												
														<span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
														! พบในสถานประกอบการอื่น<br />
														</span>
											  
												
												
												<?php }else{ ?>
                                          
                                          
                                          
                                                  <div>
                                                    <a href="organization.php?id=<?php echo $this_company_id;?>&curate=curate&focus=lawful&year=<?php echo $this_the_year;?>" style="color:#006600; text-decoration:underline;" target="_blank">! พบในมาตรา 35 ของสถานประกอบการอื่น</a>
                                                  </div>
                                                  
                                                  <?php }?>
                                          
                                          <?php }?>
                                          
                                          
                                <?php 							  
									//yoes 20160301 only check dupe on non-dummy rows
									} //if(!$sub_row["curator_is_dummy_row"]){								
								?>
                                
                                
                                  
                                  </td>
                                  <td  valign="top"  colspan="9">ผู้ถูกใช้สิทธิ: <?php echo doCleanOutput($sub_row["curator_disable_desc"]);?></td>
                              
                             </tr>      
                        
                        
                        	<?php } //end loop for child?>
                        
                        
                      <?php }//end loop for curator?>
                        
                      </table>
                      
<?php 

if($sess_accesslevel == 1){
	//$endtime = microtime(true);
	//$timediff = $endtime - $starttime;
	
	//echo $timediff;
}



?>

<?php if($required_doc_35_1 || $required_doc_35_2 || $required_doc_35_3){?>
	<br /><font color="red">กรุณาแนบไฟล์ให้ครบสำหรับทุกสัมปทานฯ</font>
<?php }?>