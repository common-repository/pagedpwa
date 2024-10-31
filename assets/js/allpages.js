

//window.onbeforeunload = function () { };
window.addEventListener("beforeunload",
function(e)
{
    ///-strfunc is server func,strObjFunc func pass back,
    function AjaxActionServFunc(strAction,strFunc,strObjFunc,objData,objInit)
    {
        ///-debugger;
        var postid=-2;
        var ajaxurl=g_opwapaged.ajax_url
        ///-g_funcCtd=funcCtd;
    
        objData.action=strAction;
        objData.function=strFunc;
        ///-m_strId
        objData.strObjFunc=strObjFunc;
        objData.postid=postid;
    
        ///-debugger;
        jQuery.post(ajaxurl, objData, 
            function(response) 
            {
                var obj=null;
                if (response!="")
                    obj=JSON.parse(response);
    
                var oCtl=g_arrControls[obj.m_strId];
                if (!oCtl)
                {
                    return;
                }
                var strMeth=obj.strObjFunc;
                oCtl[strMeth](obj);
    
                ///-g_funcCtd(obj);
            }).fail(function(xh,strErr,strMsg)
            {
                var ooo=objData;
                alert("allpages_fail-edas2");
                debugger;
            });
    
    }

    var CreateDummyCtl=function(strKey)
    {
        //var strKey=strKey;
        var ctl={"m_strId":strKey};
        if (typeof window.g_arrControls=='undefined')
          window.g_arrControls={};
        g_arrControls[strKey]=ctl;
        return ctl;
    }

    function DelSubscription(sub_id,strFuncCtd)
    {
      var uid=-1;
      var postid=-1;

      var curpostid=-1;
      var nWpUsr=g_opwapaged.ocustuser.data.ID;
      var wpuserid=nWpUsr;
      //

      var data={
          //ctlid:this.m_strId,
          //m_strId:this.m_strId,
          //ctluid:uid,
          postid:postid,
          curpostid:curpostid,
          wpuserid:wpuserid,

          sub_id:sub_id,
          href:location.href
      };

      debugger;
      var ctl=CreateDummyCtl('keyDelSubscriptionCtd');
      ctl.DelSubscriptionCtd=function(obj)
      {
        //strFuncCtd
        DelSubscriptionCtd(obj);
      }
      data.m_strId=ctl.m_strId;

      var oo=window["ctlsParams"];
      //var strAct="appyplug_action";
      var strAct="mypwaappyplug_action";
      //MyAjaxActionServFunc(g_opwapaged.ajax_url,strAct,'DelSubscription',strFuncCtd,data,oo);
      AjaxActionServFunc(strAct,'DelSubscription',strFuncCtd,data,oo);
    }
    
    var DelSubscriptionCtd=function(obj)
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
        console.log("DelSubscriptionCtd Ctd here");
        //update ui
    }

    //
    DelSubscription('','DelSubscriptionCtd');

    /*
    var confirmationMessage = "Leaving this page will result in any unsaved data being lost. \r\n"+
        "Please click the save button.\r\n"+
        "Are you sure you want to continue?";
    (e || window.event).returnValue = confirmationMessage;
    ///-Gecko + IE
    return confirmationMessage; 
    ///-Webkit, Safari, Chrome etc.
    */

    return "";

},true);

