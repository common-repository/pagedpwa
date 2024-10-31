<?php

namespace SmartIntelPWAAppy;
class Funcs
{
	const m_strKeyFor='phildesigned';
	const m_strKeyCtls='philintel';
	const m_strKeyExtra='philintelextra';

	static public $m_strPath='';
	static public $m_strUri='';
	
	//static public $m_strPageKeyLay=g_strKeyFor;
	//static public $m_strPageKeyCtls=g_strKeyCtls;
	//static public $m_strPageKeyExtra=g_strKeyExtra;

	/**
	 * define
	 */
	static public $FuturePaidFor=true;
	static public $PaidFor=true;
	static public $DevMode=true;
	/**
	 * static public $MngSubPage=true;
	 * static public $ShowRowProps=true;
	 * static public $ShowRowPropsCSS=false;
	 * static public $PageCssEtc=false;
	 */
	
	static public $DebugMode=false;
	static public $ReleaseMode=true;
	static public $Comment=false;
	static public $Platform=false;
	static public $TestNotice=true;
	static public $BothPlugins=true;
	static public $WPWidgetTest=false;
	static public $DebugEditor=false;
	static public $Dashboard=true;
	static public $OptionsImport=false;
	static public $SessionStart=false;
	static public $NullErrorNOTOK=false;
	static public $RenderThis=false;
	static public $DebugDump=false;
	static public $WPEnvironment=true;
	static public $DebugHand=false;

	static function SetDefines(&$obj)
	{
		$obj['PaidFor']=self::$PaidFor;
		$obj['DebugMode']=self::$DebugMode;
		$obj['ReleaseMode']=self::$ReleaseMode;
		$obj['Comment']=self::$Comment;
		$obj['Platform']=self::$Platform;
		$obj['TestNotice']=self::$TestNotice;
		$obj['BothPlugins']=self::$BothPlugins;
		$obj['WPWidgetTest']=self::$WPWidgetTest;
		$obj['DebugEditor']=self::$DebugEditor;
		$obj['Dashboard']=self::$Dashboard;
		$obj['DevMode']=self::$DevMode;
		$obj['RenderThis']=self::$RenderThis;
		$obj['DebugDump']=self::$DebugDump;
		$obj['WPEnvironment']=self::$WPEnvironment;
		$obj['FuturePaidFor']=self::$FuturePaidFor;

		$obj['DebugHand']=self::$DebugHand;
	}

	static public function GetMapUrl($url,&$proto,&$domain) 
	{
		$pos=-1;
		if (substr($url,0,7)=='http://')
			$pos=7;
		else if (substr($url,0,8)=='https://')
			$pos=8;
		if ($pos<0)
			return '';

		$proto=substr($url,0,$pos);

		$slash=strpos($url,'/',$pos);
		if ($slash)
		{
			$domain=substr($url,$pos,$slash-$pos);
			$path=substr($url,$slash+1);
		}
		else
		{
			$domain=substr($url,$pos);
			$path='';
		}
		return $path;
	}

	/**
	 * get page ctl by uid
	 */
	static public function GetPageCtlData($pageid,$strKeyCtls,$ctlis)
	{
		
		$ctlProps = get_post_meta( $pageid, $strKeyCtls, true );
		if (!$ctlProps)
			return null;
		foreach ($ctlProps as $ctl)
		{
			$ctlid=$ctl['props']['_ctlid'];
			if ($ctlid==$ctlis)
				break;
		}
		if ($ctlid!=$ctlis)
			return null;

		if (self::IsPageCtlDataIdUsed($pageid,$ctlis))
			return $ctl;
		return null;
	}
	
	/**
	 * return true if used
	 */
	static public function IsPageCtlDataIdUsedIn($ctlis,$lay)
	{
		foreach ($lay as $row)
		{
			$rowtype=$row['rowtype'];
			for ($i=0;$i<$rowtype;$i++)
			{
				$ids=$row["col$i"];
				$arr=explode(',',$ids);
				//array_filter();
				//array_key_exists()
				//array_value_exists();
				//array_values();
				$pos=array_search($ctlis,$arr);
				if ($pos!==false) 
					return true;
			}
		}
		return false;
	}
	
	/**
	 * return true if used
	 */
	static public function IsPageCtlDataIdUsed($pageid,$ctlis)
	{
		$lay="phildesigned";
		$lay = get_post_meta( $pageid, $lay, true );

		return self::IsPageCtlDataIdUsedIn($ctlis,$lay);
	}

	static public function do_default_content_filter( $content )
	{
		if( $content )
		{
			global $wp_embed;
			$content = $wp_embed->run_shortcode( $content );
			$content = $wp_embed->autoembed( $content );
			$content = wptexturize( $content );
			$content = convert_smilies( $content );
			$content = convert_chars( $content );
			$content = wptexturize( $content );
			$content = do_shortcode( $content );
			$content = shortcode_unautop( $content );
			$content = wpautop( $content );
		}
		return $content;
	}///-return true only if found

	/**
	 * hooks on admin_print_scripts-post.php
	 */
	static public function add_script_add_new_button()
	{
		/**
		 * global $typenow;
		 */

		
		?>
		<style type="text/css">
			.smartdes_split-page-combo {
				display: inline-block;
			}
			
			.smartdes_split-page-combo a,
			.smartdes_split-page-combo a:active,
			.smartdes_split-page-combo .expander:after {
				padding: 6px 10px;
				position: relative;
				top: -3px;
				text-decoration: none;
				border-radius: 2px 0px 0px 2px;
				background: #f7f7f7;
				text-shadow: none;
				font-weight: 600;
				font-size: 13px;
				line-height: normal; /* IE8-IE11 need this for buttons */
				color: #0073aa; /* some of these controls are button elements and don't inherit from links */
				cursor: pointer;
				outline: 0;
			}
			
			.smartdes_split-page-combo a:hover,
			.smartdes_split-page-combo .expander:hover:after {
				border-color: #008EC2;
				background: #00a0d2;
				color: #fff;
			}
			
			.smartdes_split-page-combo a:focus,
			.smartdes_split-page-combo .expander:focus:after {
				border-color: #5b9dd9;
			}
			
			.smartdes_split-page-combo .expander {
				outline: none;
				/*vertical-align: bottom;*/
			}
		
			.smartdes_split-page-combo .expander:after {
				content: "\f140";
				font: 400 20px/.5 dashicons;
				speak: none;
				top: 5px;
			<?php if ( is_rtl() ) : ?>
				right: -1px;
			<?php else : ?>
				left: -1px;
			<?php endif; ?>
				position: relative;
				vertical-align: baseline;
				text-decoration: none !important;
				margin:2px 0px 7px 0px;
				border: 1px solid #ccc;
				/*padding:5px 2px 0px 0px;*/
			}
			
			.smartdes_split-page-combo .dropdown {
				display: none;
			}
			
			.smartdes_split-page-combo .dropdown.visible {
				display: block;
				position: absolute;
				margin-top: 3px;
				z-index: 1;
			}
			
			.smartdes_split-page-combo .dropdown.visible a {
				display: block;
				top: 0;
				margin: -1px 0;
			<?php if ( is_rtl() ) : ?>
				padding-left: 9px;
			<?php else : ?>
				padding-right: 9px;
			<?php endif; ?>
			}
			
		</style>
		<?php
	}

	/**
	 * smart query
	 */
	static public function QuerySmartDataRec($parId,$ptype,$slug)
	{
		$args = array(
			//'post_name'      => $slug,
			'post_title'      => $slug,
			//'status'      => 'private',
			//'ignore_sticky_posts'      => true,
			'post_status' => array('private','publish','draft'),
			'post_type'	=> $ptype,
			'post_parent'	=> $parId,
		);
		$results=Funcs::SmartQueryArgs($args);
		if (count($results)!=1)
			return null;
		return $results[0];
	}

	/**
	 * smart query
	 */
	static public function QuerySmartSlug($slug)
	{
		$args = array(
			'post_name'      => $slug,
			'post_status' => array('private','publish','draft'),
		);
		$results=Funcs::SmartQueryArgs($args);
		if (count($results)!=1)
			return null;
		return $results[0];
	}

	/**
	 * ORDER BY {$pre}posts.menu_order ";
	 * $wpuserid may be null
	 */
	static public function QuerySmartDataRecs($parId,$ptype,$wpuserid)
	{
		if ($wpuserid==null)
		{
			$usr=wp_get_current_user();
			$wpuserid=$usr->ID;
		}
		$args = array(
			//'post_name'      => $slug,
			//'status'      => 'private',
			//'ignore_sticky_posts'      => true,
			'post_status' => array('private','publish','draft'),
			'post_type'	=> $ptype,
			'post_parent'	=> $parId,
			'post_author'	=> $wpuserid,
		);
		$results=self::SmartQueryArgs($args);
		return $results;
	}

	
	

	/**
	 * post type stuff
	 */
	static public function GetReqUrlNoQuery() 
	{
		//$url=$_SERVER['REQUEST_URI'];
		//@@bugsmy30June24
		$url=self::GetServUrl('REQUEST_URI');
		/**
		 * lose query part
		 */
		$arr=explode('?',$url);
		if (count($arr)>1)
			$url=$arr[0];
		return $url;
	}
	
	static public function GetDirsFromUrl() 
	{
		$url=self::GetReqUrlNoQuery();
		return self::GetDirsFromAUrl($url);
	}
	
