<div class="contact-form-modal" id="contactFormModal">
    <div class="contact-form-modal-overlay"></div>
    <div class="contact-form-modal-content">
        <button class="contact-form-modal-close" id="closeContactFormModal">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <h2 class="contact-form-title">Обратная связь</h2>
        <p class="contact-form-subtitle">Оставьте заявку и мы свяжемся с вами</p>
        <form class="contact-form" id="contactForm">
            @csrf
            <input type="hidden" name="girl_anketa_id" id="girlAnketaId" value="{{ $girlAnketaId ?? '' }}">
            <input type="hidden" name="girl_name" id="girlName" value="{{ $girlName ?? '' }}">
            <input type="hidden" name="girl_phone" id="girlPhone" value="{{ $girlPhone ?? '' }}">
            <input type="hidden" name="girl_url" id="girlUrl" value="{{ $girlUrl ?? '' }}">
            <input type="hidden" name="page_url" id="pageUrl" value="{{ url()->current() }}">
            
            <div class="contact-form-group">
                <input type="text" name="name" id="contactName" placeholder="Имя" required>
            </div>
            
            <div class="contact-form-group">
                <input type="email" name="email" id="contactEmail" placeholder="Email" required>
            </div>
            
            <div class="contact-form-group">
                <input type="tel" name="phone" id="contactPhone" placeholder="+7 (999) 999-99-99" required>
            </div>
            
            <button type="submit" class="contact-form-submit">
                Отправить заявку
            </button>
        </form>
    </div>
</div>

<div class="contact-success-modal" id="contactSuccessModal">
    <div class="contact-modal-content">
        <div class="contact-modal-icon">
            <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="30" cy="30" r="30" fill="#7E1D32"/>
                <path d="M25 30L28 33L35 26" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h3 class="contact-modal-title">Заявка отправлена!</h3>
        <p class="contact-modal-text">Мы свяжемся с вами в ближайшее время</p>
        <button class="contact-modal-close" id="closeSuccessModal">
            Закрыть
        </button>
    </div>
</div>

<style>
.contact-form-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    align-items: center;
    justify-content: center;
}

.contact-form-modal.active {
    display: flex;
}

.contact-form-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.contact-form-modal-content {
    position: relative;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    padding: 40px;
    background: #FFFFFF;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    animation: modalSlideIn 0.3s ease;
    z-index: 10001;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.contact-form-modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 32px;
    height: 32px;
    background: transparent;
    border: none;
    cursor: pointer;
    color: #6B7280;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.contact-form-modal-close:hover {
    color: #7E1D32;
    transform: rotate(90deg);
}

.contact-form-title {
    color: #292D33;
    font-size: 32px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 10px;
    text-align: center;
}

.contact-form-subtitle {
    color: #6B7280;
    font-size: 16px;
    font-weight: 400;
    line-height: 1.5;
    margin-bottom: 30px;
    text-align: center;
}

.contact-form-group {
    margin-bottom: 20px;
}

.contact-form-group input {
    width: 100%;
    height: 50px;
    padding: 15px 20px;
    border-radius: 10px;
    border: 1px solid #D7D7D7;
    color: #292D33;
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    font-weight: 400;
    line-height: 1.6;
    transition: all 0.3s;
}

.contact-form-group input:focus {
    border-color: #7E1D32;
}

.contact-form-group input::placeholder {
    color: #9CA3AF;
    font-family: "Noto Sans", sans-serif;
}

.contact-form-submit {
    width: 100%;
    height: 50px;
    background: #7E1D32;
    border-radius: 10px;
    color: #FFFFFF;
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.6;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
}

.contact-form-submit:hover {
    background: #5D0F1F;
}

.contact-success-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10002;
    align-items: center;
    justify-content: center;
}

.contact-success-modal.active {
    display: flex;
}

