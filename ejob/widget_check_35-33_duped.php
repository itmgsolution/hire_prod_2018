 <?php 							  
		//yoes 20160301 only check dupe on non-dummy rows
		if(!$post_row["curator_is_dummy_row"]){								
  ?>
	  
	   <?php 
	   
	   
							  
								//see if this le_id already in another ID
								
								
								$sql = "select * from lawful_employees_company
										where 
										le_code = '$this_curator_idcard'
										and 
										le_year = '$this_lawful_year'
										and
										le_is_dummy_row = 0
										";
							  
								echo $sql;
							  
								$le_result = mysql_query($sql);
								
								while ($le_row = mysql_fetch_array($le_result)) {
							
							  
							  ?>
							  
							  
								 <?php 
						
									//yoes 20151118 -- make it so company can see link
									if($sess_accesslevel == 4){
									
									?>
									
									
									
										
                                        
                                        
                                        <?php 
                        
										//yoes 20160503 --- more detailed message
										// $this_cid comes from organization.php
										if($this_cid == $le_row["le_cid"]){					
											?>
											
											 <font color="#CC3300"><strong>! มีการใส่ข้อมูลคนพิการคนนี้ลงไปใน ม.33 แล้ว</strong></font>
											
											<?php
											
										}else{
											
											?>
											
											 <span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
                                            ! คนพิการนี้มีการทำมาตรา 33 ในบริษัทอื่นแล้ว <br />
                                            </span>
											
										<?php
											
										}
									
									?>
								  
									
									
									<?php }else{ ?>
								  
									
                                      
                                      
                                        <?php 
                        
										//yoes 20160503 --- more detailed message
										if($this_cid == $le_row["le_cid"]){					
											?>
											
											 <font color="#CC3300"><strong>! มีการใส่ข้อมูลคนพิการคนนี้ลงไปใน ม.33 แล้ว</strong></font>
											
											<?php
											
										}else{
											
											?>
											
											  <div>
                                                <a href="organization.php?id=<?php echo $le_row["le_cid"];?>&le=le&focus=lawful&year=<?php echo $le_row["le_year"];?>" style="color:#990000; text-decoration:underline;" target="_blank">! พบในมาตรา 33 ของสถานประกอบการอื่น</a>
                                              </div>
											
										<?php
											
										}
									
									?>
								  
								  
								  <?php }?>
							  
							  <?php }?>
							  
	  
	   <?php 							  
			//yoes 20160301 only check dupe on non-dummy rows
			} //end if(!$post_row["curator_is_dummy_row"]){								
	  ?>                