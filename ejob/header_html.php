<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ระบบรายงานผลการจ้างงานคนพิการ</title>
<LINK REL='StyleSheet' type='text/css' href='styles.css'>
<link rel="stylesheet" href="emx_nav_left.css" type="text/css">

<script class="jsbin" src="jquery-1.11.1.min.js"></script>
<script src="./jquery_validate/jquery.validate.js"></script>
<script type='text/javascript' src="jquery_ui/jquery-ui.js"></script>

<script type="text/javascript" src="./kendo/kendo.all.min.js"></script>
<script type="text/javascript" src="./kendo/kendo.culture.th-TH.min.js"></script>
<script type="text/javascript" src="./kendo/kendo.calendar.custom.js"></script>
<script type="text/javascript" src="./scripts/site.js"></script>
<script type="text/javascript">
	kendo.culture("th-TH");
</script>
<link rel='stylesheet' id='all-css'  href='jquery_ui/jquery-ui.css' type='text/css' media='all' />

<link rel="stylesheet" type="text/css" href="./jquery.datetimepicker.css"/ >
<script src="./build/jquery.datetimepicker.full.min.js"></script>
<!--<script class="jsbin" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>-->
</head>
<style>
  
  #overlay { 
    display:none; 
    position:fixed; 
    background:#333333; 
  }
  #img-load { 
    position:fixed; 
  }
  
</style>

<body id="main_body">

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-88270725-1', 'auto');
  ga('send', 'pageview');

</script>

<table width="1024" align="center" border="0" style="background-color: #FFF; border-bottom: 1px solid #cccccc" >
<tr>
<td>


	<table border="0" width="100%" >
    	<tr>
        	<td>
            <div style="padding-bottom:10px;" class="logo_text" align="left">
            
            	<img src="http://ejob.dep.go.th/wp-content/uploads/2016/05/logohire5.1.png" />
            
            </div>
            </td>
            <td valign="bottom">
            
            
            
            	<div align="right">
            	
                    <table border="0">
                        
                        <tr>
                            <td>
                               
                               
                                <?php if(isset($sess_userid)){ ?>
                                    
                                          
                                          
                                           <a href="index.php"  class="glink" style="color:#000000;"> 
                                          หน้าแรก</a>
                                          
                                            
                                         
                                          
                                          <?php if($sess_accesslevel != 4){ //company won;t see these?>
                                              <a href="org_list.php?mode=search" class="glink" style="color:#000000;">ค้นหา<?php echo $the_company_word;?></a>
                                    
                                                  <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){ //exec won;t see these?>
                                                      
                                                      <!--<a href="organization.php?mode=new"  class="glink" style="color:#000000;"> 
                                                      เพิ่มข้อมูล</a>-->
                                                      
                                                      <a href="org_list.php?mode=letters" class="glink" style="color:#000000;">ส่งจดหมาย</a>
                                                      <!--<a href="org_list.php?mode=payment" class="glink" style="color:#000000;">ส่งเงิน</a>-->
                                                  <?php }?>
                                                  
                                                  <?php if($sess_accesslevel != 3 && $sess_accesslevel != 5 && $sess_accesslevel != 8){ //provincial and exec won;t see these?>
                                                      <a href="org_list.php?mode=announce" class="glink" style="color:#000000;">ประกาศผ่านสื่อ</a>
                                                  <?php } ?>
                                                  
                                                  
                                              <?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){ ?>
                                                <a href="view_reports_gov.php" class="glink" style="color:#000000;">รายงาน</a>
                                              <?php }else{ ?>
                                                 <a href="view_reports.php" class="glink" style="color:#000000;">รายงาน</a>
                                              <?php }?>
                                              
                                          <?php } ?>
                                            
                                            
                                            <a href="http://ejob.dep.go.th/?page_id=4793" target="_blank" class="glink" style="color:#000000;">กฎหมายที่เกี่ยวข้อง</a>
          
        									 <a href="http://ejob.dep.go.th/?page_id=4811" target="_blank" class="glink" style="color:#000000;">แบบรายงาน</a>
                                              
                                         
                                          <?php if($sess_accesslevel != 6 && $sess_accesslevel != 7 && $sess_accesslevel != 4){ //company won;t see these?>
                                              <a href="faq.php" class="glink" style="color:#000000;">ถาม-ตอบ</a>
                                          <?php }?>
                                              
                                              
                                              
                                          <?php
                                          
                                            //echo "can manage user?". $sess_can_manage_user;
                                          
                                           if($sess_accesslevel != 4){ //company won;t see these?>
                                              <?php if($sess_accesslevel == 1 ||  $sess_can_manage_user){ //only admin will see this // yoes 20141007 -- add ability for พมจ to edit users?>
                                                <a href="user_list.php" class="glink" style="color:#000000;">ผู้ใช้งานระบบ</a>
                                              <?php }?>
                                          
                                          <?php } ?>
                                          
                                          <a href="view_register.php?id=<?php echo $sess_userid; ?>" class="glink" style="color:#000000;">เปลี่ยนรหัสผ่าน</a>
                                          
                                          
                                          
                                          <a href="คู่มือการใช้งาน ระบบรายงานผลการจ้างงานคนพิการ สำหรับ สถานประกอบการ 2559.pdf" target="_blank" class="glink" style="color:#000000;">ดาวน์โหลดสื่อการสอน</a>
                                         
                                          
                                          <a href="scrp_do_logout.php" class="glink" style="color:#000000;">ออกจากระบบ</a>
                                       
                                    <?php }else{ ?>
                                    
                                           <a href="index.php"  class="glink" style="color:#000000;"> 
                                                login เข้าสู่ระบบ
                                          </a>
                                          
                                          
                                          <!-- end globalNav --> 
                                       
                                    <?php }?>
                               
                               
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <?php if($_SESSION['sess_userfullname']){ ?>
                                <div  align="right" style="color:#000000">เข้าระบบโดยชื่อ user: <?php echo $sess_userfullname?></div>
                                <?php } ?>
                            </td>
                        </tr>
                        
                    </table>
            
            	</div>
            
            </td>
         </tr>
    </table>
  
  

