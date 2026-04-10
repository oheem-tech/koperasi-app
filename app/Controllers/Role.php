<?php

namespace App\Controllers;

use App\Models\RoleModel;

class Role extends BaseController
{
    protected $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        if (!has_permission('manage_roles')) return redirect()->to('/dashboard')->with('error', 'Akses Ditolak');

        $data = [
            'title' => 'Manajemen Hak Akses & Role',
            'roles' => $this->roleModel->findAll(),
            'available_permissions' => RoleModel::getAvailablePermissions()
        ];
        return view('role/index', $data);
    }

    public function create()
    {
        if (!has_permission('manage_roles')) return redirect()->to('/dashboard');

        $data = [
            'title' => 'Tambah Role Baru',
            'available_permissions' => RoleModel::getAvailablePermissions()
        ];
        return view('role/create', $data);
    }

    public function store()
    {
        if (!has_permission('manage_roles')) return redirect()->to('/dashboard');

        $name = strtolower(trim($this->request->getPost('name')));
        if (in_array($name, ['admin', 'anggota'])) {
            return redirect()->back()->with('error', 'Nama role admin / anggota adalah nama yang dicadangkan sistem.');
        }

        $permissions = $this->request->getPost('permissions') ?? [];

        $this->roleModel->save([
            'name' => $name,
            'description' => $this->request->getPost('description'),
            'permissions' => json_encode($permissions)
        ]);

        return redirect()->to('/role')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!has_permission('manage_roles')) return redirect()->to('/dashboard');

        $role = $this->roleModel->find($id);
        if (!$role) return redirect()->to('/role')->with('error', 'Role tidak ditemukan.');

        $data = [
            'title' => 'Edit Role & Akses',
            'role' => $role,
            'current_permissions' => json_decode($role['permissions'] ?? '[]', true),
            'available_permissions' => RoleModel::getAvailablePermissions()
        ];
        return view('role/edit', $data);
    }

    public function update($id)
    {
        if (!has_permission('manage_roles')) return redirect()->to('/dashboard');

        $role = $this->roleModel->find($id);
        if (!$role) return redirect()->to('/role');

        $name = strtolower(trim($this->request->getPost('name')));
        $permissions = $this->request->getPost('permissions') ?? [];

        $this->roleModel->update($id, [
            'name' => $name,
            'description' => $this->request->getPost('description'),
            'permissions' => json_encode($permissions) // Update the json permissions
        ]);

        return redirect()->to('/role')->with('success', 'Berhasil memperbarui hak akses role ' . $role['name']);
    }

    public function delete($id)
    {
        if (!has_permission('manage_roles')) return redirect()->to('/dashboard');

        $role = $this->roleModel->find($id);
        if ($role && in_array($role['name'], ['admin', 'anggota'])) {
            return redirect()->to('/role')->with('error', 'Role default admin dan anggota tidak boleh dihapus.');
        }

        $this->roleModel->delete($id);
        return redirect()->to('/role')->with('success', 'Role berhasil dihapus.');
    }
}
