<?php

namespace App\Livewire\Pages;

use App\Livewire\Traits\CartManagement;
use App\Models\Foods; // Pastikan ini diimpor jika diperlukan untuk fungsi lain
use Livewire\Attributes\Layout;
use Livewire\Attributes\Session;
use Livewire\Component;

class CartPage extends Component
{
    use CartManagement; // Asumsi trait ini ada dan berfungsi dengan benar

    public $foods; // Properti ini tidak digunakan di sini jika hanya untuk menampilkan item keranjang
    public $title = "All Foods"; // Digunakan di layout

    public bool $selectAll = true;

    public $selectedItems = [];

    #[Session(key: 'cart_items')]
    public $cartItems = []; // Data keranjang diambil dari sesi
    #[Session(key: 'has_unpaid_transaction')]
    public $hasUnpaidTransaction; // Status transaksi yang belum dibayar dari sesi

    public function mount()
    {
        // Pastikan setiap item di $cartItems memiliki properti 'selected'
        // Ini penting jika data datang dari sesi yang mungkin tidak memiliki 'selected' secara default
        foreach ($this->cartItems as &$item) {
            if (!isset($item['selected'])) {
                $item['selected'] = true; // Default: item yang baru ditambahkan akan terpilih
            }
        }

        // Memastikan item yang dipilih diperbarui saat komponen dimuat
        $this->updateSelectedItems();
    }

    public function updatedSelectAll()
    {
        // Ketika checkbox "Pilih Semua" berubah, perbarui status 'selected' untuk semua item di keranjang
        foreach ($this->cartItems as &$item) { // Menggunakan & untuk referensi agar bisa mengubah item langsung
            $item['selected'] = $this->selectAll;
        }

        $this->updateSelectedItems(); // Perbarui daftar item yang dipilih berdasarkan perubahan
    }

    public function updateSelectedItems()
    {
        // Filter item di cartItems yang 'selected' adalah true
        $this->selectedItems = collect($this->cartItems)->filter(fn($item) => isset($item['selected']) && $item['selected'])->toArray();

        // Perbarui status selectAll berdasarkan apakah semua item di cartItems terpilih
        // Tambahkan kondisi `count($this->cartItems) > 0` untuk menghindari selectAll true saat keranjang kosong
        $this->selectAll = (count($this->selectedItems) === count($this->cartItems)) && (count($this->cartItems) > 0);

        // Mengatur ulang session 'has_unpaid_transaction'. Ini sesuai logika Anda.
        session(['has_unpaid_transaction' => false]);
    }

    public function deleteSelected()
    {
        // Filter cartItems, hanya simpan item yang 'selected' adalah false (yang tidak dihapus)
        $this->cartItems = collect($this->cartItems)->filter(fn($item) => !(isset($item['selected']) && $item['selected']))->toArray();

        // PENTING: Simpan seluruh struktur cartItems yang sudah difilter kembali ke session
        // Ini memastikan konsistensi antara properti Livewire dan data di sesi
        session(['cart_items' => $this->cartItems]);

        $this->selectedItems = []; // Mengosongkan selectedItems
        $this->updateSelectedItems(); // Perbarui status selectAll dan selectedItems setelah penghapusan
    }

    public function checkout()
    {
        // Memeriksa apakah ada item yang dipilih
        if (empty($this->selectedItems)) {
            $this->addError('selectedItems', 'Please select at least one item to proceed.');
            return;
        }

        // Menyimpan status cartItems saat ini ke session sebelum redirect.
        session(['cart_items' => $this->cartItems]);

        // Mengarahkan ke halaman checkout
        return $this->redirect('/checkout', navigate: true);
    }

    #[Layout('components.layouts.page')]
    public function render()
    {
        // Hapus `dd($this->cartItems);` jika masih ada, itu hanya untuk debugging.
        // return view('payment.cart', ['items' => $this->cartItems]); // jika Anda mengirim items secara eksplisit
        return view('payment.cart'); // Jika menu-item-list di-render dengan Livewire component nested
    }
}