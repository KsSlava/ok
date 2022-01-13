<?php

namespace App\Http\Controllers;

use App\Models\Personal as Prs;
use App\Models\Anket;
use App\Models\Education;
use App\Models\AfterEducation;
use App\Models\ProfEducation;
use App\Models\Exp;
use App\Models\Family;
use App\Models\AppTran;
use App\Models\Rest;
use App\Models\KkpCats;
use App\Models\KkpList;


use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use App\Lib\UploadImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Lib\Helper;
use Illuminate\Support\Facades\Validator;




class personal extends Controller
{


	//array_msort($arr1, array('name'=>SORT_DESC, 'cat'=>SORT_ASC));
	public function array_msort($array, $cols){

		$colarr = array();
		foreach ($cols as $col => $order) {
		    $colarr[$col] = array();
		    foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
		}
		$eval = 'array_multisort(';
		foreach ($cols as $col => $order) {
		    $eval .= '$colarr[\''.$col.'\'],'.$order.',';
		}
		$eval = substr($eval,0,-1).');';
		eval($eval);
		$ret = array();
		foreach ($colarr as $col => $arr) {
		    foreach ($arr as $k => $v) {
		        $k = substr($k,1);
		        if (!isset($ret[$k])) $ret[$k] = $array[$k];
		        $ret[$k][$col] = $array[$k][$col];
		    }
		}
		return $ret;

	}



	public function array_orderby()
	{
	    $args = func_get_args();
	    $data = array_shift($args);
	    foreach ($args as $n => $field) {
	        if (is_string($field)) {
	            $tmp = array();
	            foreach ($data as $key => $row)
	                $tmp[$key] = $row[$field];
	            $args[$n] = $tmp;
	            }
	    }
	    $args[] = &$data;
	    call_user_func_array('array_multisort', $args);
	    return array_pop($args);
	}




	public function admin(Request $request){

		

		$prs = DB::table('personal as p ')
		->leftJoin('anket as a', 'p.id', '=', 'a.personalid')
		->where('a.public', '=', '1')
		->select('p.name', 'p.sname', 'p.mname', 'p.birthdate', 'p.tel1', 'p.tel2', 'p.mob', 'a.tid')
		->orderByRaw("p.name COLLATE utf8_unicode_ci ASC")
		->get();

		//personal + last apptran

  		foreach ($prs as $k => $p) {

		  	$gla = Helper::getLastApptran($p->tid);

			if($gla){

			  	foreach ( $gla as $key => $value) {

		     		$prs[$k]->$key = $value ;
		 		
			  	}

			}


			//add name
			$prs[$k]->fullname = $prs[$k]->name.' '.$prs[$k]->sname. ' '.$prs[$k]->mname;



		}



			$prs = json_decode( json_encode($prs), true );

			$prs_az = $prs;

			$n = [];

			$noprs = []; 

			//sort by kkpCatOrd, then a-z

			foreach ($prs as $k => $v) {


				if(array_key_exists('kkpCatOrd', $v)){

					$n[$v['kkpCatOrd']][] = $v;

				}else{

					$noprs[] =  $v;

				}

 				ksort($n);


			}



			$n2 = []; 
			foreach ($n as $k => $v) {


					$n2[ $v[0]['kkpCatTitle'] ] = $this->array_orderby($n[$k], 'fullname', SORT_ASC);
				
			}



			$prs = $n2;


			if($request->route('print')=="print"){

				Helper::print_admin($prs);

			}elseif($request->route('print')=="print_az"){

				Helper::print_admin_az($prs_az);

			}else{

				return view('auth.layouts.admin', ['prs'=>$prs, 'noprs'=>$noprs ]);

			}
	}


