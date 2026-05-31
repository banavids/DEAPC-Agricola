// /* ========================================================================== */
// /* FarmSmart OS - Login Script */
// /* ========================================================================== */

// // Toggle Password Visibility
// const togglePasswordBtn = document.getElementById('togglePassword');
// const passwordInput = document.getElementById('password');

// if (togglePasswordBtn) {
//     togglePasswordBtn.addEventListener('click', function(e) {
//         e.preventDefault();
        
//         if (passwordInput.type === 'password') {
//             passwordInput.type = 'text';
//             togglePasswordBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
//         } else {
//             passwordInput.type = 'password';
//             togglePasswordBtn.innerHTML = '<i class="fas fa-eye"></i>';
//         }
//     });
// }

// // Form Submission
// const loginForm = document.getElementById('loginForm');

// if (loginForm) {
//     loginForm.addEventListener('submit', function(e) {
//         e.preventDefault();
        
//         const email = document.getElementById('email').value.trim();
//         const password = document.getElementById('password').value;
        
//         // Basic Validation
//         if (!email || !password) {
//             alert('Por favor, preencha todos os campos!');
//             return;
//         }
        
//         // Simple validation (email format)
//         if (!isValidEmail(email)) {
//             alert('Por favor, insira um email válido!');
//             return;
//         }
        
//         // Simulate Login
//         const loginBtn = loginForm.querySelector('.btn-login');
//         const originalText = loginBtn.innerHTML;
        
//         loginBtn.disabled = true;
//         loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Autenticando...';
        
//         // Simulate API call (2 seconds)
//         setTimeout(() => {
//             // Success message
//             alert(`Bem-vindo, ${email}! \n\nLogin realizado com sucesso!`);
            
//             // Reset form
//             loginForm.reset();
//             loginBtn.innerHTML = originalText;
//             loginBtn.disabled = false;
            
//             // In a real app, you would redirect here:
//             // window.location.href = 'dashboard.html';
//         }, 2000);
//     });
// }

// // Email Validation Function
// function isValidEmail(email) {
//     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//     return emailRegex.test(email);
// }

// // Simulate Status Updates (Optional)
// function updateSystemStatus() {
//     // You can make API calls here to check actual server/MQTT status
//     const serverStatus = document.querySelector('.status-card:nth-child(1) .status-value');
//     const mqttStatus = document.querySelector('.status-card:nth-child(2) .status-value');
    
//     // Example: Both services are online
//     if (serverStatus) {
//         serverStatus.className = 'status-value online';
//         serverStatus.innerHTML = '<span class="status-dot"></span> Online';
//     }
    
//     if (mqttStatus) {
//         mqttStatus.className = 'status-value conectado';
//         mqttStatus.innerHTML = '<span class="status-dot"></span> Conectado';
//     }
// }

// // Initialize on page load
// document.addEventListener('DOMContentLoaded', function() {
//     updateSystemStatus();
    
//     // Optional: Update status every 10 seconds
//     setInterval(updateSystemStatus, 10000);
// });

// // Add some keyboard shortcuts
// document.addEventListener('keydown', function(e) {
//     // Enter to submit form
//     if (e.key === 'Enter' && loginForm) {
//         loginForm.dispatchEvent(new Event('submit'));
//     }
// });

// // Add visual feedback on input focus
// const inputs = document.querySelectorAll('.form-input');
// inputs.forEach(input => {
//     input.addEventListener('focus', function() {
//         this.parentElement.style.borderColor = 'var(--primary)';
//     });
    
//     input.addEventListener('blur', function() {
//         this.parentElement.style.borderColor = 'var(--border-color)';
//     });
// });

// console.log('FarmSmart OS - Login Page Loaded');
