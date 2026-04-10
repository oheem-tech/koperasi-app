<?php

if (!function_exists('has_permission')) {
    /**
     * Checks whether the currently logged in user has a specific permission
     * by looking into their session data.
     * 
     * @param string $permission Identifer of the permission to check (e.g. 'manage_anggota')
     * @return bool True if allowed, False otherwise
     */
    function has_permission($permission)
    {
        // By default, system assumes session holds a 'permissions' array (JSON decoded)
        $userPermissions = session()->get('permissions');
        
        if (is_array($userPermissions) && in_array($permission, $userPermissions)) {
            return true;
        }

        return false;
    }
}
