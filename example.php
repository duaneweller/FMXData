<?php

	// set error reporting on
	error_reporting(-1);
	ini_set('display_errors', 1);

	// include the library
	require('FMXData.php');

	// Connect to the Database
	echo '** FILEMAKER CONNECT **<br />';
	$connect = FMXData::fmxConnect('localhost','FMXData','fmxdata','fmxdata','disable');
	print_r($connect);
	echo '<br /><br />';

	// Get the product information
	echo '** GET PRODUCT INFORMATION **<br />';
	$productInfo = FMXData::fmxProductInfo();
	print_r($productInfo);
	echo '<br /><br />';

	// Get the database list
	echo '** GET DATABASE LIST **<br />';
	$databaseList = FMXData::fmxListDatabases();
	print_r($databaseList);
	echo '<br /><br />';

	// get script list
	echo '** SCRIPT LIST **<br />';
	$scriptList = FMXData::fmxListScripts();
	print_r($scriptList);
	echo '<br /><br />';

	// get layout list
	echo '** LAYOUT LIST **<br />';
	$layoutList = FMXData::fmxListLayouts();
	print_r($layoutList);
	echo '<br /><br />';

	// get layout metadata
	echo '** LAYOUT METADATA **<br />';
	$layoutMeta = FMXData::fmxLayoutMeta('CONTACT',15297);
	print_r($layoutMeta);
	echo '<br /><br />';

	// create a new record
	echo '** CREATE RECORD **<br />';
	$data['First Name'] = 'Duane';
	$data['Last Name'] = 'Weller';
	$data['Job Title'] = 'Lead Developer';
	$fmx = new FMXData();
	$fmx->addPostFieldArray($data);
	$fmx->addPostField('Company','Excelisys Inc');
	$fmx->addScript('FMX_Script','param1');
	$fmx->addPresortScript('FMX_Presort_Script','param2');
	$fmx->addPrerequestScript('FMX_Prerequest_Script','param3');
	$createRecord = $fmx->fmxCreateRecord('CONTACT');
	print_r($createRecord);
	echo '<br /><br />';

	$recId = @$createRecord['response']['recordId'];

	// edit the existing record
	echo '** EDIT RECORD **<br />';
	$data['Job Title'] = 'FileMaker Developer';
	$data['Website'] = 'https://www.excelisys.com';
	$fmx = new FMXData();
	$fmx->addPostFieldArray($data);
	$fmx->addPostField('Company','Excelisys Inc');
	$fmx->addScript('FMX_Script','param1');
	$fmx->addPresortScript('FMX_Presort_Script','param2');
	$fmx->addPrerequestScript('FMX_Prerequest_Script','param3');
	$editRecord = $fmx->fmxEditRecord('CONTACT',$recId);
	print_r($editRecord);
	echo '<br /><br />';

	// delete an existing record
	echo '** DELETE RECORD **<br />';
	$fmx = new FMXData();
	$fmx->addScript('FMX_Script','param1');
	$fmx->addPresortScript('FMX_Presort_Script','param2');
	$fmx->addPrerequestScript('FMX_Prerequest_Script','param3');
	$deleteRecord = $fmx->fmxDeleteRecord('CONTACT',$recId);
	print_r($deleteRecord);
	echo '<br /><br />';

	// get an existing record
	echo '** GET RECORD **<br />';
	$fmx = new FMXData();
	$fmx->setResponseLayout('CONTACT');
	$fmx->addScript('FMX_Script','param1');
	$fmx->addPresortScript('FMX_Presort_Script','param2');
	$fmx->addPrerequestScript('FMX_Prerequest_Script','param3');
	$fmx->limitPortals('phone_portal,email_portal');
	$fmx->limitPortalRows('phone_portal',1,2);
	$getRecord = $fmx->fmxGetRecord('CONTACT_LIST',15297);
	print_r($getRecord);
	echo '<br /><br />';

	// get a range of records
	echo '** GET RANGE OF RECORDS **<br />';
	$fmx = new FMXData();
	$fmx->setResponseLayout('CONTACT_LIST');
	$fmx->addScript('FMX_Script','param1');
	$fmx->addPresortScript('FMX_Presort_Script','param2');
	$fmx->addPrerequestScript('FMX_Prerequest_Script','param3');
	$fmx->addSortParam('First Name','ascend',2);
	$fmx->addSortParam('Last Name','ascend',1);
	$fmx->setLimitOffset(10,50);
	$getRange = $fmx->fmxGetRange('CONTACT');
	print_r($getRange);
	echo '<br /><br />';
	
	// upload a file to a container field
	echo '** UPLOAD A FILE **<br />';
	$path = __DIR__.'/photo.jpg';
	$upload = FMXData::fmxUploadFile('CONTACT',15297,'Photo',1,$path);
	print_r($upload);
	echo '<br /><br />';
	
	// set global fields
	echo '** SET GLOBAL FIELDS **<br />';
	$fmx = new FMXData();
	$fmx->addGlobalField('CONTACT','global1','FileMaker');
	$fmx->addGlobalField('CONTACT','global2','Rocks');
	$setGlobals = $fmx->fmxSetGlobalFields();
	print_r($setGlobals);
	echo '<br /><br />';
	
	// find records
	echo '** FIND RECORDS **<br />';
	$fmx = new FMXData();
	$fmx->setResponseLayout('CONTACT_LIST');
	$fmx->addRequestArray(array('Company'=>'Bayer Ltd'));
	$fmx->addRequestArray(array('Company'=>'Bayer Group'));
	$fmx->addRequestArray(array('Job Title'=>'Teacher'),TRUE);
	$fmx->addSortParam('First Name','ascend',2);
	$fmx->addSortParam('Last Name','ascend',1);
	$fmx->addScript('FMX_Script','param1');
	$fmx->addPresortScript('FMX_Presort_Script','param2');
	$fmx->addPrerequestScript('FMX_Prerequest_Script','param3');
	$fmx->setLimitOffset(10);
	$findRecords = $fmx->fmxFindRecords('CONTACT');
	print_r($findRecords);
	echo '<br /><br />';

	// Disconnect from the Database
	echo '** FILEMAKER DISCONNECT **<br />';
	$disconnect = FMXData::fmxDisconnect();
	print_r($disconnect);
	echo '<br /><br />';

?>
