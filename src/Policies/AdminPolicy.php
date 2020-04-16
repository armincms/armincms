<?php

namespace Armincms\Policies;
 
use Illuminate\Contracts\Auth\Authenticatable as Admin;
use Illuminate\Auth\Access\HandlesAuthorization; 
use Auth;

class AdminPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any admins.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return mixed
     */
    public function viewAny(Admin $user)
    {
        return Auth::guard('admin')->check();
    }

    /**
     * Determine whether the user can view the admin.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Armincms\Sofre\Admin  $admin
     * @return mixed
     */
    public function view(Admin $user, Admin $admin)
    {
        return $admin->username != 'superadministrator' && Auth::guard('admin')->check();
    }

    /**
     * Determine whether the user can create admins.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return mixed
     */
    public function create(Admin $user)
    {
        return Auth::guard('admin')->check();
    }

    /**
     * Determine whether the user can update the admin.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Armincms\Sofre\Admin  $admin
     * @return mixed
     */
    public function update(Admin $user, Admin $admin)
    {
        return $admin->username != 'superadministrator' && Auth::guard('admin')->check();
    }

    /**
     * Determine whether the user can delete the admin.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Armincms\Sofre\Admin  $admin
     * @return mixed
     */
    public function delete(Admin $user, Admin $admin)
    {
        return $admin->username != 'superadministrator' && Auth::guard('admin')->check();
    }

    /**
     * Determine whether the user can restore the admin.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Armincms\Sofre\Admin  $admin
     * @return mixed
     */
    public function restore(Admin $user, Admin $admin)
    {
        return $admin->username != 'superadministrator' && Auth::guard('admin')->check();
    }

    /**
     * Determine whether the user can permanently delete the admin.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Armincms\Sofre\Admin  $admin
     * @return mixed
     */
    public function forceDelete(Admin $user, Admin $admin)
    {
        return $admin->username != 'superadministrator' && Auth::guard('admin')->check();
    }
}
