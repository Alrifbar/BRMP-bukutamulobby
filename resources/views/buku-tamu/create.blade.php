<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::getValue('site_title', 'Buku Tamu Digital') }}</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.className = 'theme-' + savedTheme;
            if (savedTheme === 'dark') {
                document.write('<style>body { background-color: #0F0F0F !important; color: #EAEAEA !important; opacity: 0; }</style>');
            }
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @php 
        $accent = \App\Models\Setting::getValue('accent_color', '#21c16b'); 
        $primary = \App\Models\Setting::getValue('primary_color', '#22c55e');
        $secondary = \App\Models\Setting::getValue('secondary_color', '#3b82f6');
        $background = \App\Models\Setting::getValue('background_color', '#ffffff');
        $text = \App\Models\Setting::getValue('text_color', '#1b1b18');
    @endphp
    <link rel="stylesheet" href="{{ asset('css/buku-tamu.css') }}">
    <style>
        :root {
            --accent-color: {{ $accent }};
            --primary-color: {{ $primary }};
            --secondary-color: {{ $secondary }};
            --background-color: {{ $background }};
            --text-color: {{ $text }};
        }
    </style>
</head>
<body>
    <div class="body-bg" style="background-image: url('{{ asset('images/bc depan.jpg') }}');"></div>
    
    <button class="theme-toggle" id="themeToggle" title="Toggle Theme">
        <i class="bi bi-moon-fill" id="themeIcon"></i>
    </button>
    
    <div class="background-overlay"></div>
    <div class="container">
        <div class="form-card">
            <div class="text-center mb-4">
                <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="Logo" class="logo">
                <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 8px; color: #1a1a1a;">
                    {{ \App\Models\Setting::getValue('welcome_text', 'Buku Tamu Digital') }}
                </h1>
                <p style="font-size: 15px; color: #6b7280;">
                    {{ \App\Models\Setting::getValue('subwelcome_text', 'Silakan isi form di bawah ini') }}
                </p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('buku-tamu.store') }}" id="bukuTamuForm" autocomplete="off">
                @csrf
                
                @php
                    $requiredFields = $fields->where('is_required', true);
                    $optionalFields = $fields->where('is_required', false);
                @endphp

                @if($requiredFields->count() > 0)
                <div class="form-section">
                    <h3 class="section-title">Wajib di isi:</h3>
                    <div class="grid grid-cols-2">
                        @foreach($requiredFields as $field)
                            @include('buku-tamu.partials.dynamic-field', ['field' => $field])
                        @endforeach
                    </div>
                </div>
                @endif

                @if($optionalFields->count() > 0)
                <div style="border-top: 1px solid #e5e7eb; margin: 16px 0;"></div>
                <div style="text-align: center; font-size: 16px; font-weight: 600; color: #6b7280; margin-top: 6px;">Opsional</div>

                <div class="form-section" style="margin-top: 16px;">
                    <h3 class="section-title">Tidak wajib</h3>
                    <div class="grid grid-cols-2">
                        @foreach($optionalFields as $field)
                            @if($field->type !== 'file')
                                @include('buku-tamu.partials.dynamic-field', ['field' => $field])
                            @endif
                        @endforeach
                    </div>
                    
                    {{-- Handle file fields (Selfie) separately if needed for specific UI --}}
                    @foreach($optionalFields->where('type', 'file') as $field)
                        <div class="form-group" style="margin-top: 8px;">
                            <label class="form-label">{{ $field->label }}</label>
                            <div class="camera-container">
                                <video id="video" style="display: none;"></video>
                                <canvas id="canvas" style="display: none;"></canvas>
                                <div id="photo-preview" style="display: none;">
                                    <img id="photo-img" alt="Foto Selfie">
                                </div>
                                <div style="margin-top: 16px;">
                                    <button type="button" id="start-camera" class="btn btn-secondary">
                                        <i class="bi bi-camera-fill me-2"></i> Buka Kamera
                                    </button>
                                    <button type="button" id="take-photo" class="btn btn-primary" style="display: none; margin-top: 8px;">
                                        <i class="bi bi-camera me-2"></i> Ambil Foto
                                    </button>
                                    <button type="button" id="retake-photo" class="btn btn-secondary" style="display: none; margin-top: 8px;">
                                        <i class="bi bi-arrow-clockwise me-2"></i> Ulangi Foto
                                    </button>
                                </div>
                                <input type="hidden" id="{{ $field->name }}" name="{{ $field->name }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

                <div class="form-group mt-6">
                    <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                        <i class="bi bi-send-fill me-2"></i> Submit Buku Tamu
                    </button>
                </div>
            </form>

            <div class="text-center mt-6" style="padding-top: 16px; border-top: 1px solid #e5e7eb;">
                <p style="font-size: 12px; color: #6b7280; margin-bottom: 12px;">
                    © {{ now()->year }} Buku Tamu Digital. All rights reserved.
                </p>
                <a href="{{ route('admin.login') }}" id="adminLoginLink" class="btn btn-secondary" style="display: inline-flex; width: auto; padding: 8px 16px; font-size: 13px;">
                    <i class="bi bi-shield-lock-fill me-2"></i> Login sebagai Admin
                </a>
            </div>
        </div>
    </div>

    <script>
        window.formConfig = {
            requiredFields: @json($requiredFields->pluck('name')),
            allFields: @json($fields->pluck('name'))
        };
    </script>
    <script src="{{ asset('js/buku-tamu.js') }}"></script>
</body>
</html>
