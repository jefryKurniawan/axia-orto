import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { useOnlineStatus } from './hooks/useOnlineStatus'
import App from './App'
import './index.css'

function AppWithSync() {
  useOnlineStatus()
  return <App />
}

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: 1,
      staleTime: 30_000,
      refetchOnWindowFocus: false,
    },
  },
})

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <QueryClientProvider client={queryClient}>
      <BrowserRouter basename="/app">
        <AppWithSync />
      </BrowserRouter>
    </QueryClientProvider>
  </StrictMode>,
)
