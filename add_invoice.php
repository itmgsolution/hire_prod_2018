<?php

	include "db_connect.php";
	include "session_handler.php";
	
	//yoes 20160622 -- check permisison
	if($sess_accesslevel != 1 && $sess_accesslevel != 2 && $sess_accesslevel != 3){
		header("location: index.php");	
		exit();
	}
	
	
	$the_year = $_GET[for_year]*1;
	$the_cid = $_GET[search_id]*1;
	
	$cid_province = getFirstItem("select province from company where cid = '$the_cid'");
	
?>


<?php include "header_html.php";?>






<td valign="top" style="padding-left:5px;">
                
                	
                	
                    
                    
                    
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >การส่งเงินเข้ากองทุนฯ</h2>
                    
                    
                    
                    <form method="post" >
                    
                    
                    <table border="0" cellpadding="0">
                          <tr>
                            <td><table border="0" style="padding:10px 0 0 50px;" >
                            
                              <?php if($sess_accesslevel !=4){?>
                              <tr>
                                <td colspan="4" >
								
								<span style="font-weight: bold">ข้อมูลใบเสร็จ</span></td>
                                
                              </tr>
                              <?php } ?>
                              
                              <tr>
                                <td>สถานประกอบการ</td>
                                <td colspan="3">
                                
                                
                                <strong>
								
                                <a href="organization.php?id=<?php echo $the_cid; ?>&year=<?php echo $the_year;?>&focus=lawful">
									<?php 
                                    
                                    
                                    
                                    $company_row = getFirstRow("select * from company where cid = '$the_cid'");
                                    
                                    echo formatCompanyName($company_row[CompanyNameThai],$company_row[CompanyTypeCode]);
                                    
                                    
                                    ?>
                                </a>
                                
                                
                                </strong></td>
                              </tr>
                              
                              <tr>
                                <td>เลขทะเบียนนายจ้าง</td>
                                <td colspan="3">
                                
                                
                                <strong>
								
                                <?php 
								
								echo $company_row[CompanyCode];
								
								?>
                                
                                </strong></td>
                              </tr>
                              
                              
                              
                              <tr>
                                    <td>สำหรับปี</td>
                                    <td><strong><?php 
										//**toggle payment
										
										
										echo $the_year+543;
										
										
										// ddl_year_payments will only allow to add payment year 2015?></strong></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                  </tr>
                              <tr>
                                <td>ข้อมูลการจ่ายเงินสำหรับวันที่</td>
                                <td>
								
								<strong><?php 
								
								
									if($_POST["the_date_year"] && $_POST["the_date_month"] && $_POST["the_date_day"]){
										$this_date_time = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];	
									}else{
										$this_date_time = date("Y-m-d");
									}								
								
									echo formatDateThai($this_date_time);?></strong>
                                
                                </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              
                              
                              
                              
                             
                              
                              
                              
                              <tr>
                              
                              	<td colspan="4">
                                
                                
                                
                                <?php 
								
								
									$this_lawful_year = $the_year;
									
									
									//get lawfulness's details
									$lawfulness_row = getFirstRow("select * from lawfulness where cid = '$the_cid' and year = '$the_year'");
									
									//echo "select * from lawfulness where cid = '$the_cid' and year = '$the_year'";
									
									
									//basic values
									$lawfulness_year = $lawfulness_row[Year];	
									$lawfulness_employees = $lawfulness_row[Employees];
									
									$lid_to_get_34 = $lawfulness_row[LID];
									
									//echo $lid_to_get_34;
									
									
									
									
									$lawfulness_ratio = getThisYearRatio($lawfulness_year);
									$need_for_lawful = getEmployeeRatio($lawfulness_employees,$lawfulness_ratio);
									
									
									$year_date = 365; //days in years
									$the_wage = getThisYearWage($lawfulness_year, $cid_province); //ค่าจ้างประขำปี
									//echo "-".$lawfulness_year."-";
									//echo "-".$cid_province."-";
									$wage_rate = $the_wage;
									
									if($this_lawful_year == 2011){
										
										$wage_rate = $wage_rate/2;
										$the_wage = $the_wage/2;
										
										
										$do_54_budget = getFirstItem("
							
													select
														meta_value
													from
														lawfulness_meta
													where
														meta_for = 'do_54_budget'
														and
														meta_lid = '". $lid_to_get_34."'
													
													
													");
													
																	
										$the_54_budget_date = getFirstItem("

												select
													meta_value
												from
													lawfulness_meta
												where
													meta_for = 'do_54_budget_start_date'
													and
													meta_lid = '". $lid_to_get_34."'
												
												
												");
												
											//echo $the_54_budget_date;
										
									}
									
									
									$cid_province = getFirstItem("select province from company where cid = '".$lawfulness_row[CID]."'");
									
									//also re-sync m33 here just in case
									$hire_numofemp = getFirstItem("
										SELECT 
											count(*)
										FROM 
											lawful_employees
										where
											le_cid = '".$the_cid."'
											and le_year = '".$the_year."'");

									
									//yoes 20170119
									$the_lid = $lid_to_get_34;
									
									$the_35_sql = "
		
											select
												count(*)
											from
												curator
											where
												curator_lid = '$the_lid'
												and
												curator_parent = 0
									
											";
											
									
									
									$the_35 = getFirstItem($the_35_sql);
									
									$extra_employee = $need_for_lawful-$the_35-$hire_numofemp;
									
									//echo $need_for_lawful ."-".$the_35 ."+".$hire_numofemp;
									$employees_ratio = $extra_employee;
									
									$start_money = $employees_ratio*$year_date*$the_wage;
									
									
									
									//
									$the_sql = "select *
												, receipt.amount as receipt_amount
												, lawfulness.year as lawfulness_year
												 from payment, receipt , lawfulness
													where 
													receipt.RID = payment.RID
													and
													lawfulness.LID = payment.LID
													
													and
													lawfulness.lid = '".$lid_to_get_34."' 
													
													and
													is_payback != 1
													and 
													main_flag = 1
													order by ReceiptDate, BookReceiptNo, ReceiptNo asc";
									
									//echo $the_sql; exit();
											
									$the_result = mysql_query($the_sql) or die(mysql_error()); //this one is slow...
									
									//resets
									$paid_money = 0;
									$extra_money = 0;
																		
									//echo "start_money -- $start_money --";
									
									//echo "<br>employees_ratiooo ".$employees_ratio." oo";
									
									$owned_money = $start_money;
									$paid_from_last_bill = 0;
									$this_lid_interests = 0;
									$last_payment_date = 0;
									
									while($result_row = mysql_fetch_array($the_result)){
										
											$have_some_34 = 1;
										
											$owned_money = $owned_money - $paid_from_last_bill;
											
											$this_paid_amount = $result_row["receipt_amount"];	
											
											//echo "<br>owned_money;;".$owned_money.";;";								
											
											//echo "<br>this_paid_amount**".$this_paid_amount."**";
											
											$this_lawful_year = $result_row[lawfulness_year];
																						
											if(!$last_payment_date){
																								
												if($the_54_budget_date){
		
													$last_payment_date = "$the_54_budget_date 00:00:00";
												
												}else{
													
													$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);
												}
											}
											
											//echo "---".$last_payment_date;
											
											//echo $the_54_budget_date;
																	
											if(strtotime(date($last_payment_date)) 
												< 
												strtotime(date(getDefaultLastPaymentDateByYear($this_lawful_year)))){
											
												$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);
											
											}
											
											//echo $last_payment_date;
											
											$interest_date = getInterestDate($last_payment_date, $this_lawful_year, $result_row["ReceiptDate"]);
											//echo "<br>interest_date,".$interest_date.",";										
								
											$last_payment_date_to_show = $last_payment_date;
											$last_payment_date = $result_row["ReceiptDate"];
											
											if($this_lawful_year >= 2012 || $do_54_budget){ //only show interests when 2012+
												
												//echo "<br>doGetInterests($interest_date,$owned_money,$year_date)";
												$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
											}else{
												$interest_money = 0;
											}
											
											$this_lid_interests += $interest_money;
											
											
											//echo "<br>interest_money::".$interest_money."::";	
											
											if($total_pending_interest > 0){																
												$interest_money += $total_pending_interest;					
											}
											
											
											if($this_paid_amount < $interest_money){
												$have_pending_interest = 1;
												
											}					
											
											
											$this_paid_money = $this_paid_amount-$interest_money;
											
											//echo "<br> $this_paid_money = $this_paid_amount - $interest_money ;"; 
											
											if($this_paid_money < 0){
												$this_paid_money = 0;
											}
											
											
											$paid_money += $this_paid_money;
											
											$paid_from_last_bill = $this_paid_money;
											
										
											if($this_paid_amount < $interest_money){
												$pending_interest = (($interest_money - $this_paid_amount ));
												
												$total_pending_interest = $pending_interest;
											
											 }else{
											
												$total_pending_interest = 0;
											
											}
											
											
									}//end while for looping to display payment details	
									
									//exit();
									
									//exit();
									
									//echo "($paid_money/($year_date*$the_wage)"; //exit();
									
									//yoes 20160201 --> if จ่ายเกิน then move it somewhere else
									//echo "if( $paid_money > $start_money ){"; exit();
									if( $paid_money > $start_money){
										
										$extra_money = $paid_money - $start_money;
										$paid_money = $start_money;
									}
									
									
									
									//echo $deducted_33 ;
								
								?>
                                
                                <hr>
                                <span id="calculated_34_table">
                                    <table >
                                    
                                        
                                        <tr>
                                            <td>
                                            เงินที่ต้องส่งเข้ากองทุน:                                             
                                            
                                            
                                            
                                           
                                            
                                            
                                            </td>
                                            <td>
                                            <div align="right">
                                            <?php echo $extra_employee;?> x <?php 
                                            
                                            //yoes 20151230 
                                            //special for year 2011
                                            
                                            if($this_lawful_year == 2011){
                                                echo ($wage_rate*2) . "/2";
                                            }else{																	
                                                echo $wage_rate;
                                            }
                                            
                                            
                                            ?> x <?php echo $year_date;?> = </div></td>
                                            
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
                                                ReceiptYear = '$the_year'
                                                and
                                                lawfulness.CID = '".$the_cid."' 
                                                
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
                                               
											   
											   if($the_54_budget_date){
												
													$interest_date_for_calculate_summary = "$the_54_budget_date 00:00:00";
												
												}else{
													
													$interest_date_for_calculate_summary = getDefaultLastPaymentDateByYear($this_lawful_year);	
												}
											   
                                            }
                                                                    
                                            //echo "$this_lawful_year-02-01 00:00:00";		
                                            
                                            
                                            //if last payment date is less than FEB 01 then detaulit it to FEB 01
                                            if(strtotime(date($interest_date_for_calculate_summary)) 
                                                < 
                                                strtotime(date(getDefaultLastPaymentDateByYear($this_lawful_year)))){
                                            
                                                $interest_date_for_calculate_summary = getDefaultLastPaymentDateByYear($this_lawful_year);
                                            
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
                                            $interest_date = getInterestDate($interest_date_for_calculate_summary, $this_lawful_year, $this_date_time);
                                        }
                                        
                                        //echo "<br>$actual_interest_date" . " / ". $this_lawful_year . " / ".  strtotime(date("Y-m-d"))."<br>";
                                        
                                        
                                        //yoes 20170108
                                        //interests for 2011
                                        
                                        if($this_lawful_year >= 2012 || $do_54_budget){ //only show interests when 2012+
                                            $interest_money = doGetInterests($interest_date,$owned_money,$year_date);
                                        }else{
                                            $interest_money = 0;
                                        }
                                        
                                        ?>
                                        
                                        
                                         
                                         <?php 
                                         
                                         
                                         //yoes 20170108
                                        //interests for 2011
                                         
                                         if($this_lawful_year >= 2012 || $do_54_budget){//?>
                                         
                                                <tr>
                                                    <td>
                                                    ดอกเบี้ย ณ วันที่ <br><strong><?php echo formatDateThai($this_date_time)?></strong>:                                                        </td>
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
                                        
                                        
                                        
                                        
                                        
                                        <?php 
                                        
                                        //yoes 20170108
                                        //interests for 2011
                                        
                                        
                                        
                                        if($this_lawful_year >= 2012 || $do_54_budget){//?>
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
                                    
                                    
                                    </span>
                                
                                
                                
                                
                                
                                
                                
                                <hr>
                                
                                </td>
                                
                             </tr>
                              
                              
                              
                               <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">วันที่ต้องการจ่ายเงิน</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <?php
											   
											   $selector_name = "the_date";
											  // 
											  
											  if(
											  	$_POST["the_date_year"] 
												&& $_POST["the_date_month"] 
												&& $_POST["the_date_day"]
											  ){
											  
											  $this_date_time = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];
											  
											  }else{
											  
											   $this_date_time = date("Y-m-d");
											   
											  }
											   
											  
											    //*toggles_payment*
											   //
											   include ("date_selector.php");
											   
											   ?>
                                </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">เงินต้นคงเหลือ</span></td>
                                <td colspan="3"><?php echo number_format(($owned_money),2);?> บาท</td>
                              </tr>
                              
                               <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ดอกเบี้ยค้างชำระ</span></td>
                                <td colspan="3"><?php echo formatNumber($interest_money + $total_pending_interest); ?> บาท</td>
                              </tr>
                              
                               <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ยอดเงินค้างชำระ</span></td>
                                <td colspan="3"><?php echo formatNumber($the_final_money); ?> บาท</td>
                              </tr>
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">จำนวนเงินที่ต้องการจ่าย</span></td>
                                <td colspan="3">
                                	
                                    <input name="Amount" type="text" id="Amount" style="text-align:right;" value="<?php echo default_value($_POST["Amount"],0);?>" onchange="addCommas('Amount');"/>
                                  <?php
								  	
									include "js_format_currency.php";
								  
								  ?>
                                   บาท
                                
                                </td>
                                
                              </tr>
                            
                              
                              
                              <tr>
                                <td>&nbsp;</td>
                                <td colspan="3"><input type="submit" name="do_calc" value="คำนวณเงิน" /></td>
                              </tr>
                              
                                
                              
                              
                              
                              
                              <tr>
                              	<td colspan="4">
                                	<hr />
                                </td>
                             </tr>
                             
                             
                              
                              
                              </form>
                              
                              
                              
                              <?php if($_POST[do_calc]){?>
                              
                              
                              <?php 
							  	
								$the_amount = deleteCommas($_POST["Amount"]);
							  
							  
							  ?>
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">จำนวนเงินที่ต้องการจ่าย</span></td>
                                <td colspan="3"><?php echo number_format(default_value($the_amount,0),2);?> บาท</td>
                              </tr>
                              <tr>
                                <td>จ่ายเป็นดอกเบี้ย</td>
                                <td colspan="3"><?php 
								
								
									if($the_amount){
										
										//yoes 20180311
										if($interest_money > $the_amount){
											
											$pay_for_interest = $the_amount;
											
										}else{
										
											$pay_for_interest = ($interest_money);
										
										}
										
										
									}else{
										$pay_for_interest = 0;	
									}
									
									
									echo number_format($pay_for_interest,2);
									
								?> บาท <?php
								
								
								if($interest_money-$pay_for_interest){
									
									echo "<font color='red'>(จ่ายดอกเบี้ยขาด ".number_format($interest_money-$pay_for_interest,2)." บาท)</font>";	
									
								}
								
								?></td>
                              </tr>
                              <tr>
                                <td>จ่ายเป็นเงินต้น</td>
                                <td colspan="3"><?php 
								
									
									if($the_amount){
										
										$pay_for_start = ($the_amount - $interest_money);
										
										if($pay_for_start < 0){
											$pay_for_start = 0;
										}
										
									}else{
										$pay_for_start = 0;	
									}
									
									
									
									//yoes 20170307
									//จ่ายเกิน vs จ่ายขาด
									
									if($pay_for_start < 0 && 1==0){
										
										//yoes 20180311
										$pay_for_start = 0;
										$missing_paid = $owned_money - $pay_for_start;
										echo number_format($pay_for_start,2);
										
									}elseif($owned_money < $pay_for_start){
										
										echo number_format($owned_money,2);
										$extra_paid = $pay_for_start- $owned_money;
										
									}elseif($owned_money > $pay_for_start){
										
										echo number_format($pay_for_start,2);
										$missing_paid = $owned_money - $pay_for_start;
										
									}else{
									
										echo number_format($pay_for_start,2);
									
									}
									
									
									
								?> บาท
                                
                                
                                
                                <?php
								
								
								if($extra_paid){
									
									echo "<font color='green'>(จ่ายเงินต้นเกิน ".number_format($extra_paid,2)." บาท)</font>";	
									
								}
								
								?>
                                
                                
                                 <?php
								
								
								if($missing_paid){
									
									echo "<font color='red'>(จ่ายเงินต้นขาด ".number_format($missing_paid,2)." บาท)</font>";	
									
								}
								
								?>
                                
                                
                                
                                </td>
                              </tr>
                             
                              
                              <tr>
                                <td colspan="4">
                                
                                <hr />
                                
                                </td>
                              </tr>
                              
                              
                              <form target="_blank" method="post" action="scrp_generate_invoice.php" enctype="multipart/form-data">
                              
                              
                              <tr>
                                <td valign="top">หมายเหตุ</td>
                                <td colspan="3"><label>
                                <textarea name="invoice_remarks" cols="50" rows="4" id="invoice_remarks"></textarea>
                                </label></td>
                              </tr>
                              
                              
                              <?php if(1==0){?>
                              <tr>
                                <td>เอกสารประกอบ</td>
                                <td colspan="3">
                                  
                                  	<?php 
									
										//$this_id = "$the_invoice_id";
										//$file_type = "invoice_docfile";
										
										//include "doc_file_links.php";
										
										?>
                                  
                                    <input type="file" name="invoice_docfile" id="invoice_docfile" /></td>
                              </tr>
                              <?php }?>
                              
                              <tr>
                                        <td valign="top">เจ้าหน้าที่</td>
                                        <td colspan="3"><?php 
										
											echo $sess_userfullname;
										
										?></td>
                                      </tr>
                                      
                                       <tr>
                                        <td valign="top">วันที่ทำเรื่องจ่ายเงิน</td>
                                        <td colspan="3"><?php 
										
											echo formatDateThai(date("Y-m-d"));
										
										?></td>
                                      </tr>                          

                            </table></td>
                          </tr>
                          
                          
                          <tr>
                            <td>
                            
                            <hr />
                              <div align="center">
                               
                                <?php 
								
								//**toggles_payment
								//yoes 20160111 -- just allow this
								//if(1==1){ //swap this line with line below
								
								//yoes 20160111 -- just allow this
								//yoes 20160118 -- except for excutives
								if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3)){ // && $pay_for_start > 0 ){
								//if($sub_mode == "payback" && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3)) { 
								
								?>
                                
								
									<?php if(1==0){?>
										<br>invoice_cid <input name="invoice_cid" type="text" value="<?php echo $the_cid?>" />                                
										<br>invoice_lawful_year <input name="invoice_lawful_year" type="text" value="<?php echo $the_year?>" />
										
										<br>invoice_amount <input name="invoice_amount" type="text" value="<?php echo $the_amount?>" />
										<br>invoice_principal_amount <input name="invoice_principal_amount" type="text" value="<?php echo $pay_for_start?>" />
										<br>invoice_interest_amount <input name="invoice_interest_amount" type="text" value="<?php echo $pay_for_interest?>" />
										
										<br>invoice_userid <input name="invoice_userid" type="text" value="<?php echo $sess_userid?>" />
										
									   
										<br>invoice_payment_date <input name="invoice_payment_date" type="text" value="<?php echo $this_date_time?>" />
										
										
										
										<br>invoice_owned_principal <input name="invoice_owned_principal" type="text" value="<?php echo $owned_money*1?>" />
										<br>invoice_owned_interest <input name="invoice_owned_interest" type="text" value="<?php echo ($interest_money+$total_pending_interest)*1?>" />
										
										<input type="submit" value="เพิ่มข้อมูลการจ่ายเงิน และพิมพ์ใบชำระเงิน" />
										
									<?php }else{ ?>
									
										 <input name="invoice_cid" type="hidden" value="<?php echo $the_cid?>" />                                
										 <input name="invoice_lawful_year" type="hidden" value="<?php echo $the_year?>" />
										
										 <input name="invoice_amount" type="hidden" value="<?php echo $the_amount?>" />
										 <input name="invoice_principal_amount" type="hidden" value="<?php echo $pay_for_start?>" />
										 <input name="invoice_interest_amount" type="hidden" value="<?php echo $pay_for_interest?>" />
										
										 <input name="invoice_userid" type="hidden" value="<?php echo $sess_userid?>" />
										
									   
										 <input name="invoice_payment_date" type="hidden" value="<?php echo $this_date_time?>" />
										
										 <input name="invoice_owned_principal" type="hidden" value="<?php echo $owned_money*1?>" />
										<input name="invoice_owned_interest" type="hidden" value="<?php echo ($interest_money+$total_pending_interest)*1?>" />
										
										<input type="submit" value="เพิ่มข้อมูลการจ่ายเงิน และพิมพ์ใบชำระเงิน" />
									
									
									<?php } ?>

									
								
								<?php }elseif($pay_for_start <= 0){?>
                               
								** ไม่สามารถชำระเงินได้ เพราะจำนวนเงินที่ต้องการชำระเป็นการชำระดอกเบี้ย โดยไม่ชำระเงินต้น **
							   
								<?php } ?>
                               
                              </div> 
                    		</td>
                            
                          </tr>
                            
                         </table>
                    
                  
                        
                        
				</form>                        
                    <?php } //end POST do_calc?>
                    
                    
                        
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


</body>
</html>