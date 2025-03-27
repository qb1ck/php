<?php
namespace App;
use App\controllers\UserController;

class Console
{
    public function handle(array $argv): void
    {
        $command = $argv[1] ?? null;

        switch ($command) {
            case 'users-list':
                UserController::index();
                break;

            case 'create-user':
                UserController::store();
                break;

            case 'delete-user':
                $id = $argv[2] ?? null;
                if ($id) {
                    UserController::delete($id);
                } else {
                    echo "Ошибка: укажи ID пользователя для удаления.\n";
                }
                break;



            default:
                echo "Неизвестная команда: {$command}\n";
                echo "Доступные команды:\n";
                echo "  users-list\n";
                echo "  create-user [name] [email] [password]\n";
                echo "  delete-user [id]\n";
                break;
        }
    }
}