<?php
# ******************************************************
# Initializing the session for the application
# ******************************************************
session_start();

# ******************************************************
# Setting the timezone with UTC by default
# ******************************************************

date_default_timezone_set("UTC");

# ******************************************************
# Loading Bootstrap
# ******************************************************

require 'libs/vendor/autoload.php';
require 'config/config.inc.php';
require 'libs/yodlee/CustomLayout.php';
require 'libs/yodlee/restclient.class.php';
require 'libs/yodlee/ApiLogger.php';

# ******************************************************
# Bootstrap Slim flow app
# ******************************************************

$app = new \Slim\Slim();
$logger = new \Yodlee\ApiLogger();
$rest_client = new \Yodlee\restClient();

$app = \Slim\Slim::getInstance();
$app->config(Yodlee\BaseConfig::$configSlim);

# ******************************************************
# Loading Custom Layout Base for the application
# ******************************************************

$view = new CustomLayout();
$app->config('view', $view);

$view->set_layout('layouts/custom_layout.php');

# ******************************************************
# Middleware to render a view without a Layout Base
# ******************************************************

function no_custom_layout()
{
	$app = \Slim\Slim::getInstance();
	$default_view = new \Slim\View();
	$view = $app->view($default_view);
}

# ******************************************************
# This event is executed before any to request
# ******************************************************

$app->hook("slim.before.router",function() use ($app, $rest_client, $logger){
    $baseURL = (dirname($_SERVER["SCRIPT_NAME"])=="/") ? "" : $_SERVER["SCRIPT_NAME"];
    $baseAssets = (dirname($_SERVER["SCRIPT_NAME"])=="/") ? "" : dirname($_SERVER["SCRIPT_NAME"]);
    $app->view()->appendData(array('baseURL' => $baseURL, "baseAssets" => $baseAssets));

    if(isset($_SESSION["EndPoint"])){
    	$EndPoint = $_SESSION["EndPoint"];
    }else{
		$EndPoint = Yodlee\BaseConfig::$BaseURl;
    }
    $rest_client->setUrlBase($EndPoint);

    // Load the session logger if not has been initialized.
    if(!$logger->isInitialize()){
    	$logger->startSessionLog();
    }

    $rest_client->setLogger($logger);
});

# ******************************************************
# This Action is called for the Api Test Driver 
# To sets the tokens: 							
#  	- cobrandToken 								
#  	- userToken 	
# 	- EndPoint								
# ******************************************************

$app->map('/startFlow', function() use($logger)
{

	$app = \Slim\Slim::getInstance();
	$_SESSION["cobrandToken"] = $app->request->params("cod_session_token");
	$_SESSION["userToken"] = $app->request->params("user_session_token");
	$_SESSION["EndPoint"] = $app->request->params("EndPoint");

	$_SESSION["panel_login_info"]["active"]=false;
	$_SESSION["panel_login_info"]["user_info"]=array();
	$url_flow = $app->urlFor('index');
	$app->redirect($url_flow);
})->via("GET");

# ******************************************************
# Begin the flow for the application
# ******************************************************

$app->map('/', function()
{
	$app = \Slim\Slim::getInstance();
	$cod_session_token = (isset($_SESSION["cobrandToken"])) ? $_SESSION["cobrandToken"] : "";
	$user_session_token = (isset($_SESSION["userToken"])) ? $_SESSION["userToken"] : ""; 

	// If not exist cobrand and user token is redirect to the view of login
	if(empty($cod_session_token) || empty($user_session_token)){
		$url_flow = $app->urlFor('index_login');
		$app->redirect($url_flow);
	}

	// Render in view
	$app->render("index_add_account_site_model.php", array(
		"title" => "Add Account",
		"sub_title" => "",
		"panel_login_info" => array(
			"active" => $_SESSION["panel_login_info"]["active"],
			"user_info" => $_SESSION["panel_login_info"]["user_info"],
			"base_url" => Yodlee\BaseConfig::$BaseURl
			)
	));
})->via("GET")->name("index");

# ******************************************************
# Login of the application
# ******************************************************

