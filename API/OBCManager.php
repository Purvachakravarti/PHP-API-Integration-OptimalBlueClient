<?php


class OBCManager 
{ 
    protected $clientId = '';
    protected $clientSecret = '';
    protected $grantType = '';
    protected $resource = '';
    protected $businessChannelId = Some Number;
    protected $originatorId = Some Number;

    /* OBCBorrowerJson
     * @param $loan
     * @param $user
     * (OB)loanterm  = (magilla)$loan->termpreferred
       (OB)armotisation_type  = (magilla)$loan->ratetypepreferred
       (OB)baseloanamount = (magilla)calculating it using loan->downpayment(in percentage) and loan->amount
       (OB)SalesPrice = totalprojectcost = (magilla)$loan->amount()
       (OB)Appraised Value = SalesPrice =(magilla)$loan->amount()
     */
    public function OBCBorrowerJson($loan , $lender, $borrower, $recal="" , $details="")
    {

        $raw_json = file_get_contents(__DIR__."/OptimalBlueClient/borrowerInformation.json");
        $data = json_decode( $raw_json );

        if($lender->obdefaults()) 
        {
          $lOBDefaults = $lender->obdefaults();
        }
        else{
          $lOBDefaults = '';
        }

        if(empty($lOBDefaults))
          $switch = 'default';
        else
          $switch = $lOBDefaults->switch();

        // var_dump($recal);
        if($recal == true) //coming from proposal form on pressing calculate button 
          {
            //downpayment number with no percentage sign (if needed)
            $downpayment = (!is_null($details['downpayment'])) ? (float)$details['downpayment'] : (float)(($loan->downpayment())? (substr($loan->downpayment(),0,-1)) : 0);

            //rate (if needed)
            $rate = (!is_null($details['rate'])) ? (int)$details['rate'] : 0;

            //rate type
            $armotisation_type = (!is_null($details['rate_type'])) ? array((string)$details['rate_type']) : (array((string)(($loan->ratetypepreferred() && $loan->ratetypepreferred() !== "No Preference") ? $loan->ratetypepreferred() : "Fixed")));

            //loan term
            $loan_termpreferred =  (!is_null($details['term'])) ? $this->number_to_word((int)$details['term']) :$this->number_to_word((int)$loan->termpreferred());
            $loan_termpreferred = $loan_termpreferred."Year";
            $loan_termpreferred = str_replace(' ', '',  $loan_termpreferred); 
            $loan_termpreferred = array($loan_termpreferred);
            

            //ARMFixedTerms
             $arm_rate_type = $this->adjratetypeforOB($details['arm_rate_type']);
            
          }
         else //coming from loan in database directly
          {
            
            switch ($switch) 
             {
               case '1': //Lender obdefaults set to 1 means the lender wants to overwrite loan information:
                        //loan term
                          if( $lOBDefaults->loanterm())
                          $loan_termpreferred = explode(",",$lOBDefaults->loanterm());
                          

                          //armotisation type = ratetypepreferred
                          if( $lOBDefaults->armtype())
                          $armotisation_type =  explode(",",$lOBDefaults->armtype());

                          //armotisation fixed terms
                           if($lOBDefaults->armfixedterms()) 
                           $arm_rate_type =  explode(",",$lOBDefaults->armfixedterms());
                break;

               case '0': //Lender obdefaults set to 0 means the lender wants to use loan information 1st:

                          //loan term
                          $loan_termpreferred = $this->number_to_word((int)$loan->termpreferred());
                          $loan_termpreferred = $loan_termpreferred."Year";
                          $loan_termpreferred = str_replace(' ', '',  $loan_termpreferred);
                           $loan_termpreferred = array( $loan_termpreferred );

                          //armotisation type = ratetypepreferred
                          $armotisation_type = array((string)(($loan->ratetypepreferred() && $loan->ratetypepreferred() !== "No Preference") ? $loan->ratetypepreferred() : "Fixed"));

                          //ARMFixedTerms
                          $arm_rate_type = ($loan->adjustableratetypes()) ? ($this->adjratetypeforOB($loan->adjustableratetypes())) : array("ThreeYear", "FiveYear");
               break;
               
               default:
                        //loan term
                          $loan_termpreferred = $this->number_to_word((int)$loan->termpreferred());
                          $loan_termpreferred = $loan_termpreferred."Year";
                          $loan_termpreferred = str_replace(' ', '',  $loan_termpreferred);
                           $loan_termpreferred = array( $loan_termpreferred );

                          //armotisation type = ratetypepreferred
                          $armotisation_type = array((string)(($loan->ratetypepreferred() && $loan->ratetypepreferred() !== "No Preference") ? $loan->ratetypepreferred() : "Fixed"));

                          //ARMFixedTerms
                          $arm_rate_type = ($loan->adjustableratetypes()) ? ($this->adjratetypeforOB($loan->adjustableratetypes())) : array("ThreeYear", "FiveYear");
             
                break;
             }

              //down payment
             $downpayment = (float)(($loan->downpayment())? (substr($loan->downpayment(),0,-1)) : 0);

             }
             
             //*********Common Zone*********
             switch ($switch) 
             {
               case '1': //Lender obdefaults set to 1 means the lender wants to overwrite loan information:

                          //asset documentation
                          if( $lOBDefaults->assetdoc() ) 
                          $assetdoc = (string)$lOBDefaults->assetdoc() ;

                          //DTI
                          if( $lOBDefaults->dtiratio() ) 
                            $dti = (float)$lOBDefaults->dtiratio();

                          //employement documentation
                          if($lOBDefaults->empdoc())
                              $empdoc = (string)$lOBDefaults->empdoc();
                              
                          //income documentation
                          if( $lOBDefaults->incomedoc() ) 
                              $incomedoc = (string)$lOBDefaults->incomedoc();

                          //waive escrows
                          if($lOBDefaults->waiveescrows() == "Yes" ) 
                              $waiveescrows = (boolean)true;
                          else
                              $waiveescrows = (boolean)false; 

                          //fees in
                          if( $lOBDefaults->feesin())
                            $feesin = (string)$lOBDefaults->feesin();

                          //loan type
                          if($lOBDefaults->loantype()) 
                            $loantype = (string)$lOBDefaults->loantype();

                         //EXP. APP. LEVEL(S)
                         if($lOBDefaults->expapplevel()) 
                         $expapplevel = (string)$lOBDefaults->expapplevel();

                         //PRODUCT TYPE
                         if($lOBDefaults->producttype()) 
                         $producttype = (string)$lOBDefaults->producttype();
                        
                         //EXPANDED GUIDELINES:
                         if($lOBDefaults->expandedguidelines()) 
                         $expandedguidelines = explode(",",$lOBDefaults->expandedguidelines());

               break;

               case '0': //Lender obdefaults set to 0 means the lender wants to use loan information 1st:

                          //asset documentation
                          $assetdoc = (string)"Verified" ;

                          //DTI
                          $dti =  (float)37;

                          //employement documentation
                          if($loan->taxreturns() == "Yes" || is_null($loan->taxreturns()))
                            $empdoc = (string)"Verified";

                          //income documentation
                          $incomedoc =  (string)"Verified";
                          
                          //waive escrows
                          $waiveescrows = (boolean)false;

                          //fees in
                          if( $lOBDefaults->feesin())
                            $feesin = (string)"No"; 

                          //loan type
                          if($lOBDefaults->loantype()) 
                            $loantype = (string)"Conforming";

                          //expanded app level
                          $expapplevel = (string)"NotApplicable";

                          //PRODUCT TYPE
                          $producttype = (string)"All";
               break;
               
               default:
                        //asset documentation
                        $assetdoc = (string)"Verified" ; 

                        //DTI
                          $dti =  (float)37;

                        //employement documentation
                        if($loan->taxreturns() == "Yes" || is_null($loan->taxreturns()))
                           $empdoc = "Verified";

                        //income documentation
                        $incomedoc =  (string)"Verified";

                        //waive escrows
                        $waiveescrows = (boolean)false;

                        //fees in
                        // if( $lOBDefaults && $lOBDefaults->feesin())
                            $feesin = (string)"No";

                        //loan type
                        // if($lOBDefaults && $lOBDefaults->loantype()) 
                            $loantype = (string)"Conforming"; 
                        
                        //expanded app level
                        $expapplevel = (string)"NotApplicable"; 

                        //Product type
                        $producttype = (string)"All"; 
             
                break;
             }
           
        //base loan amount field
        $base_loanamount = (float)($downpayment) ? ($loan->amount() - ((float)$downpayment/100 * $loan->amount())) :  (0.8 * $loan->amount());
       
        //creditscore field
        $user_creditscore = ($loan->knowcredit() == "Yes") ? (int)$loan->specificcredit() : substr($loan->creditscore(),0,3);

       // laon purpose field
        if( $loan->form()->name() == "Refinance" )
          $loan_purpose = (int)112;
        else if($loan->form()->name() == "Purchase")
          $loan_purpose = $loan->form()->name();

        //appraised value and sales price fields
        if($loan_purpose == 112 || $loan_purpose == "RefiCashout")
          {
            $appraisedvalue = ($loan->homevalue())? (float) $loan->homevalue() : 0; 
            $salesprice = ($loan->homevalue())? (float) $loan->homevalue() : 0;
          }
        else
          {
            $appraisedvalue = (float)$loan->amount();
            $salesprice = (float)$loan->amount();
          }

        //number of units field
        $numberofunits =(int)$loan->numberofunits();
        if($numberofunits > 4)
          $numberofunits  = 4;

        $numofunits = ($numberofunits > 1) ? $this->number_to_word($numberofunits )."Units" : $this->number_to_word($numberofunits )."Unit"; 
        $numberofunits = (($loan->numberofunits() || !is_null($loan->numberofunits())) ? (string)$numberofunits : (string)"OneUnit"  );
        
        //veteran field
        if($loan->veteran() == "Yes")
          if((strpos($loan->veterantype(), 'Duty - 1st') !== false))
            $veterantype = (int)55439;
          else if ((strpos($loan->veterantype(), 'Duty - Subsequent') !== false))
            $veterantype = (int)55440;
          else if ((strpos($loan->veterantype(), 'Reserves - 1st') !== false))
            $veterantype = (int)55441;
          else if ((strpos($loan->veterantype(), 'Reserves - Subsequent') !== false))
            $veterantype = (int)55442;

        $typeofveteran = ($loan->veteran() == "Yes") ? $veterantype : (string)"ActiveDuty";

        // property type field
        $propertytype = $loan->propertytype();
        $propertytype = str_replace(' ', '',  $propertytype);
        if($loan->propertytype() == "Other types" || $loan->propertytype() == "House" || $loan->propertytype() == "Multi-family")
          $propertytype = "SingleFamily";
        elseif($loan->propertytype() == "Mobile/Manufactured")
          $propertytype = "Modular";
        else
          $propertytype = (($loan->propertytype()) ? (string)$propertytype : (int)115 ); //work with kunal

        //occupancy field
        if($loan->businesstype() == "Primary")
            $loan_occupancy = "PrimaryResidence";
        else if($loan->businesstype() == "First Time Buyer")
             $loan_occupancy = "PrimaryResidence";
        else if($loan->businesstype() == "Home Construction") 
             $loan_occupancy = "InvestmentProperty";
        else if($loan->businesstype() == "Rental") 
             $loan_occupancy = "InvestmentProperty";
        else if($loan->businesstype() == "Second / Vacation") 
             $loan_occupancy = "SecondHome";

        //loan county field
        $loan_county = substr($loan->county(),0,-7);
        $loan_county = ($loan->county()) ? $loan_county : 'Sacramento';

        //*********Common Zone Ends*********


    // Filling of json starts   
       
    $object = $data->BorrowerInformation;

          $object->AssetDocumentation = $assetdoc; // default (int)220
          $object->DebtToIncomeRatio =  $dti; //in percentage
          $object->PledgedAssets = (boolean)false;//default false
          $object->Citizenship = (string)"USCitizen";// default (int)259
          $object->EmploymentDocumentation = (string)$empdoc;// default (int)220;
          $object->FICO = (int)$user_creditscore; //work with kunal
          $object->FirstName = "Magilla";
          $object->LastName = "Loans";
          $object->VAFirstTimeUse = ($loan->veteran() == "Yes") ? (boolean)true : (boolean)false;// default //work with kunal
          $object->MiddleName= null;// default
          $object->Suffix = null;// default
          $object->HomePhone = null;// default
          $object->WorkPhone = null;// default
          $object->Email = null;// default
          $object->DateOfBirth = null;// default
          $object->SSN = null;// default
          $object->FirstTimeHomeBuyer = (($loan->previoushomeowner() == "Yes") ? (boolean)true : (boolean)false); //default takes boolen values
          $object->IncomeDocumentation = $incomedoc;// default (int)199
          $object->TypeOfVeteran = $typeofveteran;// default ****** 
          $object->MonthlyIncome = (float)($loan->monthlyincome() || is_null($loan->monthlyincome())) ? ($loan->monthlyincome()) : 0;
          $object->MonthsReserves = 36;// default
          $object->SelfEmployed = (($loan->selfemployed() == "Yes") ? (boolean)true : (boolean)false); //default takes true
          $object->WaiveEscrows = $waiveescrows; // default
          $object->MortgageLatesX30 = 0;// default
          $object->MortgageLatesX60 = 0;// default
          $object->MortgageLatesX90 = 0;// default
          $object->MortgageLatesX120 = 0;// default
          $object->MortgageLatesRolling = 0;// default
          $object->Bankruptcy = (($loan->bankruptcy() == "No" || is_null($loan->bankruptcy())) ? (string)"Never" : "Never"); //default (int)305 *****
          $object->BankruptcyChapter = null;// default
          $object->BankruptcyDisposition = null;// default
          $object->Foreclosure = (string)"Never"; // default (int)297
          $object->DisclosureDate = null; // default
          $object->ApplicationDate = null; // default
          $object->BankStatementsForIncome = (($loan->bankstatements() == "No") ? (string)"NotApplicable" : null); //default takes 405
          $object->Address1 = null;//default
          $object->Address2 = null;//default
          $object->City = (( $loan->city() )? (string)$loan->city() : null); 
          $object->State = (( $loan->state() ) ? (string)$loan->state() : null); 
          $object->Country = (($loan->country() ) ? (string)$loan->country() : null); 
          $object->ZipCode = ( ($loan->zip()) ? (int)($loan->zip()) : null); 

        $object = $data->LoanInformation;

          $object->LoanPurpose = $loan_purpose; //default takes 106
          $object->LienType = "First"; //default (int)1
          $object->AmortizationTypes = $armotisation_type;//default (int)133 - work with kunal
          $object->ARMFixedTerms = $arm_rate_type;//default (int)271) //work with kunal

          $object->AutomatedUnderwritingSystem = (string)"NotSpecified";//default (int)278
          $object->BorrowerPaidMI = (string)"Yes";//default (int)282
          $object->Buydown = (string)"None" ;//default (int)207
          $object->CashOutAmount = (($loan->cashoutamount())? (float)$loan->cashoutamount() : (float)0); //default takes 0
          $object->DesiredLockPeriod = null; //default 30days
          $object->DesiredPrice = null; //default
          $object->DesiredRate = null; //default
          $object->FeesIn = $feesin; //default
          $object->ExpandedApprovalLevel = $expapplevel;//default takes 481
          // $object->FHACaseAssigned = $FHACaseAssigned;
          // $object->FHACaseEndorsement = $FHACaseEndorsement;
          $object->InterestOnly = (boolean)false;//default
          $object->BaseLoanAmount = $base_loanamount;
          $object->TotalLoanAmountDetails = null;//default
          $object->SecondLienAmount = (float)0;//default
          $object->HELOCDrawnAmount = (float)0;//default
          $object->HELOCLineAmount = (float)0;//default
          $object->LoanTerms = $loan_termpreferred;//*****

          $object->ProductTypes = $producttype;//default
          $object->LoanType = $loantype; //default takes 197 ********
          $object->PrepaymentPenalty = (string)"None";//default (int)211
          $object->ExemptFromVAFundingFee = (boolean)false;//default
          $object->IncludeLOCompensationInPricing = (string)"YesLenderPaid";//default (int)1550
          $object->CustomFields = null;//default ****** add loanid if possible
          $object->CurrentServicer = (string)'Not Applicable';//default
          $object->ExternalStatus = null;//default
          $object->LeadSource = null;//default **** add (string)"Magilla" if possible
          $object->InterestOnlyTerm = null;//default
          $object->CalculateTotalLoanAmount = (boolean)true;//default

         $object = $data->PropertyInformation;

          $object->AppraisedValue = $appraisedvalue;//default 
          $object->Occupancy = $loan_occupancy;//default
          $object->PropertyStreetAddress = $loan->address();
          $object->City = $loan->city();
          $object->County = $loan_county;//find-out
          $object->State = $loan->state();
          $object->ZipCode = $loan->zip();
          $object->PropertyType = $propertytype;//work with kunal
          $object->CorporateRelocation = (boolean)false;//default
          $object->SalesPrice = $salesprice;//default
          $object->NumberOfStories = (int)1;//default
          $object->NumberOfUnits = $numberofunits;//default takes 1
          $object->Construction = (boolean)false; //default
          $object->UniqueDwelling = null;//default
          $object->RepresentativeFICO = (int)$user_creditscore; 
          $object->LoanLevelDebtToIncomeRatiof = $dti; //find-out 
          $object->CoBorrowerInformation = null;
          $object->CustomerInternalId = "OBSearch";
          $object->AdditionalFields = null;

        $data->RepresentativeFICO = (int)850;
        LogAction::obclog("OBM: data2");
          return $data;
    }


