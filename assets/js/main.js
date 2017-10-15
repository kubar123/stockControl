/*
 Creator:Jakub Rybicki
 Creation Date: 12/08/2015
*/
var APIURL="http://tafeapi.lansoftprogramming.com/index.php";
function getNumFromValTextBox(elem){
	var num=$(elem).parent().find('input').val();
	num=parseInt(num);
	return num;
}

function setNumToValTextBox(elem, val){
	var num=$(elem).parent().find('input').val(val);
	//num=parseInt(num);
	
}
function addNumToValTexBoxByOne(elem){
	var num=$(elem).parent().find('input').val();
	//alert(num);
	// num = parseInt(num);
	// alert(num);
	num++;
	num=$(elem).parent().find('input').val(num);
	//alert(num+" Num");
}

function updateCircleLevel(elem){
	//alert("start");
	var qty=$(elem).parent().find('input').val();
	qty=parseInt(qty);
	var warning=$(elem).parent().parent().attr('data-warningQty');
	warning=parseInt(warning);
	//alert("qty="+qty+" war "+warning);
	if(qty<=warning){
		$(elem).siblings(":last").css('color','red');
	}else if(qty<=(warning*1.5)){
		$(elem).siblings(":last").css('color','orange');
	}else{
		$(elem).siblings(":last").css('color','green');
	}
}

function addNewItem(){
	
}


// ---------------------


document.addEventListener("menubutton", onMenuKeyDown, false);

function onMenuKeyDown() {
		$("#mypanel").panel('open');
}



// ---------- HOLD EVENTS --------------
$(document).ready(function(){
	$(".minusButton").on('taphold', function(){
		setNumToValTextBox($(this), 0);
		updateCircleLevel($(this));
	});
});

// $(document).ready(function(){
// 	$(".plusButton").on('taphold', function(){
// 		alert("ok..");
// 		(function(){
// 			alert("starting..");
// 			addNumToValTexBoxByOne($(this));
// 			setTimeout(arguments.callee, 500);
// 		})();
// 	});
// });
var clicked;
$(function(){
	var setTime;
	startClock();
	$(".plusButton").mousedown(function(){
		if(clicked==null)
			clicked=$(this);
		setTime=setTimeout(arguments.callee, 200);
		addNumToValTexBoxByOne(clicked);
	}).mouseup(function(){
		clicked = null;
		window.clearTimeout(setTime);
	});
});
var clockTick;
var clock;
function  startClock(){
	clock++;
	clockTick=setTimeout(arguments.callee, 10);
}
// function letsSee(){
// 	addNumToValTexBoxByOne(clicked);
// 	setTime=setTimeout(arguments.callee, 100);
// 	updateCircleLevel(clicked);
// }
// ---------- CLICK EVENTS -------------
function clickMenu(){
	$("#mypanel").panel("open",optionsHash);
}

// function plusClick(elem){
// 	if()
// 	var num=getNumFromValTextBox(elem);
// 	num++;
// 	$(elem).parent().find('input').val(num);
// 	updateCircleLevel(elem);
// }

function minusClicked(elem){
	var num=getNumFromValTextBox(elem);
	if(parseInt(num)<=0){
		return;
	}
	num--;
	$(elem).parent().find('input').val(num);
	updateCircleLevel(elem);
}
$(document).ready(function(){
	$(".itemInfo").click(function(){
		//alert($(this).attr('href'));
		var name=$(this).parent().attr('data-item-name');
		var warningNo=$(this).parent().attr('data-warningqty');
		var barcode=$(this).parent().attr('data-item-barcode');
		var qty=$(this).children().find('input').val();
		//alert(qty);
		$('#txtItemNameEdit').val(name);
		$('#txtQtyEdit').val(qty);
		$('#txtQtyAlertEdit').val(warningNo);
		$('#txtBarcodeEdit').val(barcode);

		$.mobile.changePage( "#page_edit_item", { role: "dialog" } );

		//do nothing if children are clicked -- +|-|input are children
	}).children().click(function(e){
		return false; 
	});
});
// animate refresh
$(function(){
	$('#refreshImg').click(function() {
			$(this).rotate({ count:12, duration:0.6, easing:'ease-out' });
	});

});


// ----------------------------- API CALLS -----------------------

//try to login using username+password

// $('#loginSubmit').click(function(){
// 	alert('clicked');
// 	var user=$('#loginUser').val();
// 	var pass=$('#loginPass').val();
// 	var json="{'user':'"+user+"',':pass':'"+pass+"'}";
// 	alert('user | pass '+user+" "+pass);
// 	// $.ajax({
	// 	type:'POST',
	// 	url: APIURL+"login",
	// 	contentType: "application/json",
	// 	data: {
	// 	  'user':user,
	// 	  'pass':pass
	//    }
 //  })
 //  .done(function(){alert('done');})
 //  .always(function(){})
 //  .fail(function(){})
 //  .success(function(data){
 //  	alert(data);
 //  })
