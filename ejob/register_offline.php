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



<table border="1" align="center" cellpadding="5" cellspacing="0" width="100%"  style="margin-top:5px; border-collapse: collapse; " >

            <tr>
              <td width="65%" rowspan="4" align="center" valign="middle" >
              <div align="center" >
              	
                <font style="font-size: 18px;">
                แบบคําขอเปิดใช้งาน Username
                </font>
                <br> <br>
				เพื่อนําส่งข้อมูลการปฏิบัติตามกฎหมายการจ้างงานคนพิการ ผ่านทางอิเล็กทรอนิกส์

                
                
                </div>
              </td>
              <td colspan="2" align="center" valign="top" >
              <div align="center" ><font style="font-size: 11px;">สำหรับเจ้าหน้าที่</font></div></td>
            </tr>
            <tr>
              <td valign="top" align="center" width="20%" ><font style="font-size: 11px;">เลขที่คำขอ</font></td>
              <td valign="top" align="center" width="30%" ><?php echo $_GET[the_uid]?></td>
            </tr>
            <tr>
              <td valign="top" align="center" ><font style="font-size: 11px;">วันที่รับ</font></td>
              <td valign="top" align="center" >&nbsp;</td>
            </tr>
            <tr>
              <td valign="top" align="center" ><font style="font-size: 11px;">เจ้าหน้าที่</font></td>
              <td valign="top" align="center" >&nbsp;</td>
            </tr>
</table>    

<table border="1" align="right" cellpadding="10" cellspacing="0" width="100%"  style="border-collapse: collapse;" >

            <tr>
              <td valign="top" align="center"  >
             <div align="center">
             เลขที่บัญชีนายจ้าง <span style="display: table-cell; border-bottom: 1px dotted black;"><?php echo $company_row["CompanyCode"]?></span> 
             </div>
             
             <div align="center">
             เลขทะเบียนนิติบุคคลของกระทรวงพาณิชย์ <span style="display: table-cell; border-bottom: 1px dotted black;">
			 
             <?php echo getFirstItem("select user_commercial_code from users where user_id = '".($_GET[the_uid]*1)."'")?>
             
             </span> 
             </div>
             
              </td>
            </tr>
            
</table>    

<table border="1" align="right" cellpadding="10" cellspacing="0" width="100%"  style="border-collapse: collapse;" >

           
            
            <tr>
              <td valign="top"  >
             1) ชื่อสถานประกอบการ
    			</td>
                
                 <td valign="top"  >
                 
             <span style="display: table-cell; border-bottom: 1px dotted black;">
			 
             <?php 
				
					$company_name_to_use = formatCompanyName($company_row["CompanyNameThai"],$company_row["CompanyTypeCode"]); 
					echo $company_name_to_use;
				
				?>
             
             </span>
           
            
    			</td>
            </tr>
            
            
            <tr>
              <td valign="top"   >
             2) สำนักงานใหญ่ตั้งอยู่เลขที่ 
    			</td>
                
                 <td valign="top"  >
                 
                 <span style="display: table-cell; border-bottom: 1px dotted black;">
                 <?php 
				 
				 echo getAddressText($company_row)
				 
				 ?>
                 </span>
                 

    			</td>
            </tr>
            
             <tr>
              <td valign="top"  >
             3) ข้อมูลการใช้งานระบบ 
    			</td>
                
                 <td valign="top"  >
                 
                 ชื่อผู้ใช้งาน: 
                 <span style="display: table-cell; border-bottom: 1px dotted black;">
                 <?php echo getFirstItem("select user_name from users where user_id = '".($_GET[the_uid]*1)."'")?>
                 </span>
                 <br>
                 อีเมล์:
                 <span style="display: table-cell; border-bottom: 1px dotted black;">
                 <?php echo getFirstItem("select user_email from users where user_id = '".($_GET[the_uid]*1)."'")?>
                 </span>

    			</td>
            </tr>
            
             <tr>
              <td valign="top"  >
             4) คำรับรอง
    			</td>
                
                 <td valign="top"  >
                 
                 ข้าพเจ้าขอรับรองว่าข้อความที่ระบุไว้ในแบบคําขอเปิดใช้งาน  Username มีความถูกต้องครบถ้วน
               
                 ซึ่งมีกรรมการผู้มีอํานาจ จํานวน...........................คน ได้ลงลายมือชื่อไว้ท้ายหนังสือฉบับนี้
                 
                 
                 <table border="0" align="center" cellpadding="0" cellspacing="0" width="100%"  style="margin-top:50px; line-height: 25px;" >
                    <tr>
                        <td align="center">
                        <div align="center">
                        ลงชื่อ ..............................................
                        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(..............................................)
                        </div>
                        </td>
                       <td align="center">
                        <div align="center">
                        ลงชื่อ ..............................................
                        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(..............................................)
                        </div>
                        </td>
                    </tr>       
                   
                </table>
                
                <table border="0" align="center" cellpadding="0" cellspacing="0" width="100%"  style="margin-top:50px; line-height: 25px;" >
                    <tr>
                        <td align="center">
                        <div align="center">
                        ลงชื่อ ..............................................
                        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(..............................................)
                        </div>
                        </td>
                       <td align="center">
                        <div align="center">
                        ลงชื่อ ..............................................
                        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(..............................................)
                        </div>
                        </td>
                    </tr>       
                   
                </table>
                
                <table border="0" align="center" cellpadding="0" cellspacing="0" width="100%"  style="margin-top:25px; line-height: 25px;" >
                        <tr>
                            <td >
                          หมายเหตุ : กรรมการผู้มีอํานาจต้องลงลายมือชื่อทุกคน
                            </td>
                           
                        </tr>       
                       
                </table>

    			</td>
            </tr>
            
            
