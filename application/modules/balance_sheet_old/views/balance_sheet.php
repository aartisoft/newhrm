<div class="content container-fluid">
          <div class="row">
            <div class="col-xs-8">
              <h4 class="page-title">Balance Sheet</h4>
            </div>
            
          </div>
          <div class="card-box">
                    <h4 class="card-title">Balance Sheet</h4>
                    <!-- <a href="<?php echo base_url().'balance_sheet/export_balance_sheet';?>">Download Excel</a> -->                    
                    <div class="table-responsive border">
                      <table class="table table-hover accounts-table mb-0">
                        <thead>
                          <th></th>
                          <?php foreach($years as $year) {?>
                          <th><?php echo $year; ?></th>
                         <?php } ?>
                        </thead>
                        <tbody>
                          <tr>
                            <td><strong>Assets</strong></td>
                             <!-- <?php foreach($revenue as $revenues) {?>
                               <td><strong><?php echo !empty($revenues)?$revenues:'-'; ?></strong></td>
                         <?php } ?> -->
                          </tr>
                             <?php 
                             $count  = 0;
                              $start_years = Date('Y', strtotime("-8 Year"));
                              $current_year = date("Y");

                              for ($i=$start_years; $i <=$current_year ; $i++) { 
                               
                              
                            if(!empty($profit[$i])) {
                                $count = count($profit[$i]);
                               foreach($profit[$i] as $profits) {
                                    // $revenue_title = json_decode($profits['revenue_title']);
                                    // $revenue_title = implode(',', $revenue_title);
                                ?>
                                  <tr>
                            
                               <td><?php echo $profits['revenue_title']; ?></td>

                               <?php
$payment_amount='-';
                               for ($j=$start_years; $j <=$current_year ; $j++) { 
      
                                    $years[]= $j;
                                    $end_year = $j+1;
                                      $financial_start_date = $i.'-04-1';
                                      $financial_end_date = $end_year.'-03-31';
                                      $this->db->select('amount');
                                      $this->db->where('p_id', $profits['payment_id']);
                                    $this->db->where('payment_date >=', $financial_start_date);
                                    $this->db->where('payment_date <=', $financial_end_date);
                                    $payment_amount = $this->db->get('payments')->row()->amount;

                                    ?>
                               <td><strong><?php echo  !empty($payment_amount)?$payment_amount:'-'; ?></strong></td>
                             <?php } ?>
                        
                           </tr>      
                           <?php    }
                              }
                                                
                           } $count1  = $count; ?>
                          <tr class="total-row">
                            <td><strong>Total Asset</strong></td>
                             <?php foreach($revenue as $revenues) {?>
                               <td><strong><?php echo !empty($revenues)?$revenues:'-'; ?></strong></td>
                         <?php } ?>
                          </tr>
                          <tr>
                            <td colspan="10"><strong>Liabilities</strong></td>
                          </tr>
                           <?php 
                           $expense_count = 0 ;
                              $start_years = Date('Y', strtotime("-8 Year"));
                              $current_year = date("Y");

                              for ($i=$start_years; $i <=$current_year ; $i++) { 
                               
                              
                            if(!empty($expenses[$i])) {
                               $expense_count = count($expenses[$i]);
                               foreach($expenses[$i] as $expense) {
                                    // $expenses_title = json_decode($expense['expenses_title']);
                                    // $expenses_title = implode(',', $expenses_title);
                                ?>
                                  <tr>
                            
                               <td><?php echo $expense['expenses_title'].'('.$expense['notes'].')'; ?></td>

                               <?php
                               for ($j=$start_years; $j <=$current_year ; $j++) { 
      
                                    $years[]= $j;
                                    $end_year = $j+1;
                                      $financial_start_date = $i.'-04-1';
                                      $financial_end_date = $end_year.'-03-31';
                                      $this->db->select('amount');
                                      $this->db->where('id', $expense['id']);
                                    $this->db->where('expense_date >=', $financial_start_date);
                                    $this->db->where('expense_date <=', $financial_end_date);
                                    $expenses = $this->db->get('expenses')->row()->amount;

                                    ?>
                               <td><strong><?php echo  !empty($expenses)?$expenses:'-'; ?></strong></td>
                             <?php } ?>
                        
                           </tr>      
                           <?php    }
                              }
                                                
                           } 
                            $expense_count1 = $expense_count; ?>
                           <tr>
                            <td>Taxes</td>
                            <?php foreach($tax_amount as $tax_amounts) {?>
                               <td><strong><?php echo !empty($tax_amounts)?$tax_amounts:'-'; ?></strong></td>
                         <?php } ?>
                          </tr>
                          <tr class="total-row">
                            <td><strong>Total Liabilities</strong></td>
                            <?php foreach($overall_expenses as $overall_expense) {?>
                               <td><strong><?php echo !empty($overall_expense)?$overall_expense:'-'; ?></strong></td>
                         <?php } ?>
                          </tr>
                          <tr>
                            <td><strong>Shareholder's Equity</strong></td>                            
                          </tr>
                           <tr>
                            <td>Shareholder's Equity</td>
                            <?php foreach($net_earnig as $net_earnigs) {?>
                               <td><strong><?php echo !empty($net_earnigs)?$net_earnigs:'-'; ?></strong></td>
                         <?php }  $totla_count = $count1 + $expense_count1 +7;?>
                          </tr>                        
                         
                        </tbody>
                      </table>
                    </div>
          </div>  
          <div class="row">
            <div class="col-xs-12" style="text-align: right;">
              <button type="submit" class="btn btn-warning custom_excel" name="excel" id="excel" data-to-excel="print_excel" data-excel-count="<?php echo $totla_count; ?>" data-excel-title="Balance Sheet"><i class="fa fa-file-excel-o"></i> &nbsp;Excel</button>
              </div>
          </div> 
