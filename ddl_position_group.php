<select name="le_position" id="le_position" onchange="checkPositionList();">
	
	<?php
	
	
		$get_position_sql = "select *
            from position_group
            order by group_name asc
            ";
		
	
    //all photos of this profile
    
  
    $position_result = mysql_query($get_position_sql);
    
    
    
    while ($position_row = mysql_fetch_array($position_result)) {
    
    
    ?>              
        <option <?php if($_POST["le_position"] == $position_row["group_id"] || $leid_row["le_position"] == $position_row["group_id"]){echo "selected='selected'";}?> value="<?php echo $position_row["group_id"];?>"><?php echo $position_row["group_name"];?></option>
    
    <?php
    }
    ?>
</select>
