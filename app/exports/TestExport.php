<?php


namespace App\exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TestExport implements FromView
{
    public function view(): View
    {
        return view('import');
    }
}