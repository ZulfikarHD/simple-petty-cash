<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as transactionsIndex, create as createTransaction } from '@/actions/App/Http/Controllers/TransactionController';
import { create as createCashFund } from '@/actions/App/Http/Controllers/CashFundController';
import { index as reportsIndex } from '@/actions/App/Http/Controllers/ReportController';
import { index as usersIndex } from '@/actions/App/Http/Controllers/UserController';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, FileBarChart, Folder, LayoutGrid, List, Plus, Users, Wallet } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.is_admin ?? false);

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Transaksi',
        href: transactionsIndex(),
        icon: List,
    },
    {
        title: 'Laporan',
        href: reportsIndex(),
        icon: FileBarChart,
    },
    {
        title: 'Catat Pengeluaran',
        href: createTransaction(),
        icon: Plus,
    },
    {
        title: 'Tambah Dana',
        href: createCashFund(),
        icon: Wallet,
    },
];

const adminNavItems: NavItem[] = [
    {
        title: 'Kelola User',
        href: usersIndex(),
        icon: Users,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'GitHub Repo',
        href: 'https://github.com/ZulfikarHD/simple-petty-cash',
        icon: Folder,
    },
    {
        title: 'Dokumentasi',
        href: 'https://github.com/ZulfikarHD/simple-petty-cash#readme',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavMain v-if="isAdmin" :items="adminNavItems" label="Admin" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