    public function createClient()
    {
        LogAction::obclog("OBM: ManagercreateClient");
        return new OptimalBlueClient($this->clientId, $this->clientSecret, $this->grantType, $this->resource, $this->businessChannelId, $this->originatorId);

    }

    /**
     * Get
     */
    public function accessToken()
    {
        $client = $this->createClient();
        return $client->getToken();

    }

    /**
     * Get
     */
    public function accessTokenExpiration()
    {
        $client = $this->createClient();
        return $client->getTokenExpiration();

    }

     /**
     * Get
     */
    public function originatorBusinessChannelID($bCId, $oId)
    {
      LogAction::obclog("OBM: Manager - bcid:".$bCId."oid:".$oId);
      LogAction::obclog("OBM: Manager - bcid:".$this->businessChannelId ."oid:".$this->originatorId);
        if($this->businessChannelId == $bCId && $this->originatorId == $oId)
          return true;
        else 
          return false;
    }


    /**
     * Post
     */
    public function BestExecutionSearch($data)
    {
        LogAction::obclog("OBM: ManagerBestExecutionSearch");
        $client = $this->createClient();
        $result = $client->bestExecutionSearch($data);
        return json_decode($result);
        
    }


     /**
     * Get
     */
    public function BESDetails($searchId , $productId)
    {
        $searchId  = (string)$searchId;
        $productId = (string)$productId;
        
        LogAction::obclog("OBM: ManagerBESDetails");
        $client = $this->createClient();
        $result = $client->bESDetails($searchId, $productId);
        return json_decode($result);
    }

