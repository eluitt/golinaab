/**
 * Header functionality - Mobile menu and predictive search
 * 
 * @package GoliNaab
 * @since 1.0.0
 */

(function() {
    'use strict';

    // DOM ready utility
    function domReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    // Mobile menu functionality
    function initMobileMenu() {
        const menuToggle = document.querySelector('.gn-menu-toggle');
        const primaryMenu = document.querySelector('#gn-primary-menu');
        const body = document.body;
        
        if (!menuToggle || !primaryMenu) return;

        // Focus trap for mobile menu
        const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
        let firstFocusableElement, lastFocusableElement;

        function trapFocus(e) {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstFocusableElement) {
                        lastFocusableElement.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastFocusableElement) {
                        firstFocusableElement.focus();
                        e.preventDefault();
                    }
                }
            }
        }

        function setFocusableElements() {
            const focusable = primaryMenu.querySelectorAll(focusableElements);
            firstFocusableElement = focusable[0];
            lastFocusableElement = focusable[focusable.length - 1];
        }

        menuToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            this.setAttribute('aria-expanded', !isExpanded);
            primaryMenu.classList.toggle('gn-menu-open');
            body.classList.toggle('gn-menu-open');
            
            if (!isExpanded) {
                setFocusableElements();
                if (firstFocusableElement) {
                    firstFocusableElement.focus();
                }
                primaryMenu.addEventListener('keydown', trapFocus);
            } else {
                primaryMenu.removeEventListener('keydown', trapFocus);
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && primaryMenu.classList.contains('gn-menu-open')) {
                menuToggle.click();
                menuToggle.focus();
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (primaryMenu.classList.contains('gn-menu-open') && 
                !primaryMenu.contains(e.target) && 
                !menuToggle.contains(e.target)) {
                menuToggle.click();
            }
        });
    }

    // Predictive search functionality
    function initPredictiveSearch() {
        const searchInput = document.querySelector('.gn-search-input');
        const searchResults = document.querySelector('#gn-search-results');
        
        if (!searchInput || !searchResults) return;

        let searchTimeout;
        let currentRequest = null;
        let selectedIndex = -1;
        let searchResultsData = [];

        // Debounced search function
        function performSearch(query) {
            if (currentRequest) {
                currentRequest.abort();
            }

            if (query.length < 1) {
                hideResults();
                return;
            }

            const nonce = document.querySelector('script[data-nonce]')?.dataset.nonce || 
                         window.gn_ajax?.nonce || '';

            currentRequest = fetch(`/wp-json/gn/v1/search?q=${encodeURIComponent(query)}&nonce=${nonce}`)
                .then(response => response.json())
                .then(data => {
                    if (data.products || data.courses) {
                        displayResults(data);
                    } else {
                        hideResults();
                    }
                })
                .catch(error => {
                    if (error.name !== 'AbortError') {
                        console.error('Search error:', error);
                        hideResults();
                    }
                });
        }

        function displayResults(data) {
            searchResultsData = [];
            let html = '';

            // Products section
            if (data.products && data.products.length > 0) {
                html += '<div class="gn-search-section">';
                html += '<h3 class="gn-search-section-title">محصولات</h3>';
                html += '<ul class="gn-search-list">';
                
                data.products.forEach((product, index) => {
                    const resultIndex = searchResultsData.length;
                    searchResultsData.push({
                        type: 'product',
                        url: product.url,
                        title: product.title
                    });
                    
                    html += `<li class="gn-search-item ${index === 0 ? 'gn-search-item-selected' : ''}" data-index="${resultIndex}">`;
                    html += `<a href="${product.url}" class="gn-search-link">`;
                    if (product.image) {
                        html += `<img src="${product.image}" alt="" class="gn-search-image">`;
                    }
                    html += `<div class="gn-search-content">`;
                    html += `<span class="gn-search-title">${product.title}</span>`;
                    if (product.price) {
                        html += `<span class="gn-search-meta">${product.price}</span>`;
                    }
                    html += `</div></a></li>`;
                });
                
                html += '</ul></div>';
            }

            // Courses section
            if (data.courses && data.courses.length > 0) {
                html += '<div class="gn-search-section">';
                html += '<h3 class="gn-search-section-title">دوره‌ها</h3>';
                html += '<ul class="gn-search-list">';
                
                data.courses.forEach((course, index) => {
                    const resultIndex = searchResultsData.length;
                    searchResultsData.push({
                        type: 'course',
                        url: course.url,
                        title: course.title
                    });
                    
                    html += `<li class="gn-search-item" data-index="${resultIndex}">`;
                    html += `<a href="${course.url}" class="gn-search-link">`;
                    if (course.image) {
                        html += `<img src="${course.image}" alt="" class="gn-search-image">`;
                    }
                    html += `<div class="gn-search-content">`;
                    html += `<span class="gn-search-title">${course.title}</span>`;
                    if (course.duration) {
                        html += `<span class="gn-search-meta">${course.duration}</span>`;
                    }
                    html += `</div></a></li>`;
                });
                
                html += '</ul></div>';
            }

            if (html) {
                searchResults.innerHTML = html;
                searchResults.classList.add('gn-search-results-visible');
                selectedIndex = 0;
                updateSelection();
            } else {
                hideResults();
            }
        }

        function hideResults() {
            searchResults.classList.remove('gn-search-results-visible');
            searchResults.innerHTML = '';
            searchResultsData = [];
            selectedIndex = -1;
        }

        function updateSelection() {
            const items = searchResults.querySelectorAll('.gn-search-item');
            items.forEach((item, index) => {
                item.classList.toggle('gn-search-item-selected', index === selectedIndex);
            });
        }

        function selectResult() {
            if (selectedIndex >= 0 && searchResultsData[selectedIndex]) {
                window.location.href = searchResultsData[selectedIndex].url;
            }
        }

        // Event listeners
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(this.value);
            }, 200); // 200ms debounce
        });

        searchInput.addEventListener('keydown', function(e) {
            const items = searchResults.querySelectorAll('.gn-search-item');
            
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    if (selectedIndex < searchResultsData.length - 1) {
                        selectedIndex++;
                        updateSelection();
                    }
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    if (selectedIndex > 0) {
                        selectedIndex--;
                        updateSelection();
                    }
                    break;
                case 'Enter':
                    e.preventDefault();
                    selectResult();
                    break;
                case 'Escape':
                    hideResults();
                    this.blur();
                    break;
            }
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                hideResults();
            }
        });

        // Focus management
        searchInput.addEventListener('focus', function() {
            if (this.value.length > 0) {
                performSearch(this.value);
            }
        });
    }

    // Initialize everything when DOM is ready
    domReady(function() {
        initMobileMenu();
        initPredictiveSearch();
    });

})();
