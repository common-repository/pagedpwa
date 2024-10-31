<?php
namespace SmartIntelPWAAppy;

class ServServPush 
{	
	static public function GetFirebaseKey() 
	{
		//$server_key = pwp_get_setting( 'firebase-serverkey' );
		
		//$usr=wp_get_current_user();
		//$usrid=$usr->ID;

		$k=self::GetOption('ServerKey');
		//SenderID
		return $k;
	}

	//return false as error
	static public function ChkServerKey() 
	{
		return true;
	}


	static public function GetBadgePost() 
	{
		return null;
	}

	//return false as error
	// or true
	//$torecip : display name
	//	 : may be null
	static public function PushMsgNoTag($data,$torecip) 
	{
		//must be devices
		$server_key=self::GetFirebaseKey();
		//self::PushMsgDataFCM2();
		
		$devices=[];
		//are the recips ids
		$devices=self::GetDevicesIdsFor($torecip);
		if (!is_array($devices) || count($devices)==0)
		{
			return false;
			/*return 
			[
				'type'    => 'error',
				'message' => __( 'No devices set', 'progressive-wp' ),
			];
			*/
		}

		self::AddImagesToData($data);

		//defaults and restrict
		$data = shortcode_atts( [
			'title'    => 'Say Hello Wave Goodbye', // Notification title
			'badge'    => '', // small Icon for the notificaion bar (96x96 px, png)
			'body'     => '', // Notification message
			'icon'     => '', // small image
			'image'    => '', // bigger image
			'openwindow' => '', // url
		], $data );

		//slash the strs
		$data['title'] = addslashes( $data['title'] );
		$data['body']  = addslashes( $data['body'] );

		//self::PushMsgData($data,$devices);
		//self::PushMsgDataFCM2($data,$devices);

		/////////////TODO

		return true;
	}



	

	static public function AddImagesToData(&$data) 
	{
		/**
		 * Badge
		*/
		//'badge'
		$badge=self::GetBadgePost();
		$badge_url='';
		if ('attachment' == get_post_type( $badge )) 
		{
			/*$badge_image = pwp_get_instance()->image_resize( $badge, 96, 96, true );
			if ( $badge_image ) 
			{
				$badge_url = $badge_image[0];
			}*/
		} 
		else 
		{
			$badge_url = '';
		}

		$data['badge']=$badge_url;
		// small Icon for the notificaion bar (96x96 px, png)

		/**
		 * Icon
		 */
		//icon'
		/**
		 * -$data['icon'] = $data['image_url'];
		 */
	}

	//from ajax call
	//$sub_id
	static public function AddSubscription($wpuserid,$pushSubs,$tokSubs)
	{
		/*
		$tmNow=time();
		//$strDt0=gmdate('Y-m-d H:i:s',$tmNow);

		//map od userid so they dont buildup endlessly
		$map=self::GetOption('PagedPwaSubs');
		if (!$map)
			$map=array();

		$o=["id"=>$sub_id,"userid"=>$wpuserid,"dated"=>$tmNow];
		$map[$wpuserid]=$o;
		$objset=update_option('PagedPwaSubs',$map);
		*/

		/**
		 * assoc with post
		 */
        //$rt=wp_set_post_terms($newid,$termid,'pagetag',false);
        //$arrFnd0=wp_get_post_terms($newid,'pagetag');

		//$o=["id"=>$sub_id,"userid"=>$wpuserid,"dated"=>$tmNow];

		$recid=0;
		if ($wpuserid)
		{
			$arrWhere=[];
			$postsDb=self::QueryTokensUser($wpuserid,$arrWhere);
			if ($postsDb && count($postsDb)>0)
			{
				$recid=$postsDb[0]->ID;
			}
		}
		else
			$recid=self::GetSubsAnon($tokSubs);

		//wpuserid
		//sub_id
		//subscripttype
		$json=wp_json_encode($pushSubs);
		if ($tokSubs)
		{
			$sub_id=$tokSubs;
		}
		else
		{
			$sub_id=$pushSubs['endpoint'];
			$sub_id=str_replace("\\",'/',$sub_id);
			$arrSub=explode('/',$sub_id);
			$sub_id=array_pop($arrSub);
					//var sub_id = pushSubs.endpoint.split('fcm/send/')[1];
		}

        $arr=array(
            //'post_type'=>'post',
            'post_author'=>$wpuserid,
            'post_type'=>'subscripttype',
            'post_content'=>'scriptbyyou',
            'post_excerpt'=>$sub_id,
            'post_content_filtered'=>$json,
            //'post_title'=>"scriptbyyou_subscription: $sub_id, $wpuserid");
            'post_title'=>"scriptbyyou_subscription: from $wpuserid");

        $newid=Funcs::CreatePostFor($recid,$arr);
        $rt=wp_set_post_terms($newid,'scriptbyyou_subscription');

		//GetDevicesMap
		//GetDeviceTo
		//GetDevicesIdsFor
	}

