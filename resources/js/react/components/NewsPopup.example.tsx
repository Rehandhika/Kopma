/**
 * Example usage of NewsPopup component
 * 
 * This file demonstrates how to integrate the NewsPopup component
 * into your application.
 * 
 * IMPORTANT: News images MUST be in 9:16 aspect ratio (portrait/vertical format)
 * This is the Instagram Story format - 1080x1920 pixels is recommended.
 */

import * as React from 'react'
import NewsPopup, { type NewsItem } from './NewsPopup'

export default function NewsPopupExample() {
    const [showPopup, setShowPopup] = React.useState(false)

    // Example news data
    // NOTE: All images should be 9:16 aspect ratio (portrait)
    // Title and content are OPTIONAL - you can have image-only news
    const exampleNews: NewsItem[] = [
        {
            id: 1,
            title: 'Pengumuman Penting',
            content: '<p>Ini adalah contoh pengumuman penting untuk pengguna.</p>',
            link: 'https://example.com/announcement',
            images: {
                default: '/images/news/news-1.jpg', // 9:16 aspect ratio
                mobile: '/images/news/news-1-mobile.jpg', // 1080x1920
                tablet: '/images/news/news-1-tablet.jpg', // 1080x1920
                desktop: '/images/news/news-1-desktop.jpg', // 1080x1920
            },
            published_at: '2026-01-27T10:00:00Z',
        },
        {
            id: 2,
            title: 'Update Sistem',
            // No content - title only with image
            link: 'https://example.com/update',
            images: {
                default: '/images/news/news-2.jpg', // 9:16 aspect ratio
            },
            published_at: '2026-01-26T15:30:00Z',
        },
        {
            id: 3,
            // No title, no content - image only (like Instagram story)
            // When link is provided, clicking the image will redirect
            link: 'https://example.com/promo',
            images: {
                default: '/images/news/news-3.jpg', // 9:16 aspect ratio
            },
            published_at: '2026-01-25T09:00:00Z',
        },
        {
            id: 4,
            title: 'Informasi Terbaru',
            content: '<p>Berikut adalah informasi terbaru yang perlu Anda ketahui.</p>',
            // No link - image is not clickable
            images: {
                default: null, // News without image (rare case)
            },
            published_at: '2026-01-24T09:00:00Z',
        },
    ]

    // Fetch news from API
    React.useEffect(() => {
        async function fetchNews() {
            try {
                const response = await fetch('/api/publik/berita')
                const data = await response.json()
                
                if (data.data && data.data.length > 0) {
                    setShowPopup(true)
                }
            } catch (error) {
                console.error('Failed to fetch news:', error)
            }
        }

        fetchNews()
    }, [])

    const handleClose = () => {
        setShowPopup(false)
    }

    return (
        <div>
            {/* Your page content */}
            <h1>Welcome to the Homepage</h1>
            
            {/* NewsPopup will automatically show if there are unviewed news */}
            {showPopup && (
                <NewsPopup 
                    news={exampleNews} 
                    onClose={handleClose} 
                />
            )}
        </div>
    )
}

/**
 * Integration Notes:
 * 
 * CRITICAL: IMAGE FORMAT
 * =====================
 * ALL news images MUST be in 9:16 aspect ratio (portrait/vertical format)
 * - Recommended resolution: 1080x1920 pixels (Instagram Story format)
 * - This is a VERTICAL format, not horizontal
 * - Images will fill the entire popup container
 * 
 * CRITICAL: SIZE
 * ==============
 * - Mobile/Tablet: 65vh (65% of viewport HEIGHT)
 * - Desktop: 80vh (80% of viewport HEIGHT)
 * - Width is calculated automatically using aspect-[9/16] CSS
 * - This maintains perfect 9:16 aspect ratio on all devices
 * - NO SCROLLING - all content fits in one view
 * - Content overlays on image with gradient background
 * 
 * OPTIONAL CONTENT
 * ================
 * - Title is OPTIONAL (can be omitted for image-only news)
 * - Content is OPTIONAL (can be omitted for image-only news)
 * - You can create pure visual stories like Instagram Stories
 * - If both title and content are omitted, only the image will be shown
 * 
 * 1. Session Storage:
 *    - The component automatically tracks viewed news using sessionStorage
 *    - Key: 'viewedNewsIds'
 *    - Only unviewed news will be shown on subsequent visits within the same session
 * 
 * 2. Navigation:
 *    - Next/Previous buttons (minimal, icon-only)
 *    - Dot indicators show current position
 *    - Position text (e.g., "1/3" format)
 *    - Loop navigation: last item → first item, first item → last item
 *    - All controls overlay on image at bottom
 * 
 * 3. Keyboard Support:
 *    - ArrowRight: Next news
 *    - ArrowLeft: Previous news
 *    - Escape: Close popup
 * 
 * 4. Responsive Design (65-80% of screen HEIGHT with 9:16 ratio):
 *    - Mobile/Tablet: 65vh height (65% of viewport height)
 *    - Desktop: 80vh height (80% of viewport height)
 *    - Width: Auto-calculated using aspect-[9/16] CSS class
 *    - Perfect 9:16 aspect ratio maintained on all devices
 *    - NO SCROLLING - everything fits in view
 *    - Prevents background scrolling when open
 *    - Lighter overlay on desktop (60% opacity vs 80% on mobile)
 * 
 * 5. Image Handling (9:16 ASPECT RATIO):
 *    - MUST use 9:16 aspect ratio images (portrait/vertical)
 *    - Image fills entire popup container
 *    - Supports responsive images (mobile, tablet, desktop variants)
 *    - All variants should maintain 9:16 aspect ratio
 *    - Lazy loading for performance
 *    - Graceful fallback if image fails to load
 * 
 * 6. Story-like Experience:
 *    - Close button (tiny, top-right corner)
 *    - Image fills entire space
 *    - Text overlays on image with gradient (bottom)
 *    - Navigation overlays at very bottom
 *    - NO SCROLLING - compact single view
 *    - Can be pure image without text (like Instagram Stories)
 * 
 * 7. Accessibility:
 *    - ARIA labels for navigation buttons
 *    - Screen reader support
 *    - Keyboard navigation
 *    - Focus management
 * 
 * 8. Professional Appearance:
 *    - Mobile/Tablet: 65% of screen HEIGHT
 *    - Desktop: 80% of screen HEIGHT (larger, more prominent)
 *    - Width automatically follows 9:16 aspect ratio
 *    - Large shadow (shadow-2xl) for depth
 *    - Rounded corners (rounded-xl on mobile, rounded-2xl on desktop)
 *    - All content overlays on image
 *    - Elegant presence
 *    - NO SCROLLING needed
 */
