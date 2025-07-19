
// Admin Panel JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initAdminFunctionality();
});

function initAdminFunctionality() {
    initDataTables();
    initCharts();
    initFormValidation();
    initModalHandlers();
    initDeleteConfirmations();
    initBulkActions();
}

// DataTables initialization
function initDataTables() {
    const tables = document.querySelectorAll('.data-table');
    
    tables.forEach(table => {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $(table).DataTable({
                responsive: true,
                pageLength: 25,
                order: [[0, 'desc']],
                language: {
                    search: "Qidirish:",
                    lengthMenu: "_MENU_ ta yozuv ko'rsatish",
                    info: "_START_ dan _END_ gacha (_TOTAL_ ta yozuvdan)",
                    paginate: {
                        first: "Birinchi",
                        last: "Oxirgi",
                        next: "Keyingi",
                        previous: "Oldingi"
                    },
                    emptyTable: "Ma'lumot topilmadi"
                }
            });
        }
    });
}

// Charts initialization
function initCharts() {
    // Test attempts chart
    const attemptsChart = document.getElementById('attempts-chart');
    if (attemptsChart && typeof Chart !== 'undefined') {
        const ctx = attemptsChart.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: JSON.parse(attemptsChart.dataset.labels || '[]'),
                datasets: [{
                    label: 'Test urinishlari',
                    data: JSON.parse(attemptsChart.dataset.data || '[]'),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Users registration chart
    const usersChart = document.getElementById('users-chart');
    if (usersChart && typeof Chart !== 'undefined') {
        const ctx = usersChart.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: JSON.parse(usersChart.dataset.labels || '[]'),
                datasets: [{
                    label: 'Ro\'yxatdan o\'tgan foydalanuvchilar',
                    data: JSON.parse(usersChart.dataset.data || '[]'),
                    backgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Form validation
function initFormValidation() {
    const forms = document.querySelectorAll('.admin-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateAdminForm(form)) {
                e.preventDefault();
            }
        });
    });
}

function validateAdminForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        clearFieldError(field);
        
        if (!field.value.trim()) {
            showFieldError(field, 'Bu maydon to\'ldirilishi shart');
            isValid = false;
        }
    });
    
    // Email validation
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        if (field.value && !isValidEmail(field.value)) {
            showFieldError(field, 'Email manzil noto\'g\'ri formatda');
            isValid = false;
        }
    });
    
    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showFieldError(field, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-red-500 text-sm mt-1';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
    field.classList.add('border-red-500');
}

function clearFieldError(field) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    field.classList.remove('border-red-500');
}

// Modal handlers
function initModalHandlers() {
    const modalTriggers = document.querySelectorAll('[data-modal-target]');
    const modalCloses = document.querySelectorAll('[data-modal-close]');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.dataset.modalTarget;
            const modal = document.getElementById(modalId);
            if (modal) {
                showModal(modal);
            }
        });
    });
    
    modalCloses.forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                hideModal(modal);
            }
        });
    });
    
    // Close modal on backdrop click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            const modal = e.target.closest('.modal');
            if (modal) {
                hideModal(modal);
            }
        }
    });
}

function showModal(modal) {
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideModal(modal) {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

// Delete confirmations
function initDeleteConfirmations() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const itemName = this.dataset.itemName || 'bu element';
            const form = this.closest('form');
            
            if (confirm(`Rostdan ham ${itemName}ni o'chirmoqchimisiz? Bu amalni bekor qilib bo'lmaydi.`)) {
                if (form) {
                    form.submit();
                } else {
                    window.location.href = this.href;
                }
            }
        });
    });
}

// Bulk actions
function initBulkActions() {
    const selectAllCheckbox = document.getElementById('select-all');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActionForm = document.getElementById('bulk-action-form');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButtons();
        });
    }
    
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAll();
            updateBulkActionButtons();
        });
    });
    
    function updateSelectAll() {
        if (selectAllCheckbox) {
            const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === itemCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
        }
    }
    
    function updateBulkActionButtons() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        const bulkActions = document.querySelectorAll('.bulk-action-btn');
        
        bulkActions.forEach(btn => {
            btn.disabled = checkedCount === 0;
            btn.textContent = btn.dataset.baseText + (checkedCount > 0 ? ` (${checkedCount})` : '');
        });
    }
}

// Export functions
function exportData(format, type) {
    const checkedItems = document.querySelectorAll('.item-checkbox:checked');
    const ids = Array.from(checkedItems).map(cb => cb.value);
    
    if (ids.length === 0) {
        alert('Hech qanday element tanlanmagan');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/export/${type}`;
    
    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.content;
        form.appendChild(csrfInput);
    }
    
    // Format
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    // IDs
    ids.forEach(id => {
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'ids[]';
        idInput.value = id;
        form.appendChild(idInput);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Real-time notifications
function initRealTimeNotifications() {
    // If using pusher or websockets
    if (typeof Pusher !== 'undefined') {
        const pusher = new Pusher(window.pusherKey, {
            cluster: window.pusherCluster
        });
        
        const channel = pusher.subscribe('admin-notifications');
        
        channel.bind('new-user-registered', function(data) {
            showNotification(`Yangi foydalanuvchi ro'yxatdan o'tdi: ${data.user.name}`, 'info');
            updateUserCount();
        });
        
        channel.bind('test-completed', function(data) {
            showNotification(`Test yakunlandi: ${data.test.title}`, 'success');
            updateTestStats();
        });
    }
}

// Update dashboard stats
function updateUserCount() {
    fetch('/admin/api/user-count')
        .then(response => response.json())
        .then(data => {
            const countElement = document.getElementById('user-count');
            if (countElement) {
                countElement.textContent = data.count;
            }
        })
        .catch(error => console.error('User count yangilanmadi:', error));
}

function updateTestStats() {
    fetch('/admin/api/test-stats')
        .then(response => response.json())
        .then(data => {
            const attemptsElement = document.getElementById('test-attempts-count');
            if (attemptsElement) {
                attemptsElement.textContent = data.attempts;
            }
        })
        .catch(error => console.error('Test statistikasi yangilanmadi:', error));
}

// Show admin notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `admin-notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    const container = document.getElementById('notifications-container') || document.body;
    container.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
    
    // Manual close
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.remove();
    });
}

function getNotificationIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// Initialize real-time features if available
if (window.enableRealTime) {
    initRealTimeNotifications();
}

// Utility functions for admin
window.AdminUtils = {
    exportData,
    showNotification,
    showModal,
    hideModal,
    updateUserCount,
    updateTestStats
};
