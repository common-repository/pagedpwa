function MySWFuncs()
{
}

MySWFuncs.DoNotiClick=function(ev)
{
    function AjaxUrlActionServFunc(ajaxurl, strAction, strFunc, strObjFunc, objData)
    {
        var strUrl = ajaxurl + "?action=" + strAction;
        objData.action = strAction;
        objData.function = strFunc;
        objData.strObjFunc = strObjFunc;
        var strJsn = JSON.stringify(objData);
 
        var rq = new Request(strUrl);
        rq.headers.append("Content-type", "application/json")

        var rii = new Object();
        rii.body = strJsn;
        rii.method = 'post';
        fetch(rq, rii);
    }

    function zzAjaxUrlActionServFunc(ajaxurl, strAction, strFunc, strObjFunc, objData)
    {
        try {
            objData.action = strAction;
            objData.function = strFunc;
            ///-m_strId
            objData.strObjFunc = strObjFunc;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxurl, true);
            xhr.setRequestHeader("Content-type", "application/json");

            xhr.onload = function () {
                var tt = typeof this.response;
            };
            xhr.onerror = function () {
            };
            xhr.onabort = function () {
            };

            var strJsn = JSON.stringify(objData);
            xhr.send(strJsn);
        }
        catch (e) {
        }
    }

    ///-strfunc is server func,strObjFunc func pass back,
    xAjaxUrlActionServFunc = function (ajaxurl, strAction, strFunc, strObjFunc, objData) {

        objData.action = strAction;
        objData.function = strFunc;
        ///-m_strId
        objData.strObjFunc = strObjFunc;

        var oSend=
        {
            url: ajaxurl,
            data: objData,
            type: 'POST',
            error: function (e, r, h) {
            }
        }
        if (strObjFunc)
        {
            oSend.success = function (response)
            {
                var obj = null;
                if (response != "")
                    obj = JSON.parse(response);

                var oCtl = g_arrControls[obj.m_strId];
                if (!oCtl) {
                    return;
                }
                var strMeth = obj.strObjFunc;
                oCtl[strMeth](obj);                
            }
        }
        //$.ajax(oSend);
        //...
    }

    ///-notificationclick
    async function DoNotiClick(event) {
        const noti = event.notification;
        const action = event.action;
        var data = noti.data;
        const reply = event.reply;
        if (data===null)
            data={};

        if (action === 'close') {
            noti.close();
        }
        else {
            noti.close();
        }

        var strKeys = "actions,badge,body,dir,icon,image,lang,renotify," +
            "requireInteraction,scenario,silent,tag,timestamp,title,vibrate";
        var notiO = {};
        var arrKeys = strKeys.split(',');
        var k, k2;
        for (k in arrKeys) {
            k2 = arrKeys[k];
            notiO[k2] = noti[k2];
        }

        if (data.recid)
        {
            var oData=
            {
                "noti": notiO,
                "notiaction": action,
                "data": data,
                "reply": reply,
            };
            var strUrl = self.registration.scope +'wp-admin/admin-ajax.php';
            AjaxUrlActionServFunc(strUrl, 'mypwaappypro_actionNoti', null, null, oData);
            //...
        }

        var fi = {
            "fullPath": "DoNotiClick",
            "noti": notiO,
            "action": action,
            "data": data,
            "reply": reply,
        };
        var prom = MySWFuncs.WritePageDataProm(fi);
        //...
        event.waitUntil(prom);
        await prom;

        //showMyNotif
        if (data.urlfull)
        {
            strUrl = encodeURI(data.urlfull);
            const promO = clients.openWindow(strUrl);
            //...
            return promO;
        }
        
        var strPg = data.page;
        var strUrl = self.registration.scope;
        if (strUrl[strUrl.length - 1] != '/')
            strUrl += "/";
        str = strUrl + "myappyin/?";
        str += "page=run&appslug=" + strPg;
        //if (m_bDoPayload)
        //	str += "&txt=" + title + "&body=" + body;
        //else
        //	str + "&tag=" + data.funcserv;

        //var strUrl = new URL(str, self.location.origin).href;
        strUrl = encodeURI(str);
        const prom2 = clients.openWindow(strUrl);
        //...
        return prom2;
    }

    //
    DoNotiClick(ev);
}

