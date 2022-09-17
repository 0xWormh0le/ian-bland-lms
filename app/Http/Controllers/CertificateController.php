<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\CourseUser;
use App\Certificate;
use App\CertificateConfig;
use App\User;
use App\Company;
use Auth, Alert;
use Carbon\Carbon;

class CertificateController extends Controller
{
    /**
     * Preview the Design Certificate.
     *
     * @return \Illuminate\Http\Response
     */
    public function preview($user_id, $course_id)
    {
        $path = 'formal-1';

        $user = User::find($user_id);
        $course = Course::withTrashed()->find($course_id);
        $courseResult = CourseUser::withTrashed()
                                ->where('user_id', $user->id)
                                ->where('course_id', $course->id)
                                ->where('completed', true)
                                ->first();

        $certificate = Certificate::where('course_id', $course_id)
                                    ->where('company_id', $user->company_id)
                                    ->withTrashed()->first();

        if ($course && $certificate && $courseResult)
        {

            $template = \DB::table('certificate_designs')
                    ->where('id', $certificate->design_id)
                    ->first();

            if($template)
            {
                $company = "" ;
                $years=$months=$weeks=$days= 0;



                if($certificate->validity_years == 1)
                     $years = $certificate->validity_years;
                if($certificate->validity_months == 1)
                     $months = $certificate->validity_months;
                if($certificate->validity_weeks == 1)
                     $weeks = $certificate->validity_weeks;
                if($certificate->validity_days == 1)
                     $days = $certificate->validity_days;

               if(Auth::check() && Auth::user()->isSysAdmin())
               { /*do nothing*/}
              else
               {

                 $certificateConfig = CertificateConfig::where("company_id", $user->company_id)->first();

                if($certificateConfig){
                   if($certificateConfig->validity_years == 1)
                        $years = $certificateConfig->validity_years;
                   if($certificateConfig->validity_months == 1)
                        $months = $certificateConfig->validity_months;
                   if($certificateConfig->validity_weeks == 1)
                        $weeks = $certificateConfig->validity_weeks;
                   if($certificateConfig->validity_days == 1)
                        $days = $certificateConfig->validity_days;
                    }
                  $company = Company::select("company_name")->where("id", $user->company_id)->first();
                  $company = $company->company_name;
                }

                $validity = "" ;
                $validity = Carbon::parse($courseResult->completion_date)->addYears($years);
                $validity = Carbon::parse($validity)->addMonths($months);
                $validity = Carbon::parse($validity)->addWeeks($weeks);
                $validity = Carbon::parse($validity)->addDays($days)->format("d-m-Y");



                $html = $template->content;
                $trans = [
                    '@BACKGROUND' => asset('storage/certificates/background/'.$template->background),
                    '@RECIPIENT' => $user->first_name.' '.$user->last_name,
                    '@COURSETITLE' => $course->title,
                    '@QRCODE' => base64_encode(\QrCode::format('png')->size(100)->generate(route('certificate.preview', ['user_id' => $user_id, 'course_id'=> $course_id]))),
                    '@VALIDITY_DURATION' => $validity,
                    '@COMPANY' => $company,
                    '@FONT_PATH' => storage_path().'/fonts/wt011.ttf'
                ];

                $trans_ext = array();
                if($certificateConfig)
                {
                  $trans_ext = [
                  '@SIGNER' => $certificateConfig->signer,
                  '@POSITION' => $certificateConfig->position,
                  '@SIGNATURE' => asset('storage/certificates/signature/'.$certificateConfig->signature),
                  ];
                }

                $trans = array_merge($trans, $trans_ext);
                $html = strtr($html, $trans);

                $pdf = \PDF::loadHTML($html);
                return $pdf->setPaper('a4', 'landscape')->stream();
            }
        }
        return trans('controllers.certificate_exist_error');
    }

    public function config()
    {
      $title = "Certificate Configuration";
      $certificate =  CertificateConfig::where("company_id", Auth::user()->company_id)->first() ;


      return view("sysconfig.certificate-config", compact('title','certificate'));
    }

    public function configUpdate(Request $request)
    {
        $certificate =  CertificateConfig::firstOrNew(["company_id" => Auth::user()->company_id]) ;

         if($request->image_base64)
         {
             if($certificate->signature)
             {
                 if(\Storage::disk('public')->exists('certificates/signature/'.$certificate->signature));
                     \Storage::disk('public')->delete('certificates/signature/'.$certificate->signature);
             }
             $signature_image = str_replace('data:image/png;base64,', '', $request->image_base64);
             $signature_image = str_replace(' ', '+', $signature_image);
             $signature =  uuid().'.png';
             \Storage::disk('public')->put('/certificates/signature/'.$signature, base64_decode($signature_image));
             $certificate->signature = $signature;
         }

         $certificate->company_id  = \Auth::user()->company_id;
         $certificate->validity_years   = $request->validity_years;
         $certificate->validity_months   = $request->validity_months;
         $certificate->validity_weeks   = $request->validity_weeks;
         $certificate->validity_days   = $request->validity_days;
         $certificate->signer    = $request->signer ;
         $certificate->position    = $request->position ;


         if($certificate->save())
         {
             Alert::success(__('messages.save_success'))->autoclose(3000);
             return redirect()->back();
         }
         Alert::error(__('messages.save_failed'))->autoclose(3000);
         return redirect()->back()->withInput();

    }
}
