<script setup lang="ts">
import { toUrl, urlIsActive } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editPassword } from '@/routes/user-password';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ChevronRight, Key, Palette, Settings, User } from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';

interface SettingsNavItem extends NavItem {
    icon: typeof User;
    description: string;
}

const sidebarNavItems: SettingsNavItem[] = [
    {
        title: 'Profil',
        href: editProfile(),
        icon: User,
        description: 'Kelola nama dan email',
    },
    {
        title: 'Password',
        href: editPassword(),
        icon: Key,
        description: 'Ubah password akun',
    },
    {
        title: 'Tampilan',
        href: editAppearance(),
        icon: Palette,
        description: 'Tema dan preferensi visual',
    },
];

const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
        <!-- Header -->
        <div class="animate-in fade-in slide-in-from-bottom-4 duration-300">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-gray-600 to-slate-700 text-white shadow-lg"
                >
                    <Settings class="h-6 w-6" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Pengaturan
                    </h1>
                    <p class="text-sm text-gray-500">
                        Kelola profil dan preferensi akun Anda
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-6 lg:flex-row">
            <!-- Navigation - Hidden on Mobile, Sidebar for Desktop -->
            <aside class="hidden w-full shrink-0 lg:block lg:w-64">
                <Card
                    class="animate-in fade-in slide-in-from-left-4 overflow-hidden duration-300"
                    style="animation-delay: 50ms"
                >
                    <CardContent class="p-0">
                        <nav class="divide-y dark:divide-gray-800">
                            <Link
                                v-for="(item, idx) in sidebarNavItems"
                                :key="toUrl(item.href)"
                                :href="item.href"
                                class="flex items-center gap-3 p-4 transition-all duration-200 active:scale-[0.98]"
                                :class="[
                                    urlIsActive(item.href, currentPath)
                                        ? 'bg-blue-50 dark:bg-blue-950/30'
                                        : 'hover:bg-gray-50 dark:hover:bg-gray-900/50',
                                ]"
                            >
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl transition-colors"
                                    :class="[
                                        urlIsActive(item.href, currentPath)
                                            ? 'bg-blue-500 text-white'
                                            : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                    ]"
                                >
                                    <component :is="item.icon" class="h-5 w-5" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="font-medium"
                                        :class="[
                                            urlIsActive(item.href, currentPath)
                                                ? 'text-blue-600 dark:text-blue-400'
                                                : 'text-gray-900 dark:text-white',
                                        ]"
                                    >
                                        {{ item.title }}
                                    </p>
                                    <p class="truncate text-xs text-gray-500">
                                        {{ item.description }}
                                    </p>
                                </div>
                                <ChevronRight
                                    class="h-5 w-5 shrink-0 text-gray-400"
                                />
                            </Link>
                        </nav>
                    </CardContent>
                </Card>
            </aside>

            <!-- Mobile Quick Navigation -->
            <div class="flex gap-2 overflow-x-auto pb-2 lg:hidden">
                <Link
                    v-for="item in sidebarNavItems"
                    :key="toUrl(item.href)"
                    :href="item.href"
                    class="flex shrink-0 items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition-all active:scale-95"
                    :class="[
                        urlIsActive(item.href, currentPath)
                            ? 'bg-blue-500 text-white shadow-lg'
                            : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                    ]"
                >
                    <component :is="item.icon" class="h-4 w-4" />
                    {{ item.title }}
                </Link>
            </div>

            <!-- Content Area -->
            <div
                class="animate-in fade-in slide-in-from-right-4 min-w-0 flex-1 duration-300 lg:animation-delay-100"
            >
                <slot />
            </div>
        </div>
    </div>
</template>
