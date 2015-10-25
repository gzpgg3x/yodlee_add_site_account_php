<?php
namespace Yodlee;

// require_once '../libs/vendor/Yaml-master/Exception/ExceptionInterface.php';
// require_once '../libs/vendor/Yaml-master/Exception/RuntimeException.php';
// require_once '../libs/vendor/Yaml-master/Exception/ParseException.php';
// require_once '../libs/vendor/Yaml-master/Exception/DumpException.php';
// require_once '../libs/vendor/Yaml-master/Unescaper.php';
// require_once '../libs/vendor/Yaml-master/Escaper.php';
// require_once '../libs/vendor/Yaml-master/Inline.php';
// require_once '../libs/vendor/Yaml-master/Parser.php';
// require_once '../libs/vendor/Yaml-master/Dumper.php';
// require_once '../libs/vendor/Yaml-master/Yaml.php';

require_once 'C:/xampp/htdocs/add_site_account/libs/vendor/Yaml-master/Exception/ExceptionInterface.php';
require_once 'C:/xampp/htdocs/add_site_account/libs/vendor/Yaml-master/Exception/RuntimeException.php';
require_once 'C:/xampp/htdocs/add_site_account/libs/vendor/Yaml-master/Exception/ParseException.php';
require_once 'C:/xampp/htdocs/add_site_account/libs/vendor/Yaml-master/Exception/DumpException.php';
require_once 'C:/xampp/htdocs/add_site_account/libs/vendor/Yaml-master/Unescaper.php';
require_once 'C:/xampp/htdocs/add_site_account/libs/vendor/Yaml-master/Escaper.php';
require_once 'C:/xampp/htdocs/add_site_account/libs/vendor/Yaml-master/Inline.php';
require_once 'C:/xampp/htdocs/add_site_account/libs/vendor/Yaml-master/Parser.php';
require_once 'C:/xampp/htdocs/add_site_account/libs/vendor/Yaml-master/Dumper.php';
require_once 'C:/xampp/htdocs/add_site_account/libs/vendor/Yaml-master/Yaml.php';

use Symfony\Component\Yaml\Yaml;


class BaseConfig {
	
	// Configuration of cobrand
	public static $CobrandCredentials = array(
		"username" => "[Enter the cobrand login]",
		"password" => "[Enter the cobrand password]",
	);
	
	// Url Base
	public static $BaseURl = "[Enter the EndPoint]";
	
	// Api Resource Path
	public static $URL_GET_COBRAND_LOGIN = "/authenticate/coblogin";
	public static $URL_GET_LOGIN_USER_LOGIN = "/authenticate/login";
	public static $URL_SEARCH_SITE = "/jsonsdk/SiteTraversal/searchSite";
	public static $URL_GET_SITE_INFO = "/jsonsdk/SiteTraversal/getSiteInfo";
	public static $URL_SITE_LOGIN_FORM = "/jsonsdk/SiteAccountManagement/getSiteLoginForm";
	public static $URL_ADD_SITE_ACCOUNT1 = "/jsonsdk/SiteAccountManagement/addSiteAccount1";
	public static $URL_GET_SITE_REFRESH_INFO = "/jsonsdk/Refresh/getSiteRefreshInfo";
	public static $URL_GET_ITEM_SUMMARIES_FOR_SITE = "/jsonsdk/DataService/getItemSummariesForSite";
	public static $URL_GET_MFA_RESPONSE_FOR_SITE = "/jsonsdk/Refresh/getMFAResponseForSite";
	public static $URL_PUT_MFA_REQUEST_FOR_SITE = "/jsonsdk/Refresh/putMFARequestForSite";
	public static $URL_GET_SITE_ACCOUNTS = "/jsonsdk/SiteAccountManagement/getSiteAccounts";

	// Configuration Base for Slim
	public static $configSlim = array(
		'debug' => true,
		'templates.path' => '../templates'
	);

	// Return a array with the information by the code error.
	// You can find more information about this on website:
	// http://developer.yodlee.com/FAQs/Error_Codes
	public static function getError($code){
		$errors = Yaml::parse(file_get_contents('../config/list_errors_codes.yml'));
		return $errors[$code];
	}
}