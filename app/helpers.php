<?php

/**
 * The application's global Helpers function.
 *
 * These middleware are run during every request to your application.
 *
 * @var array
 */

if (! function_exists('validate_role')) {
    /**
     * Validate access by Role
     *
     * @param string $route
     * @return boolean
     */
    function validate_role($route) {
        $user = \Auth::user();

        if (session('roles')) {
            $roles = session('roles');
        } else {
            if ($user->roleUser) {
                // $role_str = str_replace('portal-management.','', $user->roleUser->role_access);
                $role_str = str_replace('user-management.','', $user->roleUser->role_access);
                $roles = explode(',',$role_str);
            } else if ($user->role == 0) {
                $roles = ['superadmin'];
            } else {
                $roles = [];
            }

            session(['roles' => $roles]);
        }

        if (($route == 'courses.create' || $route == 'courses.config') &&
            (\Auth::user()->role_id == 0 || \Auth::user()->role_id == 1)) {
            return true;
        }

        if (in_array($route, $roles) || in_array('superadmin', $roles)) {
            return true;
        }

        return false;
    }
}

if (! function_exists('show_button')) {
    /**
     * Get view component of selected Button
     *
     * @param string $type
     * @param string $url
     * @param integer $id
     * @return string
     */
    function show_button($type, $route, $params, $forceShow = false, $class = '') {
        if(validate_role($route) || $forceShow)
            return view('components.button'.ucfirst($type), compact('route', 'params', 'class'));
    }
}

if (! function_exists('read_slug')) {
    /**
     * Get readable string from slug
     *
     * @param string $slug
     * @return string
     */
    function read_slug($slug) {
        return ucwords(str_replace('-', ' ', $slug));
    }
}


if (! function_exists('uuid')) {
    /**
     * Generate universally unique identifiers (UUID) version 4
     *
     * @return string
     */
    function uuid() {
        $uuid4 = \Ramsey\Uuid\Uuid::uuid4();
        return $uuid4->toString();
    }
}


if (! function_exists('lmsconfig')) {
    /**
     * Load LMS Configuration
     *
     * @return string
     */
    function lmsconfig($key) {
        $conf = [
            'datetime_format' => 'l, M jS Y, g:i A',
            'dateday_format' => 'l, M jS Y',
        ];

        return @$conf[$key] ?: '';
    }
}

if (! function_exists('datetime_format')) {
    function datetime_format($timestamp = null, $format = null) {
        if(!$format)
            $format = lmsconfig('dateday_format');
        if($timestamp)
            return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestamp)
                            ->format($format);
        return '';
    }
}

if (! function_exists('dateformat')) {
    function dateformat($date = null, $format = null) {
        if(!$format)
            $format = lmsconfig('dateday_format');

        if($date)
            return \Carbon\Carbon::createFromFormat('Y-m-d', $date)
                            ->format($format);
        return '';
    }
}

if (! function_exists('timeformat')) {
    function timeformat($time, $format) {
        if($time)
            return \Carbon\Carbon::createFromFormat('H:i:s', $time)
                            ->format($format);
        return '';
    }
}

if (! function_exists('get_words')) {
    function get_words($sentence, $count = 10) {
        $sentence = strip_tags($sentence);
        preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
        return $matches[0];
    }
}

if (! function_exists('tz_list')) {
    function tz_list() {

        $zones_array = array();
        $timestamp = time();

        foreach(timezone_identifiers_list() as $key => $zone) {
          date_default_timezone_set($zone);
          $zones_array[$key]['zone'] = $zone;
          $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
        }

        return $zones_array;
    }
}


if(! function_exists('google2fa_authenticator')){
    function google2fa_authenticator($email, $secret=""){

          $result = array();
          $google2fa =  app('pragmarx.google2fa');

        if(trim($secret) == "")
        {
          // Add the secret key to the registration data
          $result["google2fa_secret"] = $google2fa->generateSecretKey();
        }
        else {
          $result["google2fa_secret"] = $secret;
        }

          // Generate the QR image. This is the image the user will scan with their app
           // to set up two factor authentication
        //  $google2fa->setAllowInsecureCallToGoogleApis(true);

          $result['qr_image'] = $google2fa->getQRCodeInline(
              config('app.name'),
              $email,
              $result['google2fa_secret']
          );

          return $result ;
    }

}


