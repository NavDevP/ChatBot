<title>@if(isset($seo) && $seo->isNotEmpty()){{ ucwords($seo[0]->title) }}@else @yield('title',env('APP_NAME'))@endif - {{ env('APP_NAME') }}</title>
@if(isset($seo) && $seo->isNotEmpty())
<link rel="canonical" href="{{ isset($seo) ? $seo[0]->canonical: Request::Url() }}" />

<meta name="robots" content="index, follow">

<meta name="keywords" content="{{ isset($seo) ? ($seo[0]->keywords != null ?implode(',',$seo[0]->keywords): ''): '' }}" />

@if(isset($seo) && $seo[0]->description != null)
<meta property="description" content="{{ isset($seo) ? $seo[0]->description:'' }}" />
@endif


@if(isset($seo) && $seo[0]->fb_type != null)
<meta property="og:type" content="{{ isset($seo) ? $seo[0]->fb_type:'' }}" />
@endif

<meta property="og:title" content="@if(isset($seo)){{ $seo[0]->fb_title }}@else @yield('title')@endif" />

@if(isset($seo) && $seo[0]->fb_description != null)
<meta property="og:description" content="{{ isset($seo) ? $seo[0]->fb_description:'' }}" />
@endif

@if(isset($seo) && $seo[0]->fb_image != null)
<meta property="og:image" content="{{ asset('seo/image').'/'.$seo[0]->fb_image }}" />
@endif

<meta property="og:url" content="{{ isset($seo) ? ($seo[0]->permalink != null ? $seo[0]->permalink: Request::Url()): Request::Url() }}" />

@if(isset($seo) && $seo[0]->fb_site_name != null)
<meta property="og:site_name" content="{{ isset($seo) ? $seo[0]->fb_site_name:'' }}" />
@endif

<meta name="twitter:title" content="@if(isset($seo)){{ $seo[0]->twt_title }}@else @yield('title')@endif">

@if(isset($seo) && $seo[0]->twt_description != null)
<meta name="twitter:description" content="{{ isset($seo) ? $seo[0]->twt_description:'' }}">
@endif

@if(isset($seo) && $seo[0]->twt_image !=null)
<meta name="twitter:image" content="{{ asset('seo/image').'/'.$seo[0]->twt_image }}">
@endif

@if(isset($seo) && $seo[0]->twt_site != null)
<meta name="twitter:site" content="{{ isset($seo) ? $seo[0]->twt_site:'' }}">
@endif

@if(isset($seo) && $seo[0]->fb_creator != null)
<meta name="twitter:creator" content="{{ isset($seo) ? $seo[0]->twt_creator:'' }}">
@endif

@if(isset($seo) && $seo[0]->schema)
<script type='application/ld+json'>
    {{ $seo[0]->schema }}
</script>
@endif
@endif