</table>    



<table border="0" align="center" cellpadding="5" cellspacing="0" width="100%"  style="margin-top:5px;" >

           
  <tr>
              <td valign="top" align="center" colspan="2"  >
            <strong> คําแนะนําและเงื่อนไขสําหรับแบบคําขอเปิดใช้งาน Username</strong>
    </td>
            </tr>
            
	<tr>
              <td valign="top" align="left"  >

		1)

		    </td>
            
             <td valign="top" align="left"  >
		สถานประกอบการจะต้องกรอกข้อมูลรายละเอียดต่างๆ ตามจริงให้ครบถ้วน ทั้งนี้เพื่อประโยชน์แก่สถานประกอบการ หากตรวจสอบพบว่าข้อมูลดังกล่าวไม่เป็นความจริง กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการจะระงับการใช้งาน ของสถานประกอบการ โดยไม่ต้องแจ้งให้ทราบล่วงหน้า


		    </td>
    </tr>     
    
    
    <tr>
              <td valign="top" align="left"  >

		2)

		    </td>
            
             <td valign="top" align="left"  >

		หลักฐานที่ใช้ในการยื่นคำขอเปิดใช้งาน Username สำหรับใช้ในการนําส่งข้อมูลการปฏิบัติตามกฎหมายการจ้างงานคนพิการ ผ่านทางอิเล็กทรอนิกส์
        
        <table border="0" align="left" cellpadding="5" cellspacing="0" width="100%"  style="margin-top:5px;" >
			<tr>
            	<td>
                2.1) หนังสือแสดงความตกลงในการแจ้งรายงานการปฏิบัติตามกฎหมายจ้างงานคนพิการผ่านทางอิเล็กทรอนิกส์
                </td>
            </tr>
            <tr>
            	<td>
                2.2) หนังสือมติที่ประชุมคณะกรรมการที่ให้รายงานการปฏิบัติตามกฎหมายจ้างงานคนพิการผ่านทางอิเล็กทรอนิกส์
                </td>
            </tr>
            <tr>
            	<td>
                2.3) หนังสือมอบอำนาจ (ถ้ามี)
                </td>
            </tr>
             <tr>
            	<td>
                2.4) สำเนาบัตรประชาชนของผู้มีอำนาจที่ลงชื่อในคำขอ
                </td>
            </tr>
             <tr>
            	<td>
                2.5) สำเนาบัตรประชาชนของผู้รับมอบอำนาจ (ถ้ามี)
                </td>
            </tr>
            <tr>
            	<td>
                2.6) หนังสือรับรองนิติบุคคลที่มีอายุไม่เกิน 90 วัน
                </td>
            </tr>
            
        </table>


		    </td>
    </tr>      
    
    
    <tr>
              <td valign="top" align="left"  >

		3)

		    </td>
            
             <td valign="top" align="left"  >

	Username และ Password ของสถานประกอบการ จะถูกใช้แทนลายมือชื่อและตราประทับ เพื่อนําส่งข้อมูลการปฏิบัติตามกฎหมายการจ้างงานคนพิการ ผ่านทางอิเล็กทรอนิกส์
    ทั้งนี้ สถานประกอบการ จะได้รับการแจ้งเปิดใช้ Username ทางอีเมล์ที่ระบุไว้ในคำขอ


		    </td>
    </tr> 
    
    
    <tr>
              <td valign="top" align="left"  >

		4)

		    </td>
            
             <td valign="top" align="left"  >

	กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ขอรับรองว่าจะเก็บข้อมูลของสถานประกอบการไว้เป็นความลับ โดยจะมินำไปเปิดเผยที่ใด และ/หรือ เพื่อประโยชน์ทางการค้า หรือประโยชน์ทางด้านอื่นๆ โดยไม่ได้รับอนุญาต ผู้สมัครจะต้องรักษารหัสผ่านเป็นความลับ 
    และหากมีผู้อื่นล่วงรู้และเข้าใช้ระบบได้ กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ จะไม่รับผิดชอบใดๆ ทั้งสิ้น

		    </td>
    </tr> 
    
        
            
</table>    

<img src="doc_0201.jpg" style="margin-top:10px;" />





 
</body>        
</HTML>