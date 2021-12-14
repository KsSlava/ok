<?php 

namespace App\Lib;

use Illuminate\Support\Facades\Storage;
use Sunra\PhpSimple\HtmlDomParser;
use App\Models\kkp;
use App\Models\kkp_cats;
use Illuminate\Support\Facades\DB;

use PHPExcel; 
use PHPExcel_IOFactory;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Style_Alignment;
use PHPExcel_Worksheet_PageSetup; 
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Helper_HTML;
use PHPExcel_RichText;

class Helper{


    static function kkpCats(){


        $return = [];

        $return =  DB::table('kkp_cats')
                ->select('kkp_cats.*')
                ->where("kkp_cats.pb", '=', '1')
                ->orderBy('title', 'asc')
                ->get();

        return $return;
    }




    static function getLastApptran($tid){

            $r =  0;

            $query = DB::table('apptran as a')
            ->leftJoin('kkp_cats as c', 'a.kkpCatId', '=', 'c.id')
            ->leftJoin('kkp_list as l', 'a.kkpid', '=', 'l.id')
            ->select('a.date as kkpDate', 'a.kkpid as kkpId', 'a.kkpCatId', 'a.subprof as kkpSubprof', 'a.rank as kkpRank', 'a.doc as kkpDoc',
                     'c.title as kkpCatTitle',  'c.ord as kkpCatOrd',
                      'l.kod_kp', 'l.prof')
            ->where('a.tid', '=', $tid)
            ->orderBy('a.id', 'asc')->get();

            if(count($query)>0){

                foreach ($query as $q) {
                 
                    if(strlen($q->kkpDate)==10  and $q->kkpId > 0  and  $q->kkpCatId > 0) {


                        $r = $q; 

                    }


                }

            }


            return $r;
    }


    static function dateExp($dates = []){

        // $dates: array
        // ['b'=>'01-08-2006', 'e'=>'22-05-2007'], 
        // ['b'=>'24-05-2007', 'e'=>'13-05-2008'],
        // ['b'=>'27-05-2008', 'e'=>'30-06-2011'],

        $return = 0;

        if(count($dates)>0){


            $r = []; 
            $days = 0;
            $months = 0;
            $years = 0;

            $return = 0;


            //count periods of exp of work
            foreach ($dates as $k=>$a) {


                if( strtotime($a['b']) < strtotime($a['e'])  
                  //  and  strtotime($a['b']) <= strtotime(date('d-m-Y'))  
                  //  and  strtotime($a['e']) <= strtotime(date('d-m-Y')) 

                    ){

                    $d1 = date_create(trim($a['b'])); 
                    $d2 = date_create(trim($a['e'])); 
                    $i  = date_diff($d1, $d2);

                    $r[] = [

                        'days'=>$i->format('%d'),
                        'months'=>$i->format('%m'),
                        'years'=>$i->format('%y'),

                    ];

                }
                
            }



            //count total exp
            if(count($r)>0){


                foreach ($r as $v) {

                    $days = $days + $v['days']; 
                    $months = $months + $v['months']; 
                    $years = $years + $v['years']; 
                    
                }


            }


            //convert days in month, recalc days

            $ds = 30;
            if($days>$ds){

              $m = floor($days / $ds);

              $months = $months + $m; 

              $days = $days - $ds * $m;

            }




            $ys = 12;
            if($months>$ys){

              $y = floor($months / $ys) ;

               $months = $months - $ys * $y;

               $years = $years + $y; 

            }


            $return = ['days'=>$days, 'months'=>$months, 'years'=>$years] ; 

        }

            return $return;
    }



    static function getDateExp($exp){


        $arr = [];

        if($exp){


            foreach ($exp as $e) {

                $arr[] = ['b'=>$e->b, 'e'=>$e->e];
               
            }


        }


        return self::dateExp($arr);
    }



    static function getDateSpecExp($exp){


        $arr = [];

        if($exp){

           
          
            foreach ($exp as $e) {

                if($e->specexp == 1){

                    $arr[] = ['b'=>$e->b, 'e'=>$e->e];


                }

               
            }

            
            return self::dateExp($arr);   
  
        }
    }


    static function getDateExpAll($apptran=0, $d=0){

        if($apptran){

            $tmp = []; 

            $arr = [];
          
            foreach ($apptran as $a) {

                if(strlen($a->date) == 10){

                   $tmp[] = $a->date;

                }
               
            }


            arsort($tmp);


            if(count($tmp>1)){


                if(strlen($d)==10){

                    $e = $d;

                }else{

                    $e = date('d-m-Y');

                }


                    $arr[] = ['b'=>array_shift($tmp), 'e'=>$e ];

            }

         
            return self::dateExp($arr);   
  
        }
    }


    static function getDateSpecExpAll($exp=0, $apptran=0, $d=0){


        $arr = [];

        if($exp){
         
            foreach ($exp as $e) {

                if($e->specexp == 1){

                    $arr[] = ['b'=>$e->b, 'e'=>$e->e];


                }

               
            }

        }


        if($apptran){

            $mx = count($apptran); 
         
            for($i=0; $i<$mx; $i++){

                if($apptran[$i]->specexp == 1){

                    $nxt = $i+1; 

                        if(array_key_exists($nxt, $apptran)){

                            $e = $apptran[$nxt]->date;

                        }else{ 

                            if(strlen($d)==10){

                                $e = $d;

                            }else{

                                $e = date('d-m-Y');

                            }

                            

                        }
                   
                           
                           $arr[] = ['b'=>$apptran[$i]->date, 'e'=>$e];


                }
               
            }

        }



        return self::dateExp($arr);
       
    }


    //print


    static function h2s($h){

        $html = new PHPExcel_Helper_HTML;
       
        $str = $html->toRichTextObject(mb_convert_encoding(html_entity_decode($h), 'HTML-ENTITIES', 'UTF-8'));

        return $str;

    }





