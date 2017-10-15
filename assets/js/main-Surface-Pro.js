/*
 Creator:Jakub Rybicki
 Creation Date: 12/08/2015
*/
function clickMenu(){
	$("#mypanel").panel("open",optionsHash);
}
function plusClick(elem){
	var num=getNumFromValTextBox(elem);
	num++;
	$(elem).parent().find('input').val(num);
	updateCircleLevel(elem);
}
function minusClicked(elem){
	var num=getNumFromValTextBox(elem);
	num--;
	$(elem).parent().find('input').val(num);
	updateCircleLevel(elem);
}

function getNumFromValTextBox(elem){
	var num=$(elem).parent().find('input').val();
	num=parseInt(num);
	return num;
}

function updateCircleLevel(elem){
	//alert("start");
	var qty=$(elem).parent().find('input').val();
	//qty=parseInt(qty);
	var warning=$(elem).parent().parent().attr('data-warningQty');
	//warning=parseInt(warning);
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