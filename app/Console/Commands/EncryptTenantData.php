<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\Tenant;

class EncryptTenantData extends Command
{
    protected $signature   = 'tenants:encrypt';
    protected $description = 'Re-encrypt existing tenant sensitive fields with AES-256 encryption';

    public function handle(): void
    {
        $this->info('Encrypting existing tenant sensitive data...');

        $fields = ['address', 'contact_num', 'emer_contact_num', 'email', 'id_document', 'emer_contact_name'];

        // Read raw rows (bypasses Eloquent casts so we get plaintext values)
        $rows = DB::table('tenants')->get();

        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        foreach ($rows as $row) {
            $updates = [];

            foreach ($fields as $field) {
                $value = $row->$field ?? null;

                if ($value === null) {
                    continue;
                }

                // Skip if already encrypted (Crypt values start with "eyJ")
                if (str_starts_with($value, 'eyJ')) {
                    continue;
                }

                // Encrypt the plaintext value
                $updates[$field] = Crypt::encryptString($value);
            }

            if (! empty($updates)) {
                DB::table('tenants')
                    ->where('tenant_id', $row->tenant_id)
                    ->update($updates);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done! All sensitive tenant fields are now encrypted.');
    }
}
