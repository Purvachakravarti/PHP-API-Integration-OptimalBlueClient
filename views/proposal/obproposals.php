<style>
    .bes-head,
    .fps-head,
    .ip-head {
        cursor: pointer;
        background-color: #f5f5f5;
    }
    .bes-prop {
        display: none;
    }
    .fs-prop {
        display: none;
    }
    .ip {
        display: none;
    }
    .flag,
    .flag-blue,
    .flag-orange,
    .flag-green,
    .flag-red,
    .flag-yellow {
        width:15px;
        padding:0 !important;
    }
    .flag-blue {
        background-color:#242f47;
    }
    .flag-orange {
        background-color:#ea7233;
    }
    .flag-green {
        background-color:#46af50;
    }
    .flag-red {
        background-color:#b72e2a;
    }
    .flag-yellow {
        background-color:#f2e103;
    }
    .action-desktop {
        float: right;
    }
    .action {
        float: right;
    }
    .action-expand {
        float: right;
    }
    .mag-container {
        /* border:1px #242F47 solid; */
        padding:0.5em;
        border-radius:0 !important;
        margin: 0.5em;
    }
    .mag-container .ob-div {
        overflow: auto;
        max-height: 450px;
    }
    .magilla-table h4 {
        font-size:1.2em; 
        text-align:left;
    }
    .table {
        margin-bottom: 0px;
    }
    .ob-div .magilla-table.table > tbody > tr > td, 
    .ob-div .magilla-table.table > tbody > tr > th, 
    .ob-div .magilla-table.table > tfoot > tr > td, 
    .ob-div .magilla-table.table > tfoot > tr > th, 
    .ob-div .magilla-table.table > thead > tr > td, 
    .ob-div .magilla-table.table > thead > tr > th {
        text-align: center;
        vertical-align: middle !important;
        max-width: 250px;
        padding: 7px 2px !important;
        font-size: 12px;
    }
    .grey{
        border-color: grey !important;
    }
    .grey:hover{
        background-color: lightgray !important;
        border-color: grey !important;
    }
    #ineligibleTable td {
        text-align: left;
    }

    /* MOBILE */
    .proposal-card {
        box-shadow: 1px 1px 0.5em rgba(28,57,120,0.7);
        border-radius:5px;
        margin-bottom: 20px;
        width: -webkit-fill-available !important;
        padding-left:0;
        padding-right:0;
    }
    .bes-prop-mobile,
    .fs-prop-mobile,
    .ip-mobile {
        display: none;
    }
    .bes-head-mobile,
    .fps-head-mobile,
    .ip-head-mobile {
        cursor: pointer;
        background-color: #f5f5f5;
        margin: 7px 0;
        -webkit-box-shadow: 0.5px 0.5px 0px 0px rgba(0,0,0,0.75);
        -moz-box-shadow: 0.5px 0.5px 0px 0px rgba(0,0,0,0.75);
        box-shadow: 0.5px 0.5px 0px 0px rgba(0,0,0,0.75);
        height: 56px;
        border-radius: 5px;
    }
    .bes-head-mobile h3,
    .fps-head-mobile h3,
    .ip-head-mobile h3 {
        float: left;
        margin: 0 0 0 12px;
        line-height: 56px;
    }
    .bes-head-mobile button,
    .fps-head-mobile button,
    .ip-head-mobile button {
        margin: 11px;
    }
    .ob-div-mobile .proposal-card > table > tbody > tr > td, 
    .ob-div-mobile .proposal-card > table > tbody > tr > th {
        padding:0px 0px 0px 15px;
    }
    .ob-snackbar {
        display: none;
        width: 400px;
    }
    .ip-name {
        padding:12px;
        font-weight:600;
        text-align:center;
        font-size:1.0em;
        background-color:#242F47;
        color:#fff;
        border-radius:5px 5px 0px 0px;
    }
</style>