$app->map('/login',  function() use($logger)
{
	$app = \Slim\Slim::getInstance();
	$logger->startSessionLog();

	// Render in view
	$app->render("/login.php", array(
	//$app->render("C:/xampp/htdocs/add_site_account/templates/login.php", array(	
		"flow" => "Add Account",
		"title"=>"Login",
		"sub_title"=>""
	));
})->via('GET')->name("index_login");

# ******************************************************
# Destroy the session of the application 
# ******************************************************

$app->map('/logout',  function() use($logger)
{
	$app = \Slim\Slim::getInstance();
	$_SESSION = array();
	$url_flow = $app->urlFor('index_login');
	$app->redirect($url_flow);

})->via('GET')->name("logout");

# ******************************************************
# Login Validator 								  
# Return true if the user logged is valid.  	  
# ******************************************************

$app->map('/check_login_flow', 'no_custom_layout',  function() use($logger,$rest_client)
{
	$app = \Slim\Slim::getInstance();
	$cobrandToken = $userToken = "";
	$username = $app->request->params("username");
	$password = $app->request->params("password");

	// Preparing the short url and parameters necessary for the Api Service required.
	$config = array(
		"url" => Yodlee\BaseConfig::$URL_GET_COBRAND_LOGIN,
		"parameters" => array(
			"cobrandLogin"=> Yodlee\BaseConfig::$CobrandCredentials["username"],
			"cobrandPassword"=> Yodlee\BaseConfig::$CobrandCredentials["password"]
			)
	);
	// Setting the EndPoint
	$rest_client->setUrlBase(Yodlee\BaseConfig::$BaseURl);

	// Calling Api Service for cobrand login
	$cobrand_info = $rest_client->Post($config["url"], $config["parameters"]);

	if(isset($cobrand_info["Body"]->cobrandConversationCredentials)){
		$cobrandToken = $cobrand_info["Body"]->cobrandConversationCredentials->sessionToken;
	}else{
		print json_encode($cobrand_info["Body"]);
		$app->stop();
	}

	// Preparing the short url and parameters necessary for the Api Service required.
	$config = array(
		"url" => Yodlee\BaseConfig::$URL_GET_LOGIN_USER_LOGIN,
		"parameters" => array(
			"login" => $username,
			"password" => $password,
			"cobSessionToken" => $cobrandToken
			)
	);
	$user_info = $rest_client->Post($config["url"], $config["parameters"]);

	if(isset($user_info["Body"]->userContext->conversationCredentials)){
		$userToken = $user_info["Body"]->userContext->conversationCredentials->sessionToken;
		$_SESSION["panel_login_info"]["active"]=true;
		$_SESSION["panel_login_info"]["user_info"]=$user_info["Body"];

	}else{
		print json_encode($user_info["Body"]);
		$app->stop();
	}

	$_SESSION["cobrandToken"] = $cobrandToken;
	$_SESSION["userToken"] = $userToken;
	$_SESSION["EndPoint"] = Yodlee\BaseConfig::$BaseURl;
	
	$_SESSION["login_started"]=true;
	print "true";
})->via('POST');

# ******************************************************
# Returns a Json with details of the 			  
# call to service APIs 							  
# ******************************************************

$app->get("/check-logger", 'no_custom_layout', function() use($rest_client){
	$app = \Slim\Slim::getInstance();
	$logger = $rest_client->getLogger();
	$log = $logger->getLogger();
	$response = ($log=="") ? "" : json_encode($log);
	print $response;
});

# ******************************************************
# Return a list of all those sites
# by of parameter "filter_site"
# ******************************************************

$app->post('/search-site', 'no_custom_layout' ,  function() use ($rest_client)
{
	$app = \Slim\Slim::getInstance();

	$filter_site = $app->request->params("filter_site");
	// Preparing the short url and parameters necessary for the Api Service required.
	$config = array(
		"url" => Yodlee\BaseConfig::$URL_SEARCH_SITE,
		"parameters" => array(
			"cobSessionToken"=> $_SESSION["cobrandToken"],
			"userSessionToken"=> $_SESSION["userToken"],
			"siteSearchString"=> $filter_site
			)
	);

	$response = $rest_client->Post($config["url"], $config["parameters"]);

	// Render in view
	$app->render("search_site.php", array(
		"response" => $response["Body"]
		));
});

# ******************************************************
# Return a form login for the site selected.
# ******************************************************

