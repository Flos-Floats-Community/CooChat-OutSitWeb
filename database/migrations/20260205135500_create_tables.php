<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTables extends Migrator
{
    public function change()
    {
        // 创建用户表
        $userTable = $this->table('user', ['prefix' => 'cc_', 'engine' => 'InnoDB']);
        $userTable->addColumn('username', 'string', ['limit' => 50, 'comment' => '用户名'])
                  ->addColumn('password', 'string', ['limit' => 255, 'comment' => '密码哈希值'])
                  ->addColumn('ccal', 'string', ['limit' => 20, 'unique' => true, 'comment' => '类似QQ号'])
                  ->addColumn('now', 'string', ['limit' => 100, 'null' => true, 'comment' => '状态'])
                  ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
                  ->addColumn('updated_at', 'datetime', ['comment' => '更新时间'])
                  ->create();
        
        // 创建消息表
        $messageTable = $this->table('message', ['prefix' => 'cc_', 'engine' => 'InnoDB']);
        $messageTable->addColumn('sender_ccal', 'string', ['limit' => 20, 'comment' => '发送者CCAL'])
                     ->addColumn('receiver_ccal', 'string', ['limit' => 20, 'comment' => '接收者CCAL'])
                     ->addColumn('content', 'text', ['comment' => '消息内容'])
                     ->addColumn('message_type', 'string', ['limit' => 20, 'default' => 'text', 'comment' => '消息类型'])
                     ->addColumn('is_read', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '是否已读'])
                     ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
                     ->create();
        
        // 创建索引
        $this->db->query('CREATE INDEX idx_user_ccal ON cc_user (ccal)');
        $this->db->query('CREATE INDEX idx_user_username ON cc_user (username)');
        $this->db->query('CREATE INDEX idx_message_sender ON cc_message (sender_ccal)');
        $this->db->query('CREATE INDEX idx_message_receiver ON cc_message (receiver_ccal)');
        $this->db->query('CREATE INDEX idx_message_read ON cc_message (receiver_ccal, is_read)');
    }
}
