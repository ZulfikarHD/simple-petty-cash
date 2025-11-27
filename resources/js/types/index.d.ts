import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Category {
    id: number;
    name: string;
    icon: string | null;
    color: string | null;
    is_default: boolean;
    user_id: number | null;
    created_at: string;
    updated_at: string;
}

export interface Transaction {
    id: number;
    amount: string;
    description: string;
    transaction_date: string;
    category_id: number;
    user_id: number;
    category?: Category;
    user?: User;
    created_at: string;
    updated_at: string;
}

export interface CashFund {
    id: number;
    amount: string;
    note: string | null;
    fund_date: string;
    user_id: number;
    created_at: string;
    updated_at: string;
}

export interface SpendingByCategory {
    category_id: number;
    category_name: string;
    category_color: string;
    total: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}

export type BreadcrumbItemType = BreadcrumbItem;
