;(function(){
	var FRAMEURL='http://www.workday.com/index.php/notice/';
	var whenReady = (function() {               //这个函数返回whenReady()函数
    var funcs = [];             //当获得事件时，要运行的函数
    var ready = false;          //当触发事件处理程序时,切换为true
    
    //当文档就绪时,调用事件处理程序
    function handler(e) {
        if(ready) return;       //确保事件处理程序只完整运行一次
        //如果发生onreadystatechange事件，但其状态不是complete的话,那么文档尚未准备好
        if(e.type === 'onreadystatechange' && document.readyState !== 'complete') {
            return;
        }
        //运行所有注册函数
        //注意每次都要计算funcs.length
        //以防这些函数的调用可能会导致注册更多的函数
        for(var i=0; i<funcs.length; i++) {
            funcs[i].call(document);
        }
        //事件处理函数完整执行,切换ready状态, 并移除所有函数
        ready = true;
        funcs = null;
    }
    //为接收到的任何事件注册处理程序
    if(document.addEventListener) {
        document.addEventListener('DOMContentLoaded', handler, false);
        document.addEventListener('readystatechange', handler, false);            //IE9+
        window.addEventListener('load', handler, false);
    }else if(document.attachEvent) {
        document.attachEvent('onreadystatechange', handler);
        window.attachEvent('onload', handler);
    }
    //返回whenReady()函数
    return function whenReady(fn) {
        if(ready) { fn.call(document); }
        else { funcs.push(fn); }
    }
	})();
	
	var noticeObj='';
	function addNotice(){
		if (noticeObj!='') return;
		var body=document.getElementsByTagName('body')[0];
		var divObj=document.createElement('div');
		var divHead=document.createElement('div');
		var divBtn=document.createElement('span');
		var iframeObj=document.createElement('iframe');
		divBtn.innerHTML='&times';
		var sty1={lineHeight:'1em',float:'right',padding:'3px 5px',borderRadius:'6px',border:'1px solid #fff',cursor:'pointer',marginTop:'6px'};
		setStyle(divBtn,sty1)
		divHead.innerText='任务系统提示';
		divHead.appendChild(divBtn);
		sty1={minWidth:'200px',lineHeight:'34px',textAlign:'left',fontSize:'14px',paddingLeft:'10px',paddingTop:'3px'};
		setStyle(divHead,sty1);
		sty1={border:'0',display:'block',width:'300px',height:'150px'};
		setStyle(iframeObj,sty1);
		sty1={padding:'3px',paddingTop:'0',paddingBottom:'0',backgroundColor:'#484ce3',position:'fixed',right:'0',bottom:'0',color:'#fff',boxShadow:'0 0 20px rgba(0,0,0,.5)',borderRadius:'6px 6px 0 0'};
		setStyle(divObj,sty1);
		divObj.appendChild(divHead);
		divObj.appendChild(iframeObj);
		iframeObj.src=FRAMEURL;
		body.appendChild(divObj);
		noticeObj=divBtn;
		noticeObj.addEventListener('click',function(){
			if (iframeObj.style.display=='block'){
				divBtn.innerHTML='&uarr;'
				iframeObj.style.display='none';
			}else{
				divBtn.innerHTML='&times;';
				iframeObj.style.display='block';
			}
		},false);
	}
	
	function setStyle(obj,style){
		for (var x in style){
			obj.style[x]=style[x];
		}
	}
	whenReady(addNotice);
})();