//return Promise<bool>
MySWFuncs.WritePageDataProm = function (fi) {
    return new Promise(resolve => {
        self._myidb.open('webswpage',
            function (e) {
                var db = e.target.result;
                self._myidb.putDB(db, fi,
                    function () {
                        console.log('WritePageData ok');
                        console.log(fi);
                        self._myidb.closeDB(db);
                        resolve(true)
                    },
                    function () {
                        self._myidb.closeDB(db);
                        resolve(false)
                    }
                );
            },
            function () {
                console.log("db failed");
                resolve(false)
            }
        );
    }
    );
}

//return Promise<void>
//strEvt: 'periodicsync' | 'push'
//txt=>data.funcserv? future
//data.noti
MySWFuncs.showMyNotif = async function (data, txt, strEvt) {
    //noti,title,
    var o = data.noti;
    /*if (!o) 
    {
        o = { "data": {} };
        o.data.funcserv = txt;
    }
    else {
        o.data = data['click'];
        o.data.funcserv = txt;
    }*/
    //o.data = {
    //	"data": data.data
    //};
    o.data = data.data;

    //var strScope = self.registration.scope;
    //var str = "(" + strEvt + "::" + strScope + ")";

    //var str = firebase.GetUrlNotify(strEvt, data.title);
    if (strEvt=='win')
    {
        const reg = await navigator.serviceWorker.getRegistration();
        reg.showNotification(data.title, o);
    }
    else
        return self.registration.showNotification(data.title, o);
}

///-return Promise<clients>
//noti: 5 main flds
//var pg = dat.urlpage;
MySWFuncs.GetPageNoti = async function (noti, dat,strFrom) {
    //http://localhost//wp/appyapps/wp-content/plugins/appystore/apps/userfiles/images/userappwpadminme_fireadminmsg_img256.png?page=notifyappy
    //inmsg_img256.png?page=notifyappy

    if (!strFrom)
        strFrom ='push';
    var pg=null;
    var oData = {};
    if (dat)
    {
        pg = dat.urlpage;
        if (dat.urlfull) {
            oData.data = dat;
            oData.noti = noti;
            oData.title = noti.title;
            return MySWFuncs.showMyNotif(oData, '', strFrom);
        }
    }

    /*
    var o, nQ, str = noti.icon;
    //var o = {}, nQ, str = noti.icon;
    var pg = null;
    if (str) {
        nQ = str.indexOf('?');
        if (nQ >= 0)
            o = MySWFuncs.ParseQueryStr(str.substr(nQ + 1));
        if (o.page)
            pg = o.page;
    }
    */

    /*if (!pg) {
        str = noti.image;
        nQ = str.indexOf('?');
        if (nQ >= 0)
            o = MySWFuncs.ParseQueryStr(str.substr(nQ+1));
        if (o.page)
            pg = o.page;
    }*/

    var strTitle=null;
    if (pg)
        strTitle = ShowPageNoti(noti, dat, pg, oData);
    if (strTitle) {
        oData.data = dat;
        if (typeof oData.data=='undefined')
            oData.data={};
        oData.data.page=pg;
    }
    else
    {
        oData.data = dat;
        if (typeof oData.data == 'undefined')
            oData.data = {};
        oData.data.page='notifyappy';
        oData.noti = noti;
        oData.title = noti.title;
    }
    //return self.registration.showNotification(strTitle, oNotiShow);
    return MySWFuncs.showMyNotif(oData, '', strFrom);
    //return false;
}