$app->get('/get-site-login-form', 'no_custom_layout' ,  function() use ($rest_client)
{
	$app = \Slim\Slim::getInstance();
	$filter_siteId = $app->request->params("filter_siteId");

	// Preparing the short url and parameters necessary for the Api Service required.
	$config = array(
		"url" => Yodlee\BaseConfig::$URL_GET_SITE_INFO,
		"parameters" => array(
			"cobSessionToken"=> $_SESSION["cobrandToken"],
			"siteFilter.reqSpecifier"=> 1,
			"siteFilter.siteId"=> $filter_siteId
			)
	);

	$site_info = $rest_client->Post($config["url"], $config["parameters"]);

	$_SESSION["site_info"] = $site_info["Body"];

	// Preparing the short url and parameters necessary for the Api Service required.
	$config = array(
		"url" => Yodlee\BaseConfig::$URL_SITE_LOGIN_FORM,
		"parameters" => array(
			"cobSessionToken"=> $_SESSION["cobrandToken"],
			"userSessionToken"=> $_SESSION["userToken"],
			"siteId"=> $filter_siteId
			)
	);

	$site_login_form = $rest_client->Post($config["url"], $config["parameters"]);

	$_SESSION["site_login_form"] = $site_login_form["Body"];

	// Render in view
	$app->render("site_login_form.php", array(
		"siteId" => $filter_siteId,
		"site_info" => $site_info["Body"]
		));
});

# ******************************************************
# This Action add the site.
# Start a poll refresh with the memSiteAccId created.
# ******************************************************

$app->post('/add-site-account1', 'no_custom_layout' ,  function() use ($rest_client)
{
	$app = \Slim\Slim::getInstance();
	$siteId = $app->request->params("siteId");
	$login = trim($app->request->params("login"));
	$password = trim($app->request->params("password"));
	$confirm_password = trim($app->request->params("confirm_password"));

	$site_info = $_SESSION["site_info"];

	$isValid=true;

	// Verify that username and password are not empty
	if($login==""||$password==""||$confirm_password == ""){
		$_SESSION["error_msg"][] = "All fields are required!";
		$isValid=false;
	}else{
		if($password!=$confirm_password){
			$_SESSION["error_msg"][] = "Passwords must be the same";
			$isValid=false;
		}
	}

	if($isValid) {
		$credentialFields = $_SESSION["site_login_form"];
		$index = 0;
		$credentials = array();

		// Preparing the parameters to send at Api Service
		foreach($credentialFields as  $credentialfield) {
			if(is_array($credentialfield)){
				foreach($credentialfield as $key => $cfield) {
					$credentials[sprintf("credentialFields[%s].%s",$index, "displayName")] = $cfield->displayName;
					$credentials[sprintf("credentialFields[%s].%s",$index, "fieldType.typeName")] = $cfield->fieldType->typeName;
					$credentials[sprintf("credentialFields[%s].%s",$index, "helpText")] = $cfield->helpText;
					$credentials[sprintf("credentialFields[%s].%s",$index, "maxlength")] =  $cfield->maxlength;
					$credentials[sprintf("credentialFields[%s].%s",$index, "name")] =  $cfield->name;
					$credentials[sprintf("credentialFields[%s].%s",$index, "size")] = $cfield->size;
					$credentials[sprintf("credentialFields[%s].%s",$index, "value")] = ($index==0) ? $login : $password;
					$credentials[sprintf("credentialFields[%s].%s",$index, "valueIdentifier")] = $cfield->valueIdentifier;
					$credentials[sprintf("credentialFields[%s].%s",$index, "valueMask")] = $cfield->valueMask;
					$credentials[sprintf("credentialFields[%s].%s",$index, "isEditable")] = $cfield->isEditable;
					$index++;
				}
			}
		}

		$params = array(
			"cobSessionToken"=> $_SESSION["cobrandToken"],
			"userSessionToken"=> $_SESSION["userToken"],
			"siteId"=> $siteId,
			"credentialFields.enclosedType" => "com.yodlee.common.FieldInfoSingle"
		);

		foreach ($credentials as $key => $credential) {
			$params[$key] = $credential;
		}

		// Preparing the short url and parameters necessary for the Api Service required.
		$config = array(
			"url" => Yodlee\BaseConfig::$URL_ADD_SITE_ACCOUNT1,
			"parameters" => $params
		);

		$add_site_account1 = $rest_client->Post($config["url"], $config["parameters"]);

		$memSiteAccId = (isset($add_site_account1["Body"]->siteAccountId)) ? $add_site_account1["Body"]->siteAccountId:"";
		if($memSiteAccId){

			// Render in view
			$app->render("poll_refresh_site_account.php", array(
				"memSiteAccId" => $memSiteAccId,
				"site_info" => $site_info
			));
		} else {

			$messages = array("No result.");
			// Render in view
			$app->render("view_msg_errors.php", array(
				"messages" => $messages
			));
		}
	} else {
		// Render in view
		$app->render("site_login_form.php", array(
			"siteId" => $siteId,
			"site_info" => $_SESSION["site_info"]
		));
	}
});