if(! function_exists('human_filesize')){
    function human_filesize($bytes, $decimals = 2) {
    $size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' '. @$size[$factor];
   }
 }

 if(! function_exists('email_template_language')){
     function email_template_language($user, $slug) {

              $language = null ;
              $company_id = null ;
              $comLang = null ;

              if($user->company_id > 0)
              {
                 $comLang = \App\Company::select('language')->where('id', $user->company_id)->first();
                 $comLang = $comLang->language;
                 $company_id = $user->company_id;
              }

              // if($user->language !="") $language = $user->language ;
              if($language == null) $language = $comLang;


              $eTemplate  =  \App\EmailTemplate::where('slug', $slug)
                                      ->where('company_id', $company_id)
                                      ->where('language', $language)
                                      ->first();

              if(!$eTemplate)
              {

                $eTemplate  =    \App\EmailTemplate::where('slug', $slug)
                                        ->where('company_id', $company_id)
                                        ->where('language', $comLang)
                                        ->first();
              }

              if(!$eTemplate)
              {

                $eTemplate  =    \App\EmailTemplate::where('slug', $slug)
                                        ->where('company_id', null)
                                        ->where('language', $comLang)
                                        ->first();
              }

              if(!$eTemplate)
              {

                $eTemplate  =  \App\EmailTemplate::where('slug', $slug)
                                        ->where('company_id', $company_id)
                                        ->where('language', 'en')
                                        ->first();
              }
              if(!$eTemplate)
              {

                $eTemplate  =   \App\EmailTemplate::where('slug', $slug)
                                        ->where('company_id',null)
                                        ->where('language','en')
                                        ->first();

                }
              if(!$eTemplate)
              {

                $eTemplate  =  \App\EmailTemplate::where('slug', $slug)
                                        ->where('company_id', null)
                                        ->where('language', null)
                                        ->first();
              }

             return $eTemplate;
     }

   if (!function_exists('course_completion_rules_result')) {
        function course_completion_rules_result($course_id, $user_id=0, $filter="")
        {
            $returnResult = array("complete" => 0, "percentage" => 0.00);

            if ($user_id == 0) {
              $user_id = \Auth::id();
            }

            if ($filter == "archive") {
              $user = \App\User::find($user_id);
              @$courseConfig = \App\CourseConfig::withTrashed()
                                ->where("company_id", $user->company_id)
                                ->where("course_id", $course_id)->first();

              if (!$courseConfig) {
                $courseConfig = \App\CourseConfig::withTrashed()
                                ->whereNull("company_id")
                                ->where("course_id", $course_id)->first();
              }

              $courseUser = \App\CourseUser::myCourseWithTrashed($course_id, $user_id);
              $courseModules = \App\Module::withTrashed()
                                ->where('course_id', $course_id)
                                ->where('type', 'Elearning')->get();
            } else {
              $user = \App\User::find($user_id);
              @$courseConfig = \App\CourseConfig::where("company_id", $user->company_id)
                                ->where("course_id", $course_id)->first();

              if (!$courseConfig) {
                $courseConfig = \App\CourseConfig::whereNull("company_id")
                                ->where("course_id", $course_id)->first();
              }

              $courseUser = \App\CourseUser::myCourse($course_id, $user_id);
              $courseModules = \App\Module::where('course_id', $course_id)
                                ->where('type', 'Elearning')->get();
            }

            if ($courseModules && count($courseModules) == 0) {
              return $returnResult;
            }

            if ($courseConfig) {
              switch ($courseConfig->completion_rule) {
                case "all":
                case "any":
                  $completeCount = 0;

                  if ($courseUser) {
                    for ($cm = 0; $cm < count($courseModules); $cm++) {
                      if ($filter == "archive") {
                        $result = \App\CourseResult::getModuleResultWithTrashed($courseUser->id, $courseModules[$cm]->id);
                      } else {
                        $result = \App\CourseResult::getModuleResult($courseUser->id, $courseModules[$cm]->id);
                      }

                      if ($result && $result->complete_status == 'Completed') {
                        $completeCount++;
                      }
                    }
                  }

                  if ($courseConfig->completion_rule == 'all') {
                    if ($completeCount == count($courseModules)) {
                      $returnResult['complete'] = 1;
                      $returnResult['percentage'] = 100;
                    }
                  } else {
                    if ($completeCount > 0) {
                      $returnResult['complete'] = 1;
                      $returnResult['percentage'] = 100;
                    }
                  }
                  break;

                case "any":
                  break;

                case "certain":
                default:
                  $moduleRequireStatus = 0;
                  $percentRequireStatus = 0;
                  $percentCompleted = 0;

                  if (!empty($courseConfig->completion_modules)) {
                    $moduleRequired = explode(",", $courseConfig->completion_modules);
                    $requireCount = 0;
                        
                    if ($courseUser) {
                      for ($mr = 0; $mr < count($moduleRequired); $mr++) {
                        if ($filter == "archive") {
                          $result = \App\CourseResult::getModuleResultWithTrashed($courseUser->id, $moduleRequired[$mr]);
                        } else {
                          $result = \App\CourseResult::getModuleResult($courseUser->id, $moduleRequired[$mr]);
                        }

                        if ($result && $result->complete_status == 'Completed') {
                          $requireCount++;
                        }
                      }
                    }

                    if ($requireCount == count($moduleRequired)) {
                      $moduleRequireStatus = 1;
                    }
                  }

                  if ($courseConfig->completion_percentage != "" &&
                      $courseConfig->completion_percentage > 0) {

                    $percentCompleted = course_percent($course_id, $courseUser, $filter);

                    if ($percentCompleted >= $courseConfig->completion_percentage) {
                      $percentRequireStatus = 1;
                    }
                  } else {
                    //  $percentRequireStatus = 1;
                  }

                  if ($moduleRequireStatus == 1 &&
                      $percentRequireStatus == 1) {
                    $returnResult['complete'] = 1;
                    $returnResult['percentage'] = course_percent($course_id, $courseUser, $filter);
                  }
                  break;
              }
            }

            if (!$courseConfig ||
                ($courseConfig->completion_modules == "" && (
                  $courseConfig->completion_percentage == "" ||
                  $courseConfig->completion_percentage == 0
                ))) {
              
              $percent = course_percent($course_id, $courseUser, $filter);
              $returnResult['percentage'] = $percent;
              
              if ($percent == 100) {
                $returnResult['complete'] = 1;
              }
            }

            return $returnResult;
        }
    }

if(! function_exists('course_percent')){
      function course_percent($course_id, $courseUser, $filter="")
      {

       if($filter == "archive")
       {
         $courseModules = \App\Module::withTrashed()->where('course_id', $course_id)->where('type', 'Elearning')->get();
       }
       else {
         $courseModules = \App\Module::where('course_id', $course_id)->where('type', 'Elearning')->get();
       }
        $completeCount = 0;
        $percentCompleted = 0;
       if($courseUser)
        for($cm=0;$cm<count($courseModules);$cm++)
        {

         if($filter == "archive")
         {
            $result = \App\CourseResult::getModuleResultWithTrashed($courseUser->id, $courseModules[$cm]->id);
         }
         else {
            $result = \App\CourseResult::getModuleResult($courseUser->id, $courseModules[$cm]->id);
         }


          if($result && $result->complete_status == 'Completed')
          {
             $completeCount++;
          }
        }

         $total = count($courseModules);
         if($total > 0)
          $percentCompleted = ($completeCount / $total ) * 100 ;
        return $percentCompleted;
      }
   }
}

if (!function_exists('welcome_template')) {
  function welcome_template($user) {
    $company_id = 0;
    $lang = 'en';

    if ($user && $user->company) {
      $company_id = $user->company_id;
      $lang = $user->company->language ?? 'en';
    }

    $template = \App\WelcomeTemplate::where('company_id', $company_id)
      ->where('language', $lang)->first();

    if (is_null($template)) {
      $template = \App\WelcomeTemplate::where('company_id', $company_id)
        ->where('language', 'en')->first();
    }

    if (is_null($template)) {
      $template = \App\WelcomeTemplate::where('company_id', 0)
        ->where('language', $lang)->first();
    }

    if (is_null($template)) {
      $template = \App\WelcomeTemplate::where('company_id', 0)
        ->where('language', 'en')->first();
    }

    if ($template) {
      return $template->content;
    }

    return '';
  }
}