	public function insertPrimaryData($request){

				$cols = ['pid', 'name', 'sname', 'mname', 'birthdate', 'nat', 'gen',  'ad1', 'ad2', 'pserial', 'pnum', 'pwho', 'pdate', 'idpnum', 'idpwho', 'idpdate', 'idpexp', 'tel1', 'tel2', 'mob', 'mgroup', 'mcat', 'mcompos', 'mspec', 'mrank', 'mexp', 'rnm1', 'rnm2', 'specacc']; 


    		
		        //personal
				$insert = ['id'=>NULL];
	    		foreach ($request->all() as $key => $value) {

	    			if(in_array($key, $cols)){

	    				$insert[$key] = $value;

	    			}
	    			
	    		}

				$personalid = Prs::insertGetId($insert);

				
    			//anket	
				$insert = ['id'=>NULL, 'personalid'=>$personalid];

				

				$cols = ['tid', 'typework', 'lastdiss',	'lastdissdate', 'diss', 'pension', 'add', 'createdate']; 
	    		foreach ($request->all() as $key => $value) {

	    			if(in_array($key, $cols)){

	    				$insert[$key] = $value;

	    			}
	    			
	    		}

				$anketid = Anket::insertGetId($insert);
	}

	public function insertSecondaryData($request){


				//education 			
				$cols = ['title', 'doc', 'spec', 'qual', 'type', 'edn',	'year']; 

				if($request->education){

					foreach ($request->education as $educ) {
			
						$insert = [];

						$ins = 0; 
			
						foreach ($educ as $key => $value) {

							if(in_array($key, $cols)){

			    				$insert[$key] = trim($value);
			    			}


			    			if(mb_strlen(trim($value))>0 and trim($value)!== '0' and $ins == 0 ){

								$insert['id'] = NULL; 
								$insert['tid'] =$request->tid;							

								$ins = 1;

			    			}
							
						}

						if($ins==1){
			
							Education::insert($insert);

						}

						
						
					}

				
				}



				//after education 			
				$cols = ['title', 'doc', 'spec', 'qual', 'type', 'edn',	'year']; 

				if($request->aeducation){

					foreach ($request->aeducation as $educ) {


						$insert = [];

						$ins = 0; 
			
						foreach ($educ as $key => $value) {

							if(in_array($key, $cols)){

			    				$insert[$key] = trim($value);
			    			}


			    			if(mb_strlen(trim($value))>0 and trim($value)!== '0' and $ins == 0 ){

								$insert['id'] = NULL; 
								$insert['tid'] =$request->tid;							

								$ins = 1;

			    			}
							
						}

						if($ins==1){

							AfterEducation::insert($insert);

						}

						
						
					}

				}



				//exp 			
				$cols = ['b', 'e', 'lastwork', 'lastspec', 'specexp']; 

				if($request->exp){				
					foreach ($request->exp as $exp) {

						$insert = ['id'=>NULL, 'tid'=>$request->tid];
			
						foreach ($exp as $key => $value) {

							if(in_array($key, $cols)){

			    				$insert[$key] = $value;
			    			}
							
						}


						Exp::insert($insert);
						
					}
				}



				//family 			
				$cols = ['person',	'name',	'year' ]; 

				if(array_key_exists('status', $request->family[0])){

					$status = $request->family[0]['status']; 

				}else{ $status = 0;}



				foreach ($request->family as $f) {

						$insert = [];
						
						$ins = 0; 
			
						foreach ($f as $key => $value) {

							if(in_array($key, $cols)){

			    				$insert[$key] = trim($value);
			    			}


			    			if(mb_strlen(trim($value))>0 and $ins == 0 ){

								$insert['id'] = NULL;
								$insert['status'] = $status;
								$insert['tid'] =$request->tid;							
								$ins = 1;

			    			}
							
						}



						if($ins == 1 ) {

							Family::insert($insert);

						}

					

					   
					
				}



				//prof education 			
				$cols = ['title', 'type', 'form', 'doc', 'date', 'b', 'e']; 

				if($request->peduct){

					foreach ($request->peduct as $educ) {
						$insert = ['id'=>NULL, 'tid'=>$request->tid];
			
						foreach ($educ as $key => $value) {

							if(in_array($key, $cols)){

			    				$insert[$key] = $value;
			    			}
							
						}

						ProfEducation::insert($insert);
						
					}
				}


				//apptran			
				$cols = ['date', 'kkpid', 'kkpCatId', 'subprof', 'rank', 'specexp', 'doc']; 

				if($request->apptran){

					foreach ($request->apptran as $apptran) {

						$insert = [];


						//check: if not empty values: date || subprof || rank || doc, then save 
						$ins = 0; 
			
						foreach ( $apptran as $key => $value) {

							if(in_array($key, $cols)){

			    				$insert[$key] = trim($value);
			    			}


			    			if(mb_strlen(trim($value))>1 and trim($value)!== '0' and $ins == 0 ){

								$insert['id'] = NULL; 

								$insert['tid'] = $request->tid;		

								$ins = 1;

			    			}
							
						}



						if($ins==1) {

							AppTran::insert($insert);

						}



						

						
						
					}
				}


				//rest	

				$cols = ['type', 'pb', 'pe', 'rb', 're', 'doc'];
				if($request->rest){
					foreach ($request->rest as $rest) {
						$insert = ['id'=>NULL, 'tid'=>$request->tid];
			
						foreach ( $rest as $key => $value) {

							if(in_array($key, $cols)){

			    				$insert[$key] = $value;
			    			}
							
						}

						Rest::insert($insert);
						
					}
				}
	}


