<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, Category, SpendingByCategory, Transaction } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowDownRight,
    ArrowUpRight,
    Plus,
    TrendingDown,
    Wallet,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { create as createTransaction } from '@/actions/App/Http/Controllers/TransactionController';
import { index as transactionsIndex } from '@/actions/App/Http/Controllers/TransactionController';
import { create as createCashFund } from '@/actions/App/Http/Controllers/CashFundController';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';

interface Props {
    currentBalance: number;
    totalFunds: number;
    totalExpenses: number;
    currentMonthSpending: number;
    recentTransactions: Transaction[];
    spendingByCategory: SpendingByCategory[];
    hasInitialFund: boolean;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

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
    });
};

const totalCategorySpending = computed(() => {
    return props.spendingByCategory.reduce(
        (sum, cat) => sum + parseFloat(cat.total),
        0,
    );
});

const getCategoryPercentage = (total: string): number => {
    if (totalCategorySpending.value === 0) return 0;
    return (parseFloat(total) / totalCategorySpending.value) * 100;
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Setup Initial Fund Banner -->
            <div
                v-if="!hasInitialFund"
                class="animate-in fade-in slide-in-from-top-4 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 p-6 text-white shadow-lg duration-500"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">
                            Selamat Datang di Petty Cash!
                        </h3>
                        <p class="mt-1 text-amber-100">
                            Mulai dengan menetapkan saldo awal kas kecil Anda.
                        </p>
                    </div>
                    <Link :href="createCashFund().url">
                        <Button
                            class="bg-white text-orange-600 shadow-md transition-all duration-200 hover:scale-105 hover:bg-amber-50 active:scale-95"
                        >
                            <Wallet class="mr-2 h-4 w-4" />
                            Set Saldo Awal
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Balance Card - Hero Section -->
            <Card
                class="animate-in fade-in slide-in-from-bottom-4 relative overflow-hidden border-0 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white shadow-xl duration-500"
            >
                <div
                    class="pointer-events-none absolute inset-0 opacity-10"
                    style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 50%, white 1px, transparent 1px); background-size: 40px 40px;"
                />
                <CardHeader class="pb-2">
                    <CardDescription class="text-blue-200">
                        Saldo Saat Ini
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div
                        class="text-4xl font-bold tracking-tight md:text-5xl"
                    >
                        {{ formatCurrency(currentBalance) }}
                    </div>
                    <div class="mt-4 flex gap-6">
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-green-400/20"
                            >
                                <ArrowUpRight class="h-4 w-4 text-green-300" />
                            </div>
                            <div>
                                <p class="text-xs text-blue-200">Total Dana</p>
                                <p class="font-semibold">
                                    {{ formatCurrency(totalFunds) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-red-400/20"
                            >
                                <ArrowDownRight class="h-4 w-4 text-red-300" />
                            </div>
                            <div>
                                <p class="text-xs text-blue-200">
                                    Total Pengeluaran
                                </p>
                                <p class="font-semibold">
                                    {{ formatCurrency(totalExpenses) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 gap-4">
                <Link :href="createTransaction().url" class="group">
                    <Card
                        class="animate-in fade-in slide-in-from-left-4 cursor-pointer border-2 border-transparent transition-all duration-300 hover:border-blue-500 hover:shadow-lg active:scale-[0.97]"
                        style="animation-delay: 100ms"
                    >
                        <CardContent class="flex items-center gap-4 p-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg transition-transform duration-300 group-hover:scale-110"
                            >
                                <Plus class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    Catat Pengeluaran
                                </p>
                                <p class="text-sm text-gray-500">
                                    Tambah transaksi baru
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>

                <Link :href="createCashFund().url" class="group">
                    <Card
                        class="animate-in fade-in slide-in-from-right-4 cursor-pointer border-2 border-transparent transition-all duration-300 hover:border-green-500 hover:shadow-lg active:scale-[0.97]"
                        style="animation-delay: 150ms"
                    >
                        <CardContent class="flex items-center gap-4 p-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-lg transition-transform duration-300 group-hover:scale-110"
                            >
                                <Wallet class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    Tambah Dana
                                </p>
                                <p class="text-sm text-gray-500">
                                    Isi ulang kas kecil
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            <!-- Stats Row -->
            <Card
                class="animate-in fade-in slide-in-from-bottom-4 duration-500"
                style="animation-delay: 200ms"
            >
                <CardHeader class="pb-2">
                    <div class="flex items-center gap-2">
                        <TrendingDown class="h-5 w-5 text-orange-500" />
                        <CardTitle class="text-base">Pengeluaran Bulan Ini</CardTitle>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-bold text-orange-600">
                        {{ formatCurrency(currentMonthSpending) }}
                    </div>
                </CardContent>
            </Card>

            <!-- Spending by Category -->
            <Card
                v-if="spendingByCategory.length > 0"
                class="animate-in fade-in slide-in-from-bottom-4 duration-500"
                style="animation-delay: 250ms"
            >
                <CardHeader>
                    <CardTitle>Pengeluaran per Kategori</CardTitle>
                    <CardDescription>Bulan ini</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div
                            v-for="(category, index) in spendingByCategory"
                            :key="category.category_id"
                            class="animate-in fade-in slide-in-from-left-4"
                            :style="{ animationDelay: `${300 + index * 50}ms` }"
                        >
                            <div class="mb-1 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-3 w-3 rounded-full"
                                        :style="{
                                            backgroundColor:
                                                category.category_color ||
                                                '#6B7280',
                                        }"
                                    />
                                    <span class="text-sm font-medium">
                                        {{ category.category_name }}
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ formatCurrency(category.total) }}
                                </span>
                            </div>
                            <div
                                class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800"
                            >
                                <div
                                    class="h-full rounded-full transition-all duration-500"
                                    :style="{
                                        width: `${getCategoryPercentage(category.total)}%`,
                                        backgroundColor:
                                            category.category_color || '#6B7280',
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Recent Transactions -->
            <Card
                class="animate-in fade-in slide-in-from-bottom-4 duration-500"
                style="animation-delay: 300ms"
            >
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Transaksi Terakhir</CardTitle>
                            <CardDescription>5 transaksi terbaru</CardDescription>
                        </div>
                        <Link :href="transactionsIndex().url">
                            <Button variant="ghost" size="sm">
                                Lihat Semua
                            </Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="recentTransactions.length === 0"
                        class="py-8 text-center text-gray-500"
                    >
                        <Wallet class="mx-auto mb-2 h-12 w-12 opacity-50" />
                        <p>Belum ada transaksi</p>
                        <Link :href="createTransaction().url" class="mt-2 inline-block">
                            <Button variant="outline" size="sm">
                                <Plus class="mr-1 h-4 w-4" />
                                Catat Pengeluaran Pertama
                            </Button>
                        </Link>
                    </div>
                    <div v-else class="space-y-3">
                        <Link
                            v-for="(transaction, index) in recentTransactions"
                            :key="transaction.id"
                            :href="transactionsIndex().url"
                            class="animate-in fade-in slide-in-from-right-4 flex items-center justify-between rounded-xl p-3 transition-all duration-200 hover:bg-gray-50 active:scale-[0.98] dark:hover:bg-gray-800/50"
                            :style="{ animationDelay: `${350 + index * 50}ms` }"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl"
                                    :style="{
                                        backgroundColor:
                                            (transaction.category?.color ||
                                                '#6B7280') + '20',
                                    }"
                                >
                                    <div
                                        class="h-3 w-3 rounded-full"
                                        :style="{
                                            backgroundColor:
                                                transaction.category?.color ||
                                                '#6B7280',
                                        }"
                                    />
                                </div>
                                <div>
                                    <p
                                        class="line-clamp-1 font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ transaction.description }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ transaction.category?.name }} •
                                        {{ formatDate(transaction.transaction_date) }}
                                    </p>
                                </div>
                            </div>
                            <span class="font-semibold text-red-600">
                                -{{ formatCurrency(transaction.amount) }}
                            </span>
                        </Link>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
