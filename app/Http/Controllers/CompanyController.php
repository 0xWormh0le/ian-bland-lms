<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\User;
use App\CourseUser;
use App\CourseMember;
use App\MyCertificate;
use App\OtpVerify;
use App\CourseResult;
use App\CourseResultHistory;
use App\CertificateConfig;
use App\CompanyEmailConfig;
use App\CourseCompany;
use App\Course;
use App\ElearningUser;
use App\EmailTemplate;
use App\Role;
use App\Team;
use App\Ticket;
use App\TicketHistory;
use App\TicketResponse;
use App\TicketAttachment;
use App\Document;

use Yajra\Datatables\Datatables;
use Alert;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CompanyController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = session('menuLabel')['companies'];
        return view('companies.index', compact('title'));
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {
        $data = Company::all();
        return Datatables::of($data)
                        ->editColumn('company_name', function ($data) {
                            $html = '';
                            $html .= $data->company_name;
                            if($data->logo)
                                $html .= '
                                    <img class="img-responsive" src="'.asset('storage/logo/'.$data->logo).'" width="80" alt="'.trans('modules.logo').'" style="margin-left:30px;">
                                ';
                            return $html;
                        })
                        ->editColumn('active', function ($data) {
                            if($data->active == 1)
                                return '
                                    <button type="button" class="btn btn-sm btn-success" style="margin-bottom: 4px">
                                        <i class="icon-check"></i>
                                        <span>'.trans('modules.active').'</span>
                                    </button>';
                            else
                                return '
                                    <button type="button" class="btn btn-sm btn-danger" style="margin-bottom: 4px">
                                        <i class="icon-close"></i>
                                        <span>'.trans('modules.inactive').'</span>
                                    </button>';
                        })
                        ->addColumn('action', function ($data) {
                            return
                                show_button('show', 'companies.show', $data->slug)
                                ." ".
                                show_button('edit', 'companies.edit', $data->slug)
                                ." ".
                                show_button('delete', 'companies.destroy', encrypt($data->id))
                            ;
                        })
                        ->rawColumns(['company_name', 'active', 'action'])
                        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = trans('controllers.add_new_company');
        return view('companies.form', compact('title'));
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
            'company_name' => 'required',
            'max_users' => 'nullable|numeric',
            'active_from'  => 'nullable|date',
            'active_to' => 'nullable|date|after_or_equal:active_from',
            'logo' => 'nullable | image | max:5000'
        ];
        $request->validate($rules);

        $record = new Company;
        $record = $this->save($record, $request);
        if($record)
        {
            Alert::success(__('messages.save_success'))->autoclose(3000);
            return redirect()->route('companies.show', $record->slug);
        }
        Alert::error(__('messages.save_failed'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $data = Company::findBySlug($slug);
        if($data)
        {
            $breadcrumbs = [
                route('companies.index') => trans('controllers.companies'),
                '' => $data->company_name,
            ];
            
            $title = trans('controllers.details_of').$data->company_name;
            $enrolled_courses = CourseCompany::with('course.category')
                                  ->where('company_id', $data->id)
                                  ->get()
                                  ->map(function ($courseCompany) {
                                    return $courseCompany->course;
                                  });

            $unenrolled_courses = Course::with('category')
                                  ->whereNotIn('id', $enrolled_courses->map->id)
                                  ->get();
            
             return view('companies.details',
                        compact('title', 'breadcrumbs', 'data', 'enrolled_courses', 'unenrolled_courses'));
        }
        Alert::error(__('messages.invalid_request'));
        return redirect()->route('companies.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Company::findBySlug($id);
        if($data->active_from != "" && $data->active_from !=null)
        {

        $data->active_from = Carbon::parse($data->active_from)->format('d-m-Y');
      }

        if($data->active_to !="")
        $data->active_to = Carbon::parse($data->active_to)->format('d-m-Y');

        if($data)
        {
            $breadcrumbs = [
                route('companies.index') => trans('controllers.companies'),
                route('companies.show', $data->slug) => $data->company_name,
                '' => trans('controllers.edit'),
            ];
            $title = trans('controllers.edit').' '.$data->company_name;
            return view('companies.form', compact('title', 'breadcrumbs', 'data'));
        }
        Alert::error(__('messages.invalid_request'));
        return redirect()->route('companies.index');
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
            'company_name' => 'required',
            'max_users' => 'nullable|numeric',
            'active_from'  => 'nullable|date',
            'active_to' => 'nullable|date|after_or_equal:active_from',
            'logo' => 'nullable| image | max:5000'
          ];
        $request->validate($rules);


        $record = Company::find($id);
        if($record)
        {
            $record = $this->save($record, $request);
            if($record)
            {
                Alert::success(__('messages.save_success'))->autoclose(3000);
                return redirect()->route('companies.show', $record->slug);
            }
            Alert::error(__('messages.save_failed'))->autoclose(3000);
            return redirect()->back()->withInput();
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Save Data
     * @param Object $record
     * @param Request $request
     */
    function save($record, $request)
    {
        $record->company_name = $request->company_name;
        $record->max_users = $request->max_users;
        if($request->active_from != "")
         $record->active_from = Carbon::parse($request->active_from)->format('Y-m-d');
        else {
          $record->active_from = null ;
        }
        if($request->active_to != "")
         $record->active_to = Carbon::parse($request->active_to)->format('Y-m-d');
        else {
          $record->active_to = null;
        }
        $record->active = $request->has('active') ? true : false;
        if($record->id)
        {
            $record->slug = null;
            $record->updated_by = $request->user()->id;
        }else{
            $record->created_by = $request->user()->id;
        }

        if ($request->hasFile('logo')) {

            if($record->logo)
            {
                $exists = Storage::exists($record->logo);
                if($exists)
                    Storage::delete($record->logo);
            }

            $image = $request->file('logo');
            $filename = md5($record->company_name. time()).'.'.$image->getClientOriginalExtension();
            $img = \Image::make($image->getRealPath());
            $img->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            if($img->save(public_path('storage/logo/'.$filename)))
                $record->logo = $filename;
        }

        if($record->save())
            return $record;
        return false;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = Company::find(decrypt($id));
        if($record)
        {
            $id = $record->id;
            $record->deleted_by = \Auth::id();
            $record->save();
            if($record->delete())
            {
                $this->softDeleteCompanyData($id);
                Alert::success(__('messages.delete_success'))->autoclose(3000);
                return redirect()->route('companies.index');
            }
            Alert::error(__('messages.delete_failed'))->autoclose(3000);
            return redirect()->route('companies.index');
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->route('companies.index');
    }

    public function restore()
    {
      $title = trans('controllers.restore_company');
      return view('companies.restore', compact('title'));
    }


    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function trashedData()
    {
        $data = Company::onlyTrashed()->get();
        return Datatables::of($data)
                ->editColumn('company_name', function ($data) {
                    $html = '';
                    $html .= $data->company_name;
                    if($data->logo)
                        $html .= '<img class="img-responsive" src="'
                                . asset('storage/logo/'.$data->logo)
                                . '" width="80" alt="'
                                . trans('modules.logo')
                                . '" style="margin-left:30px;">';
                    return $html;
                })
                ->addColumn('action', function ($data) {
                    return
                        show_button('recycle', 'companies.restore.event', encrypt($data->id))
                        ." ".
                        show_button('delete', 'companies.delete.trash', encrypt($data->id))
                    ;
                })
                ->rawColumns(['company_name', 'action'])
                ->make(true);
    }

    public function restoreCompany($id)
    {
       $company = Company::withTrashed()->find(decrypt($id));
       $deleteAt = $company->deleted_at;

       if($company->restore())
       {
         $this->restoreSoftDeleteCompanyData($company->id, $deleteAt);
         Alert::success(__('messages.restore_success'))->autoclose(3000);
         return redirect()->route('companies.index');
       }
       else {
         Alert::error(__('messages.restore_failed'))->autoclose(3000);
       }

    }


    private function softDeleteCompanyData($id)
    {

          $users = User::where('company_id',$id)->get();

          for($u=0; $u<count($users); $u++)
          {
            CourseUser::where('user_id', $users[$u]->id)->delete();
            CourseMember::where('company_id', $id)->where('user_id', $users[$u]->id)->delete();
            MyCertificate::where('user_id',$users[$u]->id)->delete();
            OtpVerify::where('user_id',$users[$u]->id)->delete();
            $couseResult = CourseResult::where('courseuser_id', $users[$u]->id)->get();
            User::where('id',$users[$u]->id)->delete();

            for($cr=0;$cr<count($couseResult);$cr++)
            {
              CourseResultHistory::where('courseresult_id', $couseResult[$cr]->id)->delete();
              CourseResult::where('id', $couseResult[$cr]->id)->delete();
            }
          }

          CertificateConfig::where("company_id", $id)->delete();
          CompanyEmailConfig::where("company_id", $id)->delete();
          CourseCompany::where("company_id", $id)->delete();
          ElearningUser::where("company_id", $id)->delete();
          EmailTemplate::where("company_id", $id)->delete();
          Role::where("company_id", $id)->delete();
          Team::where("company_id", $id)->delete();

          $tickets = Ticket::where("company_id", $id)->get();

          for($t=0; $t<count($tickets); $t++)
          {
            TicketHistory::where('ticket_id', $tickets[$t]->id)->delete();
            TicketResponse::where('ticket_id', $tickets[$t]->id)->delete();
            Ticket::where('id', $tickets[$t]->id)->delete();
          }


    }

    private function restoreSoftDeleteCompanyData($id, $deleteAt)
    {
      $deleted_time_range1 = Carbon::createFromFormat('Y-m-d h:i:s', $deleteAt);
      $deleted_time_range2 = Carbon::createFromFormat('Y-m-d h:i:s', $deleteAt)->addMinutes(5);

      $users = User::withTrashed()->where('company_id',$id)
               ->where('deleted_at','>=', $deleted_time_range1)
               ->where('deleted_at','<=', $deleted_time_range2)->get();

      for($u=0; $u<count($users); $u++)
      {
        CourseUser::withTrashed()->where('user_id', $users[$u]->id)->where('deleted_at','>=', $deleted_time_range1)
        ->where('deleted_at','<=', $deleted_time_range2)->restore();
        CourseMember::withTrashed()->where('company_id', $id)->where('user_id', $users[$u]->id)->where('deleted_at','>=', $deleted_time_range1)
        ->where('deleted_at','<=', $deleted_time_range2)->restore();
        MyCertificate::withTrashed()->where('user_id',$users[$u]->id)->where('deleted_at','>=', $deleted_time_range1)
        ->where('deleted_at','<=', $deleted_time_range2)->restore();

        $couseResult = CourseResult::withTrashed()->where('courseuser_id', $users[$u]->id)->where('deleted_at','>=', $deleted_time_range1)
        ->where('deleted_at','<=', $deleted_time_range2)->get();
        User::withTrashed()->where('id',$users[$u]->id)->where('deleted_at','>=', $deleted_time_range1)
        ->where('deleted_at','<=', $deleted_time_range2)->restore();

        for($cr=0;$cr<count($couseResult);$cr++)
        {
          CourseResultHistory::withTrashed()->where('courseresult_id', $couseResult[$cr]->id)->where('deleted_at','>=', $deleted_time_range1)
          ->where('deleted_at','<=', $deleted_time_range2)->restore();
          CourseResult::withTrashed()->where('id', $couseResult[$cr]->id)->where('deleted_at','>=', $deleted_time_range1)
          ->where('deleted_at','<=', $deleted_time_range2)->restore();
        }
      }

      CourseCompany::withTrashed()->where("company_id", $id)->where('deleted_at','>=', $deleted_time_range1)
      ->where('deleted_at','<=', $deleted_time_range2)->restore();
      ElearningUser::withTrashed()->where("company_id", $id)->where('deleted_at','>=', $deleted_time_range1)
      ->where('deleted_at','<=', $deleted_time_range2)->restore();

      Role::withTrashed()->where("company_id", $id)->where('deleted_at','>=', $deleted_time_range1)
      ->where('deleted_at','<=', $deleted_time_range2)->restore();
      Team::withTrashed()->where("company_id", $id)->where('deleted_at','>=', $deleted_time_range1)
      ->where('deleted_at','<=', $deleted_time_range2)->restore();

      $tickets = Ticket::withTrashed()->where("company_id", $id)->where('deleted_at','>=', $deleted_time_range1)
      ->where('deleted_at','<=', $deleted_time_range2)->get();

      for($t=0; $t<count($tickets); $t++)
      {

        TicketResponse::withTrashed()->where('ticket_id', $tickets[$t]->id)->where('deleted_at','>=', $deleted_time_range1)
        ->where('deleted_at','<=', $deleted_time_range2)->restore();
        Ticket::withTrashed()->where('id', $tickets[$t]->id)->where('deleted_at','>=', $deleted_time_range1)
        ->where('deleted_at','<=', $deleted_time_range2)->restore();
      }
    }

    public function deleteTrashCompany($id)
    {
       if($this->cronToDeleteTrashCompany(decrypt($id)) == 1)
       {
         Alert::success(__('messages.delete_success'))->autoclose(3000);
         return redirect()->route('companies.index');
       }
       else
       {
         Alert::error(__('messages.delete_failed'))->autoclose(3000);
       }
    }

    public function cronToDeleteTrashCompany($id=0)
    {
      $status = 0;
      if($id > 0)
      {
        $company = Company::withTrashed()->where('id', $id)->get();
      }
      else
      {
        $company = Company::withTrashed()->where('deleted_by', 1)->whereNotNull('deleted_at')->get();
      }

      for($c=0; $c < count($company); $c++)
      {
        $deleted = new Carbon($company[$c]->deleted_at);
        $now = Carbon::now();
        $difference = $deleted->diff($now)->days;

        if($difference >= 30 || $id > 0)
        {
          if(Company::withTrashed()->find($company[$c]->id)->forcedelete())
          {
              $status = 1;
              $users = User::withTrashed()->where('company_id',$company[$c]->id)->get();

              for($u=0; $u<count($users); $u++)
              {
                CourseUser::withTrashed()->where('user_id', $users[$u]->id)->forcedelete();
                CourseMember::withTrashed()->where('company_id', $company[$c]->id)->where('user_id', $users[$u]->id)->forcedelete();
                MyCertificate::withTrashed()->where('user_id',$users[$u]->id)->forcedelete();
                OtpVerify::where('user_id',$users[$u]->id)->forcedelete();
                $couseResult = CourseResult::where('courseuser_id', $users[$u]->id)->get();
                User::withTrashed()->where('id', $users[$u]->id)->forcedelete();
                for($cr=0;$cr<count($couseResult);$cr++)
                {
                  CourseResultHistory::withTrashed()->where('courseresult_id', $couseResult[$cr]->id)->forcedelete();
                  CourseResult::withTrashed()->where('id', $couseResult[$cr]->id)->forcedelete();
                }
              }

              CertificateConfig::where("company_id", $company[$c]->id)->forcedelete();
              CompanyEmailConfig::where("company_id", $company[$c]->id)->forcedelete();
              CourseCompany::withTrashed()->where("company_id", $company[$c]->id)->forcedelete();
              ElearningUser::withTrashed()->where("company_id", $company[$c]->id)->forcedelete();
              EmailTemplate::where("company_id", $company[$c]->id)->forcedelete();
              Role::withTrashed()->where("company_id", $company[$c]->id)->forcedelete();
              Team::withTrashed()->where("company_id", $company[$c]->id)->forcedelete();

              $tickets = Ticket::withTrashed()->where("company_id", $company[$c]->id)->get();

              for($t=0; $t<count($tickets); $t++)
              {
                TicketHistory::where('ticket_id', $tickets[$t]->id)->forcedelete();
                TicketResponse::withTrashed()->where('ticket_id', $tickets[$t]->id)->forcedelete();
                Ticket::withTrashed()->where('id', $tickets[$t]->id)->forcedelete();


                $attachments = explode(",", $tickets[$t]->attachment_id);

                for($a=0;$a<count($attachments);$a++)
                {
                  $attachment = TicketAttachment::withTrashed()->where('id', $attachments[$a])->first();

                  Storage::delete('app/'.$attachment->filepath);
                  TicketAttachment::withTrashed()->where('id', $attachments[$a])->forcedelete();
                }
              }

              $documents= Document::withTrashed()->where("company_id", $company[$c]->id)->get();

             for($d=0;$d<count($documents);$d++)
             {
               Storage::delete('app/'.$documents[$d]->filepath);
               Document::withTrashed()->where("company_id", $documents[$d]->id)->forcedelete();
             }

          }
        }
      }

      return $status;

    }





}
