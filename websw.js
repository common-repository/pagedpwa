//sw for NON-firebase
//
//src:
//-C:\xampp\htdocs\wp\wp-content\plugins\pagedpwa\src\websw.js
//
//-only for dev

(function(exports) {

/**
 * debugger;
 */
  
const idb_ = {db: null};

// Bail out if no indexedDB available
const indexedDB = exports.indexedDB || exports.mozIndexedDB ||
                  exports.msIndexedDB;
if (!indexedDB) 
{
  return;
}

const FILE_STORE_ = 'entries';

const DIR_SEPARATOR = '/';
const DIR_OPEN_BOUND = String.fromCharCode(DIR_SEPARATOR.charCodeAt(0) + 1);


// Core logic to handle IDB operations =========================================

idb_.open = function(dbName, successCallback, opt_errorCallback) 
{
  var self = this;

  // TODO: FF 12.0a1 isn't liking a db name with : in it.
  var strDBNamed=dbName.replace(':', '_');
  var request = indexedDB.open(strDBNamed/*, 1 /*version*/);

  request.onerror = opt_errorCallback || onError;

  request.onupgradeneeded = function(e) 
  {
    // First open was called or higher db version was used.

   // console.log('onupgradeneeded: oldVersion:' + e.oldVersion,
   //           'newVersion:' + e.newVersion);

    self.db = e.target.result;
    self.db.onerror = onError;

    if (!self.db.objectStoreNames.contains(FILE_STORE_)) 
    {
      var store = self.db.createObjectStore(FILE_STORE_/*,{keyPath: 'id', autoIncrement: true}*/);
    }
  };

  request.onsuccess = function(e) 
  {
    self.db = e.target.result;
    self.db.onerror = onError;
    successCallback(e);
  };

  request.onblocked = opt_errorCallback || onError;
};

idb_.close = function() 
{
  idb_.closeDB(this.db);
  this.db = null;
}
idb_.closeDB = function(db) 
{
  db.close();
};

// TODO: figure out if we should ever call this method. The filesystem API
// doesn't allow you to delete a filesystem once it is 'created'. Users should
// use the public remove/removeRecursively API instead.
idb_.drop = function(successCallback, opt_errorCallback) 
{
  if (!this.db) 
  {
    return;
  }
  idb_.dropDB(this.db,successCallback, opt_errorCallback);
}
idb_.dropDB = function(db,successCallback, opt_errorCallback) 
{
  var dbName = db.name;

  var request = indexedDB.deleteDatabase(dbName);
  request.onsuccess = function(e) 
  {
    successCallback(e);
  };
  request.onerror = opt_errorCallback || onError;

  idb_.closeDB(db);
};

idb_.get = function(fullPath, successCallback, opt_errorCallback) 
{
  if (!this.db) 
  {
    return;
  }
  idb_.getDB(this.db,fullPath, successCallback, opt_errorCallback);
}

/**
 * partial key
 */
idb_.getDB = function(db,fullPath,successCallback,opt_errorCallback) 
{
  var tx = db.transaction([FILE_STORE_], 'readonly');

  //var request = tx.objectStore(FILE_STORE_).get(fullPath);
  var range = IDBKeyRange.bound(fullPath, fullPath + DIR_OPEN_BOUND, false, true);
  var request = tx.objectStore(FILE_STORE_).get(range);

  tx.onabort = opt_errorCallback || onError;
  tx.oncomplete = function(e) 
  {
    successCallback(request.result);
  };
};

/**
 * exact key
 */
idb_.getExact = function(fullPath, successCallback, opt_errorCallback) 
{
  if (!this.db) 
  {
    return;
  }
  idb_.getExactDB(this.db,fullPath, successCallback, opt_errorCallback);
}
/**
 * exact key
 */
idb_.getExactDB = function(db,fullPath, successCallback, opt_errorCallback) 
{
  var tx = db.transaction([FILE_STORE_], 'readonly');

  //var request = tx.objectStore(FILE_STORE_).get(fullPath);
  var range = IDBKeyRange.bound(fullPath, fullPath,false,false);
  var request = tx.objectStore(FILE_STORE_).get(range);

  tx.onabort = opt_errorCallback || onError;
  tx.oncomplete = function(e) 
  {
    successCallback(request.result);
  };
};

idb_.DeleteKey=function(fullPath,onOK,onErr)
{
  if (!this.db) 
  {
    return;
  }
  idb_.DeleteKeyDB(this.db,fullPath,onOK,onErr);
}

/**
 * full key
 */
idb_.DeleteKeyDB=function(db,fullPath,onOK,onErr)
{  
  var tx = db.transaction([FILE_STORE_], 'readwrite');
  tx.oncomplete = onOK;
  tx.onabort = onErr || onError;

  var request = tx.objectStore(FILE_STORE_).delete(fullPath);
}

/**
 * partial key
 */
idb_['delete'] = function(fullPath, successCallback, opt_errorCallback) 
{
  if (!this.db) 
  {
    return;
  }
  idb_['deleteDB'](this.db,fullPath, successCallback, opt_errorCallback);
}

/**
 * partial key
 */
idb_['deleteDB'] = function(db,fullPath, successCallback, opt_errorCallback) 
{
  var tx = db.transaction([FILE_STORE_], 'readwrite');
  tx.oncomplete = successCallback;
  tx.onabort = opt_errorCallback || onError;

  //var request = tx.objectStore(FILE_STORE_).delete(fullPath);
  var range = IDBKeyRange.bound(
      fullPath, fullPath + DIR_OPEN_BOUND, false, true);
  var request = tx.objectStore(FILE_STORE_)['delete'](range);
};

idb_.put = function(entry,successCallback,opt_errorCallback) 
{
  if (!this.db) 
  {
    return;
  }
  idb_.putDB(this.db,entry,successCallback,opt_errorCallback);
}

/**
 * entry must also have .fullPath
 */
idb_.putDB = function(db,entry,successCallback,opt_errorCallback) 
{
  var tx = db.transaction([FILE_STORE_], 'readwrite');
  tx.onabort = function (e)
  {
    entry.filesystem=fsWas;
    /**
     * opt_errorCallback || onError;
     */
    opt_errorCallback(e);
  }
  tx.oncomplete = function(e) 
  {
    // TODO: Error is thrown if we pass the request event back instead.
    entry.filesystem=fsWas;
    successCallback(entry);
  };

  /**
   * persist
   * lose entry.filesystem now has m_dbIDB
   * also entry.file
   */
  var fsWas=entry.filesystem;
  entry.filesystem=null;
  var request = tx.objectStore(FILE_STORE_).put(entry,entry.fullPath);
};

// Global error handler. Errors bubble from request, to transaction, to db.
function onError(e) 
{
  switch (e.target.errorCode) 
  {
    case 12:
      console.log('Error - Attempt to open db with a lower version than the ' +
                  'current one.');
      break;
    default:
      console.log('errorCode: ' + e.target.errorCode);
  }

  console.log(e, e.code, e.message);
}

/*
// Clean up.
// TODO: decide if this is the best place for this.
exports.addEventListener('beforeunload', function(e) {
  idb_.db && idb_.db.close();
}, false);
*/

//exports.idb = idb_;

exports._myidb = idb_;


})(self); // Don't use window because we want to run in workers.

