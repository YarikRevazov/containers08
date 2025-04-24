<?php

class Page {
    private $template;

    // Загружаем шаблон при создании объекта
    public function __construct($template) {
        $this->template = file_get_contents($template);
    }

    // Подстановка данных в шаблон
    public function Render($data) {
        $output = $this->template;
        foreach ($data as $key => $value) {
            // Подставляем данные вместо {{ ключ }}
            $output = str_replace("{{ $key }}", htmlspecialchars($value), $output);
        }
        return $output;
    }
}