</td>
</tr>
</table>

<table align="center" cellpadding="0" cellspacing="0"  style=" background-color: #FFF;">

<tr>
<td>
<div id="pagecell1" > 
  <!--pagecell1--> 
 
  
  

<?php

	//get this script name
	$this_page = $_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING'];
	$this_script_name = $_SERVER['SCRIPT_NAME'];

?>
<table width="1024" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
    	<td>
        
         
      <table bgcolor="#FFFFFF" width="100%"  style="padding: 0 5px 5px 5px;
     
      
      
" border="0">
        	<tr>
            	<td colspan="2">
                <h1 class="default_h1" style="margin:0; padding:0; "  >
                	<?php 
						if(isset($sess_userid)){ 
							
							//echo $this_script_name;
							
							if(strpos($this_page, "mode=letters") 
								|| strpos($this_script_name, "letter_list.php")
								|| strpos($this_page, "mode=payment") 
								|| strpos($this_script_name, "payment_list.php")
								||strpos($this_page, "mode=announce") 
								|| strpos($this_script_name, "announce_list")
								){
								echo "การดำเนินการตามกฎหมาย";							
							}elseif(strpos($this_script_name, "org_list.php")
							 || strpos($this_script_name, "organization.php")){
								echo "<?php echo $the_company_word;?>";							
							}
						
						}else{ ?>

                    	Login
                        
					<?php }?>
                </h1>
               
                </td>
			</tr>
            <tr>
            	<td valign="top" width="225" style="border-right: solid 1px #efefef;">
                	
               		<?php include "left_menu.php"; ?> 
                                        
                </td>