<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories first if they don't exist
        $categories = [
            'Grains' => Category::firstOrCreate(['name' => 'Grains']),
            'Breakfast' => Category::firstOrCreate(['name' => 'Breakfast']),
            'Superfood' => Category::firstOrCreate(['name' => 'Superfood']),
            'Snacks' => Category::firstOrCreate(['name' => 'Snacks']),
            'Dairy' => Category::firstOrCreate(['name' => 'Dairy']),
        ];

        $products = [
            [
                'name' => 'Broken Wheat',
                'category' => 'Grains',
                'pack' => '1kg pack',
                'price' => 299,
                'rating' => 4.8,
                'calories' => 340,
                'nutrition' => [
                    'protein' => '12g',
                    'carbs' => '72g',
                    'fats' => '1.5g',
                    'fiber' => '9g'
                ],
                'description' => 'Our premium Broken Wheat (Dalia) is perfect for a wholesome breakfast or a light dinner. High in fiber and specific nutrients to aid digestion.',
                'benefits' => ['High Fiber', 'Low Glycemic Index', 'Rich in B Vitamins'],
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Rolled Oats',
                'category' => 'Breakfast',
                'pack' => '500g pouch',
                'price' => 349,
                'rating' => 4.5,
                'calories' => 380,
                'nutrition' => [
                    'protein' => '12g',
                    'carbs' => '72g',
                    'fats' => '1.5g',
                    'fiber' => '9g'
                ],
                'description' => 'Our premium Rolled Oats are a nutritious breakfast option. Rich in fiber and protein, they help maintain energy levels.',
                'benefits' => ['High Fiber', 'Low Fat', 'Rich in Protein'],
                'image' => 'https://images.unsplash.com/photo-1551462147-ff29053bfc14?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Mixed Millets',
                'category' => 'Grains',
                'pack' => '1kg pack',
                'price' => 449,
                'rating' => 4.9,
                'calories' => 360,
                'nutrition' => [
                    'protein' => '11g',
                    'carbs' => '65g',
                    'fats' => '3.5g',
                    'fiber' => '8g'
                ],
                'description' => 'Our premium Mixed Millets are a nutritious option for any meal. Rich in fiber and protein, they help maintain energy levels.',
                'benefits' => ['Gluten Free', 'High Protein', 'Rich in Minerals'],
                'image' => 'https://images.unsplash.com/photo-1599307767316-776533bb941c?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Quinoa Seeds',
                'category' => 'Superfood',
                'pack' => '250g pack',
                'price' => 399,
                'rating' => 4.7,
                'calories' => 120,
                'nutrition' => [
                    'protein' => '14g',
                    'carbs' => '64g',
                    'fats' => '6g',
                    'fiber' => '7g'
                ],
                'description' => 'Our premium Quinoa Seeds are a complete protein source. Perfect for salads, bowls, and as a rice substitute.',
                'benefits' => ['Complete Protein', 'Low GI', 'Gluten Free'],
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Chia Seeds',
                'category' => 'Superfood',
                'pack' => '200g jar',
                'price' => 299,
                'rating' => 4.6,
                'calories' => 486,
                'nutrition' => [
                    'protein' => '17g',
                    'carbs' => '42g',
                    'fats' => '31g',
                    'fiber' => '34g'
                ],
                'description' => 'Our premium Chia Seeds are packed with omega-3 fatty acids, fiber, and antioxidants. Perfect for smoothies, puddings, and salads.',
                'benefits' => ['Omega-3 Rich', 'High Fiber', 'Antioxidant'],
                'image' => 'https://images.unsplash.com/photo-1510627498534-cf7e9002facc?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Flax Seeds',
                'category' => 'Superfood',
                'pack' => '250g pack',
                'price' => 189,
                'rating' => 4.5,
                'calories' => 534,
                'nutrition' => [
                    'protein' => '18g',
                    'carbs' => '29g',
                    'fats' => '42g',
                    'fiber' => '27g'
                ],
                'description' => 'Flax seeds are rich in omega-3 fatty acids and lignans. Great for digestive health and heart health.',
                'benefits' => ['Heart Healthy', 'Omega-3 Rich', 'High Fiber'],
                'image' => 'https://images.unsplash.com/photo-1558864559-ed673ba3610b?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Granola Crunch',
                'category' => 'Breakfast',
                'pack' => '400g box',
                'price' => 449,
                'rating' => 4.4,
                'calories' => 450,
                'nutrition' => [
                    'protein' => '10g',
                    'carbs' => '60g',
                    'fats' => '18g',
                    'fiber' => '8g'
                ],
                'description' => 'Delicious crunchy granola with oats, nuts, and honey. Perfect with milk or yogurt for a healthy breakfast.',
                'benefits' => ['Ready to Eat', 'Whole Grains', 'Natural Sweetener'],
                'image' => 'https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Protein Muesli',
                'category' => 'Breakfast',
                'pack' => '500g pack',
                'price' => 549,
                'rating' => 4.8,
                'calories' => 380,
                'nutrition' => [
                    'protein' => '20g',
                    'carbs' => '55g',
                    'fats' => '8g',
                    'fiber' => '10g'
                ],
                'description' => 'High protein muesli with nuts, seeds, and dried fruits. Perfect for fitness enthusiasts.',
                'benefits' => ['High Protein', 'No Added Sugar', 'Energy Boosting'],
                'image' => 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Brown Rice',
                'category' => 'Grains',
                'pack' => '1kg pack',
                'price' => 179,
                'rating' => 4.6,
                'calories' => 362,
                'nutrition' => [
                    'protein' => '7g',
                    'carbs' => '76g',
                    'fats' => '2.7g',
                    'fiber' => '3.5g'
                ],
                'description' => 'Whole grain brown rice retains the bran layer, making it more nutritious than white rice.',
                'benefits' => ['Whole Grain', 'Low GI', 'Heart Healthy'],
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Almond Butter',
                'category' => 'Snacks',
                'pack' => '200g jar',
                'price' => 599,
                'rating' => 4.7,
                'calories' => 614,
                'nutrition' => [
                    'protein' => '21g',
                    'carbs' => '20g',
                    'fats' => '56g',
                    'fiber' => '10g'
                ],
                'description' => 'Creamy almond butter made from 100% roasted almonds. No added sugar or preservatives.',
                'benefits' => ['No Added Sugar', 'High Protein', 'Vitamin E Rich'],
                'image' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?q=80&w=300&auto=format&fit=crop'
            ],
        ];

        foreach ($products as $productData) {
            $categoryName = $productData['category'];
            unset($productData['category']);

            $category = $categories[$categoryName] ?? Category::firstOrCreate(['name' => $categoryName]);

            Product::firstOrCreate(
                ['name' => $productData['name']],
                array_merge($productData, ['category_id' => $category->id])
            );
        }
    }
}
