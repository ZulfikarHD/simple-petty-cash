<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, exportMethod } from '@/actions/App/Http/Controllers/ReportController';
import type {
    BreadcrumbItem,
    Category,
    Transaction,
    User,
    ReportSummary,
} from '@/types';
import { Head, router } from '@inertiajs/vue3';
import {
    Calendar,
    Download,
    FileSpreadsheet,
    Filter,
    TrendingDown,
    Users,
    Wallet,
    X,
    PieChart,
    BarChart3,
    User as UserIcon,
} from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';
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
import { dashboard } from '@/routes';

interface Props {
    transactions: Transaction[];
    summary: ReportSummary;
    filters: {
        start_date: string;
        end_date: string;
        category_id?: number | null;
        user_id?: number | null;
    };
    categories: Category[];
    users: User[];
    isAdmin: boolean;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Laporan', href: index().url },
];

const showFilters = ref(false);
const startDate = ref(props.filters.start_date);
const endDate = ref(props.filters.end_date);
const categoryId = ref(props.filters.category_id?.toString() || '');
const userId = ref(props.filters.user_id?.toString() || '');

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
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const formatDateShort = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const applyFilters = () => {
    const params: Record<string, string> = {};
    if (startDate.value) params.start_date = startDate.value;
    if (endDate.value) params.end_date = endDate.value;
    if (categoryId.value) params.category_id = categoryId.value;
    if (userId.value) params.user_id = userId.value;

    router.get(index().url, params, {
        preserveState: true,
        preserveScroll: true,
    });
    showFilters.value = false;
};

const clearFilters = () => {
    startDate.value = new Date().toISOString().slice(0, 7) + '-01';
    endDate.value = new Date().toISOString().slice(0, 10);
    categoryId.value = '';
    userId.value = '';
    router.get(index().url, {}, {
        preserveState: true,
        preserveScroll: true,
    });
    showFilters.value = false;
};

const hasActiveFilters = computed(() => {
    return props.filters.category_id || props.filters.user_id;
});

const exportToCsv = () => {
    const params = new URLSearchParams();
    if (props.filters.start_date) params.append('start_date', props.filters.start_date);
    if (props.filters.end_date) params.append('end_date', props.filters.end_date);
    if (props.filters.category_id) params.append('category_id', props.filters.category_id.toString());
    if (props.filters.user_id) params.append('user_id', props.filters.user_id.toString());

    window.location.href = `${exportMethod().url}?${params.toString()}`;
};

// Calculate max spending for chart bars
const maxCategorySpending = computed(() => {
    if (!props.summary.by_category.length) return 0;
    return Math.max(...props.summary.by_category.map(c => parseFloat(c.total)));
});

const maxUserSpending = computed(() => {
    if (!props.summary.by_user?.length) return 0;
    return Math.max(...props.summary.by_user.map(u => parseFloat(u.total)));
});

// Total category spending for percentage
const totalCategorySpending = computed(() => {
    return props.summary.by_category.reduce((sum, cat) => sum + parseFloat(cat.total), 0);
});

const getCategoryPercentage = (total: string): number => {
    if (totalCategorySpending.value === 0) return 0;
    return (parseFloat(total) / totalCategorySpending.value) * 100;
};
</script>

