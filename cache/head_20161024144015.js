var mainScroll,listScroll,searchCache=[];window.onload=function(){baron({root:"body"});baron({root:"#live_search",scroller:".search_res",bar:".baron__bar",scrollingCls:"_scrolling"});};$(document).ready(function(){var notProgress=true,content=$("#content"),scrollPane=$(".scroll-pane1"),preview=$(".preview"),cuttedPath;$("#slidemenu1").multiSlideMenu({scrollToTopSpeed:100,slideSpeed:300,backLinkContent:"Назад",loadContainer:"#center",loadOnlyLatest:true,afterLoadDone:function(){var preview=$(".preview");try{mainScroll=baron({root:".scroll-pane1",scroller:"#content",bar:".baron__bar",scrollingCls:"_scrolling"});}catch(e){console.log(e.stack);}
resizeCatalog();if(preview.tooltip){preview.tooltip({delay:0,bodyHandler:function(){return $("<img/>").attr("src",$(this).attr("data-pid"));}});}
if(inLog){$("a.page-link").click(navigation);$("div.shapka").find("a").click(navigation);}}});loadCart();resizeCatalog();if(window.location.pathname=="/"){realMarginLi();}else{if(preview.tooltip){preview.tooltip({delay:0,bodyHandler:function(){return $("<img/>").attr("src",$(this).attr("data-pid"));}});}
if(inLog){$("a.page-link").click(navigation);$("div.shapka").find("a").click(navigation);}}
cuttedPath=window.location.pathname.split("/");switch(cuttedPath[1]){case"cart":try{$("#slidemenu0").toggleClass("hide").multiSlideMenu({autoHeightMenu:true,backOnTop:false,scrollToTopSpeed:200,slideSpeed:300,backLinkContent:"Назад",loadContainer:"html"});}catch(e){console.log(e.stack);}
break;case"category":case"search":case"auxpage_new_items":try{mainScroll=baron({root:".scroll-pane1",scroller:"#content",bar:".baron__bar",scrollingCls:"_scrolling"});}catch(e){console.log(e.stack);}
case"product":default:try{listScroll=baron({root:"#lists",scroller:".cpt_product_lists",bar:".baron__bar",scrollingCls:"_scrolling"});}catch(e){console.log(e.stack);}
if(inLog){$("li.cl-effect-21").find("a").click(navigation);}
break;}
$(window).resize(function(){if(window.location.pathname=="/"){if(inLog){setTimeout(realMarginLi,300);}}
setTimeout(resizeCatalog,300);});$(".js_login_block .js_btn_close").on("click",function(){$(this).parents(".js_login_block").hide(300);});$("#link_login").on("click",function(){var login_block=$("#log2div");if(login_block.css("display")==="none"){login_block.show(300);}else{login_block.hide(300);}
return false;});if(!strpos(window.navigator.userAgent,'Mac OS X')){$(window).bind('popstate',function(){addLoaderSmoke();$.ajax({url:location.pathname+'?ajax=1',success:function(data){$('#center').html(data);removeLoaderSmoke();updateEvents();}});});}
function realMarginLi(){var idCenter=$("#center").find(".product_topview_area"),productTopviewAreaWidth=idCenter.width(),centerLi=idCenter.find("ul li"),liWidth=centerLi.width()+6,liNum=Math.floor(productTopviewAreaWidth/liWidth),margin=Math.round((productTopviewAreaWidth-liWidth*liNum)/(liNum+1));centerLi.css({"margin-left":margin,"margin-bottom":margin});}
function resizeCatalog(){var scrollPane=$(".scroll-pane1"),productLists=$(".cpt_product_lists");cuttedPath=window.location.pathname.split("/");if(cuttedPath[1]!==""&&cuttedPath[1]!=="cart"){scrollPane.height(1);$("#content").height(1);}
$("#columns").height(1);productLists.height(1);setTimeout(realResizeCatalog,100);}
function realResizeCatalog(){var headerHeight=$("#header").height()+$(".header").height()+8,maincontentHeight=$(window).height()-headerHeight,delta=$(".product_brief_head").height()+$(".navigator").height(),baronFree=$("#content").next(),scrollPane=$(".scroll-pane1"),mainColumnsHeight=$(".cpt_maincolumns").height(),docHeight=$(document).height()-headerHeight,centerHeight=$("#center").height(),columns=$("#columns"),columnsHeight;cuttedPath=window.location.pathname.split("/");switch(cuttedPath[1]){case"category":case"search":case"auxpage_new_items":case"auxpage_divoland":case"auxpage_mixtoys":case"auxpage_dreamtoys":case"auxpage_grandtoys":case"auxpage_kindermarket":scrollPane.height(maincontentHeight-delta);$("#content").height(maincontentHeight-delta);baronFree.css({top:delta,height:maincontentHeight-delta});default:break;}
if(scrollPane.length){columns.height((mainColumnsHeight>docHeight)?mainColumnsHeight:docHeight);}else{centerHeight=((maincontentHeight-delta)>centerHeight)?(maincontentHeight-delta):centerHeight;columns.height((mainColumnsHeight>centerHeight)?mainColumnsHeight:centerHeight);}
columnsHeight=columns.height();if(cuttedPath[1]!="cart"){$(".cpt_product_lists").height(columnsHeight);}}
function strpos(haystack,needle,offset){var i=haystack.indexOf(needle,offset);return i>=0?i:false;}
function navigation(){var url=$(this).attr("href"),scrollPaine=$(".scroll-pane1"),glue="?";console.log(url);if(strpos(url,"?")!==false){glue="&";}
addLoaderSmoke();$.ajax({url:url+glue+"ajax=1",success:function(data){$("#center").html(data);removeLoaderSmoke();updateEvents(scrollPaine);}});if(url!=window.location){window.history.pushState(null,null,url);}
return false;}
function addLoaderSmoke(){var scrollPaine=$(".scroll-pane1");$("#center").addClass("smoke");scrollPaine.addClass("loader");scrollPaine.baron().dispose();}
function removeLoaderSmoke(){var scrollPaine=$(".scroll-pane1");$("#center").removeClass("smoke");scrollPaine.removeClass("loader");}
function updateEvents(){var preview=$(".preview");try{mainScroll=baron({root:".scroll-pane1",scroller:"#content",bar:".baron__bar",scrollingCls:"_scrolling"});}catch(e){console.log(e.stack);}
$("a.page-link").click(navigation);$("div.shapka").find("a").click(navigation);preview.tooltip({delay:0,bodyHandler:function(){return $("<img/>").attr("src",$(this).attr("data-pid"));}});resizeCatalog();}});function strpos(haystack,needle,offset){var i=haystack.indexOf(needle,offset);return i>=0?i:false;}
function explode(delimiter,string){var emptyArray={0:""};if(arguments.length!=2||typeof arguments[0]=="undefined"||typeof arguments[1]=="undefined"){return null;}
if(delimiter===""||delimiter===false||delimiter===null){return false;}
if(typeof delimiter=="function"||typeof delimiter=="object"||typeof string=="function"||typeof string=="object"){return emptyArray;}
if(delimiter===true){delimiter="1";}
return string.toString().split(delimiter.toString());}
function zakcia(seconds){var startDate=new Date();startDate.setSeconds(seconds);$("#z_counter").countdown({image:"/img/_digits.png",startTime:startDate});}
function loadCart(){$("#my__cart").load("/popup/show_cart.php");}
function updateClientInfo(id,qt){var zpid=$("#zpid_"+id);var oldVal=Number(zpid.text());var newVal=oldVal+Number(qt);zpid.html('<div class="animated fadeInDownBig">'+newVal+"</div>");}
function increaseNumber(id){var value=$('#qty'+id),nCol=value.val();nCol++;if(nCol<1){nCol=1;}
value.val(nCol);return false;}
function decreaseNumber(id){var value=$('#qty'+id),nCol=value.val();nCol--;if(nCol<1){nCol=1;}
value.val(nCol);return false;}
function addAll2Cart(){var myCart=$("#my__cart");myCart.html('<div style="float:right"><p style="font-size:14px;line-height:37px;color:white">Загрузка товаров...</p></div>');$("[name=product_qty]").each(function(){var id=$(this).attr("data-id");var qt=$(this).val();var query="?ukey=cart&view=noframe&action=add_product&force=yes&productID="+id+"&product_qty="+qt;if(qt>0){$.ajax({type:"GET",url:query,dataType:"html",async:true,success:function(){updateClientInfo(id,qt);}});$(this).val("");}});setTimeout(loadCart,300);}
function add2Cart(who){var myCart=$("#my__cart");myCart.html('<div style="float:right"><p style="font-size:14px;line-height:37px;color:white">Загрузка товаров...</p></div>');$(who).each(function(){var id=$(this).attr("data-id");var qt=$(this).val();if(qt==""){qt=1;}
var query="?ukey=cart&view=noframe&action=add_product&force=yes&productID="+id+"&product_qty="+qt;if(qt>0){$.ajax({type:"GET",url:query,dataType:"html",async:true,success:function(){updateClientInfo(id,qt);}});}});$(who).val("");setTimeout(loadCart,250);}
function Recard(){var sOut="";sOut+='$.post("/index.php?ukey=cart&did=37&ajax=1",{"update":1,"shopping_cart":1';for(var i=0;i<aID.length;i++){var id=aID[i];if(id>0){var val=$('#count_'+id).val();if(parseInt(val)<1||val==""){val=0;$('#count_'+id).val(val);aID[i]=0;$('#'+id).remove();}
sOut+=',"count_'+id+'":'+val;}}
sOut+='}, function(data) {; }  );';eval(sOut);}
function Reprise(oldVal){var all=0;var allB=0;var units=(currensy==9)?"&nbsp;&#8372;":"&nbsp;у.е.";var curs=(currensy==10)?usd:1;for(var i=0;i<aID.length;i++){var id=aID[i];if(id>0){var price=parseFloat($('#price_'+id).val());var val=$('#count_'+id).val();if(parseInt(val)<1||val==""){if(confirm("Удалить этот товар из корзины?")){val=0;$('#count_'+id).val(val);}else{val=oldVal||1;$('#count_'+id).val(val);}}
var sum=(price*val).toFixed(2);var bonus=0;if(bID[id]>0){bonus=parseInt(sum/curs)*bID[id];}
$('#sum_'+id).html(sum.replace(".",",")+units);$('#bonus_'+id).html(bonus+"&nbsp;бал.");all+=parseFloat(sum);allB+=bonus;}}
all=all.toFixed(2);$('#cart_total').html("<b>"+all+units+"</b>");$('#bonus_total').html("<b>"+allB+"</b>&nbspбаллов");Recard();setTimeout(loadCart,250);}
function CountDOWN(id,removeItem){removeItem=removeItem||false;if(!removeItem){var val=parseInt($('#count_'+id).val());val--;if(val>0&&val<mID[id]){if($('#min_'+id).hasClass('display_none')){$('#min_'+id).removeClass('display_none');}}
$('#count_'+id).val(val);Reprise();}else{var oldVal=parseInt($('#count_'+id).val());$('#count_'+id).val(0);Reprise(oldVal);}}
function CountUP(id){var val=parseInt($('#count_'+id).val());val++;if(val>=mID[id]){if(!$('#min_'+id).hasClass('display_none')){$('#min_'+id).addClass('display_none');}}
$('#count_'+id).val(val);Reprise();}
function allowDrop(ev){ev.preventDefault();}
function drag(ev){ev.dataTransfer.setData("text",ev.target.id);}
function drop(ev){ev.preventDefault();var data=ev.dataTransfer.getData("text");ev.target.appendChild(document.getElementById(data));}
function _changeCurrency(){document.ChangeCurrencyForm.submit();}
function changePic(id,direction){var pic="pic";var element=document.getElementById("pic"+id);var picNums=Number(element.getAttribute("data-pics"));var currPic=Number(element.getAttribute("data-current"));var startOfSrc="/pictures/";var newPic;var endOfSrc;var ext="_thm.jpg";var extDiv=".jpg";if(direction>0){if(currPic<picNums){newPic=currPic+direction;endOfSrc="-"+newPic;}else{newPic=0;endOfSrc="";}}else{if(currPic>1){newPic=currPic+direction;endOfSrc="-"+newPic;}else{if(currPic==1){newPic=0;endOfSrc="";}else{newPic=picNums;endOfSrc="-"+newPic;}}}
element.setAttribute("src",startOfSrc+id+endOfSrc+ext);element.setAttribute("data-current",newPic);element.setAttribute("data-pid",startOfSrc+id+endOfSrc+extDiv);}
function throttle(func,ms){var isThrottled=false,savedArgs,savedThis;function wrapper(){if(isThrottled){savedArgs=arguments;savedThis=this;return;}
func.apply(this,arguments);isThrottled=true;setTimeout(function(){isThrottled=false;if(savedArgs){wrapper.apply(savedThis,savedArgs);savedArgs=savedThis=null;}},ms);}
return wrapper;}
$(function(){var searchOk=$("#search_ok"),liveSearch=$(".container"),searchString=$("#searchstring");searchString.keyup(throttle(function(){var search=searchString.val();searchOk.addClass("search_loader");if(search.length>2){if(searchCache[search]){liveSearch.html(searchCache[search]);}else{$.ajax({type:"POST",url:"/popup/search.php",data:{search:search},cache:true,success:function(response){liveSearch.html(response);searchCache[search]=response;}});}
return searchOk.removeClass("search_loader");}else{liveSearch.html("");if(!search.length){searchOk.removeClass("search_loader");}}},1500));});(function($){var helper={},current,title,tID,track=false;$.tooltip={blocked:false,defaults:{delay:200,fade:false,top:150,left:100,id:"tooltip"},block:function(){$.tooltip.blocked=!$.tooltip.blocked;}};$.fn.extend({tooltip:function(settings){settings=$.extend({},$.tooltip.defaults,settings);createHelper(settings);return this.each(function(){$.data(this,"tooltip",settings);this.tOpacity=helper.parent.css("opacity");this.tooltipText=this.title;$(this).removeAttr("title");this.alt="";}).mouseover(save).mouseout(hide).click(hide);}});function createHelper(settings){if(helper.parent){return;}
helper.parent=$('<div id="'+settings.id+'"><div class="body"></div>').appendTo(document.body).hide();helper.title=$("h3",helper.parent);helper.body=$("div.body",helper.parent);}
function settings(element){return $.data(element,"tooltip");}
function handle(event){if(settings(this).delay){tID=setTimeout(show,settings(this).delay);}else{show();}
track=!!settings(this).track;$(document.body).bind("mousemove",update);update(event);}
function save(){if($.tooltip.blocked||this==current||(!this.tooltipText&&!settings(this).bodyHandler)){return;}
current=this;title=this.tooltipText;if(settings(this).bodyHandler){helper.title.hide();var bodyContent=settings(this).bodyHandler.call(this);if(bodyContent.nodeType||bodyContent.jquery){helper.body.empty().append(bodyContent);}else{helper.body.html(bodyContent);}
helper.body.show();}else{if(settings(this).showBody){var parts=title.split(settings(this).showBody);helper.title.html(parts.shift()).show();helper.body.empty();for(var i=0,part;(part=parts[i]);i++){if(i>0){helper.body.append("<br/>");}
helper.body.append(part);}
helper.body.hideWhenEmpty();}else{helper.title.html(title).show();helper.body.hide();}}
handle.apply(this,arguments);}
function show(){tID=null;if(settings(current).fade){if(helper.parent.is(":animated")){helper.parent.stop().show().fadeTo(settings(current).fade,current.tOpacity);}else{helper.parent.is(":visible")?helper.parent.fadeTo(settings(current).fade,current.tOpacity):helper.parent.fadeIn(settings(current).fade);}}else{helper.parent.show();}
update();}
function update(event){if($.tooltip.blocked){return;}
if(event&&event.target.tagName=="OPTION"){return;}
if(!track&&helper.parent.is(":visible")){$(document.body).unbind("mousemove",update);}
if(current==null){$(document.body).unbind("mousemove",update);return;}
helper.parent.removeClass("viewport-right").removeClass("viewport-bottom");var left=helper.parent[0].offsetLeft;var top=helper.parent[0].offsetTop;if(event){left=event.pageX+settings(current).left;top=event.pageY-settings(current).top;var right="auto";if(settings(current).positionLeft){right=$(window).width()-left;left="auto";}
helper.parent.css({left:left,right:right,top:top});}
var v=viewport(),h=helper.parent[0];if(v.x+v.cx<h.offsetLeft+h.offsetWidth){left-=h.offsetWidth+20+settings(current).left;helper.parent.css({left:left+"px"}).addClass("viewport-right");}
if(v.y+v.cy<h.offsetTop+h.offsetHeight){top-=h.offsetHeight+120-settings(current).top;helper.parent.css({top:top+"px"}).addClass("viewport-bottom");}}
function viewport(){return{x:$(window).scrollLeft(),y:$(window).scrollTop(),cx:$(window).width(),cy:$(window).height()};}
function hide(event){if($.tooltip.blocked){return;}
if(tID){clearTimeout(tID);}
current=null;var tsettings=settings(this);function complete(){helper.parent.hide().css("opacity","");}
if(tsettings.fade){if(helper.parent.is(":animated")){helper.parent.stop().fadeTo(tsettings.fade,0,complete);}else{helper.parent.stop().fadeOut(tsettings.fade,complete);}}else{complete();}}})(jQuery);