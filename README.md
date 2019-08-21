# PHP-API-Integration-OptimalBlueClient
TECHNOLOGY USED: 
This is done in PHP Programming Language. The framework is Zend (customized). It is using MVC architecture and curl to communicate with the server. I have used PHP OOPs principle. Also includes Json, ajax, css, jquery, js, mysql, etc. 

Below explained which folder contains what and how they are connected to each other.

API FOLDER:
•	It contains a folder called OptimalBlueClient. This folder consists of json files which is a default json file for the OBC API.
•	Note:  borrowerInformation.json is mainly being used.

•	OBCManager.php is helping the OptimalBlueClient.php

•	In the OBCManager.php, the protected variables are kept as either blank or Some Number as those are proprietary, please refer the below Image:
 
	
	This class file is basically helping the OptimalBlueClient.php to get all the information it needs. This class file is responsible for sanitizing the information, formatting them as needed, cleaning the junk if any, does the food-prep in other words.

•	Similarly,
In the OptimalBlueClient.php all the const under the OptimalBlueClient class is kept as url as those are proprietary, please refer the below Image:
 

	This class is responsible for all the server end to end connection. So, it receives all the clean prepped food from the OBCManager.php and use that to communicate with OptimalBlue API urls.

These two classes are added in the init.php. Below snippet:

 


CONTROLLER: This is the part which deals with the users’ requests for resources from the server

•	This folder contains the class file named as ProposalCommand.php :
o	In this class file you can see the functions for optimalBlueProposalsGET, optimalBlueProposalsPOST and optimalBlueProposalsTearDown
	optimalBlueProposalsGET: 
	This function basically prepares what has to be shown in the view for the users. It is trying to get the OB Best Search Execution for the lender depending on the json input to the API. 
	The json input is taken from the loan information which is already saved in the database.
	The OBCManager in the API folder is being accessed by 
$obClient = new OBCManager(); line119

 If you see the if else loop in the line 123 to 125, you can see that the loan information along with others are send in OBCBorrowerJson() which is a function in OBManager.php

	optimalBlueProposalsPOST:
	There is nothing in it as from the view nothing is being posted hence this is kept empty
	optimalBlueProposalsTearDown
	This has the view file which has to be displayed to the user

VIEW:  This part deals with presenting the data to the user. This is usually in form of HTML pages.

	This folder contains: proposal/obproposals.php which is referred in optimalBlueProposalsTearDown in Controller Class file.

	I have added the css styling on top section and js in the bottom section of the file to give a judgement of how the css and js are used. They are generally kept in some css and js folder and referred there.


MODEL:  
Just for Integration it did not need any Database connectivity. So, I haven’t used it. But if we need to access anything from the database we can code in a file and save it in model 
This generally contains the business logic and the application data. It can be used to perform data validations, process data and store it. The data can come from:
	flat file
	database
	XML document
	Other valid data sources

