<?php

/**
 * FMX - A PHP library for the FileMaker Server Data API
 *
 * Copyright (c) 2020, Duane Weller (Excelisys, Inc.)
 *
 * @version 1.0
 * @package FMXData
 * @author Duane Weller (Excelisys, Inc.)
 */

// The URL protocal "https:" (don't include the trailing "//").
define('FMX_PROTOCAL','https:');

// the app version v1 or vLatest
define('FMX_APIVERSION','v1');

// FMX GLOBALS
$fmx_host = NULL;
$fmx_database = NULL;
$fmx_auth = NULL;
$fmx_token = NULL;
$fmx_ssl_verify = NULL;

class FMXData
{	
	
	public function __construct() {
       
    }
	
	/********************************/
	/****** SESSION FUNCTIONS *******/
	/********************************/
	
	/*
	 * fmxConnect
	 *
	 * Uses the curlSession function to create a new FileMaker session.
	 *
	 * @param $host - The host server's domain or IP - can be localhost or 127.0.0.1 as well.
	 * @param $database - The filename of the FileMaker file without the extension (.fmp12).
	 * @param $username - The username of the FileMaker account.
	 * @param $password - The password of the FileMaker account.
	 */
	public static function fmxConnect($host, $database, $username, $password, $sslVerify = 'enable')
	{
		global $fmx_host;
		global $fmx_database;
		global $fmx_auth;
		global $fmx_token;
		global $fmx_ssl_verify;
		
		// set the database host
		$fmx_host = $host;
	
		// set the database global
		$fmx_database = $database;
		
		// set the auth string
		$fmx_auth = $username.':'.$password;
		
		// set the ssl verify
		$fmx_ssl_verify = $sslVerify;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['sessions'] = 'sessions';
		$url = implode($udat,'/');
	
		// send request to FileMaker Server
		$query = SELF::curlSession($url,'POST');
		
		// set the token
		$fmx_token = @$query['response']['token'];
		
		return $query;
	}

	/*
	 * fmxDisconnect
	 *
	 * Uses the curlSession function to delete the current FileMaker session.
	 *
	 */
	public static function fmxDisconnect()
	{	
		global $fmx_host;
		global $fmx_database;
		global $fmx_token;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['sessions'] = 'sessions';
		$udat['token'] = $fmx_token;
		$url = implode($udat,'/');
		
		// send request to FileMaker Server
		$result = SELF::curlSession($url,'DELETE');
		
		$fmx_token = NULL;
		
		return $result;
	}
	
	/*
	 * fmxProductInfo
	 *
	 * Uses the curlSession function to return 
	 * an array of the FileMaker Product Info.
	 *
	 */
	public static function fmxProductInfo()
	{
		global $fmx_host;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['productInfo'] = 'productInfo';
		$url = implode($udat,'/');
		
		// send request to FileMaker Server
		$result = SELF::curlSession($url,'GET');
		return $result;
	}
	
	/*
	 * fmxListDatabases
	 *
	 * Uses the curlSession function to return 
	 * an array of the FileMaker Databases.
	 *
	 */
	public static function fmxListDatabases()
	{
		global $fmx_host;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$url = implode($udat,'/');
		
		// send request to FileMaker Server
		$result = SELF::curlSession($url,'GET');
		return $result;
	}

