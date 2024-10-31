//importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js');
//importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging.js');

debugger;
//importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
//importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

var cacheName = 'fabPWAPagesApp: pagedpwa';

var m_bDoNoti=true;
var m_bDoFetch = true;
var m_bDoMsgPost = false;

var m_bDoUrlUrlRefresh = false;
//IsUrlQueryRefresh, 'fetch'
var m_bDoUrAppRefresh = false;
var m_bNotifyByFocus = false;

if (m_bDoFetch)
{
	var strUrl = self.registration.scope +
		'wp-admin/admin-ajax.php?action=mypwaappyplug_getservvars&varsonly=1';

	//-var g_opwapaged
	importScripts(strUrl);
}

importScripts(
  "https://www.gstatic.com/firebasejs/9.0.1/firebase-app-compat.js"
);
importScripts(
  "https://www.gstatic.com/firebasejs/9.0.1/firebase-messaging-compat.js"
);

const strJsn = g_opwapaged.ServAuthJsn;
const firebaseConfig = eval("(" + strJsn + ")");
console.log(":::firebase-messaging-sw::::firebaseConfig::::::::::", firebaseConfig);

// Initialize Firebase
const app = firebase.initializeApp(firebaseConfig);
const msg = firebase.messaging();

//foreground
//msg.onMessage(function (payLoad)
// on web side

////importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');
msg.onBackgroundMessage(function (payLoad) 
{
    console.log('onBackgroundMessage::');
    //console.log(payLoad);

	//var prom = showMyNotif(data, txt, 'push');
	//return self.registration.showNotification(str + data.title, o);

	console.log(
		'[firebase-messaging-sw.js] Received background message ',
		payLoad
	);
	// Customize notification here
	const notificationTitle = 'Background Message Title';
	const notificationOptions = {
		body: 'Background Message body.',
		icon: '/firebase-logo.png'
	};

	//self.registration.showNotification(notificationTitle, notificationOptions);

	//payLoad
	//collapseKey
	//data {'gcm.n.e':}
	//messageId
	//notification {'body':,'title':}

	const noti = payLoad.notification;
	const bod = noti.body;
	const tit = noti.title;
	const dat = payLoad.data;
	const dat1 = noti.data;
	const from = payLoad.from;

	return MySWFuncs.GetPageNoti(noti, dat);
	//...
}
);

//
////importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');
/*msg.setBackgroundMessageHandler(function (payLoad)
{
    console.log('setBackgroundMessageHandler::');
    console.log(payLoad);
    const title='Hello World';
    const options={
        body: payLoad.data.status
    };
    return self.registration.showNotification(title,options);
}
);*/

// Start the service worker and cache all of the app's content 
///- online so cache the files
self.addEventListener('install', async function (e) {
	///[#OldComment begin]
	debugger;
	//if (e.target.cacheName=="AppyApp: tur2")
	//{
	//  await self.registration.unregister();
	//}

	// await self.registration.unregister();

	//   caches.keys().then(function(cacheNames) 
	//   {
	// 	cacheNames.forEach(function(cacheName) 
	// 	{
	// 	  if (cacheName=='AppyApp: tur2')
	// 		caches.delete(cacheName);
	// 	});
	//   });

	//   return;

	console.log('firebase::install firebase-messaging-sw.js');

	// function GeMeProm()
	// {    
	//   return caches.delete(cacheName).then(async function(dd) 
	// 	{
	// 	  //DumpMe();
	// 	  //DumpClients();
	// 	  ////need filesToCache to have './'

	// 	  var cache=await caches.open(cacheName);

	// 	  console.log('cache.addAll xd');
	// 	  return cache.addAll(filesToCache);
	// 	});
	// }
	///[#OldComment end]

	function GeMeProm() {
		console.log('install::GeMeProm:::self ' + self.cacheName);
		console.log('install::GeMeProm:::' + cacheName);
		return caches.delete(cacheName).
			then(function (dd) {

				//action = location.href +'wp-admin/admin-ajax.php?action=appyplug_appaction';
				//var strUrl = self.registration.scope + 
				//'wp-admin/admin-ajax.php?action=mypwaappyplug_getpush&txt='+txtEnc;

				var strUrl = self.registration.scope +
					'wp-admin/admin-ajax.php?action=mypwaappyplug_getcacheurl';
				return fetch(strUrl);
			}
			).
			then(async res => {
				const filesToCache = await res.json();
				console.log('install::cache.addAll annda:::' + cacheName);
				const cache = await caches.open(cacheName);

				//var oFile = await GetUserIdFromDB();
				//self.sessionStorage['pwaPagesPolicy'] = 'kodskdsl';

				return cache.addAll(filesToCache);
			}
			);
	}

	e.waitUntil(GeMeProm());
});

self.addEventListener('activate', e => {
	console.log('firebase::activate firebase-messaging-sw.js');

	function GetPromise() {
		return new Promise(async resolve => {
			//var oFile = await GetUserIdFromDB();
			//self.sessionStorage['pwaPagesPolicy'] = oFile.offlinestrategy;
			//self.sessionStorage['pwaPagesPolicy'] = 'kodskdsl';

			var res = await self.clients.claim();
			resolve(res);
		})

	}
	//DumpClients();
	//DumpMe();
	//e.waitUntil(self.clients.claim()); 
	e.waitUntil(GetPromise());
}
);

if (m_bDoFetch)
{
	/* Serve cached content when offline */
	///-need sw devtools to be set as offline
	self.addEventListener('fetch',
		function (e) {
			function ResolveProm() {
				return new Promise(async resolve => {
					//var oFile = await GetUserRecFromDBProm();
					var oFile = MySWFuncs.GetUserRecFromDB();

					//var str ='staleWhileRevalidate';
					var str = oFile.offlinestrategy;
					console.log("oFile.offlinestrategy::" + str);
					//console.log("GetUserRecFromDB() oFile:");
					console.log(oFile);
					var resp = null;

					//var str=g_opwapaged.offlinestrategy;

					//return Promise<Response>
					if (str == 'staleWhileRevalidate') {
						resp = await MySWFuncs.HandleFetchStaleWhile(e);
						resolve(resp);
					}
					else if (str == 'NetworkFirst') {
						resp = await MySWFuncs.HandleFetchNetworkFirst(e);
						resolve(resp);
					}
					else if (str == 'CacheFirst') {
						resp = await MySWFuncs.HandleFetchCacheFirst(e);
						resolve(resp);
					}
					else if (str == 'NetworkOnly') {
						resp = await MySWFuncs.HandleFetchNetworkOnly(e);
						resolve(resp);
					}
					else if (str == 'CacheOnly') {
						resp = await MySWFuncs.HandleFetchCacheOnly(e);
						resolve(resp);
					}
					else {
						resp = await MySWFuncs.HandleFetchCacheFirst(e);
						resolve(resp);
					}
				});
			}
			//fetch handler
			console.log('fetch OK');
			e.respondWith(ResolveProm());

			//var strPol=sessionStorage.pwaPagesPolicy;
			//console.log('strPol:' + strPol);
		}
	);
}

if (m_bDoMsgPost) 
{
	self.addEventListener('message',
		(ev) => {
			MySWFuncs.HandleSWMsgs(ev);
		}
	);
}

self.ononline = function () {
	MySWFuncs.HandleOnline();
}

self.onoffline = function () {
	MySWFuncs.HandleOffline();
}

if (m_bDoNoti) {
	self.addEventListener('notificationclick', event => {
		MySWFuncs.DoNotiClick(event);
	});
}

///////
