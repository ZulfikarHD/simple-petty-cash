<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { store } from '@/actions/App/Http/Controllers/CashFundController';
import type { BreadcrumbItem, CashFund } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import {
    Calendar,
    Check,
    DollarSign,
    FileText,
    History,
    Plus,
    Wallet,
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
    hasInitialFund: boolean;
    currentBalance: number;
    fundHistory: CashFund[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: props.hasInitialFund ? 'Tambah Dana' : 'Set Saldo Awal', href: '#' },
];

const form = useForm({
    amount: '',
    note: '',
    fund_date: new Date().toISOString().split('T')[0],
});

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

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
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

    form.post(store().url, {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess.value = true;
            setTimeout(() => {
                showSuccess.value = false;
            }, 2000);
        },
    });
};

const newBalance = () => {
    const amount = parseFloat(form.amount) || 0;
    return props.currentBalance + amount;
};
</script>

<template>
    <Head :title="hasInitialFund ? 'Tambah Dana' : 'Set Saldo Awal'" />

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
            <Card
                class="border-0 text-white shadow-lg"
                :class="{
                    'bg-gradient-to-r from-emerald-500 to-green-600': hasInitialFund,
                    'bg-gradient-to-r from-amber-500 to-orange-600': !hasInitialFund,
                }"
            >
                <CardContent class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-80">Saldo Saat Ini</p>
                            <p class="text-2xl font-bold">
                                {{ formatCurrency(currentBalance) }}
                            </p>
                        </div>
                        <div v-if="form.amount" class="text-right">
                            <p class="text-sm opacity-80">Saldo Baru</p>
                            <p class="text-2xl font-bold">
                                {{ formatCurrency(newBalance()) }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form Card -->
            <Card class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <template v-if="hasInitialFund">
                            <Plus class="h-5 w-5 text-green-600" />
                            Tambah Dana Kas Kecil
                        </template>
                        <template v-else>
                            <Wallet class="h-5 w-5 text-amber-600" />
                            Set Saldo Awal Kas Kecil
                        </template>
                    </CardTitle>
                    <CardDescription>
                        {{
                            hasInitialFund
                                ? 'Isi ulang atau tambahkan dana ke kas kecil'
                                : 'Tetapkan saldo awal untuk memulai pencatatan kas kecil'
                        }}
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
                        </div>

                        <!-- Note Input -->
                        <div class="space-y-2">
                            <Label for="note" class="flex items-center gap-2">
                                <FileText class="h-4 w-4 text-gray-500" />
                                Catatan (Opsional)
                            </Label>
                            <Input
                                id="note"
                                v-model="form.note"
                                type="text"
                                :placeholder="
                                    hasInitialFund
                                        ? 'Contoh: Pengisian kas bulanan'
                                        : 'Contoh: Dana awal kas kecil bulan ini'
                                "
                                maxlength="255"
                                class="h-12"
                                :class="{ 'border-red-500': form.errors.note }"
                            />
                            <InputError :message="form.errors.note" />
                        </div>

                        <!-- Date Input -->
                        <div class="space-y-2">
                            <Label for="fund_date" class="flex items-center gap-2">
                                <Calendar class="h-4 w-4 text-gray-500" />
                                Tanggal
                            </Label>
                            <Input
                                id="fund_date"
                                v-model="form.fund_date"
                                type="date"
                                :max="new Date().toISOString().split('T')[0]"
                                class="h-12"
                                :class="{ 'border-red-500': form.errors.fund_date }"
                                required
                            />
                            <InputError :message="form.errors.fund_date" />
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            class="h-14 w-full text-lg font-semibold transition-all duration-200 active:scale-[0.98]"
                            :class="{
                                'bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700':
                                    hasInitialFund && !form.processing,
                                'bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700':
                                    !hasInitialFund && !form.processing,
                            }"
                            :disabled="form.processing"
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
                                {{ hasInitialFund ? 'Tambah Dana' : 'Set Saldo Awal' }}
                            </span>
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <!-- Fund History -->
            <Card
                v-if="fundHistory.length > 0"
                class="animate-in fade-in slide-in-from-bottom-4 duration-500"
                style="animation-delay: 100ms"
            >
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <History class="h-5 w-5 text-gray-500" />
                        Riwayat Dana
                    </CardTitle>
                    <CardDescription>
                        Daftar penambahan dana kas kecil
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div
                            v-for="(fund, idx) in fundHistory"
                            :key="fund.id"
                            class="animate-in fade-in slide-in-from-right-4 flex items-center justify-between rounded-xl border p-4 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                            :style="{ animationDelay: `${150 + idx * 50}ms` }"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30">
                                    <Plus class="h-5 w-5 text-green-600" />
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ fund.note || 'Penambahan dana' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ formatDate(fund.fund_date) }}
                                    </p>
                                </div>
                            </div>
                            <span class="font-semibold text-green-600">
                                +{{ formatCurrency(fund.amount) }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

