<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { update, index } from '@/actions/App/Http/Controllers/TransactionController';
import type { BreadcrumbItem, Category, Transaction } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import {
    Calendar,
    Check,
    DollarSign,
    FileText,
    Pencil,
    Tag,
} from 'lucide-vue-next';
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
import InputError from '@/components/InputError.vue';
import { dashboard } from '@/routes';

interface Props {
    transaction: Transaction;
    categories: Category[];
    currentBalance: number;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Transaksi', href: index().url },
    { title: 'Edit Transaksi', href: '#' },
];

const form = useForm({
    amount: props.transaction.amount,
    description: props.transaction.description,
    transaction_date: props.transaction.transaction_date,
    category_id: props.transaction.category_id.toString(),
});

const selectedCategory = ref<Category | null>(
    props.categories.find((c) => c.id === props.transaction.category_id) || null,
);
const showSuccess = ref(false);

const formatCurrency = (amount: number | string): string => {
    const num = typeof amount === 'string' ? parseFloat(amount) : amount;
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(num);
};

const selectCategory = (category: Category) => {
    selectedCategory.value = category;
    form.category_id = category.id.toString();

    if (navigator.vibrate) {
        navigator.vibrate(10);
    }
};

const formatAmountInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    let value = input.value.replace(/[^\d.]/g, '');
    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
    }
    if (parts[1] && parts[1].length > 2) {
        value = parts[0] + '.' + parts[1].substring(0, 2);
    }
    form.amount = value;
};

const submit = () => {
    if (navigator.vibrate) {
        navigator.vibrate([10, 50, 10]);
    }

    form.put(update(props.transaction.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess.value = true;
            setTimeout(() => {
                showSuccess.value = false;
            }, 2000);
        },
    });
};

// Balance calculation considering the original transaction amount
const originalAmount = parseFloat(props.transaction.amount);
const balanceWithoutTransaction = props.currentBalance + originalAmount;

const remainingBalance = () => {
    const amount = parseFloat(form.amount) || 0;
    return balanceWithoutTransaction - amount;
};
</script>

