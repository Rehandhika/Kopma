import React from 'react'
import { createRoot } from 'react-dom/client'

const HomePage = React.lazy(() => import('./pages/HomePage'))
const AboutPage = React.lazy(() => import('./pages/AboutPage'))
const ProductDetailPage = React.lazy(() => import('./pages/ProductDetailPage'))
const NotFoundPage = React.lazy(() => import('./pages/NotFoundPage'))

const rootElement = document.getElementById('react-public')

if (rootElement) {
    const page = rootElement.dataset.page || 'home'
    const slug = rootElement.dataset.slug || ''
    const initialDataElement = document.getElementById('public-initial-data')
    let initialData = null
    if (initialDataElement?.textContent) {
        try {
            initialData = JSON.parse(initialDataElement.textContent)
        } catch {
            initialData = null
        }
    }

    const Page = () => {
        if (page === 'about') return <AboutPage initialData={initialData} />
        if (page === 'product') return <ProductDetailPage slug={slug} initialData={initialData} />
        if (!['home', 'about', 'product'].includes(page)) return <NotFoundPage />
        return <HomePage initialData={initialData} />
    }

    createRoot(rootElement).render(
        <React.StrictMode>
            <React.Suspense fallback={<div className="min-h-screen bg-background" />}>
                <Page />
            </React.Suspense>
        </React.StrictMode>,
    )
}

