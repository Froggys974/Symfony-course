<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SvgService
{
    private Filesystem $filesystem;
    private string $templatesPath;
    private string $defaultSvg;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->filesystem = new Filesystem();
        
        $this->templatesPath = $parameterBag->get('kernel.project_dir') . '/templates/svg/';
        $this->defaultSvg = 'svg/default.html.twig';
    }

    public function getSvgTemplate(string $categoryName): string
    {
        $categoryName = strtolower(trim($categoryName));
        $templatePath = $this->templatesPath . "{$categoryName}.html.twig";

        return $this->filesystem->exists($templatePath) ? "svg/{$categoryName}.html.twig" : $this->defaultSvg;
    }
}

