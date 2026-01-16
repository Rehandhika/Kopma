import React from 'react'
import { Mail, MessageCircle, User } from 'lucide-react'

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { cn } from '@/lib/utils'
import { ORGANIZATION_PERIOD, ORGANIZATION_TABS } from '@/react/data/organization'

function getInitials(name) {
    const parts = String(name || '')
        .trim()
        .split(/\s+/)
        .filter(Boolean)
    if (!parts.length) return '—'
    const first = parts[0]?.[0] ?? ''
    const last = parts.length > 1 ? parts[parts.length - 1]?.[0] ?? '' : ''
    return (first + last).toUpperCase()
}

function PersonCard({
    person,
    ringClass,
    badgeClass,
    size = 'md',
}) {
    const avatarSize = size === 'lg' ? 'h-24 w-24' : 'h-16 w-16'
    const ringSize = size === 'lg' ? 'ring-4' : 'ring-4'
    const nameSize = size === 'lg' ? 'text-base sm:text-lg' : 'text-sm'

    return (
        <div className="flex flex-col items-center text-center">
            <div className={cn('rounded-full', ringSize, ringClass, 'ring-offset-4 ring-offset-background')}>
                <Avatar className={cn(avatarSize, 'bg-background/40')}>
                    {person.photoUrl ? (
                        <AvatarImage
                            src={person.photoUrl}
                            alt={person.name ? `Foto ${person.name}` : 'Foto anggota'}
                            loading={size === 'lg' ? 'eager' : 'lazy'}
                            decoding="async"
                        />
                    ) : null}
                    <AvatarFallback className="bg-background/40 text-muted-foreground">
                        <div className="flex flex-col items-center justify-center gap-1">
                            <User className="h-5 w-5 opacity-70" />
                            <span className="text-xs font-semibold tracking-widest">{getInitials(person.name)}</span>
                        </div>
                    </AvatarFallback>
                </Avatar>
            </div>

            <div className="mt-3">
                <span
                    className={cn(
                        'inline-flex items-center justify-center rounded-full border px-3 py-1 text-[11px] font-semibold tracking-wide',
                        badgeClass,
                    )}
                >
                    {person.role}
                </span>
            </div>

            <div className={cn('mt-2 font-extrabold tracking-wide text-foreground', nameSize)}>
                {String(person.name || '').toUpperCase()}
            </div>
        </div>
    )
}