	public function deleteSecondaryData($request){

		$tid = $request->route('tid');

		//Education
		Education::where('tid', '=', $tid)->delete();

		//After Education
		AfterEducation::where('tid', '=', $tid)->delete();

		//Exp 
		Exp::where('tid', '=', $tid)->delete();

		//Family 
		Family::where('tid', '=', $tid)->delete();

		//Prof Education
		ProfEducation::where('tid', '=', $tid)->delete();

		//AppTran
		AppTran::where('tid', '=', $tid)->delete();

		//Rest  
		Rest::where('tid', '=', $tid)->delete();
	}


    public function new(Request $request){


    	if ($request->isMethod('get')) {

			//generate new tid
    		$t= DB::table('anket')->select('tid')->orderby('tid', 'desc')->first();
    		
    		if($t){
    			 $tid = $t->tid;
    			 $tid = $tid + 1;
    		}else{
				 $tid = 1000000000; 
    		}

    		return view('auth.layouts.new', ['tid'=>$tid, 'kkpCats'=>Helper::kkpCats()]);
 
		}


		//try save
    	if ($request->isMethod('post')) {


                $request->pid;


		        $this->validate($request, [

					'name'        =>'required|min:3|max:30',
					'sname'       =>'required|min:3|max:30',
				    'mname'       =>'required|min:3|max:30',
					'pid'         =>'required|regex:/[0-9]{10}/|unique:personal,pid',
					'tid'		  =>'required|regex:/[0-9]{10}/|unique:anket,tid'
		            
		        ]);
    		

				$this->insertPrimaryData($request);

				//$this->insertSecondaryData($request);
    
    			return redirect()->route('edit',['tid'=>$request->tid]); 
    			
		}
	}



