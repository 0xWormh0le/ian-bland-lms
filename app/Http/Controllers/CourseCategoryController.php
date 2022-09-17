<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CourseCategory;
use Yajra\Datatables\Datatables;
use Alert;

class CourseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = 0)
    {

      $lang = 0 ;

     if($id > 0)
     {
      $lang=1;
      $parent =  CourseCategory::where("id", $id)->first();
     }
     else {
       $parent = '';
     }

      $title = @session('menuLabel')['courses'] ?: session('menuLabel')['course-management'];

      $breadcrumbs = [route('category.index') => trans('controllers.category')];
      $breadcrumbs = array_merge($breadcrumbs, [route('category.index') => trans('controllers.category')]);

    if($id > 0){
      $breadcrumbs = array_merge($breadcrumbs, [route('category.show', $parent->id) => $parent->title]);
     }

        return view('courses.category.index',compact('title', 'breadcrumbs', 'parent', 'id', 'lang'));
    }

    public function list($id = 0)
    {
      $data = CourseCategory::where("parent", $id)->get();

      return Datatables::of($data)
          ->addColumn('action', function ($data) {
              $return = "";
             if($data->parent == 0)
              $return .= show_button('subcategory', 'category.index', $data->id)." ";

              $return .= show_button('show', 'category.show', $data->id)
                  ." ".
                  show_button('edit', 'category.edit', $data->id)
                  ." ".
                  show_button('delete', 'category.destroy', encrypt($data->id));

              return $return;
          })
          ->rawColumns(['action'])
          ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id=0)
    {

      $lang = 0 ;
      if($id > 0)
      {
        $lang=1;
        $parent = CourseCategory::where('id', $id)->first();
      }

      $title = trans_choice('controllers.add_category', $lang);

      $breadcrumbs = array();
      $breadcrumbs = array_merge($breadcrumbs, [route('category.index')=> trans('controllers.category')]);

      if($id > 0){

        $breadcrumbs = array_merge($breadcrumbs, [route('category.show', $parent->id) => $parent->title]);
       }

       $breadcrumbs = array_merge($breadcrumbs, [route('category.create') => trans_choice('controllers.add_category', $lang)]);

      //$breadcrumbs = ['' => $title];

          return view('courses.category.form',compact('title', 'breadcrumbs', 'id'));
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


      $record = new CourseCategory;
      $record->title = $request->title;
      $record->parent = $request->parent;

      $record = $record->save();
      if($record)
      {
          Alert::success(__('messages.save_success'))->autoclose(3000);
          return redirect()->route('category.index', $request->parent);
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
      $parent = '' ;
      $data = CourseCategory::find($id);
      if($data->parent > 0)
       $parent = CourseCategory::find($data->parent);
      if($data)
      {
          $breadcrumbs = [
              route('category.index') => trans('controllers.category'),
              '' => $data->title,
          ];
          $breadcrumbs = array();
          $breadcrumbs = array_merge($breadcrumbs, [route('category.index') => trans('controllers.category')]);

        if($data->parent > 0){
          $breadcrumbs = array_merge($breadcrumbs, [route('category.show', $parent->id) => $parent->title]);
          $breadcrumbs = array_merge($breadcrumbs, [route('category.index',$data->parent) => trans_choice('controllers.subcategory', 0)]);
        }
          $breadcrumbs = array_merge($breadcrumbs, ['' => $data->title]);


          $title = trans('controllers.details_of').$data->title;
          return view('courses.category.details', compact('title', 'breadcrumbs', 'data', 'parent'));
      }
      Alert::error(__('messages.invalid_request'));
      return redirect()->route('category.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

      $data =  CourseCategory::find($id);
    //  $parents = CourseCategory::where('parent', 0)->get();

     if($data->parent > 0)
      $parentDetail = CourseCategory::where('id', $data->parent)->first();
      if($data)
      {
          $breadcrumbs = array();
          $breadcrumbs = array_merge($breadcrumbs, [route('category.index') => trans('controllers.category')]);

        if($data->parent > 0){
          $breadcrumbs = array_merge($breadcrumbs, [route('category.show', $parentDetail->id) => $parentDetail->title]);
          $breadcrumbs = array_merge($breadcrumbs, [route('category.index',$data->parent) => trans_choice('controllers.subcategory', 0)]);
        }
          $breadcrumbs = array_merge($breadcrumbs, [route('category.show', $data->id) => $data->title]);
          $breadcrumbs = array_merge($breadcrumbs, ['' => trans('controllers.edit')]);

          $title = trans('controllers.edit').' '.$data->title;
          return view('courses.category.form', compact('title', 'breadcrumbs', 'data'));
      }
      Alert::error(__('messages.invalid_request'));
      return redirect()->route('category.index', $data->parent);
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
          'title' => 'required'
      ];
      $request->validate($rules);


      $record = CourseCategory::find($id);
      $record->title = $request->title;
      $record->parent = $request->parent;

      if($record)
      {
          $record = $record->save();
          if($record)
          {
              Alert::success(__('messages.save_success'))->autoclose(3000);
              return redirect()->route('category.index', $request->parent);
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
      $record = CourseCategory::find(decrypt($id));
      if($record)
      {
          $record->deleted_by = \Auth::id();
          $record->save();
          if($record->delete())
          {
              Alert::success(__('messages.delete_success'))->autoclose(3000);
              return redirect()->route('category.index', $record->parent);
          }
          Alert::error(__('messages.delete_failed'))->autoclose(3000);
          return redirect()->route('category.index', $record->parent);
      }
      Alert::error(__('messages.record_not_found'))->autoclose(3000);
      return redirect()->route('category.index', $record->parent);
    }

    public function subCategories(Request $request)
    {
      $category = new CourseCategory;
      $data = $category->subCategories($request->category_id);
      return response()->json($data);
    }


}
