// admin.js
document.addEventListener('DOMContentLoaded', function() {
    // เปิด/ปิดเมนูมือถือ
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('active');
        });
    }
    
    // แท็บรายงาน
    const reportTabs = document.querySelectorAll('.report-tabs a');
    reportTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            reportTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // ยืนยันการลบ
    const deleteButtons = document.querySelectorAll('.action-btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('คุณแน่ใจที่จะลบรายการนี้?')) {
                e.preventDefault();
            }
        });
    });
    
    // ตรวจสอบแบบฟอร์ม
    const forms = document.querySelectorAll('form[method="POST"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#e74c3c';
                    const errorMsg = field.parentElement.querySelector('.error-message') || 
                                    document.createElement('div');
                    errorMsg.className = 'error-message';
                    errorMsg.textContent = 'กรุณากรอกข้อมูลนี้';
                    errorMsg.style.color = '#e74c3c';
                    errorMsg.style.fontSize = '0.9rem';
                    errorMsg.style.marginTop = '5px';
                    
                    if (!field.parentElement.querySelector('.error-message')) {
                        field.parentElement.appendChild(errorMsg);
                    }
                } else {
                    field.style.borderColor = '#ced4da';
                    const errorMsg = field.parentElement.querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (!valid) {
                e.preventDefault();
            }
        });
    });
    
    // แสดงตัวอย่างชื่อผู้ใช้จากอีเมล
    const emailInput = document.getElementById('email');
    const usernameInput = document.getElementById('username');
    
    if (emailInput && usernameInput && usernameInput.readOnly) {
        emailInput.addEventListener('blur', function() {
            if (!usernameInput.value) {
                const email = this.value;
                const username = email.split('@')[0];
                usernameInput.value = username;
            }
        });
    }
    
    // กรองตาราง
    const searchInputs = document.querySelectorAll('.table-search');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const table = this.closest('.data-table-container').querySelector('.data-table');
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    });
    
    // จัดการวันที่ในฟอร์ม
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        if (!input.value) {
            input.valueAsDate = new Date();
        }
    });
    
    // เพิ่ม CSS สำหรับเมนูมือถือ
    const style = document.createElement('style');
    style.textContent = `
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
                position: fixed;
                top: 15px;
                right: 15px;
                z-index: 1000;
                background: #3498db;
                color: white;
                border: none;
                padding: 10px;
                border-radius: 5px;
                cursor: pointer;
            }
            
            .admin-sidebar {
                position: fixed;
                left: -250px;
                z-index: 999;
                transition: left 0.3s;
            }
            
            .admin-sidebar.active {
                left: 0;
            }
            
            .admin-content {
                margin-left: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    // เพิ่มปุ่มเมนูมือถือถ้าไม่มี
    if (window.innerWidth <= 768 && !document.querySelector('.mobile-menu-toggle')) {
        const toggleButton = document.createElement('button');
        toggleButton.className = 'mobile-menu-toggle';
        toggleButton.innerHTML = '<i class="fas fa-bars"></i>';
        document.body.appendChild(toggleButton);
    }
});