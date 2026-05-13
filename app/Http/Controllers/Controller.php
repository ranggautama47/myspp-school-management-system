<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Tambahkan ini

abstract class Controller
{
    use AuthorizesRequests; // Dan tambahkan ini
}