///-after signalled from match
///-respCache is from cache, null or ok
///-e is the fetch event
///-return Promise<Response>
MySWFuncs.HandleFetchProm = async function (e, respCache) {
    try {
        // only deal with requests on the same domain.
        let request = e.request;
        let url = new URL(request.url);
        if (url.origin !== location.origin) {
            //remote .fail
            return fetch(e.request)
                .catch(function (reason) {
                    console.log("HandleFetchProm.catch:" + url + ",reason:" + reason);
                })
        }

        if (m_bDoUrlUrlRefresh) {
            var prom = MySWFuncs.IsUrlQueryRefresh(e, respCache);
            if (prom) {
                return prom;
            }
        }

        var promOK = await MySWFuncs.IsUrlQueryReset(e, respCache);
        if (promOK) {
            return promOK;
        }
        promOK = await MySWFuncs.IsUrlQueryCache(e, respCache);
        if (promOK) {
            return promOK;
        }


        if (m_bDoUrAppRefresh) {
            ///[#OldComment begin]
            //NO ? in url
            //and in cache
            ///[#OldComment end]

            var strUrlJs = e.request.url;
            var arrParts = strUrlJs.split('/');
            var nParts = arrParts.length;
            //app ?
            if (arrParts[nParts - 1] == 'app') {
                console.log('@@@@@@IT APP:' + strUrlJs);
                ///app home page, (try)get from server
                return fetch(e.request).then(
                    async function (resIt) {
                        ///offline?
                        if (resIt == null)
                            return respCache;
                        return resIt;
                    },
                    function () {
                        return respCache;
                    });
            }
        }

        if (!respCache) {
            //not in cache
            return fetch(e.request);
        }

        //not action,not ?
        var strUrlCache = e.request.url;
        if (MySWFuncs.IsUrlExcluded(strUrlCache))
        {
            return fetch(e.request);
        }

        //cache first
        return respCache ||
            fetch(e.request).catch(reason => {
                console.log('not in cache AND not online');
                return respCache;
            });
    }
    catch (exc) {
        console.log('not in cache AND not online');
        return respCache;
    }
}

//return Promise<Response>
MySWFuncs.IsUrlQueryReset = async function (e, resCache) {
    var strUrlJs = e.request.url;
    var qo = null;
    var nLast = strUrlJs.indexOf('?');
    if (nLast < 0)
        return Promise.resolve(false)

    var strUrl = strUrlJs.substr(0,nLast);
    var arr=strUrl.split('/');
    arr.pop();
    var strSub=arr.pop();

    var strParams = strUrlJs.substr(nLast + 1);
    var qo = MySWFuncs.ParseQueryStr(strParams);
    if (strSub !='noservwork' || !qo || qo['command'] != 'resetsw')
        return Promise.resolve(false)

    var regGlo=self;
    //var reg=regGlo.getRegistration();
    //var reg2 = regGlo.registration();
    var reg = regGlo.registration;
    var strScope = reg.scope;

    var ret=await reg.unregister();
    console.log('ret', ret, 'strScope', strScope, "strUrl", strUrl);

    //myOptions = { status: 200, statusText: "SuperSmashingGreat!" };
    //var res=new Response(body, options);
    var hdrs = new Headers();
    hdrs.append("content-type", "text/html");
    myOptions = { status: 200, statusText: "ListCache!", headers: hdrs };

    //var strBody = `Return Code:${ret}<br>Scope:${strScope}<br>Url:${strUrl}`;
    var strBody = `Return Code:${ret}<br><br>Scope:${strScope}<br>`;
    var res = new Response(strBody, myOptions);

    //return Promise.resolve(true);
    return res;
}

//return Promise<Response>
MySWFuncs.IsUrlExcluded = function (strUrlCache) {
    return m_arrExcluded.indexOf(strUrlCache)>=0?true:false;
}

