<?php

namespace lms\Http\Controllers;

use Illuminate\Http\Request;

use lms\Helpers\Services\ScormService;
use lms\Http\Requests;
use lms\Http\Controllers\Controller;
use lms\ScormConfiguration;

class ScormConfigurationsController extends Controller
{
    public $scormService;

    function __construct(ScormService $scormService)
    {
        $this->scormService = $scormService;
    }


    public function scorm()
    {
        $configuration = new ScormConfiguration();
        $validAccount = false;
        $validUrl = false;

        if($this->scormService->isScormConfigurationSet()){
            $configuration = $this->scormService->scormRepository->all()->first();
            $validAccount = $this->scormService->isScormAccountValid();
            $validUrl = $this->scormService->isScormUrlValid();
        }
        //View::make('admin.configuration.scorm')->with('configuration', $configuration)
        //->with('validAccount', $validAccount)->with('validUrl', $validUrl);
        return view('configurations.scorm', compact('configuration', 'validAccount', 'validUrl'));
    }

    public function scormUpdate(Request $request){
        if(!$this->scormService->isScormConfigurationSet()){
            //if($this->scormService->add(Input::all())){
            if($this->scormService->add($request->all() )){
                return redirect()->route('scorm.get')->with('success', trans('messages.success'));
                //return \Redirect::to('configuration/scorm')->with('success', trans('messages.success'));
            }else{
                return redirect()->route('scorm.get')->withErrors($this->scormService->getErrorMessages())->withInput($request->all());
                //return \Redirect::to('configuration/scorm')->withErrors($this->scormService->getErrorMessages())->withInput(\Input::all());
            }
        }else{
            $configuration = $this->scormService->scormRepository->all()->first();
            //if($this->scormService->update($configuration, \Input::all())){
            if($this->scormService->update($configuration, $request->all())){
                return redirect()->route('scorm.get')->with('success', trans('messages.success'));
                //return \Redirect::to('configuration/scorm')->with('success', trans('messages.success'));
            }else{
                return redirect()->route('scorm.get')->withErrors($this->scormService->getErrorMessages());
                //return \Redirect::to('configuration/scorm')->withErrors($this->scormService->getErrorMessages());
            }
        }

    }
}
