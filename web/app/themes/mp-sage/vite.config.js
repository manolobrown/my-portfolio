import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin'
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin';

export default defineConfig({
  base: '/app/themes/mp-sage/public/build/',

  plugins: [
    tailwindcss(),
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/editor.css',
        'resources/js/editor.js',
      ],
      refresh: true,
    }),

    wordpressPlugin(),

    // Generate the theme.json file in the public/build/assets directory
    // based on the Tailwind config and the theme.json file from base theme folder
    wordpressThemeJson({
      disableTailwindColors: false,
      disableTailwindFonts: false,
      disableTailwindFontSizes: false,
    }),
  ],

  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles': '/resources/css',
      '@fonts': '/resources/fonts',
      '@images': '/resources/images',
    },
  },

  // Production build optimizations
  build: {
    // Generate manifest for cache busting
    manifest: 'manifest.json',

    // Output directory
    outDir: 'public/build',

    // Clean output directory before build
    emptyOutDir: true,

    // Enable CSS code splitting
    cssCodeSplit: true,

    // Sourcemap configuration (disable in production for smaller builds)
    sourcemap: false,

    // Minification
    minify: 'esbuild',

    // Chunk size warning limit (500kb)
    chunkSizeWarningLimit: 500,

    // Rollup options for advanced bundling
    rollupOptions: {
      output: {
        // Manual chunks for vendor separation
        manualChunks: (id) => {
          // Vendor chunk for node_modules
          if (id.includes('node_modules')) {
            // WordPress dependencies in separate chunk
            if (id.includes('@wordpress')) {
              return 'wordpress-vendor';
            }
            return 'vendor';
          }
        },

        // Asset file naming with hash for cache busting
        assetFileNames: (assetInfo) => {
          const info = assetInfo.name.split('.');
          const ext = info[info.length - 1];

          if (/\.(png|jpe?g|svg|gif|tiff|bmp|ico)$/i.test(assetInfo.name)) {
            return `images/[name]-[hash][extname]`;
          }

          if (/\.(woff2?|eot|ttf|otf)$/i.test(assetInfo.name)) {
            return `fonts/[name]-[hash][extname]`;
          }

          return `assets/[name]-[hash][extname]`;
        },

        // Chunk file naming
        chunkFileNames: 'js/[name]-[hash].js',
        entryFileNames: 'js/[name]-[hash].js',
      },
    },

    // Target modern browsers for smaller bundles
    target: 'es2020',

    // Optimize dependencies
    commonjsOptions: {
      transformMixedEsModules: true,
    },
  },

  // Development server optimizations
  server: {
    // Faster HMR
    hmr: {
      host: 'localhost',
    },
    // Watch options
    watch: {
      usePolling: false,
    },
  },

  // Optimize dependencies
  optimizeDeps: {
    include: ['@wordpress/dom-ready'],
  },
})
