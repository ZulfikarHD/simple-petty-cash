<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/actions/App/Http/Controllers/TransactionController';
import { create, destroy, edit } from '@/actions/App/Http/Controllers/TransactionController';
import type {
    BreadcrumbItem,
    Category,
    PaginatedData,
    Transaction,
    User,
} from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    Calendar,
    Filter,
    ImageIcon,
    Pencil,
    Plus,
    Search,
    Trash2,
    User as UserIcon,
    Wallet,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ReceiptViewer from '@/components/ReceiptViewer.vue';
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
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { dashboard } from '@/routes';

interface Props {
    transactions: PaginatedData<Transaction>;
    categories: Category[];
    currentBalance: number;
    filters: {
        start_date?: string;
        end_date?: string;
        category_id?: number;
        user_id?: number;
    };
    users: User[];
    isAdmin: boolean;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Transaksi', href: index().url },
];

const showFilters = ref(false);
const deleteDialogOpen = ref(false);
const transactionToDelete = ref<Transaction | null>(null);
const showReceiptViewer = ref(false);
const selectedReceiptUrl = ref<string | null>(null);

const viewReceipt = (receiptUrl: string | null) => {
    if (receiptUrl) {
        selectedReceiptUrl.value = receiptUrl;
        showReceiptViewer.value = true;
        if (navigator.vibrate) {
            navigator.vibrate(10);
        }
    }
};

const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');
const categoryId = ref(props.filters.category_id?.toString() || '');
const userId = ref(props.filters.user_id?.toString() || '');

// Swipe state for each transaction
const swipeStates = ref<Record<number, number>>({});
const touchStartX = ref(0);

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
    startDate.value = '';
    endDate.value = '';
    categoryId.value = '';
    userId.value = '';
    router.get(index().url, {}, {
        preserveState: true,
        preserveScroll: true,
    });
    showFilters.value = false;
};

const hasActiveFilters = computed(() => {
    return props.filters.start_date || props.filters.end_date || props.filters.category_id || props.filters.user_id;
});

// Swipe handlers
const handleTouchStart = (e: TouchEvent, transactionId: number) => {
    touchStartX.value = e.touches[0].clientX;
};

const handleTouchMove = (e: TouchEvent, transactionId: number) => {
    const currentX = e.touches[0].clientX;
    const diff = touchStartX.value - currentX;

    if (diff > 0 && diff <= 120) {
        swipeStates.value[transactionId] = diff;
    }
};

const handleTouchEnd = (transactionId: number) => {
    const swipeDistance = swipeStates.value[transactionId] || 0;
    if (swipeDistance > 60) {
        swipeStates.value[transactionId] = 120;
    } else {
        swipeStates.value[transactionId] = 0;
    }
};

const resetSwipe = (transactionId: number) => {
    swipeStates.value[transactionId] = 0;
};

const confirmDelete = (transaction: Transaction) => {
    transactionToDelete.value = transaction;
    deleteDialogOpen.value = true;
};

const deleteTransaction = () => {
    if (transactionToDelete.value) {
        router.delete(destroy(transactionToDelete.value.id).url, {
            preserveScroll: true,
            onSuccess: () => {
                deleteDialogOpen.value = false;
                transactionToDelete.value = null;
            },
        });
    }
};

// Calculate running balance for each transaction
const transactionsWithBalance = computed(() => {
    let runningBalance = props.currentBalance;
    return props.transactions.data.map((transaction) => {
        const balanceAfter = runningBalance;
        runningBalance += parseFloat(transaction.amount);
        return {
            ...transaction,
            balanceAfter,
        };
    });
});
</script>

