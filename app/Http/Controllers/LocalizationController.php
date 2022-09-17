<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;

class LocalizationController extends Controller
{
    public function update($locale)
    {

       $id = Auth::id();
       User::where("id", $id)->update(["language" => $locale]);
       \Session::put('locale' , $locale);
       \Session::forget(['menu', 'menuLabel', 'moduleLabel']);
       return redirect("/");
    }


}
