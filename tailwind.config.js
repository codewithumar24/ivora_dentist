/**
 * The palette and type stack that used to live in an inline `tailwind.config`
 * script tag next to the CDN build. Same values — now compiled ahead of time.
 */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                ink: '#0A1614',
                pine: '#10403B',
                jade: '#1C7A6E',
                mint: '#A8E6D8',
                porcelain: '#F4F7F5',
                iris: '#4F5BD5',
            },
            fontFamily: {
                display: ['"Bricolage Grotesque"', 'sans-serif'],
                sans: ['"Inter Tight"', 'system-ui', 'sans-serif'],
                mono: ['"JetBrains Mono"', 'monospace'],
            },
            maxWidth: { shell: '1240px' },
        },
    },
    plugins: [],
};