function DivisionBlock({ division }) {
    const leaders = Array.isArray(division.leaders) ? division.leaders : []
    const members = Array.isArray(division.members) ? division.members : []
    const [activePerson, setActivePerson] = React.useState(null)

    const bphHero = division.key === 'bph' ? leaders[0] : null
    const remainingLeaders = division.key === 'bph' ? leaders.slice(1) : leaders

    const glassCardClass = cn(
        'group relative overflow-hidden',
        'rounded-3xl border border-border/70 bg-background/55 dark:bg-card/20 backdrop-blur-2xl',
        'shadow-[0_10px_30px_rgba(2,6,23,0.10)] dark:shadow-[0_18px_50px_rgba(0,0,0,0.35)]',
        'transition-transform',
    )

    return (
        <section className="py-10">
            <div className="text-center mb-8">
                <h3 className="text-2xl sm:text-3xl font-extrabold tracking-tight text-foreground">
                    {division.fullLabel}
                </h3>
                <div className="mt-2 text-muted-foreground text-sm">{division.label}</div>
            </div>

            {bphHero ? (
                <div className="flex justify-center mb-10">
                    <button
                        type="button"
                        onClick={() => setActivePerson(bphHero)}
                        className={cn(
                            glassCardClass,
                            'w-full max-w-md px-6 py-6 hover:scale-[1.01]',
                            'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/40 focus-visible:ring-offset-2 focus-visible:ring-offset-background',
                        )}
                    >
                        <div className="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                            <div className="absolute -top-20 left-1/2 h-44 w-[420px] -translate-x-1/2 rounded-full bg-primary/10 blur-3xl" />
                            <div className="absolute inset-0 bg-gradient-to-b from-primary/10 to-transparent" />
                        </div>
                        <div className="relative">
                        <PersonCard
                            person={bphHero}
                            ringClass={division.ringClass}
                            badgeClass={division.badgeClass}
                            size="lg"
                        />
                        </div>
                    </button>
                </div>
            ) : null}

            {remainingLeaders.length ? (
                <div className="mb-12">
                    <div className="text-center text-foreground/85 text-lg font-extrabold mb-6">
                        {division.key === 'bph' ? 'Pengurus Inti' : 'Koordinator'}
                    </div>
                    <div className="flex flex-wrap items-start justify-center gap-10">
                        {remainingLeaders.map((p) => (
                            <button
                                key={p.id}
                                type="button"
                                onClick={() => setActivePerson(p)}
                                className={cn(
                                    glassCardClass,
                                    'px-6 py-5 hover:scale-[1.01]',
                                    'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/40 focus-visible:ring-offset-2 focus-visible:ring-offset-background',
                                )}
                            >
                                <div className="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    <div className="absolute -top-20 left-1/2 h-44 w-[420px] -translate-x-1/2 rounded-full bg-foreground/5 blur-3xl dark:bg-white/10" />
                                    <div className="absolute inset-0 bg-gradient-to-b from-foreground/5 to-transparent dark:from-white/10" />
                                </div>
                                <div className="relative">
                                <PersonCard
                                    person={p}
                                    ringClass={division.ringClass}
                                    badgeClass={division.badgeClass}
                                    size="md"
                                />
                                </div>
                            </button>
                        ))}
                    </div>
                </div>
            ) : null}

            <div className="text-center text-foreground/85 text-lg font-extrabold mb-6">Semua Anggota</div>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {members.map((p) => (
                    <button
                        key={p.id}
                        type="button"
                        onClick={() => setActivePerson(p)}
                        className={cn(
                            'group text-left relative overflow-hidden',
                            'rounded-3xl border border-border/70 bg-background/55 dark:bg-card/20 backdrop-blur-2xl px-5 py-5 sm:px-6 sm:py-6',
                            'transition-transform hover:scale-[1.01]',
                            'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/40 focus-visible:ring-offset-2 focus-visible:ring-offset-background',
                            'shadow-[0_10px_30px_rgba(2,6,23,0.10)] dark:shadow-[0_18px_50px_rgba(0,0,0,0.35)]',
                        )}
                    >
                        <div className="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                            <div className="absolute -top-24 left-1/2 h-48 w-[460px] -translate-x-1/2 rounded-full bg-foreground/5 blur-3xl dark:bg-white/10" />
                            <div className="absolute inset-0 bg-gradient-to-b from-foreground/5 to-transparent dark:from-white/10" />
                        </div>
                        <div className="relative flex items-center gap-4">
                            <div className={cn('rounded-full ring-4', division.ringClass, 'ring-offset-4 ring-offset-background')}>
                                <Avatar className="h-12 w-12 sm:h-14 sm:w-14 bg-background/40">
                                    {p.photoUrl ? (
                                        <AvatarImage
                                            src={p.photoUrl}
                                            alt={p.name ? `Foto ${p.name}` : 'Foto anggota'}
                                            loading="lazy"
                                            decoding="async"
                                        />
                                    ) : null}
                                    <AvatarFallback className="bg-background/40 text-muted-foreground">
                                        <div className="flex flex-col items-center justify-center gap-1">
                                            <User className="h-5 w-5 opacity-70" />
                                            <span className="text-[10px] font-semibold tracking-widest">
                                                {getInitials(p.name)}
                                            </span>
                                        </div>
                                    </AvatarFallback>
                                </Avatar>
                            </div>
                            <div className="min-w-0">
                                <div className="text-sm sm:text-[15px] text-foreground font-extrabold tracking-wide truncate">
                                    {String(p.name || '').toUpperCase()}
                                </div>
                                <div className="mt-1">
                                    <span
                                        className={cn(
                                            'inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-semibold tracking-wide',
                                            division.badgeClass,
                                        )}
                                    >
                                        {p.role} {division.label}
                                    </span>
                                </div>
                                {p.tagline ? (
                                    <div className="mt-2 text-xs text-muted-foreground leading-snug">{p.tagline}</div>
                                ) : null}
                            </div>
                        </div>
                    </button>
                ))}
            </div>

            <Dialog open={Boolean(activePerson)} onOpenChange={(o) => !o && setActivePerson(null)}>
                <DialogContent className="max-w-2xl">
                    {activePerson ? (
                        <>
                            <DialogHeader>
                                <DialogTitle>
                                    <div className="flex flex-col sm:flex-row sm:items-center gap-5">
                                        <div className="relative shrink-0 self-center sm:self-auto">
                                            <div className="pointer-events-none absolute -inset-6 rounded-full bg-foreground/5 blur-2xl dark:bg-white/10" />
                                            <div className={cn('relative rounded-full ring-4 sm:ring-6', division.ringClass, 'ring-offset-4 ring-offset-background')}>
                                                <Avatar className="h-24 w-24 sm:h-28 sm:w-28 bg-background/40">
                                                    {activePerson.photoUrl ? (
                                                        <AvatarImage
                                                            src={activePerson.photoUrl}
                                                            alt={activePerson.name ? `Foto ${activePerson.name}` : 'Foto anggota'}
                                                            loading="eager"
                                                            decoding="async"
                                                        />
                                                    ) : null}
                                                    <AvatarFallback className="bg-background/40 text-muted-foreground">
                                                        <div className="flex flex-col items-center justify-center gap-1">
                                                            <User className="h-6 w-6 opacity-70" />
                                                            <span className="text-xs font-semibold tracking-widest">
                                                                {getInitials(activePerson.name)}
                                                            </span>
                                                        </div>
                                                    </AvatarFallback>
                                                </Avatar>
                                            </div>
                                        </div>

                                        <div className="min-w-0 text-center sm:text-left">
                                            <div className="text-base sm:text-xl font-extrabold tracking-wide">
                                                {String(activePerson.name || '').toUpperCase()}
                                            </div>
                                            <div className="mt-2 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                                <span
                                                    className={cn(
                                                        'inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-semibold tracking-wide',
                                                        division.badgeClass,
                                                    )}
                                                >
                                                    {activePerson.role}
                                                </span>
                                                <span className="inline-flex items-center rounded-full border border-border bg-muted/30 dark:bg-card/35 px-3 py-1 text-[11px] font-semibold tracking-wide text-muted-foreground">
                                                    {division.fullLabel}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </DialogTitle>
                            </DialogHeader>

                            <div className="mt-4 space-y-4">
                                {activePerson.tagline ? (
                                    <div className="text-sm text-muted-foreground">{activePerson.tagline}</div>
                                ) : null}

                                {activePerson.bio ? (
                                    <div className="rounded-2xl border border-border bg-muted/25 dark:bg-card/35 p-4 text-foreground leading-relaxed">
                                        {activePerson.bio}
                                    </div>
                                ) : null}

                                {Array.isArray(activePerson.highlights) && activePerson.highlights.length ? (
                                    <div className="flex flex-wrap gap-2">
                                        {activePerson.highlights.map((h) => (
                                            <span
                                                key={h}
                                                className={cn(
                                                    'inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-semibold tracking-wide',
                                                    division.badgeClass,
                                                )}
                                            >
                                                {h}
                                            </span>
                                        ))}
                                    </div>
                                ) : null}

                                {activePerson.links ? (
                                    <div className="flex flex-wrap gap-2">
                                        {activePerson.links.email ? (
                                            <a
                                                href={`mailto:${activePerson.links.email}`}
                                                className="inline-flex items-center gap-2 rounded-full border border-border bg-muted/25 dark:bg-card/35 px-4 py-2 text-xs font-semibold tracking-wide text-muted-foreground hover:bg-accent transition-colors"
                                            >
                                                <Mail className="h-4 w-4" />
                                                Email
                                            </a>
                                        ) : null}
                                        {activePerson.links.whatsapp ? (
                                            <a
                                                href={`https://wa.me/${String(activePerson.links.whatsapp).replace(/[^\d]/g, '')}`}
                                                target="_blank"
                                                rel="noreferrer"
                                                className="inline-flex items-center gap-2 rounded-full border border-border bg-muted/25 dark:bg-card/35 px-4 py-2 text-xs font-semibold tracking-wide text-muted-foreground hover:bg-accent transition-colors"
                                            >
                                                <MessageCircle className="h-4 w-4" />
                                                WhatsApp
                                            </a>
                                        ) : null}
                                    </div>
                                ) : null}
                            </div>
                        </>
                    ) : null}
                </DialogContent>
            </Dialog>
        </section>
    )
}

