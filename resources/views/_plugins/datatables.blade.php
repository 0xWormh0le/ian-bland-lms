@push('css')
  <link href="{{asset('vendors/dataTables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link href="{{asset('vendors/dataTables/responsive.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@push('scripts')
  <script src="{{asset('vendors/dataTables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('vendors/dataTables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="{{asset('vendors/dataTables/dataTables.responsive.min.js')}}"></script>
  <script src="{{asset('vendors/dataTables/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('vendors/dataTables/jszip.min.js')}}"></script>
  <script src="{{asset('vendors/dataTables/buttons.html5.min.js')}}"></script>
@endpush