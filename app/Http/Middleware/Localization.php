<?php
namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Company;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
      $locale = \Config::get('app.locale');


      if (\Session::has('locale')) {
            $sessionLocale = \Session::get('locale');
            if (file_exists(resource_path("lang/$sessionLocale"))) {
                   $locale = $sessionLocale;
             }
        }
       else if(Auth::check()){

          //  $dbLocale = Auth::user()->language;
           $dbLocale = null;

           if($dbLocale !="" && !is_null($dbLocale))
           {
             $locale = $dbLocale;
            \Session::put('locale' , $dbLocale);
           }
           else {
               $companyLang = Company::select("language")
                                      ->where("id", Auth::user()->company_id)->first();
               if(@$companyLang->language != "")
               {
                 $locale = $companyLang->language;
                 \Session::put('locale' , $locale);
               } else if (Auth::user()->isSysAdmin()) {
                \Session::put('locale' , $locale);
               }
           }
       }

        \App::setLocale($locale);

        //Carbon locale
        Carbon::setLocale($locale);

        $this->langPath = resource_path('lang/'.$locale);
        if(  \Session::get('old_locale') != $this->langPath)
        {
         Cache::forget('translations');
        }
        Cache::remember('translations',60, function () {
            return collect(File::allFiles($this->langPath))->flatMap(function ($file) {
              \Session::put('old_locale' , $this->langPath);
             if(str_contains($file, ['js']))
              return [
                      ($translation = $file->getBasename('.php')) => trans($translation),
                  ];
              })->toJson();
        });


        return $next($request);
    }
}
