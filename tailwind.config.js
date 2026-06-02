import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        'bg-teal-primary', 'bg-teal-light', 'bg-teal-dark', 'bg-teal-bg',
        'text-teal-primary', 'text-teal-light', 'text-teal-dark',
        'border-teal-primary', 'border-teal-light', 'border-teal-dark',
        'bg-coral', 'bg-coral-light', 'bg-coral-dark',
        'text-coral', 'text-coral-light', 'text-coral-dark',
        'border-coral', 'border-coral-light', 'border-coral-dark',
        'bg-gold', 'bg-gold-light', 'bg-gold-dark',
        'text-gold', 'text-gold-light', 'text-gold-dark',
        'border-gold', 'border-gold-light', 'border-gold-dark',
        'bg-cream', 'bg-surface-base', 'bg-surface-card',
        'text-sky', 'bg-sky',
        'shadow-card', 'shadow-teal-glow', 'shadow-coral-glow',
        'shadow-accent-glow', 'shadow-sky-glow',
        'hover:shadow-accent-glow', 'hover:shadow-teal-glow', 'hover:shadow-coral-glow',
        'hover:bg-teal-bg', 'hover:bg-coral/5',
        'rounded-card', 'rounded-pill',
        'border-l-teal-primary', 'border-l-coral', 'border-l-gold', 'border-l-sky',
        'stat-card', 'sidebar-link', 'sidebar-link-active', 'sidebar-link-active-coral',
        'sidebar-section-title', 'sidebar-overlay', 'sidebar-panel',
        'flash-message', 'btn-primary', 'btn-secondary', 'btn-danger',
        'animate-bounce-in',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                teal: {
                    primary: '#2BA8A2',
                    light: '#3CC4BD',
                    dark: '#1E8C86',
                    bg: '#E8F6F5',
                },
                gold: {
                    DEFAULT: '#FFD23F',
                    light: '#FFE47A',
                    dark: '#E6B800',
                },
                coral: {
                    DEFAULT: '#EF6C4A',
                    light: '#FF8A6A',
                    dark: '#D45233',
                },
                cream: '#FFF8E7',
                sky: '#5DADE2',
                surface: {
                    base: '#EFF8F7',
                    card: '#FFFFFF',
                },
            },
            borderRadius: {
                card: '24px',
                pill: '9999px',
            },
            boxShadow: {
                card: '0 4px 20px rgba(43,168,162,0.10)',
                'coral-glow': '0 4px 20px rgba(239,108,74,0.35)',
                'teal-glow': '0 4px 20px rgba(43,168,162,0.30)',
                'accent-glow': '0 4px 20px rgba(255,210,63,0.40)',
                'sky-glow': '0 4px 16px rgba(93,173,226,0.30)',
            },
        },
    },

    plugins: [forms],
};