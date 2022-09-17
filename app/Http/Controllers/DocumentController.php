<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Module;
use App\Document;
use Alert;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;

class DocumentController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($slug)
    {
        $title = trans("controllers.add_new").' '.session('moduleLabel')['document'];
        $course = Course::findBySlug($slug);
        if($course)
        {
            $breadcrumbs = [
                route('courses.index') => trans("controllers.courses"),
                route('courses.show', $course->slug) => $course->title,
                '' => $title,
            ];
            return view('document.form', compact('title', 'course', 'breadcrumbs'));
        }
        return redirect()->route('courses.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function moduleStore(Request $request)
    {

        $rules = [
            'title' => 'required',
            'order_no' => 'required|numeric'
        ];
        $request->validate($rules);

        $record = new Module;
        $record->title = $request->title;
        $record->type = "Document";
        $record->course_id = $request->course_id;
        $record->order_no = $request->order_no;
        $record->slug = null;
        if($record)
        {

            if($record->save())
            {
                Alert::success(__('messages.save_success'))->autoclose(3000);
                return redirect()->route('document.show', ['course'=>$record->course->slug, 'slug'=>$record->slug]);
            }
            Alert::error(__('messages.save_failed'))->autoclose(3000);
            return redirect()->back()->withInput();
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'title' => 'required',
            'upload_file' => 'required|file'
        ];
        ///$error = $request->validate($rules);
        $attributeNames = array(
           'upload_file' => 'file'
        );

        $validate = \Validator::make($request->all(), $rules);
        $validate->setAttributeNames($attributeNames);

        if ($validate->fails()){

          $errors = $validate->errors();
          return $errors->toJson();
        }
        else {

            $record = null ;
            if($request->course_id >0 )
              $record = Module::where('type', 'Document')->where('course_id', $request->course_id)->first();

           if($record)
              $record = $this->save($record, $request);

            if($record)
              {
                $msg = array('success' => __('messages.save_success'));
                return response()->json($msg);
              }

            $msg = 'error';

        }
          return $msg;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($course, $slug)
    {

        $data = Module::findBySlug($slug);
        if($data)
        {
            $title = trans("controllers.details_of").$data->type .' : '.$data->title;
            $breadcrumbs = [
                route('courses.index') => trans("controllers.list_of_courses"),
                route('courses.show', $data->course->slug) => $data->course->title,
                '' => $data->title,
            ];
            return view('document.details', compact('title', 'breadcrumbs', 'data'));
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($course, $slug)
    {

        $data = Module::findBySlug($slug);
        if($data)
        {
            $breadcrumbs = [
                route('courses.index') => trans("controllers.list_of_courses"),
                route('courses.show', $course) => $data->course->title,
                route('document.show', ['course'=>$course, 'slug'=>$slug]) => $data->title,
                '' => trans("controllers.edit"),
            ];
            $title = trans("modules.edit_document").' : '.$data->title;
            $course = $data->course;
            return view('document.form', compact('title', 'breadcrumbs', 'data', 'course'));
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required',
        ];
        $request->validate($rules);

        $record = Module::find(decrypt($id));
        $record->order_no = $request->order_no;
        if($record)
        {

            if($record->save())
            {
                Alert::success(__('messages.save_success'))->autoclose(3000);
                return redirect()->route('document.show', ['course'=>$record->course->slug, 'slug'=>$record->slug]);
            }
            Alert::error(__('messages.save_failed'))->autoclose(3000);
            return redirect()->back()->withInput();
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = Module::find(decrypt($id));
        if($record)
        {
            $slug = $record->course->slug;
            $record->deleted_by = \Auth::id();
            $record->save();
            if($record->delete())
            {
                $document = Document::where('course_id', $record->course_id)
                                     ->get();

                for($d=0; $d<count($document); $d++)
                {
                    $doc = Document::find($document[$d]->id);
                    $doc->deleted_by = \Auth::id();
                    $doc->save();
                    Storage::disk('local')->delete($doc->filepath);
                    $doc->delete();

                 }

              //  \App\Course::updateModuleInfo($record->course->id);

                Alert::success(__('messages.delete_success'))->autoclose(3000);
                return redirect()->route('courses.show', $slug);
            }
            Alert::error(__('messages.delete_failed'))->autoclose(3000);
            return redirect()->route('courses.index');
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->route('courses.index');
    }

    public static function save($record, $request)
    {
        if($record){

           if($request->id > 0)
              $detail = Document::where('id', $request->id)->first();
            else
              $detail = new Document;

            $detail->course_id = $record->course_id;
            $detail->module_id = $record->id;
            $detail->title = $request->title;
            $detail->description = $request->description;
            $detail->company_id = \Auth::user()->company_id;


            if($request->hasFile('upload_file'))
            {

                $directory = 'documents';
                if(!Storage::disk('local')->exists($directory))
                    Storage::disk('local')->makeDirectory($directory);

                if($detail->filepath)
                {
                    // Check if old file is exists and remove to replace
                    if(Storage::disk('local')->exists($detail->filepath))
                    {
                        Storage::disk('local')->delete($detail->filepath);
                        $detail->filepath = null;
                    }
                }
                $attachment = $request->file('upload_file');
                $filepath = $attachment->store($directory);
                if($filepath)
                {
                    $detail->filename = $attachment->getClientOriginalName();
                    $detail->filetype = $attachment->getMimeType();
                    $detail->filesize = $attachment->getClientSize();
                    $detail->filepath = $filepath;
                }
            }
            $detail->save();

            return $record;
        }
        return false;
    }

    public function download($id)
    {

        $doc = Document::find(decrypt($id));
        if($doc && Storage::disk('local')->exists($doc->filepath))
        {
            return Storage::download($doc->filepath, $doc->filename);
        }
        Alert::error(trans("modules.file_not_found"));
        return redirect()->back();
    }

    /**
     * Process datatables ajax request for course attachments.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function courseAttachmentData($course_id)
    {

        $company_id = \Auth::user()->company_id?:null ;
        $data = Document::select('id','title', 'filename','filetype','filepath','created_at', 'deleted_for')
                  ->where('course_id', $course_id);
        $data =   $data->where('company_id', $company_id)
                       ->orWhere('company_id', null);


        if($company_id > 0)
        {

          $document = $data->get();
          $deletedCompanyDocID = array();
          for($d=0;$d<count($document);$d++)
          {

            $deletedForArray = explode(",",$document[$d]->deleted_for);


            if(in_array($company_id, $deletedForArray))
            {

                $deletedCompanyDocID[] = $document[$d]->id;
            }
          }


          if(count($deletedCompanyDocID) > 0)
          {
            $data =   $data->whereNotIn('id', array_values($deletedCompanyDocID));
          }
        }




        return Datatables::of($data)
            ->addIndexColumn('index')
            ->editColumn('filetype', function ($data) {
                 $type = explode("/",$data->filetype) ;

                if(count($type)>0) $type = $type[1];
                return ucfirst($type);
            })
            ->addColumn('action', function ($data) {

              return '<a href="javascript:showAttachDetails('.$data->id.')" id="show_'.$data->id.'" class="btn btn-sm btn-info" data-url='.route('attachment.show',$data->id).' title="'.trans('modules.details').'">
                <i class="icon-options"></i>
              </a>'.'&nbsp;'.
              '<a href="javascript:deleteAttachFile('.$data->id.')" id="delete_'.$data->id.'" class="btn btn-sm btn-danger" data-url='.route('attachment.delete',$data->id).' title="'.trans('modules.delete').'">
                <i class="icon-trash"></i>
              </a>';
            //  show_button('remove', 'attachment.delete', $data->id, validate_role('courses.index'))
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function showAttachment(Request $request, $id)
    {
      $data = Document::select('id', 'title','description','course_id', 'filename','filetype','filepath','created_at')
                ->where('id', $id)->first();
       $data->filepath = route('document.download', encrypt($data->id));
       return response()->json($data);
    }

    public function deleteAttachment(Request $request, $id)
    {
      $data = Document::select('filepath')
                ->where('id', $id);
      $file = $data->first() ;

      if(is_null($file->company_id) && \Auth::user()->company_id)
      {
          $userCompany = \Auth::user()->company_id;
          $deletedForArray = array();
          $deletedFor = $file->deleted_for ;
          $deletedForArray = explode(",",$deletedFor);
         if(!in_array($userCompany, $deletedForArray))
         {
          $deletedForArray[] =  $userCompany;
          if(count($deletedForArray) > 1)
           $deletedForValue = implode(",", $deletedForArray);
          else {
            $deletedForValue  = $userCompany;
          }
          Document::where('id', $id)->update(['deleted_for'=> $deletedForValue]);
         }
         $response =  array("msg"=>"success") ;
      }
      else {


        if($data->delete())
        {
          Storage::disk('local')->delete($file->filepath);
         $response =  array("msg"=>"success") ;
        }
        else {
          $response =  array("msg"=>"error") ;
        }
      }
       return response()->json($response);
    }

}