<template>
    <Head title="Edit Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Success Animation -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-if="showSuccess"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                >
                    <div class="flex h-32 w-32 items-center justify-center rounded-3xl bg-green-500 text-white shadow-2xl">
                        <Check class="h-16 w-16 animate-bounce" />
                    </div>
                </div>
            </Transition>

            <!-- Balance Preview Card -->
            <Card class="border-0 bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg">
                <CardContent class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-amber-100">Saldo Tersedia</p>
                            <p class="text-2xl font-bold">
                                {{ formatCurrency(balanceWithoutTransaction) }}
                            </p>
                        </div>
                        <div v-if="form.amount" class="text-right">
                            <p class="text-sm text-amber-100">Sisa Setelah</p>
                            <p
                                class="text-2xl font-bold"
                                :class="{
                                    'text-red-200': remainingBalance() < 0,
                                    'text-green-200': remainingBalance() >= 0,
                                }"
                            >
                                {{ formatCurrency(remainingBalance()) }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form Card -->
            <Card class="animate-in fade-in slide-in-from-bottom-4 flex-1 duration-500">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Pencil class="h-5 w-5 text-amber-600" />
                        Edit Transaksi
                    </CardTitle>
                    <CardDescription>
                        Perbarui detail transaksi ini
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Amount Input -->
                        <div class="space-y-2">
                            <Label for="amount" class="flex items-center gap-2">
                                <DollarSign class="h-4 w-4 text-gray-500" />
                                Jumlah (Rp)
                            </Label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-medium text-gray-500">
                                    Rp
                                </span>
                                <Input
                                    id="amount"
                                    v-model="form.amount"
                                    @input="formatAmountInput"
                                    type="text"
                                    inputmode="decimal"
                                    placeholder="0"
                                    class="h-14 pl-12 text-2xl font-bold"
                                    :class="{ 'border-red-500': form.errors.amount }"
                                    required
                                />
                            </div>
                            <InputError :message="form.errors.amount" />
                            <p
                                v-if="remainingBalance() < 0 && form.amount"
                                class="text-sm text-red-500"
                            >
                                Saldo tidak mencukupi!
                            </p>
                        </div>

                        <!-- Category Selection -->
                        <div class="space-y-2">
                            <Label class="flex items-center gap-2">
                                <Tag class="h-4 w-4 text-gray-500" />
                                Kategori
                            </Label>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
                                <button
                                    v-for="category in categories"
                                    :key="category.id"
                                    type="button"
                                    @click="selectCategory(category)"
                                    class="flex flex-col items-center gap-2 rounded-2xl border-2 p-4 transition-all duration-200 active:scale-95"
                                    :class="{
                                        'border-amber-500 bg-amber-50 dark:bg-amber-950':
                                            selectedCategory?.id === category.id,
                                        'border-gray-200 hover:border-gray-300 dark:border-gray-700':
                                            selectedCategory?.id !== category.id,
                                    }"
                                >
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl transition-transform duration-200"
                                        :class="{
                                            'scale-110': selectedCategory?.id === category.id,
                                        }"
                                        :style="{
                                            backgroundColor: (category.color || '#6B7280') + '20',
                                        }"
                                    >
                                        <div
                                            class="h-4 w-4 rounded-full"
                                            :style="{
                                                backgroundColor: category.color || '#6B7280',
                                            }"
                                        />
                                    </div>
                                    <span
                                        class="text-center text-xs font-medium"
                                        :class="{
                                            'text-amber-700 dark:text-amber-300':
                                                selectedCategory?.id === category.id,
                                            'text-gray-600 dark:text-gray-400':
                                                selectedCategory?.id !== category.id,
                                        }"
                                    >
                                        {{ category.name }}
                                    </span>
                                </button>
                            </div>
                            <InputError :message="form.errors.category_id" />
                        </div>

                        <!-- Description Input -->
                        <div class="space-y-2">
                            <Label for="description" class="flex items-center gap-2">
                                <FileText class="h-4 w-4 text-gray-500" />
                                Deskripsi
                            </Label>
                            <Input
                                id="description"
                                v-model="form.description"
                                type="text"
                                placeholder="Contoh: Beli alat tulis kantor"
                                maxlength="200"
                                class="h-12"
                                :class="{ 'border-red-500': form.errors.description }"
                                required
                            />
                            <div class="flex items-center justify-between">
                                <InputError :message="form.errors.description" />
                                <span class="text-xs text-gray-400">
                                    {{ form.description.length }}/200
                                </span>
                            </div>
                        </div>

                        <!-- Date Input -->
                        <div class="space-y-2">
                            <Label for="transaction_date" class="flex items-center gap-2">
                                <Calendar class="h-4 w-4 text-gray-500" />
                                Tanggal Transaksi
                            </Label>
                            <Input
                                id="transaction_date"
                                v-model="form.transaction_date"
                                type="date"
                                :max="new Date().toISOString().split('T')[0]"
                                class="h-12"
                                :class="{ 'border-red-500': form.errors.transaction_date }"
                                required
                            />
                            <InputError :message="form.errors.transaction_date" />
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            class="h-14 w-full text-lg font-semibold transition-all duration-200 active:scale-[0.98]"
                            :class="{
                                'bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700':
                                    !form.processing,
                            }"
                            :disabled="form.processing || remainingBalance() < 0"
                        >
                            <span v-if="form.processing" class="flex items-center gap-2">
                                <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24">
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                        fill="none"
                                    />
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    />
                                </svg>
                                Menyimpan...
                            </span>
                            <span v-else class="flex items-center gap-2">
                                <Check class="h-5 w-5" />
                                Simpan Perubahan
                            </span>
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