var strUrl = self.registration.scope +
	'wp-admin/admin-ajax.php?action=mypwaappyplug_getservvars&sw=1';

//-var g_opwapaged

importScripts(strUrl);
//-incs MyFireBase
//const strJsn = g_opwapaged.ServAuthJsn;
//const firebaseConfig = eval("(" + strJsn + ")");
//console.log(":::::::firebaseConfig::::::::::", firebaseConfig);

/**
 *  var cacheName='WebbyApp: flowersareus';
 */
var cacheName = 'PWAPagesApp: pagedpwa_' + g_opwapaged.shortname;
var strV = g_opwapaged['SwVersion'];
if (strV && strV != '')
	cacheName += `_${strV}`;

var filesToCache1=[];
var filesToCache=filesToCache0.concat(filesToCache1);

var m_bDoPayload=true;
//var m_bDoPayload = false;
var m_bDoUrlUrlRefresh = true;
//IsUrlQueryRefresh
var m_bDoUrAppRefresh = true;
var m_bNotifyByFocus=false;

const app = firebase.initializeApp();
const msg = firebase.messaging();

//return Promise
msg.onBackgroundMessage(function (payLoad) {
	/**
	 * -google:
	 * -and mine
	 */
	const noti = payLoad.notification;
	const bod = noti.body;
	const tit = noti.title;
	const dat = payLoad.data;
	const dat1 = noti.data;
	const from = payLoad.from;

	//N.b google console passes
	//pl.notification with 5 main flds
	//pl.data = { "gcm.n.e": '1' };
	//pl.from = "834872924553";
	//me std msg:
	//pl.data = oDat['data'];

	//noti: 5 main flds
	//var pg = dat.urlpage;
	return MySWFuncs.GetPageNoti(noti, dat);
	//...
}
);

