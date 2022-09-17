@if(validate_role($route))
<a href="{{isset($id)?route($route, $id):route($route)}}" class="btn btn-primary btn-md {{ isset($class) ? $class : '' }}">
  <i class="icon-plus"></i> {{$slot}}
</a>
@endif