    /**
     * Post
     */
    public function BESAmortizationSchedule($searchId,$productId)
    {
        LogAction::obclog("OBM: ManagerAmortizationScheduleBES");
        $client = $this->createClient();
        $result = $client->bESAmortizationSchedule($searchId,$productId);
        return json_decode($result);
        
    }

    /**
     * Post
     */
    public function FPSAmortizationSchedule($searchId,$productId)
    {
        LogAction::obclog("OBM: ManagerAmortizationScheduleFPS");
        $client = $this->createClient();
        $result = $client->fPSAmortizationSchedule($searchId,$productId);
        return json_decode($result);
        
    }
   
   /**
     * Post
     */
    public function FullProductSearch($data)
    {
         LogAction::obclog("OBM: ManagerFullProductSearch");
        $client = $this->createClient();
        $result = $client->fullProductSearch($data);
        return json_decode($result);
      
    }

    /**
     * Post
     */
    public function FPSWithQM($data)
    {
         LogAction::obclog("OBM: ManagerFPSWithQM");
        $client = $this->createClient();
        $result = $client->fPSWithQM($data);
        
        return json_decode($result);
        
    }

    /**
     * Post
     */
    public function FPSProductGroups($data)
    {
       LogAction::obclog("OBM: ManagerFPSProductGroups");
       $client = $this->createClient();
       $result = $client->fPSProductGroups($data);

        return json_decode($result);
        //if((property_exists($result,'productGroups')))
       
    }


