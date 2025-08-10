<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>
        @vite('resources/css/app.css')
        <link rel="icon" href="{{ asset('images/mdc-logo.png') }}">
    </head>

    <body>
        <div id="success-message-container" class="absolute top-24 right-4 z-100">
            @if (session('success') || session('error') || session('info') || $errors->any())
                <div id="message"
                    class="p-3 rounded-md shadow-lg border-l-4
                    {{ session('success') ? 'bg-green-100 text-green-700' : '' }}
                    {{ session('error') ? 'bg-red-100 text-red-700' : '' }}
                    {{ session('info') ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $errors->any() ? 'bg-red-100 text-red-700' : '' }}">

                    {{-- Display session messages --}}
                    @if (session('success'))
                        <p>{{ session('success') }}</p>
                    @endif
                    @if (session('error'))
                        <p>{{ session('error') }}</p>
                    @endif
                    @if (session('info'))
                        <p>{{ session('info') }}</p>
                    @endif

                    {{-- Display validation errors --}}
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>

                <script>
                    setTimeout(() => {
                        const messageDiv = document.getElementById('message');
                        if (messageDiv) {
                            messageDiv.classList.add('opacity-0');
                            setTimeout(() => {
                                messageDiv.style.display = 'none';
                            }, 500);
                        }
                    }, 5000);
                </script>
            @endif
        </div>
        @include('components.navbar')

        {{-- Main Content --}}
        <main class="flex-grow container mx-auto px-4 py-6">
            @yield('content')
        </main>

        <script src="//unpkg.com/alpinejs" defer></script>

        <!-- FilePond CSS -->
        <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">

        <!-- FilePond JS -->
        <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

        <script>
            FilePond.registerPlugin(); // optional if you're using plugins
            FilePond.parse(document.body);
        </script>

        @yield('scripts')
        <x-upload-progress-modal />
    </body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function () {

  function clearErrors(form){
    form.querySelectorAll('.field-error').forEach(n => n.remove());
    form.querySelectorAll('.is-invalid').forEach(i => i.classList.remove('is-invalid'));
  }

  function showErrors(form, errors) {
    // flatten messages and alert as fallback
    const messages = [];
    for (let key in errors) {
      messages.push(...errors[key]);
      // try insert small message after the input if exists
      let base = key.split('.')[0]; // handle office_days.0 etc
      let input = form.querySelector(`[name="${key}"], [name="${base}"], [name="${base}[]"]`);
      if (input) {
        input.classList.add('is-invalid');
        const small = document.createElement('small');
        small.className = 'field-error text-red-600';
        small.innerText = errors[key][0];
        input.parentNode.insertBefore(small, input.nextSibling);
      }
    }
    if (messages.length) alert(messages.join("\n"));
  }

  document.querySelectorAll('form[data-upload]').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      clearErrors(form);

      const formData = new FormData(form);
      const xhr = new XMLHttpRequest();

      // dispatch start event (component listens)
      window.dispatchEvent(new CustomEvent('upload-start'));

      // headers
      const token = document.head.querySelector('meta[name="csrf-token"]')?.content;
      xhr.open((form.method || 'POST').toUpperCase(), form.action);

      if (token) xhr.setRequestHeader('X-CSRF-TOKEN', token);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhr.setRequestHeader('Accept', 'application/json');

      xhr.upload.addEventListener('progress', function (ev) {
        if (ev.lengthComputable) {
          const percent = Math.round((ev.loaded / ev.total) * 100);
          window.dispatchEvent(new CustomEvent('upload-progress', { detail: { progress: percent } }));
        }
      });

      xhr.addEventListener('load', function () {
        window.dispatchEvent(new CustomEvent('upload-finish'));
        // success HTTP 2xx
        if (xhr.status >= 200 && xhr.status < 300) {
          try {
            const json = JSON.parse(xhr.responseText || '{}');
            if (json.redirect) {
              window.location.href = json.redirect;
              return;
            }
          } catch (err) { /* not JSON; ignore */ }
          // fallback: reload
          window.location.reload();
          return;
        }

        // Validation errors (Laravel returns 422 with JSON)
        if (xhr.status === 422) {
          let payload = {};
          try { payload = JSON.parse(xhr.responseText); } catch(e){}
          if (payload.errors) {
            showErrors(form, payload.errors);
            return;
          }
        }

        // other errors
        alert('Upload failed. Please try again.');
      });

      xhr.addEventListener('error', function () {
        window.dispatchEvent(new CustomEvent('upload-finish'));
        alert('Network error. Upload failed.');
      });

      xhr.send(formData);
    });
  });
});
</script>

