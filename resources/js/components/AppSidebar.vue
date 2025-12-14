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
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
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

// Learning Files
if (['student','teacher','librarian'].includes(userRole)) {
    mainNavItems.push({ title: 'Learning Files', href: '/files', icon: Folder });
}

// Manage (only librarian)
if (userRole === 'librarian') {
    mainNavItems.push({ title: 'Manage', href: '/manage', icon: Folder });
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