<template>
    <Head title="Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Daftar Transaksi
                    </h1>
                    <p class="text-sm text-gray-500">
                        {{ transactions.total }} transaksi tercatat
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        size="icon"
                        @click="showFilters = !showFilters"
                        :class="{ 'border-blue-500 bg-blue-50 text-blue-600': hasActiveFilters }"
                    >
                        <Filter class="h-4 w-4" />
                    </Button>
                    <Link :href="create().url">
                        <Button class="gap-2">
                            <Plus class="h-4 w-4" />
                            <span class="hidden sm:inline">Catat</span>
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Current Balance Card -->
            <Card class="border-0 bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
                <CardContent class="flex items-center justify-between p-4">
                    <div>
                        <p class="text-sm text-blue-200">Saldo Saat Ini</p>
                        <p class="text-2xl font-bold">{{ formatCurrency(currentBalance) }}</p>
                    </div>
                    <Wallet class="h-10 w-10 text-blue-200" />
                </CardContent>
            </Card>

            <!-- Filters Panel -->
            <Card v-if="showFilters" class="animate-in fade-in slide-in-from-top-4 duration-300">
                <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-base">Filter Transaksi</CardTitle>
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
                    v-if="filters.start_date"
                    class="flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                >
                    <span>Dari: {{ filters.start_date }}</span>
                </div>
                <div
                    v-if="filters.end_date"
                    class="flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                >
                    <span>Sampai: {{ filters.end_date }}</span>
                </div>
                <div
                    v-if="filters.category_id"
                    class="flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                >
                    <span>
                        {{
                            categories.find((c) => c.id === filters.category_id)?.name
                        }}
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

            <!-- Transactions List -->
            <Card class="flex-1">
                <CardContent class="p-0">
                    <div
                        v-if="transactions.data.length === 0"
                        class="flex flex-col items-center justify-center py-16 text-gray-500"
                    >
                        <Wallet class="mb-4 h-16 w-16 opacity-30" />
                        <p class="text-lg font-medium">Belum ada transaksi</p>
                        <p class="mb-4 text-sm">
                            Mulai catat pengeluaran kas kecil Anda
                        </p>
                        <Link :href="create().url">
                            <Button>
                                <Plus class="mr-2 h-4 w-4" />
                                Catat Pengeluaran
                            </Button>
                        </Link>
                    </div>

                    <div v-else class="divide-y dark:divide-gray-800">
                        <div
                            v-for="(transaction, idx) in transactionsWithBalance"
                            :key="transaction.id"
                            class="animate-in fade-in slide-in-from-right-4 relative overflow-hidden"
                            :style="{ animationDelay: `${idx * 30}ms` }"
                        >
                            <!-- Swipe Actions -->
                            <div
                                class="absolute right-0 top-0 flex h-full items-center"
                            >
                                <Link
                                    :href="edit(transaction.id).url"
                                    class="flex h-full w-15 items-center justify-center bg-blue-500 text-white transition-colors hover:bg-blue-600"
                                >
                                    <Pencil class="h-5 w-5" />
                                </Link>
                                <button
                                    @click="confirmDelete(transaction)"
                                    class="flex h-full w-15 items-center justify-center bg-red-500 text-white transition-colors hover:bg-red-600"
                                >
                                    <Trash2 class="h-5 w-5" />
                                </button>
                            </div>

                            <!-- Transaction Item -->
                            <div
                                class="relative bg-white p-4 transition-transform duration-200 dark:bg-gray-950"
                                :style="{
                                    transform: `translateX(-${swipeStates[transaction.id] || 0}px)`,
                                }"
                                @touchstart="handleTouchStart($event, transaction.id)"
                                @touchmove="handleTouchMove($event, transaction.id)"
                                @touchend="handleTouchEnd(transaction.id)"
                                @click="resetSwipe(transaction.id)"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                            :style="{
                                                backgroundColor:
                                                    (transaction.category?.color || '#6B7280') + '20',
                                            }"
                                        >
                                            <div
                                                class="h-3 w-3 rounded-full"
                                                :style="{
                                                    backgroundColor:
                                                        transaction.category?.color || '#6B7280',
                                                }"
                                            />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <p class="line-clamp-1 font-medium text-gray-900 dark:text-white">
                                                    {{ transaction.description }}
                                                </p>
                                                <!-- Receipt indicator -->
                                                <button
                                                    v-if="transaction.has_receipt"
                                                    type="button"
                                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 transition-transform active:scale-90 dark:bg-emerald-900/30 dark:text-emerald-400"
                                                    @click.stop="viewReceipt(transaction.receipt_url)"
                                                >
                                                    <ImageIcon class="h-3.5 w-3.5" />
                                                </button>
                                            </div>
                                            <p class="text-sm text-gray-500">
                                                {{ transaction.category?.name }}
                                                <span v-if="isAdmin && transaction.user" class="ml-1">
                                                    • {{ transaction.user.name }}
                                                </span>
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ formatDate(transaction.transaction_date) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-red-600">
                                            -{{ formatCurrency(transaction.amount) }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            Saldo: {{ formatCurrency(transaction.balanceAfter) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Desktop Actions -->
                                <div class="mt-2 hidden justify-end gap-2 sm:flex">
                                    <Link :href="edit(transaction.id).url">
                                        <Button variant="ghost" size="sm">
                                            <Pencil class="mr-1 h-3 w-3" />
                                            Edit
                                        </Button>
                                    </Link>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-red-600 hover:bg-red-50 hover:text-red-700"
                                        @click.stop="confirmDelete(transaction)"
                                    >
                                        <Trash2 class="mr-1 h-3 w-3" />
                                        Hapus
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div
                v-if="transactions.last_page > 1"
                class="flex items-center justify-between"
            >
                <p class="text-sm text-gray-500">
                    Menampilkan {{ transactions.from }} - {{ transactions.to }} dari
                    {{ transactions.total }}
                </p>
                <div class="flex gap-2">
                    <Link
                        v-if="transactions.prev_page_url"
                        :href="transactions.prev_page_url"
                        preserve-scroll
                    >
                        <Button variant="outline" size="sm">
                            <ArrowLeft class="mr-1 h-4 w-4" />
                            Sebelumnya
                        </Button>
                    </Link>
                    <Link
                        v-if="transactions.next_page_url"
                        :href="transactions.next_page_url"
                        preserve-scroll
                    >
                        <Button variant="outline" size="sm">
                            Selanjutnya
                            <ArrowRight class="ml-1 h-4 w-4" />
                        </Button>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Transaksi</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus transaksi
                        "{{ transactionToDelete?.description }}"? Tindakan ini
                        tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteDialogOpen = false">
                        Batal
                    </Button>
                    <Button variant="destructive" @click="deleteTransaction">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Receipt Viewer Dialog -->
        <ReceiptViewer
            v-model:open="showReceiptViewer"
            :image-url="selectedReceiptUrl"
            title="Bukti Transaksi"
        />
    </AppLayout>
</template>