//return Promise<Response>
/// Promise<false> if not
MySWFuncs.IsUrlQueryCache = async function (e, resCache) {
    var strUrlJs = e.request.url;
    var qo = null;
    var nLast = strUrlJs.indexOf('?');
    if (nLast < 0)
        return Promise.resolve(false);

    var strUrl = strUrlJs.substr(0,nLast);
    var arr=strUrl.split('/');
    arr.pop();
    var strSub=arr.pop();

    var strParams = strUrlJs.substr(nLast + 1);
    var qo = MySWFuncs.ParseQueryStr(strParams);
    if (strSub !='swcache' || !qo || qo['command'] != 'listcache')
        return Promise.resolve(false)

    var c = await caches.open(cacheName);
    var o,k,arr=await c.keys();
    var str='';
    for (k in arr)
    {
        o = arr[k];
        str += `<div>${o.url}</div>`;
    }

    //myOptions = { status: 200, statusText: "ListCache!", headers: ['content-type:text/html'] };

    var hdrs = new Headers();
    hdrs.append("content-type", "text/html");
    myOptions = { status: 200, statusText: "ListCache!", headers: hdrs };

    //myOptions = { status: 200, statusText: "ListCache!"};
    //var res=new Response(body, options);
    var strHead="<html><head></head><body>";
    var strBody = `Cache:<br><br><code>${str}</code>`;
    str = `${strHead}${strBody}</html>`;
    var res = new Response(str, myOptions);
    //res.headers.append("content-type", "text/html");


    //return Promise.resolve(true);
    return res;
}

//return promise<fetch>
//or null
MySWFuncs.IsUrlQueryRefresh = function (e, resCache) {
    //if (e.request.url=='http://localhost/wp/appyapps/usrappwpadminme/turchickit/app')
    ///-var strUrlJs=
    //'http://localhost/wp/appyapps/wp-admin/admin-ajax.php?action=appyplug_appaction&hmpg=4804';
    var strUrlJs = e.request.url;
    var qo = null;
    var nLast = strUrlJs.indexOf('?');
    if (nLast < 0)
        return null;

    var strParams = strUrlJs.substr(nLast + 1);
    var qo = MySWFuncs.ParseQueryStr(strParams);
    if (!qo || qo['action'] != 'appyplug_appaction' || qo['hmpg'] == '')
        return null;

    ///-if (e.request.url==strUrlJs)
    //var strUId=paramsServed['wpuserid'];
    ///-must be currently logged off, chk with server for auto log on
    //if (qo && qo['action'] == 'appyplug_appaction' && qo['hmpg'] != '') 

    ///? in url
    ///-always online
    //return fetch promise
    return fetch(e.request).then(
        async function (resIt) {
            ///online user url
            if (resIt == null)
                return resCache;

            //strTxt=await resIt.text();

            /*var myclients=await self.clients.matchAll();
            var k,w;
            for (k in myclients)
            {
              w=myclients[k];
              ////window not reg the onmessage yet, cant still building page
              //w.postMessage('swhere');
            }*/
            return MySWFuncs.HandleFetchClone(e, resIt);
        },
        function () {
            ///-not online but uesr url
            console.log('user url but offline');
            return resCache;
        }
    );
}

//return promise<fetch>
//e is the fetch event
//resIt : fetch response (ie remote one)
MySWFuncs.HandleFetchClone = async function (e, resIt) {
    ///-var res2=new Response(resIt);
    var resTxt = resIt.clone();
    var strJs = await resTxt.text();

    //var strEval = "({" + strJs + "})";
    //var paramsServed={};
    var arrJs = strJs.split(';');
    var arrFnd = arrJs.filter((str) => {
        var arrSplit = str.split('=');
        return arrSplit[0] == 'paramsServed.wpuserid' ? true : false;
    }
    );

    var strUId = null;
    if (arrFnd.length == 1) {
        var strFnd = arrFnd[0];
        var arrSplit = strFnd.split('=');
        strUId = arrSplit[1];
    }
    //eval(strEval);
    //var strUId=!self['paramsServed']?null:self['paramsServed']['wpuserid'];
    if (strUId && strUId > 0) {
        //re-cache js with new userid (ie >0)
        //var res2 = resIt.clone();
        var c = await caches.open(cacheName);
        //cache.addAll(filesToCache);
        //c.put(strTxt);
        //c.put(strUrlJs,res2);

        var res3 = new Response(strJs);
        c.put(e.request, res3);
        //c.put(strUrlJs,strTxt);
        console.log('#@sw clone() OK logged on(' + strUId + '):: & cache.put() OK');

        ///-may be start page sub dir, that kicked off appyplug_appaction
        ///-http://localhost/wp/appyapps/usrappwpadminme/wagme/app
        ///-http://localhost/wp/appyapps/usrappwpadminme/wagme/app/singlegroup/Pubs

        var resBack = await c.match(e.request)
        if (resBack) {
            var strJsBk = await resBack.text();
            var strJsBk0 = strJsBk;
        }
    }
    else {
        ///N.B. NOT logges on, response may be null
        var strJs00 = 'response=null';
        if (resIt) {
            var resChk00 = resIt.clone();
            strJs00 = await resChk00.text();
        }
        console.log("@@devflower url strJs00:" + strJs00);
        console.log('#sw clone(): NOT logged on: NO cache.put() OK');
        ///-so return cache response
        //return response;

        //return resIt || fetch(e.request);
        return resIt;
    }

    ///-N.B. must return response NOT resIt!!
    //return response;
    //var e=resIt;
    //return resIt;
    return resIt;
}


