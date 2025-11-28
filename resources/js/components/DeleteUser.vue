<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Form } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

// Components
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { AlertTriangle, Lock, Trash2 } from 'lucide-vue-next';

const passwordInput = useTemplateRef('passwordInput');
</script>

<template>
    <Card class="overflow-hidden border-red-200 dark:border-red-900/50">
        <CardHeader
            class="border-b border-red-100 bg-gradient-to-r from-red-50 to-rose-50 dark:border-red-900/30 dark:from-red-950/30 dark:to-rose-950/30"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-lg"
                >
                    <Trash2 class="h-6 w-6" />
                </div>
                <div>
                    <CardTitle class="text-red-700 dark:text-red-400"
                        >Hapus Akun</CardTitle
                    >
                    <CardDescription>
                        Hapus akun dan semua data terkait secara permanen
                    </CardDescription>
                </div>
            </div>
        </CardHeader>
        <CardContent class="p-6">
            <div
                class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20"
            >
                <AlertTriangle
                    class="mt-0.5 h-5 w-5 shrink-0 text-red-600 dark:text-red-400"
                />
                <div>
                    <p
                        class="font-medium text-red-800 dark:text-red-200"
                    >
                        Peringatan
                    </p>
                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                        Tindakan ini tidak dapat dibatalkan. Semua data
                        termasuk transaksi dan pengaturan akan terhapus
                        secara permanen.
                    </p>
                </div>
            </div>

            <Dialog>
                <DialogTrigger as-child>
                    <Button
                        variant="destructive"
                        data-test="delete-user-button"
                        class="gap-2"
                    >
                        <Trash2 class="h-4 w-4" />
                        Hapus Akun
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="ProfileController.destroy.form()"
                        reset-on-success
                        @error="() => passwordInput?.$el?.focus()"
                        :options="{
                            preserveScroll: true,
                        }"
                        class="space-y-6"
                        v-slot="{ errors, processing, reset, clearErrors }"
                    >
                        <DialogHeader class="space-y-3">
                            <DialogTitle
                                >Apakah Anda yakin ingin menghapus
                                akun?</DialogTitle
                            >
                            <DialogDescription>
                                Setelah akun dihapus, semua data dan
                                transaksi akan dihapus secara permanen.
                                Masukkan password untuk mengkonfirmasi
                                penghapusan akun.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="space-y-2">
                            <Label for="password" class="sr-only"
                                >Password</Label
                            >
                            <div class="relative">
                                <Lock
                                    class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                />
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    ref="passwordInput"
                                    class="pl-10"
                                    placeholder="Masukkan password"
                                />
                            </div>
                            <InputError :message="errors.password" />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button
                                    variant="secondary"
                                    @click="
                                        () => {
                                            clearErrors();
                                            reset();
                                        }
                                    "
                                >
                                    Batal
                                </Button>
                            </DialogClose>

                            <Button
                                type="submit"
                                variant="destructive"
                                :disabled="processing"
                                data-test="confirm-delete-user-button"
                                class="gap-2"
                            >
                                <Trash2 class="h-4 w-4" />
                                {{ processing ? 'Menghapus...' : 'Hapus Akun' }}
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </CardContent>
    </Card>
</template>
