<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // Pastikan ini ada

class Foods extends Model
{
    use HasFactory;
    
    use Search; 

    protected $guarded = [];

    // Pastikan semua kolom yang bisa diisi secara massal ada di sini
    protected $fillable = [
        'name',
        'description',
        'image_path', // <--- Pastikan ini 'image_path'
        'price',
        'price_afterdiscount',
        'percent',
        'is_promo',
        'categories_id',
        'is_favorite'
    ];

    protected $searchable = ['name', 'description'];

    public function categories()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Mengambil semua makanan dengan total penjualan.
     * Mengatasi ONLY_FULL_GROUP_BY dengan menggunakan MAX() untuk kolom non-agregat.
     */
    public function getAllFoods()
    {
        return DB::table('foods')
            ->leftJoin('transaction_items', 'foods.id', '=', 'transaction_items.foods_id')
            ->select(
                'foods.id',
                DB::raw('MAX(foods.name) as name'),
                DB::raw('MAX(foods.description) as description'),
                DB::raw('MAX(foods.image_path) as image_path'), // Menggunakan image_path
                DB::raw('MAX(foods.price) as price'),
                DB::raw('MAX(foods.price_afterdiscount) as price_afterdiscount'),
                DB::raw('MAX(foods.percent) as percent'),
                DB::raw('MAX(foods.is_promo) as is_promo'),
                DB::raw('MAX(foods.categories_id) as categories_id'),
                DB::raw('MAX(foods.created_at) as created_at'),
                DB::raw('MAX(foods.updated_at) as updated_at'),
                DB::raw('MAX(foods.is_favorite) as is_favorite'),
                DB::raw('COALESCE(SUM(transaction_items.quantity), 0) as total_sold')
            )
            ->groupBy('foods.id')
            ->get();
    }

    /**
     * Mengambil detail makanan berdasarkan ID dengan total penjualan.
     * Mengatasi ONLY_FULL_GROUP_BY dengan menggunakan MAX() untuk kolom non-agregat.
     */
    public function getFoodDetails($id)
    {
        return DB::table('foods')
            ->leftJoin('transaction_items', 'foods.id', '=', 'transaction_items.foods_id')
            ->select(
                'foods.id',
                DB::raw('MAX(foods.name) as name'),
                DB::raw('MAX(foods.description) as description'),
                DB::raw('MAX(foods.image_path) as image_path'), // Menggunakan image_path
                DB::raw('MAX(foods.price) as price'),
                DB::raw('MAX(foods.price_afterdiscount) as price_afterdiscount'),
                DB::raw('MAX(foods.percent) as percent'),
                DB::raw('MAX(foods.is_promo) as is_promo'),
                DB::raw('MAX(foods.categories_id) as categories_id'),
                DB::raw('MAX(foods.created_at) as created_at'),
                DB::raw('MAX(foods.updated_at) as updated_at'),
                DB::raw('MAX(foods.is_favorite) as is_favorite'),
                DB::raw('COALESCE(SUM(transaction_items.quantity), 0) as total_sold')
            )
            ->where('foods.id', $id)
            ->groupBy('foods.id')
            ->first(); // <--- PENTING: Diubah dari ->get() menjadi ->first()
    }

    /**
     * Mengambil makanan promo dengan total penjualan.
     * Mengatasi ONLY_FULL_GROUP_BY dengan menggunakan MAX() untuk kolom non-agregat.
     */
    public function getPromo()
    {
        return DB::table('foods')
            ->leftJoin('transaction_items', 'foods.id', '=', 'transaction_items.foods_id')
            ->select(
                'foods.id',
                DB::raw('MAX(foods.name) as name'),
                DB::raw('MAX(foods.description) as description'),
                DB::raw('MAX(foods.image_path) as image_path'), // Menggunakan image_path
                DB::raw('MAX(foods.price) as price'),
                DB::raw('MAX(foods.price_afterdiscount) as price_afterdiscount'),
                DB::raw('MAX(foods.percent) as percent'),
                DB::raw('MAX(foods.is_promo) as is_promo'),
                DB::raw('MAX(foods.categories_id) as categories_id'),
                DB::raw('MAX(foods.created_at) as created_at'),
                DB::raw('MAX(foods.updated_at) as updated_at'),
                DB::raw('MAX(foods.is_favorite) as is_favorite'),
                DB::raw('COALESCE(SUM(transaction_items.quantity), 0) as total_sold')
            )
            ->where('foods.is_promo', 1)
            ->groupBy('foods.id')
            ->get();
    }

    /**
     * Mengambil makanan favorit berdasarkan total penjualan.
     * Mengatasi ONLY_FULL_GROUP_BY dengan menggunakan MAX() untuk kolom non-agregat.
     */
    public function getFavoriteFood()
    {
        
        return \App\Models\TransactionItems::select( 
                'foods.id',
                DB::raw('MAX(foods.name) as name'),
                DB::raw('MAX(foods.description) as description'),
                DB::raw('MAX(foods.image_path) as image_path'), // Menggunakan image_path
                DB::raw('MAX(foods.price) as price'),
                DB::raw('MAX(foods.price_afterdiscount) as price_afterdiscount'),
                DB::raw('MAX(foods.percent) as percent'),
                DB::raw('MAX(foods.is_promo) as is_promo'),
                DB::raw('MAX(foods.categories_id) as categories_id'),
                DB::raw('MAX(foods.created_at) as created_at'),
                DB::raw('MAX(foods.updated_at) as updated_at'),
                DB::raw('MAX(foods.is_favorite) as is_favorite'),
                DB::raw('SUM(transaction_items.quantity) as total_sold')
            )
            ->join('foods', 'transaction_items.foods_id', '=', 'foods.id')
            ->groupBy('foods.id')
            ->orderByDesc('total_sold')
            ->get();
    }
}