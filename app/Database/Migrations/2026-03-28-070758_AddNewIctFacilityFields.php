<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewIctFacilityFields extends Migration
{
    public function up()
    {
        $fields = [
            'ict_os_install' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'ict_printer'
            ],
            'ict_admin_pass_change' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'ict_os_install'
            ],
            'ict_comp_name_change' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'ict_admin_pass_change'
            ],
            'ict_updated_comp_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'ict_comp_name_change'
            ],
            'ict_domain_add' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'ict_updated_comp_name'
            ],
            'soft_eset' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'soft_pronto'
            ],
            'soft_office365' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'soft_eset'
            ],
            'soft_chrome' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'soft_office365'
            ],
            'soft_pdf_reader' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'soft_chrome'
            ],
            'soft_vlc' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'soft_pdf_reader'
            ],
            'soft_winrar' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'soft_vlc'
            ],
            'soft_zoom' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'soft_winrar'
            ],
        ];

        $this->forge->addColumn('onboarding_details', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('onboarding_details', [
            'ict_os_install',
            'ict_admin_pass_change',
            'ict_comp_name_change',
            'ict_updated_comp_name',
            'ict_domain_add',
            'soft_eset',
            'soft_office365',
            'soft_chrome',
            'soft_pdf_reader',
            'soft_vlc',
            'soft_winrar',
            'soft_zoom'
        ]);
    }
}