<template>
    <Head title="Laporan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Laporan Pengeluaran
                    </h1>
                    <p class="text-sm text-gray-500">
                        Periode: {{ formatDateShort(filters.start_date) }} - {{ formatDateShort(filters.end_date) }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        size="icon"
                        @click="showFilters = !showFilters"
                        :class="{ 'border-blue-500 bg-blue-50 text-blue-600 dark:bg-blue-900/30': hasActiveFilters }"
                    >
                        <Filter class="h-4 w-4" />
                    </Button>
                    <Button @click="exportToCsv" class="gap-2">
                        <Download class="h-4 w-4" />
                        <span class="hidden sm:inline">Export CSV</span>
                    </Button>
                </div>
            </div>

            <!-- Filters Panel -->
            <Card v-if="showFilters" class="animate-in fade-in slide-in-from-top-4 duration-300">
                <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-base">Filter Laporan</CardTitle>
                        <Button variant="ghost" size="icon" @click="showFilters = false">
                            <X class="h-4 w-4" />
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <Label class="text-sm">Tanggal Mulai</Label>
                            <div class="relative mt-1">
                                <Calendar class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                                <Input
                                    v-model="startDate"
                                    type="date"
                                    class="pl-9"
                                />
                            </div>
                        </div>
                        <div>
                            <Label class="text-sm">Tanggal Akhir</Label>
                            <div class="relative mt-1">
                                <Calendar class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                                <Input
                                    v-model="endDate"
                                    type="date"
                                    class="pl-9"
                                />
                            </div>
                        </div>
                        <div>
                            <Label class="text-sm">Kategori</Label>
                            <select
                                v-model="categoryId"
                                class="border-input bg-background mt-1 flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1"
                            >
                                <option value="">Semua Kategori</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>
                        <div v-if="isAdmin">
                            <Label class="text-sm">User</Label>
                            <select
                                v-model="userId"
                                class="border-input bg-background mt-1 flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1"
                            >
                                <option value="">Semua User</option>
                                <option
                                    v-for="user in users"
                                    :key="user.id"
                                    :value="user.id"
                                >
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <Button variant="outline" @click="clearFilters">
                            Reset
                        </Button>
                        <Button @click="applyFilters">
                            Terapkan Filter
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Active Filters -->
            <div v-if="hasActiveFilters && !showFilters" class="flex flex-wrap gap-2">
                <div
                    v-if="filters.category_id"
                    class="flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                >
                    <span>
                        {{ categories.find((c) => c.id === filters.category_id)?.name }}
                    </span>
                </div>
                <div
                    v-if="filters.user_id && isAdmin"
                    class="flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-300"
                >
                    <UserIcon class="h-3 w-3" />
                    <span>
                        {{ users.find((u) => u.id === filters.user_id)?.name }}
                    </span>
                </div>
                <button
                    @click="clearFilters"
                    class="flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-600 transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300"
                >
                    <X class="h-3 w-3" />
                    Hapus Filter
                </button>
            </div>

            <!-- Summary Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Transactions -->
                <Card class="animate-in fade-in slide-in-from-bottom-4 duration-500" style="animation-delay: 100ms">
                    <CardContent class="flex items-center gap-4 p-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 dark:bg-blue-900/30">
                            <FileSpreadsheet class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Transaksi</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ summary.total_transactions }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Total Amount -->
                <Card class="animate-in fade-in slide-in-from-bottom-4 duration-500" style="animation-delay: 150ms">
                    <CardContent class="flex items-center gap-4 p-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-900/30">
                            <TrendingDown class="h-6 w-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Pengeluaran</p>
                            <p class="text-2xl font-bold text-red-600">
                                {{ formatCurrency(summary.total_amount) }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Beginning Balance -->
                <Card class="animate-in fade-in slide-in-from-bottom-4 duration-500" style="animation-delay: 200ms">
                    <CardContent class="flex items-center gap-4 p-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-100 dark:bg-green-900/30">
                            <Wallet class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Saldo Awal</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ formatCurrency(summary.beginning_balance) }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Ending Balance -->
                <Card class="animate-in fade-in slide-in-from-bottom-4 duration-500" style="animation-delay: 250ms">
                    <CardContent class="flex items-center gap-4 p-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 dark:bg-indigo-900/30">
                            <Wallet class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Saldo Akhir</p>
                            <p class="text-2xl font-bold" :class="summary.ending_balance >= 0 ? 'text-indigo-600' : 'text-red-600'">
                                {{ formatCurrency(summary.ending_balance) }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Charts Row -->
            <div class="grid gap-4 lg:grid-cols-2">
                <!-- Spending by Category -->
                <Card class="animate-in fade-in slide-in-from-bottom-4 duration-500" style="animation-delay: 300ms">
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <PieChart class="h-5 w-5 text-blue-500" />
                            <CardTitle>Pengeluaran per Kategori</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="summary.by_category.length === 0" class="py-8 text-center text-gray-500">
                            <PieChart class="mx-auto mb-2 h-10 w-10 opacity-30" />
                            <p>Tidak ada data kategori</p>
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="(category, index) in summary.by_category"
                                :key="category.category_id"
                                class="animate-in fade-in slide-in-from-left-4"
                                :style="{ animationDelay: `${350 + index * 50}ms` }"
                            >
                                <div class="mb-1 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="h-3 w-3 rounded-full"
                                            :style="{ backgroundColor: category.category_color || '#6B7280' }"
                                        />
                                        <span class="text-sm font-medium">{{ category.category_name }}</span>
                                        <span class="text-xs text-gray-400">({{ category.count }} trx)</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        {{ formatCurrency(category.total) }}
                                    </span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div
                                        class="h-full rounded-full transition-all duration-700"
                                        :style="{
                                            width: `${getCategoryPercentage(category.total)}%`,
                                            backgroundColor: category.category_color || '#6B7280',
                                        }"
                                    />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Spending by User (Admin only) -->
                <Card v-if="isAdmin" class="animate-in fade-in slide-in-from-bottom-4 duration-500" style="animation-delay: 350ms">
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <Users class="h-5 w-5 text-green-500" />
                            <CardTitle>Pengeluaran per User</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="!summary.by_user?.length" class="py-8 text-center text-gray-500">
                            <Users class="mx-auto mb-2 h-10 w-10 opacity-30" />
                            <p>Tidak ada data user</p>
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="(user, index) in summary.by_user"
                                :key="user.user_id"
                                class="animate-in fade-in slide-in-from-right-4"
                                :style="{ animationDelay: `${400 + index * 50}ms` }"
                            >
                                <div class="mb-1 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 text-xs font-medium text-white">
                                            {{ user.user_name.charAt(0).toUpperCase() }}
                                        </div>
                                        <span class="text-sm font-medium">{{ user.user_name }}</span>
                                        <span class="text-xs text-gray-400">({{ user.count }} trx)</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        {{ formatCurrency(user.total) }}
                                    </span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div
                                        class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 transition-all duration-700"
                                        :style="{
                                            width: `${(parseFloat(user.total) / maxUserSpending) * 100}%`,
                                        }"
                                    />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Transaction List -->
            <Card class="animate-in fade-in slide-in-from-bottom-4 duration-500" style="animation-delay: 400ms">
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <BarChart3 class="h-5 w-5 text-indigo-500" />
                        <CardTitle>Detail Transaksi</CardTitle>
                    </div>
                    <CardDescription>
                        {{ transactions.length }} transaksi dalam periode ini
                    </CardDescription>
                </CardHeader>
                <CardContent class="p-0">
                    <div v-if="transactions.length === 0" class="py-16 text-center text-gray-500">
                        <Wallet class="mx-auto mb-4 h-16 w-16 opacity-30" />
                        <p class="text-lg font-medium">Tidak ada transaksi</p>
                        <p class="text-sm">Dalam periode yang dipilih</p>
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Tanggal
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Deskripsi
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Kategori
                                    </th>
                                    <th v-if="isAdmin" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        User
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Jumlah
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-800">
                                <tr
                                    v-for="(transaction, idx) in transactions"
                                    :key="transaction.id"
                                    class="animate-in fade-in transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/30"
                                    :style="{ animationDelay: `${450 + idx * 20}ms` }"
                                >
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                        {{ formatDate(transaction.transaction_date) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="line-clamp-1 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ transaction.description }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="h-2 w-2 rounded-full"
                                                :style="{ backgroundColor: transaction.category?.color || '#6B7280' }"
                                            />
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ transaction.category?.name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td v-if="isAdmin" class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 text-xs font-medium text-white">
                                                {{ transaction.user?.name?.charAt(0)?.toUpperCase() || '?' }}
                                            </div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ transaction.user?.name || '-' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <span class="font-semibold text-red-600">
                                            -{{ formatCurrency(transaction.amount) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="border-t bg-gray-50 font-semibold dark:bg-gray-800/50">
                                <tr>
                                    <td :colspan="isAdmin ? 4 : 3" class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                        Total
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-red-600">
                                        -{{ formatCurrency(summary.total_amount) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

