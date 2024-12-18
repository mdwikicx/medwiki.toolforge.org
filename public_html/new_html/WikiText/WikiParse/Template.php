<?php

namespace WikiParse\Template;

/*
Usage:

use function WikiParse\Template\getTemplate;

*/


class Template
{
    private string $template;
    private string $name;
    private string $name_strip;
    private string $templateText;
    private array $parameters;
    public function __construct(string $name, array $parameters = [], string $templateText = "")
    {
        $this->name = $name;
        $this->name_strip = trim(str_replace('_', ' ', $name));
        $this->parameters = $parameters;
        $this->templateText = $templateText;
    }
    public function getTemplateText(): string
    {
        return $this->templateText;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getStripName(): string
    {
        return $this->name_strip;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
    public function deleteParameter(string $key): void
    {
        if (array_key_exists($key, $this->parameters)) {
            unset($this->parameters[$key]);
        }
    }
    public function getParameter(string $key): string
    {
        return $this->parameters[$key] ?? "";
    }
    public function setTempName(string $name): void
    {
        $this->name = $name;
    }
    public function setParameter(string $key, string $value): void
    {
        $this->parameters[$key] = $value;
    }
    public function changeParameterName(string $old, string $new): void
    {
        $newParameters = [];
        foreach ($this->parameters as $k => $v) {
            if ($k === $old) {
                $k = $new;
            };
            $newParameters[$k] = $v;
        }
        $this->parameters = $newParameters;
    }

    public function changeParametersNames(array $params_new): void
    {
        $newParameters = [];
        foreach ($this->parameters as $k => $v) {
            $k = isset($params_new[$k]) ? $params_new[$k] : $k;
            $newParameters[$k] = $v;
        }
        $this->parameters = $newParameters;
    }

    public function toString(bool $newLine = false, $ljust = 0): string
    {
        $line = $newLine ? "\n" : "";
        $this->template = $newLine ? "{{" . trim($this->name) : "{{" . $this->name;
        $i = 1;
        foreach ($this->parameters as $key => $value) {
            $value = $newLine ? trim($value) : $value;

            if ($i == $key) {
                $this->template .= "|" . $value;
            } else {
                if ($ljust > 0) {
                    $key = str_pad($key, $ljust, " ");
                }
                $this->template .= $line . "|" . $key . " = " . $value;
            }
            $i++;
        }
        $this->template .= $line . "}}";
        return $this->template;
    }
}


class ParserTemplate
{
    private string $templateText;
    private string $name;
    private array $parameters;
    private string $pipe = "|";
    private string $pipeR = "-_-";
    public function __construct(string $templateText)
    {
        $this->templateText = trim($templateText);
        $this->parameters = array();
        $this->parse();
    }
    public function parse(): void
    {
        if (preg_match("/^\{\{(.*?)(\}\})$/s", $this->templateText, $matchesR)) {
            $DTemplate = $matchesR[1];
            $matches = [];
            preg_match_all("/\{\{(.*?)\}\}/", $DTemplate, $matches);
            foreach ($matches[1] as $matche) {
                $DTemplate = str_replace($matche, str_replace($this->pipe, $this->pipeR, $matche), $DTemplate);
            }
            $matches = [];
            preg_match_all("/\[\[(.*?)\]\]/", $DTemplate, $matches);
            foreach ($matches[1] as $matche) {
                $DTemplate = str_replace($matche, str_replace($this->pipe, $this->pipeR, $matche), $DTemplate);
            }

            $params = explode("|", $DTemplate);
            $pipeR = $this->pipeR;
            $pipe = $this->pipe;
            $params = array_map(function ($string) use ($pipeR, $pipe) {
                return str_replace($pipeR, $pipe, $string);
            }, $params);
            $data = [];
            $this->name = $params[0];
            for ($i = 1; $i < count($params); $i++) {
                $param = $params[$i];
                if (strpos($param, "=") !== false) {
                    $parts = explode("=", $param, 2);
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    $data[$key] = $value;
                } else {
                    $data[$i] = $param;
                }
            }
            $this->parameters = $data;
        }
    }
    public function getTemplate(): Template
    {
        return new Template($this->name, $this->parameters, $this->templateText);
    }
}

function getTemplate($text)
{
    $parser = new ParserTemplate($text);
    $temp = $parser->getTemplate();
    return $temp;
}