	//get devices subscribed (with user id)
	//$arrWhere is assoc arr
	static public function GetDevicesMap($arrWhere)
	{
		/*
		$usrid=1;
		//array of ['id']
		$o=["id"=>'devid_123',"userid"=>$usrid];
		$map=array();
		$map[$usrid]=$o;
		*/

		//lose PagedPwaSubs
		/*
		$map=self::GetOption('PagedPwaSubs');
		$mapNew0=array();
		$tmNow=time();
		$tmBack=strtotime('-12 HOUR',$tmNow);
		foreach ($map as $o)
		{
			//$o=["id"=>$sub_id,"userid"=>$wpuserid,"dated"=>$tmNow];
			$dated=$o['dated'];

			if (!$dated) 
				continue;
				
			if ($dated<$tmBack)
				continue;

			$userid=$o['userid'];
			$mapNew0[$userid]=$o;
		}
		//if (!$map)
		//	$map=array();
		*/
		
		//$usr=wp_get_current_user();
		//$usrid=$usr->ID;
		$mapNew=array();
		$postsDb=self::QueryTokensUser(null,$arrWhere);
		foreach ($postsDb as $r)
		{
			$modi=$r->post_modified;
			if (!$modi) 
				continue;
				
			$dated=strtotime($modi);
			//if ($dated<$tmBack)
			//	continue;

			$userid=$r->post_author;
			$sub_id=$r->post_excerpt;

			$user_obj = get_userdata($userid);
			$dname=$user_obj->data->display_name;
			$email=$user_obj->data->user_email;

			//$user_obj=Funcs::GetUsrRecFromDName($torecip);
			//if (is_wp_error( $user_obj ))
			//	return false;

			$o=[
				"id"=>$sub_id,
				"subdevice"=>$r->post_content_filtered,
				"userid"=>$userid,
				"dated"=>$dated,
				"authordname"=>$dname,
				"authoremail"=>$email
			];
			$mapNew[$userid]=$o;
		}
		return $mapNew;
	}

	//	olde $torecip : display name
	//arrUserIds : arr userid
	//return arr of dev subs obj for user display name
	// or empty arr
	static public function GetFullDeviceTo($arrUserIds) 
	{
		//$user_obj=Funcs::GetUsrRecFromDName($torecip);
		//if (is_wp_error( $user_obj ))
		//	return null;

		//$sendto=$user_obj->ID;
		$map=self::GetDevicesMap([]);

		$arr=[];
		foreach ($arrUserIds as $sendto)
		{
			$r=$map[$sendto];
			if (!$r)
				continue;
			$arr[]=$r;
		}
		return $arr;
	}
	
	//$torecip : display name
	//return dev id for user display name
	static public function GetDeviceTo($torecip) 
	{
		$user_obj=Funcs::GetUsrRecFromDName($torecip);
		if (is_wp_error( $user_obj ))
			return false;

		$sendto=$user_obj->ID;
		$map=self::GetDevicesMap([]);

		/*
		$arrFnd=wp_list_filter($arr,array('userid'=>$sendto));
		$arrKs=array_keys($arrFnd);
		$k=$arrKs[0];
		$r=$arrFnd[$k];
		*/
		$r=$map[$sendto];
		if (!$r)
			return false;

		//return 'id_dev1221';
		$strDev=$r['id'];
		return $strDev;
	}