<?php 
    $fieldsTitle = array(
        'rate'=>'Rate',
        'closingCost'=>'Closing Cost',
        'investor'=>'Investor',
        'amortizationTerm'=>'Amortization Term',
        'loanTerm'=>'Loan Term',
        'amortizationType'=>'Amortization Type',
        'price'=>'Price',
        'armMargin'=>'ARM Margin',
        'apr'=>'APR',
        'principalAndInterest'=>'P&I',
        'discount'=>'Discount',
        'productName'=>'Product Name'
    );
    $fields = array( 'rate','closingCost','investor','amortizationTerm','loanTerm','amortizationType','price','armMargin','apr','principalAndInterest','discount','productName');
    $moneyFields = array('closingCost','principalAndInterest','discount');
    $percentageFields = array('apr','rate');
   
    $obarray = array();
    $price = array();

    if(!empty($BES))
    //create an array of all the prices
    foreach($BES as $key => $proposal)
    {
        array_push($price, $proposal->price);

    }
    $search = 100;
   //get closest to 100
   // $besprice = $obClient->getClosest($search, $price);
    ?>

<!-- <div class="bootstrap-growl alert alert-info ob-snackbar">Product data added to proposal form.</div> -->

<div class="mag-container">
<!-- ------ -->
<!-- MOBILE -->
<!-- ------ -->
    <div class="ob-div-mobile visible-xs visible-sm visible-md">
        <!-- <div align="center"><img style="max-height:35px;" src="/skin/img/optimalblue.png"></div> -->
        <?php if($tAndC == true): ?>
            <?php if(!empty($BES)): ?>
            <?php foreach($BES as $key => $proposal){ ?>
                <?php if($proposal->price >= $search): ?>
                <div class="proposal-card">
                    <table class="magilla-table table table-light">
                        <tr>
                            <?php  foreach($proposal as $propField => $propValue)

                                if(in_array( $propField, $fields )): ?>
                                    <tr data-attribute-name="<?= $propField ?>">
                                    <th><?= $fieldsTitle[$propField] ?></th>
                                 <?php if(!empty($propValue)) : 
                                        echo "<td class = 'propValue' id = '$propField' data-attribute-name ='$propField' data-attribute-value = '$propValue'>";
                                                    if(in_array($propField, $moneyFields))
                                                    {
                                                        echo money_format('%0.2n', (double)$propValue);
                                                    }  
                                                    else if(in_array($propField, $percentageFields))
                                                    {
                                                      echo $propValue."%"; 
                                                    } 
                                                    else 
                                                        echo $propValue;
                                                echo "</td></tr>";
                                    else:   
                                        echo "<td> - </td></tr>";
                                    endif;
                                    $obarray[$propField] = $propValue;

                                endif;
                            ?>
                                <tr><th colspan="2" style="text-align:center;"><button class="btn btn-outline green action-mobile" title="Paste" type="button"><i class="fa fa-paste"></i></button></th></tr>
                    </table>
                </div>
                <?php break; endif; ?>
            <?php } ?>

            <div class="bes-head-mobile">
                <h3 style="display:inline-block;">Best Execution Search</h3>
                <button style="display:inline-block;" class="btn btn-outline grey action-expand" id="action" title="Expand" type="button">
                    <i class="fa fa-plus-square action"></i>
                </button>
            </div>
            <?php foreach($BES as $key => $proposal){ ?>
                <div class="proposal-card bes-prop-mobile">
                    <table class="magilla-table table table-light">
                        <tr>
                            <?php  foreach($proposal as $propField => $propValue)

                                if(in_array( $propField, $fields )): ?>
                                    <tr data-attribute-name="<?= $propField ?>">
                                    <th><?= $fieldsTitle[$propField] ?></th>
                                 <?php if(!empty($propValue)) : 
                                        echo "<td class = 'propValue' id = '$propField' data-attribute-name ='$propField' data-attribute-value = '$propValue'>";
                                                    if(in_array($propField, $moneyFields))
                                                    {
                                                        echo money_format('%0.2n', (double)$propValue);
                                                    }  
                                                    else if(in_array($propField, $percentageFields))
                                                    {
                                                      echo $propValue."%"; 
                                                    } 
                                                    else 
                                                        echo $propValue;
                                                echo "</td></tr>";
                                    else:   
                                        echo "<td> - </td></tr>";
                                    endif;
                                    $obarray[$propField] = $propValue;

                                endif;
                            ?>
                                <tr><th colspan="2" style="text-align:center;"><button class="btn btn-outline green action-mobile" title="Paste" type="button"><i class="fa fa-paste"></i></button></th></tr>
                    </table>
                </div>
            <?php } ?>


            <div class="fps-head-mobile">
                <h3 style="display:inline-block;">Full Product Search</h3>
                <button style="display:inline-block;" class="btn btn-outline grey action-expand" id="action" title="Expand" type="button">
                    <i class="fa fa-plus-square action"></i>
                </button>
            </div>

            <!-- ROWS FOR FULL PRODUCT SEARCH -->
            <?php foreach($product->products as $prodKey => $prodDetail){ ?>
                    <div class="proposal-card fs-prop-mobile">
                    <table class="magilla-table table table-light">
                        <tr>
                            <?php foreach($prodDetail as $prodField => $prodValue)
                                if(in_array( $prodField, $fields )): ?>
                                    <tr data-attribute-name="<?= $prodField ?>">
                                    <th><?= $fieldsTitle[$prodField] ?></th>
                                <?php if(!empty($prodValue)) : 

                                         echo "<td class = 'propValue' id = '$prodField' data-attribute-name ='$prodField' data-attribute-value = '$prodValue'>";
                                                if(in_array($prodField, $moneyFields))
                                                {
                                                    echo money_format('%0.2n', (double)$prodValue);
                                                }  
                                                else if(in_array($prodField, $percentageFields))
                                                {
                                                  echo $prodValue."%"; 
                                                } 
                                                else 
                                                    echo $prodValue;
                                           echo "</td></tr>";
                                    else:   
                                           echo "<td> - </td></tr>";
                                    endif;
                                    
                                endif;
                        ?>
                        
                        <tr><th colspan="2" style="text-align:center;"><button class="btn btn-outline green action-mobile" title="Paste" type="button"><i class="fa fa-paste"></i></button></th></tr>
                        
                    </table>
                </div> 
            <?php } ?>
        <?php $_SESSION['_obarray_'] = $obarray ; 

        elseif(empty($BES)):  ?>
            <?php if(!empty($BESerror)): ?>
                <h3 style="padding:10px 0 20px 0;"><?= $BESerror; ?></h3>
            <?php endif; ?>
        <?php endif;?>

        <?php if( !isset($ineligibleProds->message ) && (isset($ineligibleProds->ineligibleProducts )) && !empty($ineligibleProds->ineligibleProducts )): ?>
            <div class="ip-head-mobile">
                <h3 style="display:inline-block;">Ineligible Products</h3>
                <button style="display:inline-block;" class="btn btn-outline grey action-expand" id="action" title="Expand" type="button">
                    <i class="fa fa-plus-square action"></i>
                </button>
            </div>


            <!-- UNKNOWN -->
            <?php foreach ($ineligibleProds->ineligibleProducts as $iPKey => $iPValue) 
            if($iPValue->ineligibleStatus == "Unknown")
            { 
                echo "<div class='proposal-card ip-mobile'><table class='magilla-table table table-light'>";
                echo "<tr>";
                    echo "<td class='ip-name'>".$iPValue->productName."</td></tr>";
                    echo "<tr><td>";
                    foreach ($iPValue->ineligibleReason as $iRkey => $iRvalue)  
                        echo "- ".$iRvalue."<br>";
                    
                    echo "</td></tr>";
                    echo "<tr><td>".$iPValue->ineligibleStatus."</td></tr>";
                echo "</table></div>";
            }
            ?>
            <!-- DISQUALIFIED -->
            <?php foreach ($ineligibleProds->ineligibleProducts as $iPKey => $iPValue) 
            if($iPValue->ineligibleStatus == "Disqualified")
            { 
                echo "<div class='proposal-card ip-mobile'><table class='magilla-table table table-light'>";
                echo "<tr>";
                    echo "<td class='ip-name'>".$iPValue->productName."</td></tr>";
                    echo "<tr><td>";
                    foreach ($iPValue->ineligibleReason as $iRkey => $iRvalue)  
                        echo "- ".$iRvalue."<br>";
                    
                    echo "</td></tr>";
                    echo "<tr><td>".$iPValue->ineligibleStatus."</td></tr>";
                echo "</table></div>";
            }
            ?>
        <?php endif; ?>

        <?php if(empty($BESerror) && empty($BES)): 
                if(!empty($producterror)): ?>
                    <h3>Error: <?php echo $producterror ?></h3>
          <?php else: ?>
                    <h4>Search was not completed. Please contact <a href="mailto:magilla@magillaloans.com" target="_top">us</a> for further assistance</h4>
                <?php endif; ?>
        <?php endif; ?>
        
        <?php elseif(!($tAndC == true)): ?>
            <h3>Your account is not connected to optimal blue. Please <a href="https://www2.optimalblue.com/product-and-pricing/" rel="nofollow" target="_blank">click here</a> to get more information.</h3>
        <?php endif; ?>

    </div>

