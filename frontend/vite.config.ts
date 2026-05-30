import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
  plugins: [
    react(),
    tailwindcss(),
    VitePWA({
      registerType: 'autoUpdate',
      includeAssets: ['favicon.svg'],
      manifest: {
        name: 'AxiaOrto - Clinic ERP',
        short_name: 'AxiaOrto',
        description: 'Sistem Informasi Klinik Ortotik-Prostetik',
        theme_color: '#0f172a',
        background_color: '#f8fafc',
        display: 'standalone',
        start_url: '/app/',
        scope: '/app/',
      },
      workbox: {
        globPatterns: ['**/*.{js,css,svg,png,woff2}'],
        navigateFallback: null,
        // No runtimeCaching for /api — TanStack Query + Dexie.js handle data caching.
        // SW caching API calls causes "no-response" errors when backend is unreachable.
        ignoreURLParametersMatching: [/^page$/, /^search$/],
      },
    }),
  ],
  base: '/app/',
  build: {
    outDir: '../public/app',
    emptyOutDir: true,
    rollupOptions: {
      output: {
        manualChunks(id: string) {
          if (id.includes('node_modules/react-dom') || id.includes('node_modules/react/')) {
            return 'react'
          }
          if (id.includes('node_modules/react-router')) {
            return 'router'
          }
        }
      }
    }
  },
  server: {
    port: 5173,
    proxy: {
      '/api': 'http://localhost:8000'
    }
  }
})