	/**
	 * $url="/forums/forum/smart-designer/";
	 * must start with '/wp'
	 */
	static public function GetDirsFromAUrl($url) 
	{
		$homeurl = home_url();

		/**
		 * $po=isset(_$GLOBALS['post'])?_$GLOBALS['post']:null;
		 */

		/**
		 * $proto=null;
		 */
		$domain=null;
		/**
		 * $homeurl = "https://scriptbyyou.com";
		 */
		$proto='';
		$homepath=Funcs::GetMapUrl($homeurl,$proto,$domain);
		$homepath='/'.$homepath;

		$homelen=strlen($homepath);
		$rightdir=substr($url,$homelen);
		if ($rightdir=='' || $rightdir[0]!='/')
			$rightdir="/".$rightdir;
        $rightdir=trailingslashit( $rightdir );
		$dirs=explode('/',$rightdir);
		$len=count($dirs);

		//loose first and last
		array_shift($dirs);
		array_pop($dirs);
		return $dirs;
	}

	/**
	 * 
	 */
	public static function IsThisRoute($dirs,$strRoute,&$dirpart)
	{
		if (!$strRoute)
			return false;

		/**
		 * $route
		 */
		$route=explode('/',$strRoute);
		$nLenDir=count($dirs);
		$nLenRoute=count($route);
		if ($nLenDir!=$nLenRoute)
			return false;

		$i=0;
		foreach ($route as $proute)
		{
			$pdir=$dirs[$i];
			if ($proute=="*")
				$dirpart=$pdir;

			if ($proute!="*" && $proute!=$pdir)
				return false;
			$i++;
		}

		/**
		 * if ($this->m_strRoute==$route)
		 * 	return true;
		 */
		return true;
	}
	






	/**
	 * create attach
	 */
	//

//@@






/**
 * 
	 * REST func
	 * ok works using this
 */
	static function GetAllImagesLibAttach($size,$wpuserid)
	{
		$query_images_args = array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
			'posts_per_page' => - 1,
		);
	
		if ($wpuserid && $wpuserid>=0)
			$query_images_args['author']=$wpuserid;

		$query_images = new \WP_Query( $query_images_args );
	
		$imgs = array();
		/**
		 * $size="thumbnail";
		 */
		$icon=false;
		/**
		 * $size="large";
		 */

