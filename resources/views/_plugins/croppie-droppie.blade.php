@push('css')
  <link href="{{asset('vendors/croppie-droppie/croppie.css')}}" rel="stylesheet">
  <link href="{{asset('vendors/croppie-droppie/croppie_droppie.css')}}" rel="stylesheet">
@endpush

@push('js')
  <script src="{{asset('vendors/croppie-droppie/croppie.js')}}"></script>
  <script src="{{asset('vendors/croppie-droppie/exif.js')}}"></script>
  <script src="{{asset('vendors/croppie-droppie/croppie_droppie.js')}}"></script>
@endpush

@push('scripts')
<script>
  $(function(){
		// Initialization croppie droppie
		croppie_droppie($('#croppie_droppie'), $('#loader'));
	})
</script>
@endpush