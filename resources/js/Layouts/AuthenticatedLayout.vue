<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Modal from '@/Components/Modal.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const showingNavigationDropdown = ref(false);
const showGhnModal = ref(false);
const showGhnConfirm = ref(false);
const ghnCode = ref('');
const pendingGhnCode = ref('');

const printGHN = async () => {
    if (!ghnCode.value) return;
    const code = ghnCode.value.trim().toUpperCase();
    
    const maxChars = 8;
    const computedFontSize = code.length > maxChars ? Math.floor(54 * (maxChars / code.length)) : 54;
    
    const content = `
        <html>
            <head>
                <title>In đơn GHN</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f3f4f6; }
                    .mainPrints { 
                        width: 105mm; height: 74mm; 
                        background: white; border: none; 
                        box-sizing: border-box; 
                        padding: 3mm;
                        display: flex; flex-direction: column; 
                        justify-content: center; align-items: center; text-align: center;
                        margin: auto;
                    }
                    @media print { 
                        body { display: block; background-color: white; }
                        .mainPrints { page-break-after: always; box-shadow: none; border: none; margin: 0; } 
                        .mainPrints:last-of-type { page-break-after: avoid; } 
                        @page { size: 105mm 74mm; margin: 0; } 
                    }
                    .title { font-size: 20px; font-weight: 900; margin-bottom: 10px; line-height: 1.2; text-transform: uppercase; }
                    .code { font-weight: 900; letter-spacing: 2px; text-transform: uppercase; white-space: nowrap; width: 100%; text-align: center; }
                </style>
            </head>
            <body>
                <div class="mainPrints">
                    <div class="title">MÃ VẬN CHUYỂN<br/>GIAO HÀNG NHANH</div>
                    <div class="code" style="font-size: ${computedFontSize}px;">${code}</div>
                </div>
                <script>
                    window.print();
                    window.onafterprint = function() { window.close(); }
                <\/script>
            </body>
        </html>
    `;
    const printWindow = window.open('', '_blank');
    if(printWindow) {
        printWindow.document.open();
        printWindow.document.write(content);
        printWindow.document.close();
        printWindow.focus();
    }
    
    pendingGhnCode.value = code;
    
    const checkClosed = setInterval(async () => {
        if (printWindow && printWindow.closed) {
            clearInterval(checkClosed);
            showGhnConfirm.value = true;
        }
    }, 500);
    
    ghnCode.value = '';
    showGhnModal.value = false;
};

const confirmSaveGhn = async () => {
    try {
        await axios.post('/print-history', {
            recipient_name: 'Đơn GHN: ' + pendingGhnCode.value,
            recipient_phone: 'Mã vạch GHN',
            shipping_unit: 'Giao Hàng Nhanh'
        });
        showGhnConfirm.value = false;
        pendingGhnCode.value = '';
        if (window.location.pathname.includes('print-history')) {
            window.location.reload();
        } else {
            alert('Đã lưu lịch sử in GDN thành công!');
        }
    } catch (e) {
        console.error('Lỗi lưu lịch sử', e);
        alert('Lỗi không thể lưu lịch sử in');
    }
};

const cancelSaveGhn = () => {
    showGhnConfirm.value = false;
    pendingGhnCode.value = '';
};
</script>

<template>
    <div>
        
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                                <NavLink :href="route('print-history')" :active="route().current('print-history')">
                                    Print History
                                </NavLink>
                                <!-- <NavLink :href="route('manage')" :active="route().current('manage')">
                                    Manage Users
                                </NavLink> -->

                                <!-- Nút bật Modal in GHN (trông như 1 thẻ tab) -->
                                <button 
                                    @click="showGhnModal = true"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out cursor-pointer"
                                    style="margin-left: 2rem;"
                                >
                                    In Đơn GHN
                                </button>
                            </div>
                            
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>

        <!-- Modal in GHN -->
        <Modal :show="showGhnModal" @close="showGhnModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    In đơn Giao Hàng Nhanh
                </h2>
                <div class="flex items-center space-x-4">
                    <input 
                        type="text" 
                        v-model="ghnCode" 
                        @keyup.enter="printGHN"
                        placeholder="Nhập hoặc quét mã GHN rồi nhấn Enter..." 
                        class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm"
                        autofocus
                    >
                    <button 
                        @click="printGHN" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        IN GHN
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Modal Xác nhận lưu GHN chuyên nghiệp -->
        <Modal :show="showGhnConfirm" @close="cancelSaveGhn">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Xác nhận kết quả in
                </h2>
                <p class="text-sm text-gray-600 mb-6">
                    Lệnh in mã GHN <strong>{{ pendingGhnCode }}</strong> đã được xuất ra. Bạn đã in thành công và muốn lưu vào nhật ký in không?
                </p>
                <div class="flex items-center justify-end space-x-3">
                    <button 
                        @click="cancelSaveGhn"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        Chưa in (Không lưu)
                    </button>
                    <button 
                        @click="confirmSaveGhn"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Lưu vào Lịch sử
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>
