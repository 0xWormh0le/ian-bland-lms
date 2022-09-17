@push('css')
  <link href="{{asset('vendors/datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
@endpush

@push('js')
  <script src="{{asset('vendors/datepicker/js/bootstrap-datepicker.min.js')}}"></script>
@endpush

@push('scripts')
  <script>
  $(".datepicker").datepicker({
    format: 'dd/mm/yyyy',
  });
</script>
@endpush