.contact-modal-content {
    background: #FFFFFF;
    border-radius: 20px;
    padding: 40px;
    max-width: 400px;
    text-align: center;
    animation: modalFadeIn 0.3s ease;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.contact-modal-icon {
    margin-bottom: 20px;
}

.contact-modal-title {
    color: #292D33;
    font-size: 24px;
    font-weight: 700;
    line-height: 1.3;
    margin-bottom: 10px;
}

.contact-modal-text {
    color: #6B7280;
    font-size: 16px;
    font-weight: 400;
    line-height: 1.5;
    margin-bottom: 30px;
}

.contact-modal-close {
    width: 100%;
    height: 50px;
    background: #7E1D32;
    border-radius: 10px;
    color: #FFFFFF;
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.6;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
}

.contact-modal-close:hover {
    background: #5D0F1F;
}

@media (max-width: 768px) {
    .contact-form-modal-content {
        padding: 30px 20px;
        width: 95%;
    }
    
    .contact-form-title {
        font-size: 24px;
    }
    
    .contact-modal-content {
        margin: 0 20px;
        padding: 30px 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactFormModal = document.getElementById('contactFormModal');
    const contactForm = document.getElementById('contactForm');
    const successModal = document.getElementById('contactSuccessModal');
    const closeFormModalBtn = document.getElementById('closeContactFormModal');
    const closeSuccessModalBtn = document.getElementById('closeSuccessModal');
    const modalOverlay = contactFormModal?.querySelector('.contact-form-modal-overlay');
    const phoneInput = document.getElementById('contactPhone');
    
    function phoneMask(input) {
        if (!input) return;
        
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length > 0 && value[0] !== '7') {
                if (value[0] === '8') {
                    value = '7' + value.slice(1);
                } else {
                    value = '7' + value;
                }
            }
            
            let formattedValue = '+7';
            
            if (value.length > 1) {
                formattedValue += ' (' + value.substring(1, 4);
            }
            if (value.length >= 5) {
                formattedValue += ') ' + value.substring(4, 7);
            }
            if (value.length >= 8) {
                formattedValue += '-' + value.substring(7, 9);
            }
            if (value.length >= 10) {
                formattedValue += '-' + value.substring(9, 11);
            }
            
            e.target.value = formattedValue;
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && e.target.value === '+7') {
                e.preventDefault();
            }
        });
        
        input.addEventListener('focus', function(e) {
            if (!e.target.value) {
                e.target.value = '+7 ';
            }
        });
    }
    
    phoneMask(phoneInput);
    
    window.openContactModal = function(girlData = null) {
        if (contactFormModal) {
            if (girlData) {
                document.getElementById('girlAnketaId').value = girlData.id || '';
                document.getElementById('girlName').value = girlData.name || '';
                document.getElementById('girlPhone').value = girlData.phone || '';
                document.getElementById('girlUrl').value = girlData.url || '';
            } else {
                document.getElementById('girlAnketaId').value = '';
                document.getElementById('girlName').value = '';
                document.getElementById('girlPhone').value = '';
                document.getElementById('girlUrl').value = '';
            }
            contactFormModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    };

    document.querySelectorAll('[data-open-contact]').forEach(function(trigger) {
        trigger.addEventListener('click', function(event) {
            event.preventDefault();
            window.openContactModal();
        });
    });
    
    function closeContactModal() {
        if (contactFormModal) {
            contactFormModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    function closeSuccessModal() {
        if (successModal) {
            successModal.classList.remove('active');
        }
    }
    
    if (closeFormModalBtn) {
        closeFormModalBtn.addEventListener('click', closeContactModal);
    }
    
    if (modalOverlay) {
        modalOverlay.addEventListener('click', closeContactModal);
    }
    
    if (closeSuccessModalBtn) {
        closeSuccessModalBtn.addEventListener('click', closeSuccessModal);
    }
    
    if (successModal) {
        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                closeSuccessModal();
            }
        });
    }
    
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(contactForm);
            const submitBtn = contactForm.querySelector('.contact-form-submit');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Отправка...';
            
            try {
                const response = await fetch('{{ route("contact.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    contactForm.reset();
                    closeContactModal();
                    successModal.classList.add('active');
                    
                    setTimeout(() => {
                        closeSuccessModal();
                    }, 3000);
                } else {
                    alert('Произошла ошибка. Попробуйте еще раз.');
                }
            } catch (error) {
                alert('Произошла ошибка. Попробуйте еще раз.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }
});
</script>

