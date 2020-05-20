<?php
/**
 * @Author: bthrower
 * @CreateAt: 8/29/2019 3:25 PM
 * Project: intranet-widgets-dev
 * File Name: RolesRepository.php
 */

namespace App\Security;

class RolesRepository
{
    /**
     * CURRENT AVAILABLE ROLES.
     *
     * After creating a new role, you must update the following documents accordingly
     *  easy_admin.yaml : Add the role to easy_admin:Member:form:choices
     *
     * @var array
     */
    private const ROLES = [
        [
            'name' => 'Member',
            'role' => 'ROLE_USER',
            'description' => 'SGB Member',
        ],
        [
            'name' => 'Admin',
            'role' => 'ROLE_ADMIN',
            'description' => 'Manages all administrative functions',
        ],
    ];

    /**
     * Static getChoices Method is used to get roles for
     * Symfony Forms.
     *
     * @return array
     */
    public static function getChoices()
    {
        $choices = [];
        foreach (self::ROLES as $ROLE) {
            $choices[$ROLE['role']] = $ROLE['name'];
        }

        return $choices;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return self::ROLES;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function isRole(string $role)
    {
        foreach (self::ROLES as $curRole) {
            if ($curRole['role'] === $role) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $role
     *
     * @return array|null
     */
    public function getRole(string $role)
    {
        if ($this->isRole($role)) {
            foreach (self::ROLES as $curRole) {
                if ($curRole['role'] === $role) {
                    return $curRole;
                }
            }
        }

        return null;
    }
}
