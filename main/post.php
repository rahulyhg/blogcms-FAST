<?php
	$server = dirname(__FILE__)."/../server";
	include_once $server."/configs.php";
	if( count( $GLOBALS['url_parts'] ) === 3 ){	
		$base = $GLOBALS['base_url'];
		$cat = $GLOBALS['url_parts'][1];
		$id = $GLOBALS['url_parts'][2];
		
		try{	
			$db = new MongoClient();
			$db_getter = new MongoGetter( $db );
			$post_views = new PostViews( new Parsedown );
			$single_post_data = $db_getter->getSingleRowById( $id );
			
			if( $single_post_data ){
				$tmplt_data = array();
				$tmplt_data["title"] = $single_post_data["title"];
				$tmplt_data["description"] = $single_post_data["description"];
				$tmplt_data["styles"] = "";
				$tmplt_data["scripts"] = "";
				$tmplt_data["base"] = $base;
				$tmplt_data["header"] = $post_views->getCatHeaderList( $cat );
				$tmplt_data["search_cat"] = $cat;
				$tmplt_data["body"] = $post_views->makePostHtmlFromData( $single_post_data, $cat );
			
				$base_page = new TemplateBinder( "base_page" );
				echo $base_page->bindTemplate( $tmplt_data );
			}else{
				goTo404();
			}
		}catch( MongoException $e ){
			//echo $e->getMessage();
			//Mongo will throw an error if id does not exist, go to 404 page		
			goTo404();
		}
	}else{
		goTo404();	
	}	
?>