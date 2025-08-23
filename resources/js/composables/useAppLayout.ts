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

    // Ambil role user dari session
    const userRole = computed(() => page.props.auth?.user?.role);

    // Menu items
    const menuItems = computed<MenuItem[]>(() => {
        const items: MenuItem[] = [
            {
                label: 'Dashboard',
                lucideIcon: House, // Home icon for dashboard
                route: route('dashboard'),
                active: currentRoute.value == 'dashboard',
            },
            {
                label: 'Absensi',
                lucideIcon: LayoutGrid, // Grid for attendance
                route: route('absent'),
                active: currentRoute.value == 'absent',
            },
            {
                label: 'Pengajuan Cuti',
                lucideIcon: FileSearch, // File search for leave application
                route: route('leave.index'),
                active: currentRoute.value == 'leave.index',
            },
        ];

        // Tampilkan menu supervisor hanya jika user supervisor
        if (userRole.value === 'supervisor' || userRole.value === 'manager') {
            items.push({
                label: 'Supervisor',
                lucideIcon: Info, // Info icon for supervisor section
                items: [
                    {
                        label: 'Approval Cuti Bawahan',
                        route: route('leave.subordinate'),
                        lucideIcon: FileSearch, // File search for approval
                        active: currentRoute.value == 'leave.subordinate',
                    },
                    {
                        label: 'Dashboard Absensi Bawahan',
                        route: route('supervisor.attendance.dashboard'),
                        lucideIcon: LayoutGrid, // Grid for attendance dashboard
                        active: currentRoute.value == 'supervisor.attendance.dashboard',
                    },
                ],
            });
        }

        if (userRole.value === 'superadmin' || userRole.value === 'manager') {
            items.push({
                label: 'Master Data',
                lucideIcon: FolderGit2, // Folder for master data
                items: [
                    {
                        label: 'Department',
                        route: route('department.index'),
                        lucideIcon: LayoutGrid, // Grid for department
                        active: currentRoute.value == 'department.index',
                    },
                    {
                        label: 'Job Position',
                        route: route('position.index'),
                        lucideIcon: Info, // Info for job position
                        active: currentRoute.value == 'position.index',
                    },
                    {
                        label: 'Work Schedule',
                        route: route('work-schedule.index'),
                        lucideIcon: ExternalLink, // External link for work schedule
                        active: currentRoute.value == 'work-schedule.index',
                    },
                    {
                        label: 'Global Setting',
                        route: route('global-setting.index'),
                        lucideIcon: Settings, // Settings for global setting
                        active: currentRoute.value == 'global-setting.index',
                    },
                ],
            });
        }

        if (userRole.value === 'superadmin' || userRole.value === 'manager') {
            items.push({
                label: 'User Management',
                lucideIcon: Settings, // Settings for user management
                items: [
                    {
                        label: 'User List',
                        route: route('users.index'),
                        lucideIcon: Info, // Info for user list
                        active: currentRoute.value == 'users.index',
                    },                   
                ],
            });
        }
        return items;
    });

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
