<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, store } from '@/actions/App/Http/Controllers/UserController';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, Form } from '@inertiajs/vue3';
import { ArrowLeft, Save, ShieldCheck, User } from 'lucide-vue-next';
import { ref } from 'vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/InputError.vue';
import { dashboard } from '@/routes';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Kelola User', href: index().url },
    { title: 'Tambah User', href: '#' },
];

const isAdmin = ref(false);
</script>

<template>
    <Head title="Tambah User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link :href="index().url">
                    <Button variant="ghost" size="icon" class="shrink-0">
                        <ArrowLeft class="h-5 w-5" />
                    </Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Tambah User Baru
                    </h1>
                    <p class="text-sm text-gray-500">
                        Isi form di bawah untuk menambahkan user baru
                    </p>
                </div>
            </div>

            <!-- Form Card -->
            <Card
                class="animate-in fade-in slide-in-from-bottom-4 mx-auto w-full max-w-2xl duration-500"
            >
                <CardHeader class="border-b bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg"
                        >
                            <User class="h-6 w-6" />
                        </div>
                        <div>
                            <CardTitle>Informasi User</CardTitle>
                            <CardDescription>
                                Data akun pengguna baru
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="p-6">
                    <Form
                        v-bind="store.form()"
                        class="space-y-6"
                        v-slot="{ errors, processing }"
                    >
                        <div class="space-y-4">
                            <!-- Nama -->
                            <div class="space-y-2">
                                <Label for="name">Nama Lengkap</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    type="text"
                                    placeholder="Masukkan nama lengkap"
                                    required
                                    autocomplete="name"
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    placeholder="contoh@email.com"
                                    required
                                    autocomplete="email"
                                />
                                <InputError :message="errors.email" />
                            </div>

                            <!-- Password -->
                            <div class="space-y-2">
                                <Label for="password">Password</Label>
                                <Input
                                    id="password"
                                    name="password"
                                    type="password"
                                    placeholder="Minimal 8 karakter"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError :message="errors.password" />
                            </div>

                            <!-- Password Confirmation -->
                            <div class="space-y-2">
                                <Label for="password_confirmation"
                                    >Konfirmasi Password</Label
                                >
                                <Input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    placeholder="Ulangi password"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError
                                    :message="errors.password_confirmation"
                                />
                            </div>

                            <!-- Role Admin -->
                            <div
                                class="flex items-center justify-between rounded-xl border bg-gray-50 p-4 dark:bg-gray-900/50"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-lg"
                                        :class="
                                            isAdmin
                                                ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400'
                                                : 'bg-gray-200 text-gray-500 dark:bg-gray-700'
                                        "
                                    >
                                        <ShieldCheck class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            Jadikan Admin
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Admin dapat mengelola semua data dan user
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Checkbox
                                        id="is_admin"
                                        name="is_admin"
                                        :checked="isAdmin"
                                        @update:checked="isAdmin = $event"
                                        :value="isAdmin ? '1' : '0'"
                                    />
                                    <input
                                        type="hidden"
                                        name="is_admin"
                                        :value="isAdmin ? '1' : '0'"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div
                            class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end"
                        >
                            <Link :href="index().url">
                                <Button
                                    type="button"
                                    variant="outline"
                                    class="w-full sm:w-auto"
                                >
                                    Batal
                                </Button>
                            </Link>
                            <Button
                                type="submit"
                                :disabled="processing"
                                class="w-full gap-2 sm:w-auto"
                            >
                                <Save class="h-4 w-4" />
                                {{ processing ? 'Menyimpan...' : 'Simpan User' }}
                            </Button>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

