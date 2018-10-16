<?php

	include "db_connect.php";
	
	session_start();
	if(isset($_SESSION['sess_userid'])){
		$sess_userid = $_SESSION['sess_userid'];
	}
	if(isset($sess_userid) && $sess_accesslevel != 4 && $sess_accesslevel != 18){
		//header("location: org_list.php");
		/*header("location: dashboard.php");*/
		echo '<a href="scrp_do_logout.php" class="glink" style="color:#000000;">ออกจากระบบ</a>';
	}
	
	if( $sess_accesslevel == 4 || $sess_accesslevel == 18){		
		header("Location: org_list.php");
	}

?>
<?php include "header_html.php";?>
               
               
               
             
             <td valign="top">
                              <form action="scrp_do_login.php" method="post">
                    <table align="center" style="padding:15px 0 15px 0;">
                        <tr>
                            <td colspan="2">
                            <strong>Login</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            User name: 
                            </td>
                            <td>
                            <input name="user_name" type="text" />
                            </td>
                         </tr>
                         <tr>
                            <td>
                            Password:
                            </td>
                            <td>
                            <input name="password" type="password" />
                            <?php if($_GET["cont"]){?>
                            <input name="cont" type="hidden" value="<?php echo $_GET["cont"];?>" />
                            <?php } ?>
                            </td>
                         </tr>
                         <tr>
                            <td colspan="2" align="right">
                                <?php if($_GET["mode"] == "error_pass"){echo "invalid username or password!";}?>
                                
                                <?php if($_GET["mode"] == "pending"){echo "your account are pending approval.";}?> 
                                
                                <input name="" type="submit" value="login" />  | <a href="view_register_password.php">ลืมรหัสผ่าน คลิกที่นี่</a>
                            </td>
                         </tr>
                         
                         <tr>
                         	<td colspan="2" align="right">
                            	
                                <hr />
                                
                                <div align="center" style="padding: 10px;">
                                	
                                    <img src="decors/pdf_small.jpg" />
                                    <a href="hire_eservice_manual_for_company.pdf" style="font-weight: normal;">
	                                	ดาวน์โหลดคู่มือการใช้งานระบบรายงานการปฏิบัติตามกฎหมาย
                                    </a>
                                
                                </div>
                            
                            </td>
                         </tr>
                         
                    </table>            
                    </form>  
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

</div><!--end page cell-->
</td>
</tr>
</table>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5bb439b78a438d2b0ce00015/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</body>
</html>