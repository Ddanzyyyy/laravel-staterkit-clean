<template>
  <aside
    :class="[
      'fixed mt-16 flex flex-col lg:mt-0 top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999 border-r border-gray-200',
      {
        'lg:w-[290px]': isExpanded || isMobileOpen || isHovered,
        'lg:w-[90px]': !isExpanded && !isHovered,
        'translate-x-0 w-[290px]': isMobileOpen,
        '-translate-x-full': !isMobileOpen,
        'lg:translate-x-0': true,
      },
    ]"
    @mouseenter="!isExpanded && (isHovered = true)"
    @mouseleave="isHovered = false"
  >
    <div
      :class="[
        'py-8 flex',
        !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
      ]"
    >
      <router-link to="/">
        <img
          v-if="isExpanded || isHovered || isMobileOpen"
          class="dark:hidden"
          src="/images/logo/logo.svg"
          alt="Logo"
          width="150"
          height="40"
        />
        <img
          v-if="isExpanded || isHovered || isMobileOpen"
          class="hidden dark:block"
          src="/images/logo/logo-dark.svg"
          alt="Logo"
          width="150"
          height="40"
        />
        <img
          v-else
          src="/images/logo/logo-icon.svg"
          alt="Logo"
          width="32"
          height="32"
        />
      </router-link>
    </div>
    <div
      class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar"
    >
      <nav class="mb-6">
        <div class="flex flex-col gap-4">
          <div v-for="(menuGroup, groupIndex) in menuGroups" :key="groupIndex">
            <h2
              :class="[
                'mb-4 text-xs uppercase flex leading-[20px] text-gray-400',
                !isExpanded && !isHovered
                  ? 'lg:justify-center'
                  : 'justify-start',
              ]"
            >
              <template v-if="isExpanded || isHovered || isMobileOpen">
                {{ menuGroup.title }}
              </template>
              <HorizontalDots v-else />
            </h2>
            <ul class="flex flex-col gap-4">
              <li v-for="(item, index) in menuGroup.items" :key="item.name">
                <button
                  v-if="item.subItems"
                  @click="toggleSubmenu(groupIndex, index)"
                  :class="[
                    'menu-item group w-full',
                    {
                      'menu-item-active': isSubmenuOpen(groupIndex, index),
                      'menu-item-inactive': !isSubmenuOpen(groupIndex, index),
                    },
                    !isExpanded && !isHovered
                      ? 'lg:justify-center'
                      : 'lg:justify-start',
                  ]"
                >
                  <span
                    :class="[
                      isSubmenuOpen(groupIndex, index)
                        ? 'menu-item-icon-active'
                        : 'menu-item-icon-inactive',
                    ]"
                  >
                    <component :is="item.icon" />
                  </span>
                  <span
                    v-if="isExpanded || isHovered || isMobileOpen"
                    class="menu-item-text"
                    >{{ item.name }}</span
                  >
                  <ChevronDownIcon
                    v-if="isExpanded || isHovered || isMobileOpen"
                    :class="[
                      'ml-auto w-5 h-5 transition-transform duration-200',
                      {
                        'rotate-180 text-brand-500': isSubmenuOpen(
                          groupIndex,
                          index
                        ),
                      },
                    ]"
                  />
                </button>
                <router-link
                  v-else-if="item.path"
                  :to="item.path"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive(item.path),
                      'menu-item-inactive': !isActive(item.path),
                    },
                  ]"
                >
                  <span
                    :class="[
                      isActive(item.path)
                        ? 'menu-item-icon-active'
                        : 'menu-item-icon-inactive',
                    ]"
                  >
                    <component :is="item.icon" />
                  </span>
                  <span
                    v-if="isExpanded || isHovered || isMobileOpen"
                    class="menu-item-text"
                    >{{ item.name }}</span
                  >
                </router-link>
                <transition
                  @enter="startTransition"
                  @after-enter="endTransition"
                  @before-leave="startTransition"
                  @after-leave="endTransition"
                >
                  <div
                    v-show="
                      isSubmenuOpen(groupIndex, index) &&
                      (isExpanded || isHovered || isMobileOpen)
                    "
                  >
                    <ul class="mt-2 space-y-1 ml-9">
                      <li v-for="subItem in item.subItems" :key="subItem.name">
                        <router-link
                          :to="subItem.path"
                          :class="[
                            'menu-dropdown-item',
                            {
                              'menu-dropdown-item-active': isActive(
                                subItem.path
                              ),
                              'menu-dropdown-item-inactive': !isActive(
                                subItem.path
                              ),
                            },
                          ]"
                        >
                          {{ subItem.name }}
                          <span class="flex items-center gap-1 ml-auto">
                            <span
                              v-if="subItem.new"
                              :class="[
                                'menu-dropdown-badge',
                                {
                                  'menu-dropdown-badge-active': isActive(
                                    subItem.path
                                  ),
                                  'menu-dropdown-badge-inactive': !isActive(
                                    subItem.path
                                  ),
                                },
                              ]"
                            >
                              new
                            </span>
                            <span
                              v-if="subItem.pro"
                              :class="[
                                'menu-dropdown-badge',
                                {
                                  'menu-dropdown-badge-active': isActive(
                                    subItem.path
                                  ),
                                  'menu-dropdown-badge-inactive': !isActive(
                                    subItem.path
                                  ),
                                },
                              ]"
                            >
                              pro
                            </span>
                          </span>
                        </router-link>
                      </li>
                    </ul>
                  </div>
                </transition>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <SidebarWidget v-if="isExpanded || isHovered || isMobileOpen" />
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRoute } from "vue-router";

