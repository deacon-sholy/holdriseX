<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KycSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            ['user_id' => 2, 'document_type' => 'passport', 'status' => 'verified', 'admin_note' => 'Documents verified successfully'],
            ['user_id' => 3, 'document_type' => 'id_card', 'status' => 'verified', 'admin_note' => 'All requirements met'],
            ['user_id' => 4, 'document_type' => 'drivers_license', 'status' => 'verified', 'admin_note' => 'Approved'],
            ['user_id' => 5, 'document_type' => 'passport', 'status' => 'verified', 'admin_note' => 'Identity confirmed'],
            ['user_id' => 7, 'document_type' => 'id_card', 'status' => 'pending', 'admin_note' => null],
            ['user_id' => 10, 'document_type' => 'passport', 'status' => 'verified', 'admin_note' => 'Documents clear and valid'],
            ['user_id' => 11, 'document_type' => 'drivers_license', 'status' => 'rejected', 'admin_note' => 'Image quality too low. Please resubmit.'],
            ['user_id' => 13, 'document_type' => 'id_card', 'status' => 'under_review', 'admin_note' => null],
            ['user_id' => 14, 'document_type' => 'passport', 'status' => 'verified', 'admin_note' => 'Verified'],
            ['user_id' => 16, 'document_type' => 'id_card', 'status' => 'pending', 'admin_note' => null],
        ];

        foreach ($documents as $doc) {
            $created = now()->subDays(rand(5, 60));
            $reviewed = null;

            if (in_array($doc['status'], ['verified', 'rejected'])) {
                $reviewed = $created->copy()->addHours(rand(1, 72));
            }

            DB::table('kyc_documents')->insert([
                'user_id' => $doc['user_id'],
                'document_type' => $doc['document_type'],
                'front_image' => "kyc/{$doc['user_id']}_front_" . time() . ".jpg",
                'back_image' => "kyc/{$doc['user_id']}_back_" . time() . ".jpg",
                'status' => $doc['status'],
                'admin_note' => $doc['admin_note'],
                'reviewed_at' => $reviewed,
                'created_at' => $created,
                'updated_at' => $reviewed ?? $created,
            ]);
        }
    }
}
