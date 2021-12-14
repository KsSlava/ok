
class Dropzone {

  constructor(id, url, thW, thH, maxFiles){

    this.id  = id;
    this.url = url;
    this.thW = thW;
    this.thH = thH;
    // this.token = token;
    this.maxFiles = maxFiles;


  }


  gallery(){
    
    var dropzone      = $('#'+this.id+'Dropzone');  
    var container     = $('#'+this.id+'Container');    
    
    var butUpload     = '#'+this.id+'ButUpload'; 
    var butDelete     = '#'+this.id+'ButDelete';
    var butDeleteList = '#'+this.id+'ButDeleteList';
    var butUnselect   = '.glyphicon-remove';
    
    var thW           = this.thW;
    var thH           = this.thH;
    var id            = this.id;
    var url           = this.url;
    var maxFiles      = this.maxFiles;
    
    
    var countFiles;  // count files for upload;
    var uploadFiles; //
    var z             =0;
    var resp;  

    var upImageID = ''; var upImageDesc = ''; var keyupInterval = 0; 





    function uploadedImagesAdd(imgSrc, imgName) {   

     
       
      if(maxFiles==1 && maxFiles!==null){

        container.append( '<div class="thumbnail">'
              +   '<img src="/'+imgSrc+'">'
              +   '<span> <button id="'+id+'ButDelete" type="button" class="btn btn-danger">Видалити</button></span>'
              + '</div>'); 

      }else{

         container.append( '<div class="thumbnail">'
              + '<img src="/'+imgSrc+'">'
              + '<div><textarea id="'+imgName+'" class="'+id+'ImageDesc"  style="resize: none;" rows="7" cols="46" maxlength="320"></textarea></div>'
              + '</div>');  

      }

      
      
    }


    function thumbAdd(data, thumbContainer) {

      var countThumb = data.files.length;
      var countAddedThumb = dropzone.children().length;

      /**
       *
       * Check is use param maxFiles
       *
       * */
      if(maxFiles !== null && countAddedThumb !==maxFiles) {

        countThumb = maxFiles;

      }

      if(maxFiles !== null && countAddedThumb ==maxFiles) {

        countThumb = 0;

      }


      for (var i = 0; i < countThumb; i++) {

        var f = data.files[i];

        var reader = new FileReader();

        reader.onload = (function(theFile) {

          return function (e) {

          

          if(maxFiles!==null && maxFiles==1){

            var thumb = $('<div/>', {'class':'thumbnail prg'}).append(
                   
                    '<img src="'+e.target.result+'">'

              );

          }else{

            var thumb = $('<div/>', {'class':'thumbnail prg'}).append(

                    '<span><button id="thumbButDelete" type="button" class="btn btn-danger">X</button></span>'
                    + '<img src="'+e.target.result+'">'

              );

          }

                thumb.get(0).file = theFile;

                thumb.appendTo(thumbContainer);

          }

        })(f);

        reader.readAsDataURL(f);

      }

       return true;
    }

 
    function upload(i) {



      if(countFiles >0 && i<countFiles ) {

        console.log(uploadFiles);


        var fd = new FormData();

        fd.append(id, uploadFiles[i].file);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader("X-CSRF-TOKEN", token);
        xhr.upload.onprogress = function (e) {
          if (e.lengthComputable) {

            var percentComplete = (e.loaded / e.total) * 100;

            // dropzone.children('div:eq(' + i + ')').find('.progress-bar').css('width', percentComplete + '%');
              dropzone.children('div:eq(' + i + ')').css("background-size", percentComplete+"% 100%");

           

          }
        };


        
        xhr.onload = function () {
          if (this.status == 200) {

       
          resp = JSON.parse(this.response);
            
            //hide thumb
            if((i+1)==countFiles) {

     
                if(maxFiles==1){

                   $.when(dropzone.fadeOut(500, function(){  $(this).children().remove(); })).then( function(){

                       uploadedImagesAdd(resp.imageUrl, resp.imageName);
                       
                   });


                }




               if(maxFiles==null){
                  
                   dropzone.children().fadeOut(500, function(){ $(this).remove(); });
                         
                }
            




            }
            else {

              i = i + 1;
              upload(i);

            }


               if(maxFiles==null){
                  
                   uploadedImagesAdd(resp.imageUrl, resp.imageName);
                         
                }


               
            

          }
        }

        if (xhr.sendAsBinary) {
          // only firefox
          xhr.sendAsBinary(fd);
        } else {
          // chrome (W3C)
          xhr.send(fd);
        }

      }

    }





   dropzone.bind('dragleave', function(){

     return false;

   });

   dropzone.bind('dragover', function(){

     return false;

   });



 

    dropzone.bind('drop', function(event){

      event.preventDefault(); //disable open Image in Browser

       var  data= event.originalEvent.dataTransfer;

       var th = thumbAdd(data, dropzone); 

   
      // if maxFiles 1 - do autoupload      
          if(maxFiles==1 && th==true){

          

              setTimeout( function(){
              
                uploadFiles = $('#'+id+'Dropzone').children();
                countFiles  = uploadFiles.length;
                upload(z); 
                
                }, 500);



          }







     });


  
    //upload selected thumb
    $(document).on('click', butUpload, function(){

      uploadFiles = $('#'+id+'Dropzone').children();
      countFiles  = uploadFiles.length;
      upload(z); 

    });




 
    //Unselect thumb
    $(document).on('click', '#thumbButDelete', function(){

      $(this).parent().parent().fadeOut(500, function(){

         $(this).remove();

      });
 
    });




    // //delete images on server

    var dImageArr = [];
    var dKey = 0;

    function find(array, value) {

      for (var i = 0; i < array.length; i++) {
        if (array[i] == value) return i;
      }

      return -1;
    }


    //images descriptions  of gallery
    function postImageDesc(){

      $.ajax({

        url:url,
        type:'post',
        data:{'upImageDesc':upImageDesc, 'upImageID':upImageID},
        headers:{'X-CSRF-TOKEN':token},
        dataType: 'json',
        success: function (data) {

          if(data=="ok"){

            console.log(data+'--'+upImageID)
               
               $('#'+upImageID).addClass('saved', 700);

               setTimeout(function(){ $('#'+upImageID).removeClass('saved', 700) }, 500);



          }

        }


      });



    }

    //select id
    $(document).on('click', '.'+id+'ImageDesc', function(){

      if(upImageID !== $(this).attr('id')){
          upImageID = $(this).attr('id')
        console.log(upImageID)
      }

    });
    //post id, text



    $(document).on('keyup', '.'+id+'ImageDesc', function(e){

     

      if(upImageID.length>10){

        

         upImageDesc = $(this).val();

           clearInterval(keyupInterval);

             keyupInterval = setInterval(function(){ 

              postImageDesc(); 

           // console.log(Date.now());

             clearInterval(keyupInterval);

           }, 700);
         
      }

   });



    $(document).on('click', '#'+this.id+'Container > .thumbnail', function(){

         


          var dSrc = $(this).children('img').attr('src'); 



          /**
          *Select/Unselect image for deleting
          */

          var hasSrc = find(dImageArr, dSrc);

          if( hasSrc == -1 ) {

            dImageArr[dKey] = dSrc;  dKey++; 

           
           $(this).children('img').animate({opacity:0.4}, 100);
           $(this).children('span').css('display', 'block'); 


          }else{

            dImageArr.splice(hasSrc, 1);

             $(this).children('img').animate({opacity:1}, 300);
             $(this).children('span').css('display', 'none'); 

          } 
          //Clean empty values after deleting ia Arr
          dImageArr = $.grep(dImageArr,function(n){ return n == 0 || n });
           
    });




    $(document).on('click', butDelete, function(){



         if (dImageArr.length>0){

  console.log(butDelete)
            $.ajax({

              url:url,
              type:'post',
              data:{'delImage':dImageArr},
              headers:{'X-CSRF-TOKEN':token},
              dataType: 'json',
              success: function (data) {
                  console.info(data);

                for (var i = 0; i < data.length; i++) {


                    $('img[src="'+data[i]+'"]').parent().fadeOut(500, function(){

                      $(this).remove();

                      dropzone.fadeIn(500); 




                    });
                }

              }


            });
          
          }

    });



    $(document).on('click', butDeleteList, function(){

         if (dImageArr.length>0){


            $.ajax({

              url:url,
              type:'post',
              data:{'delImages':dImageArr},
              headers:{'X-CSRF-TOKEN':token},
              dataType: 'json',
              success: function (data) {
                  console.info(data);

                for (var i = 0; i < data.length; i++) {


                    $('img[src="'+data[i]+'"]').parent().fadeOut(500, function(){

                      $(this).remove();

                    });
                }

              }


            });
          
          }

    });





  }



}

        