<!-- ------- -->
<!-- DESKTOP -->
<!-- ------- -->
        <div class="ob-div visible-lg visible-xl">
            <!-- var_dumps -->
            <div><pre style="text-align: left; display: none" id ="data"><?php var_export($data); ?> </pre></div>
            <div><pre style="text-align: left; display: none" id = "product"><?php var_export($product); ?> </pre></div>
            <div><pre style="text-align: left; display: none" id = "bes"><?php var_export($BES); ?> </pre></div>
            <div><pre style="text-align: left; display: none" id = "ineligible"><?php var_export($ineligibleProds); ?> </pre></div>
            <div><pre style="text-align: left; display: none" id = "ineligibleproducts"><?php var_export($ineligibleProds->ineligibleProducts); ?> </pre></div>


            
            <!-- <div align="center"><img style="max-height:35px;" src="/skin/img/optimalblue.png"></div> -->
            
            <?php if($tAndC == true): ?>
                <?php if(!empty($BES)): ?>
                        <table id="besFpsTable" class="magilla-table table table-light table-hover">
                           <thead>
                                <tr>
                                    <th class="flag"></th>
                                    <th>APR</th>
                                    <th>Closing Cost</th>
                                    <th>Loan Term</th>
                                    <th>Margin</th>
                                    <th>Price</th>
                                    <th>Rate</th>
                                    <th>Discount</th>
                                    <th>P&I</th> 
                                    <!-- <th>Total Payment</th> -->
                                    <th>Amortization Term</th>
                                    <th>Amortization Type</th>
                                    <th>Investor</th>
                                    <th>Product Name</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                               
                                <!-- ROW FOR BEST OF BEST SEARCH EXECUTION -->
                                <?php foreach($BES as $key => $proposal)
                                  { ?> 
                                     <?php //if($proposal->price == $besprice): ?>
                                        <?php if($proposal->price >= $search): ?>
                                        <tr>
                                            <td class="flag-blue"></td>
                                          <?php
                                                foreach($proposal as $propField => $propValue)

                                                    if(in_array( $propField, $fields )):
                                                        if(!empty($propValue)) : 

                                                             echo "<td class = 'propValue' id = '$propField' data-attribute-name ='$propField' data-attribute-value = '$propValue'>";
                                                                    if(in_array($propField, $moneyFields))
                                                                    {
                                                                        echo money_format('%0.2n', (double)$propValue);
                                                                    }  
                                                                    else if(in_array($propField, $percentageFields))
                                                                    {
                                                                      echo $propValue."%"; 
                                                                    } 
                                                                    else 
                                                                        echo $propValue;
                                                               echo "</td>";
                                                        else:   
                                                               echo "<td> - </td>";
                                                        endif;
                                                        $obarray[$propField] = $propValue;

                                                    endif;

                                            ?>
                                            <td>
                                                <button class="btn btn-outline green action-desktop" title="Paste" type="button"><i class="fa fa-paste"></i></button>
                                            </td>

                                        </tr> <?php break; endif; ?>
                                    
                                <?php 
                                  } ?> 


                                <!-- FBEST EXECUTION SEARCH HEADER -->
                                <tr class="bes-head">
                                    <td class="flag-blue"></td>
                                    <td colspan="12">
                                        <h4>Best Execution Search</h4>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline grey action-expand" id="action" title="Expand" type="button">
                                            <i class="fa fa-plus-square action"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- ROWS FOR BEST  EXECUTION SEARCH-->                                
                                <?php 
                                foreach($BES as $key => $proposal)
                                  { ?>
                                        <tr class="bes-prop">
                                            <td class="flag-blue"></td>
                                           <?php
                                                foreach($proposal as $propField => $propValue)
                                                    if(in_array( $propField, $fields )):
                                                        if(!empty($propValue)) : 

                                                             echo "<td class = 'propValue' id = '$propField' data-attribute-name ='$propField' data-attribute-value = '$propValue'>";
                                                                    if(in_array($propField, $moneyFields))
                                                                    {
                                                                        echo money_format('%0.2n', (double)$propValue);
                                                                    }  
                                                                    else if(in_array($propField, $percentageFields))
                                                                    {
                                                                      echo $propValue."%"; 
                                                                    } 
                                                                    else 
                                                                        echo $propValue;
                                                               echo "</td>";
                                                        else:   
                                                               echo "<td> - </td>";
                                                        endif;
                                                        
                                                    endif;
                                            ?>
                                            
                                            <td>
                                                <button class="btn btn-outline green action-desktop" title="Paste" type="button"><i class="fa fa-paste"></i></button>
                                            </td>
                                            
                                        </tr> 
                                    
                                <?php 
                                  } ?>

                                    
                                    
                                <!-- FULL PRODUCT SEARCH HEADER -->
                                <tr class="fps-head">
                                    <td class="flag-green"></td>
                                    <td colspan="12">
                                        <h4>Full Product Search</h4>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline grey action-expand" id="action" title="Expand" type="button">
                                            <i class="fa fa-plus-square action"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- ROWS FOR FULL PRODUCT SEARCH -->
                                <?php 
                                    foreach($product->products as $prodKey => $prodDetail)
                                  { ?>
                                        <tr class="fs-prop">
                                            <td class="flag-green"></td>
                                           <?php
                                                foreach($prodDetail as $prodField => $prodValue)
                                                    if(in_array( $prodField, $fields )):
                                                        if(!empty($prodValue)) : 

                                                             echo "<td class = 'propValue' id = '$prodField' data-attribute-name ='$prodField' data-attribute-value = '$prodValue'>";
                                                                    if(in_array($prodField, $moneyFields))
                                                                    {
                                                                        echo money_format('%0.2n', (double)$prodValue);
                                                                    }  
                                                                    else if(in_array($prodField, $percentageFields))
                                                                    {
                                                                      echo $prodValue."%"; 
                                                                    } 
                                                                    else 
                                                                        echo $prodValue;
                                                               echo "</td>";
                                                        else:   
                                                               echo "<td> - </td>";
                                                        endif;
                                                        
                                                    endif;
                                            ?>
                                            
                                            <td>
                                                <button class="btn btn-outline green action-desktop" title="Paste" type="button"><i class="fa fa-paste"></i></button>
                                            </td>
                                            
                                        </tr> 
                                    
                                <?php 
                                  } ?>
                                 </tbody>
                        </table> 

                    <?php $_SESSION['_obarray_'] = $obarray ; 

        elseif(empty($BES)):  ?>
            <?php if(!empty($BESerror)): ?>
                <h3 style="padding:10px 0 20px 0;"><?= $BESerror; ?></h3>
            <?php endif; ?>
        <?php endif;?>
        
        
        
        <?php
        if( !isset($ineligibleProds->message ) && (isset($ineligibleProds->ineligibleProducts )) && !empty($ineligibleProds->ineligibleProducts )): ?>
            <!-- INELIGIBLE TABLE -->
            <table id="ineligibleTable" class="magilla-table table table-light table-hover">
                <!-- INELIGIBLE PRODUCTS HEADER -->
                <tr class="ip-head">
                    <td class="flag-red"></td>
                    <td colspan="2">
                        <h4>Ineligible Products</h4>
                    </td>
                    <td>
                        <button class="btn btn-outline grey action-expand" id="action" title="Expand" type="button">
                            <i class="fa fa-plus-square action"></i>
                        </button>
                    </td>
                </tr>
                <!-- TABLE FOR UNKNOWN STATUS -->
                <?php foreach ($ineligibleProds->ineligibleProducts as $iPKey => $iPValue) 
                if($iPValue->ineligibleStatus == "Unknown")
                { 
                    echo "<tr class='ip'>";
                        echo "<td class='flag-yellow'></td>";
                        echo "<td>".$iPValue->productName."</td>";
                        echo "<td>";
                        foreach ($iPValue->ineligibleReason as $iRkey => $iRvalue)  
                            echo "- ".$iRvalue."<br>";
                        
                        echo "</td>";
                        echo "<td>".$iPValue->ineligibleStatus."</td>";
                        
                    echo "</tr>";
                }
                ?>
                <!-- TABLE FOR DISQUALIFIED STATUS -->
                <?php foreach ($ineligibleProds->ineligibleProducts as $iPKey => $iPValue) 
                if($iPValue->ineligibleStatus == "Disqualified")
                { 
                    echo "<tr class='ip'>";
                        echo "<td class='flag-red'></td>";
                        echo "<td>".$iPValue->productName."</td>";
                        echo "<td>";
                        foreach ($iPValue->ineligibleReason as $iRkey => $iRvalue)  
                            echo "- ".$iRvalue."<br>";
                        
                        echo "</td>";
                        echo "<td>".$iPValue->ineligibleStatus."</td>";
                        
                    echo "</tr>";
                }
                ?>
             </table>
        <?php endif; ?>

        <?php if(empty($BESerror) && empty($BES)): 
                if(!empty($producterror)): ?>
                    <h3>Error: <?php echo $producterror ?></h3>
          <?php else: ?>
                    <h4>Search was not completed. Please contact <a href="mailto:magilla@magillaloans.com" target="_top">us</a> for further assistance</h4>
          <?php endif; ?>
        <?php endif; ?>
        
        <?php elseif(!($tAndC == true)): ?>
            <h3>Your account is not connected to optimal blue. Please <a href="https://www2.optimalblue.com/product-and-pricing/" rel="nofollow" target="_blank">click here</a> to get more information.</h3>
        <?php endif; ?>
        </div>
    </div>


