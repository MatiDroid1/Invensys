<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Página "Acerca del proyecto": explica qué es Invensys y para qué existe.
 */
class AcercaController extends Controller
{
    public function __invoke(): View
    {
        return view('acerca');
    }
}
