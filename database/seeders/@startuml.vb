@startuml
title Ecosystem & Business Process Map - CuanFlow
left to right direction

' ==============================================
' 1. STYLING & THEME (PROFESSIONAL LOOK)
' ==============================================
skinparam {
    Shadowing false
    Handwritten false
    PackageStyle rectangle
    Linetype polyline
}

' Colors Palette
!define COLOR_PRIMARY #3b82f6
!define COLOR_SUCCESS #10b981
!define COLOR_WARNING #f59e0b
!define COLOR_DANGER #ef4444
!define COLOR_DARK #1e293b
!define COLOR_LIGHT #f8fafc

skinparam usecase {
    BackgroundColor COLOR_LIGHT
    BorderColor #94a3b8
    BorderThickness 1.5
    ArrowColor #64748b
    FontColor #0f172a
    FontSize 12
}

skinparam actor {
    BackgroundColor #e2e8f0
    BorderColor #475569
    FontColor #0f172a
    FontSize 13
    FontStyle bold
}

skinparam note {
    BackgroundColor #fff1f2
    BorderColor #fda4af
    FontColor #881337
    FontSize 11
}

' ==============================================
' 2. ACTORS CLASSIFICATION
' ==============================================

' --- Internal Staff (Operasional) ---
actor "Kasir\n(Front Office)" as Kasir #pink
actor "Produksi\n(Kitchen)" as Produksi #violet
actor "Inventaris\n(Warehouse)" as Inventaris #orange

' --- Management (Strategic) ---
actor "Supervisor\n(Manager)" as Supervisor #royalblue
actor "Owner\n(Super Admin)" as Owner #darkblue

' --- External / Public ---
actor "Umum / Pengunjung" as Guest #gray
actor "Pelanggan Terdaftar" as Customer #green
actor "Reseller Resmi" as Reseller #gold

' --- Inheritance Relationships ---
Supervisor <|-- Kasir
Supervisor <|-- Produksi
Supervisor <|-- Inventaris
Owner <|-- Supervisor
Customer <|-- Guest : Upgrade
Reseller <|-- Customer : Upgrade

' ==============================================
' 3. SYSTEM MODULES (USE CASES)
' ==============================================

package "Digital Front-End (Public Access)" as PKG_FrontEnd #F0FDF4 {
    usecase "Akses Landing Page" as UC_Landing
    usecase "Lihat Katalog Produk" as UC_Catalog
    usecase "Lihat FAQ & Info" as UC_FAQ_Public
    usecase "Ajukan Menjadi Reseller" as UC_ApplyReseller
    usecase "Isi Form Pendaftaran" as UC_FillForm
    usecase "Cek Status Lamaran" as UC_CheckStatus
    
    ' Logic
    UC_Landing ..> UC_Catalog : <<include>>
    UC_Landing ..> UC_FAQ_Public : <<include>>
    UC_ApplyReseller ..> UC_FillForm : <<include>>
    UC_ApplyReseller <.. UC_CheckStatus : <<extend>>
}

package "Core Point of Sales (POS)" as PKG_POS #EFF6FF {
    usecase "Buka Shift Kasir" as UC_OpenShift
    usecase "Tutup Shift & Laporan" as UC_CloseShift
    usecase "Proses Transaksi" as UC_Trans
    usecase "Hitung Diskon Otomatis" as UC_AutoDisc
    usecase "Pilih Metode Bayar" as UC_PayMethod
    
    ' Logic
    UC_Trans ..> UC_AutoDisc : <<include>> \n(System Check)
    UC_Trans ..> UC_PayMethod : <<include>>
}

package "Back Office Management" as PKG_Admin #F8FAFC {
    ' Reseller Management
    usecase "Kelola Lamaran Reseller" as UC_ManageResellerApps
    usecase "Verifikasi/Approve Reseller" as UC_ApproveReseller
    usecase "Tolak Lamaran" as UC_RejectReseller
    
    ' Content Management
    usecase "Kelola Landing Page" as UC_ManageLanding
    usecase "Kelola FAQ" as UC_ManageFAQ
    
    ' Logic
    UC_ManageResellerApps <.. UC_ApproveReseller : <<extend>>
    UC_ManageResellerApps <.. UC_RejectReseller : <<extend>>
}

package "Supply Chain & Production" as PKG_Supply #FFF7ED {
    usecase "Proses Produksi" as UC_Production
    usecase "Resep Digital" as UC_Recipe
    usecase "Kelola Data Supplier" as UC_ManageSupplier
    usecase "Stock Opname" as UC_SO
    
    ' Logic
    UC_Production ..> UC_Recipe : <<include>>
}

package "Executive Insights" as PKG_Exec #F3E8FF {
    usecase "Lihat Dashboard Utama" as UC_Dash
    usecase "Analitik AI (Insights)" as UC_AI
    usecase "Laporan Keuangan" as UC_Finance
    
    UC_Dash <.. UC_AI : <<extend>>
}

' ==============================================
' 4. COMPLEX SYSTEM INTERACTIONS
' ==============================================

' --- Flow 1: Public & Reseller Registration ---
Guest --> UC_Landing
Guest --> UC_ApplyReseller : Ingin Bergabung?
Reseller --> UC_Landing : Cek Info Terbaru

' --- Flow 2: Reseller Approval Process (The Bridge) ---
' Owner melihat aplikasi yang masuk dari Guest
Owner --> UC_ManageResellerApps
UC_ManageResellerApps ..> UC_ApplyReseller : <<dependency>> \n(Data Source)

' --- Flow 3: Content Management ---
Owner --> UC_ManageLanding
Owner --> UC_ManageFAQ
UC_ManageLanding --|> UC_Landing : Updates Content

' --- Flow 4: Operational ---
Kasir --> UC_OpenShift
Kasir --> UC_Trans
Kasir --> UC_CloseShift

' --- Flow 5: Production & Inventory ---
Produksi --> UC_Production
Inventaris --> UC_ManageSupplier
Inventaris --> UC_SO

' --- Flow 6: Executive Control ---
Supervisor --> UC_Dash
Supervisor --> UC_Finance
Owner --> UC_AI

' ==============================================
' 5. NOTES & CLARIFICATIONS
' ==============================================
note top of UC_ApplyReseller
  <b>Fitur Reseller</b>
  Pengunjung dapat mendaftar.
  Status awal: 'Pending'.
  Membutuhkan Approval Owner.
end note

note bottom of UC_ManageResellerApps
  <b>Validasi Data</b>
  Owner memeriksa kelayakan.
  Jika OK -> Role berubah jadi Reseller.
  Jika Tidak -> Status Rejected.
end note

@enduml