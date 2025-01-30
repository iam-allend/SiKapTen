<?php

namespace App\Controllers;

use App\Models\AdminModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $adminModel;

    public function index() {

        $data = [
            'tittle' => 'Login',
        ];
        return view('auth/login', $data);
    }
    public function register() {

        $data = [
            'tittle' => 'Register',
        ];
        return view('auth/register', $data);
    }

    public function add_register(){
        $this->adminModel = new AdminModel();

        // Validasi file
        if (!$this->validate([
            'photo' => [
                'uploaded[photo]',
                'mime_in[photo,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                'max_size[photo,4096]', // max 4MB
            ],
        ])) {
            return redirect()->back()->with('error', 'Invalid file.');
        }

        // Mengambil file dan mengubah namanya
        $file = $this->request->getFile('photo');
        $newName = $file->getRandomName();
        $file->move(ROOTPATH . 'public/img_user', $newName);


        $password = $this->request->getPost('password');
        $data = [
            'id_level' => $this->request->getPost('level'),
            'full_name' => $this->request->getPost('fullname'),
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'gender' => $this->request->getPost('gender'),
            'url_image' => $newName,
            'status_registrasi' => false
        ];
        $this->adminModel->insert($data);
        return redirect()->to(base_url('login'))->with('alert','register_sukses');
    }

    public function login()
    {

        // Aturan validasi
        $rules = [
            'email-username' => 'required',
            'password' => 'required'
        ];
        
        
        if ($this->request->getMethod() === 'post') {
            $usernameOrEmail = $this->request->getPost('email-username');
            $password = $this->request->getPost('password');

            log_message('debug', 'Username/Email: ' . $usernameOrEmail);
            log_message('debug', 'Password: ' . $password);

            $userModel = new AdminModel();
            $user = $userModel->getUserByUsernameOrEmail($usernameOrEmail);

            if ($user) {
                log_message('debug', 'User  found: ' . json_encode($user));
                if (password_verify($password, $user['password'])) {
                    $this->setUserSession($user);
                    return $this->redirectUser ($user['id_level']);
                } else {
                    log_message('error', 'Password mismatch for user: ' . $usernameOrEmail);
                }
            } else {
                log_message('error', 'User  not found: ' . $usernameOrEmail);
            }

            return redirect()->back()->with('error', 'Username atau password salah');
        }

        return redirect('auth/register')->back()->with('error', 'masih error');
    }

    private function setUserSession($user)
    {
        // Set session data
        session()->set([
            'id' => $user['id_admin'],
            'username' => $user['username'],
            'level_user' => $user['id_level'],
            'level_name' => $user['level_name'], // Menyimpan nama level
            'logged_in' => true,
        ]);
    }

    private function redirectUser ($levelId)
    {
        switch ($levelId) {
            case 1: // Customer
                return redirect()->to('/shop'); // Ganti dengan URL halaman shop
            case 2: // Siswa
                return redirect()->to('/profile'); // Ganti dengan URL halaman profil siswa
            case 3: // Admin
                return redirect()->to('/admin/dashboard'); // Ganti dengan URL dashboard admin
            case 4: // Superadmin
                return redirect()->to('/superadmin/dashboard'); // Ganti dengan URL dashboard superadmin
            default:
                return redirect()->to('/register'); // Jika level tidak dikenali
        }
    }

    public function logout()
    {
        session()->destroy(); // Menghancurkan session
        return redirect()->to('auth/login'); // Ganti dengan URL login yang sesuai
    }
}