jQuery(
    function($)
    {
        window.addEventListener('message', 
        (ev) => 
        {
          //HandleSWMsgs(ev);
          var strJsn=ev.data;
          var o=JSON.parse(strJsn);
          var strCmd=o.cmd;
          if (strCmd=='swhere')
          {
            console.log("message : swhere");
            //MsgText('adminmani','swhere');
          }
          else if (strCmd=='winload')
          {
            console.log("adminmani::message : winload now posting...");
            //MsgText('adminmani::message','winload');

            var nWpUsr=g_opwapaged.ocustuser.data.ID;
            var oMsg={"cmd":"pwahere","userid":nWpUsr,"paid":g_opwapaged.paid,"rights":g_opwapaged.rights};
            var strJson=JSON.stringify(oMsg);
            ev.source.postMessage(strJson,"*");
          }
        }
        );
        
                
        //        obj.addEventListener(strEvent, func, bCapture);

          ///-strfunc is server func,strObjFunc func pass back,
        /*var CreateDummyCtl=function()
        {
            var strKey='adminmani_js';
            var ctl={"m_strId":strKey};
            window.g_arrControls={};
            g_arrControls[strKey]=ctl;
            return ctl;
        }*/
        var CreateDummyCtl=function(strKey)
        {
            //var strKey=strKey;
            var ctl={"m_strId":strKey};
            if (typeof window.g_arrControls=='undefined')
              window.g_arrControls={};
            g_arrControls[strKey]=ctl;
            return ctl;
        }
    
        function DoGetThumbs(ids)
        {
            /*var objData={'um':1,'val':'abc'};
            objData.g_pdtMydesign=bIsMy;
            objData.strUrl=window.location.href;

            objData.m_strId=ctl.m_strId;
            var oo=window["ajax_object"];*/
            
            var ctl=CreateDummyCtl('keyDoGetThumbs');
            ctl.DoGetThumbsCtd=function (obj)
            {
                DoGetThumbsCtd(obj);
            }
            
            ///-debugger;
            ///AjaxCtlServFunc("GetAllWidgets","GetAllWidgetsCtd",objData,oo);


            //var objInit=this.m_objServ;
            //var uid=objInit.uid;
            //var postid=objInit.postid;

            var curpostid=null;
            var wpuserid=null;
            if (window['ctlsParams'])
            {
                curpostid=ctlsParams['postid'];
                wpuserid=ctlsParams['wpuserid'];
            }
       
            if (window['g_opwapaged'])
            {
                var nWpUsr=g_opwapaged.ocustuser.data.ID;
                wpuserid=nWpUsr;
            }

            var data={
                ctlid:ctl.m_strId,
                m_strId:ctl.m_strId,
                wpuserid:wpuserid,
                curpostid:curpostid,

                the_ids:ids
            };
    
            var oo=window["ctlsParams"];
            //AjaxActionServFunc('mypwaappyplug_action','DoGetThumbs','DoGetThumbsCtd',data,oo);
            
            AdminMani.MyAjaxActionServFunc('mypwaappyplug_action','DoGetThumbs','DoGetThumbsCtd',data,oo);
        }

        function MsgText(msg,err)
        {
            alert(err+":"+msg);
        }
        
        ///-get dashboard html
        function DoGetThumbsCtd(obj)
        {
            //RemoveCtlFromList(obj.m_strId);
            g_arrControls[obj.m_strId]=null;
        
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

            var str=obj.htm;
            //var jq = $('#idcardswidgetleftypar');
            //jq.html(str);

            var meta=obj.meta;
            if (!meta['meta'])
            {
                //toastr.info(strMsg, strCap);
                //toastr.info('Please upload the image to create the thumbnails','Error');
                //alert('Error: Please upload the image to create the thumbnails');
                MsgText('Please upload the image to create the thumbnails','Error: ');
                return;
            }
            var arrSz=meta.meta.sizes;
            var arrWant=[128,144,152,192,256,512];
            var i,nLen=arrWant.length;
            var px,k,r;
            for (i=0;i<nLen;i++)
            {
                px=arrWant[i];
                k="PwaPlugIcon"+px;
                r=arrSz[k];
                if (!r)
                {
                    MsgText(`Please upload the image to create the thumbnails (Image ${px} needed).Ensure image is greater than 512x512`,
                        'Error: ');
                    return;
                }

                $("#img"+px).val(r.file);                
            }
        }

        function SetImgFile(obj)
        {

        }

        function GetAppyAjaxUrl()
        {
            return g_opwapaged.parajax_url;
        }
  
        function InitServedCust(strServFunc, strPgNm, strPgNm2, strPgNm3)
        {
            var ctl=CreateDummyCtl('keyInitServedCust');
            ctl.InitServedCustCtd=function (obj)
            {
                InitServedCustCtd(obj);
            }
            
            var curpostid=null;
            var wpuserid=null;
            if (window['ctlsParams'])
            {
                curpostid=ctlsParams['postid'];
                wpuserid=ctlsParams['wpuserid'];
            }

            if (window['g_opwapaged'])
            {
                var nWpUsr=g_opwapaged.ocustuser.data.ID;
                wpuserid=nWpUsr;
            }

            var data={
                ctlid:ctl.m_strId,
                m_strId:ctl.m_strId,
                wpuserid:wpuserid,
                curpostid:curpostid,

                strPgNm:strPgNm,
                isproplugin: g_opwapaged.isproplugin?1:0,
                homepg:g_opwapaged.home
            };
            if (strPgNm2)
                data.strPgNm2 = strPgNm2;
            if (strPgNm3)
                data.strPgNm3 = strPgNm3;

            var ajaxurl=GetAppyAjaxUrl();
            var oo=window["ctlsParams"];
            //appyplug_action
            //mypwaappyplug_action
            AdminMani.MyAjaxUrlServFunc(ajaxurl, 'appyplug_action', strServFunc,'InitServedCustCtd',data,oo);
        }

        ///-get dashboard html
        function InitServedCustCtd(obj)
        {
            //RemoveCtlFromList(obj.m_strId);
            g_arrControls[obj.m_strId]=null;
        
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

            var str=obj.htm;
            //var jq = $('#idcardswidgetleftypar');
            //jq.html(str);

        }

        function HandleImgClick() 
        {
            //MsgText("clicked","ooook");

            image_frame = wp.media(
                {
                    title: 'Select Media',
                    ///-multiple : false,
                    multiple: false,
                    ///-library : { type : 'image,video,audio',},
                    library: { type: 'image', },
                    philmem: 34,
                    ///-library : { type : 'file',},
                    ///-button: {
                    ///-	text: 'GetMe media'
                    ///-},
                });

            image_frame.on('open',
                function () {
                    ids = [7];
                    ///- On open, get the id from the hidden input
                    ids.forEach(function (id) {
                        var attachment = wp.media.attachment(id);
                        ///-attachment.fetch();
                        ///-selection.add( attachment ? [ attachment ] : [] );
                    }
                    );

                }
            );

            //
            image_frame.on('select', function () {

                ///- Get media attachment details from the frame state
                var attachment = image_frame.state().get('selection').first().toJSON();

                var attachment2 = attachment;

                ///- Send the attachment URL to our custom image input field.
                ///-imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

                ///- Send the attachment id to our hidden input
                ///-imgIdInput.val( attachment.id );

                ///- Hide the add image link
                ///-addImgLink.addClass( 'hidden' );

                ///- Unhide the remove image link
                ///-delImgLink.removeClass( 'hidden' );
            });
            //
            image_frame.on('close',
                function () {
                    ///- On close, get selections and save to the hidden input
                    ///- plus other AJAX stuff to refresh the image preview
                    var selection = image_frame.state().get('selection');
                    var gallery_ids = new Array();
                    var my_index = 0;
                    selection.each(function (attachment) {
                        gallery_ids[my_index] = attachment['id'];
                        my_index++;
                    });
                    var ids = gallery_ids.join(",");
                    //jQuery('input#myprefix_image_id').val(ids);
                    //WPMediaGallery.Refresh_Image(ids);

                    //MsgText("Got:"+ ids,"OK");

                    DoGetThumbs(ids);

                }
            );

            image_frame.open();
        }

        function HandleImgClickSel(bMulti,func) {

            image_frame = wp.media(
                {
                    title: 'Select Media',
                    ///-multiple : false,
                    multiple: bMulti,
                    ///-library : { type : 'image,video,audio',},
                    library: { type: 'image', },
                    philmem: 34,
                    ///-library : { type : 'file',},
                    ///-button: {
                    ///-	text: 'GetMe media'
                    ///-},
                });

            image_frame.on('open',
                function () {
                    ids = [7];
                    ///- On open, get the id from the hidden input
                    ids.forEach(function (id) {
                        var attachment = wp.media.attachment(id);
                        ///-attachment.fetch();
                        ///-selection.add( attachment ? [ attachment ] : [] );
                    }
                    );

                }
            );

            //
            image_frame.on('select', function () {

                ///- Get media attachment details from the frame state
                var attachment = image_frame.state().get('selection').first().toJSON();

                var attachment2 = attachment;

                ///- Send the attachment URL to our custom image input field.
                ///-imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

                ///- Send the attachment id to our hidden input
                ///-imgIdInput.val( attachment.id );

                ///- Hide the add image link
                ///-addImgLink.addClass( 'hidden' );

                ///- Unhide the remove image link
                ///-delImgLink.removeClass( 'hidden' );

                /*
                var selection = image_frame.state().get('selection');
                var arrSel = [];
                selection.each(function (attach) {
                    arrSel.push(attach.attributes);
                });
                func(arrSel);
                */
            });
            //
            image_frame.on('close',
                function () {

                    ///- On close, get selections and save to the hidden input
                    ///- plus other AJAX stuff to refresh the image preview
                    var selection = image_frame.state().get('selection');
                    var arrSel = [];
                    selection.each(function (attach) {
                        arrSel.push(attach.attributes);
                    });
                    func(arrSel);
                }
            );

            image_frame.open();
        }
        //statements

        $("#idDoMediaLib").click(function(ui){

            HandleImgClick();
        });

        $("#idDoMediaLibMono").click(function (ui) {

            HandleImgClickSel(false, (arrSel) =>
            {
                if (arrSel.length == 0) {
                    MsgText('Please select an image', 'Error: ');
                    return;
                }
                const img = arrSel[0];
                const w = img.width;
                const h = img.height;
                if (w != 512 || h != 512) {
                    MsgText(`Please select an image of size 512 by 512, Not (${w}x${h})`, 'Error: ');
                    return;
                }
                const url = img.url;
                $("#imgmaskable").val(url);
            });
        });

        $("#idDoMediaLibMask").click(function (ui) {

            HandleImgClickSel(false, (arrSel) => {
                debugger;
                if (arrSel.length == 0) {
                    MsgText('Please select an image', 'Error: ');
                    return;
                }
                const img = arrSel[0];
                const w = img.width;
                const h = img.height;
                if (w!=192 || h!=192)
                {
                    MsgText(`Please select an image of size 192 by 192, Not (${w}x${h})`, 'Error: ');
                    return;
                }
                const url = img.url;
                $("#imgmaskable").val(url);
            });
        });

        $("#idDoMediaLibMask512").click(function (ui) {

            HandleImgClickSel(false, (arrSel) => {
                debugger;
                if (arrSel.length == 0) {
                    MsgText('Please select an image', 'Error: ');
                    return;
                }
                const img = arrSel[0];
                const w = img.width;
                const h = img.height;
                if (w != 512 || h != 512) {
                    MsgText(`Please select an image of size 512 by 512, Not (${w}x${h})`, 'Error: ');
                    return;
                }
                const url = img.url;
                $("#imgmaskable512").val(url);
            });
        });

        $("#idDoMediaLibScr512").click(function (ui) {

            HandleImgClickSel(false, (arrSel) => {
                if (arrSel.length == 0) {
                    MsgText('Please select an image', 'Error: ');
                    return;
                }
                const img = arrSel[0];
                const w = img.width;
                const h = img.height;
                if (w != 512 || h != 512) {
                    MsgText(`Please select an image of size 512 by 512, Not (${w}x${h})`, 'Error: ');
                    return;
                }
                const url = img.url;
                $("#imgScr512").val(url);
            });
        });

        //&& el.value!=''
        var el = document.getElementById('idPagePosted');
        if (el)
        {
            InitServedCust('InitServedCust','a2hspage');
        }
        var el = document.getElementById('idPagePosted_Email');
        if (el) {
            var el2 = document.getElementById('idPagePosted_EmailUse');
            var el3 = document.getElementById('idPagePosted_ServerKey');
            InitServedCust('InitServedCustFlds', el.value, el2.value, el3.value);
        }
    }
);

