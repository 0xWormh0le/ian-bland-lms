@extends('layouts.app')

@section('title', $title)

@section('content')
  <div class="row">
    @include('courses.company.details_content', ['show_overview' => true]);
  </div>
@endsection


@include('_plugins.datatables')
