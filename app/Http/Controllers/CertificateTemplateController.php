<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CertificateDesign;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Alert;

class CertificateTemplateController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = CertificateDesign::all();
        $title = session('menuLabel')['configuration.certificate-templates'];
        return view('certificate-templates.index', compact('datas', 'title'));
    }

    /**
     * Create new Design Certificate.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumbs = [
                '' => trans('controllers.create'),
            ];
        $title = trans('controllers.create_new_template');

        $html = (string) view('certificate-templates.starter-template');
        $background = asset('img/starter-certificate.jpg');

        return view('certificate-templates.form', compact('title', 'breadcrumbs', 'background', 'html'));
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
            'name' => 'required',
        ];
        $request->validate($rules);

        $record = new CertificateDesign;
        $record = $this->save($record, $request);

        if($record)
        {
            return redirect()->route('certificate-templates.edit', $record->id);
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
        $data = Company::find($id);
        $breadcrumbs = [
            route('companies.index') => trans('controllers.companies'),
            '' => $data->company_name,
        ];
        $title = trans('controllers.details_of').$data->company_name;
        return view('companies.details', compact('title', 'breadcrumbs', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = CertificateDesign::find($id);
        if($data)
        {
            $breadcrumbs = [
                route('certificate-templates.index') => trans('controllers.list_of_all_templates'),
                route('certificate-templates.preview', $data->id) => $data->name,
                '' => trans('controllers.edit'),
            ];
            $title = trans('controllers.edit_template').$data->name;
            $background = asset('storage/certificates/background/'.$data->background);
            $html = $data->content;
            return view('certificate-templates.form', compact('title', 'breadcrumbs', 'data', 'background', 'html'));
        }
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
            'name' => 'required',
        ];
        $request->validate($rules);

        $record = CertificateDesign::find($id);
        if($record)
        {
            $record = $this->save($record, $request);
            if($record)
            {
                return redirect()->route('certificate-templates.edit', $record->id);
            }
            Alert::error(__('messages.save_failed'))->autoclose(3000);
            return redirect()->back()->withInput();
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    function save($record, $request)
    {
        $filename = '';
        if($request->hasFile('background'))
        {
            if($record->background)
            {
                $oldfile = '/certificates/background/'.$record->background;
                $exists = Storage::disk('public')->exists($oldfile);
                if($exists)
                    Storage::disk('public')->delete($oldfile);
            }
            $file = $request->file('background');
            $ext = $file->getClientOriginalExtension();
        }elseif(!$record->id){
            $path = public_path('img/starter-certificate.jpg');
            $ext = File::extension($path);
            $file = File::get($path);
        }
        if(isset($file))
        {
            $filename = uuid().'.'.$ext;

            $storage = Storage::disk('public')->put('/certificates/background/'.$filename, file_get_contents($file));
        }

        $record->name = $request->name;
        $record->orientation = $request->orientation;
        $record->pagesize = $request->pagesize;
        if($filename !== '')
            $record->background = $filename;
        $record->content = $request->code;

        if($record->id)
            $record->updated_by = \Auth::id();
        else
            $record->created_by = \Auth::id();

        if($record->save())
        {
            return $record;
        }
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
        $record = CertificateDesign::find($id);
        if($record)
        {
            $record->deleted_by = \Auth::id();
            $record->save();
            if($record->delete())
            {
                Alert::success(__('messages.delete_success'))->autoclose(3000);
                return redirect()->route('certificate-templates.index');
            }
            Alert::error(__('messages.delete_failed'))->autoclose(3000);
            return redirect()->route('certificate-templates.index');
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->route('certificate-templates.index');
    }

    /**
     * Preview the Design Certificate.
     *
     * @return \Illuminate\Http\Response
     */
    public function preview($id)
    {
        $data = CertificateDesign::find($id);
        if($data)
        {
            $html = $data->content;
            $trans = [
                '@BACKGROUND' => asset('storage/certificates/background/'.$data->background),
                '@RECIPIENT' => trans('controllers.name'),
                '@COURSETITLE' => trans('controllers.course_title'),
                '@QRCODE' => base64_encode(\QrCode::format('png')->size(100)->generate(route('home'))),
            ];
            $html = strtr($html, $trans);
        }
        else{
            $html = '';
        }

        $pdf = \PDF::loadHTML($html);
        $pdf->setPaper(@$data->pagesize?:'a4', @$data->orientation?:'landscape');

        return $pdf->stream();
    }

    /**
     * Duplicate the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, $id)
    {
        $old = CertificateDesign::find($id);
        if($old)
        {
            $path = public_path('storage/certificates/background/'.$old->background);
            $ext = File::extension($path);
            $file = File::get($path);
            if(isset($file))
            {
                $filename = uuid().'.'.$ext;
                $storage = Storage::disk('public')->put('/certificates/background/'.$filename, $file);
            }

            $record = new CertificateDesign;
            $record->name = $old->name;
            $record->orientation = $old->orientation;
            $record->pagesize = $old->pagesize;
            $record->content = $old->content;
            $record->background = $filename;
            $record->created_by = \Auth::id();

            if($record->save())
            {
                return redirect()->route('certificate-templates.edit', $record->id);
            }
            Alert::error(__('messages.save_failed'))->autoclose(3000);
            return redirect()->back()->withInput();
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Set Publish status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function publish($id)
    {
        $record = CertificateDesign::find($id);
        if($record)
        {
            if($record->draft)
                $record->draft = false;
            else
                $record->draft = true;
            $record->updated_by = \Auth::id();
            $record->save();
            if($record->save())
            {
                return redirect()->route('certificate-templates.index');
            }
            Alert::error(__('messages.delete_failed'))->autoclose(3000);
            return redirect()->route('certificate-templates.index');
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->route('certificate-templates.index');
    }
}
