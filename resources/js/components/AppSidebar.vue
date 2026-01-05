<script setup lang="ts">
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
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, FileText, Inbox, Bookmark, Users, Activity } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const userRole = props.auth.user?.role;

const mainNavItems: NavItem[] = [];

// Dashboard always visible
mainNavItems.push({ title: 'Dashboard', href: dashboard(), icon: LayoutGrid });

// Library
if (['student','teacher','librarian'].includes(userRole)) {
    mainNavItems.push({ title: 'Library', href: '/library', icon: BookOpen });
}

// My Requests
if (['student','teacher'].includes(userRole)) {
     mainNavItems.push({ title: 'My Requests', href: '/my-requests', icon: FileText });
}

// Manage Requests / Active Borrows (Librarian)
if (userRole === 'librarian') {
    mainNavItems.push({ title: 'Manage Requests', href: '/manage-requests', icon: Inbox });
    mainNavItems.push({ title: 'Active Borrows', href: '/admin/active-borrows', icon: Activity });
}

// My Books (Student/Teacher only)
if (['student','teacher'].includes(userRole)) {
     mainNavItems.push({ title: 'My Books', href: '/my-books', icon: Bookmark });
}

// Learning Files
if (['student','teacher','librarian'].includes(userRole)) {
    mainNavItems.push({ title: 'Learning Files', href: '/learning-files', icon: Folder });
}

// User Management (Librarian/Admin only)
if (userRole === 'librarian') {
    mainNavItems.push({ title: 'Manage Users', href: '/users', icon: Users });
}


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
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
