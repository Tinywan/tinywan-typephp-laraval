<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsSkuModel extends Model
{
  protected $connection = 'mx_erp';

  protected $table='goods_skus';
}