	//	olde $torecip may be null for all
	//		$torecip : display name
	//arrUserIds : arr userid
	//return array of subscription objs for user (optional)
	//or false as error
	static public function GetDevicesObjsFor($arrUserIds) 
	{
		$devices=[];
		if (!$arrUserIds)
		{
			//to ALL devices
			//array of ids
			$mapDevSubs=self::GetDevicesMap([]);
			foreach ($mapDevSubs as $dev)
			{
				$devices[]=$dev;
			}
		}
		else
		{			
			$arrdevsubs=self::GetFullDeviceTo($arrUserIds);
			if ($arrdevsubs===null)
				return false;

			foreach ($arrdevsubs as $devsubs)
			{
				$devices[]=$devsubs;
			}				
		}
		return $devices;
	}
	
	//$torecip may be null
	//$torecip : display name
	//return array of ids for user (optional)
	//or false as error
	static public function GetDevicesIdsFor($torecip) 
	{
		if (!$torecip)
		{
			//to ALL devices
			//array of ids
			$mapDevSubs=self::GetDevicesMap([]);
			foreach ($mapDevSubs as $dev)
			{
				//$add_device = false;
				
				/*if ( empty( $send_grp ) ) 
				{
					// send if no limitation set
					$add_device = true;
				} 
				else
				{
					foreach ( $send_grp as $send_to ) 
					{
						if ($dev['id']==$send_to) 
						{
							$add_device = true;
						}
					}
				}*/
				/*if ($add_device) 
				{
					$devices[]=$dev['id'];
				}*/
				$devices[]=$dev['id'];
			}
		}
		else
		{			
			$devid=self::GetDeviceTo($torecip);
			if ($devid===false)
				return false;

			$devices[]=$devid;
		}
		return $devices;
	}

	static public function DelSubscriptionCur()
	{
		$usr=wp_get_current_user();
		$usrid=$usr->ID;
		self::DelSubscription($usrid);
	}

	static public function DelSubscription($wpuserid)
	{
		/*
		//map od userid so they dont buildup endlessly
		$map=self::GetOption('PagedPwaSubs');
		if (!$map)
			return;

		$nUsrId=intval($wpuserid);
		$o=$map[$nUsrId];
		//$devId=$map['id'];
		if (!$o)
			return;

		unset($map[$nUsrId]);
		$objset=update_option('PagedPwaSubs',$map);
		*/

		//$o=["id"=>$sub_id,"userid"=>$wpuserid,"dated"=>$tmNow];
		//$map[$wpuserid]=$o;
		if ($wpuserid)
		{
			$postsDb=self::QueryTokensUser($wpuserid,[]);
			if (count ($postsDb)>0)
			{
				$success = wp_delete_post($postsDb[0]->ID,true);
			}
		}
	}

	




	//$nFrom may be null
	//is array (assoc)
	static public function QueryTokensUser($nFrom,$argsWhere)
	{

		$ptype='subscripttype';
		$argsWhere['post_type']=$ptype;
		if ($nFrom)
			$argsWhere['author']=$nFrom;

		$args = array(
    		'suppress_filters' => true,
			//'nopaging' => true,
			'posts_per_page' => 100,
			'offset' => 0,
			'number' => 100,
			'orderby' => 'post_modified',
			'order' => 'asc',
			'groupby' => 'post_modified',
			'group' => 'asc',
		);

        foreach ($argsWhere as $k=>$v)
        {
            $args[$k]=$v;
        }
		$query = new \WP_Query($args);

		$recs=[];
		foreach ($query->posts as $po)
		{
			$recs[]=$po;
		}
		return $recs;	
	}

    static private function GetOption($nm)
    {
        $nm="pagedpwa_$nm";
        return get_option($nm);
    }

    static private function GetSubsAnon($sub_id)
    {
		$recid=0;
		//$arrWhere=[''=>$sub_id];
		$postsDb=self::QueryTokensUser(null,[]);
		if ($postsDb && count($postsDb)>0)
		{
			foreach ($postsDb as $po)
			{
				if ($po->post_excerpt==$sub_id)
				{
					$recid=$postsDb[0]->ID;
					break;
				}
			}
		}
		return $recid;
    }

}
