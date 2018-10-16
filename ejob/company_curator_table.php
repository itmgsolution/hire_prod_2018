<!----------------- START TABLE FOR CURATOR DETAILS ------------>
                                <table id="submitted_curator_table">
                                
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
                                              
                                        </tr> 
                                        
                                         <?php
                       
                            //get main curator
                            $sql = "select * from curator_company where curator_lid = '".$lawful_values["LID"]."' and curator_parent = 0";
                            //echo $sql;
                            
							$count_usee = getFirstItem("select count(*) from curator_company where curator_parent = '".$lawful_values["LID"]."'");	
							
                            $org_result = mysql_query($sql);
                            $total_records = 0;
							
                            while ($post_row = mysql_fetch_array($org_result)) {			
                                
                                $total_records++;
								
								$curator_id = $post_row["curator_id"];
                        
                        ?>
                             <tr >
                              <td style="border-top:1px solid #999999; "><div align="center"><strong><?php echo $total_records;?></strong></div></td>
                              <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_name"]);?></td>
                              <td style="border-top:1px solid #999999;"><?php echo formatGender($post_row["curator_gender"]);?></td>
                              <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_age"]);?></td>
                              
                              <td style="border-top:1px solid #999999;">
                              
                              
                              <?php echo doCleanOutput($post_row["curator_idcard"]);?>
                              
                                                    
                              
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
                               
                                <td style="border-top:1px solid #999999;">
								
									<?php echo doCleanOutput($post_row["curator_event_desc"]);?>
                                    
                                    <?php
									
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
                                                                                
                                        while ($file_row = mysql_fetch_array($curator_file_path)) {
												$file_count_35++;
												
												if($file_count_35 > 1){echo "<br>";}
                                        
                                        ?>
                                            
											
											<a href="hire_docfile/<?php echo $file_row["file_name"];?>" target="_blank">
                                            

												 <?php 
                                                        if($file_row["file_type"] == "curator_docfile$is_extra_table"){
                                                            echo "สำเนาหนังสือแจ้งขอใช้สิทธิ";
                                                            $required_doc_35_1--;
                                                        }elseif($file_row["file_type"] == "curator_docfile_2$is_extra_table"){
                                                            echo "สำเนาหนังสือแจ้งผลการดำเนินการ";																												
                                                            $required_doc_35_2--;
                                                            
                                                        }elseif($file_row["file_type"] == "curator_docfile_3$is_extra_table"){
                                                            echo "สำเนาสัญญาสัมปทาน";																												
                                                            $required_doc_35_3--;
                                                            
                                                        }else{
                                                            echo "ไฟล์แนบ";	
                                                        }
                                                        
                                                    ?>
                                            
                                            </a>
											
                                        <?php
                                        
                                        
                                        }
                                        
                                        
                                        ?>
                                    
                                    
                                    </td>
                               
                             
                            </tr>      
                        
                        	
                         
                               	  <tr>
                                  	<td colspan="10">
                                    	
                                 <table align="left">
                                    
                                <?php 
								
									//get sub-curator
									$sql = "select 
												* 
											from 
												curator_company 
											where curator_parent = '".$curator_id."'";
									//echo $sql;
									
									$sub_result = mysql_query($sql);
									$total_sub = 0;
									while ($sub_row = mysql_fetch_array($sub_result)) {			
								
										$total_sub++;
									
								?>
                                 
                                 
                                 <?php if($total_sub == 1){?>
                                 
                                 
                                 <tr>
                                     <td colspan="6">
                                        <i>(ผู้ถูกใช้สิทธิของ <?php echo doCleanOutput($post_row["curator_name"]);?> - <?php echo doCleanOutput($post_row["curator_idcard"]);?>)</i>
                                      </td>
                                  </tr>
                                
                                 <tr bgcolor="#efefef">
                                 	
                                     <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
                                      <td><div align="center">ชื่อ-นามสกุล</div></td>
                                      <td><div align="center">เพศ</div></td>
                                      <td><div align="center">อายุ</div></td>
                                      <td><div align="center">เลขที่บัตรประชาชน</div></td>
                                      <td><div align="center">ลักษณะความพิการ</div></td>
                                    
                                </tr> 
                                 
                                 <?php }?>
                                 
							
                                 <tr>
                                 
                                 
                                 
                                  <td valign="top"><div align="center"><?php echo $total_sub;?></div></td>
                                  <td valign="top"><?php echo doCleanOutput($sub_row["curator_name"]);?></td>
                                  <td valign="top"><?php echo formatGender($sub_row["curator_gender"]);?></td>
                                  <td valign="top"><?php echo doCleanOutput($sub_row["curator_age"]);?></td>
                                  <td valign="top">
								  
								  <?php echo doCleanOutput($sub_row["curator_idcard"]);?>
                                  
                                  
                                  
                                          
                                          
                                         
                                  
                                  </td>
                                  <td  valign="top"><?php echo doCleanOutput($sub_row["curator_disable_desc"]);?></td>
                                 
                                  
                                 
                                  
                                  
                                </tr>  
                                
                                <?php } //END LOOP FOR CHILD CURATOR?>
                     			
                                		</table>
                                   </td>
                             </tr>
                        
                        
                        
                        
                        
                      <?php 
					  
					  		}//end loop for PARENT curator
					  
					  
					  
					  ?>
                                        
                                                    
								</table>                            
                            
                            	<!--------- END TABLE FOR CURATOR ---------->