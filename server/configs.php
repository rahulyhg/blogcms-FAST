<?php 
session_start();
date_default_timezone_set('UTC');
$GLOBALS['server_path'] = dirname(__FILE__);
$GLOBALS['index_path'] = $GLOBALS['server_path']."/../main";
$GLOBALS['base_url'] = "http://".$_SERVER['HTTP_HOST'];
$GLOBALS['template_dir'] = $GLOBALS['server_path']."/templates";
$GLOBALS['cache_dir'] = dirname(__FILE__)."/page_cache";
$GLOBALS['url_parts'] = preg_split( "/\//", preg_replace( "/\/$/", "", preg_replace( "/\?.+/", "", substr( $_SERVER['REQUEST_URI'], 1 ) ) ) );

//folder where temp image thumbnail files are created and then copied into mongo file store
$GLOBALS['tmp_file_directory'] = "/tmp";

//audio and video files allowed to be uploaded through the manager
//if adding new type add a new permitted extension & mime type
$GLOBALS["upload_vars"] = array(
	"allowed_image_extensions"=>array("gif", "jpeg", "jpg", "png", "JPG"),
	"allowed_audio_extensions"=>array("mp3"),
	"allowed_mimetypes"=>array("image/jpeg", "image/gif", "image/jpg", "image/png", "audio/mpeg"),
	"max_kb_img_upload"=>500,
	"max_mb_audio_upload"=>40
);
$GLOBALS["upload_vars"]["allowed_extensions"] = array_merge ( $GLOBALS["upload_vars"]["allowed_image_extensions"], $GLOBALS["upload_vars"]["allowed_audio_extensions"] );

//mongo DB name project uses
$GLOBALS['mongo_db_name'] = "blog";

//keyword used to access manager page (no spaces!)
$GLOBALS['manager_keyword'] = "manager";

//# of posts that show up per page
$GLOBALS['amount_on_main_page'] = 4;

//# of post previews that show up underneath post when viewing post url 
$GLOBALS['amount_of_next_posts'] = 1;

//# of posts that show up on the "posts" tab in the manager
$GLOBALS['amount_on_manger_tab'] = 5;

//minutes until cache file expires
$GLOBALS['max_page_cache_mins'] = -5; //turned off for dev turn on for prod to resonable amount of mins

$GLOBALS['max_title_length'] = 500;
$GLOBALS['max_desc_length'] = 500;
$GLOBALS['max_tags_length'] = 1000;
$GLOBALS['max_folder_path_length'] = 1000;

function returnMessage( $success, $message, $data ){
	$holder = Array( 'result'=>$success, 'message'=>$message, 'data'=>$data );
	return json_encode( $holder );
};
	
//autoload any class with this function
spl_autoload_register('myAutoloader');

function myAutoloader( $className )
{
    $path = dirname(__FILE__).'/classes/';

    include $path.$className.'.php';
}

function goTo404(){
	$error_box = $GLOBALS['server_path']."/pages/404.php";
	header( $_SERVER["SERVER_PROTOCOL"]." 404 Not Found" );	
	include $error_box;	
}


//mongo connection string can be changed here
function MongoConnection(){
	return new MongoClient(/*"mongodb:///tmp/mongodb-27017.sock"*/);
}
?>
