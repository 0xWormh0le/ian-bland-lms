@extends('layouts.app')

@section('content')
<h2 class='text-center' style='font-size:10em; font-weight: 100;'>401</h2>
<h2>{{ $exception->getMessage() }}</h2>
@endsection
