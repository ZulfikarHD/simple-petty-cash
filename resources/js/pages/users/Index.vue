<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index,
    create,
    destroy,
    edit,
} from '@/actions/App/Http/Controllers/UserController';
import type { BreadcrumbItem, PaginatedData, User } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    Filter,
    Pencil,
    Plus,
    Search,
    Shield,
    ShieldCheck,
    Trash2,
    User as UserIcon,
    Users,
    X,
} from 'lucide-vue-next';
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
    users: PaginatedData<User>;
    filters: {
        search?: string;
        role?: string;
    };
    statistics: {
        total: number;
        admins: number;
        users: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Kelola User', href: index().url },
];

const showFilters = ref(false);
const deleteDialogOpen = ref(false);
const userToDelete = ref<User | null>(null);

const searchQuery = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || '');

// Swipe state for each user
const swipeStates = ref<Record<number, number>>({});
const touchStartX = ref(0);

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const applyFilters = () => {
    const params: Record<string, string> = {};
    if (searchQuery.value) params.search = searchQuery.value;
    if (roleFilter.value) params.role = roleFilter.value;

    router.get(index().url, params, {
        preserveState: true,
        preserveScroll: true,
    });
    showFilters.value = false;
};

const clearFilters = () => {
    searchQuery.value = '';
    roleFilter.value = '';
    router.get(
        index().url,
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
    showFilters.value = false;
};

const hasActiveFilters = computed(() => {
    return props.filters.search || props.filters.role;
});

// Swipe handlers
const handleTouchStart = (e: TouchEvent, userId: number) => {
    touchStartX.value = e.touches[0].clientX;
};

const handleTouchMove = (e: TouchEvent, userId: number) => {
    const currentX = e.touches[0].clientX;
    const diff = touchStartX.value - currentX;

    if (diff > 0 && diff <= 120) {
        swipeStates.value[userId] = diff;
    }
};

const handleTouchEnd = (userId: number) => {
    const swipeDistance = swipeStates.value[userId] || 0;
    if (swipeDistance > 60) {
        swipeStates.value[userId] = 120;
    } else {
        swipeStates.value[userId] = 0;
    }
};

const resetSwipe = (userId: number) => {
    swipeStates.value[userId] = 0;
};

const confirmDelete = (user: User) => {
    userToDelete.value = user;
    deleteDialogOpen.value = true;
};

const deleteUser = () => {
    if (userToDelete.value) {
        router.delete(destroy(userToDelete.value.id).url, {
            preserveScroll: true,
            onSuccess: () => {
                deleteDialogOpen.value = false;
                userToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Kelola User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Kelola User
                    </h1>
                    <p class="text-sm text-gray-500">
                        {{ users.total }} user terdaftar
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        size="icon"
                        @click="showFilters = !showFilters"
                        :class="{
                            'border-blue-500 bg-blue-50 text-blue-600':
                                hasActiveFilters,
                        }"
                    >
                        <Filter class="h-4 w-4" />
                    </Button>
                    <Link :href="create().url">
                        <Button class="gap-2">
                            <Plus class="h-4 w-4" />
                            <span class="hidden sm:inline">Tambah</span>
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-3 gap-3">
                <Card
                    class="animate-in fade-in slide-in-from-bottom-4 border-0 bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg duration-300"
                >
                    <CardContent class="p-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20"
                            >
                                <Users class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-2xl font-bold">
                                    {{ statistics.total }}
                                </p>
                                <p class="text-xs text-blue-100">Total</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card
                    class="animate-in fade-in slide-in-from-bottom-4 border-0 bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg duration-300"
                    style="animation-delay: 50ms"
                >
                    <CardContent class="p-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20"
                            >
                                <ShieldCheck class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-2xl font-bold">
                                    {{ statistics.admins }}
                                </p>
                                <p class="text-xs text-amber-100">Admin</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card
                    class="animate-in fade-in slide-in-from-bottom-4 border-0 bg-gradient-to-br from-emerald-500 to-green-600 text-white shadow-lg duration-300"
                    style="animation-delay: 100ms"
                >
                    <CardContent class="p-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20"
                            >
                                <UserIcon class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-2xl font-bold">
                                    {{ statistics.users }}
                                </p>
                                <p class="text-xs text-emerald-100">User</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Filters Panel -->
            <Card
                v-if="showFilters"
                class="animate-in fade-in slide-in-from-top-4 duration-300"
            >
                <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-base">Filter User</CardTitle>
                        <Button
                            variant="ghost"
                            size="icon"
                            @click="showFilters = false"
                        >
                            <X class="h-4 w-4" />
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <Label class="text-sm">Cari</Label>
                            <div class="relative mt-1">
                                <Search
                                    class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                />
                                <Input
                                    v-model="searchQuery"
                                    type="text"
                                    class="pl-9"
                                    placeholder="Nama atau email..."
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                        </div>
                        <div>
                            <Label class="text-sm">Role</Label>
                            <select
                                v-model="roleFilter"
                                class="border-input bg-background mt-1 flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1"
                            >
                                <option value="">Semua Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">User Biasa</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <Button variant="outline" @click="clearFilters">
                            Reset
                        </Button>
                        <Button @click="applyFilters"> Terapkan Filter </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Active Filters -->
            <div
                v-if="hasActiveFilters && !showFilters"
                class="flex flex-wrap gap-2"
            >
                <div
                    v-if="filters.search"
                    class="flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                >
                    <Search class="h-3 w-3" />
                    <span>{{ filters.search }}</span>
                </div>
                <div
                    v-if="filters.role"
                    class="flex items-center gap-1 rounded-full bg-purple-100 px-3 py-1 text-sm text-purple-700 dark:bg-purple-900/30 dark:text-purple-300"
                >
                    <Shield class="h-3 w-3" />
                    <span>{{
                        filters.role === 'admin' ? 'Admin' : 'User Biasa'
                    }}</span>
                </div>
                <button
                    @click="clearFilters"
                    class="flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-600 transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300"
                >
                    <X class="h-3 w-3" />
                    Hapus Filter
                </button>
            </div>

            <!-- Users List -->
            <Card class="flex-1">
                <CardContent class="p-0">
                    <div
                        v-if="users.data.length === 0"
                        class="flex flex-col items-center justify-center py-16 text-gray-500"
                    >
                        <Users class="mb-4 h-16 w-16 opacity-30" />
                        <p class="text-lg font-medium">Belum ada user</p>
                        <p class="mb-4 text-sm">
                            Tambahkan user baru untuk memulai
                        </p>
                        <Link :href="create().url">
                            <Button>
                                <Plus class="mr-2 h-4 w-4" />
                                Tambah User
                            </Button>
                        </Link>
                    </div>

                    <div v-else class="divide-y dark:divide-gray-800">
                        <div
                            v-for="(user, idx) in users.data"
                            :key="user.id"
                            class="animate-in fade-in slide-in-from-right-4 relative overflow-hidden"
                            :style="{ animationDelay: `${idx * 30}ms` }"
                        >
                            <!-- Swipe Actions -->
                            <div
                                class="absolute right-0 top-0 flex h-full items-center"
                            >
                                <Link
                                    :href="edit(user.id).url"
                                    class="flex h-full w-15 items-center justify-center bg-blue-500 text-white transition-colors hover:bg-blue-600"
                                >
                                    <Pencil class="h-5 w-5" />
                                </Link>
                                <button
                                    @click="confirmDelete(user)"
                                    class="flex h-full w-15 items-center justify-center bg-red-500 text-white transition-colors hover:bg-red-600"
                                >
                                    <Trash2 class="h-5 w-5" />
                                </button>
                            </div>

                            <!-- User Item -->
                            <div
                                class="relative bg-white p-4 transition-transform duration-200 dark:bg-gray-950"
                                :style="{
                                    transform: `translateX(-${swipeStates[user.id] || 0}px)`,
                                }"
                                @touchstart="handleTouchStart($event, user.id)"
                                @touchmove="handleTouchMove($event, user.id)"
                                @touchend="handleTouchEnd(user.id)"
                                @click="resetSwipe(user.id)"
                            >
                                <div
                                    class="flex items-center justify-between gap-4"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full"
                                            :class="
                                                user.is_admin
                                                    ? 'bg-gradient-to-br from-amber-400 to-orange-500'
                                                    : 'bg-gradient-to-br from-blue-400 to-blue-500'
                                            "
                                        >
                                            <span
                                                class="text-lg font-semibold text-white"
                                            >
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div class="min-w-0">
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <p
                                                    class="line-clamp-1 font-medium text-gray-900 dark:text-white"
                                                >
                                                    {{ user.name }}
                                                </p>
                                                <span
                                                    v-if="user.is_admin"
                                                    class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
                                                >
                                                    <ShieldCheck
                                                        class="h-3 w-3"
                                                    />
                                                    Admin
                                                </span>
                                            </div>
                                            <p
                                                class="line-clamp-1 text-sm text-gray-500"
                                            >
                                                {{ user.email }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                Bergabung
                                                {{ formatDate(user.created_at) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Desktop Actions -->
                                    <div class="hidden gap-2 sm:flex">
                                        <Link :href="edit(user.id).url">
                                            <Button variant="ghost" size="sm">
                                                <Pencil class="mr-1 h-3 w-3" />
                                                Edit
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="text-red-600 hover:bg-red-50 hover:text-red-700"
                                            @click.stop="confirmDelete(user)"
                                        >
                                            <Trash2 class="mr-1 h-3 w-3" />
                                            Hapus
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div
                v-if="users.last_page > 1"
                class="flex items-center justify-between"
            >
                <p class="text-sm text-gray-500">
                    Menampilkan {{ users.from }} - {{ users.to }} dari
                    {{ users.total }}
                </p>
                <div class="flex gap-2">
                    <Link
                        v-if="users.prev_page_url"
                        :href="users.prev_page_url"
                        preserve-scroll
                    >
                        <Button variant="outline" size="sm">
                            <ArrowLeft class="mr-1 h-4 w-4" />
                            Sebelumnya
                        </Button>
                    </Link>
                    <Link
                        v-if="users.next_page_url"
                        :href="users.next_page_url"
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
                    <DialogTitle>Hapus User</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus user
                        "{{ userToDelete?.name }}"? Semua data terkait user ini
                        akan ikut terhapus.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteDialogOpen = false">
                        Batal
                    </Button>
                    <Button variant="destructive" @click="deleteUser">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

