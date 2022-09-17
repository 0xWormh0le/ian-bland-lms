@push('css')
  <link href="{{asset('vendors/select2/css/select2.min.css')}}" rel="stylesheet">
  <link href="{{asset('vendors/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet">
@endpush

@push('js')
  <script src="{{asset('vendors/select2/js/select2.min.js')}}"></script>
@endpush

@push('scripts')
  <script>
  $(".select2").select2();
</script>
@endpush