<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\Friendship;
use App\Models\Goal;
use App\Models\GoalContribution;
use App\Models\Group;
use App\Models\User;
use App\Services\SplitCalculator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategories();

        if (User::count() === 0) {
            $this->seedDemoData();
        }
    }

    protected function seedCategories(): void
    {
        $cats = [
            ['name_en' => 'Food & Drinks',   'name_ar' => 'أكل وشرب',     'icon' => '🍽️', 'color' => '#F97316', 'keywords' => ['food','dinner','lunch','breakfast','restaurant','cafe','أكل','عشاء','غداء','فطار','مطعم','كافيه','قهوة']],
            ['name_en' => 'Groceries',       'name_ar' => 'بقالة',         'icon' => '🛒', 'color' => '#84CC16', 'keywords' => ['groceries','market','supermarket','kazyon','metro','بقالة','سوبر','كازيون','مترو']],
            ['name_en' => 'Transport',       'name_ar' => 'مواصلات',       'icon' => '🚕', 'color' => '#3B82F6', 'keywords' => ['uber','careem','indrive','taxi','bus','metro','أوبر','كريم','تاكسي','مواصلات','ميكروباص']],
            ['name_en' => 'Bills',           'name_ar' => 'فواتير',        'icon' => '🧾', 'color' => '#EF4444', 'keywords' => ['bill','electricity','water','gas','internet','فاتورة','كهرباء','مياه','غاز','نت','إنترنت']],
            ['name_en' => 'Rent',            'name_ar' => 'إيجار',         'icon' => '🏠', 'color' => '#8B5CF6', 'keywords' => ['rent','إيجار','شقة']],
            ['name_en' => 'Entertainment',   'name_ar' => 'ترفيه',         'icon' => '🎬', 'color' => '#EC4899', 'keywords' => ['cinema','movie','game','netflix','سينما','فيلم','نتفليكس','لعبة']],
            ['name_en' => 'Travel',          'name_ar' => 'سفر',           'icon' => '✈️', 'color' => '#06B6D4', 'keywords' => ['flight','hotel','trip','airbnb','طيران','فندق','رحلة']],
            ['name_en' => 'Shopping',        'name_ar' => 'تسوق',          'icon' => '🛍️', 'color' => '#F59E0B', 'keywords' => ['clothes','shoes','amazon','noon','تسوق','هدوم','أمازون','نون']],
            ['name_en' => 'Health',          'name_ar' => 'صحة',           'icon' => '💊', 'color' => '#10B981', 'keywords' => ['pharmacy','doctor','hospital','صيدلية','دكتور','مستشفى','دواء']],
            ['name_en' => 'Gifts',           'name_ar' => 'هدايا',         'icon' => '🎁', 'color' => '#F43F5E', 'keywords' => ['gift','birthday','هدية','عيد ميلاد']],
            ['name_en' => 'Other',           'name_ar' => 'أخرى',          'icon' => '📦', 'color' => '#6B7280', 'keywords' => []],
        ];

        foreach ($cats as $c) {
            Category::firstOrCreate(['name_en' => $c['name_en']], $c);
        }
    }

    protected function seedDemoData(): void
    {
        $me = User::create([
            'name' => 'Mostafa',
            'email' => 'devuser1@esystematic.org',
            'password' => Hash::make('password'),
            'phone' => '+201000000001',
            'instapay_handle' => 'mostafa@instapay',
            'vodafone_cash' => '01000000001',
            'color' => '#FF6B35',
        ]);
        $john = User::create(['name' => 'John', 'email' => 'john@demo.com', 'password' => Hash::make('password'), 'color' => '#3B82F6']);
        $wade = User::create(['name' => 'Wade', 'email' => 'wade@demo.com', 'password' => Hash::make('password'), 'color' => '#10B981']);
        $jack = User::create(['name' => 'Jack', 'email' => 'jack@demo.com', 'password' => Hash::make('password'), 'color' => '#EC4899']);
        $kim  = User::create(['name' => 'Kim',  'email' => 'kim@demo.com',  'password' => Hash::make('password'), 'color' => '#8B5CF6']);

        foreach ([$john, $wade, $jack, $kim] as $f) {
            Friendship::create(['requester_id' => $me->id, 'addressee_id' => $f->id, 'status' => 'accepted']);
        }

        $birthday = Group::create(['name' => 'Birthday House', 'owner_id' => $me->id, 'icon' => '🎂', 'color' => '#F97316', 'currency' => 'EGP']);
        $party    = Group::create(['name' => 'Party Time',    'owner_id' => $me->id, 'icon' => '🎉', 'color' => '#EC4899', 'currency' => 'EGP']);
        $shopping = Group::create(['name' => 'Shopping',      'owner_id' => $me->id, 'icon' => '🛍️', 'color' => '#F59E0B', 'currency' => 'EGP']);

        foreach ([$me, $john, $wade] as $u) $birthday->members()->attach($u->id, ['role' => $u->id === $me->id ? 'owner' : 'member', 'joined_at' => now()]);
        foreach ([$me, $john, $wade, $kim] as $u) $party->members()->attach($u->id, ['role' => $u->id === $me->id ? 'owner' : 'member', 'joined_at' => now()]);
        foreach ([$me, $jack, $kim] as $u) $shopping->members()->attach($u->id, ['role' => $u->id === $me->id ? 'owner' : 'member', 'joined_at' => now()]);

        $foodCat = Category::where('name_en', 'Food & Drinks')->first();
        $shopCat = Category::where('name_en', 'Shopping')->first();
        $entCat  = Category::where('name_en', 'Entertainment')->first();

        $this->makeExpense($birthday, $me, $foodCat, 4508.32, 'Birthday dinner', '-3 days');
        $this->makeExpense($birthday, $john, $entCat, 1505.00, 'Cake & decorations', '-2 days');
        $this->makeExpense($shopping, $me, $shopCat, 505.00, 'Party supplies', '-5 days');
        $this->makeExpense($party, $wade, $foodCat, 2501.32, 'Drinks & snacks', '-1 day');

        $goal = Goal::create([
            'group_id' => $party->id, 'owner_id' => $me->id,
            'name' => 'Marsa Alam Trip', 'target_amount' => 25000, 'currency' => 'EGP',
            'deadline' => now()->addMonths(2)->toDateString(), 'icon' => '🏖️', 'color' => '#06B6D4',
        ]);
        GoalContribution::create(['goal_id' => $goal->id, 'user_id' => $me->id, 'amount' => 3000, 'contributed_at' => now()]);
        GoalContribution::create(['goal_id' => $goal->id, 'user_id' => $john->id, 'amount' => 2500, 'contributed_at' => now()]);
    }

    private function makeExpense(Group $group, User $payer, ?Category $cat, float $amount, string $desc, string $when): void
    {
        $expense = Expense::create([
            'group_id' => $group->id,
            'payer_id' => $payer->id,
            'category_id' => $cat?->id,
            'amount' => $amount,
            'currency' => $group->currency,
            'description' => $desc,
            'occurred_at' => Carbon::parse($when),
            'split_type' => 'equal',
        ]);
        $memberIds = $group->members->pluck('id')->all();
        $shares = array_fill_keys($memberIds, 1);
        $amounts = SplitCalculator::compute('equal', $amount, $shares);
        foreach ($amounts as $uid => $amt) {
            ExpenseSplit::create(['expense_id' => $expense->id, 'user_id' => $uid, 'amount' => $amt, 'share_value' => 1]);
        }
    }
}