		$upload_dir   = wp_upload_dir();
		$row=0;
		foreach ( $query_images->posts as $post ) 
		{
			$obj=array();
			$url = wp_get_attachment_url( $post->ID );
			$meta = wp_get_attachment_metadata( $post->ID );
			$imageSrc = wp_get_attachment_image_src( $post->ID, $size, $icon );
			$obj['title']=$post->post_title;
			$obj['url']=$url;
			$obj['urlsize']=$imageSrc[0];
			$obj['urlwdt']=$imageSrc[1];
			$obj['urlhgt']=$imageSrc[2];
			$obj['author']=$post->post_author;
			$obj['Id']=$row;
			$obj['idpos']=$row;
			$obj['postId']=$post->ID;
			$imgs[]=$obj;
			$row++;
		}
		return $imgs;
	}

	static public function CreatePostFor($usepostid,&$arrRec) 
	{
		$usr=wp_get_current_user();
        $usrid=$usr->ID;
        
		//$piname=CardsByYou::GetPlugNameHere();

		$ptype='';

        $rec=array();
		$rec['post_author']=$usrid;
		///////$rec['post_content']=$strIds;
		/**
		 * $rec['post_content_filtrerd']='';
		 * $rec['post_title']=$strCtlUId;
		 */
		
		/**
		 * $rec['ping_status']=$prodcode;
		 * $rec['post_excerpt']=$txcode;
		 */
		$rec['post_status']='publish';
		/**
		 * $rec['post_status']='private';
		 */

        /**
         * $ptype='post';
         */
        $rec['post_type']=$ptype;
		/**
		 * $rec['post_name']=$named;
		 * $rec['post_title']=$named;
		 */
        
        /*$arrFields=self::GetPostFields();
        foreach ($arrFields as $strField)
        {
            $rec[$strField]=$arrRec[$strField];
        }*/
        foreach ($arrRec as $k=>$v)
        {
            $rec[$k]=$v;
        }

		$rec['ID']=$usepostid;
		$newid=wp_insert_post($rec);
		return $newid;
	}
	
	//e-commerce
	static function CreateNewPerMeta($newid)
	{
		$um=update_post_meta($newid,'printedcount', 0);
		$val0_b=get_post_meta( $newid, 'printedcount', true);
		$um=update_post_meta($newid,'payedup', false);
		$val0_b=get_post_meta( $newid, 'payedup', true);
		$um=update_post_meta($newid,'faked', false);
	}

	//privatemsg
	//$torecip : display name
	//$sendto : recip user id
	static function CreateNewMsgMeta($newid,$torecip,$sendto)
	{
		$um=update_post_meta($newid,'recip', $torecip);
		$um=update_post_meta($newid,'sendto', $sendto);
		$val0_b=get_post_meta( $newid, 'sendto', true);
		$um=update_post_meta($newid,'readit', 0);
	}

	static function CreateNewPerPost($xcardid)
	{
		$arr=array(
			'post_type'=>'cardpersonal',
			/**
			 * 'post_content'=>$xml,
			 */
			'post_content'=>'',
			'post_content_filtered'=>$xcardid,
			'post_title'=>'per card');
		$newid=self::CreatePostFor(0,$arr);

		self::CreateNewPerMeta($newid);		
		return $newid;
	}

	static function CreateNewMsgPost($strSub,$torecip,$strMsg,$sendto,$noti)
	{
		return self::CreateNewMsgPostAuth(null,$strSub,$torecip,$strMsg,$sendto,$noti);
	}

	//wpuserid may be null
	static function CreateNewMsgPostAuth($wpuserid,$strSub,$torecip,$strMsg,$sendto,$noti)
	{
		$arr=array(
			//'post_type'=>'privatemsg',
			'post_type'=>'ptypemsgtype',
			/**
			 * 'post_content'=>$xml,
			 */
			'post_content'=>$strMsg,
			'post_content_filtered'=>'',
			'post_title'=>$strSub,
			'post_excerpt'=>'pubact');
		if ($wpuserid)
			$arr['post_author']=$wpuserid;

		$newid=self::CreatePostFor(0,$arr);
		$po0=get_post($newid);

		$um=update_post_meta($newid,'noti', $noti);

		self::CreateNewMsgMeta($newid,$torecip,$sendto);
		return $newid;
	}

	/**
	 * retruren new xml from des
	 */
	static function CreatePersonalXml($strXml,$shop)
	{
        $doc = new \DOMDocument();
        $doc->loadXml($strXml);

		if ($shop['regthis']=='y')
			return $strXml;
			
		$elList = $doc->getElementsByTagName('back');
        $elBack=$elList[0];
		if (!$elBack)
			return $strXml;

		$par=$elBack->parentNode;
		$par->removeChild($elBack);

		$newxml=$doc->saveXML();
		return $newxml;
	}
	
	
	static function GetDesignFile($xcardid)
	{
		/**
		 * return "files/a_{$xcardid}_";
		 */
		return "files/A_{$xcardid}_";
	}
	
	//$termids may be null
	static public function CountPostsForTaxTermsSql($termids,$sqlAnd,$joins,$order)
	{
		global $wpdb;
		$pre=$wpdb->prefix;
		/**
		 * $strSelect="SELECT count ({$pre}posts.*)  ";
		 * $strSelect="SELECT count (*)  ";
		 */
		$strSelect="SELECT SQL_CALC_FOUND_ROWS {$pre}posts.*  ";
		return self::GetPostsForTaxTermsSql($termids,$sqlAnd,$joins,$order,$strSelect);
	}
	
	//$termids may be null
	static public function FindPostsForTaxTermsSql($termids,$sqlAnd,$joins,$order)
	{
		global $wpdb;
		$pre=$wpdb->prefix;
		$strSelect="SELECT  {$pre}posts.*  ";
		return self::GetPostsForTaxTermsSql($termids,$sqlAnd,$joins,$order,$strSelect);
	}

	//$termids may be null
	static public function GetPostsForTaxTermsSql($termids,$sqlAnd,$joins,$order,$strSelect)
	{
		/**
		 * $id=$post->ID;
		 */

		//smartcat
		/**
		 * $terms = get_terms( array(
		 * 	'taxonomy' => $taxnamed,
		 * 	'fields'   => 'id=>slug',
		 * ) );
		 * $termids=array_keys($terms);
		 */

		$in=null;
		if ($termids)
			$in=implode(',',$termids);
		//55,56,57,58,59,60,61,62,65
		//AND {$pre}posts.post_type = 'smartprod' 

		/**
		 * and id=$id  
		 */

		global $wpdb;
		$pre=$wpdb->prefix;

		if ($in)
		{
			$sql=
			"$strSelect  
			FROM {$pre}posts  
			LEFT JOIN {$pre}term_relationships ON ({$pre}posts.ID = {$pre}term_relationships.object_id) 
			$joins 
			WHERE 1=1  AND (   {$pre}term_relationships.term_taxonomy_id IN ($in)) 
			AND $sqlAnd 
			GROUP BY {$pre}posts.ID 
			$order";
		}
		else
		{
			$sql=
			"$strSelect  
			FROM {$pre}posts  
			$joins 
			WHERE 1=1  
			AND $sqlAnd 
			GROUP BY {$pre}posts.ID 
			$order";
		}

		/**
		 * ORDER BY {$pre}posts.post_date DESC";
		 */
		return $sql;
	}

	static public function WherePostArgsTableSql($args,$table)
	{
		$arr=array();
		foreach ($args as $k=>$v)
		{
			if (is_scalar($v) )
			{
				/**
				 * $arr[]="{$table}.$k='$v'";
				 */
				$cmp=$k=self::GetKeyCmp($k,$v);
				$arr[]="{$table}.$cmp";
			}
			else
			{
				$in=implode("','",$v);
				$arr[]="{$table}.$k in ('$in')";
			}
		}
		$sql=implode(' and ',$arr);
		return $sql;
	}


	/**
	 * parse post for img find only - get just 1 etc
	 * return arr of new imgs
	 */
	static public function SmartGetPostAllImgFnd($thbsz,$szParams)
	{
		global $post;
		/**
		 * preg_match( '/<img [^\>]*\ \/>/i', $post->post_content, $matches );
		 * preg_match( '/<img [^\>]*\ \/>/i', $post->post_content, $matches0 );
		 */

		//find regular images
		$mypat='/<img [^\>]*\ \/>/i';
		preg_match_all($mypat,$post->post_content, $matches, PREG_SET_ORDER );

		/**
		 * $pattern="\[(\[?)(img)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)";
		 * $um=preg_match_all( '/'. $pattern .'/s', $post->post_content, $matchesimg );
		 */

		/**
		 * if ( ! empty( $matches[0] ) ) 
		 */
		$arrNewUrl=array();
		foreach ($matches as $match)
		{
			/**
			 * .smartdesignedpage
			 * class="alignright"
			 * $output = $matches[0];
			 */
			$url=$match[0];
			/**
			 * $newurl=self::ReplaceImgUrl($arrhtm,$arrcleaned,$thbsz,$szParams,$contenttext,$url);
			 */
			$newurl=self::ReplaceImgUrl($url,$thbsz,$szParams);

			$arrNewUrl[]=$newurl;
		}
		//$html=$post->post_content;
		//-$newehtml=preg_replace($mypat,$arrNewUrl,$html);
		//$newhtml=preg_grep($mypat,$arrNewUrl);
		//$um=$newhtml;
		return $arrNewUrl;
	}

	/**
	 * return new img tag from old $url in <img from media insert
	 * $thbsz=new size, $szParams=WP sizes,
	 */
	//&$arrhtm,&$arrcleaned
	/**
	 * return new url
	 */
	static public function ReplaceImgUrl($url,$thbsz,$szParams)
	{
		$icon=null;
		/**
		 * uh?//
		 */
		
		/**
		 * $arrhtm[]=$url;
		 * $contenttext=str_replace($url,'',$contenttext);
		 */

		/**
		 * remove class="alignright"
		 * wp-image-168 wp-post-image
		 * size-medium wp-image-266
		 */
		$imgHtm=self::SmartParseHtmlTag($url,$arrAttribs,$bEmpty);
		$cls=isset($arrAttribs['class'])?$arrAttribs['class']:null;
		$imgId=null;
		if ($cls)
			$imgId=self::FindImgInClassId($cls);
		if ($imgId)
		{
			list($src, $width, $height, $is_intermediate)=
				wp_get_attachment_image_src( $imgId, $thbsz, $icon );
			$arrAttribs['src'] = $src;
			$arrAttribs['width'] = $width;
			$arrAttribs['height'] = $height;
		}
		else
		{
			$arrAttribs['width'] = $szParams['width'];
			$arrAttribs['height'] = $szParams['height'];
		}
		$strNew=self::RemoveClasses($cls,array('alignright','alignnone','alignleft','aligncenter'));
		$arrAttribs['class']=$strNew;
		/**
		 * unset();
		 */
		$arrDef=array();
		/**
		 * $arrDef['class']=$strNew;
		 */
		$imgHtm=self::BuildHtmlTag($imgHtm,$arrAttribs,$arrDef,$bEmpty);

		/**
		 * $arrcleaned[]=$imgHtm;
		 * self::AddExif($exif,$imgId);
		 */

		return $imgHtm;
	}

	/**
	 * <img class="alignnone size-medium wp-image-166" .......
	 * return tag (null as error)
	 */
	static public function SmartParseHtmlTag($strHtml,&$arrAttribs,&$bEmpty)
	{
		$nLen=strlen($strHtml);
		$nPos=0;

		/**
		 * find spc first for tag
		 */
		$nFnd=strpos($strHtml,' ',$nPos);
		if ($nFnd==false)
			return null;

		$tag=substr($strHtml,0,$nFnd);
		$tag=substr($tag,1);

		$nPos=$nFnd+1;
		while ($nPos<$nLen)
		{
			$nFnd=strpos($strHtml,'=',$nPos);
			if ($nFnd==false)
				break;

			$name=substr($strHtml,$nPos,$nFnd-$nPos);
			$name=trim($name,"'\" ");
			$nPos=$nFnd+1;
			$sep=substr($strHtml,$nPos,1);
			$nPos++;
			$nFnd=strpos($strHtml,$sep,$nPos);
			if ($nFnd==false)
				break;

			$val=substr($strHtml,$nPos,$nFnd-$nPos);
			$val=trim($val,"'\"");
			$arrAttribs[$name]=$val;

			$nPos=$nFnd+1;
		}
		$bEmpty=false;
		$name=substr($strHtml,$nPos);
		$name=trim($name,"'\" ");
		if ($name=='/>')
			$bEmpty=true;
		return $tag;
	}

	/**
	 * wp-image-168 wp-post-image
	 * size-medium wp-image-266
	 */
	static public function FindImgInClassId($cls)
	{
		$arr=explode(' ',$cls);
		$strFind='wp-image-';

		foreach ($arr as $str)
		{
			$strIn=substr($str,0,9);
			if ($strIn==$strFind)
			{
				return substr($str,9);
			}
		}
		return null;
	}

	/**
	 * remove class="alignright"
	 */
	static public function RemoveClasses($cls,$arrRemove)
	{
		$arr=explode(' ',$cls);
		$strNew='';
		foreach ($arr as $str)
		{
			$fnd=in_array($str,$arrRemove);
			if (!$fnd)
			{
				if ($strNew!="")
					$strNew.=' ';
				$strNew.=$str;
			}
		}
		return $strNew;
	}

	/**
	 * assumes empty tag
	 * return html img
	 */
	static public function BuildHtmlTag($strHtmlTag,&$arrAttribs,$defArgs,$bEmpty)
	{
		$strHtml='';
		/**
		 * uh?//
		 */

		$args = wp_parse_args( $arrAttribs, $defArgs );
		
		$arr=explode(" ",$strHtml);
		$htm="<$strHtmlTag";
		foreach ($args as $key=>$val)
		{
			$htm.=" $key='$val'";
		}

		if ($bEmpty)
			$htm.="/>";
		else
			$htm.=">";
		return $htm;
	}

	//public shops in order
	static public function GetAllShops() 
	{
		/*$args = array( 
			'post_type' => 'shopcardtype',
			'posts_per_page' => -1,
			'meta_key'=>'shoppublic',
			'meta_value'=>'no',
		);*/
		$args=array(
			'post_type' => 'shopcardtype',
			'nopaging' => true,
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'asc',
			'meta_query' => array(
				'relation'=>"AND",
				array(
					'key' => 'shoppublic',
					'value' => 'no',
					'compare' => '!=',
				)
			),
		);

		$query = new \WP_Query( $args );
		return $query->posts;
	}

	static public function GetAllShopTypes() 
	{
		$args = array( 
			'post_type' => 'shopbiztype',
			'posts_per_page' => -1,
		);
		$query = new \WP_Query( $args );
		return $query->posts;
	}

	/**
	 * return obj of meta vals
	 */
	static public function GetShopBy($metaname,$val) 
	{
		$args = array( 
			'post_type' => 'shopcardtype',
			'meta_key'      => $metaname,
			'meta_value'      => $val,
			'posts_per_page' => -1,
		);
		$query = new \WP_Query( $args );
		$po=$query->post;
		if (!$po)
			return null;

		$id=$po->ID;
		$obj=self::GetShopRecObj($id);
		return $obj;
	}

	static public function GetShopRecBy($metaname,$val) 
	{
		$args = array( 
			'post_type' => 'shopcardtype',
			'meta_key'      => $metaname,
			'meta_value'      => $val,
			'posts_per_page' => -1,
		);
		$query = new \WP_Query( $args );
		$po=$query->post;
		if (!$po)
			return null;

		return $po;
	}

	/**
	 * shop type -'route keyprods',sub dirdes
	 */
	//meta fields
	static public function GetShopRecObj($id) 
	{
		$arr=array('shopownid','shoppublic','shoptype','sides','adminsides','canvaswdt','canvashgt',
			'xmlcontent','xmlcontentper',
			'subdir','posttype','thbwdtsmall','thbhgtsmall',
			'thbwdtbig','thbhgtbig','isdefault','pageslugdesign',
			'pageslugmetafields','pageslugper','posttypecat',
			'regthis','usecanvasset','defside','stopautoleft','stopautoright','accordionhgt',
			'bordercolor','borderstyle','shopformat');
		$obj=array();
		foreach ($arr as $str)
		{
			$obj[$str]=get_post_meta($id, $str, true);
		}
		return $obj;
	}

	/**
	 * return obj of meta vals
	 */
	static public function GetShopTypeBy($metaname,$val) 
	{
		$args = array( 
			'post_type' => 'shopbiztype',
			'meta_key'      => $metaname,
			'meta_value'      => $val,
			'posts_per_page' => -1,
		);
		$query = new \WP_Query( $args );
		$po=$query->post;
		if (!$po)
			return null;

		$id=$po->ID;
		$obj=self::GetShopTypeRecObj($id);
		return $obj;
	}

	///biz type
	static public function GetShopTypeRecObj($id) 
	{
		$arr=array('shopownid','shoppublic','shoptype','sides','canvaswdt','canvashgt',
			'xmlcontent','xmlcontentper',
			'subdir','posttype','thbwdtsmall','thbhgtsmall',
			'thbwdtbig','thbhgtbig','isdefault','pageslugdesign',
			'pageslugmetafields','pageslugper','posttypecat','shopformat');
		$obj=array();
		foreach ($arr as $str)
		{
			$obj[$str]=get_post_meta($id, $str, true);
		}
		return $obj;
	}

	/**
	 * shop has posttypecat
	 */
	static public function GetPTypeCatShop($strPType) 
	{
        $shop=Funcs::GetShopBy('posttype',$strPType);
        if ($shop['posttypecat'])
            $taxnamed=$shop['posttypecat'];
        else
			$taxnamed="{$strPType}cat";
		return $taxnamed;
	}

	static public function CreateMyPageOpt($optrec)
	{
		$usr=wp_get_current_user();
		$usrid=$usr->ID;

		$newid=self::UpdateMyPage(0,$usrid,$optrec);
        
        return $newid;
	}

	static public function UpdateMyPage($usepostid,$usrid,$optrec) 
	{
		$rec=array();
		$rec['ID']=$usepostid;
		$rec['post_author']=$usrid;
		/**
		 * $rec['post_date']='';
		 * $rec['post_date_gmt']='';
		 */
		///////$rec['post_content']=$strIds;
		/**
		 * $rec['post_content_filtrerd']='';
		 * $rec['post_title']=$strCtlUId;
		 */
		
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
		$rec['post_type']='papr';

		//$rec['comment_status']='';
		/**
		 * $rec['ping_status']='';
		 * $rec['post_password']='';
		 * $rec['post_name']='';
		 * $rec['to_ping']='';
		 * $rec['pinged']='';
		 */
		$rec['post_modified']='';
		$rec['post_modified_gmt']='';
		$rec['post_parent']=0;
		$rec['menu_order']=0;

		foreach ($optrec as $k=>$v)
		{
			$rec[$k]=$v;
			//$rec['post_name']=$titleslug;
			//$rec['post_title']=$titleslug;
		}
		$newid=wp_insert_post($rec);
		return $newid;
	}

	/**
	 * keep content
	 * return <img> html
	 */
	static public function GetPostImgAsZoom(&$content)
	{
		global $post;

		$sz='large';
		/**
		 * $sz='thumbnail';
		 * $sz='post-thumbnail';
		 */
		$szParams=null;
		$arrImg=Funcs::SmartGetPostAllImgFnd($sz,$szParams);
		$strImg1=$arrImg[0];

		$htm=self::GetMyImgAsZoom($strImg1,null);
		return $htm;
	}
	
	/**
	 * return <img> html, $strHome may be null
	 */
	static public function GetMyImgAsZoom($strImg1,$strHome)
	{
		$strTag=Funcs::SmartParseHtmlTag($strImg1,$arrAttribs,$bEmpty);
		$strImg1=$arrAttribs['src'];
		$strImg2=$arrAttribs['data-mybiggy'];
		/**
		 * unset($arrAttribs['data-mybiggy']);
		 * $imgHtm=Funcs::BuildHtmlTag($imgHtm,$arrAttribs,null,$bEmpty);
		 */

        /*$filt='<div class="easyzoom" data-inline-option="true">';
        //$filt='<div style="overflow:scroll;" class="easyzoom" >';
        $filt.="<a href='$strImg2'>";
        $filt.="<img src='$strImg1' alt='' />";
        $filt.="</a>";
		$filt.="</div>";*/

		if ($strHome)
		{
			$strHomeRt=get_home_url();
			$arrDir1=explode('/',$strImg1);
			$fn1=array_pop($arrDir1);
			$arrDir2=explode('/',$strImg2);
			$fn2=array_pop($arrDir2);
			$strImg1="{$strHome}files/bigsmall/$fn1";
			$strImg2="{$strHome}files/bigsmall/$fn2";
		}

		//strtotime
		//$a=get_the_time( 'U' );
		//$b=esc_attr( get_the_time() );
		$ti=time();
		/**
		 * $tii=esc_attr($ti);
		 */
		$strImg1="$strImg1?a=$ti";

		$filt="<img class='ElevateMe' data-zoom-image='$strImg2' src='$strImg1' alt='' />";
		return $filt;
	}

	/**
	 * replace reg ex
	 */
	static public function CvtHtmImgAsZoom($htm,$strHome)
	{
		$arrhtm=null;
		$inst=new RegReplaceAllImgs($htm,$arrhtm);
		$inst->SetHomeUrl($strHome);

		$mypat='/<img [^\>]*\ \/>/i';
		$cb=array($inst,'SmartReplaceImgCB');

		/**
		 * $counted=0;
		 */
		$html=preg_replace_callback($mypat,$cb,$htm);
		return $html;
	}

	/**
	 * replace reg ex
	 */
	static public function CvtHtmImgAsZoomNew($htm,$new)
	{
		$arrhtm=null;
		$inst=new RegReplaceAllImgs($htm,$arrhtm,$new);
		$mypat='/<img [^\>]*\ \/>/i';
		$cb=array($inst,'ReplaceMyImgWithNew');

		/**
		 * $counted=0;
		 */
		$html=preg_replace_callback($mypat,$cb,$htm);
		if ($arrhtm==null || count($arrhtm)==0)
			$html.=$new;
		return $html;
	}

	/**
	 * replace reg ex
	 */
	static public function CvtHtmImgAttachAsZoom($htm,$strHome)
	{
		$arrhtm=null;
		$inst=new RegReplaceAllImgs($htm,$arrhtm);
		$inst->SetHomeUrl($strHome);

		$mypat='/<img [^\>]*\ \/>/i';
		$cb=array($inst,'SmartReplaceImgAttachCB');

		/**
		 * $counted=0;
		 */
		$html=preg_replace_callback($mypat,$cb,$htm);
		return $html;
	}

	public static function IsShopAdmin($shop)
	{
		$rights=CardsByYou::GetUserRights();
		$ownid=$shop['shopownid'];
		$usr=wp_get_current_user();
		$usrid=$usr->ID;
		
		if ($rights==2 || ($rights==1 && self::IsShopUseIn($ownid,$usrid)) )
			return true;
		return false;
	}
	
	public static function IsShopUseIn($ownid,$usrid)
	{
		$arr=explode(',',$ownid);
		return in_array($usrid,$arr);
	}

	/**
	 * if failed
	 */
	static public function GetDefPage()
	{
		$arr=CardsByYou::GetMenusUrl();
		/**
		 * $slug=$arr['splithomeposts'];
		 * $slug=$arr['homeperpage'];
		 */
		$slug=$arr['mylogin'];
		
		$query_args = array(
			//'ID'      => 3,
			'post_type'      => 'page',
			/**
			 * 'title'		=>'Profile',
			 */
			//'name'		=>$slug,
			'name'		=>'@@@',
		);

		global $wp_query;
		$wp_query->query($query_args);
		the_post();
	}

	static public function GetSmartPgMetaKeys($strKey,$strVal,$strKey2,$strVal2) 
	{
		/**
		 * $results = new \WP_Query(
		 */

		$args=array(
			'post_status' => array('private','publish'),
			'post_type' => 'page',
			'nopaging' => true,
			'posts_per_page' => -1,
			//'s' => stripslashes($_ POST['search']),
			'meta_query' => array(
				'relation'=>"AND",
				array(
					'key' => $strKey,
					'value' => $strVal,
					'compare' => '=',
					//'type' => 'numeric',
					//'type' => 'bool'
				),
				array(
					'key' => '_wp_page_template',
					'value' => 'templates/pt-saveme-builder.php',
					'compare' => '=',
					//'type' => 'numeric',
					//'type' => 'bool'
				),
				array(
					'key' => $strKey2,
					'value' => $strVal2,
					'compare' => '=',
				)
			),
		);
		return $args;
	}

	static public function SetQuerySmart($smartslug,$pinm)
	{
		/**
		 * $despageid=DoPostTypes::GetGoodPageId($smartslug,$pinm);
		 */

		$args=self::GetSmartPgMetaKeys('_smartfilename',$smartslug,'_smartplugin',$pinm);

		///self::GetMetaKeyPages2(($smartslug,$pinm);
		
		global $wp_query;
		$wp_query->query($args);
		global $post;
		the_post();
	}	

	static public function GetButStyleOpt()
	{
		/**
		 * return 'background-color:blue';
		 */
		global $PagedPwa_pdtOptDesigner;
		//@@bugsmy14thMay24
		//$_offpapers=$opt['_offpapers'];
		if (!isset($PagedPwa_pdtOptDesigner['butbkclr']))
			return '';
		return 'background-color:'.$PagedPwa_pdtOptDesigner['butbkclr'];
	}

	static public function GetAllSmartPagesArgs() 
	{
		$args=array(
			'post_status' => array('private','publish'),
			'post_type' => 'page',
			'nopaging' => true,
			'posts_per_page' => -1,
			//'s' => stripslashes($_ POST['search']),
			'meta_query' => array(
				'relation'=>"AND",
				array(
					'key' => '_wp_page_template',
					'value' => 'templates/pt-saveme-builder.php',
					'compare' => '=',
					//'type' => 'numeric',
					//'type' => 'bool'
				),
			),
		);
		return $args;
	}


	static public function GetSubDirCardId($cardid,&$bisPer)
	{
		$po=get_post($cardid);
		$bisPer=false;
		if ($po->post_type=='cardpersonal')
		{
			$path="files/cust_{$cardid}_.pdt";
			$bisPer=true;
		}
		else
		{
			if ($po->post_type=='carddesign')
				$cardid=$po->post_content_filtered;
			$path="files/A_{$cardid}_";
		}
		return $path;
	}
	
	static public function IsPersonalInFile($percardid)
	{
		$po=get_post($percardid);
		$dsgid=$po->post_parent;
		$poDes=get_post($dsgid);
		$ptype=$poDes->post_type;
		$shop=Funcs::GetShopBy('posttype',$ptype);

		$bFileXmlPer=$shop['xmlcontentper']!='content'?true:false;
		if (!$bFileXmlPer)
		{
			/**
			 * shop says 'content'
			 */
			$strcontent=$po->post_mime_type;
			$bFileXmlPer=$strcontent!='content'?true:false;
			/**
			 * default blank=file
			 */
		}
		return $bFileXmlPer;
	}
	
	static public function IsDesignInFile($descardid)
	{
		$po=get_post($descardid);
		$ptype=$po->post_type;
		$shop=Funcs::GetShopBy('posttype',$ptype);
		$bFileXml=$shop['xmlcontent']!='content'?true:false;
		return $bFileXml;
	}



	/**
	 * wp_update_post, KEEP post_content
	 * return false as error
	 */
	static public function SafeUpdatePost($po)
	{
		global $wpdb;

		$strcontent=$po->post_content;
		$um=wp_update_post($po);

		/**
		 * N.b post_content GETS sanitised, so go direct:
		 */
		$rows=$wpdb->update( $wpdb->posts, array('post_content'=>$strcontent),array('ID'=>$po->ID));
		/**
		 * N.B. $rows=0 may not be an error if data update was done already 
		 */

		/**
		 * if ($rows<1)
		 * 	$rows=$wpdb->update( $wpdb->posts, array('post_content'=>$strcontent),array('ID'=>$po->ID));
		 */

		$strErr=$wpdb->last_error;

		//$rows=$wpdb->update( $wpdb->posts, array('to_ping'=>$strXml),array('ID'=>$newid));
		//$rows=$wpdb->update( $wpdb->posts, array('pinged'=>$strXml),array('ID'=>$newid));
		/**
		 * $postarr = sanitize_post( $postarr, 'db' );
		 * N.B. get_post() incorrectly wont retrieve these vals, yet because of cache!!!!!
		 */

		return $strErr==''?true:false;
	}

	/**
	 * smart query
	 */
	//userwpadminme
	static public function QuerySmartDataRecUsr($ptype,$slug,$strUserDir)
	{
		$dname=CardsByYou::GetDNameFromUser($strUserDir);
		return self::QuerySmartDataRecDName($ptype,$slug,$dname);
	}

	/**
	 * smart query
	 */
	//wpadminme
	static public function QuerySmartDataRecDName($ptype,$slug,$dname)
	{
		$usr=self::GetUsrRecFromDName($dname);
		if ($usr==null)
			return null;

		$userId=$usr->ID;

		$args = array(
			//'post_name'      => $slug,
			'post_title'      => $slug,
			//'status'      => 'private',
			//'ignore_sticky_posts'      => true,
			'post_status' => array('private','publish','draft'),
			'post_type'	=> $ptype,
			'post_author'	=> $userId,
		);
		$results=Funcs::SmartQueryArgs($args);
		if (count($results)!=1)
			return null;
		return $results[0];
	}

	static public function GetUsrRecFromDName($dname)
	{
		global $wpdb;
		$db_field='display_name';
		$sql=$wpdb->prepare(
			"SELECT * FROM $wpdb->users WHERE $db_field = %s LIMIT 1",	//---orgfixprep
			$dname
		);
		$usr = $wpdb->get_row($sql);
		return $usr;
	}





	
	/**
	 * return cls obj, or null
	 */
	static public function FindClassJsn(&$clses,$strCls)
	{
		foreach ($clses as $o)
		{
			$ctl=$o['ctl'];
			if ($ctl['class']==$strCls)
				return $o;
		}
		return null;
	}

	static function GetCtlClsType($mapCls)
	{
		$clsCtl=$mapCls['ctl'];
		if (isset($clsCtl['thewidget']) && $clsCtl['thewidget'])
			return 2;
		if (isset($clsCtl['htmlelem']) && $clsCtl['htmlelem'])
			return 0;
		return 1;
	}

	static public function FindClsProp(&$clsProps,$named)
	{
		foreach ($clsProps as $prop)
		{
			if ($prop['named']==$named)
				return $prop;
		}
		return null;
	}

	/**
	 * $arr is classy
	 * get newval from class => props
	 */
	static public function FindNewVal(&$ctl,&$arr,&$cls)
	{
		$clsProps=&$cls['props'];
		foreach ($clsProps as $i=>$p)
		{
			if (isset($p['newval']))
			{
				$val=$p['newval'];
				$strType=$p['type'];
				if ($strType=='bool')
				{
					$valis=$p['newval']==true;
					if (gettype($valis)=='boolean');
						$val=$val?"True":"False";
				}
				$k=$p['named'];
				$arr[$k]=$val;
				$ctl['props'][$k]=$val;	
			}
		}
	}

	/**
	 *  return id or 0 as not found
	 */
	static public function GetAppTypeId($FilterAppType)
	{
		$ptypeApp=CardsByYou::GetPTypeAppTypes();
		$args = array(
			'post_type'      => $ptypeApp,
			'posts_per_page' => - 1,
		);

		$metaq = array(
			'relation'=>"AND",
			array(
				'key' => 'named',
				'value' => $FilterAppType,
			)
		);		
		$args['meta_query']=$metaq;

		$nWantAppType=0;
		$query = new \WP_Query($args);
		$arr= $query->posts;
		if (count($arr)==1)
			$nWantAppType=$arr[0]->ID;
		return $nWantAppType;
	}

	static public function LosePrefix1($str,&$strUsr)
	{
        /**
         * "1_788_one"
         */
		$nUnd=strpos($str,'_');
        if ($nUnd===false || $nUnd<0)
            return $str;
        $strUsr=substr($str,0,$nUnd);
		return substr($str,$nUnd+1);
	}

	


	/**
	 * smart query
	 */
	static public function QuerySmartDataRecTitle($ptype,$slug,$userId)
	{
		$args = array(
			//'post_name'      => $slug,
			'post_title'      => $slug,
			//'status'      => 'private',
			//'ignore_sticky_posts'      => true,
			'post_status' => array('private','publish','draft'),
			'post_type'	=> $ptype,
			'post_author'	=> $userId,
		);
		$results=Funcs::SmartQueryArgs($args);
		if (count($results)!=1)
			return null;
		return $results[0];
	}

	//
	//////smart page support
	/**
	 * get page ctl by cls name
	 */
	static public function GetPageCtlClsData($pageid,$strKeyCtls,$clsis)
	{
		$ctlProps = get_post_meta( $pageid, $strKeyCtls, true );
		if (!$ctlProps)
			return null;

		$bFound=false;
		foreach ($ctlProps as $ctl)
		{			
			$clsid=$ctl['props']['_class'];
			if ($clsid==$clsis)
			{
				$ctlid=$ctl['props']['_ctlid'];
				if (self::IsPageCtlDataIdUsed($pageid,$ctlid))
				{
					$bFound=true;
					break;
				}
			}
		}
		if (!$bFound)
			return null;

		/**
		 * if ($clsid!=$clsis)
		 * 	return null;
		 */
		return $ctl;
	}

	/**
	 * get return $args array
	 * $shopbyuserid,$bSanitize  GONE
	 * $bFiltAuth filter on post_author
	 * $strStatus post_status 'all' ok, N.B. null NOT ok
	 * $argsSet merged in extra main args, null IS OK
	 */
	static public function GetLoadFastArgs($ptype,$strTax,$strTax2,$bFiltAuth,$arrTermid,
		$strStatus,$argsSet)
	{
		$usr=wp_get_current_user();
		$usrid=$usr->ID;

		if (!$strTax)
		{
			/**
			 * find cat for this ptype
			 */
			$arr=get_object_taxonomies($ptype);
			$nLen=count($arr);
			$strTax=$arr[$nLen-1];
		}

		$argsWhere = array(
			'post_type' 	=> $ptype,
			//'post_status'   => 'publish',
			//'post_status'   => $strStatus,
		);

		$strStati=strtolower($strStatus);
		if ($strStati=='any')
		{
			/**
			 * exclude trash
			 */
			$argsWhere['post_status!=']='trash';
		}
		else if ($strStati=='all')
		{
			/**
			 * leave it
			 */
			unset($argsWhere['post_status']);
		}
		else
		{
			$argsWhere['post_status']=$strStatus;
		}

		if ($bFiltAuth)
			$argsWhere['post_author']=$usrid;

		global $wpdb;
		$sqlFormat="select %select% from $wpdb->posts ".
			"%join% where %where% order by %order% limit %limit%";

		$args = array(
			'format'=>$sqlFormat,
			'where'=>$argsWhere,
			//'order'=>'post_modified asc',
			'order'=>'menu_order asc',
			'limit'=>'0,240',
		);

		if ($argsSet)
		{
			$args=array_merge($args,$argsSet);
		}

		//0 : NO terms, All posts
		//-1 : no cats
		if ($strTax && is_array($arrTermid) && $arrTermid[0]!=0)
		{
			if ($arrTermid[0]==-1)
			{
				/**
				 * NONE
				 */
				$terms = get_terms( array(
					'taxonomy' => $strTax,
					//'fields'   => 'id',
					'fields'   => 'id=>slug',
				) );
				$arrTermid=array();
				foreach ($terms as $k=>$v)
				{
					$arrTermid[]=$k;
				}

				$args['tax_query']=
					array(
						'outer'=>'left',
						'taxonomy'=>$strTax,
						//'notterms'=>$arrTermid,
						'terms'=>$arrTermid,
					);
			}
			else
			{
				$catid1=$arrTermid[0];
				$tax1=array(
					'taxonomy'=>$strTax,
					'terms'=>array($catid1),
					);

				///cat 1..
				$args['tax_query']=$tax1;

				if ($strTax2!=null && count($arrTermid)>1 && $arrTermid[1]!=0)
				{
					$catid2=$arrTermid[1];
					//cat2...
					$tax2=array(
						'taxonomy'=>$strTax2,
						'terms'=>array($catid2),
					);
					$args['tax2_query']=$tax2;
				}
			}
		}
		
		/*if ($useserverpaging)
		{
			//$args['limit']='0,100';
			$args['limit']="0,$nPageSize";
		}*/

		if ($strTax2)
			$taxnamed=$strTax2;
		else
			$taxnamed=$strTax;
		
		return $args;
	}



	/**
	 * args is top level inc 'meta_query'
	 */
	static public function GetActPubUser()
	{
		//alyssa/
		//ben/
		//ben2/
		$usrid = get_current_user_id();
		$userobj = get_userdata($usrid);
		
		$strUrl=$userobj->data->user_url;
		$domain=null;
		$proto='';
		$homepath=Funcs::GetMapUrl($strUrl,$proto,$domain);
		$actor=substr($strUrl,strlen($proto));
		return $actor;
	}

	/**
	 * $bApp true if app(PWA), false: web
	 */
	static public function GetUserConfig($bApp,$rec)
	{
		$authid=$rec->post_author;
		$user_obj = get_userdata($authid);
		//$bPWA=true;

		$strConfig=self::GetAppPageConfig($bApp,$rec);
		if (!$strConfig)
		{
			if ($bApp)
				$strConfig="pwainstall.json";
			else
				$strConfig="pwaweb.json";
		}
		return $strConfig;
	}

	/**
	 * return str fname, or null
	 */
	static public function GetAppPageConfig($bApp,$po)
	{
		$configFile=null;
		$appId=$po->post_parent;
		if ($appId>0)
		{
			/**
			 * not:
			 * pwaweb.json ||
			 * pwainstall.json
			 */
			if (!$bApp)
				$configFile=get_post_meta($appId,'configwidgets',true);
			else
				$configFile=get_post_meta($appId,'configpwa',true);
		}
		return $configFile;
	}

	static public function GetAccessibleHtml($ctl,$cls)
	{
		$clsCtl=$cls['ctl'];
		$clsProps=$cls['props'];
		$arrAttribs=array();
		foreach ($clsProps as $p)
		{
			if ($p['generator']!='buildspa')
				continue;

			$attr=$p['htmlattrib'];
			$nm=$p['named'];
			if ($ctl[$nm])
				$arrAttribs[]="$attr='{$ctl[$nm]}'";
		}
		$str=implode(' ',$arrAttribs);
		return $str;
	}
	

	static public function GetHomeRef()
	{
		//$host=$_SERVER['HTTP_HOST'];
		$home=get_home_url();
		////$parts=parse_url($home);
		//$domain=wp_parse_url($url,PHP_URL_HOST);
		$parts=wp_parse_url($home);
		$domainpath=$parts['host'];
		if (isset($parts['path']))
			$domainpath.=$parts['path'];

		//$ref="{$host}$home";
		$home=str_replace("/",'-',$domainpath);
		//$home=str_replace("/",'~',$home);

		return $home;
	}

	static public function GetHomeRefUrl($home)
	{
		//$parts=parse_url($home);
		//$domain=wp_parse_url($url,PHP_URL_HOST);
		$parts=wp_parse_url($home);
		$domainpath=$parts['host'].$parts['path'];

		//$ref="{$host}$home";
		$home=str_replace("/",'-',$domainpath);
		//$home=str_replace("/",'~',$home);

		return $home;
	}

	//return url without the trailing /
	static public function GetScriptByYouUrl()
	{
		$url="https://scriptbyyou.com";
		return $url;
	}

	static public function GetCustData()
	{
		$usr=wp_get_current_user();
		$oCustData=array();
		$oCustData['ocustuser']=$usr;
		$home=get_home_url();
		$oCustData['home']=$home;
		$oCustData['cookie']=isset($_COOKIE['wp-settings-1'])?$_COOKIE['wp-settings-1']:'';
		//wp-admin/admin-ajax
		$oCustData['ajax_url']=admin_url( 'admin-ajax.php');
		$oCustData['paid']=0;
		//$oCustData['lickey']=0;
        $periodictag=self::GetOption('periodictag');
		$oCustData['periodictag']=$periodictag;

        $offlinestrategy=self::GetOption('offlinestrategy');
		$oCustData['offlinestrategy']=$offlinestrategy;

		$rights=MyPWAAppyPlug::GetUserRights();
		$oCustData['rights']=$rights;
        $lickey=self::GetOption('lickey');
		$oCustData['lickey']=$lickey;
        $ServerKey=self::GetOption('ServerKey');
		$oCustData['ServerKey']=$ServerKey;
        $ServAuthJsn=self::GetOption('ServAuthJsn');
		$oCustData['ServAuthJsn']=$ServAuthJsn;
		$oCustData['parajax_url']=self::GetScriptByYouUrl()."/wp-admin/admin-ajax.php";
		//$oCustData['isproplugin']=is_plugin_active('PagedPWAPro/pwaappypro.php');

		$installed = false;
		if ( function_exists( 'is_plugin_active' ) ) 
		{
			$plugins = \get_plugins();
			$plugins = array_keys( $plugins );

			$pluginDir="pwappypro/pwaappypro.php";
			foreach ( $plugins as $plugin_path ) {
				if ( strpos( $plugin_path, $pluginDir ) === 0 ) 
				{
					$installed = true;
					break;
				}
			}
			if ($installed)
				$installed=\is_plugin_active($pluginDir);
		}
		$oCustData['isproplugin']=$installed;

		$kg=self::GetOption('GoogFBKey');
		$nMsgSysGoog=0;
		if ((!$lickey || $lickey!='') && $ServerKey==$kg)
			$nMsgSysGoog=1;
		$oCustData['MsgSysGoog']=$nMsgSysGoog;
		$shortname=self::GetOption('shortname');
		$oCustData['shortname']=$shortname;

        $SubscribeAuto=self::GetOption('SubscribeAuto');
		$oCustData['SubscribeAuto']=$SubscribeAuto;
        $SubscribeBut=self::GetOption('SubscribeBut');
		$oCustData['SubscribeBut']=$SubscribeBut;
        $start_url=self::GetOption('start_url');
		$oCustData['start_url']=$start_url;
        $SwVersion=self::GetOption('SwVersion');
		$oCustData['SwVersion']=$SwVersion;
        $SubscribeAnon=self::GetOption('SubscribeAnon');
		$oCustData['SubscribeAnon']=$SubscribeAnon;
		return $oCustData;
	}

	//
	static public function GetSafeDisplayName($user_obj)
	{
		$dname=$user_obj->data->display_name;
		return $dname;
	}

	/**
	 * define( 'WP_ADMIN', true );
	 */
	///for ajax calls meaning get_home_url(); default logic is broken for https
	static public function GetSafeHomeUrl()
	{
		$strDef=null;
		if (is_ssl())
			$strDef="https";
		$url=get_home_url(null,'',$strDef);
		return $url;
	}

	/**
	 * shops filter
	 * shopbyuserid is for shop filter accord to 'shopownid'
	 * 	set to true or 'True'
	 * bFiltAuth is for main recs author filtering
	 */
	static function LoadPTypeShopped($ptype,$shopbyuserid,$bFiltAuth)
	{
		$rights=MyPWAAppyPlug::GetUserRights();
		$usr=wp_get_current_user();
		$usrid=$usr->ID;

		$args = array(
			'post_type'      => $ptype,
			'posts_per_page' => - 1,
		);
		if ($bFiltAuth)
			$args['author']=$usrid;

		$query = new \WP_Query($args);

		$recs=array();
		foreach ($query->posts as $rec)
		{
			$o=self::LoadPostTypeRec($ptype,$rec,null);
			if ($shopbyuserid && $shopbyuserid=='True')
			{
				/**
				 * recs are shops
				 */
				$obj=Funcs::GetShopRecObj($rec->ID);
				$ownid=$obj['shopownid'];

				/**
				 * if ($rights==2 || ($rights==1 && $ownid==$usrid) )
				 */
				if ($rights==2 || ($rights==1 && Funcs::IsShopUseIn($ownid,$usrid)) )
					$recs[]=$o;
			}
			else
				$recs[]=$o;
		}
		return $recs;
	}

	/**
	 * Ajax call means define( 'WP_ADMIN', true );
	 *  add extra flds for meta data
	 * $taxnamed is used for _cats field post terms
	 * - may be nukk
	 */
	static public function LoadPostTypeRec($ptypeIn,&$rec,$taxnamed)
	{
		$pgid=$rec->ID;

		$authid=$rec->post_author;

		$user_obj = get_userdata($authid);
		/**
		 * $dname=$user_obj->data->display_name;
		 */
		$dname=self::GetSafeDisplayName($user_obj);

		/**
		 * $url=self::GetRouteUrl('userx');
		 */
		$adMin=is_admin();
		$isssl=is_ssl();
		$url=get_home_url();
		$url=self::GetSafeHomeUrl();
		
		$urlpage="$url/user{$dname}/$rec->post_title";
		$rec->runurl=$urlpage;

		/**
		 * $perma=home_url();
		 */
		$rec->permaurl=get_permalink($rec);

		$td=get_the_modified_date('',$rec);		
		$tt=get_the_modified_time('',$rec);
		$dt=sprintf('%1$s %2$s',
			$td,esc_attr( $tt ));
		$rec->dt=$dt;

		if (!$taxnamed)
		{
			/**
			 * $taxonomy_obj = get_taxonomy( $tax );
			 */
			$taxnamed='apppagecat';

			//get_post_type_capabilities
			/**
			 * $oos=get_post_type_object($ptype);
			 */
			$arr=get_object_taxonomies($ptype);
			/**
			 * N.B. ajax call WONT have these
			 */
			$nLen=count($arr);
			if ($nLen>0)
				$taxnamed=$arr[$nLen-1];
		}

		$o=(array)$rec;
		$cat=array();

		$arrTerms=wp_get_post_terms($rec->ID,$taxnamed);
		if ( ! is_wp_error( $arrTerms ) )
		{
			foreach ($arrTerms as $tm)
			{
				$cat[]=$tm;
				/**
				 * $tag=$tm->slug;
				 */
			}	
		} 
		$o['_cat']=$cat;
		return $o;
	}


	/**
	 * $piname is compulsory
	 */
	//$pi : number or str
	static public function GetGoodPageIdHm($pi,$piname,$reqpath) 
	{
		$umint=(int)$pi;
		$npostid=intval($pi);
		if ((string)$npostid!=$pi)
			$pi=self::GetPageSlugHm($pi,$piname,$reqpath);
		return $pi;
	}

	/**
	 * $piname is compulsory
	 */
	static public function GetPageSlugHm($strSlug,$piname,$reqpath) 
	{
		/**
		 * templates/pt-saveme-builder.php
		 */

		/**
		 * should always work
		 */
		$recs=self::GetMetaKeyPagesHm('_smartfilename',$strSlug,
				'_smartplugin',$piname,$reqpath);

		if (count($recs)==0)
		{
			$recs=self::GetMetaKeyPagesHm('_smartfilename',$strSlug,
				'_smartplugin',$piname,$reqpath);
			return null;
		}

		$rec=$recs[0];
		return $rec->ID;
	}

	//_smarthomey
	static public function GetMetaKeyPagesHm($strKey,$strVal,$strKey2,$strVal2,$reqpath) 
	{
		$results = new \WP_Query(array(
			'post_status' => array('private','publish'),
			'post_type' => 'page',
			'nopaging' => true,
			'posts_per_page' => -1,
			//'s' => stripslashes($_ POST['search']),
			'meta_query' => array(
				'relation'=>"AND",
				array(
					'key' => $strKey,
					'value' => $strVal,
					'compare' => '=',
					//'type' => 'numeric',
					//'type' => 'bool'
				),
				array(
					'key' => '_wp_page_template',
					'value' => 'templates/pt-saveme-builder.php',
					'compare' => '=',
					//'type' => 'numeric',
					//'type' => 'bool'
				),
				array(
					'key' => $strKey2,
					'value' => $strVal2,
					'compare' => '=',
				),
				array(
					'key' => '_smarthomey',
					'value' => $reqpath,
					'compare' => '=',
				)
			),
		));

		$recs=$results->posts;
		return $recs;
	}

	//$json : smart pg
	static public function CreateMyPageSmartHm($file,$piname,$json,$hmpg)
	{
		/**
		 * $titleslug="$file (Auto Page)";
		 */
		$titleslug="{$hmpg}_$file";
		$optrec=array();
		$optrec['post_name']=$titleslug;
		$optrec['post_title']=$file;
		$optrec['post_type']='page';
		//hmpg

		$pgid=self::CreateMyPageSmartOpt($file,$piname,$json,$dirPath,$optrec);
        $umid1=update_post_meta($pgid,'_smarthomey',$hmpg);
		return $pgid;
	}

	/**
	 * smart page & pagetag
	 * return new post id (or -1 as error)
	 */
	static public function CreateMyPageSmartOpt($file,$piname,$json,$dirPath,$optrec)
	{
		$arr=json_decode($json,true);

		if (isset($arr['extra']))
		{
			$xtra=$arr['extra'];
			if (!$optrec['post_name'] && isset($xtra['_postname']) && $xtra['_postname']!='')
				$optrec['post_name']=$xtra['_postname'];
			if (!$optrec['post_name'] && isset($xtra['_posttitle']) && $xtra['_posttitle']!='')
				$optrec['post_title']=$xtra['_posttitle'];
		}

		/**
		 * double // it
		 */
		$ctls=&$arr['instances'];
		if (!$ctls)
		{
			//-return -1;
			$ctls=array();
		}
		foreach ($ctls as &$ctl)
		{
			$p=&$ctl['props'];
			/**
			 * $p['_class']=addslashes($p['_class']);
			 */
			$p['_class']=self::DoubleBackslash($p['_class']);
			if (isset($p['Instance']))
				$p['Instance']=self::DoubleBackslash($p['Instance']);
		}
		/**
		 * $ctls1=$arr['instances'];
		 */

		//-$usrx=get_current_user();
		$usr=wp_get_current_user();
		$usrid=$usr->ID;
		
		/**
		 * $titleslug="$file (Auto Page)";
		 */
		$newid=self::UpdateMyPageSmart(0,$usrid,$optrec);

		$umid0=update_post_meta($newid,'_wp_page_template','templates/pt-saveme-builder.php');
		$umid2=update_post_meta($newid,'_smartfilename',$file);
        $umid1=update_post_meta($newid,'_smartplugin',$piname);
        

        /**
         * smart design page
         */
        $strLay=self::GetMyPageKeyLayPlug();
		$strKeyCtls=self::GetMyPageKeyCtls();
		$strKeyExtra=self::GetMyPageKeyExtra();
        
		$str1=$strLay;
		$str2=$strKeyCtls;
		$str3=$strKeyExtra;
		/**
		 * $arr=json_decode($json,true);
		 */

		/*$o0 = update_post_meta( $newid, $str1, $arr['layout'] );
		$o1 = update_post_meta( $newid, $str2, $arr['instances'] );
		$o2 = update_post_meta( $newid, $str3, $arr['extra'] );
		$ob=get_post_meta($newid,$str2,true);*/

		add_post_meta( $newid, $str1, $arr['layout'], true );

		$ins=$arr['instances'];
		//$json=json_encode($ins);
		//$strNew=addslashes($json);
		//$insNew=json_decode($strNew,true);
		add_post_meta($newid, $str2, $ins, true);

		$ob1=get_post_meta($newid,$str2,true);
		$ob2=get_post_meta($newid,$str2,false);

		add_post_meta( $newid, $str3, $arr['extra'], true );
		$ob=get_post_meta($newid,$str2,true);


        //@@
        $xtr=$arr['extra'];
        $pp=$xtr['pageprops'];
        $pl=isset($pp['PageList'])?$pp['PageList']:null;
        $gt=taxonomy_exists('pagetag');
        if (!$gt || !$pl)
	        return $newid;

        /**
         * does term exist
         * $tms=get_terms('pagetag');
         */
        $tmfnd = get_term_by('slug', $pl, 'pagetag');
        if (!$tmfnd)
        {
            /**
             * create term
             */
            $term_new = wp_insert_term($pl,'pagetag', array(
                'name' => $pl,
                'slug' => $pl,
                'parent' => 0,
            ));
            $termid=$term_new['term_id'];
        }
        else
        {
            $termid=$tmfnd->term_id;
        }

        /**
         * wp_set_object_terms($newid,);
         */
        $arrFnd=wp_get_post_terms($newid,'pagetag');
        /**
         * 72 post types
		 * 73 routes
         */
		$sg=null;
		if (is_array($arrFnd) && count($arrFnd)>0)
		{
        	$tm=$arrFnd[0];
			$sg=$tm->slug;
		}
        if (!$sg)
        {
            /**
             * assoc with post
             */
            $rt=wp_set_post_terms($newid,$termid,'pagetag',false);
            $arrFnd0=wp_get_post_terms($newid,'pagetag');
        }

        return $newid;
	}

	static public function UpdateMyPageSmart($usepostid,$usrid,$optrec) 
	{
		$rec=array();
		$rec['ID']=$usepostid;
		$rec['post_author']=$usrid;
		/**
		 * $rec['post_date']='';
		 * $rec['post_date_gmt']='';
		 */
		///////$rec['post_content']=$strIds;
		/**
		 * $rec['post_content_filtrerd']='';
		 * $rec['post_title']=$strCtlUId;
		 */
		
		/**
		 * $rec['ping_status']=$prodcode;
		 * $rec['post_excerpt']=$txcode;
		 */
		$rec['post_status']='publish';
		/**
		 * $rec['post_status']='private';
		 */
		$rec['post_type']='page';
		//$rec['comment_status']='';
		/**
		 * $rec['ping_status']='';
		 * $rec['post_password']='';
		 * $rec['post_name']='';
		 * $rec['to_ping']='';
		 * $rec['pinged']='';
		 */
		$rec['post_modified']='';
		$rec['post_modified_gmt']='';
		$rec['post_parent']=0;
		$rec['menu_order']=0;

		foreach ($optrec as $k=>$v)
		{
			$rec[$k]=$v;
			//$rec['post_name']=$titleslug;
			//$rec['post_title']=$titleslug;
		}

		/**
		 * $rec['post_mime_type']=$price;
		 */

		/**
		 * $rec['guid']=$strCtlUId;
		 * $rec['post_category']='';
		 * $rec['tags_input']='';
		 * $rec['tax_input']='';
		 * $rec['meta_input']='';
		 */
		$newid=wp_insert_post($rec);
		return $newid;
	}

	static public function DoubleBackslash($str)
	{
		$str2='';
		$nLen=strlen($str);
		for ($i=0;$i<$nLen;$i++)
		{
			$chr=$str[$i];
			if ($chr=="\\")
				$str2.=$chr;
			$str2.=$chr;
		}
		return $str2;
	}
	
	static function GetMyPageKeyLayPlug()
	{
		return self::m_strKeyFor;
	}

	static function GetMyPageKeyCtls()
	{
		return self::m_strKeyCtls;
	}

	static function GetMyPageKeyExtra()
	{
		return self::m_strKeyExtra;
	}



	static public function GetPushMsgSys()
	{
        $ServerKey=self::GetOption('ServerKey');
		if (!$ServerKey || $ServerKey=='')
			return 0;
		$kg=self::GetOption('GoogFBKey');
		$nMsgSysGoog=0;
		if ($ServerKey==$kg)
			$nMsgSysGoog=1;
		return $nMsgSysGoog;
	}

	static public function PostStr($v,$def,$bMulti=false)
	{
		$v=isset($_POST[$v])?$_POST[$v]:$def;
		//return sanitize_key($v);
		if ($bMulti)
			return sanitize_textarea_field($v);
		//return sanitize_title($v);
		return sanitize_text_field($v);
	}

	static public function PostUrl($v0,$def)
	{
		if (!isset($_POST[$v0]))
			return $def;
		$v=isset($_POST[$v0])?$_POST[$v0]:$def;
		//@@bugsmy14thMay24
		//return sanitize_url($v);
		$abs=isset($v[0]) && $v[0]=='/';
		if (!$abs)
		{
			//$v1=esc_url("/".$v);
			//@@bugsmy30June24
			$v1=sanitize_url("/".$v);
			return substr($v1,1);
		}
		return sanitize_url($v);
	}

	static public function PostHtm($v,$def,$filt=null)
	{
		$v=isset($_POST[$v])?$_POST[$v]:$def;
		if ($filt==null)
			$v=wp_kses_post($v);
		else
			$v=wp_kses($v,$filt);
		return $v;
	}
	
	static public function PostJs($v0,$def,$bIsStr=false)
	{
		$v=isset($_POST[$v0])?$_POST[$v0]:$def;
		//@@bugsmy14thMay24
		if ($v===$def)
			return $def;

		if (!$bIsStr)
		{
			$v=wp_json_encode($v);
			$v=json_decode($v,true);
		}
		else
			$v=esc_js($v);
		return sanitize_textarea_field($v);
	}

	static public function FilesVal($v0,$def)
	{
		$v=isset($_FILES[$v0])?$_FILES[$v0]:$def;
		//@@bugsmy14thMay24
		if ($v===$def)
			return $def;

		if (is_string($v))
			return sanitize_textarea_field($v);

		return sanitize_textarea_field($v);
	}

	static public function EcStr($str)
	{
		$str0=esc_html($str);//@@bugsmy14thMay24
		echo $str0;
	}

	static public function EcJs($js,$bPretty=false)
	{
		echo esc_js($js);
		//@@bugsmy14thMay24
	}
	
	static public function EcOut($str)
	{
		self::EcAsc($str);		
	}
	
	static public function EcHtm($str)
	{
		Funcs2::MyKss1($str);
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
		Funcs2::MyKss1($str,['script'=>['defer','src','type']]);
	}

	static public function EcOption($str)
	{
		Funcs2::MyKss1($str,['option'=>['value','selected']]);
	}

	static public function EcInp($str)
	{
		//$allowed_html = wp_kses_allowed_html( $allowed_html );

		//$str=self::MyKss($str, ['input']);
 		//echo $str;

		//echo wp_kses_post($str);
		Funcs2::MyKss1($str);
	}

	static public function EcIFr($str)
	{
 		//echo self::MyKss($str, ['iframe']);
		//echo wp_kses_post($str);
		Funcs2::MyKss1($str,['iframe'=>['style','src']]);
	}

	static public function EcTag($str,$tags)
	{
		//if (is_string($tags))
		//	$tags=[$tags];
 		//echo self::MyKss($str, $tags);
		//echo wp_kses_post($str);
		Funcs2::MyKss1($str);
	}

	static public function EcTok($str)
	{
		self::EcAsc($str);
	}
	
	static public function EcAsc($str)
	{
		$str0=esc_textarea($str);
		if ($str0!=$str)
			$str0=$str0;
		echo $str0;
	}

    static private function GetOption($nm)
    {
        $nm="pagedpwa_$nm";
        return get_option($nm);
    }

	static public function GetServUrl($str0)
	{
		$str=$_SERVER[$str0];
		$str0=sanitize_url($str);
		return $str0;
	}

	static public function GetServName($str0)
	{
		$str=$_SERVER[$str0];
		$str0=sanitize_text_field($str);
		return $str0;
	}
}