    /**
     * Get
     */
    public function FPSProductDetails($searchId, $productId)
    {
         LogAction::obclog("OBM: ManagerFPSProductDetails");
        $searchId  = (string)$searchId;
        $productId = (string)$productId;

        $client = $this->createClient();
        
        $result = $client->fPSProductDetails($searchId, $productId);

        return json_decode($result);
        //if(property_exists($result, 'quotes')))
        
    }

    

    /**
     * Get
     */
    public function FPSProductGroupDetailsBestPrice($searchId, $productGroupId)
    {
         LogAction::obclog("OBM: ManagerFPSProductGroupDetailsBestPrice");
        // $ProductGroupId = "1_127_133_149_0_0_0";
        // $searchId = "982962064E1497449160";
        $searchId  = (string)$searchId;
        $productGroupId = (string)$productGroupId;

        $client = $this->createClient();

        $quotes = $client->fPSProductGroupDetailsBestPrice($searchId, $productGroupId);
        return json_decode($quotes);

        //if((property_exists($quotes,'quotes')))
      
    }

    /**
     * Get
     */
    public function FPSGuidelineDocument()
    {
         LogAction::obclog("OBM: ManagerFPSGuidelineDocument");
        $isIndex = false;
        $value = "10028_06122017_0756325069.pdf";

        $client = $this->createClient();

        $pdf = $client->fPSGuidelineDocument($isIndex,$value);
        $filename = __DIR__.'./downloads/testGuidlineDocument.pdf';
        $handle = fopen($filename,'w');
        fwrite($handle,$pdf);
        fclose($handle);

        if((mime_content_type($filename) == 'application/pdf'))
            return true;
        else
            return false;
    }

