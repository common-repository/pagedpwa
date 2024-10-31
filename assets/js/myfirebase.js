const myfirebase = new MyFireBase();
var firebase = null;

var g_bGetTokenSim=true;
var bIsRunLocal = self.location.hostname == "localhost" ? true : false;
g_bGetTokenSim = bIsRunLocal?true:false;
g_bGetTokenSim = false;
//////////
////////g_bGetTokenSim = false;

//class MyMsgPushy
function MyMsgPushy() 
{
    this.m_cbBackMsg = null;
    this.m_cbForeMsg=null;

    self.addEventListener('push',async (event) => {
        //PushMessageData
        //var json=event.data.json();
        var txt = event.data.text();
        //may be null
        //txt=encodeURI(txt);

        /*var bWantNotifyProm=false;
        if (txt && txt!='.')
          bWantNotifyProm=true;*/

        var bWantNotifyProm = true;
        //assumes for now poked from dev tools
        //const bGotPay = m_bDoPayload;
        const bGotPay=true;
        if (bGotPay)
        {
            //pl
            //chk foreground or background
            const bDoBack = true;

            if (bDoBack) 
            {
                //background
                ////////const msg = firebase.getMsg();
                var cb = msg.GetBackMsgCB();

                var oDat = JSON.parse(txt);

                var pl = {};


                if (oDat['notification']) {
                    const noti = oDat['notification'];
                    pl.notification = {
                        body: noti.body,
                        title: noti.title,
                        icon: noti.icon,
                        image: noti.image,
                        badge: noti.badge,
                        //data: 0
                    };
                }
                else
                {
                    pl.notification = {
                        body: oDat.body,
                        title: oDat.title,
                        icon: oDat.icon,
                        image: oDat.image,
                        badge: oDat.badge,
                        //data: 0
                    };
                }

                //pl.data = { "gcm.n.e": '1' };
                //pl.data = {"urlpage":txt};
                pl.data = oDat['data'];
                if (typeof pl.notification.data=='undefined')
                    pl.notification.data={};
                var k;
                for (k in pl.data)
                {
                    pl.notification.data[k] = pl.data[k];
                }

                var prom = cb(pl);
                event.waitUntil(prom);
            }

        }
    });




}

MyMsgPushy.prototype.onBackgroundMessage = function (cb) {
    this.m_cbBackMsg = cb;
}

MyMsgPushy.prototype.GetBackMsgCB = function (cb) 
{
    return this.m_cbBackMsg;
}

MyMsgPushy.prototype.onMessage = function (cb)
{
    this.m_cbForeMsg=cb;

    navigator.serviceWorker.addEventListener('message',
        (ev) => {
            var strJsn = ev.data;
            var o = JSON.parse(strJsn);
            switch (o.cmd) {
                //		var oCmd={cmd:"gotpushmsg",title:"mytitle"};
                case 'gotpushmsg':
                    //MsgText("Reads:" + o.title,"gotpushmsg");
                    console.log(o);
                    const msg = myfirebase.getMsg();
                    if (msg) {
                        var cb = msg.GetForeMsgCB();
                        if (cb)
                            cb(o.payload);
                    }
                    break;
            }
        }, true
    );
}

MyMsgPushy.prototype.GetForeMsgCB = function (cb) 
{
    return this.m_cbForeMsg;
}

MyMsgPushy.prototype.getToken = function (subs) 
{
    function ArrBufToBase64(buffer) {
        var binary = '';
        var bytes = new Uint8Array(buffer);
        var len = bytes.byteLength;
        for (var i = 0; i < len; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return window.btoa(binary);
    }

    /*if (!g_bGetTokenSim)
    {
        return navigator.serviceWorker.ready
            .then(function (reg) {
                return reg.pushManager.subscribe(subs);
            });
    }*/

    return new Promise(resolve=>
    {
        if (!g_bGetTokenSim) {
            return navigator.serviceWorker.ready
                .then(function (reg) {
                    return reg.pushManager.subscribe(subs);
                })
                .then(function(subMe){
                    /*var o = {};
                    o.endpoint = subMe.endpoint;
                    o.keys = {
                        "p256dh": "BIPUL12DLfytvTajnryr2PRdAgXS3HGKiLqndGcJGabyhHheJYlNGCeXl1dn18gSJ1WAkAPIxr4gK0_dQds4yiI=",
                        "auth": "FPssNDTKnInHVndSTdbKFw=="
                    };*/

                    const o = {
                        endpoint: subMe.endpoint,
                        keys: {
                            p256dh: ArrBufToBase64(subMe.getKey('p256dh')),
                            auth: ArrBufToBase64(subMe.getKey('auth')),
                        },
                    };
                    resolve(o);
                })
                .catch(err=>{
                    MyMsgPushy.MyText(err.message, err.code);
                });
        }
        else
        {
            //need SimSubscribe            
        }

    });

}

MyMsgPushy.MyText = function (str,strTitle) 
{
    if (window['MsgText'])
        MsgText(str, strTitle);
    else
        alert(strTitle + " : "+str);
}

MyMsgPushy.prototype.deleteToken = function (subs) {
    if (!g_bGetTokenSim)
    {
        navigator.serviceWorker.ready
            .then(function (reg) {
                reg.pushManager.getSubscription().then(function (subs) {
                    if (!subs) {
                        MyMsgPushy.MyText('You have no subscription at the momment', 'OK');
                        return null;
                    }
                    const prom = subs.unsubscribe();
                    prom.then(function () {
                        var sub_id = subs.endpoint.split('fcm/send/')[1];
                        ///window.DelSubscription(sub_id, 'DelSubscriptionCtd');
                        return sub_id;
                    })
                    .catch(function (err) {
                        MyMsgPushy.MyText(err.message, 'unsubscribe FAILED : ' + err.code);
                        return null;
                    });
                });
            });
    }

    return new Promise(resolve => {
        //sim it
        resolve(null);
    });

}

//class MyFireBase
function MyFireBase() {
    this.m_msg = null;
}

MyFireBase.prototype.initializeApp = function (init) {
}

MyFireBase.prototype.messaging = function () 
{
    if (this.m_msg)
        return this.m_msg;

    //self.addEventListener('push'
    var msg = new MyMsgPushy();
    this.m_msg = msg;
    return msg;
}



MyFireBase.prototype.getMsg = function () {
    return this.m_msg;
}

