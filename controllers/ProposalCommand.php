<?php
class ProposalCommand extends APP_Controller_LenderCommand
{
	 

	/**
	 * [indexGET description]
	 * 
	 * @param  [type] $request  [description]
	 * @param  [type] $response [description]
	 * 
	 * @return [type]           [description]
	 */
	public function historyGET( $request, $response )
	{
		$view 	= $this->view();
		$loan = $this->param('loan',0);

		$type 	= (string)$this->param( 'type', 'accepted' );

		$datagrid = new ModelDatagrid( 'mag_proposal inner join mag_loan on ( mag_loan.loan_id = mag_proposal.loan_id ) ', array( 'mag_proposal.*', 'mag_loan.loan_id' ), false );

		if(!empty($loan)){
			$datagrid->setCondition( ' mag_proposal.proposal_status in ( ?, ?, ?, ? ) and mag_proposal.proposal_archived = ? and mag_proposal.user_id = ? AND mag_loan.loan_status in ( ?, ?, ? ) AND mag_proposal.loan_id = ?', array( 'ACCEPTED', 'PENDING', 'REJECTED', 'RETRACTED', 'NO', $this->user()->id(), 'ACTIVE', 'ARCHIVED', 'EXPIRED' , $loan ) );

		}else{
			$datagrid->setCondition( ' mag_proposal.proposal_status in ( ?, ?, ? ) and mag_proposal.proposal_archived = ? and mag_proposal.user_id = ? AND mag_loan.loan_status in ( ?, ?, ? ) ', array( 'ACCEPTED', 'PENDING', 'REJECTED','NO', $this->user()->id(), 'ACTIVE', 'ARCHIVED', 'EXPIRED' ) );
		}

		$datagrid->allowSortBy( 'mag_proposal.proposal_id', 'mag_proposal.proposal_amount', 'mag_proposal.proposal_equity', 'mag_proposal.proposal_term', 'mag_proposal.proposal_rate', 'mag_proposal.proposal_added', 'mag_proposal.proposal_status' );
		$datagrid->setInitialSortBy( array( 'mag_proposal.proposal_added' => 'desc' ) );
		$datagrid->setPerPage( 10 );
		
		$view->current_page = 'my-proposals';
		$view->proposals = $datagrid;
		$view->loan_model = new LoanModel();
		$view->loan_id = $loan;
		$view->proposal_model = new ProposalModel();
		
		$view->display( 'proposal/history.php' );
	}
	
