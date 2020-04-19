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
	while($row = $api->db_fetch_array($result))
	{
			$this_layer = $row['int_layer_id'];
			
			echo "Layer: " . $this_layer . "\n";
			
			$sql = "SELECT int_ssshout_id, var_shouted FROM tbl_ssshout WHERE int_layer_id = " . $this_layer;
			echo $sql . "\n";
			$result_msgs = $api->db_select($sql);
			while($row_msg = $api->db_fetch_array($result_msgs))
			{
				echo "Message: " . $row_msg['var_shouted'] . "    ID:" . $row_msg['int_ssshout_id'] . "\n";
				
				global $cnf;
				
				if($cnf['db']['deleteDeletes'] === true) {
					
					//if($cnf['uploads']['use'] == "amazonAWS") {
					//Search for any images in the message
					echo "Search term = " . $cnf['uploads']['replaceHiResURLMatch'] . "\n";
					$url_matching = "ajmp";		//Works with Amazon based jpgs on atomjump.com which include ajmp.
					if($cnf['uploads']['replaceHiResURLMatch']) $url_matching = $cnf['uploads']['replaceHiResURLMatch'];
					
					
					$preg_search = "/.*?" . $url_matching ."(.*?)\.jpg/i";
					preg_match_all($preg_search, $row_msg['var_shouted'], $matches);
					
					for($all_cnt = 0; $all_cnt < count($matches); $all_cnt++) {
						//print_r($matches);					
						
						if(count($matches[$all_cnt]) > 1) {
							//Yes we have at least one image
							for($cnt = 1; $cnt < count($matches[$all_cnt]); $cnt++) {
								echo "Matched image raw: " . $matches[$all_cnt][$cnt] . "\n";
								$between_slashes = explode( "/", $matches[$all_cnt][$cnt]);
								$len = count($between_slashes) - 1;
								$image_name = $between_slashes[$len];
								echo "Image name: " . $image_name . "\n";
					
							}
						}
					}
					
					//Delete the record
					//TEMPOUT$api->db_select("DELETE FROM tbl_ssshout WHERE int_ssshout_id = " . $row_msg['int_ssshout_id']);
				
				
				} else {
					echo "Deactivating.";
					//TEMPOUT$api->db_update("tbl_ssshout", "enm_active = false WHERE int_ssshout_id = " . $row_msg['int_ssshout_id']);
				}
			}
			
			if($cnf['db']['deleteDeletes'] === true) {
				//Now delete the layer itself
				//TEMPOUT$api->db_select("DELETE FROM tbl_layer WHERE int_layer_id = " . $this_layer);
			}
		
	} 
		

	
	session_destroy();  //remove session




?>