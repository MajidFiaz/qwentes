<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class User extends Model
{
    protected $table = 'users';
    protected $hidden = ['password'];
    protected $visible = ['id','givenName','familyName','email','dateOfBirth','address','createdAt'];
    protected $appends = ['address'];
    protected $fillable = ['givenName', 'familyName', 'email', 'dateOfBirth', 'password', 'street', 'city', 'postalCode', 'countryCode', 'lat', 'lng'];
    public $timestamps = false;


    public function getUserByEmail($email){
        return User::where("email",$email)->first();
    }

    public function getAddressAttribute(){
        $address = [
            'city'=> $this->attributes['city'],
            'postalCode'=> $this->attributes['postalCode'],
            'street'=> $this->attributes['street'],
            'countryCode'=> $this->attributes['countryCode'],
            'coordinates'=> [
                'lat'=> $this->attributes['lat'],
                'lng'=> $this->attributes['lng']
            ]
        ];
        return $address;
    }

}