/**
 *  Start the service worker and cache all of the app's content 
 *  online so cache the files
 */
self.addEventListener('install',async function(e) 
{



	function GeMeProm() {
		return caches.delete(cacheName).
			then(function (dd) {


				var strUrl = self.registration.scope +
					'wp-admin/admin-ajax.php?action=mypwaappyplug_getcacheurl';
				return fetch(strUrl);
			}
			).
			then(async res => {
				const filesToCache = await res.json();
				console.log('cache.addAll OK');
				const cache=await caches.open(cacheName);


				return cache.addAll(filesToCache);
			}
			)
			.catch((err) => {
				const str = err['message'] ? err['message'] :"cache.addAll FAILED";
				console.log("error for register('./websw.js')");
				console.log("str:" + str);
				console.log(filesToCache);
				console.log("@urls are not getting the requests, please specify correct cache files");
			}
			);

	}

	e.waitUntil(GeMeProm());
});

self.addEventListener('activate', e => {
	console.log('activate');


	function GetPromise() {
		return new Promise(async resolve => {
			//var oFile = await Get UserRecFromDB();
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


/* Serve cached content when offline */
/**
 * need sw devtools to be set as offline
 */
self.addEventListener('fetch', 
  function(e) 
  {
	function ResolveProm()
	{
		return new Promise(async resolve=>
		{
			//var oFile = await GetUserRecFromDBProm();
			var oFile = MySWFuncs.GetUserRecFromDB();

			//var str ='staleWhileRevalidate';
			var str = oFile.offlinestrategy;
			//console.log("oFile.offlinestrategy::"+str);
			//console.log("GetUserRecFromDB() oFile:");
			//console.log(oFile);
			var resp=null;

			//var str=g_opwapaged.offlinestrategy;

			//return Promise<Response>
			if (str == 'staleWhileRevalidate') 
			{
				resp = await MySWFuncs.HandleFetchStaleWhile(e);
				resolve(resp);
			}
			else if (str == 'NetworkFirst') 
			{
				resp = await MySWFuncs.HandleFetchNetworkFirst(e);
				resolve(resp);
			}
			else if (str == 'CacheFirst') 
			{
				resp = await MySWFuncs.HandleFetchCacheFirst(e);
				resolve(resp);
			}
			else if (str == 'NetworkOnly') 
			{
				resp = await MySWFuncs.HandleFetchNetworkOnly(e);
				resolve(resp);
			}
			else if (str == 'CacheOnly') 
			{
				resp = await MySWFuncs.HandleFetchCacheOnly(e);
				resolve(resp);
			}
			else
			{
				resp = await MySWFuncs.HandleFetchCacheFirst(e);
				resolve(resp);
			}
		});
	}
    //fetch handler
    //console.log('fetch OK');
	e.respondWith(ResolveProm());

	//var strPol=sessionStorage.pwaPagesPolicy;
	//console.log('strPol:' + strPol);
  }
);

self.addEventListener('message', 
	(ev) => {
		MySWFuncs.HandleSWMsgs(ev);
	}
);

self.ononline = function() 
{
	MySWFuncs.HandleOnline();
}

self.onoffline = function() 
{
	MySWFuncs.HandleOffline();
}

self.addEventListener('notificationclick', event => {
	MySWFuncs.BadgeDown();	
	MySWFuncs.DoNotiClick(event);
});

////////////









self.addEventListener("push", (event) => {
	MySWFuncs.BadgeUp();
});

