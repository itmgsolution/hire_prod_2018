﻿<?php

	include "db_connect.php";
	include "scrp_config.php";
	include "session_handler.php";
	
	
		
	////manage year
	//try to show last "document_requests" entry
	if($_POST["ddl_year"]){
		//echo "in the loop";
		$this_year = doCleanInput($_POST["ddl_year"]);
		$conditions = " and Year = '$this_year'";
		
	}elseif($_GET["ddl_year"]){
		$this_year = doCleanInput($_GET["ddl_year"]);
		$conditions = " and Year = '$this_year'";
	}elseif($_GET["year"]){
		$this_year = doCleanInput($_GET["year"]);
		$conditions = " and Year = '$this_year'";
	}elseif($_POST["year_curator"]){
		//for curator's auto submit
		$this_year = doCleanInput($_POST["year_curator"]);
		$conditions = " and Year = '$this_year'";
	}else{
	
		//$this_year = strtotime(date('Y'),date('Y')."+1 year");
		//$this_year = date ( 'Y', strtotime ( '+123 day' . date('Y') ) );
		
		if(date("m") >= 9){
			$this_year = date("Y")+1; //new year at month 9
		}else{
			$this_year = date("Y");
		}
		
		$conditions = " and Year = '$this_year'";
	}
	
	
	//echo $this_year;
	
	$this_lawful_year = $this_year;
		
	//current mode
	if($_GET["mode"] == "new"){
		$mode = "new";	
	}elseif(is_numeric($_GET["id"])){
		$mode = "edit";
		$this_id = $_GET["id"];
		$this_cid = $this_id;
		$this_focus = $_GET["focus"];
		$post_row = getFirstRow("select * 
								from 
									company
								where 
									cid  = '$this_id'
								limit 0,1");
								
		//vars to use
		$output_fields = array(
						
						'CID'
						,'Employees'
						,'CompanyCode'
						,'CompanyNameThai'
						,'CompanyNameEng'
						,'Address1'
						
						,'Moo'
						,'Soi'
						,'Road'
						,'Subdistrict'
						,'District'
						
						,'Province'
						,'Zip'
						,'Telephone'
						,'email'
						,'TaxID'
						
						,'CompanyTypeCode'
						,'BusinessTypeCode'
						,'BranchCode'
						,'org_website'
						
						,'LawfulFlag'
						,'Status'
						
						,'NoRecipient'
						,'NoRecipient_remark'
						
						,'ContactPerson1'
						,'ContactPhone1'
						,'ContactEmail1'
						,'ContactPosition1'
						,'ContactPerson2'
						,'ContactPhone2'
						,'ContactEmail2'
						,'ContactPosition2'
						
						);
				
		for($i = 0; $i < count($output_fields); $i++){
			//clean all inputs
			$output_values[$output_fields[$i]] .= (doCleanOutput($post_row[$output_fields[$i]]));
		}
				
		
		
	}else{
		header("location: index.php");
	}	





///curator thingie


///curator thingie
$is_on_ictmerlin = 0;
//echo "ictmerlin: ".$is_on_ictmerlin; exit();

if($_POST["btn_get_curator_data"] && $is_on_ictmerlin){

	$curator_lid = doCleanInput($_POST["curator_lid"]);

	//use dummy data instead
	$sql = "
			insert into 
				curator(
				
					curator_name
					,curator_idcard
					,curator_gender
					,curator_age
					,curator_lid
					
					,curator_parent										
					,curator_disable_desc					
					, curator_is_disable
					
					
					
				)values(
				
				
					'นาย ผู้ใช้สิทธิ " . rand(100,999) . "'
					,'1234567890" . rand(100,999) . "'
					,'m'
					,'35'
					,'$curator_lid'
					
					,'0'					
					,'ความพิการทางสติปัญญา'					
					, '0'					
				
				)
				
			";
			
			//echo "what: ".$sql; exit();
			
		mysql_query($sql);
		
		
		//
		$curator_last_id = mysql_insert_id();		
		
		//try simulate add 2 curators
		for($i = 1;$i <= 1; $i++){
			
			$sql = "
				insert into 
					curator(
					
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,curator_parent
											
						,curator_disable_desc
						
						, curator_is_disable
						
					)values(
					
					
						'นาย ถูกใช้สิทธิ_$i " . rand(100,999) . "'
						,'1234567890" . rand(100,999) . "'
						,'m'
						,'20'
						,'$curator_lid'
						,'$curator_last_id'
						
						
						,'ความพิการทางสติปัญญา'
						
						, '1'					
					
					)
					
				";
				
				//echo $sql;
				
				mysql_query($sql);
		
		}
		
		if($this_year >= 2013){
		
			//only do auto post if >= year 2013
			$_GET["auto_post"] = 1;
			//also add curator flag
			$_GET["curate"] = "curate";
			
			//also pre-populate the "just inserted curator"
			$_GET["curator_id"] = $curator_last_id;
			
		}



}elseif($_POST["btn_get_curator_data"]){

	//echo "try get curator data, instead of manual input!";
	
	
	
	$oracle_user = 'opp$_dba'; //nep_card
	$oracle_password = "password";	//password
	$oracle_db = "(DESCRIPTION =
				(ADDRESS_LIST = 
					(ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.3.13)(PORT = 1521))
				)
				(CONNECT_DATA =
					(SERVICE_NAME = ORCL)
				)
			)";
			
	$oracle_connect = oci_connect($oracle_user, $oracle_password, $oracle_db, "TH8TISASCII");
	
	if($oracle_connect){
		//echo "<br><font color='green'>connection estrabished!</font>";
	}else{
		//echo "<br><font color='red'>connection failed!</font>";
		//exit;
	}
	
	$the_id = "3100905673020";
	if($_POST["curator_idcard"] && is_numeric($_POST["curator_idcard"])){
		$the_id = $_POST["curator_idcard"];
	}else{
	
		$the_id = "";
		for($i=1;$i<=13;$i++){
			$the_id .= $_POST["id_".$i]; 
		}
		
	}
	
	$the_id = addslashes(substr($the_id,0,13));
	
	$curator_parent = doCleanInput($_POST["curator_parent"]);
	
	
	
	
	
	
	
	//-----------------------------------------
	//--------------- try get mn_desperson -> start below
	//-----------------------------------------
	
	
	
	$mm = oci_parse($oracle_connect, "select * from MN_DES_PERSON where PERSON_CODE = '$the_id'");
	oci_execute($mm, OCI_DEFAULT);
	
	//echo "select * from MN_DES_PERSON where PERSON_CODE = '$the_id'";
	
	while (oci_fetch($mm)) {
	
		//found this person code
		$has_maimad = 1;

		$the_maimad = oci_result($mm, "MAIMAD_ID");
		//
		$PERSON_CODE = oci_result($mm, "PERSON_CODE");
		//
		
		$FIRST_NAME_THAI = oci_result($mm, "FIRST_NAME_THAI");
		$FIRST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($FIRST_NAME_THAI));
		
		$LAST_NAME_THAI = oci_result($mm, "LAST_NAME_THAI");
		$LAST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($LAST_NAME_THAI));
		
		$SEX_CODE = oci_result($mm, "SEX_CODE");
		$BIRTH_DATE = oci_result($mm, "BIRTH_DATE");
		$BIRTH_DATE = birthday($BIRTH_DATE);
		
		$the_prefix = oci_result($mm, "PREFIX_CODE_THAI");
		
		////////
		$mmm = oci_parse($oracle_connect, "select * from MN_DES_DEFORMED where MAIMAD_ID = '$the_maimad'");
		oci_execute($mmm, OCI_DEFAULT);
		while (oci_fetch($mmm)) {
			//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
			
			$DEFORM_ID = oci_result($mmm, "DEFORM_ID");
		}
	
	}
	
	
	
	if($has_maimad){
	
		//get prefix
		$s = oci_parse($oracle_connect, "select * from BS_PREFIX where PREFIX_CODE = '$the_prefix'");
		oci_execute($s, OCI_DEFAULT);
		while (oci_fetch($s)) {
			//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
			
			$PREFIX_NAME_ABBR = oci_result($s, "PREFIX_NAME_ABBR");
			$PREFIX_NAME_ABBR = iconv("WINDOWS-874", "UTF-8", ($PREFIX_NAME_ABBR));
		}
		
		
		//exit();
		
		if($DEFORM_ID == 1 || $DEFORM_ID == 6 || $DEFORM_ID == 12){
			$DEFORM_ID = "ความพิการทางการเห็น";
		}
		if($DEFORM_ID == 2 || $DEFORM_ID == 7 || $DEFORM_ID == 13){
			$DEFORM_ID = "ความพิการทางการได้ยินหรือสื่อความหมาย";
		}
		if($DEFORM_ID == 3 || $DEFORM_ID == 8 || $DEFORM_ID == 14){
			$DEFORM_ID = "ความพิการทางการเคลื่อนไหวหรือร่างกาย";
		}
		if($DEFORM_ID == 4 || $DEFORM_ID == 9 || $DEFORM_ID == 15){
			$DEFORM_ID = "ความพิการทางจิตใจหรือพฤติกรรม หรือออทิสติก";
		}
		if($DEFORM_ID == 5 || $DEFORM_ID == 10 || $DEFORM_ID == 16){
			$DEFORM_ID = "ความพิการทางสติปัญญา";
		}
		if($DEFORM_ID == 6 || $DEFORM_ID == 11 || $DEFORM_ID == 17){
			$DEFORM_ID = "ความพิการทางการเีรียนรู้";
		}
		if($DEFORM_ID == 18){
			$DEFORM_ID = "ทางออทิสติก";
		}
	
		$curator_lid = doCleanInput($_POST["curator_lid"]);
		
		$sql = "
			insert into 
				curator(
				
					curator_name
					,curator_idcard
					,curator_gender
					,curator_age
					,curator_lid
					
					,curator_parent										
					,curator_disable_desc					
					, curator_is_disable
					
					
					
				)values(
				
				
					'$PREFIX_NAME_ABBR $FIRST_NAME_THAI $LAST_NAME_THAI'
					,'$PERSON_CODE'
					,'". strtolower($SEX_CODE)."'
					,'$BIRTH_DATE'
					,'$curator_lid'
					
					,'$curator_parent'					
					,'$DEFORM_ID'					
					, '1'					
				
				)
				
			";
			
			//echo $sql;
			
		mysql_query($sql);
		
		$curator_last_id = mysql_insert_id();	
		
		
		//end add curator
		if($this_year >= 2013){
		
			//only do auto post if >= year 2013
			$_GET["auto_post"] = 1;
			//also add curator flag
			$_GET["curate"] = "curate";
			
			$_GET["curator_id"] = $curator_last_id;
			
		}
		
		
		
	}		
	
	
	
	
	
	//-----------------------------------------
	//--------------- try get mn_desperson -> ended above
	//-----------------------------------------
	
	
	
	
	
	
	//-----------------------------------------
	//--------------- try get curator -> start below -> only start this if not have maimad from above
	//-----------------------------------------
	
	
	if(!$has_maimad && !$curator_parent){ //only do this if didn't have maimad, and have no parent
		
		
		$oci_sql = "select * from MN_DES_CURATOR where PERSON_CODE = '$the_id'";
		//echo $oci_sql;
		$s = oci_parse($oracle_connect, $oci_sql);
		oci_execute($s, OCI_DEFAULT);
		while (oci_fetch($s)) {
		
		
			$FIRST_NAME_THAI = oci_result($s, "FIRST_NAME_THAI");
			$FIRST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($FIRST_NAME_THAI));
			
			$LAST_NAME_THAI = oci_result($s, "LAST_NAME_THAI");
			$LAST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($LAST_NAME_THAI));
			
			$RELATION_OTHER = oci_result($s, "RELATION_OTHER");
			$RELATION_OTHER = iconv("WINDOWS-874", "UTF-8", ($RELATION_OTHER));
			
			$SALARY = oci_result($s, "SALARY");
			
			$ISSUE_DATE = oci_result($s, "ISSUE_DATE");
			
			$the_prefix = oci_result($s, "PREFIX_CODE_THAI");
					
			$the_maimad = oci_result($s, "MAIMAD_ID");
			
			//echo $the_prefix." - ".$FIRST_NAME_THAI . " - ". $LAST_NAME_THAI;
		
		}
		
		//get prefix
		$s = oci_parse($oracle_connect, "select * from BS_PREFIX where PREFIX_CODE = '$the_prefix'");
		oci_execute($s, OCI_DEFAULT);
		while (oci_fetch($s)) {
			//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
			
			$PREFIX_NAME_ABBR = oci_result($s, "PREFIX_NAME_ABBR");
			$PREFIX_NAME_ABBR = iconv("WINDOWS-874", "UTF-8", ($PREFIX_NAME_ABBR));
		}
	
	
		
		//echo "<br>- curator is -> ". $PREFIX_NAME_ABBR . " ".$FIRST_NAME_THAI . " " .$LAST_NAME_THAI. " " .$RELATION_OTHER . " " . $SALARY . " " . $ISSUE_DATE;
		
		if($FIRST_NAME_THAI){
		
			//got this curator info
			//-> insert it into curator
			
			$the_curator_gender = "m";
			
			if($PREFIX_NAME_ABBR == "นาง" || $PREFIX_NAME_ABBR == "นางสาว" || $PREFIX_NAME_ABBR == "น.ส."){
				$the_curator_gender = "f";
			}
			
			$curator_lid = doCleanInput($_POST["curator_lid"]);
			
			$sql = "
				insert into 
					curator(
					
						curator_name
						,curator_idcard
						,curator_gender
						
						,curator_lid
						,curator_parent
						
						,curator_event_desc
						
						, curator_value
						
					)values(
					
					
						'$PREFIX_NAME_ABBR $FIRST_NAME_THAI $LAST_NAME_THAI'
						,'$the_id'
						,'$the_curator_gender'
						
						,'$curator_lid'
						,'0'
						
						,'$RELATION_OTHER'
						
						, '$SALARY'
					
					)
					
				";
				
			//echo $sql;
			
			mysql_query($sql);
			
			$curator_last_id = mysql_insert_id();
			
			//next try MAIMAD DATA
			$mm = oci_parse($oracle_connect, "select * from MN_DES_PERSON where MAIMAD_ID = '$the_maimad'");
			oci_execute($mm, OCI_DEFAULT);
			
			while (oci_fetch($mm)) {
		
				//
				$PERSON_CODE = oci_result($mm, "PERSON_CODE");
				//
				
				$FIRST_NAME_THAI = oci_result($mm, "FIRST_NAME_THAI");
				$FIRST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($FIRST_NAME_THAI));
				
				$LAST_NAME_THAI = oci_result($mm, "LAST_NAME_THAI");
				$LAST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($LAST_NAME_THAI));
				
				$SEX_CODE = oci_result($mm, "SEX_CODE");
				$BIRTH_DATE = oci_result($mm, "BIRTH_DATE");
				$BIRTH_DATE = birthday($BIRTH_DATE);
				
				$the_prefix = oci_result($mm, "PREFIX_CODE_THAI");
				
				////////
				$mmm = oci_parse($oracle_connect, "select * from MN_DES_DEFORMED where MAIMAD_ID = '$the_maimad'");
				oci_execute($mmm, OCI_DEFAULT);
				while (oci_fetch($mmm)) {
					//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
					
					$DEFORM_ID = oci_result($mmm, "DEFORM_ID");
				}
			
			}
			
			//get prefix
			$s = oci_parse($oracle_connect, "select * from BS_PREFIX where PREFIX_CODE = '$the_prefix'");
			oci_execute($s, OCI_DEFAULT);
			while (oci_fetch($s)) {
				//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
				
				$PREFIX_NAME_ABBR = oci_result($s, "PREFIX_NAME_ABBR");
				$PREFIX_NAME_ABBR = iconv("WINDOWS-874", "UTF-8", ($PREFIX_NAME_ABBR));
			}
			
			
			//exit();
			
			if($DEFORM_ID == 1 || $DEFORM_ID == 6 || $DEFORM_ID == 12){
				$DEFORM_ID = "ความพิการทางการเห็น";
			}
			if($DEFORM_ID == 2 || $DEFORM_ID == 7 || $DEFORM_ID == 13){
				$DEFORM_ID = "ความพิการทางการได้ยินหรือสื่อความหมาย";
			}
			if($DEFORM_ID == 3 || $DEFORM_ID == 8 || $DEFORM_ID == 14){
				$DEFORM_ID = "ความพิการทางการเคลื่อนไหวหรือร่างกาย";
			}
			if($DEFORM_ID == 4 || $DEFORM_ID == 9 || $DEFORM_ID == 15){
				$DEFORM_ID = "ความพิการทางจิตใจหรือพฤติกรรม หรือออทิสติก";
			}
			if($DEFORM_ID == 5 || $DEFORM_ID == 10 || $DEFORM_ID == 16){
				$DEFORM_ID = "ความพิการทางสติปัญญา";
			}
			if($DEFORM_ID == 6 || $DEFORM_ID == 11 || $DEFORM_ID == 17){
				$DEFORM_ID = "ความพิการทางการเีรียนรู้";
			}
			if($DEFORM_ID == 18){
				$DEFORM_ID = "ทางออทิสติก";
			}
		
			
			
			$sql = "
				insert into 
					curator(
					
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,curator_parent
											
						,curator_disable_desc
						
						, curator_is_disable
						
					)values(
					
					
						'$PREFIX_NAME_ABBR $FIRST_NAME_THAI $LAST_NAME_THAI'
						,'$PERSON_CODE'
						,'". strtolower($SEX_CODE)."'
						,'$BIRTH_DATE'
						,'$curator_lid'
						,'$curator_last_id'
						
						
						,'$DEFORM_ID'
						
						, '1'					
					
					)
					
				";
				
				//echo $sql;
				
				mysql_query($sql);
				
				
				//end add curator
				if($this_year >= 2013){
				
					//only do auto post if >= year 2013
					$_GET["auto_post"] = 1;
					
					//also add curator flag
					$_GET["curate"] = "curate";
					
					$_GET["curator_id"] = $curator_last_id;
					
				}
		
		}else{
		
			//no first name thai?>        
			<script>
			alert("ไม่พบข้อมูลผู้ใช้สิทธิ");
			</script>        
			<?php
		
		}
	
	
	}
	
	
	//-----------------------------------------
	//--------------- try get curator -> end above
	//-----------------------------------------
	
	
	
	

}



include "scrp_add_curator.php";



if($this_lawful_year >= 2013 ){
					
	//show main branch only
	$is_2013 = 1;

}




