<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/19/2019
 * Time: 7:07 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'card_id', 'amount', 'oper_code', 'comment'
    ];
}