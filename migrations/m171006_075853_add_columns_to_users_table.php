<?php

use yii\db\Migration;

class m171006_075853_add_columns_to_users_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('users', 'auth_key', $this->string(32));
        $this->addColumn('users', 'password_hash', $this->string());
        $this->addColumn('users', 'password_reset_token', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('users', 'auth_key');
        $this->dropColumn('users', 'password_hash');
        $this->dropColumn('users', 'password_reset_token');

        return false;
    }
}