    /**
     * Get
     */
    public function FPSIneligibleProducts($searchId)
    {
         LogAction::obclog("OBM: ManagerFPSIneligibleProducts searchId: ".$searchId);
        // $searchId = "984016757E1497532721";
        $searchId  = (string)$searchId;

        $client = $this->createClient();

        $result = $client->fPSIneligibleProducts($searchId);
        return json_decode($result);
        //if((property_exists($result,'ineligibleProducts')))
       
    }

    /**
     * Get
     */
    public function FPSLenderFees($searchId, $productId)
    {
         LogAction::obclog("OBM: ManagerFPSLenderFees");
        // $searchId = "983125601E1497455176";
        // $productId = "18243808";

        $searchId  = (string)$searchId;
        $productId = (string)$productId;

        $client = $this->createClient();

        $result = $client->fPSLenderFees($searchId, $productId);
        return json_decode($result);
        //if(property_exists($result, 'lenderFeeDetails')))
        
    }

    

    /**
     * Get
     */
    public function MSSAvailableStrategies()
    {
         LogAction::obclog("OBM: ManagerMSSAvailableStrategies");
        $client = $this->createClient();

        $result = $client->mSSAvailableStrategies();
        
        return $result;
        //if(property_exists($result, 'index')))
        
    }

    /**
     * Get
     */
    public function MSSCurrentServicer()
    {
         LogAction::obclog("OBM: ManagerMSSCurrentServicer");
        $client = $this->createClient();

        $result = $client->mSSCurrentServicer();
        return json_decode($result);
        //if(property_exists($result, 'name')))
      
    }

