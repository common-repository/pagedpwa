<?php
namespace SmartIntelPWAAppy;

class PostTypeAdmin 
{
	private $m_pi=null;
	private $m_strPostType='';
	private $m_strShowName='';
	private $m_pobj=null;
	private $m_strRoute='';
	private $m_arrTaxonomy=array();
	protected $m_bPgSlugJsnFile=false;
	protected $m_strDir=null;

	public function SetSlugJsnFile($bSet)
	{
		$this->m_bPgSlugJsnFile=$bSet;
	}

	public function SetDir($strDir)
	{
		$this->m_strDir=$strDir;
	}

	public function RegisterHooks($bInInit)
	{
		$ptype=$this->m_strPostType;

		if ($bInInit)
			$this->reg_custom_post_type();
		else
			add_action('init', array($this,'reg_custom_post_type'));

		if (isset($this->m_pobj['admincusttaxes']) && $this->m_pobj['admincusttaxes']!=null)
		{
			if ($bInInit)
				$this->reg_custom_tax();
			else
				add_action('init', array($this,'reg_custom_tax'));
		}


		if (isset($this->m_pobj['adminpostmsgs']) && $this->m_pobj['adminpostmsgs']!=null)
			add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );


		add_filter( 'manage_' . $ptype . '_posts_columns',
			array( $this, 'column_headers' ) );

