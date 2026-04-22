/**
 * Buku Tamu Digital - Dynamic Script
 */

let stream = null;

// Utility for debouncing
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

function startCamera() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Browser Anda tidak mendukung akses kamera.');
        return;
    }

    navigator.mediaDevices.getUserMedia({ video: { width: 320, height: 240 } })
        .then(s => {
            stream = s;
            const video = document.getElementById('video');
            video.srcObject = stream;
            video.play();
            video.style.display = 'block';
            document.getElementById('start-camera').style.display = 'none';
            document.getElementById('take-photo').style.display = 'inline-block';
        })
        .catch(err => {
            console.error('Camera Error:', err);
            alert('Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.');
        });
}

function takePhoto() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    canvas.width = 320;
    canvas.height = 240;
    canvas.getContext('2d').drawImage(video, 0, 0, 320, 240);
    const data = canvas.toDataURL('image/jpeg', 0.8); 
    document.getElementById('photo-img').src = data;
    
    // Find any hidden input that might be for selfie_photo
    const selfieInput = document.getElementById('selfie_photo') || document.querySelector('input[type="hidden"][name*="selfie"]');
    if (selfieInput) {
        selfieInput.value = data;
    }
    
    video.style.display = 'none';
    document.getElementById('take-photo').style.display = 'none';
    document.getElementById('photo-preview').style.display = 'block';
    document.getElementById('retake-photo').style.display = 'inline-block';
    
    if (stream) {
        stream.getTracks().forEach(t => t.stop());
        stream = null;
    }
    checkForm();
}

function retakePhoto() {
    document.getElementById('photo-preview').style.display = 'none';
    document.getElementById('retake-photo').style.display = 'none';
    const selfieInput = document.getElementById('selfie_photo') || document.querySelector('input[type="hidden"][name*="selfie"]');
    if (selfieInput) {
        selfieInput.value = '';
    }
    startCamera();
    checkForm();
}

function checkForm() {
    const config = window.formConfig || { requiredFields: [], allFields: [] };
    const required = config.requiredFields;
    let valid = true;

    for (const name of required) {
        const el = document.getElementsByName(name)[0] || document.getElementById(name);
        if (!el || !el.value) {
            valid = false;
            break;
        }
    }

    // Custom logic for numeric fields like 'usia' if it exists
    const usiaEl = document.getElementsByName('usia')[0] || document.getElementById('usia');
    if (valid && usiaEl && usiaEl.value) {
        const usiaNum = parseInt(usiaEl.value, 10);
        if (isNaN(usiaNum) || usiaNum <= 0) {
            valid = false;
        }
    }

    document.getElementById('submit-btn').disabled = !valid;

    // Update visual indicators for all fields
    config.allFields.forEach(name => {
        const el = document.getElementsByName(name)[0] || document.getElementById(name);
        if (el) {
            if (el.type === 'file') {
                // Special handling for hidden selfie input
                const preview = document.getElementById('photo-preview');
                const group = document.getElementById(name + '_group') || el.closest('.form-group');
                if (preview && preview.style.display !== 'none') {
                    if (group) group.classList.add('valid-field');
                } else {
                    if (group) group.classList.remove('valid-field');
                }
            } else if (el.value && el.value.trim() !== '') {
                el.classList.add('is-valid');
            } else {
                el.classList.remove('is-valid');
            }
        }
    });
}

const debouncedCheckForm = debounce(checkForm, 100);

// Theme Synchronization
function applyTheme(theme) {
    const body = document.body;
    const themeIcon = document.getElementById('themeIcon');
    
    body.classList.remove('light-mode', 'dark-mode');
    document.documentElement.className = 'theme-' + theme;
    
    if (theme === 'dark') {
        body.classList.add('dark-mode');
        body.style.opacity = '1'; // Ensure body is visible after theme applied
        if (themeIcon) themeIcon.className = 'bi bi-sun-fill';
    } else {
        body.classList.add('light-mode');
        body.style.opacity = '1';
        if (themeIcon) themeIcon.className = 'bi bi-moon-fill';
    }
    
    localStorage.setItem('buku-tamu-theme', theme);
    localStorage.setItem('theme', theme);
}

document.addEventListener('DOMContentLoaded', function() {
    const config = window.formConfig || { requiredFields: [], allFields: [] };
    
    // Camera listeners
    const startCamBtn = document.getElementById('start-camera');
    if (startCamBtn) startCamBtn.onclick = startCamera;
    
    const takePhotoBtn = document.getElementById('take-photo');
    if (takePhotoBtn) takePhotoBtn.onclick = takePhoto;
    
    const retakePhotoBtn = document.getElementById('retake-photo');
    if (retakePhotoBtn) retakePhotoBtn.onclick = retakePhoto;
    
    // Form validation on input/change
    config.allFields.forEach(name => {
        const elements = document.getElementsByName(name);
        elements.forEach(el => {
            el.addEventListener('input', debouncedCheckForm);
            el.addEventListener('change', checkForm);
            el.setAttribute('autocomplete', 'off');
        });
    });
    
    // Theme toggle
    const themeToggleBtn = document.getElementById('themeToggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            const currentTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });
    }
    
    const savedTheme = localStorage.getItem('theme') || localStorage.getItem('buku-tamu-theme') || 'light';
    applyTheme(savedTheme);

    const form = document.getElementById('bukuTamuForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            checkForm();
            if (document.getElementById('submit-btn').disabled) {
                e.preventDefault();
                alert('Mohon lengkapi semua bidang wajib.');
            }
        });
    }

    setTimeout(checkForm, 500);
    setTimeout(checkForm, 2000);
    checkForm();
});
