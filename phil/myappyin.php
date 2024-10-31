<?php
namespace SmartIntelPWAAppy;

if ( ! defined( 'ABSPATH' ) ) 
{
	exit; // Exit if accessed directly
}

class MyAppyIn
{
	static public function CallPage($despageid)
	{
		global $post;

		$post=get_post($despageid);
		$htm=RenderPage::DoMyPageContent($despageid);
	}

	static public function InitPage()
	{
		RenderPage::DoAjaxPageScripts();
		//apply_filters('SmartDesign_DoAjaxPageScripts',null);
	}

	static public function GetVal($v,$def)
	{
		$v=isset($_GET[$v])?$_GET[$v]:$def;
		if ($v===$def)
			return $def;

		if (is_string($v))
			return sanitize_textarea_field($v);

		if (is_object($v))
		{
			$v=wp_json_encode($v);
			return json_decode($v,true);
		}
		if (is_array($v))
		{
			$v=wp_json_encode($v);
			return json_decode($v,true);
		}
		return $v;
	}

	static public function EcAsc($str)
	{
		$str0=esc_textarea($str);
		//@@bugsmy14thMay24
		echo $str0;
	}

	static public function EcIFr($str)
	{
 		//echo self::MyKss($str, ['iframe']);
		//echo wp_kses_post($str);
		Funcs2::MyKss1($str,['iframe'=>['style','src']]);
	}
}

$bIsAjaxPage=true;
//$bWantDirect=true;
//$bIsAjaxPage=$PagedPwa_routedata['ajaxpage']=='true'?true:false;

$pg=MyAppyIn::GetVal('page',null);
$smartslug=MyAppyIn::GetVal('slug',null);

//if (!$smartslug)
//	$smartslug='appypage';

//$appslug='a2hspage';

$gettxt=MyAppyIn::GetVal('txt',null);
$getbody=MyAppyIn::GetVal('body',null);
$gettag=MyAppyIn::GetVal('tag',null);
$appslug=MyAppyIn::GetVal('appslug',null);

$ref=Funcs::GetHomeRef();




wp_enqueue_script('frontonmsg.js',PAGEDPWA_MYPWAAPPYPLUGAPP_URI . 'assets/js/frontonmsg.js', 
	array(), PAGEDPWA_MYPWAAPPYPLUGAPP_VERSION, true );

MyPWAAppyPlug::GetUserStuff();
	
$despageid=null;


$dirs=Funcs::GetDirsFromUrl();
$slug=$dirs[count($dirs)-1];


	//'title'      => $slug,
//$smartslug='appypage';

$pinm=MyPWAAppyPlug::GetPlugNameHere();
//$despageid=DoPostTypes::GetGoodPageId($smartslug,$pinm);

global $post;
//null is allowed, optional smart page run on host
if ($smartslug)
{
	$despageid=RenderPage::GetGoodPageId($smartslug,$pinm);
	///create rec?!?
	
	if (!$despageid)
	{
		self::EcStr("$smartslug not found");
		die;
	}

	/**
	 * $post=get_post($despageid);
	 */
	Funcs::SetQuerySmart($smartslug,$pinm);
}



get_header();

$home=get_home_url();
//PTS_CARDSBYYOU_URI;
//$dirs=Funcs::GetDirsFromUrl();
$arr=explode('/',$home);
array_pop($arr);
$home=implode('/',$arr);


//<div id="container" class="one-column">

?>

<div id="acontainer" style='max-width: calc( 100% - 6em );margin: 0 auto;' class="one-column">

<main id="main" style='width:100%;' class="site-main  site-main--single" role="main">

<div style='font-size:12pt;'>

<?php

$host=Funcs2::GetServVar('HTTP_HOST');
//@@bugsmy14thMay24
//$ref0=$_SERVER['HTTP_REFERRER'];
$reqpath0=Funcs::GetReqUrlNoQuery();

$scriptby=Funcs::GetScriptByYouUrl();

//$tagmsg=$_ GET['tag'];
//if (!$tagmsg)
//	$tagmsg='';

$me = get_current_user_id();

if ($pg=='design')
{
	MyAppyIn::EcIFr("<iframe style='width:100%;height:800px;' 
		src='$scriptby/designfrm/?pageid=$appslug&reqpath=$ref&me=$me'></iframe>");
}
else if ($pg=='skin')
{
	MyAppyIn::EcIFr("<iframe style='width:100%;height:800px;' 
		src='$scriptby/skinfrm/?pageid=$appslug&reqpath=$ref&me=$me'></iframe>");
}
else if ($pg=='run')
{
	if ($gettag)
	{
		MyAppyIn::EcIFr("<iframe style='width:100%;height:800px;' 
			src='$scriptby/mybarepage/?pageid=$appslug&reqpath=$ref&me=$me&tagmsg=$gettag'></iframe>");
	}
	else
	{
		MyAppyIn::EcIFr("<iframe style='width:100%;height:800px;' 
			src='$scriptby/mybarepage/?pageid=$appslug&reqpath=$ref&me=$me&txtmsg=$gettxt&body=$getbody'></iframe>");
	}
}

global $PagedPwa_smart_pageid;
//@@bugsmy14thMay24
$PagedPwa_smart_pageid=$despageid;

global $PagedPwa_pdtMySmartRun;
//@@bugsmy14thMay24
$PagedPwa_pdtMySmartRun=true;

?>


	</div>
	</main><!-- #main -->

</div><!-- #primary -->

<div style="height:12em;" id='content'>

<?php

get_footer();
?>
</div>
