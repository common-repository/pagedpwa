      ///
        window.addEventListener('message', 
            (ev) => 
            {
              function GetIDBRecFromDBProm(strDb, strKey) {
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



                //HandleSWMsgs(ev);
                var strJsn=ev.data;
                var o=JSON.parse(strJsn);
                var strCmd=o.cmd;
                if (strCmd=='swhere')
                {
                  console.log("frontonmsg::message : swhere");
                  //MsgText('adminmani','swhere');
                }
                else if (strCmd=='winload')
                {
                  console.log("frontonmsg::message : winload now posting...");
                  //MsgText('adminmani::message','winload');
      
                  var nWpUsr=g_opwapaged.ocustuser.data.ID;
                  var oMsg={"cmd":"pwahere","userid":nWpUsr,
                    "paid":g_opwapaged.paid,"rights":g_opwapaged.rights};
                  var strJson=JSON.stringify(oMsg);
                  ev.source.postMessage(strJson,"*");
                }
                else if (strCmd == 'do_GetIDBRecFromDBProm') {
                  GetIDBRecFromDBProm(o.strDb, o.strKey)
                    .then(res => {
                      var oMsg = {
                        "cmd": "done_GetIDBRecFromDBProm",
                        "result": res,
                      };
                      var strJson = JSON.stringify(oMsg);
                      ev.source.postMessage(strJson,"*");
                  });
                return;
              }



              }
        );

