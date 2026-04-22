{{-- Global CSS Variables for User Theme --}}
@php
    // Get settings from database
    $colors = [
        'primary_color' => \App\Models\Setting::getValue('primary_color', '#667eea'),
        'secondary_color' => \App\Models\Setting::getValue('secondary_color', '#764ba2'),
        'accent_color' => \App\Models\Setting::getValue('accent_color', '#21c16b'),
        'background_color' => \App\Models\Setting::getValue('background_color', '#f3f6fb'),
        'text_color' => \App\Models\Setting::getValue('text_color', '#1b1b18'),
    ];
    
    $fonts = [
        'heading_font' => \App\Models\Setting::getValue('heading_font', 'Inter, sans-serif'),
        'body_font' => \App\Models\Setting::getValue('body_font', 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif'),
        'font_size_base' => \App\Models\Setting::getValue('font_size_base', '16px'),
    ];
    
    $content = [
        'site_title' => \App\Models\Setting::getValue('site_title', 'Buku Tamu Digital'),
        'site_description' => \App\Models\Setting::getValue('site_description', 'Sistem buku tamu digital modern'),
        'footer_text' => \App\Models\Setting::getValue('footer_text', '© 2024 Buku Tamu Digital. All rights reserved.'),
    ];
@endphp

<style>
    :root {
        --primary-color: {{ $colors['primary_color'] }};
        --secondary-color: {{ $colors['secondary_color'] }};
        --accent-color: {{ $colors['accent_color'] }};
        --background-color: {{ $colors['background_color'] }};
        --text-color: {{ $colors['text_color'] }};
        --heading-font: {{ $fonts['heading_font'] }};
        --body-font: {{ $fonts['body_font'] }};
        --font-size-base: {{ $fonts['font_size_base'] }};
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: var(--body-font) !important;
        font-size: var(--font-size-base) !important;
        line-height: 1.6;
        color: var(--text-color) !important;
        background: url('{{ \App\Models\Setting::getBackgroundUrl() }}') no-repeat center center fixed;
        background-size: cover;
        min-height: 100vh;
        position: relative;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: -1;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: var(--heading-font) !important;
        color: var(--text-color) !important;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    h1 { font-size: calc(var(--font-size-base) * 2.5); }
    h2 { font-size: calc(var(--font-size-base) * 2); }
    h3 { font-size: calc(var(--font-size-base) * 1.75); }
    h4 { font-size: calc(var(--font-size-base) * 1.5); }
    h5 { font-size: calc(var(--font-size-base) * 1.25); }
    h6 { font-size: var(--font-size-base); }

    /* Form Container */
    .form-container {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
        max-width: 600px;
        width: 100%;
        margin: 40px auto;
        position: relative;
        z-index: 1;
    }

    /* Header Section */
    .form-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .form-header .logo {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        border-radius: 20px;
        object-fit: cover;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        animation: logoFloat 4s ease-in-out infinite;
    }

    @keyframes logoFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(5deg); }
    }

    .form-header h1 {
        font-size: calc(var(--font-size-base) * 2);
        font-weight: 700;
        margin-bottom: 8px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-header p {
        opacity: 0.8;
        font-size: calc(var(--font-size-base) * 0.875);
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-color);
        font-size: calc(var(--font-size-base) * 0.875);
    }

    .form-control {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid rgba(102, 126, 234, 0.2);
        border-radius: 12px;
        font-size: var(--font-size-base);
        font-family: var(--body-font) !important;
        background: rgba(255, 255, 255, 0.9);
        color: var(--text-color) !important;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }

    .form-control::placeholder {
        color: var(--text-color);
        opacity: 0.5;
    }

    select.form-control {
        cursor: pointer;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* Button Styles */
    .btn {
        font-family: var(--body-font) !important;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 16px 32px;
        font-weight: 600;
        font-size: var(--font-size-base);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:active {
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.9);
        color: var(--text-color);
        border: 2px solid rgba(102, 126, 234, 0.2);
    }

    .btn-secondary:hover {
        background: white;
        border-color: var(--primary-color);
        transform: translateY(-2px);
    }

    /* Success Page Styles */
    .success-container {
        text-align: center;
        padding: 60px 40px;
    }

    .success-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 30px;
        background: linear-gradient(135deg, var(--accent-color), #0a9b4e);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        color: white;
        animation: successPulse 2s ease-in-out infinite;
    }

    @keyframes successPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .success-title {
        font-size: calc(var(--font-size-base) * 2.5);
        font-weight: 700;
        margin-bottom: 16px;
        background: linear-gradient(135deg, var(--accent-color), #0a9b4e);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .success-message {
        font-size: calc(var(--font-size-base) * 1.125);
        margin-bottom: 40px;
        opacity: 0.9;
    }

    /* Alert Styles */
    .alert {
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-family: var(--body-font) !important;
    }

    .alert-success {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    /* Footer */
    .form-footer {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(102, 126, 234, 0.1);
        color: var(--text-color);
        opacity: 0.8;
        font-size: calc(var(--font-size-base) * 0.875);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-container {
            margin: 20px;
            padding: 30px 20px;
        }

        .form-header h1 {
            font-size: calc(var(--font-size-base) * 1.75);
        }

        .btn {
            width: 100%;
            padding: 18px;
        }
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--secondary-color);
    }

    /* Camera Styles */
    .camera-container {
        border: 2px solid rgba(102, 126, 234, 0.2);
        border-radius: 12px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.9);
        text-align: center;
    }

    #video, #canvas, #photo-img {
        border-radius: 8px;
        width: 100%;
        max-width: 320px;
        height: auto;
        margin: 0 auto 15px;
        display: block;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    #camera-controls {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }

    #camera-controls .btn {
        padding: 12px 20px;
        font-size: calc(var(--font-size-base) * 0.875);
        min-width: 120px;
    }

    .btn-success {
        background: linear-gradient(135deg, var(--accent-color), #0a9b4e);
        color: white;
        box-shadow: 0 10px 25px rgba(33, 193, 107, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(33, 193, 107, 0.4);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
    }

    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(245, 158, 11, 0.4);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    .btn:disabled:hover {
        transform: none !important;
        box-shadow: none !important;
    }
</style>
