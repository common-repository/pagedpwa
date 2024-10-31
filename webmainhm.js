
//var g_bAppFireJsAPI = false;
var g_bAppFireJsAPI = true;

g_bAppFireJsAPI=true;
g_bAppFireJsAPI = g_opwapaged['MsgSysGoog']==1?true:false;


var g_bDoInitSWDataDB = true;
g_bDoInitSWDataDB=false;

//setting for FB only
var g_bSoSubsFBNow = false;
var g_bSoSubsFBBut = true;
var g_bSoSubsAnon = false;
g_bSoSubsFBNow=false;
g_bSoSubsFBBut=true;
if (typeof d_bSoSubsFBNow!='undefined')
	g_bSoSubsFBNow = d_bSoSubsFBNow;
if (typeof d_bSoSubsFBBut != 'undefined')
	g_bSoSubsFBBut = d_bSoSubsFBBut;

if (typeof g_opwapaged['SubscribeAuto'] != 'undefined') {
	var str = g_opwapaged['SubscribeAuto'];
	g_bSoSubsFBNow = str == 'on' ? true : false;
}
if (typeof g_opwapaged['SubscribeBut'] != 'undefined') {
	var str = g_opwapaged['SubscribeBut'];
	g_bSoSubsFBBut = str == 'on' ? true : false;
}
if (typeof g_opwapaged['SubscribeAnon'] != 'undefined') {
	var str = g_opwapaged['SubscribeAnon'];
	g_bSoSubsAnon = str == 'on' ? true : false;
}






'use strict';

