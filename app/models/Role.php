<?php
class Role {
    public function getRoles() {
        return [
            'SUPERADMIN',
            'ADMIN_PROVINSI',
            'ADMIN_UPPD',
            'OPERATOR'
        ];
    }
}