	public function edit(Request $request){

		$return = [];


		if ($request->isMethod('get')) {

			if ($request->route('tid')){

				$tid = $request->route('tid'); 

				

				//Anket 
				$anket = Anket::where('tid', '=', $tid)->first(); 

				if($anket){

					//Personal 
					$personal = Prs::where('id', '=', $anket->personalid)->first(); 

					//Education
					$education = Education::where('tid', '=', $tid)->get();

					//After Education
					$aeducation = AfterEducation::where('tid', '=', $tid)->get();

					//Exp 
					$exp = Exp::where('tid', '=', $tid)->orderby('id', 'ASC')->get();

					//Family 
					$family = Family::where('tid', '=', $tid)->get();

					//Prof Education
					$peducation = ProfEducation::where('tid', '=', $tid)->get();

					//AppTran
					//$apptran = AppTran::where('tid', '=', $tid)->get();


					$apptran = DB::table('apptran as a')
					 ->leftJoin('kkp_list as k', 'a.kkpid', '=', 'k.id')
					 ->select('a.*', 'k.*')
					 ->where('a.tid','=',$tid)
					 ->get();


					//get child anket
					$child = Anket::where('parent_tid', '=', $anket->tid)->first(); 

					 

					//Rest  
					$rest = Rest::where('tid', '=', $tid)->get();


					$return = [

						'anket'=> $anket, 
						'personal'=>$personal,

						'education'=>$education,
						'aeducation'=>$aeducation,
						'peducation'=>$peducation,
						'exp'=>$exp,
						'family'=>$family,
						'apptran'=>$apptran,
						'rest'=>$rest,
						'kkpCats'=>Helper::kkpCats(),

						'dateExp'=>Helper::getDateExp($exp),
						'dateSpecExp'=>Helper::getDateSpecExp($exp),
						'dateExpAll'=>Helper::getDateExpAll($apptran),
						'dateSpecExpAll'=>Helper::getDateSpecExpAll($exp, $apptran),
						'child' => $child


					];


					if($request->route('print')=="print"){

						Helper::print_anket($return);

					}else{

						

						return response()->view('auth.layouts.edit', $return)->withCookie(cookie('p', $personal->id));


					}

					


				}


			}


			

		}

		if ($request->isMethod('post')) {

		        $this->validate($request, [

					'name'        =>'required|min:3|max:30',
					'sname'       =>'required|min:3|max:30',
				    'mname'       =>'required|min:3|max:30',

		            
		        ]);



				$cols = ['name', 'sname', 'mname', 'birthdate', 'nat', 'gen',  'ad1', 'ad2', 'pserial', 'pnum', 'pwho', 'pdate', 'idpnum', 'idpwho', 'idpdate', 'idpexp', 'tel1', 'tel2', 'mob', 'mgroup', 'mcat', 'mcompos', 'mspec', 'mrank', 'mexp', 'rnm1', 'rnm2', 'specacc', 'invcat', 'invtype', 'invb', 'inve']; 

    		
		        //update personal
				$update = [];



	    		foreach ($request->all() as $key => $value) {

	    			if(in_array($key, $cols)){

	    				$update[$key] = $value;

	    			}	

	    		}


	    		if(!$request->has('invtype')){

	    			$update['invtype']="0";

	    		}else{

	    		    $update['invb'] = "";

	    			$update['inve'] = "";
	    		}





				Prs::where('id', '=', $request->personalid)->update($update);

				
    			//update anket	
				$update = [];

				$cols = ['typework', 'lastdiss',	'lastdissdate', 'diss', 'pension', 'add', 'createdate']; 
	    		foreach ($request->all() as $key => $value) {

	    			if(in_array($key, $cols)){

	    				$update[$key] = $value;

	    			}
	    			
	    		}

				Anket::where('tid', '=', $request->route('tid'))->update($update);


				$this->deleteSecondaryData($request); 


				$this->insertSecondaryData($request);
		        
		        

				return redirect()->route('edit',['tid'=>$request->route('tid')]); 




		}
	}



	public function searchkpp(Request $request){


		if($request->type == "prof"){


			$val = $request->val;

			//filter
			$val = preg_replace('/ {2,}/', ' ', $val);
			$val = preg_replace('/[^а-яА-Яа-яА-Яa-zA-Z\s]/u','', $val);
			$val = trim($val);

			//split str
			$split  = explode(" ", $val);

			//implode to %like%
			$search_str = '%'.implode("%", $split).'%';


			
			//search
			$kkpList = KkpList::where('prof', 'like', $search_str)->orderby('prof', 'asc')->take(1000)->get();


		}

			

		if($request->type == "code"){


			$val = $request->val;

			//filter
			$val = preg_replace('/ {1,}/', ' ', $val);
			$val = preg_replace('/[^0-9.]/u','', $val);
			$val = trim($val);

			
			//implode to like%
			$search_str = $val.'%';
			
			//search
			$kkpList = KkpList::where('kod_kp', 'like', $search_str)->orderby('prof', 'asc')->take(1000)->get();


		}





			$searshResults = [];


			foreach ($kkpList as $kkp) {
			
				$searshResults[] = json_decode($kkp, 1);
			}



			echo json_encode($searshResults);
	}


