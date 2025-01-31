<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'tittle' => 'Beranda',
        ];
        return view('frontend/index', $data);
    }
    public function about()
    {
        $data = [
            'tittle' => 'Tentang Kami',
        ];
        return view('frontend/about', $data);
    }
    public function profile()
    {
        $data = [
            'tittle' => 'Dashboard Profile',
        ];
        return view('frontend/profile', $data);
    }
    public function contactus()
    {
        $data = [
            'tittle' => 'Hubungi Kami',
        ];
        return view('frontend/contactus', $data);
    }
}
