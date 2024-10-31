<?php
/**
 * @package pwaappy
 * @version 2.1.5
 */
/*
Plugin Name: PagedPWA
Description: This is a plugin for PagedPWA enabling PWA functionality in WP websites
Author: bristolwebmatters.co.uk
Version: 2.1.5
Author URI: http://bristolwebmatters.co.uk/
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

namespace SmartIntelPWAAppy;

define( 'PAGEDPWA_MYPWAAPPYPLUGAPP_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );
//@@bugsmy14thMay24
define( 'PAGEDPWA_MYPWAAPPYPLUGAPP_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
//@@bugsmy14thMay24
define( 'PAGEDPWA_MYPWAAPPYPLUGAPP_VERSION', '1.5.19' );
//@@bugsmy14thMay24

define( 'PAGEDPWA_MYPWAAPPY_WANTPWA',1);
//@@bugsmy14thMay24

require_once( __DIR__.'/inc/functions.php' );


require_once( __DIR__.'/inc/optionsauto.php' );
require_once( __DIR__.'/inc/servservpush.php' );

require_once(PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."copy/posttypeadmin.php");
use \SmartIntelPWAAppy\PostTypeAdmin as RegPostType;
require_once( __DIR__.'/inc/theplugin.php');

class MyPWAAppyPlug
{	
	static public function RegisterHooks()
	{
		//$bstrSafeMode=$_ GET['resetsettings'];
		$bstrSafeMode=self::GetVal('resetsettings',null);

		$bIsSafeMode=$bstrSafeMode=='safe';

		//if (!$bIsSafeMode)
			//add_action('admin_menu', array(__CLASS__,'InitMsgType'),1,0);
			add_action( 'template_include', array(__CLASS__,'HandleTemplateInc'),1000 );

		//jquery, xmlparse ...
		////apply_filters('Smart Design_DoAjaxPageScripts',null);
			
		//add_action( 'wp_enqueue_scripts', array(__CLASS__,'IncFrontHdrScripts') );

		//ajax action
		$ns=array(__CLASS__,'mypwaappyplug_action');
		add_action( 'wp_ajax_mypwaappyplug_action', $ns );
		add_action( 'wp_ajax_nopriv_mypwaappyplug_action', $ns );

		add_filter( 'allowed_http_origins', array(__CLASS__,'add_allowed_origins') );
		//allow appyapp in 'cards' host iframe

		$ns=array(__CLASS__,'mypwaappyplug_manifest');
		add_action( 'wp_ajax_mypwaappyplug_manifest', $ns );
		add_action( 'wp_ajax_nopriv_mypwaappyplug_manifest', $ns );

		$ns=array(__CLASS__,'mypwaappyplug_getpush');
		add_action( 'wp_ajax_mypwaappyplug_getpush', $ns );
		add_action( 'wp_ajax_nopriv_mypwaappyplug_getpush', $ns );

		$ns=array(__CLASS__,'mypwaappyplug_getcacheurl');
		add_action( 'wp_ajax_mypwaappyplug_getcacheurl', $ns );
		add_action( 'wp_ajax_nopriv_mypwaappyplug_getcacheurl', $ns );

		$ns=array(__CLASS__,'mypwaappyplug_getservvars');
		add_action( 'wp_ajax_mypwaappyplug_getservvars', $ns );
		add_action( 'wp_ajax_nopriv_mypwaappyplug_getservvars', $ns );


		add_action('init', array(__CLASS__,'DoInitPage'));
		//
			

		add_action( 'wp_head', array( __CLASS__, 'AddHdrScripts' ) );
		add_action( 'template_redirect', array(__CLASS__,'HandleRedirect'),1000 );
		add_action('admin_init', array(__CLASS__,'AddImageSize'));

		//$ns=array(__CLASS__,'do_filter_content');
		//add_filter( 'the_content', $ns, 10);
		add_action( 'wp_enqueue_scripts', array(__CLASS__,'IncFrontAddScripts') );



		add_action('init', array(__CLASS__,'InitMsgType'));
		//admin pgs
		add_action('PagedPwa_GetMetaBoxPg', array(__CLASS__,'GetMetaBoxPg'),10,3);

		$ns=array(__CLASS__,'my_login_redirect');
		add_action( 'login_redirect', $ns,3,3);

		//
		add_action( 'parse_request', [ __CLASS__, 'HandleParseUrl' ] );
		//add_action( 'SmartPostTypes_CallFromMetabox', [ __CLASS__, 'CallFromMetabox' ] );
		add_filter( 'js_escape', [__CLASS__,'PwaForWpJsEscape'],
			100,2 );
	}
    


	/**
	 * add_action( 'wp_ajax_nopriv_mypwaappyplug_manifest
	 */
    static public function mypwaappyplug_manifest() 
	{

		$byts=self::GenManifest();
		if (!$byts)
		{
			//return;
			//exit;
			$byts='';
		}
		
		header('Content-Type: application/json');
		//status_header(200); 
		
		$status_header="HTTP/1.1 200 OK";
		$code=200;
		header( $status_header, true, $code );
		
		self::EcJs($byts);
		exit;
		
		wp_die(); // this is required to terminate immediately and return a proper response
	}


	/**
	 * add_action( 'wp_ajax_nopriv_mypwaappyplug_getpush
	 */
    static public function mypwaappyplug_getcacheurl() 
	{
        $FilesCache=self::GetOption('FilesCache');
		$arr=explode("\r\n",$FilesCache);
		$json=wp_json_encode($arr);
		
		//e cho $json;
		self::EcJs($json);
		exit;
		
		wp_die(); // this is required to terminate immediately and return a proper response
	}

	
	/**
	 * add_action( 'wp_ajax_mypwaappyplug_getservvars', $ns );
	 */
    static public function mypwaappyplug_getservvars() 
	{
		$oCustData=Funcs::GetCustData();
		$strJson=wp_json_encode($oCustData);
		$str="var g_opwapaged=$strJson;";

		header('Content-Type: application/javascript');
		status_header(200); 

		self::EcJs($str);

		//'wp-admin/admin-ajax.php?action=mypwaappyplug_getservvars&varsonly=1';
		$poVarsOnly=self::GetVal('varsonly',null);
		if (!$poVarsOnly)
		{
			//webmainhs.js and websw.js
			//
			
			$f=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."assets/js/myfirebase.js";
			$js=file_get_contents($f);		//---orgfixfgc
			self::EcJs($js);
			$f=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."assets/js/_webmainhm.js";
			$js=file_get_contents($f);		
			self::EcJs($js);
			
			$poSw=self::GetVal('sw',null);
			if ($poSw)
			{
				//self::mypwaappyplug_getcacheurl();
		        $FilesCache=self::GetOption('FilesCache');
				$arr=explode("\r\n",$FilesCache);
				$json=wp_json_encode($arr);
				$js="var filesToCache0=$json;";
				self::EcJs($js);
			
		        $FilesNotCache=self::GetOption('FilesNotCache');
				$arr=explode("\r\n",$FilesNotCache);
				$json=wp_json_encode($arr);
				$js="var m_arrExcluded=$json;";
				self::EcJs($js);

			}
			//?action=mypwaappyplug_getservvars&sw=1
			$js=";firebase=myfirebase;";
			self::EcJs($js);
		}

		$d=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."assets/js/swpagenoti";
		$strPageHand=self::GetPageNoti($d);

		/*
		$f=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."assets/js/swpagenoti.js";
		if (file_exists($f))
		{
			$js=file_get_contents($f);
			ec ho $js;
		}
		*/

		$str="function ShowPageNoti(noti, dat,pg, oData)\r\n";
		$str.="{";
		$str.=$strPageHand;
		$str.="}";
		self::EcJs($str);

		$f=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."assets/js/myswfuncs.js";
		$js=file_get_contents($f);		
		self::EcJs($js);
		
		exit;
		
		wp_die(); // this is required to terminate immediately and return a proper response
	}

	static private function AddImg(&$i,$strKey,$strImg)
    {
		self::AddImgPurpose($i,$strKey,$strImg,'any');
	}

	static private function AddImgPurpose(&$i,$strKey,$strImg,$purpose)
    {
		if (!$strImg)
			return;

        $uploads = wp_upload_dir();
        $base=$uploads['basedir'];
        //$baseurl=$uploads['baseurl'];
        $baseurl=$uploads['url'];

        $r=array();
        $r['src']="{$baseurl}/$strImg";
        $r['sizes']="{$strKey}x{$strKey}";
        $r['type']="image/png";
        $r['purpose']=$purpose;
        $i[]=$r;
    }

	/**
	 * return json
	 * or null as error
	 */
	static public function GenManifest() 
	{
        $offlinestrategy=self::GetOption('offlinestrategy');
		if (!$offlinestrategy)
			return null;

		$img128=self::GetOption('img128');
		$img144=self::GetOption('img144');
		$img152=self::GetOption('img152');
		$img192=self::GetOption('img192');
		$img256=self::GetOption('img256');
		$img512=self::GetOption('img512');
		$bigname=self::GetOption('bigname');
		$shortname=self::GetOption('shortname');
		$start_url=self::GetOption('start_url');
		$displayas=self::GetOption('displayas');
		$bgcolor=self::GetOption('bgcolor');
		$themecolor=self::GetOption('themecolor');

		$i=array();
		self::AddImg($i,'128',$img128);
		self::AddImg($i,'144',$img144);
		self::AddImg($i,'152',$img152);
		self::AddImg($i,'192',$img192);
		self::AddImg($i,'256',$img256);
		self::AddImg($i,'512',$img512);

		$imgmaskable=self::GetOption('imgmaskable');
		$imgmaskable512=self::GetOption('imgmaskable512');
		self::AddImgPurpose($i,'512',$imgmaskable512,'maskable');
		self::AddImgPurpose($i,'192',$imgmaskable,'maskable');
		$imgMono=self::GetOption('imgMono');
		self::AddImgPurpose($i,'512',$imgMono,'monochrome');

		$scr=[];
		$imgScr512=self::GetOption('imgScr512');
		self::AddImgPurpose($scr,'512',$imgScr512,'maskable');

		$m=array();
		$m['name']=$bigname;
		$m['short_name']=$shortname;
		$appdescript=self::GetOption('appdescript');
		$m['description']=$appdescript;
		$m['icons']=$i;
		if ($scr)
			$m['screenshots']=$scr;
		$m['lang']='en-US';
		$m['display']=$displayas;
		$m['background_color']=$bgcolor;
		$m['theme_color']=$themecolor;

		$m['gcm_sender_id']="103953800507";
		$sid=self::GetOption('SenderID');
		if ($sid)
			$m['gcm_sender_id']=$sid;

		//share target
		$st=[];
		$params=[];

		//UTM tracking
		$utm=[];
        $utm['campaign']=self::GetOption('UTMcampaign');
        $utm['source']=self::GetOption('UTMsource');
        $utm['medium']=self::GetOption('UTMmedium');
        $utm['content']=self::GetOption('UTMcontent');
        $utm['term']=self::GetOption('UTMterm');
		$q='';
		$bValued=false;
		foreach ($utm as $k=>$v)
		{
			if (!$v)
				continue;

			$bValued=true;
			if ($q!='')
				$q.="&";

			$v=urlencode($v);
			$q.="$k=$v";
		}
		$start_url=str_replace("\\",'/',$start_url);
		$nLen=strlen($start_url);
		if ($start_url[$nlen-1]!='/')
			$start_url.='/';
		if ($q)
		{
			//$q=urlencode($q);
			//$q=rawurlencode($q);
			$start_url.="?".$q;
		}
		$m['start_url']=$start_url;

		//shortcuts
        $BadgeShortcut=self::GetOption('BadgeShortcut');
        $shortpage_url=self::GetOption('shortpage_url');
		if ($shortpage_url && $BadgeShortcut && $BadgeShortcut=='on')
		{
			$sc=[[
				"name"=>$bigname,
				"url"=>$shortpage_url,
				"description"=>$appdescript
			]];
			$m['shortcuts']=$sc;
		}

        $diras=self::GetOption('diras');
		if ($diras)
			$m['dir']=$diras;
        $orientationas=self::GetOption('orientationas');
		if ($orientationas)
			$m['orientation']=$orientationas;

		///$st['action']='/share-target/';
		///$st['method']='GET';
		//$params['title']='title';
		//$params['text']='text';
		//$params['url']='url';

		$st['action']='/bookmark';
		$st['method']='POST';
		$st['enctype']='multipart/form-data';

		$params['url']='link';

		$fil0['name']="records";
		$fil0['accept']=["text/csv",".csv"];
		$fil1['name']="graphs";
		$fil1['accept']="image/svg+xml";
		$fils=[$fil0,$fil1];
		///$params['files']=$fils;

		$st['params']=$params;
		//$m['share_target']=$st;

        $extramanifest=self::GetOption('extramanifest');
		$o=json_decode($extramanifest,true);
		if ($o)
			$m=array_merge($m,$o);

		$json=wp_json_encode($m,JSON_PRETTY_PRINT);

		//$ld=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."man.test.json.txt";
		//$ld=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."man.test.json";
		//file_put_contents($ld,$json);
		return $json;
	}

	/**
	 * do_action( 'get_header', $name, $args );
	 */
	static public function do_filter_header($name,$args)
	{
		global $post;
		if (is_admin())
			return false;

		if (!self::IsStartUrlPage())
			return false;

		//$htm=self::DoMyContent();
		//ec ho anything AND a2hs stops firing
		//ec ho "<span>abc</span>";
		return false;
	}




	/**
	 * add_action('admin_init'
	 */
	static public function AddImageSize() 
	{
		add_image_size('PwaPlugIcon128',128,128,true);
		add_image_size('PwaPlugIcon144',144,144,true);
		add_image_size('PwaPlugIcon152',152,152,true);
		add_image_size('PwaPlugIcon192',192,192,true);
		add_image_size('PwaPlugIcon256',256,256,true);
		add_image_size('PwaPlugIcon512',512,512,true);
		//add_image_size('AppyAppsIcon1024',1024,1024,false);
	}
	

		
	/**
	 * add_action('init',
	 */
	static public function DoInitPage() 
	{

	}



	/**
	 * add_action( 'wp_enqueue_scripts'
	 */
	static public function IncFrontAddScripts()
	{
		wp_enqueue_style( 'allpages.css', PAGEDPWA_MYPWAAPPYPLUGAPP_URI. 'assets/allpages.css', 
			array(), PAGEDPWA_MYPWAAPPYPLUGAPP_VERSION );

		wp_enqueue_script('top_idbfuncs',PAGEDPWA_MYPWAAPPYPLUGAPP_URI . 'assets/js/idbfuncs.js', 
			array('jquery'), PAGEDPWA_MYPWAAPPYPLUGAPP_VERSION, true );

		if (!self::IsStartUrlPage())
		{
			//all pages
			//AddHdrScripts
			//$url="{$admin}admin-ajax.php?action=mypwaappyplug_getservvars";
			wp_enqueue_script('myfirebase.js',PAGEDPWA_MYPWAAPPYPLUGAPP_URI . 'assets/js/myfirebase.js', 
				array(), PAGEDPWA_MYPWAAPPYPLUGAPP_VERSION, true );
			//const firebase = new MyFireBase();
			//needs:
			//const msg = firebase.messaging();
			//msg.onMessage
			return;
		}

		//
		//sart page only ...



	}

	/**
	 * add_action( 'wp_enqueue_scripts'
	 */
	static public function IncFrontHdrScripts()
	{
	}

	/**
	 * add_action( 'template_include'
	 */
	static public function HandleTemplateInc($tplt) 
	{
		if (self::IsRequestSW())
			return $tplt;

		$dirs=Funcs::GetDirsFromUrl();
		$tmpl=self::HandleRoute($dirs,$tplt);
		return $tmpl;
	}

	/**
	 * add_action( 'template_redirect'
	 */
	//ec ho bytes
	static public function HandleRedirect($tplt) 
	{
		$dirs=Funcs::GetDirsFromUrl();
		if (self::IsRequestSW())
		{
			//$sw=$url;
			//$fname='websw.js';
			$fname=$dirs[0];
			$path=__DIR__."/{$fname}";
			$bytes=file_get_contents($path);

			header('Content-Type: application/javascript');
			status_header(200); 

			self::EcJs($bytes);
			exit;
		}
	}

	/**
	 * return arr route data
	 */
	static public function GetRoutesUrl()
	{
		$arr=array();

		$arr['myappyin']=array('myappyin','myappyin.php');
		return $arr;
	}

	/**
	 * return local path
	 * handle menu request - bail out
	 * add_action( 'template_include'
	 */
	static public function HandleRoute($dirs,$tplt)
	{
		//$url=$_SERVER['REQUEST_URI'];
		//@@bugsmy30June24
		$url=Funcs::GetServUrl('REQUEST_URI');
		$rights=self::GetUserRights();

		

		$h=is_home();
		$p=is_page();
		$s=is_single();
		if ($h)
		{
			return $tplt;
		}

		$newtplt=self::IsRoute($dirs,$tplt,$rttype,$ast);
		if ($newtplt==null)
			return $tplt;

		global $wp_query;
		$wp_query->is_404 = false;
		status_header(200); 
	
		global $wp_query;		
		return $newtplt;
	}

	static public function IsAdminish()
	{
		$rights=self::GetUserRights();
		if ($rights>=2)
			return true;
		return false;
	}

	/**
	 *  -1(anon),0(ord),1(user admin),2(wp admin)
	 */
	static public function GetUserRights() 
	{
		//-< manage_options, level_1, level_0
		if (!is_user_logged_in())
			return -1;
			
		if (current_user_can('manage_options'))
			return 2;

		//$usr=wp_get_current_user();
		//$usr->get_role_caps();
		//$wp_roles = wp_roles();
		//$r=$wp_roles['cards_admin'];
			//get_roles

		if (current_user_can('cards_admin_create'))
		//if (current_user_can('cards_admin'))
			return 1;
		return 0;
	}
	
	static public function GetPlugNameHere()
	{
		$d=plugin_dir_path(__FILE__);
		$d2=str_replace('/',"\\",$d);
		$d3=explode("\\",$d2);
		$sd=array_pop($d3);
		if (!$sd)
			$sd=array_pop($d3);
			return $sd;
	}
	
	/**
	 * return null as not me
	 */
	static public function IsRoute($dirs,$tplt,&$routetype,&$dirpart)
	{
		$dirpart=null;

		$arr=self::GetRoutesUrl();
		foreach ($arr as $key=>$d)
		{
			$rt=$d[0];
			$tpltis=$d[1];
			$fnd=Funcs::IsThisRoute($dirs,$rt,$dirpart);
			if ($fnd)
			{
				$routetype=$key;
				$tplt=__DIR__."/phil/$tpltis";
				return $tplt;
			}
		}
		return null;
	}
	
	/**
	 * ajax
	 * add_action( 'wp_ajax_nopriv_mypwaappyplug_action
	 */
    static public function mypwaappyplug_action() 
	{
		///$path=PTS_APPYPLUGAPP_PATH . 'assets/1617156928903.jpg';
		//$path=PTS_APPYPLUGAPP_PATH . 'assets/IMG_20180218_135930.jpg';
		//$um=Funcs::GetImageLocation($path);

		//-$function = $_ POST['function'];
		//if ($function=='DoFollowMe')
		//	self::DoFollowMe();
		$function=self::ReqVal('function',null);
		if ($function=='DoGetThumbs')
			self::DoGetThumbs();


		else if ($function=='AddSubscription')
			self::AddSubscription();
		else if ($function=='UpdateMetaField')
			self::UpdateMetaField();
		else if ($function=='UpdateRecFieldItem')
			self::UpdateRecFieldItem();
		else if ($function=='DelSubscription')
			self::DelSubscription();
		else if ($function=='StorePageCtl')
			self::StorePageCtl();
		else if ($function=='SetPushMsgType')
			self::SetPushMsgType();

		wp_die(); // this is required to terminate immediately and return a proper response
	}



	/**
	 * need to be logged on as reported by WP
	 * =true: ok
	 */
	static function ChkSecurity(&$obj,$wpuserid)
	{
		$obj['valid']=true;
		if (!is_user_logged_in())
		{
			$obj['valid']=false;
			$obj['errmsg']='Please login first';
			return false;
		}

		//-$usr=wp_get_current_user();
		$usr = get_current_user_id();
		if ($usr!=$wpuserid)
		{
			$obj['valid']=false;
			$obj['errmsg']='Security Problem';
			return false;
		}
		return true;
	}

	/**
	 * from route
	 * fromcardsbyyou
	 */
	static public function GetUserStuff() 
	{
		if (!is_user_logged_in())
			return;

		$us0=wp_get_current_user();
		$usrid = get_current_user_id();
		//$passnow = get_user_meta( $usrid, 'passnow', true );
		$vouchcredit = get_user_meta( $usrid, 'vouchcredit', true );
		if (!is_numeric($vouchcredit))
			$vouchcredit=0;

		//$vouchcredit=5.99;

		$data=array(
			//'passnow'=>$passnow,
			'vouchcredit'=>$vouchcredit,
		);
		$strJson=wp_json_encode($data);
		self::EcScr("<script type='text/javascript' media='all'>var g_oUsersMeta=$strJson;\r\n</script>");
	}
	
	/**
	 * CORS
	 * header('Access-Control-Allow-Origin: *');
	 * https://wordpress.stackexchange.com/questions/198943/is-there-a-way-to-enable-cross-origin-resource-sharing-for-wordpress-ajaxurl
	 */
	static public function add_allowed_origins($origins)
	{
		//header( 'Access-Control-Allow-Origin: ' . $origin );
		//header( 'Access-Control-Allow-Credentials: true' );
		header( 'Access-Control-Allow-headers: *');

		//Access-Control-Allow-Methods: *

		//-$origins[] = 'https://site1.example.com';
		//-$origins[] = 'https://site2.example.com';
		//$origins[] = '*';
		//$origins[]=$_SERVER['HTTP_ORIGIN'];
		//@@bugsmy30June24
		$origins[]=Funcs::GetServUrl('HTTP_ORIGIN');

		return $origins;
	}
	//return apply_filters( 'http_origin', $origin );



	
	static public function IsRequestSW()
	{
		if (PAGEDPWA_MYPWAAPPY_WANTPWA!=1)
			return false;

		//$rh=self::IsHomeRootUrl();
		//if (!$rh)
		//	return false;

		//$url=$_SERVER['REQUEST_URI'];
		//@@bugsmy30June24
		$url=Funcs::GetServUrl('REQUEST_URI');

		$dirs=Funcs::GetDirsFromUrl();
		if (count ($dirs)==1 && ($dirs[0]=='websw.js' || $dirs[0]=='firebase-messaging-sw.js'))
		{
			//$rh=self::IsHomeRootUrl();
			return true;
		}
		return false;
	}


	/**
	 * add_action( 'wp_head'
	 */
	static public function AddHdrScripts()
	{
		$oCustData=Funcs::GetCustData();
		$strJson=wp_json_encode($oCustData);
		$str="<script type='text/javascript'>var g_opwapaged=$strJson</script>";
		self::EcScr($str);

		//
		$hdr=self::GetMetaCSP();
		self::EcHtm($hdr);

		//$rh=self::IsHomeRootUrl();
		$rh=self::IsStartUrlPage();
		if ($rh && PAGEDPWA_MYPWAAPPY_WANTPWA==1)
		{
			$img128=self::GetOption('img128');
			$i=array();
			self::AddImg($i,'128',$img128);
			$r=$i[0];
			$hdrs=get_headers($r['src']);
			if ($hdrs===false)
				return;


			//admin_url( 'admin-ajax.php');
			$admin=admin_url();
			$url="{$admin}admin-ajax.php?action=mypwaappyplug_manifest";

			//route start_url
			$str="<link rel='manifest' href='$url'>";
			self::EcHtm($str);

			$bAppFireJsCompat=false;
			self::GetRunConfig($bAppFireJsLib,$bAppFireJsCompat,$bAppMyStartup,$bAppFireEs6,$bMyFBLib);
			if ($bAppFireJsLib)
			{
				//$ver='10.0.0';
				$ver='8.10.1';

				self::EcScr("<script defer src='https://www.gstatic.com/firebasejs/{$ver}/firebase-app.js'></script>");
				self::EcScr("<script defer src='https://www.gstatic.com/firebasejs/{$ver}/firebase-auth.js'></script>");
				self::EcScr("<script defer src='https://www.gstatic.com/firebasejs/{$ver}/firebase-firestore.js'></script>");
				self::EcScr("<script defer src='https://www.gstatic.com/firebasejs/{$ver}/firebase-analytics.js'></script>");

				//ec ho '<script defer src="https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging.js"></script>';
				self::EcScr("<script defer src='https://www.gstatic.com/firebasejs/{$ver}/firebase-messaging.js'></script>");

				// ...
				//-import { initializeApp } from "https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js";
				//-import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.0.0/firebase-analytics.js";
				//-ec ho '<script defer src="./init-firebase.js"></script>';
			}
			if ($bAppFireJsCompat)
			{
				$ver='9.0.1';
				self::EcScr("<script defer src='https://www.gstatic.com/firebasejs/{$ver}/firebase-app-compat.js'></script>");
				self::EcScr("<script defer src='https://www.gstatic.com/firebasejs/{$ver}/firebase-messaging-compat.js'></script>");
			}
	
			if ($bAppMyStartup)
			{
				$url=PAGEDPWA_MYPWAAPPYPLUGAPP_URI."webmainhm.js";
				$str="<script type='text/javascript' defer src='$url'></script>";
				self::EcScr($str);

				if ($bMyFBLib)
				{
					//my fb library for webmainhm.js
					$admin=admin_url();
					$url="{$admin}admin-ajax.php?action=mypwaappyplug_getservvars";
					$str="<script type='text/javascript' src='$url'></script>";
					self::EcScr($str);
				}

			}
			//-webgcm after webmainhm because firebase.initializeApp
			if ($bAppFireEs6)
			{
				$url=PAGEDPWA_MYPWAAPPYPLUGAPP_URI."webgcm.js";
				$str="<script type='module' src='$url'></script>";
				self::EcScr($str);
			}
		}
	}



	/**
	 * home url
	 * N.B wont work for routes cause $post
	 */
	static public function IsHomeRootUrl() 
	{
		global $post;
		if (!$post)
			return false;

		//$h=is_home();
		$dirs=Funcs::GetDirsFromUrl();
		return count($dirs)==0?true:false;
	}

	static public function DoGetThumbs()
	{
		//-$strFunc = $_ POST['strFunc'];
		$curpostid=self::PostVal('curpostid',null);
		$wpuserid=self::PostVal('wpuserid',null);
		$strId=self::PostVal('m_strId',null);
		$strObjFunc=self::PostVal('strObjFunc',null);
		$the_ids=self::PostVal('the_ids',null);

		//-ret
		$obj=array();
		$obj['ok']=1;
		$obj['valid']=true;

		$bValid=self::ChkSecurity($obj,$wpuserid);
		$objMeta=array();
		$idgood=$the_ids;
		$img=wp_get_attachment_url($idgood);
		if ($bValid && $img!==false)
		{	
			$icon=false;
			$size='large';
			$meta=wp_get_attachment_metadata($idgood);
			$imageSrc=wp_get_attachment_image_src($idgood,$size,$icon);
		
			$postrec=get_post($idgood);
			$objMeta['id']=$idgood;
	
			$objMeta['htmtext']=$postrec->post_title;
	
			$objMeta['imgorig']=$img;
			$objMeta['imgurl']=$imageSrc[0];
	
			$objMeta['imgwidth']=$meta['width'];
			$objMeta['imgheight']=$meta['height'];
	
			$objMeta['url']=$imageSrc[0];
			$objMeta['wdt']=$imageSrc[1];
			$objMeta['hgt']=$imageSrc[2];
			$objMeta['pass']=$imageSrc[3];

			$objMeta['meta']=$meta;
		}

		$obj['meta']=$objMeta;
		//$obj['usrid']=$usrid;

		$obj['m_strId']=$strId;
		$obj['strObjFunc']=$strObjFunc;
		//-$obj['strFunc']=$_ POST['strFunc'];	

		$strJson=wp_json_encode($obj);
		self::EcJs($strJson);
	}

	/**
	 * action wp_ajax_myprefix_get_image/myprefix_get_image
	 */
	static public function GetImagesFromIds()
	{
		$potheids=self::ReqVal('the_ids',null);
		if (!$potheids )
		{
			wp_send_json_error();
			return;
		}
		$the_ids=self::ReqVal('the_ids',null);

		$wpuserid=self::ReqVal('wpuserid',null);
		$galleryparent=self::ReqVal('galleryparent',null);
		$galnameid=self::ReqVal('galnameid',null);
		$size=self::ReqVal('size',null);
		$curgalid=self::ReqVal('curgalid',null);
		$oldarr=self::ReqVal('oldarr',null);



		//-$idgood=filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
		$arrIds=explode(',',$the_ids);
		//-$size="medium";
		$icon=false;
		//-$size=$_ GET['size'];
		//-$size="thumbnail";
		$m_strId=self::ReqVal('m_strId',null);

		//-$arrImgs=array();
		$nCount=count($arrIds);
		for ($i=0;$i<$nCount;$i++)
		{
			$idgood=$arrIds[$i];

			$img=wp_get_attachment_url($idgood);
			if ($img===false)
				continue;

			$meta = wp_get_attachment_metadata( $idgood );
			$imageSrc = wp_get_attachment_image_src( $idgood, $size, $icon );
		
			$postrec=get_post($idgood);
			//-wp_get_attachment()

			$objMeta=array();
			$objMeta['id']=$idgood;

			$objMeta['htmtext']=$postrec->post_title;

			$objMeta['imgurl']=$imageSrc[0];
			$objMeta['imgorig']=$img;

			$objMeta['imgwidth']=$meta['width'];
			$objMeta['imgheight']=$meta['height'];

			$objMeta['url']=$imageSrc[0];
			$objMeta['wdt']=$imageSrc[1];
			$objMeta['hgt']=$imageSrc[2];
			$objMeta['pass']=$imageSrc[3];

			//-<img>
			//-$image = wp_get_attachment_image( $idgood, $size, $icon, 
			//-	array( 'id' => 'myprefix-preview-image' ) );

			$oldarr[]=$objMeta;
		}

		$arrAllIds=array();
		foreach ($oldarr as $rec)
		{
			$arrAllIds[]=$rec['id'];
		}
		$strIds=implode(',',$arrAllIds);

		self::UpdateMyGallery($curgalid,$strIds,$wpuserid,$galleryparent,$galnameid);

		$ret['arrimg']=$oldarr;

		$ret['m_strId']=$m_strId;
		//-$data = array('image'=>$image,);
		wp_send_json_success($ret);     
	} 
	
	static public function IsStartUrlPage()
	{
		$start_url=self::GetOption('start_url');
		if ($start_url===false)
			return false;

		$parts=wp_parse_url($start_url);
		$start_path=$parts['path'];
		$here=Funcs::GetReqUrlNoQuery();
		$here=trailingslashit($here);
		$start_path=trailingslashit($start_path);
		return $start_path==$here?true:false;
		//return self::IsHomeRootUrl();
	}
	
	static public function GetMsgAtKey($strAction)
	{
		//$hook="SmartPagesPWA_Serv_{$strAction}";
		$jsonset=self::GetOption($strAction);
		return $jsonset;
	}

	static public function SetMsgAtKey($strAction,$r)
	{
		$b=$r['noti']['body'];
		$r['noti']['body']=stripslashes($b);

		$objset=self::UpdateOption($strAction,$r);
		return $objset;
	}

	/**
	 * add_action( 'wp_ajax_nopriv_appyplug_action
	 */
	static public function AddSubscription()
	{
		//-$strFunc = $_ POST['strFunc'];

		//$sub_id = $ POST['sub_id'];
		$curpostid=self::PostVal('curpostid',null);
		$postid=self::PostVal('postid',null);
		$wpuserid=self::PostVal('wpuserid',null);
		$postid=self::PostVal('mypostid',null);
		$pushSubs=self::PostVal('pushSubs',null);
		$tokSubs=self::PostVal('tokSubs',null);

		//
		$bOk=true;
		$obj['ok']=1;
		$obj['valid']=$bOk;

		//mypwaappyplug_getpush
		ServServPush::AddSubscription($wpuserid,$pushSubs,$tokSubs);

		//
		$strId=self::PostVal('m_strId',null);
		$strObjFunc=self::PostVal('strObjFunc',null);

		$obj['m_strId']=$strId;
		$obj['strObjFunc']=$strObjFunc;

		$strJson=wp_json_encode($obj);
		self::EcJs($strJson);
	}

	/**
	 * add_action( 'wp_ajax_nopriv_appyplug_action
	 */
	static public function DelSubscription()
	{
		//-$strFunc = $_ POST['strFunc'];
		$curpostid=self::PostVal('curpostid',null);
		$postid=self::PostVal('postid',null);
		$wpuserid=self::PostVal('wpuserid',null);
		$postid=self::PostVal('mypostid',null);
		$sub_id=self::PostVal('sub_id',null);
		$subs=self::PostVal('subs',null);

		//
		$bOk=true;
		$obj['ok']=1;
		$obj['valid']=$bOk;

		//mypwaappyplug_getpush
		ServServPush::DelSubscription($wpuserid);


		//
		$strId=self::PostVal('m_strId',null);
		$strObjFunc=self::PostVal('strObjFunc',null);


		$obj['m_strId']=$strId;
		$obj['strObjFunc']=$strObjFunc;

		$strJson=wp_json_encode($obj);
		self::EcJs($strJson);
	}

	/**
	 * add_action('init'
	 */
	static public function InitMsgType() 
	{
        global $pagenow;
		global $typenow,$hook_suffix;

		//if ($pagenow!='tools.php')
		//	return;



		self::RegSubscriptType(124);
		self::RegUtmTrackPtype(125);
	}


	static public function RegSubscriptType($recid)
	{
		$ptype='subscripttype';
		$pgslug='mymsgtype';
		//smart pg for design ctls
		//$pgslug='adminpgmsgtype';
		$pgslug='subscripttype';

		$row=array();
		$row['myid']=$recid;
		$row['posttype']=$ptype;
		$row['pageslug']=$pgslug;
		//-page ptype
		$row['tag']="{$ptype}tag";
		$row['ajaxpage']=true;
		//$row['capability']='manage_options';
		//$row['level']=0;
		$row['realtype']='Manage Push Subscription';
		$row['edpageid']='';
		$row['adminrole']='';
		$row['routeid']='';
		$row['routed']='';
		$row['named']='Push Subscriptions';
		$row['myicon']='';
		
		$bInInit=true;
		$strShow="abcshow_$ptype";

		$pi=new ThePlugIn();
		$oPostType=new MyRegPostType($row,$ptype,$strShow,null,$pi);
		//return;
		$oPostType->SetSlugJsnFile(true);
		$oPostType->SetDir(PAGEDPWA_MYPWAAPPYPLUGAPP_PATH);

		$oPostType->RegisterBoxesData();
		//-=> m_pobj['metaboxes
		$oPostType->RegisterAdminCols();
		//-=> m_pobj['['admincols
		$oPostType->RegisterAdminCombos();
		//-=> m_pobj['['admincombos

		$oPostType->RegisterRowActions();
		//-=> m_pobj['['rowactions

		//-$oPostType->RegisterRegTypeJson();
		$oPostType->RegisterRegTypeData();
		//-=> m_pobj['['adminregtype

		$oPostType->RegisterHooks($bInInit);
		//$oPostType->test_reg_custom_post_type();
	}
	
	static public function RegUtmTrackPtype($recid)
	{
		$ptype='utmtrackptype';
		//smart pg for design ctls
		//$pgslug='adminpgmsgtype';
		$pgslug='utmtrackptype';

		$row=array();
		$row['myid']=$recid;
		$row['posttype']=$ptype;
		$row['pageslug']=$pgslug;
		//-page ptype
		$row['tag']="{$ptype}tag";
		$row['ajaxpage']=true;
		//$row['capability']='manage_options';
		//$row['level']=0;
		$row['realtype']='UTM tracking data';
		$row['edpageid']='';
		$row['adminrole']='';
		$row['routeid']='';
		$row['routed']='';
		$row['named']='UTM post';
		$row['myicon']='';
		
		$bInInit=true;
		$strShow="abcshow_$ptype";

		$pi=new ThePlugIn();
		$oPostType=new MyRegPostType($row,$ptype,$strShow,null,$pi);
		//return;
		$oPostType->SetSlugJsnFile(true);
		$oPostType->SetDir(PAGEDPWA_MYPWAAPPYPLUGAPP_PATH);

		$oPostType->RegisterBoxesData();
		//-=> m_pobj['metaboxes
		$oPostType->RegisterAdminCols();
		//-=> m_pobj['['admincols
		$oPostType->RegisterAdminCombos();
		//-=> m_pobj['['admincombos

		$oPostType->RegisterRowActions();
		//-=> m_pobj['['rowactions

		//-$oPostType->RegisterRegTypeJson();
		$oPostType->RegisterRegTypeData();
		//-=> m_pobj['['adminregtype

		$oPostType->RegisterHooks($bInInit);
		//$oPostType->test_reg_custom_post_type();
	}

	/**
	 * ec ho at tyhe moment
	 * $ds=predefined, $d=col name, $c=col?
	 */
	static public function GetDataForCol($po,$ds,$d,$c,$row,$coltype,$mk)
	{
		$po_id=$po->ID;
		if ($mk)
		{
			$v=get_post_meta($po_id,$mk,true);
			self::EcOut($v);
			return $v;
		}
		//-predefined cols
		switch ($ds)
		{
			case "html":
				$d=str_replace('[','<',$d);
				$d=str_replace(']','>',$d);
				self::EcHtm($d);
				break;
			case 'post_content':
				$o=json_decode($po->post_content,true);
				$col=$o[$d];
				if ($col)
					self::EcOut($col);
				else
					self::EcHtm('&nbsp;');
				break;
			/*case 'author':
				MenusAdmin::topic_author_display_link($d,$po->ID);
				break;*/
			/*case 'voices':
				$num=DoPostTypes::GetVoicesOf($po->ID,$d);
				ec ho $num;
				break;*/
			case 'title':
				self::EcOut(self::GetTitleLink( $po ));
				break;
			case 'created':
				printf( '%1$s <br /> %2$s',
					get_the_date(),
					esc_attr( get_the_time() )
				);
				break;
			/*case 'freshness':
				$last_active = MenusAdmin::get_topic_last_active_time( $po_id, false );
				if ( !empty( $last_active ) ) {
					ec ho esc_html( $last_active );
				} else {
					esc_html_e( 'No Replies' );
				}
				break;*/
			/*case 'transpose':
				$v=MyPostType::DoTransposeHtml($d);
				ec ho $v;
				break;*/
			default:
				$col=$po->$d;
				if (!$col || !is_scalar($col))
					self::EcHtm('&nbsp;');
				else
				{
					self::EcOut($col);
				}
				break;
		}
	}

	static public function GetTitleLink( $po ) 
	{
		$t=$po->post_title;
		$href=get_permalink($po);
		if ($href===false)
			return $t;
		$a="<a href='$href'>$t</a>";
		return $a;
	}
	
	//			apply_filters_ref_array( 'PagedPwa_GetMetaBoxPg', array($po,$objBx,&$bx) );
	static public function GetMetaBoxPg($po,$objBx,&$bx) 
	{
		$bx['pageid']=null;
		$bx['dbkey']='pwakey';
		//pwakey2

		$ptype=$po->post_type;
		$id=$po->ID;

		
		if ($ptype=='subscripttype')
		{
			self::DoMetaDBFldsSubs($id);
		}
		else if ($ptype=='utmtrackptype')
		{
			self::DoMetaDBFldsUTM($id);
		}


		//$po=$arrAll[0];
		//$objBx=$arrAll[1];
		//$bx=&$arrAll[2];

		//ec ho "phil here";
	}

	/**
	 * subscription
	 */
    static public function DoMetaDBFldsSubs($id)
    {
        $prefix='';
        $debug=false;

		$po=get_post($id);
		$devid=$po->post_excerpt;
        ?>
		<form>

		<label for="recip">Device Id</label>
		<input type='button' value='Copy to Clipboard' 
			onclick='var b=document.getElementById("idCopyClipBut");b.select();b.setSelectionRange(0,999999);navigator.clipboard.writeText(b.value)'>
		<br/>
        <input style="width:100%" id='idCopyClipBut' type="text" name="pwakey[post][post_excerpt]" 
			id="recip" title='Device Token Id from subscription' 
             value='<?php self::EcStr($devid); ?>'>

		</form>
        <?php

		//
		$url='https://console.firebase.google.com/project/$YOURPROJID/notification/compose';
		//ec ho "<a href='$url'>Go to Google Console Compose Message</a>";
		self::EcHtm("<br><br>Push Messages (Google Console \$YOURPROJID needed): ".
			"<textarea style='width:100%;height:6em;'>$url</textarea>");

		wp_nonce_field( 'bbp_topic_metabox_save', 'bbp_topic_metabox' );
	}

	/**
	 * subscription
	 */
    static public function DoMetaDBFldsUTM($id)
    {
        $prefix='';
        $debug=false;

		$po=get_post($id);
		$utm_campaign=get_post_meta($id,'utm_campaign',true);
		$utm_source=get_post_meta($id,'utm_source',true);
		$utm_medium=get_post_meta($id,'utm_medium',true);
		$utm_content=get_post_meta($id,'utm_content',true);
		$utm_term=get_post_meta($id,'utm_term',true);

        ?>
		<form>

		<br/>
		<label for="utm_campaign">Campaign</label><br/>
        <input style="width:100%" type="text" name="pwakey2[postmeta][utm_campaign]" 
			id="utm_campaign" title='Utm Campaign' 
             value='<?php self::EcStr($utm_campaign); ?>'>
		<br/>
		<label for="utm_source">Source</label><br/>
        <input style="width:100%" type="text" name="pwakey2[postmeta][utm_source]" 
			id="utm_source" title='Utm Source' 
             value='<?php self::EcStr($utm_source); ?>'>
		<br/>
		<label for="utm_medium">Medium</label><br/>
        <input style="width:100%" type="text" name="pwakey2[postmeta][utm_medium]" 
			id="utm_medium" title='Utm Medium' 
             value='<?php self::EcStr($utm_medium); ?>'>
		<br/>
		<label for="recip">Content</label><br/>
        <input style="width:100%" type="text" name="pwakey2[postmeta][utm_content]" 
			id="utm_content" title='Utm Content' 
             value='<?php self::EcStr($utm_content); ?>'>
		<br/>
		<label for="utm_term">Term</label><br/>
        <input style="width:100%" type="text" name="pwakey2[postmeta][utm_term]" 
			id="utm_term" title='Utm Term' 
             value='<?php self::EcStr($utm_term); ?>'>
		<br/>

		</form>
        <?php
		
		//fld must exist
		wp_nonce_field( 'appy_topic_metabox_save', 'subpwaplug');

		//wp_nonce_field( 'bbp_topic_metabox_save', 'bbp_topic_metabox' );
		//$nooHtml=wp_nonce_field( 'appy_topic_metabox_save', 'appy_topic_metabox',false );
		wp_nonce_field( 'appy_topic_metabox_save', 'appy_topic_metabox');

		//function wp_nonce_field( $action = -1, $name = '_wpnonce', $referer = true, $ec ho = true ) {


		//if (isset($_ POST['appy_topic_metabox']))
		//	$nonce=wp_verify_nonce( $_ POST['appy_topic_metabox'], 'appy_topic_metabox_save' );


		$noo=wp_create_nonce(-1);
		$nonce=wp_verify_nonce( $noo);
			
		$noo=wp_create_nonce('appy_topic_metabox_save');
		$nonce=wp_verify_nonce( $noo, 'appy_topic_metabox_save' );


	}


	/**
	 * ajax, 
	 * cross domain: appystore, no ChkSecurity
	 */
	static public function UpdateMetaField()
	{
		$strId=self::PostVal('m_strId',null);
		$strObjFunc=self::PostVal('strObjFunc',null);
		$wpuserid=self::PostVal('wpuserid',null);
		$arrFields=self::PostVal('arrFields',null);
		$arrWhere=self::PostVal('arrWhere',null);

		$strError='';
		$obj=array();
		$obj['ok']=1;
		$obj['valid']=true;

		global $wpdb;

		//$bValid=self::ChkSecurity($obj,$wpuserid);
		$bValid=true;
		if ($bValid)
		{
			//-exists or not
			$sqlWhere=Funcs::WherePostArgsSql($arrWhere);
			$row = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE $sqlWhere LIMIT 1");
			if (!$row)
			{
				//-may not exist
				$rows=$wpdb->insert( $wpdb->postmeta, 
					array('post_id'=>$arrWhere['post_id'],'meta_key'=>$arrWhere['meta_key'],
					'meta_value'=>$arrFields['meta_value']));
			}
			else
			{
				//-$rows=$wpdb->update( $wpdb->postmeta, array('post_parent' => $fnd->ID),array('post_id'=>$r->ID) );
				$rows=$wpdb->update( $wpdb->postmeta, $arrFields,$arrWhere);
			}
		}
		$obj['result']=$rows;

		$obj['m_strId']=$strId;
		$obj['strObjFunc']=$strObjFunc;
		$strJson=wp_json_encode($obj);

		self::EcJs($strJson);
	}

	/**
	 * add_action( 'wp_ajax_nopriv_mypwaappyplug_action
	 */
	static public function UpdateRecFieldItem()
	{
		//$strFunc = $_ POST['strFunc'];
		$curpostid=self::PostVal('curpostid',null);
		$wpuserid=self::PostVal('wpuserid',null);
		$recid=self::PostVal('recid',null);
		$fld=self::PostVal('fld',null);
		$valHex=self::PostVal('value',null);
		$value=hex2bin($valHex);

		$loadrecs=self::PostVal('loadrecs',null);


		$poTmp=get_post($recid);
		$ptype = $poTmp->post_type;

		$poTmp->$fld=$value;
		if ($fld=='post_title')
		{
			//-slug as well
			$poTmp->post_name=$value;
		}

		$success = wp_update_post($poTmp);

		//-ret
		$obj=array();
		$obj['recid']=$recid;

		$obj['posts']=array();
		if ($loadrecs=='true')
		{
			$posts=Funcs::LoadPTypeShopped($ptype,false,true);
			$obj['posts']=$posts;
		}

		$ok=$success?true:false;
		$obj['valid']=$ok==false?false:true;

		$strId=self::PostVal('m_strId',null);
		$strObjFunc=self::PostVal('strObjFunc',null);


		$obj['ok']=1;

		$obj['m_strId']=$strId;
		$obj['strObjFunc']=$strObjFunc;
		//-$obj['strFunc']=$_ POST['strFunc'];

		$strJson=wp_json_encode($obj);
		self::EcJs($strJson);
	}


	



	static public function my_login_redirect( $redirect_to, $request, $user ) 
	{    
		global $hook_suffix;
		global $typenow;
		global $pagenow;

		if ($request=='')
		{
			ServServPush::DelSubscriptionCur();
			//add_action( 'wp_enqueue_scripts', array(__CLASS__,'AdminLoginScripts') );
			self::AdminLoginScripts();
		}

		return $redirect_to; 
	}

	/**
	 * add_action( 'wp_enqueue_scripts'
	 */
	static public function AdminLoginScripts()
	{
		wp_enqueue_script('unsubs',PAGEDPWA_MYPWAAPPYPLUGAPP_URI . 'assets/js/unsubs.js', 
			array(), PAGEDPWA_MYPWAAPPYPLUGAPP_VERSION, true );
	}

	static public function GetMetaCSP()
	{
		//$hdr='<meta http-equiv="Content-Security-Policy" content="frame-src *;allow-file-access-from-files"/>';
		$hdr='<meta http-equiv="Content-Security-Policy" content="frame-src *"/>';
		//$hdr='<meta http-equiv="Content-Security-Policy" content="frame-src *;frame-ancestors \'self\'"/>';
		return $hdr;
	}
	
	static public function GetRunConfig(&$bAppFireJsLib,$bAppFireJsCompat,&$bAppMyStartup,
		&$bAppFireEs6,&$bMyFBLib)
	{

		//1.
		$bAppFireJsLib=true;
		//inc <script> FB libs CDN

		//2.
		//$bAppFireJsCompat=true;
		$bAppFireJsCompat=false;
		// compat: msg,app

		//3.
		$bAppMyStartup=true;
		//start webmainhm.js
		
		//4.
		//@3:02 am 5th Aug 2023
		//$bMyFBLib=false;
		$bMyFBLib=true;
		$bAppFireJsAPI=true;
		$bMyFBLib=!$bAppFireJsAPI;
		$bMyFBLib = Funcs::GetPushMsgSys()==1?false:true;

		//?action=mypwaappyplug_getservvars
		//sub option of bAppMyStartup

		//5.
		$bAppFireEs6=false;
		//js (non mod) v8.10.1 ok
		/////////OOOOOOOOOOOOOOOKKKKKKKKKKKKk
		//startup module: webgcm.js

		//6.........::
		//webmainhm.js has option
		//6b..........::
		//C: \xampp\htdocs\wp\wp - content\plugins\pwaappy\assets\js\myfirebase.js
		//N.b also myfirebase.js hase options

	}



	

	
	/**
	 * add_action( 'wp_ajax_nopriv_appyplug_action
	 */
	static public function StorePageCtl()
	{
		//$data = $_ POST['data'];
		//$fname = $_ POST['fname'];
		//
		$curpostid=self::PostVal('curpostid',null);
		$postid=self::PostVal('postid',null);
		$postid=self::PostVal('mypostid',null);
		$arrData=self::PostVal('arrData',null);

		$bOk=true;
		$obj['ok']=1;
		$obj['valid']=$bOk;

		//$pa=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."assets/js/$fname";
		//$pa=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."assets/js/swpagenoti/$fname.js";
		$paPI=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH;
		foreach ($arrData as $o)
		{
			$data=$o['data'];
			$pa=$paPI.$o['subdir'];
			$pa=trailingslashit($pa);
			$pa.=$o['fname'];

			if ($data)
				file_put_contents($pa,$data);
			else
				wp_delete_file($pa);
		}

		//
		$strId=self::PostVal('m_strId',null);
		$strObjFunc=self::PostVal('strObjFunc',null);

		$obj['m_strId']=$strId;
		$obj['strObjFunc']=$strObjFunc;
		$obj['map']=$map;

		$strJson=wp_json_encode($obj);
		self::EcJs($strJson);
	}	
	
	/**
	 * return conbined contents str
	 */
	static public function GetPageNoti($dir) 
	{
		$baseurl=trailingslashit($dir);
		$path=$baseurl."*.js";

		$str='';
		$it = new \DirectoryIterator("glob://".$path);	
	
		$arr=array();
		foreach($it as $f) 
		{
			if ($it->isDot()) 
				continue;
	
			$realname=$f->getFilename();
			$nDot=strrpos($realname,'.');
			if (!$nDot)
				continue;

			$realname=substr($realname,0,$nDot);
	
			$dirPath=$f->getPathName();	
			$data=file_get_contents($dirPath);
			$data=stripslashes($data);
			$data="if (pg=='$realname')\r\n{{$data}}";
			$str.=$data;
		}
		return $str;
	}
	
	/**
	 * add_action( 'wp_ajax_nopriv_appyplug_action
	 */
	static public function SetPushMsgType()
	{
		$curpostid=self::PostVal('curpostid',null);
		$postid=self::PostVal('postid',null);
		$postid=self::PostVal('mypostid',null);
		$strSel=self::PostVal('nSel',null);
		
		//
		$bOk=true;
		$obj['ok']=1;
		$obj['valid']=$bOk;

		$nSel=intval($strSel);
		if ($nSel==0)
		{
			//FB
			$key=self::GetOption('GoogFBKey');
			self::UpdateOption('ServerKey',$key);
		}
		else if ($nSel==1)
		{
			//VAPID
			$key=self::GetOption('publicKey');
			self::UpdateOption('ServerKey',$key);
		}

		self::SetObjKeys($obj);

		//
		$strId=self::PostVal('m_strId',null);
		$strObjFunc=self::PostVal('strObjFunc',null);

		$obj['m_strId']=$strId;
		$obj['strObjFunc']=$strObjFunc;
		$obj['map']=$map;

		$strJson=wp_json_encode($obj);
		self::EcJs($strJson);
	}	
	
	static public function SetObjKeys(&$obj)
	{
		$k=self::GetOption('publicKey');
		//$kp=self::GetOption('privateKey');
		$obj['publicKey']=$k;
		//$obj['privateKey']=$kp;
		$kk=self::GetOption('ServerKey');
		$obj['ServerKey']=$kk;
		$kg=self::GetOption('GoogFBKey');
		$obj['GoogFBKey']=$kg;
	}

	public static function HandleParseUrl() 
	{
		$dirs=Funcs::GetDirsFromUrl();
		$here=implode('//',$dirs);
		$start_url=self::GetOption('start_url');
		$start_url=trailingslashit($start_url);
		$home=home_url();
		$here="$home/$here";
		if ($here!=$start_url)
			return;

		$campaign=self::GetVal('campaign',null);
		$source=self::GetVal('source',null);
		$medium=self::GetVal('medium',null);
		$content=self::GetVal('content',null);
		$term=self::GetVal('term',null);

		$meta=[
			'utm_campaign'=>$campaign,
			'utm_source'=>$source,
			'utm_medium'=>$medium,
			'utm_content'=>$content,
			'utm_term'=>$term,
		];
		self::CreatePTypeUTM($meta);
		//wp_die();
	}

	static public function CreatePTypeUTM($meta)
	{
		$usr=wp_get_current_user();
		$usrid=$usr->ID;

		/**
		 * $rec['post_date']='';
		 * $rec['post_date_gmt']='';
		 */
		///////$rec['post_content']=$strIds;
		/**
		 * $rec['post_content_filtrerd']='';
		 */
		$rec['post_title']='UTM Tracking Data';
		
		/**
		 * $rec['ping_status']=$prodcode;
		 * $rec['post_excerpt']=$txcode;
		 * $rec['post_status']='publish';
		 * $rec['post_status']='private';
		 */
		$rec['post_status']='publish';
		/**
		 * $rec['post_type']='page';
		 */
		$rec['post_type']='utmtrackptype';

		//$rec['comment_status']='';
		/**
		 * $rec['ping_status']='';
		 * $rec['post_password']='';
		 * $rec['post_name']='';
		 * $rec['to_ping']='';
		 * $rec['pinged']='';
		 */
		//$rec['post_modified']='';
		//$rec['post_modified_gmt']='';

		$newid=Funcs::UpdateMyPage(0,$usrid,$rec);
		self::DoUpdateUTMDBMeta($newid,$meta);
        
        return $newid;
	}

	static public function DoUpdateUTMDBMeta($newid,$oVal)
	{
		foreach ($oVal as $k=>$v)
		{
			$um=update_post_meta($newid,$k, $v);
		}
	}

	/*
	//apply_filters('SmartPostTypes_CallFromMetabox',$ref);
	public static function CallFromMetabox($ref) 
	{
				// $ref=array(
				// 	'ret'=>&$ret,
				// 	'tablevars'=>&$alltablevars,
				// 	'varsb'=>&$allvarsb,
				// 	'box'=>$bx,
				// 	'func'=>$bx['servtag'],
				// 	'pobj'=>$this->m_pobj,
				// 	'po'=>$po);
		$tv=$ref['tablevars'];
		$meta=$tv['postmeta'];
	}
	*/

	static public function PostVal($v0,$def)
	{
		$v=isset($_POST[$v0])?$_POST[$v0]:$def;
		//@@bugsmy14thMay24
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
		return sanitize_textarea_field($v);
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
		return sanitize_textarea_field($v);
	}

	static public function ReqVal($v,$def)
	{
		$v=isset($_REQUEST[$v])?$_REQUEST[$v]:$def;
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
		return sanitize_textarea_field($v);
	}

	static public function EcStr($str0)
	{
		$str=esc_html($str0);
		echo $str;
		//@@bugsmy14thMay24
	}

	static public function EcJs($js,$bPretty=false)
	{
		return Funcs::EcJs($js,$bPretty);
	}
	
	static public function EcOut($str)
	{
		self::EcAsc($str);		
	}
	
	static public function EcHtm($str)
	{
		return Funcs::EcHtm($str);
	}

	static public function MyKss($str,$arr)
	{
		$map=[];
		foreach ($arr as $tag)
		{
			$map[$tag]=1;
		}
 		echo wp_kses($str, $map);
	}

	static public function EcScr($str)
	{
		return Funcs::EcScr($str);
	}

	static public function EcInp($str)
	{
		return Funcs::EcInp($str);
	}

	static public function EcIFr($str)
	{
		return Funcs::EcIFr($str);
	}

	static public function EcTag($str,$tags)
	{
		return Funcs::EcTag($str,$tags);
	}

	static public function EcTok($str)
	{
		return Funcs::EcTok($str);
	}
	
	static public function EcAsc($str)
	{
		Funcs::EcAsc($str);
	}

	static private function UpdateOption($nm,$val)
    {
        $nm="pagedpwa_$nm";
        return update_option($nm,$val);
    }

    static private function GetOption($nm)
    {
        $nm="pagedpwa_$nm";
        return get_option($nm);
    }

	static public function PwaForWpJsEscape($str,$str2)
	{    
		return $str2;
	}
}

MyPWAAppyPlug::RegisterHooks();
