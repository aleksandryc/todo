<template>
  <div id="app">
    <h1>Демонстрация динамических форм</h1>

    <div class="controls">
      <label for="form-selector">Выберите форму: </label>
      <select id="form-selector" v-model="selectedFormKey">
        <option value="userProfile">Профиль пользователя (с dependsOn, hidden)</option>
        <option value="contactUs">Связаться с нами (с notes, dependsOn)</option>
        <option value="advanceFormExample">Расширенная форма (AGENTS.md)</option>
        <option value="addressForm">Форма с адресом (вложенная)</option>
        <option value="nonExistentForm">Несуществующая форма</option>
      </select>
      <button @click="preloadData" style="margin-left: 10px;">Предзаполнить данные</button>
      <button @click="clearPreloadedData" style="margin-left: 10px;">Очистить предзаполненные</button>
      <label style="margin-left: 20px;">
        <input type="checkbox" v-model="showDebug"/> Показать отладочную информацию
      </label>
    </div>

    <div class="form-container">
      <DynamicForm
        v-if="selectedFormKey"
        :key="selectedFormKey + JSON.stringify(currentInitialData)"
        :form-key="selectedFormKey"
        :initial-data="currentInitialData"
        @submit="handleFormSubmit"
        @form-loaded="onFormLoaded"
        @form-error="onFormError"
        :debug-mode="showDebug"
      />
      <p v-else>Пожалуйста, выберите форму для отображения.</p>
    </div>

    <div v-if="submittedData" class="submission-result">
      <h3>Данные отправлены:</h3>
      <pre>{{ submittedData }}</pre>
    </div>

    <div v-if="formLoadMessage" class="load-message">
      <p>{{ formLoadMessage }}</p>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import DynamicForm from './components/DynamicForm.vue';
import type { FormData, FormConfiguration } from './types';

const availableFormKeys = ['userProfile', 'contactUs', 'advanceFormExample', 'addressForm', 'nonExistentForm'];
const selectedFormKey = ref<string>(availableFormKeys[0]);
const submittedData = ref<FormData | null>(null);
const formLoadMessage = ref<string>('');
const showDebug = ref<boolean>(true);

const initialProfileData: Partial<FormData> = {
  username: 'JaneDoe',
  email: 'jane.doe@example.com',
  personalSite: 'https://janesite.com',
  bio: 'Люблю Vue и динамические формы! Опыт 5 лет.',
  experienceYears: 5,
  notifications: false,
  // Для smsNotifications (dependsOn 'phone')
  phone: '+79201234567', // Если телефон есть, smsNotifications должен стать активным
  smsNotifications: true, // Предзаполняем, чтобы проверить dependsOn
};

const initialAddressData: Partial<FormData> = {
  fullName: 'Иван Иванов',
  'deliveryAddress.street': 'ул. Центральная, д. 10, кв. 5',
  'deliveryAddress.city': 'Москва',
  'deliveryAddress.zipCode': '123456', // Должен быть строкой для pattern валидации
  comments: 'Позвонить за час до доставки'
};

};

const initialContactUsData: Partial<FormData> = {
  fullName: 'Петр Сидоров',
  contactEmail: 'petr@example.com',
  subject: 'support', // Это должно сделать поле department активным
  message: 'Проблема с доступом к системе.',
  department: 'IT отдел', // Предзаполняем, чтобы проверить dependsOn
  termsAccepted: true,
};

const initialAdvanceFormData: Partial<FormData> = {
  employmentType: "Full Time",
  accessNeeds: ["External VPN Access"],
  plantLocation: "Winkler"
};


const currentInitialData = ref<Partial<FormData> | undefined>(undefined);

watch(selectedFormKey, (newKey) => {
  // Очищаем preloadedDataForForm при смене формы, чтобы initialData не переносились на другую форму
  currentInitialData.value = undefined;
  formLoadMessage.value = ''; // Очищаем сообщение о загрузке
  submittedData.value = null; // Очищаем результат отправки
}, { immediate: true });


const handleFormSubmit = (data: FormData) => {
  console.log('Форма отправлена из App.vue:', data);
  submittedData.value = data;
  alert('Данные формы отправлены! (см. консоль и блок "Данные отправлены")');
};

const onFormLoaded = (config: FormConfiguration) => {
  formLoadMessage.value = `Форма "${config.title || config.key}" (Описание: ${config.description || 'N/A'}) успешно загружена.`;
  // submittedData.value = null; // Не очищаем здесь, чтобы видеть результат после предзаполнения и сабмита
};

const onFormError = (error: string) => {
  formLoadMessage.value = `Ошибка загрузки формы: ${error}`;
  submittedData.value = null;
};

const preloadData = () => {
  submittedData.value = null; // Очищаем предыдущий результат
  if (selectedFormKey.value === 'userProfile') {
    currentInitialData.value = { ...initialProfileData };
    formLoadMessage.value = 'Данные для "Профиль пользователя" предзаполнены.';
  } else if (selectedFormKey.value === 'addressForm') {
    currentInitialData.value = { ...initialAddressData };
    formLoadMessage.value = 'Данные для "Форма с адресом" предзаполнены.';
  } else if (selectedFormKey.value === 'contactUs') {
    currentInitialData.value = { ...initialContactUsData };
    formLoadMessage.value = 'Данные для "Связаться с нами" предзаполнены.';
  } else if (selectedFormKey.value === 'advanceFormExample') {
    currentInitialData.value = { ...initialAdvanceFormData };
    formLoadMessage.value = 'Данные для "Расширенная форма" предзаполнены.';
  } else {
    currentInitialData.value = undefined;
    formLoadMessage.value = `Для формы "${selectedFormKey.value}" нет данных для предзаполнения.`;
  }
};

const clearPreloadedData = () => {
  submittedData.value = null; // Очищаем предыдущий результат
  currentInitialData.value = undefined;
  formLoadMessage.value = 'Предзаполненные данные очищены.';
};

</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  margin: 20px;
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
}

.controls {
  margin-bottom: 20px;
  padding: 10px;
  background-color: #f5f5f5;
  border-radius: 4px;
}

.controls label {
  margin-right: 10px;
}

.controls select {
  padding: 8px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

.form-container {
  padding: 20px;
  border: 1px solid #eee;
  border-radius: 4px;
  background-color: #fff;
}

.submission-result {
  margin-top: 20px;
  padding: 15px;
  background-color: #e6ffed;
  border: 1px solid #b2fcc2;
  border-radius: 4px;
}

.submission-result h3 {
  margin-top: 0;
  color: #006422;
}

.submission-result pre {
  background-color: #f0fff4;
  padding: 10px;
  border-radius: 4px;
  white-space: pre-wrap;
  word-wrap: break-word;
}

.load-message {
  margin-top: 15px;
  padding: 10px;
  background-color: #f0f8ff;
  border: 1px solid #cce7ff;
  border-radius: 4px;
}
</style>
