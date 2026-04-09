@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Point of Sale - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@push('styles')
    {{-- Fix for Midtrans Snap CORS/PNA error on Localhost/127.0.0.1 --}}
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <style>
        /* ===================== GLOBAL & LAYOUT ===================== */
        html,
        body {
            height: 100%;
            overflow: hidden;
            /* No global scroll on desktop */
            color: #111827;
        }

        /* Responsive Scroll for Mobile/Tablet */
        @media (max-width: 1024px) {
            html, body {
                height: auto;
                overflow: auto;
                overflow-x: hidden;
            }
        }

        body {
            background-color: #f5f5f7;
        }

        main {
            height: calc(100vh - 64px) !important;
            overflow: hidden !important;
            display: flex;
            flex-direction: column;
            transition: height 0.3s ease-in-out;
            position: relative;
        }

        @media (max-width: 1024px) {
            main {
                height: auto !important;
                min-height: calc(100vh - 64px);
                overflow: visible !important;
            }
        }

        body.pos-fullscreen main {
            height: 100vh !important;
            min-height: 100vh !important;
        }

        body.pos-fullscreen .pos-container {
            height: 100vh !important;
        }

        @media (max-width: 1024px) {
            body.pos-fullscreen {
                overflow: auto !important;
            }

            body.pos-fullscreen main {
                height: auto !important;
                min-height: 100vh !important;
                overflow: visible !important;
            }

            body.pos-fullscreen .pos-container,
            body.pos-fullscreen .order-panel {
                height: auto !important;
                overflow: visible !important;
            }
        }

        /* Override wrapper div in app-sidebar to be full height and full width for POS */
        main>div {
            height: 100% !important;
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            display: flex;
            flex-direction: column;
        }

        .pos-container {
            flex: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            width: 100%;
            min-height: 0;
        }

        .breadcrumb-container {
            display: none;
        }

        .pos-main {
            flex: 1;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            gap: 0.75rem;
            padding: 0.75rem;
            overflow: hidden;
            min-height: 0;
            width: 100%;
        }

        .pos-main.layout-swapped {
            grid-template-columns: 360px minmax(0, 1fr) !important;
        }

        .pos-main.layout-swapped .order-panel {
            order: -1;
        }

        /* ===================== PANELS ===================== */
        .products-panel,
        .order-panel {
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            min-height: 0;
            border: 1px solid #e5e7eb;
            box-shadow:
                0 8px 16px rgba(15, 23, 42, 0.04),
                0 1px 3px rgba(15, 23, 42, 0.08);
        }

        .products-toolbar {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            flex-shrink: 0;
            background-color: #f9fafb;
        }

        .products-content {
            flex: 1;
            overflow-y: auto;
            padding: 0.75rem;
            min-height: 0;
        }

        /* ===================== SEARCH & FILTER ===================== */
        .search-box {
            position: relative;
            flex: 1;
            max-width: 320px;
        }

        .search-input {
            width: 100%;
            padding: 0.55rem 0.9rem 0.55rem 2.3rem;
            font-size: 0.813rem;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            color: #111827;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }

        .search-input::placeholder {
            color: #9ca3af;
        }

        .search-input:focus {
            outline: none;
            border-color: #658C58;
            background-color: #ffffff;
            box-shadow:
                0 0 0 1px rgba(101, 140, 88, 0.08),
                0 6px 10px rgba(15, 23, 42, 0.06);
        }

        .search-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.85rem;
            color: #9ca3af;
        }

        @media (max-width: 640px) {
            .search-box {
                max-width: none !important;
                width: 100%;
            }
        }

        .filter-select {
            padding: 0.5rem 0.75rem;
            font-size: 0.813rem;
            min-width: 140px;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            background-color: #ffffff;
            color: #111827;
            cursor: pointer;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
            /* appearance: none; */
            /* -webkit-appearance: none; */
            -moz-appearance: none;
        }

        .filter-select:focus {
            outline: none;
            border-color: #658C58;
            box-shadow: 0 0 0 1px rgba(101, 140, 88, 0.12);
        }

        .category-tabs {
            margin-top: 0.5rem;
            display: flex;
            gap: 0.35rem;
            overflow-x: auto;
            padding-bottom: 0.1rem;
        }

        .category-tabs::-webkit-scrollbar {
            height: 4px;
        }

        .category-tab {
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 999px;
            border: 1px solid transparent;
            background-color: #f3f4f6;
            color: #4b5563;
            white-space: nowrap;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }

        .category-tab i {
            font-size: 0.8rem;
        }

        .category-tab:hover {
            background-color: #e5e7eb;
        }

        .category-tab.active {
            background-color: #f0f7f0;
            border-color: #658C58;
            color: #31694E;
        }

        /* ===================== PRODUCT GRID ===================== */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 0.75rem;
        }

        .product-card {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.55rem;
            cursor: pointer;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.12s ease, background-color 0.15s ease;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .product-card:hover {
            border-color: #658C58;
            box-shadow:
                0 10px 18px rgba(15, 23, 42, 0.08),
                0 1px 3px rgba(15, 23, 42, 0.08);
            transform: translateY(-1px);
            background-color: #f0f7f0;
        }

        .product-image {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            background-color: #e5e7eb;
        }

        .product-placeholder {
            width: 100%;
            height: 100px;
            background-color: #f3f4f6;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .product-placeholder i {
            color: #9ca3af;
            font-size: 1.5rem;
        }

        .product-placeholder span {
            /* font-family: 'Outfit', sans-serif; */
        }

        .product-name {
            font-size: 0.78rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.12rem;
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 1.8rem;
        }

        .product-price {
            font-size: 0.82rem;
            font-weight: 700;
            color: #111827;
            margin-top: auto;
        }

        .product-stock {
            font-size: 0.65rem;
            margin-top: 0.15rem;
        }

        /* Discount Badge */
        .discount-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            z-index: 10;
        }

        .discount-badge>div {
            font-size: 0.62rem;
            padding: 0.18rem 0.45rem;
            border-radius: 999px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.15);
        }

        /* ===================== ORDER PANEL ===================== */
        .order-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            flex-shrink: 0;
            background-color: #f9fafb;
        }

        .order-items {
            flex: 1;
            overflow-y: auto;
            padding: 0.75rem;
            min-height: 0;
            background-color: #ffffff;
        }

        .order-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.6rem;
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease, transform 0.12s ease;
        }

        .order-item:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
            box-shadow: 0 7px 12px rgba(15, 23, 42, 0.06);
            transform: translateY(-1px);
        }

        .order-item-info {
            flex: 1;
            min-width: 0;
        }

        .order-item-name {
            font-size: 0.813rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .order-item-price {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }

        @media (max-width: 640px) {
            .order-items {
                padding: 1rem;
            }

            .order-item {
                padding: 0.85rem 1rem;
                gap: 0.75rem;
                margin-bottom: 0.75rem;
                border-radius: 12px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            }

            .order-item-name {
                font-size: 1rem;
                margin-bottom: 0.2rem;
                white-space: normal;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                line-height: 1.2;
            }

            .order-item-price {
                font-size: 0.875rem;
            }

            .summary-row {
                font-size: 0.95rem;
                margin-bottom: 0.4rem;
            }

            .summary-total {
                font-size: 1.25rem;
                padding-top: 0.75rem;
                margin-top: 0.75rem;
            }

            .order-footer {
                padding: 1.25rem 1rem 2rem;
            }

            .discount-item-name {
                font-size: 0.9rem;
            }

            .discount-item-detail {
                font-size: 0.8rem;
            }

            .discount-summary-btn .text-xs {
                font-size: 0.85rem !important;
            }

            .discount-summary-btn .text-[10px] {
                font-size: 0.7rem !important;
            }

            .discount-item-remove i {
                font-size: 0.9rem !important;
            }

            .order-item .text-\[10px\] {
                font-size: 0.75rem !important;
            }
        }

        .qty-controls {
            display: inline-flex;
            align-items: stretch;
            gap: 0;
            border: 1px solid #d1d5db;
            border-radius: 999px;
            overflow: hidden;
            background-color: #f9fafb;
        }

        .qty-btn {
            width: 23px;
            height: 23px;
            border: none;
            border-radius: 0;
            background: transparent;
            font-size: 0.8rem;
            color: #4b5563;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .qty-btn:hover {
            background-color: #e5e7eb;
            color: #111827;
        }

        .qty-input {
            width: 34px;
            height: 23px;
            font-size: 0.75rem;
            border: none;
            border-left: 1px solid #d1d5db;
            border-right: 1px solid #d1d5db;
            text-align: center;
            background-color: #ffffff;
            color: #111827;
        }

        .qty-input:focus {
            outline: none;
        }

        @media (max-width: 640px) {
            .qty-btn {
                width: 38px;
                height: 38px;
                font-size: 1.25rem;
            }
            .qty-input {
                width: 52px;
                height: 38px;
                font-size: 1.05rem;
            }
            .order-item .text-red-500 i {
                font-size: 1.15rem;
                padding-left: 0.5rem;
            }
        }

        .order-footer {
            padding: 0.8rem 1rem 1.5rem; /* Increased bottom padding for mobile safety */
            border-top: 1px solid #e5e7eb;
            flex-shrink: 0;
            background-color: #f9fafb;
        }

        .summary-row {
            margin-bottom: 0.25rem;
            font-size: 0.813rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-total {
            padding-top: 0.5rem;
            margin-top: 0.5rem;
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }

        /* ===================== BUTTONS ===================== */
        .btn-primary,
        .btn-secondary {
            width: 100%;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border: none;
            cursor: pointer;
            font-weight: 600;
            white-space: nowrap;
            transition: background-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease, transform 0.1s ease, border-color 0.18s ease;
        }

        .btn-primary {
            padding: 0.75rem;
            font-size: 0.875rem;
            background-color: #658C58;
            color: #ffffff;
            box-shadow:
                0 10px 18px rgba(101, 140, 88, 0.35),
                0 1px 3px rgba(15, 23, 42, 0.25);
        }

        .btn-primary:hover {
            background-color: #31694E;
            transform: translateY(-1px);
            box-shadow:
                0 14px 24px rgba(49, 105, 78, 0.4),
                0 2px 4px rgba(15, 23, 42, 0.35);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow:
                0 7px 12px rgba(101, 140, 88, 0.35),
                0 1px 3px rgba(15, 23, 42, 0.35);
        }

        .btn-secondary {
            margin-top: 0.45rem;
            padding: 0.55rem 0.75rem;
            font-size: 0.813rem;
            background-color: #ffffff;
            color: #4b5563;
            border: 1px solid #e5e7eb;
            box-shadow: 0 3px 6px rgba(15, 23, 42, 0.06);
        }

        .btn-secondary:hover {
            background-color: #f3f4f6;
            transform: translateY(-1px);
        }

        /* ===================== DISCOUNT LIST ===================== */
        .discount-container {
            margin-bottom: 0.75rem;
        }

        .discount-summary-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: linear-gradient(to right, #fef2f2, #fff1f2);
            border: 1px solid #fecaca;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .discount-summary-btn:hover {
            background: linear-gradient(to right, #fee2e2, #ffe4e6);
            border-color: #fda4af;
        }

        .discount-list {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background-color: #fff;
            border-radius: 0 0 12px 12px;
            border: 1px solid #fecaca;
            border-top: none;
            margin-top: -6px;
            padding-top: 6px;
        }

        .discount-list.open {
            max-height: 500px;
            /* arbitrary large value */
            transition: max-height 0.3s ease-in;
        }

        .discount-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.6rem 1rem;
            border-bottom: 1px dashed #fee2e2;
        }

        .discount-item:last-child {
            border-bottom: none;
        }

        .discount-item-info {
            flex: 1;
            min-width: 0;
        }

        .discount-item-name {
            font-size: 0.75rem;
            font-weight: 700;
            color: #991b1b;
            display: block;
            truncate: true;
        }

        .discount-item-detail {
            font-size: 0.65rem;
            color: #b91c1c;
            display: block;
        }

        .discount-item-remove {
            padding: 0.35rem;
            color: #f87171;
            transition: color 0.2s;
            cursor: pointer;
        }

        .discount-item-remove:hover {
            color: #dc2626;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        @media (max-width: 1024px) and (orientation: portrait) {
            .pos-main {
                grid-template-columns: 1fr;
                display: flex;
                flex-direction: column;
                height: auto !important;
                overflow: visible !important;
            }

            body.pos-fullscreen main {
                min-height: 100vh !important;
                height: auto !important;
                overflow: visible !important;
            }

            .order-panel {
                order: 2;
                margin-top: 0.5rem;
            }
        }

        @media (max-width: 1024px) and (orientation: landscape) {
            .pos-main {
                grid-template-columns: 1fr 320px;
                height: calc(100vh - 64px) !important;
                overflow: hidden !important;
            }

            body.pos-fullscreen .pos-main {
                height: auto !important;
                overflow: visible !important;
            }

            body.pos-fullscreen main {
                height: auto !important;
                min-height: 100vh !important;
                overflow: visible !important;
            }

            .products-content, .order-items {
                overflow-y: auto;
            }

            body.pos-fullscreen .products-content, 
            body.pos-fullscreen .order-items {
                overflow-y: visible !important;
                max-height: none !important;
            }
        }

        @media (max-width: 640px) {
            .pos-main {
                padding: 0.5rem;
                gap: 0.5rem;
            }

            .order-panel {
                height: auto;
                max-height: none;
                margin-bottom: 4rem; /* Safe area for mobile navigation */
            }

            .btn-primary {
                padding: 0.85rem;
                font-size: 0.95rem;
            }
            
            .products-toolbar .flex.gap-2.justify-between {
                flex-direction: column;
                gap: 0.75rem !important;
            }
            
            .products-toolbar .flex.gap-2.w-full.max-w-xl {
                flex-direction: column;
                max-width: none;
            }
            
            #customerSearchContainer {
                max-width: none !important;
                width: 100%;
            }
        }

        /* ===================== EMPTY STATE ===================== */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1.25rem;
            background-color: #f9fafb;
            border-radius: 10px;
            border: 1px dashed #d1d5db;
            color: #6b7280;
            text-align: center;
            font-size: 0.875rem;
        }

        .empty-state i {
            font-size: 1.6rem;
            color: #9ca3af;
            margin-bottom: 0.5rem;
        }

        /* ===================== PAYMENT AREA ===================== */
        .payment-view {
            animation: fadeIn 0.15s ease-out;
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 0.85rem;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
            cursor: pointer;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease, background-color 0.15s ease;
        }

        .payment-method:hover {
            border-color: #658C58;
            box-shadow:
                0 10px 14px rgba(15, 23, 42, 0.08),
                0 1px 3px rgba(15, 23, 42, 0.08);
            transform: translateY(-1px);
            background-color: #f0f7f0;
        }

        .payment-icon {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            flex-shrink: 0;
            background-color: #658C58;
        }

        .payment-info {
            flex: 1;
        }

        .payment-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #111827;
        }

        .payment-subtitle {
            font-size: 0.75rem;
            color: #6b7280;
        }

        /* ===================== CALCULATOR ===================== */
        .calc-btn {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
            padding: 0.8rem 0.2rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        @media (max-width: 640px) {
            .calc-btn {
                padding: 1.25rem 0.5rem;
                font-size: 1.25rem;
                border-radius: 16px;
            }
        }

        .calc-btn:hover {
            background-color: #f3f4f6;
            border-color: #d4d4d8;
            box-shadow: 0 6px 10px rgba(15, 23, 42, 0.08);
            transform: translateY(-1px);
        }

        .calc-history-item {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
            background-color: #ffffff;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease;
            font-size: 0.78rem;
        }

        .calc-history-item:hover {
            background-color: #f3f4f6;
            border-color: #d4d4d8;
            box-shadow: 0 6px 10px rgba(15, 23, 42, 0.08);
            transform: translateY(-1px);
        }

        .calc-history-expression {
            color: #6b7280;
        }

        .calc-history-result {
            font-weight: 600;
            color: #111827;
        }

        /* ===================== TOAST ===================== */
        #toastContainer>div {
            border-radius: 8px;
            box-shadow:
                0 14px 28px rgba(15, 23, 42, 0.35),
                0 0 0 1px rgba(15, 23, 42, 0.06);
        }

        /* ===================== SCROLLBAR ===================== */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 999px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        /* ===================== ANIMATIONS ===================== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(3px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===================== RESPONSIVE ===================== */
        @media (max-width: 1024px) {
            .pos-container {
                height: auto;
                overflow: visible;
                padding-bottom: 1rem;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }
        }

        @media (max-width: 640px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .order-header,
            .order-footer {
                padding-inline: 0.75rem;
            }
            
            .order-item {
                padding: 0.85rem; /* Even larger padding for touch */
                gap: 0.75rem;
                margin-bottom: 0.75rem;
            }

            .order-item-name {
                font-size: 0.95rem; /* Slightly larger */
                margin-bottom: 0.2rem;
            }
        }

        @media (max-width: 640px) {
            .pos-main {
                padding: 0.5rem;
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .order-header,
            .order-footer {
                padding-inline: 0.75rem;
            }
        }

        .customer-search-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: background-color 0.15s ease;
            border-bottom: 1px solid #f3f4f6;
        }

        .customer-search-item:last-child {
            border-bottom: none;
        }

        .customer-search-item:hover {
            background-color: #f0f7f0;
        }

        .customer-search-item.selected {
            background-color: #eef2ed;
            border-left: 3px solid #658C58;
        }

        /* Modal Table Styles */
        #salesTableBody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        #salesTableBody tr:hover {
            background-color: #f9fafb;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* ==================== UNIFIED MODAL SYSTEM ==================== */
        /* 
     * All modals share:
     * - Consistent rounded corners (rounded-2xl = 1rem)
     * - Full-screen on mobile (≤640px)
     * - Responsive sizing on tablet/desktop
     */

        /* Base Modal Overlay - All modals */
        #startSalesModal,
        #paymentSuccessModal,
        #openingAmountModal,
        #calculatorModal,
        #productSettingsModal,
        #salesTodayModal,
        #saleDetailModal,
        #debtPaymentModal,
        #tableManagementModal,
        #financeModal {
            backdrop-filter: blur(2px);
        }

        /* Base Modal Content Box */
        .modal-content,
        #startSalesModal>div,
        #paymentSuccessModal>div,
        #openingAmountModal>div {
            display: flex;
            flex-direction: column;
            max-height: 90vh;
            overflow: hidden;
            border-radius: 1rem;
            /* rounded-2xl */
        }

        /* Desktop Modal Sizes */
        @media (min-width: 641px) {

            /* Large - Sales Today Modal */
            #salesTodayModal .modal-content {
                max-width: 1400px;
                width: 95vw;
            }

            /* Medium Large - Product Settings, Table Management */
            #productSettingsModal .modal-content,
            #tableManagementModal .modal-content {
                max-width: 900px;
                width: 90vw;
            }

            /* Medium - Sale Detail, Calculator, Debt Payment */
            #saleDetailModal .modal-content {
                max-width: 900px;
                width: 90vw;
            }

            #calculatorModal .modal-content {
                max-width: 700px;
                width: 90vw;
            }

            #debtPaymentModal .modal-content {
                max-width: 650px;
                width: 90vw;
            }

            /* Small - Start Sales, Payment Success, Opening Amount */
            #startSalesModal>div,
            #paymentSuccessModal>div,
            #openingAmountModal>div {
                max-width: 420px;
                width: 90vw;
            }
        }

        /* ==================== MOBILE FULL-SCREEN MODALS ==================== */
        @media (max-width: 640px) {

            /* All modals go full-screen */
            #startSalesModal,
            #paymentSuccessModal,
            #openingAmountModal,
            #calculatorModal,
            #productSettingsModal,
            #salesTodayModal,
            #saleDetailModal,
            #debtPaymentModal,
            #tableManagementModal,
            #financeModal {
                padding: 0 !important;
            }

            #startSalesModal>div,
            #paymentSuccessModal>div,
            #openingAmountModal>div,
            #calculatorModal .modal-content,
            #productSettingsModal .modal-content,
            #salesTodayModal .modal-content,
            #saleDetailModal .modal-content,
            #debtPaymentModal .modal-content,
            #tableManagementModal .modal-content,
            #financeModal .modal-content {
                width: 100vw !important;
                max-width: 100vw !important;
                height: 100vh !important;
                max-height: 100vh !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }

            /* Ensure content scrolls properly - Small centered modals */
            #startSalesModal>div,
            #paymentSuccessModal>div,
            #openingAmountModal>div {
                overflow-y: auto;
                justify-content: center;
                padding: 1.5rem;
                height: auto !important;
                min-height: 100vh !important;
            }

            /* Large complex modals - fixed layout */
            #calculatorModal>div,
            #productSettingsModal>div,
            #debtPaymentModal>div,
            #tableManagementModal .modal-content {
                overflow: hidden;
                padding: 0;
            }

            /* Ensure table-based modals have proper scrolling */
            #salesTodayModal .modal-content,
            #saleDetailModal .modal-content,
            #tableManagementModal .modal-content {
                overflow: hidden;
            }

            /* Mobile table font size adjustment */
            #salesTodayModal table,
            #saleDetailModal table {
                font-size: 0.75rem;
            }

            #salesTodayModal th,
            #salesTodayModal td,
            #saleDetailModal th,
            #saleDetailModal td {
                padding: 0.5rem 0.25rem;
            }
        }

        /* Scrollable Content Area */
        .modal-scrollable-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            min-height: 0;
        }

        /* Table Wrapper untuk horizontal scroll */
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-wrapper table {
            min-width: 100%;
            border-collapse: collapse;
        }

        /* Sticky Table Header */
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f9fafb;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        /* Summary Cards - Better Grid */
        .summary-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1.5rem;
            background: linear-gradient(to bottom, #f9fafb, #ffffff);
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .summary-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }

        .summary-card-label {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 0.25rem;
        }

        .summary-card-subvalue {
            font-size: 0.813rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }

        /* Mobile Optimizations - Summary Cards & Tables */
        @media (max-width: 1024px) {
            .summary-cards-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
                padding: 1rem;
            }

            .table-wrapper table {
                font-size: 0.75rem;
            }

            .table-wrapper th,
            .table-wrapper td {
                padding: 0.5rem 0.25rem;
                white-space: nowrap;
            }
        }

        @media (max-width: 640px) {
            .summary-cards-container {
                grid-template-columns: 1fr;
                gap: 0.5rem;
                padding: 0.75rem;
            }

            .table-wrapper {
                font-size: 0.7rem;
            }

            .table-wrapper th,
            .table-wrapper td {
                padding: 0.4rem 0.2rem;
            }

            /* Compact summary cards */
            .summary-card-value {
                font-size: 1.25rem;
            }

            .summary-card-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }
        }

        /* Custom Scrollbar untuk Modal */
        .modal-scrollable-content::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .modal-scrollable-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modal-scrollable-content::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .modal-scrollable-content::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Table Wrapper Scrollbar */
        .table-wrapper::-webkit-scrollbar {
            height: 6px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        /* Detail Modal - Better Layout */
        .detail-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .detail-info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            transition: transform 0.2s;
        }

        .detail-info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        /* Items Table - Better spacing */
        .items-table-container {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .items-table-container table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .items-table-container thead {
            background: linear-gradient(to bottom, #f9fafb, #f3f4f6);
        }

        .items-table-container tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Payment Summary Box */
        .payment-summary-box {
            background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Better Badge Styling */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        /* Action Buttons - Consistent sizing */
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.813rem;
            font-weight: 600;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .action-btn-primary {
            background-color: #3b82f6;
            color: white;
            border: none;
        }

        .action-btn-primary:hover {
            background-color: #2563eb;
        }

        .action-btn-danger {
            background-color: #ef4444;
            color: white;
            border: none;
        }

        .action-btn-danger:hover {
            background-color: #dc2626;
        }

        .action-btn-secondary {
            background-color: white;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        .action-btn-secondary:hover {
            background-color: #f9fafb;
        }

        /* Modal Header - Fixed */
        .modal-header-fixed {
            flex-shrink: 0;
            border-bottom: 1px solid #e5e7eb;
            background: white;
            z-index: 10;
        }

        /* Modal Footer - Fixed */
        .modal-footer-fixed {
            flex-shrink: 0;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
            z-index: 10;
        }
    </style>
@endpush

@section('content')
    <!-- POS Init Loader -->
    <div id="pos-init-loader"
        class="fixed inset-0 bg-white z-[99999] flex flex-col items-center justify-center transition-opacity duration-300">
        <svg class="w-16 h-16 animate-spin text-cuan-dark" viewBox="0 0 80 80" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M40 10V70M10 40H70M20 20L60 60M60 20L20 60" stroke="currentColor" stroke-width="8"
                stroke-linecap="round" />
        </svg>
        <div class="flex gap-1.5 mt-4">
            <div class="w-1.5 h-1.5 bg-cuan-dark rounded-full animate-bounce"></div>
            <div class="w-1.5 h-1.5 bg-cuan-dark rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
            <div class="w-1.5 h-1.5 bg-cuan-dark rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
        </div>
        <p class="mt-3 text-cuan-dark font-semibold text-sm animate-pulse">Menyiapkan Kasir...</p>
    </div>
    <!-- Toast Container -->


    <!-- Modal: Mulai Penjualan -->
    <div id="startSalesModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 lg:p-8">
            <div class="text-center mb-5">
                <h3 class="text-2xl font-black text-gray-900 mb-1">Selamat Datang!</h3>
                <p class="text-gray-500 font-medium tracking-tight">Mulai sesi penjualan Anda hari ini</p>
            </div>
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 mb-5">
                <p class="text-[11px] text-gray-400 text-center uppercase font-bold tracking-widest">
                    SESI BARU
                </p>
            </div>
            <div class="flex gap-2.5">
                <button onclick="declineStartSales()"
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Tidak
                </button>
                @can('buka kasir')
                    <button onclick="openOpeningAmountModal()"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-cuan-green to-cuan-dark text-white rounded-lg text-sm font-semibold hover:opacity-90 transition-all shadow-md hover:shadow-lg">
                        Ya, Mulai
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Modal: Payment Success -->
    <div id="paymentSuccessModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 lg:p-8">
            <div class="text-center mb-5">
                <h3 class="text-2xl font-black text-cuan-green">Berhasil!</h3>
                <p class="text-gray-500 text-sm font-medium">Transaksi telah diproses</p>
            </div>
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 mb-5">
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">No. Invoice:</span>
                        <span class="font-bold text-gray-900" id="successInvoiceNumber">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal:</span>
                        <span class="text-gray-900 font-medium" id="successDate">-</span>
                    </div>
                    <div class="flex justify-between" id="successCustomerRow" style="display: none;">
                        <span class="text-gray-600">Pelanggan:</span>
                        <span class="font-bold text-gray-900" id="successCustomer">-</span>
                    </div>
                    <div class="flex justify-between text-base font-bold border-t border-cuan-green/20 pt-2 mt-2">
                        <span class="text-gray-700">Total:</span>
                        <span class="text-cuan-green" id="successTotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm" id="successChangeRow" style="display: none;">
                        <span class="text-gray-600">Kembalian:</span>
                        <span class="font-bold text-green-600" id="successChange">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm" id="successDebtRow" style="display: none;">
                        <span class="text-gray-600">Sisa Utang:</span>
                        <span class="font-bold text-red-600" id="successDebt">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-xs py-1" id="successResellerSyncRow" style="display: none;">
                        <span class="text-blue-600 font-semibold italic">Sync Reseller:</span>
                        <span class="font-bold text-blue-700" id="successResellerSync">Data Berhasil Disinkronkan</span>
                    </div>
                </div>
            </div>
            <div class="space-y-2.5">
                @can('cetak struk')
                    <button onclick="printReceipt()"
                        class="w-full flex items-center justify-center gap-2.5 px-4 py-3 bg-gradient-to-r from-cuan-green to-cuan-dark text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-all shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        <span>Cetak Struk</span>
                    </button>
                @endcan
                @can('unduh struk')
                    <button onclick="downloadReceipt()"
                        class="w-full flex items-center justify-center gap-2.5 px-4 py-3 bg-white border-2 border-cuan-green/20 text-cuan-green rounded-xl text-sm font-semibold hover:bg-cuan-green/10 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span>Download Struk (PDF)</span>
                    </button>
                @endcan

                <button onclick="handlePrintInvoice()"
                    class="w-full flex items-center justify-center gap-2.5 px-4 py-3 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition-all shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span>Cetak Surat Invoice</span>
                </button>
                <button onclick="closePaymentSuccessModal()"
                    class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition-all">
                    Selesai
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Invoice Details (Popup if no customer) -->
    <div id="invoiceDetailsModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Data Invoice</h3>
                <button onclick="closeInvoiceDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">Silakan lengkapi data pelanggan untuk tampilan di invoice.</p>

            <form id="invoiceDetailsForm" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Pelanggan</label>
                    <input type="text" name="customer_name" id="inv_customer_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Contoh: John Doe">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nomor Telepon</label>
                    <input type="text" name="customer_phone" id="inv_customer_phone"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Contoh: 08123456789">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alamat</label>
                    <textarea name="customer_address" id="inv_customer_address" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Contoh: Jl. Merdeka No. 123"></textarea>
                </div>
                <div id="inv_due_date_container">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jatuh Tempo (Opsional)</label>
                    <input type="date" name="due_date" id="inv_due_date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <button type="button" onclick="submitInvoiceDetails()"
                    class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-md mt-2">
                    Cetak Invoice
                </button>
            </form>
        </div>
    </div>

    <div class="pos-container">

        <!-- Main Content -->
        <div class="pos-main">
            <!-- Left Panel: Products -->
            <div class="products-panel">
                <div class="products-toolbar">
                    <div class="flex gap-2 justify-between">
                        <div class="flex gap-2 w-full max-w-xl">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" id="searchProduct" class="search-input" placeholder="Cari produk...">
                            </div>

                            <!-- Customer Search Dropdown -->
                            <div class="flex items-center gap-2 w-full max-w-[360px]">
                                <div class="relative flex-1" id="customerSearchContainer">
                                    <div class="relative">
                                        <i
                                            class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                        <input type="text" id="searchCustomer" class="search-input !pl-9"
                                            placeholder="Masukan data pelanggan...  (Nama, Email, No Tlp)"
                                            autocomplete="off">
                                        <button id="clearCustomerBtn"
                                            class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <!-- Dropdown Results -->
                                    <div id="customerSearchResults"
                                        class="hidden absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-60 overflow-y-auto custom-scrollbar">
                                        <!-- Results populated via JS -->
                                    </div>
                                </div>

                                <!-- Customer Type Badge -->
                                <div id="customerTypeBadge"
                                    class="hidden items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold shadow-sm transition-all animate-fadeIn whitespace-nowrap border">
                                    <i id="customerTypeIcon" class="fas fa-crown"></i>
                                    <span id="customerTypeText" class="hidden md:inline">VIP</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="openScanner()"
                                class="h-[38px] w-[38px] flex-shrink-0 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 hover:bg-purple-200 transition-colors border border-purple-200"
                                title="Scan Barcode">
                                <i class="fas fa-qrcode"></i>
                            </button>
                            <button onclick="openPendingProductionModal()"
                                class="h-[38px] px-3 flex-shrink-0 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors border border-blue-100 font-bold text-xs gap-2"
                                title="Antrean Dapur">
                                <i class="fas fa-utensils"></i>
                            </button>
                        </div>
                    </div>
                    <div class="category-tabs" id="categoryTabs">
                    </div>
                </div>

                <div class="products-content custom-scrollbar">
                    <!-- Browse Products View -->
                    <div id="view-browse">
                        <div class="product-grid" id="productGrid">
                            @forelse($products as $product)
                                <div class="product-card" data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}" data-product-code="{{ $product->code }}"
                                    data-product-barcode="{{ $product->barcode }}"
                                    data-product-price="{{ $product->selling_price }}"
                                    data-product-reseller-price="{{ $product->reseller_price }}"
                                    data-product-promo-price="{{ $product->promo_price }}"
                                    data-product-hpp="{{ $product->hpp }}" data-category="{{ $product->category_id }}" @php
                                        $isProduced = !$product->is_stock;
                                        if ($isProduced) {
                                            $estStock = $product->getEstimatedStockPortions(auth()->user()->outlet_id);
                                        } else {
                                            $stock = $product->stocks->where('outlet_id', auth()->user()->outlet_id)->first();
                                            $estStock = $stock ? $stock->quantity : 0;
                                        }
                                    @endphp data-estimated-stock="{{ $estStock }}"
                                    data-is-produced="{{ $isProduced ? 'true' : 'false' }}" onclick="addProductToCart(this)">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                            class="product-image">
                                    @else
                                        <div class="product-placeholder">
                                            <span
                                                class="text-gray-400 text-3xl font-bold tracking-wider">{{ $product->initials }}</span>
                                        </div>
                                    @endif
                                    <div class="product-name">{{ $product->name }}</div>
                                    <div class="product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </div>
                                    @if(!$product->is_stock)
                                        @php
                                            $estimatedStock = $product->getEstimatedStockPortions(auth()->user()->outlet_id);
                                        @endphp
                                        <div class="product-stock {{ $estimatedStock > 0 ? 'text-blue-600' : 'text-red-400' }}"
                                            data-product-id="{{ $product->id }}"
                                            title="Estimasi stok berdasarkan bahan baku yang tersedia">
                                            ± <span class="stock-qty">{{ number_format($estimatedStock, 0, ',', '.') }}</span> porsi
                                        </div>
                                    @elseif($product->track_stock)
                                        @php
                                            $stock = $product->stocks->where('outlet_id', auth()->user()->outlet_id)->first();
                                            $stockQty = $stock ? $stock->quantity : 0;
                                        @endphp
                                        <div class="product-stock {{ $stockQty > 0 ? 'text-green-600' : 'text-red-600' }}"
                                            data-product-id="{{ $product->id }}">
                                            Stok: <span class="stock-qty">{{ number_format($stockQty, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="empty-state" style="grid-column: 1/-1;">
                                    <i class="fas fa-box-open"></i>
                                    <p>Belum ada produk tersedia</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Service Type View -->
                    <div id="view-service" class="hidden payment-view">
                        <button onclick="backToBrowse()"
                            class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 text-sm font-medium">
                            <i class="fas fa-arrow-left"></i> Kembali ke Menu
                        </button>

                        <div
                            class="bg-gray-50 border border-gray-200 rounded-2xl p-6 mb-8 flex flex-col items-center justify-center text-center">
                            <p class="text-sm text-gray-500 uppercase tracking-wider mb-1 font-semibold">Total Pembayaran
                            </p>
                            <p class="text-4xl font-extrabold text-gray-900" id="serviceSelectTotal">Rp 0</p>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">Pilih Tipe Layanan</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div onclick="selectServiceType('dine_in')"
                                class="flex flex-col items-center gap-4 p-8 bg-white border-2 border-gray-200 rounded-2xl cursor-pointer hover:border-cuan-green hover:bg-cuan-green/5 transition-all group">
                                <div
                                    class="w-20 h-20 bg-cuan-green/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-utensils text-3xl text-cuan-green"></i>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-bold text-gray-900">Makan di Tempat</p>
                                    <p class="text-sm text-gray-500">Dine In</p>
                                </div>
                            </div>

                            <div onclick="selectServiceType('take_away')"
                                class="flex flex-col items-center gap-4 p-8 bg-white border-2 border-gray-200 rounded-2xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all group">
                                <div
                                    class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-shopping-bag text-3xl text-blue-600"></i>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-bold text-gray-900">Bawa Pulang</p>
                                    <p class="text-sm text-gray-500">Take Away</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Selection View (only if has_table_system) -->
                    <div id="view-table" class="hidden payment-view">
                        <button onclick="setUIState('service')"
                            class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 text-sm font-medium">
                            <i class="fas fa-arrow-left"></i> Kembali ke Tipe Layanan
                        </button>

                        <div
                            class="bg-gray-50 border border-gray-200 rounded-2xl p-6 mb-6 flex flex-col items-center justify-center text-center">
                            <p class="text-sm text-gray-500 uppercase tracking-wider mb-1 font-semibold">Total Pembayaran
                            </p>
                            <p class="text-4xl font-extrabold text-gray-900" id="tableSelectTotal">Rp 0</p>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-chair text-cuan-green"></i>
                            Pilih Meja
                        </h3>

                        <div id="tableSelectionGrid"
                            class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-6 max-h-[300px] overflow-y-auto custom-scrollbar">
                            <!-- Tables will be loaded dynamically -->
                        </div>

                        <div id="selectedTableInfo"
                            class="hidden bg-cuan-green/5 border border-cuan-green/20 rounded-xl p-4 mb-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-cuan-green/20 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-chair text-cuan-green"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Meja Dipilih</p>
                                        <p class="font-bold text-gray-900" id="selectedTableNumber">-</p>
                                    </div>
                                </div>
                                <button onclick="clearSelectedTable()" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <button onclick="proceedToPaymentSelection()" id="btnProceedPayment" disabled
                            class="btn-primary w-full opacity-50 cursor-not-allowed">
                            <i class="fas fa-arrow-right mr-2"></i>
                            Lanjut ke Pembayaran
                        </button>

                        <button onclick="skipTableSelection()"
                            class="w-full mt-3 px-4 py-2.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-forward mr-2"></i>
                            Lewati (Tanpa Meja)
                        </button>
                    </div>

                    <!-- Payment Selection View -->
                    <div id="view-select" class="hidden payment-view">
                        <button onclick="backFromPaymentSelection()"
                            class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 text-sm font-medium">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </button>

                        <!-- Service & Table Badge -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <div id="paymentServiceBadge"
                                class="hidden bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-2">
                                <i id="serviceIcon" class="fas fa-shopping-bag text-blue-600"></i>
                                <span class="text-sm font-medium text-blue-800" id="paymentServiceText">Bawa Pulang (Take
                                    Away)</span>
                            </div>
                            <div id="paymentTableBadge"
                                class="hidden bg-cuan-green/5 border border-cuan-green/20 rounded-xl p-3 flex items-center gap-2">
                                <i class="fas fa-chair text-cuan-green"></i>
                                <span class="text-sm font-medium text-cuan-dark">Meja: <span
                                        id="paymentTableNumber">-</span></span>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 border border-gray-200 rounded-2xl p-6 mb-8 flex flex-col items-center justify-center text-center">
                            <p class="text-sm text-gray-500 uppercase tracking-wider mb-1 font-semibold">Total Pembayaran
                            </p>
                            <p class="text-4xl font-extrabold text-gray-900" id="selectTotal">Rp 0</p>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 mb-4">Pilih Metode Pembayaran</h3>
                        <div class="payment-methods">
                            @can('proses pembayaran tunai')
                                <div class="payment-method" onclick="setUIState('cash')">
                                    <div class="payment-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="payment-info">
                                        <div class="payment-title">Tunai</div>
                                        <div class="payment-subtitle">Pembayaran cash</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </div>
                            @endcan

                            @can('proses pembayaran transfer')
                                <div class="payment-method" onclick="setUIState('transfer')">
                                    <div class="payment-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="payment-info">
                                        <div class="payment-title">Card</div>
                                        <div class="payment-subtitle">Debit / Credit / Transfer</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </div>
                            @endcan

                            @can('proses pembayaran digital')
                                <div class="payment-method" @if(config('app.env') === 'production')
                                    onclick="Swal.fire({title: 'Informasi', text: 'Midtrans sedang dalam penanganan (maintenance). Silakan pilih metode pembayaran lain.', icon: 'info', customClass: {popup: 'rounded-3xl'}})"
                                @else onclick="setUIState('midtrans')" @endif>
                                    <div class="payment-icon">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <div class="payment-info">
                                        <div class="payment-title">Midtrans</div>
                                        <div class="payment-subtitle">QRIS, E-Wallet, VA</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </div>
                            @endcan
                        </div>
                    </div>

                    <!-- Cash Payment View -->
                    <div id="view-cash" class="hidden payment-view">
                        <button onclick="setUIState('select')"
                            class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 text-sm">
                            <i class="fas fa-arrow-left"></i> Kembali ke Metode
                        </button>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Pembayaran Tunai</h3>
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                            <p class="text-xs text-gray-600 mb-1">Total:</p>
                            <p class="text-2xl font-bold text-green-600" id="cashTotal">Rp 0</p>
                        </div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Uang:</label>
                        <input type="number" id="cashPaidAmount"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-base font-semibold mb-4 qty-input"
                            placeholder="0" onkeyup="calculateChange()">
                        <div class="p-4 bg-gray-50 rounded-xl mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600" id="changeLabel">Kembalian:</span>
                                <span class="text-lg font-bold text-green-600" id="changeAmount">Rp 0</span>
                            </div>
                        </div>
                        <button id="btn-process-cash" onclick="processCashPayment()" class="btn-primary">
                            <i class="fas fa-check-circle"></i>
                            Proses Pembayaran
                        </button>
                    </div>

                    <!-- Card Payment View -->
                    <div id="view-transfer" class="hidden payment-view">
                        <button onclick="setUIState('select')"
                            class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 text-sm">
                            <i class="fas fa-arrow-left"></i> Kembali ke Metode
                        </button>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Pembayaran Digital / Card</h3>

                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                            <p class="text-xs text-gray-600 mb-1">Total yang harus dibayar:</p>
                            <p class="text-2xl font-bold text-blue-600" id="transferTotal">Rp 0</p>
                        </div>

                        <!-- 1. Selection Grid (Minimalist) -->
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Metode Pembayaran:</label>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-6">
                            @forelse($outletPaymentLinks as $link)
                                <div class="payment-card-option flex flex-col items-center justify-center gap-2 p-3 border border-gray-200 rounded-2xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all bg-white text-center h-24 shadow-sm"
                                    onclick="selectCardOption(this, '{{ $link->id }}', '{{ $link->paymentMethod->name }}', '{{ $link->account_number }}', '{{ $link->account_name }}', '{{ $link->qr_image ? Storage::url($link->qr_image) : '' }}')">

                                    <div class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center">
                                        @if($link->paymentMethod->icon && Storage::disk('public')->exists($link->paymentMethod->icon))
                                            <img src="{{ Storage::url($link->paymentMethod->icon) }}"
                                                class="w-full h-full object-contain filter drop-shadow-sm">
                                        @else
                                            <div
                                                class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                <i class="fas fa-wallet text-sm sm:text-base"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div
                                        class="text-[10px] sm:text-xs font-bold text-gray-700 leading-tight line-clamp-2 w-full">
                                        {{ $link->paymentMethod->name }}</div>
                                </div>
                            @empty
                                <div
                                    class="col-span-full text-center py-8 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                    <i class="fas fa-wallet text-3xl mb-2 opacity-30"></i>
                                    <p class="text-xs">Belum ada metode pembayaran.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- 2. Detail Reveal Area -->
                        <div id="cardPaymentDetails" class="hidden">
                            <div
                                class="bg-white border-2 border-dashed border-blue-200 rounded-xl p-5 mb-5 flex flex-col items-center text-center relative overflow-hidden">

                                <!-- Bank/Method Name -->
                                <div class="mb-4 w-full">
                                    <h4 class="text-base font-bold text-gray-900" id="detailMethodName">-</h4>

                                    <!-- Account Info -->
                                    <div id="detailAccountInfo"
                                        class="mt-2 p-2 bg-gray-50 rounded-lg inline-block min-w-[200px]">
                                        <p class="text-sm font-mono font-bold text-gray-800 tracking-wide"
                                            id="detailAccNumber"></p>
                                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mt-0.5"
                                            id="detailAccName"></p>
                                    </div>
                                </div>

                                <!-- QR Code Area -->
                                <div id="detailQrContainer" class="hidden w-full flex flex-col items-center animate-fadeIn">
                                    <div class="relative group cursor-pointer"
                                        onclick="openQrFullscreen(document.getElementById('detailQrImage').src)">
                                        <div
                                            class="p-3 bg-white rounded-2xl border border-gray-200 shadow-lg mb-2 transition-transform transform group-hover:scale-105">
                                            <img id="detailQrImage" src="" class="w-48 h-48 sm:w-56 sm:h-56 object-contain">
                                        </div>
                                        <!-- Hover Overlay Hint -->
                                        <div
                                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                            <span
                                                class="bg-black bg-opacity-70 text-white px-4 py-1.5 rounded-full text-xs font-semibold backdrop-blur-sm">
                                                <i class="fas fa-expand mr-1"></i> Perbesar
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-blue-600 font-medium bg-blue-50 px-3 py-1.5 rounded-full mt-2 cursor-pointer hover:bg-blue-100 transition-colors"
                                        onclick="openQrFullscreen(document.getElementById('detailQrImage').src)">
                                        <i class="fas fa-search-plus mr-1"></i>Klik gambar untuk memperbesar
                                    </p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="selectedCardId" value="">
                        <input type="hidden" id="selectedCardName" value="">

                        <!-- 3. Action Section -->
                        <div id="cardPaymentActions" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">No. Referensi (Opsional):</label>
                            <input type="text" id="transferReference"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-4 text-sm font-medium"
                                placeholder="Contoh: REF-123456">

                            <button id="btn-process-transfer" onclick="processTransferPayment()"
                                class="btn-primary w-full py-3.5 text-base shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                                <i class="fas fa-check-circle text-lg"></i>
                                <span>Konfirmasi Pembayaran</span>
                            </button>
                        </div>
                    </div>

                    <!-- Midtrans Payment View -->
                    <div id="view-midtrans" class="hidden payment-view">
                        <button onclick="setUIState('select')"
                            class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 text-sm">
                            <i class="fas fa-arrow-left"></i> Kembali ke Metode
                        </button>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Pembayaran via Midtrans</h3>
                        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 mb-4">
                            <p class="text-xs text-gray-600 mb-1">Total:</p>
                            <p class="text-2xl font-bold text-purple-600" id="midtransTotal">Rp 0</p>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Klik tombol di bawah untuk membuka Snap (QRIS / E-Wallet /
                            VA).</p>
                        <button id="btn-process-midtrans" @if(config('app.env') === 'production')
                            onclick="Swal.fire({title: 'Informasi', text: 'Midtrans sedang dalam penanganan (maintenance). Silakan pilih metode pembayaran lain.', icon: 'info', customClass: {popup: 'rounded-3xl'}})"
                        @else onclick="openMidtransPayment()" @endif class="btn-primary">
                            <i class="fas fa-qrcode"></i>
                            Bayar via Midtrans
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Order Summary -->
            <div class="order-panel">
                <div class="order-header flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Ringkasan Pesanan</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Total Item: <span id="totalItems"
                                class="font-semibold text-gray-900">0</span></p>
                    </div>

                    <div class="flex-shrink-0 relative">
                        <!-- Dropdown Button -->
                        <button onclick="togglePOSMenu()"
                            class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-200 transition-all flex items-center gap-1.5">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="posDropdownMenu"
                            class="hidden absolute right-0 top-full mt-2 bg-white rounded-lg shadow-xl border border-gray-200 z-50 min-w-[200px] overflow-hidden">
                            <!-- Buka/Tutup Toko -->
                            @can('tutup kasir')
                                <button onclick="handleCloseCashRegister()" id="menuCloseCashRegister"
                                    class="hidden w-full px-4 py-2.5 text-left text-sm hover:bg-red-50 transition-colors flex items-center gap-2 text-red-600 font-medium border-b border-gray-100">
                                    <i class="fas fa-sign-out-alt w-4"></i>
                                    <span>Tutup Toko</span>
                                </button>
                            @endcan
                            @can('buka kasir')
                                <button onclick="openOpeningAmountModal(); togglePOSMenu();" id="menuOpenCashRegister"
                                    class="hidden w-full px-4 py-2.5 text-left text-sm hover:bg-green-50 transition-colors flex items-center gap-2 text-green-600 font-medium border-b border-gray-100">
                                    <i class="fas fa-door-open w-4"></i>
                                    <span>Buka Toko</span>
                                </button>
                            @endcan

                            <!-- Penjualan Hari Ini -->
                            @can('lihat riwayat kasir')
                                <button onclick="openSalesTodayModal(); togglePOSMenu();"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-indigo-50 transition-colors flex items-center gap-2 text-gray-700 border-b border-gray-100">
                                    <i class="fas fa-chart-line w-4 text-indigo-600"></i>
                                    <span>Penjualan Hari Ini</span>
                                </button>
                            @endcan

                            <!-- Kalkulator -->
                            <button onclick="openCalculator(); togglePOSMenu();"
                                class="w-full px-4 py-2.5 text-left text-sm hover:bg-purple-50 transition-colors flex items-center gap-2 text-gray-700 0 border-b border-gray-100">
                                <i class="fas fa-calculator w-4 text-purple-600"></i>
                                <span>Kalkulator</span>
                            </button>

                            @if(auth()->user()->can('buat pemasukan') && auth()->user()->can('buat pengeluaran'))
                                <button onclick="openFinanceModal(); togglePOSMenu();"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-emerald-50 transition-colors flex items-center gap-2 text-gray-700 border-b border-gray-100">
                                    <i class="fas fa-file-invoice-dollar w-4 text-emerald-600"></i>
                                    <span>Operasional (Kas)</span>
                                </button>
                            @endif

                            <!-- Kelola Meja -->
                            @can('pilih meja pos')
                                <button id="btnManageTables" onclick="openTableManagementModal(); togglePOSMenu();"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-cuan-green/5 transition-colors flex items-center gap-2 text-gray-700 border-b border-gray-100"
                                    style="{{ auth()->user()->outlet->has_table_system ? '' : 'display: none;' }}">
                                    <i class="fas fa-chair w-4 text-cuan-green"></i>
                                    <span>Kelola Meja</span>
                                </button>
                            @endcan

                            @can('atur tampilan produk pos')
                                <button onclick="openProductSettingsModal(); togglePOSMenu();"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-cuan-green/10 transition-colors flex items-center gap-2 text-gray-700 border-b border-gray-100">
                                    <i class="fas fa-cog w-4 text-cuan-green"></i>
                                    <span>Pengaturan</span>
                                </button>
                            @endcan

                            <!-- Penjualan Hari Ini -->
                            <a href="{{ route('dashboard') }}"
                                class="block w-full px-4 py-2.5 text-left text-sm hover:bg-indigo-50 transition-colors flex items-center gap-2 text-gray-700 border-b border-gray-100">
                                <i class="fas fa-list w-4 text-indigo-600"></i>
                                <span>Menu</span>
                            </a>
                        </div>
                    </div>

                </div>

                <div class="order-items custom-scrollbar">
                    <div id="cartItemsPreview">
                        <div class="empty-state" id="emptyCartPreview">
                            <i class="fas fa-shopping-cart"></i>
                            <p>Keranjang kosong</p>
                        </div>
                    </div>
                </div>

                <div class="order-footer">
                    <div class="summary-row">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold text-gray-900" id="summarySubtotal">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-gray-600">Diskon:</span>
                        <span class="font-semibold text-red-600" id="summaryDiscount">- Rp 0</span>
                    </div>
                    <div class="summary-total">
                        <span class="text-gray-900">Total:</span>
                        <span class="text-gray-900" id="summaryGrandTotal">Rp 0</span>
                    </div>

                    <div id="actionsControls" class="mt-4">
                        <button id="btnBayar" onclick="showPaymentSelection()" class="btn-primary">
                            <i class="fas fa-credit-card"></i>
                            <span>Bayar Sekarang</span>
                        </button>
                        @can('batalkan transaksi')
                            <button id="btnClearCart" onclick="clearCart()" class="btn-secondary text-red-600 hover:bg-red-50">
                                <i class="fas fa-trash-alt mr-2"></i>
                                Kosongkan Keranjang
                            </button>
                        @endcan
                    </div>

                    <div id="actionsPayflowSummary" class="hidden mt-4">
                        <div
                            class="w-full px-4 py-3 bg-cuan-green/10 text-cuan-dark rounded-xl text-sm font-semibold flex items-center justify-between">
                            <span id="payflowSummaryLabel">0 item · Rp 0</span>
                            <button class="text-xs underline hover:no-underline" onclick="backToBrowse()">Ubah
                                Pesanan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Scanner -->
        <div id="scannerModal"
            class="hidden fixed inset-0 bg-black bg-opacity-75 z-[60] flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden">
                <div
                    class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4 justify-between items-center bg-gray-50">
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <h3 class="font-bold text-gray-900 hidden sm:block">Scan Barcode</h3>
                    </div>

                    <div class="relative flex-1 max-w-md w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="manualBarcodeInput"
                            class="w-full pl-9 pr-3 py-2 bg-white border border-gray-300 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-cuan-green focus:border-cuan-green transition-all font-mono"
                            placeholder="Cari nama atau scan kode..." autofocus autocomplete="off">
                        <div id="barcodeRecommendations"
                            class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl z-[70] hidden max-h-64 overflow-y-auto overflow-x-hidden">
                        </div>
                    </div>

                    <button onclick="closeScanner()"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-200 transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-0 relative bg-black flex justify-center items-center" style="min-height: 300px;">
                    <div id="reader" class="w-full max-w-md"></div>
                    <!-- Overlay visual for scanning area -->
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                        <div class="w-64 h-64 border-2 border-cuan-green border-dashed rounded-2xl opacity-30"></div>
                        <div
                            class="absolute top-4 left-1/2 -translate-x-1/2 px-3 py-1 bg-black bg-opacity-50 text-white text-[10px] rounded-full">
                            Arahkan kamera ke barcode produk
                        </div>
                    </div>
                </div>

                <div
                    class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center text-[10px] text-gray-500">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Kamera Aktif
                    </div>
                    <span>Mendukung Scan Manual & Bluetooth Scanner</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Input Modal Awal -->
    <div id="openingAmountModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 lg:p-8">
            <div class="text-center mb-5">
                <h3 class="text-2xl font-black text-gray-900 mb-1">Modal Awal</h3>
                <p class="text-gray-500 text-sm font-medium">Masukan saldo kas awal</p>
            </div>
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 mb-5">
                <p class="text-[10px] text-gray-400 text-center uppercase font-bold tracking-widest">
                    REQUIRED FOR CASH FLOW TRACKING
                </p>
            </div>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Modal Awal (Rp):</label>
                <input type="number" id="openingAmountInput"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent text-base font-semibold"
                    placeholder="0" min="0" step="1000" onkeypress="if(event.key==='Enter') submitOpeningAmount()">
                <p class="text-xs text-gray-400 mt-2 italic">
                    Saldo kas untuk keperluan kembalian transaksi tunai
                </p>
            </div>
            <div class="flex gap-2.5">
                <button onclick="skipOpeningAmount()"
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Lewati (Rp 0)
                </button>
                <button onclick="submitOpeningAmount()"
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-cuan-green to-cuan-dark text-white rounded-lg text-sm font-semibold hover:opacity-90 transition-all shadow-md hover:shadow-lg">
                    Simpan & Mulai
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Kalkulator -->
    <div id="calculatorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div
            class="modal-content bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-xl font-black text-gray-900">Kalkulator</h3>
                <button onclick="closeCalculator()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 h-full">
                    <!-- Calculator -->
                    <div class="lg:col-span-3">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <!-- Display -->
                            <div class="bg-white rounded-lg p-4 mb-4 shadow-inner">
                                <div class="text-right text-sm text-gray-500 h-6" id="calcExpression"></div>
                                <div class="text-right text-3xl font-bold text-gray-900 break-all" id="calcDisplay">0</div>
                            </div>

                            <!-- Buttons -->
                            <div class="grid grid-cols-4 gap-2">
                                <button onclick="calcClear()"
                                    class="calc-btn bg-red-500 hover:bg-red-600 text-white col-span-2 font-black">C</button>
                                <button onclick="calcDelete()"
                                    class="calc-btn bg-cuan-green hover:bg-cuan-dark text-white font-black text-xs">DEL</button>
                                <button onclick="calcOperator('/')"
                                    class="calc-btn bg-cuan-green hover:bg-cuan-dark text-white text-xl">÷</button>

                                <button onclick="calcNumber('7')" class="calc-btn">7</button>
                                <button onclick="calcNumber('8')" class="calc-btn">8</button>
                                <button onclick="calcNumber('9')" class="calc-btn">9</button>
                                <button onclick="calcOperator('*')"
                                    class="calc-btn bg-cuan-green hover:bg-cuan-dark text-white text-xl">×</button>

                                <button onclick="calcNumber('4')" class="calc-btn">4</button>
                                <button onclick="calcNumber('5')" class="calc-btn">5</button>
                                <button onclick="calcNumber('6')" class="calc-btn">6</button>
                                <button onclick="calcOperator('-')"
                                    class="calc-btn bg-cuan-green hover:bg-cuan-dark text-white text-xl">−</button>

                                <button onclick="calcNumber('1')" class="calc-btn">1</button>
                                <button onclick="calcNumber('2')" class="calc-btn">2</button>
                                <button onclick="calcNumber('3')" class="calc-btn">3</button>
                                <button onclick="calcOperator('+')"
                                    class="calc-btn bg-cuan-green hover:bg-cuan-dark text-white text-xl">+</button>

                                <button onclick="calcNumber('0')" class="calc-btn col-span-2">0</button>
                                <button onclick="calcDecimal()" class="calc-btn">.</button>
                                <button onclick="calcEquals()"
                                    class="calc-btn bg-cuan-dark hover:bg-cuan-dark/80 text-white font-black text-xl">=</button>
                            </div>
                        </div>
                    </div>

                    <!-- History -->
                    <div class="lg:col-span-2 flex flex-col h-full lg:min-h-[400px]">
                        <div class="flex items-center justify-between mb-3 flex-shrink-0">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-widest">Riwayat</h4>
                            <button onclick="calcClearHistory()"
                                class="text-[10px] text-red-500 hover:text-red-700 font-bold uppercase tracking-wider">
                                Hapus Riwayat
                            </button>
                        </div>
                        <div id="calcHistory" class="flex-1 overflow-y-auto space-y-2 custom-scrollbar pr-1">
                            <div
                                class="text-center text-gray-400 text-sm py-8 border-2 border-dashed border-gray-100 rounded-xl">
                                <i class="fas fa-history text-3xl mb-2 opacity-30"></i>
                                <p class="font-medium">Belum ada riwayat</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Atur Produk -->
    <div id="productSettingsModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div
            class="modal-content bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-xl font-black text-gray-900">Pengaturan POS</h3>
                <button onclick="closeProductSettingsModal()"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                <!-- Toggle: Sembunyikan Stok Habis -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 mb-1">Sembunyikan Stok Habis</div>
                        <div class="text-sm text-gray-600">Produk dengan stok 0 tidak akan ditampilkan</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="hideOutOfStock" class="sr-only peer" onchange="applyProductSettings()">
                        <div
                            class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cuan-green/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green">
                        </div>
                    </label>
                </div>

                <!-- Toggle: Sembunyikan Navbar -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 mb-1">Mode Layar Penuh</div>
                        <div class="text-sm text-gray-600">Sembunyikan navbar untuk area kerja yang lebih luas</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="hideNavbarToggle" class="sr-only peer"
                            onchange="toggleNavbarVisibility(this.checked)">
                        <div
                            class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cuan-green/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green">
                        </div>
                    </label>
                </div>

                <!-- Toggle: Sistem Meja -->
                <div class="flex items-center justify-between p-4 bg-cuan-green/5 rounded-xl border border-cuan-green/20">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 mb-1">Sistem Meja</div>
                        <div class="text-sm text-gray-600">Aktifkan untuk memilih meja sebelum pembayaran</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="tableSystemToggle" class="sr-only peer" {{ auth()->user()->outlet && auth()->user()->outlet->has_table_system ? 'checked' : '' }}
                            onchange="toggleTableSystem(this.checked)">
                        <div
                            class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cuan-green/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green/50">
                        </div>
                    </label>
                </div>

                <!-- Toggle: Produksi Otomatis -->
                <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 mb-1">Produksi Otomatis</div>
                        <div class="text-sm text-gray-600">Otomatis menyelesaikan produksi untuk item non-stok</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="autoProductionToggle" class="sr-only peer" {{ auth()->user()->outlet && auth()->user()->outlet->auto_production ? 'checked' : '' }}
                            onchange="toggleAutoProduction(this.checked)">
                        <div
                            class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500">
                        </div>
                    </label>
                </div>

                <!-- Toggle: Posisi Panel -->
                <div class="flex items-center justify-between p-4 bg-cuan-green/10 rounded-xl border border-cuan-green/20">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 mb-1">Tukar Posisi Panel</div>
                        <div class="text-sm text-gray-600">Pindahkan ringkasan pesanan ke sisi kiri</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="layoutPositionToggle" class="sr-only peer"
                            onchange="togglePanelPosition(this.checked)">
                        <div
                            class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cuan-green/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green">
                        </div>
                    </label>
                </div>

                <!-- Select: Urutkan Produk -->
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <label class="block font-semibold text-gray-900 mb-3">Urutkan Produk</label>
                    <select id="sortProducts"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent text-sm"
                        onchange="applyProductSettings()">
                        <option value="default">Default (ID)</option>
                        <option value="name-asc">Nama (A-Z)</option>
                        <option value="name-desc">Nama (Z-A)</option>
                        <option value="price-asc">Harga Termurah</option>
                        <option value="price-desc">Harga Termahal</option>
                        <option value="discount">Produk Diskon</option>
                    </select>
                </div>

                <!-- Toggle: Sembunyikan Beberapa Produk -->
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-semibold text-gray-900">Sembunyikan Beberapa Produk</div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="enableProductHiding" class="sr-only peer"
                                onchange="toggleProductListVisibility()">
                            <div
                                class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cuan-green/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green">
                            </div>
                        </label>
                    </div>

                    <!-- Product List (Hidden by default) -->
                    <div id="productVisibilityList" class="hidden mt-4 space-y-2 max-h-64 overflow-y-auto custom-scrollbar">
                        @foreach($products as $product)
                            <div
                                class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-cuan-green transition-colors">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $product->code }}</div>
                                    @if($product->track_stock)
                                        @php
                                            $stock = $product->stocks->where('outlet_id', auth()->user()->outlet_id)->first();
                                            $stockQty = $stock ? $stock->quantity : 0;
                                        @endphp
                                        <div class="text-xs {{ $stockQty > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            Stok: {{ number_format($stockQty, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer ml-3">
                                    <input type="checkbox" class="sr-only peer product-visibility-toggle"
                                        data-product-id="{{ $product->id }}" {{ $product->is_active ? 'checked' : '' }}
                                        onchange="toggleProductVisibility({{ $product->id }}, this.checked)">
                                    <div
                                        class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cuan-green/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green">
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 flex-shrink-0">
                <button onclick="resetProductSettings()"
                    class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                    <i class="fas fa-undo mr-2"></i>Reset
                </button>
                <button onclick="closeProductSettingsModal()"
                    class="flex-1 px-4 py-3 bg-gradient-to-r from-cuan-green to-cuan-dark text-white rounded-xl font-semibold hover:opacity-90 transition-all shadow-md">
                    <i class="fas fa-check mr-2"></i>Selesai
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Antrean Dapur (Kitchen Queue) -->
    <div id="pendingProductionModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div
            class="modal-content bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-xl font-black text-gray-900">Antrean Dapur</h3>
                <div class="flex items-center gap-3">
                    <button onclick="refreshPendingProduction()"
                        class="text-cuan-green hover:text-cuan-dark text-sm font-semibold flex items-center gap-1 transition-colors"
                        title="Refresh">
                        <i class="fas fa-sync-alt" id="refreshSpinner"></i>
                    </button>
                    <button onclick="closePendingProductionModal()"
                        class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar" id="pendingProductionContent">
                <!-- Loading state -->
                <div id="pendingProductionLoading" class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-cuan-green/20 border-t-cuan-green mb-4">
                    </div>
                    <p class="text-sm text-gray-500">Memuat antrean dapur...</p>
                </div>

                <!-- Empty state -->
                <div id="pendingProductionEmpty" class="hidden flex flex-col items-center justify-center py-12">
                    <p class="text-lg font-black text-gray-900 uppercase tracking-tighter">Pesanan Selesai</p>
                    <p class="text-sm text-gray-500 mt-1">Tidak ada pesanan yang menunggu produksi</p>
                </div>

                <!-- Sales list -->
                <div id="pendingProductionList" class="hidden space-y-4"></div>
            </div>
        </div>
    </div>

    <!-- Modal: Penjualan Hari Ini -->
    <div id="salesTodayModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div
            class="modal-content bg-white rounded-2xl shadow-2xl max-w-7xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Header - Fixed -->
            <div class="modal-header-fixed flex items-center justify-between p-6">
                <div>
                    <h3 class="text-xl font-black text-gray-900">Penjualan Hari Ini</h3>
                    <p class="text-sm text-gray-600 mt-1" id="salesTodayDate">-</p>
                </div>
                <button onclick="closeSalesTodayModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="modal-scrollable-content">
                <!-- Summary Cards -->
                <div class="summary-cards-container">
                    <!-- Total Transaksi -->
                    <div class="summary-card">
                        <div class="summary-card-label">Total Transaksi</div>
                        <div class="summary-card-value text-gray-900" id="modalTotalTransactions">0</div>
                    </div>

                    <!-- Total Pendapatan -->
                    <div class="summary-card">
                        <div class="summary-card-label">Total Pendapatan</div>
                        <div class="summary-card-value text-cuan-green" id="modalTotalRevenue">Rp 0</div>
                    </div>

                    <!-- Tunai -->
                    <div class="summary-card">
                        <div class="summary-card-label">Tunai</div>
                        <div class="summary-card-value text-gray-700" id="modalCashTotal">Rp 0</div>
                    </div>

                    <!-- Non-Tunai -->
                    <div class="summary-card">
                        <div class="summary-card-label">Non-Tunai</div>
                        <div class="summary-card-value text-gray-700" id="modalNonCashTotal">Rp 0</div>
                    </div>

                    <!-- Total Piutang -->
                    <div class="summary-card">
                        <div class="summary-card-label">Total Piutang</div>
                        <div class="summary-card-value text-red-600" id="modalDebtTotal">Rp 0</div>
                        <div class="summary-card-subvalue">
                            Terbayar: <span id="modalDebtPaid" class="text-cuan-green font-semibold">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="px-6 pb-6">
                    <div class="table-wrapper">
                        <table class="w-full text-sm">
                            <thead class="sticky-header">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">No. Invoice</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Waktu</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Pelanggan</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kasir</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Metode</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Total Diskon</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Total</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="salesTableBody">
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                        <p>Memuat data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer - Fixed -->
            <div class="modal-footer-fixed p-6 flex justify-end gap-3">
                @can('lihat riwayat kasir')
                    <a href="{{ route('sales.index') }}" class="action-btn action-btn-secondary">
                        <i class="fas fa-external-link-alt"></i>
                        Lihat Semua Penjualan
                    </a>
                @endcan
                <button onclick="closeSalesTodayModal()" class="action-btn action-btn-secondary">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Detail Penjualan -->
    <div id="saleDetailModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4">
        <div class="modal-content bg-white rounded-2xl shadow-2xl">
            <!-- Header - Fixed -->
            <div class="modal-header-fixed flex items-center justify-between p-6">
                <div>
                    <h3 class="text-xl font-black text-gray-900">Detail Penjualan</h3>
                    <p class="text-sm text-gray-600 mt-1" id="detailInvoiceNumber">-</p>
                </div>
                <button onclick="closeSaleDetailModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="modal-scrollable-content">
                <div class="p-6 space-y-6">
                    <!-- Info Cards Grid -->
                    <div class="detail-info-grid">
                        <div class="detail-info-card bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200">
                            <div class="text-[10px] text-blue-500 font-bold mb-1 uppercase tracking-widest">KASIR</div>
                            <div class="font-bold text-gray-900" id="detailCashier">-</div>
                        </div>

                        <div class="detail-info-card bg-gradient-to-br from-green-50 to-green-100 border-green-200">
                            <div class="text-[10px] text-green-500 font-bold mb-1 uppercase tracking-widest">PELANGGAN</div>
                            <div class="font-bold text-gray-900" id="detailCustomer">-</div>
                        </div>

                        <div class="detail-info-card bg-gradient-to-br from-purple-50 to-purple-100 border-purple-200">
                            <div class="text-[10px] text-purple-500 font-bold mb-1 uppercase tracking-widest">WAKTU</div>
                            <div class="font-bold text-gray-900" id="detailTime">-</div>
                        </div>

                        <div
                            class="detail-info-card bg-gradient-to-br from-cuan-green/10 to-cuan-green/20 border-cuan-green/20">
                            <div class="text-[10px] text-cuan-green font-bold mb-1 uppercase tracking-widest">PEMBAYARAN
                            </div>
                            <div class="font-bold text-gray-900" id="detailPaymentMethod">-</div>
                        </div>
                    </div>

                    <!-- Structured Notes (New) -->
                    <div id="detailNotesContainer" class="hidden">
                        <h4 class="font-bold text-gray-900 mb-2 uppercase text-[10px] tracking-widest">Catatan & Promo</h4>
                        <div id="detailStructuredNotes" class="space-y-3"></div>
                    </div>

                    <!-- Daftar Item -->
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3 uppercase text-[10px] tracking-widest">Item Pesanan</h4>
                        <div class="items-table-container">
                            <div class="table-wrapper">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left text-gray-600 font-semibold">Produk</th>
                                            <th class="px-4 py-3 text-center text-gray-600 font-semibold">Qty</th>
                                            <th class="px-4 py-3 text-right text-gray-600 font-semibold">Harga</th>
                                            <th class="px-4 py-3 text-right text-gray-600 font-semibold">Diskon</th>
                                            <th class="px-4 py-3 text-right text-gray-600 font-semibold">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailItemsBody">
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="fas fa-spinner fa-spin"></i> Memuat...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Pembayaran -->
                    <div class="payment-summary-box">
                        <h4 class="font-bold text-gray-900 mb-4 uppercase text-[10px] tracking-widest">Ringkasan</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold text-gray-900" id="detailSubtotal">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Pajak</span>
                                <span class="font-semibold text-gray-900" id="detailTax">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Diskon</span>
                                <span class="font-semibold text-red-600" id="detailDiscount">-Rp 0</span>
                            </div>
                            <div class="border-t-2 border-gray-300 pt-3 flex justify-between text-lg">
                                <span class="font-bold text-gray-900">Total Akhir</span>
                                <span class="font-bold text-cuan-green" id="detailGrandTotal">Rp 0</span>
                            </div>
                            <div class="bg-white rounded-lg p-3 space-y-2 border border-gray-200">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Bayar</span>
                                    <span class="font-semibold text-blue-600" id="detailPaid">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Kembalian</span>
                                    <span class="font-semibold text-green-600" id="detailChange">Rp 0</span>
                                </div>
                                <!-- Debt Info (Hidden by default) -->
                                <div id="detailDebtInfo" class="hidden border-t pt-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Sisa Utang</span>
                                        <span class="font-bold text-red-600" id="detailRemainingDebt">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer - Fixed -->
            <div class="modal-footer-fixed p-6 flex justify-end gap-3" id="saleDetailFooter">
                <div id="detailActionButtons" class="flex gap-2"></div>
                <button onclick="closeSaleDetailModal()" class="action-btn action-btn-secondary">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <div id="debtPaymentModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div
            class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
                <div>
                    <h3 class="text-xl font-black text-gray-900">Pembayaran Utang</h3>
                    <p class="text-sm text-gray-600 mt-1">Masukkan informasi pelanggan</p>
                </div>
                <button onclick="closeDebtPaymentModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                <!-- Payment Info Summary -->
                <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-5 rounded-2xl border border-gray-100">
                    <div>
                        <div class="text-[10px] font-bold text-cuan-green uppercase mb-1">Total Belanja</div>
                        <div class="text-xl font-black text-gray-900" id="debtTotalAmount">Rp 0</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-green-600 uppercase mb-1">Dibayar</div>
                        <div class="text-xl font-black text-green-600" id="debtPaidAmount">Rp 0</div>
                    </div>
                    <div class="col-span-2 pt-3 border-t border-cuan-green/20">
                        <div class="text-[10px] font-bold text-red-500 uppercase mb-1">Sisa (Utang)</div>
                        <div class="text-3xl font-black text-red-600" id="debtRemainingAmount">Rp 0</div>
                    </div>
                </div>

                <form id="debtPaymentForm" class="space-y-4">
                    <input type="hidden" id="debtCustomerId" name="customer_id">
                    <input type="hidden" id="debtActualPaidAmount" name="paid_amount">

                    <!-- Customer Search/Name -->
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="debtCustomerName" name="customer_name"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent resize-none"
                            placeholder="Cari atau ketik nama pelanggan..." required autocomplete="off">

                        <!-- Customer Search Results -->
                        <div id="debtCustomerSearchResults"
                            class="hidden absolute z-10 w-full mt-2 bg-white border-2 border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto custom-scrollbar">
                            <!-- Results akan diisi via JavaScript -->
                        </div>
                    </div>

                    <!-- Customer Phone -->
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="debtCustomerPhone" name="customer_phone"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent resize-none"
                            placeholder="Contoh: 08123456789" required autocomplete="off">
                    </div>

                    <!-- Customer Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="debtCustomerEmail" name="customer_email"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent resize-none"
                            placeholder="email@example.com" required>
                    </div>

                    <!-- Customer Address -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Alamat (Opsional)
                        </label>
                        <textarea id="debtCustomerAddress" name="customer_address" rows="2"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent resize-none"
                            placeholder="Alamat lengkap pelanggan..."></textarea>
                    </div>

                    <!-- Customer Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tipe Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" id="debtCustomerTypeHidden" name="customer_type" value="regular">
                        <select id="debtCustomerType"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent bg-gray-100 cursor-not-allowed"
                            disabled required>
                            <option value="regular">Regular</option>
                            <option value="reseller">Reseller</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>

                    <!-- Credit Limit -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Batas Kredit (Opsional)
                        </label>
                        <input type="number" id="debtCreditLimit" name="credit_limit"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent resize-none"
                            placeholder="0" min="0" step="1000">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ada batas kredit</p>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jatuh Tempo (Opsional)
                        </label>
                        <input type="date" id="debtDueDate" name="due_date"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent resize-none"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Catatan (Opsional)
                        </label>
                        <textarea id="debtNotes" name="notes" rows="2"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent resize-none"
                            placeholder="Catatan tambahan..."></textarea>
                    </div>
                </form>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 flex-shrink-0 bg-gray-50">
                <button onclick="closeDebtPaymentModal()"
                    class="flex-1 px-4 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <button onclick="submitDebtPayment()"
                    class="flex-1 px-4 py-3 bg-gradient-to-r from-cuan-green to-cuan-dark text-white rounded-xl font-semibold hover:opacity-90 transition-all shadow-md">
                    <i class="fas fa-check mr-2"></i>Proses Utang
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Kelola Meja -->
    <div id="tableManagementModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div
            class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Header - Fixed -->
            <div class="modal-header-fixed flex items-center justify-between p-6">
                <div>
                    <h3 class="text-xl font-black text-gray-900">Status Meja</h3>
                    <p class="text-sm text-gray-600 mt-1">Pantau dan ubah status ketersediaan meja outlet</p>
                </div>
                <button onclick="closeTableManagementModal()"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="modal-scrollable-content">
                <!-- Stats -->
                <div class="summary-cards-container">
                    <div class="summary-card">
                        <div class="summary-card-label">Total Meja</div>
                        <div class="summary-card-value text-gray-900" id="tmTotalTables">0</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-label">Tersedia</div>
                        <div class="summary-card-value text-cuan-green" id="tmAvailableTables">0</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-label">Terisi</div>
                        <div class="summary-card-value text-red-600" id="tmOccupiedTables">0</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-label">Dipesan</div>
                        <div class="summary-card-value text-orange-500" id="tmReservedTables">0</div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Tables Grid -->
                    <div id="tableManagementGrid"
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <!-- Tables will be loaded dynamically -->
                    </div>

                    <!-- Empty State -->
                    <div id="tableManagementEmpty"
                        class="hidden text-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 mx-auto max-w-2xl">
                        <h4 class="text-gray-900 font-black text-lg mb-1 uppercase tracking-tight">Belum Ada Meja</h4>
                        <p class="text-gray-500 text-sm mb-6">Silakan tambahkan data meja melalui menu pengaturan meja</p>
                        <a href="{{ route('tables.create') }}"
                            class="inline-flex items-center gap-2 px-8 py-3 bg-cuan-green text-white rounded-full font-bold text-sm hover:bg-cuan-dark transition-all shadow-lg hover:shadow-emerald-200 active:scale-95">
                            <i class="fas fa-plus"></i>
                            Tambah Meja Sekarang
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer - Fixed -->
            <div class="modal-footer-fixed p-6 flex justify-end gap-3">
                <a href="{{ route('tables.index') }}"
                    class="action-btn action-btn-primary !bg-cuan-green hover:!bg-cuan-dark">
                    <i class="fas fa-external-link-alt"></i>
                    Pengaturan Meja Lengkap
                </a>
                <button onclick="closeTableManagementModal()" class="action-btn action-btn-secondary">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Operasional (Pemasukan/Pengeluaran) -->
    <div id="financeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div
            class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
                <div>
                    <h3 class="text-xl font-black text-gray-900">Operasional (Kas)</h3>
                    <p class="text-xs text-gray-500 mt-1">Catat pemasukan atau pengeluaran non-penjualan</p>
                </div>
                <button onclick="closeFinanceModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-100 bg-gray-50/50">
                <button onclick="switchFinanceTab('income')" id="tab-income"
                    class="flex-1 py-4 text-sm font-bold border-b-2 transition-all flex items-center justify-center gap-2 border-emerald-600 text-emerald-600 bg-white">
                    Pemasukan
                </button>
                <button onclick="switchFinanceTab('expense')" id="tab-expense"
                    class="flex-1 py-4 text-sm font-bold border-b-2 transition-all flex items-center justify-center gap-2 border-transparent text-gray-500 hover:text-red-600 hover:bg-red-50/30">
                    Pengeluaran
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                <!-- Pemasukan Form -->
                <form id="incomeForm" class="space-y-4">
                    @csrf
                    <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100 mb-6">
                        <label class="block text-xs font-bold text-emerald-700 uppercase mb-2">Jumlah Pemasukan (Rp)</label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-4 flex items-center text-emerald-500 font-bold">Rp</span>
                            <input type="number" name="amount" required step="0.01" min="0.01"
                                class="w-full pl-12 pr-4 py-3 bg-white border-2 border-emerald-200 rounded-xl text-2xl font-black text-emerald-600 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 placeholder-emerald-100"
                                placeholder="0">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi / Judul <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="description" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="Contoh: Dana awal, Jual aset">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Metode <span
                                    class="text-red-500">*</span></label>
                            <select name="payment_method" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="cash">Tunai (Cash)</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="card">Kartu Debit/Kredit</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="income_date" value="{{ date('Y-m-d') }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">No. Referensi (Opsional)</label>
                            <input type="text" name="reference_number"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="No. Resi / Bukti">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan Tambahan</label>
                        <textarea name="notes" rows="2"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 text-sm resize-none"
                            placeholder="Detail tambahan..."></textarea>
                    </div>
                </form>

                <!-- Pengeluaran Form -->
                <form id="expenseForm" class="hidden space-y-4" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-red-50 p-4 rounded-xl border border-red-100 mb-6">
                        <label class="block text-xs font-bold text-red-700 uppercase mb-2">Jumlah Biaya (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-red-500 font-bold">Rp</span>
                            <input type="number" name="amount" required step="0.01" min="0.01"
                                class="w-full pl-12 pr-4 py-3 bg-white border-2 border-red-200 rounded-xl text-2xl font-black text-red-600 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 placeholder-red-100"
                                placeholder="0">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori <span
                                    class="text-red-500">*</span></label>
                            <select name="expense_category_id" id="finance_expense_category" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 text-sm">
                                <option value="" disabled selected>Pilih Kategori</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Metode <span
                                    class="text-red-500">*</span></label>
                            <select name="payment_method" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 text-sm">
                                <option value="cash">Tunai (Kas Toko)</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="card">Kartu Debit/Kredit</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="description" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 text-sm"
                                placeholder="Contoh: Belanja bahan, Listrik">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bukti (Optional)</label>
                            <input type="file" name="receipt_image" accept="image/*"
                                class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan Internal</label>
                        <textarea name="notes" rows="2"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 text-sm resize-none"
                            placeholder="Keterangan tambahan..."></textarea>
                    </div>
                </form>
            </div>

            <div class="p-6 border-t border-gray-200 flex gap-3 flex-shrink-0">
                <button onclick="closeFinanceModal()"
                    class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button id="btnSubmitFinance" onclick="submitFinanceForm()"
                    class="flex-1 px-4 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:opacity-90 transition-all shadow-md flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Simpan Pemasukan</span>
                </button>
            </div>
        </div>
    </div>

    </div>

    <!-- Modal: Product Details (Qty & Notes) -->
    <div id="productDetailModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4">
        <div
            class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col animate-scaleIn">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <div>
                    <h3 id="detailModalProductName" class="text-xl font-black text-gray-900 uppercase tracking-tighter">
                        Product Name</h3>
                    <p id="detailModalProductPrice" class="text-cuan-green font-black mt-1">Rp 0</p>
                </div>
                <button onclick="closeProductDetailModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-6 overflow-y-auto">
                <!-- Quantity Control -->
                <div>
                    <label
                        class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-3 text-center">Jumlah</label>
                    <div class="flex items-center justify-center gap-4">
                        <button onclick="detailModalDecrement()"
                            class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-100 active:scale-95 transition-all border border-gray-100">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="detailModalQty" value="1" min="1"
                            class="w-24 text-center text-3xl font-black text-gray-900 border-none focus:ring-0 bg-transparent">
                        <button onclick="detailModalIncrement()"
                            class="w-12 h-12 rounded-xl bg-cuan-green/10 flex items-center justify-center text-cuan-green hover:bg-cuan-green/20 active:scale-95 transition-all">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Attributed To -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Atas
                            Nama</label>
                        <div class="relative">
                            <input type="text" id="detailModalAttributedTo"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent text-sm font-bold"
                                placeholder="Nama pembeli / Pesanan untuk...">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Catatan
                            Pesanan</label>
                        <textarea id="detailModalNotes" rows="3"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent text-sm font-medium resize-none"
                            placeholder="Contoh: Kurangi gula, Tidak pakai es, dll..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-gray-100 flex gap-3">
                <button onclick="closeProductDetailModal()"
                    class="flex-1 px-4 py-3 border border-gray-200 text-gray-500 rounded-xl font-bold hover:bg-gray-50 transition-all text-xs uppercase tracking-widest">
                    Batal
                </button>
                <button id="detailModalSubmitBtn" onclick="submitProductDetail()"
                    class="flex-[2] px-4 py-3 bg-gradient-to-r from-cuan-green to-cuan-dark text-white rounded-xl font-black hover:opacity-90 shadow-lg shadow-emerald-100 transition-all active:scale-95 text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                    <span id="detailModalSubmitText">Tambah ke Keranjang</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: QR Fullscreen -->
    <div id="qrFullscreenModal"
        class="hidden fixed inset-0 bg-black bg-opacity-90 z-[70] flex items-center justify-center p-4 backdrop-blur-sm transition-opacity duration-300"
        onclick="if(event.target === this) closeQrFullscreen()">
        <div class="relative w-full max-w-lg h-auto flex flex-col items-center justify-center animate-scaleIn">
            <button onclick="closeQrFullscreen()"
                class="absolute -top-12 right-0 sm:-right-8 text-white hover:text-gray-300 transition-colors p-2 bg-white bg-opacity-10 rounded-full w-10 h-10 flex items-center justify-center">
                <i class="fas fa-times text-xl"></i>
            </button>
            <div class="bg-white p-3 rounded-2xl shadow-2xl">
                <img id="qrFullscreenImage" src="" class="w-full h-auto max-h-[70vh] object-contain rounded-lg">
            </div>
            <p class="text-white text-sm mt-5 font-medium bg-white bg-opacity-10 px-4 py-2 rounded-full backdrop-blur-md">
                <i class="fas fa-qrcode mr-2"></i>Scan QR Code untuk membayar
            </p>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        window.permissions = {
            cetakStruk: @json(auth()->user()->can('cetak struk')),
            unduhStruk: @json(auth()->user()->can('unduh struk')),
            cetakStrukPenjualan: @json(auth()->user()->can('cetak struk penjualan')),
            unduhStrukPenjualan: @json(auth()->user()->can('unduh struk penjualan')),
            refundPenjualan: @json(auth()->user()->can('refund penjualan')),
            lihatDetailPenjualan: @json(auth()->user()->can('lihat detail penjualan'))
        };
    </script>
    <script>
        // ==================== GLOBAL VARIABLES ====================
        let UI_STATE = 'browse';
        let cart = @json($cart ?? []);
        let cartSummary = @json($cartSummary) || { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0, total_items: 0 };
        let categories = @json($categories ?? []);
        let activeDiscountPlan = @json($activeDiscountPlan ?? null);
        let availableDiscounts = [];
        let calcCurrentValue = '0';
        let calcPreviousValue = '';
        let calcOperation = null;
        let calcHistory = [];
        let currentSaleId = null;

        let debounceTimer = null;
        let selectedCustomerData = null;
        let debtPaymentData = {
            grandTotal: 0,
            paidAmount: 0,
            remainingAmount: 0
        };
        let currentCustomer = @json($selectedCustomer);
        let customerSearchTimeout = null;

        document.addEventListener('DOMContentLoaded', async function () {
            try {
                // Run all initialization tasks
                loadCalcHistory();
                loadProductSettings();

                // Wait for all async tasks
                await Promise.all([
                    checkCashRegister(),
                    loadAvailableDiscounts()
                ]);

                // Final UI renders
                renderCart();
                setUIState('browse');

                if (productSettings.hideOutOfStock || productSettings.sortBy !== 'default' || productSettings.hiddenProducts.length > 0) {
                    applyProductSettings();
                }

                renderCategoryTabs();
                renderCategoryDropdown();
                initCategoryHandlers();
                initClickOutsideHandler();
                initDiscountUI();
                initCustomerSearch(); // Moved from later in the file

            } catch (error) {
                console.error('POS Initialization error:', error);
            } finally {
                // Hide initialization loader
                const loader = document.getElementById('pos-init-loader');
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 300);
                }
            }
        });

        // ==================== CATEGORY FUNCTIONS ====================
        function renderCategoryTabs() {
            const tabsContainer = document.getElementById('categoryTabs');
            if (!tabsContainer) return;

            let html = `<button class="category-tab active" data-category="">
            <i class="fas fa-th-large mr-1.5"></i>Semua
        </button>`;

            categories.forEach(cat => {
                const icon = cat.icon || 'fa-folder';
                html += `<button class="category-tab" data-category="${cat.id}">
                <i class="fas ${icon} mr-1.5"></i>${cat.name}
            </button>`;
            });

            tabsContainer.innerHTML = html;
        }

        function renderCategoryDropdown() {
            const dropdown = document.getElementById('filterCategory');
            if (!dropdown) return;

            let html = '<option value="">Semua Kategori</option>';
            categories.forEach(cat => {
                html += `<option value="${cat.id}">${cat.name}</option>`;
            });

            dropdown.innerHTML = html;
        }

        function initCategoryHandlers() {
            // Klik tab kategori
            document.addEventListener('click', function (e) {
                const tab = e.target.closest('.category-tab');
                if (!tab) return;

                document.querySelectorAll('.category-tab').forEach(t => {
                    t.classList.remove('active');
                });
                tab.classList.add('active');

                const categoryId = tab.dataset.category;
                const searchTerm = document.getElementById('searchProduct')?.value.toLowerCase() || '';

                const dropdown = document.getElementById('filterCategory');
                if (dropdown) dropdown.value = categoryId;

                filterProducts(searchTerm, categoryId);
            });

            // Dropdown kategori
            const dropdown = document.getElementById('filterCategory');
            if (dropdown) {
                dropdown.addEventListener('change', function () {
                    const categoryId = this.value;
                    const searchTerm = document.getElementById('searchProduct')?.value.toLowerCase() || '';

                    document.querySelectorAll('.category-tab').forEach(tab => {
                        if (tab.dataset.category === categoryId) {
                            tab.classList.add('active');
                        } else {
                            tab.classList.remove('active');
                        }
                    });

                    filterProducts(searchTerm, categoryId);
                });
            }

            // Pencarian
            const searchInput = document.getElementById('searchProduct');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase();
                    const categoryId = document.getElementById('filterCategory')?.value || '';
                    filterProducts(searchTerm, categoryId);
                });
            }
        }

        function filterProducts(searchTerm, categoryId) {
            const productCards = document.querySelectorAll('.product-card');
            const productGrid = document.getElementById('productGrid');
            let visibleCount = 0;

            productCards.forEach(card => {
                const productName = (card.dataset.productName || '').toLowerCase();
                const productCode = (card.dataset.productCode || '').toLowerCase();
                const productCategory = card.dataset.category || '';

                const matchesSearch = !searchTerm ||
                    productName.includes(searchTerm) ||
                    productCode.includes(searchTerm);

                const matchesCategory = !categoryId || productCategory == categoryId;

                if (matchesSearch && matchesCategory) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            updateEmptyState(productGrid, visibleCount, searchTerm, categoryId);
        }

        function updateEmptyState(productGrid, visibleCount, searchTerm, categoryId) {
            const existingEmpty = productGrid.querySelector('.empty-state-filter');
            if (existingEmpty) existingEmpty.remove();

            if (visibleCount === 0) {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'empty-state empty-state-filter';
                emptyDiv.style.gridColumn = '1/-1';

                let message = 'Produk tidak ditemukan';
                if (searchTerm && categoryId) {
                    const categoryName = getCategoryName(categoryId);
                    message = `Tidak ada produk "${searchTerm}" di kategori ${categoryName}`;
                } else if (searchTerm) {
                    message = `Tidak ada produk dengan kata kunci "${searchTerm}"`;
                } else if (categoryId) {
                    const categoryName = getCategoryName(categoryId);
                    message = `Tidak ada produk di kategori ${categoryName}`;
                }

                emptyDiv.innerHTML = `
                <i class="fas fa-search"></i>
                <p>${message}</p>
                ${(searchTerm || categoryId) ? `
                    <button onclick="clearFilters()" class="mt-3 px-4 py-2 bg-indigo-500 text-white rounded-lg text-sm hover:bg-indigo-600 transition-colors">
                        Hapus Filter
                    </button>
                ` : ''}
            `;
                productGrid.appendChild(emptyDiv);
            }
        }

        function getCategoryName(categoryId) {
            const category = categories.find(cat => cat.id == categoryId);
            return category ? category.name : 'Kategori';
        }

        function clearFilters() {
            const searchInput = document.getElementById('searchProduct');
            if (searchInput) searchInput.value = '';

            const dropdown = document.getElementById('filterCategory');
            if (dropdown) dropdown.value = '';

            document.querySelectorAll('.category-tab').forEach(tab => {
                if (tab.dataset.category === '') {
                    tab.classList.add('active');
                } else {
                    tab.classList.remove('active');
                }
            });

            filterProducts('', '');
            showToast('info', 'Filter dihapus');
        }

        // ==================== DISCOUNT FUNCTIONS ====================
        function loadAvailableDiscounts() {
            return fetch("{{ route('pos.discounts.available') }}")
                .then(async (r) => {
                    if (!r.ok) {
                        throw new Error('HTTP ' + r.status);
                    }
                    return r.json();
                })
                .then((data) => {
                    if (data.success) {
                        // Filter hanya non-voucher untuk badge
                        availableDiscounts = (data.discounts || []).filter(d => !d.is_voucher);
                        renderDiscountBadges();
                    }
                })
                .catch(err => {
                    console.error('Failed to load discounts:', err);
                });
        }


        function renderDiscountBadges() {
            document.querySelectorAll('.product-card').forEach(card => {
                const productId = parseInt(card.dataset.productId);
                const categoryId = parseInt(card.dataset.category);

                const discounts = availableDiscounts.filter(d => {
                    if (!d.can_apply) return false;
                    if (d.product_id && d.product_id === productId) return true;
                    if (d.category_id && d.category_id === categoryId) return true;
                    if (!d.product_id && !d.category_id) return true;
                    return false;
                });

                if (discounts.length > 0) {
                    const discount = discounts[0];
                    const badge = createDiscountBadge(discount);

                    const existing = card.querySelector('.discount-badge');
                    if (existing) existing.remove();

                    card.appendChild(badge);
                }
            });
        }

        function createDiscountBadge(discount) {
            const badge = document.createElement('div');
            badge.className = 'discount-badge';

            let text = '';
            let color = 'bg-red-500';

            switch (discount.type) {
                case 'percentage':
                    text = `-${formatNumber(discount.value)}%`;
                    break;
                case 'fixed':
                    text = `-Rp ${formatNumber(discount.value)}`;
                    color = 'bg-cuan-green/100';
                    break;
                case 'buy_x_get_y':
                    text = `Beli ${discount.buy_quantity} Gratis ${discount.get_quantity}`;
                    color = 'bg-green-500';
                    break;
            }

            badge.innerHTML = `
            <div class="${color} text-white px-2 py-1 rounded-md text-xs font-bold shadow-md">
                ${text}
            </div>
        `;

            return badge;
        }

        function applyDiscount(discountCode = null) {
            const payload = discountCode ? { discount_code: discountCode } : {};

            fetch('/pos/discounts/apply', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        activeDiscountPlan = data.discount_plan;
                        cartSummary = data.cart_summary;

                        renderCart();

                        if (data.discount_plan.requires_free_item_selection) {
                            showFreeItemSelectionModal(data.discount_plan);
                        } else {
                            showToast('success', `Diskon ${data.discount_plan.discount_name} diterapkan`);
                        }
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(() => showToast('error', 'Gagal menerapkan diskon'));
        }

        // ====== FREE ITEM (BOGO) MODAL ======
        function showFreeItemSelectionModal(discountPlan) {
            if (!discountPlan) {
                console.warn('showFreeItemSelectionModal: discountPlan is null');
                return;
            }

            // Pastikan ini adalah diskon BOGO
            if (discountPlan.discount_type !== 'buy_x_get_y') {
                console.warn('showFreeItemSelectionModal: not a BOGO discount');
                return;
            }

            // Pastikan ada kuota dan kandidat
            const quota = discountPlan.free_item_quota || 0;
            const candidates = discountPlan.free_item_candidates || [];

            if (quota <= 0) {
                showToast('warning', 'Tidak ada kuota item gratis yang tersedia');
                return;
            }

            if (candidates.length === 0) {
                showToast('warning', 'Tidak ada item yang memenuhi syarat untuk hadiah gratis');
                return;
            }

            try {
                const modal = document.getElementById('freeItemModal') || createFreeItemModal();
                activeDiscountPlan = discountPlan;

                const quotaElement = document.getElementById('freeItemQuota');
                const remainingElement = document.getElementById('freeItemsRemaining');

                if (quotaElement) quotaElement.textContent = quota;
                if (remainingElement) remainingElement.textContent = quota;

                const candidatesList = document.getElementById('freeItemCandidates');
                if (candidatesList) {
                    candidatesList.innerHTML = '';

                    candidates.forEach(candidate => {
                        // Validasi data kandidat
                        if (!candidate.product_id || !candidate.product_name) {
                            console.warn('Invalid candidate data:', candidate);
                            return;
                        }

                        const stockLimit = (typeof candidate.available_stock !== 'undefined') ? candidate.available_stock : 999999;

                        // Max quantity for this item is limited by: 
                        // 1. How many parent items bought (max_free_qty)
                        // 2. Global quota (though this is dynamic, we clamp it here too for safety)
                        // 3. Available stock
                        const maxFreeQty = Math.min(candidate.max_free_qty || 0, quota, stockLimit);

                        const isOutOfStock = stockLimit <= 0;

                        const item = document.createElement('div');
                        item.className = 'free-item-candidate';
                        item.innerHTML = `
                        <div class="flex items-center justify-between p-3 border ${isOutOfStock ? 'border-red-200 bg-red-50' : 'border-gray-200 hover:bg-cuan-green/10'} rounded-xl transition-all duration-200">
                            <div class="flex-1">
                                <div class="font-bold text-gray-900">${escapeHtml(candidate.product_name)}</div>
                                <div class="text-sm text-gray-600">Rp ${formatNumber(candidate.unit_price || 0)}</div>
                                <div class="flex gap-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        Maks. per item: ${candidate.max_free_qty || 0}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${isOutOfStock ? 'bg-red-200 text-red-800' : 'bg-green-100 text-green-800'}">
                                        Stok tersedia: ${stockLimit}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
                                <button type="button" 
                                        class="w-8 h-8 flex items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-100 transition-colors ${isOutOfStock ? 'opacity-50 cursor-not-allowed' : ''}" 
                                        ${isOutOfStock ? 'disabled' : ''}
                                        onclick="adjustFreeQty(${candidate.product_id}, -1)">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <input type="number" 
                                       min="0" 
                                       max="${maxFreeQty}"
                                       value="0"
                                       readonly
                                       class="w-10 text-center bg-transparent font-bold text-gray-900 border-none focus:ring-0 p-0"
                                       id="freeQty_${candidate.product_id}"
                                       data-available-stock="${stockLimit}">
                                <button type="button" 
                                        class="w-8 h-8 flex items-center justify-center rounded-md bg-cuan-green text-white hover:bg-cuan-dark transition-colors shadow-sm ${isOutOfStock ? 'opacity-50 cursor-not-allowed' : ''}" 
                                        ${isOutOfStock ? 'disabled' : ''}
                                        onclick="adjustFreeQty(${candidate.product_id}, 1)">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    `;
                        candidatesList.appendChild(item);
                    });
                }

                modal.classList.remove('hidden');

                // Fokus ke modal untuk aksesibilitas
                modal.focus();

            } catch (error) {
                console.error('Error showing free item modal:', error);
                showToast('error', 'Gagal membuka modal pemilihan item gratis');
            }
        }

        // Helper function untuk escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function createFreeItemModal() {
            const modal = document.createElement('div');
            modal.id = 'freeItemModal';
            modal.className = 'hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
            modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-hidden flex flex-col">
                <div class="flex items-center justify-between mb-4 flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-gift text-cuan-green mr-2"></i>
                        Pilih Item Gratis
                    </h3>
                    <button onclick="closeFreeItemModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <div class="bg-cuan-green/10 border border-cuan-green/20 rounded-xl p-3 mb-4 flex-shrink-0">
                    <p class="text-sm text-cuan-dark">
                        <i class="fas fa-info-circle mr-2"></i>
                        Anda berhak mendapat <span id="freeItemQuota" class="font-bold">0</span> item gratis!
                        Sisa: <span id="freeItemsRemaining" class="font-bold">0</span>
                    </p>
                </div>

                <div id="freeItemCandidates" class="flex-1 overflow-y-auto space-y-2 custom-scrollbar mb-4">
                </div>

                <div class="flex gap-3 flex-shrink-0">
                    <button onclick="closeFreeItemModal()" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button onclick="confirmFreeItems()" class="flex-1 px-4 py-3 bg-gradient-to-r from-cuan-green to-cuan-dark text-white rounded-xl font-semibold hover:opacity-90 transition-all shadow-md">
                        <i class="fas fa-check mr-2"></i>
                        Konfirmasi
                    </button>
                </div>
            </div>
        `;
            document.body.appendChild(modal);
            return modal;
        }

        function adjustFreeQty(productId, delta) {
            if (!activeDiscountPlan) return;
            const input = document.getElementById(`freeQty_${productId}`);
            if (!input) return;

            const quota = activeDiscountPlan.free_item_quota || 0;
            const max = parseInt(input.max) || 0;

            // Hitung total yang sudah digunakan (tidak termasuk item saat ini)
            let currentUsed = 0;
            document.querySelectorAll('[id^="freeQty_"]').forEach(inp => {
                if (inp.id !== `freeQty_${productId}`) {
                    currentUsed += parseInt(inp.value || '0') || 0;
                }
            });

            let currentVal = parseInt(input.value || '0') || 0;
            let newVal = currentVal + delta;

            // PERBAIKAN: Validasi ketat untuk batas kuota global
            if (delta > 0) {
                const totalAfterChange = currentUsed + newVal;
                if (totalAfterChange > quota) {
                    const maxAllowed = quota - currentUsed;
                    if (maxAllowed <= 0) {
                        showToast('warning', `Kuota gratis sudah habis (${quota} item)`);
                        return;
                    }
                    newVal = maxAllowed;
                    showToast('info', `Dibatasi ke ${maxAllowed} item (sisa kuota)`);
                }
            }

            // Batasi berdasarkan max per item dan minimum 0
            newVal = Math.max(0, Math.min(max, newVal));

            input.value = newVal;
            updateFreeItemsRemaining();
        }

        function updateFreeItemsRemaining() {
            if (!activeDiscountPlan) return;
            const quota = activeDiscountPlan.free_item_quota;
            let used = 0;

            document.querySelectorAll('[id^="freeQty_"]').forEach(input => {
                used += parseInt(input.value || '0') || 0;
            });

            const remaining = Math.max(0, quota - used);
            document.getElementById('freeItemsRemaining').textContent = remaining;

            if (used > quota) {
                showToast('warning', 'Jumlah melebihi kuota gratis!');
            }
        }

        function confirmFreeItems() {
            if (!activeDiscountPlan) {
                showToast('error', 'Tidak ada diskon BOGO aktif');
                return;
            }

            const freeItems = [];
            const eligibleProductIds = (activeDiscountPlan.free_item_candidates || []).map(c => c.product_id);

            document.querySelectorAll('[id^="freeQty_"]').forEach(input => {
                const qty = parseInt(input.value || '0') || 0;
                if (qty > 0) {
                    const productId = parseInt(input.id.replace('freeQty_', ''));

                    // PERBAIKAN: Validasi keamanan - pastikan item eligible
                    if (!eligibleProductIds.includes(productId)) {
                        showToast('error', 'Item tidak memenuhi syarat untuk hadiah gratis');
                        return;
                    }

                    // PERBAIKAN: Validasi max per item
                    const maxQty = parseInt(input.max) || 0;
                    if (qty > maxQty) {
                        showToast('error', `Jumlah melebihi batas maksimal (${maxQty})`);
                        return;
                    }

                    // Validasi Stok (double check frontend)
                    const availableStock = parseInt(input.dataset.availableStock);
                    if (!isNaN(availableStock) && qty > availableStock) {
                        showToast('error', `Stok tidak mencukupi. Tersedia: ${availableStock}`);
                        return;
                    }

                    freeItems.push({
                        product_id: productId,
                        quantity: qty
                    });
                }
            });

            if (freeItems.length === 0) {
                showToast('warning', 'Pilih minimal 1 item gratis');
                return;
            }

            const totalSelected = freeItems.reduce((acc, item) => acc + item.quantity, 0);
            const quota = activeDiscountPlan.free_item_quota || 0;

            if (totalSelected > quota) {
                showToast('error', `Jumlah item (${totalSelected}) melebihi kuota gratis (${quota})!`);
                return;
            }

            // Tampilkan loading
            const confirmBtn = document.querySelector('#freeItemModal button[onclick="confirmFreeItems()"]');
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            }

            fetch('/pos/discounts/assign-free-items', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ free_items: freeItems })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        activeDiscountPlan = data.discount_plan;
                        cartSummary = data.cart_summary;

                        closeFreeItemModal();
                        renderCart();

                        showToast('success', 'Item gratis berhasil diterapkan!');
                    } else {
                        showToast('error', data.message || 'Gagal memproses item gratis');
                    }
                })
                .catch((err) => {
                    console.error('Error confirming free items:', err);
                    showToast('error', 'Gagal memproses item gratis');
                })
                .finally(() => {
                    // Reset button state
                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Konfirmasi';
                    }
                });
        }

        function closeFreeItemModal() {
            const modal = document.getElementById('freeItemModal');
            if (modal) modal.classList.add('hidden');
        }

        // ==================== DISCOUNT HUB ====================
        function toggleDiscountList() {
            const list = document.getElementById('discountList');
            const icon = document.getElementById('discountToggleIcon');
            if (list) {
                list.classList.toggle('open');
                if (icon) {
                    icon.classList.toggle('rotate-180');
                }
            }
        }

        function removeAppliedDiscount(id) {
            fetch('{{ route("pos.discounts.remove") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        activeDiscountPlan = data.discount_plan;
                        cartSummary = data.cart_summary;
                        renderCart();
                        showToast('success', 'Diskon dihapus');
                    } else {
                        showToast('error', data.message || 'Gagal menghapus diskon');
                    }
                })
                .catch(() => showToast('error', 'Terjadi kesalahan saat menghapus diskon'));
        }

        function clearDiscount(confirm = true) {
            if (!activeDiscountPlan) return;

            const performClear = () => {
                fetch('/pos/discounts/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            activeDiscountPlan = null;
                            cartSummary = data.cart_summary;
                            renderCart();
                            showToast('success', 'Diskon dihapus');
                        }
                    })
                    .catch(() => showToast('error', 'Gagal menghapus diskon'));
            };

            if (confirm) {
                Swal.fire({
                    title: 'Hapus Diskon?',
                    text: "Diskon yang diterapkan akan dihapus",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#658C58',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    iconColor: '#658C58',
                    customClass: {
                        popup: 'rounded-[1.5rem] border-none shadow-2xl',
                        title: 'font-black text-gray-900',
                    },
                    backdrop: 'rgba(0, 0, 0, 0.5)'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performClear();
                    }
                });
            } else {
                performClear();
            }
        }

        function initDiscountUI() {
            const actions = document.getElementById('actionsControls');
            if (!actions || document.getElementById('btnDiscountCode')) return;
            const btn = document.createElement('button');
            btn.id = 'btnDiscountCode';
            btn.className = 'btn-secondary mb-2';
            btn.innerHTML = '<i class="fas fa-ticket-alt mr-2"></i>Gunakan Voucher';
            btn.onclick = showDiscountCodeModal;

            actions.insertBefore(btn, actions.firstChild);
        }

        function showDiscountCodeModal() {
            const modal = document.getElementById('discountCodeModal') || createDiscountCodeModal();
            modal.classList.remove('hidden');
            setTimeout(() => document.getElementById('discountCodeInput')?.focus(), 200);
        }

        function createDiscountCodeModal() {
            const modal = document.createElement('div');
            modal.id = 'discountCodeModal';
            modal.className = 'hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
            modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-cuan-green to-cuan-dark rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-ticket-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Masukkan Kode Voucher</h3> <!-- ⬅️ Ubah -->
                </div>
                <div class="mb-4">
                    <input type="text" 
                        id="discountCodeInput" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-cuan-green focus:border-transparent text-center text-lg font-semibold uppercase"
                        placeholder="KODE VOUCHER" 
                        onkeypress="if(event.key==='Enter') submitDiscountCode()">
                </div>

                <div class="flex gap-3">
                    <button onclick="closeDiscountCodeModal()" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50">
                        Batal
                    </button>
                    <button onclick="submitDiscountCode()" class="flex-1 px-4 py-3 bg-gradient-to-r from-cuan-green to-cuan-dark text-white rounded-lg font-semibold hover:opacity-90">
                        Terapkan
                    </button>
                </div>
            </div>
        `;
            document.body.appendChild(modal);
            return modal;
        }

        function submitDiscountCode() {
            const input = document.getElementById('discountCodeInput');
            const code = input?.value.trim().toUpperCase();
            if (!code) {
                showToast('warning', 'Masukkan kode voucher');
                return;
            }

            // Langsung fetch tanpa bikin fungsi baru
            fetch('/pos/discounts/apply', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ discount_code: code })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        activeDiscountPlan = data.discount_plan;
                        cartSummary = data.cart_summary;

                        renderCart();

                        if (data.discount_plan.requires_free_item_selection) {
                            showFreeItemSelectionModal(data.discount_plan);
                        } else {
                            showToast('success', `Voucher ${data.discount_plan.discount_name} diterapkan`);
                        }

                        closeDiscountCodeModal();
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(() => showToast('error', 'Gagal menerapkan voucher'));
        }

        function closeDiscountCodeModal() {
            const modal = document.getElementById('discountCodeModal');
            if (modal) {
                modal.classList.add('hidden');
                const input = document.getElementById('discountCodeInput');
                if (input) input.value = '';
            }
        }

        // ==================== DROPDOWN MENU FUNCTIONS ====================
        function togglePOSMenu() {
            const menu = document.getElementById('posDropdownMenu');
            menu.classList.toggle('hidden');
        }

        function initClickOutsideHandler() {
            document.addEventListener('click', function (e) {
                const menu = document.getElementById('posDropdownMenu');
                if (!menu) return;
                const isInsideMenu = menu.contains(e.target);
                const btn = e.target.closest('button');
                const isToggleButton = btn && btn.onclick && String(btn.onclick).includes('togglePOSMenu');
                if (!isInsideMenu && !isToggleButton) {
                    menu.classList.add('hidden');
                }
            });
        }

        // ==================== CALCULATOR FUNCTIONS ====================
        function openCalculator() {
            document.getElementById('calculatorModal').classList.remove('hidden');
            updateCalcDisplay();
        }

        function closeCalculator() {
            document.getElementById('calculatorModal').classList.add('hidden');
        }

        function calcNumber(num) {
            if (calcCurrentValue === '0' || calcCurrentValue === 'Error') {
                calcCurrentValue = num;
            } else {
                calcCurrentValue += num;
            }
            updateCalcDisplay();
        }

        function calcOperator(op) {
            if (calcPreviousValue !== '' && calcOperation !== null) {
                calcEquals();
            }
            calcPreviousValue = calcCurrentValue;
            calcOperation = op;
            calcCurrentValue = '0';
            updateCalcDisplay();
        }

        function calcDecimal() {
            if (!calcCurrentValue.includes('.')) {
                calcCurrentValue += '.';
                updateCalcDisplay();
            }
        }

        function calcEquals() {
            if (calcPreviousValue === '' || calcOperation === null) return;
            const prev = parseFloat(calcPreviousValue);
            const current = parseFloat(calcCurrentValue);
            let result;

            switch (calcOperation) {
                case '+': result = prev + current; break;
                case '-': result = prev - current; break;
                case '*': result = prev * current; break;
                case '/':
                    if (current === 0) {
                        calcCurrentValue = 'Error';
                        updateCalcDisplay();
                        calcPreviousValue = '';
                        calcOperation = null;
                        return;
                    }
                    result = prev / current;
                    break;
                default: return;
            }

            const expression = `${prev} ${calcOperation === '*' ? '×' : calcOperation === '/' ? '÷' : calcOperation} ${current}`;
            addToCalcHistory(expression, result);

            calcCurrentValue = result.toString();
            calcPreviousValue = '';
            calcOperation = null;
            updateCalcDisplay();
        }

        function calcClear() {
            calcCurrentValue = '0';
            calcPreviousValue = '';
            calcOperation = null;
            updateCalcDisplay();
        }

        function calcDelete() {
            if (calcCurrentValue.length > 1) {
                calcCurrentValue = calcCurrentValue.slice(0, -1);
            } else {
                calcCurrentValue = '0';
            }
            updateCalcDisplay();
        }

        function updateCalcDisplay() {
            document.getElementById('calcDisplay').textContent = calcCurrentValue;
            let expression = '';
            if (calcPreviousValue !== '') {
                const opSymbol = calcOperation === '*' ? '×' : calcOperation === '/' ? '÷' : calcOperation;
                expression = `${calcPreviousValue} ${opSymbol}`;
            }
            document.getElementById('calcExpression').textContent = expression;
        }

        function addToCalcHistory(expression, result) {
            const historyItem = {
                expression: expression,
                result: result,
                timestamp: new Date().toISOString()
            };
            calcHistory.unshift(historyItem);

            if (calcHistory.length > 50) {
                calcHistory = calcHistory.slice(0, 50);
            }

            saveCalcHistory();
            renderCalcHistory();
        }

        function saveCalcHistory() {
            localStorage.setItem('pos_calc_history', JSON.stringify(calcHistory));
        }

        function loadCalcHistory() {
            const saved = localStorage.getItem('pos_calc_history');
            if (saved) {
                try {
                    calcHistory = JSON.parse(saved);
                    renderCalcHistory();
                } catch (e) {
                    calcHistory = [];
                }
            }
        }

        function renderCalcHistory() {
            const container = document.getElementById('calcHistory');
            if (calcHistory.length === 0) {
                container.innerHTML = `
                <div class="text-center text-gray-400 text-sm py-8">
                    <i class="fas fa-history text-3xl mb-2 opacity-50"></i>
                    <p>Belum ada riwayat</p>
                </div>`;
                return;
            }

            let html = '';
            calcHistory.forEach((item, index) => {
                html += `
                <div class="calc-history-item" onclick="calcUseHistory(${index})">
                    <div class="calc-history-expression">${item.expression}</div>
                    <div class="calc-history-result">= ${formatNumber(item.result)}</div>
                </div>`;
            });

            container.innerHTML = html;
        }

        function calcUseHistory(index) {
            const item = calcHistory[index];
            calcCurrentValue = item.result.toString();
            calcPreviousValue = '';
            calcOperation = null;
            updateCalcDisplay();
        }

        function calcClearHistory() {
            Swal.fire({
                title: 'Hapus Riwayat?',
                text: "Semua riwayat kalkulator akan dihapus",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                iconColor: '#dc2626',
                customClass: {
                    popup: 'rounded-[1.5rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                },
                backdrop: 'rgba(0, 0, 0, 0.5)'
            }).then((result) => {
                if (result.isConfirmed) {
                    calcHistory = [];
                    saveCalcHistory();
                    renderCalcHistory();
                    calcHistory = [];
                    saveCalcHistory();
                    renderCalcHistory();
                    showToast('success', 'Riwayat berhasil dihapus');
                }
            });
        }

        // ==================== CASH REGISTER FUNCTIONS ====================
        function checkCashRegister() {
            return fetch('{{ route("cash-register.check") }}')
                .then(r => r.json())
                .then(data => {
                    if (data.is_open) {
                        document.getElementById('menuCloseCashRegister').classList.remove('hidden');
                        document.getElementById('menuOpenCashRegister').classList.add('hidden');
                    } else if (data.has_unfinished) {
                        const hasDeclined = sessionStorage.getItem('pos_declined_modal');
                        if (hasDeclined === 'true') {
                            document.getElementById('menuOpenCashRegister').classList.remove('hidden');
                            document.getElementById('menuCloseCashRegister').classList.add('hidden');
                        } else {
                            document.getElementById('startSalesModal').classList.remove('hidden');
                        }
                    } else {
                        const hasDeclined = sessionStorage.getItem('pos_declined_modal');
                        if (hasDeclined === 'true') {
                            document.getElementById('menuOpenCashRegister').classList.remove('hidden');
                            document.getElementById('menuCloseCashRegister').classList.add('hidden');
                        } else {
                            document.getElementById('startSalesModal').classList.remove('hidden');
                        }
                    }
                });
        }

        function closeStartSalesModal() {
            document.getElementById('startSalesModal').classList.add('hidden');
        }

        function openOpeningAmountModal() {
            document.getElementById('openingAmountModal').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('openingAmountInput').focus();
            }, 200);
        }

        function closeOpeningAmountModal() {
            document.getElementById('openingAmountModal').classList.add('hidden');
            document.getElementById('openingAmountInput').value = '';
        }

        function submitOpeningAmount() {
            const amount = parseFloat(document.getElementById('openingAmountInput').value) || 0;
            if (amount < 0) {
                showToast('warning', 'Jumlah tidak boleh negatif');
                return;
            }

            fetch('{{ route("cash-register.start") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ opening_amount: amount })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        closeOpeningAmountModal();
                        closeStartSalesModal();

                        if (data.is_continued) {
                            showToast('success', 'Melanjutkan sesi penjualan sebelumnya');
                        } else {
                            showToast('success', `Toko dibuka dengan modal awal Rp ${formatNumber(amount)}`);
                        }

                        document.getElementById('menuCloseCashRegister').classList.remove('hidden');
                        document.getElementById('menuOpenCashRegister').classList.add('hidden');
                        sessionStorage.removeItem('pos_declined_modal');
                    } else {
                        showToast('error', data.message || 'Gagal memulai sesi');
                    }
                })
                .catch(() => {
                    showToast('error', 'Terjadi kesalahan saat memulai sesi');
                });
        }

        function skipOpeningAmount() {
            Swal.fire({
                title: 'Lewati Modal Awal?',
                text: "Modal awal akan diset Rp 0. Anda yakin?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#658C58',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lewati',
                cancelButtonText: 'Batal',
                iconColor: '#658C58',
                customClass: {
                    popup: 'rounded-[1.5rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                },
                backdrop: 'rgba(0, 0, 0, 0.5)'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("cash-register.start") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ opening_amount: 0 })
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                closeOpeningAmountModal();
                                closeStartSalesModal();

                                if (data.is_continued) {
                                    showToast('success', 'Melanjutkan sesi penjualan sebelumnya');
                                } else {
                                    showToast('success', 'Toko dibuka dengan modal awal Rp 0');
                                }

                                document.getElementById('menuCloseCashRegister').classList.remove('hidden');
                                document.getElementById('menuOpenCashRegister').classList.add('hidden');
                                sessionStorage.removeItem('pos_declined_modal');
                            } else {
                                showToast('error', data.message || 'Gagal memulai sesi');
                            }
                        })
                        .catch(() => {
                            showToast('error', 'Terjadi kesalahan');
                        });
                }
            });
        }

        function declineStartSales() {
            closeStartSalesModal();
            document.getElementById('menuOpenCashRegister').classList.remove('hidden');
            document.getElementById('menuCloseCashRegister').classList.add('hidden');

            sessionStorage.setItem('pos_declined_modal', 'true');

            showToast('info', 'Anda bisa buka toko kapan saja dengan klik tombol "Buka Toko"');
        }

        function handleCloseCashRegister() {
            fetch('{{ route("cash-register.check-sales") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const totalSales = parseFloat(data.total_sales || 0);
                        if (totalSales <= 0) {
                            closeCashRegisterSilent();
                        } else {
                            window.location.href = '{{ route("cash-register.close") }}';
                        }
                    } else {
                        showToast('error', 'Gagal mengecek data penjualan');
                    }
                })
                .catch(() => {
                    showToast('error', 'Terjadi kesalahan saat tutup toko');
                });
        }

        function closeCashRegisterSilent() {
            fetch('{{ route("cash-register.close-silent") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('menuCloseCashRegister').classList.add('hidden');
                        document.getElementById('menuOpenCashRegister').classList.remove('hidden');
                        showToast('success', 'Toko ditutup. Tidak ada penjualan di sesi ini.');
                    } else {
                        showToast('error', data.message || 'Gagal menutup toko');
                    }
                })
                .catch(() => {
                    showToast('error', 'Gagal menutup toko');
                });
        }

        // ==================== UTILITY FUNCTIONS ====================
        function showToast(type, message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: type,
                title: message
            });
        }

        function formatNumber(num) {
            num = Number(num || 0);
            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function formatDateTime(s) {
            if (!s) return '-';
            let d = new Date(s);
            if (isNaN(d.getTime())) {
                // Try fixing SQL timestamp format for Safari: "YYYY-MM-DD HH:MM:SS" -> "YYYY-MM-DDTHH:MM:SS"
                if (typeof s === 'string') {
                    d = new Date(s.replace(' ', 'T'));
                }
            }
            if (isNaN(d.getTime())) return '-';

            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            const h = String(d.getHours()).padStart(2, '0');
            const m = String(d.getMinutes()).padStart(2, '0');
            return `${day}/${month}/${year} ${h}:${m}`;
        }

        // ==================== CART FUNCTIONS ====================
        let currentDetailCartKey = null;

        function addProductToCart(el) {
            const isStoreOpen = !document.getElementById('menuCloseCashRegister').classList.contains('hidden');
            if (!isStoreOpen) {
                showToast('warning', 'Buka toko terlebih dahulu untuk mulai transaksi!');
                return;
            }

            const productId = el.dataset.productId;
            const productName = el.dataset.productName;
            const productPrice = parseFloat(el.dataset.productPrice || 0);
            const estStock = parseFloat(el.dataset.estimatedStock || 0);
            const isProduced = el.dataset.isProduced === 'true';

            // Check stock availability
            if (estStock <= 0) {
                showToast('error', 'Stok tidak cukup!');
                return;
            }

            // If item already in cart, increment quantity automatically (+1)
            if (cart[productId]) {
                incrementQty(productId);
                return;
            }

            // NEW PRODUCT: Show Modal
            showProductDetailModal({
                productId: productId,
                productName: productName,
                productPrice: productPrice,
                isProduced: isProduced,
                estStock: estStock
            });
        }

        function showProductDetailModal(data, isEdit = false) {
            const modal = document.getElementById('productDetailModal');
            document.getElementById('detailModalProductName').textContent = data.productName;
            document.getElementById('detailModalProductPrice').textContent = 'Rp ' + formatNumber(data.productPrice);

            const qtyInput = document.getElementById('detailModalQty');
            const attributedInput = document.getElementById('detailModalAttributedTo');
            const notesInput = document.getElementById('detailModalNotes');
            const submitText = document.getElementById('detailModalSubmitText');

            if (isEdit) {
                currentDetailCartKey = data.cartKey;
                qtyInput.value = data.quantity || 1;

                // Parse notes if they follow our format
                let rawNotes = data.notes || '';
                let attributedTo = '';
                let userNotes = '';

                if (rawNotes.includes('Atas nama:')) {
                    const parts = rawNotes.split('___NOTES___');
                    attributedTo = parts[0].replace('Atas nama:', '').trim();
                    userNotes = parts[1] ? parts[1].trim() : '';
                } else {
                    userNotes = rawNotes;
                }

                attributedInput.value = attributedTo;
                notesInput.value = userNotes;
                submitText.textContent = 'Simpan Perubahan';
            } else {
                currentDetailCartKey = null;
                qtyInput.value = 1;
                notesInput.value = '';

                // Pre-fill attributed name if customer selected
                if (typeof currentCustomer !== 'undefined' && currentCustomer) {
                    attributedInput.value = currentCustomer.name;
                } else {
                    attributedInput.value = '';
                }
                submitText.textContent = 'Tambah ke Keranjang';
            }

            // Store data for submission
            modal.dataset.productId = data.productId;
            modal.dataset.isProduced = data.isProduced;
            modal.dataset.estStock = data.estStock;
            modal.dataset.productName = data.productName;

            modal.classList.remove('hidden');
        }

        function closeProductDetailModal() {
            document.getElementById('productDetailModal').classList.add('hidden');
        }

        function detailModalIncrement() {
            const input = document.getElementById('detailModalQty');
            input.value = parseInt(input.value) + 1;
        }

        function detailModalDecrement() {
            const input = document.getElementById('detailModalQty');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function submitProductDetail() {
            const modal = document.getElementById('productDetailModal');
            const productId = modal.dataset.productId;
            const qty = parseInt(document.getElementById('detailModalQty').value);
            const attributedTo = document.getElementById('detailModalAttributedTo').value.trim();
            const userNotes = document.getElementById('detailModalNotes').value.trim();

            const isProduced = modal.dataset.isProduced === 'true';
            const estStock = parseFloat(modal.dataset.estStock || 0);
            const productName = modal.dataset.productName;

            // Stock check for kitchen items
            if (isProduced && qty > estStock) {
                Swal.fire({
                    title: 'Stok Terbatas',
                    text: `Stok bahan baku di dapur untuk ${productName} mungkin kurang. Lanjutkan?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#658C58',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Lanjutkan',
                    cancelButtonText: 'Batal',
                    iconColor: '#658C58',
                    customClass: {
                        popup: 'rounded-[1.5rem] border-none shadow-2xl',
                        title: 'font-black text-gray-900',
                    },
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        performSubmitProductDetail(productId, qty, attributedTo, userNotes);
                    }
                });
            } else {
                performSubmitProductDetail(productId, qty, attributedTo, userNotes);
            }
        }

        function performSubmitProductDetail(productId, qty, attributedTo, userNotes) {
            // Format notes exactly as requested:
            // Atas nama: [Name]
            // ___NOTES___
            // [Notes]
            const nameLabel = attributedTo ? `Atas nama: ${attributedTo}\n` : '';
            const finalNotes = `${nameLabel}___NOTES___\n${userNotes}`;

            if (currentDetailCartKey) {
                // EDIT MODE
                performUpdateCartQuantity(currentDetailCartKey, qty, finalNotes);
            } else {
                // ADD MODE
                performAddToCart(productId, qty, finalNotes);
            }
            closeProductDetailModal();
        }

        function performAddToCart(productId, quantity = 1, notes = '') {
            fetch('{{ route("pos.cart.add") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ product_id: productId, quantity: quantity, notes: notes })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        cart = data.cart;
                        cartSummary = data.cart_summary;
                        if (data.discount_plan !== undefined) activeDiscountPlan = data.discount_plan;
                        renderCart();
                        showToast('success', 'Ditambahkan ke keranjang');
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(() => showToast('error', 'Terjadi kesalahan'));
        }

        function updateCartQuantity(cartKey, newQty, notes = null) {
            let qty = parseFloat(newQty);
            if (isNaN(qty) || qty < 0) qty = 0;

            const el = document.querySelector(`.product-card[data-product-id="${cartKey}"]`);
            if (el) {
                const estStock = parseFloat(el.dataset.estimatedStock || 0);
                const isProduced = el.dataset.isProduced === 'true';
                const productName = el.dataset.productName;

                const currentQty = cart[cartKey] ? parseFloat(cart[cartKey].quantity) : 0;

                // Only show modal if qty increased and exceeds estStock
                if (isProduced && qty > estStock && qty > currentQty) {
                    Swal.fire({
                        title: 'Stok Terbatas',
                        text: `Stok bahan baku di dapur untuk ${productName} mungkin kurang. Lanjutkan?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#658C58',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Lanjutkan',
                        cancelButtonText: 'Batal',
                        iconColor: '#658C58',
                        customClass: {
                            popup: 'rounded-[1.5rem] border-none shadow-2xl',
                            title: 'font-black text-gray-900',
                        },
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            performUpdateCartQuantity(cartKey, qty, notes);
                        } else {
                            renderCart(); // Reset quantity in UI
                        }
                    });
                    return;
                }
            }
            performUpdateCartQuantity(cartKey, qty, notes);
        }

        function performUpdateCartQuantity(cartKey, qty, notes = null) {
            const payload = { cart_key: cartKey, quantity: qty };
            if (notes !== null) payload.notes = notes;

            fetch('{{ route("pos.cart.update") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        cart = data.cart;
                        cartSummary = data.cart_summary;
                        if (data.discount_plan !== undefined) activeDiscountPlan = data.discount_plan;
                        renderCart();
                    } else {
                        showToast('error', data.message || 'Gagal update item');
                        renderCart(); // Reset locally
                    }
                })
                .catch(() => {
                    showToast('error', 'Gagal update item');
                    renderCart(); // Reset locally
                });
        }

        function removeCartItem(cartKey) {
            fetch('{{ route("pos.cart.remove") }}', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ cart_key: cartKey })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        cart = data.cart;
                        cartSummary = data.cart_summary;
                        if (data.discount_plan !== undefined) activeDiscountPlan = data.discount_plan;
                        renderCart();
                        showToast('success', 'Item dihapus');
                    } else {
                        showToast('error', data.message || 'Gagal menghapus item');
                    }
                })
                .catch(() => showToast('error', 'Gagal menghapus item'));
        }

        function clearCart() {
            Swal.fire({
                title: 'Kosongkan Keranjang?',
                text: "Semua item di keranjang akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#658C58',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Kosongkan',
                cancelButtonText: 'Batal',
                iconColor: '#658C58',
                customClass: {
                    popup: 'rounded-[1.5rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("pos.cart.clear") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                cart = {};
                                cartSummary = { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0, total_items: 0 };
                                activeDiscountPlan = null; // ⬅️ TAMBAHKAN INI
                                renderCart();
                                setUIState('browse');
                                showToast('success', 'Keranjang dikosongkan');
                            }
                        })
                }
            });
        }

        function showEditCartItemModal(cartKey) {
            const item = cart[cartKey];
            if (!item) return;

            // Find the product card for stock info
            const el = document.querySelector(`.product-card[data-product-id="${item.product_id}"]`);
            const estStock = el ? parseFloat(el.dataset.estimatedStock || 0) : 999;
            const isProduced = el ? (el.dataset.isProduced === 'true') : false;

            showProductDetailModal({
                cartKey: cartKey,
                productId: item.product_id,
                productName: item.product_name,
                productPrice: item.unit_price,
                quantity: item.quantity,
                notes: item.notes,
                isProduced: isProduced,
                estStock: estStock
            }, true);
        }

        function renderCart() {
            const preview = document.getElementById('cartItemsPreview');
            const oldDiscountInfo = document.getElementById('discountInfo');
            if (oldDiscountInfo) oldDiscountInfo.remove();

            const totalItems = Object.values(cart || {}).reduce((s, i) => s + Number(i.quantity || 0), 0);
            document.getElementById('totalItems').textContent = totalItems;

            const isPayflow = (UI_STATE !== 'browse');

            if (!cart || Object.keys(cart).length === 0) {
                // PERBAIKAN: Clear discount plan jika cart kosong
                activeDiscountPlan = null;

                preview.innerHTML = `
                <div class="empty-state" id="emptyCartPreview">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Keranjang kosong</p>
                </div>`;
            } else {
                let html = '';
                for (const [key, item] of Object.entries(cart)) {
                    const subtotal = Number(item.subtotal || (item.unit_price * item.quantity));
                    const formattedPrice = formatNumber(item.unit_price);
                    const formattedSubtotal = formatNumber(subtotal);

                    html += `
                <div class="order-item">
                    <div class="order-item-info cursor-pointer flex-1 group" onclick="showEditCartItemModal('${key}')">
                        <div class="order-item-name group-hover:text-indigo-600 transition-colors" title="${item.product_name}">${item.product_name}</div>
                        <div class="order-item-price">@ Rp ${formattedPrice} = <span class="font-semibold text-indigo-600">Rp ${formattedSubtotal}</span></div>
                        ${item.notes ? `<div class="text-[10px] text-gray-400 mt-1 italic line-clamp-1"><i class="fas fa-sticky-note mr-1 text-gray-300"></i>${item.notes.replace(/\n/g, ' ')}</div>` : ''}
                    </div>

                    ${!isPayflow
                            ? `
                        <div class="flex items-center gap-2">
                            <div class="qty-controls">
                                <button class="qty-btn" onclick="decrementQty('${key}')">−</button>
                                <input type="number" min="0" step="1" value="${Number(item.quantity)}"
                                        class="qty-input"
                                        onblur="onQtyBlur('${key}', this.value)"
                                        onkeydown="if(event.key==='Enter'){onQtyBlur('${key}', this.value)}">
                                <button class="qty-btn" onclick="incrementQty('${key}')">+</button>
                            </div>
                            <button class="text-red-500 hover:text-red-700" title="Hapus" onclick="removeCartItem('${key}')">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </div>
                        `
                            : `
                        <div class="text-sm font-bold text-gray-700">x${Number(item.quantity)}</div>
                        `
                        }
                </div>`;
                }
                preview.innerHTML = html;
            }

            // ====== INFO DISKON DI AREA KERANJANG - RESPONSIVE LIST ======
            if (activeDiscountPlan && activeDiscountPlan.applied_discounts && activeDiscountPlan.applied_discounts.length > 0) {
                const discountInfo = document.createElement('div');
                discountInfo.id = 'discountInfo';
                discountInfo.className = 'discount-container';

                const discounts = activeDiscountPlan.applied_discounts;
                const totalSaved = activeDiscountPlan.total_discount || cartSummary.total_discount || 0;

                let listHtml = '';
                discounts.forEach(d => {
                    const isBogo = d.type === 'buy_x_get_y';
                    const detailText = isBogo ? `Beli ${d.quota} item gratis` : `Hemat Rp ${formatNumber(d.amount)}`;
                    const iconClass = isBogo ? 'fa-gift' : 'fa-tag';
                    const onclickAction = isBogo ? `onclick="showFreeItemSelectionModal(activeDiscountPlan)"` : '';

                    listHtml += `
                <div class="discount-item">
                    <div class="discount-item-info ${isBogo ? 'cursor-pointer' : ''}" ${onclickAction}>
                        <span class="discount-item-name"><i class="fas ${iconClass} mr-1"></i> ${d.name}</span>
                        <span class="discount-item-detail">${detailText}</span>
                    </div>
                    <div class="discount-item-remove" onclick="removeAppliedDiscount(${d.id})" title="Hapus Diskon">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </div>
                </div>`;
                });

                discountInfo.innerHTML = `
                <div class="discount-summary-btn" onclick="toggleDiscountList()">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <i class="fas fa-percentage text-xs"></i>
                        </div>
                        <div class="text-left">
                            <div class="text-[10px] font-bold text-red-500 uppercase tracking-wider">Diskon Terpakai</div>
                            <div class="text-xs font-black text-gray-900">${discounts.length} Promo Aktif</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="text-right mr-1">
                            <div class="text-[10px] text-gray-400">Total Hemat</div>
                            <div class="text-xs font-black text-emerald-600">Rp ${formatNumber(totalSaved)}</div>
                        </div>
                        <i id="discountToggleIcon" class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform"></i>
                    </div>
                </div>
                <div id="discountList" class="discount-list">
                    ${listHtml}
                    <div class="p-2 border-t border-dashed border-red-100">
                        <button onclick="clearDiscount()" class="w-full py-1.5 text-[10px] font-bold text-red-400 hover:text-red-600 transition-colors uppercase tracking-widest">
                            Hapus Semua Diskon
                        </button>
                    </div>
                </div>
            `;

                if (preview.firstChild) {
                    preview.prepend(discountInfo);
                } else {
                    preview.appendChild(discountInfo);
                }
            } else if (activeDiscountPlan && activeDiscountPlan.discount_name) {
                // Fallback untuk data diskon lama (singel)
                const discountInfo = document.createElement('div');
                discountInfo.id = 'discountInfo';
                const saved = activeDiscountPlan.total_discount || cartSummary.total_discount || 0;

                discountInfo.innerHTML = `
                <div class="bg-gradient-to-r from-red-50 to-cuan-green/10 border border-red-100 rounded-xl p-3 mb-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-[10px] font-bold text-red-500 uppercase mb-0.5">Diskon Aktif</div>
                            <div class="text-sm font-black text-gray-900">${activeDiscountPlan.discount_name}</div>
                            <div class="text-xs font-bold text-emerald-600 mt-1">Hemat Rp ${formatNumber(saved)}</div>
                        </div>
                        <button onclick="clearDiscount()" class="w-8 h-8 rounded-full hover:bg-red-100 text-red-400 transition-colors flex items-center justify-center">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
                if (preview.firstChild) preview.prepend(discountInfo);
                else preview.appendChild(discountInfo);
            }

            // ====== RINGKASAN DI FOOTER ======
            document.getElementById('summarySubtotal').textContent = 'Rp ' + formatNumber(cartSummary.subtotal || 0);
            document.getElementById('summaryDiscount').textContent = '- Rp ' + formatNumber(cartSummary.total_discount || 0);
            document.getElementById('summaryGrandTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total || 0);

            document.getElementById('cashTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total || 0);
            if (document.getElementById('selectTotal')) document.getElementById('selectTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total || 0);
            document.getElementById('transferTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total || 0);
            document.getElementById('midtransTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total || 0);

            const payLabel = `${totalItems} item · Rp ${formatNumber(cartSummary.grand_total || 0)}`;
            const labelEl = document.getElementById('payflowSummaryLabel');
            if (labelEl) labelEl.textContent = payLabel;
        }

        function incrementQty(cartKey) {
            const item = cart[cartKey];
            if (!item) return;
            const newQty = Number(item.quantity) + 1;
            updateCartQuantity(cartKey, newQty);
        }

        function decrementQty(cartKey) {
            const item = cart[cartKey];
            if (!item) return;
            const newQty = Math.max(0, Number(item.quantity) - 1);
            updateCartQuantity(cartKey, newQty);
        }

        function onQtyBlur(cartKey, value) {
            let v = value === '' ? 0 : value;
            updateCartQuantity(cartKey, v);
        }

        // ==================== UI STATE FUNCTIONS ====================
        function setUIState(state) {
            UI_STATE = state;
            const views = ['browse', 'service', 'table', 'select', 'cash', 'transfer', 'midtrans'];
            views.forEach(v => {
                const el = document.getElementById(`view-${v}`);
                if (el) el.classList.add('hidden');
            });
            const active = document.getElementById(`view-${state}`);
            if (active) active.classList.remove('hidden');

            // Toggle Toolbar Visibility (Only hide the search/filter area)
            const toolbar = document.querySelector('.products-toolbar');

            if (state === 'browse') {
                if (toolbar) toolbar.classList.remove('hidden');
            } else {
                if (toolbar) toolbar.classList.add('hidden');

                // Auto scroll to top for all screens
                window.scrollTo({ top: 0, behavior: 'smooth' });
                const contentArea = document.querySelector('.products-content');
                if (contentArea) {
                    contentArea.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }

            updateRightActions();
            renderCart();

            if (state === 'cash') {
                calculateChange();
                setTimeout(() => document.getElementById('cashPaidAmount').focus(), 120);
            }
        }

        function updateRightActions() {
            const isPayflow = (UI_STATE !== 'browse');
            const controls = document.getElementById('actionsControls');
            const badge = document.getElementById('actionsPayflowSummary');
            if (!controls || !badge) return;

            if (isPayflow) {
                controls.classList.add('hidden');
                badge.classList.remove('hidden');
            } else {
                controls.classList.remove('hidden');
                badge.classList.add('hidden');
            }
        }

        function backToBrowse() {
            setUIState('browse');
        }

        function showPaymentSelection() {
            initiatePaymentFlow();
        }

        // ==================== PAYMENT FUNCTIONS ====================
        function calculateChange() {
            const paid = parseFloat(document.getElementById('cashPaidAmount').value) || 0;
            const change = paid - (cartSummary.grand_total || 0);
            const amountEl = document.getElementById('changeAmount');
            const labelEl = document.getElementById('changeLabel');

            if (change < 0) {
                labelEl.textContent = 'Sisa:';
                amountEl.textContent = '- Rp ' + formatNumber(Math.abs(change));
                amountEl.classList.remove('text-green-600');
                amountEl.classList.add('text-red-600');
            } else {
                labelEl.textContent = 'Kembalian:';
                amountEl.textContent = 'Rp ' + formatNumber(change);
                amountEl.classList.remove('text-red-600');
                amountEl.classList.add('text-green-600');
            }
        }

        // ==================== PAYMENT FUNCTIONS ====================
        function processCashPayment() {
            const paid = parseFloat(document.getElementById('cashPaidAmount').value) || 0;
            const grandTotal = cartSummary.grand_total || 0;

            if (paid < grandTotal) {
                const shortfall = grandTotal - paid;

                Swal.fire({
                    title: 'Jumlah Uang Kurang',
                    html: `
                    <div class="text-left space-y-4 mt-2">
                        <div class="bg-gradient-to-br from-cuan-green/10 to-cuan-dark/10 p-5 rounded-2xl border border-cuan-green/20 shadow-sm">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[10px] font-bold text-cuan-green uppercase tracking-wider">Total Transaksi</span>
                                <span class="text-lg font-black text-gray-900">Rp ${formatNumber(grandTotal)}</span>
                            </div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Uang Diterima</span>
                                <span class="text-lg font-black text-emerald-600">Rp ${formatNumber(paid)}</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-cuan-green/20">
                                <span class="text-[10px] font-bold text-red-500 uppercase tracking-wider">Sisa Kekurangan</span>
                                <span class="text-2xl font-black text-red-600">Rp ${formatNumber(shortfall)}</span>
                            </div>
                        </div>
                        <div class="flex gap-3 text-emerald-600 bg-emerald-50 p-3 rounded-xl border border-emerald-100 items-start">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <p class="text-xs font-medium leading-relaxed">
                                Pembayaran kurang dari total belanja. Apakah sisa kekurangan ini akan dicatat sebagai <b>utang pelanggan</b>?
                            </p>
                        </div>
                    </div>
                `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#658C58',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-check mr-2"></i>Ya, Catat Utang',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Tidak, Batalkan',
                    iconColor: '#658C58',
                    customClass: {
                        popup: 'rounded-[1.5rem] border-none shadow-2xl swal-wide',
                        title: 'font-black text-gray-900',
                        confirmButton: 'px-6 py-3',
                        cancelButton: 'px-6 py-3'
                    },
                    backdrop: 'rgba(0, 0, 0, 0.5)'
                }).then((result) => {
                    console.log('Swal shortfall result:', result);
                    if (result.isConfirmed) {
                        try {
                            console.log('Attempting to open debt modal...');
                            openDebtPaymentModal(grandTotal, paid, shortfall);
                        } catch (e) {
                            console.error('Critical error in Swal callback:', e);
                            showToast('error', 'Terjadi kesalahan saat membuka modal utang');
                        }
                    }
                });

                return;
            }

            const btn = document.getElementById('btn-process-cash');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...`;

            // Process normal cash payment if amount is sufficient
            fetch('{{ route("payment.cash") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    paid_amount: paid,
                    service_type: selectedServiceType,
                    table_id: selectedTableId
                })
            })
                .then(r => r.json())
                .then(async data => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    if (data.success) {
                        if (data.sale && data.sale.items) {
                            updateProductStockFromSaleItems(data.sale.items);
                        }
                        if (activeDiscountPlan && activeDiscountPlan.discount_type === 'buy_x_get_y') {
                            updateProductStockWithFreeItems(activeDiscountPlan);
                        }

                        refreshAllProductStocks();

                        await fetch('{{ route("pos.cart.clear") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        cart = {};
                        cartSummary = { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0, total_items: 0 };
                        activeDiscountPlan = null;

                        renderCart();
                        setUIState('browse');

                        // RESET INPUTS
                        const cashInput = document.getElementById('cashPaidAmount');
                        if (cashInput) cashInput.value = '';
                        clearCustomer(true);

                        openPaymentSuccessModal({
                            sale_id: data.sale.id,
                            invoice_number: data.sale.invoice_number,
                            created_at: data.sale.created_at,
                            grand_total: data.sale.grand_total,
                            change_amount: data.change,
                            customer_name: data.sale.customer_name
                        });
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        showToast('error', data.message || 'Gagal proses pembayaran');
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    showToast('error', 'Gagal proses pembayaran');
                });
        }

        function openDebtPaymentModal(grandTotal, paidAmount, remainingAmount) {
            console.log('openDebtPaymentModal called:', { grandTotal, paidAmount, remainingAmount });
            try {
                const totalAmount = grandTotal;
                const paid = paidAmount;
                const shortfall = remainingAmount || (totalAmount - paid);

                debtPaymentData = {
                    grandTotal: totalAmount,
                    paidAmount: paid,
                    remainingAmount: shortfall
                };

                const totalEl = document.getElementById('debtTotalAmount');
                const paidEl = document.getElementById('debtPaidAmount');
                const remEl = document.getElementById('debtRemainingAmount');
                const actualPaidEl = document.getElementById('debtActualPaidAmount');

                if (!totalEl || !paidEl || !remEl || !actualPaidEl) {
                    console.error('One or more debt modal summary elements not found');
                    throw new Error('Elemen modal tidak ditemukan');
                }

                totalEl.textContent = 'Rp ' + formatNumber(totalAmount);
                paidEl.textContent = 'Rp ' + formatNumber(paid);
                remEl.textContent = 'Rp ' + formatNumber(shortfall);
                actualPaidEl.value = paid;

                // Reset form first
                resetDebtPaymentForm();

                // Target elements
                const inputId = document.getElementById('debtCustomerId');
                const inputName = document.getElementById('debtCustomerName');
                const inputPhone = document.getElementById('debtCustomerPhone');
                const inputEmail = document.getElementById('debtCustomerEmail');
                const inputAddress = document.getElementById('debtCustomerAddress');
                const inputType = document.getElementById('debtCustomerType');
                const inputTypeHidden = document.getElementById('debtCustomerTypeHidden');
                const inputCreditLimit = document.getElementById('debtCreditLimit');

                // If a customer is already selected in main POS, pre-fill the modal
                if (typeof currentCustomer !== 'undefined' && currentCustomer) {
                    console.log('Pre-filling debt modal with currentCustomer:', currentCustomer);

                    if (inputId) inputId.value = currentCustomer.id || '';
                    if (inputName) {
                        inputName.value = currentCustomer.name || '';
                        if (inputName.value) {
                            inputName.readOnly = true;
                            inputName.classList.add('bg-gray-100');
                        } else {
                            inputName.readOnly = false;
                            inputName.classList.remove('bg-gray-100');
                        }
                    }
                    if (inputPhone) {
                        inputPhone.value = currentCustomer.phone || '';
                        if (inputPhone.value) {
                            inputPhone.readOnly = true;
                            inputPhone.classList.add('bg-gray-100');
                        } else {
                            inputPhone.readOnly = false;
                            inputPhone.classList.remove('bg-gray-100');
                        }
                    }
                    if (inputEmail) {
                        inputEmail.value = currentCustomer.email || '';
                        if (inputEmail.value) {
                            inputEmail.readOnly = true;
                            inputEmail.classList.add('bg-gray-100');
                        }
                    }
                    if (inputAddress) inputAddress.value = currentCustomer.address || '';

                    const customerType = currentCustomer.type || 'regular';
                    if (inputType) inputType.value = customerType;
                    if (inputTypeHidden) inputTypeHidden.value = customerType;

                    if (inputCreditLimit) inputCreditLimit.value = currentCustomer.credit_limit || '';

                    selectedCustomerData = currentCustomer;
                } else {
                    // Ensure inputs are editable if no customer selected
                    if (inputName) {
                        inputName.readOnly = false;
                        inputName.classList.remove('bg-gray-100');
                    }
                    if (inputPhone) {
                        inputPhone.readOnly = false;
                        inputPhone.classList.remove('bg-gray-100');
                    }
                    if (inputEmail) {
                        inputEmail.readOnly = false;
                        inputEmail.classList.remove('bg-gray-100');
                    }
                }

                const modal = document.getElementById('debtPaymentModal');
                if (!modal) {
                    console.error('debtPaymentModal element not found');
                    throw new Error('Modal utang tidak ditemukan di DOM');
                }
                modal.classList.remove('hidden');

                // Initialize customer search
                initDebtCustomerSearch();

                setTimeout(() => {
                    if (inputName && !inputName.readOnly) {
                        inputName.focus();
                    } else if (document.getElementById('debtNotes')) {
                        document.getElementById('debtNotes').focus();
                    }
                }, 200);
            } catch (e) {
                console.error('Exception in openDebtPaymentModal:', e);
                showToast('error', 'Gagal membuka modal utang: ' + e.message);
            }
        }

        /**
         * Close debt payment modal
         */
        function closeDebtPaymentModal() {
            document.getElementById('debtPaymentModal').classList.add('hidden');
            resetDebtPaymentForm();
        }

        // ==================== TABLE MANAGEMENT FUNCTIONS ====================
        let tablesData = [];
        let selectedTableId = null;
        let selectedTableNumber = null;
        let selectedServiceType = 'take_away'; // default
        let hasTableSystem = {{ auth()->user()->outlet && auth()->user()->outlet->has_table_system ? 'true' : 'false' }};

        async function loadTablesData() {
            try {
                const response = await fetch('/api/tables');
                const data = await response.json();
                tablesData = data.tables || [];
                return tablesData;
            } catch (error) {
                console.error('Error loading tables:', error);
                tablesData = [];
                return [];
            }
        }

        function openTableManagementModal() {
            document.getElementById('tableManagementModal').classList.remove('hidden');
            loadAndRenderTableManagement();
        }

        function closeTableManagementModal() {
            document.getElementById('tableManagementModal').classList.add('hidden');
        }

        async function loadAndRenderTableManagement() {
            await loadTablesData();
            const grid = document.getElementById('tableManagementGrid');
            const empty = document.getElementById('tableManagementEmpty');

            if (tablesData.length === 0) {
                grid.classList.add('hidden');
                empty.classList.remove('hidden');
                return;
            }

            grid.classList.remove('hidden');
            empty.classList.add('hidden');

            // Update stats
            document.getElementById('tmTotalTables').textContent = tablesData.length;
            document.getElementById('tmAvailableTables').textContent = tablesData.filter(t => t.status === 'available').length;
            document.getElementById('tmOccupiedTables').textContent = tablesData.filter(t => t.status === 'occupied').length;
            document.getElementById('tmReservedTables').textContent = tablesData.filter(t => t.status === 'reserved').length;

            // Render tables
            grid.innerHTML = tablesData.map(table => {
                const statusColors = {
                    available: {
                        bg: 'bg-white',
                        border: 'border-cuan-green/10',
                        icon: 'bg-cuan-green/10 text-cuan-green',
                        badge: 'bg-cuan-green text-white',
                        shadow: 'hover:shadow-cuan-green/20'
                    },
                    occupied: {
                        bg: 'bg-white',
                        border: 'border-red-100',
                        icon: 'bg-red-100 text-red-600',
                        badge: 'bg-red-500 text-white',
                        shadow: 'hover:shadow-red-200'
                    },
                    reserved: {
                        bg: 'bg-white',
                        border: 'border-orange-100',
                        icon: 'bg-orange-50 text-orange-500',
                        badge: 'bg-orange-500 text-white',
                        shadow: 'hover:shadow-orange-200'
                    },
                    maintenance: {
                        bg: 'bg-white',
                        border: 'border-gray-100',
                        icon: 'bg-gray-100 text-gray-600',
                        badge: 'bg-gray-500 text-white',
                        shadow: 'hover:shadow-gray-200'
                    }
                };
                const colors = statusColors[table.status] || statusColors.maintenance;
                const statusLabels = { available: 'Tersedia', occupied: 'Terisi', reserved: 'Dipesan', maintenance: 'Maintenance' };

                return `
                <div class="${colors.bg} ${colors.border} border-2 rounded-2xl p-4 flex flex-col items-center justify-center cursor-pointer hover:shadow-xl ${colors.shadow} transition-all duration-300 group relative overflow-hidden"
                     onclick="toggleTableStatusFromModal(${table.id})">
                    <div class="absolute top-0 right-0 p-1.5">
                        <div class="w-2.5 h-2.5 rounded-full ${colors.badge.split(' ')[0]} animate-pulse"></div>
                    </div>
                    <div class="w-16 h-16 ${colors.icon} rounded-2xl flex flex-col items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300 shadow-sm border border-gray-50">
                        <span class="text-xl font-black leading-none">${table.table_number}</span>
                        <span class="text-[9px] font-bold mt-1 opacity-60">KAP: ${table.capacity}</span>
                    </div>
                    <div class="text-sm font-bold text-gray-900 group-hover:text-cuan-green transition-colors truncate w-full text-center px-1">
                        ${table.name || 'Meja ' + table.table_number}
                    </div>
                    <div class="text-[10px] font-medium text-gray-400 mt-0.5 tracking-tighter truncate w-full text-center">
                        <i class="fas fa-map-marker-alt text-[8px] mr-1"></i>${table.location || 'Area Umum'}
                    </div>
                    <div class="mt-3 w-full">
                        <div class="text-[10px] font-black ${colors.badge} rounded-lg py-1.5 w-full text-center shadow-sm uppercase tracking-wider">
                            ${statusLabels[table.status] || table.status}
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        }

        async function toggleTableStatusFromModal(tableId) {
            try {
                const response = await fetch(`/tables/${tableId}/quick-toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showToast('success', `Status meja berhasil diubah ke ${data.status_label}`);
                    loadAndRenderTableManagement();
                }
            } catch (error) {
                showToast('error', 'Gagal mengubah status meja');
            }
        }

        async function toggleTableSystem(enabled) {
            try {
                const response = await fetch('/api/outlet/toggle-table-system', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ enabled })
                });
                const data = await response.json();
                if (data.success) {
                    hasTableSystem = enabled;

                    // Toggle visibility of Manage Tables button
                    const btnManageTables = document.getElementById('btnManageTables');
                    if (btnManageTables) {
                        btnManageTables.style.display = enabled ? '' : 'none';
                    }

                    showToast('success', enabled ? 'Sistem meja diaktifkan' : 'Sistem meja dinonaktifkan');
                }
            } catch (error) {
                showToast('error', 'Gagal mengubah pengaturan sistem meja');
                document.getElementById('tableSystemToggle').checked = !enabled;
            }
        }

        async function toggleAutoProduction(enabled) {
            try {
                const response = await fetch('/api/outlet/toggle-auto-production', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ enabled })
                });
                const data = await response.json();
                if (data.success) {
                    showToast('success', enabled ? 'Produksi otomatis diaktifkan' : 'Produksi otomatis dinonaktifkan');
                } else {
                    showToast('error', data.message || 'Gagal mengubah pengaturan produksi otomatis');
                    document.getElementById('autoProductionToggle').checked = !enabled;
                }
            } catch (error) {
                showToast('error', 'Gagal mengubah pengaturan produksi otomatis');
                document.getElementById('autoProductionToggle').checked = !enabled;
            }
        }

        // Payment Flow: Service Type Selection
        function initiatePaymentFlow() {
            if (Object.keys(cart).length === 0) {
                showToast('warning', 'Keranjang masih kosong');
                return;
            }

            // CHECK BOGO: Jika ada diskon BOGO tapi belum pilih item gratis
            if (activeDiscountPlan && activeDiscountPlan.discount_type === 'buy_x_get_y') {
                const freeItems = activeDiscountPlan.affected_items || [];
                const totalFreeQty = freeItems.reduce((sum, item) => sum + (item.free_qty || 0), 0);

                if (totalFreeQty === 0) {
                    Swal.fire({
                        title: 'Item Gratis Belum Dipilih',
                        text: "Anda memiliki promo Buy X Get Y yang aktif. Ingin pilih item gratis sekarang?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Pilih Sekarang',
                        cancelButtonText: 'Tidak, Hapus Promo',
                        confirmButtonColor: '#658C58',
                        cancelButtonColor: '#6b7280',
                        iconColor: '#658C58',
                        customClass: {
                            popup: 'rounded-[1.5rem] border-none shadow-2xl',
                            title: 'font-black text-gray-900',
                        },
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showFreeItemSelectionModal(activeDiscountPlan);
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            clearDiscount(false);
                            // Lanjut bayar SETELAH diskon dihapus? 
                            // Sebaiknya biarkan user klik bayar lagi agar summary terupdate dengan benar
                            showToast('info', 'Promo dikosongkan.');
                        }
                    });
                    return;
                }
            }

            if (hasTableSystem) {
                document.getElementById('serviceSelectTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total);
                setUIState('service');
            } else {
                selectedServiceType = 'take_away';
                selectedTableId = null;
                selectedTableNumber = null;
                proceedToPaymentSelection();
            }
        }

        function selectServiceType(type) {
            selectedServiceType = type;

            if (type === 'dine_in') {
                document.getElementById('tableSelectTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total);
                renderTableSelection();
                clearSelectedTable();
                setUIState('table');
            } else {
                selectedTableId = null;
                selectedTableNumber = null;
                proceedToPaymentSelection();
            }
        }

        // Table Selection for Payment Flow
        async function renderTableSelection() {
            await loadTablesData();
            const grid = document.getElementById('tableSelectionGrid');

            const availableTables = tablesData.filter(t => t.status === 'available');

            if (availableTables.length === 0) {
                grid.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-chair text-gray-300"></i>
                    </div>
                    <p class="text-sm text-gray-500">Tidak ada meja tersedia</p>
                    <button onclick="skipTableSelection()" class="mt-4 text-cuan-green font-semibold text-sm">Tetap lanjut tanpa meja</button>
                </div>
            `;
                return;
            }

            grid.innerHTML = availableTables.map(table => `
            <div class="table-select-item bg-white border-2 border-gray-200 rounded-xl p-3 text-center cursor-pointer hover:border-amber-400 hover:bg-cuan-green/5 transition-all"
                 onclick="selectTable(${table.id}, '${table.table_number}', '${table.name || ''}')">
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-sm font-bold text-emerald-700">${table.table_number}</span>
                </div>
                <p class="text-xs font-medium text-gray-900 truncate">${table.name || 'Meja ' + table.table_number}</p>
                <p class="text-[10px] text-gray-500"><i class="fas fa-users"></i> ${table.capacity}</p>
            </div>
        `).join('');
        }

        function selectTable(tableId, tableNumber, tableName) {
            selectedTableId = tableId;
            selectedTableNumber = tableNumber;

            // Update UI
            document.querySelectorAll('.table-select-item').forEach(el => {
                el.classList.remove('border-amber-500', 'bg-cuan-green/5', 'ring-2', 'ring-amber-300');
            });
            event.currentTarget.classList.add('border-amber-500', 'bg-cuan-green/5', 'ring-2', 'ring-amber-300');

            // Show selected info
            document.getElementById('selectedTableInfo').classList.remove('hidden');
            document.getElementById('selectedTableNumber').textContent = tableName ? `${tableName} (No. ${tableNumber})` : `Meja ${tableNumber}`;

            // Enable proceed button
            const btn = document.getElementById('btnProceedPayment');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        function clearSelectedTable() {
            selectedTableId = null;
            selectedTableNumber = null;

            document.querySelectorAll('.table-select-item').forEach(el => {
                el.classList.remove('border-amber-500', 'bg-cuan-green/5', 'ring-2', 'ring-amber-300');
            });

            document.getElementById('selectedTableInfo').classList.add('hidden');

            const btn = document.getElementById('btnProceedPayment');
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        function proceedToPaymentSelection() {
            // Update Service Type Badge
            const serviceBadge = document.getElementById('paymentServiceBadge');
            const serviceText = document.getElementById('paymentServiceText');
            const serviceIcon = document.getElementById('serviceIcon');

            if (serviceBadge) {
                serviceBadge.classList.remove('hidden');
                if (selectedServiceType === 'dine_in') {
                    serviceBadge.className = 'bg-cuan-green/5 border border-cuan-green/20 rounded-xl p-3 flex items-center gap-2';
                    serviceText.className = 'text-sm font-medium text-cuan-dark';
                    serviceText.textContent = 'Makan di Tempat (Dine In)';
                    serviceIcon.className = 'fas fa-utensils text-cuan-green';
                } else {
                    serviceBadge.className = 'bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-2';
                    serviceText.className = 'text-sm font-medium text-blue-800';
                    serviceText.textContent = 'Bawa Pulang (Take Away)';
                    serviceIcon.className = 'fas fa-shopping-bag text-blue-600';
                }
            }

            // Update Table Badge
            const tableBadge = document.getElementById('paymentTableBadge');
            if (tableBadge) {
                if (selectedTableId) {
                    tableBadge.classList.remove('hidden');
                    document.getElementById('paymentTableNumber').textContent = selectedTableNumber;
                } else {
                    tableBadge.classList.add('hidden');
                }
            }

            // Update Select Total
            const selectTotal = document.getElementById('selectTotal');
            if (selectTotal) {
                selectTotal.textContent = 'Rp ' + formatNumber(cartSummary.grand_total);
            }

            setUIState('select');
        }

        function skipTableSelection() {
            selectedTableId = null;
            selectedTableNumber = null;
            proceedToPaymentSelection();
        }

        function backFromPaymentSelection() {
            if (hasTableSystem) {
                if (selectedServiceType === 'dine_in') {
                    setUIState('table');
                } else {
                    setUIState('service');
                }
            } else {
                backToBrowse();
            }
        }


        // ==================== FINANCE MODAL FUNCTIONS ====================
        let currentFinanceTab = 'income';

        function openFinanceModal() {
            document.getElementById('financeModal').classList.remove('hidden');
            switchFinanceTab('income'); // Reset to income
            loadFinanceCategories();
        }

        function closeFinanceModal() {
            document.getElementById('financeModal').classList.add('hidden');
            document.getElementById('incomeForm').reset();
            document.getElementById('expenseForm').reset();
        }

        function switchFinanceTab(tab) {
            currentFinanceTab = tab;
            const incomeForm = document.getElementById('incomeForm');
            const expenseForm = document.getElementById('expenseForm');
            const incomeTabBtn = document.getElementById('tab-income');
            const expenseTabBtn = document.getElementById('tab-expense');
            const submitBtn = document.getElementById('btnSubmitFinance');
            const submitIcon = submitBtn.querySelector('i');
            const submitText = submitBtn.querySelector('span');

            if (tab === 'income') {
                incomeForm.classList.remove('hidden');
                expenseForm.classList.add('hidden');

                // Tab styling
                incomeTabBtn.className = 'flex-1 py-4 text-sm font-bold border-b-2 transition-all flex items-center justify-center gap-2 border-emerald-600 text-emerald-600 bg-white';
                expenseTabBtn.className = 'flex-1 py-4 text-sm font-bold border-b-2 transition-all flex items-center justify-center gap-2 border-transparent text-gray-500 hover:text-red-600 hover:bg-red-50/30';

                // Button styling
                submitBtn.className = 'flex-1 px-4 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:opacity-90 transition-all shadow-md flex items-center justify-center gap-2';
                submitText.textContent = 'Simpan Pemasukan';
            } else {
                incomeForm.classList.add('hidden');
                expenseForm.classList.remove('hidden');

                // Tab styling
                expenseTabBtn.className = 'flex-1 py-4 text-sm font-bold border-b-2 transition-all flex items-center justify-center gap-2 border-red-600 text-red-600 bg-white';
                incomeTabBtn.className = 'flex-1 py-4 text-sm font-bold border-b-2 transition-all flex items-center justify-center gap-2 border-transparent text-gray-500 hover:text-emerald-600 hover:bg-emerald-50/30';

                // Button styling
                submitBtn.className = 'flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-semibold hover:opacity-90 transition-all shadow-md flex items-center justify-center gap-2';
                submitText.textContent = 'Simpan Pengeluaran';
            }
        }

        function loadFinanceCategories() {
            const select = document.getElementById('finance_expense_category');
            if (select.options.length > 1) return; // Already loaded

            fetch('{{ route("finance.categories.ajax") }}')
                .then(r => r.json())
                .then(categories => {
                    categories.forEach(cat => {
                        const opt = document.createElement('option');
                        opt.value = cat.id;
                        opt.textContent = cat.name;
                        select.appendChild(opt);
                    });
                });
        }

        function submitFinanceForm() {
            const isIncome = (currentFinanceTab === 'income');
            const form = isIncome ? document.getElementById('incomeForm') : document.getElementById('expenseForm');
            const url = isIncome ? '{{ route("finance.income.store.ajax") }}' : '{{ route("finance.expense.store.ajax") }}';

            // Basic validation
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const btn = document.getElementById('btnSubmitFinance');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Menyimpan...</span>';
            btn.disabled = true;

            const formData = new FormData(form);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message);
                        closeFinanceModal();
                        // Optional: refresh other data here
                    } else {
                        showToast('error', data.message || 'Gagal menyimpan data');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('error', 'Terjadi kesalahan sistem');
                })
                .finally(() => {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                });
        }

        /**
         * Reset debt payment form
         */
        function resetDebtPaymentForm() {
            document.getElementById('debtPaymentForm').reset();
            document.getElementById('debtCustomerId').value = '';
            selectedCustomerData = null;
            hideDebtCustomerSearchResults();
        }

        /**
         * Initialize customer search with debounce
         */
        let isDebtCustomerSearchInitialized = false;
        function initDebtCustomerSearch() {
            if (isDebtCustomerSearchInitialized) return;

            const nameInput = document.getElementById('debtCustomerName');
            const phoneInput = document.getElementById('debtCustomerPhone');

            if (!nameInput || !phoneInput) return;

            nameInput.addEventListener('input', function () {
                const searchTerm = this.value.trim();
                performDebtCustomerSearch(searchTerm);
            });

            phoneInput.addEventListener('input', function () {
                const searchTerm = this.value.trim();
                performDebtCustomerSearch(searchTerm);
            });

            // Hide results when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.closest('#debtCustomerName') &&
                    !e.target.closest('#debtCustomerPhone') &&
                    !e.target.closest('#debtCustomerSearchResults')) {
                    hideDebtCustomerSearchResults();
                }
            });

            isDebtCustomerSearchInitialized = true;
        }

        /**
         * Perform debt customer search with debounce (300ms)
         */
        let debounceTimerDebtCustomer;
        function performDebtCustomerSearch(searchTerm) {
            clearTimeout(debounceTimerDebtCustomer);

            if (searchTerm.length < 2) {
                hideDebtCustomerSearchResults();
                return;
            }

            debounceTimerDebtCustomer = setTimeout(() => {
                fetch(`{{ route('pos.customer.search') }}?q=${encodeURIComponent(searchTerm)}`)
                    .then(r => r.json())
                    .then(customers => {
                        displayDebtCustomerSearchResults(customers);
                    })
                    .catch(err => {
                        console.error('Debt customer search error:', err);
                        hideDebtCustomerSearchResults();
                    });
            }, 300);
        }

        /**
         * Display customer search results
         */
        function displayDebtCustomerSearchResults(customers) {
            const resultsContainer = document.getElementById('debtCustomerSearchResults');

            if (customers.length === 0) {
                hideDebtCustomerSearchResults();
                return;
            }

            let html = '';
            customers.forEach(customer => {
                html += `
                <div class="customer-search-item" onclick="selectDebtCustomer(${JSON.stringify(customer).replace(/"/g, '&quot;')})">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">${customer.name}</div>
                            <div class="text-sm text-gray-600">${customer.phone}</div>
                            ${customer.email ? `<div class="text-xs text-gray-500">${customer.email}</div>` : ''}
                        </div>
                        <div class="text-right">
                            <div class="text-xs px-2 py-1 rounded-full ${getCustomerTypeBadgeClass(customer.type)}">
                                ${getCustomerTypeLabel(customer.type)}
                            </div>
                            ${customer.total_debt > 0 ? `
                                <div class="text-xs text-red-600 mt-1">
                                    Utang: Rp ${formatNumber(customer.total_debt)}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
            });

            resultsContainer.innerHTML = html;
            resultsContainer.classList.remove('hidden');
        }

        /**
         * Hide customer search results
         */
        function hideDebtCustomerSearchResults() {
            const resultsContainer = document.getElementById('debtCustomerSearchResults');
            if (resultsContainer) {
                resultsContainer.classList.add('hidden');
                resultsContainer.innerHTML = '';
            }
        }

        /**
         * Select customer from search results
         */
        function selectDebtCustomer(customer) {
            selectedCustomerData = customer;

            const inputId = document.getElementById('debtCustomerId');
            const inputName = document.getElementById('debtCustomerName');
            const inputPhone = document.getElementById('debtCustomerPhone');
            const inputEmail = document.getElementById('debtCustomerEmail');
            const inputAddress = document.getElementById('debtCustomerAddress');
            const inputType = document.getElementById('debtCustomerType');
            const inputTypeHidden = document.getElementById('debtCustomerTypeHidden');
            const inputCreditLimit = document.getElementById('debtCreditLimit');

            if (inputId) inputId.value = customer.id;
            if (inputName) {
                inputName.value = customer.name;
                inputName.readOnly = true;
                inputName.classList.add('bg-gray-100');
            }
            if (inputPhone) {
                inputPhone.value = customer.phone;
                inputPhone.readOnly = true;
                inputPhone.classList.add('bg-gray-100');
            }
            if (inputEmail) {
                inputEmail.value = customer.email || '';
                inputEmail.readOnly = true;
                inputEmail.classList.add('bg-gray-100');
            }
            if (inputAddress) inputAddress.value = customer.address || '';

            if (inputType) inputType.value = customer.type || 'regular';
            if (inputTypeHidden) inputTypeHidden.value = customer.type || 'regular';

            if (inputCreditLimit) inputCreditLimit.value = customer.credit_limit || '';

            hideDebtCustomerSearchResults();

            showToast('success', `Pelanggan ${customer.name} dipilih`);
        }

        /**
         * Submit debt payment
         */
        function submitDebtPayment() {
            const form = document.getElementById('debtPaymentForm');
            const formData = new FormData(form);

            // Validate required fields
            if (!formData.get('customer_name') || !formData.get('customer_phone')) {
                showToast('error', 'Nama dan nomor telepon wajib diisi');
                return;
            }

            // Convert FormData to JSON
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            // Add service type and table id
            data.service_type = selectedServiceType;
            data.table_id = selectedTableId;

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("debt.process") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
                .then(async r => {
                    if (!r.ok) {
                        const text = await r.text();
                        console.error('Debt payment bad response:', r.status, text);
                        throw new Error('Server responded with status ' + r.status);
                    }
                    return r.json();
                })
                .then(async responseData => {
                    Swal.close();

                    if (responseData.success) {
                        // Update stock on frontend
                        if (responseData.sale && responseData.sale.items) {
                            updateProductStockFromSaleItems(responseData.sale.items);
                        }
                        if (activeDiscountPlan && activeDiscountPlan.discount_type === 'buy_x_get_y') {
                            updateProductStockWithFreeItems(activeDiscountPlan);
                        }

                        refreshAllProductStocks();

                        // Clear cart
                        await fetch('{{ route("pos.cart.clear") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        cart = {};
                        cartSummary = { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0, total_items: 0 };
                        activeDiscountPlan = null;
                        renderCart();

                        closeDebtPaymentModal();
                        setUIState('browse');
                        clearCustomer(true);

                        // Check if there are non-stock items that need production
                        const hasNonStockItems = responseData.sale.items && responseData.sale.items.some(item => !item.is_stock);

                        if (hasNonStockItems) {
                            Swal.fire({
                                title: '<h3 class="text-xl font-extrabold text-gray-900">Produksi Sekarang?</h3>',
                                html: '<p class="text-sm text-gray-500">Terdapat produk yang perlu dimasak/diproduksi. Kirim pesanan ke dapur sekarang?</p>',
                                icon: 'question',
                                iconColor: '#658C58',
                                showCancelButton: true,
                                confirmButtonText: '<i class="fas fa-play mr-2"></i> Ya, Produksi Sekarang',
                                cancelButtonText: 'Tidak, Nanti Saja',
                                customClass: {
                                    popup: 'rounded-[1.5rem] border-none shadow-2xl',
                                    confirmButton: 'bg-cuan-green hover:bg-cuan-dark text-white rounded-xl px-6 py-3 text-sm font-bold shadow-lg shadow-emerald-200 border-none transition-all active:scale-95 mx-2',
                                    cancelButton: 'bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-xl px-6 py-3 text-sm font-bold border-none transition-all mx-2',
                                    actions: 'mt-6',
                                },
                                buttonsStyling: false,
                                reverseButtons: true,
                                allowOutsideClick: false
                            }).then((prodResult) => {
                                if (prodResult.isConfirmed) {
                                    // Trigger production (handles auto_production status updates)
                                    fetch(`/debt/${responseData.sale.id}/produce`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                        .then(r => r.json())
                                        .then(data => {
                                            if (data.success) {
                                                showToast('success', data.message);
                                            } else {
                                                showToast('error', data.message);
                                            }
                                        }).catch(err => console.error('Failed to trigger production:', err));
                                } else if (prodResult.dismiss === Swal.DismissReason.cancel) {
                                    // Mark as waiting
                                    fetch(`/debt/${responseData.sale.id}/wait`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                        .then(r => r.json())
                                        .then(waitResponse => {
                                            if (waitResponse.success) {
                                                showToast('info', waitResponse.message);
                                            }
                                        })
                                        .catch(err => console.error('Failed to mark as waiting:', err));
                                }

                                // Always show success modal after confirmation
                                openPaymentSuccessModal({
                                    sale_id: responseData.sale.id,
                                    invoice_number: responseData.sale.invoice_number,
                                    created_at: responseData.sale.created_at,
                                    grand_total: responseData.sale.grand_total,
                                    debt_amount: responseData.sale.debt_amount,
                                    customer_name: responseData.sale.customer_name
                                });
                            });
                        } else {
                            openPaymentSuccessModal({
                                sale_id: responseData.sale.id,
                                invoice_number: responseData.sale.invoice_number,
                                created_at: responseData.sale.created_at,
                                grand_total: responseData.sale.grand_total,
                                debt_amount: responseData.sale.debt_amount,
                                customer_name: responseData.sale.customer_name
                            });
                        }
                    } else {
                        showToast('error', responseData.message);
                    }
                })
                .catch(err => {
                    Swal.close();
                    console.error('Debt payment error:', err);
                    showToast('error', 'Terjadi kesalahan saat memproses utang');
                });
        }

        /**
         * Helper: Get customer type badge class
         */
        function getCustomerTypeBadgeClass(type) {
            const classes = {
                'regular': 'bg-gray-100 text-gray-700',
                'reseller': 'bg-blue-100 text-blue-700',
                'vip': 'bg-purple-100 text-purple-700'
            };
            return classes[type] || classes['regular'];
        }

        /**
         * Helper: Get customer type label
         */
        function getCustomerTypeLabel(type) {
            const labels = {
                'regular': 'Regular',
                'reseller': 'Reseller',
                'vip': 'VIP'
            };
            return labels[type] || 'Regular';
        }

        // Helper: Select Card
        function selectCardOption(el, id, name, accNum, accName, qrUrl) {
            // 1. Highlight Selection
            document.querySelectorAll('.payment-card-option').forEach(opt => {
                opt.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50', 'scale-95');
                opt.classList.add('border-gray-200', 'bg-white');
            });

            // Add active styles
            el.classList.remove('border-gray-200', 'bg-white');
            el.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50', 'scale-95');

            // 2. Set Hidden Values
            document.getElementById('selectedCardId').value = id;
            document.getElementById('selectedCardName').value = name;

            // 3. Populate Details
            document.getElementById('detailMethodName').textContent = name;

            const accInfoDiv = document.getElementById('detailAccountInfo');
            const accNumEl = document.getElementById('detailAccNumber');
            const accNameEl = document.getElementById('detailAccName');

            if (accNum) {
                accInfoDiv.classList.remove('hidden');
                accNumEl.textContent = accNum;
                accNameEl.textContent = accName || '';
            } else {
                accInfoDiv.classList.add('hidden');
            }

            // 4. Handle QR
            const qrContainer = document.getElementById('detailQrContainer');
            const qrImg = document.getElementById('detailQrImage');

            if (qrUrl) {
                qrContainer.classList.remove('hidden');
                qrImg.src = qrUrl;

                // Scroll to QR if visible
                setTimeout(() => {
                    qrContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            } else {
                qrContainer.classList.add('hidden');
                qrImg.src = '';
            }

            // 5. Show Detail Section & Actions with animation
            const detailsSection = document.getElementById('cardPaymentDetails');
            const actionsSection = document.getElementById('cardPaymentActions');

            detailsSection.classList.remove('hidden');
            actionsSection.classList.remove('hidden');

            // Simple fade in effect
            detailsSection.style.animation = 'fadeIn 0.3s ease-out';
            actionsSection.style.animation = 'fadeIn 0.3s ease-out 0.1s both';
        }

        function processTransferPayment() {
            const cardId = document.getElementById('selectedCardId').value;
            const cardName = document.getElementById('selectedCardName').value;
            const ref = document.getElementById('transferReference').value;

            if (!cardId) {
                showToast('warning', 'Pilih kartu pembayaran');
                return;
            }

            const btn = document.getElementById('btn-process-transfer');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...`;

            fetch('{{ route("payment.transfer") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    transfer_method: cardName,
                    outlet_payment_link_id: cardId,
                    reference_number: ref,
                    service_type: selectedServiceType,
                    table_id: selectedTableId
                })
            })
                .then(r => r.json())
                .then(async data => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    if (data.success) {
                        if (data.sale && data.sale.items) {
                            updateProductStockFromSaleItems(data.sale.items);
                        }
                        // PERBAIKAN: Update stock dengan free items untuk BOGO
                        if (activeDiscountPlan && activeDiscountPlan.discount_type === 'buy_x_get_y') {
                            updateProductStockWithFreeItems(activeDiscountPlan);
                        }

                        refreshAllProductStocks();

                        await fetch('{{ route("pos.cart.clear") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });

                        // PERBAIKAN: Reset semua state
                        cart = {};
                        cartSummary = { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0, total_items: 0 };
                        activeDiscountPlan = null; // ⬅️ TAMBAHKAN INI

                        renderCart();
                        setUIState('browse');
                        clearCustomer();
                        openPaymentSuccessModal({
                            sale_id: data.sale.id,
                            invoice_number: data.sale.invoice_number,
                            created_at: data.sale.created_at,
                            grand_total: data.sale.grand_total
                        });
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        showToast('error', data.message);
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    showToast('error', 'Gagal proses pembayaran');
                });
        }

        function openMidtransPayment() {
            if (!cart || Object.keys(cart).length === 0) {
                showToast('warning', 'Keranjang kosong');
                return;
            }
            const btn = document.getElementById('btn-process-midtrans');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Menghubungkan...`;

            fetch('{{ route("payment.midtrans.token") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    service_type: selectedServiceType,
                    table_id: selectedTableId
                })
            })
                .then(r => r.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    if (!data.success) {
                        showToast('error', data.message || 'Gagal membuat token Midtrans');
                        return;
                    }

                    snap.pay(data.snap_token, {
                        onSuccess: function () {
                            fetch('/api/sale/' + data.sale_id)
                                .then(res => res.json())
                                .then(async saleData => {
                                    if (saleData.items) {
                                        updateProductStockFromSaleItems(saleData.items);
                                    }
                                    // PERBAIKAN: Update stock dengan free items untuk BOGO
                                    if (activeDiscountPlan && activeDiscountPlan.discount_type === 'buy_x_get_y') {
                                        updateProductStockWithFreeItems(activeDiscountPlan);
                                    }

                                    refreshAllProductStocks();

                                    await fetch('{{ route("pos.cart.clear") }}', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                    });

                                    // PERBAIKAN: Reset semua state
                                    cart = {};
                                    cartSummary = { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0, total_items: 0 };
                                    activeDiscountPlan = null; // ⬅️ TAMBAHKAN INI

                                    renderCart();
                                    setUIState('browse');
                                    clearCustomer();
                                    openPaymentSuccessModal({
                                        sale_id: saleData.id,
                                        invoice_number: saleData.invoice_number,
                                        created_at: saleData.created_at,
                                        grand_total: saleData.grand_total,
                                        customer_name: saleData.customer_name
                                    });
                                })
                                .catch(() => showToast('error', 'Pembayaran berhasil, tapi gagal mengambil data transaksi'));
                        },
                        onPending: function () { showToast('info', 'Menunggu pembayaran'); },
                        onError: function () { showToast('error', 'Pembayaran via Midtrans gagal'); },
                        onClose: function () { showToast('info', 'Jendela pembayaran ditutup'); }
                    });
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    showToast('error', 'Gagal membuat token');
                });
        }

        function updateProductStockFromSaleItems(items) {
            if (!items || !items.length) return;
            items.forEach(item => {
                const wrap = document.querySelector(`.product-stock[data-product-id="${item.product_id}"]`);
                if (!wrap) return;
                const qtySpan = wrap.querySelector('.stock-qty');
                let currentQty = parseInt((qtySpan.textContent || '0').replace(/\./g, '')) || 0;
                let newQty = currentQty - item.quantity;
                if (newQty < 0) newQty = 0;
                qtySpan.textContent = formatNumber(newQty);
                wrap.classList.toggle('text-green-600', newQty > 0);
                wrap.classList.toggle('text-red-600', newQty <= 0);
            });
        }

        // PERBAIKAN: Fungsi baru untuk update stock dengan free items BOGO
        function updateProductStockWithFreeItems(discountPlan) {
            if (!discountPlan || discountPlan.discount_type !== 'buy_x_get_y') return;
            if (!discountPlan.affected_items || !discountPlan.affected_items.length) return;

            discountPlan.affected_items.forEach(item => {
                const freeQty = item.free_qty || 0;
                if (freeQty <= 0) return;

                const wrap = document.querySelector(`.product-stock[data-product-id="${item.product_id}"]`);
                if (!wrap) return;

                const qtySpan = wrap.querySelector('.stock-qty');
                let currentQty = parseInt((qtySpan.textContent || '0').replace(/\./g, '')) || 0;
                let newQty = currentQty - freeQty;
                if (newQty < 0) newQty = 0;

                qtySpan.textContent = formatNumber(newQty);
                wrap.classList.toggle('text-green-600', newQty > 0);
                wrap.classList.toggle('text-red-600', newQty <= 0);
            });
        }

        function refreshAllProductStocks() {
            fetch('{{ route("pos.products.stocks") }}')
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.stocks) {
                        data.stocks.forEach(s => {
                            const wrap = document.querySelector(`.product-stock[data-product-id="${s.product_id}"]`);
                            if (!wrap) return;

                            const qtySpan = wrap.querySelector('.stock-qty');
                            if (qtySpan) {
                                qtySpan.textContent = formatNumber(Math.floor(s.stock));
                                if (s.is_produced) {
                                    wrap.classList.toggle('text-blue-600', s.stock > 0);
                                    wrap.classList.toggle('text-red-400', s.stock <= 0);
                                    wrap.classList.remove('text-green-600', 'text-red-600');
                                } else {
                                    wrap.classList.toggle('text-green-600', s.stock > 0);
                                    wrap.classList.toggle('text-red-600', s.stock <= 0);
                                    wrap.classList.remove('text-blue-600', 'text-red-400');
                                }
                            }

                            // Update dataset for product card
                            const card = document.querySelector(`.product-card[data-product-id="${s.product_id}"]`);
                            if (card) {
                                card.dataset.estimatedStock = s.stock;
                            }
                        });
                    }
                })
                .catch(err => console.error('Failed to refresh stocks:', err));
        }

        function openPaymentSuccessModal(data) {
            currentSaleId = data.sale_id || data.id;
            document.getElementById('successInvoiceNumber').textContent = data.invoice_number || '-';
            document.getElementById('successDate').textContent = formatDateTime(data.created_at || new Date());
            document.getElementById('successTotal').textContent = 'Rp ' + formatNumber(data.grand_total || 0);
            if (data.change_amount && data.change_amount > 0) {
                document.getElementById('successChangeRow').style.display = 'flex';
                document.getElementById('successChange').textContent = 'Rp ' + formatNumber(data.change_amount);
            } else {
                document.getElementById('successChangeRow').style.display = 'none';
            }

            // Utang
            const debtRow = document.getElementById('successDebtRow');
            if (debtRow) {
                if (data.debt_amount && data.debt_amount > 0) {
                    debtRow.style.display = 'flex';
                    document.getElementById('successDebt').textContent = 'Rp ' + formatNumber(data.debt_amount);
                } else {
                    debtRow.style.display = 'none';
                }
            }
            // Customer
            const customerRow = document.getElementById('successCustomerRow');
            if (customerRow) {
                if (data.customer_name) {
                    customerRow.style.display = 'flex';
                    document.getElementById('successCustomer').textContent = data.customer_name;
                } else {
                    customerRow.style.display = 'none';
                }
            }

            // Reseller Sync
            const resellerSyncRow = document.getElementById('successResellerSyncRow');
            if (resellerSyncRow) {
                if (data.sale && data.sale.reseller_sync) {
                    resellerSyncRow.style.display = 'flex';
                } else {
                    resellerSyncRow.style.display = 'none';
                }
            }

            document.getElementById('paymentSuccessModal').classList.remove('hidden');
        }

        function closePaymentSuccessModal() {
            document.getElementById('paymentSuccessModal').classList.add('hidden');
            currentSaleId = null;

            // PERBAIKAN: Pastikan discount info benar-benar hilang
            activeDiscountPlan = null;
            renderCart();
        }

        function printReceipt() {
            if (!currentSaleId) {
                showToast('error', 'Sale ID tidak ditemukan');
                return;
            }
            window.open('/receipt/print/' + currentSaleId, '_blank');
        }

        /**
         * Handle Invoice Printing Logic
         */
        function handlePrintInvoice() {
            if (!currentSaleId) {
                showToast('error', 'Sale ID tidak ditemukan');
                return;
            }

            // Show loading state
            Swal.fire({
                title: 'Memuat data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch sale data to get customer info even after session clear
            fetch('/api/sale/' + currentSaleId)
                .then(r => r.json())
                .then(data => {
                    Swal.close();

                    // Reset form first
                    document.getElementById('invoiceDetailsForm').reset();

                    // Check if customer exists in sale record
                    if (data.customer_id && data.customer) {
                        document.getElementById('inv_customer_name').value = data.customer.name || '';
                        document.getElementById('inv_customer_phone').value = data.customer.phone || '';
                        document.getElementById('inv_customer_address').value = data.customer.address || '';
                    } else if (data.customer_name) {
                        // Handle case where custom name was saved but not linked to customer ID (if any)
                        document.getElementById('inv_customer_name').value = data.customer_name;
                    }

                    // Handle due date if it's a debt sale
                    if (data.payment_method === 'debt' && data.debt) {
                        document.getElementById('inv_due_date').value = data.debt.due_date ? data.debt.due_date.substring(0, 10) : '';
                    }

                    openInvoiceDetailsModal();
                })
                .catch(err => {
                    Swal.close();
                    console.error('Fetch sale error:', err);
                    showToast('error', 'Gagal memuat detail transaksi');
                    openInvoiceDetailsModal(); // Still open modal so user can fill manually
                });
        }

        function openInvoiceDetailsModal() {
            document.getElementById('invoiceDetailsModal').classList.remove('hidden');
        }

        function closeInvoiceDetailsModal() {
            document.getElementById('invoiceDetailsModal').classList.add('hidden');
            document.getElementById('invoiceDetailsForm').reset();
        }

        function submitInvoiceDetails() {
            if (!currentSaleId) return;

            const name = document.getElementById('inv_customer_name').value;
            const phone = document.getElementById('inv_customer_phone').value;
            const address = document.getElementById('inv_customer_address').value;
            const dueDate = document.getElementById('inv_due_date').value;

            if (!name) {
                showToast('warning', 'Nama pelanggan wajib diisi');
                return;
            }

            // Create a hidden form to submit via POST to open in new tab
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/receipt/invoice/' + currentSaleId + '/print';
            form.target = '_blank';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const inputs = [
                { name: 'customer_name', value: name },
                { name: 'customer_phone', value: phone },
                { name: 'customer_address', value: address },
                { name: 'due_date', value: dueDate }
            ];

            inputs.forEach(input => {
                const el = document.createElement('input');
                el.type = 'hidden';
                el.name = input.name;
                el.value = input.value;
                form.appendChild(el);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            closeInvoiceDetailsModal();
        }

        function downloadReceipt() {
            if (!currentSaleId) {
                showToast('error', 'Sale ID tidak ditemukan');
                return;
            }
            showToast('info', 'Memproses download...');
            const a = document.createElement('a');
            a.href = '/receipt/download/' + currentSaleId;
            a.download = '';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(() => showToast('success', 'Struk berhasil didownload!'), 500);
        }

        // ==================== KEYBOARD SHORTCUTS ====================
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('productSettingsModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeProductSettingsModal();
                }
                const calcModal = document.getElementById('calculatorModal');
                if (calcModal && !calcModal.classList.contains('hidden')) {
                    closeCalculator();
                    return;
                }
                const successModal = document.getElementById('paymentSuccessModal');
                if (successModal && !successModal.classList.contains('hidden')) {
                    closePaymentSuccessModal();
                    return;
                }

                const openingModal = document.getElementById('openingAmountModal');
                if (openingModal && !openingModal.classList.contains('hidden')) {
                    closeOpeningAmountModal();
                    return;
                }

                if (UI_STATE === 'cash' || UI_STATE === 'transfer' || UI_STATE === 'midtrans') {
                    setUIState('select');
                } else if (UI_STATE === 'select') {
                    setUIState('browse');
                }
            }

            if (e.key === 'Enter' && UI_STATE === 'cash') {
                const cashInput = document.getElementById('cashPaidAmount');
                if (document.activeElement === cashInput) {
                    e.preventDefault();
                    processCashPayment();
                }
            }

            if (e.key === 'Enter') {
                const openingInput = document.getElementById('openingAmountInput');
                if (document.activeElement === openingInput) {
                    e.preventDefault();
                    submitOpeningAmount();
                }
            }
        });

        // Keyboard untuk kalkulator
        document.addEventListener('keydown', function (e) {
            const modal = document.getElementById('calculatorModal');
            if (!modal || modal.classList.contains('hidden')) return;
            if (e.key >= '0' && e.key <= '9') {
                e.preventDefault();
                calcNumber(e.key);
            } else if (e.key === '.') {
                e.preventDefault();
                calcDecimal();
            } else if (e.key === '+' || e.key === '-' || e.key === '*' || e.key === '/') {
                e.preventDefault();
                calcOperator(e.key);
            } else if (e.key === 'Enter' || e.key === '=') {
                e.preventDefault();
                calcEquals();
            } else if (e.key === 'Backspace') {
                e.preventDefault();
                calcDelete();
            } else if (e.key === 'Delete' || e.key.toLowerCase() === 'c') {
                e.preventDefault();
                calcClear();
            }
        });

        // ==================== PRODUCT SETTINGS FUNCTIONS ====================
        let productSettings = {
            hideOutOfStock: false,
            sortBy: 'default',
            hiddenProducts: [],
            hideNavbar: false // Default false
        };

        function openProductSettingsModal() {
            document.getElementById('productSettingsModal').classList.remove('hidden');
            loadProductSettings();
        }

        function closeProductSettingsModal() {
            document.getElementById('productSettingsModal').classList.add('hidden');
        }

        /* -------------------------------------------------------------------------- */
        /*                        PENDING PRODUCTION (KITCHEN QUEUE)                  */
        /* -------------------------------------------------------------------------- */

        async function openPendingProductionModal() {
            document.getElementById('pendingProductionModal').classList.remove('hidden');
            await refreshPendingProduction();
        }

        function closePendingProductionModal() {
            document.getElementById('pendingProductionModal').classList.add('hidden');
        }

        async function refreshPendingProduction() {
            const spinner = document.getElementById('refreshSpinner');
            const list = document.getElementById('pendingProductionList');
            const loading = document.getElementById('pendingProductionLoading');
            const empty = document.getElementById('pendingProductionEmpty');

            if (spinner) spinner.classList.add('animate-spin');
            loading.classList.remove('hidden');
            list.classList.add('hidden');
            empty.classList.add('hidden');

            try {
                const response = await fetch('{{ route("pos.pending-production") }}');
                const data = await response.json();

                if (data.success && data.sales && data.sales.length > 0) {
                    renderPendingProductionList(data.sales);
                    list.classList.remove('hidden');
                    loading.classList.add('hidden');
                } else {
                    empty.classList.remove('hidden');
                    loading.classList.add('hidden');
                }
            } catch (err) {
                console.error('Failed to fetch pending production:', err);
                showToast('error', 'Gagal memuat antrean dapur');
                empty.classList.remove('hidden');
                loading.classList.add('hidden');
            } finally {
                if (spinner) spinner.classList.remove('animate-spin');
            }
        }

        function renderPendingProductionList(sales) {
            const list = document.getElementById('pendingProductionList');
            list.innerHTML = '';

            sales.forEach(sale => {
                const saleEl = document.createElement('div');
                saleEl.className = 'bg-gray-50 rounded-xl overflow-hidden border border-gray-200';

                let itemsHtml = '';
                sale.items.forEach(item => {
                    const productName = item.product?.name || 'Produk Tidak Dikenal';
                    const unitName = item.product?.unit?.name || 'Pcs';
                    const quantity = item.quantity || 0;
                    const notes = item.notes || '';
                    const isWaiting = item.production_status === 'waiting';

                    itemsHtml += `
                    <div class="flex justify-between items-start py-2 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <div class="text-sm font-bold text-gray-800">
                                ${productName}
                                ${isWaiting ? '<span class="text-[10px] font-bold bg-cuan-green/20 text-cuan-green px-1.5 py-0.5 rounded ml-2">Tunda</span>' : ''}
                            </div>
                            ${notes ? `<div class="text-xs text-cuan-green">Note: ${notes}</div>` : ''}
                        </div>
                        <div class="text-[10px] font-black bg-cuan-green/10 text-cuan-green px-2 py-0.5 rounded-full uppercase tracking-tighter">
                            ${quantity} ${unitName}
                        </div>
                    </div>
                `;
                });

                saleEl.innerHTML = `
                <div class="p-4 bg-white border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-cuan-green bg-cuan-green/10 px-2 py-0.5 rounded uppercase tracking-widest">${sale.invoice_number}</span>
                            <span class="text-xs text-gray-400 font-medium">${sale.created_at ? new Date(sale.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) : '--:--'}</span>
                        </div>
                        <div class="text-sm font-bold text-gray-800 mt-1">${sale.customer ? sale.customer.name : 'Pelanggan Umum'}</div>
                    </div>
                    <button onclick="ringKitchenBell(${sale.id}, this)" class="w-10 h-10 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-cuan-green/10 hover:text-cuan-green transition-all active:scale-95 shadow-sm border border-gray-100 group">
                        <i class="fas fa-bell group-hover:animate-shake"></i>
                    </button>
                </div>
                <div class="p-4 bg-gray-50/50">
                    ${itemsHtml}
                </div>
            `;
                list.appendChild(saleEl);
            });
        }

        function ringKitchenBell(saleId, button) {
            const icon = button.querySelector('i');

            // Visual feedback (animation)
            icon.classList.add('animate-shake');
            button.classList.add('bg-blue-600', 'text-white');
            button.classList.remove('bg-blue-50', 'text-blue-600');

            fetch(`/pos/notify-kitchen/${saleId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Optional: minimal success feedback
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(err => {
                    console.error('Failed to ring bell:', err);
                })
                .finally(() => {
                    // Keep animation for a bit
                    setTimeout(() => {
                        icon.classList.remove('animate-shake');
                        button.classList.remove('bg-blue-600', 'text-white');
                        button.classList.add('bg-blue-50', 'text-blue-600');
                    }, 1000);
                });
        }


        function loadProductSettings() {
            const saved = localStorage.getItem('pos_product_settings');
            if (saved) {
                try {
                    productSettings = JSON.parse(saved);
                    document.getElementById('hideOutOfStock').checked = productSettings.hideOutOfStock;
                    document.getElementById('sortProducts').value = productSettings.sortBy;

                    // Load Navbar Setting
                    const hideNavbar = productSettings.hideNavbar || false;
                    document.getElementById('hideNavbarToggle').checked = hideNavbar;
                    toggleNavbarVisibility(hideNavbar, false); // false = jangan show toast saat load awal

                    if (productSettings.hiddenProducts && productSettings.hiddenProducts.length > 0) {
                        document.getElementById('enableProductHiding').checked = true;
                        toggleProductListVisibility();

                        // Update toggle states
                        productSettings.hiddenProducts.forEach(productId => {
                            const toggle = document.querySelector(`.product-visibility-toggle[data-product-id="${productId}"]`);
                            if (toggle) toggle.checked = false;
                        });
                    }
                } catch (e) {
                    console.error('Failed to load product settings:', e);
                }
            }
        }

        function saveProductSettings() {
            localStorage.setItem('pos_product_settings', JSON.stringify(productSettings));
        }

        function toggleNavbarVisibility(hide, showNotification = true) {
            // Global Alpine.js Toggle
            window.dispatchEvent(new CustomEvent('toggle-fullscreen', { detail: hide }));

            if (hide) {
                document.body.classList.add('pos-fullscreen');
            } else {
                document.body.classList.remove('pos-fullscreen');
            }

            productSettings.hideNavbar = hide;
            saveProductSettings();

            if (showNotification) {
                showToast('success', hide ? 'Navbar disembunyikan' : 'Navbar ditampilkan');
            }
        }

        function toggleProductListVisibility() {
            const checkbox = document.getElementById('enableProductHiding');
            const list = document.getElementById('productVisibilityList');

            if (checkbox.checked) {
                list.classList.remove('hidden');
            } else {
                list.classList.add('hidden');
                // Reset all toggles to checked
                document.querySelectorAll('.product-visibility-toggle').forEach(toggle => {
                    toggle.checked = true;
                });
                productSettings.hiddenProducts = [];
                saveProductSettings();
                applyProductSettings();
            }
        }

        function toggleProductVisibility(productId, isVisible) {
            if (isVisible) {
                // Remove from hidden list
                productSettings.hiddenProducts = productSettings.hiddenProducts.filter(id => id !== productId);
            } else {
                // Add to hidden list
                if (!productSettings.hiddenProducts.includes(productId)) {
                    productSettings.hiddenProducts.push(productId);
                }
            }

            saveProductSettings();
            applyProductSettings();
        }



        function openQrFullscreen(src) {
            const modal = document.getElementById('qrFullscreenModal');
            const img = document.getElementById('qrFullscreenImage');
            if (modal && img && src) {
                img.src = src;
                modal.classList.remove('hidden');
                // Prevent scrolling on body
                document.body.style.overflow = 'hidden';
            }
        }

        function closeQrFullscreen() {
            const modal = document.getElementById('qrFullscreenModal');
            if (modal) {
                modal.classList.add('hidden');
                // Restore scrolling
                document.body.style.overflow = '';
            }

            // Clean src after closing
            setTimeout(() => {
                const img = document.getElementById('qrFullscreenImage');
                if (img) img.src = '';
            }, 300);
        }

        // Close fullscreen on ESC
        document.addEventListener('keydown', function (event) {
            if (event.key === "Escape") {
                closeQrFullscreen();
            }
        });
        function applyProductSettings() {
            // Save current settings
            productSettings.hideOutOfStock = document.getElementById('hideOutOfStock').checked;
            productSettings.sortBy = document.getElementById('sortProducts').value;
            saveProductSettings();

            const productGrid = document.getElementById('productGrid');
            const productCards = Array.from(document.querySelectorAll('.product-card'));

            // Apply filters
            productCards.forEach(card => {
                const productId = parseInt(card.dataset.productId);
                let shouldShow = true;

                // Check if manually hidden
                if (productSettings.hiddenProducts.includes(productId)) {
                    shouldShow = false;
                }

                // Check out of stock filter
                if (shouldShow && productSettings.hideOutOfStock) {
                    const stockEl = card.querySelector('.product-stock .stock-qty');
                    if (stockEl) {
                        const stockQty = parseInt(stockEl.textContent.replace(/\./g, '')) || 0;
                        if (stockQty <= 0) {
                            shouldShow = false;
                        }
                    }
                }

                card.style.display = shouldShow ? '' : 'none';
            });

            // Apply sorting
            const visibleCards = productCards.filter(card => card.style.display !== 'none');

            visibleCards.sort((a, b) => {
                switch (productSettings.sortBy) {
                    case 'name-asc':
                        return a.dataset.productName.localeCompare(b.dataset.productName);
                    case 'name-desc':
                        return b.dataset.productName.localeCompare(a.dataset.productName);
                    case 'price-asc':
                        return parseFloat(a.dataset.productPrice) - parseFloat(b.dataset.productPrice);
                    case 'price-desc':
                        return parseFloat(b.dataset.productPrice) - parseFloat(a.dataset.productPrice);
                    case 'discount':
                        const aHasDiscount = a.querySelector('.discount-badge') ? 1 : 0;
                        const bHasDiscount = b.querySelector('.discount-badge') ? 1 : 0;
                        return bHasDiscount - aHasDiscount;
                    default:
                        return parseInt(a.dataset.productId) - parseInt(b.dataset.productId);
                }
            });

            // Re-append in sorted order
            visibleCards.forEach(card => productGrid.appendChild(card));

            // Update empty state
            const visibleCount = visibleCards.length;
            updateEmptyState(productGrid, visibleCount, '', '');

            showToast('success', 'Pengaturan produk diterapkan');
        }

        function resetProductSettings() {
            Swal.fire({
                title: 'Reset Pengaturan?',
                text: "Semua pengaturan produk akan dikembalikan ke default",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
                iconColor: '#dc2626',
                customClass: {
                    popup: 'rounded-[1.5rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    productSettings = {
                        hideOutOfStock: false,
                        sortBy: 'default',
                        hiddenProducts: []
                    };

                    localStorage.removeItem('pos_product_settings');

                    document.getElementById('hideOutOfStock').checked = false;
                    document.getElementById('sortProducts').value = 'default';
                    document.getElementById('enableProductHiding').checked = false;
                    toggleProductListVisibility();

                    // Reset all toggles
                    document.querySelectorAll('.product-visibility-toggle').forEach(toggle => {
                        toggle.checked = true;
                    });

                    // Show all products
                    document.querySelectorAll('.product-card').forEach(card => {
                        card.style.display = '';
                    });

                    // Reset order to default (by ID)
                    const productGrid = document.getElementById('productGrid');
                    const productCards = Array.from(document.querySelectorAll('.product-card'));
                    productCards.sort((a, b) => parseInt(a.dataset.productId) - parseInt(b.dataset.productId));
                    productCards.forEach(card => productGrid.appendChild(card));

                    applyProductSettings();
                    showToast('success', 'Pengaturan direset ke default');
                }
            });
        }

        // ==================== SALES TODAY MODAL FUNCTIONS ====================
        function openSalesTodayModal() {
            document.getElementById('salesTodayModal').classList.remove('hidden');
            loadSalesToday();
        }

        function closeSalesTodayModal() {
            document.getElementById('salesTodayModal').classList.add('hidden');
        }

        function loadSalesToday() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('salesTodayDate').textContent = formatDate(today);

            fetch(`{{ route('sales.daily') }}?date=${today}`)
                .then(r => r.json())
                .then(data => {
                    // Update summary cards
                    document.getElementById('modalTotalTransactions').textContent = data.summary.transactions;
                    document.getElementById('modalTotalRevenue').textContent = 'Rp ' + formatNumber(data.summary.revenue);
                    document.getElementById('modalCashTotal').textContent = 'Rp ' + formatNumber(data.totals.cash);

                    const nonCash = data.totals.qris + data.totals.transfer;
                    document.getElementById('modalNonCashTotal').textContent = 'Rp ' + formatNumber(nonCash);

                    // ✅ Update card Total Piutang
                    document.getElementById('modalDebtTotal').textContent = 'Rp ' + formatNumber(data.totals.debt || 0);
                    document.getElementById('modalDebtPaid').textContent = 'Rp ' + formatNumber(data.totals.debt_paid || 0);

                    // Render table
                    const tbody = document.getElementById('salesTableBody');
                    if (data.sales.length === 0) {
                        tbody.innerHTML = `
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>Belum ada transaksi hari ini</p>
                            </td>
                        </tr>
                    `;
                        return;
                    }

                    let html = '';
                    data.sales.forEach(sale => {
                        const paymentBadge = getPaymentBadge(sale.payment_method);
                        const statusBadge = sale.status === 'completed'
                            ? '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Selesai</span>'
                            : '<span class="badge badge-danger"><i class="fas fa-undo mr-1"></i>Refund</span>';

                        const refundButton = (window.permissions.refundPenjualan && sale.status === 'completed' && (sale.payment_method === 'cash' || sale.payment_method === 'transfer'))
                            ? `<button onclick="confirmRefund(${sale.id})" class="px-3 py-1.5 bg-red-500 text-white rounded-lg text-xs font-semibold hover:bg-red-600 transition-colors">
                            <i class="fas fa-undo mr-1"></i>Refund
                        </button>`
                            : '-';

                        const customerName = sale.customer_name || (sale.customer ? sale.customer.name : 'Umum');

                        let totalHtml = `Rp ${formatNumber(sale.grand_total)}`;
                        if (sale.payment_method === 'debt') {
                            const paid = sale.paid_amount || 0;
                            const remaining = sale.remaining_amount || (sale.grand_total - paid);

                            totalHtml = `
                            <div>
                                <div class="font-bold text-gray-900">Rp ${formatNumber(sale.grand_total)}</div>
                                <div class="text-xs text-green-600 mt-1">Bayar: Rp ${formatNumber(paid)}</div>
                                <div class="text-xs text-red-600 font-semibold">Sisa: Rp ${formatNumber(remaining)}</div>
                            </div>
                        `;
                        }

                        const detailButton = window.permissions.lihatDetailPenjualan
                            ? `<button onclick="showSaleDetail(${sale.id})" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors border border-blue-200">
                            <i class="fas fa-eye mr-1"></i>Detail
                           </button>`
                            : '';

                        html += `
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">${sale.invoice_number}</td>
                            <td class="px-4 py-3">${sale.time}</td>
                            <td class="px-4 py-3">${customerName}</td>
                            <td class="px-4 py-3">${sale.cashier || '-'}</td>
                            <td class="px-4 py-3">${paymentBadge}</td>
                            <td class="px-4 py-3 text-right font-semibold text-red-600">Rp ${formatNumber(sale.total_discount)}</td>
                            <td class="px-4 py-3 text-right">${totalHtml}</td>
                            <td class="px-4 py-3 text-center">${statusBadge}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    ${detailButton}
                                    ${refundButton}
                                </div>
                            </td>
                        </tr>
                    `;
                    });

                    tbody.innerHTML = html;
                })
                .catch(() => {
                    showToast('error', 'Gagal memuat data penjualan');
                    document.getElementById('salesTableBody').innerHTML = `
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-red-500">
                            <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                            <p>Gagal memuat data</p>
                        </td>
                    </tr>
                `;
                });
        }

        function getPaymentBadge(method) {
            const badges = {
                'cash': '<span class="badge" style="background-color: #dbeafe; color: #1e40af;"><i class="fas fa-money-bill-wave mr-1"></i>Tunai</span>',
                'transfer': '<span class="badge" style="background-color: #fef3c7; color: #92400e;"><i class="fas fa-building-columns mr-1"></i>Transfer</span>',
                'qris': '<span class="badge" style="background-color: #e9d5ff; color: #6b21a8;"><i class="fas fa-qrcode mr-1"></i>QRIS</span>',
                'debt': '<span class="badge" style="background-color: #fee2e2; color: #991b1b;"><i class="fas fa-file-invoice-dollar mr-1"></i>Utang</span>'
            };
            return badges[method] || method;
        }

        function confirmRefund(saleId) {
            Swal.fire({
                title: 'Refund Transaksi?',
                text: "Stok akan dikembalikan dan status transaksi akan diubah menjadi 'Refunded'",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Refund',
                cancelButtonText: 'Batal',
                iconColor: '#dc2626',
                customClass: {
                    popup: 'rounded-[1.5rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    processRefund(saleId);
                }
            });
        }

        function processRefund(saleId) {
            fetch(`/sales/${saleId}/refund`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Refund berhasil diproses');
                        loadSalesToday(); // Reload table
                    } else {
                        showToast('error', data.message || 'Refund gagal');
                    }
                })
                .catch(() => {
                    showToast('error', 'Terjadi kesalahan saat refund');
                });
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        function showSaleDetail(saleId) {
            document.getElementById('saleDetailModal').classList.remove('hidden');

            // Reset content
            document.getElementById('detailItemsBody').innerHTML = '<tr><td colspan="5" class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>';

            fetch(`/api/sale/${saleId}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('detailInvoiceNumber').textContent = data.invoice_number;
                    document.getElementById('detailCashier').textContent = data.cashier_name;
                    document.getElementById('detailCustomer').textContent = data.customer_name;
                    document.getElementById('detailTime').textContent = data.created_at;
                    document.getElementById('detailPaymentMethod').textContent = data.payment_method.toUpperCase();

                    // Render Items
                    let itemsHtml = '';
                    data.items.forEach(item => {
                        const itemDiscount = item.discount_amount || 0;
                        itemsHtml += `
                        <tr class="border-b last:border-0 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-800">${item.product_name}</td>
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold">${item.quantity}</td>
                            <td class="px-4 py-3 text-right text-gray-600">Rp ${formatNumber(item.price)}</td>
                            <td class="px-4 py-3 text-right text-red-600">${itemDiscount > 0 ? '-Rp ' + formatNumber(itemDiscount) : '-'}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp ${formatNumber(item.subtotal)}</td>
                        </tr>
                    `;
                    });
                    document.getElementById('detailItemsBody').innerHTML = itemsHtml;

                    // Render Totals
                    document.getElementById('detailSubtotal').textContent = 'Rp ' + formatNumber(data.subtotal);
                    document.getElementById('detailTax').textContent = 'Rp ' + formatNumber(data.tax);
                    document.getElementById('detailDiscount').textContent = '-Rp ' + formatNumber(data.total_discount);
                    document.getElementById('detailGrandTotal').textContent = 'Rp ' + formatNumber(data.grand_total);
                    document.getElementById('detailPaid').textContent = 'Rp ' + formatNumber(data.paid_amount);
                    document.getElementById('detailChange').textContent = 'Rp ' + formatNumber(data.change_amount);

                    // ✅ Handle debt info
                    if (data.payment_method === 'debt' && data.debt) {
                        const debtInfo = document.getElementById('detailDebtInfo');
                        debtInfo.classList.remove('hidden');
                        document.getElementById('detailRemainingDebt').textContent = 'Rp ' + formatNumber(data.debt.remaining_amount || 0);
                    } else {
                        document.getElementById('detailDebtInfo').classList.add('hidden');
                    }

                    // ✅ Handle Structured Notes (Promo info)
                    const notesContainer = document.getElementById('detailNotesContainer');
                    const notesContent = document.getElementById('detailStructuredNotes');
                    notesContent.innerHTML = '';
                    notesContainer.classList.add('hidden');

                    if (data.notes) {
                        try {
                            const notes = JSON.parse(data.notes);
                            let html = '';

                            // 1. Customer Type Info
                            if (notes.customer_type_info) {
                                const typeInfo = notes.customer_type_info;
                                html += `
                                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="px-2 py-0.5 rounded bg-indigo-600 text-white text-[10px] font-bold uppercase">
                                            ${typeInfo.label}
                                        </span>
                                        <span class="text-xs font-bold text-indigo-700">
                                            Hemat: Rp ${formatNumber(typeInfo.total_savings || 0)}
                                        </span>
                                    </div>
                                    <div class="space-y-1.5">
                                        ${(typeInfo.adjustments || []).map(adj => `
                                            <div class="text-[11px] flex justify-between items-center text-indigo-900">
                                                <div class="truncate mr-4">
                                                    <span class="font-bold">${adj.qty}x</span> ${adj.product_name}
                                                </div>
                                                <div class="text-right whitespace-nowrap">
                                                    <span class="text-gray-400 line-through">Rp ${formatNumber(adj.original_price)}</span>
                                                    <i class="fas fa-arrow-right mx-1 text-[8px] opacity-30"></i>
                                                    <span class="font-bold">Rp ${formatNumber(adj.applied_price)}</span>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>`;
                            }

                            // 2. Discount Plan
                            if (notes.discount_plan) {
                                const plan = notes.discount_plan;
                                const appliedDiscounts = plan.applied_discounts || [];

                                let planHtml = `<div class="bg-gray-50 border border-gray-200 rounded-xl p-3 space-y-3">`;

                                if (appliedDiscounts.length > 0) {
                                    planHtml += `<div class="space-y-2">
                                    ${appliedDiscounts.map(applied => `
                                        <div class="flex items-start justify-between gap-3 pb-2 border-b border-gray-100 last:border-0 last:pb-0">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 uppercase">
                                                        ${(applied.type || 'DISCOUNT').toUpperCase()}
                                                    </span>
                                                    <p class="text-sm font-bold text-gray-900 truncate">${applied.name || 'Diskon'}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-black text-cuan-green">- Rp ${formatNumber(applied.amount || 0)}</p>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>`;
                                }

                                // Free Items (BOGO)
                                if (plan.free_item_quota > 0) {
                                    let freeItems = [];
                                    appliedDiscounts.forEach(ad => {
                                        if (ad.type === 'buy_x_get_y' && ad.free_items) {
                                            freeItems = [...freeItems, ...ad.free_items];
                                        }
                                    });

                                    if (freeItems.length > 0) {
                                        planHtml += `
                                        <div class="pt-2 border-t border-gray-200">
                                            <div class="flex items-center justify-between bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg mb-2">
                                                <span class="text-xs font-bold uppercase">Hadiah Gratis</span>
                                                <span class="text-xs font-black">${plan.free_item_quota} Item</span>
                                            </div>
                                            <div class="space-y-1">
                                                ${freeItems.map(fi => `
                                                    <div class="flex items-center justify-between text-[11px] text-emerald-600 px-1">
                                                        <span>${fi.product_name || 'Item'}</span>
                                                        <span class="font-bold">x${fi.free_qty || 1}</span>
                                                    </div>
                                                `).join('')}
                                            </div>
                                        </div>`;
                                    }
                                }

                                planHtml += `</div>`;
                                html += planHtml;
                            }

                            if (html) {
                                notesContent.innerHTML = html;
                                notesContainer.classList.remove('hidden');
                            } else if (data.notes.trim()) {
                                // Fallback plain text
                                notesContent.innerHTML = `<p class="text-sm text-gray-600">${data.notes}</p>`;
                                notesContainer.classList.remove('hidden');
                            }
                        } catch (e) {
                            // Plain text fallback
                            notesContent.innerHTML = `<p class="text-sm text-gray-600">${data.notes}</p>`;
                            notesContainer.classList.remove('hidden');
                        }
                    }

                    // Customer notes separate
                    if (data.customer_notes) {
                        notesContent.innerHTML += `
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Catatan Pelanggan</p>
                            <p class="text-sm text-gray-600 italic">"${data.customer_notes}"</p>
                        </div>`;
                        notesContainer.classList.remove('hidden');
                    }

                    // Render Action Buttons
                    const actionContainer = document.getElementById('detailActionButtons');
                    actionContainer.innerHTML = '';

                    if (data.status === 'completed') {
                        if (window.permissions.unduhStrukPenjualan) {
                            actionContainer.innerHTML += `
                            <a href="/receipt/${data.id}/download" target="_blank" class="px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition-colors flex items-center gap-2">
                                <i class="fas fa-file-pdf"></i> Download
                            </a>
                        `;
                        }
                        if (window.permissions.cetakStrukPenjualan) {
                            actionContainer.innerHTML += `
                            <a href="/sales/${data.id}/print" target="_blank" class="px-4 py-2 bg-cuan-green text-white rounded-lg text-xs font-semibold hover:opacity-90 transition-colors flex items-center gap-2">
                                <i class="fas fa-print"></i> Cetak
                            </a>
                        `;
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('error', 'Gagal memuat detail transaksi');
                    closeSaleDetailModal();
                });
        }

        function closeSaleDetailModal() {
            document.getElementById('saleDetailModal').classList.add('hidden');
        }


    </script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let html5QrcodeScanner = null;

        function openScanner() {
            document.getElementById('scannerModal').classList.remove('hidden');

            // Initialize scanner if not already
            if (!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5Qrcode("reader");
            }

            const config = { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };

            // Prefer back camera
            html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
                .catch(err => {
                    console.error("Error starting scanner", err);
                    showToast('error', 'Gagal membuka kamera: ' + err);
                    closeScanner();
                });
            const manualInput = document.getElementById('manualBarcodeInput');
            if (manualInput) {
                manualInput.value = '';
                manualInput.focus();

                let debounceTimer;

                // 1. Handle Scanner "Enter" / Manual Submit
                manualInput.onkeydown = function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(debounceTimer);

                        const code = this.value.trim();
                        if (code.length > 0) {
                            handleManualBarcode(code);
                        }
                    }
                };

                // 2. Handle Manual Typing (Debounce & Recommendations)
                manualInput.oninput = function () {
                    clearTimeout(debounceTimer);
                    const code = this.value.trim();

                    if (code.length > 0) {
                        showBarcodeRecommendations(code);

                        // 1 second debounce for automatic submit
                        debounceTimer = setTimeout(() => {
                            // Check if there is exactly one recommendation or a perfect match
                            const recommendations = document.querySelectorAll('.barcode-recommendation');
                            if (recommendations.length === 1) {
                                recommendations[0].click();
                            } else {
                                // Try exact match even if multiple recommendations start with the same prefix
                                handleManualBarcode(code);
                            }
                        }, 1000);
                    } else {
                        document.getElementById('barcodeRecommendations').classList.add('hidden');
                    }
                };
            }
        }

        function handleManualBarcode(code) {
            // Reuse the logic from barcode_script partial
            if (typeof handleBarcodeScan === 'function') {
                const found = handleBarcodeScan(code);
                if (found) {
                    // Feedback visual/audio could go here
                    showToast('success', 'Produk ditemukan: ' + code);
                    document.getElementById('manualBarcodeInput').value = ''; // Clear for next scan
                    document.getElementById('barcodeRecommendations').classList.add('hidden');
                    playBeep();
                } else {
                    // "Jika tidak ada maka tidak terjadi apa apa" - User request (maybe silent log?)
                    console.log('Barcode not found: ' + code);
                }
            }
        }

        function showBarcodeRecommendations(code) {
            const recommendationsDiv = document.getElementById('barcodeRecommendations');
            const productCards = document.querySelectorAll('.product-card');
            let html = '';
            let foundCount = 0;

            productCards.forEach(card => {
                const barcode = card.getAttribute('data-product-barcode') || '';
                const productName = card.getAttribute('data-product-name') || '';

                if (barcode.toLowerCase().includes(code.toLowerCase()) || productName.toLowerCase().includes(code.toLowerCase())) {
                    foundCount++;
                    html += `
                        <div class="barcode-recommendation p-3 hover:bg-cuan-green/10 cursor-pointer border-b border-gray-100 flex justify-between items-center transition-colors" 
                             onclick="selectRecommendedBarcode('${barcode}')">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-900">${productName}</span>
                                <span class="text-xs text-gray-500 font-mono">${barcode}</span>
                            </div>
                            <i class="fas fa-plus text-cuan-green text-xs"></i>
                        </div>
                    `;
                }
            });

            if (foundCount > 0) {
                recommendationsDiv.innerHTML = html;
                recommendationsDiv.classList.remove('hidden');
            } else {
                recommendationsDiv.classList.add('hidden');
            }
        }

        window.selectRecommendedBarcode = function (barcode) {
            const input = document.getElementById('manualBarcodeInput');
            input.value = barcode;
            handleManualBarcode(barcode);
        };

        function closeScanner() {
            document.getElementById('scannerModal').classList.add('hidden');

            // Cleanup Input
            const input = document.getElementById('manualBarcodeInput');
            if (input) {
                input.value = '';
                input.oninput = null; // Remove listener
            }

            const recommendationsDiv = document.getElementById('barcodeRecommendations');
            if (recommendationsDiv) {
                recommendationsDiv.classList.add('hidden');
                recommendationsDiv.innerHTML = '';
            }

            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    console.log("Scanner stopped");
                }).catch(err => {
                    console.error("Failed to stop scanner", err);
                });
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Code matched = ${decodedText}`, decodedResult);

            // Stop scanning temporarily or close modal?
            // Let's close modal first to prevent multiple scans
            closeScanner();

            // 1. Cari produk di DOM berdasarkan barcode
            const productCard = document.querySelector(`.product-card[data-product-barcode="${decodedText}"]`);

            if (productCard) {
                // 2. Add to cart
                addProductToCart(productCard);

                // Audio feedback
                playBeep();

                showToast('success', 'Produk ditemukan & ditambahkan!');
            } else {
                // Play error sound?
                showToast('error', `Produk dengan barcode ${decodedText} tidak ditemukan`);

                // Re-open scanner after delay? Or keep it closed?
                // User might want to try again.
            }
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
            // console.warn(`Code scan error = ${error}`);
        }

        function playBeep() {
            // Simple beep using AudioContext
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);

                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(1000, audioCtx.currentTime); // 1000Hz
                gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);

                oscillator.start();
                oscillator.stop(audioCtx.currentTime + 0.1); // 100ms beep
            } catch (e) {
                console.error("Audio beep failed", e);
            }
        }
    </script>
    <script>
        // ==========================================
        // CUSTOMER SEARCH LOGIC
        // ==========================================

        function updateProductPricesUI() {
            const cards = document.querySelectorAll('.product-card');
            cards.forEach(card => {
                let price = parseFloat(card.dataset.productPrice);
                if (currentCustomer) {
                    // PERBAIKAN: Cek verifikasi ResellerApplication sebelum terapkan harga
                    if (currentCustomer.type === 'reseller' && currentCustomer.is_verified_reseller && card.dataset.productResellerPrice && card.dataset.productResellerPrice !== '') {
                        price = parseFloat(card.dataset.productResellerPrice);
                    }
                    // VIP Disabled per request
                    /* else if (currentCustomer.type === 'vip' && card.dataset.productPromoPrice && card.dataset.productPromoPrice !== '') {
                        price = parseFloat(card.dataset.productPromoPrice);
                    } */
                }

                const priceEl = card.querySelector('.product-price');
                if (priceEl) {
                    priceEl.innerText = 'Rp ' + formatNumber(price);
                }
            });
        }

        function initCustomerSearch() {
            const searchInput = document.getElementById('searchCustomer');
            const resultsContainer = document.getElementById('customerSearchResults');
            const searchContainer = document.getElementById('customerSearchContainer');
            const clearBtn = document.getElementById('clearCustomerBtn');

            // Check if elements exist
            if (!searchInput || !resultsContainer || !searchContainer || !clearBtn) return;

            // Hide results when clicking outside
            document.addEventListener('click', function (e) {
                if (!searchContainer.contains(e.target)) {
                    resultsContainer.classList.add('hidden');
                }
            });

            clearBtn.addEventListener('click', clearCustomer);

            searchInput.addEventListener('input', function (e) {
                const query = e.target.value.trim();

                if (query.length === 0) {
                    resultsContainer.classList.add('hidden');
                    clearBtn.classList.add('hidden');
                    return;
                }

                clearBtn.classList.remove('hidden');

                // Debounce
                clearTimeout(customerSearchTimeout);
                customerSearchTimeout = setTimeout(() => {
                    fetchCustomers(query);
                }, 1000); // 1 detik debounce
            });

            // Focus handler to reshow results if value exists
            searchInput.addEventListener('focus', function () {
                if (this.value.trim().length > 0) {
                    resultsContainer.classList.remove('hidden');
                }
            });

            // Initial UI setup if customer already set
            if (currentCustomer) {
                searchInput.value = `${currentCustomer.name} (${currentCustomer.type.toUpperCase()})`;
                searchInput.disabled = true;
                searchInput.classList.add('bg-cuan-green/10', 'text-cuan-dark', 'font-semibold');
                clearBtn.classList.remove('hidden');
                updateProductPricesUI();
            }
        }

        async function fetchCustomers(query) {
            const resultsContainer = document.getElementById('customerSearchResults');

            try {
                resultsContainer.innerHTML = '<div class="p-3 text-center text-gray-500 text-sm"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>';
                resultsContainer.classList.remove('hidden');

                const response = await fetch(`{{ route('pos.customer.search') }}?q=${encodeURIComponent(query)}`);
                const customers = await response.json();

                if (customers.length === 0) {
                    resultsContainer.innerHTML = '<div class="p-3 text-center text-gray-500 text-sm">Tidak ditemukan pelanggan</div>';
                    return;
                }

                let html = '';
                customers.forEach(customer => {
                    let badgeClass = 'bg-gray-100 text-gray-800';
                    let typeLabel = customer.type;

                    if (customer.type === 'reseller') {
                        badgeClass = 'bg-blue-100 text-blue-800';
                        typeLabel = 'RESELLER';
                    }
                    if (customer.type === 'vip') badgeClass = 'bg-purple-100 text-purple-800';

                    html += `
                        <div class="customer-search-item hover:bg-cuan-green/10 p-3 cursor-pointer border-b border-gray-100 last:border-0" 
                             onclick='selectCustomer(${JSON.stringify(customer).replace(/'/g, "&#39;")})'>
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-semibold text-gray-900">${customer.name}</div>
                                    <div class="text-xs text-gray-500">${customer.phone || '-'} | ${customer.code}</div>
                                </div>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full ${badgeClass}">
                                    ${typeLabel}
                                </span>
                            </div>
                        </div>
                    `;
                });

                resultsContainer.innerHTML = html;

            } catch (error) {
                console.error('Error fetching customers:', error);
                resultsContainer.innerHTML = '<div class="p-3 text-center text-red-500 text-sm">Gagal memuat data</div>';
            }
        }

        async function selectCustomer(customer) {
            currentCustomer = customer;
            const searchInput = document.getElementById('searchCustomer');
            const resultsContainer = document.getElementById('customerSearchResults');
            const clearBtn = document.getElementById('clearCustomerBtn');

            // Update UI
            searchInput.value = customer.name;
            searchInput.disabled = true;
            searchInput.classList.add('bg-cuan-green/10', 'text-cuan-dark', 'font-semibold');
            resultsContainer.classList.add('hidden');
            clearBtn.classList.remove('hidden');

            // Update Badge
            const badge = document.getElementById('customerTypeBadge');
            const badgeIcon = document.getElementById('customerTypeIcon');
            const badgeText = document.getElementById('customerTypeText');

            badge.className = 'flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold shadow-sm transition-all animate-fadeIn whitespace-nowrap border';

            if (customer.type === 'vip') {
                badge.classList.add('bg-purple-50', 'text-purple-700', 'border-purple-200');
                badgeIcon.className = 'fas fa-crown text-purple-600';
                badgeText.textContent = 'VIP';
            } else if (customer.type === 'reseller') {
                if (customer.is_verified_reseller) {
                    badge.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-200');
                    badgeIcon.className = 'fas fa-sync text-blue-600';
                    badgeText.textContent = 'RESELLER (SYNC ACTIVE)';
                } else {
                    badge.classList.add('bg-gray-100', 'text-gray-600', 'border-gray-300');
                    badgeIcon.className = 'fas fa-store text-gray-500';
                    badgeText.textContent = 'RESELLER (UNVERIFIED)';
                }
            } else {
                badge.classList.add('bg-gray-50', 'text-gray-700', 'border-gray-200');
                badgeIcon.className = 'fas fa-user text-gray-500';
                badgeText.textContent = 'REGULAR';
            }

            // Update product list prices
            updateProductPricesUI();

            // Send to Backend
            try {
                const response = await fetch('{{ route('pos.customer.set') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ customer_id: customer.id })
                });

                const data = await response.json();
                if (data.success) {
                    showToast('success', 'Pelanggan dipilih: ' + customer.name);
                    // Update cart state with new prices
                    cart = data.cart;
                    cartSummary = data.cart_summary;
                    renderCart();
                }
            } catch (e) {
                console.error(e);
                showToast('error', 'Gagal set pelanggan');
            }
        }

        async function clearCustomer(silent = false) {
            const searchInput = document.getElementById('searchCustomer');
            const clearBtn = document.getElementById('clearCustomerBtn');

            currentCustomer = null;
            searchInput.value = '';
            searchInput.disabled = false;
            searchInput.classList.remove('bg-cuan-green/10', 'text-orange-900', 'font-semibold');
            clearBtn.classList.add('hidden');

            // Hide Badge
            document.getElementById('customerTypeBadge').classList.add('hidden');
            document.getElementById('customerTypeBadge').classList.remove('flex');

            // Update product list prices back to normal
            updateProductPricesUI();

            // Send Clear to Backend
            try {
                const response = await fetch('{{ route('pos.customer.set') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ customer_id: null })
                });
                const data = await response.json();
                if (data.success) {
                    cart = data.cart;
                    cartSummary = data.cart_summary;
                    renderCart();
                    if (!silent) showToast('info', 'Pelanggan di-reset');
                }
            } catch (e) {
                console.error(e);
                showToast('error', 'Gagal reset pelanggan');
            }
        }

        // Initialize on load is now handled in the main initialization block above.


        function togglePanelPosition(checked) {
            const main = document.querySelector('.pos-main');
            if (checked) {
                main.classList.add('layout-swapped');
                localStorage.setItem('pos_layout_swapped', 'true');
            } else {
                main.classList.remove('layout-swapped');
                localStorage.setItem('pos_layout_swapped', 'false');
            }
        }

        // Init Panel Position on Load
        document.addEventListener('DOMContentLoaded', () => {
            const isSwapped = localStorage.getItem('pos_layout_swapped') === 'true';
            if (isSwapped) {
                const main = document.querySelector('.pos-main');
                if (main) main.classList.add('layout-swapped');

                // Wait for modal to be ready (though simple checkbox selection works directly if ID exists)
                // But since modal is in DOM, we can just select it.
                const toggle = document.getElementById('layoutPositionToggle');
                if (toggle) toggle.checked = true;
            }
        });
    </script>
    @include('main.pos.partials.barcode_script')

    {{-- Realtime Production Completed Notification (via Pusher CDN) --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        (function () {
            const PUSHER_KEY = @json(config('broadcasting.connections.pusher.key'));
            const PUSHER_CLUSTER = @json(config('broadcasting.connections.pusher.options.cluster'));
            const OUTLET_ID = @json(auth()->user()->outlet_id);

            console.log('[POS Realtime] Initializing...', { key: PUSHER_KEY ? '***' + PUSHER_KEY.slice(-4) : 'MISSING', cluster: PUSHER_CLUSTER, outlet: OUTLET_ID });

            if (!PUSHER_KEY || PUSHER_KEY === 'your-app-key') {
                console.error('[POS Realtime] Pusher key not configured. Realtime notifications DISABLED.');
                return;
            }

            const pusher = new Pusher(PUSHER_KEY, {
                cluster: PUSHER_CLUSTER,
                forceTLS: true
            });

            pusher.connection.bind('connected', function () {
                console.log('[POS Realtime] ✅ Connected to Pusher successfully');
            });
            pusher.connection.bind('error', function (err) {
                console.error('[POS Realtime] ❌ Pusher connection error:', err);
            });

            const channelName = 'production.outlet.' + OUTLET_ID;
            const channel = pusher.subscribe(channelName);

            channel.bind('pusher:subscription_succeeded', function () {
                console.log('[POS Realtime] ✅ Subscribed to channel:', channelName);
            });

            channel.bind('pusher:subscription_error', function (err) {
                console.error('[POS Realtime] ❌ Subscription error for channel:', channelName, err);
            });

            // Sound Notification
            const notificationSound = new Audio("{{ asset('assets/sounds/ting.mp3') }}");
            notificationSound.volume = 0.8;

            function playNotificationSound() {
                notificationSound.play().catch(function (e) {
                    console.warn('[POS Realtime] Audio play blocked (autoplay policy):', e.message);
                });
            }

            // Listen for production-completed event
            channel.bind('production-completed', function (data) {
                console.log('[POS Realtime] 🔔 Production completed event received:', data);

                const invoice = data.orderData ? data.orderData.invoice_number : (data.invoice_number || 'Unknown');

                playNotificationSound();

                // Show SweetAlert2 Toast
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    html: `
                    <div class="text-left">
                        <p class="text-sm font-black text-gray-900">Pesanan Selesai</p>
                        <p class="text-[11px] font-bold text-gray-500">Invoice: ${invoice} siap disajikan</p>
                    </div>
                `,
                    showConfirmButton: false,
                    timer: 8000,
                    timerProgressBar: true,
                    background: '#ffffff',
                    iconColor: '#658C58',
                    customClass: {
                        popup: 'rounded-[1rem] shadow-xl border border-emerald-100',
                    },
                    didOpen: function (toast) {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                // Trigger update to the pending production list if modal is open or for silent refresh
                if (typeof refreshPendingProduction === 'function') {
                    refreshPendingProduction();
                }

                console.log('[POS Realtime] ✅ Toast notification displayed for:', invoice);
            });

            // Listen for order-refunded event
            channel.bind('order-refunded', function (data) {
                if (typeof refreshPendingProduction === 'function') {
                    refreshPendingProduction();
                }
            });

            channel.bind('kitchen-bell', function (data) {
                playNotificationSound();
            });

            console.log('[POS Realtime] 🎧 Listening for production-completed on channel:', channelName);
        })();
    </script>
@endpush