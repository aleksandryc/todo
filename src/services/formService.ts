import type { FormConfiguration } from '../types';

// Имитация базы данных конфигураций форм
// В реальном приложении это будет запрашиваться с бэкенда (например, из config/userforms.php)
const mockFormConfigurations: Record<string, FormConfiguration> = {
  userProfile: {
    key: 'userProfile',
    title: 'Профиль пользователя',
    description: 'Здесь вы можете обновить информацию вашего профиля, включая имя пользователя, email и краткую биографию.',
    fields: [
      {
        name: 'username',
        label: 'Имя пользователя',
        type: 'text',
        defaultValue: 'JohnDoe',
        validation: [
          { type: 'required', message: 'Имя пользователя обязательно.' },
          { type: 'minLength', value: 3, message: 'Имя должно быть не менее 3 символов.' },
        ],
        attrs: { autocomplete: 'username' },
      },
      {
        name: 'email',
        label: 'Email',
        type: 'email',
        placeholder: 'your@example.com',
        validation: [
          { type: 'required', message: 'Email обязателен.' },
          { type: 'email', message: 'Введите корректный email адрес.' },
        ],
      },
      {
        name: 'personalSite',
        label: 'Личный сайт',
        type: 'url',
        placeholder: 'https://example.com',
        validation: [
            // Простое правило для примера, можно усложнить
            { type: 'pattern', value: '^https?://.+$', message: 'Введите корректный URL сайта.'}
        ]
      },
      {
        name: 'bio',
        label: 'О себе',
        type: 'textarea',
        placeholder: 'Расскажите немного о себе...',
        validation: [
          { type: 'maxLength', value: 200, message: 'Описание не должно превышать 200 символов.' }
        ]
      },
      {
        name: 'experienceYears',
        label: 'Опыт работы (лет)',
        type: 'number',
        min: 0,
        max: 50,
        step: 1,
        defaultValue: 0,
        validation: [
            {type: 'required', message: 'Укажите опыт работы.'}
        ]
      },
      {
        name: 'birthDate',
        label: 'Дата рождения',
        type: 'date',
        validation: [
          { type: 'required', message: 'Дата рождения обязательна.' }
        ]
      },
      {
        name: 'notifications',
        label: 'Получать уведомления по Email',
        type: 'checkbox', // Одиночный чекбокс
        defaultValue: true,
      },
      {
        name: 'smsNotifications',
        label: 'Получать SMS уведомления (требуется телефон)',
        type: 'checkbox',
        defaultValue: false,
        dependsOn: [
          { field: 'phone', condition: 'filled', initialDisabled: true }
        ]
      },
      {
        name: 'phone',
        label: 'Номер телефона (для SMS)',
        type: 'text', // Можно сделать type 'tel'
        placeholder: '+7 XXX XXX XX XX',
        attrs: { autocomplete: 'tel' },
        // Валидация телефона может быть сложной, здесь простой пример
        validation: [
            {type: 'pattern', value: '^\\+?[1-9]\\d{1,14}$', message: 'Введите корректный номер телефона.'}
        ],
      }
    ],
    submitEndpoint: '/api/user/profile',
    // Пример _hidden_fields из AGENTS.md
    hiddenFields: {
        "form_version": {
            label: "Версия формы", // label для hidden полей опционален, но может быть полезен для отладки
            value: "1.2"
        }
    }
  },
  contactUs: {
    key: 'contactUs',
    title: 'Свяжитесь с нами',
    description: 'Задайте нам вопрос или оставьте отзыв.',
    fields: [
      {
        name: 'fullName',
        label: 'Полное имя',
        type: 'text',
        validation: [{ type: 'required', message: 'Полное имя обязательно.' }]
      },
      {
        name: 'contactEmail',
        label: 'Контактный Email',
        type: 'email',
        validation: [
          { type: 'required', message: 'Контактный Email обязателен.' },
          { type: 'email', message: 'Введите корректный email.' }
        ]
      },
      {
        name: 'subject',
        label: 'Тема',
        type: 'select',
        placeholder: 'Выберите тему...',
        // PHP: 'options' => ["General", "Support", "Feedback"]
        // Преобразуем в { value: string, label: string }[]
        // Или ожидаем, что бэкенд уже так отдаст. Пока делаем так, будто бэкенд отдает правильно.
        options: [
          { value: 'general', label: 'Общий вопрос' },
          { value: 'support', label: 'Техническая поддержка' },
          { value: 'feedback', label: 'Обратная связь' }
        ],
        validation: [{ type: 'required', message: 'Выберите тему обращения.' }]
      },
      {
        name: 'message',
        label: 'Сообщение',
        type: 'textarea',
        rows: 5,
        validation: [{ type: 'required', message: 'Введите ваше сообщение.' }],
        attrs: { 'data-custom': 'message-field' }
      },
      {
        name: 'department',
        label: 'Отдел (если применимо)',
        type: 'text',
        dependsOn: [ // Поле будет доступно, только если выбрана тема "Техническая поддержка"
          { field: 'subject', condition: 'equals', value: 'support', initialDisabled: true }
        ]
      },
      {
        name: 'attachmentInfo',
        type: 'notes', // Пример поля notes
        label: 'Информация по вложениям', // label для notes тоже может быть
        value: 'Пожалуйста, прикладывайте скриншоты в форматах JPG или PNG, размером не более 5МБ.'
      },
      {
        name: 'termsAccepted',
        label: 'Я согласен с условиями обработки данных',
        type: 'checkbox', // Одиночный чекбокс
        validation: [
            {type: 'required', value: true, message: 'Необходимо принять условия.'} // Для чекбокса required обычно означает, что он должен быть true
        ]
      },
      {
        name: 'hiddenSource',
        type: 'hidden',
        defaultValue: 'contact-us-page'
      }
    ]
  },
  // Пример из AGENTS.md для radio, checkbox-group и select
  // Ключ "advance-form" и "plant"
  advanceFormExample: {
    key: 'advanceFormExample',
    title: 'Расширенная форма (Пример из AGENTS.md)',
    description: 'Демонстрация radio, checkbox-group и select.',
    fields: [
      {
        name: 'employmentType',
        label: 'Тип занятости',
        type: 'radio',
        // AGENTS.md: 'options' => ["Full Time", "Part Time"]
        // Преобразуем к нужному формату:
        options: [
          { value: "Full Time", label: "Full Time" },
          { value: "Part Time", label: "Part Time" }
        ],
        validation: [{type: 'required', message: 'Выберите тип занятости'}]
      },
      {
        name: 'accessNeeds',
        label: 'Необходимые доступы',
        type: 'checkbox', // Используем type: 'checkbox' для checkbox-group
        // AGENTS.md: "options" => ["External Email Access", "External VPN Access"]
        // Преобразуем:
        options: [
          { value: "External Email Access", label: "External Email Access" },
          { value: "External VPN Access", label: "External VPN Access" }
        ],
        // Для checkbox group, defaultValue должен быть массивом
        defaultValue: []
      },
      {
        name: 'plantLocation',
        label: 'Локация завода (Plant)',
        type: 'select',
        // AGENTS.md: "value" => [ ["value" => "Morden", "display" => "Morden"], ... ]
        // "value" в AGENTS.md для select - это на самом деле options.
        // "display" соответствует "label".
        options: [
          { value: "Morden", label: "Morden" },
          { value: "Winkler", label: "Winkler" }
        ],
        placeholder: 'Выберите локацию',
        validation: [{type: 'required', message: 'Выберите локацию завода'}]
      },
      {
        name: 'guidelines',
        type: 'notes',
        label: 'Руководство по заполнению',
        // AGENTS.md: 'notes-guide' => [ 'label' => ..., 'type' => 'notes', 'value' => ... ]
        value: 'Все поля, отмеченные звездочкой (*), обязательны для заполнения. Для получения дополнительной информации обратитесь к внутреннему порталу.'
      }
    ]
  },
  // Существующая addressForm, оставляем для проверки вложенности
  addressForm: {
    key: 'addressForm',
    title: 'Форма с адресом (вложенная структура)',
    description: 'Укажите ваш адрес доставки.',
    fields: [
      {
        name: 'fullName',
        label: 'ФИО получателя',
        type: 'text',
        validation: [{ type: 'required', message: 'ФИО обязательно' }]
      },
      {
        name: 'deliveryAddress',
        label: 'Адрес доставки',
        type: 'group',
        fields: [
          {
            name: 'street',
            label: 'Улица',
            type: 'text',
            validation: [{ type: 'required', message: 'Улица обязательна' }]
          },
          {
            name: 'city',
            label: 'Город',
            type: 'text',
            validation: [{ type: 'required', message: 'Город обязателен' }]
          },
          {
            name: 'zipCode',
            label: 'Почтовый индекс',
            type: 'text',
            validation: [
              { type: 'required', message: 'Индекс обязателен' },
              { type: 'pattern', value: '^\\d{6}$', message: 'Индекс должен состоять из 6 цифр' }
            ]
          }
        ]
      },
      {
        name: 'comments',
        label: 'Комментарии к заказу',
        type: 'textarea'
      }
    ]
  }
};

/**
 * Имитирует асинхронную загрузку конфигурации формы по ее ключу.
 * @param formKey Ключ формы для загрузки.
 * @returns Promise, который разрешается с конфигурацией формы или null, если не найдено.
 */
export const fetchFormConfiguration = (formKey: string): Promise<FormConfiguration | null> => {
  console.log(`[FormService] Запрос конфигурации для ключа: ${formKey}`);
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      const config = mockFormConfigurations[formKey];
      if (config) {
        console.log(`[FormService] Конфигурация для '${formKey}' найдена.`, config);
        resolve(JSON.parse(JSON.stringify(config))); // Возвращаем копию, чтобы избежать мутаций
      } else {
        console.warn(`[FormService] Конфигурация для '${formKey}' не найдена.`);
        resolve(null);
      }
      // Для имитации ошибки сети/сервера:
      // if (Math.random() > 0.8) {
      //   reject(new Error('Ошибка сети при загрузке конфигурации формы.'));
      // }
    }, 500); // Имитация задержки сети
  });
};
