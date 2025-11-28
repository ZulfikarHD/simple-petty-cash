<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import { type BreadcrumbItem } from '@/types';
import { useAppearance } from '@/composables/useAppearance';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';
import { dashboard } from '@/routes';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Check, Monitor, Moon, Palette, Sun } from 'lucide-vue-next';

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Pengaturan Tampilan', href: edit().url },
];

const { appearance, updateAppearance } = useAppearance();

const themes = [
    {
        value: 'light',
        icon: Sun,
        label: 'Terang',
        description: 'Tampilan cerah untuk penggunaan siang hari',
        gradient: 'from-amber-400 to-orange-500',
        bgPreview: 'bg-white',
    },
    {
        value: 'dark',
        icon: Moon,
        label: 'Gelap',
        description: 'Tampilan gelap untuk penggunaan malam hari',
        gradient: 'from-slate-600 to-slate-800',
        bgPreview: 'bg-gray-900',
    },
    {
        value: 'system',
        icon: Monitor,
        label: 'Sistem',
        description: 'Mengikuti pengaturan sistem perangkat Anda',
        gradient: 'from-blue-500 to-indigo-600',
        bgPreview: 'bg-gradient-to-r from-white to-gray-900',
    },
] as const;
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Pengaturan Tampilan" />

        <SettingsLayout>
            <div class="flex flex-col gap-6">
                <!-- Appearance Card -->
                <Card class="overflow-hidden">
                    <CardHeader
                        class="border-b bg-gradient-to-r from-violet-50 to-purple-50 dark:from-violet-950/30 dark:to-purple-950/30"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-lg"
                            >
                                <Palette class="h-6 w-6" />
                            </div>
                            <div>
                                <CardTitle>Tema Tampilan</CardTitle>
                                <CardDescription>
                                    Pilih tema yang nyaman untuk mata Anda
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="p-6">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <button
                                v-for="theme in themes"
                                :key="theme.value"
                                @click="updateAppearance(theme.value)"
                                class="group relative overflow-hidden rounded-2xl border-2 p-4 text-left transition-all duration-300 active:scale-[0.97]"
                                :class="[
                                    appearance === theme.value
                                        ? 'border-blue-500 bg-blue-50 shadow-lg dark:bg-blue-950/30'
                                        : 'border-gray-200 hover:border-gray-300 hover:shadow-md dark:border-gray-700 dark:hover:border-gray-600',
                                ]"
                            >
                                <!-- Check indicator -->
                                <div
                                    v-if="appearance === theme.value"
                                    class="absolute right-3 top-3 flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-white"
                                >
                                    <Check class="h-4 w-4" />
                                </div>

                                <!-- Theme Preview -->
                                <div
                                    class="mb-4 flex h-20 items-center justify-center overflow-hidden rounded-xl shadow-inner"
                                    :class="theme.bgPreview"
                                >
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br text-white shadow-lg"
                                        :class="theme.gradient"
                                    >
                                        <component
                                            :is="theme.icon"
                                            class="h-6 w-6"
                                        />
                                    </div>
                                </div>

                                <h3
                                    class="font-semibold"
                                    :class="[
                                        appearance === theme.value
                                            ? 'text-blue-700 dark:text-blue-300'
                                            : 'text-gray-900 dark:text-white',
                                    ]"
                                >
                                    {{ theme.label }}
                                </h3>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ theme.description }}
                                </p>
                            </button>
                        </div>

                        <!-- Info -->
                        <div
                            class="mt-6 rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/50"
                        >
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Catatan:</span>
                                Perubahan tema akan langsung diterapkan dan tersimpan
                                secara otomatis.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
