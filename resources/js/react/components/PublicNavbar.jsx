import React from 'react'
import { Home, Info, LogIn, Menu } from 'lucide-react'

import { Button } from '@/components/ui/button'
import { Menubar, MenubarContent, MenubarItem, MenubarMenu, MenubarTrigger } from '@/components/ui/menubar'
import StoreStatusPopover from '@/react/components/StoreStatusPopover'
import ThemeToggle from '@/react/components/ThemeToggle'

function useActivePath() {
    const [path, setPath] = React.useState(() => window.location.pathname)

    React.useEffect(() => {
        const onPopState = () => setPath(window.location.pathname)
        window.addEventListener('popstate', onPopState)
        return () => window.removeEventListener('popstate', onPopState)
    }, [])

    return path
}

export default function PublicNavbar() {
    const path = useActivePath()
    const isAbout = path === '/about'

    return (
        <div className="sticky top-6 z-50 px-4 mb-8">
            <div className="max-w-7xl mx-auto">
                <div className="relative rounded-2xl border border-border bg-background/70 backdrop-blur-xl shadow-[0_4px_30px_rgba(0,0,0,0.1)] px-4 md:px-6 py-3 md:py-4 flex items-center justify-between">
                    <a href="/" className="flex items-center gap-3 group">
                        <div className="relative w-10 h-10 flex items-center justify-center">
                            <div className="absolute inset-0 bg-gradient-to-tr from-indigo-600 to-purple-600 rounded-xl rotate-3 transition-transform group-hover:rotate-6" />
                            <div className="absolute inset-0 bg-background rounded-xl rotate-3 scale-[0.9]" />
                            <span className="relative font-semibold text-xl text-foreground">S</span>
                        </div>
                        <div className="hidden sm:flex flex-col">
                            <span className="font-semibold text-lg text-foreground tracking-tight leading-none group-hover:text-primary transition-colors">
                                SIKOPMA
                            </span>
                            <span className="text-[10px] uppercase tracking-[0.2em] text-muted-foreground hidden sm:block">
                                Future Store
                            </span>
                        </div>
                    </a>

                    <div className="absolute inset-y-0 left-1/2 -translate-x-1/2 flex items-center">
                        <div className="scale-90 md:scale-100">
                            <StoreStatusPopover />
                        </div>
                    </div>

                    <div className="hidden md:flex items-center justify-end gap-2">
                        <Button
                            asChild
                            variant="ghost"
                            className={[
                                isAbout ? '' : 'bg-accent text-accent-foreground',
                                'text-muted-foreground hover:text-foreground',
                            ].join(' ')}
                        >
                            <a href="/">Katalog</a>
                        </Button>
                        <Button
                            asChild
                            variant="ghost"
                            className={[
                                isAbout ? 'bg-accent text-accent-foreground' : '',
                                'text-muted-foreground hover:text-foreground',
                            ].join(' ')}
                        >
                            <a href="/about">Tentang</a>
                        </Button>
                        <ThemeToggle />
                        <Button
                            asChild
                            className="rounded-xl bg-indigo-600/15 hover:bg-indigo-600/25 text-indigo-700 dark:text-indigo-200 hover:text-indigo-900 dark:hover:text-white border border-indigo-500/30"
                            variant="outline"
                        >
                            <a href="/admin/login">Login</a>
                        </Button>
                    </div>

                    <div className="flex md:hidden items-center gap-2">
                        <ThemeToggle />
                        <Menubar className="border-border bg-background/60">
                            <MenubarMenu>
                                <MenubarTrigger className="px-2.5 data-[state=open]:bg-accent">
                                    <Menu className="h-4 w-4" />
                                </MenubarTrigger>
                                <MenubarContent align="end" className="min-w-44">
                                    <MenubarItem asChild>
                                        <a href="/" className="flex items-center gap-2">
                                            <Home className="h-4 w-4 text-muted-foreground" />
                                            Katalog
                                        </a>
                                    </MenubarItem>
                                    <MenubarItem asChild>
                                        <a href="/about" className="flex items-center gap-2">
                                            <Info className="h-4 w-4 text-muted-foreground" />
                                            Tentang
                                        </a>
                                    </MenubarItem>
                                    <MenubarItem asChild>
                                        <a href="/admin/login" className="flex items-center gap-2">
                                            <LogIn className="h-4 w-4 text-muted-foreground" />
                                            Login
                                        </a>
                                    </MenubarItem>
                                </MenubarContent>
                            </MenubarMenu>
                        </Menubar>
                    </div>
                </div>
            </div>
        </div>
    )
}
