<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_112.xls");
	$is_excel = 1;

}elseif($_POST["report_format"] == "words"){
	
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=nep_hire_report.doc");	

}elseif($_POST["report_format"] == "pdf"){
	
	$is_pdf = 1;
	//header("location: create_pdf_2.php");

}else{

	header ('Content-type: text/html; charset=utf-8');
}

$the_year = "2011";

if(isset($_POST["ddl_year"])){
	$the_year = $_POST["ddl_year"];
}

if($the_year > 2012){
	$is_2013 = 1;
}

$the_year_to_use = formatYear($the_year);

$province_text = "ทั่วประเทศ";
$province_filter = "";		
if(isset($_POST["Province"]) && $_POST["Province"] != "" && $_POST["rad_area"] == "province"){
	$province_filter = " and a.Province = '".$_POST["Province"]."'";
	
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}




if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	$province_table = ", provinces";
	$province_filter = " and a.Province in (select province_id from provinces where section_id = '".$_POST["Section"]."')";
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
}

if($_POST["CompanyTypeCode"] == "14"){
	
	$typecode_filter = " and CompanyTypeCode = '14'";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	$typecode_filter = " and CompanyTypeCode != '14'";
	$business_type = "สถานประกอบการ";
}


///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter .= " and CompanyTypeCode >= 200  and CompanyTypeCode < 300";
	
}else{
	
	$typecode_filter .= " and CompanyTypeCode < 200";
	
}




//businesss type
if(isset($_POST["BusinessTypeCode"]) && $_POST["BusinessTypeCode"]){
	
	if($_POST["BusinessTypeCode"] == "0000"){
	
		//not indicated business code?
		//also include NULL
		$business_filter = "
		
					and 
					
					(
					
						a.BusinessTypeCode = '".$_POST["BusinessTypeCode"]."'
						or 
						a.BusinessTypeCode 
						NOT IN (

							SELECT BusinessTypeCode
							FROM businesstype
						)
						OR a.BusinessTypeCode =  ''
					)
					
					";
	
	}else{
	
		$business_filter = " and a.BusinessTypeCode = '".$_POST["BusinessTypeCode"]."'";
	}
	
	
	
	$bus_name = getFirstItem("select BusinessTypeName from businesstype where BusinessTypeCode = '".$_POST["BusinessTypeCode"]."'");
	$business_text = ": ". $bus_name;
}


//yoes 20130813 - add last modify date/time for lawfulness
if($_POST["date_from_year"] > 0 && $_POST["date_from_month"] > 0 && $_POST["date_from_day"] > 0){

	$the_mod_year = $_POST["date_from_year"];
	$the_month = $_POST["date_from_month"];
	$the_day = $_POST["date_from_day"];
	
	$filter_from = " and LastModifiedDateTime >= '$the_mod_year-$the_month-$the_day 00:00:01'";
}

if($_POST["date_to_year"] > 0 && $_POST["date_to_month"] > 0 && $_POST["date_to_day"] > 0){

	$the_mod_year = $_POST["date_to_year"];
	$the_month = $_POST["date_to_month"];
	$the_day = $_POST["date_to_day"];
	
	$filter_to = " and LastModifiedDateTime <= '$the_mod_year-$the_month-$the_day 23:59:59'";
}

if($_POST["chk_from"] && ($filter_from || $filter_to)){

	$last_modified_sql = "
	
			and
			a.CID in (
			
				select mod_cid from modify_history where mod_type = 1
				
				$filter_from
				$filter_to			
			)	
			";	
}





////// starts LOGIC here


$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_$the_year'"),100);

if($is_2013){

	$condition_sql .= " and branchCode < 1";

}



$main_sql = "

			select
				*
				, b.Employees as company_employees
				, a.CID as the_company_cid
				, b.LID as the_lid
			from
				company a
				join lawfulness b on a.CID = b.CID
				left outer join provinces c on a.Province = c.province_id
			where
				
				b.Year = '$the_year'
				
				
				$province_filter
				
				$condition_sql

				$business_filter
				
				$last_modified_sql
				
				$typecode_filter
			
			order by
				province_name, CompanyNameThai asc
			
			
			";


////////			
if($is_2013 ){



	//yoes 20140515 - fix it so it has same count logic as report 1
	//is it correct? - maybe
	$main_sql = "

			select
				*
				, b.Employees as company_employees
				, a.CID as the_company_cid
				, b.LID as the_lid
			from
				company a
				join lawfulness b on a.CID = b.CID
				left outer join provinces c on a.Province = c.province_id
				
				
				
				
			where
				
				b.Year = '$the_year'
								
				$province_filter
				
				$condition_sql
				
				$business_filter
				
				$last_modified_sql
				
				$typecode_filter
			
			order by
				province_name, CompanyNameThai asc
			
			
			";



}			
			



			
//echo $main_sql;		exit();	

if($is_pdf || $is_excel){

	$w50 = 50;
	$w75 = 75;
	$w100 = 100;
	$w125 = 125;
	$w350 = 350;
	
}



?>

<div align="center">
            <strong>รายละเอียดการปฏิบัติตามกฎหมายเรื่องการจ้างงานคนพิการแยกตามประเภทกิจการ<?php echo $business_text;?> <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />
</strong>
              <br>