# ******************************************************
# Eval a memSiteAcc for MFA or Normal 
# Poll Get Refresh Info
# ******************************************************

$app->post('/get-site-refresh-info', 'no_custom_layout' ,  function() use ($rest_client)
{
	$app = \Slim\Slim::getInstance();
	$memSiteAccId = $app->request->params("memSiteAccId");
	
	// Preparing the short url and parameters necessary for the Api Service required.
	$config = array(
		"url" => Yodlee\BaseConfig::$URL_GET_SITE_REFRESH_INFO,
		"parameters" => array(
			"cobSessionToken"=> $_SESSION["cobrandToken"],
			"userSessionToken"=> $_SESSION["userToken"],
			"memSiteAccId"=> $memSiteAccId
			)
	);

	$get_site_refresh_info = $rest_client->Post($config["url"], $config["parameters"]);
	
	$siteRefreshStatus = $get_site_refresh_info["Body"]->siteRefreshStatus->siteRefreshStatus;
	$refreshMode = $get_site_refresh_info["Body"]->siteRefreshMode->refreshMode;

	// If refreshMode is MFA start a get mfa response
	// You can find more information about this on website:
	// http://developer.yodlee.com/Indy_FinApp/Aggregation_Services_Guide/Aggregation_REST_API_Reference
	if($refreshMode=="MFA"){

		// CASE: MFA
		if($siteRefreshStatus=="LOGIN_FAILURE"){
			$error = Yodlee\BaseConfig::getError(402);
			$messages = array($error["description"]);
			// Render in view
			$app->render("view_msg_errors.php", array(
				"messages" => $messages
			));
		}

		if($siteRefreshStatus=="REFRESH_TRIGGERED"){
			$site_info = $_SESSION["site_info"];
			// Render in view
			$app->render("view_get_mfa_response_for_site.php", array(
				"memSiteAccId" => $memSiteAccId,
				"site_info" => $site_info
			));
		}

		// If siteRefreshStatus == REFRESH_COMPLETED then redirect to the view for render a item summary for site.
		if($siteRefreshStatus=="REFRESH_COMPLETED" || $siteRefreshStatus=="REFRESH_TIMED_OUT"){
			$url_get_item_summaries_for_site = $app->urlFor('getItemSummariesForSite', array('memSiteAccId' => $memSiteAccId));
			$app->redirect($url_get_item_summaries_for_site);
		}else{
			print "";
		}

	}else if($refreshMode=="NORMAL"){
		// CASE: NORMAL
		if($siteRefreshStatus=="REFRESH_COMPLETED" || $siteRefreshStatus=="REFRESH_TIMED_OUT" || $siteRefreshStatus=="LOGIN_FAILURE") {
			
			if($siteRefreshStatus=="LOGIN_FAILURE"){
				$error = Yodlee\BaseConfig::getError(402);
				$messages = array($error["description"]);
				
				// Render in view
				$app->render("view_msg_errors.php", array(
					"messages" => $messages
				));
			} else {

				// Redirect to the view for render a item summary for site.
				$url_get_item_summaries_for_site = $app->urlFor('getItemSummariesForSite', array('memSiteAccId' => $memSiteAccId));
				$app->redirect($url_get_item_summaries_for_site);
			}
		} else {
			print "";
		}
	}
});

# ******************************************************
# This action allow visualize the site account added.
# ******************************************************