function AdminMani()
{
}
    
AdminMani.MyAjaxActionServFunc=function(strAction,strFunc,strObjFunc,objData,objInit)
{
    var ajaxurl=window.ajaxurl;
    AdminMani.MyAjaxUrlServFunc(ajaxurl,strAction,strFunc,strObjFunc,objData,objInit);
}

AdminMani.MyAjaxUrlServFunc=function(ajaxurl,strAction,strFunc,strObjFunc,objData,objInit)
{
        var postid=-2;
        ///-g_funcCtd=funcCtd;
  
        //http://localhost/wp/pwatest/
        //src="http://localhost/wp/appyapps/mybarepage/?pageid=notifyappy&reqpath=http:--localhost-wp-pwatest&tagmsg=paybob"
        //ajaxurl="http://localhost/wp/appyapps/wp-admin/admin-ajax.php";
    
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
                    ///[#NullErrorNOTOK begin]
                    //debugger;
                    //alert('look at me FATAL');
                    ///[#NullErrorNOTOK end]
                    return;
                }
                var strMeth=obj.strObjFunc;
                oCtl[strMeth](obj);
    
                ///-g_funcCtd(obj);
            }).fail(function()
            {
                alert("fail-edas_admin");
            });
    
}      

AdminMani.FormPosted=function()
{
}

