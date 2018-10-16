<HTML >
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
  	<style>
	body {
		font-size: 14px;
		/*line-height: 30px;
		margin: 0px;
		padding: 0px;*/
	}
	
	@page{
		/*top-margin: 5px;*/
	}
	
	.underline{
		border-bottom: 1px dotted #000;	
	}
	</style>
  
</HEAD>
<body >
<?php 
	
	include "db_connect.php";
	
	//select company information
	//print_r($_POST);
	
	$company_row = getFirstRow("select * from company where cid = '$_GET[the_cid]'");
	
	//print_r($company_row);
	
	//yoes 20151123 --> also see which province is this company
	$is_provincial = 0;
	$province_name = "กรุงเทพมหานคร";
	$doc_prefix = "๐";
	$the_dear = "อธิบดีกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ";
	$the_organization = "กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ";
	
	if($company_row[Province] != 1){
		
		$is_provincial = 1;
		$province_name = getFirstItem("select province_name from provinces where province_id = '$company_row[Province]'");
		$doc_prefix = "๑";
		$the_dear = "พัฒนาสังคมและความมั่นคงของมนุษย์จังหวัด".$province_name;
		$the_organization = "สํานักงานพัฒนาสังคมและความมั่นคงของมนุษย์จังหวัด".$province_name;
	}

	//echo $province_name;


?>



<img src="doc_0100.jpg" />

<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%"  style="margin-top:5px; line-height: 25px;" >
  <tr>
    <td  valign="top" style="color: #333" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ด้วยสถานประกอบการ<span style="display: table-cell; border-bottom: 1px dotted black; ">
        <?php 
				
					$company_name_to_use = formatCompanyName($company_row["CompanyNameThai"],$company_row["CompanyTypeCode"]); 
					echo $company_name_to_use;
				
				?>
        </span> เลขที่บัญชีนายจ้าง <span style="display: table-cell; border-bottom: 1px dotted black;"><?php echo $company_row["CompanyCode"]?></span>
      
      <br>
      ที่อยู่ <span style="display: table-cell; border-bottom: 1px dotted black;"><?php echo getAddressText($company_row)?></span></td>
  </tr>
</table>

<img src="doc_0101.jpg" style="margin-top:10px;" />
<img src="doc_0102.jpg" style="width:95%; margin-left: 15px; margin-top:5px;" />
<img src="doc_0103.jpg" style="width:95%; margin-left: 15px; margin-top:5px;"/>
<img src="doc_0104.jpg" style="width:95%; margin-left: 15px; margin-top:5px;"/>
<img src="doc_0105.jpg" style="width:100%; margin-left: 15px; margin-top:5px;"/>
<img src="doc_0106.jpg" style="width:100%; margin-left: 15px; margin-top:5px;"/>



 
</body>        
</HTML>