function WebMainHm()
{
	if (!'serviceWorker' in navigator) {
		return;
	}

	/**
	 * construction
	 */
	//
	/**
	 * global-ish scope
	 */
	var CreateDummyCtl = function (strKey) {
		//var strKey=strKey;
		var ctl = { "m_strId": strKey };
		if (typeof window.g_arrControls == 'undefined')
			window.g_arrControls = {};
		g_arrControls[strKey] = ctl;
		return ctl;
	}

	const GetHomeRefHere = function () 
	{
		/**
		 * reqpath
		 * var reqpath0='http:--localhost-wp-pwatest';
		 */
		var strLoc = g_opwapaged.home;
		///var strLoc = location. href;
		if (strLoc[strLoc.length - 1] == '/')
			strLoc = strLoc.substring(0, strLoc.length - 1);

		/**
		 * lose http scheme
		 */
		var arr = strLoc.split('://');
		strLoc = arr[1];

		//var reqpath0=location. href.replace('/\//g','-');
		//var reqpath1=location. href.replace('/\/g','-');
		var reqpath = strLoc.replace(/\//g, '-');
		//var strUrl=str.replace(/-/g,'-');
		//var strUrl=str.replace(/\//g,'-');

		return reqpath;
	}

	function MyText(strA, strB) 
	{
		if (typeof MsgText != 'undefined')
			MsgText(strA, strB);
		else
			console.log(strB+"::"+strA);
	}
	function DelSubscription(sub_id, subs, subsFull, strFuncCtd) {
		var uid = -1;
		var postid = -1;

		var curpostid = -1;
		var nWpUsr = g_opwapaged.ocustuser.data.ID;
		var wpuserid = nWpUsr;
		//

		var data = {
			//ctlid:this.m_strId,
			//m_strId:this.m_strId,
			//ctluid:uid,
			postid: postid,
			curpostid: curpostid,
			wpuserid: wpuserid,

			sub_id: sub_id,
			//subs: subs,
			subsFull: subsFull,
			bAppFireJsAPI: g_bAppFireJsAPI,
			opwapaged: g_opwapaged,
		};

		var ctl = CreateDummyCtl('keyDelSubscription');
		ctl.DelSubscriptionCtd = function (obj) {
			//strFuncCtd
			DelSubscriptionCtd(obj);
		}
		data.m_strId = ctl.m_strId;

		var oo = window["ctlsParams"];
		//var strAct="appyplug_action";
		var strAct = "mypwaappyplug_action";
		MyAjaxActionServFunc(g_opwapaged.ajax_url, strAct, 'DelSubscription', strFuncCtd, data, oo);
		MyAjaxActionServFunc(g_opwapaged.parajax_url, 'appyplug_action', 'DelSubscription', strFuncCtd, data, oo);
	}

	//was sub_id
	//
	function AddSubscription(tokSubs,pushSubs, strFuncCtd) 
	{
		var uid = -1;
		var postid = -1;

		var curpostid = -1;
		var nWpUsr = g_opwapaged.ocustuser.data.ID;
		var wpuserid = nWpUsr;
		//
		

		var data = {
			//ctlid:this.m_strId,
			//m_strId:this.m_strId,
			//ctluid:uid,
			postid: postid,
			curpostid: curpostid,
			wpuserid: wpuserid,

			sub_id: '',
			tokSubs: tokSubs,
			pushSubs: pushSubs,
			bAppFireJsAPI: g_bAppFireJsAPI,
			opwapaged: g_opwapaged,
		};

		var ctl = CreateDummyCtl('keyAddSubscription');
		ctl.AddSubscriptionCtd = function (obj) {
			//strFuncCtd
			AddSubscriptionCtd(obj);
		}
		data.m_strId = ctl.m_strId;

		var oo = window["ctlsParams"];
		//var strAct="appyplug_action";
		var strAct = "mypwaappyplug_action";
		MyAjaxActionServFunc(g_opwapaged.ajax_url, strAct, 'AddSubscription', strFuncCtd, data, oo);
		MyAjaxActionServFunc(g_opwapaged.parajax_url,'appyplug_action', 'AddSubscription', strFuncCtd, data, oo);
    }
    
    var AddSubscriptionCtd=function(obj)
    {
        var bOk=obj.ok==1?true:false;
        if (!bOk)
        {
            MsgText('NOT Completed','Error');
            return;
        }
        if (!obj.valid)
        {
            MsgText(obj.errmsg,'Error');
            return;
        }
        console.log("AddSubscriptionCtd here");
        //update ui
    }
    
	/**
	 * strfunc is server func,strObjFunc func pass back,
	 * call url of parent not iframed
	 * ajaxurl may be null to calc iframe url
	 */
	var MyAjaxActionServFunc = function (ajaxurl, strAction, strFunc, strObjFunc, objData, objInit) 
	{
		//debugger;
		var postid = -2;


		if (!ajaxurl) {

				ajaxurl = "https://scriptbyyou.com/wp-admin/admin-ajax.php";
		}

		objData.action = strAction;
		objData.function = strFunc;
		/**
		 * m_strId
		 */
		objData.strObjFunc = strObjFunc;
		objData.postid = postid;

		//-debugger;
		jQuery.post(ajaxurl, objData,
			function (response) {
				var obj = null;
				if (response != "")
					obj = JSON.parse(response);

				var oCtl = g_arrControls[obj.m_strId];
				if (!oCtl) {
					return;
				}
				var strMeth = obj.strObjFunc;
				oCtl[strMeth](obj);

				//-g_funcCtd(obj);
			}).fail(function () {
				alert("fail-webmainhm");
			});
	}

	var ShowSubscribeButton = function (obj) {
		const elDiv = document.querySelector('#idSubscribeIconDiv');
		var sty = elDiv.style;
		sty.top = '';
		sty.left = '';
		sty.width = '';
		sty.height = '';
		elDiv.innerHTML = "";
	}
	
	/**
	 * ui handler
	 */
	async function DoSubscribeButton(ev) 
	{
		if (ev.srcElement.nodeName.toLowerCase()!='input')
			return;

		var elDiv =ev.srcElement.parentNode;
		//const elDiv = document.querySelector('#idSubscribeIconDiv');
		var s = '[data-mynameid="idSubscribeSel"]';
		var elSel = elDiv.querySelector(s);
		var nSel=GetOptionIndex(elSel);
		var opt = GetOptionAt(elSel, nSel);
		var str = opt.value;

		if (str =='subscribe')
		{
			const permission = await requestNotificationPermission();
			if (g_bAppFireJsAPI && g_bSoSubsFBBut)
				DoSubsButNotiFB();
			else
				DoSubsButNotiMy();
			//DoSubscribeButtonSDK();
		}
		else if (str == 'unsubscribe')
		{
			if (g_bAppFireJsAPI && g_bSoSubsFBBut)
				DoUnSubsButNotiFB();
			else
				DoUnSubsButNotiMy();
		}
	}

	async function DoInitGCMSDK() 
	{
		console.log("before InstallAppHomePg");
		InstallAppHomePg(false);
		console.log("after InstallAppHomePg");

		/**
		 * import { initializeApp } from "https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js";
		 * import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.0.0/firebase-analytics.js";
		 *  TODO: Add SDKs for Firebase products that you want to use
		 *  https://firebase.google.com/docs/web/setup#available-libraries
		 */

		/**
		 *  Your web app's Firebase configuration
		 *  For Firebase JS SDK v7.20.0 and later, measurementId is optional
		 */

		/**
		 * From library:
		 * 	firebase
		 */

		const strJsn = g_opwapaged.ServAuthJsn;
		const firebaseConfig = eval("(" + strJsn + ")");


		/**
		 *  Initialize Firebase
		 */
		const app = firebase.initializeApp(firebaseConfig);
		//const analytics = app.getAnalytics(app);
		const app2=app;

		/**
		 * msg
		 */
		const msg = firebase.messaging();
		//js



		//My SW::
		var bMySw = true;
		if (bMySw) {
			if ('serviceWorker' in navigator) {
				//websw.js
				//firebase-messaging-sw.js
				//

				var strSWIs;
				strSWIs = "websw.js";

				var strV = g_opwapaged.start_url;
				if (strV[strV.length - 1] != '/')
					strV += "/";
				strV += "websw.js";
				strV = AddSWVersion(strV);

				strSWIs = AddSWVersion(strSWIs);
				//navigator.serviceWorker.register(strSWIs, { "scope": g_opwapaged.start_url })
				navigator.serviceWorker.register(strSWIs)
					.then(function (registration) {
						console.log("Service Worker Registered");
						if (registration.active) {

							if (registration.active.scriptURL != strV) {
								//stop this page

								if (self.localStorage.m_strWantVer == strV) {
									alert("FAILED to update the service worker, please close all other tabs in the browser for this domain ");
									self.localStorage.m_strWantVer = '';
									return;
								}
								self.localStorage.m_strWantVer = strV;

								registration.unregister().then(() => {
									if (location.href == g_opwapaged.start_url)
										location.reload();
									else
										location.href = g_opwapaged.start_url;
									return;
								});
								return;
							}
							registration.update();
						}

						msg.useServiceWorker(registration);
					}
					);
			}
			//return;
			await navigator.serviceWorker.ready;
		}


		if (g_bSoSubsFBNow) {
			SubscribeMsgType();
		}



		//foreground
		msg.onMessage(function(payLoad)
		{
			console.log('fire onMessage::');
			console.log(payLoad);
			console.log('onMessage::');
			MySWFuncs.GetPageNoti(payLoad.notification, payLoad.data, 'win');
		});


		//let deferredPrompt;
		///WebMainHm.deferredPrompt=null;
		WebMainHm.bA2HSDonePop = false;

		var nWpUsr = g_opwapaged.ocustuser.ID;
		if (g_bSoSubsFBBut && (g_bSoSubsAnon || nWpUsr != 0))
			LoadSubscribeHtml();

		//... set html,populate and DoSubscribeButton()



		//end DoInitGCMSDK()
	}

	//no ret
	async function DoSubsButNotiFB()
	{
		try
		{
			await navigator.serviceWorker.ready;
			//applicationServerKey: g_opwapaged.ServerKey

			const msg = firebase.messaging();
			await msg.requestPermission();
			console.log('Fire OK Got permission');
			

			var subsFull=null;
			const nMsgSys = g_opwapaged['MsgSysGoog'];
			var token = null;
			if (nMsgSys==0)
			{
				const sub = {
					userVisibleOnly: true,
					applicationServerKey: g_opwapaged.ServerKey
				};
				/*const sub={
					userVisibleOnly: true,
					vapidKey: g_opwapaged.ServerKey
				};*/
				const reg=await navigator.serviceWorker.ready;
				const subs=await reg.pushManager.subscribe(sub);
				subsFull = getFullSubs(subs);

				//subsFull = await getTokenFull(subs);
			}
			else
			{
				var prom = msg.getToken(
					{
						vapidKey: g_opwapaged.ServerKey
					});
				token = await prom;

				console.log('token is:');
				console.log(token);

				const reg = await navigator.serviceWorker.getRegistration();
				const subs = await reg.pushManager.getSubscription();
				subsFull = getFullSubs(subs);
			}
			console.log('full subs token is:');
			console.log(subsFull);

			//pass (full subs) to my server
			AddSubscription(token,subsFull, 'AddSubscriptionCtd');
		}
		catch (err)
		{
			//console.log('Error Ocurred:' + err);
			//not really an error
		}
	}

	async function DoSubsButNotiMy() 
	{

		if (!g_opwapaged.ServerKey)
		{
			MyText('You have no server key at the momment', 'Error');
			return;
		}

		const msg = firebase.messaging();

		//MsgText('DoSubscribeButton','clicked ooooooook');
		navigator.serviceWorker.ready.then(
			function (reg) 
			{
				//applicationServerKey: "public-vapid-key"
				/*const subs = {
					"userVisibleOnly": true,
				};*/
				//applicationServerKey,
				/*const subs = {
					userVisibleOnly: true,
				};*/
				const subs = {
					userVisibleOnly: true,
					applicationServerKey: g_opwapaged.ServerKey
				};
				//reg.pushManager.subscribe({
				//	userVisibleOnly: true
				//})
				//reg.pushManager.subscribe({})

				//reg.pushManager.subscribe(subs)
				msg.getToken(subs)
				.then(function (pushSubs) {
					//var sub_id = pushSubs.endpoint.split('fcm/send/')[1];
					AddSubscription(null,pushSubs, 'AddSubscriptionCtd');
				})
					.catch(function (err) {
						MyText(err.message, 'pushManager.subscribe FAILED : ' + err.code);
				});
			}
		);
	}
	
	//no ret
	async function DoUnSubsButNotiFB() 
	{
		const reg=await navigator.serviceWorker.ready;

		const msg = firebase.messaging();
		await msg.requestPermission();
		console.log('Fire OK Got permission');

		const subs=await reg.pushManager.getSubscription();
		var sub_id = null, subsFull=null;
		if (subs)
		{
			subsFull = getFullSubs(subs);
			sub_id = subs.endpoint.split('fcm/send/')[1];
		}

		var prom = msg.deleteToken();
		await prom;

		console.log('DoUnSubsButNotiFB ookk:');

		//pass to server
		DelSubscription(sub_id, subs, subsFull, 'DelSubscriptionCtd');
	}

	async function DoUnSubsButNotiMy() 
	{
		const reg = await navigator.serviceWorker.ready;
		const msg = firebase.messaging();

		var sub_id=null;
		var subs=null;
		var subsFull = null;
		if (reg.pushManager)
		{
			subs = await reg.pushManager.getSubscription();
			if (subs)
			{
				subsFull = getFullSubs(subs);
				sub_id = subs.endpoint.split('fcm/send/')[1];
			}
		}
		var prom = msg.deleteToken();
		await prom;
		DelSubscription(sub_id, subs, subsFull, 'DelSubscriptionCtd');

	};

	var DelSubscriptionCtd = function (obj) {
		var bOk = obj.ok == 1 ? true : false;
		if (!bOk) {
			MsgText('NOT Completed', 'Error');
			return;
		}
		if (!obj.valid) {
			MsgText(obj.errmsg, 'Error');
			return;
		}
		console.log("DelSubscriptionCtd here");
		//update ui
	}


	const requestNotificationPermission = async () => {
		const permission = await window.Notification.requestPermission()
		// value of permission can be 'granted', 'default', 'denied'
		// granted: user has accepted the request
		// default: user has dismissed the notification permission popup by clicking on x
		// denied: user has denied the request.
		if (permission !== 'granted') {
			throw new Error('Permission not granted for Notification')
		}
		return permission;
	}
	
	///decl
	//load button page
	var LoadHtml = function (usrslug, reqpath, strFuncCtd, ctl) 
	{
		var strFuncky = strFuncCtd;
		var uid = -1;
		var postid = -1;

		var curpostid = -1;
		var wpuserid = -1;
		//use reqpath

		var data = {
			//ctlid:this.m_strId,
			//m_strId:this.m_strId,
			//ctluid:uid,
			postid: postid,
			curpostid: curpostid,
			wpuserid: wpuserid,

			pageslug: usrslug,
			reqpath: reqpath,
		};

		data.m_strId = ctl.m_strId;

		console.log("LoadHtml:: reqpath:" + reqpath);

		var oo = window["ctlsParams"];
		var strAct = "appyplug_action";
		MyAjaxActionServFunc(null, strAct, 'GetSimplePageHomeHtml', strFuncCtd, data, oo);
	}

	function PopulateSelectOptVal(elemSelect, arrNames, arrValues) {
		var i, nCount = arrValues.length;

		var oOption, elemOptionDom;
		var nodeText;
		for (i = 0; i < nCount; i++) {
			oOption = document.createElement("OPTION");
			elemSelect.options.add(oOption);

			oOption.value = arrValues[i];

			nodeText = document.createTextNode(arrNames[i]);
			oOption.appendChild(nodeText);
		}
	}

	function ClearOptions(elemSelect) {
		var elemColl = elemSelect.options;
		elemColl.length = 0;
	}

	function GetOptionIndex(elemSelect) {
		var elemColl = elemSelect.options;
		return elemColl.selectedIndex;
	}

	function SetOptionIndex(elemSelect, nIndex) {
		var elemColl = elemSelect.options;
		elemColl.selectedIndex = nIndex;
	}

	function GetOptionAt(elemSelect, nAt) {
		var elemColl = elemSelect.options;
		var op = elemColl[nAt];
		return op;
	}

	var LoadHtmlSubsCtd = function (obj)
	{
		var bOk = obj.ok == 1 ? true : false;
		if (!bOk) {
			MsgText('NOT Completed', 'Error');
			return;
		}
		if (!obj.valid) {
			MsgText(obj.errmsg, 'Error');
			return;
		}

		const elDiv = document.querySelector('#idSubscribeIconDiv');
		elDiv.innerHTML = obj.html;
		var s = '[data-mynameid="idSubscribeSel"]';
		var elSel = elDiv.querySelector(s);

		if (elSel)
		{
			var arrNm = ['Subscribe', 'Un-subscribe'];
			var arrVal = ['subscribe', 'unsubscribe'];
			ClearOptions(elSel);
			PopulateSelectOptVal(elSel, arrNm, arrVal);
			SetOptionIndex(elSel, 0);
		}

		
		//ShowPopupButton(obj);
	}

	function AddSWVersion(strR) 
	{
		var strV = g_opwapaged.SwVersion;
		if (!strV || strV == '')
			return strR;
		return strR + `?ver=${strV}`;
	}

	function RegMyServWorker()
	{
		//var str = location. href;
		var str = g_opwapaged.start_url;
		if (str[str.length - 1] != '/')
			str += "/";
		str += "websw.js";
		//str = 'websw.js';
		//str = '/websw.js';
		console.log("webmainhm::serviceWorker.register:" + str + ":");

		var strErr = '';
		var oGotReg = null;
		str = AddSWVersion(str);
		//navigator.serviceWorker.register(str, { "scope": g_opwapaged.start_url })
		navigator.serviceWorker.register(str)
			.then(function (reg) {
				if (reg.active) {

					if (reg.active.scriptURL != str)
					{
						if (self.localStorage.m_strWantVer==str)
						{
							alert("FAILED to update the service worker, please close all other tabs in the browser for this domain ");
							self.localStorage.m_strWantVer = '';
							return;
						}
						self.localStorage.m_strWantVer = str;
						reg.unregister().then(()=>{
							if (location.href == g_opwapaged.start_url)
								location.reload();
							else
								location.href = g_opwapaged.start_url;
							return;
						});
						return;
					}
					reg.update();
				}

				WebMainHm.m_swReg = reg;
				//var arr=urlBase64ToUint8Array("'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBUY");
				var arr = null;
				//"applicationServerKey":null
				//applicationServerKey: "public-vapid-key"
				const subs = {
					"userVisibleOnly": true,
				};
				oGotReg = reg;
				////////ScheduleNotification(reg,'reg ok');

				//test
				/*
				var nWpUsr = g_opwapaged.ocustuser.ID;
				var sub_id = 'turkey_subscription_id:' + nWpUsr + ",loc:" + location. href;
				Add Subscription(sub_id, 'AddSubscriptionCtd');
				*/
				//...

				strErr = 'Got register sw OooK';
				console.log("webmainhm::serviceWorker.register:" + strErr + ":");

				//if (g_bAppFireJsAPI)
				//{
				//	debugger;
				//	DoInitGCMSDK();
				//}

				//return promise
				/*return reg.pushManager.subscribe(subs).then(u=>{
					console.log('pushManager.subscribepushManager.subscribepushManager.subscribe');
					var a=u;
				});*/

				//var pushSubs=await reg.pushManager.subscribe(subs);
				//var pushSubs2 = pushSubs;
				/*return reg.pushManager.subscribe(subs)*/
			})
			/*.then(function (pushSubs) {
				//subscribe cb
				//declare
				//subscribe cb
				//statements
				//https://fcm.googleapis.com/fcm/send/ff-a6jqCKi0:APA91bHqHHQjS...
				var sub_id = pushSubs.endpoint.split('fcm/send/')[1];
				Add Subscription(sub_id, 'AddSubscriptionCtd');
				//...
	
				//ui..
				//applicationServerKey needed!!!!
				var str = 'got subscription:' + JSON.stringify(pushSubs);
				strErr = 'Got reg sw AND subs';
	
				console.log(str);
				return pushSubs;
			})*/
			.catch((err) => {
				//changePushStatus(false);
				//alert(plugin['message_pushadd_failed']);
				if (!oGotReg)
					MyText(err.message, 'SW FAILED NO sw: ' + err.code + ":" + strErr);
				else
					MyText(err.message, 'Got SW but, Can\'t subscribe: ' + err.code + ":" + strErr);
				console.log(err);
				console.log("error for register('./websw.js')");
				console.log("strErr:" + strErr);
				console.log(oGotReg);
			}
		);
	}

	function LoadSubscribeHtml() {
		const elAddBtnX = document.querySelector('.add-button');
		//const elBtnDiv = document.querySelector('#idAddAppIconDiv');
		//hide initially, if exists
		if (elAddBtnX)
			elAddBtnX.style.display = 'none';
		//if (elBtnDiv)
		//elBtnDiv.style.display = 'none';

		/**
		 * subscribe button
		 */
		//var strCSS = 'z-index:999999;position:fixed;bottom:0px;left:40px;width:330px;height:30px;';
		//var strCSS = 'z-index:999999;position:fixed;bottom:0px;left:10px;';
		var strCSS = 'z-index:999999;position:fixed';
		//"<input id='idSubscribeIcon' " +
		//"value='...Subscribe to notifications' type='button'/>" +
		//"<div id='idSubscribeIconIn'></div>" +

		//var str =
		//	"<div id='idSubscribeIconDiv' style='" + strCSS + "'>" +
		//	"</div>";
		var str = '';
		var el = document.createElement('div');
		el.innerHTML = str;
		el.id = 'idSubscribeIconDiv';
		el.style.zIndex = '999999';
		el.style.position = 'fixed';
		document.body.appendChild(el);
		const elDiv = document.querySelector('#idSubscribeIconDiv');
		elDiv.onclick = DoSubscribeButton;

		var reqpath = GetHomeRefHere();
		var ctl = CreateDummyCtl('keyLoadHtml_subscribepage');
		var strFuncCtd = 'LoadHtmlSubsCtd';
		ctl[strFuncCtd] = function (obj) {
			LoadHtmlSubsCtd(obj);
		}
		//if (IsLoggedOnHere())
		LoadHtml('subscribepage', reqpath, strFuncCtd, ctl);
		// ... LoadHtmlSubsCtd
	}

	function IsLoggedOnHere() 
	{
		var nWpUsr = g_opwapaged.ocustuser.data.ID;
		return nWpUsr ? true : false;
	}

	//InitSWDataDB (fi)
	//chks bA2HSDonePop
	//'idAddAppIconDiv' initially 'none'
	//LoadHtml('a2hspage'
	//elBtnDiv.addEventListener('click'
	function InstallAppHomePg(bDoInitSWDataDB)
	{
		console.log('InstallAppHomePg', bDoInitSWDataDB);

		window.addEventListener('beforeinstallprompt', (e) => 
		{
			console.log('line 787 beforeinstallprompt');

			function InitSWDataDB() {
				//logged on or not(0)
				//var nWpUsr=g_opwapaged.ocustuser.data.ID;
				var nWpUsr = g_opwapaged.ocustuser.ID;

				//
				//done already
				self._myidb.open('websw',
					function (e) {
						var db = e.target.result;
						//$oCustData['paid']=self::IsProVersion()?1:0;

						var fi = {
							"fullPath": "userid",
							"userid": nWpUsr,
							"paid": g_opwapaged.paid,
							"periodictag": g_opwapaged.periodictag,
							"offlinestrategy": g_opwapaged.offlinestrategy,
							"ServAuthJsn": g_opwapaged.ServAuthJsn,
						};
						//var fi={"fullPath":"userid","userid":21};
						self._myidb.putDB(db, fi,
							function () {
								console.log('putDB ok');
								console.log(fi);
								self._myidb.closeDB(db);
							},
							function () {
								self._myidb.closeDB(db);
							}
						);
					},
					function () {
						console.log("db failed");
					}
				);
			}

			console.log('line 826 beforeinstallprompt');

			//
			function InitSWDataPost(reg) {
				if (!reg)
					return;

				//var nWpUsr=g_opwapaged.ocustuser.data.ID;
				var nWpUsr = g_opwapaged.ocustuser.ID;
				const sw = reg.active;
				//    if (o.msg === 'ClearCache') 

				var o = { "msg": "setuserid", "userid": nWpUsr };
				var strJsn = JSON.stringify(o);
				sw.postMessage(strJsn);

			}

			var LoadHtmlCtd = function (obj) {
				var bOk = obj.ok == 1 ? true : false;
				if (!bOk) {
					MsgText('NOT Completed', 'Error');
					return;
				}
				if (!obj.valid) {
					MsgText(obj.errmsg, 'Error');
					return;
				}

				ShowPopupButton(obj);
			}


			//e is event from beforeinstallprompt
			var ShowPopupButton = function (obj) {

				if (!obj.html)
				{
					elAddBtn.style.display = '';
					elBtnDiv.style.display = '';
					return;
				}
				
				const elDiv = document.querySelector('#idAddAppIconDiv');
				elDiv.style.top = '';
				elDiv.style.left = '';
				elDiv.style.width = '';
				elDiv.style.height = '';
				elDiv.innerHTML = obj.html;

				elAddBtn.style.display = '';
				elBtnDiv.style.display = '';
			}

			//end decl in InstallAppHomePg

			console.log('line 874 beforeinstallprompt');

			//statements cb:
			//
			console.log('line 879 beforeinstallprompt fired');
			if (bDoInitSWDataDB)
				InitSWDataDB();
			//...

			//window.postMessage('abc');
			//const registration = await navigator.serviceWorker.getRegistration();

			//navigator.serviceWorker.getRegistration()
			//var reg0=WebMainHm.m_swReg;
			//var sw0=reg0.active;
			//
			//InitSWDataPost ...
			if (WebMainHm.m_swReg) {
				//if newly reg..
				if (!WebMainHm.m_swReg.active) {
					console.log('newly reg but');
					console.log(WebMainHm.m_swReg);
					navigator.serviceWorker.ready
						.then(function (reg) {
							if (reg.active) {
								console.log('serviceWorker.ready newly');
								console.log(reg);
								InitSWDataPost(reg);
							}
						});

				}
				else
					InitSWDataPost(WebMainHm.m_swReg);
			}
			else {
				//if reg before
				navigator.serviceWorker.ready
					.then(function (reg) {
						var s2 = reg;

						if (!reg.active) {
							console.log('serviceWorker.ready');
							console.log(reg);
						}

						InitSWDataPost(reg);

						/*for(let reg of registrations) 
						{ 
						  const sw=reg.active;
				    
					  
						  var clis=null;
						  if (sw)
							clis=await sw.clients;
					  
						}*/
					});
			}

			if (WebMainHm.bA2HSDonePop)
				return;

			WebMainHm.bA2HSDonePop = true;

			//beforeinstall..

			var strCSS = 'z-index:999999;position:fixed;top:0px;left:150px;width:330px;height:30px;';
			var str =
				"<div id='idAddAppIconDiv' style='" + strCSS + "'>" +
				"<input id='idAddAppIcon' " +
				"value='Add New App Icon To Desktop' type='button'/>" +
				"</div>";
			var el = document.createElement('div');
			el.innerHTML = str;
			document.body.appendChild(el);

			const elAddBtn = document.querySelector('#idAddAppIcon');
			const elBtnDiv = document.querySelector('#idAddAppIconDiv');

			// Prevent Chrome 67 and earlier from automatically showing the prompt
			e.preventDefault();
			// Stash the event so it can be triggered later.
			WebMainHm.deferredPrompt = e;
			// Update UI to notify the user they can add to home screen
			//elAddBtn.style.display = '';
			elAddBtn.style.display = 'none';
			elBtnDiv.style.display = 'none';
			//beforeinstallprompt
			//no show now

			var reqpath = GetHomeRefHere();
			//chg default button to custom page



			var ctl = CreateDummyCtl('keyLoadHtml_a2hspage');
			//ctl.LoadHtmlCtd = function (obj) {
			var strFuncCtd = 'LoadHtmlCtd';
			ctl[strFuncCtd] = function (obj) {
				//strFuncCtd
				LoadHtmlCtd(obj);
				//strObjFunc
				//m_strId
				//window[strFuncCtd](obj);
			}
			//if (IsLoggedOnHere())
			LoadHtml('a2hspage', reqpath, strFuncCtd, ctl);

			elBtnDiv.addEventListener('click', () => {
				// hide our user interface that shows our A2HS button
				//elBtnDiv.style.display = 'none';
				// Show the prompt
				WebMainHm.deferredPrompt.prompt();
				// Wait for the user to respond to the prompt
				WebMainHm.deferredPrompt.userChoice.then((choiceResult) => {
					if (choiceResult.outcome === 'accepted') {
						elBtnDiv.style.display = 'none';
						console.log('User accepted the A2HS prompt');
					}
					else {
						console.log('User dismissed the A2HS prompt');
						e.stopPropagation();
						e.preventDefault();
						return;
					}
					WebMainHm.deferredPrompt = null;
				});
			});
		}
		);
	}




	
	function getFullSubs(subMe) {
		function ArrBufToBase64(buffer) {
			var binary = '';
			var bytes = new Uint8Array(buffer);
			var len = bytes.byteLength;
			for (var i = 0; i < len; i++) {
				binary += String.fromCharCode(bytes[i]);
			}
			return window.btoa(binary);
		}

		//
		const o = {
			endpoint: subMe.endpoint,
			keys: {
				p256dh: ArrBufToBase64(subMe.getKey('p256dh')),
				auth: ArrBufToBase64(subMe.getKey('auth')),
			},
		};
		return o;
	}

	//return Promise<FullSubs>
	function getTokenFull(subs) {
		function ArrBufToBase64(buffer) {
			var binary = '';
			var bytes = new Uint8Array(buffer);
			var len = bytes.byteLength;
			for (var i = 0; i < len; i++) {
				binary += String.fromCharCode(bytes[i]);
			}
			return window.btoa(binary);
		}

		return new Promise(resolve => {
				return navigator.serviceWorker.ready
					.then(function (reg) {
						return reg.pushManager.subscribe(subs);
					})
					.then(function (subMe) {
						const o = {
							endpoint: subMe.endpoint,
							keys: {
								p256dh: ArrBufToBase64(subMe.getKey('p256dh')),
								auth: ArrBufToBase64(subMe.getKey('auth')),
							},
						};
						resolve(o);
					});
			}
		);

	}

	var SubscribeMsgType = async function () {
		const permission = await requestNotificationPermission();
		if (g_bAppFireJsAPI)
			DoSubsButNotiFB();
		else
			DoSubsButNotiMy();
	}


	//statements main WebMainHm
	//statements WebMainHm main

///   [#GoogAppFireJsAPI begin]
	if (g_bAppFireJsAPI) {
		DoInitGCMSDK();
		return;
	}
///    [#GoogAppFireJsAPI end]

///   [#!GoogAppFireJsAPI begin]
	//1. reg sw
	// ...
	//2. LoadHtml('subscribepage
	//3. addEventListener('beforeinstallprompt
	//3b. LoadHtml('a2hspage
	//4. RegisterPeriodicSync
	//5. addEventListener('message

	firebase=myfirebase;

	console.log("webmainhm::href:" + location.href + ":(" + g_opwapaged.start_url+")");
	RegMyServWorker();
	//...

	let deferredPrompt;
	let bA2HSDonePop = false;
	var nWpUsr = g_opwapaged.ocustuser.ID;
	if (g_bSoSubsFBBut && (g_bSoSubsAnon || nWpUsr!=0))
		LoadSubscribeHtml();
	//... set html,populate and DoSubscribeButton()
	if (g_bSoSubsFBNow)
	{
		SubscribeMsgType();
	}

	InstallAppHomePg(g_bDoInitSWDataDB);
	//..html,click


	//        obj.addEventListener(strEvent, func, bCapture);
	self.addEventListener('message',
		(ev) => {
			debugger;
			//HandleSWMsgs(ev);
			var strJsn = ev.data;
			var o = JSON.parse(strJsn);
			var strCmd = o.cmd;
			if (strCmd == 'swhere') {
				console.log("message : swhere");
				MsgText('RegMsgHandler', 'swhere');
			}
		}, false
	);
	/*window.addEventListener('message',
		(ev) => {
			debugger;
		}, true
	);*/
	//foreground

	//my fb fake
	const msg = firebase.messaging();
	msg.onMessage(function (payLoad) {
		console.log('fire onMessage::');
		console.log(payLoad);
		console.log('onMessage::');

		MySWFuncs.GetPageNoti(payLoad.notification, payLoad.data,'win');
	});

	//navigator.serviceWorker.addEventListener

///   [#!GoogAppFireJsAPI end]


}
//end WebMainHm() 
WebMainHm.m_swReg = null;
WebMainHm.bA2HSDonePop = false;
WebMainHm.deferredPrompt=null;

WebMainHm();


