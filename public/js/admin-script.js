/**
 * MCD Inter College - Admin Panel JavaScript
 * Handles menu interactions, mobile responsiveness, and UI enhancements
 */

(function() {
    'use strict';

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        
        // ============================================
        // MOBILE MENU TOGGLE
        // ============================================
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const horizontalMenu = document.querySelector('.horizontal-menu');
        
        console.log('Mobile Menu Toggle:', mobileMenuToggle);
        console.log('Horizontal Menu:', horizontalMenu);
        
        if (mobileMenuToggle && horizontalMenu) {
            mobileMenuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Menu toggle clicked!');
                horizontalMenu.classList.toggle('mobile-menu-active');
                
                // Toggle icon
                const icon = this.querySelector('i');
                if (icon) {
                    if (icon.classList.contains('fa-bars')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                        console.log('Changed to X icon');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                        console.log('Changed to bars icon');
                    }
                }
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.horizontal-menu') && 
                    !event.target.closest('.mobile-menu-toggle')) {
                    horizontalMenu.classList.remove('mobile-menu-active');
                    const icon = mobileMenuToggle.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                }
            });
        } else {
            console.error('Mobile menu elements not found!');
        }

        // ============================================
        // DROPDOWN MENU ENHANCEMENTS
        // ============================================
        const menuItems = document.querySelectorAll('.menu-item');
        
        menuItems.forEach(function(item) {
            const dropdown = item.querySelector('.dropdown-menu-custom');
            
            if (dropdown) {
                // For mobile: toggle on click
                if (window.innerWidth <= 768) {
                    const menuLink = item.querySelector('.menu-link');
                    
                    menuLink.addEventListener('click', function(e) {
                        if (this.nextElementSibling && 
                            this.nextElementSibling.classList.contains('dropdown-menu-custom')) {
                            e.preventDefault();
                            
                            // Close other dropdowns
                            menuItems.forEach(function(otherItem) {
                                if (otherItem !== item) {
                                    const otherDropdown = otherItem.querySelector('.dropdown-menu-custom');
                                    if (otherDropdown) {
                                        otherDropdown.classList.remove('mobile-dropdown-active');
                                    }
                                }
                            });
                            
                            // Toggle current dropdown
                            dropdown.classList.toggle('mobile-dropdown-active');
                        }
                    });
                }
            }
        });

        // ============================================
        // SMOOTH SCROLL FOR ANCHOR LINKS
        // ============================================
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        
        anchorLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href !== '#!') {
                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });

        // ============================================
        // ACTIVE MENU HIGHLIGHTING
        // ============================================
        const currentPath = window.location.pathname;
        const menuLinks = document.querySelectorAll('.menu-link, .dropdown-item-custom');
        
        menuLinks.forEach(function(link) {
            const href = link.getAttribute('href');
            if (href && currentPath.includes(href) && href !== '/admin/dashboard') {
                link.classList.add('active');
                
                // If it's a dropdown item, also highlight parent
                const parentMenuItem = link.closest('.menu-item');
                if (parentMenuItem) {
                    const parentLink = parentMenuItem.querySelector('.menu-link');
                    if (parentLink) {
                        parentLink.classList.add('active');
                    }
                }
            }
        });

        // ============================================
        // NOTIFICATION CLICK HANDLERS
        // ============================================
        const notificationIcons = document.querySelectorAll('.notification-icon');
        
        notificationIcons.forEach(function(icon) {
            icon.addEventListener('click', function() {
                // Add your notification handling logic here
                console.log('Notification clicked');
            });
        });

        // ============================================
        // TABLE ENHANCEMENTS
        // ============================================
        const tables = document.querySelectorAll('table');
        
        tables.forEach(function(table) {
            // Make tables responsive
            if (!table.parentElement.classList.contains('table-responsive')) {
                const wrapper = document.createElement('div');
                wrapper.classList.add('table-responsive');
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
            
            // Add hover effect to rows
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(function(row) {
                row.style.cursor = 'pointer';
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fc';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });

        // ============================================
        // FORM VALIDATION ENHANCEMENTS
        // ============================================
        const forms = document.querySelectorAll('form');
        
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        
                        // Remove invalid class on input
                        field.addEventListener('input', function() {
                            this.classList.remove('is-invalid');
                        });
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields');
                }
            });
        });

        // ============================================
        // WINDOW RESIZE HANDLER
        // ============================================
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Refresh dropdown behavior on resize
                if (window.innerWidth > 768) {
                    const horizontalMenu = document.querySelector('.horizontal-menu');
                    if (horizontalMenu) {
                        horizontalMenu.classList.remove('mobile-menu-active');
                    }
                    const dropdowns = document.querySelectorAll('.dropdown-menu-custom');
                    dropdowns.forEach(function(dropdown) {
                        dropdown.classList.remove('mobile-dropdown-active');
                    });
                }
            }, 250);
        });

        // ============================================
        // LOADING INDICATOR
        // ============================================
        window.showLoading = function() {
            const loader = document.createElement('div');
            loader.id = 'page-loader';
            loader.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            loader.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.9);display:flex;align-items:center;justify-content:center;z-index:9999;';
            document.body.appendChild(loader);
        };

        window.hideLoading = function() {
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.remove();
            }
        };

        // ============================================
        // CONSOLE LOG - SYSTEM READY
        // ============================================
        console.log('%c MCD Inter College - Admin Panel ', 'background: #4e73df; color: white; font-size: 16px; padding: 10px;');
        console.log('%c System Ready ✓ ', 'background: #1cc88a; color: white; font-size: 14px; padding: 5px;');
    });

})();
