<!DOCTYPE html>
<html {{ language_attributes() }} class="no-js">
  <head>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite('resources/js/frontend/app.js')
    @endif
  </head>
  <body>
    <main>
      @yield('content')
    </main>
  </body>
</html>
