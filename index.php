<?php

	/*
	
	Query for all aged message forums based off the tbl_layer.date_to_decay values. Note: most
	forums will have a NULL entry here, which means they don't age and should be ignored.
	
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



?>