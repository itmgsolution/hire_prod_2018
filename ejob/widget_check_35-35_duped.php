 <?php 							  
		//yoes 20160301 only check dupe on non-dummy rows
		if(!$post_row["curator_is_dummy_row"]){								
  ?>
	  	  
							  
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
									and
									curator_is_disable = 1
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