</div>

                  <!-- Excel Explort -->

                <div id="print_excel" style="display:none">
                      <table>
                        <tr> 
                          <th colspan="8">Balance Sheet</th>
                        </tr>                           
                        <tr> 
                          <th colspan="8"><?php echo date('d-m-Y');?></th>
                        </tr>
                      </table>
                     <table class="table" border="1" cellspacing="0" cellpadding="3" style=" width:100% !important;">
                        <tbody>
                         <tr class="black_color" style="border:1px solid grey !important; text-align:center">
                         <td></td>
                          <?php foreach($years1 as $year) {?>
                          <td><?php echo $year; ?></td>
                         <?php } ?>
                       </tr>
                        
                          <tr>
                            <td colspan="10"><strong>Assets</strong></td>                            
                          </tr>
                         <?php 
                          $start_years = Date('Y', strtotime("-8 Year"));
                          $current_year = date("Y");

                          for ($i=$start_years; $i <=$current_year ; $i++) { 
                               
                              
                            if(!empty($profit[$i])) {
                               foreach($profit[$i] as $profits) { ?>
                                
                            <tr>
                            
                               <td><?php echo $profits['revenue_title']; ?></td>

                               <?php
$payment_amount='-';
                               for ($j=$start_years; $j <=$current_year ; $j++) { 
      
                                    $years[]= $j;
                                    $end_year = $j+1;
                                      $financial_start_date = $i.'-04-1';
                                      $financial_end_date = $end_year.'-03-31';
                                      $this->db->select('amount');
                                      $this->db->where('p_id', $profits['payment_id']);
                                    $this->db->where('payment_date >=', $financial_start_date);
                                    $this->db->where('payment_date <=', $financial_end_date);
                                    $payment_amount = $this->db->get('payments')->row()->amount;

                                    ?>
                                  <td><strong><?php echo  !empty($payment_amount)?$payment_amount:'-'; ?></strong></td>
                             <?php } ?>
                        
                           </tr>      
                           <?php    }
                              }
                                                
                           } ?>
                          <tr class="total-row">
                            <td><strong>Total Asset</strong></td>
                             <?php foreach($revenue as $revenues) {?>
                               <td><strong><?php echo !empty($revenues)?$revenues:'-'; ?></strong></td>
                         <?php } ?>
                          </tr>
                          <tr>
                            <td colspan="10"><strong>Liabilities</strong></td>
                          </tr>
                           <?php 
                              $start_years = Date('Y', strtotime("-8 Year"));
                              $current_year = date("Y");

                              for ($i=$start_years; $i <=$current_year ; $i++) { 
                               
                              
                            if(!empty($expenses1[$i])) {
                               foreach($expenses1[$i] as $expense) {
                                    // $expenses_title = json_decode($expense['expenses_title']);
                                    // $expenses_title = implode(',', $expenses_title);
                                ?>
                                  <tr>
                            
                               <td><?php echo $expense['expenses_title'].'('.$expense['notes'].')'; ?></td>

                               <?php
                               for ($j=$start_years; $j <=$current_year ; $j++) { 
      
                                    $years[]= $j;
                                    $end_year = $j+1;
                                      $financial_start_date = $i.'-04-1';
                                      $financial_end_date = $end_year.'-03-31';
                                      $this->db->select('amount');
                                      $this->db->where('id', $expense['id']);
                                    $this->db->where('expense_date >=', $financial_start_date);
                                    $this->db->where('expense_date <=', $financial_end_date);
                                    $expenses = $this->db->get('expenses')->row()->amount;

                                    ?>
                               <td><strong><?php echo  !empty($expenses)?$expenses:'-'; ?></strong></td>
                             <?php } ?>
                        
                           </tr>      
                           <?php    }
                              }
                                                
                           } ?>
                           <tr>
                            <td>Taxes</td>
                            <?php foreach($tax_amount as $tax_amounts) {?>
                               <td><strong><?php echo !empty($tax_amounts)?$tax_amounts:'-'; ?></strong></td>
                         <?php } ?>
                          </tr>
                          <tr class="total-row">
                            <td><strong>Total Liabilities</strong></td>
                            <?php foreach($overall_expenses as $overall_expense) {?>
                               <td><strong><?php echo !empty($overall_expense)?$overall_expense:'-'; ?></strong></td>
                         <?php } ?>
                          </tr>
                          <tr>
                            <td><strong>Shareholder's Equity</strong></td>                            
                          </tr>
                           <tr>
                            <td>Shareholder's Equity</td>
                            <?php foreach($net_earnig as $net_earnigs) {?>
                               <td><strong><?php echo !empty($net_earnigs)?$net_earnigs:'-'; ?></strong></td>
                         <?php } ?>
                          </tr>                        
                         
                        </tbody>
                      </table>
                    </div> 


                