</div>
    
    
<table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td width="0" rowspan="3" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="0" rowspan="3" align="center" valign="bottom"><div align="center"><strong>เลขทะเบียนนายจ้าง</strong></div></td>
        <td width="0" rowspan="3" align="center" valign="bottom"><div align="center"><strong>ชื่อสถานประกอบการ</strong></div></td>
        <td width="0" rowspan="3" align="center" valign="bottom"><strong>ประเภทกิจการ</strong></td>
        <td width="0" rowspan="3" align="center" valign="bottom"><div align="center"><strong>ที่อยู่</strong></div></td>
        <td colspan="5" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>การปฏิบัติตามกฎหมาย</strong></div></td>
        </tr>
      <tr >
        <td width="0" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>
          
          มาตรา 33
  <br />(ราย)
          
          
        </strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>มาตรา 34 (บาท)</strong></div></td>
        <td colspan="2" align="center" valign="bottom" ><div align="center"><strong>มาตรา 35</strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>สถานะ
</strong></div></td>
      </tr>
      <tr >
        <td width="0" align="center" valign="bottom" ><div align="center"><strong>คนพิการ<br />
          
          (ราย)
</strong></div></td>
        <td width="0" align="center" valign="bottom" ><div align="center"><strong>ผู้ดูแลคนพิการ<br />
        (ราย) </strong></div></td>
        </tr>
      </thead>
      
      <tbody>
      
      
      
      <?php
		  $lawful_result = mysql_query($main_sql);	
		  
		  while ($lawful_row = mysql_fetch_array($lawful_result)) {
		  
			$row_count++;
			$company_name_to_use = formatCompanyName($lawful_row["CompanyNameThai"],$lawful_row["CompanyTypeCode"]);
			
			$address_to_use = getAddressText($lawful_row);
			//
			
			$final_employee = getEmployeeRatio( $lawful_row["company_employees"],$ratio_to_use);
			
			$type_35_to_use = getFirstItem("select count(*) from curator where curator_lid = '".$lawful_row["LID"]."'");
			
			
			
			/////
			//try generate recipt data
			$the_money_sql = "select sum(receipt.Amount)  as the_amount
							from payment, receipt , lawfulness
							where 
							receipt.RID = payment.RID
							and
							lawfulness.LID = payment.LID
							and
							ReceiptYear = '".$the_year."'
							and
							lawfulness.CID = '".$lawful_row["the_company_cid"]."'
							
							and is_payback = '0'
							
							";
							
			//echo $the_money_sql;
			
			$paid_money = getFirstItem($the_money_sql);
			
			$this_row = 0;
			
			
			//curator
			$the_sql = "
												
					select count(*) 
					from 
					curator 
					where 
					curator_lid = '".$lawful_row["the_lid"]."' 
					and curator_parent = 0
					and
					curator_is_disable = 0
				
				";
		
				$curator_user = getFirstItem($the_sql);	
				
				
				
				$the_sql = "
														
							select count(*) 
							from 
							curator 
							where 
							curator_lid = '".$lawful_row["the_lid"]."' 
							and curator_parent = 0
							and
							curator_is_disable = 1
						
						";
				
				$curator_usee = getFirstItem($the_sql);		
				
			
			
	  ?>
      
              <tr>
                <td width="<?php echo $w50?>"  valign="top"><div align="center"><?php echo $row_count;?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $lawful_row["CompanyCode"];?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $company_name_to_use;?></div>          </td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo getFirstItem("select BusinessTypeName from businesstype where BusinessTypeCode = '".$lawful_row["BusinessTypeCode"]."'");?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
                <td width="<?php echo $w75?>" align="right"  valign="top"><div align="right"><?php echo $lawful_row["Hire_NumofEmp"]?></div></td>
                <td width="<?php echo $w75?>" align="right"  valign="top"><div align="right"><?php echo formatMoneyReport($paid_money);?></div></td>
                
                
                
                <?php
				
					
					//echo $the_money_sql;
					
					if($money_num_rows){
					
						//echo "hey";
						while ($pmh_row = mysql_fetch_array($paid_money_history_result)) {
						
							
						?>
                        
                        <?php
						
						
						}
					
					}else{
					
					?>
                    
                    <td width="<?php echo $w75?>" align="right"  valign="top"><div align="right"><?php echo $curator_usee;?></div></td>
                    <td width="<?php echo $w75?>" align="right"  valign="top"><div align="right"><?php echo $curator_user;?></div></td>
                        <td width="<?php echo $w75?>"  valign="top" align="center"><div align="center">
                        
                        <?php if($lawful_row["LawfulStatus"] == 0){echo 'ไม่ปฏิบัติตามกฎหมาย';}?>
                        <?php if($lawful_row["LawfulStatus"] == 1){echo 'ปฏิบัติตามกฎหมาย';}?>
                          <?php if($lawful_row["LawfulStatus"] == 2){echo 'ปฏิบัติตามกฏหมายแต่ไม่ครบตามอัตราส่วน';}?>
                          <?php if($lawful_row["LawfulStatus"] == 3){echo 'ไม่เข้าข่ายจำนวนลูกจ้าง';}?>
                        
                        </div></td>
                    <?php
					
					}
				
				?>
              </tr>
     
     
     			 <?php
				
					
					//echo $the_money_sql;
					
					if($money_num_rows){
					
						//echo "hey";
						while ($pmh_row = mysql_fetch_array($paid_money_history_result_2)) {
						
							
						?>
                        <?php
						
						
						}
					
					}
					
					?>
                    
     			
     	
     	<?php }?>
	  </tbody>
        
        <tfoot>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
