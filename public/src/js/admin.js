var chId = 0; var chName; var chIdVal; var x = 0;  

var sisp = ""
function setIdSelectorProf(i){

	sisp = i;

}






$(document).ready(function(){


    news = new Dropzone('photo', '/auth/admin/upload', 325, null, 1);
    news.gallery();

    newsGallery = new Dropzone('photoGallery', '/auth/admin/upload', null, 230, null);
    newsGallery.gallery();
	

   $("#pid").inputmask({"mask": "9999999999"});
   $("#createDate, #birthdate, .expB, .expE, .lastdissdate, .pdate, .peductdate, .peductB, .peductE, .apptrandate, .pb, .pe, .rb, .re, #xdateSpecExp, #dissdate, #invb, #inve").inputmask({"mask": "99-99-9999"});
   $(".educationYear, .aeducationYear, .familyyear").inputmask({"mask": "9999"});
   $("#mob").inputmask({"mask": "(999) 999-99-99"});
   /*
   *
   *Education
   *
   */

	//check: checked educationTypes and show table
	$("#educationTypes").click(function(){
		
		if(checkEducationTypesChecked()){
			$('.educT').fadeIn("slow");
			$('#butt_addeduc').fadeIn("slow");

		}
	})

	//add row
	$("#butt_addeduc").click(function(){  addRowEducation()  })

	//del row
	$(document).on('click', '.butt_deltd', function(){  delRowEducation($(this))  })








   /*
   *
   *apptran
   *
   */

   //resize height of popup window


   wH = $(window)[0].innerHeight
   sacH = (wH/100)*73;
   sarH = (wH/100)*30;

   $('#search_apptran_container').css('max-height', sacH+'px')
   $('.search_apptran_results').css('height', sarH+'px' )

   









   $('.addprof').click(function(){

   		$('#search_apptran_box').show();

   });


   // @type: prof, code
   var sp; 
   var search_results;

	function search_apptran_ajax(val, type){

		$.ajax({

			headers:
	        { 'X-CSRF-TOKEN': token },
			url:'/auth/admin/searchkpp/',
			method:'POST',
			//async: false,
			data: {'val':val, 'type':type},
			dataType:'json',
			error: function(){

				
			},
			success: function(e){


				if(e.length>0){

					for(i=0; i<e.length; i++){


						if(i==0){


							search_results = '<table width="100%">'
							

						}
							search_results = search_results 
							+'<tr data-kkpid="'+e[i].id+'" data-prof="'+e[i].prof+'" data-kodkp="'+e[i].kod_kp+'">'
								+'<td>'+(i+1)+'</td>'
								+'<td class="td_prof">'+e[i].prof+'</td>'
								+'<td width="50px">'+e[i].kod_kp+'</td>'
								+'<td width="50px">'+e[i].kod_zkppt+'</td>'
								+'<td width="50px">'+e[i].vipusk_etkd+'</td>'
								+'<td width="50px">'+e[i].vipusk_dkhp+'</td>'


							+'</tr>'

					}

							search_results = search_results
							 +'</table>';



					$('.search_apptran_results').html(search_results)
					//console.log(search_results);

				}else{


					$('.search_apptran_results').html("");

				}


				
			}

		});
	}


	function search_apptran(val, type){


		val = val.trim();

		if(type == 'prof'){m=4; }
		if(type == 'code'){m=3; }

			if(val.length >= m) {

			if(typeof sp === "undefined") {

				sp = setTimeout(  function(){ 	search_apptran_ajax(val, type) }, 1000);

			}else{

				clearInterval(sp); 

				sp = setTimeout(   function(){	search_apptran_ajax(val, type) }, 1000);

			}


		}
	}


   	//search by prof
	$('#search_by_prof').on('input',function(){

		search_apptran( $(this).val(), 'prof');

	});

    //search by code (код кп)
	$('#search_by_code').on('input',function(){

		search_apptran( $(this).val(), 'code');

	});



	//selecting prof in popup window
	var trOLD = 0; var trID =""; var trPROF=""; var trCODE =""; 

	$(document).on('click', '.td_prof', function(){


	     tr  = $(this).parent(); 
	     trID = tr.attr('data-kkpid'); 
	     trPROF = tr.attr('data-prof'); 
	     trCODE = tr.attr('data-kodkp'); 



	     if(trOLD==0){

	     	tr.css("background-color", "#8eadc5"); 

	     }else{


			trOLD.css("background-color", ""); 


			tr.css("background-color", "#8eadc5");

	     }



	     trOLD = tr;

	})



	//apply of selecting prof
	function closeApptran(){

   		$('#search_apptran_box').hide();

   		$('#search_by_prof').val("");

   		$('#search_by_code').val("");

   		$('.search_apptran_results').html("");

   		trID = ""; trPROF=""; trCODE =""; trOLD =0;
		
	}





	$(document).on('click', '.applySelectProf', function(){


		if(trID!==""){

			$('#kkpid'+sisp).val(trID); 
			$('#prof'+sisp).val(trPROF); 
			$('#code'+sisp).val(trCODE); 


			$('#addprof'+sisp).hide();
			$('#prof'+sisp).show();

			$('#delprof'+sisp).show();

			closeApptran()

		}


	})




		//clear addons 


		$(document).on('click', '.delprof', function(){

			$('#kkpid'+sisp).val("");
			$('#prof'+sisp).val(""); 
			$('#code'+sisp).val(""); 


			$('#addprof'+sisp).show();
			$('#prof'+sisp).hide();

			$('#delprof'+sisp).hide();

		})





	//close popup window
   $('.close_apptran_search_form').click(function(){


			closeApptran()


   })










	$('.content :input').each(function(){ $(this).attr("autocomplete", "off") });




		function checkForm(){


			chIdVal = $('#'+chId).val();
	     

			$.ajax({

				headers:
	            { 'X-CSRF-TOKEN': token },
				url:'/auth/admin/new',
				method:'POST',
				//async: false,
				data: chId+'='+chIdVal+'&chIdVal=1',
				//dataType:'json',
				error: function(){

					$('#'+chId).addClass('has-error'); 
				},
				success: function(e){

					$('#'+chId).removeClass('has-error'); 
				}

			});

	        console.log(chId+'--'+chIdVal);

		}


    	 // setInterval(function(){    



   		 //  checkForm() }, 2000)




	//check input
    $(document).on('click', function(e){  

       		 e.target.id

			if(e.target.id.length>1){
			     
				chId = e.target.id;

						
			}


    });




	//Exp, SpecExp  recount via new date
	rse = $('#rse')  
	
	rse.click(function(){


		xdate = $('#xdateSpecExp').val();
		xtid  = $('#tid').val();


		if(xdate!=="00-00-0000" && xdate.length==10){

			$.ajax({

				headers:
	            { 'X-CSRF-TOKEN': token },
				url:'/auth/admin/xspecexp',
				method:'POST',
				//async: false,
				data: {'xdate':xdate, 'xtid':xtid},
				dataType:'json',
				error: function(){


					

					
				},
				success: function(e){


					
					
					$('#xd1').html(e.dateExpAll.days)
					$('#xm1').html(e.dateExpAll.months)
					$('#xy1').html(e.dateExpAll.years)
					                 
					$('#xd2').html(e.dateSpecExpAll.days)
					$('#xm2').html(e.dateSpecExpAll.months)
					$('#xy2').html(e.dateSpecExpAll.years)



					
				}

			});

		}


	})



	$('#rse_close').click(function(){


				$('#xd1').html("")
				$('#xm1').html("")
				$('#xy1').html("")
				 
				$('#xd2').html("")
				$('#xm2').html("")
				$('#xy2').html("")

				$('#xdateSpecExp').val("00-00-0000");



	})




	// Archive

	$('#btn_show_archive_box').click(function(){

		$('#archive_box').show()

	})


	$('#close_archive_box').click(function(){ 

		$('#archive_box').hide()

		$('#diss').val("")

		$('#dissdate').val("")

	})

	// add to Archive

	$('#btn_add_to_archive').click(function(){

		$.ajax({

			headers:
		    { 'X-CSRF-TOKEN': token },
			url:'/auth/admin/toArchive',
			method:'POST',
			//async: false,
			data: {'diss': $('#diss').val(), 'dissdate': $('#dissdate').val(), 'tid': $('#tid').val() },
			// dataType:'json',
			error: function(){

				console.log('errrrrr')
			},
			success: function(e){


				if(e==1){

					
					document.location.href = 'http://ok/auth/admin/archive'


				}else{


					//error
					if(e.length>0){

						e.forEach(function(v, i){

							$('#'+v).css('border-color', 'red')

						})
					}





				}


				
			}

		});		






	})



	$('#btn_from_archive').click(function(){

		$.ajax({

			headers:
		    { 'X-CSRF-TOKEN': token },
			url:'/auth/admin/fromArchive',
			method:'POST',
			//async: false,
			data: {'tid': $('#tid').val() },
			// dataType:'json',
			error: function(){

				
			},
			success: function(e){

				document.location.href = 'http://ok/auth/admin/edit/'+e
			
			}

		});		






	})




    //check disabled form
   //  setInterval(function(){

   //  	if($('#invcat').val()>0){

			// $('#invtypeDisp').show();

		
   //  	}else{

			
			// $('#invtypeDisp').hide();
   //  	}


   //  }, 1000)






});






