<?php

	/*
	
	Query for all aged message forums based off the tbl_layer.date_to_decay values. Note: most
	forums will have a NULL entry here, which means they don't age and should be ignored.
	This will be called hourly on a regular cronjob.
	
	1. Loop through each forum, query for all messages from that forum.
	2. Loop through each message from that forum and search individually within that message for
	images that are held on this server. They will start with the config.json JSON entry uploads.vendor.imageURL
	if images are uploaded via AmazonAWS (/digitalocean), or with the current server URL /images/im/ if uploaded
	to the same server.
	3. Delete the image from using the AmazonAWS API or the local file-system unlink();
	4. Delete the message
	5. Repeat for all messages in forum. 
	6. Delete the forum.
	
	*/



	function trim_trailing_slash_local($str) {
        return rtrim($str, "/");
    }
    
    function add_trailing_slash_local($str) {
        //Remove and then add
        return rtrim($str, "/") . '/';
    }


	if(!isset($aged_config)) {
        //Get global plugin config - but only once
		$data = file_get_contents (dirname(__FILE__) . "/config/config.json");
        if($data) {
            $aged_config = json_decode($data, true);
            if(!isset($aged_config)) {
                echo "Error: remove_aged config/config.json is not valid JSON.";
                exit(0);
            }
     
        } else {
            echo "Error: Missing config/config.json in remove_aged plugin.";
            exit(0);
     
        }
  
  
    }



    $agent = $aged_config['agent'];
	ini_set("user_agent",$agent);
	$_SERVER['HTTP_USER_AGENT'] = $agent;
	$start_path = add_trailing_slash_local($aged_config['serverPath']);

	
	
	$notify = false;
	include_once($start_path . 'config/db_connect.php');	
	
	$define_classes_path = $start_path;     //This flag ensures we have access to the typical classes, before the cls.pluginapi.php is included
	require($start_path . "classes/cls.pluginapi.php");
	
	$api = new cls_plugin_api();
	
	

	
	$sql = "SELECT * FROM tbl_layer WHERE date_to_decay IS NOT NULL AND date_to_decay < NOW()";
    $result = $api->db_select($sql);
	if($rows = $api->db_fetch_array($result))
	{
		foreach($rows as $row) {
			$this_layer = $row['int_layer_id'];
			
			echo $this_layer . "\n";	//$config;
			
		}
		
	} 
		

	
	session_destroy();  //remove session




?>