jQuery(document).ready(function($) {
    
    // Initialiseer alle galerijen op de pagina
    $('.kapel-footer-gallery').each(function() {
        var $gallery = $(this);
        var $slides = $gallery.find('.kfg-slide');
        
        if ($slides.length <= 1) {
            return; // Geen rotatie nodig voor één foto
        }
        
        var currentIndex = 0;
        var transitionType = $gallery.data('transition') || 'fade';
        var transitionSpeed = parseInt(kfgSettings.transitionSpeed) || 1000;
        var displayDuration = parseInt(kfgSettings.displayDuration) || 5000;
        
        // Functie om naar de volgende slide te gaan
        function nextSlide() {
            var $current = $slides.eq(currentIndex);
            currentIndex = (currentIndex + 1) % $slides.length;
            var $next = $slides.eq(currentIndex);
            
            if (transitionType === 'slide') {
                // Slide effect
                $current.animate({
                    left: '-100%',
                    opacity: 0
                }, transitionSpeed, function() {
                    $(this).removeClass('active').css({left: '0', opacity: 1});
                });
                
                $next.css({left: '100%', opacity: 1}).addClass('active').animate({
                    left: '0'
                }, transitionSpeed);
            } else {
                // Fade effect (standaard)
                $current.fadeOut(transitionSpeed, function() {
                    $(this).removeClass('active');
                });
                
                $next.addClass('active').fadeIn(transitionSpeed);
            }
        }
        
        // Start de automatische rotatie
        setInterval(nextSlide, displayDuration);
        
        // Voeg navigatie pijlen toe
        var $prevArrow = $('<button class="kfg-arrow kfg-arrow-prev" aria-label="Vorige foto">&lsaquo;</button>');
        var $nextArrow = $('<button class="kfg-arrow kfg-arrow-next" aria-label="Volgende foto">&rsaquo;</button>');
        
        $gallery.append($prevArrow).append($nextArrow);
        
        // Functie om naar vorige slide te gaan
        function prevSlide() {
            var $current = $slides.eq(currentIndex);
            currentIndex = (currentIndex - 1 + $slides.length) % $slides.length;
            var $prev = $slides.eq(currentIndex);
            
            if (transitionType === 'slide') {
                $current.animate({
                    left: '100%',
                    opacity: 0
                }, transitionSpeed, function() {
                    $(this).removeClass('active').css({left: '0', opacity: 1});
                });
                
                $prev.css({left: '-100%', opacity: 1}).addClass('active').animate({
                    left: '0'
                }, transitionSpeed);
            } else {
                $current.fadeOut(transitionSpeed, function() {
                    $(this).removeClass('active');
                });
                
                $prev.addClass('active').fadeIn(transitionSpeed);
            }
        }
        
        // Klik handlers voor pijlen
        $prevArrow.on('click', function(e) {
            e.preventDefault();
            prevSlide();
        });
        
        $nextArrow.on('click', function(e) {
            e.preventDefault();
            nextSlide();
        });
    });
});
