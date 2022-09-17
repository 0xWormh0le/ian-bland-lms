<!-- Breadcrumb-->
<ol class="breadcrumb">
  @if(Route::currentRouteName() == 'Home')
    <li class="breadcrumb-item active text-capitalize">@lang("modules.dashboard")</li>
  @else
    <li class="breadcrumb-item text-capitalize">
      <a href="{{route('home')}}">@lang("modules.dashboard")</a>
    </li>

    @isset($breadcrumbs)
      @foreach($breadcrumbs as $url => $label)
        <li class="breadcrumb-item text-capitalize">
        @if($url !== '')
          <a href="{{$url}}">{{$label}}</a>
        @else
          {{$label}}
        @endif
        </li>
      @endforeach
    @else
      @php
        $routes = explode('.', Route::currentRouteName());
        $lastRoute = array_pop($routes);
        $parent = implode('.', $routes);
      @endphp

      @if(Route::has($parent.'.index') && $parent!=='home'  && $lastRoute!=='index')
      <li class="breadcrumb-item text-capitalize">
        <a href="{{route($parent.'.index')}}">{{read_slug($parent)}}</a>
      </li>
      @endif
      @if($lastRoute)
      <li class="breadcrumb-item text-capitalize">
        {{($lastRoute == 'index' ? read_slug($parent) :  read_slug($lastRoute))}}
      </li>
      @endif
    @endisset
  @endif
</ol>
