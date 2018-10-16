<?php if($post_row["le_is_dummy_row"] == "0"){?>

	 <?php 
                               
							   
		  $this_le_code = $post_row["le_code"];
			$this_le_id = $post_row["le_id"];
			$this_le_cid = $post_row["le_cid"];
			$this_le_year = $post_row["le_year"];
			
		   $this_lawful_year = $this_lawful_year;             
			
											   
		  $sql = "select 
				* 
				from 
				curator_company a, lawfulness_company b
				
				where 
				a.curator_lid 	= b.LID
				
				and
				curator_idcard = '$this_le_code'
			
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
	
	   
		
		$this_company_id = $le_row["CID"];
		$this_the_year = $le_row["Year"];
		
		//yoes 20160201
		//variable here
		$have_duplicate_35 = 1;    
	  
	  ?>
	  
	  <div>
	  
	  
		   <?php 
		
		//yoes 20151118 -- make it so company can see link
		if($sess_accesslevel == 4){
		
		?>
		
		
		
		
        
				<?php 
                        
                        //yoes 20160503 --- more detailed message
                        if($post_row["le_cid"] == $le_row["CID"]){					
                            ?>
                            
                             <font color="#CC3300"><strong>! มีการใส่ข้อมูลคนพิการคนนี้ลงไปใน ม.35 แล้ว</strong></font>
                            
                            <?php
                            
                        }else{
                            
                            ?>
                            
                               <span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
                                ! คนพิการนี้มีการทำมาตรา 35 ในบริษัทอื่นแล้ว
                                </span>
                            
                        <?php
                            
                        }
                    
                    ?>
	  
		
		
		<?php }else{ ?>
	  
      	
		
        			
        	<?php 
				
				//yoes 20160503 --- more detailed message
				if($post_row["le_cid"] == $le_row["CID"]){					
					?>
                    
                     <font color="#CC3300"><strong>! มีการใส่ข้อมูลคนพิการคนนี้ลงไปใน ม.35 แล้ว</strong></font>
                    
                    <?php
					
				}else{
					
					?>
                    
                    	<a href="organization.php?id=<?php echo $this_company_id;?>&curate=curate&focus=lawful&year=<?php echo $this_the_year;?>" style="color:#006600; text-decoration:underline;" target="_blank">! พบในมาตรา 35 ของสถานประกอบการอื่น</a>
                    
                <?php
					
				}
			
			?>
      
	  
		
		
		
		<?php }?>
		
	  </div>
	  
	  <?php } //endwhile?>
	
    
<?php }//end if?>