	/**
	 * [optimalBlueProposalsGET description]
	 * 
	 * @param  [type] $request  [description]
	 * @param  [type] $response [description]
	 * 
	 * @return [type]           [description]
	 */
	public function optimalBlueProposalsGET( $request, $response )
	{
		
		$view 	= $this->view();

		$config = RPC_Registry::get( 'config' );
		if($config['white_label']['allowfakestuff']): 
			$email = 'something@gmail.com';
		else:
			$email = '' ;
		endif;
		

		$loan_id 		= (int)$this->param( 'loanid', 0 );
		
		
		$tAndC = "";

		$BES = "";
		$BESSearchId = "";

		$product = "";
		$FPSSearchId = "";

		// Declaring all Arrays
		$details = array();

		$BESProdIdArray = array();
		$FPSProdIdArray  = array();
		

		$recal		= (string)$this->param( 'recal', 0 );
		// $withObpresets = (string)$this->param( 'withObpresets', 0 );

		if($recal == true)
		{
			$armratetypes = @$request->get['p']['proposal_armratetypes'];
			
			$details =  
					array( 'downpayment'  => @$request->get['p']['proposal_downpayment'],
				 		   'amount' 	  => @$request->get['p']['proposal_amount'], 
				 		   'equity' 	  => @$request->get['p']['proposal_equity'], 
				 		   'rate'		  => @$request->get['p']['proposal_rate'], 
				 		   'rate_type'	  => @$request->get['p']['proposal_rate_type'], 
				 		   'term'		  => @$request->get['p']['proposal_term'],
				 		   'arm_rate_type'=> $armratetypes );
		
				// var_dump($details);
		}
		else
			LogAction::obclog("optimalBlueProposalsGET: recal set as not true");

		

		
		LogAction::obclog("optimalBlueProposalsGET: loan_id ".$loan_id );
		
		$model 	= new LoanModel();
		$loan 	= $model->loadBySql( ' loan_id = ? ', array( $loan_id ) );

		$user_model = new UserModel();
		//borrower
		$borrower  = $user_model->loadBySql( 'user_id = ? and user_status = ?', array(  $loan->user_id() , 'ACTIVE' ) );

		//lender
		$lender = $this->user();

		//Optimal Blue Client Implementation
		$obClient = new OBCManager();


		//Create the json from loaninfo and the borrower info
		if($recal)
			$data = $obClient->OBCBorrowerJson( $loan,$lender, $borrower , true , $details);
		else
			$data = $obClient->OBCBorrowerJson( $loan,$lender, $borrower, false, $details);


		LogAction::obclog("OBM: data");


		if(!empty($data))
		$view->data = $data;
		// var_dump($data);
		

		//Step :1 ::::::validate the client if, with the clientid and clientsearch, token gets generated or not for Magilla::::::
		$validToken = $obClient->accessToken();
		if(!is_null($validToken))
		{

			//Step :2 ::::::check if the user_email is attached to the clientID provided by OBC:::::: 

			$getcustomers = $obClient->MSSAvailableStrategies();

			// Step :3 ::::::search for the user_email and get the originatorid and the businesschannelid associated with it::::
			foreach ($getcustomers as $cusindex => $cusValues) 
				foreach ($cusValues->businessChannels as $cusBCID => $cusBCValues) 
					foreach($cusBCValues as $cusOID => $cusOValues)
						if($cusOID == "originators")
							foreach($cusOValues as $cusokey => $cusovalues)
							{
							
								if(isset($cusovalues->email) && $cusovalues->email == $email )
								{	


									$tAndC = true;
									$oId = $cusovalues->index;
									$bCId = $cusBCValues->index ;

									$validate = $obClient->originatorBusinessChannelID($bCId, $oId);
									LogAction::obclog($validate);
								
								
									if($validate)
									{

										//Step 4: :::::: ****BEST EXECUTION SEARCH IMPLEMETATION ****:::::

										$BES = $obClient->BestExecutionSearch($data);
										// var_dump($BES);
										
										if(!empty($BES))
										{

											if(!empty($BES->messages[0]->message) && isset($BES->messages[0]->message))
											{
												$view->BESerror = $BES->messages[0]->message;
												LogAction::obclog("BESerror has been detected ");
											}

											if(isset($BES->products))
											{
												$view->BES = $BES->products;

												LogAction::obclog("BES is available"); 
												
												//get all the productids of BES in an array and use them as per needed
												foreach($BES->products as $BESProdKey => $BESProdValue)
													array_push($BESProdIdArray,$BESProdValue->productId);
												
													//using the searchid and the productid from the array get the list of BES details in the view
													if(!empty($BESProdIdArray))
													$view->BESProdIdArray = $BESProdIdArray;
											}	

											//search id
											if(isset($BES->searchId))
											{
												$BESSearchId  = $BES->searchId;
												$view->BESSearchId = $BESSearchId;
											}
											

													// var_dump($BESSearchId);
											

										}
										else
											{
												$view->BES = $BES;
												LogAction::obclog("BES is empty");
											} 



										//Step 5: :::::: *** FULL PRODUCT SEARCH - FPS***::::::
										$product   = $obClient->FullProductSearch($data);
										
										// var_dump($product);
										
										if(!empty($product))
										{
												$view->product = $product;
												
												//check for errors
												if(!empty($product->messages[0]->message))
												{
													$view->perror = $product->messages[0]->message;
													LogAction::obclog( "product->message has been detected");
												}

												//Check for incorrect input to OB
												if(isset($product->modelState))
													foreach ($product->modelState as $key => $value) 
													{
														$producterror = $value[0];
														$view->producterror = $producterror;
														LogAction::obclog("key".$key."value".$value[0] );

													}
												
												//get all the productids from the "FPS" in an array and use them as per need
												if(isset($product->products))
												{
													foreach($product->products as $productsKey => $productsValue)
														array_push($FPSProdIdArray,$productsValue->productId);
												}
												//using the searchid and the productid from the array get the list of FPS details in the view
												if(!empty($FPSProdIdArray))
												$view->FPSProdIdArray = $FPSProdIdArray;
												
												// var_dump($FPSProdIdArray);
												
												//get the search id 
												if(isset($product->searchId))									
												$FPSSearchId  = $product->searchId;
												// var_dump($FPSSearchId);
											


												//ineligible products for only the FPS
												$ineligibleProds = $obClient->FPSIneligibleProducts($FPSSearchId);
												if(!empty($ineligibleProds))
														$view->ineligibleProds = $ineligibleProds;
													else
														$view->ineligibleProds = "";




										}
										else
											{
												$view->product = $product;
												LogAction::obclog("ProposalCommand: productId and searchId is not getting pulled");
											}

											$view->obClient = $obClient ;
									
									}
									else
									{

										LogAction::obclog("validation failed for OBClient in Proposal Command");
									}
									
								}
								
							}


							$view->tAndC = $tAndC;
							$view->loan = $loan;

							//1st window where the obtimal blue chart is obtained from the presets

							// if($withObpresets)
							// {
							// 	$view->display( 'proposal/obpresetsproposals.php' );
							// 	exit;
							// }
							// else{
							// 	$view->display( 'proposal/obproposals.php' );
							// }
								
								

		}
		else
		{
			LogAction::obclog("Unable to create the token for Magilla, Check clientId and clientsearch of Magilla with OBC");
		}
		

		
		//Optimal Blue Ends
		
	}


	/**
	 * [optimalBlueProposalsPOST description]
	 * 
	 * @param  [type] $request  [description]
	 * @param  [type] $response [description]
	 * 
	 * @return [type]           [description]
	 */
	public function optimalBlueProposalsPOST( $request, $response )
	{
		$view 		= $this->view();
		$loan 		= $view->loan;

	}


	// *
	//  * [optimalBlueProposalsTearDown description]
	//  * 
	//  * @param  [type] $request  [description]
	//  * @param  [type] $response [description]
	//  * 
	//  * @return [type]           [description]
	 
	public function optimalBlueProposalsTearDown( $request, $response )
	{

		$this->view()->display( 'proposal/obproposals.php' );

	}



}

?>
