<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DataUserController extends Controller
{
    public function index()
    {
        // $tes = ' ';

        // return trim($tes) ?? null;
        return view('datauser', [
            'user' => User::all()
        ]);
    }

    public function tambah(Request $request)
    {
        $validateData = $request->validate([            
            'username' => 'min:3|required|unique:users',
            'pass' => 'required|min:3',
            'level' => 'required'
        ]);

        $hashPass = Hash::make($validateData['pass']);

        User::create(
            [
                'username' => $validateData['username'],
                'level' => $validateData['level'],
                'password' => $hashPass
            ]
        );
        session()->flash('success', 'User "'.$validateData['username'].'" Berhasil Ditambahkan.');
        return back();
    }

    public function delete($id)
    {
        // dd($id);
        $data = User::find($id);

        // dd($data);
        // Check if the resource exists
        if (!$data) {
            return redirect()->route('datauser.index')->with('error', 'Data tidak ditemukan');
        }

        // Delete the resource
        $data->delete();

        return redirect()->route('datauser.index')->with('success', 'Data Berhasil Dihapus');
    }

    public function update(Request $request, $id)
    {
        $validateData = $request->validate([            
            'OldPass' => 'required|min:3',
            'NewPass' => 'required|min:3',
        ]);

        $user = User::find($id);
        // return $user;
        $pass = $user->password;
        // return $pass;

        if (Hash::check($validateData['OldPass'], $pass)) {
            $hashPass = Hash::make($validateData['NewPass']);

            User::where('id', $id)->update([
                'password' => $hashPass
            ]);

            return redirect()->route('datauser.index')->with('success', 'Password berhasil diubah');
        } else{
            return redirect()->route('datauser.index')->with('error', 'Password Salah, Gagal mengubah password');
        }

    }
}
