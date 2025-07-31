<?php

use Filament\Pages\SubNavigationPosition;

return [

    /**
     * Указать названия таблицы перед импортом миграцией
     */
    'table_name' => 'md_email_templates',

    /**
     * Классы почты будут созданы в этом каталоге.
     */
    'mailable_directory' => 'Mail/Maksde/EmailTemplates',

    /**
     * Дефолтный язык в шаблоне электронного письма.
     */
    'default_locale' => 'ru',

    /**
     * Языки для которых будут заводиться шаблоны электронного письма.
     */
    'languages' => [
        'ru' => 'Русский',
        //        'en_GB' => 'British',
        //        'en_US' => 'USA',
    ],

    /**
     * Параметры навигации панели администратора
     */
    'navigation' => [
        'enabled' => true,
        'templates' => [
            'sort' => 10,
            'label' => 'Шаблоны электронных писем',
            'icon' => 'heroicon-o-envelope',
            'group' => 'Управление',
            'cluster' => false,
            'position' => SubNavigationPosition::Top,
        ],
    ],

];