// });
//backToHome();
// console.log(localStorage.user);
// console.log(localStorage.vKey);
// ------ IF LOGGED IN, VALIDATE -----------
$(function(){
	if(localStorage.vKey!==undefined && localStorage.user!==undefined){
		var info=JSON.stringify({
			"user":localStorage.user,
			"vKey":localStorage.vKey
		});
		$.ajax({
			type:'POST',
			url: APIURL+"/login",
			contentType: "application/json",
			dataType: "json",
			data: info
	  })
	  .done(function(){})
	  .always(function(){})
	  .fail(function(data){
	  	localStorage.user=undefined;
	  	localStorage.vKey=undefined;
	  	var a=JSON.parse(JSON.stringify(data));
	  	alert(a.responseText);
		$.mobile.navigate("#page_Register");

	  })
	  .success(function(data){
	  	var d=JSON.parse(JSON.stringify(data));
	  	//alert("already logged in");
	  	localStorage.setItem('vKey',d.vKey);
	  	localStorage.setItem('user',d.user);
	  	$('#LoginRegisterTabs').append('<div id="changePassTab" data-inset="false" data-collapsed="false" class="boxShadow" data-collapsed-icon="carat-d" data-expanded-icon="carat-u" data-role="collapsible"><h4>Change Password</h4><ul data-role="listview"><li><form><input id="ChangePassOld" type="text" val="" placeholder="Old password"/><input type="text" placeholder="New password" id="newPass" /><input type="password" id="changePassConfirm" placeholder="confirm New password"/><input type="button" onclick="changePass()" value="Create house"/></form></li></ul></div>').trigger('create');
	  	$('#loginTab').remove();
		$('#registerTokenTab').remove();
		$('#newHouseTab').remove();
	  });
	}else{
		$.mobile.navigate("#page_Register");
	}
});
function backToHome(){
	$(function(){
	$.mobile.navigate('#');
	});
}

// --------------------- CHANGE PASSWORD --------------
function changePass(){
	var user=localStorage.user;
	var oldPass=$('#ChangePassOld').val();
	var newPass=$('#newPass').val();
	var newPassConfirm=$('#changePassConfirm').val();
	if(newPass!=newPassConfirm){
		alert("Error: Passwords must match!");
		return;
	}
	var jsonData=JSON.stringify({
		"user":user,
		"pass":oldPass,
		"newPass":newPass
	});
	
	$.ajax({
		type:'POST',
		url: APIURL+"/modify/user",
		contentType: "application/json",
		dataType: "json",
		data: jsonData
  })
  .done(function(){})
  .always(function(){})
  .fail(function(data){
  	var a=JSON.parse(JSON.stringify(data));
  	alert(a.responseText);
  })
  .success(function(data){
  	var d=JSON.parse(JSON.stringify(data));
  	alert(JSON.stringify(data));
  	// console.log("logged in as: "+d.user);
  	// localStorage.setItem('vKey',d.vKey);
  	// localStorage.setItem('user',d.user);
  	//$.mobile.navigate('#');
  });
}

// ---- end login validation -----------
// $('#loginSubmit').on('click','#loginSubmit',function(){
// $('#loginSubmit').on('vclick',function(){
$(function(){


$('#loginSubmit').on('vclick', function(){
	console.log("click");
	alert('clicked');
	//var json="{'user':'"+user+"',':pass':'"+pass+"'}";
	//json=JSON.stringify(json);
	//alert('user | pass '+user+" "+pass);
	$.ajax({
		type:'POST',
		url: APIURL+"/login",
		contentType: "application/json",
		dataType: "json",
		data: getUserPassLoginJSON()
  })
  .done(function(){})
  .always(function(){})
  .fail(function(data){
  	var a=JSON.parse(JSON.stringify(data));
  	alert(a.responseText);
  })
  .success(function(data){
  	var d=JSON.parse(JSON.stringify(data));
  	console.log("logged in as: "+d.user);
  	localStorage.setItem('vKey',d.vKey);
  	localStorage.setItem('user',d.user);
  	$.mobile.navigate('#');
  });
});
});
function getUserPassLoginJSON(){
	var user=$('#loginUser').val();
	var pass=$('#loginPass').val();
	return JSON.stringify({
		"user":user,
		"pass":pass
	});
}

