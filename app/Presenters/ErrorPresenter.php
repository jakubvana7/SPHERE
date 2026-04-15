<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;

final class ErrorPresenter extends Presenter
{
    public function renderDefault(\Throwable $exception): void
    {
        $this->setLayout(false);
        if ($exception instanceof BadRequestException) {
            $code = $exception->getCode();
            $this->setView(in_array($code, [403, 404, 405, 410, 501]) ? (string) $code : '4xx');
            $this->template->code    = $code;
            $this->template->message = $exception->getMessage() ?: 'Stránka nebyla nalezena.';
        } else {
            $this->setView('500');
            $this->template->code    = 500;
            $this->template->message = 'Došlo k chybě serveru.';
        }
    }
}
