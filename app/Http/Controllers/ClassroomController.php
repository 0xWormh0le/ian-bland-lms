<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Module;
use App\Classroom;
use Alert;


class ClassroomController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($slug)
    {
        $title = trans('controllers.add_new').session('moduleLabel')['classroom'];
        $course = Course::findBySlug($slug);
        if($course)
        {
            $breadcrumbs = [
                route('courses.index') => trans('controllers.list_of_courses'),
                route('courses.show', $course->slug) => $course->title,
                '' => $title,
            ];
            return view('classrooms.form', compact('title', 'course', 'breadcrumbs'));
        }
        return redirect()->route('courses.index');
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
        ];
        $request->validate($rules);

        $record = new Module;
        $record = $this->save($record, $request);
        if($record)
        {
            Alert::success(__('messages.save_success'))->autoclose(3000);
            return redirect()->route('courses.show', $record->course->slug);
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
    public function show($course, $slug)
    {
        $data = Module::findBySlug($slug);
        if($data)
        {
            $title = trans('controllers.details_of').$data->type .' : '.$data->title;
            $breadcrumbs = [
                route('courses.index') => trans('controllers.list_of_courses'),
                route('courses.show', $data->course->slug) => $data->course->title,
                '' => $data->title,
            ];
            return view('classrooms.details', compact('title', 'breadcrumbs', 'data'));
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
                route('courses.index') => trans('controllers.list_of_courses'),
                route('courses.show', $course) => $data->course->title,
                route('classrooms.show', ['course'=>$course, 'slug'=>$slug]) => $data->title,
                '' => trans('controllers.edit'),
            ];
            $title = trans('controllers.edit_classroom').$data->title;
            return view('classrooms.form', compact('title', 'breadcrumbs', 'data'));
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
        if($record)
        {
            $record = $this->save($record, $request);
            if($record)
            {
                Alert::success(__('messages.save_success'))->autoclose(3000);
                return redirect()->route('classrooms.show', ['course'=>$record->course->slug, 'slug'=>$record->slug]);
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
        $record->course_id = $request->course_id;
        $record->title = $request->title;
        $record->order_no = $request->order_no;
        $record->type = 'Classroom';
        if($record->id)
        {
            $record->slug = null;
            $record->updated_by = $request->user()->id;
        }else{
            $record->created_by = $request->user()->id;
        }
        if($record->save())
            return $record;
        return false;
    }

}
