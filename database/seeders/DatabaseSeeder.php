<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\{User,WasteType,Deposit};
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin=User::create(['name'=>'Administrator RecycleHub','username'=>'admin','email'=>'admin@recyclehub.test','phone'=>'081000000001','password'=>Hash::make('Admin12345'),'role'=>'admin','balance'=>0,'status'=>'aktif']);
        $siti=User::create(['name'=>'Siti Rahma','username'=>'sitirahma','email'=>'sitirahma@example.test','phone'=>'081234567891','password'=>Hash::make('Nasabah12345'),'role'=>'nasabah','balance'=>45000,'status'=>'aktif']);
        User::create(['name'=>'Budi Santoso','username'=>'budisantoso','email'=>'budi@example.test','phone'=>'081234567892','password'=>Hash::make('Nasabah12345'),'role'=>'nasabah','balance'=>25000,'status'=>'aktif']);
        User::create(['name'=>'Andi Pratama','username'=>'andipratama','email'=>'andi@example.test','phone'=>'081234567893','password'=>Hash::make('Nasabah12345'),'role'=>'nasabah','balance'=>12000,'status'=>'aktif']);
        $wastes=[
          ['name'=>'Botol Plastik','price_per_kg'=>3000,'description'=>'Botol plastik PET bersih dan kering','status'=>'aktif'],
          ['name'=>'Kardus','price_per_kg'=>2000,'description'=>'Kardus bekas yang masih utuh','status'=>'aktif'],
          ['name'=>'Kertas','price_per_kg'=>2500,'description'=>'Kertas bekas dan koran','status'=>'aktif'],
          ['name'=>'Kaleng','price_per_kg'=>5000,'description'=>'Kaleng aluminium dan besi ringan','status'=>'aktif'],
          ['name'=>'Plastik Campuran','price_per_kg'=>1500,'description'=>'Plastik campuran yang sudah dipilah','status'=>'aktif']];
        foreach($wastes as $w) WasteType::create($w);
        $waste=WasteType::where('name','Botol Plastik')->first();
        Deposit::create(['transaction_code'=>'DEP-DEMO-001','user_id'=>$siti->id,'waste_type_id'=>$waste->id,'transaction_date'=>now()->subDays(2)->toDateString(),'weight'=>5,'price_per_kg'=>3000,'total_amount'=>15000,'notes'=>'Setoran contoh','processed_by'=>$admin->id,'status'=>'berhasil']);

    }
}
