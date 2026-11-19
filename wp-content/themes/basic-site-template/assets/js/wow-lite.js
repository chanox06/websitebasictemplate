/**
 * wow-lite - a tiny, dependency-free replacement for WOW.js.
 *
 * Reveals the homepage content as it scrolls into view, mirroring the
 * WOW.js + animate.css behaviour of the previous Your Company website.
 *
 * The homepage is composed from Site Editor templates/parts, so instead of
 * requiring `wow` classes in the markup this script tags the section blocks
 * automatically and then reveals them with IntersectionObserver.
 *
 * You may still add the classes manually anywhere, e.g.:
 *   <div class="wow fadeInUp delay-2">...</div>
 * Optional attributes (any element): data-wow-duration / data-wow-delay.
 *
 * Available effects:
 *   fadeIn, fadeInUp, fadeInDown, fadeInLeft, fadeInRight,
 *   slideInUp, slideInDown, slideInLeft, slideInRight,
 *   zoomIn, zoomInUp, flipInX
 * (combine with delay-1..6, slow, slower)
 */
( function () {
    'use strict';

    var OPTIONS = {
        threshold: 0.15,
        rootMargin: '0px 0px -8% 0px'
    };

    // Homepage content sections that should reveal on scroll.
    var SECTIONS = '.main-content, .main-sub-content, .sub-content-bottom, ' +
        '.bottom-content, .sub-bottom-content, .foooter-sub-content';

    /**
     * Add the WOW classes to an element (once).
     *
     * @param {Element} el     Target element.
     * @param {string}  effect Animation name, e.g. "fadeInUp".
     * @param {number}  delay  Stagger index (0 = none, mapped to delay-1..6).
     */
    function tag( el, effect, delay ) {
        if ( ! el || el.classList.contains( 'wow' ) ) {
            return;
        }

        el.classList.add( 'wow', effect );

        if ( delay > 0 ) {
            el.classList.add( 'delay-' + Math.min( delay, 6 ) );
        }
    }

    /**
     * Is the element already inside a tagged ancestor (up to a boundary)?
     *
     * @param {Element} el       Element to test.
     * @param {Element} boundary Stop climbing at this ancestor.
     * @return {boolean} True when a tagged ancestor exists.
     */
    function withinTagged( el, boundary ) {
        var node = el.parentElement;

        while ( node && node !== boundary ) {
            if ( node.classList.contains( 'wow' ) ) {
                return true;
            }
            node = node.parentElement;
        }

        return false;
    }

    /**
     * Tag the meaningful blocks of one homepage section.
     *
     * @param {Element} section A homepage section wrapper.
     */
    function tagSection( section, sectionIndex ) {
        var i;

        sectionIndex = sectionIndex || 0;

        // Leaf "cards": columns that do not contain nested columns.
        var columns = section.querySelectorAll( '.wp-block-column' );

        for ( i = 0; i < columns.length; i++ ) {
            var col = columns[ i ];

            if ( col.querySelector( '.wp-block-column' ) ) {
                continue;
            }

            var index = 0;
            var total = 1;
            var parent = col.parentElement;

            if ( parent ) {
                var siblings = Array.prototype.filter.call( parent.children, function ( child ) {
                    return child.classList.contains( 'wp-block-column' );
                } );

                index = siblings.indexOf( col );
                if ( index < 0 ) {
                    index = 0;
                }
                total = siblings.length;
            }

            if ( total === 2 ) {
                // Two-column rows slide in from the sides (left / right).
                tag( col, index === 0 ? 'fadeInLeft' : 'fadeInRight', 0 );
            } else {
                // Card grids rise up with a stagger.
                tag( col, 'fadeInUp', index );
            }
        }

        // Section headings that are not already inside a card.
        var headings = section.querySelectorAll( 'h1, h2, h3, h4' );

        for ( i = 0; i < headings.length; i++ ) {
            if ( withinTagged( headings[ i ], section ) ) {
                continue;
            }

            // Alternate section headings so they slide in from both sides.
            tag( headings[ i ], sectionIndex % 2 === 0 ? 'fadeInLeft' : 'fadeInRight', 0 );
        }

        // Standalone button groups.
        var buttons = section.querySelectorAll( '.wp-block-buttons' );

        for ( i = 0; i < buttons.length; i++ ) {
            if ( withinTagged( buttons[ i ], section ) ) {
                continue;
            }
            tag( buttons[ i ], 'fadeInUp', 1 );
        }

        // Standalone cover blocks (background panels).
        var covers = section.querySelectorAll( '.wp-block-cover' );

        for ( i = 0; i < covers.length; i++ ) {
            if ( withinTagged( covers[ i ], section ) ) {
                continue;
            }

            // Alternate the panel slide direction too.
            tag( covers[ i ], sectionIndex % 2 === 0 ? 'fadeInRight' : 'fadeInLeft', 0 );
        }
    }

    /**
     * Automatically tag the homepage hero and content sections.
     */
    function tagHomepage() {
        // Fade the hero in as a whole (its slides animate on their own).
        tag( document.querySelector( '.banner-area' ), 'fadeIn', 0 );

        var sections = document.querySelectorAll( SECTIONS );

        for ( var i = 0; i < sections.length; i++ ) {
            // The section classes are also reused on inner columns; only
            // process the outermost wrapper of each section.
            if ( sections[ i ].parentElement && sections[ i ].parentElement.closest( SECTIONS ) ) {
                continue;
            }

            tagSection( sections[ i ], i );
        }
    }

    /**
     * Reveal every element at once (safe fallback).
     *
     * @param {NodeList} elements Elements to reveal.
     */
    function revealAll( elements ) {
        for ( var i = 0; i < elements.length; i++ ) {
            elements[ i ].classList.add( 'animated' );
        }
    }

    /**
     * Apply optional data-wow-duration / data-wow-delay overrides.
     *
     * @param {Element} el A `.wow` element.
     */
    function applyTiming( el ) {
        var duration = parseFloat( el.getAttribute( 'data-wow-duration' ) );
        var delay = parseFloat( el.getAttribute( 'data-wow-delay' ) );

        if ( ! isNaN( duration ) ) {
            el.style.setProperty( 'animation-duration', duration + 'ms', 'important' );
        }

        if ( ! isNaN( delay ) ) {
            el.style.setProperty( 'animation-delay', delay + 'ms', 'important' );
        }
    }

    /**
     * Initialise the reveal behaviour.
     */
    function init() {
        try {
            tagHomepage();
        } catch ( err ) {
            // Never let tagging errors leave content hidden.
        }

        var elements = document.querySelectorAll( '.wow' );

        if ( ! elements.length ) {
            return;
        }

        var reduceMotion = window.matchMedia &&
            window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

        // Respect reduced-motion preferences and legacy browsers: show at once.
        if ( reduceMotion || ! ( 'IntersectionObserver' in window ) ) {
            revealAll( elements );
            return;
        }

        var observer = new IntersectionObserver( function ( entries ) {
            entries.forEach( function ( entry ) {
                if ( ! entry.isIntersecting ) {
                    return;
                }

                entry.target.classList.add( 'animated' );
                observer.unobserve( entry.target );
            } );
        }, OPTIONS );

        for ( var i = 0; i < elements.length; i++ ) {
            applyTiming( elements[ i ] );
            observer.observe( elements[ i ] );
        }
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }

} )();
