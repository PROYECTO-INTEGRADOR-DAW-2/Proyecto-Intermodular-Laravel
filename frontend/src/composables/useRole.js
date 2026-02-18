import { storeToRefs } from 'pinia'
import { useAuthStore } from '../stores/auth'

export function useRole() {
    const authStore = useAuthStore()
    const { user } = storeToRefs(authStore)

    /**
     * Get all roles for the current user.
     * Checks BOTH user.role (direct column) and user.roles (pivot array).
     */
    const getAllRoles = () => {
        if (!user.value) return []

        const roleSet = new Set()

        // 1. Direct role column (e.g. user.role = 'admin')
        if (user.value.role) {
            roleSet.add(user.value.role.toLowerCase())
        }

        // 2. Pivot roles array (e.g. user.roles = ['admin', 'editor'])
        if (user.value.roles) {
            const rolesArr = Array.isArray(user.value.roles) ? user.value.roles : [user.value.roles]
            rolesArr.forEach(r => {
                const name = typeof r === 'string' ? r : r.name
                if (name) roleSet.add(name.toLowerCase())
            })
        }

        return Array.from(roleSet)
    }

    const can = (permission) => {
        const userRoles = getAllRoles()
        if (userRoles.length === 0) return false

        const rules = {
            admin: ['create', 'edit', 'delete', 'moderate', 'view_admin_panel'],
            vendor: ['create', 'edit', 'delete'],
            editor: ['moderate'],
            user: ['read']
        }

        return userRoles.some(role => {
            const rolePermissions = rules[role]
            return rolePermissions && rolePermissions.includes(permission)
        })
    }

    const hasRole = (roleName) => {
        const userRoles = getAllRoles()
        return userRoles.includes(roleName.toLowerCase())
    }

    return { can, hasRole }
}
