@php
    $successMessage = session('success') ?? session('status');
    $errorMessage = session('error');
@endphp

@if ($successMessage || $errorMessage)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if ($successMessage)
                    window.notyf?.success(@json($successMessage));
                @endif

                @if ($errorMessage)
                    window.notyf?.error(@json($errorMessage));
                @endif
            });
        </script>
    @endpush
@endif