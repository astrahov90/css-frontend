<?php

namespace core\traits;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

trait TwigTemplateEngineTrait
{
    private Environment $templateEngine;

    public function setTemplateEngine(): void
    {
        $loader = new FilesystemLoader($this->getBaseDir().'/views/');
        $twig = new Environment($loader, [
            'cache' => $this->getBaseDir().'/views/cache',
        ]);

        $this->templateEngine = $twig;
    }

    protected function renderTemplate(string $templateName, array $data): string
    {
        $this->templateEngine->addGlobal('session', $_SESSION);
        return $this->templateEngine->render(str_replace('\\', DIRECTORY_SEPARATOR,$templateName), $data);
    }

    public function render(string $templateName, array $data=[]): string
    {
       return $this->renderTemplate($templateName, $data);
    }
}