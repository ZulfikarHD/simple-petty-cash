<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index,
    update,
    resetPassword,
} from '@/actions/App/Http/Controllers/UserController';
import type { BreadcrumbItem, User } from '@/types';
import { Head, Link, Form, router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Key, Save, ShieldCheck, User as UserIcon } from 'lucide-vue-next';
import { ref, computed } from 'vue';
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
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import InputError from '@/components/InputError.vue';
import { dashboard } from '@/routes';

interface Props {
    user: User;
}

const props = defineProps<Props>();

const page = usePage();
const currentUser = computed(() => page.props.auth.user);
const isSelf = computed(() => currentUser.value.id === props.user.id);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Kelola User', href: index().url },
    { title: 'Edit User', href: '#' },
];

const isAdmin = ref(props.user.is_admin);
const resetPasswordDialogOpen = ref(false);
const isResettingPassword = ref(false);

const handleResetPassword = () => {
    isResettingPassword.value = true;
    router.post(
        resetPassword(props.user.id).url,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                resetPasswordDialogOpen.value = false;
            },
            onFinish: () => {
                isResettingPassword.value = false;
            },
        },
    );
};
</script>

<template>
    <Head :title="`Edit User - ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link :href="index().url">
                    <Button variant="ghost" size="icon" class="shrink-0">
                        <ArrowLeft class="h-5 w-5" />
                    </Button>
                </Link>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Edit User
                    </h1>
                    <p class="text-sm text-gray-500">
                        Ubah data user {{ user.name }}
                    </p>
                </div>
                <!-- Reset Password Button -->
                <Button
                    v-if="!isSelf"
                    variant="outline"
                    class="gap-2"
                    @click="resetPasswordDialogOpen = true"
                >
                    <Key class="h-4 w-4" />
                    <span class="hidden sm:inline">Reset Password</span>
                </Button>
            </div>

            <!-- User Info Card -->
            <div
                class="animate-in fade-in slide-in-from-bottom-4 mx-auto flex w-full max-w-2xl items-center gap-4 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 p-4 text-white shadow-lg duration-300"
            >
                <div
                    class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-white/20 text-2xl font-bold"
                >
                    {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold">{{ user.name }}</h2>
                        <span
                            v-if="user.is_admin"
                            class="rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium"
                        >
                            Admin
                        </span>
                    </div>
                    <p class="text-blue-100">{{ user.email }}</p>
                </div>
            </div>

            <!-- Form Card -->
            <Card
                class="animate-in fade-in slide-in-from-bottom-4 mx-auto w-full max-w-2xl duration-500"
                style="animation-delay: 100ms"
            >
                <CardHeader class="border-b bg-gradient-to-r from-gray-50 to-slate-50 dark:from-gray-950/30 dark:to-slate-950/30">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-gray-600 to-slate-700 text-white shadow-lg"
                        >
                            <UserIcon class="h-6 w-6" />
                        </div>
                        <div>
                            <CardTitle>Update Informasi</CardTitle>
                            <CardDescription>
                                Ubah data akun pengguna
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="p-6">
                    <Form
                        :action="update(user.id).url"
                        method="put"
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
                                    :default-value="user.name"
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
                                    :default-value="user.email"
                                    placeholder="contoh@email.com"
                                    required
                                    autocomplete="email"
                                />
                                <InputError :message="errors.email" />
                            </div>

                            <!-- Password (Optional) -->
                            <div class="space-y-2">
                                <Label for="password"
                                    >Password Baru
                                    <span class="text-gray-400"
                                        >(opsional)</span
                                    ></Label
                                >
                                <Input
                                    id="password"
                                    name="password"
                                    type="password"
                                    placeholder="Kosongkan jika tidak ingin mengubah"
                                    autocomplete="new-password"
                                />
                                <InputError :message="errors.password" />
                            </div>

                            <!-- Password Confirmation -->
                            <div class="space-y-2">
                                <Label for="password_confirmation"
                                    >Konfirmasi Password Baru</Label
                                >
                                <Input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    placeholder="Ulangi password baru"
                                    autocomplete="new-password"
                                />
                                <InputError
                                    :message="errors.password_confirmation"
                                />
                            </div>

                            <!-- Role Admin -->
                            <div
                                v-if="!isSelf"
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
                                            Status Admin
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
                                    />
                                    <input
                                        type="hidden"
                                        name="is_admin"
                                        :value="isAdmin ? '1' : '0'"
                                    />
                                </div>
                            </div>

                            <!-- Self Edit Warning -->
                            <div
                                v-if="isSelf"
                                class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20"
                            >
                                <p class="text-sm text-amber-800 dark:text-amber-200">
                                    Anda sedang mengedit akun sendiri. Status
                                    admin tidak dapat diubah untuk mencegah
                                    lockout.
                                </p>
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
                                {{
                                    processing
                                        ? 'Menyimpan...'
                                        : 'Simpan Perubahan'
                                }}
                            </Button>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </div>

        <!-- Reset Password Dialog -->
        <Dialog v-model:open="resetPasswordDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Reset Password</DialogTitle>
                    <DialogDescription>
                        Password user "{{ user.name }}" akan direset dengan
                        password baru yang di-generate secara otomatis. Password
                        baru akan ditampilkan setelah proses reset berhasil.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button
                        variant="outline"
                        @click="resetPasswordDialogOpen = false"
                    >
                        Batal
                    </Button>
                    <Button
                        @click="handleResetPassword"
                        :disabled="isResettingPassword"
                        class="gap-2"
                    >
                        <Key class="h-4 w-4" />
                        {{
                            isResettingPassword
                                ? 'Mereset...'
                                : 'Reset Password'
                        }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

