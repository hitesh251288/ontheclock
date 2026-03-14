function openWindow(a)
{
	window.open(\"FlagApplication.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");
}
function checkFlagApplicationPrint(a)
{
	var x = document.frm1;
	if (check_valid_date(x.txtFrom.value) == false)
	{
		alert('Invalid From Date. Date Format should be DD/MM/YYYY');
		x.txtFrom.focus();
		return false;
	}else{
		if (a == 0){
			if (confirm('Go Green - Think Twice before you Print this Document \Are you sure want to Print?')){
				x.action = 'FlagApplication.php?prints=yes';
				x.target = '_blank';x.submit();
				return true;
			}else{
				return false;
			}
		}else{
			x.action = 'FlagApplication.php?prints=yes&excel=yes';
			x.target = '_blank';
			x.submit();
			return true;
		}
	}
}
function checkFlagApplicationSearch(){
	var x = document.frm1;
	if (check_valid_date(x.txtFrom.value) == false){
		alert('Invalid From Date. Date Format should be DD/MM/YYYY');
		x.txtFrom.focus();
		return false;
	}else{
		x.action = 'FlagApplication.php?prints=no';
		x.target = '_self';x.btSearch.disabled = true;
		return true;
	}
}
function checkAssignTextbox(x){
	if (x.value*1 != x.value/1){
		alert(\"ONLY Numeric Value ALLOWED as OT\");
		x.focus();
	}
}
function insertOTAll(x, w){
	y = document.frm1.txtOTAll.value;z = document.frm1.txtRemarkAll.value;
	if (y != \"\" && y*1 == y/1){
		if (x.value == \"\" || x.value == 0){
			x.value = y;w.value = z;
		}
	}
}
function insertAllOT(){
	x = document.frm1;
	if (x.txtOTAll.value != \"\" && x.txtOTAll.value != 0){
		if (confirm(\"Enter OT = \"+x.txtOTAll.value+\" in all the Below Blank OT Records?\")){
			for (i=0;i<x.txhCount.value;i++){
				if (document.getElementById(\"txtOT\"+i).value == \"\" || document.getElementById(\"txtOT\"+i).value == 0){
					document.getElementById(\"txtOT\"+i).value = x.txtOTAll.value;document.getElementById(\"txtRemark\"+i).value = x.txtRemarkAll.value;
				}
			}
		}
	}else{
		alert(\"Please enter the OT value to be assigned to all Records\");
		x.txtOTAll.focus();
	}
}
function checkA2All(){
	x = document.frm1;
	if (x.chkA2All.checked == true){
		if (confirm(\"Approve All OT\")){
			for (i=0;i<x.txhCount.value;i++){
				if (document.getElementById(\"txtOT\"+i)){
					if (document.getElementById(\"txtOT\"+i).value != \"\" && document.getElementById(\"txtOT\"+i).value != 0){
						document.getElementById(\"chkA2\"+i).checked = true;
					}
				}
			}
		}else{
			x.chkA2All.checked = false;
		}
	}else{
		if (confirm(\"De-Approve All OT\")){
			for (i=0;i<x.txhCount.value;i++){
				document.getElementById(\"chkA2\"+i).checked = false;
			}
		}else{
			x.chkA2All.checked = true;
		}
	}
}
function checkA3All(){
	x = document.frm1;
	if (x.chkA3All.checked == true){
		if (confirm(\"Authorize All OT\")){
			for (i=0;i<x.txhCount.value;i++){
				if (document.getElementById(\"txtOT\"+i).value != \"\" && document.getElementById(\"txtOT\"+i).value != 0 && (document.getElementById(\"chkA2\"+i).checked == true || document.getElementById(\"chkA2\"+i).value == 1)){
					document.getElementById(\"chkA3\"+i).checked = true;
				}
			}
		}else{
			x.chkA3All.checked = false;
		}
	}else{
		if (confirm(\"De-Authorize All OT\")){
			for (i=0;i<x.txhCount.value;i++){
				document.getElementById(\"chkA3\"+i).checked = false;
			}
		}else{
			x.chkA3All.checked = true;
		}
	}
}
function saveChanges(){
	x = document.frm1;
	if (x.lstDeptTerminal.value == \"\"){
		alert(\"Please select a Department Terminal\");
		x.lstDeptTerminal.focus();
	}else{
		if (confirm(\"Save Changes?\")){
			x.act.value = \"saveChanges\";
			x.btSubmit.disabled = true;
			x.submit();
		}
	}
}
function checkFlag(x, y){
	if (x.checked == true){
		if (y.value == \"\"){
			alert('Please select a Flag');
			x.checked = false;
			y.focus();
		}
	}
}
function deleteRecord(a){
	x = document.frm1;
	if (confirm('Delete this Record')){
		x.act.value = \"deleteRecord\";
		x.action = 'FlagApplication.php?txtID='+(a*1024*1024);
		x.submit();
	}
}
function checkFromDate(x){
	var d = new Date().getFullYear();
	if (check_valid_date(x.value) == false){
		alert('Invalid From Date. Date Format should be DD/MM/YYYY');
		x.focus();
		return false;
	}else if (x.value.substring(6, 10) != d){
		alert('Invalid From Year. Only Current Year Allowed');
		x.focus();
		return false;
	}else{
		return true;
	}
}
function checkToDate(x){
	z = document.frm1;
	var d = new Date().getFullYear();
	if (check_valid_date(x.value) == false){
		alert('Invalid From Date. Date Format should be DD/MM/YYYY');
		x.focus();
		return false;
	}else if (z.txhFlagLimitType.value == 'Jan 01' && x.value.substring(6, 10) != d){
		alert('Invalid To Year. Only Current Year Allowed');
		x.focus();
		return false;
	}else if (z.txhFlagLimitType.value == 'Employee Start Date' && (x.value.substring(6, 10) < d || x.value.substring(6, 10) > (d+1)) ){
		alert('Invalid To Year. Only Current and Next Year Allowed');
		x.focus();
		return false;
	}else{
		return true;
	}
}