?>
<?php include "header_html.php";?>
<?php include "global.js.php";?>
              <td valign="top">
              
              	<?php //echo $this_lawful_year; ?>
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                    <?php if($mode == "new"){ ?>
					เพิ่มข้อมูล<?php echo $the_company_word;?>
                    <?php }elseif($mode == "edit"){ ?>
                    การจ้างงานคนพิการของ<?php echo $the_company_word;?>: <font color="#006699"><?php $company_name_to_use = formatCompanyName($output_values["CompanyNameThai"],$output_values["CompanyTypeCode"]); echo $company_name_to_use;?>
                    <?php } ?>
                    </font></h2>
                    
                    <?php if(strpos($this_page, "mode=new")){ ?>
                    
                    <?php }else{ ?>
                     <div style="padding:5px 0 10px 2px"><a href="org_list.php">รายชื่อ<?php echo $the_company_word;?></a> > <?php echo $company_name_to_use;?></div>
                     <?php }?>
                    
                    
                    
                    <?php
					
					
						//is this main branch or not?
						if($output_values["BranchCode"] > 0){
						
							//have branch code that is not 0
							//this is not a main branch
							$is_main_branch = 0;
						
						}else{
						
							$is_main_branch = 1;
						
						}
						
						if($_GET["all_tabs"] == 1 || $_GET["focus"]){
						
							$show_all_tabs = 1;
						
						}
					  
					  
					  
						//if this is not main branch, see if have lawfulness records
						if(!$is_main_branch){
						
							//see if has lawful
							$lawful_record = getFirstItem("select count(*) from lawfulness where CID = '".$output_values["CID"]."'");
							
							//
						
						}
						
						//echo $lawful_record;
					
					?>
                    
                    
                    
                    
                    <?php 
					
					//have info inputted from company?
					
					if($sess_accesslevel != 4){

						//only do this for non-company					
													
						//$count_info = countCompanyInfo($output_values["CID"], $this_lawful_year);
						
						$count_info = getFirstItem("select 
							count(*)
						from 
							lawfulness_company 
						where 
							CID = '".$output_values["CID"]."' 
							and 
							Year = '$this_lawful_year' 
							
							");
													
					}
							
					if(countCompanyInfo($output_values["CID"], $this_lawful_year) && $sess_accesslevel != 4){
					
					
					?>
                    
                     <div align="right" style="padding:10px 30px 10px 0;">
	                    <strong style="font-size: 20px; color:#C30; ">*** มีการส่งข้อมูลเข้ามาใหม่จากสถานประกอบการ</strong>
                    </div>
                    
                    
                    <?php
							
						
					}
					
					
					?>
                    
                    
                   
                    
                <table width="100%" >
                        <tr>
                        <td class="td_bordered">
              <table cellspacing="0">
                                <tr>
                                
                                  
                                  <?php if($mode != "new"){ ?>
                                  
                                  
                                  <td <?php echo $hide_style;?>>
                                      <a href="#history" onClick="showTab('history'); return false;">
                                      <div id="tab_history_black" class="white_on_black" style="width:160px;" align="center">ประวัติการปฎิบัติตามกฎหมาย</div>
                                      <div id="tab_history_grey" class="white_on_grey" style="width:160px; display:none; " align="center">ประวัติการปฎิบัติตามกฎหมาย</div>
                                      </a>
                                  </td>
                                  
                                  
                                  
                                  <td><a href="#general" onClick="showTab('general'); return false;">
                                  <div id="tab_general_black" class="white_on_black" style="width:120px; display:none;" align="center">ข้อมูลทั่วไป/ที่อยู่</div>
                                  <div id="tab_general_grey" class="white_on_grey" style="width:120px;" align="center">ข้อมูลทั่วไป/ที่อยู่</div>
                                  </a></td>
                                  
                                  
                                  
                                  <?php
								  
								  	
								  
								  
								  
								  
								  	//hide other tabs for non-main brancg
									//that have no lawful records
									if(
										(!$is_main_branch && $lawful_record < 1 && !$show_all_tabs)
										
										||
										(!$is_main_branch && $this_year >= 2012 && !$show_all_tabs)
										
										){
									
										$hide_style = 'style="display:none"';
									
									}
								  
								  ?>
                                  
                                  
                                  <td <?php echo $hide_style;?>>
									  <?php if($sess_accesslevel != 4){?>
                                          <a href="#official" onClick="showTab('official'); return false;">
                                          <div id="tab_official_black" class="white_on_black" style=" display:none;" align="center">จดหมายแจ้ง</div>
                                          <div id="tab_official_grey" class="white_on_grey" style=" " align="center">จดหมายแจ้ง</div>
                                          </a>
                                      <?php }?>
                                  </td>
                                  
                                  
                                  
                                  
                                  <td <?php echo $hide_style;?>>
                                      <a href="#lawful" onClick="showTab('lawful'); return false;">
                                      <div id="tab_lawful_black" class="white_on_black" style="width:150px; display:none;" align="center">การปฏิบัติตามกฎหมาย</div>
                                      <div id="tab_lawful_grey" class="white_on_grey" style="width:150px; " align="center">การปฏิบัติตามกฎหมาย</div>
                                      </a>
                                  </td>
                                  
                                  
                                  
                                  <?php if($count_info){ //only show this if info sumbitted?>
                                   <td <?php echo $hide_style;?>>
                                      <a href="#input" onClick="showTab('input'); return false;">
                                      <div id="tab_input_black" class="white_on_black" style="width:160px; display:none;" align="center">ข้อมูลจากสถานประกอบการ</div>
                                      <div id="tab_input_grey" class="white_on_grey" style="width:160px;  " align="center">ข้อมูลจากสถานประกอบการ</div>
                                      </a>
                                  </td>
                                  <?php }?>
                                  
                                  
                                  
                                  
                                  
                                  
                                  
                                  
                                  <?php }else{?>
                                  <td><a href="#" >
                                  <div id="tab_general_black" class="white_on_black" style="width:120px;" align="center">ข้อมูลทั่วไป/ที่อยู่</div>
                                  </a></td>
                                  <?php }?>
                                  
                                  
                                  
                                </tr>
                          </table>
                         </td>
                    </tr>
                </table>
                  <script>	
				  	function showTab(what){
						//toggle table on/off
						document.getElementById('general').style.display = 'none';
						
						
						
						document.getElementById('lawful').style.display = 'none';
						
						document.getElementById('history').style.display = 'none';
						
						
						
						
						document.getElementById('tab_general_black').style.display = 'none';
						document.getElementById('tab_general_grey').style.display = '';
						
						
						document.getElementById('tab_history_black').style.display = 'none';
						document.getElementById('tab_history_grey').style.display = '';

						<?php if($sess_accesslevel != 4){?>
						document.getElementById('official').style.display = 'none';
						document.getElementById('tab_official_black').style.display = 'none';
						document.getElementById('tab_official_grey').style.display = '';
						<?php } ?>
						
						
						<?php if($count_info){ //only show this if info sumbitted?>
						document.getElementById('input').style.display = 'none';
						document.getElementById('tab_input_black').style.display = 'none';
						document.getElementById('tab_input_grey').style.display = '';
						<?php }?>
						
						document.getElementById(what).style.display = '';
						
						document.getElementById('tab_lawful_black').style.display = 'none';
						document.getElementById('tab_lawful_grey').style.display = '';
						
						
						
						
						
						
						document.getElementById('tab_'+what+'_black').style.display = '';
						document.getElementById('tab_'+what+'_grey').style.display = 'none';
						
					}
					
				  </script>
                  <script language='javascript'>
						<!--
						function validateForm(frm) {
							
							if(frm.CompanyCode.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: <?php echo $the_code_word;?>");
								frm.CompanyCode.focus();
								return (false);
							}
							if(frm.CompanyNameThai.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อบริษัท(ภาษาไทย)");
								frm.CompanyNameThai.focus();
								return (false);
							}
							if(frm.Employees.value.length == 0)
							{
								alert("กรุณาใส่ข้อมูล: จำนวน<?php echo $the_employees_word;?>");
								frm.Employees.focus();
								return (false);
							}
							//----
							if(frm.CompanyTypeCode.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: ประเภทธุรกิจ");
								frm.CompanyTypeCode.focus();
								return (false);
							}
							
							<?php if($output_values["CompanyTypeCode"] >= 200 && $output_values["CompanyTypeCode"] <= 300 || $sess_accesslevel == 6 ||  $sess_accesslevel == 7){ //don't validate this if this is an GOV company?> 
							
							<?php }else{?>
							
								if(frm.BusinessTypeCode.selectedIndex == 0)
								{
									alert("กรุณาใส่ข้อมูล: ประเภทกิจการ");
									frm.BusinessTypeCode.focus();
									return (false);
								}
							
							<?php }?>
							
							<?php if($sess_accesslevel != 3){ //only admin can select a province?>
							if(frm.Province.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: จังหวัด"); 
								frm.Province.focus();
								return (false);
							}
							<?php }?>
							
							
							//----
							return(true);									
						
						}
						-->
					
					</script>
                    <?php if($mode == "new"){ ?>
                   
                    
                    <form method="post" action="scrp_add_org.php" onsubmit="return validateForm(this);">
                    <?php }elseif($mode == "edit"){ ?>
                <form method="post" action="scrp_update_org.php" onSubmit="return validateForm(this);">
                    <input name="CID" type="hidden" value="<?php echo $output_values["CID"];?>" />
                    <?php } ?>
                    
                    <?php 
						if($_GET["new_id"]){
					?>							
                         <div style="color:#990000; padding:5px 0 0 0; font-weight: bold;">* ข้อมูล<?php echo $the_code_word;?> <a href="organization.php?id=<?php echo $_GET["new_id_link"];?>"><?php echo $_GET["new_id"];?> สาขา <?php echo $_GET["branch"];?></a> มีอยู่ในระบบแล้ว</div>
                    <?php
						}					
					?>
                    
                    <?php 
						if($_GET["added"]=="added"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ข้อมูล<?php echo $the_company_word;?>ใหม่ได้ถูกบันทึกลงฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["updated"]=="updated" && !isset($_GET["year"])){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูล<?php echo $the_company_word;?>เสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["updated"]=="updated" && isset($_GET["year"])){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูล<?php echo $the_company_word;?>ปี <?php echo formatYear($_GET["year"]);?> เสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["reg"]=="reg"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มรหัสไปรษณีย์ลงทะเบียนเสร็จสิ้น</div>
                    <?php
						}					
					?>
					<?php 
						if($_GET["delletter"]=="delletter"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* จดหมายแจ้งได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["delpayment"]=="delpayment"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ข้อมูลการส่งเงินเข้ากองทุนได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                    
                    <?php //echo $this_year;?>
                  <table style=" padding:10px 0 0px 0; " id="general">
                  
                  
                  
                  
                  
                  	<?php // populate branch info 
					
					
						
						
						
					
						$branch_count = getFirstItem("select count(*) from company where CompanyCode = '".$output_values["CompanyCode"]."'");
					
						if($branch_count > 1 && $this_year >= 2013){
					
					?>
                    
                    
                    
                    
                    
                    
                    
                    <?php 
						//if($this_year >= 2013){ //only show this for years more than 2013
						//if(1==1){	
					?>
                  
                 	  <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:5px 0 5px 0;">ข้อมูลสาขา</div><?php //echo $is_main_branch;?></td>
                      </tr>
                      <tr>
                        <td colspan="4">
                        
                        
                        		
                        
                        		<div >
                                    <table cellpadding="3" style="border-collapse:collapse;" border="1">
                                        <tr bgcolor="#9C9A9C" align="center">
                                             <td>
                                                <div align="center">
                                                    <span class="column_header">
                                                    รหัสสาขา
                                                    </span>
                                                </div>
                                             </td> 
                                            <td>
                                                <div align="center">
                                                    <span class="column_header">
                                                    ชื่อสาขา
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div align="center">
                                                    <span class="column_header">
                                                    จำนวน<?php echo $the_employees_word;?> (คน)
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        
                                        <?php
										
										
											$get_branch_sql = "select * from company where CompanyCode = '".$output_values["CompanyCode"]."' order by BranchCode asc";
										
											$branch_result = mysql_query($get_branch_sql);
										
											
										
											//total records 
											$total_records = 0;
										
											while ($branch_row = mysql_fetch_array($branch_result)) {
											
												$padding_left = 'style="';
												$is_selected = 0;
												
												if($branch_row["BranchCode"] > 0){
													$padding_left .= 'padding-left:20px;';
												}
												
												if($branch_row["BranchCode"]  == $output_values["BranchCode"]){
													$padding_left .= ' font-weight: bold; color:#009900;';
													$is_selected = 1;
												}
												
												$padding_left .= '"';
											
										?>
                                        
                                        
                                        	 <tr >
                                                 <td <?php echo $padding_left?> >
                                                    <?php echo $branch_row["BranchCode"];?>
                                                 </td> 
                                                <td <?php echo $padding_left?>>
                                                
                                                	<?php if(!$is_selected){?>
	                                                	<a href="organization.php?id=<?php echo $branch_row["CID"];?><?php
                                                        
															if($show_all_tabs){
																echo "&all_tabs=1";
															}
														
														?>&year=<?php echo $this_year;?>" style="font-weight: normal;">
                                                    <?php }?>
                                                    
                                                    
                                                	    <?php echo $branch_row["CompanyNameThai"];?>
                                                    
                                                    <?php if(!$is_selected){?>
	                                                	</a>
                                                    <?php }?>
                                                    
                                                    
                                                    
                                                 </td>
                                                 <td <?php echo $padding_left?>>
                                                 	<div align="right">
                                                    <?php 
													
													echo formatEmployee($branch_row["Employees"]);
													$sum_employees += $branch_row["Employees"];
													
													?>
                                                    </div>
                                                 </td>
                                            </tr>
                                        
                                        
                                        <?php										
											
											}
										
										?>
                                        
                                        	<tr >
                                             <td>
                                               
                                             </td> 
                                            <td bgcolor="">
                                                <div align="right">
                                                   
                                                    รวม
                                                  
                                                </div>
                                            </td>
                                            <td>
                                                <div align="right">
                                                    <?php echo formatEmployee($sum_employees);?>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        
                                        
                                        
                                    </table>
                               </div>
                                
                               
                                
                                
                        
                        </td>
                      </tr>
                      
                      
                      <?php // }?>
                      
                      
                      
                       <?php }//show all tabs?>
                      
                      
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                      <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:0 0 5px 0;">ข้อมูลทั่วไป</div></td>
                      </tr>
                      <tr>
                        <td><?php echo $the_code_word;?>: </td>
                        <td><label>
                          <?php 
						  		if($sess_accesslevel == 4){ 
									//company didnt see this textbox
									 echo $output_values["CompanyCode"];
                          		}else{ 
						  ?>
	                          <input type="text" name="CompanyCode" value="<?php echo $output_values["CompanyCode"];?>" />*
                          <?php }?>    
                            </label></td>
							
							
                       
						
								
								 <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
							   
								<?php }else{?>
									  <td class="td_left_pad"> เลขที่ประจำตัวผู้เสียภาษีอากร: </td>
										<td><input type="text" name="TaxID" value="<?php echo $output_values["TaxID"];?>" /></td>
								<?php }?>
						
						
						
                      </tr>
                      <tr>
                        <td>เลขที่สาขา:</td>
                        <td>
                         <?php 
						  		if($sess_accesslevel == 4){ 
									//company didnt see this textbox
									 echo $output_values["BranchCode"];
                          		}else{ 
						  ?>
                        <input type="text" name="BranchCode" value="<?php echo $output_values["BranchCode"];?>" />
                        <?php } ?></td>
                        <td class="td_left_pad">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td > 
                        <?php
						
						if($sess_accesslevel == "6" || $sess_accesslevel == "7"){
							
							echo "ประเภทหน่วยงาน";
							
							
						}else{
							
							echo "ประเภทธุรกิจ";
							
						}
						
						?>
                        :</td>
                        <td><?php include "ddl_org_type.php";?>
                          * </td>
						  
					  <?php if($output_values["CompanyTypeCode"] >= 200 && $output_values["CompanyTypeCode"] <= 300 || $sess_accesslevel == 6 ||  $sess_accesslevel == 7){ //don't validate this if this is an GOV company?> 
						
						
						<?php }else{?>
									<td class="td_left_pad"> ประเภทกิจการ:</td>
									<td><?php include "ddl_bus_type.php";?> *</td>
						<?php }?>
						
						
                      </tr>
                      
					  
					  <tr>
                        <td>
						
						
						  <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
								ชื่อหน่วยงาน (ภาษาไทย): 
							<?php }else{?>
								 ชื่อบริษัท (ภาษาไทย): 
							<?php }?>
						</td>
                        <td><input type="text" name="CompanyNameThai" value="<?php echo ($output_values["CompanyNameThai"]);?>" />
                        *</td>
                        <td class="td_left_pad"> 
						
						
						 <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
								ชื่อหน่วยงาน (ภาษาอังกฤษ): 
							<?php }else{?>
								 ชื่อบริษัท (ภาษาอังกฤษ): 
							<?php }?>
						
						</td>
                        <td><input type="text" name="CompanyNameEng" value="<?php echo $output_values["CompanyNameEng"];?>" /></td>
                      </tr>
					  
					  
                      <tr>
                        <td > จำนวน<?php echo $the_employees_word;?>:</td>
                        <td><input type="text" name="Employees" id="Employees2" value="<?php echo formatEmployee($output_values["Employees"]);?>" onChange="addEmployeeCommas('Employees2');"/>
                          คน*<?php include "js_format_employee.php";?></td>
                        
                        
                        
                         <?php
						
						if($sess_accesslevel == "6" || $sess_accesslevel == "7"){
							
						?>
                        
                        
                             <td class="td_left_pad">
                            
                            </td>
                            <td></td>
                        
                        <?php							
							
						}else{
							
						?>
                        
                             <td class="td_left_pad">
                            สถานะของกิจการ
                            </td>
                            <td><?php include "ddl_company_status.php";?></td>
                        
                        <?php
							
						}
						
						?>
                       
                        
                        
                      </tr>
                     
                      <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:5px 0 5px 0;">ที่อยู่</div></td>
                      </tr>
                      <tr>
                        <td>สถานที่ตั้งเลขที: </td>
                        <td><label>
                          <input type="text" name="Address1" value="<?php echo $output_values["Address1"];?>" />
                        </label></td>
                        <td class="td_left_pad">ซอย: </td>
                        <td><input type="text" name="Soi" value="<?php echo $output_values["Soi"];?>" /></td>
                      </tr>
                      <tr>
                        <td>หมู่:</td>
                        <td><input type="text" name="Moo" value="<?php echo $output_values["Moo"];?>" /></td>
                        <td class="td_left_pad"> ถนน:</td>
                        <td><input type="text" name="Road" value="<?php echo $output_values["Road"];?>" /></td>
                      </tr>
                      <tr>
                        <td>ตำบล/แขวง: </td>
                        <td><input type="text" name="Subdistrict" value="<?php echo $output_values["Subdistrict"];?>" /></td>
                        <td class="td_left_pad"> อำเภอ/เขต:</td>
                        <td><input type="text" name="District" value="<?php echo $output_values["District"];?>" /></td>
                      </tr>
                      <tr>
                        <td>จังหวัด: </td>
                        <td><?php include "ddl_org_province.php"?>
