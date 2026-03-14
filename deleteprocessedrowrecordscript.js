function checkSubmit(){
	if (confirm('Are you sure you want to DELETE the selected Record(s)')){
		x = document.frm1;
		x.target = '_self';
		x.act.value='deleteRecord';
		x.btSubmit.disabled = true;
		x.submit();
	}
}
function checkDelete(x, y, z){
	if (x.checked == true){
		if (y.value == 0 || y.value == ''){
			y.value = z.value;
		}
	}else{
		y.value = 0;
	}
}
function checkAll(){
	x = document.frm1;
	y = x.chkDelete;
	z = x.txtCount.value;
	for (i=0;i<z;i++){
		if (y.checked == true){
			document.getElementById("chkDelete"+i).checked = true;
		}else{
			document.getElementById("chkDelete"+i).checked = false;
		}
	}
}