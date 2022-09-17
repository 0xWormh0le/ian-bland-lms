<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\CourseConfig;
use App\Certificate;
use App\Module;
use App\CourseCategory;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Alert, DB;
use Carbon\Carbon;


class CourseController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $course_durations = ["Mins", "Hours", "Days", "Weeks", "Months"];

    public function index()
    {
        $title = @session('menuLabel')['courses'] ?: session('menuLabel')['course-management'];
        $breadcrumbs = ['' => $title];

		    $enrolledCourseIds = array();

        $createPermission = 0 ;
        $page = 'course';

        if (\Auth::user()->company_id) {
            $enrolledCourseIds = \App\CourseCompany::where('company_id', \Auth::user()->company_id)->where('active', true)->pluck('course_id');
            $courses = Course::whereIn('id', $enrolledCourseIds)
                            ->orderByRaw('en_title IS NULL')
                            ->orderBy('en_title')
                            ->orderBy('title')
                            ->get();
        } else {
            $createPermission = 1 ;
            $superAdmin = \App\User::where('role_id', 0)->pluck('id');

            $courses = Course::whereIn('courses.created_by', $superAdmin)
                            ->orderByRaw('en_title IS NULL')
                            ->orderBy('en_title')
                            ->orderBy('title')
                            ->get();
          //  $courses = Course::all();
        }

        $parentCategory = CourseCategory::where("parent", 0)->get();
        
        return view('courses.index', compact(
          'courses',
          'title',
          'breadcrumbs',
          'enrolledCourseIds',
          'parentCategory',
          'createPermission',
          'page'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $url = '';
        $title = trans('controllers.add_new_course');
        $categories = CourseCategory::where("parent", 0)->get();
        $course_durations = $this->course_durations;
        return view('courses.form', compact('title', 'url', 'categories', 'course_durations'));
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
            'category_id' => 'required',
            'duration' => 'required|numeric',
            'cover_image' => 'nullable | image | max:5000',
        ];

        $request->validate($rules);

        $record = new Course;
        $record = $this->save($record, $request);
        if($record)
        {
            Alert::success(__('messages.save_success'))->autoclose(3000);
            return redirect()->route('courses.show', $record->slug);
        }
        Alert::error(__('messages.save_failed'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Course::findBySlug($id);

        if ($data) {
            $breadcrumbs = [
                route('courses.index') => trans('controllers.courses'),
                '' => $data->title,
            ];
            
            $title = trans('controllers.details_of_course');

            $moduleCount = Module::select('id')
                    ->where('type', 'Document')
                    ->where('course_id', $data->id)->count();

            $deadline = null;
            $courseCompany = null;
            $user = auth()->user();

            if (!$user->isSysAdmin()) {
              $courseCompany = \App\CourseCompany::where('company_id', $user->company_id)
                                                  ->where('course_id', $data->id)
                                                  ->where('active', 1)
                                                  ->first();

              $deadline = optional($courseCompany)->deadline;
            }

            $modules = $data->modules()->where('type', 'Elearning')->orderBy('order_no')->get();

            return view('courses.details', compact(
              'title',
              'breadcrumbs',
              'data',
              'deadline',
              'courseCompany',
              'moduleCount',
              'modules'
            ));
        }
        Alert::error(__('messages.invalid_request'));
        return redirect()->route('courses.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $data = Course::findBySlug($slug);
        $categories = CourseCategory::where("parent", 0)->get();
        $course_durations = $this->course_durations;

        if($data  && \Auth::user()->company_id)
        {
            $result = \App\CourseCompany::select('deadline', 'notification_reminder', 'completion_notification')
                                ->where('company_id', \Auth::user()->company_id)
                                ->where('active', true)
                                ->where('course_id', $data->id)
                                ->first();
           if($result && trim($result->deadline) !="")
           {
            $number = explode(" ",$result->deadline) ;
            $data->deadline =  $number[0];
            $data->deadline_part = $number[1];

           }
           if($result)
           {
             $data->notification_reminder = $result->notification_reminder ;
             $data->completion_notification = $result->completion_notification ;
           }


        }

        if($data)
        {
            $breadcrumbs = [
                route('courses.index') => trans('controllers.courses'),
                route('courses.show', $data->slug) => $data->title,
                '' => trans('controllers.edit'),
            ];
            $title = trans('controllers.edit').' '.$data->title;


            $duration_details = explode(" ",$data->duration) ;
            if(count($duration_details) > 1)
            {
              $data->duration_num = $duration_details[0];
              $data->duration_type = $duration_details[1];
            }


            $data->sub_categories = CourseCategory::where("parent", $data->category_id)->get();

            $certificate = $data->certificate;
            $config = $this->config($data);

            return view('courses.form',
              array_merge(
                compact(
                  'title',
                  'breadcrumbs',
                  'data',
                  'categories',
                  'course_durations',
                  'certificate'
                ), $config)
            );
        }

        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->route('courses.index');
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
        if(\Auth::user()->isSysAdmin())
        {
          $rules = [
              'title' => 'required',
              'category_id' => 'required',
              'duration' => 'required|numeric',
              'cover_image' => 'nullable | image | max:5000',
          ];
          $request->validate($rules);
        }
        elseif(\Auth::user()->company_id){
          $rules = [
              'deadline' => 'nullable|numeric|min:1',
              'duration' => 'required|numeric',
          ];
          $request->validate($rules);
        }

        $record = Course::find($id);
        if($record)
        {
            $record = $this->save($record, $request);

            if($record)
            {
                Alert::success(__('messages.save_success'))->autoclose(3000);
                return redirect()->route('courses.show', $record->slug);
            }
            Alert::error(__('messages.save_failed'))->autoclose(3000);
            return redirect()->back()->withInput();
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Store/Update a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save($record, $request)
    {
        if($request->hasFile('cover_image'))
        {
            if($record->image)
            {
                $oldfile = '/courses/images/'.$record->image;
                $exists = Storage::disk('public')->exists($oldfile);
                if($exists)
                    Storage::disk('public')->delete($oldfile);
            }
            $file = $request->file('cover_image');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = uuid().'.'.$extension;
            $path = Storage::disk('public')->path('courses/images/'. $filename);
            list($width, $height) = getimagesize($file);
            $ratio = $width / $height;
            if( $ratio > 1) {
                $resized_width = 700; //suppose 700 is max width or height
                $resized_height = 700/$ratio;
            }
            else {
                $resized_width = 700*$ratio;
                $resized_height = 700;
            }
            Image::make($file->getRealPath())->resize($resized_width, $resized_height)->save($path);
            $record->image = $filename;
        }

      //  if(\Auth::user()->isSysAdmin())
         {
            $fields = ['title', 'description', 'deadline_date', 'category_id', 'sub_category_id', 'en_title', 'language'];
            foreach ($fields as $f) {
              if ($request->has($f)) {
                $record->$f = $request->input($f);
              }
            }

            if ($request->has('duration') && $request->has('duration_type')) {
              $record->duration = $request->duration." ".$request->duration_type;
            }

            if ($request->has('title')) {
              $str = strtolower($request->title);
              $record->slug = preg_replace('/\s+/', '-', $str);
              $record->slug = $record->slug.'-'.time();
            }

            if($record->id)
            {
              //  $record->slug = null;
                $record->updated_by = $request->user()->id;
            }else{

                $record->created_by = $request->user()->id;
            }


            $record->save();
         }

         if($record->id >0 && \Auth::user()->company_id >0)
         {
            if(trim($request->deadline) > 0)
            {
             $deadline =  $request->deadline.' '.$request->deadline_duration;
            }
            else {
              $deadline = "";
            }

             $Companyrecord = \App\CourseCompany::where('company_id', \Auth::user()->company_id)
                                 ->where('course_id', $record->id);

              $recordCount =  $Companyrecord->first();
             if($recordCount && $recordCount->id > 0)
             {
               $Companyrecord->update(["deadline" => $deadline,
                         "notification_reminder" => $request->notification_reminder,
                         "completion_notification" => $request->completion_notification,
                         "updated_at" => Carbon::now()
                       ]);
             }
             else {
               \App\CourseCompany::insert(['company_id'=> \Auth::user()->company_id,
                         'course_id'=> $record->id,
                         "deadline" => $deadline,
                         "notification_reminder" => $request->notification_reminder,
                         "completion_notification" => $request->completion_notification,
                         "created_by" => \Auth::user()->id,
                         "created_at" => Carbon::now()
                       ]);
             }
         }

        return $record;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = Course::find(decrypt($id));
        if($record)
        {
            $record->deleted_by = \Auth::id();
            $record->save();
            if($record->delete())
            {
            //   \App\Certificate::where('course_id', $record->id)->delete();
               \App\CourseCompany::where('course_id', $record->id)->delete();
               \App\CourseConfig::where('course_id', $record->id)->delete();
               \App\CourseMember::where('course_id', $record->id)->delete();
               \App\CourseUser::where('course_id', $record->id)->delete();
               \App\Document::where('course_id', $record->id)->delete();
               \App\Elearning::where('course_id', $record->id)->delete();
               \App\CourseUser::where('course_id', $record->id)->delete();
               \App\ElearningUser::where('course_id', $record->id)->delete();
               \App\Module::where('course_id', $record->id)->delete();
               \App\CourseUser::where('course_id', $record->id)->delete();
            //   \App\MyCertificate::where('course_id', $record->id)->delete();

               $scormsData = \App\SCORM::where('course_id', $record->id)->get();

               for($sd=0; $sd < count($scormsData); $sd++)
               {
                 \App\SCORMReport::where('course', $scormsData[$sd]->id)->delete();
                 \App\SCORMSCOs::where('scormid', $scormsData[$sd]->id)->delete();
                 \App\SCORMSCOData::where('scormid', $scormsData[$sd]->id)->delete();
                 \App\SCORMTrack::where('scormid', $scormsData[$sd]->id)->delete();

                 if(file_exists($scormsData[$sd]->repository)) {

                   system("sudo rm -rf ".escapeshellarg($scormsData[$sd]->repository));

                 }

               }
               \App\SCORM::where('course_id', $record->id)->delete();
               \App\CourseUser::where('course_id', $record->id)->delete();

                Alert::success(__('messages.delete_success'))->autoclose(3000);
                return redirect()->route('courses.index');
            }
            Alert::error(__('messages.delete_failed'))->autoclose(3000);
            return redirect()->route('courses.index');
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->route('courses.index');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function modulesData($course_id)
    {
        $data = Module::select(
                    'order_no',
                    'title',
                    'type',
                    'created_at',
                    'id',
                    'course_id',
                    'slug'
                )
                ->where('course_id', $course_id)
                ->orderBy('order_no', 'asc');


        return Datatables::of($data)
            ->editColumn('title', function ($data) {

               if($data->type == 'Document')
                  return session('moduleLabel')['document'];
                return $data->title;
              })
            ->editColumn('type', function ($data) {
                if($data->type == 'Elearning')
                    return session('moduleLabel')['elearning'];
                elseif($data->type == 'Document')
                    return $data->type;

                return show_button('show', $route.'.show', ['course'=>$data->course->slug, 'slug'=>$data->slug], validate_role('courses.index'));
            })
            ->addColumn('action', function ($data) {
                if($data->type == 'Elearning')
                    $route = 'elearning';
                elseif($data->type == 'Document')
                    $route = 'document';

            //    if(\Auth::user()->isSysAdmin())
                  return show_button('show', $route.'.show', ['course'=>$data->course->slug, 'slug'=>$data->slug], validate_role('courses.index'));
            //    elseif($route != 'document') {
              //    return show_button('show', $route.'.show', ['course'=>$data->course->slug, 'slug'=>$data->slug], validate_role('courses.index'));

            //    }
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function config($course)
    {

          if (\Auth::user()->isSysAdmin()) {
            $record = CourseConfig::whereNull('company_id')->where('course_id', $course->id)->first();
          } else {
            $record = CourseConfig::where('company_id', \Auth::user()->company_id)->where('course_id', $course->id)->first();
          }


          if (!$record) {

            if(\Auth::user()->isSysAdmin()) {
              $record = CourseConfig::withTrashed()->whereNull('company_id')->where('course_id', $course->id)->first();
            } else {
              $record = CourseConfig::withTrashed()->where('company_id', \Auth::user()->company_id)->where('course_id', $course->id)->first();
            }


            if (!$record) {
              $record = new CourseConfig;
            }

            if (!\Auth::user()->isSysAdmin()) {
              $record->company_id = \Auth::user()->company_id;
            }

            $record->course_id = $course->id;
            $record->transversal_rule = 'none';
            $record->completion_rule = 'any';
            $record->completion_modules = '';
            $record->completion_percentage = 0;
            $record->learning_path = '';
            $record->get_certificate = 0;
            $record->deleted_at = null;
            $record->deleted_by = null;
            $record->save();
          }

          $courseConfig = $record;
          $companyConfig = null;

          if (\Auth::user()->company_id > 0) {
            $companyConfig = \App\CertificateConfig::where('company_id', \Auth::user()->company_id)->first();
          }

          // $defaultCertDesign = optional(\App\CertificateDesign::where('draft', 0)->first())->id;
          // if (is_null($defaultCertDesign)) {
            $defaultCertDesign = 1;
          // }

          return compact(
            'courseConfig',
            'companyConfig',
            'defaultCertDesign'
          );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function updateConfig(Request $request, $slug)
    {
      $rules = [];
      if(!$request->completion_rule && $request->completion_rule != "all")
      {
        $rules = [
            'completion_modules' => 'required_without:completion_percentage',
            'completion_percentage' => 'required_without:completion_modules'
        ];

      }
      if($request->get_certificate && $request->get_certificate !="")
      {
        $rules = [
            'certificate_name' => 'required'
        ];
      }
      if(count($rules) > 0)
      {
        $request->validate($rules);
      }


        $course = Course::findBySlug($slug);
        if($course)
        {

            $record = CourseConfig::find($request->id);
            if(!$record)
            {
                $record = new CourseConfig;
                $record->course_id = $course->id;
            }

           if(!\Auth::user()->isSysAdmin())
           {
               $record->company_id = \Auth::user()->company_id ;
           }

            if($request->learning_path !="" && count($request->learning_path) > 0)
            {
              $request->learning_path = implode(",", $request->learning_path);
            }
            $record->transversal_rule = $request->transversal_rule ?: 'none';
            $record->completion_rule = $request->completion_rule ?: 'any';
            $record->completion_modules = $request->has('completion_modules') ? implode(',', $request->completion_modules) : '';
            $record->completion_percentage = $request->completion_percentage ?: 0;
            $record->learning_path = $request->learning_path ?: '';

          if($request->get_certificate && $request->get_certificate !="")
            $record->get_certificate = $request->get_certificate;
           else {
             $record->get_certificate  = 0 ;
           }

           if(\Auth::user()->isSysAdmin())
           {
             $hasCertificate = Certificate::where('course_id', $course->id)->first();
           }
           else {
             $hasCertificate = Certificate::where('company_id', \Auth::user()->company_id)->where('course_id', $course->id)->first();
           }


            if($hasCertificate){
                $certificate = Certificate::find($hasCertificate->id);
                $certificate->updated_by = \Auth::id();
                if(!\Auth::user()->isSysAdmin())
                {
                  $certificate->company_id = \Auth::user()->company_id;
                }
            }
            else{
                $certificate = new Certificate;
                $certificate->course_id = $course->id;
                $certificate->design_id = 1;
                $certificate->certificate_name = $request->certificate_name?:'';
                $certificate->created_by = \Auth::id();
                if(!\Auth::user()->isSysAdmin())
                {
                  $certificate->company_id = \Auth::user()->company_id;
                }
            }
            if($request->get_certificate == 1)
            {
                $certificate->design_id = $request->design_id?:1;
                $certificate->certificate_name = $request->certificate_name?:'';
                $certificate->validity_years = $request->validity_years;
                $certificate->validity_months = $request->validity_months;
                $certificate->validity_weeks = $request->validity_weeks;
                $certificate->validity_days = $request->validity_days;
                $certificate->active = true;
            }
            else{
                $certificate->active = false;
            }
            $certificate->save();

            if($record->save())
            {
                Alert::success(__('messages.save_success'))->autoclose(3000);
                return redirect()->route('courses.show', $course->slug);
            }
            Alert::error(__('messages.save_failed'))->autoclose(3000);
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->back()->withInput();
    }


    public function ajaxCourses(Request $request)
    {

      if(\Auth::user()->company_id && !$request->type)
      {
          $enrolledCourseIds = \App\CourseCompany::where('company_id', \Auth::user()->company_id)
                            ->where('active', true)->pluck('course_id');

          $courses = Course::whereIn('courses.id', $enrolledCourseIds);

          if($request->category_id > 0)
           $courses->where("courses.category_id", $request->category_id);
          if($request->sub_category_id > 0)
           $courses->where("courses.sub_category_id", $request->sub_category_id);
          if($request->language != "0")
           $courses->where("courses.language", $request->language);



          $courses =  $courses->get();

      }
      elseif($request->type=="mycourse")
      {

          $enrolledCourseIds = \App\CourseUser::where('user_id', \Auth::user()->id)->where('active', true)->pluck('course_id');

          $courses = Course::whereIn('id', $enrolledCourseIds);

          if($request->category_id > 0)
           $courses->where("category_id", $request->category_id);
          if($request->sub_category_id > 0)
           $courses->where("sub_category_id", $request->sub_category_id);

          $courses =  $courses->get();


      }
      else
      {
          $superAdmin = \App\User::where('role_id', 0)->pluck('id');
          if($request->page == "course-company")
          {
              $courses = Course::whereNotIn('courses.created_by', $superAdmin);
          }
          else {
              $courses = Course::whereIn('courses.created_by', $superAdmin);
          }


        //  $courses = Course::select("*");

          if($request->category_id > 0)
           $courses->where("category_id", $request->category_id);
          if($request->sub_category_id > 0)
           $courses->where("sub_category_id", $request->sub_category_id);
          if($request->language != "0")
           $courses->where("language", $request->language);


          $courses = $courses->get();


      }

      return view('courses.lists', compact('courses'));

    }

    public function courseList()
    {
        $title = trans('controllers.list_of_courses');;
        $breadcrumbs = ['' => $title];
        return view('courses.course_list', compact('title', 'breadcrumbs'));
    }

    public function courseListData()
    {
      if(\Auth::user()->company_id)
      {
          $enrolledCourseIds = \App\CourseCompany::where('company_id', \Auth::user()->company_id)
                                                  ->where('active', true)->pluck('course_id');
          $courses = Course::whereIn('id', $enrolledCourseIds)->get();
      }
      else
          $courses = Course::all();
      return Datatables::of($courses)
                ->rawColumns(['id', 'title', 'slug'])
                ->make(true);
    }


    public function courseCompany()
    {

        $title = @session('menuLabel')['courses'] ?: session('menuLabel')['course-management'];
        $breadcrumbs = ['' => $title];

        $enrolledCourseIds = array();
        $createPermission = 1 ;
        $page = 'course-company';

    /*    $enrolledCourseIds = \App\CourseCompany::where('company_id', \Auth::user()->company_id)
                                 ->where('active', true)->pluck('course_id');
     */
        $superAdmin = \App\User::where('role_id', 0)->pluck('id');

        $courses = Course::whereNotIn('courses.created_by', $superAdmin)->get();

        $parentCategory = CourseCategory::where("parent", 0)->get();
        return view('courses.index', compact(
          'courses',
          'title',
          'breadcrumbs',
          'enrolledCourseIds',
          'parentCategory',
          'createPermission',
          'page'
        ));
    }

}
