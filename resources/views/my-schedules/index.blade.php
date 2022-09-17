@extends('layouts.app')

@section('title', $title)

@section('content')

<div class="col-md-12">
  <div class="card">
      <div class="card-header"><i class="fa fa-calendar"></i> {{$title}}</div>
      <div class="card-body">
        {!! $calendar->calendar() !!}
      </div>
    </div>
</div>

@endsection

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>
{!! $calendar->script() !!}
@endpush