    /**
     * Get
     */
    public function MSSCustomFields()
    {
         LogAction::obclog("OBM: ManagerMSSCustomFields");
        $client = $this->createClient();

        $result = $client->mSSCustomFields();
        return json_decode($result);
        //if(property_exists($result, 'customFieldDescription')))
      
    }

    /**
     * Get
     */
    public function MSSExternalStatus()
    {
         LogAction::obclog("OBM: ManagerMSSExternalStatus");
        $client = $this->createClient();

        $result = $client->mSSExternalStatus();
        return json_decode($result);
    //if(property_exists($result, 'value')))
        
    }

    /**
     * Get
     */
    public function MSSLeadSource()
    {
         LogAction::obclog("OBM: ManagerMSSLeadSource");
        $client = $this->createClient();

        $result = $client->mSSLeadSource();
        return json_decode($result);
    //if(property_exists($result, 'value')))
        
    }

    /**
     * Get
     */
    public function MSSStateAndCounty()
    {
         LogAction::obclog("OBM: ManagerMSSStateAndCounty");
        $client = $this->createClient();

        $result = $client->mSSStateAndCounty();
        return json_decode($result);
       // if(property_exists($result, 'name')))
        
    }


public function number_to_word( $num = '' )
{
    $num    = ( string ) ( ( int ) $num );
   
    if( ( int ) ( $num ) && ctype_digit( $num ) )
    {
        $words  = array( );
       
        $num    = str_replace( array( ',' , ' ' ) , '' , trim( $num ) );
       
        $list1  = array('','one','two','three','four','five','six','seven',
            'eight','nine','ten','eleven','twelve','thirteen','fourteen',
            'fifteen','sixteen','seventeen','eighteen','nineteen');
       
        $list2  = array('','ten','twenty','thirty','forty','fifty','sixty',
            'seventy','eighty','ninety','hundred');
       
        $list3  = array('','thousand','million','billion','trillion',
            'quadrillion','quintillion','sextillion','septillion',
            'octillion','nonillion','decillion','undecillion',
            'duodecillion','tredecillion','quattuordecillion',
            'quindecillion','sexdecillion','septendecillion',
            'octodecillion','novemdecillion','vigintillion');
       
        $num_length = strlen( $num );
        $levels = ( int ) ( ( $num_length + 2 ) / 3 );
        $max_length = $levels * 3;
        $num    = substr( '00'.$num , -$max_length );
        $num_levels = str_split( $num , 3 );
       
        foreach( $num_levels as $num_part )
        {
            $levels--;
            $hundreds   = ( int ) ( $num_part / 100 );
            $hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '' );
            $tens       = ( int ) ( $num_part % 100 );
            $singles    = '';
           
            if( $tens < 20 )
            {
                $tens   = ( $tens ? ' ' . $list1[$tens] . ' ' : '' );
            }
            else
            {
                $tens   = ( int ) ( $tens / 10 );
                $tens   = ' ' . $list2[$tens] . ' ';
                $singles    = ( int ) ( $num_part % 10 );
                $singles    = ' ' . $list1[$singles] . ' ';
            }
            $words[]    = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' );
        }
       
        $commas = count( $words );
       
        if( $commas > 1 )
        {
            $commas = $commas - 1;
        }
       
        $words  = implode( ', ' , $words );
       
        //Some Finishing Touch
        //Replacing multiples of spaces with one space
        $words  = trim( str_replace( ' ,' , ',' , trim( ucwords( $words ) ) ) , ', ' );
        if( $commas )
        {
            $words  = str_replace( ',' , ' and' , $words );
        }
       
        return $words;
    }
    else if( ! ( ( int ) $num ) )
    {
        return 'Zero';
    }
    return '';
}

