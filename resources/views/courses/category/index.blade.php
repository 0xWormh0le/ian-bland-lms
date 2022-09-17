@extends('layouts.app')

@section('title', $title)

@section('content')

<div class="col-sm-12">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-sm-6">
          <i class="icon-layers"></i> {{$parent?ucfirst($parent->title):''}} {{$parent?trans_choice('modules.subcategory', 1) : trans('modules.course_categories')}}
        </div>
        <div class="col-sm-6 text-right">
          @buttonAdd(['route'=>'category.create', 'id' => $id])
            {{trans_choice("modules.add_new_category", $lang)}}
          @endbuttonAdd
        </div>
      </div>
    </div>
    <div class="card-body">
      <table class="table table-bordered datatable" id="categoriesTable" style='border-collapse: collapse !important'>
        <thead>
          <tr>
            <th>@lang('modules.title')</th>
            <th>@lang('modules.date_added')</th>
            <th></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection


@include('_plugins.datatables')
@push('scripts')
<script>
$(document).ready(function() {


    var companiesTable = $('#categoriesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('category.list', $parent?$parent->id:0) !!}",
        columns: [
            { data: 'title', name: 'title',  width: 180 },
            { data: 'created_at', name: 'created_at', width: 100, class:'text-center', render: renderColumnDate },
            { data: 'action', name: 'action', sortable:false, class:'text-center', width: 170 },
        ],
    });


});
</script>
@endpush