	public function xspecexp(Request $request){


		$out = [];


		if(preg_match('/[0-9]{2}-[0-9]{2}-[0-9]{4}/',$request->xdate) and strlen($request->xtid)==10){

			//Exp 
			$exp = Exp::where('tid', '=', $request->xtid)->orderby('id', 'ASC')->get();

	        //AppTran
			$apptran = AppTran::where('tid', '=', $request->xtid)->orderby('id', 'ASC')->get();


			$out['dateExpAll'] = Helper::getDateExpAll($apptran, $request->xdate); 


			$out['dateSpecExpAll'] = Helper::getDateSpecExpAll($exp, $apptran, $request->xdate);


			echo json_encode($out);

			
		}else{

			return 0; 

			
		}
	}


	public function archive(){

		

		$prs = DB::table('personal as p ')
		->leftJoin('anket as a', 'p.id', '=', 'a.personalid')
		->where('a.public', '=', '0')
		->select('p.name', 'p.sname', 'p.mname', 'p.birthdate', 'p.tel1', 'p.tel2', 'p.mob', 'a.tid')
		->get();

		//personal + last apptran

  		foreach ($prs as $k => $p) {

		  	$gla = Helper::getLastApptran($p->tid);

			if($gla){

			  	foreach ( $gla as $key => $value) {

		     		$prs[$k]->$key = $value ;
		 		
			  	}

			}


			//add name
			$prs[$k]->fullname = $prs[$k]->name.' '.$prs[$k]->sname. ' '.$prs[$k]->mname;



		}



			$prs = json_decode( json_encode($prs), true );

			$n = [];

			$noprs = []; 

			//sort by kkpCatOrd, then a-z

			foreach ($prs as $k => $v) {


				if(array_key_exists('kkpCatOrd', $v)){

					$n[$v['kkpCatOrd']][] = $v;

				}else{

					$noprs[] =  $v;

				}

 				ksort($n);


			}



			$n2 = []; 
			foreach ($n as $k => $v) {


					$n2[ $v[0]['kkpCatTitle'] ] = $this->array_orderby($n[$k], 'fullname', SORT_ASC);
				
			}



			$prs = $n2;

		
		return view('auth.layouts.archive', ['prs'=>$prs, 'noprs'=>$noprs ]);
	}



	//add anket to archive
	public function toArchive(Request $request){


		$error =[]; 

		
		$diss = $request['diss'];

		$dissdate = $request['dissdate'];

		$tid = $request['tid'];


		//check diss, dissdate, tid 

		//check format: 00-00-0000
		if(preg_match('/([0-9]{2})[-]([0-9]{2})[-]([0-9]{4})/i', $dissdate, $m)){

	        if($m[1]>31 and $m[2]>12 and $m[3]>date('Y') ){ $error[] = "dissdate";  }

		}else{ $error[] = "dissdate"; }

		//check diss srt
		if(mb_strlen($diss)<5){ $error[] = "diss"; }


		if(count($error)<1){

			$a = Anket::where('tid', '=', $tid)->first();

			if($a){

				$cd = $a['createdate'];


					

				if(strtotime($dissdate)>strtotime($cd) and strlen($cd)==10){

					
					Anket::where('tid', '=', $tid)->update(['public'=>'0', 'diss'=>$diss, 'dissdate'=>$dissdate]);

					return 1; 
				}
			}

		}else{


		    return $error; 

		}
	}

