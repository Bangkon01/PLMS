// Slideshow functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize slideshow
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const indicators = document.querySelectorAll('.indicator');
    
    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Remove active class from all indicators
        indicators.forEach(indicator => {
            indicator.classList.remove('active');
        });
        
        // Show selected slide
        slides[index].classList.add('active');
        indicators[index].classList.add('active');
        currentSlide = index;
    }
    
    // Add click events to indicators
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', function() {
            showSlide(index);
        });
    });
    
    // Auto advance slides every 5 seconds
    setInterval(function() {
        let nextSlide = (currentSlide + 1) % slides.length;
        showSlide(nextSlide);
    }, 5000);
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#e74c3c';
                } else {
                    field.style.borderColor = '#ddd';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('กรุณากรอกข้อมูลในช่องที่จำเป็นให้ครบถ้วน');
            }
        });
    });
    
    // Initialize category filtering if on categories page
    if (document.querySelector('.category-buttons')) {
        const categoryButtons = document.querySelectorAll('.category-btn');
        const bookCards = document.querySelectorAll('.book-card');
        
        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Update active button
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const category = this.getAttribute('data-category');
                
                // Show/hide books based on category
                bookCards.forEach(card => {
                    const bookCategory = card.getAttribute('data-category');
                    
                    if (category === 'all' || bookCategory === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }
    
    // Initialize date pickers
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    const nextWeekFormatted = nextWeek.toISOString().split('T')[0];
    
    dateInputs.forEach(input => {
        if (input.id === 'borrow_date') {
            input.value = today;
            input.min = today;
        } else if (input.id === 'return_date') {
            input.value = nextWeekFormatted;
            input.min = today;
        }
    });
});