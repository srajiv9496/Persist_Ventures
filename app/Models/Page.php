<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Page extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'username',
        'address',
        'role'
    ];

    /**
     * Insert data into the database if the username does not exist.
     *
     * @param array $data
     * @return void
     */
    public static function insertData($data)
    {
        $exists = DB::table('users')->where('username', $data['username'])->exists();

        if (!$exists) {
            DB::table('users')->insert($data);
        }
    }
}