class RegReplaceAllImgs
{
	public $m_arrhtm=null;
	public $m_htm=null;
	public $m_newhtm=null;
	public $m_strHome=null;

	function __construct($htm,&$arrhtm,$newhtm=null)
	{
		$this->m_htm=$htm;
		$this->m_arrhtm=&$arrhtm;
		$this->m_newhtm=$newhtm;
	}

	public function SetHomeUrl($strHome)
	{
		$this->m_strHome=$strHome;
	}

	/**
	 * $arrIn
	 * replace myimg in htm with zoom img
	 */
	public function SmartReplaceImgCB($matches)
	{		
		$img=$matches[0];
		
		/**
		 * $newtag=SmartDesign_IntelPages::ReplaceImgUrl2Base64($img);
		 */
		$newtag=Funcs::GetMyImgAsZoom($img,$this->m_strHome);

		$this->m_arrhtm[]=$newtag;
		return $newtag;
	}

	/**
	 * replace myimg in htm with new zoom img
	 */
	public function ReplaceMyImgWithNew($matches)
	{		
		$img=$matches[0];
		
		/**
		 * $newtag=SmartDesign_IntelPages::ReplaceImgUrl2Base64($img);
		 */

		$this->m_arrhtm[]=$this->m_newhtm;
		return $this->m_newhtm;
	}

