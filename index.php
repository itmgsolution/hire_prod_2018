<?php
	
	include "db_connect.php";
	
	session_start();
	if(isset($_SESSION['sess_userid'])){
		$sess_userid = $_SESSION['sess_userid'];
	}
	if(isset($sess_userid) && $sess_accesslevel != 4 && $sess_accesslevel != 18){
		//header("location: org_list.php");
		header("location: dashboard.php");
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
                            <strong>Login สำหรับเจ้าหน้าที่ระบบรายงานผลการจ้างงานคนพิการ</strong>
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
                                
                                <input name="" type="submit" value="login" /> | <a href="view_register_password.php">ลืมรหัสผ่าน คลิกที่นี่</a>
                            </td>
                         </tr>
						 <tr>
                            <td colspan="2" align="right">
								<hr>
								ต้องการเข้าใช้งานระบบรายงานผลการจ้างงาน<b><u>สำหรับสถานประกอบการ</b></u> <u><a href="http://ejob.dep.go.th/ejob/">กรุณาคลิกที่นี่</a></u>
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
<?php //yoes 20170213 -- set faux cron here ?>
<script>
	$.get("ajax_do_snapshot.php");
</script>

</body>
</html>