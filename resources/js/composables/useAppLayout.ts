import { ref, computed, onMounted, onUnmounted, watchEffect } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { LayoutGrid, House, Info, Settings, LogOut, ExternalLink, FileSearch, FolderGit2 } from 'lucide-vue-next';
import { MenuItem } from '@/types';

export function useAppLayout() {
    const page = usePage();
    const currentRoute = computed(() => {
        // Access page.url to trigger re-computation on navigation.
        /* eslint-disable @typescript-eslint/no-unused-vars */
        const url = page.url;
        /* eslint-enable @typescript-eslint/no-unused-vars */
        return route().current();
    });

    // Menu items
    const menuItems = computed<MenuItem[]>(() => [
        {
            label: 'Home',
            lucideIcon: House,
            route: route('welcome'),
            active: currentRoute.value == 'welcome',
        },
        {
            label: 'Dashboard',
            lucideIcon: LayoutGrid,
            route: route('dashboard'),
            active: currentRoute.value == 'dashboard',
        },
        {
            label: 'Pengajuan Cuti',
            lucideIcon: FileSearch,
            route: route('leave.index'),
            active: currentRoute.value == 'leave.index',
        },
        {
            label: 'Supervisor',
            lucideIcon: FolderGit2,
            items: [
                {
                    label: 'Approval Cuti Bawahan',
                    route: route('leave.subordinate'),
                    lucideIcon: ExternalLink,
                    active: currentRoute.value == 'leave.subordinate',
                },
            ],
        },
    ]);

    // User menu and logout functionality.
    const logoutForm = useForm({});
    const logout = () => {
        logoutForm.post(route('logout'));
    };
    const userMenuItems: MenuItem[] = [
        {
            label: 'Settings',
            route: route('profile.edit'),
            lucideIcon: Settings,
        },
        {
            separator: true
        },
        {
            label: 'Log out',
            lucideIcon: LogOut,
            command: () => logout(),
        },
    ];

    // Mobile menu
    const mobileMenuOpen = ref(false);
    if (typeof window !== 'undefined') {
        const windowWidth = ref(window.innerWidth);
        const updateWidth = () => {
            windowWidth.value = window.innerWidth;
        };
        onMounted(() => {
            window.addEventListener('resize', updateWidth);
        });
        onUnmounted(() => {
            window.removeEventListener('resize', updateWidth);
        });
        watchEffect(() => {
            if (windowWidth.value > 1024) {
                mobileMenuOpen.value = false;
            }
        });
    }

    return {
        currentRoute,
        menuItems,
        userMenuItems,
        mobileMenuOpen,
        logout,
    };
}
