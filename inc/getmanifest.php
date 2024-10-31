<?php
namespace SmartIntelPWAAppy;
//include "C:\\xampp\\htdocs\\wp\wp-admin\admin-ajax.php";
//require_once( __DIR__.'/inc/functions.php' );
//$_ REQUEST['action']='getmanifest';
//require_once("C:/xampp/htdocs/wp/wp-admin/admin-ajax.php");

$lp=__DIR__."/../man.test.json";
$byts=file_get_contents($lp);       //---orgfixfgc

header('Content-Type: application/json');
//status_header(200); 

$status_header="HTTP/1.1 200 OK";
$code=200;
header( $status_header, true, $code );

//ec ho $byts; 
//@@bugsmy14thMay24		
echo esc_textarea($byts);


exit;
