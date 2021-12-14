<?php
/**
 * Upload Images Class

 * @param $thuW  int
 * @param $thuH  int
 * @param $orgW  int
 * @param $orgH  int
 * @param $disk  string
 * @param $id    int
 * @param $disks array
 * 1.step  - resize;
 * 2.step  - save;
 * if $thuW & $thuH = 0, no resize/save Thumb
 * if $orgW & $orgH = 0, no resize/save Original 
 */

namespace App\Lib;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;


    class UploadImage
    {

       public $exOrg = '.jpg';
       public $exThu = 'thu.jpg';
       public $imageUrl = 'public/src/images/';
       public $quality = 85; 

        public function save($tW, $tH, $oW, $oH, $disk, $file, $fileName ){


            $imgW = Image::make($file)->width();
            $imgH = Image::make($file)->height();
            $nameOrg = $fileName . $this->exOrg;
            $nameThu = $fileName . $this->exThu;

            $return = '';
            $arr  = array( 

                'imageUrl' => $this->imageUrl . $disk . '/' . $nameThu,
                'imageName' => $fileName

                );  
           


            //Image resize save
            if($imgW >= $oW  & $oH ==0) {

                $image = Image::make($file)->resize($oW, null, function ($constraint) {$constraint->aspectRatio();})->stream('jpg', $this->quality);
                Storage::disk($disk)->put($nameOrg, $image);



            }elseif($imgW < $oW  & $oH ==0) {

                $image = Image::make($file)->resize($imgW, $imgH)->stream('jpg', $this->quality);
                Storage::disk($disk)->put($nameOrg, $image);


            }elseif($imgH >= $oH & $oW ==0) {

                $image = Image::make($file)->resize(null, $oH, function ($constraint) {$constraint->aspectRatio();})->stream('jpg', $this->quality);
                Storage::disk($disk)->put($nameOrg, $image);


            }elseif($imgW < $oH  & $oW ==0) {

                $image = Image::make($file)->resize($imgW, $imgH)->stream('jpg', $this->quality);
                Storage::disk($disk)->put($nameOrg, $image);


            }elseif($oW > 0 & $oH > 0){

                $image = Image::make($file)->resize($oW, $oH)->stream('jpg', $this->quality);
                Storage::disk($disk)->put($nameOrg, $image);

            }

            


            if(Storage::disk($disk)->has($nameOrg)) {

                //Thumb resize save
                if($imgW >= $tW  & $tH ==0) {

                    $thumb = Image::make($file)->resize($tW, null, function ($constraint) {$constraint->aspectRatio();})->stream('jpg', $this->quality);
                    Storage::disk($disk)->put($nameThu, $thumb);
                     $return = $arr;


                }elseif($imgH >= $tH & $tW ==0) {

                    $thumb = Image::make($file)->resize(null, $tH, function ($constraint) {$constraint->aspectRatio();})->stream('jpg', $this->quality);
                    Storage::disk($disk)->put($nameThu, $thumb);
                     $return = $arr;
                }
                elseif($imgW < $tW & $tH==0){

                    $thumb = Image::make($file)->resize($imgW, $imgH)->stream('jpg', $this->quality);
                    Storage::disk($disk)->put($nameThu, $thumb);
                     $return = $arr;

                }
                elseif($imgH < $tH & $tW==0){

                    $thumb = Image::make($file)->resize($imgW, $imgH)->stream('jpg', $this->quality);
                    Storage::disk($disk)->put($nameThu, $thumb);
                     $return = $arr;

                }


                


            }


            return $return;
            
        }
        

        
    }