//return Promise<Response>
//ok
MySWFuncs.HandleFetchCacheFirst = function (e) {
    //but wait for signal:
    return caches.match(e.request).then(
        function (response) {
            return MySWFuncs.HandleFetchProm(e, response);
        }
    );
}

//return Promise<Response>
//ok
MySWFuncs.HandleFetchNetworkFirst = function (e) {
    var prom = MySWFuncs.HandleFetchProm(e, null);
    prom.then(
        r => {
            if (r.status == 404)
                return caches.match(e.request);
            else
                return prom;
        },
        r => {
            return caches.match(e.request);
        }
    )
    .catch(err => {
        return caches.match(e.request);
    }
    );
    return prom;
}

//return Promise<Response>
//ok
MySWFuncs.HandleFetchNetworkOnly = function (e) {
    return MySWFuncs.HandleFetchProm(e, null);
}

//return Promise<Response>
//ok
MySWFuncs.HandleFetchCacheOnly = function (e) {
    //but wait for signal
    return caches.match(e.request);
}

//return Promise<Response>
//@@
//staleWhileRevalidate
MySWFuncs.HandleFetchStaleWhile = function (e) {

    async function PutIt(res) {
        var c = await caches.open(cacheName);
        //var res = new Response(r);
        //var res = await r.clone();
        c.put(e.request, res);
    }

    //return Promise<Response>
    function FetchPutIt(e) {
        var prom = MySWFuncs.HandleFetchProm(e, null);
        prom.then(
            async r => {
                if (e.request.method.toLowerCase() != 'get')
                    return prom;

                //ok?
                if (!r)
                    return prom;
                if (r.status != 404) {
                    var res = await r.clone();
                    await PutIt(res);
                    //...
                    return r;
                }
                return prom;
            },
            r => {
                return prom;
            }
        )
        .catch(err => {
            return prom;
        }
        );
        return prom;
    }

    //but wait for signal:
    return caches.match(e.request).then(
        function (response) {
            //ok cached
            if (response)
                //return MySWFuncs.HandleFetchProm(e, response);
                return response;
            else {
                //no
                return FetchPutIt(e);
                //...
                //return Promise.resolve();
            }
        },
        function () {
            //no
            return FetchPutIt(e);
            //...
            //return Promise.resolve();
        }
    );
}

MySWFuncs.GetUserRecFromDB = function()  {
    return g_opwapaged;
}

//resolve to file obj
//or null if error
MySWFuncs.GetUserRecFromDBProm = function() {
    return new Promise(resolve => {
        //idb_.open = function(dbName, successCallback, opt_errorCallback) 
        self._myidb.open('websw',
            function (e) {
                var db = e.target.result;
                //var fi={"fullPath":"userid","userid":o.userid};
                //idb_.getDB = function(db,fullPath,successCallback,opt_errorCallback) 

                self._myidb.getDB(db, 'userid',
                    function (result) {
                        self._myidb.closeDB(db);
                        //var fi=result;
                        resolve(result);
                    },
                    function () {
                        self._myidb.closeDB(db);
                        resolve(null);
                    }
                );
            },
            function () {
                console.log("db failed");
                resolve(null);
            }
        );
    }
    );
}