	//create new anket like as previous anket (copy anket)
	public function fromArchive(Request $request){


		$tid = $request['tid'];

		$anket = Anket::select('*')
		->where('tid', '=', $tid)->first();


		//generate new tid
		$t= DB::table('anket')->select('tid')->orderby('tid', 'desc')->first();
		
		if($t){
			 $newtid = $t->tid;
			 $newtid = $newtid + 1;
		}else{
			 $newtid = 1000000000; 
		}



		$insert = [

			'id'=>NULL,
			'personalid'=>$anket->personalid,
			'tid'=>$newtid,
			'typework'=>$anket->typework,
			'lastdiss'=>$anket->diss,
			'lastdissdate'=>$anket->dissdate,
			'diss'=>'',
			'dissdate'=>'',
			'pension'=>$anket->pension,
			'add'=>$anket->add,
			'public'=>'1',
			'createdate'=>date('d-m-Y')

		];


	    if(Anket::insertGetId($insert) >0) {


	    	//copy education

	    	$out = Education::select('title', 'doc', 'spec', 'qual', 'type', 'edn',	'year')
	    	->where('tid', '=', $tid)->orderBy('id', 'asc')->get();


	    	foreach ($out as $o) {

	    		$o = json_decode($o, true);

	    		$insert = ['id'=>NULL, 'tid'=>$newtid];

	    		foreach ($o as $k => $v) {

					$insert[$k]=$v;
	    			
	    		}

	    		Education::insert($insert);
	    		
	    	}



	    	//copy after education

	    	$out = AfterEducation::select('title', 'doc', 'year','spec', 'edn')
	    	->where('tid', '=', $tid)->orderBy('id', 'asc')->get();


	    	foreach ($out as $o) {

	    		$o = json_decode($o, true);

	    		$insert = ['id'=>NULL, 'tid'=>$newtid];

	    		foreach ($o as $k => $v) {

					$insert[$k]=$v;
	    			
	    		}

	    		AfterEducation::insert($insert);
	    		
	    	}



	    	//copy last work

	    	$out = Exp::select('b', 'e', 'lastwork', 'lastspec', 'specexp')
	    	->where('tid', '=', $tid)->orderBy('id', 'asc')->get();


	    	foreach ($out as $o) {

	    		$o = json_decode($o, true);

	    		$insert = ['id'=>NULL, 'tid'=>$newtid];

	    		foreach ($o as $k => $v) {

					$insert[$k]=$v;
	    			
	    		}

	    		Exp::insert($insert);
	    		
	    	}



	    	//copy family

	    	$out = Family::select('person', 'name', 'year', 'status')
	    	->where('tid', '=', $tid)->orderBy('id', 'asc')->get();


	    	foreach ($out as $o) {

	    		$o = json_decode($o, true);

	    		$insert = ['id'=>NULL, 'tid'=>$newtid];

	    		foreach ($o as $k => $v) {

					$insert[$k]=$v;
	    			
	    		}

	    		Family::insert($insert);
	    		
	    	}


	    	//copy prof education

	    	$out = ProfEducation::select('title', 'type', 'form', 'doc', 'date', 'b', 'e')
	    	->where('tid', '=', $tid)->orderBy('id', 'asc')->get();


	    	foreach ($out as $o) {

	    		$o = json_decode($o, true);

	    		$insert = ['id'=>NULL, 'tid'=>$newtid];

	    		foreach ($o as $k => $v) {

					$insert[$k]=$v;
	    			
	    		}

	    		ProfEducation::insert($insert);
	    		
	    	}



	    	//copy apptran

	    	$out = AppTran::select('date', 'kkpid', 'kkpCatId', 'subprof', 'rank', 'specexp', 'doc')
	    	->where('tid', '=', $tid)->orderBy('id', 'asc')->get();


	    	foreach ($out as $o) {

	    		$o = json_decode($o, true);

	    		$insert = ['id'=>NULL, 'tid'=>$newtid];

	    		foreach ($o as $k => $v) {

					$insert[$k]=$v;
	    			
	    		}


	    		AppTran::insert($insert);
	    		
	    	}


	    	//copy rest

	    	$out = Rest::select('type', 'pb', 'pe', 'rb', 're', 'doc')
	    	->where('tid', '=', $tid)->orderBy('id', 'asc')->get();


	    	foreach ($out as $o) {

	    		$o = json_decode($o, true);

	    		$insert = ['id'=>NULL, 'tid'=>$newtid];

	    		foreach ($o as $k => $v) {

					$insert[$k]=$v;
	    			
	    		}

	    		Rest::insert($insert);
	    		
	    	}




	    	return $newtid;

	    	



	    }
	}


