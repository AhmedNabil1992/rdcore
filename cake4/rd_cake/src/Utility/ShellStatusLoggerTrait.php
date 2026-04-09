<?php
declare(strict_types=1);

namespace App\Utility;

use Cake\Datasource\ConnectionManager;
use Cake\I18n\FrozenTime;

trait ShellStatusLoggerTrait
{
    protected function recordShellSuccess(string $name): void
    {
        $connection = ConnectionManager::get('default');
        $lastRun = FrozenTime::now()->format('Y-m-d H:i:s');

        $existing = $connection
            ->execute('SELECT id FROM shell_status WHERE name = :name LIMIT 1', ['name' => $name])
            ->fetch('assoc');

        if ($existing) {
            $connection->execute(
                'UPDATE shell_status SET last_run = :last_run WHERE id = :id',
                [
                    'last_run' => $lastRun,
                    'id' => $existing['id'],
                ]
            );

            return;
        }

        $connection->execute(
            'INSERT INTO shell_status (name, last_run) VALUES (:name, :last_run)',
            [
                'name' => $name,
                'last_run' => $lastRun,
            ]
        );
    }
}
