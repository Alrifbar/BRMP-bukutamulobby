// Update time information
function updateTimeInfo() {
    const now = new Date();
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const dayName = days[now.getDay()];
    const dateStr = `${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
    const timeStr = now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit'
    });
    
    // Update time display elements if they exist
    const currentDayElement = document.getElementById('currentDay');
    const currentDateElement = document.getElementById('currentDate');
    const currentTimeElement = document.getElementById('currentTime');
    
    if (currentDayElement) currentDayElement.textContent = dayName;
    if (currentDateElement) currentDateElement.textContent = dateStr;
    if (currentTimeElement) currentTimeElement.textContent = timeStr;
}

// Update time every second
setInterval(updateTimeInfo, 1000);

// Initialize time info on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTimeInfo();
});

// Custom Header Functions
function toggleDateDropdown() {
    const dropdown = document.querySelector('.date-dropdown');
    dropdown.classList.toggle('active');
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function closeDropdown(e) {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

function setDateRange(range) {
    const dateRangeElement = document.getElementById('selectedDateRange');
    const today = new Date();
    let startDate, endDate, displayText;
    
    switch(range) {
        case 'today':
            startDate = endDate = today;
            displayText = today.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            break;
        case 'last7days':
            startDate = new Date(today);
            startDate.setDate(startDate.getDate() - 7);
            endDate = today;
            displayText = `${startDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })} - ${endDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}`;
            break;
        case 'last30days':
            startDate = new Date(today);
            startDate.setDate(startDate.getDate() - 30);
            endDate = today;
            displayText = `${startDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })} - ${endDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}`;
            break;
        case 'thisMonth':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            displayText = `${startDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })} - ${endDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}`;
            break;
        case 'lastMonth':
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            displayText = `${startDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })} - ${endDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}`;
            break;
        case 'custom':
            // Open custom date picker modal
            openCustomDateModal();
            return;
        default:
            return;
    }
    
    dateRangeElement.textContent = displayText;
    
    // Close dropdown
    document.querySelector('.date-dropdown').classList.remove('active');
    
    // Update charts based on date range
    updateChartsForDateRange(startDate, endDate);
    
    showNotification(`Rentang tanggal diperbarui ke ${displayText}`, 'success');
}

function openCustomDateModal() {
    // Create a modal with Date Range Picker
    const modal = document.createElement('div');
    modal.className = 'modal show';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <span>📅</span>
                    <span>Rentang Tanggal Kustom</span>
                </div>
                <button class="close-btn" onclick="this.closest('.modal').remove()">&times;</button>
            </div>
            
            <form id="customDateForm">
                <div class="form-group">
                    <label class="form-label">Pilih Rentang Tanggal</label>
                    <input type="text" id="dateRangePicker" class="form-control" style="cursor: pointer;" readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Pemilihan Cepat</label>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                        <button type="button" class="btn btn-secondary" onclick="setQuickRange(7)">7 Hari Terakhir</button>
                        <button type="button" class="btn btn-secondary" onclick="setQuickRange(30)">30 Hari Terakhir</button>
                        <button type="button" class="btn btn-secondary" onclick="setQuickRange('month')">Bulan Ini</button>
                        <button type="button" class="btn btn-secondary" onclick="setQuickRange('year')">Tahun Ini</button>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="applyCustomDateRange()">Terapkan</button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Initialize Date Range Picker
    const today = new Date();
    const thirtyDaysAgo = new Date(today);
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    
    $('#dateRangePicker').daterangepicker({
        startDate: thirtyDaysAgo,
        endDate: today,
        locale: {
            format: 'DD MMM YYYY',
            separator: ' - ',
            applyLabel: 'Terapkan',
            cancelLabel: 'Batal',
            fromLabel: 'Dari',
            toLabel: 'Sampai',
            customRangeLabel: 'Kustom',
            weekLabel: 'M',
            daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            firstDay: 1
        },
        ranges: {
            'Hari Ini': [today, today],
            'Kemarin': [new Date(today.getTime() - 24 * 60 * 60 * 1000), new Date(today.getTime() - 24 * 60 * 60 * 1000)],
            '7 Hari Terakhir': [new Date(today.getTime() - 6 * 24 * 60 * 60 * 1000), today],
            '30 Hari Terakhir': [new Date(today.getTime() - 29 * 24 * 60 * 60 * 1000), today],
            'Bulan Ini': [new Date(today.getFullYear(), today.getMonth(), 1), new Date(today.getFullYear(), today.getMonth() + 1, 0)],
            'Bulan Lalu': [new Date(today.getFullYear(), today.getMonth() - 1, 1), new Date(today.getFullYear(), today.getMonth(), 0)]
        }
    }, function(start, end, label) {
        // This callback fires when date range is selected
        console.log('Date range selected:', start, end, label);
    });
}

function applyCustomDateRange() {
    const picker = $('#dateRangePicker').data('daterangepicker');
    const startDate = picker.startDate.toDate();
    const endDate = picker.endDate.toDate();
    
    const displayText = `${startDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })} - ${endDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}`;
    
    document.getElementById('selectedDateRange').textContent = displayText;
    
    // Close modal
    document.querySelector('.modal').remove();
    
    // Update charts
    updateChartsForDateRange(startDate, endDate);
    
    showNotification(`Rentang tanggal kustom diterapkan: ${displayText}`, 'success');
}

function updateChartsForDateRange(startDate, endDate) {
    // Update all charts based on selected date range
    console.log('Updating charts for date range:', startDate, 'to', endDate);
    
    // Format dates for API call
    const formattedStartDate = startDate.toISOString().split('T')[0];
    const formattedEndDate = endDate.toISOString().split('T')[0];
    
    console.log('Formatted dates:', {
        start_date: formattedStartDate,
        end_date: formattedEndDate
    });
    
    // Show loading notification
    showNotification('Memperbarui grafik...', 'info');
    
    // Fetch real data from backend
    fetch(`/admin/grafik-filter?start_date=${formattedStartDate}&end_date=${formattedEndDate}`)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            
            // Update monthly chart
            updateMonthlyChart(data.labelsBulanan, data.grafikBulanan);
            
            // Update daily chart
            updateDailyChart(data.labelsHarian, data.grafikHarian);
            
            // Update category chart
            updateCategoryChart(data.labelsKeperluan, data.grafikKeperluan);
            
            // Update yearly chart
            updateYearlyChart(data.labelsTahunan, data.grafikTahunan);
            
            showNotification('Grafik berhasil diperbarui!', 'success');
        })
        .catch(error => {
            console.error('Error fetching chart data:', error);
            showNotification('Gagal memperbarui grafik', 'error');
        });
}

function updateMonthlyChart(labels, data) {
    const chart = Chart.getChart('monthlyChart');
    if (!chart) return;
    
    chart.data.labels = labels;
    chart.data.datasets[0].data = data;
    chart.update();
}

function updateDailyChart(labels, data) {
    const chart = Chart.getChart('dailyChart');
    if (!chart) return;
    
    chart.data.labels = labels;
    chart.data.datasets[0].data = data;
    chart.update();
}

function updateCategoryChart(labels, data) {
    const chart = Chart.getChart('categoryChart');
    if (!chart) return;
    
    chart.data.labels = labels;
    chart.data.datasets[0].data = data;
    chart.update();
}

function updateYearlyChart(labels, data) {
    const chart = Chart.getChart('yearlyChart');
    if (!chart) return;
    
    chart.data.labels = labels;
    chart.data.datasets[0].data = data;
    chart.update();
}

function copyChart() {
    // Copy chart data or image to clipboard
    navigator.clipboard.writeText('Data grafik disalin ke clipboard!').then(() => {
        showNotification('Grafik disalin ke clipboard!', 'success');
    }).catch(() => {
        showNotification('Gagal menyalin grafik', 'error');
    });
}

function toggleView() {
    // Toggle between different chart layouts
    const chartsGrid = document.querySelector('.charts-grid');
    if (chartsGrid.style.gridTemplateColumns === '1fr') {
        chartsGrid.style.gridTemplateColumns = 'repeat(auto-fit, minmax(500px, 1fr))';
        showNotification('Beralih ke tampilan grid', 'success');
    } else {
        chartsGrid.style.gridTemplateColumns = '1fr';
        showNotification('Beralih ke tampilan daftar', 'success');
    }
}

function shareChart() {
    // Share chart functionality
    if (navigator.share) {
        navigator.share({
            title: 'Buku Tamu - Grafik Pengunjung',
            text: 'Lihat grafik pengunjung ini',
            url: window.location.href
        }).then(() => {
            showNotification('Grafik berhasil dibagikan!', 'success');
        }).catch(() => {
            showNotification('Gagal membagikan grafik', 'error');
        });
    } else {
        // Fallback: copy URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('URL grafik disalin ke clipboard!', 'success');
        });
    }
}

function openChartSettings() {
    // Open chart settings modal
    showNotification('Pengaturan grafik segera hadir!', 'info');
}

function editChart() {
    // Open the existing customize modal for the first chart
    openCustomizeModal('monthlyChart', 'Grafik Pengunjung Per Bulan');
}

// Global variable to store current chart being customized
let currentChartId = null;
let currentChartTitle = null;

// Function to open customization modal
function openCustomizeModal(chartId, chartTitle) {
    currentChartId = chartId;
    currentChartTitle = chartTitle;
    
    // Set modal title
    document.getElementById('modalTitle').textContent = 'Kustomisasi: ' + chartTitle;
    
    // Get current chart instance
    const chart = Chart.getChart(chartId);
    if (chart) {
        // Set current values in form
        document.getElementById('chartTitle').value = chart.options.plugins.title ? 
            (chart.options.plugins.title.text || chartTitle) : chartTitle;
        
        // Set chart type
        document.getElementById('chartType').value = chart.config.type;
        
        // Set default dates (last 30 days)
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - 30);
        
        document.getElementById('startDate').value = startDate.toISOString().split('T')[0];
        document.getElementById('endDate').value = endDate.toISOString().split('T')[0];
        
        // Reset filters
        document.getElementById('serviceFilter').selectedIndex = 0;
        document.getElementById('colorTheme').value = 'default';
    }
    
    // Show modal
    document.getElementById('customizeModal').classList.add('show');
}

// Function to close customization modal
function closeCustomizeModal() {
    document.getElementById('customizeModal').classList.remove('show');
    currentChartId = null;
    currentChartTitle = null;
}

// Function to apply customization
function applyCustomization() {
    if (!currentChartId) return;
    
    const chart = Chart.getChart(currentChartId);
    if (!chart) return;
    
    // Get form values
    const newTitle = document.getElementById('chartTitle').value;
    const newType = document.getElementById('chartType').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const colorTheme = document.getElementById('colorTheme').value;
    const serviceFilter = Array.from(document.getElementById('serviceFilter').selectedOptions)
        .map(option => option.value);
    
    // Apply title change
    if (newTitle) {
        chart.options.plugins.title = {
            display: true,
            text: newTitle,
            font: {
                size: 16,
                weight: 'bold'
            }
        };
    }
    
    // Apply color theme
    const colorThemes = {
        'default': ['#16a34a', '#22c55e', '#10b981', '#059669'],
        'blue': ['#3b82f6', '#60a5fa', '#2563eb', '#1d4ed8'],
        'purple': ['#8b5cf6', '#a78bfa', '#7c3aed', '#6d28d9'],
        'orange': ['#f59e0b', '#fbbf24', '#d97706', '#b45309'],
        'red': ['#ef4444', '#f87171', '#dc2626', '#b91c1c'],
        'teal': ['#14b8a6', '#2dd4bf', '#0d9488', '#0f766e']
    };
    
    const colors = colorThemes[colorTheme] || colorThemes['default'];
    
    // Update chart colors
    if (chart.data.datasets && chart.data.datasets[0]) {
        if (newType === 'pie' || newType === 'doughnut') {
            chart.data.datasets[0].backgroundColor = colors;
        } else {
            chart.data.datasets[0].borderColor = colors[0];
            chart.data.datasets[0].backgroundColor = colors[0] + '20'; // Add transparency
        }
    }
    
    // Change chart type if different
    if (chart.config.type !== newType) {
        chart.config.type = newType;
        
        // Adjust options for different chart types
        if (newType === 'pie' || newType === 'doughnut') {
            chart.options.scales = {};
        } else {
            // Restore scales for non-pie charts
            if (!chart.options.scales) {
                chart.options.scales = {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                };
            }
        }
    }
    
    // Update chart
    chart.update();
    
    // Close modal
    closeCustomizeModal();
    
    // Show success message
    showNotification('Grafik berhasil diperbarui!', 'success');
}

// Function to show notification
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    let background;
    
    if (type === 'success') {
        background = 'linear-gradient(135deg, #16a34a, #22c55e)';
    } else if (type === 'error') {
        background = 'linear-gradient(135deg, #ef4444, #f87171)';
    } else if (type === 'info') {
        background = 'linear-gradient(135deg, #3b82f6, #60a5fa)';
    }
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${background};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 2000;
        animation: slideInRight 0.3s ease-out;
        font-size: 14px;
        font-weight: 500;
    `;
    notification.textContent = message;
    
    // Add animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Show notification
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideInRight 0.3s ease-out reverse';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('customizeModal');
    if (event.target === modal) {
        closeCustomizeModal();
    }
}
