<script setup lang="ts">
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/user-password';
import { Form, Head } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { Check, Key, Lock, Save, ShieldCheck } from 'lucide-vue-next';
import { dashboard } from '@/routes';

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Pengaturan Password', href: edit().url },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Pengaturan Password" />

        <SettingsLayout>
            <div class="flex flex-col gap-6">
                <!-- Password Card -->
                <Card class="overflow-hidden">
                    <CardHeader
                        class="border-b bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/30"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg"
                            >
                                <Key class="h-6 w-6" />
                            </div>
                            <div>
                                <CardTitle>Ubah Password</CardTitle>
                                <CardDescription>
                                    Gunakan password yang kuat untuk keamanan akun
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="p-6">
                        <Form
                            v-bind="PasswordController.update.form()"
                            :options="{
                                preserveScroll: true,
                            }"
                            reset-on-success
                            :reset-on-error="[
                                'password',
                                'password_confirmation',
                                'current_password',
                            ]"
                            class="space-y-6"
                            v-slot="{ errors, processing, recentlySuccessful }"
                        >
                            <div class="space-y-2">
                                <Label for="current_password">Password Saat Ini</Label>
                                <div class="relative">
                                    <Lock
                                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                    />
                                    <Input
                                        id="current_password"
                                        name="current_password"
                                        type="password"
                                        class="pl-10"
                                        autocomplete="current-password"
                                        placeholder="Masukkan password saat ini"
                                    />
                                </div>
                                <InputError :message="errors.current_password" />
                            </div>

                            <div class="space-y-2">
                                <Label for="password">Password Baru</Label>
                                <div class="relative">
                                    <Key
                                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                    />
                                    <Input
                                        id="password"
                                        name="password"
                                        type="password"
                                        class="pl-10"
                                        autocomplete="new-password"
                                        placeholder="Minimal 8 karakter"
                                    />
                                </div>
                                <InputError :message="errors.password" />
                            </div>

                            <div class="space-y-2">
                                <Label for="password_confirmation"
                                    >Konfirmasi Password Baru</Label
                                >
                                <div class="relative">
                                    <ShieldCheck
                                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                    />
                                    <Input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        class="pl-10"
                                        autocomplete="new-password"
                                        placeholder="Ulangi password baru"
                                    />
                                </div>
                                <InputError
                                    :message="errors.password_confirmation"
                                />
                            </div>

                            <!-- Tips -->
                            <div
                                class="rounded-xl border border-blue-100 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950/30"
                            >
                                <p
                                    class="text-sm font-medium text-blue-800 dark:text-blue-200"
                                >
                                    Tips Password Aman:
                                </p>
                                <ul
                                    class="mt-2 space-y-1 text-sm text-blue-700 dark:text-blue-300"
                                >
                                    <li class="flex items-center gap-2">
                                        <div
                                            class="h-1.5 w-1.5 rounded-full bg-blue-500"
                                        />
                                        Minimal 8 karakter
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div
                                            class="h-1.5 w-1.5 rounded-full bg-blue-500"
                                        />
                                        Kombinasi huruf besar, kecil, dan angka
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div
                                            class="h-1.5 w-1.5 rounded-full bg-blue-500"
                                        />
                                        Hindari informasi pribadi
                                    </li>
                                </ul>
                            </div>

                            <div class="flex items-center gap-4 border-t pt-6">
                                <Button
                                    :disabled="processing"
                                    data-test="update-password-button"
                                    class="gap-2"
                                >
                                    <Save class="h-4 w-4" />
                                    {{
                                        processing
                                            ? 'Menyimpan...'
                                            : 'Simpan Password'
                                    }}
                                </Button>

                                <Transition
                                    enter-active-class="transition ease-in-out duration-300"
                                    enter-from-class="opacity-0 translate-x-2"
                                    leave-active-class="transition ease-in-out duration-300"
                                    leave-to-class="opacity-0 translate-x-2"
                                >
                                    <div
                                        v-show="recentlySuccessful"
                                        class="flex items-center gap-2 text-sm font-medium text-green-600"
                                    >
                                        <div
                                            class="flex h-6 w-6 items-center justify-center rounded-full bg-green-100"
                                        >
                                            <Check class="h-4 w-4" />
                                        </div>
                                        Password berhasil diubah
                                    </div>
                                </Transition>
                            </div>
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