function addHouse(){
	var hName=$('#txtHName').val();
	var user=$('#txtUser').val();
	var pass=$('#txtPass').val();
	var passConfirm=$('#txtConfPass').val();
	if(passConfirm!=pass){
		alert("Passwords must match!");
		return;
	}
	var jsonData=JSON.stringify({
		"user":user,
		"pass":pass,
		"hName":hName
	});
	//get data

	$.ajax({
		type:'POST',
		url: APIURL+"/add/house",
		contentType: "application/json",
		dataType: "json",
		data: jsonData
  })
  .done(function(){})
  .always(function(){})
  .fail(function(data){
  	var a=JSON.parse(JSON.stringify(data));
  	alert(a.responseText);
  })
  .success(function(data){
  	var d=JSON.parse(JSON.stringify(data));
  	console.log("logged in as: "+d.user);
  	localStorage.setItem('vKey',d.vKey);
  	localStorage.setItem('user',d.user);
  	$.mobile.navigate('#');
  });
}



// ************************************************************************************************
// **										EXTERNAL CODE										  *
// **										PLEASE IGNORE										  *
// ************************************************************************************************

// - - - - - - - - - - - - - - - - - ANIMATION - ROTATE IMG - - - - - - - - - - - - - - - - - - - -
/*
jQuery-Rotate-Plugin v0.2 by anatol.at
http://jsfiddle.net/Anatol/T6kDR/
*/
$.fn.rotate=function(options) {
	var $this=$(this), prefixes, opts, wait4css=0;
	prefixes=['-Webkit-', '-Moz-', '-O-', '-ms-', ''];
	opts=$.extend({
		startDeg: false,
		endDeg: 360,
		duration: 1,
		count: 1,
		easing: 'linear',
		animate: {},
		forceJS: false
	}, options);

	function supports(prop) {
		var can=false, style=document.createElement('div').style;
		$.each(prefixes, function(i, prefix) {
			if (style[prefix.replace(/\-/g, '')+prop]==='') {
				can=true;
			}
		});
		return can;
	}

	function prefixed(prop, value) {
		var css={};
		if (!supports.transform) {
			return css;
		}
		$.each(prefixes, function(i, prefix) {
			css[prefix.toLowerCase()+prop]=value || '';
		});
		return css;
	}

	function generateFilter(deg) {
		var rot, cos, sin, matrix;
		if (supports.transform) {
			return '';
		}
		rot=deg>=0 ? Math.PI*deg/180 : Math.PI*(360+deg)/180;
		cos=Math.cos(rot);
		sin=Math.sin(rot);
		matrix='M11='+cos+',M12='+(-sin)+',M21='+sin+',M22='+cos+',SizingMethod="auto expand"';
		return 'progid:DXImageTransform.Microsoft.Matrix('+matrix+')';
	}

	supports.transform=supports('Transform');
	supports.transition=supports('Transition');

	opts.endDeg*=opts.count;
	opts.duration*=opts.count;

	if (supports.transition && !opts.forceJS) { // CSS-Transition
		if ((/Firefox/).test(navigator.userAgent)) {
			wait4css=(!options||!options.animate)&&(opts.startDeg===false||opts.startDeg>=0)?0:25;
		}
		$this.queue(function(next) {
			if (opts.startDeg!==false) {
				$this.css(prefixed('transform', 'rotate('+opts.startDeg+'deg)'));
			}
			setTimeout(function() {
				$this
					.css(prefixed('transition', 'all '+opts.duration+'s '+opts.easing))
					.css(prefixed('transform', 'rotate('+opts.endDeg+'deg)'))
					.css(opts.animate);
			}, wait4css);

			setTimeout(function() {
				$this.css(prefixed('transition'));
				if (!opts.persist) {
					$this.css(prefixed('transform'));
				}
				next();
			}, (opts.duration*1000)-wait4css);
		});

	} else { // JavaScript-Animation + filter
		if (opts.startDeg===false) {
			opts.startDeg=$this.data('rotated') || 0;
		}
		opts.animate.perc=100;

		$this.animate(opts.animate, {
			duration: opts.duration*1000,
			easing: $.easing[opts.easing] ? opts.easing : '',
			step: function(perc, fx) {
				var deg;
				if (fx.prop==='perc') {
					deg=opts.startDeg+(opts.endDeg-opts.startDeg)*perc/100;
					$this
						.css(prefixed('transform', 'rotate('+deg+'deg)'))
						.css('filter', generateFilter(deg));
				}
			},
			complete: function() {
				if (opts.persist) {
					while (opts.endDeg>=360) {
						opts.endDeg-=360;
					}
				} else {
					opts.endDeg=0;
					$this.css(prefixed('transform'));
				}
				$this.css('perc', 0).data('rotated', opts.endDeg);
			}
		});
	}

	return $this;
};
// - - - - - - - - - - - - - - - END OF ANIMATION - ROTATE IMG - - - - - - - - - - - - - - - - - - 






// ************************************************************************************************
// **									END OF EXTERNAL CODE									  *
// **								    	PLEASE IGNORE										  *
// ************************************************************************************************