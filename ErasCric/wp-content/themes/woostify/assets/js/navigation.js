/**
 * Navigation.js
 *
 * @package woostify
 */

'use strict';

if ( typeof woostifyEvent == 'undefined' ){
	var woostifyEvent = {};
}
// Mobile menu tab.
function mobileMenuTab() {
	var mobileTabsWrapperEls = document.querySelectorAll( 'ul.mobile-nav-tab' );

	if ( ! mobileTabsWrapperEls.length ) {
		return;
	}

	mobileTabsWrapperEls.forEach(
		function( mobileTabsWrapperEl ) {
			var sidebarWrapper = mobileTabsWrapperEl.parentNode;
			var tabs           = mobileTabsWrapperEl.querySelectorAll( '.mobile-tab-title' );
			var menus;

			if ( sidebarWrapper.classList.contains( 'sidebar-menu' ) ) {
				menus = sidebarWrapper.querySelectorAll( '.site-navigation nav' );
			}
			if ( sidebarWrapper.classList.contains( 'woostify-nav-menu-inner' ) ) {
				menus = sidebarWrapper.querySelectorAll( 'nav' );
			}

			if ( ! tabs.length || ! menus.length ) {
				return;
			}

			menus[0].classList.add( 'active' );

			tabs.forEach(
				function( tab, tabIndex ) {
					tab.onclick = function() {
						if ( tab.classList.contains( 'active' ) ) {
							return;
						}

						for ( var i = 0, j = tabs.length; i < j; i++ ) {
							tabs[i].classList.remove( 'active' );
						}
						tab.classList.add( 'active' );

						menus.forEach(
							function( menu, menuIndex ) {
								if ( tabIndex === menuIndex ) {
									menu.classList.add( 'active' );
								} else {
									menu.classList.remove( 'active' );
								}
							}
						)
					}
				}
			)
		}
	)
}

// Open Menu mobile.
function nav() {
	var menuToggleBtn = document.getElementsByClassName( 'toggle-sidebar-menu-btn' );

	if ( ! menuToggleBtn.length ) {
		return;
	}

	for ( var i = 0, j = menuToggleBtn.length; i < j; i++ ) {
		menuToggleBtn[i].addEventListener(
			'click',
			function() {
				document.documentElement.classList.add( 'sidebar-menu-open' );
				closeAll();
			}
		);
	}
}

// Accordion menu on sidebar.
function sidebarMenu( node ) {
	var selector = ( arguments.length > 0 && undefined !== arguments[0] ) ? jQuery( node ) : jQuery( '.sidebar-menu .primary-navigation' ),
		arrow    = selector.find( '.arrow-icon' );
	if( ! arrow.length ) return;
	jQuery( arrow ).off('click').on(
		'click',
		function( e ) {
			e.preventDefault();
			var t        = jQuery( this ),
				siblings = t.parent().siblings( 'ul' ),
				arrow    = t.parent().parent().parent().find( '.arrow-icon' ),
				subMenu  = t.parent().parent().parent().find( 'li .sub-menu, li .sub-mega-menu' );

			if ( siblings.hasClass( 'show' ) ) {
				siblings.slideUp(
					200,
					function() {
						jQuery( this ).removeClass( 'show' );
					}
				);

				// Remove active state.
				t.removeClass( 'active' );
			} else {
				subMenu.slideUp(
					200,
					function() {
						jQuery( this ).removeClass( 'show' );
					}
				);
				siblings.slideToggle(
					200,
					function() {
						jQuery( this ).toggleClass( 'show' );
					}
				);

				// Add active state for current arrow.
				arrow.removeClass( 'active' );
				t.addClass( 'active' );
			}
		}
	);
}


function subMenuNavOffScreen() {

	if ( !window.matchMedia( '( min-width: 992px )' ).matches ) {
		return;
	}

	var menuMainMenu = document.querySelector('nav.main-navigation ul');
	
	if ( !menuMainMenu ) {
		return;
	}

	var parents = menuMainMenu.querySelectorAll('.sub-menu > li.menu-item-has-children');
	
	if ( parents.length == 0 ) {
		return;
	}
	
	parents.forEach(function(parent) {

		var submenu = parent.querySelector('ul.sub-menu');

		if ( !submenu ) {
			return;
		}

		var preSubmenu = submenu.previousElementSibling;
		var menuItemArrow = submenu.querySelectorAll('.menu-item-arrow'); 
		// Get the width and position of the submenu
		var submenuWidth = ( submenu.offsetWidth != null )? submenu.offsetWidth : 0;
		var submenuPosition = ( submenu.getBoundingClientRect().left != null )? submenu.getBoundingClientRect().left : 0;

		// // Get the width of the window
		var windowWidth = window.innerWidth;
		
		// Check if the submenu goes beyond the right edge of the window
		if ( submenuPosition + submenuWidth > windowWidth ) {
			// Add a class to the submenu to make it appear to the left
			preSubmenu.classList.add('submenu-left-active');
			submenu.classList.add('submenu-left');
			
		}
		else {
			submenu.classList.remove('submenu-left');
			preSubmenu.classList.remove('submenu-left-active');
		}
		
		if ( menuItemArrow.length != 0 ) {
			menuItemArrow.forEach(function (arrow) {
				var menuItemArrowParent = arrow.closest('a');
				menuItemArrowParent.classList.add('icon-submenu-left');
			});
		}

	});
	
}

// Fallback for other dev.
function navFallback() {

	subMenuNavOffScreen();
	
	if ( window.matchMedia( '( min-width: 992px )' ).matches ) {
		return;
	}

	var userArgent = navigator.userAgent;

	if ( userArgent && ( userArgent.includes( 'Android' ) || userArgent.includes( 'Mobile' ) ) ) {
		return;
	}

	document.documentElement.classList.remove( 'cart-sidebar-open', 'sidebar-menu-open' );
	
}

document.addEventListener(
	'DOMContentLoaded',
	function() {
		if( (woostifyEvent.navEvent||0 ) ){
			return;
		}
		nav();
		sidebarMenu();
		sidebarMenu( '.woostify-nav-menu-widget .categories-navigation' );
		mobileMenuTab();
		woostifyEvent.navEvent = 1;
	}
);

window.addEventListener( 'resize', navFallback );