	/**
	 * $arrIn
	 * replace myimg in htm with zoom img
	 */
	public function SmartReplaceImgAttachCB($matches)
	{		
		$img=$matches[0];
		
		/**
		 * $newtag=SmartDesign_IntelPages::ReplaceImgUrl2Base64($img);
		 */
		$newtag=self::GetMyImgAttachAsZoom($img,$this->m_strHome);
		if (!$newtag)
			$newtag=$img;

		$this->m_arrhtm[]=$newtag;
		return $newtag;
	}

	/**
	 * return <img> html, $strHome may be null
	 */
	static public function GetMyImgAttachAsZoom($strImg1,$strHome)
	{
		$strTag=Funcs::SmartParseHtmlTag($strImg1,$arrAttribs,$bEmpty);
		$strImg1=$arrAttribs['src'];
		$pgid=$arrAttribs['data-attachid'];
		if (!$pgid)
			return $strImg1;

		$po=get_post($pgid);

		$url=wp_get_attachment_url($pgid);
		$meta = wp_get_attachment_metadata($pgid);

		//$size="thumbnail";
		/*if ($sz)
			$size=$sz;
		else
			//$size="AppyAppsIcon256";
			$size="thumbnail";*/
		$icon=false;
		$size="thumbnail";

		/**
		 * if historically was year/month
		 */
		$strSubMonth=$meta['file'];
		$dirs=explode('/',$strSubMonth);
		array_pop($dirs);
		$strSubParts=implode('/',$dirs);

		$imageSrc = wp_get_attachment_image_src($pgid, $size, $icon);
		$url0=$imageSrc[0];
		$urlwdt=$imageSrc[1];
		$urlhgt=$imageSrc[2];

		$f=$meta['sizes'][$size]['file'];
		if (!$f)
		{
			//$size="thumbnail";
			//$f=$meta['sizes'][$size]['file'];
			$f=$meta['file'];
			$mime = get_post_mime_type($pgid);
		}
		else
			$mime=$meta['sizes'][$size]['mime-type'];

		$size="large";
		$imageSrc1 = wp_get_attachment_image_src($pgid, $size, $icon);
		$url1=$imageSrc1[0];

		$filt="<img class='ElevateMe' data-zoom-image='$url1' src='$url0' alt='' />";
		return $filt;
	}

