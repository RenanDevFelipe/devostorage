<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMovimentacoesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'produto_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],

            'usuario_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],

            'tipo' => [
                'type'       => 'ENUM("entrada", "saida")',
                'null'       => false,
            ],

            'quantidade' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
            ],

            'data' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey('produto_id', 'produtos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('usuario_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('movimentacoes', true);
    }

    public function down()
    {
        $this->forge->dropTable('movimentacoes', true);
    }
}