<script type="text/javascript">
    // show / hide search results for desktop
    $('.bes-head').on('click',function(){
        $('.bes-prop').toggleClass('shown').fadeToggle(400);
        if($('.bes-prop').hasClass('shown')){
            toCollapse($(this));
        } else {
            toExpand($(this));
        }
    });
    $('.fps-head').on('click',function(){
        $('.fs-prop').toggleClass('shown').fadeToggle(400);
        if($('.fs-prop').hasClass('shown')){
            toCollapse($(this));
        } else {
            toExpand($(this));
        }
    });
    $('.ip-head').on('click',function(){
        $('.ip').toggleClass('shown').fadeToggle(400);
        if($('.ip').hasClass('shown')){
            toCollapse($(this));
        } else {
            toExpand($(this));
        }
    });

    // show / hide search results for mobile
    $('.bes-head-mobile').on('click',function(){
        $('.bes-prop-mobile').toggleClass('shown').fadeToggle(400);
        if($('.bes-prop-mobile').hasClass('shown')){
            toCollapse($(this));
        } else {
            toExpand($(this));
        }
    });
    $('.fps-head-mobile').on('click',function(){
        $('.fs-prop-mobile').toggleClass('shown').fadeToggle(400);
        if($('.fs-prop-mobile').hasClass('shown')){
            toCollapse($(this));
        } else {
            toExpand($(this));
        }
    });
    $('.ip-head-mobile').on('click',function(){
        $('.ip-mobile').toggleClass('shown').fadeToggle(400);
        if($('.ip-mobile').hasClass('shown')){
            toCollapse($(this));
        } else {
            toExpand($(this));
        }
    });


    // collapse / expand changes for i tag in button
    function toCollapse(icon){
        icon.find('i').toggleClass('fa-plus-square fa-minus-square')
        icon.prop('title', 'Collapse');
    }
    function toExpand(icon){
        icon.find('i').toggleClass('fa-minus-square fa-plus-square');
        icon.prop('title', 'Expand');
    }

    

    //Populate button 
    $('.action-desktop, .action-mobile').click(function(){
        <?php $loan_amount = $data->LoanInformation->BaseLoanAmount; ?>
        var loanamount =  <?php echo $loan_amount; ?>;
        console.log("laob:"+loanamount);
        //refer the function defination in loan/dashboard/lender/details.php
        rowAttributes(this, loanamount);
        // $('.ob-snackbar').fadeIn();
        // setTimeout(function(){$('.ob-snackbar').fadeOut();},3000);

    });


    // move product name value to top
    var productName = $('.proposal-card tr[data-attribute-name="productName"]');
    for(var i = 0; i < productName.length; i++){
        var row = productName[i];
        var th = $(row).find('th');
        var td = $(row).find('td');
        $(th).remove();
        $(td).attr('colspan','2');
        $(td).css({'padding':'12px','font-weight':'600','text-align':'center','font-size':'1.0em','background-color':'#242F47','color':'#fff','border-radius':'5px 5px 0px 0px'});
        var table = $(row).closest('table')
        $(table).prepend(row);
    }
</script>