$app->get('/get-item-summaries-for-site/:memSiteAccId', 'no_custom_layout' ,  function($memSiteAccId) use ($rest_client)
{
	$app = \Slim\Slim::getInstance();
	// Preparing the short url and parameters necessary for the Api Service required.
	$config = array(
		"url" => Yodlee\BaseConfig::$URL_GET_ITEM_SUMMARIES_FOR_SITE,
		"parameters" => array(
			"cobSessionToken"=> $_SESSION["cobrandToken"],
			"userSessionToken"=> $_SESSION["userToken"],
			"memSiteAccId"=> $memSiteAccId
			)
	);

	$get_item_summaries_for_site = $rest_client->Post($config["url"], $config["parameters"]);

	// Render in view
	$app->render("view_get_item_summaries_for_site.php", array(
		"response" => $get_item_summaries_for_site["Body"]
	));
})->name("getItemSummariesForSite");


# ******************************************************
# This action return a form for a MFA Case:
# 	- Token
# 	- Security Question
# ******************************************************

$app->post('/get-mfa-response-for-site', 'no_custom_layout' ,  function() use ($rest_client)
{
	$app = \Slim\Slim::getInstance();
	$memSiteAccId = $app->request->params("memSiteAccId");
	$site_info = $_SESSION["site_info"];

	// Preparing the short url and parameters necessary for the Api Service required.
	$config = array(
		"url" => Yodlee\BaseConfig::$URL_GET_MFA_RESPONSE_FOR_SITE,
		"parameters" => array(
			"cobSessionToken"=> $_SESSION["cobrandToken"],
			"userSessionToken"=> $_SESSION["userToken"],
			"memSiteAccId"=> $memSiteAccId
			)
	);

	$get_mfa_response_for_site = $rest_client->Post($config["url"], $config["parameters"]);
	$_SESSION["get_mfa_response_for_site"] = $get_mfa_response_for_site["Body"];

	$retry = $get_mfa_response_for_site["Body"]->retry;
	$code =  (isset($get_mfa_response_for_site["Body"]->errorCode)) ? $get_mfa_response_for_site["Body"]->errorCode : NULL;
	$isMessageAvailable = (isset($get_mfa_response_for_site["Body"]->isMessageAvailable)) ? $get_mfa_response_for_site["Body"]->isMessageAvailable : "";

	if(!$retry) {
		if(is_null($code) && isset($get_mfa_response_for_site["Body"]->fieldInfo)) {
			if($isMessageAvailable){
				$fieldInfo = $get_mfa_response_for_site["Body"]->fieldInfo;

				if(isset($fieldInfo->responseFieldType)){
					// Render in view
					$app->render("view_form_mfa_token.php", array(
						"params" => $get_mfa_response_for_site["Body"],
						"memSiteAccId" => $memSiteAccId,
						"site_info" => $site_info
					));
				}

				if(isset($fieldInfo->questionAndAnswerValues)){
					// Render in view
					$app->render("view_form_mfa_question.php", array(
						"params" => $get_mfa_response_for_site["Body"],
						"memSiteAccId" => $memSiteAccId,
						"site_info" => $site_info
					));
				}
			}else{
				$messages = array("Error while trying to get information of ".$site_info->defaultDisplayName.".");
				// Render in view
				$app->render("view_msg_errors.php", array(
					"messages" => $messages
				));
			}
		}else if($code==0){
			// Render in view
			$app->render("poll_refresh_site_account.php", array(
				"memSiteAccId" => $memSiteAccId,
				"site_info" => $site_info
			));
		}else if($code > 0){
			$error = Yodlee\BaseConfig::getError($code);
			$messages = array($error["description"]);
			// Render in view
			$app->render("view_msg_errors.php", array(
				"messages" => $messages
			));
		}else{
			$messages = array("Unexpected error.");
			// Render in view
			$app->render("view_msg_errors.php", array(
				"messages" => $messages
			));
		}
	} else {
		// Render a response empty
		print "";
	}
});

# ******************************************************
# This action return a timeout with 522.
#
# You can find more information about this on website:
# http://developer.yodlee.com/FAQs/Error_Codes
# ******************************************************

