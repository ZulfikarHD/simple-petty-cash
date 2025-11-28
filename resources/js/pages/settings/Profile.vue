<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import InputError from '@/components/InputError.vue';
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
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { AlertCircle, Check, Mail, Save, User } from 'lucide-vue-next';
import { dashboard } from '@/routes';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Pengaturan Profil', href: edit().url },
];

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Pengaturan Profil" />

        <SettingsLayout>
            <div class="flex flex-col gap-6">
                <!-- Profile Card -->
                <Card class="overflow-hidden">
                    <CardHeader
                        class="border-b bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg"
                            >
                                <User class="h-6 w-6" />
                            </div>
                            <div>
                                <CardTitle>Informasi Profil</CardTitle>
                                <CardDescription>
                                    Perbarui nama dan alamat email Anda
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="p-6">
                        <Form
                            v-bind="ProfileController.update.form()"
                            class="space-y-6"
                            v-slot="{ errors, processing, recentlySuccessful }"
                        >
                            <div class="space-y-2">
                                <Label for="name">Nama Lengkap</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    :default-value="user.name"
                                    required
                                    autocomplete="name"
                                    placeholder="Masukkan nama lengkap"
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Alamat Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    :default-value="user.email"
                                    required
                                    autocomplete="username"
                                    placeholder="contoh@email.com"
                                />
                                <InputError :message="errors.email" />
                            </div>

                            <!-- Email Verification Warning -->
                            <div
                                v-if="mustVerifyEmail && !user.email_verified_at"
                                class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20"
                            >
                                <div class="flex items-start gap-3">
                                    <AlertCircle
                                        class="mt-0.5 h-5 w-5 shrink-0 text-amber-600"
                                    />
                                    <div class="flex-1">
                                        <p
                                            class="text-sm font-medium text-amber-800 dark:text-amber-200"
                                        >
                                            Email belum diverifikasi
                                        </p>
                                        <p
                                            class="mt-1 text-sm text-amber-700 dark:text-amber-300"
                                        >
                                            <Link
                                                :href="send()"
                                                as="button"
                                                class="font-medium underline underline-offset-2 hover:no-underline"
                                            >
                                                Klik di sini
                                            </Link>
                                            untuk mengirim ulang email verifikasi.
                                        </p>

                                        <div
                                            v-if="status === 'verification-link-sent'"
                                            class="mt-2 flex items-center gap-2 text-sm font-medium text-green-600"
                                        >
                                            <Mail class="h-4 w-4" />
                                            Link verifikasi baru telah dikirim ke email Anda.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 border-t pt-6">
                                <Button
                                    :disabled="processing"
                                    data-test="update-profile-button"
                                    class="gap-2"
                                >
                                    <Save class="h-4 w-4" />
                                    {{ processing ? 'Menyimpan...' : 'Simpan' }}
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
                                        Tersimpan
                                    </div>
                                </Transition>
                            </div>
                        </Form>
                    </CardContent>
                </Card>

                <!-- Delete User Section -->
                <DeleteUser />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
