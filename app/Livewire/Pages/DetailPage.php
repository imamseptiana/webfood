<?php

namespace App\Livewire\Pages;

use App\Models\Category;
use App\Models\Foods;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class DetailPage extends Component
{
    public $categories;
    public $matchedCategory;
    public $food; // Properti ini akan menampung model Foods atau stdClass
    public $title = "Favorite";

    public function mount(Foods $foods, $id)
    {
        $this->categories = Category::all();

        // Pastikan getFoodDetails mengembalikan model Eloquent atau stdClass yang konsisten.
        // Jika getFoodDetails mengembalikan model Eloquent, maka $this->food akan menjadi objek model.
        // Jika getFoodDetails mengembalikan stdClass, maka $this->food akan menjadi stdClass.
        $this->food = $foods->getFoodDetails($id);

        if (empty($this->food)) {
            abort(404);
        }

        // Pastikan $this->food->categories_id bisa diakses.
        // Jika $this->food adalah stdClass, ini baik-baik saja.
        // Jika $this->food adalah model Eloquent, ini juga baik-baik saja.
        $this->matchedCategory = collect($this->categories)->firstWhere('id', $this->food->categories_id);
    }

    public function addToCart()
    {
        $cartItems = session('cart_items', []);

        // Pastikan $this->food diubah menjadi array asosiatif yang bersih
        // sebelum digunakan.
        $foodData = [];
        if ($this->food instanceof \Illuminate\Database\Eloquent\Model) {
            // Jika $this->food adalah instance dari model Eloquent, gunakan toArray()
            $foodData = $this->food->toArray();
        } elseif (is_object($this->food)) {
            // Jika $this->food adalah objek (misalnya stdClass), cast ke array
            $foodData = (array) $this->food;
        } else {
            // Jika ada skenario lain (misalnya $this->food sudah array atau null),
            // tambahkan penanganan error atau log di sini
            // Untuk debugging, Anda bisa dd($this->food); di sini
            return; // Atau lempar exception
        }

        // Tambahkan 'id' ke foodData jika somehow tidak ada (pencegahan ekstra)
        // Ini mungkin tidak diperlukan jika getFoodDetails selalu mengembalikan 'id'
        if (!isset($foodData['id']) && isset($this->food->id)) {
             $foodData['id'] = $this->food->id;
        }


        $existingItemIndex = collect($cartItems)->search(fn($item) => isset($item['id']) && $item['id'] === $foodData['id']);

        if ($existingItemIndex !== false) {
            $cartItems[$existingItemIndex]['quantity'] += 1;
        } else {
            $cartItems[] = array_merge(
                $foodData, // Gunakan foodData yang sudah bersih
                [
                    'quantity' => 1,
                    'selected' => true,
                ]
            );
        }

        session(['cart_items' => $cartItems]);
        session(['has_unpaid_transaction' => false]);

        $this->dispatch('toast',
            data: [
                'message1' => 'Item added to cart',
                'message2' => $foodData['name'] ?? 'Unknown Item', // Gunakan foodData['name']
                'type' => 'success',
            ]
        );
    }

    public function orderNow()
    {
        $this->addToCart();
        return redirect()->route('payment.checkout');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('product.details');
    }
}