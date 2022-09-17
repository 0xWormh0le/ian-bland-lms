<form action="{{ route($route, $params) }}" method="POST" style="display:inline;">
  @method('DELETE')
  @csrf()

  <button type="submit" class="btn btn-md btn-danger delete {{ isset($class) ? $class : '' }}" title="@lang('modules.delete')">
    <i class="icon-trash"></i> @lang('modules.delete')
  </button>
</form>