import {
  LayoutDashboardIcon,
  BoxCubeIcon,
  ListIcon,
  BarChartIcon,
  PieChartIcon,
  BellIcon,
  UserGroupIcon,
  SettingsIcon,
  ChevronDownIcon,
  HorizontalDots,
} from "../../icons";
import SidebarWidget from "./SidebarWidget.vue";
import { useSidebar } from "@/composables/useSidebar";

const route = useRoute();

const { isExpanded, isMobileOpen, isHovered, openSubmenu } = useSidebar();

const menuGroups = [
  {
    title: "Utama",
    items: [
      {
        icon: LayoutDashboardIcon,
        name: "Dashboard",
        subItems: [
          { name: "Ringkasan Okupansi", path: "/" },
          { name: "Ringkasan Pendapatan", path: "/dashboard/revenue" },
          { name: "Grafik Tren Booking", path: "/dashboard/trend" },
          { name: "Reservasi Menunggu Approval", path: "/dashboard/pending" },
        ],
      },
    ],
  },
  {
    title: "Hotel",
    items: [
      {
        icon: BoxCubeIcon,
        name: "Manajemen Kamar",
        subItems: [
          { name: "Daftar Tipe Kamar", path: "/rooms/types" },
          { name: "Kalender Ketersediaan", path: "/rooms/calendar" },
          { name: "Blokir/Buka Kamar", path: "/rooms/maintenance" },
          { name: "Fasilitas & Detail", path: "/rooms/facilities" },
        ],
      },
      {
        icon: ListIcon,
        name: "Reservasi & Booking",
        subItems: [
          { name: "Daftar Reservasi", path: "/reservations" },
          { name: "Reservasi Pending", path: "/reservations/pending" },
          { name: "Reservasi Disetujui", path: "/reservations/approved" },
          { name: "Reservasi Ditolak", path: "/reservations/rejected" },
          { name: "Detail & Riwayat", path: "/reservations/history" },
          { name: "Check-in/Check-out", path: "/reservations/checkin" },
        ],
      },
      {
        icon: BarChartIcon,
        name: "Manajemen Harga",
        subItems: [
          { name: "Harga Reguler", path: "/pricing/default" },
          { name: "Harga Musiman", path: "/pricing/seasonal" },
          { name: "Kalender Harga", path: "/pricing/calendar" },
          { name: "Riwayat Perubahan", path: "/pricing/history" },
        ],
      },
    ],
  },
  {
    title: "Laporan",
    items: [
      {
        icon: PieChartIcon,
        name: "Laporan & Analitik",
        subItems: [
          { name: "Laporan Okupansi", path: "/reports/occupancy" },
          { name: "Laporan Pendapatan", path: "/reports/revenue" },
          { name: "Laporan Reservasi", path: "/reports/booking" },
          { name: "Ekspor Laporan", path: "/reports/export" },
        ],
      },
    ],
  },
  {
    title: "Sistem",
    items: [
      {
        icon: BellIcon,
        name: "Notifikasi",
        subItems: [
          { name: "Log Notifikasi", path: "/notifications" },
        ],
      },
      {
        icon: UserGroupIcon,
        name: "Manajemen Pengguna",
        subItems: [
          { name: "Daftar Admin/Staff", path: "/users" },
          { name: "Role & Hak Akses", path: "/users/roles" },
          { name: "Log Aktivitas", path: "/users/activity" },
        ],
      },
      {
        icon: SettingsIcon,
        name: "Pengaturan",
        subItems: [
          { name: "Profil Hotel", path: "/settings/hotel" },
          { name: "Kebijakan Check-in/Out", path: "/settings/policy" },
          { name: "Pengaturan Notifikasi", path: "/settings/notifications" },
          { name: "Pengaturan Umum", path: "/settings/general" },
        ],
      },
    ],
  },
];

const isActive = (path) => route.path === path;

const toggleSubmenu = (groupIndex, itemIndex) => {
  const key = `${groupIndex}-${itemIndex}`;
  openSubmenu.value = openSubmenu.value === key ? null : key;
};

const isAnySubmenuRouteActive = computed(() => {
  return menuGroups.some((group) =>
    group.items.some(
      (item) =>
        item.subItems && item.subItems.some((subItem) => isActive(subItem.path))
    )
  );
});

const isSubmenuOpen = (groupIndex, itemIndex) => {
  const key = `${groupIndex}-${itemIndex}`;
  return (
    openSubmenu.value === key ||
    (isAnySubmenuRouteActive.value &&
      menuGroups[groupIndex].items[itemIndex].subItems?.some((subItem) =>
        isActive(subItem.path)
      ))
  );
};

const startTransition = (el) => {
  el.style.height = "auto";
  const height = el.scrollHeight;
  el.style.height = "0px";
  el.offsetHeight; // force reflow
  el.style.height = height + "px";
};

const endTransition = (el) => {
  el.style.height = "";
};
</script>