*</td>
                        <td class="td_left_pad"> รหัสไปรษณีย์:</td>
                        <td><input type="text" name="Zip" value="<?php echo $output_values["Zip"];?>" /></td>
                      </tr>
                      <tr>
                        <td>โทรศัพท์:</td>
                        <td><input type="text" name="Telephone" value="<?php echo $output_values["Telephone"];?>" /></td>
                        <td class="td_left_pad">email:</td>
                        <td><input type="text" name="email" value="<?php echo $output_values["email"];?>" /></td>
                      </tr>
                      <tr>
                        <td>เวปไซต์:</td>
                        <td><input type="text" name="org_website" value="<?php echo $output_values["org_website"];?>" /></td>
                        <td class="td_left_pad">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:5px 0 5px 0;">ข้อมูลติดต่อ</div></td>
                      </tr>
                      <tr>
                        <td>ชื่อผู้ติดต่อ 1: </td>
                        <td><label>
                          <input type="text" name="ContactPerson1" value="<?php echo $output_values["ContactPerson1"];?>" />
                        </label></td>
                        <td class="td_left_pad">เบอร์โทรศัพท์: </td>
                        <td><input type="text" name="ContactPhone1" value="<?php echo $output_values["ContactPhone1"];?>" /></td>
                      </tr>
                      <tr>
                        <td>ตำแหน่ง:</td>
                        <td><input type="text" name="ContactEmail1" value="<?php echo $output_values["ContactEmail1"];?>" /></td>
                        <td class="td_left_pad"> อีเมล์:</td>
                        <td><input type="text" name="ContactPosition1" value="<?php echo $output_values["ContactPosition1"];?>" /></td>
                      </tr>
                       <tr>
                        <td>ชื่อผู้ติดต่อ 2: </td>
                        <td><label>
                          <input type="text" name="ContactPerson2" value="<?php echo $output_values["ContactPerson2"];?>" />
                        </label></td>
                        <td class="td_left_pad">เบอร์โทรศัพท์: </td>
                        <td><input type="text" name="ContactPhone2" value="<?php echo $output_values["ContactPhone2"];?>" /></td>
                      </tr>
                      <tr>
                        <td>ตำแหน่ง:</td>
                        <td><input type="text" name="ContactEmail2" value="<?php echo $output_values["ContactEmail2"];?>" /></td>
                        <td class="td_left_pad"> อีเมล์:</td>
                        <td><input type="text" name="ContactPosition2" value="<?php echo $output_values["ContactPosition2"];?>" /></td>
                      </tr>
                      <tr>
                        <td colspan="4">
                          <div align="center">
                          	<hr />
                            
                            <?php if($sess_accesslevel !=5){ //exec can't do these?>
                                
                                <?php if($mode == "new"){?>                            
                             
                                เพิ่มข้อมูลปฎิบัติตามกฏหมายสำหรับปี:
                             	<?php include "ddl_year.php";?>
                                
                                <input type="submit" name="button" id="button" value="เพิ่มรายชื่อ<?php echo $the_company_word;?>" 
                                onclick = "return confirm('ต้องการเพิ่มรายชื่อ<?php echo $the_company_word;?>นี้?');"
                                 />
                                 
                                 
                                
                                <?php }?>
                                <?php if($mode == "edit" && $sess_accesslevel != "4"){?>
                                <input type="submit" name="button" id="button" value="ปรับปรุงข้อมูล<?php echo $the_company_word;?>" 
                                onclick = "return confirm('ต้องการปรับปรุงข้อมูล<?php echo $the_company_word;?>นี้?');"
                                />
                                <?php }?>
                                
                             <?php }?>
                          </div>                        </td>
                      </tr>
                      <tr>
                        <td colspan="4">
                          <div align="left">
                          	<hr />
                            <?php if($mode == "edit" && $sess_accesslevel == 1){?>
                            <input type="submit" name="btn_delete" id="btn_delete" value="'ลบ'<?php echo $the_company_word;?>" 
                             onclick = "return doConfirmDelete();"
                            />
                            <script>
								function doConfirmDelete(){
									confirm_1 = confirm('ต้องการลบข้อมูล<?php echo $the_company_word;?>นี้? ข้อมูล<?php echo $the_company_word;?>ที่ถูกลบไปแล้วจะไม่สามารถนำกลับคืนมาได้');
									
									if(confirm_1){
										return confirm('กดยืนยันอีกครั้งเพื่อลบ<?php echo $the_company_word;?>นี้');
									}else{
										return false;
									}
								}
							</script>
                            <?php }?>
                          </div>                        </td>
                      </tr>
              </table>
                </form>
              
              
              
              
              
              
              
              
              
              
              
              
              
              
              
             <table style=" padding:10px 0 0px 0; " id="history" width="100%">
             
             	<tr>
                    <td ><div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการปฎิบัติตามกฎหมาย</div></td>
                  </tr>
                  
                  
                  
                  
                  <tr>
                  
                  	<td>
                    
                    <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                    	<tr bgcolor="#9C9A9C" align="center" >
                    	  <td><div align="center"><span class="column_header">ปี</span> </div></td>
           	           	   
                            
                            <td>
                            	<div align="center"><span class="column_header">จำนวน<?php echo $the_employees_word;?> (ราย)</span> </div>                            </td>
                            
                             <td><div align="center"><span class="column_header">จดหมายแจ้ง</span> </div></td>
                             <td>
                            	<div align="center"><span class="column_header">อัตราส่วน</span> </div>                            </td>
                            
                            <td ><div align="center"><span class="column_header">รับคนพิการเข้าทำงาน<br />
                              ตามมาตรา 33 (ราย)</span></div></td>
                            
                            <!--
                            <td ><div align="center"><span class="column_header">เงินที่ต้องส่งเข้ากองทุน<br />
                            ตามมาตรา 34</span> </div></td>-->
                            
							
							<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){?>
							
							<?php }else{?>
							
								<td ><div align="center"><span class="column_header">จ่ายเงินแทนการรับคนพิการ<br />
								ตามมาตรา 34</span> </div></td>
								
							<?php }?>
                           
                            <td ><div align="center"><span class="column_header">การให้สัมปทาน<br />
                            ตามมาตรา 35 (ราย)</span></div></td>
                            <td ><div align="center"><span class="column_header">สถานะ</span> </div></td>
                    	</tr>
                        
                        <?php
						
						
						//generate letter history
							$get_history_sql = "
										select
											 *
										from 
											lawfulness
										where
											CID = '$this_id'
											and
											Year <= '".(date("Y")+1)."'
											and
											Year >= '$dll_year_start'	
										
										order by Year desc
										
										";
							
							//echo $get_letter_sql;
							
							$history_result = mysql_query($get_history_sql);
							
							while ($lawful_row = mysql_fetch_array($history_result)) {
							
								$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_".$lawful_row["Year"]."'"),100);
								
								if($lawful_row["Employees"] > 0){
									$employees_to_use = $lawful_row["Employees"];
								}else{
									$employees_to_use = $output_values["Employees"];
									
									if($lawful_row["LawfulStatus"] == 3 && $lawful_row["Year"] == 2011){
									
										$employees_to_use = 0;
									
									}
									
									
								}
								
								$final_employee = getEmployeeRatio( $employees_to_use,$ratio_to_use);
			
							
								$curator_usee = getFirstItem("select 
									count(*) 
								from 
									curator 
									, lawfulness
								where 
								
									curator_lid = LID
									and
									Year = '".$lawful_row["Year"]."' 
									and
									CID = '$this_id'							
									
										
									and 
									
									(
									
										curator_parent in(
										
											select curator_id 
											from 
												curator 
												, lawfulness
											where 
											
												curator_lid = LID
												and
												Year = '".$lawful_row["Year"]."' 
												and
												CID = '$this_id'	
											
												and curator_parent = 0
										
										)
										
										
										
										OR (
									
											curator_is_disable = 1
											and 
											curator_parent = 0
										
										)
									
									)
									
									
									
									
									");
									
									
									$the_sql = "select sum(receipt.Amount) 
														from payment, receipt , lawfulness
														where 
														receipt.RID = payment.RID
														and
														lawfulness.LID = payment.LID
														and
														ReceiptYear = '".$lawful_row["Year"]."'
														and
														lawfulness.CID = '$this_id'
														and
														is_payback != 1
														";
														
									$paid_money_history = getFirstItem("$the_sql");
									
									$the_sql = "select sum(receipt.Amount) 
														from payment, receipt , lawfulness
														where 
														receipt.RID = payment.RID
														and
														lawfulness.LID = payment.LID
														and
														ReceiptYear = '".$lawful_row["Year"]."'
														and
														lawfulness.CID = '$this_id'
														and
														is_payback = 1
														";
														
									$back_money_history = getFirstItem("$the_sql");
									
									$paid_money_history = $paid_money_history - $back_money_history;
									
									
									//-----------
									$wage_rate = default_value(getFirstItem("select var_value from vars where var_name = 'wage_".$this_lawful_year."'"),159);
									//$year_date = date("z", mktime(0,0,0,12,31,$this_lawful_year));
									$year_date = 365;
									
									$start_money = $extra_employee*$wage_rate*$year_date;
									
									
							
							?>
                            
                                <tr >
                                  <td valign="top">
                                  
                                 
                                  	<div align="center">
                                    
                                    
                                  	<?php if($sess_accesslevel != 4 || ($sess_accesslevel == 4 && $lawful_row["Year"] >= 2014)){ ?>
                                        <a href="organization.php?id=<?php echo $this_id;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>">
 	                               <?php }?>
                                   
                                            <?php echo formatYear($lawful_row["Year"]);?>                                        
                                            
                                   <?php if($sess_accesslevel != 4 || ($sess_accesslevel == 4 && $lawful_row["Year"] >= 2014)){ ?>
                                        </a>
                                    <?php }?>
                                        
                                   </div>                                  </td>
                                  
                                   
                                    
                                    <td valign="top">
                                   	 <div align="right">
									 
									 <?php echo number_format($employees_to_use,0);?>
                                     
                                     </div>                                    </td>    
                                    
                                    
                                     <td valign="top">
                                     
                                     
                                     <?php
                        
										//try to show last "document_requests" entry
										
										
										$sql = "select * 
											from 
												document_requests
											where 
												docr_org_id  = '$this_id'
												
												 and docr_year = '".$lawful_row["Year"]."'
											
											order by docr_id desc
											limit 0,1";
										//echo $sql;
										$docr_row_history = getFirstRow($sql);
									
										if($docr_row_history["docr_status"] == 1){
											?>
                                            <a href="organization.php?id=<?php echo $this_id;?>&focus=official&year=<?php echo $lawful_row["Year"];?>"><font color='green'>ได้รับเอกสารครบแล้ว</font></a>
											
											<?php
										}else{
											echo "";?>
                                            
                                            
                                            <a href="organization.php?id=<?php echo $this_id;?>&focus=official&year=<?php echo $lawful_row["Year"];?>"> <font color='#CC3300'>ได้รับเอกสารยังไม่ครบ</font></a>
                                            
                                            <?php
										}
									
									?>
                                     
                                     </td>
                                     <td valign="top">
                                    
                                    <div align="center">
                                        <?php echo $ratio_to_use;?> ต่อ 1 = <?php echo $final_employee;?> ราย                                    </div>                                    </td>
                                    
                                    
                                        
                                    <td valign="top">
                                    <div align="right">
									
                                    <a href="organization.php?id=<?php echo $this_id;?>&focus=lawful&le=le&year=<?php echo $lawful_row["Year"];?>">
									<?php echo $lawful_row["Hire_NumofEmp"]?>
                                    </a>
                                    
                                    </div>                                    </td>
                                    
                                    <!--
                                    <td >
                                    	<div align="right">
                                    	<?php echo formatMoney($paid_money) ;?>
                                        </div>
                                    </td>-->
                                    
                                    <td valign="top" 
									
									<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){?>
									style="display:none;"
									<?php }?>
									
									>
                                    	<div align="right">
                                        
                                        <?php
										
										$the_sql = "select * 
														from payment, receipt , lawfulness
														where 
														receipt.RID = payment.RID
														and
														lawfulness.LID = payment.LID
														and
														ReceiptYear = '".$lawful_row["Year"]."'
														and
														lawfulness.CID = '$this_id'
														
														order by
														
														PaymentDate asc
														
														";
														
										$paid_money_history_result = mysql_query($the_sql);
										
										
										if(mysql_num_rows($paid_money_history_result)){
										
										?>
                                        
                                        
                                        <table border="1" style="border-collapse:collapse;" cellpadding="3" cellspacing="0">
                                        	<tr style="background-color:#CCCCCC;">
                                            	<td>
                                                 <div align="center">เล่มที่                                                </div></td>
                                                <td>
                                                  <div align="center">เลขที่                                                </div></td>
                                                <td>
                                                  <div align="center">จำนวนเงิน (บาท)                                                </div></td>
                                            </tr>
                                            
                                        
                                        
                                        <?php
										
										
										}
										
										while ($pmh_row = mysql_fetch_array($paid_money_history_result)) {
										
										?>
										
                                        <tr>
                                        	<td>
                                              <div align="left">
											  
											  <a href="view_payment.php?id=<?php echo $pmh_row["RID"];?>"><?php echo $pmh_row["BookReceiptNo"];?></a>
                                              
                                              </div></td>
                                             <td>
                                               <div align="left"><?php echo $pmh_row["ReceiptNo"];?>                                            </div></td>
                                            	<td>
                                                  <div align="right">
												  <?php if($pmh_row["is_payback"]){echo "<font >-";}?>
                                                  
												  <?php echo formatNumber($pmh_row["Amount"]);?>                                            
                                                  <?php if($pmh_row["is_payback"]){echo "</font>";}?>
                                                  
                                                  
                                                  </div></td>
                                        </tr>
                                        
                                        
                                        <?php	
										
										}
										
										?>
                                        
                                        
                                        
                                        
                                        <?php if(mysql_num_rows($paid_money_history_result)){?>
                                        
                                         <tr>
                                        	<td>
                                           </td>
                                             <td>
                                               รวม</td>
                                            	<td>
                                                  <div align="right"><a href="organization.php?id=<?php echo $this_id;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>#the_payment_details">
                                                  <?php echo formatMoney($paid_money_history) ;?>                                       
                                                    </a></div></td>
                                        </tr>
                                        
                                        
                                        	</table>
                                        
                                        <?php }else{ //end if if(mysql_num_rows($paid_money_history_result)){?>
                                        
                                        
                                        	<a href="organization.php?id=<?php echo $this_id;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>#the_payment_details">
                                                 0.00                                    
                                                    </a>
                                        
                                        <?php }?>
                                        
                                        
                                        
                                      </div>                                    </td>
                                   
                                    <td valign="top">
                                   	 	<a href="organization.php?id=<?php echo $this_id;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>&curate=curate"><div align="right"><?php 
										
											
											$this_curator_usee_sql = "
											
											
												select 
													count(*) 
												from 
													curator 
													, lawfulness
												where 
												
													curator_lid = LID
													and
													Year = '".$lawful_row["Year"]."' 
													and
													CID = '$this_id'							
													
														
													and 
													curator_parent = 0
											
										";
										
										
											$this_curator_usee_sqlssss = "
												select 
													count(*) 
												from 
													curator 
												where 
													curator_lid = '".$lawful_values["LID"]."' and curator_parent = 0
											";
														
											//echo $this_curator_usee_sql;
											
											$this_curator_usee = getFirstItem($this_curator_usee_sql);
											
											echo $this_curator_usee;
										
										
										?></div>        </a>                            </td>
                                    
                                    <td valign="top">
                                    
                                    	<div align="center"><?php echo getLawfulImage($lawful_row["LawfulStatus"]);?></div>                                    </td>
                                </tr>
                            
                            
                            <?php } ?>
                      </table>
                    
                    
                    
                    </td>
                    
                 </tr>
                  
                  
            </table>
            
            
            
            
            
            
            
            
            
            
            
              
              
              
              
              	<?php
				
					
					
					
				
					//only show this to non-company
					if($sess_accesslevel !=4){
				?>
              		
                    
                    <table style=" padding:10px 0 0px 0; " id="official">
                      
                      <tr>
                        <td><div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการส่งจดหมายแจ้ง</div> 
                        
                        <?php
						
						
						//echo $this_year;
							//echo $this_lawful_year;
						
						
						 if($sess_accesslevel != 5){?>
                        <a href="org_list.php?mode=letters&search_id=<?php echo $this_id;?>&for_year=<?php echo $this_lawful_year;?>">+ ส่งจดหมายแจ้ง</a>                         
                        <?php }?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                        
                        <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                    	<tr bgcolor="#9C9A9C" align="center" >
                    	  <td><div align="center"><span class="column_header">วันที่</span> </div></td>
           	           	    <td>
                            	<div align="center"><span class="column_header">ครั้งที่</span> </div></td>
                            <td ><div align="center"><span class="column_header">หนังสือเลขที่</span> </div></td>
                            <td ><div align="center"><span class="column_header">เลขที่ลงทะเบียน</span> </div></td>
                            <?php if($sess_accesslevel != 5){?>
                            <td >  <div align="center"><span class="column_header">ลบข้อมูล</span> </div></td>
                            <?php }?>
                          </tr>
                             
                         <?php 
							
							
							
							//generate letter history
							$get_letter_sql = "select *
										from documentrequest a, docrequestcompany b
										where
											a.rid = b.rid
										and 
											CID = '$this_id'
										
										$conditions	
										
										and is_hold_letter = '0'
										
										order by RequestDate desc
										
										";
							
							//echo $get_letter_sql;
							
							$letter_result = mysql_query($get_letter_sql);
							
							while ($post_row = mysql_fetch_array($letter_result)) {
							?>
                        <tr bgcolor="#ffffff" align="center" >
                          <td> <?php echo formatDateThai($post_row["RequestDate"]);?></td>
                       	    <td>

                            	<?php echo doCleanOutput($post_row["RequestNum"]);?>                          </td>
                            <td ><a href="view_letter.php?id=<?php echo doCleanOutput($post_row["RID"]);?>"><?php echo doCleanOutput($post_row["GovDocumentNo"]);?></a> </td>
                            <td >
                            <?php if($sess_accesslevel != 5){?>
                              <form action="scrp_add_register.php"  method="post">
                            <?Php } ?>
                            <input name="PostRegNum" type="text" value="<?php echo doCleanOutput($post_row["PostRegNum"]);?>" />
                            <input name="DID" type="hidden" value="<?php echo doCleanOutput($post_row["DID"]);?>" />
                            <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                            <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />
                            
                            <?php if($sess_accesslevel != 5){?>
                           		<input name="add_code" type="submit" value="เพิ่มรหัส" />
							 <?php }?>   
                            </form>  
                            
                                                  </td>
							<?php if($sess_accesslevel != 5){?>
                                <td>
                                        <div align="center"><a href="scrp_delete_doccom.php?id=<?php echo doCleanOutput($post_row["DID"]);?>&cid=<?php echo doCleanOutput($output_values["CID"]);?>" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" border="0" /></a> </div>
                                    
                                </td>
                            <?php }?>
                            
                            
                            
                          </tr>
                          <?php
						  	} //generate letter history?>
                      </table>                       </td>
                      </tr>
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      <!---           แจ้งอายัด -                  -              -->
                      
                      
                      
                      <tr>
                        <td>
                        <hr /><div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการแจ้งอายัด</div>
                        
                        <?php if($sess_accesslevel != 5){?>
                        <a href="org_list.php?mode=letters&search_id=<?php echo $this_id;?>&for_year=<?php echo $this_lawful_year;?>&type=hold">+ แจ้งอายัด</a>                         
                        <?php }?>
                        
                        </td>
                      </tr>
                       <tr>
                        <td>
                        
                            <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                                <tr bgcolor="#9C9A9C" align="center" >
                                  <td><div align="center"><span class="column_header">วันที่</span> </div></td>
                                    <td>
                                        <div align="center"><span class="column_header">ครั้งที่</span> </div></td>
                                    <td ><div align="center"><span class="column_header">หนังสือเลขที่</span> </div></td>
                                    <td ><div align="center"><span class="column_header">เลขที่ลงทะเบียน</span> </div></td>
                                    <?php if($sess_accesslevel != 5){?>
                                    <td >  <div align="center"><span class="column_header">ลบข้อมูล</span> </div></td>
                                    <?php }?>
                              </tr>
                                  
                                   <?php 
							
							
							
							//generate letter history
							$get_letter_sql = "select *
										from documentrequest a, docrequestcompany b
										where
											a.rid = b.rid
										and 
											CID = '$this_id'
										
										$conditions	
										
										and is_hold_letter = '1'
										
										order by RequestDate desc
										
										";
							
							//echo $get_letter_sql;
							
							$letter_result = mysql_query($get_letter_sql);
							
							while ($post_row = mysql_fetch_array($letter_result)) {
							?>
                        <tr bgcolor="#ffffff" align="center" >
                          <td> <?php echo formatDateThai($post_row["RequestDate"]);?></td>
                       	    <td>

                            	<?php echo doCleanOutput($post_row["RequestNum"]);?>                          </td>
                            <td ><a href="view_letter.php?id=<?php echo doCleanOutput($post_row["RID"]);?>&type=hold"><?php echo doCleanOutput($post_row["GovDocumentNo"]);?></a> </td>
                            <td >
                            <?php if($sess_accesslevel != 5){?>
                              <form action="scrp_add_register.php"  method="post">
                            <?Php } ?>
                            <input name="PostRegNum" type="text" value="<?php echo doCleanOutput($post_row["PostRegNum"]);?>" />
                            <input name="DID" type="hidden" value="<?php echo doCleanOutput($post_row["DID"]);?>" />
                            <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                            <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />
                            
                            <?php if($sess_accesslevel != 5){?>
                           		<input name="add_code" type="submit" value="เพิ่มรหัส" />
							 <?php }?>   
                            </form>  
                            
                                                  </td>
							<?php if($sess_accesslevel != 5){?>
                                <td>
                                        <div align="center"><a href="scrp_delete_doccom.php?id=<?php echo doCleanOutput($post_row["DID"]);?>&cid=<?php echo doCleanOutput($output_values["CID"]);?>" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" border="0" /></a> </div>
                                    
                                </td>
                            <?php }?>
                            
                            
                            
                          </tr>
                          <?php
						  	} //generate letter history?>
                                  
                                  
                                  
                          </table>
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      <tr>
                        <td><hr /><div style="font-weight: bold; padding:0 0 5px 0;">เอกสารจาก<?php echo $the_company_word;?></div></td>
                      </tr>
                      <tr>
                        <td><?php
                        
							//echo $this_year;
							//echo $this_lawful_year;
						
							//try to show last "document_requests" entry
							//print_r($_POST);
							
							if($_POST["ddl_year"]){
								$this_docr_year = doCleanInput($_POST["ddl_year"]);
								$doc_conditions = " and docr_year = '$this_docr_year'";
							}elseif($_GET["ddl_year"]){
								$this_docr_year = doCleanInput($_GET["ddl_year"]);
								$doc_conditions = " and docr_year = '$this_docr_year'";
							}elseif($_GET["year"]){
								$this_docr_year = doCleanInput($_GET["year"]);
								$doc_conditions = " and docr_year = '$this_docr_year'";
							}else{
								$this_docr_year = date('Y');
							}
							
							$sql = "select * 
								from 
									document_requests
								where 
									docr_org_id  = '$this_id'
									
									$doc_conditions
								
								order by docr_id desc
								limit 0,1";
							//echo $sql;
							$docr_row = getFirstRow($sql);
						
							if($docr_row["docr_status"] == 1){
								$stat_1_checked = 'checked="checked"';
								$stat_2_checked = '';
								$date_to_show = ($docr_row["docr_date"]);
							}else{
								$stat_1_checked = '';
								$stat_2_checked = 'checked="checked"';
								$date_to_show = date("Y-m-d");
							}
						
						?>
                        <table border="0">
                        <form action="?id=<?php echo $this_id;?>&focus=official" method="post">
                         <tr>
                            <td colspan="2">ข้อมูลประจำปี 
                              <?php
                              
							  	$dll_year_name = "docr_year";
							  	include "ddl_year_auto_submit_lawful.php";
							  
							  
							  //	echo $this_year;
								//echo $this_lawful_year;
							  
							  
							  ?></td>
                          </tr>
                        </form>
                        
                        <form method="post" action="scrp_update_org_doc_stat.php">
                          
                         
                          <tr>
                            <td><label>
                            <input type="radio" name="docr_status" id="docr_stat" value="1" <?php echo $stat_1_checked;?> />
                            </label>
                            ได้รับเอกสารครบแล้ว ณ วันที </td>
                            <td>
                            
                            
                             <?php 
							
							//echo $this_year;
							//echo $this_lawful_year;
							
							?>
                            
                            <?php
											   
							   $selector_name = "docr_date";
							   
							   $this_date_time = $docr_row["docr_date"];
							 
							   if($this_date_time != "0000-00-00"){
								   $this_selected_year = date("Y", strtotime($this_date_time));
								   $this_selected_month = date("m", strtotime($this_date_time));
								   $this_selected_day = date("d", strtotime($this_date_time));
							   }else{
								   $this_selected_year = 0;
								   $this_selected_month = 0;
								   $this_selected_day = 0;
							   }
							   
							   include ("date_selector.php");
							   
							   ?>            <?php 
							
							//echo $this_year;
							//echo $this_lawful_year;
							
							?>                 </td>
                          </tr>
                          <tr>
                            <td colspan="2"><div style="padding-left:25px;"><textarea name="docr_status_remark" cols="35" rows="3"><?php echo doCleanOutput($docr_row["docr_status_remark"]);?></textarea></div>
                            
                            
                            <?php 
							
							//echo $this_year;
							//echo $this_lawful_year;
							
							?>
                            
                            </td>
                          </tr>
                          <tr>
                            <td><input type="radio" name="docr_status" id="docr_stat2" value="0"  <?php echo $stat_2_checked;?>/> 
                              ได้รับเอกสารยังไม่ครบ เอกสารที่ยังขาดคือ </td>
                            <td><label></label></td>
                          </tr>
                          <tr>
                            <td colspan="2"><div style="padding-left:25px;"><textarea name="docr_desc" cols="35" rows="3"><?php echo doCleanOutput($docr_row["docr_desc"]);?></textarea></div></td>
                          </tr>
                          <tr>
                            <td colspan="2"><div align="center">
                              <input name="docr_year" type="hidden" value="<?php echo $this_docr_year;?>" />
                              
                              <?php if($sess_accesslevel != 5){?>
                                  <input type="submit" name="button2" id="button2" value="ปรับปรุงข้อมูล" 
                                  onclick = "return confirm('ต้องการปรับปรุงข้อมูลเอกสารจาก<?php echo $the_company_word;?>นี้?');"
                                  />
                              <?php }?>
                              <input name="docr_org_id" type="hidden" value="<?php echo $this_id; ?>" />
                            </div>
                            
                            
                            <?php 
							
							//echo $this_year;
							//echo $this_lawful_year;
							//
							?>
                            
                            
                            </td>
                          </tr>
                        </table></form></td>
                      </tr>
                      
                    </table>
                  
              		<?php } //end if to show this tab for non-company only?>
              
              
              
              	                       
                 
              
              
              
              
              
              
                  <table style=" padding:10px 0 0px 0; " id="lawful">
                    
                      <tr>
                        <td><div style="font-weight: bold; padding:0 0 5px 0;">การปฏิบัติตามกฎหมายของ<?php echo $the_company_word;?></div></td>
                      </tr>
                      <tr>
                        <td><?php
                        
							
							
							//echo $this_year;
							//echo $this_lawful_year;
							
							$lawful_row = getFirstRow("select * 
								from 
									lawfulness
								where 
									CID  = '$this_id'
									
									$conditions
									
								order by LID desc
								
								limit 0,1");
								
							
						
							$stat_1_checked = '';
							$stat_2_checked = '';
							$stat_3_checked = '';
							$stat_4_checked = '';
							
							$no_recipient_checked = '';
						
							if($lawful_row["LawfulStatus"] == "0"){
								$stat_2_checked = 'checked="checked"';//unlawful
							}elseif($lawful_row["LawfulStatus"] == "1"){
								$stat_1_checked = 'checked="checked"'; //lawful
							}elseif($lawful_row["LawfulStatus"] == "2"){
								$stat_4_checked = 'checked="checked"';//in progress
							}elseif($lawful_row["LawfulStatus"] == "3"){
								$stat_3_checked = 'checked="checked"';//no employee
							}else{
								//blank row

								//yoes 2012 nov 5 -> if lawful row is blank then -> default check on nothing
								//$stat_2_checked = 'checked="checked"';//unlawful
								
								$is_blank_lawful = 1;
							}
							
							if($lawful_row["NoRecipient"] == "1"){
								$no_recipient_checked = 'checked="checked"';
							}
							
							$lawful_fields = array(
								'LID'
								,'CID'
								,'lawfulStatus'
								,'Employees'
								
								,'Hire_status'
								,'Hire_NumofEmp'
								,'Hire_docfile'
								
								,'Conc_status'
								
								,'Conc1_status'
								,'Conc1_docfile'
								,'Conc2_status'
								,'Conc2_docfile'
								,'Conc3_status'
								,'Conc3_docfile'
								,'Conc4_status'
								,'Conc4_docfile'
								,'Conc5_status'
								,'Conc5_docfile'
								
								,'pay_status'
						
								,'cash_date'
								,'cash_amount'
								,'cash_docfile'
								
								,'check_bank'
								,'check_number'
								,'check_date'
								,'check_amount'
								,'check_docfile'
								
								,'note_number'
								,'note_date'
								,'note_amount'
								,'note_docfile'
								
								,'NoRecipient'
								,'NoRecipient_remark'
								,'Hire_NewEmp'
								
								,'lawful_order'
								
								
								);
								
							for($i = 0; $i < count($lawful_fields); $i++){
								//clean all inputs
								$lawful_values[$lawful_fields[$i]] .= doCleanOutput($lawful_row[$lawful_fields[$i]]);
							}
						
						?>
                       	  <form action="?id=<?php echo $this_id;?>&focus=lawful" method="post">
                            <table>
                            	<tr>
                                  <td colspan="2">ข้อมูลประจำปี
                                  <?php
                              		
									//print_r($_POST);
							  		include "ddl_year_auto_submit_lawful.php";
							  
							  	?>
                              	</td>
                                </tr>
                            </table>
                          </form>
                            <script language='javascript'>
							
								
								<!--
								function validateLawfulStat(frm) {
									
									lawful_value = getCheckedValue(frm.lawfulStatus);
									//alert(lawful_value);
									
									<?php if($sess_accesslevel !=4){?>
									if(lawful_value == 0){
										if(frm.Hire_status.checked || frm.pay_status.checked || frm.Conc_status.checked){
											alert("ข้อมูลผิดพลาด -> ไม่ปฏิบัติตามกฎหมาย แต่มีการ จ้างคนพิการเข้าทำงาน, ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ หรือ ให้สัมปทานฯ ");
											
											return false;
										}
									}
									
									if(lawful_value == 1){
										
										if(!frm.Hire_status.checked && !frm.pay_status.checked  && !frm.Conc_status.checked){
											alert("* ข้อมูลผิดพลาด -> ปฏิบัติตามกฎหมาย แต่ไม่มีการ  จ้างคนพิการเข้าทำงาน,  ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ หรือ ให้สัมปทานฯ ");
											return false;
										}
									}
									<?php } ?>
									
									
									if(frm.Hire_status.checked && frm.Hire_NumofEmp.value < 1){
											alert("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n ข้อมูลผิดพลาด -> มีการจ้างงานคนพิการ แต่ไม่มีจำนวนคนพิการ\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
											return false;
									}
									
									//hire
									if(!frm.Hire_status.checked && frm.Hire_NumofEmp.value != 0){
											alert("!!!! มีจำนวนคนพิการ แต่ไม่ได้ check 'จ้างคนพิการเข้าทำงาน '!!!!");
											return false;
									}
									
									
									//hire
									if(frm.pay_status.checked && frm.have_receipt.value == 0){
											alert("!!!! มีการ check 'ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ' แต่ไม่มีข้อมูลใบเสร็จ!!!!");
											return false;
									}
									
									if(!frm.pay_status.checked && frm.have_receipt.value == 1){
											alert("!!!! มีข้อมูลใบเสร็จ แต่ไม่มีการ check 'ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ' !!!!");
											return false;
									}
									
									//conc
									if((frm.Conc1_status.checked || frm.Conc2_status.checked || frm.Conc3_status.checked || frm.Conc4_status.checked || frm.Conc5_status.checked) && !frm.Conc_status.checked){
											alert("!!!! มีรายละเอียดการให้สัมปทาน แต่ไม่ check ช่อง 'ให้สัมปทานฯ'!!!!");
											return false;
									}
									if((!frm.Conc1_status.checked && !frm.Conc2_status.checked && !frm.Conc3_status.checked && !frm.Conc4_status.checked && !frm.Conc5_status.checked) && frm.Conc_status.checked){
											alert("!!!! มีการ check ช่อง 'ให้สัมปทานฯ' แต่ไม่มีรายละเอียดการให้สัมปทาน!!!!");
											return false;
									}
									
									
									return true;
																								
								
								}
								
								function getCheckedValue(radioObj) {
									if(!radioObj)
										return "";
									var radioLength = radioObj.length;
									if(radioLength == undefined)
										if(radioObj.checked)
											return radioObj.value;
										else
											return "";
									for(var i = 0; i < radioLength; i++) {
										if(radioObj[i].checked) {
											return radioObj[i].value;
										}
									}
									return "";
								}

								-->
							
							</script>
                            <form id="lawful_form" 
                            
                            <?php if($sess_accesslevel == 4){
									//company do what company do
									?>
                            	action="scrp_update_org_lawful_stat_company.php"
                            <?php }else{
								//else, normal case
								?>
                            	action="scrp_update_org_lawful_stat.php"
                            <?php }?>
                            
                            
                            method="post" enctype="multipart/form-data" 
                            
                            <?php if($is_2013 != 1){?>
                            onSubmit="return validateLawfulStat(this);"
                            <?php }?>
                            
                            >
							
							
            
									
									
									
                   <table border="0">
								
								 <tr>
									  <td colspan="2">
									  
									  
											<table border="0" width="100%">
                                
													<?php if($sess_accesslevel !=4){?>
													
													<tr>
													  <td>
													  
													  
													   <?php if($is_2013){?>

															<?php if($lawful_row["LawfulStatus"] == 3){?>
														
															<img src="decors/checked.gif" />
														
															<?php }else{?>
															
																<input name="" type="radio" value="" disabled="disabled" />
															
															<?php }?>
														
														
													   <?php }else{?>
														  <input type="radio" name="lawfulStatus" id="" value="3"  <?php echo $stat_3_checked;?>/>                      
														
														<?php }?>
													  
														ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?>
                                                       </td>
                                                       
                                                       <td colspan="2">
                                                       
                                                       	
                                                        	<table align="right">
                                                            	<tr>
                                                                	<td   bgcolor="#fcfcfc" >ลำดับ:</td>
													  <td  bgcolor="#fcfcfc">
                                                      
                                                      	<input name="lawful_order" type="text" size="5" style="text-align: right;" value="<?php echo doCleanOutput($lawful_values["lawful_order"])?>" />
                                                      
													  </td>
                                                                </tr>
                                                            </table>
                                                       
                                                       </td>
                                                       
												  </tr>
                                                    
                                                    
													<tr>
													  <td>
													  
													  <?php //echo "is_2013: ". $is_2013;?>
													  
													  <?php if($is_2013){?>
													  
														<input name="lawfulStatus" type="hidden" value="<?php echo $lawful_row["LawfulStatus"];?>" />
													  
														<?php if($lawful_row["LawfulStatus"] == 0){?>
														
															<img src="decors/checked.gif" />
															
														<?php }else{?>
														
															<input name="" type="radio" value="" disabled="disabled" />
														
														<?php }?>
														
														
													  <?php }else{?>
														  <input type="radio" name="lawfulStatus" id="" value="0"  <?php echo $stat_2_checked;?> />                                  
													  
													  <?php }?>
													  
													  
													  &nbsp;
													  
													  
														ไม่ปฏิบัติตามกฎหมาย
                                                       </td>
                                                        
                                                        
													  <td rowspan="4" valign="top" bgcolor="#fcfcfc" >หมายเหตุ:</td>
													  <td rowspan="4" bgcolor="#fcfcfc"><textarea name="NoRecipient_remark" rows="5" style="width:100%;"><?php echo doCleanOutput($lawful_values["NoRecipient_remark"])?></textarea>
													  </td>
                                                      
                                                      
													</tr>
													
													
													<tr>
													  <td style="padding-left: 20px;">
														<input name="NoRecipient" type="checkbox" value="1" <?php echo $no_recipient_checked;?>/> ไม่มีคนรับเอกสาร
														
													  </td>
													</tr>
												   
												   
													<tr>
													  <td>
													  
													  
													  
													   <?php if($is_2013){?>

															<?php if($lawful_row["LawfulStatus"] == 2){?>
														
															<img src="decors/checked.gif" />
														
															<?php }else{?>
															
																<input name="" type="radio" value="" disabled="disabled" />
															
															<?php }?>
														
													   <?php }else{?>
														   <input type="radio" name="lawfulStatus" id="" value="2"  <?php echo $stat_4_checked;?>/>           
														
														<?php }?>
													 
													  
													  
					ปฏิบัติตามกฏหมายแต่ไม่ครบตามอัตราส่วน</td>
													</tr>
													
													
													
													 <?php } ?>
													
									</table>
									  
									  
									  
									  </td>
								  
								  </tr>
								
                                <tr>
                                  <td>
                                   <?php if($sess_accesslevel !=4){?>
                                  	<label>
                                   
                                   
                                   <?php if($is_2013){?>

										<?php if($lawful_row["LawfulStatus"] == 1){?>
                                    
                                        <img src="decors/checked.gif" />
                                    
                                        <?php }else{?>
                                        
                                            <input name="" type="radio" value="" disabled="disabled" />
                                        
                                        <?php }?>
                                    
                                    
                                   <?php }else{?>
                                       <input type="radio" name="lawfulStatus" id="" value="1" <?php echo $stat_1_checked;?> />                             
                                    
                                    <?php }?>
                                   
                                   
                                    
                                    
					
                                    </label>
                                    ปฏิบัติตามกฎหมาย<br />
                                    <?php } ?>
                                    <div class="style86" style="padding: 10px 0 10px 0;">
                                    
                                    <table >
                                    	
                                        
                                    	<tr>
                                    	  <td colspan="3">
                                          
                                          	<table border="0" style="margin: 5px 0 15px 30px;">
                                            
                                                             <!----- starts lawful employees and Ratio --->
                                                        <!----- starts lawful employees and Ratio --->
                                                        <!----- starts lawful employees and Ratio --->
                                                        <!----- starts lawful employees and Ratio --->
                                                        
                                                        
                                                        
                                                        
                                                      
                                                        <tr>
                                                          <td bgcolor="#fcfcfc">จำนวน<?php echo $the_employees_word;?>: </td>
                                                          <td>
                                                          
                                                          <?php 
														  
														  	//what employees to show
															
															//if this is a company, try get the inputted employees
															if($sess_accesslevel == 4){
																
																//new for company only -> create LID record if not existed
																$lid_existed = getFirstItem("select 
																								count(*) 
																							from 
																								lawfulness_company 
																							where 
																								LID = '" . $lawful_values["LID"] . "'");
																								
																
																
																//echo "existed: ".$lid_existed;
																
																//no compnay -> create it		
																if(!$lid_existed){
																	
																	$sql = "insert into
																					lawfulness_company(
																						LID
																						,CID
																						,Year
																						, Employees
																																									
																					)
																					
																					select 
																						LID
																						,CID
																						,Year	
																						, Employees
																						
																					from
																						lawfulness
																					where
																						LID = '" . $lawful_values["LID"] . "'";
																						
																	//echo $sql;
																	
																	mysql_query($sql)or die(mysql_error());
																	
																}
																
																
																//override it lawful value
																$company_employees = getFirstItem("select 
																						Employees 
																					from 
																						lawfulness_company 
																					where 
																						LID = '" . $lawful_values["LID"] . "'");
																				
																//if have value then override it		
																if($company_employees){
																	$lawful_values["Employees"] = $company_employees;
																}
																
																
																
																//20140220
																//see if this company got submmited or not
																 $submitted_company_lawful = getFirstItem("
																 										select 
																											lawful_submitted
																										from
																											lawfulness_company
																										where
																											LID = '" . $lawful_values["LID"] . "'
																										");
																
																
															}
                                                          
                                                          
                                                            if($lawful_values["Employees"]){
                                                                //have lawful value, use lawful value
                                                                $employee_to_use = $lawful_values["Employees"];
                                                                
                                                            }else{
                                                            
                                                                //didn't have lawful value, use ORG's value
                                                                if($sum_employees){
                                                                    //if have branch, use employees from all branch
                                                                    $employee_to_use = $sum_employees;
                                                                    
                                                                }else{
                                                                
                                                                    //else, just use ORG's employees
                                                                    $employee_to_use = $output_values["Employees"];
                                                                
                                                                }
                                                            }
                                                            
                                                            ?>
                                                          
                                                          <strong><?php echo formatEmployee($employee_to_use);?></strong>
                                                          
                                                           คน |
                                                           
                                                           
                                                           
                                                           <input name="Employees" id="Employees" style="width:50px" type="hidden" value="<?php echo ($employee_to_use);?>"  />
                                                           
                                                           
                                                           <?php if($sess_accesslevel == 5){ //exec can do nothing?>
                                                  
                                                          <?php }elseif($sess_accesslevel != 4 && !$is_blank_lawful){ //can only do this if already have lawful row?>
                                                                 
                                                                 
                                                                 <a href="#" onclick="fireMyPopup('employees_popup',500,250); return false;">ปรับปรุงข้อมูล</a>
                                                           
                                                            <?php }elseif($sess_accesslevel == 4 && ($submitted_company_lawful == 1 || $submitted_company_lawful == 2)){ //can only do this if already have lawful row?>
                                                                 
                                                                                                                                  
                                                                 <!--<a href="#" onclick="fireMyPopup('employees_popup',500,250); return false;">ปรับปรุงข้อมูล</a>-->
                                                           
                                                           
                                                          <?php }elseif(!$is_blank_lawful){ ?>
                                                                   <a href="#" onclick="fireMyPopup('employees_popup',500,250); return false;">ปรับปรุงข้อมูล</a>
                                                           
                                                           <?Php }?>
                                                           
                                                          </td>
                                                        </tr>
                                                        
                                                        <tr>
                                                          <td bgcolor="#fcfcfc" style="padding-right:20px;">อัตราส่วน<?php echo $the_employees_word;?>ต่อคนพิการ: </td>
                                                          <td><?php 
                                                          
                                                            //what ratio to use?
                                                            $ratio_to_use = default_value(getFirstItem("select var_value 
                                                                                from vars where var_name = 'ratio_$this_lawful_year'"),100);
                                                            
                                                            //$half_ratio_to_use = $ratio_to_use/2;
                                                            
                                                            echo ($ratio_to_use);
                                                            
                                                          
                                                          ?>:1 = <strong id="employee_ratio"><?php 
                                                            //if employee > 200
                                                            
                                                            
                                                            
                                                            $final_employee = getEmployeeRatio($employee_to_use,$ratio_to_use);
                                                            
                                                            echo formatEmployee($final_employee);
                                                            
                                                            ?></strong> คน</td>
                                                        </tr>
                                                        
                                                        
                                                        
                                                        <script>
                                                            function reCalculateRatio(){
                                                            
                                                                
                                                                employee_to_use = document.getElementById("Employees").value; 
                                                                
                                                                employee_to_use = employee_to_use.replace(/,/g,"");
                                                                //
                                                                //alert(employee_to_use);
                                                                
                                                                if(employee_to_use > 0){
                                                                    if(employee_to_use > <?php echo $ratio_to_use;?> || employee_to_use == <?php echo $ratio_to_use;?>){
                                                                        left_over = employee_to_use%<?php echo $ratio_to_use;?>;
                                                                        if(left_over <= <?php echo $half_ratio_to_use;?>){
                                                                            ratio_to_use = Math.floor(employee_to_use/<?php echo $ratio_to_use;?>);
                                                                        }else{
                                                                            ratio_to_use = Math.ceil(employee_to_use/<?php echo $ratio_to_use;?>);
                                                                        }
                                                                    }else{
                                                                        ratio_to_use = 0;
                                                                    }
                                                                }
                                                                
                                                                document.getElementById("employee_ratio").innerHTML = ratio_to_use;
                                                            }
                                                        </script>
                                                        
                                                        
                                                        
                                                        
                                                        
                                                        
                                                        <!----- Ends lawful employees and Ratio --->
                                                        <!----- Ends lawful employees and Ratio --->
                                                        <!----- Ends lawful employees and Ratio --->
                                                        <!----- Ends lawful employees and Ratio --->
                                            
                                            
                                            
                                            </table>
                                          
                                          
                                          
                                          </td>
                                   	  </tr>
                                      
                                      
                                    	<tr>
                                        	<td>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            
                                            <?php if($sess_accesslevel == 4){?>
                                            
                                            
                                            <?php }elseif($is_2013){?>

												<input name="Hire_status" type="hidden" id="Hire_status" value="<?php echo $lawful_row["Hire_status"];?>" />
												
												<?php if($lawful_row["Hire_status"] == 1){?>
                                            
                                                <img src="decors/checked.gif" />
                                                
                                            
                                                <?php }else{?>
                                                
                                                    <input name="" type="checkbox" value="" disabled="disabled" />
                                                
                                                <?php }?>
                                            
                                            
                                            <?php }else{?>
                                               <input name="Hire_status" type="checkbox" id="Hire_status" value="1" <?php echoChecked($lawful_values["Hire_status"])?> />                                                                      
                                            
                                            <?php }?>
                                            
                                    		
                                            
                                            </td>
                                            <td><span style="font-weight: bold;color:#006600;">
                                             <?php //if($sess_accesslevel ==4){
											 if(1==1){?>
                                             	มาตรา 33 จ้างคนพิการเข้าทำงาน
                                             <?php }else{ ?>
                                            	ปฏิบัติตามมาตรา 33
                                             <?php } ?>
                                            </span></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    	<tr>
                                    	  <td>&nbsp;</td>
                                    	  <td colspan="2" style="padding-left: 30px;">
                                          
                                          <table border="0">
                                          
                                          
                                          
                                          
                                          	
                                            
                                            
                                            <tr>
                                              <td>จำนวนคนพิการที่ทำงานในปัจจุบัน</td>
                                              <td>
                                              
                                              <?php 
											  
											  	//now we count number of hired employeed directly from LE list
											  											  
												//for company -> get this from company table
												if($sess_accesslevel == 4){
													
													$hire_numofemp = getFirstItem("
														SELECT 
															count(*)
														FROM 
															lawful_employees_company
														where
															le_cid = '$this_id'
															and le_year = '$this_lawful_year'");/**/
													
												}else{
													
													$hire_numofemp = getFirstItem("
														SELECT 
															count(*)
														FROM 
															lawful_employees
														where
															le_cid = '$this_id'
															and le_year = '$this_lawful_year'");/**/
													
													
												}
												
												
												if($hire_numofemp == 0 && $sess_accesslevel != 4){
												
													//no "real" value, use thos that is in lawfulness instead
													$hire_numofemp = $lawful_values["Hire_NumofEmp"];
													
												}
												
											   ?>
                                              
                                               <strong><?php echo default_value($hire_numofemp,0);?></strong>
                                              
                                              
                                              <input name="Hire_NumofEmp" type="hidden" id="Hire_NumofEmp" size="10" value="<?php echo formatEmployee(default_value($hire_numofemp,"0"));?>" onChange=" addEmployeeCommas('Hire_NumofEmp');"/> 
                                              
                                              
                                              คน || 
                                              
                                              
                                              <?php if(!$is_blank_lawful && $sess_accesslevel != 4 ){?>
                                              
                                              <a href="" onClick="fireMyPopup('my_popup',1020,160); return false;">ข้อมูลคนพิการที่ได้รับเข้าทำงาน</a>
                                              
                                              <?php }?>
                                              
                                              
                                              <?php if($sess_accesslevel == 4 && !$submitted_company_lawful){?>
                                              
                                              <a href="" onClick="fireMyPopup('my_popup',1020,160); return false;">ข้อมูลคนพิการที่ได้รับเข้าทำงาน</a>
                                              
                                              <?php }?>
                                              
                                             
                                              
                                              </td>
                                            </tr>
                                            
                                            
                                            
                                           
                                            
                                            
                                            
                                            <tr>
                                              <td>ผู้พิการใช้สิทธิมาตรา 35</td>
                                              <td>
                                              
                                              
                                              <?php
											  	
													
													//curator user are person OR disabled person who is the "top" level
													
													//$curator_table_name is from file "scrp_add_curator"
													
													
													$curator_user = getFirstItem("select 
															count(*) 
														from 
															$curator_table_name 
															
														where 
														
															curator_lid = '".$lawful_values["LID"]."'
																
																
															and 
															
															curator_parent = 0
															
															
															");	
													
													
													
											  ?>
                                              <strong><?php echo $curator_user;?></strong>
                                              
                                               
                                              คน
                                            </tr>
                                            
                                            
                                             <tr>
                                              <td>จำนวนคนพิการที่รับเพิ่ม</td>
                                              <td>
                                              
                                              <b>
                                              <?php 
											  
											  	$extra_emp = $final_employee - $hire_numofemp - $curator_user;
												
												if($extra_emp < 0){
													$extra_emp = 0;
												}
												
											    echo formatEmployee(default_value($extra_emp,"0"));
											  ?>
                                              </b>
                                              
                                              
                                              <input name="Hire_NewEmp" type="hidden" id="Hire_NewEmp" size="10" value="<?php echo $extra_emp;?>" onChange=" addEmployeeCommas('Hire_NewEmp');" /> 
                                              คน
                                            </tr>
                                            
                                            
                                            
                                            
                                            <?php if($sess_accesslevel != 4){ //company won't see this?>
                                                
                                                <tr>
                                                  <td>เอกสารประกอบ</td>
                                                  <td><span class="style86" style="padding: 10px 0 10px 0;">
                                                   
                                                    <div style="width:400px; padding-bottom:5px;">
                                                    <?php 
                                                        
                                                        //do $this_id swap thing because doc link use LID, but consume $this_id
                                                        //but $this_id on this page is CID and not LID...
                                                        $this_id_temp = $this_id;
                                                        $this_id = $lawful_values["LID"];
                                                        
                                                        $file_type = "Hire_docfile";
                                                    
                                                        include "doc_file_links.php";
                                                        
                                                        $this_id = $this_id_temp;
                                                        
                                                    ?>
                                                    </div>
                                                    <input type="file" name="Hire_docfile" id="Hire_docfile" />
                                                   </span></td>
                                                </tr>
                                            
                                            <?php }?>
                                            
                                            

                                          </table>
                                          
                                          
                                          </td>
                                   	  </tr>
                                    	<tr>
                                    	  <td colspan="3">&nbsp;</td>
                                   	  </tr>
                                      
                                       <?php if($submitted_company_lawful){?>
                                            <tr>
                                            	<td>
                                                </td>
                                            	<td colspan="2">
                                                
                                              
                                              
                                              <!------------ DETAILS TABLE -------------->
                                             <table>
                                                 <tr>
                                                    <td bgcolor="#efefef" colspan="9">
                                                    <strong>ข้อมูลคนพิการที่ได้รับเข้าทำงาน</strong>
                                                    </td>
                                                </tr>
                                                    
                                                     <?php
                                
                                    
                                                    
                                                        
                                                        $get_org_sql = "SELECT *
                                                                        FROM 
                                                                        
                                                                        lawful_employees_company
                                                                        
                                                                        where
                                                                            le_cid = '$this_id'
                                                                            and le_year = '$this_lawful_year'
                                                                        order by le_id asc
                                                                        ";
                                                        
                                                    
                                                    
                                                    //echo $get_org_sql;
                                                    $org_result = mysql_query($get_org_sql);
                                                    $total_records = 1;
                                                    while ($post_row = mysql_fetch_array($org_result)) {
                                                
                                                        if($total_records == 1){
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
                                                         
                                                        </tr>
                                                        
                                                        <?php
                                                        
                                                        }											
                                                    
                                                    ?>     
                                                <tr>
                                                  <td valign="top"><div align="center"><?php echo $total_records;?></div></td>
                                                  <td valign="top"><?php echo doCleanOutput($post_row["le_name"]);?></td>
                                                  <td valign="top"><?php echo formatGender($post_row["le_gender"]);?></td>
                                                  <td valign="top"><?php echo doCleanOutput($post_row["le_age"]);?></td>
                                                  <td valign="top">
                                                  <?php echo doCleanOutput($post_row["le_code"]);?>
                                                  
                                                     
                                                  
                                                  </td>
                                                  <td valign="top"><?php echo doCleanOutput($post_row["le_disable_desc"]);?></td>
                                                  <td valign="top"><?php echo formatDateThai($post_row["le_start_date"],0);?></td>
                                                  
                                                  <td valign="top"><div align="right">
                                                  
                                                  <?php echo formatNumber($post_row["le_wage"]);?>
                                                  
                                                  
                                                  <?php echo getWageUnit($post_row["le_wage_unit"]);?>
                                                  
                                                  </div></td>
                                                  
                                                  <td valign="top"><?php echo doCleanOutput($post_row["le_position"]);?></td>
                                                 
                                                
                                                  
                                                  
                                                </tr>
                                                <?php 
                                                    $total_records++;
                                                    
                                                    //END LOOP TO CREATE LAWFUL EMPLOYEES
                                                     
                                                    }?>
                                                
                                                
                                             </table>
                                
                                
                                
                                
                                </td>
                            </tr>    
                            
                            
                            
                            <!----- END ROW FOR lawful_employees----->
                                              
                                              <?php }?>
                                      
                                      
                                      
                                      
                                      
                                    </table>

                                    <table 
									id="rule_34_table" border="0"
                                    
                                    <?php if($sess_accesslevel == 4){ //wont show this for company?>
                                    	style="display:none;"
                                    <?php }?>
                                    
                                    >
									
									
                                    	
                                    
                                      <tr>
                                        <td>
                                        
                                        <div align="right" style="margin-left: 30px;">
                                        
                                        <?php if($is_2013){?>

                                            <input name="pay_status" type="hidden" id="pay_status" value="<?php echo $lawful_row["pay_status"];?>" />
                                            
                                            <?php if($lawful_row["pay_status"] == 1){?>
                                        
                                            <img src="decors/checked.gif" />
                                            
                                        
                                            <?php }else{?>
                                            
                                                <input name="" type="checkbox" value="" disabled="disabled" />
                                            
                                            <?php }?>
                                        
                                        
                                        <?php }else{?>
                                           <input name="pay_status" type="checkbox" id="pay_status" value="1" <?php echoChecked($lawful_values["pay_status"])?>/>                               
                                        
                                        <?php }?>
                                        
                                        </div>
                                        
                                        </td>
                                        <td><span style="font-weight: bold; color:#006600;">
                                       <?php //if($sess_accesslevel ==4){
											 if(1==1){?>
                                             	มาตรา 34 ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ
                                             <?php }else{ ?>
                                            	ปฏิบัติตามมาตรา 34
                                             <?php } ?>
                                        </span></td>
                                        <td>&nbsp;</td>
                                      </tr>

                                      <tr >
                                        <td>&nbsp;</td>
                                        <td colspan="2" style="padding-left: 30px;" >
                                        
                                        
                                        
                                        <table border="0">
                                            <tr>
                                              <td valign="top">
                                                
                                                
                                                    <!-- start payment table -->
                                                    <!-- start payment table --> 
                                                    <!-- start payment table -->
                                                    <!-- start payment table -->
                                                    <!-- start payment table -->
                                                    <!-- start payment table -->
                                                    <!-- start payment table -->
                                                    
                                                    
                                                    
                                                    <table border="0" >
                                                        
                                                        
                                                         <?php
                                                                    
                                                                //$extra_employee = $lawful_values["Hire_NewEmp"];
                                                                $extra_employee = $extra_emp;													
                                                                //if($extra_employee == 0){
                                                                //	$extra_employee = $final_employee - $lawful_values["Hire_NumofEmp"];
                                                                    
                                                                //}
                                                                
                                                                $wage_rate = default_value(getFirstItem("select var_value from vars where var_name = 'wage_".$this_lawful_year."'"),159);
                                                                //$year_date = date("z", mktime(0,0,0,12,31,$this_lawful_year))+1;
                                                                $year_date = 365;
                                                                
                                                                
            
                                                                //20140224 - not used this
                                                                //$interest_date = dateDiffTs(strtotime(date("$this_lawful_year-01-31")), strtotime(date("Y-m-d")));
                                                                
                                                                $start_money = $extra_employee*$wage_rate*$year_date;
                                                                
                                                            
                                                                /*$the_sql = "select sum(receipt.Amount) 
                                                                    from payment, receipt , lawfulness
                                                                    where 
                                                                    receipt.RID = payment.RID
                                                                    and
                                                                    lawfulness.LID = payment.LID
                                                                    and
                                                                    ReceiptYear = '$this_lawful_year'
                                                                    and
                                                                    lawfulness.CID = '".$this_id."'
                                                                    and
                                                                    is_payback != 1
                                                                    ";*/
                                                                    
                                                                //$paid_money = getFirstItem("$the_sql");
                                                                //echo $the_sql;
                                                                
                                                                
                                                                $the_sql = "select sum(receipt.Amount) 
                                                                    from payment, receipt , lawfulness
                                                                    where 
                                                                    receipt.RID = payment.RID
                                                                    and
                                                                    lawfulness.LID = payment.LID
                                                                    and
                                                                    ReceiptYear = '$this_lawful_year'
                                                                    and
                                                                    lawfulness.CID = '".$this_id."'
                                                                    and
                                                                    is_payback = 1
                                                                    ";
                                                                    
                                                                $payback_money = getFirstItem("$the_sql");
                                                                
                                                                //------
                                                                
                                                                //echo "start money: $start_money<br>" ;
                                                                //echo "paid_money: $paid_money<br>" ;
                                                                
                                                                $owned_money = $start_money - $paid_money ;//+$payback_money
                                                                
                                                                
                                                                
                                                                if($owned_money < 0){
                                                                    $owned_money = 0;
                                                                }
                                                            
                                                                
                                                            
                                                            ?>
                                                        
                                                     
                                                      <tr>
                                                        <td>
                                                        
                                                          <a name="the_payment_details" id="the_payment_details"></a>
                                                          
                                                          
                                                        <hr />
                                                       <strong> ข้อมูลการส่งเงินเข้ากองทุน</strong>
                                                       
                                                       <?php if($sess_accesslevel!=4 && $sess_accesslevel!=5 && !$is_blank_lawful){?>
                                                       <a href="org_list.php?search_id=<?php echo $this_id?>&mode=payment&for_year=<?php echo $this_lawful_year;?>" style="font-weight: bold;">+ เพิ่มข้อมูลการส่งเงินเข้ากองทุน</a>
                                                       <?php }?>
                                                       
                                                       <br />
                                                        <a href="#" onClick="togglePaymentDetails(); return false;">- แสดงข้อมูลการจ่ายเงิน</a>
                                                        
                                                        <script>
                                                        
                                                            function togglePaymentDetails(){
                                                            
                                                            
                                                                if(document.getElementById('payment_details').style.display == 'none'){
                                                                    document.getElementById('payment_details').style.display = '';																
                                                                }else{
                                                                    document.getElementById('payment_details').style.display = 'none';
                                                                }
                                                            
                                                            }
                                                        
                                                            
                                                        
                                                        </script>
                                                       
                                                       
                                                       <?php echo "<div id='payment_details' >";?>
                                                       
                                                       
                                                        <?php 
                                                            //generate reciept info
                                                            $the_sql = "select * from payment, receipt , lawfulness
                                                                        where 
                                                                        receipt.RID = payment.RID
                                                                        and
                                                                        lawfulness.LID = payment.LID
                                                                        and
                                                                        ReceiptYear = '$this_lawful_year'
                                                                        and
                                                                        lawfulness.CID = '".$this_id."' 
                                                                        
                                                                        and
                                                                        is_payback != 1
                                                                        order by ReceiptDate asc";
                                                            
                                                            //echo $the_sql;
                                                            $the_result = mysql_query($the_sql);
                                                            
                                                            $have_receipt = 0;
                                                            while($result_row = mysql_fetch_array($the_result)){
                                                            
                                                                $have_receipt = 1;
                                                                
                                                                //echo "select * from receipt where RID = '".$result_row["RID"]."'";										
                                                                $receipt_row = getFirstRow("select * from receipt where RID = '".$result_row["RID"]."'");
                                                            
                                                            ?>
                                                            
                                                                    
                                                                    
                                                                     <div style="padding:5px">
                                                                     <?php if($sess_accesslevel != 4){?>
                                                                        ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <a href="view_payment.php?id=<?php echo $result_row["RID"]?>"><?php echo $receipt_row["ReceiptNo"]?></a> 
                                                                        <?php }elseif($sess_accesslevel == 4 && strlen($receipt_row["BookReceiptNo"]) > 0){?>
                                                                        ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <?php echo $receipt_row["ReceiptNo"]?> 
                                                                        <?php //}elseif($sess_accesslevel == 4){
                                                                            }elseif(1 == 0){																	
                                                                        ?>
                                                                         <a href="scrp_delete_receipt.php?id=<?php echo doCleanOutput($result_row["RID"]);?>"  onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');" title="ลบข้อมูลการจ่ายเงินออกจากระบบ"><img src="decors/trashcan_icon.jpg" border="0" /></a>
                                                                        <?php }?>
                                                                        
                                                                        วันที่จ่าย <?php echo formatDateThai($receipt_row["ReceiptDate"])?> จำนวนเงิน <?php echo formatNumber($receipt_row["Amount"])?> บาท จ่ายโดย <?php echo formatPaymentName($receipt_row["PaymentMethod"]);?> <?php
                                                                        
                                                                        $paid_for = getFirstItem("select count(*) from payment where payment.rid = '".$result_row["RID"]."'");
                                                                        if($paid_for > 1){
                                                                            echo "<span style='color:green'>จ่ายให้ $paid_for แห่ง</span>";
                                                                        }
                                                                        ?>
                                                                        
                                                                        
                                                                        
                                                                        <br />
                                                                        เงินต้น ณ วันที่จ่าย
                                                                        
                                                                        <?php
                                                                        
                                                                        //echo "owned money = $owned_money<br>";
                                                                        //echo "paid_from_last_bill = $paid_from_last_bill<br>";
                                                                        
                                                                        $owned_money = $owned_money - $paid_from_last_bill;//+ $receipt_row["Amount"]
                                                                        echo formatNumber($owned_money);
                                                                        
                                                                        ?> บาท
                                                                        
                                                                        
                                                                        <br />
                                                                        
                                                                        
                                                                        
                                                                       
                                                                        
                                                                        <?php
                                                                        
                                                                            $this_paid_amount = $receipt_row["Amount"];											
                                                                                                            
                                                                                                            
                                                                            
                                                                            if(!$last_payment_date){
                                                                                $last_payment_date = "$this_lawful_year-01-31 00:00:00";
                                                                            }
                                                                                                    
                                                                            //echo "$this_lawful_year-02-01 00:00:00";		
                                                                            
                                                                            
                                                                            //if last payment date is less than FEB 01 then detaulit it to FEB 01
                                                                            if(strtotime(date($last_payment_date)) 
                                                                                < 
                                                                                strtotime(date("$this_lawful_year-01-31"))){
                                                                            
                                                                                $last_payment_date = "$this_lawful_year-01-31 00:00:00";
                                                                            
                                                                            }
                                                                            
                                                                            
                                                                                
                                                                            
                                                                            //echo "last_payment_date: $last_payment_date <br>";												
                                                                            $interest_date = getInterestDate($last_payment_date, $this_lawful_year, $receipt_row["ReceiptDate"]);
                                                                            //echo "----".$interest_date;
                
                                                                            $last_payment_date_to_show = $last_payment_date;
                                                                            //echo "last_payment_date_to_show: $last_payment_date_to_show <br>";
                                                                            
                                                                            
                                                                            //update last payment date to use it for next record
                                                                            $last_payment_date = $receipt_row["ReceiptDate"];
                                                                            //echo "last_payment_date: $last_payment_date <br>";		
                                                                            
                                                                            
                                                                            
                                                                            
                                                                                                                                        
                                                                            //$interest_date = getInterestDate("2012-07-13 00:00:00", $this_lawful_year, $receipt_row["ReceiptDate"]);
                                                                            
                                                                            //echo "<br>2012-07-13 00:00:00" . " / ". $this_lawful_year . " / ". $receipt_row["ReceiptDate"]."<br>";
                                                                            
                                                                            //echo "interst date: $interest_date<br>";
                                                                            //echo "owned_money: $owned_money<br>";
                                                                            //echo "year_date: $year_date<br>";
                                                                            
                                                                            if($this_lawful_year >= 2012){ //only show interests when 2012+
                                                                                $interest_money = doGetInterests($interest_date,$owned_money,$year_date);
                                                                            }else{
                                                                                $interest_money = 0;
                                                                            }
                                                                            
                                                                            
                                                                            
                                                                            
                                                                            //echo "<br>".$interest_date . " " . $owned_money . " " .$year_date."<br>";
                                                                            //$have_pending_interest = 0;
                                                                            if($total_pending_interest > 0){
                                                                            
                                                                                //if have pending interests, add it here
                                                                                $interest_money += $total_pending_interest;
                                                                            
                                                                            }
                                                                            
                                                                            
                                                                            
                                                                            if($this_paid_amount < $interest_money){
                                                                                $have_pending_interest = 1;
                                                                                $interest_money_to_show = $this_paid_amount;
                                                                            }else{
                                                                                $interest_money_to_show = $interest_money;
                                                                            }
                                                                            
                                                                            
                                                                            
                                                                            
                                                                            
                                                                        ?>
                                                                        
                                                                        
                                                                        <?php 
                                                                            //if($last_payment_date_to_show != "$this_lawful_year-01-31 00:00:00"){
                                                                            if($is_pay_detail_first_row > 0){
                                                                            ?>
                                                                            
                                                                            วันที่จ่ายล่าสุด ของใบเสร็จนี้ <?php echo formatDateThai($last_payment_date_to_show);?><br />
                                                                        
                                                                        <?php
                                                                            }
                                                                            $is_pay_detail_first_row++;
                                                                        ?>
                                                                        
                                                                        
                                                                        
                                                                        <?php if($this_lawful_year >= 2012){ //only show interests when 2012+?>
                                                                                        
                                                                                        
                                                                                        ดอกเบี้ย ณ วันที่จ่าย: <?php echo $interest_date;?> วัน x 7.5/100/<?php echo $year_date?> x <?php echo formatNumber($owned_money);?> = <?php  
                                                                                        
                                                                                        //echo $interest_money. "-" . $total_pending_interest ."-"; 
                                                                                        
                                                                                        echo formatNumber($interest_money - $total_pending_interest);?> บาท
                                                                                        
                                                                                        
                                                                                       
                                                                                        
                                                                                        
                                                                                        <?php 
                                                                                            if($total_pending_interest > 0){
                                                                                            
                                                                                            
                                                                                            ?>
                                                                                            
                                                                                            +ดอกเบี้ยค้างชำระ <?php echo formatNumber($total_pending_interest);?> บาท
                                                                                                
                                                                                        <?php	
                                                                                        
                                                                                        
                                                                                            }
                                                                                        ?>
                                                                                        
                                                                                        <br />
                                                                                        
                                                                        <?php }?>
                                                                                        
                                                                        
                                                                        <?php if($this_lawful_year >= 2012){ //only show interests when 2012+?>
                                                                                     
                                                                                    จ่ายเป็นดอกเบี้ย
                                                                                    <?php
                                                                                        $total_interest_money += $interest_money_to_show;
                                                                                    
                                                                                        echo formatNumber($interest_money_to_show);
                                                                                        
                                                                                    ?>
                                                                                    บาท 
                                                                        
                                                                         <?php }?>
                                                                        
                                                                        
                                                                        จ่ายเป็นเงินต้น 
                                                                        
                                                                        
                                                                        <?php 
                                                                        
                                                                            
                                                                            $this_paid_money = $this_paid_amount-$interest_money;
                                                                            
                                                                            if($this_paid_money < 0){
                                                                                $this_paid_money = 0;
                                                                            }
                                                                            
                                                                            $total_paid_money += $this_paid_money;
                                                                            
                                                                            echo formatNumber($this_paid_money);
                                                                            
                                                                            
                                                                            //echo "this paid money: ".$this_paid_money;
                                                                            
                                                                            $paid_money += $this_paid_money;
                                                                            
                                                                            $paid_from_last_bill = $this_paid_money;
                                                                            
                                                                            //if($paid_from_last_bill == 0){
                                                                            //	echo "wrong";
                                                                            //}else{
                                                                            //	echo "right ".$paid_from_last_bill;
                                                                            //}
                                                                        ?>
                                                                        
                                                                        
                                                                        บาท 
                                                                        
                                                                        <?php 
                                                                        
                                                                        
                                                                        if($this_paid_amount < $interest_money){
                                                                            $pending_interest = (($interest_money - $this_paid_amount ));
                                                                            
                                                                            $total_pending_interest = $pending_interest;
                                                                        ?>
                                                                        ดอกเบี้ยค้างชำระ <?php echo formatNumber($pending_interest);?> บาท
                                                                        <?php }else{
                                                                        
                                                                            $total_pending_interest = 0;
                                                                        
                                                                        }?>
                                                                        
                                                                     </div>
                                                                     
                                                                     <?php if(strlen($receipt_row["ReceiptNote"])>0){ ?>
                                                                         <div style="padding:5px">
                                                                         ชำระเพื่อ: <?php echo $receipt_row["ReceiptNote"]?>                                                             </div>
                                                                     <?php } ?>
                                                                     
                                                                     
                                                                     
                                                            <?php
                                                                
                                                                }		//end while for looping to display payment details										
                                                            ?>
                                                            
                                                            <?php echo "</div> <!--- DIV closing payment details tag-->";?>
                                                            
                                                             <input name="have_receipt" type="hidden" value="<?php echo $have_receipt?>" />                                         	
                                                             
                                                            <hr />
                                                            
                                                        </td>
                                                        
                                                        
                                                       
                                                        
                                                     </tr>
                                                      
                                                      
                                                    <tr>
                                                        <td>
                                                                                                        
                                                        
                                                            <?php //if($start_money > 0){
                                                                //only show this for 2012++ year
                                                                if($this_lawful_year > 2011){
                                                                //only show this if has starting money
                                                            ?>
                                                            <table>
                                                            
                                                                
                                                                <tr>
                                                                    <td>
                                                                    เงินที่ต้องส่งเข้ากองทุน:                                             
                                                                    
                                                                    
                                                                    <input name="have_receipt" type="hidden" value="<?php echo $have_receipt;?>" />           
                                                                    
                                                                   
                                                                    
                                                                    
                                                                    </td>
                                                                    <td>
                                                                    <div align="right">
                                                                    <?php echo $extra_employee;?> x <?php echo $wage_rate;?> x <?php echo $year_date;?> = </div></td>
                                                                    
                                                                    <td>
                                                                    <div align="right">
                                                                    <?Php echo formatNumber($start_money);?>                                                        </div>
                                                                    <td>
                                                                    บาท                                                        
                                                                    
                                                                    
                                                                     <input name="money_per_person" type="hidden" value="<?php echo $wage_rate * $year_date;?>" />           
                                                                    
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                    ยอดเงินที่จ่ายเข้ากองทุนแล้ว:
                                                                    </td>
                                                                    <td>
                                                                    <div align="right">
                                                                    = </div></td>
                                                                    
                                                                    <td>
                                                                    <div align="right">
                                                                    <?Php echo formatNumber($paid_money); ?>                                                        </div>
                                                                    
                                                                    <input name="total_paid" type="hidden" value="<?php echo $paid_money;?>" />
                                                                    <td>
                                                                    บาท                                                        </td>
                                                                </tr>
                                                                
                                                                 
                                                                
                                                                <tr>
                                                                    <td>
                                                                    เงินต้นคงเหลือ:
                                                                    </td>
                                                                    <td>
                                                                    <div align="right">
                                                                    = </div></td>
                                                                    
                                                                    <td>
                                                                    <div align="right">
                                                                    
                                                                    <?Php 
                                                                        
                                                                        //update owned money here
                                                                        $owned_money = $start_money - $paid_money;// - $payback_money
                                                                        
                                                                        echo formatNumber($owned_money);
                                                                    
                                                                        
                                                                    
                                                                    
                                                                    ?>                                                        </div>
                                                                    <td>
                                                                    บาท                                                        </td>
                                                                </tr>
                                                                
                                                               
                                                                
                                                                <tr>
                                                                    <td>
                                                                    วันที่จ่ายเงินเข้ากองทุนล่าสุด:                                                        </td>
                                                                    <td>
                                                                    <div align="right">
                                                                   
                                                                    </div>
                                                                    
                                                                    
                                                                    </td>
                                                                    <td colspan="2">
                                                                    <div align="right">
                                                                     <?php 
                                                                    
                                                                    
                                                                    $the_sql = "select max(paymentDate) from payment, receipt , lawfulness
                                                                        where 
                                                                        receipt.RID = payment.RID
                                                                        and
                                                                        lawfulness.LID = payment.LID
                                                                        and
                                                                        ReceiptYear = '$this_lawful_year'
                                                                        and
                                                                        lawfulness.CID = '".$this_id."' 
                                                                        
                                                                        and
                                                                        is_payback != 1
                                                                        ";
                                                                    
                                                                    //echo $the_sql ;
                                                                    
                                                                    $actual_interest_date = getFirstItem($the_sql);
																	//echo "----".$actual_interest_date;
																	
																	
																	
																	
																	//////////
																	//
																	//
																	// 	20140224
																	//	clean this
																	//
																	//
																	//////////
																	
																	
																	//new vars
																	$interest_date_for_calculate_summary = $actual_interest_date;
																	
																	
																	 if(!$interest_date_for_calculate_summary){
																		$interest_date_for_calculate_summary = "$this_lawful_year-01-31 00:00:00";
																	}
																							
																	//echo "$this_lawful_year-02-01 00:00:00";		
																	
																	
																	//if last payment date is less than FEB 01 then detaulit it to FEB 01
																	if(strtotime(date($interest_date_for_calculate_summary)) 
																		< 
																		strtotime(date("$this_lawful_year-01-31"))){
																	
																		$interest_date_for_calculate_summary = "$this_lawful_year-01-31 00:00:00";
																	
																	}
																	
																	
																	//////////
																	//
																	//
																	// 	20140224
																	//	END clean this
																	//
																	//
																	//////////
																	
																	
                                                                    
                                                                    if($actual_interest_date && $actual_interest_date != '0000-00-00 00:00:00'){
                                                                        echo formatDateThai($actual_interest_date);
                                                                    }else{
                                                                        echo "ไม่เคยมีการจ่ายเงิน";
                                                                    }
                                                                    
                                                                    ?>                                                        </div>                                                        </td>
                                                                     
                                                                </tr>
                                                                
                                                                <?php
                                                                
                                                                //cal culate interest money
                                                                
                                                                if($owned_money <= 0){
                                                                
                                                                    //no longer calculate interests
                                                                    $interest_date = 0;
                                                                }else{
                                                                    $interest_date = getInterestDate($interest_date_for_calculate_summary, $this_lawful_year, "Y-m-d");
                                                                }
                                                                
                                                                //echo "<br>$actual_interest_date" . " / ". $this_lawful_year . " / ".  strtotime(date("Y-m-d"))."<br>";
                                                                
                                                                if($this_lawful_year >= 2012){ //only show interests when 2012+
                                                                    $interest_money = doGetInterests($interest_date,$owned_money,$year_date);
                                                                }else{
                                                                    $interest_money = 0;
                                                                }
                                                                
                                                                ?>
                                                                
                                                                
                                                                 
                                                                 <?php if($this_lawful_year >= 2012){//?>
                                                                 
                                                                        <tr>
                                                                            <td>
                                                                            ดอกเบี้ย ณ วันนี้:                                                        </td>
                                                                            <td>
                                                                            <div align="right">
                                                                            <?php echo formatNumber($owned_money);?> x 7.5/100/<?php echo $year_date;?> x <?php echo $interest_date;?> = 
                                                                            </div>
                                                                            
                                                                            
                                                                            </td>
                                                                            <td>
                                                                            <div align="right">
                                                                            <?Php echo formatNumber($interest_money);?>                                                        </div>                                                        </td>
                                                                             <td>
                                                                            บาท                                                        </td>
                                                                        </tr>
                                                                <?php }?>
                                                                
                                                                
                                                                
                                                                
                                                                
                                                                <?php if($this_lawful_year >= 2012){//?>
                                                                 <tr>
                                                                    <td>
                                                                    ดอกเบี้ยค้างชำระ:
                                                                    </td>
                                                                    <td>
                                                                    <div align="right">
                                                                    = </div></td>
                                                                    
                                                                    <td>
                                                                    <div align="right">
                                                                    <?Php echo formatNumber($total_pending_interest);?>                                                        </div>
                                                                    <td>
                                                                    บาท                                                        </td>
                                                                </tr>
                                                                <?php }?>
                                                                
                                                                
                                                                
                                                                <tr>
                                                                    <td>
                                                                    ขอเงินคืนจากกองทุนฯ:
                                                                    </td>
                                                                    <td>
                                                                    <div align="right">
                                                                    = </div></td>
                                                                    
                                                                    <td>
                                                                    <div align="right">
                                                                    <?Php echo formatNumber($payback_money);?>                                                        </div>
                                                                    <td>
                                                                    บาท                                                        </td>
                                                                </tr>
                                                                
                                                                
                                                                
                                                                <tr>
                                                                    <td>
                                                                    
                                                                    <?php 
                                                                        $the_final_money = $owned_money + $interest_money +$payback_money +$total_pending_interest;
                                                                        //$the_final_money = $owned_money;
                                                                        
                                                                        //yoes 20130801 - add proper decimal to final monty
                                                                        //$the_final_money = number_format($the_final_money,2);
                                                                        $the_final_money = round($the_final_money,2);
                                                                    
                                                                        if($the_final_money < 0){
                                                                    ?>
                                                                          ต้องส่งเงินคืน:
                                                                            
                                                                    <?php }else{?>
                                                                    
                                                                    
                                                                    
                                                                          ยอดเงินค้างชำระ:      
                                                                          
                                                                                                   
                                                                    <?php }?>
                                                                    
                                                                    
                                                                    
                                                                    
                                                                    
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                    <td>
                                                                    <div align="right">
                                                                    
                                                                    <input name="the_final_money" type="hidden" value="<?php echo $the_final_money;?>" />
                                                                    
                                                                    <?Php 
                                                                    
                                                                        
                                                                    
                                                                        
                                                                        if(floor($the_final_money) > 0){
                                                                            echo "<font color='red'>";
                                                                        }else if($the_final_money < 0){
                                                                            echo "<font color='green'>";
                                                                            $the_final_money = $the_final_money * -1;
                                                                        }else{
                                                                            echo "<font>";
                                                                        }
                                                                    
                                                                        echo formatNumber($the_final_money);
                                                                        
                                                                        echo "</font>";
                                                                        
                                                                        ?>
                                                                        
                                                                        
                                                                     </div>
                                                                    </td>
                                                                    
                                                                     <td>
                                                                    บาท                                                        </td>
                                                                </tr>
                                                            </table>
                                                            
                                                            
                                                            <?php }//starting_money > 0?>
                                                        
                                                        </td>
                                                      </tr>
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                       <tr>
                                                        <td>
                                                        
                                                             <hr />
                                                           <strong> รายละเอียดขอเงินคืนจากกองทุนฯ</strong>
                                                           
                                                           <?php if($sess_accesslevel!=4 && $sess_accesslevel!=5 && !$is_blank_lawful){?>
                                                       <a href="org_list.php?search_id=<?php echo $this_id?>&mode=payment&for_year=<?php echo $this_lawful_year;?>&payback=1" style="font-weight: bold;">+ เพิ่มรายละเอียดขอเงินคืนจากกองทุนฯ</a>
                                                       <?php }?>
                                                           
                                                           
                                                           <br />
                                                        <a href="#" onClick="togglePaybackDetails(); return false;">- แสดงข้อมูลการขอเงินคืน</a>
                                                        
                                                        <script>
                                                        
                                                            function togglePaybackDetails(){
                                                            
                                                            
                                                                if(document.getElementById('payback_details').style.display == 'none'){
                                                                    document.getElementById('payback_details').style.display = '';																
                                                                }else{
                                                                    document.getElementById('payback_details').style.display = 'none';
                                                                }
                                                            
                                                            }
                                                        
                                                            
                                                        
                                                        </script>
                                                           
                                                           
                                                       <?php echo "<div id='payback_details' >";?>
                                                           
                                                           
                                                             <?php 
                                                            //generate payback info
                                                            $the_sql = "select * from payment, receipt , lawfulness
                                                                        where 
                                                                        receipt.RID = payment.RID
                                                                        and
                                                                        lawfulness.LID = payment.LID
                                                                        and
                                                                        ReceiptYear = '$this_lawful_year'
                                                                        and
                                                                        lawfulness.CID = '".$this_id."' 
                                                                        
                                                                        and
                                                                        is_payback = 1
                                                                        order by receipt.RID desc";
                                                            
                                                            //echo $the_sql;
                                                            $the_result = mysql_query($the_sql);
                                                            
                                                            $have_receipt = 0;
                                                            while($result_row = mysql_fetch_array($the_result)){
                                                            
                                                                $have_receipt = 1;
                                                                
                                                                //echo "select * from receipt where RID = '".$result_row["RID"]."'";										
                                                                $receipt_row = getFirstRow("select * from receipt where RID = '".$result_row["RID"]."'");
                                                            
                                                            ?>
                                                                     <div style="padding:5px">
                                                                     <?php if($sess_accesslevel != 4){?>
                                                                        ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <a href="view_payment.php?id=<?php echo $result_row["RID"]?>"><?php echo $receipt_row["ReceiptNo"]?></a> 
                                                                        <?php }elseif($sess_accesslevel == 4 && strlen($receipt_row["BookReceiptNo"]) > 0){?>
                                                                        ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <?php echo $receipt_row["ReceiptNo"]?> 
                                                                        <?php //}elseif($sess_accesslevel == 4){
                                                                            }elseif(1 == 0){																	
                                                                        ?>
                                                                         <a href="scrp_delete_receipt.php?id=<?php echo doCleanOutput($result_row["RID"]);?>"  onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');" title="ลบข้อมูลการจ่ายเงินออกจากระบบ"><img src="decors/trashcan_icon.jpg" border="0" /></a>
                                                                        <?php }?>
                                                                        
                                                                        วันที่จ่าย <?php echo formatDateThai($receipt_row["ReceiptDate"])?> จำนวนเงิน <?php echo formatNumber($receipt_row["Amount"]); $total_payback_money += $receipt_row["Amount"];?> บาท จ่ายโดย <?php echo formatPaymentName($receipt_row["PaymentMethod"]);?> <?php
                                                                        
                                                                        $paid_for = getFirstItem("select count(*) from payment where payment.rid = '".$result_row["RID"]."'");
                                                                        if($paid_for > 1){
                                                                            echo "<span style='color:green'>จ่ายให้ $paid_for แห่ง</span>";
                                                                        }
                                                                        ?>
                                                                     </div>
                                                                     
                                                                     <?php if(strlen($receipt_row["ReceiptNote"])>0){ ?>
                                                                         <div style="padding:5px">
                                                                         ชำระเพื่อ: <?php echo $receipt_row["ReceiptNote"]?>                                                             </div>
                                                                     <?php } ?>
                                                                     
                                                                     
                                                                     
                                                            <?php
                                                                
                                                                }		//end loop for display "payback"										
                                                            ?>
                                                           
                                                           
                                                           <?php echo "</div>"; // end div for "payback"?>
                                                           
                                                           
                                                           
                                                           
                                                           
                                                           
                                                           
                                                         </td>
                                                         
                                                         
                                                         
                                                         
                                                          
                                                         
                                                         
                                                         
                                                       </tr>
                                                      
                                                      
                                                      <tr>
                                                        
                                                        <td colspan="4"><hr /></td>
                                                      </tr>
                                                    </table>
                                                    
                                                    
                                                    
                                                    <!-- end payment table -->
                                                    <!-- end payment table -->
                                                    <!-- end payment table -->
                                                    <!-- end payment table -->
                                                    <!-- end payment table -->
                                                     <!-- end payment table -->
                                                
                                                
                                                
                                                
                                              </td>
                                                <td valign="top">
                                               
                                            
                                            			
                                                        
                                                        <table style="border:1px solid #666; margin: 5px 0 0 5px;">
                                                        	<tr>
                                                            	<td colspan="3">
                                                               	 
                                                                <div align="center" style="font-weight: bold; padding: 5px; background-color:#efefef">สรุปการส่งเงิน</div>
                                                                </td>
                                                            	
                                                           </tr>
                                                           <tr>
                                                            	<td>
                                                               	เงินต้น 
                                                                </td>
                                                            	<td>
                                                                
                                                                    <div align="right">
                                                                    <?php echo formatNumber($total_paid_money); ?>
                                                                    </div>
                                                                
                                                                </td>
                                                            	<td>บาท</td>
                                                           </tr>
                                                            <tr>
                                                            	<td>
                                                               	ดอกเบี้ย 
                                                                </td>
                                                            	<td>
                                                                     <div align="right">
                                                                    <?php echo formatNumber($total_interest_money); ?>
                                                                    </div>
                                                                </td>
                                                            	<td>บาท</td>
                                                           </tr>
                                                            <tr>
                                                            	<td>รับเงินคืนจากกองทุน 
                                                                </td>
                                                            	<td>
                                                                <div align="right">
                                                                    <?php echo formatNumber($total_payback_money); ?>
                                                                  </div>
                                                                </td>
                                                            	<td>บาท</td>
                                                           </tr>
                                                           <tr>
                                                            	<td>
                                                               	รวม 
                                                                </td>
                                                            	<td>
																<div align="right">
																<strong><?php echo formatNumber($total_paid_money + $total_interest_money - $total_payback_money); ?></strong>
                                                                </div>
                                                                </td>
                                                            	<td>บาท</td>
                                                           </tr>
                                                       </table>
                                            
                                           
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        
                                        
                                        
                                        </td>
                                      </tr>
                                    </table>


<?php 
	if($output_values["CompanyTypeCode"] >= 200 && $output_values["CompanyTypeCode"] <= 300 || $sess_accesslevel == 6 ||  $sess_accesslevel == 7){
		//if(1==1){
?>							
<script>	
document.getElementById('rule_34_table').style.display = 'none';
</script>
<?php }?>








                                    
<table border="0">

	
  <tr>
    <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    
    
    	<?php if($sess_accesslevel == 4){?>
        
        
    
    	<?php }elseif($is_2013){?>

            <input name="Conc_status" type="hidden" id="Conc_status" value="<?php echo $lawful_row["Conc_status"];?>" />
            
            <?php if($lawful_row["Conc_status"] == 1){?>
        
            	<img src="decors/checked.gif" />
            
        
            <?php }else{?>
            
                <input name="" type="checkbox" value="" disabled="disabled" />
            
            <?php }?>
        
        
        <?php }else{?>
           <input name="Conc_status" type="checkbox" id="Conc_status" value="1"  <?php echoChecked($lawful_values["Conc_status"])?>/>                                                                          
        
        <?php }?>
        
        
        
        </td>
    <td><span style="font-weight: bold;color:#006600;">
    <?php //if($sess_accesslevel ==4){
	 if(1==1){?>
        มาตรา 35 ให้สัมปทานฯ
     <?php }else{ ?>
        ปฏิบัติตามมาตรา 35
     <?php } ?></span></td>
    <td></td>
  </tr>
  <tr>
    <td ></td>
    <td>
    
    - มีผู้ใช้สิทธิ: <strong><?php 
	
			
			//$curator_table_name is from scrp_add_curator.php
	
			$curator_user = getFirstItem("
													
											select count(*) 
											from 
											
											$curator_table_name 
											
											where 
											curator_lid = '".$lawful_values["LID"]."' and curator_parent = 0
											
										
										");
	
	
			echo $curator_user;
	
		?></strong> คน, ผู้พิการถูกใช้สิทธิ: <strong><?php 
	
			
			//$curator_usee is disabled person who has parents (right is used by someone else)
			//disable whose rights is not used by someone else is not $curator_usee
			
			$curator_usee = getFirstItem("
						
								select count(*) 
								from 
								$curator_table_name 
								where 
								curator_lid = '".$lawful_values["LID"]."' and curator_parent > 0
			
								");
							
			echo $curator_usee;
	
		?></strong> คน
    
    <input name="curator_usee" type="hidden" value="<?php echo $curator_user;?>" />
    
    
    <?php if(!$is_blank_lawful && $sess_accesslevel != 4){?>
    <a href="#" onClick="doPopSubCurator('0'); fireMyPopup('35_popup',1020,500); document.getElementById('curator_form').reset(); return false;" style="font-size: 16px; ">+เพิ่มข้อมูลผู้ใช้สิทธิ คลิกที่นี่</a>
    <?php }?>
    
    <?php if($sess_accesslevel == 4 && !$submitted_company_lawful){?>
    <a href="#" onClick="doPopSubCurator('0'); fireMyPopup('35_popup',1020,500); document.getElementById('curator_form').reset(); return false;" style="font-size: 16px; ">+เพิ่มข้อมูลผู้ใช้สิทธิ คลิกที่นี่</a>
    <?php }?>
    
    
    <hr />
    
    </td>
    <td></td>
  </tr>
  
  
 
  
  <tr>
    <td ></td>
    <td>
    
   
   ดูข้อมูล/แก้ไขข้อมูลผู้ใช้สิทธิได้โดยการคลิกรายชื่อด้านล่าง
   
   <table style="border-collapse:collapse"  border="1" cellpadding="3">
   
   <?php
			
		//get main curator
		$sql = "select * from $curator_table_name where curator_lid = '".$lawful_values["LID"]."' and curator_parent = 0 order by curator_id asc";
		//echo $sql;
		
		$org_result = mysql_query($sql);
		$total_records = 0;
		while ($post_row = mysql_fetch_array($org_result)) {			
			
			$total_records++;
	
	
			if($total_records%2 == 1){
			
				echo "<tr>";
			
			}
			
			$curator_usee = getFirstItem("select count(*) from $curator_table_name where curator_parent = '".$post_row["curator_id"]."' ");
			
	?>
   	
    	
        <td>
        <strong><?php echo $total_records;?></strong>
        </td>
        
        
        <td>
        
        
        <?php if(!$submitted_company_lawful){?>
       		 <a href="view_curator.php?curator_id=<?php echo $post_row["curator_id"];?>" style="font-weight: normal" ><?php echo doCleanOutput($post_row["curator_name"]);?></a>
        <?php }else{?>
      		 <?php echo doCleanOutput($post_row["curator_name"]);?>
        <?php }?>
        
        
        
        
        </td>
    	<td>
        
        <?php if($post_row["curator_is_disable"] == 1){?>
	        คนพิการ
         <?php }else{ ?>
	       	 ผู้ดูแลคนพิการ 
             
             <?php if($curator_usee < 1){?>
             <font color="#FF0000">*ไม่มีข้อมูลคนพิการ</font>
             <?php }?>
             
         <?php }?>
         
         
         
         </td>
         <td>
             
             
        <?php if(!$submitted_company_lawful){?>
        
        <a href="scrp_delete_curator_new.php?id=<?php echo doCleanOutput($post_row["curator_id"]);?>&cid=<?php echo $this_cid;?>&year=<?php echo $this_lawful_year;?>" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูลผู้ใช้สิทธิ? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" height="10"  border="0" /></a>
        
        <?php }?>
         
         
        </td>
        
    
     
     <?php 
	 
	 		if($total_records%2 == 0){
			
				echo "</tr>";
			
			}
	 
	 	}//end drawing curators
		
		//see if has to close any extra </tr>
		if($total_records%2 == 1){
			
				echo "<td></td></tr>";
			
			}
		?>
    
    </table>
    
    
    
    </td>
    <td></td>
  </tr>
  
  
   
  
  
  
</table>












<?php 


		////////////////// 
		//////////
		//////////		NEW AS OF 20140215
		//////////		Lawfulnewss 34 for ORG
		//////////
		////////////////// 

?>









<?php 


			
			
//PAYMENT FOR COMPANY ----> ONLY DO THIS IF NEEDED TO			
$extra_employee = $final_employee - $hire_numofemp - $curator_user;

		
if($sess_accesslevel == 4 && $extra_employee > 0){ // company do whatever company do  -------------> PAYMENT


?>
<table border="0" style="padding-top:20px;">

	
    
    
     
    
    
    
    
    
  <tr>
    <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    
    
           <!--<input name="sss" type="checkbox" id="sss" value="1" />-->                                                                      
        
       
        
        </td>
    <td><span style="font-weight: bold;color:#006600;">
    มาตรา 34 ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ </span>
    
    
    </td>
    <td></td>
    
  </tr>
  
 
   
    <tr>
        <td ></td>
        <td colspan="2">
        
        
    <div style="font-size:18px; padding:10px 0; color:#C00;">
	    ท่านต้องชำระเงินตามมาตรา 34 
    </div>
       
  <table cellpadding="3" style="margin:0px 0 0 20px;" >
   	  
        <tr>
            <td> ประจำปี</td>
            <td><strong><?php echo formatYear($this_year);?></strong>  </td>
            <td>&nbsp;</td>
            <td>อัตราค่าแรง</td>
            <td><strong id="province_wage"><?php 
			
			$this_year_wage = default_value(getFirstItem("select var_value from vars where var_name = 'wage_$this_year'"),300);
			
			echo $this_year_wage;
			
			?></strong> บาท/วัน</td>
 	    </tr>
        
        
                	<tr>
                	  <td valign="top">จำนวนลูกจ้างทั่วประเทศ                      
                      
                      </td>
                	  <td valign="top"> 
                      		<strong><?php echo formatEmployee($employee_to_use);?></strong>
                                 
                                 </td>
                	  <td valign="top">คน</td>
                	  <td valign="top">อัตราส่วนที่ต้องรับคนพิการ</td>
                	  <td valign="top">
                      <?php echo $ratio_to_use;?> :1 = <strong id="employee_ratio"><?php  echo formatEmployee($final_employee);?></strong> คน
                      </td>
              	  </tr>
                	<tr>
                	  <td>รับคนพิการเข้าทำงานแล้ว</td>
                	  <td>
                      
                      <strong><?php echo $hire_numofemp;?></strong>
                      
                      </td>
                	  <td>คน</td>
                	  <td>ให้สัมปทานฯ ตาม ม.35</td>
                	  <td>
                      
                      <strong><?php echo $curator_user;?></strong>
                      
                	  คน/สัญญา</td>
              	  </tr>
                  
                 
                  
                  
                  <tr>
                	  <td>วันที่ต้องการชำระเงิน</td>
                	  <td>
                      
                      <?php 
					  
					  	//get inputted date
						$company_pay_data = getFirstRow("select * from payment_company where CID = '$this_id' and Year = '$this_lawful_year'");
                                                
					    //
					  	$selector_name = "the_pay_date";						
						$this_date_time = $company_pay_data["PayDate"];	
						//
						$this_ref_number = $company_pay_data["RefNo"];			
														   
						
					  	include "date_selector.php";
					  
					  ?>
                      
                      
                	    </td>
                	  <td>&nbsp;</td>
                	  <td>&nbsp;</td>
                	  <td></td>
              	  </tr>
                  
                  
                  
                  
                  <?php 
				  
				  	$interest_date = getInterestDate($last_payment_date, $this_lawful_year, $this_date_time);
				  
				  ?>
                  
                  
                  
                  
                  
                	<tr <?php if(!$interest_date){?> style="display:none"<?php }?>>
                	  <td>เงินต้น
                      <!--<br>
               	      (จ่ายภายในวันที่ 31 ม.ค. 2557)--> </td>
                	  <td colspan="4">
                	    
                        
                        <span id="final_value_to_show" style="color:#060;">
               	        
							<?Php
                            
                            if($extra_employee < 0){
                                $extra_employee = 0;	 
                            }
                            
                            $extra_money = $extra_employee * $this_year_wage * 365;	
                            
                             echo formatNumber($extra_money);
                             
                             ?>
                         
                         
               	        
               	        </span> บาท
                	                   	        
                	     
                         
                         
                         
               	     </td>
               	    </tr>
                    
                    
                    <tr <?php if(!$interest_date){?> style="display:none"<?php }?>>
                	  <td>
                      ดอกเบี้ย     (<?Php
                            
							
                            
                             echo ($interest_date);
                             
                             ?> วัน นับจากวันที่ 31 ม.ค.)                 
                      </td>
                	  <td colspan="4">
                	    
                        <font color="#FF0000"><?php 
						
						$interest_money = doGetInterests($interest_date,$extra_money,$year_date);
						
						 echo formatNumber($interest_money);
						 

						 
						
						?></font>
                       
               	        
							
                         
						บาท
                         
                         
               	     </td>
               	    </tr>
                    
                     <tr>
                	  <td>
                     จำนวนเงินที่ต้องจ่าย           
                      </td>
                	  <td colspan="4">
                	    
                        <?php 
						
							$final_money = $interest_money + $extra_money;
							
							echo formatNumber($final_money);
						
						?> บาท
                        
                        <input type="hidden" name="Amount" value="<?php echo $final_money;?>" />
                         <input type="hidden" name="auto_post" value="<?php echo $_GET["auto_post"];?>" />
                         
                         
               	     </td>
               	    </tr>
                    
                
                
                  
                  
                  <tr>
                	  <td>จ่ายโดย</td>
                	  <td colspan="3"><select name="PaymentMethod" id="PaymentMethod" onchange="doToggleMethod();">
                                    <option value="Cash" >เงินสด</option>
                                    <option value="Cheque" <?php if($company_pay_data["PaymentMethod"]=="Cheque"){echo "selected='selected'";}?>>เช็ค</option>
                                    <option value="Note" <?php if($company_pay_data["PaymentMethod"]=="Note"){echo "selected='selected'";}?>>ธนาณัติ</option>
                                  </select></td>
                	  
              	  </tr>
                   <tr>
                                <td colspan="4">
                                
                                
                                
                                <table id="cash_table" border="0" style="padding:0px 0 0 50px;" >
                                    <tr>
                                      <td><span style="font-weight: bold">ข้อมูลการจ่ายเงินสด</span></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                  </table>
                                  
                                  
                                    <table id="cheque_table" border="0" style="padding:0px 0 0 50px;"  >
                                      <tr>
                                        <td><span style="font-weight: bold">ข้อมูลการจ่ายเช็ค</span></td>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">ธนาคาร</span></td>
                                        <td><?php
											
												$this_bank_id = $company_pay_data["bank_id"];
										
											  include "ddl_bank.php";
											  ?>                                        </td>
                                      </tr>
                                      <tr>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">เลขที่เช็ค</span></td>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">
                                          <input name="Cheque_ref_no" type="text" id="Cheque_ref_no" value="<?php echo $this_ref_number;?>"  />
                                        </span></td>
                                      </tr>
                                      
                                      <?php if($this_date_time){?>
                                      
                                      <tr>
                                        <td>ลงวันที่</td>
                                        <td>
                                        <?php 
										
										$selector_name = "the_date";										
										$this_date_time = $company_pay_data["PayDate"];											
										//include "date_selector.php";
										
										echo formatDateThai($this_date_time);
										
										
										?>
                                        </td>
                                        
                                      </tr>
                                      
                                      <?php }?>
                                      
                                    </table>
                                    
                                    
                                    
                                    
   								 <table id="note_table" border="0" style="padding:0px 0 0 50px; " >
                                      <tr>
                                        <td><span style="font-weight: bold">ข้อมูลการจ่ายธนาณัติ</span></td>
                                        <td>&nbsp;</td>
                                       
                                      </tr>
                                      <tr>
                                        <td>เลขที่ธนาณัติ</td>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">
                                          <input name="Note_ref_no" type="text" id="Note_ref_no" value="<?php echo $this_ref_number;?>"  />
                                        </span></td>
                                        
                                      </tr>
                                      
                                      <?php if($this_date_time){?>
                                           <tr>
                                            <td>ลงวันที่</td>
                                            <td>
                                             <?php 
                                            
                                            $selector_name = "the_note_date";										
                                            $this_date_time = $company_pay_data["PayDate"];												
                                            //include "date_selector.php";
                                            echo formatDateThai($this_date_time);
                                            
                                            ?>
                                            </td>
                                            
                                          </tr>
                                      <?php }?>
                                      
                                  </table>
                                  
                                  
                                  
                                  </td>
                              </tr>
                              
                              <?php if(!$submitted_company_lawful){?>
                              <tr>
                              	<td colspan="6">
                                <hr />
                                    <div align="center">
                                    
                                      <input type="submit" value="คำนวณเงินมาตรา 34" />
                                                                      
                                    </div>
                                <hr />
                                </td>
                                
                             </tr>
                             <?php }?>
                  
 				 </table>
                 
                 <script>
									
														
							function doToggleMethod(){
							
								the_method = document.getElementById("PaymentMethod").value;
							
								document.getElementById("cash_table").style.display = "none";
								document.getElementById("cheque_table").style.display = "none";
								document.getElementById("note_table").style.display = "none";
								
								if(the_method == "Cash"){
									//document.getElementById("cash_table").style.display = "";
								}else if(the_method == "Cheque"){
									document.getElementById("cheque_table").style.display = "";
								}else if(the_method == "Note"){
									document.getElementById("note_table").style.display = "";
								}
							}	
							
							doToggleMethod();							
							
							
						</script>
                 
       
       </td>
   
   </tr>
   

	
    
    
    

  
</table>  

<?php }//end if($sess_accesslevel == 4 ////---------> Payment?>






<?php 


		////////////////// 
		//////////
		//////////		NEW AS OF 20140215
		//////////		Company Attachment
		//////////
		////////////////// 

?>


<?php if($sess_accesslevel == 4){ // company do whatever company do?>

<table border="0" style="padding-top:20px;">

	
  <tr>
    <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    
          <!-- <input name="ปปป" type="checkbox" id="ปปป" value="1" /> -->
          
          
        </td>
    <td><span style="font-weight: bold;color:#006600;">
    เอกสารเพิ่มเติม </span></td>
    <td></td>
    <td></td>
    
  </tr>
  
 
   
    <tr>
    	<td >
        </td>
        <td  >เอกสารประกอบ</td>
        <td colspan="2" >
        
        
        
        <div style="width:400px; padding-bottom:5px;">
        <?php 
            
            //do $this_id swap thing because doc link use LID, but consume $this_id
            //but $this_id on this page is CID and not LID...
            $this_id_temp = $this_id;
            $this_id = $lawful_values["LID"];
            
            $file_type = "company_docfile";
        
            include "doc_file_links.php";
            
            $this_id = $this_id_temp;
            
        ?>
        </div>
                           
        
        <?php if(!$submitted_company_lawful){?>
                                
            <input type="file" name="company_docfile" id="company_docfile"/>
            
            <hr />
            
            <input type="submit" value="เพิ่มเอกสารประกอบ"/>
        
        <?php }?>
        
        </td>
       
    </tr>
    
    
    
    <tr>
    	<td >
        </td>
    	<td colspan="3" >
        <hr />
        </td>
    </tr>
    
    
    <tr>
    	<td >
        </td>
        <td valign="top">
        <span style="font-weight: bold;color:#006600;">
        หมายเหตุเพิ่มเติม(ถ้ามี)
        </span>
        </td>
        <td colspan="2" >
       
       
       
       
        </td>
    </tr>
    
    <tr>
    	<td >
        </td>
        <td colspan="3">
       
       
       
       <textarea name="lawful_remarks" cols="50" rows="4" id="lawful_remarks"><?php echo getFirstItem("select lawful_remarks from lawfulness_company where CID = '$this_id' and Year = '$this_lawful_year'");?></textarea>
       
       
        </td>
    </tr>
    
</table>    


<?php }?>








<script>
	function doPopSubCurator(parent){				
						
		document.getElementById('curator_parent').value = parent;
		
		if(parent == 3){
		
			document.getElementById('curator_input_forms').style.display = 'none';
		
		}else if(parent == 0){
		
			//parent -> have events
			document.getElementById('tr_curator_event').style.display = '';
			document.getElementById('tr_curator_event_2').style.display = '';
			document.getElementById('tr_curator_docfile').style.display = '';
			document.getElementById('tr_curator_disable').style.display = 'none';
			
			document.getElementById('the_parent').style.display = '';
			document.getElementById('the_child').style.display = 'none';
			
			document.getElementById('curator_input_forms').style.display = '';
			
			//
			document.getElementById('curator_is_disable').style.display = '';
			document.getElementById('curator_is_disable_text').style.display = '';
			
			document.getElementById('curator_start_date_text').style.display = '';
			document.getElementById('curator_start_date_day').style.display = '';
			document.getElementById('curator_start_date_month').style.display = '';
			document.getElementById('curator_start_date_year').style.display = '';
			
			document.getElementById('curator_end_date_text').style.display = '';
			document.getElementById('curator_end_date_day').style.display = '';
			document.getElementById('curator_end_date_month').style.display = '';
			document.getElementById('curator_end_date_year').style.display = '';
			
		}else{
		
			document.getElementById('tr_curator_event').style.display = 'none';
			document.getElementById('tr_curator_event_2').style.display = 'none';
			document.getElementById('tr_curator_docfile').style.display = 'none';
			document.getElementById('tr_curator_disable').style.display = '';
			
			document.getElementById('the_parent').style.display = 'none';
			document.getElementById('the_child').style.display = '';
			
			document.getElementById('curator_input_forms').style.display = '';
			
			//
			document.getElementById('curator_is_disable').style.display = 'none';
			document.getElementById('curator_is_disable_text').style.display = 'none';
			
			document.getElementById('curator_start_date_text').style.display = 'none';
			document.getElementById('curator_start_date_day').style.display = 'none';
			document.getElementById('curator_start_date_month').style.display = 'none';
			document.getElementById('curator_start_date_year').style.display = 'none';
			
			document.getElementById('curator_end_date_text').style.display = 'none';
			document.getElementById('curator_end_date_day').style.display = 'none';
			document.getElementById('curator_end_date_month').style.display = 'none';
			document.getElementById('curator_end_date_year').style.display = 'none';
		
		}
	}
</script>

 


<div align="center">
    	            
    	</div>


<!----------------------->


                                  <td>&nbsp;</td>
                                </tr>
                                
                                
                                
                                <tr>
                                  <td colspan="2"><div align="center">
                                  	<hr />
                                      <input name="Year" type="hidden" value="<?php echo $this_lawful_year;?>" />
                                      
                                      <?php if($sess_accesslevel == 5){ //exec can do nothing?>
                                      
                                      <?php }elseif($sess_accesslevel !=4){ ?>
                                      <input type="submit" name="update_lawful" id="update_lawful" value="ปรับปรุงข้อมูล" 
                                      
                                      
										  <?php if(!$_GET["auto_post"]){?>
                                          onclick = "return confirm('ต้องการปรับปรุงข้อมูลการปฏิบัติตามกฎหมายนี้?');"
                                          <?php }?>
                                      
                                      />
                                      
                                      	<?php if($_GET["auto_post"]){ //if this is an auto-post?>
                                        	
                                            <input name="le" type="hidden" value="<?php echo $_GET["le"];?>" />
                                            <input name="delle" type="hidden" value="<?php echo $_GET["delle"];?>" />
                                            <input name="curate" type="hidden" value="<?php echo $_GET["curate"];?>" />
                                            <input name="curator_id" type="hidden" value="<?php echo $_GET["curator_id"];?>" />
                                           
                                        
                                        <?php }?>
                                        
                                        
                                      <?php }else{ ?>
                                      
                                      <!--<input type="submit" name="save_lawful" id="save_lawful" value="บันทึกข้อมูล" 
                                      onclick = "return confirm('บันทึกข้อมูลการปฏิบัติตามกฎหมายนี้?');"
                                      />
                                      
                                       <input type="button" name="cancel_lawful" id="cancel_lawful" value="ยกเลิกการแก้ไขข้อมูล" 
                                      onclick = "doConfirmCancel();"
                                      />
                                      
                                      
                                      -->
                                      <?php if(!$submitted_company_lawful){?>
                                     
                                     
                                          <input type="submit" name="submit_lawful" id="submit_lawful" value="บันทึกข้อมูล" 
                                          onclick = "return confirm('บันทึกข้อมูลการปฏิบัติตามกฎหมายนี้?');"
                                          />
                                          
                                          
                                        
                                          <input type="button" name="submit_doc" id="submit_doc" value="ยื่นแบบฟอร์มออนไลน์"                                      
                                          />
                                          
                                          <input type="hidden" name="is_submitted" id="is_submitted" value="0" />
                                      
                                      <?php }?>
                                      
                                      <hr />
                                      
                                      
                                      
                                      
                                    <script>
									  	function doConfirmCancel(){
											if(confirm('ยกเลิกการแก้ไขข้อมูล และกลับไปหน้าแรก?')){
												document.location = "organization.php?id=<?php echo $this_id;?>";
											}else{
												return false;
											}
										}
									  </script>
                                      
                                      
                                      
									<script>                                        
                                        
										var form = document.getElementById('lawful_form');
										
                                        document.getElementById('submit_doc').onclick = function() {
                                            
                                            if(confirm('การยื่นเอกสารออนไลน์ – หลังจากยื่นแล้วจะไม่สามารถกลับมาแก้ไขผ่านระบบออนไลน์ได้อีก และกรุณาพิมพ์แบบรายงาน เพื่อลงนาม พร้อมประทับตรา แล้วจัดส่งเอกสารต่างๆ มาที่สํานักงาน ภายในวันที่ 31 มกราคม')){
                                                    
                                                //form.action = 'report_org_list.php';
                                                //form.target = '_blank';
												
												document.getElementById('is_submitted').value = 1;
                                                form.submit();
                                            }else{                                            
                                                return false;                                              
                                                
                                            }
                                        }
										
										
                                    </script>
                                      
                                      
                                      <?php } ?>
                                      
                                      <input name="CID" type="hidden" value="<?php echo $this_id; ?>" />
                                      <input name="LID" type="hidden" value="<?php echo $lawful_values["LID"]; ?>" />
                                      
                                      
                                      
                                        
                                        
                                  </div></td>
                                </tr>
                          </form>
                          
                          
                          
                             <?php if(($this_lawful_year == 2014) && $sess_accesslevel == 4){?>
                                    <tr>
                                          <td colspan="2">
                                          <div align="center">
                                          
                                          <?php if($output_values["Province"] == 1){?>
                                             	<form method="post" action="./tcpdf/bangkok_57_pdf.php" target="_blank">
                                            <?php }else{?>
                                            	<form method="post" action="./tcpdf/province_57_pdf.php" target="_blank">
                                            <?php }?>
                                            
                                                    <input name="" type="submit" value="พิมพ์แบบฟอร์มออนไลน์" />
                                                                                    
                                                    <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                                    <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                                    <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
                                                    
                                                    
                                                    <input type="hidden" name="employee_to_use" value="<?php echo $employee_to_use; ?>"/>
                                                    <input type="hidden" name="final_employee" value="<?php echo $final_employee; ?>"/>
                                                    <input type="hidden" name="curator_user" value="<?php echo $curator_user; ?>"/>
                                                    
                                                    <input type="hidden" name="hire_numofemp" value="<?php echo $hire_numofemp; ?>"/>
                                                    <input type="hidden" name="extra_money" value="<?php echo $final_money; ?>"/>
                                                    
                                                    <input type="hidden" name="extra_employee" value="<?php echo $extra_employee; ?>"/>
                                                   
                                                
                                                </form>
                                          </div>
                                         </td>
                                    </tr>
                          
                                  <?php }?>
                          
                          
                          <?php if($sess_accesslevel != 4){ ?>
                      <tr>
                        <td><hr />
                        <div style="font-weight: bold; padding: 5px; background-color:#efefef">การประกาศผ่านสื่อ</div>
                        
                      				  <table border="0" >
                                        	
                                          <tr>
                                          	<td>
                                            <?php 
												//generate reciept info
												$the_sql = "select * from announcecomp where CID = '".$this_id."' order by AID desc";
												
												$the_result = mysql_query($the_sql);
												
												while($result_row = mysql_fetch_array($the_result)){
													//echo "select * from receipt where RID = '".$result_row["RID"]."'";										
													$announcement_row = getFirstRow("select * from announcement where AID = '".$result_row["AID"]."'");
												
												?>
                                               			 <div style="padding:5px">ประกาศผ่านสื่อเลขที่ <a href="view_announce.php?id=<?php echo $announcement_row["AID"]?>"><?php echo $announcement_row["GovDocNo"]?></a> <!--ครั้งที่ <?php echo $announcement_row["ANum"]?> วันที่ <?php echo formatDateThai($announcement_row["ADate"])?> ประกาศทาง <?php echo getFirstItem("select newspaper_name from newspaper where newspaper_id = '".$announcement_row["newspaper_id"]."'");?>--></div>
                                                <?php
													
													}												
												?>
		                                         
                                         	</td>
                                         </tr>
                                          
                                          <tr>
                                            
                                            <td colspan="4"><hr />
                                            
                                             <?php if($sess_accesslevel!=5){?> 
                                            <a href="org_list.php?search_id=<?php echo $this_id?>&mode=announce&for_year=<?php echo $this_lawful_year;?>" style="font-weight: bold;">+ เพิ่มข้อมูลการแจ้งผ่านสื่อ</a>
                                            <?php }?>
                                            
                                            
                                            </td>
                                          </tr>
                                          
                                          <tr>
                                            
                                            <td colspan="4"><hr /></td>
                                          </tr>
                          </table>
                        
                        </td>
                      </tr>
                            <?php }// <?php if($sess_accesslevel != 4){ ?>   
                               
                  </table>
                  
                  
                  
                  
                                    
                  
                  
                  
                  
                </td>
              </tr>
            </table>
            
            
            
            
            
            
            
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            
            
            
            
            <?php 
			
			if($count_info){ //only show this if info sumbitted			
			//if(1==1){
			
			?>
            
            <table style=" padding:10px 0 0px 0; " id="input">
                 	 <tr>
                        <td>
                        	<div style="font-weight: bold; padding:0 0 5px 0;">
                            
                            	
                                ข้อมูลจากสถานประกอบการ
                            
                            
                            </div>
                            
                            </td>
                      </tr>
                      
                      
                      <tr>
                            <td>
                            
                            <form action="?id=<?php echo $this_id;?>&focus=input" method="post">
                            <table>
                            	<tr>
                                  <td colspan="2">ข้อมูลประจำปี
                                  <?php
                              		
									//print_r($_POST);
							  		include "ddl_year_auto_submit_lawful.php";
							  
							  	?>
                              	</td>
                                </tr>
                            </table>
                          </form>
                            
                            
                            </td>
                      </tr>
                      
                      
                      
                      
                      <tr>
                      	<td>
                        
                        
                        
                        	 <table border="0" style="margin: 5px 0 15px 30px;">
                               <tr>
                               
                               
                               
                               
                               
                               
                               
                               
                                
                                  <td bgcolor="#fcfcfc">จำนวน<?php echo $the_employees_word;?>: </td>
                                  <td>
                                  
                                  <?php 
                                  
								  
									
									//override it lawful value
									$company_employees = getFirstItem("select 
															Employees 
														from 
															lawfulness_company 
														where 
															LID = '" . $lawful_values["LID"] . "'");
													
									//if have value then override it		
									if($company_employees){
										$lawful_values["Employees"] = $company_employees;
									}
														
                                  
                                    if($lawful_values["Employees"]){
                                        //have lawful value, use lawful value
                                        $employee_to_use = $lawful_values["Employees"];
                                        
                                    }else{
                                    
                                        //didn't have lawful value, use ORG's value
                                        if($sum_employees){
                                            //if have branch, use employees from all branch
                                            $employee_to_use = $sum_employees;
                                            
                                        }else{
                                        
                                            //else, just use ORG's employees
                                            $employee_to_use = $output_values["Employees"];
                                        
                                        }
                                    }
                                    
                                    ?>
                                  
                                 <strong><?php echo formatEmployee($employee_to_use);?></strong>  คน
                                   
                                  </td>
                                </tr>
                                
                                <tr>
                                  <td bgcolor="#fcfcfc" style="padding-right:20px;">อัตราส่วน<?php echo $the_employees_word;?>ต่อคนพิการ: </td>
                                  <td><?php 
                                  
                                    //what ratio to use?
                                    $ratio_to_use = default_value(getFirstItem("select var_value 
                                                        from vars where var_name = 'ratio_$this_lawful_year'"),100);
                                    
                                    //$half_ratio_to_use = $ratio_to_use/2;
                                    
                                    echo ($ratio_to_use);
                                    
                                  
                                  ?>:1 = <strong id="employee_ratio"><?php 
                                    //if employee > 200
                                    
                                    
                                    
                                    $final_employee = getEmployeeRatio($employee_to_use,$ratio_to_use);
                                    
                                    echo formatEmployee($final_employee);
                                    
                                    ?></strong> คน</td>
                                </tr>
                                
                                
                                                                
                              </table>
                              
                        
                        
                        
                        </td>
                     </tr>
                     
                     <tr>
                    	<td>
                             <hr />
                             <span style="font-weight: bold;color:#006600;">
                                             
                                             	มาตรา 33 จ้างคนพิการเข้าทำงาน
                                           
                                            </span>
                       </td>                
					</tr>   
                    
                    
                    
                    
                     <tr>
                    	<td>
                        	
                            
                            	 <table border="0">
                                          
                                            
                                           
                                              <tr>
                                              <td>จำนวนคนพิการที่ทำงานในปัจจุบัน</td>
                                              <td>
                                              
                                              <?php 
											  
											  
													
												$hire_numofemp = getFirstItem("
													SELECT 
														count(*)
													FROM 
														lawful_employees_company
													where
														le_cid = '$this_id'
														and le_year = '$this_lawful_year'");/**/
													
												
												
												
												if($hire_numofemp == 0 && $sess_accesslevel != 4){
												
													//no "real" value, use thos that is in lawfulness instead
													$hire_numofemp = $lawful_values["Hire_NumofEmp"];
													
												}
												
											   ?>
                                              
                                               <strong><?php echo default_value($hire_numofemp,0);?></strong>
                                              
                                              
                                             
                                              
                                              
                                              คน 
                                              
                                              
                                            
                                              
                                              </td>
                                            </tr>
                                            
                                            
                                            <tr>
                                              <td>ผู้พิการใช้สิทธิมาตรา 35</td>
                                              <td>
                                              
                                              
                                              <?php
											  	
													
													//curator user are person OR disabled person who is the "top" level
													
													//$curator_table_name is from file "scrp_add_curator"
													
													
													$curator_user = getFirstItem("select 
															count(*) 
														from 
															curator_company 
															
														where 
														
															curator_lid = '".$lawful_values["LID"]."'
																
																
															and 
															
															curator_parent = 0
															
															
															");	
													
													
													
											  ?>
                                              <strong><?php echo $curator_user;?></strong>
                                              
                                               
                                              คน
                                            </tr>
                                            
                                            
                                             <tr>
                                              <td>จำนวนคนพิการที่รับเพิ่ม</td>
                                              <td>
                                              
                                              <b>
                                              <?php 
											  
											  	$extra_emp = $final_employee - $hire_numofemp - $curator_user;
												
												if($extra_emp < 0){
													$extra_emp = 0;
												}
												
											    echo formatEmployee(default_value($extra_emp,"0"));
											  ?>
                                              </b>
                                                                                          
                                             
                                              คน
                                            </tr>
                                            
                                            
                                            
                                            
                                            
                                            
                                            </table>
                                            
                        
                        
                        
                        
                        			<!------------ DETAILS TABLE -------------->
                        			 <table>
                                         <tr>
                                            <td bgcolor="#efefef" colspan="9">
                                            <strong>ข้อมูลคนพิการที่ได้รับเข้าทำงาน</strong>
                                            </td>
                                        </tr>
                                            
                                             <?php
                        
                            
                                            
                                                
                                                $get_org_sql = "SELECT *
                                                                FROM 
                                                                
                                                                lawful_employees_company
                                                                
                                                                where
                                                                    le_cid = '$this_id'
                                                                    and le_year = '$this_lawful_year'
                                                                order by le_id asc
                                                                ";
                                                
                                            
                                            
                                            //echo $get_org_sql;
                                            $org_result = mysql_query($get_org_sql);
                                            $total_records = 1;
                                            while ($post_row = mysql_fetch_array($org_result)) {
                                        
                                                if($total_records == 1){
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
                                                 
                                                </tr>
                                                
                                                <?php
                                                
                                                }											
                                            
                                            ?>     
                                        <tr>
                                          <td valign="top"><div align="center"><?php echo $total_records;?></div></td>
                                          <td valign="top"><?php echo doCleanOutput($post_row["le_name"]);?></td>
                                          <td valign="top"><?php echo formatGender($post_row["le_gender"]);?></td>
                                          <td valign="top"><?php echo doCleanOutput($post_row["le_age"]);?></td>
                                          <td valign="top">
                                          <?php echo doCleanOutput($post_row["le_code"]);?>
                                          
                                             
                                          
                                          </td>
                                          <td valign="top"><?php echo doCleanOutput($post_row["le_disable_desc"]);?></td>
                                          <td valign="top"><?php echo formatDateThai($post_row["le_start_date"],0);?></td>
                                          
                                          <td valign="top"><div align="right">
                                          
                                          <?php echo formatNumber($post_row["le_wage"]);?>
                                          
                                          
                                          <?php echo getWageUnit($post_row["le_wage_unit"]);?>
                                          
                                          </div></td>
                                          
                                          <td valign="top"><?php echo doCleanOutput($post_row["le_position"]);?></td>
                                         
                                        
                                          
                                          
                                        </tr>
                                        <?php 
                                            $total_records++;
                                            
                                            //END LOOP TO CREATE LAWFUL EMPLOYEES
                                             
                                            }?>
                                        
                                        
                                  	 </table>
                        
                        
                        
                        
                        </td>
                    </tr>    
                    
                    
                    
                    <!----- END ROW FOR lawful_employees----->
                    
                    
                    
                    
                    <!----- START ROW for CURATOR ----->
                    
                    <tr>
                      
                     		 <td>
                             
                             <hr />
                             
                             
                             <span style="font-weight: bold;color:#006600;">
                                             
                                             	มาตรา 35 จ้างคนพิการเข้าทำงาน
                                           
                                            </span></td>
                                            
                                            
						</tr>        
                        
                        
                        
                        
                        
                        <tr>
                        	<td>
                            
                            
                             - มีผู้ใช้สิทธิ: <strong><?php 
							
									
									//$curator_table_name is from scrp_add_curator.php
							
									$curator_user = getFirstItem("
																			
																	select count(*) 
																	from 
																	
																	curator_company
																	
																	where 
																	curator_lid = '".$lawful_values["LID"]."' and curator_parent = 0
																	
																
																");
							
							
									echo $curator_user;
							
								?></strong> คน, ผู้พิการถูกใช้สิทธิ: <strong><?php 
							
									
									//$curator_usee is disabled person who has parents (right is used by someone else)
									//disable whose rights is not used by someone else is not $curator_usee
									
									$curator_usee = getFirstItem("
												
														select count(*) 
														from 
														curator_company
														where 
														curator_lid = '".$lawful_values["LID"]."' and curator_parent > 0
									
														");
													
									echo $curator_usee;
							
								?></strong> คน
                            
                            
                            	 <hr />
  
  
  								<!----------------- START TABLE FOR CURATOR DETAILS ------------>
                                <table>
                                
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
                               
                                <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_event_desc"]);?></td>
                               
                             
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
                                                       
                            
                            </td>
                       </tr>
                            
                            
                         <!--------- END ROW FOR CURATOR ---------->   
                         
                         
                         
                         
                         
                         
                         
                         
                         
                         
                         
                         
                         <?php 
				 
				 
				 //////////
				 //
				 //
				 //		20140220 - get payment detail
				 //
				 //
				 //////////
				 
				 
				 $company_payment_row = getFirstRow("
				 
											select
												*
											from
												payment_company
											where
												 CID = '$this_id'
												 and Year = '$this_lawful_year'
				 
											");
				 
				 
				 ?>
                         
                         
                         
                         
                 <!----- START ROW for PAYMENT ----->
                    
                   	 <tr <?php if(!$company_payment_row){?>style="display: none;"<?php }?> >
                      
                     		 <td>
                             
                             <hr />
                             
                             
                             <span style="font-weight: bold;color:#006600;">
                                             
                                             	มาตรา 34 ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ
                                           
                                            </span>
                                            
                                            
                             
                             
                             
                             
                             
                             
                             
                             
                             
                             
                             <!----- START PAYMENT DETAIL ----->
                             
                             
                             
                             
                             <table cellpadding="3" style="margin:0px 0 0 20px;" >
                                      
                                                <tr>
                                                  <td>วันที่ชำระเงิน</td>
                                                  <td>
                                                  
                                                  <?php echo formatDateThai($company_payment_row["PayDate"]);?>
                                                  
                                                  
                                                    </td>
                                                  <td>&nbsp;</td>
                                                  <td>&nbsp;</td>
                                                  <td></td>
                                              </tr>
                                                <tr>
                                                  <td>จำนวนเงินที่ชำระ</td>
                                                  <td colspan="3"><?Php echo formatNumber($company_payment_row["Amount"]);?>
                                                     บาท </td>
                                                  
                                              </tr>
                                              <!--<tr>
                                                  <td>ดอกเบี้ย</td>
                                                  <td colspan="3"><?Php echo formatNumber(789);?>
                                                     บาท </td>
                                                  
                                              </tr>-->
                                              
                                              <tr>
                                                  <td>จ่ายโดย</td>
                                                  <td colspan="3">
                                                  
                                                  <strong><?php 
												  
												  echo formatPaymentName($company_payment_row["PaymentMethod"]);
												  
												  ?></strong>
                                                  
                                                  </td>
                                                  
                                              </tr>
                                              
                                              
                                              
                                              
                                              <?php if($company_payment_row["PaymentMethod"] == "Cheque"){?>
                                              
                                              
                                                    <tr>
                                                                <td colspan="4">
                                                              
                                                                <table id="cheque_table" border="0" style="padding:0px 0 0 50px;"  >
                                                                  <tr>
                                                                    <td><span style="font-weight: bold">ข้อมูลการจ่ายเช็ค</span></td>
                                                                    <td>&nbsp;</td>
                                                                  </tr>
                                                                  <tr>
                                                                    <td><span class="style86" style="padding: 10px 0 10px 0;">ธนาคาร</span></td>
                                                                    <td><?php echo getFirstItem("select bank_name from bank where bank_id = '".$company_payment_row["bank_id"]."'");?>                                       </td>
                                                                  </tr>
                                                                  <tr>
                                                                    <td><span class="style86" style="padding: 10px 0 10px 0;">เลขที่เช็ค</span></td>
                                                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                                                      <?php echo $company_payment_row["RefNo"]?>
                                                                    </span></td>
                                                                  </tr>
                                                                  
                                                                  <tr>
                                                                    <td>ลงวันที่</td>
                                                                    <td>
                                                                    <?php echo formatDateThai($company_payment_row["PaymentDate"]);?>
                                                                    </td>
                                                                    
                                                                  </tr>
                                                                </table>
                                                                
                                                                
                                                              
                                                              
                                                              </td>
                                                          </tr>
                                              
                                              
                                              
                                              
                                              
                                              <?php }?>
                                              
                                              
                                              <?php if($company_payment_row["PaymentMethod"] == "Note"){?>
                                              		 <tr>
                                                            <td colspan="4">
                                                            
                                                            
                                                            
                                                           
                                                              
                                                              
                                                                <table id="cheque_table" border="0" style="padding:0px 0 0 50px;"  >
                                                                  <tr>
                                                                    <td><span style="font-weight: bold">ข้อมูลการจ่ายธนาณัติ</span></td>
                                                                    <td>&nbsp;</td>
                                                                  </tr>
                                                                 
                                                                  <tr>
                                                                    <td><span class="style86" style="padding: 10px 0 10px 0;">เลขที่ธนาณัติ</span></td>
                                                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                                                      <?php echo $company_payment_row["RefNo"]?>
                                                                    </span></td>
                                                                  </tr>
                                                                  
                                                                  <tr>
                                                                    <td>ลงวันที่</td>
                                                                    <td>
                                                                    <?php echo formatDateThai($company_payment_row["NoteDate"]);?>
                                                                    </td>
                                                                    
                                                                  </tr>
                                                                </table>
                                                                
                                                                
                                                              
                                                              
                                                              </td>
                                                          </tr>
                                                          
                                                 <?php }?>
                                              
                                             </table>
                             
                             
                             
                             
                             
                             <!------ END PAYMENT DETAIL ------>
                             
                             
                                            
                                            
                                            
                                            
                                            
                             </td>
                                            
                                            
						</tr>        
                         
                         
                         
                         
                         
                         <!--------- END ROW FOR PAYMENT ---------->  
                         
                         
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                    <!----- START ROW for FILES ----->
                    
                   	 <tr>
                      
                     		 <td>
                             
                             <hr />
                             
                             
                             <div style="font-weight: bold;color:#006600; padding-bottom:10px;">
                                             
                                              	เอกสารเพิ่มเติม	
                                           
                           </div>
                                            
                          
                            
                            
                           <div style="width:400px; padding-bottom:5px;">
							<?php 
                                
                                //do $this_id swap thing because doc link use LID, but consume $this_id
                                //but $this_id on this page is CID and not LID...
                                $this_id_temp = $this_id;
                                $this_id = $lawful_values["LID"];
                                
                                $file_type = "company_docfile";
                            
                                include "doc_file_links.php";
                                
                                $this_id = $this_id_temp;
                                
                            ?>
                            </div>
                             
                             
                             </td>
                             
                             
                    </tr>
                    
                   
                   
                    <!----- END ROW for FILES ----->
                   
                   
                   
                   
                   
                   
                   
                   
                   <!----- START ROW for REMARKS ----->
                    
                   	 <tr>
                      
                     		 <td>
                             
                             <hr />
                             
                             
                             <div style="font-weight: bold;color:#006600; padding-bottom:10px;">
                                             
                                              	หมายเหตุเพิ่มเติม	
                                           
                           </div>
                                            
                          
                            
                            
                            <?php echo getFirstItem("
							
							
										select
															lawful_remarks
														from
															lawfulness_company
														where
															 CID = '$this_id'
                                                             and Year = '$this_lawful_year'
							
							
									");?>
                             
                             
                             </td>
                             
                             
                    </tr>
                    
                   
                   
                    <!----- END ROW for REMARKS ----->
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                    
                    
                    
                    <tr>
                    	<td>
                        
                        <hr />
                        
                        <div align="center">
                        
                        
                        
                        <table>
                        	<tr>
                            	<td>
                                
                                <?php if( getFirstItem("
							
							
										select
															lawful_submitted
														from
															lawfulness_company
														where
															 CID = '$this_id'
                                                             and Year = '$this_lawful_year'")==1){							
							
									?>
                                
                                
                                <form method="post" action="scrp_transfer_data.php">
                                
                                        <input name="" type="submit" value="บันทึกข้อมูลจากสถานประกอบการ"
                                        
                                         onclick = "return confirm('ต้องการบันทึกข้อมูลจากสถานประกอบการนี้?');"
                                        
                                        />
                                                                        
                                        <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                        <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                        <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
                                    
                                </form>
                                
                                
                                <?php }?>
                                
                                
                                </td>
                                <td>
                        
                        
                        		<?php if($this_lawful_year == 2013 && $output_values["Province"] == 1){?>
                                
                        		<form method="post" action="./tcpdf/bangkok_56_pdf.php" target="_blank">
                            
                                    <input name="" type="submit" value="พิมพ์แบบฟอร์มเป็น pdf" />
                                                                    
                                    <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                    <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                    <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
                                
	                            </form>
                                
                                <?php }?>
                            
                                
                               </td>
                           </tr>
                       </table>
                               
                                                       
                       
                        
                        
                        </div>
                        
                        
                        </td>
                    </tr>
                      
  
                    
</table><!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
<?php }//end if($count_info){ ?>            
            
            
                    
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




  


<div id="employees_popup" style=" position:absolute; padding:3px; background-color:#006699; width: 500px; display:none; " >

	<form  method="post" enctype="multipart/form-data" action="scrp_update_lawful_employees.php"><!--- curator information just get posted into this page-->
	<table  bgcolor="#FFFFFF" width="500" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;  ">
    
    	<tr>
            <td colspan="2">
                    <div style="font-weight: bold;color:#006600;  " >
                    ปรับปรุงจำนวน<?php echo $the_employees_word;?>สำหรับมาตรา 33
                    </div> 
				</td>
        </tr>
    
    	<tr>
        	<td>
            จำนวน<?php echo $the_employees_word;?>: 
            </td>
            <td>
            <input name="update_employees" id="update_employees" style="width:50px" type="text" value="<?php echo formatEmployee($employee_to_use);?>" onchange="addEmployeeCommas('update_employees');"  /> คน
            </td>
        </tr>
        
        <tr>
            <td colspan="2">
            	<div align="center">
                   <input name="" type="submit" value="ปรับปรุงข้อมูล"/>
                   <input name="" type="button" onClick="fadeOutMyPopup('employees_popup'); return false;" value="ปิดหน้าต่าง"/>
                   
                  	<input name="LID" type="hidden" value="<?php echo $lawful_values["LID"];?>" />
                    
                    <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                    <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />
                  
                </div>
			</td>
        </tr>
        
    	
    </table>

</form>
</div>      



 <div id="35_popup" style=" position:absolute; padding:3px; background-color:#006699; width: 1000px; display:none;   " >
  
 
 	<form id="curator_form" name="curator_form"  method="post" enctype="multipart/form-data" ><!--- curator information just get posted into this page-->
	<table  bgcolor="#FFFFFF" width="1000" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;  ">
    
    <tr>
    	<td>
        
        <div align="center">
        
        <table width="700">
        
        
        	<tr>
            	<td colspan="4">
                    <div style="font-weight: bold;color:#006600; padding-bottom:15px; " >
                    <?php //if($sess_accesslevel ==4){
                     if(1==1){?>
                        มาตรา 35 ให้สัมปทานฯ
                     <?php }else{ ?>
                        ปฏิบัติตามมาตรา 35
                     <?php } ?> </div> 
				</td>
                <td>
                	<div align="right">
                	<a href="#" onClick="fadeOutMyPopup('35_popup'); return false;">ปิดหน้าต่าง X </a>
                    </div>
                </td>
            </tr>                     
        
        </table>
        
        
        <?php 
				if(is_numeric($_GET["curator_id"]) && !$_POST["do_cancel_edit"]){
		
					//pre-fill curator
					$is_edit_curator = 1;
					
					
					
					$curator_id_to_fill = $_GET["curator_id"];
					
					$the_sql = "select * from curator where curator_id = '$curator_id_to_fill'";
					
					$curator_row_to_fill = getFirstRow($the_sql);
					
					//echo $the_sql;
					
					//echo $curator_row_to_fill[0];
					
					if($curator_row_to_fill["curator_parent"] == 0){
						$is_curator_parent = 1;
					}
		
				}
		?>
        
         <div align="center">
        		 <table id="curator_input_forms" style="display: block;">
                        <tr bgcolor="#efefef">
                            <td colspan="10">
                                
                                <strong id="the_parent" style="display:none;">เพิ่มผู้ใช้สิทธิ</strong>        
                                <strong id="the_child" style="display:none;">เพิ่มผู้ถูกใช้สิทธิ</strong>        
                                
                                
                                </td>
                        </tr>
                        
                    <tr>
                            <td>
                            
                                เลขที่บัตรประชาชน        </td>
                            <td>
                            
                                <input type="text" name="curator_idcard___" id="curator_idcard____" maxlength="13"
                                
                                value="<?php echo $curator_row_to_fill["curator_idcard"];?>" style="display:none;"
                                
                                 />
                                 
                                 <?php 
								 	$id_form_name = "curator_form";
									$id_form_to_show = $curator_row_to_fill["curator_idcard"];
									
									//echo $id_form_to_show;
								 
								 ?>
                                 
                                 <?php include "txt_id_card.php";?> *
                                 
                                 
                                 
                                
                                <?php if($sess_accesslevel != 5 && $sess_accesslevel != 4 ){//company and exec can't do all these?>
	                                <input name="btn_get_curator_data" type="submit" value="ดึงข้อมูล" 
                                    
                                    onclick="return validateCuratorPersonCode(document.getElementById('curator_form'));"
                                    />        
                                <?php }?>
                                
                                
                                <script>
								
									function validateCuratorPersonCode(frm) {
								
										var checkOK = "1234567890";
							
										<?php for($i=1;$i<=13;$i++){?>
										if(frm.id_<?php echo $i;?>.value.length < 1)
										{
											alert("กรุณาใส่ข้อมูล: เลขที่บัตรประชาชน");
											frm.id_<?php echo $i;?>.focus();
											return (false);
										}
										
										var checkStr = frm.id_<?php echo $i;?>.value;
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
										 alert("เลขบัตรประชาชนต้องเป็นตัวเลขเท่านั้น");
										 frm.id_<?php echo $i;?>.focus();
										 return (false);
									   }
										<?php }?>
									
										return true;
									
									}
								
								
								</script>
                                </td>
                            <td>
                            
                                ชื่อ-นามสกุล        </td>
                            <td>
                            
                                <input type="text" name="curator_name" id="curator_name" 
                                
                                 value="<?php echo $curator_row_to_fill["curator_name"];?>"
                                
                                />    *    </td>
                        </tr>
                        
                        
                        <tr>
                            <td>
                            
                                เพศ        </td>
                            <td>
                            
                                <select name="curator_gender" id="curator_gender">
                                    <option value="m" 
                                    
                                    <?php if($curator_row_to_fill["curator_gender"] == "m"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >ชาย</option>
                                    <option value="f"
                                    
                                     <?php if($curator_row_to_fill["curator_gender"] == "f"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >หญิง</option>
                                  </select>    *    </td>
                            <td>
                            
                                อายุ        </td>
                            <td>
                            
                                <input name="curator_age" type="text" id="curator_age" size="10"  value="<?php echo $curator_row_to_fill["curator_age"];?>" />       </td>
                        </tr>
                        
                        
                        
                         <tr>
                            <td>
                            
                                <span id="curator_is_disable_text">ผู้ใช้สิทธิเป็น</span>
                                
                                </td>
                            <td colspan="3">
                            <div id="curator_is_disable">
                                <input name="curator_is_disable" type="radio" value="0" onClick="document.getElementById('tr_curator_disable').style.display = 'none';" checked="checked" 
                                
                                <?php if($curator_row_to_fill["curator_is_disable"] == "0"){echo 'checked="checked"';}?>
                                
                                
                                /> ผู้ดูแลคนพิการ
                                   
                                <input name="curator_is_disable" type="radio" value="1" onClick="document.getElementById('tr_curator_disable').style.display = '';"
                                
                                <?php if($curator_row_to_fill["curator_is_disable"] == "1"){?>
                                checked="checked"
                                <?php }?>
                                
                                /> คนพิการ
                                
                                
                            </div>
                            </td>
                           
                        </tr>
                        
                         <tr id="tr_curator_disable">
                          <td valign="top">ลักษณะความพิการ</td>
                          <td colspan="3"><?php 
						  	
							$do_hide_blank_dis = 1; 
							
							include "ddl_disable_type.php";
							
							?></td>
                        </tr>
                        
                       
                        
                        
                        <tr>
                            <td>
                            
                            	<span id="curator_start_date_text">วันเริ่มต้นสัญญา</span>
                                
                                </td>
                            <td>
                            <?php
											   
							   $selector_name = "curator_start_date";
							   $this_date_time = $curator_row_to_fill["curator_start_date"];
							   
							   include ("date_selector.php");
							   
							   ?> *
                                </td>
                            <td>
                            	<span id="curator_end_date_text">
                            	วันสิ้นสุดสัญญา</span>
                            </td>
                            <td>
                            
                            	 <?php
											   
							   $selector_name = "curator_end_date";
							   $this_date_time = $curator_row_to_fill["curator_end_date"];
							   
							   include ("date_selector_plus_ten.php");
							   
							   //reset this_date_time just in case
							   $this_date_time = "0000-00-00";
							   
							   ?> *
                            
                              </td>
                        </tr>
                        
                        
                        <tr id="tr_curator_event" style="display: none;">
                          <td valign="top">กิจกรรมตามมาตรา 35</td>
                          <td >
                          
                          	<select name="curator_event" id="curator_event">
                                    <option value="การให้สัมปทาน" 
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "การให้สัมปทาน"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >การให้สัมปทาน</option>
                                    <option value="จัดสถานที่จำหน่ายสินค้าหรือบริการ"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "จัดสถานที่จำหน่ายสินค้าหรือบริการ"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >จัดสถานที่จำหน่ายสินค้าหรือบริการ</option>
                                    <option value="จัดจ้างเหมาช่วงงาน"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "จัดจ้างเหมาช่วงงาน"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >จัดจ้างเหมาช่วงงาน</option>
                                    <option value="ฝึกงาน"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "ฝึกงาน"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >ฝึกงาน</option>
                                    <option value="การให้ความช่วยเหลืออื่นใด"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "การให้ความช่วยเหลืออื่นใด"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >การให้ความช่วยเหลืออื่นใด</option>
                            </select>
                                                    </td>
                                                    
                            
                            <td><div align="right">มูลค่า</div></td>
                            <td><input name="curator_value" id="curator_value" style="text-align:right;"  type="text" size="10" 
                            
                             value="<?php echo formatMoney($curator_row_to_fill["curator_value"]);?>"
                            
                            onChange="addCommas('curator_value');"/> บาท </td>
                        </tr>
                        
                        <tr id="tr_curator_event_2" style="display: none;" >
                          <td valign="top">รายละเอียด</td>
                          <td colspan="3">
                          
                          	
                            <textarea name="curator_event_desc" cols="40" rows="4"><?php echo $curator_row_to_fill["curator_event_desc"];?></textarea>                          </td>
                        </tr>
                        
                        <tr id="tr_curator_docfile">
                         <td valign="top">เอกสารประกอบ</td>
                          <td colspan="3">
							<input type="file" name="curator_docfile" id="curator_docfile" />
                            </td>
                        
                        </tr>
                        
                        
                        
                       
                        
                        
                        <tr>
                            <td colspan="4">
                                <div align="center">
                                
                                
                                <input name="year_curator" type="hidden" value="<?php echo $this_lawful_year;?>" />
                                 
                                 
                                 <?php if($sess_accesslevel != 5){//exec can't do all these?>
                                 
                                 	 <?php if($is_edit_curator){?>
		                               <input name="do_add_curator" type="submit" value="แก้ไขข้อมูล" />
                                       <input name="do_cancel_edit" type="submit" value="ยกเลิกการแก้ไข" />
                                     <?php }else{ ?>
                                     
                                     	<input name="do_add_curator" type="submit" value="บันทึก" style="font-size:18px; font-weight: bold;" 
                                        
                                        
                                        onclick="return validateCuratorForm(document.getElementById('curator_form'));"
                                        
                                        />
                                     <?php }?>  
                                       
                                <?php }?>
                                <input name="" type="button" value="ปิดหน้าต่าง" onClick="fadeOutMyPopup('35_popup'); return false;" />
                                 
                                 <?php if($is_edit_curator){?>
                                 
                                  <input name="curator_id" type="hidden" value="<?php echo $curator_row_to_fill["curator_id"];?>" />
                                 <?php }?>
                                 
                                 <input name="curator_lid" type="hidden" value="<?php echo $lawful_values["LID"]; ?>" />
                                  
                                   <?php if($is_edit_curator){?>
                                  <input name="curator_parent" id="curator_parent" type="hidden" value="<?php echo $curator_row_to_fill["curator_parent"];?>" />
                                  <?php }else{?>
                                  <input name="curator_parent" id="curator_parent" type="hidden" value="0" />
                                  <?php }?>
                                  
                                  
                                </div>                            </td>
                        </tr>
                      </table>
        </div>
      
        
        
        
        
        </div>
        
        
        
       
        
        
        
        </td>
	</tr>
            
    
   
	</table>
    </form>
    
    <script language='javascript'>
						<!--
						function validateCuratorForm(frm) {
							
							
							var checkOK = "1234567890";
   							
							<?php for($i=1;$i<=13;$i++){?>
							if(frm.id_<?php echo $i;?>.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: เลขที่บัตรประชาชน");
								frm.id_<?php echo $i;?>.focus();
								return (false);
							}
							
							var checkStr = frm.id_<?php echo $i;?>.value;
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
							 alert("เลขบัตรประชาชนต้องเป็นตัวเลขเท่านั้น");
							 frm.id_<?php echo $i;?>.focus();
							 return (false);
						   }
							<?php }?>
							
							
							if(frm.curator_name.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อ-นามสกุล");
								frm.curator_name.focus();
								return (false);
							}
							if(frm.curator_age.value.length == 0)
							{
								//alert("กรุณาใส่ข้อมูล: อายุ");
								//frm.curator_age.focus();
								//return (false);
							}
							
							
							//age
							var checkOK = "1234567890";
							
							var checkStr = frm.curator_age.value;
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
							 alert("อายุต้องเป็นตัวเลขเท่านั้น");
							 frm.curator_age.focus();
							 return (false);
						   }
						   //end age
							
							
							//----
							if(frm.curator_start_date_day.selectedIndex == 0 || frm.curator_start_date_month.selectedIndex == 0 || frm.curator_start_date_year.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: วันเริ่มต้นสัญญา");
								//frm.CompanyTypeCode.focus();
								return (false);
							}
							if(frm.curator_end_date_day.selectedIndex == 0 || frm.curator_end_date_month.selectedIndex == 0 || frm.curator_end_date_year.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: วันสิ้นสุดสัญญา");
								//frm.CompanyTypeCode.focus();
								return (false);
							}
							
							if(frm.curator_value.value.length == 0 || frm.curator_value.value < 1)
							{
								//alert("กรุณาใส่ข้อมูล: มูลค่า");
								//frm.curator_value.focus();
								//return (false);
							}
							//----
							return(true);									
						
						}
						-->
					
					</script>
    
</div>                

<?php

?>










  
 <div id="my_popup" style="position: absolute;  padding:3px; background-color:#006699; width: 1000px; display:none;   " >
                                  <table  bgcolor="#FFFFFF" width="1000" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;  ">
                                  <script language='javascript'>
									<!--
									
									function doValidateId(){
									
									
										var the_id = document.getElementById('le_code').value;
									
										alert(the_id);
									
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
										 alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
										 document.getElementById('le_code').focus();
										 return (false);
									   }
										
										
										if(the_id.length != 13)
										{
											alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
											document.getElementById('le_code').focus();
											return (false);

										}
										
										//return true;
									
									
									}
									
									
									function doValidateEmployeeInfo(frm) {
										
										
										var checkOK = "1234567890";
										
										
										<?php for($i=1;$i<=13;$i++){?>
										if(frm.leid_<?php echo $i;?>.value.length < 1)
										{
											alert("กรุณาใส่ข้อมูล: เลขที่บัตรประชาชน");
											frm.leid_<?php echo $i;?>.focus();
											return (false);
										}
										
										var checkStr = frm.leid_<?php echo $i;?>.value;
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
										 alert("เลขบัตรประชาชนต้องเป็นตัวเลขเท่านั้น");
										 frm.leid_<?php echo $i;?>.focus();
										 return (false);
									   }
										<?php }?>
													
													
												   
										
										
										
										//----
										return(true);									
									
									}
									-->
								
								</script>
                                    <form name="le_form" method="post" action="scrp_add_lawful_employee.php" onSubmit="return doValidateEmployeeInfo(this);">
                                    <tr bgcolor="#efefef">
                                    	<td colspan="11">
                                        <strong>ข้อมูลคนพิการที่ได้รับเข้าทำงาน</strong>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                      <td colspan="11">
                                      	<table border="0" align="center" bgcolor="#FFFFFF">
                                        	<tr>
                                            	<td colspan="2">
                                                <?php 
													if($_GET["delle"]=="delle" ){
												?>							
													 <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ข้อมูลได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
												<?php
													}					
												?>
                                                <?php 
													if($_GET["le"]=="le" ){
												?>							
													 <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มข้อมูลเรียบร้อย</div>
												<?php
													}					
												?>
                                                </td>
                                            </tr>
                                          <tr>
                                            <td>
                                            
                                             <?php
											
												if(is_numeric($_GET["leid"])){
												
													//if have leid then populate defaul value.....
													
													if($sess_accesslevel == 4){
													
														$leid_row = getFirstRow("select 
																* 
																from 
																lawful_employees_company
																where le_id = '".doCleanInput($_GET["leid"])."'");
															
													}else{
														
														$leid_row = getFirstRow("select 
																* 
																from 
																lawful_employees 
																where le_id = '".doCleanInput($_GET["leid"])."'");

													}
												
													
												
												}
											
											?>
                                            
                                            เลขที่บัตรประชาชน</td>
                                            <td>
                                            <input name="le_id" type="hidden" value="<?php echo doCleanInput($_GET["leid"]);?>" />
                                            
                                            
                                            <?php 
												$id_form_name = "le_form";
												$id_form_to_show = $leid_row["le_code"];
												
												$txt_id_card_prefix = "le";
												
												include "txt_id_card.php";
												
												$txt_id_card_prefix = "";
											?>
                                            
                                            <input type="text" name="le_code" id="le_code" style="display: none;" maxlength="13" value="<?php echo $leid_row["le_code"]?>"  />
                                            
                                            
                                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 4 ){//company and exec can't do all these?>
                                            <input id="btn_get_data" type="button" value="ดึงข้อมูล" onClick="return doGetData();" />
                                            <?php }?>
                                            
                                            <img id="img_get_data" src="decors/loading.gif" width="10" height="10" style="display:none;" />
                                            
                                            <script>
											
												function doGetData(){
												
													var the_id = "";
													
													//
													<?php for($i=1;$i<=13;$i++){?>
													the_id = the_id + document.getElementById('leid_<?php echo $i;?>').value;
													<?php }?>
												
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
													 alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
													 document.getElementById('leid_1').focus();
													 return (false);
												   }
													
													
													if(the_id.length != 13)
													{
														alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
														document.getElementById('leid_1').focus();
														return (false);
													}
												
													//alert("do get data");
													document.getElementById('btn_get_data').style.display = 'none';
													document.getElementById('img_get_data').style.display = '';
													
													
													
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
													http_request.open('POST', "./ajax_get_des_person.php", true);
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
															document.getElementById('btn_get_data').style.display = '';
															document.getElementById('img_get_data').style.display = 'none';
														
															//alert(http_request.responseText);
															//return false;
															
															if(http_request.responseText == "no_result"){
															
																alert("ไม่พบข้อมูลคนพิการ");
																//no result
																//document.getElementById("none_to_rate").style.display = "block";
																//document.getElementById("have_to_rate").style.display = "none";
																//document.getElementById("rate_me_table").style.display = "none";
																
															}else{
															
																var JSONFile = http_request.responseText;  
																eval(JSONFile); 										
																//alert(someVar.color); // Outputs 'blue' 
																
																//alert(someVar.DEFORM_ID);
																
																document.getElementById('le_full_name').value =  someVar.PREFIX_NAME_ABBR + someVar.FIRST_NAME_THAI + " " + someVar.LAST_NAME_THAI;
																if(someVar.SEX_CODE == 'M'){
																	document.getElementById('le_gender').selectedIndex  = 0;
																}
																if(someVar.SEX_CODE == 'F'){
																	document.getElementById('le_gender').selectedIndex  = 1;
																}
																
																
																if(someVar.DEFORM_ID == 1 || someVar.DEFORM_ID == 6 || someVar.DEFORM_ID == 12){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 1;
																}
																if(someVar.DEFORM_ID == 2 || someVar.DEFORM_ID == 7 || someVar.DEFORM_ID == 13){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 2;
																}
																if(someVar.DEFORM_ID == 3 || someVar.DEFORM_ID == 8 || someVar.DEFORM_ID == 14){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 3;
																}
																if(someVar.DEFORM_ID == 4 || someVar.DEFORM_ID == 9 || someVar.DEFORM_ID == 15){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 4;
																}
																if(someVar.DEFORM_ID == 5 || someVar.DEFORM_ID == 10 || someVar.DEFORM_ID == 16){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 5;
																}
																if(someVar.DEFORM_ID == 6 || someVar.DEFORM_ID == 11 || someVar.DEFORM_ID == 17){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 6;
																}
																if(someVar.DEFORM_ID == 18){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 7;
																}
																
																document.getElementById('le_age').value = someVar.BIRTH_DATE;
															
															}
															//
															
														} else {
															alert('การเชื่อมต่อผิดพลาด โปรดลองอีกครั้ง');
														}
													}
												
												}
											
											</script>
                                            
                                            </td>
                                            
                                            
                                            
                                            
                                            <td class="td_left_pad">
                                            
                                           
                                            
                                            
                                            ชื่อ-นามสกุล</td>
                                            <td><label>
                                              <input type="text" name="le_name" id="le_full_name" value="<?php echo $leid_row["le_name"]?>" />
                                            </label></td>
                                          </tr>
                                          <tr>
                                            <td>เพศ</td>
                                            <td><label>
                                              <select name="le_gender" id="le_gender">
                                                <option value="m" <?php if($leid_row["le_gender"]=="m"){?>selected="selected"<?php }?>>ชาย</option>
                                                <option value="f" <?php if($leid_row["le_gender"]=="f"){?>selected="selected"<?php }?>>หญิง</option>
                                              </select>
                                            </label></td>
                                            <td class="td_left_pad">อายุ</td>
                                            <td><input name="le_age" type="text" id="le_age" size="10" value="<?php echo $leid_row["le_age"]?>"/></td>
                                          </tr>
                                          <tr>
                                            <td>ลักษณะความพิการ</td>
                                            <td><?php 
											
												$dis_type_suffix = "_hire";
												include "ddl_disable_type.php";
												$dis_type_suffix = "";
												
												?></td>
                                            <td class="td_left_pad">เริ่มบรรจุงาน</td>
                                            <td>
                                            
                                            <?php
											
											$selector_name = "le_date";
											
											if($leid_row["le_start_date"]){
												$this_date_time = $leid_row["le_start_date"];
											}
											
											include ("date_selector_employee.php");
											
											?>
                                            
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>ค่าจ้าง</td>
                                            <td><input name="le_wage" type="text" id="le_wage" size="10"  style="text-align:right;" onChange="addCommas('le_wage');" value="<?php echo formatMoney($leid_row["le_wage"])?>"/> <?php
								  	
												include "js_format_currency.php";
											  
											  ?>
                                              
                                              <select name="le_wage_unit" id="le_wage_unit">
                                              	
                                                <option <?php if($leid_row["le_wage_unit"] == 0){?>selected="selected"<?php }?> value="0">บาท/เดือน</option>
                                                <option <?php if($leid_row["le_wage_unit"] == 1){?>selected="selected"<?php }?> value="1">บาท/วัน</option>
                                                <option <?php if($leid_row["le_wage_unit"] == 2){?>selected="selected"<?php }?> value="2">บาท/ชม.</option>
                                              
                                              </select>
                                              
                                              </td>
                                            <td class="td_left_pad">ตำแหน่งงาน</td>
                                            <td><input type="text" name="le_position" id="le_position" value="<?php echo $leid_row["le_position"]?>"/></td>
                                          </tr>
                                          <tr>
                                            <td colspan="4"><div align="center">
                                            	<?php if($sess_accesslevel != 5){//exec can't do all these?>
                                                <input type="submit" name="button4" id="button4" value="<?php
                                                
													if($leid_row["le_id"]){
													
														echo "แก้ไขข้อมูล";
													
													}else{
														echo "เพิ่มข้อมูล";
													
													}
												
												?>" />
                                                <?php }?>
                                                <input name="" type="button" value="ปิดหน้าต่าง" onClick="fadeOutMyPopup('my_popup'); return false;" />
                                                
                                            </div></td>
                                          </tr>
                                          <input name="le_year" type="hidden" value="<?php echo $this_lawful_year;?>" />
                                          <input name="le_cid" type="hidden" value="<?php echo $this_id; ?>" />
                                         
                                    </form>
                                      </table>
                                      
                                      </td>
                                    </tr>
                                    
                                    
                                    
                                    <?php
					
						
										if($sess_accesslevel == 4){
											
											$get_org_sql = "SELECT *
															FROM 
															
															lawful_employees_company
															
															where
																le_cid = '$this_id'
																and le_year = '$this_lawful_year'
															order by le_id asc
															";
											
										}else{
						
											$get_org_sql = "SELECT *
															FROM 
															
															lawful_employees
															
															
															
															where
																le_cid = '$this_id'
																and le_year = '$this_lawful_year'
															order by le_id asc
															";
														
										}
										
										
										//echo $get_org_sql;
										$org_result = mysql_query($get_org_sql);
										$total_records = 1;
										while ($post_row = mysql_fetch_array($org_result)) {
									
											if($total_records == 1){
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
                                              <?php if($sess_accesslevel != 5 ){?>
                                              <td><div align="center">ลบข้อมูล</div></td>
                                              <td><div align="center">แก้ไขข้อมูล</div></td>
                                              <?php }?>
                                            </tr>
                                            
                                            <?php
											
											}											
										
										?>     
                                    <tr>
                                      <td valign="top"><div align="center"><?php echo $total_records;?></div></td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_name"]);?></td>
                                      <td valign="top"><?php echo formatGender($post_row["le_gender"]);?></td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_age"]);?></td>
                                      <td valign="top">
									  <?php echo doCleanOutput($post_row["le_code"]);?>
                                      
										  <?php 
                                          
                                            //see if this le_id already in another ID
                                            
                                            $this_le_code = $post_row["le_code"];
                                            $this_le_id = $post_row["le_id"];
                                            $this_le_cid = $post_row["le_cid"];
                                            $this_le_year = $post_row["le_year"];
                                            
                                            $sql = "select * from lawful_employees where le_code = '$this_le_code'
                                                        and le_id != '$this_le_id' and le_year = '$this_lawful_year'";
                                          
                                          
                                          
                                            $le_result = mysql_query($sql);
                                            
                                            while ($le_row = mysql_fetch_array($le_result)) {
                                        
                                          
                                          ?>
                                          
                                          <div>
                                            <a href="organization.php?id=<?php echo $le_row["le_cid"];?>&le=le&focus=lawful&year=<?php echo $le_row["le_year"];?>" style="color:#990000; text-decoration:underline;" target="_blank">! พบใน<?php echo $the_company_word;?>อื่น</a>
                                          </div>
                                          
                                          <?php }?>
                                          
                                          
                                          <?php 
                                            
											   
											  $sql = "select 
													* 
													from 
													curator a, lawfulness b
													
													where 
													a.curator_lid 	= b.LID
													
													and
													curator_idcard = '$this_le_code'
												
													and
													year = '$this_lawful_year'
												";
                                          
                                          	//echo $sql;
										  
                                            $le_result = mysql_query($sql);
                                            
                                            while ($le_row = mysql_fetch_array($le_result)) {
                                        
                                           
                                            
                                            $this_company_id = $le_row["CID"];
                                            $this_the_year = $le_row["Year"];
                                          
                                          ?>
                                          
                                          <div>
                                            <a href="organization.php?id=<?php echo $this_company_id;?>&curate=curate&focus=lawful&year=<?php echo $this_the_year;?>" style="color:#006600; text-decoration:underline;" target="_blank">! พบในมาตรา 35</a>
                                          </div>
                                          
                                          <?php }?>
                                      
                                      </td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_disable_desc"]);?></td>
                                      <td valign="top"><?php echo formatDateThai($post_row["le_start_date"],0);?></td>
                                      
                                      <td valign="top"><div align="right">
									  
									  <?php echo formatNumber($post_row["le_wage"]);?>
                                      
                                      
                                      <?php echo getWageUnit($post_row["le_wage_unit"]);?>
                                      
                                      </div></td>
                                      
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_position"]);?></td>
                                     
                                     <?php if($sess_accesslevel != 5 ){?>
                                          <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                                          
                                          
                                          <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leid=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล"><img src="decors/create_user.gif" alt="" border="0" /></a></div></td>
                                      <?php }?>
                                      
                                      
                                    </tr>
                                    <?php $total_records++;}?>
                                    
                                    <?php if($total_records == 1 && $sess_accesslevel != 4){?>
                                     <tr >
                                    	<td colspan="10">
                                        
		                                    <div align="center">
                                            	<form method="post" action="scrp_import_last_lawful_employee.php"  onsubmit="return confirm('ต้องการนำเข้าข้อมูลคนพิการที่ได้รับเข้าทำงานจากปีที่แล้วมาใส่ในปีนี้?');">
                                                	<input name="le_year" type="hidden" value="<?php echo $this_lawful_year;?>" />
                                         			<input name="le_cid" type="hidden" value="<?php echo $this_id; ?>" />
 		                                           	<input name="import_last_le" type="submit" value="นำเข้าข้อมูลจากปีที่แล้ว" />
                                              	</form>  
                                            </div>
                                    	</td>
                                     </tr>
                                    <?php }?>
                                    
                                  </table>
</div>
<script>

<?php if(($_GET["le"] == "le" || $_GET["delle"] == "delle") &&  !$_GET["auto_post"]){ ?>
fireMyPopup('my_popup',1020,160);
<?php }?>

<?php 
if($this_focus == "official" || $this_focus == "lawful" || $this_focus == "general" || $this_focus == "history" || $this_focus == "input"){
?>
	showTab('<?php echo $this_focus;?>');
<?php
}elseif($mode=="new"){
//if($mode=="new"){

?>

document.getElementById('lawful').style.display = 'none';

document.getElementById('history').style.display = 'none';

<?php if($sess_accesslevel !=4){ ?>
document.getElementById('official').style.display = 'none';
<?php } ?>

<?php

}else{
//if($mode=="new"){
?>


document.getElementById('lawful').style.display = 'none';

document.getElementById('general').style.display = 'none';

<?php if($sess_accesslevel !=4){ ?>
document.getElementById('official').style.display = 'none';
<?php } ?>


document.getElementById('input').style.display = 'none';



<?php
}
?>
</script>
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
  
   <?php if(($_GET["curate"] == "curate" || $curate == "curate") &&  !$_GET["auto_post"]){?>
	<script>
    
		
        //doPopSubCurator('3');
		//fireMyPopup('35_popup',1020,160);
   		// alert("what");
		//window.location = "view_curator.php?curator_id=<?php echo $curator_last_id;?>";
		
    </script>
    <?php }?>
    
    
    <?php if($is_edit_curator && $is_curator_parent){?>
		
		<script>        
            doPopSubCurator('0');
            // alert("what");
        </script>
    
    <?php }?>
    
    <?php if($is_edit_curator && !$is_curator_parent){?>
		
		<script>        
            doPopSubCurator('<?php echo $curator_row_to_fill["curator_parent"];?>');
            // alert("what");
        </script>
    
    <?php }?>
    
    
    
    
      <?php if($curator_row_to_fill["curator_is_disable"] == "1"){ //pre-fill curator information -> show this dropdown?>
     
        <script>
            document.getElementById('tr_curator_disable').style.display = '';
        </script>
     
     <?php }?>
    
    
    <?php if($_GET["auto_post"] ){ //auto-post -> show loading screen?>
        <div id="overlay"> 
           <div id="img-load" style="color:#FFFFFF; text-align:center">
           	<img src="./decors/bigrotation2.gif"  />
            
            <?php if($_GET["le"]){?>
            <br />
            <strong>กำลังเพิ่มข้อมูลคนพิการที่ได้ีรับเข้าทำงาน...</strong>
            <?php }elseif($_GET["delle"]){?>
            <br />
            <strong>กำลังลบข้อมูลคนพิการที่ได้ีรับเข้าทำงาน...</strong>
            <?php }elseif($_GET["curate"]){?>
            <br />
            <strong>กำลังปรับปรุงข้อมูลมาตรา 35...</strong>
            <?php }else{?>
            <br />
            <strong>กำลังปรับปรุงข้อมูล...</strong>
            <?php }?>
            
            
            </div>
        </div>
        
        <script>
        $t = $("#main_body");
        
        $("#overlay").css({
          opacity: 0.5,
          top: 0,
          left: 0,
          width: $t.outerWidth(),
          height: $t.outerHeight()
        });
        
        $("#img-load").css({
          top:  (380),
          left: ($t.width() / 2 -110)
        });
        
        //$t.mouseover(function(){
           $("#overlay").fadeIn();
        //}
        //);
        </script>
        
    <?php }?>
    
    
    <?php if($_GET["auto_post"]){?>
    
    	<script>
		document.forms["lawful_form"].submit();
		</script>
    
    <?php }?>
    
    
    



</body>
</html>