	/*
	 * curlSession
	 *
	 * cURL function for all database session features
	 *
	 * @param $url - The url to the FileMaker Server.
	 * @param $action - Can be either "POST", "DELETE", or "GET".
	 */
	private static function curlSession($url,$action)
	{
		global $fmx_auth;
		global $fmx_ssl_verify;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		if($fmx_ssl_verify == 'disable')
		{
			// required if SSL is default cert or self signed
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $fmx_auth);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if($action == 'POST')
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
			curl_setopt($ch, CURLOPT_POST, TRUE);
		} elseif( $action == 'DELETE' ) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}
		$query = curl_exec($ch);
		curl_close($ch);
		
		$result = json_decode($query,TRUE); // convert JSON to array
		
		return $result;
	}
	
	
	
	
	
	
	/********************************/
	/* FILEMAKER DATABASE FUNCTIONS */
	/********************************/
	
	/*
	 * fmxFieldDataArray
	 *
	 * The Field Data Array Property
	 * Stores an array of key value pairs
	 * for the query parameters.
	 *
	 */
	private $fmxFieldDataArray = array();
	
	/*
	 * fmxQueryStringArray
	 *
	 * The Query String Property
	 * Stores an array of key value pairs
	 * for the query string parameters.
	 *
	 */
	private $fmxQueryStringArray = array();
	
	/*
	 * fmxScriptArray
	 *
	 * The Script Array stores the script 
	 * names and script parameters to use.
	 * Requres a separate property because 
	 * when run as GET or DELETE parameters 
	 * are added to the query string but when  
	 * running POST or PATCH, parameters are
	 * added to the field data.
	 *
	 */
	private $fmxScriptArray = array();
	
	/*
	 * fmxSortArray
	 *
	 * The Sort Array Property
	 * Stores the fields and sort order
	 * as an array. Array is converted to
	 * JSON when added to $fmxQueryStringArray
	 * variable.
	 */
	private $fmxSortArray = array();
	
	/*
	 * fmxQueryRequestArray
	 *
	 * The Query Request Array Property
	 * Stores query requests for FileMaker
	 * finds. Used exclusivly with the 
	 * fmxFindRecords function.
	 */
	private $fmxQueryRequestArray = array();
	
	/*
	 * fmxGlobalFieldArray
	 *
	 * The Global Field Array Property
	 * Stores a list of global fields.
	 * Used exclusivly with the 
	 * fmxSetGlobalFields function.
	 */
	private $fmxGlobalFieldArray = array();
	
	/*
	 * addPostFieldArray
	 *
	 * Adds an array of fields to post
	 * to the $fmxFieldDataArray property.
	 *
	 * @param $data - An array where the field name is the key for each value.
	 */
	public function addPostFieldArray($data)
	{
		$this->fmxFieldDataArray = array_merge($this->fmxFieldDataArray, $data);
	}
	 
	/*
	 * addPostField
	 *
	 * Adds a field to post
	 * to the $fmxFieldDataArray property.
	 *
	 * @param $field - The field name to add.
	 * @param $value - The matching field value to set.
	 */
	public function addPostField($field,$value)
	{
		$paramArray = $this->fmxFieldDataArray;
		$paramArray[$field] = $value;
		$this->fmxFieldDataArray = $paramArray;
	}
	 
	/*
	 * limitPortals
	 *
	 * Adds a portal keyword option to the 
	 * $fmxQueryStringArray property.
	 * 
	 * NOTE: I find this works best with layout object names.
	 *
	 * @param $portalList - A comma-delimited list of portal object names.
	 *
	 * NOTE THE PORTAL KEYWORD DOES NOT APPEAR
	 * TO BE WORKING IN THE DATA API AS DOCUMENTED.
	 */
	public function limitPortals($portalList)
	{
	 	// get the $fmxQueryStringArray
	 	$queryArray = $this->fmxQueryStringArray;
	 	
	 	// explode the portal list into an array if it's not already one
	 	if(!is_array($portalList))
	 	{
	 		$portalList = explode(',',$portalList);
	 	}
	 	
	 	// create the comma-delimited quoted portal list
	 	$queryArray['portal'] = '["'.implode('","',$portalList).'"]';
	 	$this->fmxQueryStringArray = $queryArray;
	}
	 
	/*
	 * limitPortalRows
	 *
	 * Adds a portal offset and limit to the
	 * $fmxQueryStringArray property.
	 *
	 * @param $portal - The portal object name or related table occurrance name.
	 * @param $limit - The maximum numer of records to return.
	 * @param $offset - (optional) The number of records to skip.
	 */
	public function limitPortalRows($portal,$limit,$offset=1)
	{
	 	$queryArray = $this->fmxQueryStringArray;
	 	$queryArray['_offset.'.$portal] = $offset;
	 	$queryArray['_limit.'.$portal] = $limit;
	 	$this->fmxQueryStringArray = $queryArray;
	}
	
	/*
	 * setLimitOffset
	 *
	 * Adds a portal option to the 
	 * $fmxQueryStringArray property.
	 *
	 * @param $limit - The maximum numer of records to return.
	 * @param $offset - (Optional) The number of records to skip.
	 */
	public function setLimitOffset($limit,$offset=1)
	{
	 	$queryArray = $this->fmxQueryStringArray;
	 	$queryArray['_offset'] = $offset;
	 	$queryArray['_limit'] = $limit;
	 	$this->fmxQueryStringArray = $queryArray;
	}
	
	/*
	 * setResponseLayout
	 *
	 * Adds a response layout option to the 
	 * $fmxQueryStringArray property.
	 *
	 * @param $layout - The FileMaker Layout to use.
	 */
	public function setResponseLayout($layout)
	{
	 	$queryArray = $this->fmxQueryStringArray;
	 	$queryArray['layout.response'] = $layout;
	 	$this->fmxQueryStringArray = $queryArray;
	}
	
	/*
	 * addScript
	 *
	 * Adds a script to perform to the 
	 * $fmxScriptArray property.
	 *
	 * @param $scriptName - The name of the script to perform.
	 * @param $parameter - (Optional) The script prameter to pass to the script.
	 */
	public function addScript($scriptName,$parameter=NULL)
	{
		$scriptArray = $this->fmxScriptArray;
	 	$scriptArray['script'] = $scriptName;
	 	if(isset($parameter))
	 	{
	 		$scriptArray['script.param'] = $parameter;
	 	}
	 	$this->fmxScriptArray = $scriptArray;
	}
	
	/*
	 * addPresortScript
	 *
	 * Adds a presort script to perform to the 
	 * $fmxScriptArray property.
	 *
	 * @param $scriptName - The name of the script to perform.
	 * @param $parameter - (Optional) The script prameter to pass to the script.
	 */
	public function addPresortScript($scriptName,$parameter=NULL)
	{
		$scriptArray = $this->fmxScriptArray;
	 	$scriptArray['script.presort'] = $scriptName;
	 	if(isset($parameter))
	 	{
	 		$scriptArray['script.presort.param'] = $parameter;
	 	}
	 	$this->fmxScriptArray = $scriptArray;
	}
	
	/*
	 * addPrerequestScript
	 *
	 * Adds a prerequest script to perform to the 
	 * $fmxScriptArray property.
	 *
	 * @param $scriptName - The name of the script to perform.
	 * @param $parameter - (Optional) The script prameter to pass to the script.
	 */
	public function addPrerequestScript($scriptName,$parameter=NULL)
	{
		$scriptArray = $this->fmxScriptArray;
	 	$scriptArray['script.prerequest'] = $scriptName;
	 	if(isset($parameter))
	 	{
	 		$scriptArray['script.prerequest.param'] = $parameter;
	 	}
	 	$this->fmxScriptArray = $scriptArray;
	}
	
	/*
	 * addSortParam
	 *
	 * Adds a sort parameter to the 
	 * $fmxSortArray property.
	 *
	 * @param $fieldname - The name of the field to sort.
	 * @param $direction - (Optional) Can be either ascend or descend. The default is ascend
	 * @param $order - (Optional) The order in which to sort the field. If not specified field is added to the end of the array.
	 */
	public function addSortParam( $fieldname, $direction = 'ascend', $order = NULL )
	{
		$sortArray = $this->fmxSortArray;
		
		// set the index where the field will be ordered
		// default just adds it to the $sortArray in the order it's entered
		if(isset($order)) {
			$index = $order;
		} else {
			$index = count($sortArray) + 1;
		}
		
		// add the field to the sort order
		$sortArray[$index] = array('fieldName'=>$fieldname,'sortOrder'=>$direction);
		
		// update the $fmxSortArray property
		$this->fmxSortArray = $sortArray;
	}
	
	/*
	 * addRequestArray
	 *
	 * Adds a query request to the  
	 * $fmxQueryRequestArray property.
	 *
	 * @param $fieldArray - An array of field=>value pairs to search.
	 * @param $omit - (Optional) Can be either TRUE or FALSE. The default is FALSE
	 */
	public function addRequestArray( $fieldArray, $omit = FALSE)
	{
		// add omit option to the $fieldArray
		if( $omit == TRUE ) {
			$fieldArray['omit'] = 'true';
		}
		
		// get the fmxQueryRequestArray
		$queryArray = $this->fmxQueryRequestArray;
		$elements = (count($queryArray) - 1);
		$index = ($elements + 1);
		
	 	// add the request to the fmxQueryRequestArray
	 	$this->fmxQueryRequestArray[$index] = $fieldArray;
	}
	
	/*
	 * addGlobalField
	 *
	 * Adds a global field to the  
	 * $fmxGlobalFieldArray property.
	 *
	 * @param $baseTable - The basetable name.
	 * @param $fieldName - The global field to set.
	 * @param $value - The value to set the field to.
	 */
	public function addGlobalField($baseTable, $fieldName, $value)
	{
		// get the fmxGlobalFieldArray
		$fieldArray = $this->fmxGlobalFieldArray;
		$fieldArray[$baseTable.'::'.$fieldName] = $value;
		$this->fmxGlobalFieldArray = $fieldArray;
	}
	
	/*
	 * fmxListScripts
	 *
	 * Uses the curlFileMaker function to return 
	 * an array of the FileMaker script names.
	 *
	 */
	public static function fmxListScripts()
	{
		global $fmx_host;
		global $fmx_database;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['scripts'] = 'scripts';
		$url = implode($udat,'/');
		
		// send request to FileMaker Server
		$result = SELF::curlFileMaker($url,NULL,'GET');
		
		return $result;
	}
	
	/*
	 * fmxListLayouts
	 *
	 * Uses the curlFileMaker function to return 
	 * an array of the FileMaker layout names.
	 *
	 */
	public static function fmxListLayouts()
	{
		global $fmx_host;
		global $fmx_database;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['layouts'] = 'layouts';
		$url = implode($udat,'/');
		
		// send request to FileMaker Server
		$result = SELF::curlFileMaker($url,NULL,'GET');
		
		return $result;
	}
	
	/*
	 * fmxLayoutMeta
	 *
	 * Uses the curlFileMaker function to return 
	 * an array of the FileMaker Layout Metadata.
	 *
	 * @param $layout - The FileMaker Layout to query.
	 * @param $recordId - (Optional) record id for related value lists.
	 */
	public static function fmxLayoutMeta($layout,$recordId = NULL)
	{
		global $fmx_host;
		global $fmx_database;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['layouts'] = 'layouts';
		$udat['layoutName'] = $layout;
		$url = implode($udat,'/');
		
		// For layout metadata recordId is added to the query string.
		// Use of the fmxQueryStringArray is not required here.
		// There are no other query string parameters for this function.
		if(isset($recordId))
		{
			$queryString['recordId'] = $recordId;
			$url = $url.'?'.http_build_query($queryString);
		}
		
		// send request to FileMaker Server
		$result = SELF::curlFileMaker($url,NULL,'GET');
		
		return $result;
	}
	
	/*
	 * fmxCreateRecord
	 *
	 * Uses the curlFileMaker function to create 
	 * a new record in the FileMaker database.
	 *
	 * @param $layout - The FileMaker Layout to query.
	 */
	public function fmxCreateRecord($layout)
	{
		global $fmx_host;
		global $fmx_database;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['layouts'] = 'layouts';
		$udat['layoutName'] = $layout;
		$udat['records'] = 'records';
		$url = implode($udat,'/');
		
		// get the field data array
		$query['fieldData'] = $this->fmxFieldDataArray;
		
		// For POST functions -  add scripts to the query
		$query = array_merge($query, $this->fmxScriptArray);
		
		// add the query string
		if(isset($this->fmxQueryStringArray))
		{
			$url = $url.'?'.http_build_query($this->fmxQueryStringArray);
		}
		
		// send request to FileMaker Server
		$result = $this->curlFileMaker($url,$query,'POST');
		
		return $result;
	}
	
	/*
	 * fmxEditRecord
	 *
	 * Uses the curlFileMaker function to edit 
	 * a record in the FileMaker database.
	 *
	 * @param $layout - The FileMaker Layout to query.
	 * @param $recId - The recordId for the record to be updated.
	 */
	public function fmxEditRecord($layout, $recId)
	{
		global $fmx_host;
		global $fmx_database;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['layouts'] = 'layouts';
		$udat['layoutName'] = $layout;
		$udat['records'] = 'records';
		$udat['recordId'] = $recId;
		$url = implode($udat,'/');
		
		// get the field data array
		$query['fieldData'] = $this->fmxFieldDataArray;
		
		// For PATCH functions -  add scripts to the query
		$query = array_merge($query, $this->fmxScriptArray);
		
		// add the query string
		if(isset($this->fmxQueryStringArray))
		{
			$url = $url.'?'.http_build_query($this->fmxQueryStringArray);
		}
		
		// send request to FileMaker Server
		$result = $this->curlFileMaker($url,$query,'PATCH');
		
		return $result;
	}
	
	/*
	 * fmxDeleteRecord
	 *
	 * Uses the curlFileMaker function to delete 
	 * a record in the FileMaker database.
	 *
	 * @param $layout - The FileMaker Layout to query.
	 * @param $recId - The recordId for the record to be deleted.
	 */
	public function fmxDeleteRecord($layout, $recId)
	{
		global $fmx_host;
		global $fmx_database;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['layouts'] = 'layouts';
		$udat['layoutName'] = $layout;
		$udat['records'] = 'records';
		$udat['recordId'] = $recId;
		$url = implode($udat,'/');
		
		// For DELETE functions -  merge script options with the query string.
		$this->fmxQueryStringArray = array_merge($this->fmxQueryStringArray, $this->fmxScriptArray);
		
		// add the query string
		if(isset($this->fmxQueryStringArray))
		{
			$url = $url.'?'.http_build_query($this->fmxQueryStringArray);
		}
		
		// send request to FileMaker Server
		$result = $this->curlFileMaker($url,NULL,'DELETE');
		
		return $result;
	}
	
	/*
	 * fmxGetRecord
	 *
	 * Uses the curlFileMaker function to get 
	 * a record in the FileMaker database.
	 *
	 * @param $layout - The FileMaker Layout to query.
	 * @param $recId - The recordId for the record to be returned.
	 */
	public function fmxGetRecord($layout, $recId)
	{
		global $fmx_host;
		global $fmx_database;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['layouts'] = 'layouts';
		$udat['layoutName'] = $layout;
		$udat['records'] = 'records';
		$udat['recordId'] = $recId;
		$url = implode($udat,'/');
		
		// For GET functions -  merge script options with the query string.
		$this->fmxQueryStringArray = array_merge($this->fmxQueryStringArray, $this->fmxScriptArray);
		
		// add the query string
		if(isset($this->fmxQueryStringArray))
		{
			$url = $url.'?'.http_build_query($this->fmxQueryStringArray);
		}
		
		// send request to FileMaker Server
		$result = $this->curlFileMaker($url,NULL,'GET');
		
		return $result;
	}
	
	/*
	 * fmxGetRange
	 *
	 * Uses the curlFileMaker function to get 
	 * a range of records from the FileMaker database.
	 *
	 * @param $layout - The FileMaker Layout to query.
	 */
	 public function fmxGetRange($layout)
	 {
	 	global $fmx_host;
		global $fmx_database;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['layouts'] = 'layouts';
		$udat['layoutName'] = $layout;
		$udat['records'] = 'records';
		$url = implode($udat,'/');
		
		// For GET functions -  merge script options with the query string.
		$this->fmxQueryStringArray = array_merge($this->fmxQueryStringArray, $this->fmxScriptArray);
		
		// add sort parameters to query string
		if(isset($this->fmxSortArray))
		{
			$sorts = $this->fmxSortArray;
			for ( $i = 0; $i <= count($sorts); $i++ )
			{
				if(isset($sorts[$i]))
				{
					$sortArray[] = $sorts[$i];
				}
			}
			$this->fmxQueryStringArray['_sort'] = json_encode($sortArray);
		}
		
		// add the query string
		if(isset($this->fmxQueryStringArray))
		{
			$url = $url.'?'.http_build_query($this->fmxQueryStringArray);
		}
		
		// send request to FileMaker Server
		$result = $this->curlFileMaker($url,NULL,'GET');
		
		return $result;
	 }
	 
	/*
	 * fmxUploadFile
	 *
	 * Uses the curlFileMaker function to upload
	 * a file to the FileMaker database.
	 *
	 * @param $layout - The FileMaker Layout to query.
	 * @param $recId - The id of the record to upload.
	 * @param $field - The field to uplod into.
	 * @param $repetition - The field repetition to upload into.
	 * @param $pathToFile - The path to the file on disk.
	 */
	public static function fmxUploadFile( $layout, $recId, $field, $repetition = 1, $pathToFile )
	{
		global $fmx_host;
		global $fmx_database;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['layouts'] = 'layouts';
		$udat['layoutName'] = $layout;
		$udat['records'] = 'records';
		$udat['recordId'] = $recId;
		$udat['containers'] = 'containers';
		$udat['field'] = $field;
		$udat['repetition'] = $repetition;
		$url = implode($udat,'/');
		
		// extract the file name
		$pathArray = explode('/',$pathToFile);
		$fileName = $pathArray[(count($pathArray) - 1)];
		
		// Set the upload parameter with curl_file_create
		// for PHP versions > 5.5. The @ format is now deprecated.
		$postfields['upload'] = curl_file_create($pathToFile);
		$postfields['filename'] = $fileName;
		
		$result = SELF::curlFileMaker( $url, $postfields, 'UPLOAD' );
		return $result;
	}
	
	/*
	 * fmxFindRecords
	 *
	 * Uses the curlFileMaker function to find 
	 * records in the FileMaker database.
	 *
	 * @param $layout - The FileMaker Layout to query.
	 */
	 public function fmxFindRecords($layout)
	 {
	 	global $fmx_host;
		global $fmx_database;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['layouts'] = 'layouts';
		$udat['layoutName'] = $layout;
		$udat['find'] = '_find';
		$url = implode($udat,'/');
		
		// get the query request array
		$query['query'] =  $this->fmxQueryRequestArray;
		
		// add the query string elements
		if(isset($this->fmxQueryStringArray))
		{
			$queryString = $this->fmxQueryStringArray;
			$newString = array();
			foreach( $queryString as $key => $value )
			{
				$newKey = explode('.',$key);
				if( $newKey[0] == '_limit') {
					$newKey[0] = 'limit';
				} elseif ($newKey[0] == '_offset') {
					$newKey[0] = 'offset';
				}
				$newKey = implode('.', $newKey);
				$newString[$newKey] = $value;
			}
			$query = array_merge($query, $newString);
		}
		
		// add sort parameters to query string
		if(isset($this->fmxSortArray))
		{
			$sorts = $this->fmxSortArray;
			for ( $i = 0; $i <= count($sorts); $i++ )
			{
				if(isset($sorts[$i]))
				{
					$sortArray[] = $sorts[$i];
				}
			}
			$query['sort'] = $sortArray;
		}
		
		// For POST functions -  add scripts to the query
		$query = array_merge($query, $this->fmxScriptArray);
		
		// send request to FileMaker Server
		$result = $this->curlFileMaker($url,$query,'POST');
		
		return $result;
	 }
	 
	/*
	 * fmxSetGlobalFields
	 *
	 * Uses the curlFileMaker function to set 
	 * global fields in the FileMaker database.
	 *
	 */
	public function fmxSetGlobalFields()
	{
		global $fmx_host;
		global $fmx_database;
		
		// assemble the URL
		$udat['protocal'] = FMX_PROTOCAL;
		$udat['space'] = '';
		$udat['host'] = $fmx_host;
		$udat['fmi'] = 'fmi';
		$udat['data'] = 'data';
		$udat['apiVersion'] = FMX_APIVERSION;
		$udat['databases'] = 'databases';
		$udat['database'] = $fmx_database;
		$udat['globals'] = 'globals';
		$url = implode($udat,'/');
		
		$query['globalFields'] = $this->fmxGlobalFieldArray;
		
		// send request to FileMaker Server
		$result = $this->curlFileMaker($url,$query,'PATCH');
		
		return $result;
	}
	
	/*
	 * curlFileMaker
	 *
	 * Sends a cURL request to the FileMaker Server.
	 * Returns the response from the server.
	 *
	 * @param $url - The url to the FileMaker Server.
	 * @param $postfields - An array of the post fields for the query.
	 * @param $action - Can be either "POST", "DELETE", "PATCH" or "GET".
	 */
	private static function curlFileMaker($url,$postfields,$action)
	{
		global $fmx_token;
		global $fmx_ssl_verify;
		
		// initialize cURL
		$ch = curl_init();
		
		// set the URL
		curl_setopt($ch, CURLOPT_URL, $url);
		
		// ignore SSL verification
		if($fmx_ssl_verify == 'disable')
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		}
		
		// set default header
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$fmx_token
		));
		
		// set options based on actions
		if($action == 'UPLOAD') {
			// The upload is sent as POST.
			// Multipart/form-data will automatically get set when the
			// CURLOPT_POSTFIELDS are set as an array instead of a string.
			// The CURLOPT_HTTPHEADER is reset here to override the default.
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Bearer '.$fmx_token
			));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		} elseif($action == 'POST') {
			if($postfields)
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postfields));
			}
		} elseif($action == 'PATCH') {
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postfields));
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		} elseif($action == 'DELETE') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		} // default action is GET
		
		// return the result
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		// execute the cURL request
		$query = curl_exec($ch);
		curl_close($ch);
		
		// decode the result and return
		$result = json_decode($query,TRUE); // convert JSON to array
		return $result;
	}
	
} 

?>