	//return anket from archive
	public function returnArchive(Request $request){

		$tid = $request['tid'];

		Anket::where('tid', '=', $tid)->update(['public'=>'1', 'diss'=>'', 'dissdate'=>'']);

		return 1; 

	}


	public function hb(Request $request){


		$return = [];

	
			   
		if(preg_match('/1|2|3|4|5|6|7|8|9|10|11|12/', $request->route('sd'))){

			$sd = (int) $request->route('sd');

			$sd = (string) $sd;
		        
			if((int)$sd < 10){

				$sd = "0".$sd;
		        
     		}

		}else{

			$sd = date('m');
		}


		



		$selectedDate = [];


		for($i=1; $i<=31; $i++){


			if($i<10){

				$selectedDate[] = (string) "0".$i."-".$sd; 

			}else{

				$selectedDate[] = (string) $i."-".$sd; 

			}
	
		}



		
		$prs = DB::table('personal as p ')
		->leftJoin('anket as a', 'p.id', '=', 'a.personalid')
		->where('a.public', '=', '1')
		->select('p.name', 'p.sname', 'p.mname', 'p.birthdate', 'p.tel1', 'p.tel2', 'p.mob', 'a.tid')
		->orderby('p.birthdate', 'asc')
		->get();



  		foreach ($prs as $k => $p) {

				
			  	$gla = Helper::getLastApptran($p->tid);

				if($gla){

				  	foreach ( $gla as $key => $value) {

			     		$prs[$k]->$key = $value ;
			 		
				  	}

				}


				//add name
				$prs[$k]->fullname = $prs[$k]->name.' '.$prs[$k]->sname. ' '.$prs[$k]->mname;

			

		}



			$prs = json_decode( json_encode($prs), true );

			$reSortPrs = [];
			foreach ($prs as $k => $p) {

				$check = date('d-m', strtotime($p['birthdate']));

				if(in_array($check, $selectedDate) and array_key_exists('kkpCatOrd', $p)){

					foreach ($p as $key => $val) {


						$reSortPrs[$k][$key]=$val;
						
					}


				}


				

				
			}


			$return = [
				'prs' => $reSortPrs, 
				'b'=>"01"."-".$sd."-".date("Y"),
				'e'=>date('t', strtotime( date('Y')."-".$sd) ). "-".$sd."-".date('Y'),
				'slug'=>(int)$sd
			];




			if($request->route('print')=="print"){


				Helper::print_hb($return);



			}else{

				return view('auth.layouts.hb', $return);

			}
	}


	public function ann(Request $request){


		$return = [];

	
			   
		
		$prs = DB::table('personal as p ')
		->leftJoin('anket as a', 'p.id', '=', 'a.personalid')
		->where('a.public', '=', '1')
		->select('p.name', 'p.sname', 'p.mname', 'p.birthdate', 'p.tel1', 'p.tel2', 'p.mob', 'a.tid')
		->orderby('p.birthdate', 'asc')
		->get();



  		foreach ($prs as $k => $p) {
				
			  	$gla = Helper::getLastApptran($p->tid);

				if($gla){

				  	foreach ( $gla as $key => $value) {

			     		$prs[$k]->$key = $value ;
			 		
				  	}

				}

				//add name
				$prs[$k]->fullname = $prs[$k]->name.' '.$prs[$k]->sname. ' '.$prs[$k]->mname;
		}



			$prs = json_decode( json_encode($prs), true );


		
  			$nowYear = (int) date("Y");

			$reSortPrs = [];
			foreach ($prs as $k => $p) {

				
 			    $bd = (int) date("Y", strtotime($p['birthdate']));


 			    $x = $nowYear - $bd; 

 			    if(preg_match('/40|45|50|55|60|65|70|75|80|85|90|95|100|105|110/', $x) and array_key_exists("prof", $p)){

 			    	$reSortPrs[ (int) date('md', strtotime($p['birthdate'])) ] = $p;
 			    }

			
			}


	 		ksort($reSortPrs);


			$return = [
				'prs' => $reSortPrs, 
			];


			if($request->route('print')=="print"){

				Helper::print_ann($return);

			}else{

				return view('auth.layouts.ann', $return);

			}
	}


