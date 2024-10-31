<?php
namespace SmartIntelPWAAppy;
use \SmartIntelPWAAppy\PostTypeAdmin as RegPostType;

class MyRegPostType extends RegPostType 
{

	/**
	 * 		$ptid=$this->GetPageSlugId();
	 * override
	 */
	public function GetPageSlugId()
	{
		$strSlug=$this->GetPageSlug();
		if ($this->m_bPgSlugJsnFile)
		{
			return $strSlug;
		}

		if (is_numeric($strSlug))
			return $strSlug;

			//return 46;
			/**
			 * $piname=$this->m_pobj['__pi']->Get PlugName();
			 */
		///////@@@@$piname=MenusAdmin::GetPlugName($this->m_pobj);
		/**
		 * if ($piname=='forum')
		 * 	$um=21;
		 */


	}

	/**
	 * override
	 */
	public function GetGoodPageId($pgid)
	{
		if ($this->m_bPgSlugJsnFile)
		{
			return $pgid;
		}
		//return 46;

	}
	
	/**
	 * override
	 */
	public function GetPlugins()
	{
		//$arrpi=DoPostTypes::GetPlugins();
		return array($this);
	}

	public function BuildUrlRoute($route,$post_name)
	{
		/**
		 * return MenusAdmin::BuildUrlRoute($route,$post_name);
		 */
		$str=MyPWAAppyPlug::GetRouteUrl($route);
		return $str;
	}

	public function is_post_request()
	{
	}
	
	/**
	 * $po is new post
	 *  no route data here as it was triggered by db chg (ajax call)
	 */
	public function MaybeEmailSubs($po)
	{
		return '';
	}
	

}

class ThePlugIn 
{	
	/**
	 * private $m_pi=null;
	 */

	public function __construct()
	{
	}

	public function GetPlugName() 
	{
		return MyPWAAppyPlug::GetPlugNameHere();
	}

}