    static function print_hb($data=[]){


        if(count($data)>0){







            // Создаем объект класса PHPExcel
            $xls = new PHPExcel();
            // Устанавливаем индекс активного листа
            $xls->setActiveSheetIndex(0);
            // Получаем активный лист
            $sheet = $xls->getActiveSheet();
            $sheet->getPageSetup()->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);

            $sheet->getPageMargins()->setTop(0);
            $sheet->getPageMargins()->setRight(0);
            $sheet->getPageMargins()->setLeft(0.65);
            $sheet->getPageMargins()->setBottom(0);


            // Подписываем лист
            $sheet->setTitle('Дні народження');
            // Объединяем ячейки
            $sheet->mergeCells('A1:K1');

            $sheet->getStyle("A1")->getAlignment()->setWrapText(true);

            // Выравнивание текста
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
            // Шрифт Times New Roman
            $sheet->getStyle('A1')->getFont()->setName('Arial');
             
            // Размер шрифта 18
            $sheet->getStyle("A1")->getFont()->setSize(14);

            // Жирный
            $sheet->getStyle("A1")->getFont()->setBold(true);

            $sheet->getRowDimension("1")->setRowHeight(50);

            $sheet->getColumnDimensionByColumn("A")->setAutoSize(true);
            $sheet->getColumnDimension("K")->setWidth(10);
            $sheet->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            // Вставляем текст в ячейку A1

            $title = "Перелік дней народжень співробітників ХОАМДТ ім М. КУЛІША \n з " . $data['b'] ." по ". $data['e'];

            $sheet->setCellValue("A1", $title);

            //B
          



            $i = 0; 
            $r = 2;

            foreach ($data['prs'] as $person) {

                $i++;
                $r++;

                   
                $sheet->setCellValue("A".$r, (string) $i);

                $sheet->mergeCells("B".$r.":F".$r);
                $sheet->setCellValue("B".$r, $person['fullname']);


                if($person['kkpSubprof'] !== "" ){

                    $prof =  $person['prof']." ".$person['kkpSubprof'];

                }else{

                    $prof =  $person['prof'];

                }

                $sheet->mergeCells("G".$r.":I".$r);
                $sheet->setCellValue("G".$r, $prof);


                $sheet->setCellValue("K".$r, $person['birthdate']);



                
            }
           

            $f = 'Дні_народження_з_'.$data['b'] ."_-_". $data['e'].'.xlsx';

            header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=$f");

            $objWriter = new PHPExcel_Writer_Excel2007($xls);
            $objWriter->save('php://output');

        }


    }



    static function excelStr($str, $cspace){
        

        if(mb_strlen($str)>0){

            $t = "<u>&nbsp;&nbsp;".$str."&nbsp;&nbsp;</u>";

        }else{

            $t = "<u>";

            for($x=1; $x<=$cspace; $x++){

                $t .= "&nbsp;";

            }

            $t .= "</u>";

        }


        return $t;

    }


    static function print_admin($data=[]){


        if(count($data)>0){



            // Создаем объект класса PHPExcel
            $xls = new PHPExcel();
            // Устанавливаем индекс активного листа
            $xls->setActiveSheetIndex(0);
            // Получаем активный лист
            $sheet = $xls->getActiveSheet();
            $sheet->getPageSetup()->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);

            $sheet->getPageMargins()->setTop(0);
            $sheet->getPageMargins()->setRight(0);
            $sheet->getPageMargins()->setLeft(0.65);
            $sheet->getPageMargins()->setBottom(0);


            // Подписываем лист
            $sheet->setTitle("Cпівробітники М.КУЛІША");
            // Объединяем ячейки
            $sheet->mergeCells('A1:E1');

            $sheet->getStyle("A1")->getAlignment()->setWrapText(true);

            // Выравнивание текста
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
            // Шрифт Times New Roman
            $sheet->getStyle('A1')->getFont()->setName('Arial');
             
            // Размер шрифта 18
            $sheet->getStyle("A1")->getFont()->setSize(14);

            // Жирный
            $sheet->getStyle("A1")->getFont()->setBold(true);

            $sheet->getRowDimension("1")->setRowHeight(50);

           //  $sheet->getColumnDimensionByColumn("A")->setAutoSize(true);
     
            $sheet->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            // Вставляем текст в ячейку A1

            $title = "Перелік cпівробітників ХОАМДТ ім М. КУЛІША \n станом на " . date('d-m-Y');

            $sheet->setCellValue("A1", $title);

            $bg = array(
                'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'c3d1e2')
                )
            );
          
            $sheet->getColumnDimension("A")->setWidth(4);
            $sheet->getColumnDimension("B")->setWidth(35);
            $sheet->getColumnDimension("C")->setWidth(40);
            $sheet->getColumnDimension("D")->setWidth(15);
            $sheet->getColumnDimension("E")->setWidth(15);
            
            $r = 2;
            $i = 0; 
            foreach ($data as $cat=>$person) {

              
                $r++;


                $sheet->mergeCells('A'.$r.':E'.$r);

                $sheet->getStyle("A".$r)->applyFromArray($bg);
                $sheet->getStyle("A".$r)->getFont()->setBold(true);
                   
                $sheet->setCellValue("A".$r, (string) $cat);

                    
                    foreach ($person as $p) {

                        $r++;
                        $i++;

                       $sheet->setCellValue("A".$r, $i);

                        $sheet->setCellValue("B".$r, $p['fullname']);

                        

                        $sheet->setCellValue("C".$r, $p['prof']);

                        

                        $sheet->setCellValue("D".$r, $p['birthdate']);

                       

                        $sheet->setCellValue("E".$r, $p['mob']);

                        
                    }



                // $sheet->mergeCells("B".$r.":F".$r);
                // $sheet->setCellValue("B".$r, $person['fullname']);


                // if($person['kkpSubprof'] !== "" ){

                //     $prof =  $person['prof']." ".$person['kkpSubprof'];

                // }else{

                //     $prof =  $person['prof'];

                // }

                // $sheet->mergeCells("G".$r.":I".$r);
                // $sheet->setCellValue("G".$r, $prof);


                // $sheet->setCellValue("K".$r, $person['birthdate']);



                
            }
           

            $f = 'Перелік_cпівробітників_'.date('d-m-Y').'.xlsx';

            header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=$f");

            $objWriter = new PHPExcel_Writer_Excel2007($xls);
            $objWriter->save('php://output');

        }


    }


    static function print_admin_az($data=[]){




        if(count($data)>0){



            // Создаем объект класса PHPExcel
            $xls = new PHPExcel();
            // Устанавливаем индекс активного листа
            $xls->setActiveSheetIndex(0);
            // Получаем активный лист
            $sheet = $xls->getActiveSheet();
            $sheet->getPageSetup()->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);

            $sheet->getPageMargins()->setTop(0);
            $sheet->getPageMargins()->setRight(0);
            $sheet->getPageMargins()->setLeft(0.65);
            $sheet->getPageMargins()->setBottom(0);


            // Подписываем лист
            $sheet->setTitle("Cпівробітники М.КУЛІША (А-Я)");
            // Объединяем ячейки
            $sheet->mergeCells('A1:E1');

            $sheet->getStyle("A1")->getAlignment()->setWrapText(true);

            // Выравнивание текста
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
            // Шрифт Times New Roman
            $sheet->getStyle('A1')->getFont()->setName('Arial');
             
            // Размер шрифта 18
            $sheet->getStyle("A1")->getFont()->setSize(14);

            // Жирный
            $sheet->getStyle("A1")->getFont()->setBold(true);

            $sheet->getRowDimension("1")->setRowHeight(50);

           //  $sheet->getColumnDimensionByColumn("A")->setAutoSize(true);
     
            $sheet->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            // Вставляем текст в ячейку A1

            $title = "Перелік cпівробітників ХОАМДТ ім М. КУЛІША \n станом на " . date('d-m-Y');

            $sheet->setCellValue("A1", $title);

            $bg = array(
                'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'c3d1e2')
                )
            );
          
            $sheet->getColumnDimension("A")->setWidth(4);
            $sheet->getColumnDimension("B")->setWidth(38);
            $sheet->getColumnDimension("C")->setWidth(40);
            $sheet->getColumnDimension("D")->setWidth(15);
            $sheet->getColumnDimension("E")->setWidth(15);
            
            $r = 2;
            $i = 0; 
            foreach ($data as $k=>$p) {



                if(array_key_exists('prof', $p)) {

                    $r++;
                    $i++;

                    $sheet->setCellValue("A".$r, $i);

                    $sheet->setCellValue("B".$r, $p['fullname']);


                    $sheet->setCellValue("C".$r, $p['prof']);

                    $sheet->setCellValue("D".$r, $p['birthdate']);

                    $sheet->setCellValue("E".$r, $p['mob']);


                }



                        
                  

               
            }
           

            $f = 'Перелік_cпівробітників_(А-Я)_'.date('d-m-Y').'.xlsx';

            header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=$f");

            $objWriter = new PHPExcel_Writer_Excel2007($xls);
            $objWriter->save('php://output');

        }


    }



    static function print_ann($data=[]){


        if(count($data)>0){

            PHPExcel_Settings::setLocale('ru');

            // Создаем объект класса PHPExcel
            $xls = new PHPExcel();
            // Устанавливаем индекс активного листа
            $xls->setActiveSheetIndex(0);
            // Получаем активный лист
            $sheet = $xls->getActiveSheet();
            $sheet->getPageSetup()->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);

            $sheet->getPageMargins()->setTop(0);
            $sheet->getPageMargins()->setRight(0);
            $sheet->getPageMargins()->setLeft(0.65);
            $sheet->getPageMargins()->setBottom(0);


            // Подписываем лист
            $sheet->setTitle('Дні народження');
            // Объединяем ячейки
            $sheet->mergeCells('A1:K1');

            $sheet->getStyle("A1")->getAlignment()->setWrapText(true);

            // Выравнивание текста
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
            // Шрифт Times New Roman
            $sheet->getStyle('A1')->getFont()->setName('Arial');
             
            // Размер шрифта 18
            $sheet->getStyle("A1")->getFont()->setSize(14);

            // Жирный
            $sheet->getStyle("A1")->getFont()->setBold(true);

            $sheet->getRowDimension("1")->setRowHeight(50);

             $sheet->getColumnDimensionByColumn("A")->setAutoSize(true);
             $sheet->getColumnDimension("K")->setWidth(16);
            $sheet->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            // Вставляем текст в ячейку A1

            $title = "Перелік співробітників - ювілярів ХОАМДТ ім М. КУЛІША \n у 2021 р.";

            $sheet->setCellValue("A1", $title);

            //B
          



            $i = 0; 
            $r = 2;

            foreach ($data['prs'] as $person) {

                $i++;
                $r++;

                   
                $sheet->setCellValue("A".$r, (string) $i);

                $sheet->mergeCells("B".$r.":F".$r);
                $sheet->setCellValue("B".$r, $person['fullname']);


                if($person['kkpSubprof'] !== "" ){

                    $prof =  $person['prof']." ".$person['kkpSubprof'];

                }else{

                    $prof =  $person['prof'];

                }

                $sheet->mergeCells("G".$r.":I".$r);
                $sheet->setCellValue("G".$r, $prof);

                $age =  (string) ( (int) date('Y') - (int) date('Y', strtotime($person['birthdate'])));


                $sheet->setCellValue("K".$r,  $person['birthdate']. "   ".$age." .р");



                
            }
           

            $f = 'Ювілеї_'.date('Y').'.xlsx';

            header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=$f");

            $objWriter = new PHPExcel_Writer_Excel2007($xls);
            $objWriter->save('php://output');

        }


    }


    static function months($k){


        $months = array('01'=>'січня', '02'=>'лютого', '03'=>'березня', '04'=>'квітня', '05'=>'травня', '06'=>'червня',
                       '07'=>'липня', '08'=>'серпня', '09'=>'вересня', '10'=>'жовтня', '11'=>'листопада', '12'=>'грудня');


    return $months[$k];
    }


    static function uu($text){

            $r  = new PHPExcel_RichText();
            $r_set = $r->createTextRun($text);
            $r_set->getFont()->setUnderline(true);
            return $r;

    }


    static function print_anket($data=[]){


        if(count($data)>0){

            $anket = $data['anket'];
            $personal = $data['personal'];
            $education = $data['education'];
            $aeducation = $data['aeducation'];
            $peducation = $data['peducation'];
            $apptran = $data['apptran'];
            $rest = $data['rest'];
            $exp = $data['exp'];
            $family = $data['family'];


            $dateExp = $data['dateExp'];

            

            // Создаем объект класса PHPExcel
            $xls = new PHPExcel();
            // Устанавливаем индекс активного листа
            $xls->setActiveSheetIndex(0);
            // Получаем активный лист
            $sheet = $xls->getActiveSheet();
            $sheet->getPageSetup()->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);

            $sheet->getPageMargins()->setTop(0);
            $sheet->getPageMargins()->setRight(0);
            $sheet->getPageMargins()->setLeft(0.5);
            $sheet->getPageMargins()->setBottom(0);
            $sheet->getDefaultStyle()->getFont()->setName('Arial');
            $sheet->getDefaultStyle()->getFont()->setSize(7);


            $sheet->getStyle("A1:O500")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

            // Подписываем лист
            $sheet->setTitle('Анкета');
            // Объединяем ячейки
            $sheet->mergeCells('A1:E2');
            $sheet->getStyle("A1")->getAlignment()->setWrapText(true);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("A1")->getFont()->setBold(true);

            $sheet->setCellValue("A1", "Херсонський обласний академічний\nмузично-драматичний театр ім М. Куліша");


          
            $border = array(
                'borders'=>array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                )
            );

            $sheet->getStyle("A2:E2")->applyFromArray($border);

            $sheet->mergeCells('A3:E3');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A3")->getFont()->setItalic(true);

            $sheet->setCellValue("A3", "Найменування підприємства (установи, організації)");

            $sheet->getColumnDimension("A")->setWidth(13.67);
            $sheet->setCellValue("A4", "Код ЄДРПОУ");

            $sheet->mergeCells('B4:E4');
            $sheet->getStyle('B4:E4')->applyFromArray($border);
            $sheet->getStyle("B4")->getFont()->setBold(true);
            $sheet->getRowDimension("4")->setRowHeight(17.50);

            $sheet->setCellValue("B4", " 02225855");

            $sheet->mergeCells('K3:O4');
            $sheet->getStyle("K3")->getAlignment()->setWrapText(true);
            $sheet->setCellValue("K3", "ЗАТВЕРДЖЕНО\nнаказом Держкомстату та Міністерства оборони\nУкраїни від 25 грудня 2009 р. № 495.656");


            $sheet->mergeCells('A6:A8');
            $sheet->getStyle("A6")->getFont()->setBold(true);
            $sheet->getStyle("A6")->getFont()->setSize(8);
            $sheet->getStyle("A6")->getAlignment()->setWrapText(true);
            $sheet->getStyle("A6")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("A6", "Дата\nзаповнення");



            $sheet->mergeCells('B6:C8');
            $sheet->getStyle("B6")->getFont()->setBold(true);
            $sheet->getStyle("B6")->getFont()->setSize(8);
            $sheet->getStyle("B6")->getAlignment()->setWrapText(true);
            $sheet->getStyle("B6")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("B6", "Табельний номер");


            $sheet->mergeCells('D6:F8');
            $sheet->getStyle("D6")->getFont()->setBold(true);
            $sheet->getStyle("D6")->getFont()->setSize(8);
            $sheet->getStyle("D6")->getAlignment()->setWrapText(true);
            $sheet->getStyle("D6")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("D6", "Індивідуальний\nідентифікаційний\nномер");

            $sheet->mergeCells('G6:H8');
            $sheet->getStyle("G6")->getFont()->setBold(true);
            $sheet->getStyle("G6")->getFont()->setSize(8);
            $sheet->getStyle("G6")->getAlignment()->setWrapText(true);
            $sheet->getStyle("G6")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("G6", "Cтать\n(чоловіча,\nжіноча)");

            $sheet->mergeCells('I6:K8');
            $sheet->getStyle("I6")->getFont()->setBold(true);
            $sheet->getStyle("I6")->getFont()->setSize(8);
            $sheet->getStyle("I6")->getAlignment()->setWrapText(true);
            $sheet->getStyle("I6")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getStyle('I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("I6", "Вид роботи\n(основна,\nза сумісництвом)");

            $sheet->getRowDimension("8")->setRowHeight(14);





            $sheet->getStyle('A9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("A9", (string) $anket->createdate);

            $sheet->mergeCells('B9:C9');
            $sheet->getStyle('B9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("B9", (string) $anket->tid);


            $sheet->mergeCells('D9:F9');
            $sheet->getStyle('D9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("D9", (string) $personal->pid);



            $sex = ["жіноча", "чоловіча"];            
            $sheet->mergeCells('G9:H9');
            $sheet->getStyle('G9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("G9", $sex[(int)$personal->gen]);



            $typeOfwork = ["oсновна", "за сумнісництвом", "за суміщенням"]; 

            $sheet->mergeCells('I9:K9');
            $sheet->getStyle('I9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("I9",  $typeOfwork[(int) $anket->typework]);

            $all_border = array(
                'borders'=>array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                )
            );


            $sheet->getStyle("A6:K9")->applyFromArray($all_border);



            //photo
            // $sheet->mergeCells('M6:O18');
            // $sheet->getStyle('M6:O18')->applyFromArray($all_border);


            $sheet->mergeCells('A12:O16');
            $sheet->getStyle("A12")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A12")->getFont()->setBold(true);
            $sheet->getStyle("A12")->getFont()->setSize(20);
            $sheet->setCellValue("A12",  "ОСОБОВА КАРТКА ПРАЦІВНИКА");

            $sheet->getStyle("A18")->getFont()->setBold(true);
            $sheet->getStyle("A18")->getFont()->setSize(10);
            $sheet->setCellValue("A18",  "I. ЗАГАЛЬНІ ВІДОМОСТІ");



            $t = "1. Прізвище <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$personal->name."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u> ім'я <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$personal->sname."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u> по батькові <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$personal->mname."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>";
            $sheet->getStyle("A19")->getFont()->setSize(10);
            $sheet->setCellValue("A19",  self::h2s($t));

            $t  = "2. Дата народження \"<u>&nbsp;".date('d', strtotime($personal->berthdate))."&nbsp;</u>\"";
            $t .= "<u>&nbsp;&nbsp;&nbsp;" . self::months( date('m', strtotime($personal->berthdate)) )."&nbsp;&nbsp;&nbsp;</u>&nbsp;";
            $t .= "<u>&nbsp;&nbsp;&nbsp;" . date('Y', strtotime($personal->berthdate)) ."&nbsp;&nbsp;&nbsp;</u>р.";
            $t .= "&nbsp;&nbsp;3. Громадянство<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$personal->nat."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>";
            
            $sheet->getStyle("A20")->getFont()->setSize(10);
            $sheet->setCellValue("A20",  self::h2s($t));

            //Education
            $r=21;
            if(count($education)>0){

                $edn = ['', 'базова загальна середня', 'повна загальна середня', 'професійно технічна', 'неповна вища', 'базова вища', 'повна вища']; 

                $t = '';
                foreach ($education as $k => $e) {
                    if($k<(count($education)-1)){
                        $t .= $edn[(int)$e->edn].", ";
                    }else{
                        $t .= $edn[(int)$e->edn];
                    }
                }

                $sheet->getStyle("A".$r)->getFont()->setSize(10);
                $sheet->setCellValue("A".$r,  "4. Освіта (".$t.")");



                //Education table

               
                $r++;
                $sheet->mergeCells("A".$r.":G".$r);
                $sheet->mergeCells("H".$r.":L".$r);  
                $sheet->mergeCells("M".$r.":O".$r);

                $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setBold(true);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("A".$r, "Назва освітнього закладу");
                $sheet->setCellValue("H".$r, "Диплом (свідоцтво), серія, номер");  
                $sheet->setCellValue("M".$r, "Рік закінчення");
           

                foreach ($education as $k => $e) {

                    $r++;
                    
                    $sheet->mergeCells("A".$r.":G".$r);
                    $sheet->mergeCells("H".$r.":L".$r);  
                    $sheet->mergeCells("M".$r.":O".$r);
                    $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);

                    $sheet->setCellValue("A".$r, $e->title);
                    $sheet->setCellValue("H".$r, $e->doc);  
                    $sheet->setCellValue("M".$r, $e->year);


                }


                //part 2 of table
                $r++;

                $sheet->mergeCells("A".$r.":G".$r);
                $sheet->mergeCells("H".$r.":L".$r);  
                $sheet->mergeCells("M".$r.":O".$r);

                $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setBold(true);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("A".$r, "Спеціальність (професія) за дипломом (свідоцтвом)");
                $sheet->setCellValue("H".$r, "Кваліфікація за дипломом (свідоцтвом)");  
                $sheet->setCellValue("M".$r, "Форма навчання (денна, вечірня, заочна)");
           

                foreach ($education as $k => $e) {

                    $r++;
                    
                    $sheet->mergeCells("A".$r.":G".$r);
                    $sheet->mergeCells("H".$r.":L".$r);  
                    $sheet->mergeCells("M".$r.":O".$r);
                    $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);

                    $sheet->setCellValue("A".$r, $e->spec);
                    $sheet->setCellValue("H".$r, $e->qual);  
                    $sheet->setCellValue("M".$r, $e->type);


                }

                
            }else{

                $sheet->getStyle("A".$r)->getFont()->setSize(10);
                $sheet->setCellValue("A".$r,  "4. Освіта ");
                
                //Education table

                $r++;

                $sheet->mergeCells("A".$r.":G".$r);
                $sheet->mergeCells("H".$r.":L".$r);  
                $sheet->mergeCells("M".$r.":O".$r);

                $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setBold(true);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("A".$r, "Назва освітнього закладу");
                $sheet->setCellValue("H".$r, "Диплом (свідоцтво), серія, номер");  
                $sheet->setCellValue("M".$r, "Рік закінчення");
           

                for($i=0; $i<=2; $i++){

                    $r++;
                    
                    $sheet->mergeCells("A".$r.":G".$r);
                    $sheet->mergeCells("H".$r.":L".$r);  
                    $sheet->mergeCells("M".$r.":O".$r);
                    $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);

                    $sheet->setCellValue("A".$r, "");
                    $sheet->setCellValue("H".$r, "");  
                    $sheet->setCellValue("M".$r, "");


                }


                //part 2 of table
                $r++;

                $sheet->mergeCells("A".$r.":G".$r);
                $sheet->mergeCells("H".$r.":L".$r);  
                $sheet->mergeCells("M".$r.":O".$r);

                $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setBold(true);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("A".$r, "Спеціальність (професія) за дипломом (свідоцтвом)");
                $sheet->setCellValue("H".$r, "Кваліфікація за дипломом (свідоцтвом)");  
                $sheet->setCellValue("M".$r, "Форма навчання (денна, вечірня, заочна)");
           


                for($i=0; $i<=2; $i++){

                    $r++;
                    
                    $sheet->mergeCells("A".$r.":G".$r);
                    $sheet->mergeCells("H".$r.":L".$r);  
                    $sheet->mergeCells("M".$r.":O".$r);
                    $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);

                    $sheet->setCellValue("A".$r, "");
                    $sheet->setCellValue("H".$r, "");  
                    $sheet->setCellValue("M".$r, "");


                }


            }

            //AfterEducation
            $r++;
            if(count($aeducation)>0){

                $aedn = ['', 'аспірантурі', 'адюнктурі', 'докторантурі']; 

                $t = '';
                foreach ($aeducation as $k => $ae) {
                    if($k<(count($aeducation)-1)){
                        $t .= $aedn[(int)$ae->edn].", ";
                    }else{
                        $t .= $aedn[(int)$ae->edn];
                    }
                }

                $r++; 
                $sheet->getStyle("A".$r)->getFont()->setSize(10);
                $sheet->setCellValue("A".$r,  "5. Післядипломна професійна підготовка: навчання в ".$t);



                //AfterEducation table

                $r++; 

                $sheet->mergeCells("A".$r.":F".$r);
                $sheet->mergeCells("G".$r.":J".$r);  
                $sheet->mergeCells("K".$r.":L".$r);
                $sheet->mergeCells("M".$r.":O".$r);

                $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setBold(true);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("A".$r, "Назва освітнього, наукового закладу ");
                $sheet->setCellValue("G".$r, "Диплом, номер, дата видачі ");  
                $sheet->setCellValue("K".$r, "Рік закінчення");
                $sheet->setCellValue("M".$r, "Науковий ступінь,учене звання");
           

                foreach ($aeducation as $k => $ae) { 

                    $r++;
                    
                    $sheet->mergeCells("A".$r.":F".$r);
                    $sheet->mergeCells("G".$r.":J".$r);  
                    $sheet->mergeCells("K".$r.":L".$r);
                    $sheet->mergeCells("M".$r.":O".$r);

                    $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);

                    $sheet->setCellValue("A".$r, $ae->title);
                    $sheet->setCellValue("G".$r, $ae->doc);  
                    $sheet->setCellValue("K".$r, $ae->year);
                    $sheet->setCellValue("M".$r, $ae->spec);


                }

            }else{

               
                $r++; 
                $t = '';
                $sheet->getStyle("A".$r)->getFont()->setSize(10);
                $sheet->setCellValue("A". $r,  "5. Післядипломна професійна підготовка: навчання в ".$t);



                //AfterEducation table

                $r++; 

                $sheet->mergeCells("A".$r.":F".$r);
                $sheet->mergeCells("G".$r.":J".$r);  
                $sheet->mergeCells("K".$r.":L".$r);
                $sheet->mergeCells("M".$r.":O".$r);

                $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setBold(true);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("A".$r, "Назва освітнього, наукового закладу ");
                $sheet->setCellValue("G".$r, "Диплом, номер, дата видачі ");  
                $sheet->setCellValue("K".$r, "Рік закінчення");
                $sheet->setCellValue("M".$r, "Науковий ступінь,учене звання");
           

                for($i=0; $i<=1; $i++) { 

                    $r++;
                    
                    $sheet->mergeCells("A".$r.":F".$r);
                    $sheet->mergeCells("G".$r.":J".$r);  
                    $sheet->mergeCells("K".$r.":L".$r);
                    $sheet->mergeCells("M".$r.":O".$r);

                    $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);

                    $sheet->setCellValue("A".$r, "");
                    $sheet->setCellValue("G".$r, "");  
                    $sheet->setCellValue("K".$r, "");
                    $sheet->setCellValue("M".$r, "");


                }


            }

        //last Work
            $r++;
            if(count($exp)>0){

                $k  = count($exp)-1; 
                $t  = "6. Останнє місце роботи <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$exp[$k]->lastwork."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>";
                if(mb_strlen($exp[$k]->lastwork)>=33 or mb_strlen($exp[$k]->lastspec)>30){
                   
                    $sheet->getStyle("A".$r)->getAlignment()->setWrapText(true);
                    $sheet->getRowDimension($r)->setRowHeight(27);
                    $sheet->mergeCells("A".$r.":O".$r);

                    $t.="\n";
                }

                $t .= "посада (професія) <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$exp[$k]->lastspec."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>";

                $sheet->setCellValue("A".$r, self::h2s($t));

            }else{


                $t  = "6. Останнє місце роботи <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>";

                $t .= "посада (професія) <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>";

                $sheet->setCellValue("A".$r, self::h2s($t));

            }


        //Exp         
            $r++;
          
            $t  = "7. Стаж роботи станом на \"<u>&nbsp;".date('d', strtotime($anket->createdate))."&nbsp;</u>\"";
            $t .= "<u>&nbsp;&nbsp;&nbsp;" . self::months( date('m', strtotime($anket->createdate)) )."&nbsp;&nbsp;&nbsp;</u>&nbsp;";
            $t .= "<u>&nbsp;&nbsp;&nbsp;" . date('Y', strtotime($anket->createdate)) ."&nbsp;&nbsp;&nbsp;</u>р.";
            $t .= " Загальний<u>&nbsp;&nbsp;&nbsp;" .$dateExp['days']."&nbsp;&nbsp;&nbsp;</u>днів";
            $t .= "<u>&nbsp;&nbsp;&nbsp;".$dateExp['months']."&nbsp;&nbsp;&nbsp;</u>місяців";
            $t .= "<u>&nbsp;&nbsp;&nbsp;".$dateExp['years']."&nbsp;&nbsp;&nbsp;</u>років,";

            $sheet->setCellValue("A".$r, self::h2s($t));


            $r++; 

            $t = "що дає право на надбавку за вислугу років";
            $t .= "<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>днів";
            $t .= "<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>місяців";
            $t .= "<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>років.";

            $sheet->setCellValue("D".$r, self::h2s($t));


            $r++; 

            $t = "8. Дата та причина звільнення (скорочення штатів, за власним бажанням, за прогул та інші порушення, невідповідність\nзайманій посаді тощо)";
            $t .= "\"<u>&nbsp;".date('d', strtotime($anket->lastdissdate))."&nbsp;</u>\"";
            $t .= "<u>&nbsp;&nbsp;&nbsp;" . self::months( date('m', strtotime($anket->lastdissdate)) )."&nbsp;&nbsp;&nbsp;</u>&nbsp;";
            $t .= "<u>&nbsp;&nbsp;&nbsp;" . date('Y', strtotime($anket->lastdissdate)) ."&nbsp;&nbsp;&nbsp;</u>р.";

            $sheet->mergeCells("A".$r.":O".$r);
            $sheet->getStyle("A".$r)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($r)->setRowHeight(27);
            $sheet->getStyle("A".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $sheet->setCellValue("A".$r, self::h2s($t));

        //Pension 

            $r++;
        
            $t  = "Відомості про отримання пенсії (у разі наявності вказати вид пенсійних виплат згідно з чинним законодавством)\n";

            $sheet->mergeCells("A".$r.":O".$r);
            if(strlen($anket->pension)>3){

                $t .= "<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$anket->pension."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>";

            }else{

                $sheet->getStyle("A".$r.":O".$r)->applyFromArray($border);

            }

            
            $sheet->getStyle("A".$r)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($r)->setRowHeight(27);
            $sheet->setCellValue("A".$r, self::h2s($t));
           
        //Family
            $r++;
            $r++;

            $t="";
            if(count($family)>0){

                if($anket->gen==1){

                    if($family[0]->status == 0) { $t = "неодружений";}else{ $t = "одружений"; } 

                }else{

                    if($family[0]->status == 0) { $t = "незаміжня";}else{ $t = "заміжня"; } 
                }
            }


            $sheet->setCellValue("A".$r, self::h2s("10. Родинний стан <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$t."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>"));


            
           
            if(count($family)>0){


                $r++; 

                $sheet->mergeCells("A".$r.":D".$r);
                $sheet->mergeCells("E".$r.":L".$r);  
                $sheet->mergeCells("M".$r.":O".$r);
              

                $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setBold(true);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("A".$r, "Ступінь родинного зв'язку (склад сім'ї)");
                $sheet->setCellValue("E".$r, "П. І. Б.");  
                $sheet->setCellValue("M".$r, "Рік закінчення");
     
           

                foreach ($family as $k => $f) { 

                    $r++;
                    
                    $sheet->mergeCells("A".$r.":D".$r);
                    $sheet->mergeCells("E".$r.":L".$r);  
                    $sheet->mergeCells("M".$r.":O".$r);

                    $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);

                    $sheet->setCellValue("A".$r, $f->person);
                    $sheet->setCellValue("E".$r, $f->name);  
                    $sheet->setCellValue("M".$r, $f->year);


                }

            }else{

               
                $r++; 

                $sheet->mergeCells("A".$r.":D".$r);
                $sheet->mergeCells("E".$r.":L".$r);  
                $sheet->mergeCells("M".$r.":O".$r);
              

                $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setBold(true);
                $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("A".$r, "Ступінь родинного зв'язку (склад сім'ї)");
                $sheet->setCellValue("E".$r, "П. І. Б.");  
                $sheet->setCellValue("M".$r, "Рік закінчення");
     
           

                for ($i=0; $i<=3; $i++) { 

                    $r++;
                    
                    $sheet->mergeCells("A".$r.":D".$r);
                    $sheet->mergeCells("E".$r.":L".$r);  
                    $sheet->mergeCells("M".$r.":O".$r);

                    $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);

                    $sheet->setCellValue("A".$r, "");
                    $sheet->setCellValue("E".$r, "");  
                    $sheet->setCellValue("M".$r, "");


                }


            }

            $r++;
            $t = "11. Місце фактичного проживання (область, місто, район, вулиця, № будинку, квартири, номер контактного\nтелефону, поштовий індекс)      ".$personal->ad1;
            $sheet->mergeCells("A".$r.":O".$r);
            $sheet->getStyle("A".$r)->getAlignment()->setWrapText(true);
            $sheet->getStyle("A".$r)->getFont()->setSize(9);
            $sheet->getRowDimension($r)->setRowHeight(24);
            $sheet->setCellValue("A".$r, $t);
            $sheet->getStyle("D".$r.":O".$r)->applyFromArray($border);


            $r++;
            $t = "12. Місце проживання за державною реєстрацією ".$personal->ad2;
            $sheet->getStyle("G".$r.":O".$r)->applyFromArray($border);
            $sheet->getRowDimension($r)->setRowHeight(16);
            $sheet->setCellValue("A".$r, self::h2s($t));

            $r++;
            $t = "Паспорт:  серія&nbsp;<u>&nbsp;&nbsp;".$personal->pserial."&nbsp;&nbsp;</u>&nbsp;&nbsp;№<u>&nbsp;&nbsp;".$personal->pnum."&nbsp;&nbsp;</u>,";
            $t .= "  ким виданий  " . $personal->pwho;
            $sheet->getStyle("G".$r.":O".$r)->applyFromArray($border);
            $sheet->setCellValue("A".$r, self::h2s($t));

            $r++;
            $t = "дата видачі   " . $personal->pdate;
            $sheet->getStyle("B".$r.":O".$r)->applyFromArray($border);
            $sheet->setCellValue("A".$r, self::h2s($t));

            $r++;
            $r++;
            $sheet->getStyle("A".$r)->getFont()->setSize(10);
            $sheet->getStyle("A".$r)->getFont()->setBold(true);
            $sheet->setCellValue("A".$r,  "II. ВІДОМОСТІ ПРО ОБЛІКОВИЙ ОБЛІК");



            $r++;
            $t = "Група обліку ".self::excelStr($personal->mgroup, 44);
            $sheet->setCellValue("A".$r,  self::h2s($t));

            $t = "Придатність до військової служби ".self::excelStr($personal->mexp, 34);
            $sheet->setCellValue("H".$r,  self::h2s($t));



            $r++;
            $t = "Категорія обліку ".self::excelStr($personal->mcat, 39);
            $sheet->setCellValue("A".$r,  self::h2s($t));

            $t = "Назва райвійськомату за місцем реєстрації ".self::excelStr($personal->rnm1, 24);
            $sheet->setCellValue("H".$r,  self::h2s($t));


            $r++; 
            $sheet->mergeCells("A".$r.":E".$r);
            $sheet->getStyle("A".$r.":E".$r)->applyFromArray($border);

            $sheet->mergeCells("H".$r.":O".$r);
            $sheet->getStyle("H".$r.":O".$r)->applyFromArray($border);




            $r++;
            $t = "Склад ".self::excelStr($personal->mrank, 55);
            $sheet->setCellValue("A".$r,  self::h2s($t));

            $t = "Назва райвійськомату за місцем фактичного проживання";
            $sheet->setCellValue("H".$r,  self::h2s($t));



            $r++;
            $t = "Військове звання ".self::excelStr($personal->mcompos, 39);
            $sheet->setCellValue("A".$r,  self::h2s($t));

            $sheet->mergeCells("H".$r.":O".$r);
            $sheet->getStyle("H".$r.":O".$r)->applyFromArray($border);

            $t = self::excelStr($personal->rnm2, 28);
            $sheet->setCellValue("H".$r,  self::h2s($t));



            $r++;
            $t = "Війсоково-облікова спец. № ".self::excelStr($personal->mspec, 21);
            $sheet->setCellValue("A".$r,  self::h2s($t));

            $t = "Перебування на спеціальному обліку".self::excelStr($personal->spec, 31);
            $sheet->setCellValue("H".$r,  self::h2s($t));


            //Prof education

            $r=76;
            $sheet->getStyle("A".$r)->getFont()->setSize(10);
            $sheet->getStyle("A".$r)->getFont()->setBold(true);
            $t = "ІІІ. ПРОФЕСІЙНА ОСВІТА НА ВИРОБНИЦТВІ (ЗА РАХУНОК ПІДПРИЄМСТВА-РОБОТОДАВЦЯ)";
            $sheet->setCellValue("A".$r, $t);


            $r++;             
            $sheet->mergeCells("B".$r.":D".$r);  
            $sheet->mergeCells("E".$r.":G".$r);
            $sheet->mergeCells("H".$r.":I".$r);
            $sheet->mergeCells("J".$r.":K".$r);
            $sheet->mergeCells("L".$r.":O".$r);
            $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
            $sheet->getStyle("A".$r.":O".$r)->getFont()->setBold(true);
            $sheet->getRowDimension($r)->setRowHeight(21);
            $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);
            $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setWrapText(true);


            $sheet->setCellValue("A".$r, "Дата");
            $sheet->setCellValue("B".$r, "Назва структурного\nпідрозділу");
            $sheet->setCellValue("E".$r, "Період\nнавчання");
            $sheet->setCellValue("H".$r, "Вид\nнавчання");
            $sheet->setCellValue("J".$r, "Форма\nнавчання");
            $sheet->setCellValue("L".$r, "Назва документа, що посвідчує\nпрофесійну освіту, ким виданий");
           
            if(count($peducation)>0){

                    foreach ($peducation as $p) {

                        $r++;             
                        $sheet->mergeCells("B".$r.":D".$r);  
                        $sheet->mergeCells("E".$r.":G".$r);
                        $sheet->mergeCells("H".$r.":I".$r);
                        $sheet->mergeCells("J".$r.":K".$r);
                        $sheet->mergeCells("L".$r.":O".$r);
                        $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                        $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                        $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);


                        $sheet->setCellValue("A".$r, $p->date);
                        $sheet->setCellValue("B".$r, $p->title);
                        $sheet->setCellValue("E".$r, $p->b ."-". $p->e);
                        $sheet->setCellValue("H".$r, $p->type);
                        $sheet->setCellValue("J".$r, $p->form);
                        $sheet->setCellValue("L".$r, $p->doc);



                       
                    }


            }else{



                    for($i=0; $i<=5; $i++){

                        $r++;             
                        $sheet->mergeCells("B".$r.":D".$r);  
                        $sheet->mergeCells("E".$r.":G".$r);
                        $sheet->mergeCells("H".$r.":I".$r);
                        $sheet->mergeCells("J".$r.":K".$r);
                        $sheet->mergeCells("L".$r.":O".$r);
                        $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                        $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                        $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);

                    
                    }




            }

   //App Tran

            $r++;
            $r++;
            $sheet->getStyle("A".$r)->getFont()->setSize(10);
            $sheet->getStyle("A".$r)->getFont()->setBold(true);
            $t = "IV. ПРИЗНАЧЕННЯ І ПЕРЕВЕДЕННЯ";
            $sheet->setCellValue("A".$r, $t);


            $r++;
            $sheet->mergeCells("A".$r.":A".($r+1));               
            $sheet->mergeCells("B".$r.":E".($r+1)); 

            $sheet->mergeCells("F".$r.":J".$r);
            $sheet->mergeCells("F".($r+1).":I".($r+1));

            $sheet->mergeCells("K".$r.":L".($r+1));
            $sheet->mergeCells("M".$r.":O".($r+1));
            $sheet->getStyle("A".$r.":O".($r+1))->applyFromArray($all_border);


            $sheet->getStyle("A".$r.":O".($r+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A".$r.":O".($r+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A".$r.":O".($r+1))->getFont()->setSize(8);
            $sheet->getStyle("A".$r.":O".($r+1))->getFont()->setBold(true);
            $sheet->getStyle("A".$r.":O".($r+1))->applyFromArray($all_border);
            $sheet->getStyle("A".$r.":O".($r+1))->getAlignment()->setWrapText(true);


            $sheet->setCellValue("A".$r, "Дата");
            $sheet->setCellValue("B".$r, "Назва структурного\nпідрозділу");
            $sheet->setCellValue("F".$r, "Професія, посада");
            $sheet->setCellValue("F".($r+1), "Назва");
            $sheet->setCellValue("J".($r+1), "Код за КП*");
            $sheet->setCellValue("K".$r, "Розряд\n(cклад)");
            $sheet->setCellValue("M".$r, "Підстава,\nнаказ №");


            $r++;
            if(count($apptran)>0){

                    foreach ($apptran as $a) {

                        $r++;
            
                        $sheet->mergeCells("B".$r.":E".$r); 
                        $sheet->mergeCells("F".$r.":I".$r);
                        $sheet->mergeCells("K".$r.":L".$r);
                        $sheet->mergeCells("M".$r.":O".$r);
     
                        $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                        $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);


                        foreach (self::kkpCats() as $c) {

                            if($c->id == $a->kkpCatId){

                                $sheet->setCellValue("B".$r, $c->title);

                            }
                        }


                        $sheet->setCellValue("A".$r, $a->date);
                        $sheet->setCellValue("F".$r, $a->prof);
                        $sheet->setCellValue("J".$r, $a->kkpid);
                        $sheet->setCellValue("K".$r, $a->rank);
                        $sheet->setCellValue("M".$r, $a->doc);



                       
                    }


            }else{


                for($i=0; $i<=3; $i++){


                        $r++;
            
                        $sheet->mergeCells("B".$r.":E".$r); 
                        $sheet->mergeCells("F".$r.":I".$r);
                        $sheet->mergeCells("K".$r.":L".$r);
                        $sheet->mergeCells("M".$r.":O".$r);
     
                        $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
                        $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);




                }



            }



            //rest

            $r++;
            $r++;
            $sheet->getStyle("A".$r)->getFont()->setSize(10);
            $sheet->getStyle("A".$r)->getFont()->setBold(true);
            $t = "V. ВІДПУСТКИ";
            $sheet->setCellValue("A".$r, $t);

            $r++;
            $sheet->mergeCells("A".$r.":D".($r+1));
            $sheet->mergeCells("E".$r.":G".($r+1));
            $sheet->mergeCells("H".$r.":K".$r);
            $sheet->mergeCells("H".($r+1).":I".($r+1));
            $sheet->mergeCells("J".($r+1).":K".($r+1));
            $sheet->mergeCells("L".$r.":O".($r+1));

            $sheet->getStyle("A".$r.":O".($r+1))->applyFromArray($all_border);


            $sheet->getStyle("A".$r.":O".($r+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A".$r.":O".($r+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A".$r.":O".($r+1))->getFont()->setSize(8);
            $sheet->getStyle("A".$r.":O".($r+1))->getFont()->setBold(true);
            $sheet->getStyle("A".$r.":O".($r+1))->applyFromArray($all_border);
            $sheet->getStyle("A".$r.":O".($r+1))->getAlignment()->setWrapText(true);


            $sheet->setCellValue("A".$r, "Вид відпустки");
            $sheet->setCellValue("E".$r, "За який період");
            $sheet->setCellValue("H".$r, "Дата");
            $sheet->setCellValue("H".($r+1), "початок відпустки");
            $sheet->setCellValue("J".($r+1), "закінчення відпустки");
            $sheet->setCellValue("L".$r, "Підстава,наказ №");



            if(count($rest)>0){

                $r++;
                foreach ($rest as $rt) {

                   $r++;
                   
                    $sheet->mergeCells("A".$r.":D".$r);
                    $sheet->mergeCells("E".$r.":G".$r);
                    $sheet->mergeCells("H".$r.":I".$r);
                    $sheet->mergeCells("J".$r.":K".$r);
                    $sheet->mergeCells("L".$r.":O".$r);

                    $sheet->getStyle("A".$r.":O".$r)->applyFromArray($all_border);


                    $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A".$r.":O".$r)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(8);
        
                    $sheet->setCellValue("A".$r, $rt->type);
                    $sheet->setCellValue("E".$r, $rt->pb ."-".$rt->pe);
                    $sheet->setCellValue("H".$r, $rt->rb);
                    $sheet->setCellValue("J".$r, $rt->re);
                    $sheet->setCellValue("L".$r, $rt->doc);

                 }

            }


            $r++;
            $r++;

            $sheet->setCellValue("A".$r, "Додаткові відомості");
            $sheet->setCellValue("D".$r, $anket->add);
            $sheet->getStyle("D".$r.":O".$r)->applyFromArray($border);
            $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(10);

            $r++;
            $sheet->getStyle("A".$r.":O".$r)->applyFromArray($border);
            $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(10);

            $r++;
            $r++;
            $sheet->setCellValue("A".$r, "Дата і причина звільнення (підстава) ");
            $sheet->getStyle("F".$r.":O".$r)->applyFromArray($border);
            $sheet->setCellValue("F".$r, $anket->dissdate . "   ". $anket->diss);
            $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(10);

            $r++;
            $r++;
            $sheet->setCellValue("A".$r, "Працівник кадрової служби");
            $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(10);

            $sheet->getStyle("E".$r.":G".$r)->applyFromArray($border);
            $sheet->getStyle("I".$r.":K".$r)->applyFromArray($border);
            $sheet->getStyle("M".$r.":O".$r)->applyFromArray($border);

            $r++;
            $sheet->mergeCells("E".$r.":G".$r);
            $sheet->mergeCells("I".$r.":K".$r);
            $sheet->mergeCells("M".$r.":O".$r);

            $sheet->setCellValue("E".$r, "(посада)");
            $sheet->setCellValue("I".$r, "(підпис)");
            $sheet->setCellValue("M".$r, "(П.І.Б)");
            $sheet->getStyle("E".$r.":O".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E".$r.":O".$r)->getFont()->setSize(8);

            $r++;
            $r++;
            $sheet->setCellValue("A".$r, "Підпис працівника");
            $sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(10);

            $sheet->mergeCells("C".$r.":D".$r);
            $sheet->getStyle("C".$r.":D".$r)->applyFromArray($border);
           

            $t  = "\"<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>\"";
            $t .= "<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>&nbsp;";
            $t .= "<u>&nbsp;&nbsp;&nbsp;20&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>р.";
            $sheet->setCellValue("L".$r, self::h2s($t));






            //$sheet->getStyle("A".$r.":O".$r)->getFont()->setSize(10);



           

            $f = 'Ювілеї_'.date('Y').'.xlsx';

            header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=$f");


            $objWriter = new PHPExcel_Writer_Excel2007($xls);
            $objWriter->save('php://output');

        }


    }

    static function getImage($data, $disk){


        $imageUrl     = '/public/src/images/';
        $exOrg        = '.jpg';
        $exThu        = 'thu.jpg';


        $showImage = json_decode($data->image, true); 

        $imageSrc = $imageUrl . $disk . '/' . $showImage[1] . $exOrg; 

        if (Storage::disk( $disk )->has($showImage[1]  . $exOrg ) ){

            $showImage = $imageSrc;

        } else {

            $showImage = 0; 

        }


        return $showImage; 


    }


    static function getGallery($data, $disk){


        $imageUrl     = '/public/src/images/';
        $exOrg        = '.jpg';
        $exThu        = 'thu.jpg';



                    $showImages = json_decode($data->images, true);
                    $showGallery = array(); 

                    if(is_array($showImages)){

                        foreach ($showImages as $key => $value) {
                            
                            $showGallery[] = [ 

                            'thu'=>$imageUrl . $disk . '/' . $value . $exThu,
                            'org'=>$imageUrl . $disk . '/' . $value . $exOrg

                             ];

                        }

                    }


        return $showGallery; 


    }



    public function strHash($str){

        $str = trim($str);
        $str = preg_replace('/&(amp;)?(.+?);/', '', $str);
        $str = str_replace('"', "", $str);
        $str = str_replace('«', "", $str);
        $str = str_replace('»', "", $str);
        $str = md5(str_replace(" ", "", $str)); 

        return $str; 
    }



    static function validateRules($id){

        $rules = [

            'perNum' =>'required|min:5|max:10',
            'id'=>'required|min:5|max:10',

        ];



        return [$id =>$rules[$id]]; 




    }





}

?>