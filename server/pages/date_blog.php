<?php
	//included in index.php which has configs.php included already
	$base = $GLOBALS['base_url'];
	$url = $_SERVER["REQUEST_URI"];
	$cache = new CacheController( $GLOBALS['cache_dir'], $url );
	
	if( $cache->urlInCache() && !$cache->cacheMinutesOverLimit( $GLOBALS['max_page_cache_mins'] ) ){   
	    echo $cache->pullUrlContentFromCache();
	    //echo "cached";
        exit;	
    }
    
    if( $part_count > 1 ){
	    goTo404();
	    exit;
	}	

	//part-count defined in index.php    
	$_GET['cat'] = ( $url_parts[0] !== "" )? $url_parts[0] : $GLOBALS['post_categories'][0]; //cat is first url part or the default cat	
	$cat = $_GET['cat'];		
	
	$time = ( isset($_GET['after']) )? $_GET['after'] : time();
   try{
	   $db = MongoConnection();
		$db_getter = new MongoGetter( $db ); 
		$parsedown = new Parsedown();				
    	$post_views = new PostViews( $parsedown );
    	$post_views->lazy_load_imgs = true; //turn on image lazy loading, false to turn off	
    	$post_controller = new PostController( $db_getter, $post_views );
		$mongo_results = $post_controller->getHomePagePostsByTime( $time, $cat ); //false if no result set
	}catch( MongoException $e ){
		//echo $e->getMessage();
		//Mongo error, go to 404 page		
		goTo404();
		exit;
	}			

	if( $mongo_results ){
    	$template = file_get_contents( $GLOBALS['template_dir']."/base_page.txt" );
    	$title = $cat." - ".$_SERVER['HTTP_HOST'];		
    	$desc= $_SERVER['HTTP_HOST']." - browse ".$cat;
		$scripts = "<script src='/scripts/page_actions/main_analytics.js'></script>";
        $scripts .=( $post_views->lazy_load_imgs )? "<script src='/scripts/page_actions/blog_scroll_actions.js'></script>" : "";
		
		$tmplt_data = array();
		$tmplt_data["title"] = $title;
		$tmplt_data["description"] = $desc;
		$tmplt_data["styles"] = "";
		$tmplt_data["scripts"] = $scripts;
		$tmplt_data["base"] = $base;
		$tmplt_data["category"] = $cat;
		$tmplt_data["search_placeholder"] = "search $cat";	
		$tmplt_data["search_value"] = "";		
		$tmplt_data["header"] = $post_views->getCatHeaderList( $cat );
		$tmplt_data["body"] = $mongo_results;
		
		$full_page = TemplateBinder::bindTemplate( $template, $tmplt_data );	
		$cache->saveUrlContentToCache( $full_page ); //save page to cache
		echo $full_page;
	}else{
		//if mongo results are false go to 404,	logic in getHomePagePosts Funtion			
		goTo404();
	}
	
?>