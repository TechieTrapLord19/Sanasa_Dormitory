<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'first_name' => 'Juan',
                'middle_name' => 'Santos',
                'last_name' => 'Cruz',
                'email' => 'juan.cruz@email.com',
                'address' => '123 Main Street, Manila',
                'birth_date' => '1995-03-15',
                'id_document' => 'ID-001-2023',
                'contact_num' => '09171234567',
                'emer_contact_num' => '09187654321',
                'status' => 'active',
            ],
            [
                'first_name' => 'Maria',
                'middle_name' => 'Garcia',
                'last_name' => 'Reyes',
                'email' => 'maria.reyes@email.com',
                'address' => '456 Oak Avenue, Quezon City',
                'birth_date' => '1998-07-22',
                'id_document' => 'ID-002-2023',
                'contact_num' => '09189876543',
                'emer_contact_num' => '09165432109',
                'status' => 'active',
            ],
            [
                'first_name' => 'Pedro',
                'middle_name' => 'Dela',
                'last_name' => 'Rosa',
                'email' => 'pedro.rosa@email.com',
                'address' => '789 Pine Street, Makati',
                'birth_date' => '1992-11-10',
                'id_document' => 'ID-003-2023',
                'contact_num' => '09156789012',
                'emer_contact_num' => '09123456789',
                'status' => 'active',
            ],
            [
                'first_name' => 'Ana',
                'middle_name' => 'Luisa',
                'last_name' => 'Santos',
                'email' => 'ana.santos@email.com',
                'address' => '321 Elm Road, Cebu',
                'birth_date' => '1996-05-08',
                'id_document' => 'ID-004-2023',
                'contact_num' => '09145678901',
                'emer_contact_num' => '09134567890',
                'status' => 'active',
            ],
            [
                'first_name' => 'Carlos',
                'middle_name' => 'Miguel',
                'last_name' => 'Fernandez',
                'email' => 'carlos.fernandez@email.com',
                'address' => '654 Maple Drive, Davao',
                'birth_date' => '1994-09-20',
                'id_document' => 'ID-005-2023',
                'contact_num' => '09167890123',
                'emer_contact_num' => '09156789012',
                'status' => 'active',
            ],
            [
                'first_name' => 'Rosa',
                'middle_name' => 'Patricia',
                'last_name' => 'Morales',
                'email' => 'rosa.morales@email.com',
                'address' => '987 Cedar Lane, Laguna',
                'birth_date' => '1999-01-14',
                'id_document' => 'ID-006-2023',
                'contact_num' => '09178901234',
                'emer_contact_num' => '09167890123',
                'status' => 'active',
            ],
            [
                'first_name' => 'Jose',
                'middle_name' => 'Antonio',
                'last_name' => 'Lopez',
                'email' => 'jose.lopez@email.com',
                'address' => '147 Birch Boulevard, Antipolo',
                'birth_date' => '1997-06-25',
                'id_document' => 'ID-007-2023',
                'contact_num' => '09189012345',
                'emer_contact_num' => '09178901234',
                'status' => 'active',
            ],
            [
                'first_name' => 'Angela',
                'middle_name' => 'Rose',
                'last_name' => 'Gonzalez',
                'email' => 'angela.gonzalez@email.com',
                'address' => '258 Walnut Way, Valenzuela',
                'birth_date' => '1993-12-03',
                'id_document' => 'ID-008-2023',
                'contact_num' => '09190123456',
                'emer_contact_num' => '09189012345',
                'status' => 'active',
            ],
            [
                'first_name' => 'Ricardo',
                'middle_name' => 'James',
                'last_name' => 'Martinez',
                'email' => 'ricardo.martinez@email.com',
                'address' => '369 Spruce Street, Paranaque',
                'birth_date' => '1991-08-17',
                'id_document' => 'ID-009-2023',
                'contact_num' => '09201234567',
                'emer_contact_num' => '09190123456',
                'status' => 'active',
            ],
            [
                'first_name' => 'Sophia',
                'middle_name' => 'Marie',
                'last_name' => 'Diaz',
                'email' => 'sophia.diaz@email.com',
                'address' => '741 Ash Avenue, Pasig',
                'birth_date' => '2000-04-11',
                'id_document' => 'ID-010-2023',
                'contact_num' => '09212345678',
                'emer_contact_num' => '09201234567',
                'status' => 'active',
            ],
            [
                'first_name' => 'Miguel',
                'middle_name' => 'Angel',
                'last_name' => 'Ramos',
                'email' => 'miguel.ramos@email.com',
                'address' => '852 Pine Street, Quezon City',
                'birth_date' => '1992-11-30',
                'id_document' => 'ID-011-2023',
                'contact_num' => '09223456789',
                'emer_contact_num' => '09212345678',
                'status' => 'active',
            ],
            [
                'first_name' => 'Isabella',
                'middle_name' => 'Grace',
                'last_name' => 'Vargas',
                'email' => 'isabella.vargas@email.com',
                'address' => '963 Elm Street, Mandaluyong',
                'birth_date' => '1995-07-22',
                'id_document' => 'ID-012-2023',
                'contact_num' => '09234567890',
                'emer_contact_num' => '09223456789',
                'status' => 'active',
            ],
            [
                'first_name' => 'Andres',
                'middle_name' => 'Felipe',
                'last_name' => 'Cabrera',
                'email' => 'agdasgkdsah@sajdavh',
                'address' => '159 Oak Lane, Bacoor',
                'birth_date' => '1994-03-05',
                'id_document' => 'ID-013-2023',
                'contact_num' => '09245678901',
                'emer_contact_num' => '09234567890',
                'status' => 'active',

            ],
            [
                'first_name' => 'Camila',
                'middle_name' => 'Isabel',
                'last_name' => 'Navarro',
                'email' => 'camila.navarro@email.com',
                'address' => '753 Maple Drive, Las Piñas',
                'birth_date' => '1996-09-14',
                'id_document' => 'ID-014-2023',
                'contact_num' => '09256789012',
                'emer_contact_num' => '09245678901',
                'status' => 'active',
            ],
        ];

        foreach ($tenants as $tenant) {
            Tenant::create($tenant);
        }
    }
}