$app->get('/timeOut', 'no_custom_layout' ,  function() 
{
	$app = \Slim\Slim::getInstance();

	$error = Yodlee\BaseConfig::getError(522);
	$messages = array($error["description"]);

	// Render in view
	$app->render("view_msg_errors.php", array(
		"messages" => $messages
	));
});

# ******************************************************
# This action verify the credentials for the 
# site account added.
# ******************************************************

$app->post('/put-mfa-request-for-site', 'no_custom_layout' ,  function() use ($rest_client)
{
	$app = \Slim\Slim::getInstance();
	$get_mfa_response_for_site = $_SESSION["get_mfa_response_for_site"];
	$memSiteAccId=  $app->request->params("memSiteAccId");
	
	
	if(isset($get_mfa_response_for_site->fieldInfo->responseFieldType)){
		$token=  $app->request->params("token");
		if(empty($token)){
			$app->stop();
		}

		$params = array(
				"cobSessionToken"=> $_SESSION["cobrandToken"],
				"userSessionToken"=> $_SESSION["userToken"],
				"memSiteAccId"=> $memSiteAccId,
				"userResponse.objectInstanceType" => "com.yodlee.core.mfarefresh.MFATokenResponse",
				"userResponse.token" => $token
				);

		// Preparing the short url and parameters necessary for the Api Service required.
		$config = array(
			"url" => Yodlee\BaseConfig::$URL_PUT_MFA_REQUEST_FOR_SITE,
			"parameters" => $params
		);
	}

	if(isset($get_mfa_response_for_site->fieldInfo->questionAndAnswerValues)){
		$fields = $app->request->params("field");
		
		$fieldInfo_to_send = array();
		foreach ($get_mfa_response_for_site->fieldInfo->questionAndAnswerValues as $index => $reg) {
			if(!isset($fields[$reg->metaData])) {
				$app->stop();
			}

			if(empty($fields[$reg->metaData])){
				$app->stop();
			}

			$fieldInfo_to_send[ sprintf("userResponse.quesAnsDetailArray[%s].answer", $index) ] 				= $fields[$reg->metaData];
			$fieldInfo_to_send[ sprintf("userResponse.quesAnsDetailArray[%s].answerFieldType", $index) ]		= $reg->responseFieldType;
			$fieldInfo_to_send[ sprintf("userResponse.quesAnsDetailArray[%s].metaData", $index) ]				= $reg->metaData;
			$fieldInfo_to_send[ sprintf("userResponse.quesAnsDetailArray[%s].question", $index) ]				= $reg->question;
			$fieldInfo_to_send[ sprintf("userResponse.quesAnsDetailArray[%s].questionFieldType", $index) ]		= $reg->questionFieldType;
		}

		$params = array(
				"cobSessionToken"=> $_SESSION["cobrandToken"],
				"userSessionToken"=> $_SESSION["userToken"],
				"memSiteAccId"=> $memSiteAccId,
				"userResponse.objectInstanceType" => "com.yodlee.core.mfarefresh.MFAQuesAnsResponse"
				);

		foreach ($fieldInfo_to_send as $key => $field) {
			$params[$key] = $field;
		}

		// Preparing the short url and parameters necessary for the Api Service required.
		$config = array(
			"url" => Yodlee\BaseConfig::$URL_PUT_MFA_REQUEST_FOR_SITE,
			"parameters" => $params
		);
	}

	$put_mfa_request_for_site = $rest_client->Post($config["url"], $config["parameters"]);

	if($put_mfa_request_for_site["Body"]->primitiveObj=="true"){
		$site_info = $_SESSION["site_info"];
		//will call the api service -> get-mfa-response-for-site
		// Render in view
		$app->render("view_get_mfa_response_for_site.php", array(
			"memSiteAccId" => $memSiteAccId,
			"site_info" => $site_info
		));
	}else if($put_mfa_request_for_site["Body"]->primitiveObj=="false") {

		$error = Yodlee\BaseConfig::getError(402);
		$messages = array($error["description"]);

		// Render in view
		$app->render("view_msg_errors.php", array(
			"messages" => $messages
		));
		
	} else {
		// Render in view
		$app->render("view_msg_errors.php", array(
			"messages" => $put_mfa_request_for_site["Body"]
		));
	}
});

$app->run();