public function getClosest($search, $arr) {
   $closest = null;
   foreach ($arr as $item) {
      if ($closest === null || abs($search - $closest) > abs($item - $search)) {
         $closest = $item;
      }
   }
   return $closest;
}

  public static function adjratetypeforOB ($ratetypes = null){

      $adjratetypesinloan = array();
      $adjratetypesforOB  = array();
      if( $ratetypes )
      {
        if(is_array($ratetypes)){
          $adjratetypesinloan = $ratetypes;
        }
        else{
        $adjratetypesinloan = array_filter( explode( '|', trim( $ratetypes, '|' ) ) );
        }
      
        $alladjratetypes = array( '1 Mo'=>'OneMonth',
                                  '3 Mo'=>'ThreeMonth',
                                  '6 Mo'=>'SixMonth',
                                  '1 Yr'=>'OneYear',
                                  '2 Yr'=>'TwoYear',
                                  '5 Yr'=>'FiveYear',
                                  '7 Yr'=>'SevenYear',
                                  '10 Yr'=>'TenYear',
                                  '15 Yr'=>'FifteenYear',
                                  '6 YR'=>'SixYear'
                                );
        foreach ($adjratetypesinloan as $adjr) {
          if(isset($alladjratetypes[$adjr])){
            array_push($adjratetypesforOB, $alladjratetypes[$adjr]);
          }
        }
        return $adjratetypesforOB ;
    }
    return null ;
  }

}