	public function upload(Request $request){


		//photo

        if($request->file('photo')){
        
            $UI       = new UploadImage();
            $disk     = 'personal';
            $f     = $request->file('photo');
            $n = md5(rand(1000, 50000).date('d-m-Y-G-i-s'));

            $image =  $UI->save(500, 0, 1000, 0, $disk, $f, $n);

            Prs::where('id', '=', $request->cookie('p'))->update([

            	'image'=>$n

            ]);
        	
            return response()->json($image);
        
        }


        if($request->input('delImage')){

        	$response = [];

        	$path = $request->input('delImage');

            $image =  Prs::where('id', '=', $request->cookie('p'))->value('image'); 

            if (strlen($image) > 10){
                  
                // delete thumb, full image 
                Storage::Delete( str_replace('thu', '', $path) ); 
                Storage::Delete( $path ); 

                Prs::where('id', '=', $request->cookie('p'))->update(['image'=>""]); 

                $response[] = $path; 

            }   


            return response($response);
          

        }  


        //gallery

        if($request->file('photoGallery')){

            $UI       = new UploadImage();
            $disk     = 'personalGallery';
            $f     = $request->file('photoGallery');
            $n = md5(rand(1000, 50000).date('d-m-Y-G-i-s'));

            $image =  $UI->save(500, 0, 1000, 0, $disk, $f, $n);


            $arr = json_decode(Prs::where('id', '=', $request->cookie('p'))->value('images'), true);

            if(is_array($arr)){

            	$arr[$n] ='';

            }else{

            	$arr = [];
            	$arr[$n] ='';

            }


            Prs::where('id', '=', $request->cookie('p'))->update([

                'images'=>json_encode($arr)

            ]);
        	
            return response()->json($image);
        
        }



       if($request->input('delImages')){


            $galleryImages =  Prs::where('id', '=', $request->cookie('p'))->value('images'); 

            if (strlen($galleryImages) > 5){

                $galleryImagesArr = json_decode($galleryImages, true); 

                foreach ($request->input('delImages') as $k => $v) {

             
                    //get imageID
                    preg_match("/[\/]{1}([A-Za-z0-9]+)[\.]jpg/", $v, $o);
                    $imageID = str_replace('thu', '', $o[1]);


                    //delete imageID from Arr
                    if (array_key_exists($imageID, $galleryImagesArr)){

                     
                        unset( $galleryImagesArr[$imageID] ); 
                   
                        // delete thumb, full image 
                        Storage::Delete( str_replace('thu', '', $v) ); 
                        Storage::Delete( $v ); 

                        $response[] = $v; 

                    }
                   
              
                }

                    //update db table images
                    if (!empty($galleryImagesArr)){

                        $images = json_encode( $galleryImagesArr ); 

                    }else{

                        $images = ''; 

                    }


                   Prs::where('id', '=', $request->cookie('p'))->update(array(

                        'images'        => $images,
                        

                    ));


            return response($response);

            }

        }


        //gallery descriptions 

       if($request->input('upImageDesc')){

			$desc = trim($request->input('upImageDesc'));
			$pID = $request->input('upImageID'); 


			$arr = json_decode(Prs::where('id', '=', $request->cookie('p'))->value('images'), true);


			if(is_array($arr)){


				if(array_key_exists($pID, $arr)){

					$arr[$pID] = $desc;

				}


			}




			Prs::where('id', '=', $request->cookie('p'))->update([

			'images'=>json_encode($arr)

			]);



       } 
	}




}