		add_action( 'manage_' . $ptype. '_posts_custom_column',  
			array( $this, 'column_data' ), 10, 2 );

		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );
		add_filter( 'page_row_actions', array( $this, 'row_actions' ), 15, 2 );

		///add_filter( 'manage_edit-' . $ptype. '_columns',                                     
		///	array( $this, 'my_events_columns' ));

		add_filter( 'manage_edit-' . $ptype. '_sortable_columns',                                     
			array( $this, 'my_column_register_sortable' ));

		add_action( 'pre_get_posts',array( $this, 'datatype_columns_orderby' ));

		// Topic metabox actions
		add_action( 'add_meta_boxes', array( $this, 'attributes_metabox'      ) );
		//@@add_action( 'save_post',      array( $this, 'attributes_metabox_save' ) );

		// Check if there are any appy_toggle_topic_* requests on admin_init, also have a message displayed

		// Add ability to filter topics and replies per forum

		add_filter( 'restrict_manage_posts', array( $this, 'filter_cars_by_taxonomies'),10,2  );

		///add_filter( 'parse_query', array($this,'sort_movie_by_meta_value') );

		add_filter( 'restrict_manage_posts', array( $this, 'restrict_manage_mycombos'),10,2  );
		add_filter( 'parse_query', array($this,'filter_manage_mycombos') );
	}

	public function GetAdminRow() 
	{
		return $this->m_pobj;
	}

	static public function GetNameSpace() {
		return __CLASS__;
	}

	public function GetPostType() {
		return $this->m_strPostType;
	}

	public function GetPlugIn() {
		return $this->m_pi;
	}

	public function IsRouteType() {
		return $this->m_strRoute==''?false:true;
	}

	public function __construct($pobj,$ptype,$showname,$route,$pi)
	{
		$this->m_pi=$pi;
		$this->m_pobj=$pobj;
		$this->m_strPostType=$ptype;
		$this->m_strShowName=$showname;
		$this->m_strRoute=$route;
	}

	public function IsPageTypeLevel($nCount)
	{
		if ($this->m_pobj['level']==$nCount)
			return $this->m_pobj['level'];
		return -1; 
	}

	public function GetLevel()
	{
		return $this->m_pobj['level'];
	}
	
	public function IsPageType($pType)
	{
		if ($this->m_strPostType==$pType)
			return true;
		return false; 
	}
	
	public function GetRealType()
	{
		return $this->m_pobj['realtype'];
	}
	
	public function RegisterRowActions()
	{
		$this->m_pobj['rowactions']=array();

		$strKeyExtra='philintelextra';
		$strKeyCtls=Funcs::m_strKeyCtls;
		$ptid=$this->GetPageSlugId();
		$ctlfnd=$this->GetPageCtlClsData($ptid,$strKeyCtls,'MyPageTypeRowActCtl');
		if ($ctlfnd==null)
			return;

		$json=$ctlfnd['props']['Settings'];
		$vals=json_decode($json,true);

		$this->m_pobj['rowactions']=$vals;
	}
	
	public function RegStdPostType($bInInit)
	{
		$pi=$this->m_pi;

		////-	$ndebug=21;

		$this->RegisterBoxesData();
		$this->RegisterRowActions();
		$this->RegisterAdminCombos();
		$this->RegisterCustTaxJson();

		$this->RegStdHooks($bInInit);
	}

	public function RegStdHooks($bInInit)
	{
		$ptype=$this->m_strPostType;

		$ptid=$this->GetPageSlugId();

		switch ($ptype)
		{
			case 'user':
				add_filter( 'user_row_actions', array( $this, 'row_actions_userstd' ), 10, 2 );
				break;
			default:
				add_filter( 'post_row_actions',array( $this, 'row_actions_std' ), 10, 2 );
				add_filter( 'page_row_actions', array( $this, 'row_actions_std' ), 15, 2 );
				break;
		}

		if ($this->m_pobj['admincusttaxes']!=null)
		{
			if ($bInInit)
				$this->reg_custom_tax();
			else
				add_action('init', array($this,'reg_custom_tax'));
		}
		if ($ptype!='page')
			add_filter( 'restrict_manage_posts', array( $this, 'filter_cars_by_taxonomies'),10,2  );
		else
			add_filter( 'restrict_manage_posts', array( $this, 'filter_cars_by_taxonomies'),10,2  );

		add_filter( 'restrict_manage_posts', array( $this, 'restrict_manage_stdcombos'),10,2  );
		add_filter( 'parse_query', array($this,'filter_manage_mycombos') );


		add_filter( 'manage_' . $ptype . '_posts_columns',
			array( $this, 'column_headers' ) );
		add_action( 'manage_' . $ptype. '_posts_custom_column',  
			array( $this, 'column_data' ), 10, 2 );

		add_action( 'add_meta_boxes', array( $this, 'attributes_metabox'      ) );
		//@@add_action( 'save_post',      array( $this, 'attributes_metabox_save' ) );
	}
	


	public static function GetPage()
	{
		return "index.php";
	}
	
	
	public function GetPageSlug()
	{
		return $this->m_pobj['pageslug'];
	}
	
	public function GetPageSlugId()
	{
		$strSlug=$this->GetPageSlug();
		if (is_numeric($strSlug))
			return $strSlug;

		///~@$piname=MenusAdmin::GetPlugName($this->m_pobj);

		$pg=Funcs::GetPageSlug($strSlug,$piname);
		if (!$pg)
		{
			$pg=Funcs::GetPageSlug($strSlug,$piname);
			$pg=$pg;
		}
		return $pg;
	}
	
	public function RegisterAdminCols()
	{
		$strKeyCtls=Funcs::m_strKeyCtls;
		$ptid=$this->GetPageSlugId();
		$ctlfnd=$this->GetPageCtlClsData($ptid,$strKeyCtls,'MyPageTypeAdColsCtl');
		if ($ctlfnd==null)
			return;
		
		$json=$ctlfnd['props']['Settings'];
		$vals=json_decode($json,true);
		$this->m_pobj['admincols']=$vals;
	}
	
	public function RegisterAdminCombos()
	{
		$strKeyCtls=Funcs::m_strKeyCtls;
		$ptid=$this->GetPageSlugId();
		$ctlfnd=$this->GetPageCtlClsData($ptid,$strKeyCtls,'AdminFilterCtl');
		if ($ctlfnd==null)
			return;

		$json=$ctlfnd['props']['Settings'];
		$vals=json_decode($json,true);
		$this->m_pobj['admincombos']=$vals;
	}
	
	public function RegisterBoxesData()
	{
		global $post;

		$ptype=$this->m_strPostType;
		$strKeyExtra='philintelextra';

		$strKeyCtls='philintel';
		$ptid=$this->GetPageSlugId();

		$ctlfnd=$this->GetPageCtlClsData($ptid,$strKeyCtls,'MyPageTypeBoxesCtl');
		if ($ctlfnd==null)
			return;

		$json=$ctlfnd['props']['Settings'];
		//$jsnClsAllb='{'.$jsnClsAll.'}';
		$vals=json_decode($json,true);

		$i=0;
		$arrGood=array();
		foreach ($vals as &$bx)
		{
			$bxpgid=$bx['pageid'];
			if (!$bxpgid)
				continue;

			$forcat=$bx['forcat'];
			if ($forcat!='')
			{
				$bx['dbkey']=$bx['tag'];
				//$bx['dbkey']=$dbkey;
			}
			else
			{
				$pgid=$bxpgid;

				$pgid=$this->GetGoodPageId($pgid);
				/*$pinm=$this->m_pi->GetPlugName();
				$pgid=Funcs::GetGoodPageId($pgid,$pinm);
				if (!$pgid)
				{
					$pgid=Funcs::GetGoodPageId($bxpgid,$pinm);
				}*/

				$extra = get_post_meta( $pgid, $strKeyExtra, true );
				$dbkey=$extra['pageprops']['StoreKey'];
				if (!$dbkey)
					$dbkey=$bx['tag'];
				$bx['dbkey']=$dbkey;
			}

			$arrGood[]=$bx;
			$i++;
		}		
		$this->m_pobj['metaboxes']=$arrGood;
	}
	
	public function RegisterTypeFromArr()
	{
		$post_type=$this->m_strPostType;
		$jsonObj=
		["labels"=> 
			[
			  "name"=>"##yourname##",
			  "singular_name"=>"##yourname##",
			  "menu_name"=>"##yourname##",
			  "all_items"=>"All ##yournames##",
			  "parent_item"=>"Parent ##yourname##",
			  "parent_item_colon"=>"Parent ##yourname##:",
			  "new_item_name"=>"New ##yourname## Name",
			  "add_new_item"=>"Add New ##yourname##",
			  "edit_item"=>"Edit ##yourname##",
			  "update_item"=>"Update ##yourname##",
			  "view_item"=>"View Item",
			  "separate_items_with_commas"=>"Separate ##yournames## with commas",
			  "add_or_remove_items"=>"Add or remove ##yournames##",
			  "choose_from_most_used"=>"Choose from the most used ##yournames##",
			  "popular_items"=>"Popular Items",
			  "search_items"=>"Search ##yournames##",
			  "not_found"=>"Not Found",
			  "no_terms"=>"No items",
			  "items_list"=>"Items list",
			  "items_list_navigation"=>"Items list navigation"
			],
			"hierarchical"=>true,
			"public"=>true,
			"rewrite"=>["slug"=>"myforums/forum"],
			"supports"=> 
			[
			  "title",
			  "editor",
			  "excerpt"
			],
			"taxonomies"=>["category", "post_tag"],
			"show_ui"=>true,
			"show_admin_column"=>true,
			"show_in_nav_menus"=>true,
			"show_tagcloud"=>true
		];

		$json=json_encode($jsonObj);

			
//"capabilities":'##capis_edit_posts##',
		$real=$this->m_pobj['realtype'];
		$named=$this->m_pobj['named'];
		if (!$named)
			$named=$real;
		$json2=str_replace("##yourname##",$real,$json);
		$jsonNow=str_replace("##yournames##",$named,$json2);

		$vals=json_decode($jsonNow,true);
		$caps=isset($this->m_pobj['capability'])?$this->m_pobj['capability']:null;
		if ($caps)
		{
			$vals['capabilities']=array('edit_post' => $caps,'edit_posts' => $caps,
				'delete_post' => $caps,'edit_others_posts' => $caps,
				'publish_posts' => $caps,'read_private_posts' => $caps,
				'read_post'=>$caps);

			////--uh?-$vals['capability_type']=array();
			$vals['map_meta_cap']=false;
		}

		$slug=$this->m_pobj['routed'];
		if ($slug)
			$vals['rewrite']=array("slug"=>$slug);
		else
			unset($vals['rewrite']);

		$myicon=$this->m_pobj['myicon'];
		if ($myicon)
		{
			//$url=PTS_POSTTYPE_URI."assets/left - Copy.jpg";
			if (substr($myicon,0,1)=='/')
			{
				$dirs=explode('/',PAGEDPWA_MYPWAAPPYPLUGAPP_URI);
				array_pop($dirs);
				array_pop($dirs);

				$pinm=$this->m_pi->GetPlugName();
				array_push($dirs,$pinm);
				array_push($dirs,$myicon);
				$url=implode('/',$dirs);

				$vals['menu_icon']=$url;
			}
			else
				$vals['menu_icon']=$myicon;

			//$icon="&#x1F354";
			//$icon='&#xFE0F';
		}
		$this->m_pobj['adminregtype']=$vals;
	}
	
	public function RegisterRegTypeData()
	{
		$arr=$this->RegisterRegTypeJson();
		if ($arr)
			return $arr;

		$this->RegisterTypeFromArr();
	}

	public function RegisterRegTypeJson()
	{
		$strKeyCtls=Funcs::m_strKeyCtls;
		$ptid=$this->GetPageSlugId();
		$ctlfnd=self::GetPageCtlDataId($ptid,$strKeyCtls,'reg_type');
		if ($ctlfnd==null)
			return null;

		$json=$ctlfnd['props']['MyJson'];
		$vals=json_decode($json,true);
		$this->m_pobj['adminregtype']=$vals;
		return $vals;
	}
	

	public function RegisterCustTaxJson()
	{
		$ptype=$this->m_strPostType;
		$strKeyCtls=Funcs::m_strKeyCtls;
		
		$ptid=$this->GetPageSlugId();
		if ($ptid==2642)
		$ndebug=21;
		///$ptid=$this->GetPageId();

		$arrctl=self::GetPageCtlsDataId($ptid,$strKeyCtls,'cust_tax');
		$this->m_pobj['admincusttaxes']=null;
		if ($arrctl==null)
			return;

		$arrCustTaxes=array();
		foreach ($arrctl as $ctl)
		{
			$o=array();
			$json=$ctl['props']['MyJson'];
			$vals=json_decode($json,true);
			$o['admincusttax']=$vals;
			$o['admincusttaxtypes']=null;
			$strTypes=$ctl['props']['PostTypes'];
			if ($strTypes)
			{
				$arrTypes=explode(',',$strTypes);
				$o['admincusttaxtypes']=$arrTypes;
			}
			$arrCustTaxes[]=$o;
		}
		$this->m_pobj['admincusttaxes']=$arrCustTaxes;
	}

	public function IsAdminScrOK()
	{
		$lo=is_user_logged_in();
		if (!$lo)
			return false;

		$cap=$this->m_pobj['adminrole'];
		if (!$cap)
			return true;

		if (!is_admin())
			return false;

		$priv=current_user_can($cap);

		$user_id = get_current_user_id();
		$userdata = get_userdata( $user_id );
		$allcaps = $userdata->allcaps;
		if ($allcaps[$cap])
			return true;

		return false;
	}


	public function reg_acustom_tax($args,$arrExTypes)
	{
		$tax=$args['labels']['singular_name'];
		if ($tax=='')
			$tax=$tax;
			

		$tax=strtolower($tax);
		
		$ptype=$this->m_strPostType;
		$ptid=$this->GetPageSlugId();

		$this->m_arrTaxonomy[]=$tax;

		$arrTypes=array($ptype);
		$taxonomy_obj = get_taxonomy( $tax );
		if ($taxonomy_obj && is_array($taxonomy_obj->object_type))
		{
			$arrTypes=array_merge($taxonomy_obj->object_type,$arrTypes);
			$arrTypes=array_unique($arrTypes);
		}
		if ($arrExTypes)
		{
			$arrTypes=array_merge($arrTypes,$arrExTypes);
			$arrTypes=array_unique($arrTypes);
		}
		register_taxonomy($tax,$arrTypes,$args);
		$taxonomy_obj = get_taxonomy( $tax );

		

		/*$terms = get_terms( array(
			'taxonomy' => $tax,
			'fields'   => 'id=>slug',
		) );
		$t=get_terms($tax);
		if ($tax=='smartsidefields')
		{
			$t2=$t;
			$t=get_terms($tax);
		}*/
	}

	public function reg_custom_post_type()
	{
		if (!self::WantAdminSys())
			return;

		$post_type=$this->m_strPostType;
		$bWantIt=true;

		$args=$this->m_pobj['adminregtype'];
		$args['public']=true;
		$args['show_ui']=true;
		$args['show_in_nav_menus']=true;
		$args['show_in_admin_bar']=true;
		$args['menu_position']=null;
		/*
		//$args['show_in_menu']=null;
		$args['show_in_menu']='tools.php';
		//$args['show_in_menu']='about.php';
		//$args['show_in_menu']='admin.php?page=Paged-PWA';
		//$args['show_in_menu']='options-general.php';
		*/

		$um=register_post_type($post_type,$args);
	}
	

	public function attributes_metabox() 
	{
		$metaboxes=&$this->m_pobj['metaboxes'];
		if ($metaboxes==null)
			return;

		global $post;
		$ptype=$post->post_type;
		if ($ptype!=$this->m_strPostType)
			return;

		if (!$this->IsAdminScrOK())
			return;

		$pos=0;
		$metaid='';
		foreach ($metaboxes as &$bx)
		{
			//$bx['page id'];
			$forcat=$bx['forcat'];
			if ($forcat!='')
				$debug=21;

			$bxtitle=$bx['title'];
			$metaid="metabx_{$bx['pageid']}_$pos";
			$bx['refid']=$metaid;
			$horz=$bx['horz']?$bx['horz']:'side';
			$vert=$bx['vert']?$bx['vert']:'high';
			add_meta_box (
				$metaid,
				$bxtitle,
				array($this,'appy_topic_metabox_html'),
				$this->m_strPostType,
				$horz,
				$vert
			);
			$pos++;
		}
	}

	private function FindMetaBoxById($id) 
	{
		$metaboxes=$this->m_pobj['metaboxes'];
		foreach ($metaboxes as $bx)
		{
			if ($bx['refid']==$id)
				return $bx;
		}
		return null;
	}
	

	
	static public function LoadPostForTaxTerm($taxnamed,$term,$post_type)
	{
		$args = array( 
			'post_type'        => $post_type,
			'posts_per_page' => -1,
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => $taxnamed,
					'field'    => 'slug',
					'terms'    => $term,
				)
			),
		);
		$myquery= new \WP_Query($args);
		return $myquery->posts;
	}


	public function appy_topic_metabox_html($po,$objBx) 
	{
		$id=$objBx['id'];
		$bx=$this->FindMetaBoxById($id);

		$post_id = get_the_ID();
		global $post;

		$forcat=$bx['forcat'];
		$arrPgs=array();
		if ($forcat!='')
		{
			$objBx['po']=$po;
			apply_filters_ref_array( 'PagedPwa_GetMetaBoxPg', array($po,$objBx,&$bx) );
			$pgid=$bx['pageid'];
			if ($pgid && $pgid!=-1)
				$arrPgs=array($pgid);
			else if (isset($bx['pageids']) && $pgids=$bx['pageids'])
				$arrPgs=$pgids;
		}
		else
		{
			$pgid=$bx['pageid'];

			$pgid=$this->GetGoodPageId($pgid);
			$arrPgs=array($pgid);
		}

		$strKey=$bx['dbkey'];



		foreach ($arrPgs as $pgnum)
		{
			apply_filters( 'PagedPwa_DoSimpleForm', $pgnum,$post_id );
			wp_nonce_field( 'appy_topic_metabox_save', 'appy_topic_metabox' );
		}
	}

	static public function ProcessVars($vars,&$tablevars) 
	{
		$arrScalar=[];
		foreach ($vars as $n=>$v)
		{
			if (!is_scalar($v))
			{
				$tablevars[$n]=$v;
			}
			else
				$arrScalar[$n]=$v;
		}
		return $arrScalar;
	}
	
	static public function DeepMerge($arr,&$arrNew) 
	{
		foreach ($arr as $n=>$v)
		{
			if (!is_scalar($v))
			{
				self::DeepMerge($v,$arrNew[$n]);
			}
			else
				$arrNew[$n]=$v;
		}
	}
	
	static public function StorePostedVars($strKey,$posed) 
	{
		//$strKey=$bx['dbkey'];
		//$vars=$_ POST[$strKey];
		
		//$fnd=$_ POST['mysearch'];
		//$str=$fnd['find'];
		$vars=$posed[$strKey];

		$tablevars=array();
		$varsb=self::ProcessVars($vars,$tablevars);

		$um=self::UpdateOption($strKey, $varsb);
		$val0_b=self::GetOption( $strKey, true);


		$post_id=-1;

		if ($tablevars)
		{
			self::StoreTableVars($post_id,$tablevars);
		}
	}
	
	static public function StoreTableVars($post_id,$tablevars) 
	{
		foreach ($tablevars as $k=>$o)
		{
			$t=$k;
			if ($t=='post')
			{
				$data=$o;
				$po=get_post($post_id);
				$poarr=(array)$po;
				$data=array_merge($poarr,$o);
				$um=wp_update_post( $data );
				//foreach ($o as $v)
				//{
				//	$um=wp_update_post( $post_id, $v );
				//}
			}
			else if ($t='postmeta')
			{
				foreach ($o as $k=>$v)
				{
					$g=get_post_meta($post_id, $k,true);
					$um=update_post_meta($post_id, $k, $v);	
					$g2=get_post_meta($post_id, $k,true);
				}
			}
		}
	}

	////-for std types eg user
	public function row_actions_userstd($actions,$uo) 
	{
		if (!$uo)
			return $actions;

		$ud=$uo->data;
			
		// Apply this only on a specific post type 
		if (!$this->IsAdminScrOK())
			return $actions;

		$ptype=$this->m_strPostType;
		$arrAct=$this->m_pobj['rowactions'];
		$i=0;
		foreach ($arrAct as $act)
		{
			$route=$act['theurl'];
			///~@$url=MenusAdmin::GetUserUrlRoute($route,$uo);
			$url=$this->AddUrlPostId($url);

			$strNamed=$act['title'];
			$actions["{$this->m_pi->GetPageTypeId()}{$ptype}_{$i}"] = "<a href='$url'>$strNamed</a>";
			$i++;
		}
		return $actions;
	}

	////-for std types eg page,post (not user)
	public function row_actions_std($actions,$post) 
	{
		if (!$post)
			return $actions;

		$po=$post;
		if ( $po->post_type !=$this->m_strPostType) 
			return $actions;
			
		if (!$this->IsAdminScrOK())
			return $actions;

		$ptype=$this->m_strPostType;
		$arrAct=$this->m_pobj['rowactions'];
		$i=0;
		foreach ($arrAct as $act)
		{
			$route=$act['theurl'];
			$url=$this->BuildUrlRoute($route,$post->post_name);

			$url=$this->AddUrlPostId($url);
	
			$strNamed=$act['title'];
			$actions["{$ptype}_{$i}"] = "<a href='$url'>$strNamed</a>";
			$i++;
		}
		return $actions;
	}

	public function AddUrlPostId($url) 
	{
		global $post;
		if ($post)
			$url.="/?post={$post->ID}";
		return $url;
	}

	public function row_actions($actions,$post) 
	{
		$ptype=$this->m_strPostType;
		$po=$post;

		if (!$post)
			return $actions;

		if ($post->post_type!=$ptype)
			return $actions;

		if (!$this->IsAdminScrOK())
			return $actions;

		$ptype=$this->m_strPostType;
		$arrAct=$this->m_pobj['rowactions'];
		$i=0;
		if (!$arrAct)
			return $actions;
		foreach ($arrAct as $act)
		{
			$route=$act['theurl'];

			$url=$this->BuildUrlRoute($route,$post->post_name);
			$url=$this->AddUrlPostId($url);
		
			$strNamed=$act['title'];
			$actions["{$ptype}_{$i}"] = "<a href='$url'>$strNamed</a>";
			$i++;
		}
		return $actions;
	}
	
	private  function FindColRecByTag( $tag ) 
	{
		$cols=$this->m_pobj['admincols'];
		if (!$cols)
			return null;
		foreach ($cols as $rec)
		{
			if ($rec['tag']==$tag)
				return $rec;
		}
		return null;
	}
	
	/*public function my_events_columns( $columns ) 
	{
		global $post;

		foreach ($columns as $key=>$col)
		{
			self::column_data($key, $post->ID);
		}
	}*/

	public function my_column_register_sortable( $columns ) 
	{
		$cols=$this->m_pobj['admincols'];
		if (!$cols)
			return $columns;

		foreach ($cols as $rec)
		{
			if ($rec['sortcol']=='y')
			{
				$tag=$rec['tag'];
				$columns[$tag] = $tag;
			}
		}

		/*$columns['prods_author'] = 'prods_author';
		$columns['prods_freshness'] = 'prods_freshness';
		$columns['prods_created'] = 'prods_created';
		$columns['MyTitle'] = 'MyTitle';*/
		return $columns;
	}
	
	public function datatype_columns_orderby($query)
	{
		if (!is_admin())
			return;

		if (!isset($query->query['post_type']))
			return;

		$post_type = $query->query['post_type'];
		$ptype=$this->m_strPostType;
		if ($post_type!=$ptype)
			return;
		
		$mq=$query->is_main_query();

		//$orderby1=get_query_var('orderby');
		//$orderby2=$query->query_vars['orderby'];
		$orderby = $query->get( 'orderby');
		$rec=$this->FindColRecByTag($orderby);
		if (!$rec)
			return;
		if ($rec['sortcol']!='y')
			return;

		//$query->set('orderby', 'column table header tag id'); //like comments 
		//$query->set('order', 'DESC'); //change order

		/*'meta_query' => array(
			'relation' => 'AND',
			array(
				array(
					'key' => '_sale_price',
					'value' => 0,
					'compare' => '>',
					'type' => 'numeric'
				),
			),*/



		$metakey=$rec['metakey'];
		if ($metakey)
		{
			//post meta col
			$query->meta_query(array('type'=>'numeric'));
			$query->set('type','numeric');
			$query->set('meta_key',$metakey);
			
			if ($rec['coltype']!='n')
				$query->set('orderby','meta_value');
			else
				$query->set('orderby','meta_value_num');
		}
		else
		{
			//post col
			if ($rec['coltype']=='n')
				$query->set('type','numeric');
		}
		return;

		switch($orderby)
		{
			case 'online_start':
				$query->set('meta_key','online_start');
				$query->set('orderby','meta_value');
				break;
			case 'price':
				$query->meta_query(array('type'=>'numeric'));
				$query->set('type','numeric');
				$query->set('meta_key','theprice');
				//$query->set('orderby','meta_value');
				$query->set('orderby','meta_value_num');
				//$query->set('orderby','x');

				//$query->set('ordertype','numeric');
				//$query->set('meta_key','theprice');
				//$query->set('orderby','theprice');
				//$query->set('order', 'DESC');
				break;
			/*case 'price':
				$query->meta_query(
					array(
						'relation' => 'AND',
						array(
						array(
							'key' => 'meta_key',
							'value' => 'theprice',
							'type' => 'numeric'
						)
						)
					)
				);
				break;*/
			default:
				break;
		}
	}

	
	public function column_data( $column, $po_id ) 
	{
		if (!$this->IsAdminScrOK())
			return;

		$cols=$this->m_pobj['admincols'];
		$rec=$this->FindColRecByTag($column);
		if (!$rec)
			return;

		$c=$rec['colname'];
		$d=$rec['datacol'];
		$ds=$rec['datasrc'];

		$po=get_post();
		$strType=$rec['coltype'];
		$mk=$rec['metakey'];
		///DoPostTypes::GetDataForCol($po,$ds,$d,$c,$this->m_pobj,$strType);
		
		MyPWAAppyPlug::GetDataForCol($po,$ds,$d,$c,$this->m_pobj,$strType,$mk);
	}
	
	public function column_headers( $columns ) 
	{
		if (!$this->IsAdminScrOK())
			return $columns;

		$strKeyCtls=Funcs::m_strKeyCtls;
		$ptid=$this->GetPageSlugId();
		$ctlfnd=$this->GetPageCtlClsData($ptid,$strKeyCtls,'MyPageTypeAdColsCtl');
		if ($ctlfnd==null)
			return $columns;
		
		$json=$ctlfnd['props']['Settings'];
		$vals=json_decode($json,true);

		if (!$vals)
		{
			$ndebugis=21;
			return array();
		}

		$columns=array();
		foreach ($vals as $rec)
		{
			$tag=$rec['tag'];
			$columns[$tag]=$rec['colname'];
		}
		return $columns;
	}
	
	public function filter_cars_by_taxonomies( $post_type, $which) 
	{
		$ptype=$this->m_strPostType;
		// Apply this only on a specific post type 
		if ($post_type!=$ptype)
			return;

		if (!$this->IsAdminScrOK())
			return;

		// A list of taxonomy slugs to filter by 


		$taxonomies = $this->m_arrTaxonomy;
		if (!$taxonomies)
			return;

		foreach ( $taxonomies as $taxonomy_slug ) 
		{ 
			self::filter_taxonomy($taxonomy_slug,$which);
		}
	}

	public static function PopulateTerms($terms,$parent,$level,$strDef)
	{
		foreach ( $terms as $term ) 
		{    
			if ($term->parent!=$parent)
				continue;

			//printf('<option value="%1$s" %2$s>%3$s (%4$s)</op    $term->slug,    
			//( ( isset( $_ GET[$taxonomy_slug] ) && 
			//( $_    $term->name,    $term->count   );  }   ec ho '</select>'; 
			$sel= $term->slug==$strDef?"selected":"";
			$spcs='';
			$spc="&nbsp;&nbsp;&nbsp;";
			for ($i=0;$i<$level;$i++)
			{
				$spcs.=$spc;
			}
			$sty='';
			Funcs::EcTag("<option $sty $sel class='level-$level' value='{$term->slug}'>{$spcs}{$term->name}</option>",'option');
			//ec ho "<option class='level-0' value='{$term->name}'>{$term->name}</option>";

			self::PopulateTerms($terms,$term->term_id,$level+1,$strDef);
		}
	}

	public static function filter_taxonomy( $taxonomy_slug, $which)
	{
		// Retrieve taxonomy data  
		$taxonomy_obj = get_taxonomy( $taxonomy_slug );  
		$taxonomy_name = $taxonomy_obj->labels->name;
			
		$terms = get_terms( $taxonomy_slug );
  		// Display filter HTML
		//$terms0 = get_terms('smartcat');


		
		Funcs::EcTag("<select name='{$taxonomy_slug}' id='{$taxonomy_slug}'>",'select'); 
		//ec ho '<option value="">' . sprintf( esc_html__( 'Show All   
		//$strDef=isset($_ GET[$taxonomy_slug])?$_ GET[$taxonomy_slug]:null;
		$strDef=self::GetVal('$taxonomy_slug',null);

		Funcs::EcTag("<option value='0'>All</option>",'option');

		self::PopulateTerms($terms,0,0,$strDef);

		Funcs::EcHtm("</select>");
	}


	public function restrict_manage_mycombos($post_type, $which)
	{
		$ptype=$this->m_strPostType;
		// Apply this only on a specific post type 
		if ($post_type!=$ptype)
			return;

		if (!$this->IsAdminScrOK())
			return;

		if (method_exists($this,'GetPlugins'))
			$arrpi=$this->GetPlugins();
		else
		{
			$arrpi=DoPostTypes::GetPlugins();
		}

		$cols=isset($this->m_pobj['admincombos'])?$this->m_pobj['admincombos']:null;
		
		if (!$cols)
			return;

		foreach ($cols as $rec)
		{
			$this->restrict_manage_acombo($rec);
		}
	}

    public function GetFromCSV($fields)
    {
		/*
		////$delimiter = null, $enclosure = null, $escape = null
        // Temporary output a line to memory to get line as string
        $fp = fopen('php://temp', 'w+');
        //$arguments = array("abc"=>"um","abc2"=>"um2");
        //array_unshift($arguments, $fp);
        //call_user_func_array('fputcsv', $arguments);
		fputs($fp,$fields);
        rewind($fp);

        //$line = '';

        //while ( feof($fp) === false ) {
        //    $line .= fgets($fp);
        //}
		$arr=fgetcsv($fp);

        fclose($fp);*/
		
		///$csv=str_replace(';',"\n",$fields);
		//$arr2=str_getcsv($csv,",","\n");
		//$arr2=str_getcsv($csv);
		$arr=explode(';',$fields);
		$results=array();
		foreach ($arr as $line)
		{
			$arrVals=str_getcsv($line);
			$one=trim($arrVals[0]);
			if (!$one)
				break;
			$two=trim($arrVals[1]);
            $item = new \stdClass();
            $item->metakey = $one;
            $item->post_title = $two;
			$results[]=$item;
		}
		return $results;
	}
	
	public function restrict_manage_acombo($rec)
	{
		$posttypes=$rec['dbposttypes'];
		$dbsql=$rec['dbsql'];
		$combodata=$rec['combodata'];
		$txt=$rec['text'];
		$getkey=$rec['action'];
		$valcolname=$rec['valcolname'];
		
		global $wpdb;


		$results = null;
		if ($dbsql)
		{
			$sql=$dbsql;
			////////go $sql=$wpdb-> prepare($sql);//---orgfixprep
			////////go $results = $wpdb->get_results($sql);
		}
		else if ($combodata)
		{
			$results =$this->GetFromCSV($combodata);
		}
		else
		{
			if ($posttypes=='taxonomy')
			{
				Funcs::EcHtm("<span><span style='float:left;'>$txt</span>");
				$taxonomy_slug=$valcolname;
				$terms = get_terms($taxonomy_slug);
				if (!$terms)
				{
					//fix bug:1 terms (although used count:0) wont show
					$terms = get_terms( array(
						'update_term_meta_cache' => false,
						'taxonomy' => $taxonomy_slug,
						'hide_empty'=>false,
					//'fields'   => 'id=>slug',
					) );
				}


				//$strDef = $_ GET[$getkey];
				$strDef=self::GetVal($getkey,null);

				Funcs::EcTag("<select name='{$taxonomy_slug}' id='{$taxonomy_slug}'>",'select'); 
				Funcs::EcTag("<option value='0'>All</option>",'option');		
				self::PopulateTerms($terms,0,0,$strDef);
				Funcs::EcHtm("</select>");

				Funcs::EcHtm("</span>");
				return;
			}
			$arrposttypes=explode(',',$posttypes);



			$child=$this->m_pi->FindLevelAt($arrposttypes[0]);
			if ($child)
				$ct=$child->GetPostType();
			else 
				$ct=$arrposttypes[0];

			$par=$this->m_pi->FindLevelAt($arrposttypes[1]);
			if ($par)
				$pt=$par->GetPostType();
			else 
				$pt=$arrposttypes[1];
			
			$sql=
			"select distinct f.id as metakey,f.post_title  ".
			"from {$wpdb->posts} t ".
			"inner join {$wpdb->posts} f on t.post_parent =f.id ".
			"where f.post_type='$pt' and t.post_type='$ct' ".
			"order by f.id";

			////////go$sql=$wpdb-> prepare($sql);//---orgfixprep
			////////go $results = $wpdb->get_results($sql);
		}


		$html = array();
		$html[] = "<select id='$getkey' name='$getkey'>";
		$html[] = "<option value='None'>All</option>";
		//$this_sort = isset($_ GET[$getkey])?$_ GET[$getkey]:null;
		$this_sort=self::GetVal($getkey,null);
		
		foreach($results as $meta_key) 
		{
			$default = ($this_sort==$meta_key->metakey ? ' selected="selected"' : '');
			$value = esc_attr("$meta_key->post_title");
			$html[] = "<option value='{$meta_key->metakey}'$default>{$value}</option>";
		}
		$html[] = "</select>";
		$strHtm=implode("\n",$html);
		Funcs::EcHtm("<span><span style='float:left;'>$txt</span>");
		Funcs::EcHtm($strHtm."</span>");
	}

	public function filter_manage_mycombos($query) 
	{
		/*if ($query->query['s']!="")
		{
			global $PagedPwa_routedata;
			$tplt=DoPostTypes::FindRouteFromUrl();
			//if (!$PagedPwa_routedata)
			//	return;
			$query->query_vars['post_type'] = $ptype;
			$query->query['post_type'] = $ptype;
			//$a=$PagedPwa_routedata['tag]'];
			return;
		}*/

		$qptype = isset($query->query['post_type'])?$query->query['post_type']:'';
		$mq=$query->is_main_query();

		$ptype=$this->m_strPostType;
		$cols=isset($this->m_pobj['admincombos'])?$this->m_pobj['admincombos']:null;
		if (!$cols)
			return;

		global $pagenow;
		// Apply this only on a specific post type 


		$poPostType=self::GetVal('post_type',null);
		if (is_admin() && $pagenow=='edit.php' && $poPostType && $poPostType==$ptype)
		{
			$ok=true;
			foreach ($cols as $rec)
			{
				$getkey=$rec['action'];

				$poGetkey=self::GetVal($getkey,null);
				if ($poGetkey && $poGetkey !='None')
				{

					$posttypes=$rec['dbposttypes'];
					$dbsql=$rec['dbsql'];
					$combodata=$rec['combodata'];
					$txt=$rec['text'];
					$valcolname=$rec['valcolname'];
					//$qvv=$_ GET[$getkey];
					$qvv=self::GetVal($getkey,null);

					$qvv=$query->query_vars[$getkey];
					$qvk=$query->query_vars[$valcolname];
					$mq=$query->meta_query;
					$tqa=$query->tax_query->queries;
					
					if ($posttypes=='taxonomy')
					{
						///tax
						/*'tax_query'      => array(
							array(
								'taxonomy' => 'pagetag',
								'field'    => 'slug',
								'terms'    => $myterms,
							),*/
						$args=
							array(
								'taxonomy' => $valcolname,
								'field'    => 'slug',
								'terms'    => $qvv,
							);
						$query->tax_query=$args;
					}
					else if ($dbsql)
					{
						///sql
					}
					else if ($valcolname)
					{
						//post types child/parent or meta
						//meta key/val
						$metakeycolname=$rec['metakeycolname'];
						$orderby=$rec['orderby'];
						//$v=$_ GET[$getkey];
						$v=self::GetVal($getkey,null);

						//-$query->query_vars['post_parent'] = $_ GET[$getkey];
						$query->query_vars['orderby'] = $orderby;
						$query->query_vars[$valcolname] = self::GetVal($getkey,null);
						if ($metakeycolname)
							$query->query_vars['meta_key'] = $metakeycolname;
					}
				}
			}
		}
	}

	public function restrict_manage_stdcombos($post_type, $which)
	{
		$ptype=$this->m_strPostType;
		// Apply this only on a specific post type 
		if ($post_type!=$ptype)
			return;

		if (!$this->IsAdminScrOK())
			return;

				
		$arrpi=DoPostTypes::GetPlugins();

		$cols=$this->m_pobj['admincombos'];
		if (!$cols)
			return;

		foreach ($cols as $rec)
		{
			$this->restrict_manage_acombo($rec);
		}
	}


	///add_action('restrict_manage_posts','restrict_manage_movie_sort_by_genre');
	public static function restrict_manage_movie_sort_by_genre($nLev) 
	{
		//topic
		if ($nLev!=1)
			return;



		global $wpdb;


	$sql=
	"select distinct f.id as metakey,f.post_title  ".
	"from {$wpdb->posts} t ".
	"inner join {$wpdb->posts} f on t.post_parent =f.id ".
	"where f.post_type='um1posttype' and t.post_type='um2posttype' ".
	"order by f.id";

	$getkey='sortby';
		
		////////go $sql=$wpdb-> prepare($sql);//---orgfixprep
		////////go $results = $wpdb->get_results($sql);
		$html = array();
		$html[] = "<select id='$getkey' name='$getkey'>";
		$html[] = "<option value='None'>All</option>";
		//$this_sort = $ _GET[$getkey];
		$this_sort=self::GetVal($getkey,null);

		foreach($results as $meta_key) 
		{
			$default = ($this_sort==$meta_key->metakey ? ' selected="selected"' : '');
			$value = esc_attr("$meta_key->post_title({$meta_key->metakey})");
			$html[] = "<option value='{$meta_key->metakey}'$default>{$value}</option>";
		}
		$html[] = "</select>";
		$strHtm=implode("\n",$html);
		Funcs::EcHtm("<span><span style='float:left;'>Parent:</span>");
		Funcs::EcHtm($strHtm."</span>");
	}


	public static function sort_movie_by_meta_value($query) 
	{
		global $pagenow;
		$ptype="um2posttype";
		
		$poPostType=self::GetVal('post_type',null);
		$poSortBy=self::GetVal('sortby',null);
		if (is_admin() && $pagenow=='edit.php' &&
			$poPostType && $poPostType==$ptype && 
			$poSortBy  && $poSortBy !='None')  
			{
				$v=self::GetVal('sortby',null);
				$query->query_vars['orderby'] = 'id desc';
				$query->query_vars['post_parent'] = self::GetVal('sortby',null);
			}
	}

	///@@

	static public function WantAdminSys()
	{
		return true;
	}

	static public function IsRestrictMe($cap='manage_options')
	{
		return false;
		$priv=current_user_can($cap);
		if ($priv) 
			return false;
		return true;
	}


	////@@@@@@@@@@@@@@
	/*
	public function x_column_data( $column, $po_id ) 
	{
		$cols=$this->m_pobj['admincols'];
		$rec=$this->FindColRecByTag($column);
		if (!$rec)
			return;

		$c=$rec['colname'];
		$d=$rec['datacol'];
		$ds=$rec['datasrc'];

		$po=get_post();

		switch ($ds)
		{
			case "html":
				$d=str_replace('[','<',$d);
				$d=str_replace(']','>',$d);
				ec ho $d;
				break;
			case 'post_content':
				$o=json_decode($po->post_content,true);
				$col=$o[$d];
				ec ho $col;
				break;
			case 'author':
				MenusAdmin::appy_topic_author_display_name( $po_id );
				break;
			case 'created':
				printf( '%1$s <br /> %2$s',
					get_the_date(),
					esc_attr( get_the_time() )
				);
				break;
			case 'freshness':
				$last_active = MenusAdmin::appy_get_topic_last_active_time( $po_id, false );
				if ( !empty( $last_active ) ) {
					ec ho esc_html( $last_active );
				} else {
					esc_html_e( 'No Replies' ); // This should never happen
				}
				break;
			default:
				$col=$po->$d;
				ec ho $col;
				break;
		}
	}*/

	/*public function x_column_headers( $columns ) 
	{

		$columns = array(
			'cb'                    => '<input type="checkbox" />',
			'title'                 => __( 'Title'),
			'prods_my_content'       => __( 'Price (Content)'),
			'prods_key_name' 		=> __( 'Key'),
			'prods_status' 		=> __( 'Status'),
			'prods_author'      => __( 'Author' ),
			'prods_created'     => __( 'Created'),
			'prods_freshness'   => __( 'Freshness')
		);
		return $columns;
	}*/

	/*public function xx_reg_custom_tax()
	{
		$ptype=$this->m_strPostType;
		$tax="manufacturer";
		//$tax="Manufacturer";
		$this->m_strTaxonomy=$tax;

		$labels = array(
			'name'                       => _x( 'Manufacturers', 'Taxonomy General Name', 'text_domain' ),
			'singular_name'              => _x( 'Manufacturer', 'Taxonomy Singular Name', 'text_domain' ),
			'menu_name'                  => __( 'Manufacturer', 'text_domain' ),
			'all_items'                  => __( 'All Manufacturers', 'text_domain' ),
			'parent_item'                => __( 'Parent Manufacturer', 'text_domain' ),
			'parent_item_colon'          => __( 'Parent Manufacturer:', 'text_domain' ),
			'new_item_name'              => __( 'New Manufacturer Name', 'text_domain' ),
			'add_new_item'               => __( 'Add New Manufacturer', 'text_domain' ),
			'edit_item'                  => __( 'Edit Manufacturer', 'text_domain' ),
			'update_item'                => __( 'Update Manufacturer', 'text_domain' ),
			'view_item'                  => __( 'View Item', 'text_domain' ),
			'separate_items_with_commas' => __( 'Separate manufactures with commas', 'text_domain' ),
			'add_or_remove_items'        => __( 'Add or remove manufactures', 'text_domain' ),
			'choose_from_most_used'      => __( 'Choose from the most used manufactures', 'text_domain' ),
			'popular_items'              => __( 'Popular Items', 'text_domain' ),
			'search_items'               => __( 'Search manufactures', 'text_domain' ),
			'not_found'                  => __( 'Not Found', 'text_domain' ),
			'no_terms'                   => __( 'No items', 'text_domain' ),
			'items_list'                 => __( 'Items list', 'text_domain' ),
			'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( $tax, array( $ptype ), $args );
		
	}*/

	public function GetGoodPageId($pgid)
	{
		if (is_numeric($pgid))
			return $pgid;

		$pinm=$this->m_pi->GetPlugName();
		$pgid=Funcs::GetGoodPageId($pgid,$pinm);
		if (!$pgid)
		{
			$bxpgid=-1;
			$pgid=Funcs::GetGoodPageId($bxpgid,$pinm);
		}
		return $pgid;
	}

	public function BuildUrlRoute($route,$post_name)
	{
		///~@return MenusAdmin::BuildUrlRoute($route,$post_name);
	}

	public function is_post_request()
	{
		///~@return MenusAdmin::is_post_request();
	}
	
	public function SetRowAdminTaxTypes($arr) 
	{

		$arrCustTaxes=&$this->m_pobj['admincusttaxes'];
		$nLen=count($arrCustTaxes);
		for ($i=0;$i<$nLen;$i++)
		{
			$o=&$arrCustTaxes[$i];
			$o['admincusttaxtypes']=$arr;
		}

	}
	
	public function SetRowAdminCusTax(&$arrCustTaxes) 
	{
		$this->m_pobj['admincusttaxes']=$arrCustTaxes;
	}
	
	public function GetPageCtlClsData($pageid,$strKeyCtls,$clsis)
	{
		//$pageid=46;
		if (!$this->m_bPgSlugJsnFile)
		{
			$ctlProps = get_post_meta( $pageid, $strKeyCtls, true );
			$ctlLay = get_post_meta( $pageid, Funcs::m_strKeyFor, true );
			if (!$ctlProps)
				return null;
		}
		else
		{
			$lp="{$this->m_strDir}smartpages/$pageid.json";
			$json=file_get_contents($lp);//---orgfixfgc
			//wp_remote_get
			$obj=json_decode($json,true);
			if (!$obj)
				return null;
			$ctlProps=$obj['instances'];
			$ctlLay=$obj['layout'];
		}			
		return self::GetPageCtlClsObj($ctlProps,$ctlLay,$clsis);
	}

	//////smart page support
	static public function GetPageCtlClsObj($ctlProps,$ctlLay,$clsis)
	{
		$bFound=false;
		foreach ($ctlProps as $ctl)
		{			
			$clsid=$ctl['props']['_class'];
			if ($clsid==$clsis)
			{
				$ctlid=$ctl['props']['_ctlid'];
				if (self::IsPageCtlDataIdUsedIn($ctlid,$ctlLay))
				{
					$bFound=true;
					break;
				}
			}
		}
		if (!$bFound)
			return null;

		return $ctl;
	}

	static public function IsPageCtlDataIdUsed($pageid,$ctlis)
	{
		$lay="phildesigned";
		$lay = get_post_meta( $pageid, $lay, true );

		return self::IsPageCtlDataIdUsedIn($ctlis,$lay);
	}

	static public function GetPageCtlsDataId($pageid,$strKeyCtls,$ctlis)
	{
		$ctlProps = get_post_meta( $pageid, $strKeyCtls, true );
		if (!$ctlProps)
			return null;
		
		$lay="phildesigned";
		$lay = get_post_meta( $pageid, $lay, true );

		$arr=array();
		foreach ($ctlProps as $ctl)
		{
			$ctlid=isset($ctl['props']['Id'])?$ctl['props']['Id']:null;
			if ($ctlid==$ctlis && self::IsPageCtlDataIdUsedIn($ctl['props']['_ctlid'],$lay))
			{
				$arr[]=$ctl;
			}
		}
		return $arr;
	}

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

	static public function GetPageCtlDataId($pageid,$strKeyCtls,$ctlis)
	{
		$ctlProps = get_post_meta( $pageid, $strKeyCtls, true );
		if (!$ctlProps)
			return null;
		

		$lay="phildesigned";
		$lay = get_post_meta( $pageid, $lay, true );

		foreach ($ctlProps as $ctl)
		{
			$ctlid=$ctl['props']['_ctlid'];
			if ($ctlid==$ctlis && self::IsPageCtlDataIdUsedIn($ctl['props']['_ctlid'],$lay))
				break;
		}
		if ($ctlid!=$ctlis)
			return null;
			
		return $ctl;
	}

	static public function GetVal($v,$def)
	{
		$v=isset($_GET[$v])?$_GET[$v]:$def;
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

}


?>
