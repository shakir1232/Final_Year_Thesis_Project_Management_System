<?php

namespace App\Http\Controllers;

use App\Sample_data;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use App\Student;
use App\Feedback;
use App\User;
use App\Internal_Mark;
use DB;

class InternalmarkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
          public function __construct()
    {
        $this->middleware('auth');
    }
     
    public function index(Request $request)
    {
if(request()->ajax())
     {
      if($request->category)
      {
       $data = DB::table('students')
       ->where('sv_name',auth()->user()->name)
         ->where('semester', $request->category);
      }
      else
      {
       $data = DB::table('students')
         ->where('sv_name',auth()->user()->name);
      }
      return DataTables::of($data)
                    ->addColumn('action', function($data){
                        $button = '<button type="button" name="edit" id="'.$data->id.'" class="edit btn btn-primary btn-sm"> <i class="fa fa-tag"></i> Enter Mark</button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
     }
          
        $user= User:: all();
        return view('admin.internalmarks',compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            's_id' => 'required',
            's_name' => 'required',
            'semester' => 'required',
            'email' => 'required|email|string|max:255|unique:internal__marks',        
            'intrnl_mark' => 'required'
            
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

       $student = new Internal_Mark;

         $student->s_id = $request->input('s_id');
         $student->s_name = $request->input('s_name');
         $student->semester = $request->input('semester');
         $student->email = $request->input('email'); 
         $student->intrnl_mark  = $request->input('intrnl_mark');
         $student->spr_mark  = $request->input('spr_mark');
         $student->intrnl_initial  = $request->input('intrnl_initial');
         $student->intrnl_name = $request->input('intrnl_name');
         $student->user_id = auth()->user()->id;
         
        $student->save();

        return response()->json(['success' => 'Mark Added successfully.']);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sample_data  $sample_data
     * @return \Illuminate\Http\Response
     */
    public function show($sample_data)
    {
        if(request()->ajax())
        {
            $data = Student::findOrFail($sample_data);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sample_data  $sample_data
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = Student::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sample_data  $sample_data
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sample_data $sample_data)
    {

$rules = array(
            's_id' =>  'required',
            's_name'=>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            's_id'   =>  $request->s_id,
            's_name'   =>  $request->s_name,
            'batch'    =>  $request->batch,
            'semester' =>  $request->semester,
            'email'    =>  $request->email,
            'phone'    =>  $request->phone,
            'project'  =>  $request->project,
            'cgpa'     =>  $request->cgpa,
            'credit'     =>  $request->credit,
            'study'    =>  $request->study,
            'title'    =>  $request->title,
            'description' =>  $request->description

        );

        Student::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Student Info is successfully updated']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sample_data  $sample_data
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Student::findOrFail($id);
        $data->delete();
    }
}
