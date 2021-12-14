

/*
education
*/ 
var chEd = 0; 
function checkEducationTypesChecked(){

	$('#educationTypes').children('input').each( function(){

		if($(this)[0].checked){

			chEd = 1;
			
			return false;

		}else{

			chEd = 0;
		}

	})


	return chEd; 

}




var are = 0; 
function addRowEducation(){

	are ++

	tr = '   <tr>'+
		        '<td>             <input  name="education[\''+are+'\'][\'title\']" maxlength="47" type="text"></td>'+
		        '<td>             <input  name="education[\''+are+'\'][\'doc\']"   maxlength="47" type="text"></td>'+
		       ' <td width="50">  <input  name="education[\''+are+'\'][\'year\']"  maxlength="47" type="text"></td>'+
		       ' <td>             <input  name="education[\''+are+'\'][\'spec\']"  maxlength="47" type="text"></td>'+
		       ' <td>             <input  name="education[\''+are+'\'][\'qual\']"  maxlength="47" type="text"></td>'+
		       ' <td width="100"> <input  name="education[\''+are+'\'][\'type\']"  maxlength="47" type="text"></td>'+
		        '<td class="butt_deltd" style="border:0;">x</td>'+
		    '</tr>'	;


    $('.educT').append(tr);
}


function delRowEducation(e){

	//disable delete first row

	if(are>0){

		e.parent('tr').remove();

		are--;

	}

	

}






