<?php use Illuminate\Support\Arr; ?>
@extends('auth.admin')

@section('content')
<form novalidate path="/edit/{{$anket->tid}}" method="post" id="form">
<div class="content">
    <div class="btn_print"><a href="/{{Request::path()}}/print">Друк</a></div> 
    <br/>   
    <div class="category">Загальна</div>
    <div class="contGray">

        <table width="100%">
            <tr>
                <td width="auto">
                    <label>Дата заповнення</label><input  id="createDate" name="createdate" type="text" size="6" value="{{$anket->createdate}}">
                    <label>Табельний номер</label><input id="tid" type="text" size="7" maxlength="10" value="{{$anket->tid}}" readonly>
                    <label>ІІН</label><input id="pid" class="{{ $errors->has('pid') ? 'error':''  }}" type="text" size="7" maxlength="10" name="pid" value="{{ old('pid')}} {{$personal->pid}}">
                    <label>Стать</label>
                    <select id="gen" name="gen">
                        <option {{ old('gen') !== null ? old('gen')==''   ? 'selected': '' :  $personal->gen=='' ? 'selected': ''}} value=""></option>
                        <option {{ old('gen') !== null ? old('gen')=='0'  ? 'selected': '' :  $personal->gen=='0'? 'selected': ''}} value="0">жіноча</option>
                        <option {{ old('gen') !== null ? old('gen')=='1'  ? 'selected': '' :  $personal->gen=='1'? 'selected': ''}}  value="1">чоловіча</option>
                    </select>
                    <label>Вид роботи</label>
                    <select id="typework" name="typework">
                        <option {{ old('typework') !== null ? old('typework')=="" ?  'selected': ''   : $anket->typework=='' ? 'selected': ''  }} value=""></option>
                        <option {{old('typework') !== null ? old('typework')=="0" ? 'selected': ''  : $anket->typework=='0' ? 'selected': '' }} value="0">основна</option>
                        <option {{old('typework') !== null ? old('typework')=="1" ? 'selected': ''  : $anket->typework=='1' ? 'selected': '' }} value="1">за сумнісництвом</option>
                        <option {{old('typework') !== null ? old('typework')=="2" ? 'selected': ''  : $anket->typework=='2' ? 'selected': '' }} value="2">за суміщенням</option>
                    </select>
                    <br/>
                    <br/>
                    <label>призвище</label><input  id="name" class="{{ $errors->has('name') ? 'error':''  }}" name="name" type="text" size="20" maxlength="15" value="{{ old('name') !==null ? old('name') : $personal->name }}">
                    <label>ім`я</label><input  id="sname"  class="{{ $errors->has('sname') ? 'error':''  }}"  name="sname"    type="text" size="20" maxlength="15" value="{{ old('sname') !==null ? old('sname') : $personal->sname }}">
                    <label>по батькові</label><input  id="mname" class="{{ $errors->has('mname') ? 'error':''  }}" name="mname" type="text" size="20" maxlength="15" value="{{ old('mname') !==null ? old('mname') : $personal->mname }}">
                    <label>дата народження</label><input  id="birthdate" name="birthdate" type="text" size="6" value="{{ old('birthdate') !==null ? old('birthdate') : $personal->birthdate }}" >
                    <label>громадянство</label><input  id="nat"  name="nat" type="text" size="10" value="{{ old('nat') !==null ? old('nat') : $personal->nat }}">
                    <br/>
                    <br/>
                    <label>Група інвалідності</label>
                    <select name="invcat" id="invcat">
                        <option {{ old('invcat') !== null ? old('invcat')=='0'   ? 'selected': '' :  $personal->invcat=='0' ? 'selected': ''}} value="0"></option>
                        <option {{ old('invcat') !== null ? old('invcat')=='1'   ? 'selected': '' :  $personal->invcat=='1' ? 'selected': ''}} value="1">I</option>
                        <option {{ old('invcat') !== null ? old('invcat')=='2'   ? 'selected': '' :  $personal->invcat=='2' ? 'selected': ''}} value="2">II</option>
                        <option {{ old('invcat') !== null ? old('invcat')=='3'   ? 'selected': '' :  $personal->invcat=='3' ? 'selected': ''}} value="3">III</option>
                    </select>
                    <span id="invtypeDisp" >
                        <label>Безстроково</label>
                        <input type="checkbox" value="1" id="invtype" name="invtype" {{ old('invtype') !== null ? old('invtype')=='1'   ? 'checked': '' :  $personal->invtype=='1' ? 'checked': ''}}>
                    </span>
                    <label>початок</label><input  id="invb" name="invb" type="text" size="6" value="{{ old('invb') !==null ? old('invb') : $personal->invb }}">

                    <label>кінець</label><input  id="inve" name="inve" type="text" size="6" value="{{ old('inve') !==null ? old('inve') : $personal->inve }}">            
                </td>

               <!--  photo -->
                <td width="100px;" style="border:1px dashed #000; vertical-align: middle; text-align: center;">

                    @if(strlen($personal->image)>10)

                    <div id="photoContainer">
                        <div class="thumbnail">
                            <img src="/public/src/images/personal/{{$personal->image}}thu.jpg">
                            <span>
                                <button id="photoButDelete" type="button" class="btn btn-danger">Видалити</button>
                            </span>
                        </div>
                    </div>
                    <div id="photoDropzone" style="display: none;"></div>
                    @else

                    <div id="photoContainer"></div>
                    <div id="photoDropzone"></div>
                    @endif
                </td>
            </tr>

        </table>







    </div>





<!-- Education  -->

    <div class="category">Освіта</div>
    <div class="cont"> 
        <table class="tb">
            <tr align="center">
                <td>тип</td> 
                <td>Назва освітнього закладу</td>
                <td>Диплом (свідоцтво), серія, номер</td>
                <td width="50">Рік закінчення</td>
                <td>Спеціальність (професія) за дипломом (свідоство)</td>
                <td>Класифікація за дипломом (свідоцтвом)</td>
                <td width="100">Форма навчання (денна, вечірня, заочна)</td>

            </tr>

            <?php 

            if(count($education)>0){

                $max = count($education)  + 2;

            }else {

                $max = 2;

            }


            for($i=0; $i<=$max; $i++){ 

                ?>

        <tr>
            <td>
                <select name="education[<?php echo $i ?>][edn]">
                    <option {{ old('education') !==null ? old('education')[$i]['edn']=='0' ? 'selected': '' : Arr::exists($education, $i) ? $education[$i]['edn']=='0' ? 'selected': '' : ''}} value='0'></option>
                    <option {{ old('education') !==null ? old('education')[$i]['edn']=='1' ? 'selected': '' : Arr::exists($education, $i) ? $education[$i]['edn']=='1' ? 'selected': '' : ''}} value='1'>базова загальна середня</option>
                    <option {{ old('education') !==null ? old('education')[$i]['edn']=='2' ? 'selected': '' : Arr::exists($education, $i) ? $education[$i]['edn']=='2' ? 'selected': '' : ''}} value='2'>повна загальна середня</option>
                    <option {{ old('education') !==null ? old('education')[$i]['edn']=='3' ? 'selected': '' : Arr::exists($education, $i) ? $education[$i]['edn']=='3' ? 'selected': '' : ''}} value='3'>професійно технічна</option>
                    <option {{ old('education') !==null ? old('education')[$i]['edn']=='4' ? 'selected': '' : Arr::exists($education, $i) ? $education[$i]['edn']=='4' ? 'selected': '' : ''}} value='4'>неповна вища</option>
                    <option {{ old('education') !==null ? old('education')[$i]['edn']=='5' ? 'selected': '' : Arr::exists($education, $i) ? $education[$i]['edn']=='5' ? 'selected': '' : ''}} value='5'>базова вища</option>
                    <option {{ old('education') !==null ? old('education')[$i]['edn']=='6' ? 'selected': '' : Arr::exists($education, $i) ? $education[$i]['edn']=='6' ? 'selected': '' : ''}} value='6'>повна вища</option>
                </select>
                
           </td>

<td><input name="education[<?php echo $i ?>][title]" maxlength="990" type="text" 
value="{{ old('education') !==null ? old('education')[$i]['title'] : Arr::exists($education, $i) ? $education[$i]['title'] : ''}}"> </td>


<td><input  name="education[<?php echo $i ?>][doc]"  maxlength="990" type="text" 
value="{{ old('education') !==null ? old('education')[$i]['doc'] : Arr::exists($education, $i) ?  $education[$i]['doc'] : ''}}">   </td>

<td width="50"><input  class="educationYear" name="education[<?php echo $i ?>][year]" maxlength="990" type="text" 
value="{{ old('education') !==null ? old('education')[$i]['year'] : Arr::exists($education, $i) ? $education[$i]['year'] : ''}}"></td>

<td><input  name="education[<?php echo $i ?>][spec]" maxlength="990" type="text" 
value="{{ old('education') !==null ? old('education')[$i]['spec'] : Arr::exists($education, $i) ? $education[$i]['spec'] : ''}}"></td>
<td><input  name="education[<?php echo $i ?>][qual]" maxlength="990" type="text" 
value="{{ old('education') !==null ? old('education')[$i]['qual'] : Arr::exists($education, $i) ? $education[$i]['qual'] : ''}}"></td>
<td width="100">
<input  name="education[<?php echo $i ?>][type]" maxlength="990" type="text" 
value="{{ old('education') !==null ? old('education')[$i]['type'] : Arr::exists($education, $i) ? $education[$i]['type'] : ''}}"></td>
                        <!-- <td class=\"butt_deltd\" style=\"border:0;\">x</td> -->
                    </tr>

                  

          <?php  } ?>

        </table>


    </div>






<!-- After Education  -->

    <div class="category">Післядипломна професійна підготовка</div>
    <div class="contGray"> 

        <table class="tb">
            <tr align="center">
                <td>навчання в</td> 
                <td>Назва освітнього, наукового закладу</td>
                <td>Диплом, номер, дата видачі</td>
                <td width="50">Рік закінчення</td>
                <td>Науковий ступінь, ученне звання</td>
            </tr>

            <?php 


            if(count($aeducation)>0){

                $max = count($aeducation)  + 2;

            }else {

                $max = 2;

            }


            for($i=0; $i<=$max; $i++){ 





                ?>


               


                    <tr>

                        <td>
                            <select  name="aeducation[<?php echo $i ?>][edn]">
<option {{ old('aeducation') !==null ? old('aeducation')[$i]['edn']=='0' ? 'selected' : '' : Arr::exists($aeducation, $i) ? $aeducation[$i]['edn'] =='' ? 'selected' : '' : '' }} value='0'></option>
<option {{ old('aeducation') !==null ? old('aeducation')[$i]['edn']=='1' ? 'selected' : '' : Arr::exists($aeducation, $i) ? $aeducation[$i]['edn'] =='1' ? 'selected' : '' : '' }} value='1'>аспірантурі</option>
<option {{ old('aeducation') !==null ? old('aeducation')[$i]['edn']=='2' ? 'selected' : '' : Arr::exists($aeducation, $i) ? $aeducation[$i]['edn'] =='2' ? 'selected' : '' : '' }} value='2'>адюнктурі</option>
<option {{ old('aeducation') !==null ? old('aeducation')[$i]['edn']=='3' ? 'selected' : '' : Arr::exists($aeducation, $i) ? $aeducation[$i]['edn'] =='3' ? 'selected' : '' : '' }} value='3'>докторантурі</option>
                            </select>
                            
                       </td>

<td><input  name="aeducation[<?php echo $i ?>][title]" maxlength="990" type="text"  
value="{{ old('aeducation') !==null ? old('aeducation')[$i]['title'] : Arr::exists($aeducation, $i) ? $aeducation[$i]['title'] : '' }}"></td>
<td><input  name="aeducation[<?php echo $i ?>][doc]"  maxlength="990" type="text"  
value="{{ old('aeducation') !==null ? old('aeducation')[$i]['doc'] : Arr::exists($aeducation, $i) ? $aeducation[$i]['doc'] : '' }}"></td>
<td width="50">
<input  class="aeducationYear" name="aeducation[<?php echo $i ?>][year]" maxlength="990" type="text"  
value="{{ old('aeducation') !==null ? old('aeducation')[$i]['year'] : Arr::exists($aeducation, $i) ? $aeducation[$i]['year'] : '' }}">
</td>
<td><input  name="aeducation[<?php echo $i ?>][spec]" maxlength="990" type="text"  
value="{{ old('aeducation') !==null ? old('aeducation')[$i]['spec'] : Arr::exists($aeducation, $i) ? $aeducation[$i]['spec'] : '' }}"></td>

                    </tr>

                  

          <?php  } ?>

        </table>
    </div>







<!-- Last work  -->

    <div class="category">Останнє місце роботи</div>
    <div class="cont"> 
        <table class="tb">
            <tr align="center">
                <td>місце роботи</td>
                <td>посада <br/>(професія)</td>
                <td width="50">вислуга <br/>років</td>
                <td width="80">початок</td>
                <td width="80">закінчення</td>
            </tr>

            <?php 


             if(count($exp)>0){

                $max = count($exp)  + 2;

            }else {

                $max = 2;

            }


            for($i=0; $i<=$max; $i++){ 


             ?>

                
<tr>

<td><input   name="exp[<?php echo $i ?>][lastwork]" maxlength="990" type="text" 

value="{{old('exp')!==null ? old('exp')[$i]['lastwork'] : Arr::exists($exp, $i) ? $exp[$i]['lastwork'] : "" }}" required=""></td>



<td><input   name="exp[<?php echo $i ?>][lastspec]"  maxlength="990" type="text" 
value="{{old('exp')!==null ? old('exp')[$i]['lastspec'] : Arr::exists($exp, $i) ? $exp[$i]['lastspec'] : "" }}" required=""></td>

<td>
<input type="checkbox"  name="exp[<?php echo $i ?>][specexp]" value="1" 

@if( old('exp') )

    @if( old('exp')[$i]['specexp'] == 1)

        checked

    @endif


@elseif( Arr::exists($exp, $i) )


    @if( $exp[$i]['specexp'] == 1 )


        checked

    @endif 

@endif




 >
</td>



<td width="50">
<input  class="expB" name="exp[<?php echo $i ?>][b]" maxlength="990" type="text" 
value="{{old('exp')!==null ? old('exp')[$i]['b'] :  Arr::exists($exp, $i) ? $exp[$i]['b'] : "" }}" required="">
</td>
<td width="50">
<input  class="expE" name="exp[<?php echo $i ?>][e]" maxlength="990" type="text" 
value="{{old('exp')!==null ? old('exp')[$i]['e'] : Arr::exists($exp, $i) ? $exp[$i]['e'] : "" }}" required="">
</td>

</tr>

                    

           <?php } ?>

        </table>
        <br/>

<!--  Стаж роботи, вислуга років    -->    

        <table class="tb" style="font-size:13px; width:auto;">
            <tr>
                    <td width="200">Стаж роботи, вислуга років</td>
                    <td width="100" align="center">Станом на</td>
                    <td width="40" align="center">Днів</td>
                    <td width="40" align="center">Місяців</td>
                    <td width="40" align="center">Років</td>
            </tr>




            <tr>
                    <td width="200">Стаж роботи </td>
                    <td rowspan="2" width="100" align="center">{{$anket->createdate}}</td>
                    <td width="40" align="center">{{ $dateExp ? $dateExp['days'] : ''}}</td>
                    <td width="40" align="center">{{ $dateExp ? $dateExp['months'] : ''}}</td>
                    <td width="40" align="center">{{ $dateExp ? $dateExp['years'] : ''}}</td>
            </tr>

            <tr>
                <td width="200">Вислуга років </td>
                    <td width="40" align="center">{{ $dateSpecExp ? $dateSpecExp['days'] : ''}}</td>
                    <td width="40" align="center">{{ $dateSpecExp ? $dateSpecExp['months'] : ''}}</td>
                    <td width="40" align="center">{{ $dateSpecExp ? $dateSpecExp['years'] : ''}}</td>
                </tr>                
            </tr>
            <tr>
                <td width="200">Загальний стаж роботи</td>
                    <td rowspan="2" width="100" align="center"><?php echo date('d-m-Y')?></td>
                    <td width="40" align="center">{{ $dateExpAll ? $dateExpAll['days'] : ''}}</td>
                    <td width="40" align="center">{{ $dateExpAll ? $dateExpAll['months'] : ''}}</td>
                    <td width="40" align="center">{{ $dateExpAll ? $dateExpAll['years'] : ''}}</td>
                </tr>

            <tr>
                <td width="200">Загальна вислуга років</td>
                    <td width="40" align="center">{{ $dateSpecExpAll ? $dateSpecExpAll['days'] : ''}}</td>
                    <td width="40" align="center">{{ $dateSpecExpAll ? $dateSpecExpAll['months'] : ''}}</td>
                    <td width="40" align="center">{{ $dateSpecExpAll ? $dateSpecExpAll['years'] : ''}}</td>
                </tr>                
            </tr>
        </table>


       <br/>

<!--  Перерахунок Стаж роботи, вислуга років    --> 

        <table class="tb" style="font-size:13px; width:auto;">
            <tr>
                    <td width="200">Стаж роботи, вислуга років</td>
                    <td width="100" align="center">Станом на</td>
                    <td width="40" align="center">Днів</td>
                    <td width="40" align="center">Місяців</td>
                    <td width="40" align="center">Років</td>
            </tr>




           <tr>
                <td width="200">Загальний стаж роботи</td>
                    <td rowspan="2" width="100" align="center"><input style="text-align: center;" type="text" name="xdateSpecExp" id="xdateSpecExp" value="00-00-0000"></td>
                    <td width="40" align="center" id="xd1"></td>
                    <td width="40" align="center" id="xm1"></td>
                    <td width="40" align="center" id="xy1"></td>
                </tr>

            <tr>
                <td width="200">Загальна вислуга років</td>
                    <td width="40" align="center" id="xd2"></td>
                    <td width="40" align="center" id="xm2"></td>
                    <td width="40" align="center" id="xy2"></td>
                </tr>                
            </tr>
        </table>
        <br>
        <button id="rse" type="button">Рахувати</button> <button id="rse_close" type="button">Стерти</button>



       <br/>
        <br/>
        <br/>
        <label>Дата та причина звільнення</label>
        <input  class="lastdissdate" name="lastdissdate" size="6" type="text" 
        value="{{old('lastdissdate') !==null ? old('lastdissdate') : $anket->lastdissdate}}">
        <input  name="lastdiss" maxlength="120" size="90" type="text"
        value="{{old('lastdiss') !==null ? old('lastdiss') : $anket->lastdiss}}">
        <br/>
        <br/>
        <label>Відомості про отримання пенсії</label>
        <input  name="pension" maxlength="120" size="90" type="text" 
        value="{{old('pension') !==null ? old('pension') : $anket->pension}}">
    </div>





<!-- Family  -->

    <div class="category">Родинний стан</div>
    <div class="contGray"> 
<label>заміжня/одружений</label><input type="checkbox"  name="family[0][status]" value="1" 

    @if(old('family'))
        @foreach (old('family')[0] as $key => $value) 
            @if($key=="status")

                checked

                @break;

            @endif
        @endforeach
    @elseif(Arr::exists($family, 0)) 

            @if($family[0]['status']=='1'))

            checked

            @endif

    @endif

>

        <br/>
         <br/>

        <table class="tb">
            <tr align="center">
                <td>Ступінь родинного звязк(склад сімї)</td>
                <td>ПІБ</td>
                <td width="50">Рік народження</td>

            </tr>

            <?php 


            if(count($family)>0){

                $max = count($family)  + 2;

            }else {

                $max = 2;

            }


            for($i=0; $i<=$max; $i++){ 


             ?>

               
<tr>

    <td><input  name="family[<?php echo $i ?>][person]" maxlength="990" type="text" 
    value="{{old('family') !==null ? old('family')[$i]['person'] : Arr::exists($family, $i) ? $family[$i]['person'] : '' }} "></td>

    <td><input  name="family[<?php echo $i ?>][name]"  maxlength="990" type="text" 
    value="{{old('family') !==null ? old('family')[$i]['name'] : Arr::exists($family, $i) ? $family[$i]['name'] : '' }} "></td>

    <td width="50"><input class="familyyear" name="family[<?php echo $i ?>][year]" maxlength="990" type="text" 
    value="{{old('family') !==null ? old('family')[$i]['year'] : Arr::exists($family, $i) ? $family[$i]['year'] : '' }} ">
    </td>

</tr>

                   

         <?php   } ?>

        </table>
        <br/>
        <label>Місце фактичного прож.</label><input  type="text" name="ad1" size="100" 
        value="{{old('ad1') !==null ? old('ad1') : $personal->ad1}}">
        <br/>
        <br/>
        <label>Місце прож. за держ.реєст.</label><input  type="text" name="ad2" size="100" 
        value="{{old('ad2') !==null ? old('ad2') : $personal->ad2}}">
        <br/>
        <br/>
        <label>Паспорт: серія</label><input  type="text" size="1" name="pserial" 
        value="{{old('pserial') !==null ? old('pserial') : $personal->pserial}}">
        <label>№</label><input  type="text" size="4" name="pnum" 
        value="{{old('pnum') !==null ? old('pnum') : $personal->pnum}}">
        <label>Ким виданий</label><input  type="text" size="50" name="pwho" 
        value="{{old('pwho') !==null ? old('pwho') : $personal->pwho}}">
        <label>Дата видачі</label><input  type="text" size="6" name="pdate" class="pdate" 
        value="{{old('pdate') !==null ? old('pdate') : $personal->pdate}}">
        <br/>
        <br/>
        <label>ID паспорт: </label>
        <label>№</label><input  type="text" size="8" name="idpnum" value="{{old('pserial') !==null ? old('idpnum') : $personal->idpnum}}">
        <label>Ким виданий</label><input  type="text" size="4" maxlenght="4" name="idpwho" value="{{old('pserial') !==null ? old('idpwho') : $personal->idpwho}}">
        <label>Дата видачі</label><input  type="text" size="6" name="idpdate" class="pdate" value="{{old('pserial') !==null ? old('idpdate') : $personal->idpdate}}">
        <label>Дійсний до</label><input  type="text" size="6" name="idpexp" class="pdate"  value="{{old('pserial') !==null ? old('idpexp') : $personal->idpexp}}">
        <br/>
        <br/>
        <label>тел.1 </label><input  type="text" size="12"  name="tel1" value="{{old('pserial') !==null ? old('tel1') : $personal->tel1}}">
        <label>тел.2 </label><input  type="text" size="12"  name="tel2" value="{{old('pserial') !==null ? old('tel2') : $personal->tel2}}">
        <label>моб.  </label><input  type="text" size="10"  name="mob" id="mob" value="{{old('pserial') !==null ? old('mob') : $personal->mob}}">


    </div>

<!-- military records   -->
    <div class="category">Відомості про військовий облік</div>
    <div class="cont"> 

        <label>Група обліку</label><input  type="text" size="100" name="mgroup" 
        value="{{old('mgroup') !==null ? old('mgroup') : $personal->mgroup}}">
        <br/>
        <br/>
        <label>Категорія обліку</label><input  type="text" size="100" name="mcat" 
        value="{{old('mcat') !==null ? old('mcat') : $personal->mcat}}">
        <br/>
        <br/>
        <label>Склад</label><input  type="text" size="100" name="mcompos" 
        value="{{old('mcompos') !==null ? old('mcompos') : $personal->mcompos}}">
        <br/>
        <br/>
        <label>Військове звання</label><input  type="text" size="100" name="mrank" 
        value="{{old('mrank') !==null ? old('mrank') : $personal->mrank}}">
        <br/>
        <br/>
        <label>Війсоково-облікова спец. №</label><input  type="text" size="100" name="mspec" 
        value="{{old('mspec') !==null ? old('mspec') : $personal->mspec}}">
        <br/>
        <br/>
        <label>Придатність до військової служби</label><input  type="text" size="80" name="mexp" 
        value="{{old('mexp') !==null ? old('mexp') : $personal->mexp}}">
        <br/>
        <br/>
        <label>Назва райвійськомату за місцем реєстрації</label><input  type="text" size="80" name="rnm1" 
        value="{{old('rnm1') !==null ? old('rnm1') : $personal->rnm1}}">
        <br/>
        <br/>
        <label>Назва райвійськомату за місцем фактичного проживання</label><input  type="text" size="60" name="rnm2" 
        value="{{old('rnm2') !==null ? old('rnm2') : $personal->rnm2}}">
        <br/>
        <br/>
        <label>Перебування на спеціальному обліку</label><input  type="text" size="80" name="specacc" 
        value="{{old('specacc') !==null ? old('specacc') : $personal->specacc}}">

    </div>

<!-- Prof Educ  -->

    <div class="category">Професійна освіта на виробництві (за рахунок підприємства)</div>
    <div class="contGray"> 
        <table class="tb">
            <tr align="center">
                <td width="80">Дата</td>
                <td>Назва структурного підрозділу</td>
                <td width="80">Початок</td>
                <td width="80">Кінець</td>
                <td width="150">Вид навчання</td>
                <td width="150">Форма навчання</td>
                <td>Назва документу, що посвідчує професійну освіту, ким виданий</td>
            </tr>

            <?php

            if(count($peducation)>0){

                $max = count($peducation)  + 2;

            }else {

                $max = 2;

            }


            for($i=0; $i<=$max; $i++){ 


             ?>



           
            <tr>                                                                                                       
            <td width="80"><input class="peductdate" name="peduct[<?php echo $i ?>][date]"  maxlength="990" type="text" 
            value="{{old('peduct') !== null ? old('peduct')[$i]['date'] : Arr::exists($peducation, $i) ? $peducation[$i]['date'] : "" }}"></td>
            <td><input  name="peduct[<?php echo $i ?>][title]" maxlength="990" type="text" 
            value="{{old('peduct') !== null ? old('peduct')[$i]['title'] : Arr::exists($peducation, $i) ? $peducation[$i]['title'] : "" }}"></td>
            <td width="80"><input class="peductB" name="peduct[<?php echo $i ?>][b]" maxlength="990" type="text" 
            value="{{old('peduct') !== null ? old('peduct')[$i]['b'] : Arr::exists($peducation, $i) ? $peducation[$i]['b'] : "" }}"></td>
            <td width="80"><input class="peductE" name="peduct[<?php echo $i ?>][e]"     maxlength="990" type="text" 
            value="{{old('peduct') !== null ? old('peduct')[$i]['e'] :  Arr::exists($peducation, $i) ? $peducation[$i]['e'] : "" }}"></td>
            <td width="150"><input  name="peduct[<?php echo $i ?>][type]"  maxlength="990" type="text" 
            value="{{old('peduct') !== null ? old('peduct')[$i]['type'] : Arr::exists($peducation, $i) ? $peducation[$i]['type'] : "" }}"></td>
            <td width="150"><input  name="peduct[<?php echo $i ?>][form]"  maxlength="990" type="text" 
            value="{{old('peduct') !== null ? old('peduct')[$i]['form'] : Arr::exists($peducation, $i) ? $peducation[$i]['form'] : "" }}"></td>
            <td><input  name="peduct[<?php echo $i ?>][doc]"   maxlength="990" type="text" 
            value="{{old('peduct') !== null ? old('peduct')[$i]['doc'] : Arr::exists($peducation, $i) ? $peducation[$i]['doc'] : "" }}"></td>
            </tr>

               

         <?php   } ?>

        </table>
    </div>

<!-- apptran  -->

    <div class="category">Призначення і переведення</div>
    <div class="cont"> 
        <table class="tb">
            <tr align="center">
                <td width="80px">Дата</td>
                <td width="260">Назва структурного підрозділу</td>
                <td width="120">Старший, головний, тощо</td>
                <td>Назва професії, посади</td>
                <td>Код за КП*</td>
                <td>Розряд (склад)</td>
                <td width="50">вислуга <br/>років</td>
                <td>Підстава, наказ №</td>
            </tr>

            <?php 

            if(count($apptran)>0){

                $max = count($apptran)  + 2;

            }else {

                $max = 2;

            }


            for($i=0; $i<=$max; $i++){ 

             ?>

                
<tr>

<!-- Дата -->
    <td width="80px">
    <input  class="apptrandate" name="apptran[<?php echo $i ?>][date]" maxlength="990" type="text" 
    value="{{old('apptran')!==null ? old('apptran')[$i]['date'] : Arr::exists($apptran, $i) ? $apptran[$i]->date : "" }}">
    </td>


<!-- Назва структурного підрозділу -->
    <td width="260px">
        <select name="apptran[<?php echo $i ?>][kkpCatId]">
        <option value=""></option>";


        @foreach($kkpCats as $kkp)

        <option

            @if(old('apptran'))

                    @if(old('apptran')[$i]['kkpCatId'] == $kkp->id)

                        selected

                        value="{{$kkp->id}}"

                        > {{$kkp->title}}

                    @else


                        value="{{$kkp->id}}"

                        > {{$kkp->title}}


                    @endif
                    
            @elseif(Arr::exists($apptran, $i))

                @if($apptran[$i]->kkpCatId == $kkp->id)

                            selected

                            value="{{$kkp->id}}"

                            > {{$kkp->title}}


               @else 


                            value="{{$kkp->id}}"

                            > {{$kkp->title}}

                @endif



            @else 

                    value="{{$kkp->id}}"

                    > {{$kkp->title}}




           @endif

       </option>


        @endforeach;
        </select>
    </td>

<!-- Старший, головний, тощо -->
    <td width="150px"><input  name="apptran[<?php echo $i ?>][subprof]" maxlength="20" type="text" 
     value="{{old('apptran')!==null ? old('apptran')[$i]['subprof'] : Arr::exists($apptran, $i) ? $apptran[$i]->subprof : '' }}"></td>

<!-- Назва професії, посади -->
<td align="center" style="position:relative;">

    
   <!--add -->
    <div onclick="setIdSelectorProf('<?php echo $i ?>')" class="addprof" id="addprof<?php echo $i ?>"





        {{ old('apptran')[$i]['prof']==null  ?  

                Arr::exists($apptran, $i) ? 

                    $apptran[$i]->prof==null ? 

                        '' 

                    : 'style=display:none;' 

                : '' 

            : 'style=display:none;' }}


    >+</div>

     <!-- del addon -->


     <div onclick="setIdSelectorProf('<?php echo $i ?>')" class="delprof" id="delprof<?php echo $i ?>" 


         {{ old('apptran')[$i]['prof']==null  ?  
                Arr::exists($apptran, $i) ? 
                    $apptran[$i]->prof==null ? 

                        'style=display:none;' 
                    : '' 
                : 'style=display:none;' 
            : '' }}

 
     >x</div>


     <input id="prof<?php echo $i ?>" name="apptran[<?php echo $i ?>][prof]" maxlength="990" type="text"  
     value="{{ old('apptran')!==null ? old('apptran')[$i]['prof'] : Arr::exists($apptran, $i) ? $apptran[$i]->prof : '' }}" 


        {{ old('apptran')[$i]['prof']==null  ?   Arr::exists($apptran, $i) ? $apptran[$i]->prof==null ? 'style=display:none;' : '' : 'style=display:none;' : '' }}


      readonly>



     <input id="kkpid<?php echo $i ?>" name="apptran[<?php echo $i ?>][kkpid]"  type="hidden" 
     value="{{old('apptran')!==null ? old('apptran')[$i]['kkpid'] : Arr::exists($apptran, $i) ? $apptran[$i]->kkpid : "" }}">



</td>
<!-- Код за КП* -->
<td width="60px"><input id="code<?php echo $i ?>" name="apptran[<?php echo $i ?>][code]" maxlength="990" type="text"  

 value="{{old('apptran')!==null ? old('apptran')[$i]['code'] : Arr::exists($apptran, $i) ? $apptran[$i]->kod_kp : "" }}" readonly>

  </td>

<!-- Розряд (склад) -->
<td width="150px"><input  name="apptran[<?php echo $i ?>][rank]" maxlength="990" type="text"  
value="{{old('apptran')!==null ? old('apptran')[$i]['rank'] : Arr::exists($apptran, $i) ? $apptran[$i]->rank : "" }}"></td>

<!-- Вислуга років -->
<td>
<input type="checkbox"  name="apptran[<?php echo $i ?>][specexp]" value="1" 

@if( old('apptran') )

    @if( old('apptran')[$i]->specexp == 1)

        checked

    @endif


@elseif( Arr::exists($apptran, $i) )


    @if( $apptran[$i]->specexp == 1 )


        checked

    @endif 

@endif




 >
</td>



<!-- Підстава, наказ № -->
<td width="150px"><input  name="apptran[<?php echo $i ?>][doc]" maxlength="990" type="text"  
value="{{old('apptran')!==null ? old('apptran')[$i]['doc'] : Arr::exists($apptran, $i) ? $apptran[$i]->doc : "" }}"></td>

</tr>

                    
         <?php   } ?>

        </table>
    </div>

<!-- rest  -->

    <div class="category">Відпустки</div>
    <div class="contGray"> 
        <table class="tb">
            <tr align="center">
                <td>Вид відпустки</td>
                <td colspan="2">За який період</td>
                <td width="80">Початок відпустки</td>
                <td width="80">Закінчення відпустки</td>
                <td>Підстава, наказ №</td>
            </tr>

            <?php 

            if(count($rest)>0){

                $max = count($rest)  + 2;

            }else {

                $max = 2;

            }


            for($i=0; $i<=$max; $i++){ 

             ?>

             
<tr>
<td><input  name="rest[<?php echo $i ?>][type]" maxlength="990" type="text" 
value="{{ old('rest')!==null ? old('rest')[$i]['type'] : Arr::exists($rest, $i) ? $rest[$i]['type'] : "" }}">
</td>
<td width="80"><input  class="pb" name="rest[<?php echo $i ?>][pb]" maxlength="990" type="text" value="{{old('rest')!==null ? old('rest')[$i]['pb'] : Arr::exists($rest, $i) ? $rest[$i]['pb'] : "" }}"></td>
<td width="80"><input  class="pe" name="rest[<?php echo $i ?>][pe]" maxlength="990" type="text" value="{{old('rest')!==null ? old('rest')[$i]['pe'] : Arr::exists($rest, $i) ? $rest[$i]['pe'] : "" }}"></td>
<td width="80"><input  class="rb" name="rest[<?php echo $i ?>][rb]" maxlength="990" type="text" value="{{old('rest')!==null ? old('rest')[$i]['rb'] : Arr::exists($rest, $i) ? $rest[$i]['rb'] : "" }}"></td>
<td width="80"><input  class="re" name="rest[<?php echo $i ?>][re]" maxlength="990" type="text" value="{{old('rest')!==null ? old('rest')[$i]['re'] : Arr::exists($rest, $i) ? $rest[$i]['re'] : "" }}"></td>
<td><input  name="rest[<?php echo $i ?>][doc]" maxlength="990" type="text" value="{{old('rest')!==null ? old('rest')[$i]['doc'] : Arr::exists($rest, $i) ? $rest[$i]['doc'] : "" }}"></td>
</tr>

                 

         <?php   } ?>

        </table>
    </div>



 <label>Додаткові відомості</label><input  name="add" type="text" size="120" value="{{old('add')!==null ? old('add') : $anket->add }}">
<br>
<br>
<br>
<br>

    <div class="category">Документи (скан, фото)
        
             <button id="photoGalleryButUpload" type="button" class="btn btn-outline-success  float-right">Додати</button>
       
        
    </div>
    <div class="contGray">

        <div id="photoGalleryDropzone"></div>
        <br>
        <button id="photoGalleryButDeleteList" type="button" class="btn btn-outline-danger float-right">Видалити</button>
        <br>
        <div id="photoGalleryContainer">
           <?php
                if(strlen($personal->images)>10) {

                    $arr = json_decode($personal->images, true); 

                    if(is_array($arr)){

                        foreach ($arr as $img => $desc) {

                         echo    '<div class="thumbnail">
                                    <img src="/public/src/images/personalGallery/'.$img.'thu.jpg">
                                    <div>
                                        <textarea id="'.$img.'" class="photoGalleryImageDesc" style="resize: none;" rows="7" cols="46" maxlength="320">'.$desc.'</textarea>
                                    </div>
                                </div>';
                            # code...
                        }
                    }
                } 
           ?>


        </div>

    </div> 





@if($anket->public == '0')
  <label>Причина звільнення</label><input  name="diss" type="text" size="120" value="{{old('diss')!==null ? old('add') : $anket->diss }}">
<br>
<br>
  <label>Дата звільнення</label><input  name="dissdate" type="text" size="6" value="{{old('dissdate')!==null ? old('add') : $anket->dissdate }}">
@endif
 <input type="hidden" value="{{$personal->id}}" name ="personalid">
 {{ csrf_field() }}
</form>


<br>
<br>
<br>

@if($anket->public == '0')
<label>Створити нову анкету з цієї </label><button id="btn_show_archive_box" type="button">Створити</button>
@else
<label>Закриття анкети </label><button id="btn_show_archive_box" type="button">Звільнити та пенеести до архіву</button>
@endif


<br>
<br>

</div>



 <!-- search apptran form -->
    <div id="search_apptran_box">

        <div id="search_apptran_container">

            <!-- close form -->
            <div style="text-align: right;">
                <div class="close_apptran_search_form" style="display: inline-block;">X</div>
            </div>

            <h2>Пошук професії, посади</h2>

            <div>
            <label>За назвою</label>
             <br/>
            <input type="text"  size="60" name="search_by_prof" id="search_by_prof" value="">
            <br/>
            <br/>
            <label>За кодом</label>
            <br/>
            <input type="text" size="15" name="search_by_code" id="search_by_code" value="">

            </div>


            <br/>
            <br/>


            <table width="98%">
            <tr>
                <td>#</td>
                <td>Професія, посада</td>
                <td width="50px" align="center">код <br/>кп</td>
                <td width="50px" align="center">код <br/>зкппт</td>
                <td width="50px" align="center">випуск <br/>еткд</td>
                <td width="50px" align="center">випуск <br/>дкхп</td>
            </tr>
            </table>       


            <div class="search_apptran_results"></div>

            <button class="applySelectProf" >застосувати </button>
            

        </div>
    </div>






<!-- arhive box -->

<div id="archive_box">  

    <div id="archive_container">

    <div align="right"><div id="close_archive_box">X</div></div>
    <!-- add to archive -->  

    @if($anket->public =='0')
    <b>УВАГА!<br/>
    Буде створено нову анкету з цієї <br/><br/>
    {{$personal->name}} {{$personal->sname}} {{$personal->mname}}</b>
    <br/><br/>
    <button id="btn_from_archive">Так, створити нову анктету з цієї</button>
    <br/>

    @else

    <div id="add_to_archive">
    <b>УВАГА!<br/>
    Звільнення та перенесення до архіву співробтника: <br/>
    {{$personal->name}} {{$personal->sname}} {{$personal->mname}}</b><br/>
    <br/>

        <label>Причина звільнення</label>
        <br/>
        <input  id="diss" name="diss" type="text" size="60" value="">
        <br/>
        <br/>
        <label>Дата звільнення</label>
        <br/>
        <input  id="dissdate" name="dissdate" type="text" size="6" value="<?php echo date('d-m-Y') ?>">
        <br/>
        <br/>
        <br/>
        <br/>
        <button id="btn_add_to_archive">Перенести до архіву</button>
    </div>

    @endif


    <!-- end add to archive -->


    <!-- repair from archive -->


    <!-- end repair from archive -->

    </div>












    
</div>



<!-- end add to arhive box -->


@endsection