export default function OrganizationSection() {
    const [tab, setTab] = React.useState('all')

    const divisions = Array.isArray(ORGANIZATION_PERIOD?.divisions) ? ORGANIZATION_PERIOD.divisions : []
    const tabKeys = ORGANIZATION_TABS.map((t) => t.key)

    const renderContent = (key) => {
        if (key === 'all') return divisions.map((d) => <DivisionBlock key={d.key} division={d} />)
        const found = divisions.find((d) => d.key === key)
        return found ? <DivisionBlock division={found} /> : null
    }

    return (
        <div className="relative overflow-hidden rounded-3xl border border-border bg-card/20 backdrop-blur-2xl shadow-[0_24px_90px_rgba(0,0,0,0.18)]">
            <div className="absolute inset-0 bg-gradient-to-b from-indigo-50 via-background to-indigo-50 dark:from-[#0b1633] dark:via-[#122453] dark:to-[#0b1633]" />
            <div className="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-fuchsia-500/15 blur-3xl dark:bg-fuchsia-500/25" />
            <div className="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-indigo-500/12 blur-3xl dark:bg-indigo-500/25" />
            <div className="absolute inset-0 pointer-events-none">
                <div className="absolute -top-32 left-1/2 h-64 w-[760px] -translate-x-1/2 rounded-full bg-foreground/5 blur-3xl dark:bg-white/10" />
                <div className="absolute inset-0 bg-gradient-to-b from-foreground/5 via-transparent to-transparent opacity-60 dark:from-white/10" />
            </div>

            <div className="relative px-6 sm:px-10 py-12">
                <div className="text-center">
                    <div className="text-muted-foreground font-semibold tracking-wide">{ORGANIZATION_PERIOD.periodLabel}</div>
                    <div className="text-muted-foreground text-sm mt-1">{ORGANIZATION_PERIOD.subtitle}</div>
                    <div className="mt-6 text-3xl sm:text-4xl font-extrabold tracking-tight text-foreground">
                        Struktur Kepengurusan
                    </div>
                    <div className="mt-3 text-sm text-muted-foreground max-w-2xl mx-auto">
                        Ketuk kartu untuk melihat detail anggota. Desain glass iPhone dengan micro-interaction halus.
                    </div>
                </div>

                <div className="mt-8 flex justify-center">
                    <Tabs value={tab} onValueChange={setTab} className="w-full">
                        <div className="mx-auto max-w-5xl">
                            <div className="rounded-[28px] bg-gradient-to-r from-fuchsia-600 via-pink-600 to-violet-600 p-1 shadow-[0_12px_30px_rgba(0,0,0,0.28)]">
                                <TabsList
                                    className={cn(
                                        'w-full h-auto bg-background/10 backdrop-blur-xl p-1.5 rounded-[24px]',
                                        'flex justify-start md:justify-center gap-2',
                                        'overflow-x-auto',
                                        '[scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden',
                                    )}
                                >
                                    {ORGANIZATION_TABS.map((t) => (
                                        <TabsTrigger
                                            key={t.key}
                                            value={t.key}
                                            className={cn(
                                                'rounded-full px-4 py-2 text-sm font-bold tracking-wide',
                                                'text-white/90 data-[state=active]:text-white',
                                                'data-[state=active]:bg-white/20 data-[state=active]:shadow-sm',
                                                'hover:bg-white/15 transition-colors',
                                                'min-w-max',
                                            )}
                                        >
                                            {t.label}
                                        </TabsTrigger>
                                    ))}
                                </TabsList>
                            </div>
                        </div>

                        <div className="mt-10 mx-auto max-w-6xl">
                            {tabKeys.map((k) => (
                                <TabsContent key={k} value={k} className="mt-0">
                                    <div className="rounded-3xl border border-border/70 bg-card/25 backdrop-blur-xl px-6 sm:px-10">
                                        {renderContent(k)}
                                    </div>
                                </TabsContent>
                            ))}
                        </div>
                    </Tabs>
                </div>
            </div>
        </div>
    )
}