	// N.B  class is RegReplaceAllImgs


}
class Funcs2
{
	static public function EcHtm2($str)
	{
		$str0=wp_kses_post($str);
		if ($str0!=$str)
		{
			$str0=self::MyKss2($str, ['meta']);
			if ($str0!=$str)
				$str0=$str0;
		}
		echo $str0;
	}

	static public function GetKssMap($str,&$map)
	{
		$bEmpty=false;
		$tg=null;

		$tg=Funcs::SmartParseHtmlTag($str,$arrAttribs,$bEmpty);
		if (!$arrAttribs)
		{
		$tg=Funcs::SmartParseHtmlTag($str,$arrAttribs,$bEmpty);
			$arrAttribs=[];
		}
		$map2=[];
		foreach ($arrAttribs as $k=>$v)
		{
			$map2[$k]=true;
		}
		$map[$tg]=$map2;
	}

	static public function MyKss1($str,$arrTags=null)
	{
		$map = wp_kses_allowed_html('post');
		self::GetKssMap($str,$map);
		if ($arrTags)
		{
			foreach ($arrTags as $k=>$v)
			{
				$map2=[];
				foreach ($v as $v2)
				{
					$map2[$v2]=true;
				}
				$map[$k]=$map2;
			}
		}

		$str0=wp_kses($str, $map);
		if ($str0!=$str)
		{
			$str0=$str0;
		}
 		echo $str0;
		return $str0;
	}

	static public function GetSessVar($str)
	{
		$str=$_SESSION[$str];
		$str0=esc_textarea($str);
		if ($str0!=$str)
		{
			$str0=$str0;
		}
		return $str0;
	}

	static public function GetServVar($str0)
	{
		$str=$_SERVER[$str0];
		$str0=esc_textarea($str);
		if ($str0!=$str)
		{
			$str0=$str0;
		}
		return $str0;
	}

}