MySWFuncs.HandleSWMsgs = function (ev) 
{
    //    var o={"msg":"setuserid","userid":nWpUsr};

    console.log("Cache HandleSWMsgs: #1");
    const o = JSON.parse(ev.data);

    /*if (o.msg == 'testme') 
    {
        debugger;

        var wc=ev.source;
        //wc.addEventListener('message', function (ev) {
        //	debugger;
        //});

        var oCmd={cmd:"gotpushmsg",title:"mytitle"};
        var strJsn = JSON.stringify(oCmd);
        wc.postMessage(strJsn);
        return;
    }*/

    if (o.msg == 'setuserid') {
        //self.m_ nUserId=o.userid;
        //self.localStorage.m_ nUserId=o.userid;
        return;
    }

    if (o.msg === 'ClearCache') {
        console.log("Cache HandleSWMsgs: #2");

        caches.keys().then(function (cacheNames) {
            console.log("Cache font keys: #2/b");
            var a = cacheNames;
        });


        caches.delete(o.cacheName)
            .then((success) => {
                console.log("Cache removal status: " + success);
            })
            .catch((err) => {
                console.log("Cache removal error: ", err);
            });
    }
}

MySWFuncs.HandleOnline = function () 
{
    console.log('Your worker is now online');
}

MySWFuncs.HandleOffline = function () {
    console.log('Your worker is now onoffline');
}

MySWFuncs.ParseQueryStr = function (str) {
    var arrMap = new Array();

    var arr = str.split("&");
    var i, nCount = arr.length;
    var arrPair;
    var fg = new String();
    var strName, strValue
    for (i = 0; i < nCount; i++) {
        arrPair = arr[i].split("=");
        strName = arrPair[0];
        strValue = arrPair[1];
        arrMap[strName] = strValue;
    }
    return arrMap;
}

//static 
MySWFuncs.getFullSubs = function (subs) {
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

MySWFuncs.GetIDBRecFromDBProm = function (strDb, strKey) {
    return new Promise(resolve => {
        //idb_.open = function(dbName, successCallback, opt_errorCallback) 
        self._myidb.open(strDb,
            function (e) {
                var db = e.target.result;
                //var fi={"fullPath":"userid","userid":o.userid};
                //idb_.getDB = function(db,fullPath,successCallback,opt_errorCallback) 

                self._myidb.getDB(db, strKey,
                    function (result) {
                        self._myidb.closeDB(db);
                        //var fi=result;
                        resolve(result);
                    },
                    function () {
                        self._myidb.closeDB(db);
                        resolve(null);
                    }
                );
            },
            function () {
                console.log("db failed");
                resolve(null);
            }
        );
    }
    );
}

MySWFuncs.MySetBadge = function (nNum) {
    if (navigator.setAppBadge)
        navigator.setAppBadge(nNum);
    else if (navigator.setExperimentalAppBadge)
        navigator.setExperimentalAppBadge(nNum);
    else if (window.ExperimentalBadge)
        window.ExperimentalBadge.set(nNum);
}

MySWFuncs.MyClearBadge = function () {
    if (navigator.clearAppBadge)
        navigator.clearAppBadge();
    else if (navigator.clearExperimentalAppBadge)
        navigator.clearExperimentalAppBadge();
    else if (window.ExperimentalBadge)
        window.ExperimentalBadge.clear();
}

MySWFuncs.SetClrBadge = function () {
    if (MySWFuncs.m_nReadMe && MySWFuncs.m_nReadMe > 0)
        MySWFuncs.MySetBadge(MySWFuncs.m_nReadMe);
    else
        MySWFuncs.MyClearBadge();
}

MySWFuncs.BadgeUp = function () {
    MySWFuncs.m_nReadMe++;
    MySWFuncs.SetClrBadge();
}

MySWFuncs.BadgeDown = function () {
    MySWFuncs.m_nReadMe--;
    if (MySWFuncs.m_nReadMe < 0)
        MySWFuncs.m_nReadMe = 0;
    MySWFuncs.SetClrBadge();
}

MySWFuncs.m_nReadMe = 0;
