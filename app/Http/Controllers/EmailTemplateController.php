<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmailTemplate;
use Yajra\Datatables\Datatables;
use Alert;
use Auth;
use Carbon\Carbon;

class EmailTemplateController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = trans('controllers.email_setup');

        $language = '';

        if ($request->user()->isSysAdmin()) {
          $language = \Session::get('locale');
        } else if (empty($company->language)) {
          $language = \Config::get("app.fallback_locale");
        } else {
          $language = $company->language;
        }

        $breadcrumbs = [
          '' => $title
        ];
        return view('email-templates.index', compact(
          'title',
          'breadcrumbs',
          'language'
        ));
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData(Request $request)
    {
        if (!Auth::user()->isSysAdmin()) {
          $companyId = Auth::user()->company_id;
          $companyMail = EmailTemplate::where('company_id', $companyId)
                                      ->where('language', $request->language);
          $slugs = $companyMail->pluck('slug');
          $langIds = $companyMail->pluck('id');

          $superMail = EmailTemplate::where('company_id', null)
                        ->where('language', $request->language)
                        ->whereNotIn('slug', $slugs)
                        ->whereNotIn('id', $langIds);
          $superSlugs = $superMail->pluck('slug');
          $superLangIds = $superMail->pluck('id');

          $data = EmailTemplate::where('company_id', null)
                  ->whereNotIn("slug", $slugs)
                  ->whereNotIn("slug", $superSlugs)
                  ->whereNotIn("id", $langIds)
                  ->whereNotIn("id", $superLangIds)
                  ->where("language", null)
                  ->union($superMail)
                  ->union($companyMail)
                  ->get();

          if (count($data) == 0) {
            $data = EmailTemplate::where('company_id', null)->where("language", null)->get();
          }
        } else {
          $data = array();

          if ($request->language) {
            \Session::put('language', $request->language);
            $data = EmailTemplate::where('company_id', null);
            $langTemplates = $data->where('language', $request->language);
            $langIds = $langTemplates->pluck('id');
            $slugs = $langTemplates->pluck('slug');

            $data = EmailTemplate::where('company_id', null)
                  ->whereNotIn("id", $langIds)
                  ->whereNotIn("slug", $slugs)
                  ->where("language", null)
                  ->union($langTemplates)
                  ->get();

          }

          if (empty($request->language) || count($data) == 0) {
            $data = EmailTemplate::where('company_id', null)->where("language", null)->get();
          }
        }

        for ($d = 0; $d < count($data); $d++) {
          $data[$d]->language = $request->language;
        }

        return Datatables::of($data)
                ->editColumn('updated_at', function ($data) {
                  return Carbon::parse($data->updated_at)->format('d-m-Y h:i:s');
                })
                ->addColumn('action', function ($data) {
                    return
                      '<a href="javascript:editTemplate('.$data->id.')" class="btn btn-sm btn-primary" title="'.trans('modules.edit').'">
                        <i class="icon-pencil"></i>
                      </a>'
                      ." ".
                      '<a href="'.route('email-setup.editor', [$data->slug, $data->language]).
                        '" class="btn btn-sm btn-warning"><i class="icon-pencil"></i> '.trans('controllers.design').'</a>'

                  //    '<a href="'.route('email-setup.show', [$data->slug, $data->language]).'" class="btn btn-sm btn-warning"><i class="icon-pencil"></i> '.trans('controllers.design').'</a>'
                    ;
                })
                ->rawColumns(['action'])
                ->make(true);
    }



    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug, $language="")
    {
        if(!Auth::user()->isSysAdmin())
         {
          $companyId = Auth::user()->company_id;
          $data = EmailTemplate::where('company_id', $companyId)
                                ->where('slug', $slug);

          if($language !="")
          {
            $data->where('language', $language);
          }
          $data = $data->first();
         }
         else {
             $data = EmailTemplate::where('company_id', null)->where('slug', $slug);
             if($language !="")
             {
               $data->where('language', $language);
             }
             $data = $data->first();
         }

        if(!$data)
        {
          $data = EmailTemplate::where('company_id', null)
                  ->where("slug", $slug);

                  if($language !="")
                  {
                    $data->where('language', $language);
                  }

          $data = $data->first();
        }

        if(!$data)
        {
          $data = EmailTemplate::where('company_id', null)
                  ->where("slug", $slug);

          $data = $data->first();
        }

        if($data)
        {
            $breadcrumbs = [
                route('email-setup.index') => trans('controllers.email_setup'),
                route('email-setup.show', [$data->slug,$language]) => $data->template_name,
                '' => trans('controllers.edit'),
            ];
            $title = trans('controllers.edit_design').$data->template_name;
            return view('email-templates.design', compact('title', 'breadcrumbs', 'data', 'language'));
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->route('email-setup.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit($slug, $language="")
    {

        if(!Auth::user()->isSysAdmin())
         {
          $companyId = Auth::user()->company_id;
          $data = EmailTemplate::where('company_id', $companyId)
                                ->where('slug', $slug);

                  if($language !="")
                  {
                    $data->where('language', $language);
                  }
          $data = $data->first();
         }
         else {
             $data = EmailTemplate::where('company_id', null)->where('slug', $slug);

             if($language !="")
             {
               $data->where('language', $language);
             }

             $data = $data->first();
         }

        if(!$data)
        {
          $data = EmailTemplate::where('company_id', null)
                  ->where("slug", $slug);

                  if($language !="")
                  {
                    $data->where('language', $language);
                  }
          $data = $data->first();
        }




        if($data)
        {
            $breadcrumbs = [
                route('email-setup.index') => trans('controllers.email_setup'),
                route('email-setup.show', [$data->slug,$language]) => $data->template_name,
                '' => trans('controllers.edit'),
            ];
            $title = trans('controllers.edit').' '.$data->team_name;
            return view('email-templates.form', compact('title', 'breadcrumbs', 'data'));
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->route('email-setup.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $language="")
    {

      if(Auth::user()->isSysAdmin())
      {
         $record = EmailTemplate::find($id);
        if($language != "")
        {
          $subject = $record->subject;
          $record  = EmailTemplate::updateOrCreate(['template_name' => $record->template_name, 'slug'=>$record->slug, 'language'=>$language]);
          $record->subject = $subject;
        }

      }
      else {

          $template = EmailTemplate::find($id);
          $companyId = Auth::user()->company_id;

         $record =  EmailTemplate::updateOrCreate(['template_name' => $template->template_name, 'company_id' => $companyId, 'slug'=>$template->slug, 'language'=>$language]);
         $record->subject = $template->subject;
         $record->updated_by =Auth::user()->id;
       }

        if($record)
        {

            if($request->has('subject'))
                $record->subject = $request->subject;
            if($request->has('body'))
                $record->content = $request->body;

            $record->updated_at = date('Y-m-d H:i:s');
            if($record->save())
            {
                Alert::success(__('messages.save_success'))->autoclose(3000);
                return redirect()->route('email-setup.index');
            }
            Alert::error(__('messages.save_failed'))->autoclose(3000);
            return redirect()->back()->withInput();
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    public function ajaxGet(Request $request)
    {
         $record = EmailTemplate::select("template_name", "subject")->find($request->id);
         return response()->json($record);
    }

    public function ajaxUpdate(Request $request)
    {
      $checkRecord = EmailTemplate::find($request->id);
      if(!Auth::user()->isSysAdmin())
       {
         $record  = EmailTemplate::updateOrCreate(['company_id'=> Auth::user()->company_id, 'slug'=>$checkRecord->slug, 'language'=>$request->language],
           ['template_name' =>$request->template_name]);
       }
       else {
         $record  = EmailTemplate::updateOrCreate(['slug'=>$checkRecord->slug, 'language'=>$request->language],
         ['template_name' =>$request->template_name]);
        }

       if($record)
       {
        // $record->template_name = $request->template_name;
         $record->subject = $request->subject;
         $record->content = $checkRecord->content;
         $record->updated_at = date('Y-m-d H:i:s');

         if($record->save())
         {
            $msg = __('messages.save_success');

         }
         else
          $msg = __('messages.save_failed');

       }
       else
        $msg = __('messages.invalid_request');

        $res = array('msg'=>$msg);

      return response()->json($res);
    }

    public function visualEditor(Request $request){

      $slug = $request->slug ;
      $language = $request->language ;

      if(!Auth::user()->isSysAdmin())
       {
        $companyId = Auth::user()->company_id;
        $data = EmailTemplate::where('company_id', $companyId)
                              ->where('slug', $slug);

        if($language !="")
        {
          $data->where('language', $language);
        }
        $data = $data->first();
       }
       else {
           $data = EmailTemplate::where('company_id', null)->where('slug', $slug);
           if($language !="")
           {
             $data->where('language', $language);
           }
           $data = $data->first();
       }

      if(!$data)
      {
        $data = EmailTemplate::where('company_id', null)
                ->where("slug", $slug);

                if($language !="")
                {
                  $data->where('language', $language);
                }

        $data = $data->first();
      }

      if(!$data)
      {
        $data = EmailTemplate::where('company_id', null)
                ->where("slug", $slug);

        $data = $data->first();
      }

      if($data)
      {
          $breadcrumbs = [
              route('email-setup.index') => trans('controllers.email_setup'),
              route('email-setup.editor', [$data->slug,$language]) => $data->template_name,
              '' => trans('controllers.edit'),
          ];
          $title = trans('controllers.edit_design').$data->template_name;
        //  return view('email-templates.design', compact('title', 'breadcrumbs', 'data', 'language'));
          return view('email-templates.editor', compact('title', 'breadcrumbs', 'data', 'language'));
      }
      Alert::error(__('messages.invalid_request'))->autoclose(3000);
      return redirect()->route('email-setup.index');

    }

    public function ajaxVariableGet(Request $request)
    {
         $data = EmailTemplate::where('id', $request->id);
        

         $variable = "<b>@URL </b>: To redirect specific url according to the template</br>";
         $variable .= "<b>@FIRSTNAME </b>: User First Name</br>";
         $variable .= "<b>@LASTNAME </b>: User Last Name</br>";
         $variable .= "<b>@COURSE_NAME </b>: User Course Name</br>";
         $variable .= "<b>@ENROLLED_BY </b>: Course Enrolled By Name</br>";
         $variable .= "<b>@PORTAL </b>: Web Portal Name</br>";
         $variable .= "<b>@COMPANY </b>: User Company Name</br>";
         $variable .= "<b>@SENDER_NAME </b>: Sender Name</br>";
         $variable .= "<b>@SENDER_EMAIL </b>: Sender Email Name</br>";
         $variable .= "<b>@TICKET_NO </b>: Ticket no</br>";
         $variable .= "<b>@TICKET_SUBJECT </b>: Ticket Subject</br>";
         $variable .= "<b>@TICKET_CONTENT </b>: Ticket details</br>";

         return response()->json($variable);
    }

}
