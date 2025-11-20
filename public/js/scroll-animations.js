/**
 * Scroll Animations - Reusable scrollytelling animations
 * Similar to landing page animations
 */
(function() {
  'use strict';

  // Check if IntersectionObserver is available
  if (!window.IntersectionObserver) {
    return; // Fallback for older browsers
  }

  // Animation types
  const animationTypes = {
    'fade-in': { opacity: 0, transform: 'none' },
    'fade-up': { opacity: 0, transform: 'translateY(30px)' },
    'fade-down': { opacity: 0, transform: 'translateY(-30px)' },
    'slide-left': { opacity: 0, transform: 'translateX(50px)' },
    'slide-right': { opacity: 0, transform: 'translateX(-50px)' },
    'zoom-in': { opacity: 0, transform: 'scale(0.9)' },
    'zoom-out': { opacity: 0, transform: 'scale(1.1)' }
  };

  // Initialize animations
  function initScrollAnimations() {
    // Animate sections with data-animate attribute
    const animatedSections = document.querySelectorAll('[data-animate]');
    
    const sectionObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const section = entry.target;
          const animationType = section.dataset.animate || 'fade-up';
          
          section.classList.add('animate-visible');
          
          // Animate children with stagger
          const children = section.querySelectorAll('[data-animate-item]');
          children.forEach((child, index) => {
            setTimeout(() => {
              child.classList.add('animate-visible');
            }, index * 100);
          });
        }
      });
    }, { 
      threshold: 0.1, 
      rootMargin: '0px 0px -50px 0px' 
    });
    
    animatedSections.forEach(section => {
      sectionObserver.observe(section);
    });

    // Animate individual elements with data-animate-item
    const animatedItems = document.querySelectorAll('[data-animate-item]');
    
    const itemObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-visible');
        }
      });
    }, { 
      threshold: 0.1, 
      rootMargin: '0px 0px -30px 0px' 
    });
    
    animatedItems.forEach(item => {
      itemObserver.observe(item);
    });
  }

  // Page load animations
  function initPageLoadAnimations() {
    const pageElements = document.querySelectorAll('[data-animate-on-load]');
    pageElements.forEach((el, index) => {
      setTimeout(() => {
        el.classList.add('animate-visible');
      }, index * 80 + 100);
    });
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initScrollAnimations();
      initPageLoadAnimations();
    });
  } else {
    initScrollAnimations();
    initPageLoadAnimations();
  }
})();

