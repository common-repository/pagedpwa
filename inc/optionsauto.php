<?php
namespace SmartIntelPWAAppy;

class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );

        $ns=array($this,'options_page_scripts');
        add_action( 'admin_enqueue_scripts', $ns );
        //add_action( 'wp_enqueue_scripts', $ns );

		//add_action( 'wp_admin_head', array( $this, 'AddHdrScripts' ) );
		add_action( 'admin_head', array( $this, 'AddHdrScripts' ) );

    }

    //grp - Settings
    //smart_designer_manifest_pg
    public function register_pagesSet($par) 
    {
        $nm="nmAdminMens";
        $title="titAdminMens";
        $caps='manage_options';

        //#1
        $name="PWA Manifest Settings";
        $cb=array( $this,'AdminSubPageSetManifestPg');
        $pgnm='smart_designer_manifest_pg';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);
        //#2
        $name="Offline Cache Policy & Keys";
        $cb=array( $this,'AdminSubPageSetPolicyPg');
        $pgnm='smart_designer_policy_pg';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //#3
        /*$name="Smart Designer Setting: abc3";
        $cb=array( $this,'AdminSubPageSet3PgBut');
        $pgnm='smart_designer_settings_pg3';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);
        */
	}

    public function AdminSubPageSetManifestPg($k)
    {
        $this->ShowPageManifest();
    }

    public function AdminSubPageSetPolicyPg($k)
    {
        $this->ShowPagePolicy();
    }

    public function AdminSubPageSet2PgBut($key)
    {
        $prefix='';
        $debug=false;
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcHtm("<h3>AdminSubPageSet2PgBut</h3>");

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                Funcs::EcOut("home:$home,ref:$ref,scriptby:$scriptby");

                ?>
            </div>
        </div>
        <?php
    }

    public function AdminSubPageSet3PgBut($key)
    {
        $prefix='';
        $debug=false;
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcHtm("<h3>AdminSubPageSet3PgBut</h3>");

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                Funcs::EcOut("home:$home,ref:$ref,scriptby:$scriptby");

                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Add options page
     */
    /**
     * add_action( 'admin_menu'
     */
    public function add_plugin_page()
    {
        $caps='manage_options';

        //Settings
        $title="Paged PWA Settings";
        $par="smart_designer_manifest_pg";
        
        $icon=PAGEDPWA_MYPWAAPPYPLUGAPP_URI."assets/pwa-menu-icon.png";
        $nm="nmAdminSet1";

        $cb=null;
        add_menu_page($nm,$title,$caps,$par,$cb,$icon,90);
        $this->register_pagesSet($par);



        //grp1
        $title="Paged PWA Run";
        //$par='paged-pwa';
        //menu grp & first pg
        //$par="smart_designer_run_send_push_form";
        $par="smart_designer_run_button";
        
        //$icon=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."assets/pwa-menu-icon.png";
        $icon=PAGEDPWA_MYPWAAPPYPLUGAPP_URI."assets/pwa-menu-icon.png";
        $nm="nmAdminMens";
        $aboutKey='keyAbout';

        //$cb=array( $this, 'AdminSubPageAbout' );
        $cb=null;
        add_menu_page($nm,$title,$caps,$par,$cb,$icon,100);

        //add_submenu_page($par, __('About', 'my progressive-wp'), __('About', 'my progressive-wp'),
        //    $caps,$aboutKey,$cb);

        $this->register_pages1($par);


        //'Settings' page
        /*$menuis='Smart Paged PWA Manifest Options';
        $menunameid=self::GetMenuNameId();
        // This page will be under "Settings"
        add_options_page(
            'Settings Smart Paged PWA', 
            $menuis, 
            'manage_options', 
            $menunameid, 
            array( $this, 'AdminPageInvoked' )
        );*/

        //grp 2
        $title="Paged PWA Design";
        //$par2='paged-pwa2';
        //$par2='smart_designer_send_push_form';
        $par2='smart_designer_button';
        
        //first pg
        $icon=PAGEDPWA_MYPWAAPPYPLUGAPP_URI."assets/pwa-menu-icon.png";
        $nm="nmAdminMens2";
        $aboutKey='keyAbout';
        //$cb=array($this,'AdminSubPageAbout');
        $cb=null;
        add_menu_page($nm,$title,$caps,$par2,$cb,$icon,101);

        $this->register_pages2($par2);

        //grp 3
        $title="Paged PWA Skin";
        //$par2='paged-pwa2';
        //$par3='Smart_Designer_skin_send_push';
        $par3='smart_designer_skin_button';
        
        //first pg
        $icon=PAGEDPWA_MYPWAAPPYPLUGAPP_URI."assets/pwa-menu-icon.png";
        $nm="nmAdminMens3";
        $aboutKey='keyAbout2';
        //$cb=array($this,'AdminSubPageAbout');
        $cb=null;
        add_menu_page($nm,$title,$caps,$par3,$cb,$icon,102);

        $this->register_pages3($par3);

        //
        //grp 0
        $title="Paged PWA All Pages";
        $par0='smart_designer_runappdataposts';
        
        //first pg
        $icon=PAGEDPWA_MYPWAAPPYPLUGAPP_URI."assets/pwa-menu-icon.png";
        $nm="nmAdminMens0";
        $nm=$title;
        //$cb=array($this,'AdminSubPageAbout');
        $cb=null;
        add_menu_page($nm,$title,$caps,$par0,$cb,$icon,109);

        $this->register_pages0($par0);

    }

    /**
     * Options page callback
     */
    /**
     * menu item invoked
     */
    //array( $this, 'AdminPageInvoked' )
    public function AdminPageInvoked()
    {
        // Set class property
        $this->options = self::GetOption( 'my_option_name' );
        $this->ShowPageManifest();
    }

    private function AddImg(&$i,$strKey,$strImg)
    {
        $uploads = wp_upload_dir();
        $base=$uploads['basedir'];
        $baseurl=$uploads['baseurl'];

        $r=array();
        $r['src']="{$baseurl}/$strImg";
        $r['sizes']="{$strKey}x{$strKey}";
        $r['type']="image/png";
        $i[]=$r;
    }

    private function RenderOptions($arr,$displayas)
    {
        //option selected='selected' 
        if (!$arr)
            return "";

        $strHtm='';
        foreach ($arr as $val)
        {
            $sel="";
            if ($val==$displayas)
                $sel="selected='selected'";
            $strHtm.="<option $sel>$val</option>";
        }
        return $strHtm;
    }

    private function RenderOptionsVal($map,$displayas)
    {
        //option selected='selected' 
        if (!$map)
            return "";

        $strHtm='';
        foreach ($map as $k=>$val)
        {
            $sel="";
            if ($k==$displayas)
                $sel="selected='selected'";
            $strHtm.="<option $sel value='$k'>$val</option>";
        }
        return $strHtm;
    }

    //get/post page 1
    private function ShowPageManifest()
    {
        //$htm="<div>MY PPAGE HEEE</div";
        //ec ho $htm;

        //global $wp_embed;
        //$content = $wp_embed->run_shortcode( $content );
        //$content = do_shortcode( $content );

        //$pageid=875;
        /**
         * $strOptKey='testoptions';
         * $oData=get_ option($strOptKey);
         */

        //$pageid=795;
        
        //$htm=do_shortcode("[smartdesigned id='$pageid']");
        //ec ho $htm;
        
        //apply_filters('Smart Design_DoSimpleForm',$pageid,null);
        /**
         * apply_filters('Smart Design_DoAjaxPage',$pageid,$strOptKey,$oData);
         */
        //apply_filters('Smart Design_DoAjaxPage',$pageid,null);


        //ec ho "<br>abc turkey bob";

        $meth=strtolower(self::GetServVar('REQUEST_METHOD'));
        //@@bugsmy14thMay24

        if ($meth=='post')
        {
            $img128=Funcs::PostUrl('img128',null);  //---orgfixnon
            $img144=Funcs::PostUrl('img144',null);
            $img152=Funcs::PostUrl('img152',null);
            $img192=Funcs::PostUrl('img192',null);
            $img256=Funcs::PostUrl('img256',null);
            $img512=Funcs::PostUrl('img512',null);
            $imgMono=Funcs::PostUrl('imgMono',null);
            $imgmaskable=Funcs::PostUrl('imgmaskable',null);
            $imgmaskable512=Funcs::PostUrl('imgmaskable512',null);
            $imgScr512=Funcs::PostUrl('imgScr512',null);
            $bigname=Funcs::PostStr('bigname',null);
            $appdescript=Funcs::PostStr('appdescript',null);
            $shortname=Funcs::PostStr('shortname',null);
            $start_url=Funcs::PostStr('start_url',null);
            $shortpage_url=Funcs::PostStr('shortpage_url',null);
            $displayas=Funcs::PostStr('displayas',null);
            $bgcolor=Funcs::PostStr('bgcolor',null);
            $themecolor=Funcs::PostStr('themecolor',null);
            $lickey=Funcs::PostStr('lickey',null);
            $diras=Funcs::PostStr('diras',null);
            $orientationas=Funcs::PostStr('orientationas',null);
            $UTMcampaign=Funcs::PostStr('UTMcampaign',null);
            $UTMsource=Funcs::PostStr('UTMsource',null);
            $UTMmedium=Funcs::PostStr('UTMmedium',null);
            $UTMcontent=Funcs::PostStr('UTMcontent',null);
            $UTMterm=Funcs::PostStr('UTMterm',null);
    	    $start_url=trailingslashit($start_url);
            $BadgeShortcut=Funcs::PostStr('BadgeShortcut',null);
            $extramanifest=Funcs::PostStr('extramanifest',null,true);
            $extramanifest=stripslashes($extramanifest);

            self::UpdateOption('img128',$img128);
			self::UpdateOption('img144',$img144);
			self::UpdateOption('img152',$img152);
			self::UpdateOption('img192',$img192);
			self::UpdateOption('img256',$img256);
			self::UpdateOption('img512',$img512);
			self::UpdateOption('imgMono',$imgMono);
			self::UpdateOption('imgmaskable',$imgmaskable);
			self::UpdateOption('imgmaskable512',$imgmaskable512);
			self::UpdateOption('imgScr512',$imgScr512);

			self::UpdateOption('bigname',$bigname);
			self::UpdateOption('appdescript',$appdescript);            
			self::UpdateOption('shortname',$shortname);
			self::UpdateOption('start_url',$start_url);
			self::UpdateOption('shortpage_url',$shortpage_url);
            
			self::UpdateOption('displayas',$displayas);
			self::UpdateOption('bgcolor',$bgcolor);
			self::UpdateOption('themecolor',$themecolor);
			self::UpdateOption('diras',$diras);
			self::UpdateOption('orientationas',$orientationas);

			//update_ option('lickey',$lickey);
			self::UpdateOption('UTMcampaign',$UTMcampaign);
			self::UpdateOption('UTMsource',$UTMsource);
			self::UpdateOption('UTMmedium',$UTMmedium);
			self::UpdateOption('UTMcontent',$UTMcontent);
			self::UpdateOption('UTMterm',$UTMterm);
			self::UpdateOption('BadgeShortcut',$BadgeShortcut);
	    	self::UpdateOption('extramanifest',$extramanifest);

            //ec ho "<script type='text/javascript'>AdminMani.FormPosted();</script>";
            Funcs::EcInp("<input type='hidden' id='idPagePosted' value='$bigname'/>");
            //InitServedCust

                ?>        
                <hr/>
                <hr/>
                <div style='background-color: #becbd9;'>
                <br/>
                <div>Page has been submitted OK</div>
                </div>
                <hr/>
                <hr/>
                <?php                

            /*
            $i=array();
            self::AddImg($i,'128',$img128);
            self::AddImg($i,'144',$img144);
            self::AddImg($i,'152',$img152);
            self::AddImg($i,'192',$img192);
            self::AddImg($i,'256',$img256);
            self::AddImg($i,'512',$img512);

            $m=array();
            $m['name']=$bigname;
            $m['short_name']=$shortname;
            $m['icons']=$i;
            $m['lang']='en-US';
            $m['start_url']=$start_url;
            $m['display']=$displayas;
            $m['background_color']=$bgcolor;
            $m['theme_color']=$themecolor;

            $json=json_encode($m,JSON_PRETTY_PRINT);
            //$ld=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."man.test.json.txt";
            $ld=PAGEDPWA_MYPWAAPPYPLUGAPP_PATH."man.test.json";
            file_put_contents($ld,$json);
            */
        }
        else
        {
            $img128=self::GetOption('img128');
            $img144=self::GetOption('img144');
            $img152=self::GetOption('img152');
            $img192=self::GetOption('img192');
            $img256=self::GetOption('img256');
            $img512=self::GetOption('img512');
            $imgMono=self::GetOption('imgMono');
            $imgmaskable=self::GetOption('imgmaskable');
            $imgmaskable512=self::GetOption('imgmaskable512');
            $imgScr512=self::GetOption('imgScr512');            
            
            $bigname=self::GetOption('bigname');
            $appdescript=self::GetOption('appdescript');            
            $shortname=self::GetOption('shortname');
            $start_url=self::GetOption('start_url');
            $shortpage_url=self::GetOption('shortpage_url');
            
            $displayas=self::GetOption('displayas');
            $bgcolor=self::GetOption('bgcolor');
            $themecolor=self::GetOption('themecolor');
            $diras=self::GetOption('diras');
            $orientationas=self::GetOption('orientationas');
            

            //$lickey=self::GetOption('lickey');
            $UTMcampaign=self::GetOption('UTMcampaign');
            $UTMsource=self::GetOption('UTMsource');
            $UTMmedium=self::GetOption('UTMmedium');
            $UTMcontent=self::GetOption('UTMcontent');
            $UTMterm=self::GetOption('UTMterm');
            $BadgeShortcut=self::GetOption('BadgeShortcut');
            $extramanifest=self::GetOption('extramanifest');
        }

        $homey=get_home_url();
        $bBadgeShortcut=$BadgeShortcut=='on'?true:false;
        $strBadgeShortcut='';
        if ($bBadgeShortcut)
            $strBadgeShortcut="checked='true' ";

        $home=get_home_url();
        $maniUrl="$home/wp-admin/admin-ajax.php?action=mypwaappyplug_manifest";
        ?>

        <div>

            <form action="" method="post">

            <br/>
            <input type="button" id='idDoMediaLib' value="Select or Upload Icon Image ">
            <br/>

            <div style='padding-left:8em;'>

            <br/>
            <label for="img128">Image 128</label>
            <input style='width:500px;' type="text" name="img128" id="img128" 
                title='Please specify img128' value='<?php Funcs::EcStr($img128); ?>'>

            <br/>
            <label for="img144">Image 144</label>
            <input style='width:500px;' type="text" name="img144" id="img144" 
                title='Please specify img144' value='<?php Funcs::EcStr($img144); ?>'>
            <br/>
            <label for="img152">Image 152</label>
            <input style='width:500px;' type="text" name="img152" id="img152" 
                title='Please specify img152' value='<?php Funcs::EcStr($img152); ?>'>
            <br/>
            <label for="img192">Image 192</label>
            <input style='width:500px;' type="text" name="img192" id="img192" 
                title='Please specify img192' value='<?php Funcs::EcStr($img192); ?>'>
            <br/>
            <label for="img256">Image 256</label>
            <input style='width:500px;' type="text" name="img256" id="img256" 
                title='Please specify img256' value='<?php Funcs::EcStr($img256); ?>'>
            <br/>
            <label for="img512">Image 512</label>
            <input style='width:500px;' type="text" name="img512" id="img512" 
                title='Please specify img512' value='<?php Funcs::EcStr($img512); ?>'>

            <br/>
            </div>

            <br/>
            <div style='background-color:#d9d3d3'>Maskable, mono & Screenshot images (optional)</div>
            <div style='padding-left:1em;'>

            <label style='display:inline-block;width:200px;' for="imgMono">Image Monochrome (512x512)</label>
            <input style='width:500px;' type="text" name="imgMono" id="imgMono" 
                title='Please specify a mono image' value='<?php Funcs::EcStr($imgMono); ?>'>
            <input type="button" id='idDoMediaLibMono' value="Select or Upload a Monochrome Image ">
            <br/>
            <label style='display:inline-block;width:200px;' for="imgmaskable">Image Maskable (192x192)</label>
            <input style='width:500px;' type="text" name="imgmaskable" id="imgmaskable" 
                title='Please specify a maskable image' value='<?php Funcs::EcStr($imgmaskable); ?>'>
            <input type="button" id='idDoMediaLibMask' value="Select or Upload a Mask Image ">
            <br/>
            <label style='display:inline-block;width:200px;' for="imgmaskable512">Image Maskable (512x512)</label>
            <input style='width:500px;' type="text" name="imgmaskable512" id="imgmaskable512" 
                title='Please specify a maskable image' value='<?php Funcs::EcStr($imgmaskable512); ?>'>
            <input type="button" id='idDoMediaLibMask512' value="Select or Upload a Mask Image ">
            <br/>
            <label style='display:inline-block;width:200px;' for="imgScr512">Image Screenshot (512x512)</label>
            <input style='width:500px;' type="text" name="imgScr512" id="imgScr512" 
                title='Please specify a screen shot image' value='<?php Funcs::EcStr($imgScr512); ?>'>
            <input type="button" id='idDoMediaLibScr512' value="Select or Upload a Screenshot Image ">

            </div>

            <br/>
            <div style='background-color:#d9d3d3'>Main Settings</div>
            <div style='padding-left:1em;'>
            
            <label style='display:inline-block;width:200px;' for="bigname">Big Name</label>
            <input style='width:20em;' type="text" name="bigname" id="bigname" title='Please specify bigname' 
                value='<?php Funcs::EcStr($bigname); ?>'>
            <br/>
            <label style='display:inline-block;width:200px;' for="appdescript">Description</label>
            <input style='width:20em;' type="text" name="appdescript" id="appdescript" 
                title='Please specify a description' value='<?php Funcs::EcStr($appdescript); ?>'>
            <br/>
            <label style='display:inline-block;width:200px;' for="shortname">Short Name</label>
            <input style='width:16em;' type="text" name="shortname" id="shortname" title='Please specify shortname' 
                value='<?php Funcs::EcStr($shortname); ?>'>

            <br/>
            <label style='vertical-align:top;display:inline-block;width:200px;' 
                for="start_url">Startup Url
            </label>
            <span style='display:inline-block;width:500px;' >
            <input style="width:100%" type="text" name="start_url" id="start_url" 
                title='Please specify the start url (eg <?php Funcs::EcStr($home); ?>)' 
                value='<?php Funcs::EcStr($start_url); ?>'><br>
             (eg <code><?php Funcs::EcStr($homey); ?></code>)
            </span>
            <br/>

            <br/>
            <label style='display:inline-block;width:200px;' for="displayas">Display as</label>
            <select name="displayas" id="displayas" title='Please specify display'>
            <?php Funcs::EcHtm(self::RenderOptions(array('standalone','fullscreen','minimal-ui'),$displayas)); ?>
            </select>

            <br/>
            <label style='display:inline-block;width:200px;' for="bgcolor">Background Colour</label>
            <input type="color" name="bgcolor" id="bgcolor" title='Please specify bgcolor' 
                value='<?php Funcs::EcStr($bgcolor); ?>'>
            <br/>
            <label style='display:inline-block;width:200px;' for="themecolor">Theme Colour</label>
            <input type="color" name="themecolor" id="themecolor" title='Please specify themecolor' 
                value='<?php Funcs::EcStr($themecolor); ?>'>

            <br/>
            <label style='display:inline-block;width:200px;' for="diras">Direction</label>
            <select name="diras" id="diras" title='Please specify display'>
            <?php Funcs::EcHtm(self::RenderOptions(['ltr','rtl'],$diras)); ?>
            </select>

            <br/>
            <label style='display:inline-block;width:200px;' for="orientationas">Orientation</label>
            <select name="orientationas" id="orientationas" title='Please specify display'>
            <?php Funcs::EcHtm(self::RenderOptions(['portrait','landscape','any'],$orientationas)); ?>
            </select>


            <br/>
            <br/>
            <label style='display:inline-block;width:200px;' for="themecolor">UTM tracking</label>
            <div style='padding-left:10em;'>
            Campaign: <input type="text" name="UTMcampaign" id="UTMcampaign" title='Please specify the campaign' 
                value='<?php Funcs::EcStr($UTMcampaign); ?>'>
            Source: <input type="text" name="UTMsource" id="UTMsource" title='Please specify the source' 
                value='<?php Funcs::EcStr($UTMsource); ?>'>
            Medium: <input type="text" name="UTMmedium" id="UTMmedium" title='Please specify the medium' 
                value='<?php Funcs::EcStr($UTMmedium); ?>'><br>
            Content: <input type="text" name="UTMcontent" id="UTMcontent" title='Please specify the content' 
                value='<?php Funcs::EcStr($UTMcontent); ?>'>
            Term: <input type="text" name="UTMterm" id="UTMterm" title='Please specify the term' 
                value='<?php Funcs::EcStr($UTMterm); ?>'><br>
            </div>

            <br/>
            <label style='vertical-align:top;display:inline-block;width:200px;' 
            for="BadgeShortcut">Allow Badge Shortcut</label>
            <span style='display:inline-block;width:500px;' >
            <input type="checkbox" name="BadgeShortcut" id="BadgeShortcut" 
                <?php Funcs::EcStr($strBadgeShortcut)?> title='Badge Shortcut' >

            <input style="width:100%" type="text" name="shortpage_url" id="shortpage_url" 
                title='Please specify the start url (eg <?php Funcs::EcStr($home); ?>)' 
                value='<?php Funcs::EcStr($shortpage_url); ?>'><br>
                The Page to display for the shortcut (eg <code><?php Funcs::EcStr($homey); ?></code>)

            </span>
            <br/>


            <br/>
            <br/>
            <label style='vertical-align:top;display:inline-block;width:190px;' 
            for="forcache"><br>Extra manifest settings:<br> 
                <input type='button' id='idServExtraMani' onclick='HandleExtraMani(this);' 
                    value='Paste Example Here'/>
                    <br><br><br>
                    <a target='_blank' href='<?php Funcs::EcStr($maniUrl); ?>'>Check Manifest here:</a>
            </label>
            <textarea style='height:8em;width:50em;' name="extramanifest" id="extramanifest" 
                title='Set extra manifest settings here inc http handlers' >
                    <?php Funcs::EcJs($extramanifest); ?></textarea>
            <br/>
            <div><span style='vertical-align:top;display:inline-block;width:190px;' ></span>
            <span style='vertical-align:top;display:inline-block;'>
            Additional properties can be set here (for example 'scope')
            <br></span></div>            


            </div>


            <br/>
            <br/>

            <input type="submit" name="submitthis" value="Submit Form">

            </form>
        </div>


        <?php


    }

    //get/post page 2
    private function ShowPagePolicy()
    {
        $meth=strtolower(self::GetServVar('REQUEST_METHOD'));
        //@@bugsmy14thMay24
        $lickey='';

        if ($meth=='post')
        {
            $ServerKey=Funcs::PostStr('ServerKey',null);
            $FilesCache=Funcs::PostStr('FilesCache',null,true);
            $offlinestrategy=Funcs::PostStr('offlinestrategy',null);
            $periodictag=Funcs::PostStr('periodictag',null);
            $EmailKey=Funcs::PostStr('EmailKey',null);
            $EmailUse=Funcs::PostStr('EmailUse',null);
            $FilesNotCache=Funcs::PostStr('FilesNotCache',null,true);
            $ServAuthJsn=Funcs::PostStr('ServAuthJsn',null,true);

            //$SenderID=isset($_ POST['SenderID'])?$_ POST['SenderID']:null;
            //$lickey=isset($_ POST['lickey'])?$_ POST['lickey']:null;
            $ServAuthJsn=stripslashes($ServAuthJsn);

            $SubscribeAuto=Funcs::PostStr('SubscribeAuto',null);
            $SubscribeBut=Funcs::PostStr('SubscribeBut',null);
            $SwVersion=Funcs::PostStr('SwVersion',null);
            $SubscribeAnon=Funcs::PostStr('SubscribeAnon',null);
            //SubscribeBut

            $bValid=true;
            if (!$EmailKey || !$EmailUse)
            {
                $bValid=false;

                ?>        
                <hr/>
                <hr/>
                <div style='background-color: #e2c22161;'>Error:
                <br/>
                <div>Please specify your email and check the box next to it</div>
                <div>(This is required for security reasons)</div>
                </div>
                <?php
            }

            if ($bValid)
            {
			    $pubkey=self::GetOption('publicKey');
                if ($ServerKey && $ServerKey!=$pubkey)
                {
        			self::UpdateOption('ServerKey',$ServerKey);
	        		self::UpdateOption('GoogFBKey',$ServerKey);
                }

    			//self::UpdateOption('SenderID',$SenderID);
	    		//self::UpdateOption('lickey',$lickey);
    			self::UpdateOption('FilesCache',$FilesCache);
	    		self::UpdateOption('offlinestrategy',$offlinestrategy);
		    	self::UpdateOption('periodictag',$periodictag);            
			    self::UpdateOption('EmailKey',$EmailKey);
			    self::UpdateOption('EmailUse',$EmailUse);                
    			self::UpdateOption('FilesNotCache',$FilesNotCache);

			    self::UpdateOption('ServAuthJsn',$ServAuthJsn);
    			self::UpdateOption('SubscribeAuto',$SubscribeAuto);
	    		self::UpdateOption('SubscribeBut',$SubscribeBut);
	    		self::UpdateOption('SwVersion',$SwVersion);
	    		self::UpdateOption('SubscribeAnon',$SubscribeAnon);

                //ec ho "<script type='text/javascript'>AdminMani.FormPosted();</script>";
                ?>        
                <hr/>
                <hr/>
                <div style='background-color: #becbd9;'>
                <br/>
                <div>Page has been submitted OK</div>
                </div>
                <hr/>
                <hr/>
                <?php

            }
            Funcs::EcInp("<input type='hidden' id='idPagePosted_Email' value='$EmailKey'/>");
            Funcs::EcInp("<input type='hidden' id='idPagePosted_EmailUse' value='$EmailUse'/>");
            Funcs::EcInp("<input type='hidden' id='idPagePosted_ServerKey' value='$ServerKey'/>");
            //InitServedCust
        }
        else
        {
            $ServerKey=self::GetOption('ServerKey');
            //$SenderID=self::GetOption('SenderID');
            //$lickey=self::GetOption('lickey');

            //$me = get_current_user_id();

            $FilesCache=self::GetOption('FilesCache');
            $offlinestrategy=self::GetOption('offlinestrategy');
            $periodictag=self::GetOption('periodictag');            
            $EmailKey=self::GetOption('EmailKey');
            $EmailUse=self::GetOption('EmailUse');            
            $ServAuthJsn=self::GetOption('ServAuthJsn');
            $FilesNotCache=self::GetOption('FilesNotCache');
            $SubscribeAuto=self::GetOption('SubscribeAuto');
            $SubscribeBut=self::GetOption('SubscribeBut');
            $SwVersion=self::GetOption('SwVersion');
            $SubscribeAnon=self::GetOption('SubscribeAnon');            
        }

        $home=get_home_url();
        //$urlSignup="$home/pwaappypro";
        //$urlSignup="$home/pwaappypro";


        $reset="$home/noservwork/?command=resetsw";
        $vwcache="$home/swcache/?command=listcache";

        $privacy="https://scriptbyyou.com/myprivacy";
        

        $bSubscribeAuto=$SubscribeAuto=='on'?true:false;
        $bSubscribeBut=$SubscribeBut=='on'?true:false;
        $bSubscribeAnon=$SubscribeAnon=='on'?true:false;

        $strCheckedAuto='';
        if ($bSubscribeAuto)
            $strCheckedAuto="checked='true' ";
        $strCheckedBut='';
        if ($bSubscribeBut)
            $strCheckedBut="checked='true' ";
        $strSubscribeAnon='';
        if ($bSubscribeAnon)
            $strSubscribeAnon="checked='true' ";
        ?>


        <div>

            <form action="" method="post">

            <br/>

            <div style='padding-left:8em;'>
            </div>

            <br/>
            <label style='word-wrap:break-word;vertical-align:top;display:inline-block;width:190px;' 
            for="foremail">Your Email: (Needed for security)
            </label>
            <span style='width:50em;'>
            <input type="text" name="EmailKey" id="EmailKey" 
                title='Email' 
                value='<?php Funcs::EcStr($EmailKey); ?>'>
            <span>&nbsp;&nbsp;&nbsp;Allow this for security&nbsp;&nbsp;</span>
            <input type="checkbox" name="EmailUse" id="EmailUse" 
                title='Allow this for security' >
            </span>

            <br/>
            <div><span style='vertical-align:top;display:inline-block;width:190px;' ></span>
            <span style='vertical-align:top;display:inline-block;'>
            <a href='<?php Funcs::EcHtm($privacy); ?>'>Privacy here</a>.
            But at a glance: data only collected when needed, No data passed to 3rd parties,
            Software as a service (Saas) 
            (see readme.txt for this)</span></div>
            <br/>

            <br/>
            <label style='vertical-align:top;display:inline-block;width:190px;' 
            for="forcache"><br>Files to Cache:<br>(1 url per line starting with 
                <?php Funcs::EcStr($home); ?>)                
            </label>
            <textarea style='height:8em;width:50em;' name="FilesCache" id="FilesCache" 
                title='Files to Cache' ><?php Funcs::EcOut($FilesCache); ?></textarea>
            <br/>
            <div><span style='vertical-align:top;display:inline-block;width:190px;' ></span>
            <span style='vertical-align:top;display:inline-block;'>You can use the following URL:
            <code><?php Funcs::EcOut($vwcache); ?></code> to grab the URLs you wish to paste here.
            <br>For example if you use 'Stale While Revalidate' offline strategy</span></div>

            <br/>
            <label style='vertical-align:top;display:inline-block;width:190px;' 
            for="forcache"><br>Files NOT to Cache:<br>(1 url per line starting with 
                <?php Funcs::EcOut($home); ?>)                
            </label>
            <textarea style='height:8em;width:50em;' name="FilesNotCache" id="FilesNotCache" 
                title='Files to Cache' ><?php Funcs::EcOut($FilesNotCache); ?></textarea>
            <br/>

            <br/>
            <label style='display:inline-block;width:190px;' for="offlinestrategy">
                Offline strategy:<br>                
            </label>
            <select name="offlinestrategy" id="offlinestrategy" title='Please specify offline policy'>
            <?php Funcs::EcOption(self::RenderOptionsVal(
                array(
                    'staleWhileRevalidate'=>'Stale While Revalidate',
                    'NetworkFirst'=>'Network First',
                    'CacheFirst'=>'Cache First',
                    'NetworkOnly'=>'Network Only',
                    'CacheOnly'=>'Cache Only'),
                $offlinestrategy)); ?>
            </select>
            <br/>
            <div><span style='vertical-align:top;display:inline-block;width:190px;' ></span>
            <span style='vertical-align:top;display:inline-block;'>
            N.B. 'Cache Only' may mean pages cannot be accessed to give you the control you need.
            <br>In such cases you can remove the service worker with the URL: 
            <code><?php Funcs::EcOut($reset); ?></code>
            </span>
            </div>
            <br/>
                
            <?php
            self::ServKeys($ServerKey,$ServAuthJsn);
            ?>

            <br/>
            <label style='display:inline-block;width:190px;' 
            for="SubscribeAuto">Subscribe Automatically</label>
                <input type="checkbox" name="SubscribeAuto" id="SubscribeAuto" 
                <?php Funcs::EcStr($strCheckedAuto)?> title='Subscribe Automatically' >
            <br/>
            <br/>
            <label style='display:inline-block;width:190px;' 
                for="SubscribeBut">Show Button UI for Subscribe</label>
            <input type="checkbox" name="SubscribeBut" id="SubscribeBut" 
                <?php Funcs::EcStr($strCheckedBut)?> title='Show Subscribe Widget Control UI' >
            <span style='vertical-align:top;display:inline-block;width:20px;' ></span>
            <label 
                for="SubscribeAnon">Show Button UI when anonymous</label>
            <input type="checkbox" name="SubscribeAnon" id="SubscribeAnon" 
                <?php Funcs::EcStr($strSubscribeAnon)?> title='Show Subscribe when anonymous' >
            <br/>


            <br/>
            <label style='display:inline-block;width:190px;' 
                for="SwVersion">Service Worker Version</label>
            <span style='vertical-align:top;display:inline-block;'>
                <input type="text" name="SwVersion" id="SwVersion" 
                    value='<?php Funcs::EcStr($SwVersion); ?>'
                    title='Service Worker Version to ensure users have the latest' >
                <br/>
                Service Worker Version to ensure ALL users have the latest files
                <br>
                N.B. when changed ALL users will be forced to re-subscribe to push messaging 
            </span>

            <br/>
            <br/>
            <input type="submit" name="submitpolicy" value="Submit Form">

            </form>
        </div>

        <?php
    }

    static public function GetMenuNameId()
    {
        return 'smartdes-setting-'.__NAMESPACE__; 
    }

    /**
     * add_action( 'admin_enqueue_scripts'
     */
    public function options_page_scripts()
    {        
        global $pagenow;
    	global $typenow;
        global $hook_suffix;

        $menunameid="settings_page_".self::GetMenuNameId();
        if ($hook_suffix==$menunameid)
        {
            //apply_filters('Smart Design_DoAjaxPageScripts',0);
            //Render Page::DoAjaxPageScripts();
        }


        //wp_enqueue_script('wp-tinymce');
        //wp_enqueue_script('.....wp-media');

        wp_enqueue_media();
		wp_enqueue_script( 'adminmani', PAGEDPWA_MYPWAAPPYPLUGAPP_URI . 'assets/js/adminmani.js', 
			array(), PAGEDPWA_MYPWAAPPYPLUGAPP_VERSION, true );
            
        $oCustData=Funcs::GetCustData();
		wp_localize_script('adminmani','g_opwapaged',$oCustData);
    }
	
    /**
     * add_action( 'wp_head'
     */
	public function AddHdrScripts()
	{
		//$url=PAGEDPWA_MYPWAAPPYPLUGAPP_URI."assets/js/adminmani.js";		
		//$str="<script type='text/javascript' src='$url'></script>";
		//ec ho $str;
		$hdr=MyPWAAppyPlug::GetMetaCSP();
		Funcs::EcHtm($hdr);
        ?>
            <script type='text/javascript'>
                function HandleJsnCodeEg(el)
                {
                    var elTA=document.getElementById('ServAuthJsn');
                    var o=
    		        {
    	    		apiKey: "YOUR_API_KEY",
	    	    	authDomain: "YOURPROJ.firebaseapp.com",
		        	projectId: "YOURPROJ",
			        storageBucket: "YOURPROJ.appspot.com",
			        messagingSenderId: "YOUR_SENDER_ID",
			        appId: "FROM_GOOGLE",
			        measurementId: "FROM_GOOGLE"
		            }

                    var strJson = JSON.stringify(o, null, 2);
                    elTA.value=strJson;
                }
                function HandleExtraMani(el)
                {
                    var homey='<?php Funcs::EcOut(get_home_url());?>';
                    var elTA=document.getElementById('extramanifest');
                    var o=
    		        {
    	    		scope: homey,
		            }

                    var strJson = JSON.stringify(o, null, 2);
                    elTA.value=strJson;
                }
            </script>
        <?php
	}
    
    public function AdminSubPageAbout($key)
    {
        $prefix='';
        $debug=false;
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcStr("about here is $key");
                if ( $debug ) 
                {
                    Funcs::EcHtm('<pre>');
                    /*ec ho esc_html( print_r( $this->get_settings(), true ) );*/
                    Funcs::EcHtm('</pre>');
                }
                ?>
                <form method="post" action="options.php">
                    <?php
                    /*settings_errors( "{$this->prefix}-errors" );
                    settings_fields( "{$this->prefix}-group" );
                    $this->do_settings_sections( $key );
                    submit_button();*/
                    ?>
                </form>
            </div>
        </div>
        <?php
    }

    //$name, $key
    public function AdminSubPageInGrp($key)
    {
        $prefix='';
        $debug=false;
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcStr("key is $key");
                if ( $debug ) 
                {
                    Funcs::EcHtm('<pre>');
                    /*ec ho esc_html( print_r( $this->get_settings(), true ) );*/
                    Funcs::EcHtm('</pre>');
                }
                ?>
                <form method="post" action="options.php">
                    <?php
                    /*settings_errors( "{$this->prefix}-errors" );
                    settings_fields( "{$this->prefix}-group" );
                    $this->do_settings_sections( $key );
                    submit_button();*/
                    ?>
                </form>
            </div>
        </div>
        <?php
    }

    //grp 1 - run
    //smart_designer_run_button
    public function register_pages1($par) 
    {
        $nm="nmAdminMens";
        $title="titAdminMens";
        $caps='manage_options';

        /*
        //sendpush
        $name="Smart Designer Send Push";
        $cb=array( $this,'AdminSubPageSndPushDes');
        $pgnm='Smart_Designer_Send_Push';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        $name="Smart Designer Skin Send Push";
        $cb=array( $this,'AdminSubPageSndPushSkin');
        $pgnm='Smart_Designer_Skin_Send_Push';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);
        */

        /*
        //sendpushgui
        $name="Smart Designer Send Push Message Manager";
        $cb=array( $this,'AdminSubPageSndPushGuiDes');
        $pgnm='Smart_Designer_Send_Push_Gui';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        $name="Smart Designer Skin Send Push Message Manager";
        $cb=array( $this,'AdminSubPageSndPushGuiSkin');
        $pgnm='Smart_Designer_Skin_Send_Push_Gui';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //notifyappy
        $name="Smart Designer Page Receive Push";
        $cb=array( $this,'AdminSubPageDesigner');
        $pgnm='Smart_Designer_Pg_Receive';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        $name="Smart Designer Skin Receive Push";
        $cb=array( $this,'AdminSubPageSkin');
        $pgnm='Smart_Designer_Skin';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //a2hspage
        $name="Smart Designer Button";
        $cb=array( $this,'AdminSubPageButDes');
        $pgnm='Smart_Designer_Button';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        $name="Smart Designer Skin Button";
        $cb=array( $this,'AdminSubPageButSkin');
        $pgnm='Smart_Designer_Skin_Button';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);
        */


        /*
        $cb=array( $this, 'AdminSubPageInGrp' );
        $arrPgs=["pg1"=>"page1","pg2"=>"page2","pg3"=>"page3"];
		foreach ($arrPgs as $key => $name)
        {
			add_submenu_page($par,$name,$name,$caps,$key,$cb);

            ///
            //function () use ( $name, $key ) {
			//} );
            //
		}
        */

        //a2hspage
        $name="Smart Designer Run: Home Screen Button";
        $cb=array( $this,'AdminSubPageRunPgBut');
        $pgnm='smart_designer_run_button';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //subscribepage
        $name="Smart Designer Run: Subscription Buttons";
        $cb=array( $this,'AdminSubPageRunPgSubs');
        $pgnm='smart_designer_run_subs';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //sendpush
        // $name="Smart Designer Run: Send Push Form";
        // $cb=array( $this,'AdminSubPageRunPgSndPush');
        // $pgnm='smart_designer_run_send_push_form';
        // add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //sendpushgui
        //AdminSubPageRunPgSndPushGui
        // $name="Smart Designer Run: Send Push Message Manager";
        // $cb=array( $this,'AdminSubPageRunPgSndPushGui');
        // $pgnm='smart_designer_run_send_push_gui';
        // add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //notifyappy
        $name="Smart Designer Run: Receive Push Page";
        $cb=array( $this,'AdminSubPageRunPgRcvPush');
        $pgnm='smart_designer_run_push';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

	}

    //sharetarget run
    public function AdminSubRunPgShareTarget($k)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($name); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcHtm("<h3>Smart Designer Run: Share Target</h3>");

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='sharetarget';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/mybarepage/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }


    //grp 2
    //design
    public function register_pages2($par) 
    {
        $caps='manage_options';

        //a2hspage
        $name="Smart Designer Page: Home Screen Button";
        $cb=array( $this,'AdminSubPageButDes');
        $pgnm='smart_designer_button';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //subscribepage
        $name="Smart Designer Page: Subscription Buttons";
        $cb=array( $this,'AdminSubPageSubsDes');
        $pgnm='smart_designer_subs';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //sendpush
        // $name="Smart Designer Page: Send Push Form";
        // $cb=array( $this,'AdminSubPageSndPushDes');
        // $pgnm='smart_designer_send_push_form';
        // add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        /*$name="Smart Designer Skin: Send Push";
        $cb=array( $this,'AdminSubPageSndPushSkin');
        $pgnm='Smart_Designer_skin_send_push';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);
        */

        //sendpushgui
        // $name="Smart Designer Page: Send Push Message Manager";
        // $cb=array( $this,'AdminSubPageSndPushGuiDes');
        // $pgnm='smart_designer_send_push_gui';
        // add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        /*$name="Smart Designer Skin: Send Push Message Manager";
        $cb=array( $this,'AdminSubPageSndPushGuiSkin');
        $pgnm='smart_designer_skin_send_push_gui';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);
        */

        //notifyappy
        $name="Smart Designer Page: Receive Push";
        $cb=array( $this,'AdminSubPageDesigner');
        $pgnm='smart_designer_pg_receive';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        /*$name="Smart Designer Skin: Receive Push";
        $cb=array( $this,'AdminSubPageSkin');
        $pgnm='smart_designer_skin';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);
        */

        /*$name="Smart Designer Skin: Button";
        $cb=array( $this,'AdminSubPageButSkin');
        $pgnm='smart_designer_skin_button';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);*/

	}

    //sharetarget design
    public function AdminSubDesPgShareTarget($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcHtm("<h3>Smart Designer Page: Share Target</h3>");

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();
                $scriptby=Funcs::GetScriptByYouUrl();

                $appslug='sharetarget';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/designfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }

    //grp 3
    //skin
    public function register_pages3($par) 
    {
        $caps='manage_options';

        //a2hspage
        $name="Smart Designer Skin: Home Screen Button";
        $cb=array( $this,'AdminSubPageButSkin');
        $pgnm='smart_designer_skin_button';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //subscribepage
        $name="Smart Designer Skin: Subscription Buttons";
        $cb=array( $this,'AdminSubPageSubsSkin');
        $pgnm='smart_designer_skin_subs';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //sendpush
        // $name="Smart Designer Skin: Send Push Form";
        // $cb=array( $this,'AdminSubPageSndPushSkin');
        // $pgnm='Smart_Designer_skin_send_push';
        // add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //sendpushgui
        // $name="Smart Designer Skin: Send Push Message Manager";
        // $cb=array( $this,'AdminSubPageSndPushGuiSkin');
        // $pgnm='smart_designer_skin_send_push_gui';
        // add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

        //notifyappy
        $name="Smart Designer Skin: Receive Push";
        $cb=array( $this,'AdminSubPageSkin');
        $pgnm='smart_designer_skin';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);

	}
    
    //custchatback skin
    public function AdminSubSkinPgShareTarget($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcHtm("<h3>Smart Designer Skin: Share Target</h3>");

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='sharetarget';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/skinfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }

    //grp 0
    //smart_designer_runappdataposts
    //smart_designer_runappdataposts
    public function register_pages0($par) 
    {
        $caps='manage_options';

        //'appdatapostsfor'
        $name="Run appdatapostsfor";
        $cb=array( $this,'AdminSubPageDataLst');
        $pgnm='smart_designer_runappdataposts';
        add_submenu_page($par,$name,$name,$caps,$pgnm,$cb);
	}

    //'appdatapostsfor'
    //run grp 0 CB
    public function AdminSubPageDataLst($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcHtm("<h3>Smart Designer Pages:</h3>");

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='appdatapostsfor';
                $me = get_current_user_id();
                $o=Funcs::GetCustData();
                $pi=$o['isproplugin']?'1':'0';
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/mybarepagesys/?pageme=$appslug&pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey&pi=$pi'>
                    </iframe>");
                ?>
            </div>
        </div>
        <?php
    }

    //notifyappy designer
    public function AdminSubPageDesigner($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Page: Receive Push</h3>",'h3');

                //$host=$_SERVER['HTTP_HOST'];
                $home=get_home_url();
                //$ref="{$host}$home";
                $ref=Funcs::GetHomeRef();
                $scriptby=Funcs::GetScriptByYouUrl();

                $appslug='notifyappy';
                $reqpath0=Funcs::GetReqUrlNoQuery();
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/designfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");
                ?>
            </div>
        </div>
        <?php
    }

    //notifyappy skin
    public function AdminSubPageSkin($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';

        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Skin : Receive Push</h3>",'h3');

                //$host=$_SERVER['HTTP_HOST'];
                $home=get_home_url();
                //$ref="{$host}$home";
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();

                $reqpath0=Funcs::GetReqUrlNoQuery();
                $appslug='notifyappy';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/skinfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }

    //subscribepage design
    public function AdminSubPageSubsDes($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';

        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Page: Buttons for Subscribe</h3>",'h3');

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();
                //$arr=explode('/',$home);
                //array_pop($arr);
                //$home=implode('/',$arr);
                $scriptby=Funcs::GetScriptByYouUrl();

                $appslug='subscribepage';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/designfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }

    //a2hspage design
    //design grp 2 CB
    public function AdminSubPageButDes($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Page: Button for Home Screen</h3>",'h3');

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();
                //$arr=explode('/',$home);
                //array_pop($arr);
                //$home=implode('/',$arr);
                $scriptby=Funcs::GetScriptByYouUrl();

                $appslug='a2hspage';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/designfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }

    //a2hspage skin
    public function AdminSubPageButSkin($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Skin: Button for Home Screen</h3>",'h3');

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='a2hspage';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/skinfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }

    //subscribepage skin
    public function AdminSubPageSubsSkin($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Skin: Buttons for Subscribe</h3>",'h3');

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='subscribepage';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/skinfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }

    //sendpush des
    /*
    public function AdminSubPageSndPushDes($key)
    {
        $prefix='';
        $debug=false;
        $lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php ec ho $prefix; ?>-wrap <?php ec ho $key; ?>-wrap">
            <h1><?php ec ho $key; ?></h1>
            <div class="<?php ec ho $prefix; ?>-wrap__content">
                <?php
                ec ho "<h3>Smart Designer Page: Send Push Form Message</h3>";

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();
                $scriptby=Funcs::GetScriptByYouUrl();

                $appslug='sendpush';
                $me = get_current_user_id();
                ec ho "<i frame style='width:100%;height:800px;' 
                    src='$scriptby/designfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>";

                ?>
            </div>
        </div>
        <?php
    }
    */

    //sendpush skin
    /*
    public function AdminSubPageSndPushSkin($key)
    {
        $prefix='';
        $debug=false;
        $lickey=self::GetOption('lickey');
        ?>
        <div class="wrap <?php ec ho $prefix; ?>-wrap <?php ec ho $key; ?>-wrap">
            <h1><?php ec ho $key; ?></h1>
            <div class="<?php ec ho $prefix; ?>-wrap__content">
                <?php
                ec ho "<h3>Smart Designer Skin: Send Push Form Message</h3>";

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='sendpush';
                $me = get_current_user_id();
                ec ho "<i frame style='width:100%;height:800px;' 
                    src='$scriptby/skinfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>";

                ?>
            </div>
        </div>
        <?php
    }
    */

    //sendpushgui des
    /*
    public function AdminSubPageSndPushGuiDes($key)
    {
        $prefix='';
        $debug=false;
        $lickey=self::GetOption('lickey');
        ?>
        <div class="wrap <?php ec ho $prefix; ?>-wrap <?php ec ho $key; ?>-wrap">
            <h1><?php ec ho $key; ?></h1>
            <div class="<?php ec ho $prefix; ?>-wrap__content">
                <?php
                ec ho "<h3>Smart Designer Page: Send Push Message Manager</h3>";

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();
                $scriptby=Funcs::GetScriptByYouUrl();

                $appslug='sendpushgui';
                $me = get_current_user_id();
                ec ho "<i frame style='width:100%;height:800px;' 
                    src='$scriptby/designfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>";

                ?>
            </div>
        </div>
        <?php
    }
    */

    //sendpushgui skin
    /*
    public function AdminSubPageSndPushGuiSkin($key)
    {
        $prefix='';
        $debug=false;
        $lickey=self::GetOption('lickey');
        ?>
        <div class="wrap <?php ec ho $prefix; ?>-wrap <?php ec ho $key; ?>-wrap">
            <h1><?php ec ho $key; ?></h1>
            <div class="<?php ec ho $prefix; ?>-wrap__content">
                <?php
                ec ho "<h3>Smart Designer Skin: Send Push Message Manager</h3>";

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='sendpushgui';
                $me = get_current_user_id();
                ec ho "<i frame style='width:100%;height:800px;' 
                    src='$scriptby/skinfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>";

                ?>
            </div>
        </div>
        <?php
    }
    */

    //custchatback design
    public function AdminSubDesPgCustMsgs($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Page: Customer Receive And Send Msgs</h3>",'h3');

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();
                $scriptby=Funcs::GetScriptByYouUrl();

                $appslug='custchatback';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/designfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }
    
    //custchatback skin
    public function AdminSubSkinPgCustMsgs($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Skin: Customer Receive And Send Msgs</h3>",'h3');

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='custchatback';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/skinfrm/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }


    //notifyappy run
    public function AdminSubPageRunPgRcvPush($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Run: Receive Push</h3>",'h3');

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='notifyappy';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/mybarepage/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }

    //a2hspage run
    public function AdminSubPageRunPgBut($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Run: Button for Home Screen</h3>",'h3');

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='a2hspage';
                //$appslug='notifyappy';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/mybarepage/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }

    //subscribepage run
    public function AdminSubPageRunPgSubs($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Run: Buttons for Subscribe</h3>",'h3');

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='subscribepage';
                //$appslug='notifyappy';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/mybarepage/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }

    //sendpush run
    /*
    public function AdminSubPageRunPgSndPush($key)
    {
        $prefix='';
        $debug=false;
        $lickey=self::GetOption('lickey');
        ?>
        <div class="wrap <?php ec ho $prefix; ?>-wrap <?php ec ho $key; ?>-wrap">
            <h1><?php ec ho $key; ?></h1>
            <div class="<?php ec ho $prefix; ?>-wrap__content">
                <?php
                ec ho "<h3>Smart Designer Run: Send Push Form Message</h3>";

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='sendpush';
                //$appslug='notifyappy';
                $me = get_current_user_id();
                ec ho "<i frame style='width:100%;height:800px;' 
                    src='$scriptby/mybarepage/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>";

                ?>
            </div>
        </div>
        <?php
    }
    */

    //sendpushgui run
    /*
    public function AdminSubPageRunPgSndPushGui($key)
    {
        $prefix='';
        $debug=false;
        $lickey=self::GetOption('lickey');
        ?>
        <div class="wrap <?php ec ho $prefix; ?>-wrap <?php ec ho $key; ?>-wrap">
            <h1><?php ec ho $key; ?></h1>
            <div class="<?php ec ho $prefix; ?>-wrap__content">
                <?php
                ec ho "<h3>Smart Designer Run: Send Push Message Manager</h3>";

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='sendpushgui';
                //$appslug='notifyappy';
                $me = get_current_user_id();
                ec ho "<i frame style='width:100%;height:800px;' 
                    src='$scriptby/mybarepage/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>";

                ?>
            </div>
        </div>
        <?php
    }
    */

    //custchatback run
    public function AdminSubRunPgCustMsgs($key)
    {
        $prefix='';
        $debug=false;
        //$lickey=self::GetOption('lickey');
        $lickey='';
        ?>
        <div class="wrap <?php Funcs::EcStr($prefix); ?>-wrap <?php Funcs::EcStr($key); ?>-wrap">
            <h1><?php Funcs::EcStr($key); ?></h1>
            <div class="<?php Funcs::EcStr($prefix); ?>-wrap__content">
                <?php
                Funcs::EcTag("<h3>Smart Designer Run: Customer Receive And Send Msgs</h3>",'h3');

                $home=get_home_url();
                $ref=Funcs::GetHomeRef();

                $scriptby=Funcs::GetScriptByYouUrl();
                $appslug='custchatback';
                $me = get_current_user_id();
                Funcs::EcIFr("<iframe style='width:100%;height:800px;' 
                    src='$scriptby/mybarepage/?pageid=$appslug&reqpath=$ref&me=$me&lickey=$lickey'></iframe>");

                ?>
            </div>
        </div>
        <?php
    }

    public function ServPeriodic()
    {
        ?>
            <br/>
            <label style='display:inline-block;width:190px;' for="periodictag">Periodic tag name:</label>
            <input style='width:50em;' type="text" name="periodictag" id="periodictag" 
                title='Please specify tag for periodic message:' value='<?php Funcs::EcStr($periodictag); ?>'>
            <br/>

            <label style='display:inline-block;width:190px;' for="SenderID">Google Sender ID:</label>
            <input style='width:50em;' type="text" name="SenderID" id="SenderID" 
                title='Server Id' 
                value='<?php Funcs::EcStr($SenderID); ?>'>
            <br/>

        <?php
    }

    public function ServKeys($ServerKey,$ServAuthJsn)
    {
        ?>

            <label style='vertical-align:top;display:inline-block;width:190px;' 
                for="ServAuthJsn"><br>Paste Your Authentification Code here:<br><br>
                Copy it from your Google Firebase Account Console <br><br>
                (include the { to start and everything inside it then } at the end)
                <br><br>
                <input type='button' id='idServAuthJsnEg' onclick='HandleJsnCodeEg(this);' 
                    value='Paste Example Here'/>
            </label>
            <span style='vertical-align:top;width:50em;display:inline-block;'>
            <textarea style='height:20em;width:50em;' name="ServAuthJsn" id="ServAuthJsn" 
                title='Paste It Here' ><?php Funcs::EcJs($ServAuthJsn); ?></textarea>
            <br/>
            <span>Google Console Project overview: General tab</span>
            </span>
            <br/>


            <br/>
            <label style='vertical-align:top;display:inline-block;width:190px;' 
                for="ServerKey">Google Server Key:
            <br>(Also called application server key or generate key pair)
            </label>
            <span style='width:50em;display:inline-block;'>
            <input style='width:50em;' type="text" name="ServerKey" id="ServerKey" 
                title='Server Key' 
                value='<?php Funcs::EcTok($ServerKey); ?>'>
            <span>
                Google Console Project overview: Cloud Message tab / Web Configuration / Web Push certificates.
            </span>
            </span>
            <br/>
            <br/>

        <?php
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

	static public function GetServVar($str0)
	{
		$str=$_SERVER[$str0];
        return esc_textarea($str);
	}
}

if( is_admin() )
    $my_settings_page = new MySettingsPage();
