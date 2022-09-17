@php
  $exists = explode(',', @$data->role_access);
@endphp

<div class="col-md-9" id="role-tree" data-viewonly="{{isset($viewOnly) ? true:false}}">
  <ul>
    <li id="all" class="jstree-open">@lang('modules.all_roles')
      <ul>
        @foreach($roles as $parent_id => $parent)
        <li id="{{ $parent_id }}" class="jstree-open" {{in_array($parent_id, $exists) ? 'data-checkstate=checked' : ''}}>
          {{ !is_array($parent) ? $parent : $parent['label'] }}
          @if(is_array($parent) && isset($parent['menus']))
          <ul>
            @foreach($parent['menus'] as $child_id => $child)
            <li id="{{ $parent_id.'.'.$child_id }}" class="jstree-open" {{in_array($child_id, $exists) ? 'data-checkstate=checked' : ''}}>
              {{ !is_array($child) ? $child : $child['label'] }}
              @if(is_array($child) && isset($child['menus']))
                <ul>
                @foreach($child['menus'] as $grandchild_id => $grandchild)
                  <li id="{{ $parent_id.'.'.$child_id.'.'.$grandchild_id }}" class="jstree-open" {{in_array($child_id.'.'.$grandchild_id, $exists) ? 'data-checkstate=checked' : ''}}>
                    {{ !is_array($grandchild) ? $grandchild : $grandchild['label'] }}
                  </li>
                @endforeach
                </ul>
              @endif
            </li>
            @endforeach
          </ul>
          @endif
        </li>
        @endforeach
      </ul>
    </li>
  </ul>
</div>

@push('css')
  <link href="{{asset('vendors/pretty-checkbox/pretty-checkbox.min.css')}}" rel="stylesheet">
  <link href="//cdn.materialdesignicons.com/2.6.95/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="{{asset('vendors/jstree/themes/default/style.min.css')}}" rel="stylesheet">
@endpush

@push('js')
  <script src="{{asset('vendors/jstree/jstree.min.js')}}"></script>
@endpush
@push('scripts')
<script>
(function ($, undefined) {
    "use strict";
    $.jstree.plugins.noclose = function () {
        this.close_node = $.noop;
    };
})(jQuery);
$(document).ready(function () {
  $("input:checkbox.parent").prop('checked', true);
  $("input:checkbox").prop('checked', true);

  var tree = $('#role-tree').jstree({
    plugins : ["checkbox", "noclose"],
    core : {
      themes:{
        icons: false,
      },
    }
  });
  $('li[data-checkstate="checked"]').each(function() {
      tree.jstree('check_node', $(this));
  });
  if($('#role-tree').data('viewonly') == true)
  {
    $('#role-tree li').each( function() {
        $("#role-tree").jstree().disable_node(this.id);
    })
  }

  $('#form').submit(function() {
    var checked = tree.jstree(true).get_selected();
    $("#role_access").val(checked);
  });
});
</script>
@endpush
