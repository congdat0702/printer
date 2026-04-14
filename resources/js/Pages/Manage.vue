<template>
    <Head title="Manage Users" />
    <AuthenticatedLayout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Users</h2>
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <h3>Create User</h3>
              <form @submit.prevent="createUser">
                <div class="mb-4">
                  <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                  <input type="text" id="name" v-model="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                  <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                  <input type="email" id="email" v-model="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                  <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                  <input type="password" id="password" v-model="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="flex items-center justify-between">
                  <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Create User
                  </button>
                </div>
              </form>
              <div v-if="flash.success" class="mt-4 p-4 bg-green-100 text-green-700 rounded">
                {{ flash.success }}
              </div>
              <div v-if="flash.error" class="mt-4 p-4 bg-red-100 text-red-700 rounded">
                {{ flash.error }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script>
  import { Head } from '@inertiajs/vue3';
  import { defineAsyncComponent, ref } from 'vue';
  import axios from 'axios';
  
  const AuthenticatedLayout = defineAsyncComponent(() => import('@/Layouts/AuthenticatedLayout.vue'));
  
  export default {
    components: {
      AuthenticatedLayout,
      Head
    },
    setup() {
      const name = ref('');
      const email = ref('');
      const password = ref('');
      const flash = ref({});
  
      const createUser = async () => {
        flash.value = {}; // Reset flash messages
        try {
          const response = await axios.post('/users', {
            name: name.value,
            email: email.value,
            password: password.value
          });
  
          if (response.data.success) {
            flash.value = { success: response.data.success };
            name.value = '';
            email.value = '';
            password.value = '';
          } else if (response.data.error) {
            flash.value = { error: response.data.error };
          }
        } catch (error) {
          if (error.response && error.response.data.errors) {
            flash.value = { error: Object.values(error.response.data.errors).flat().join(', ') };
          } else {
            flash.value = { error: 'There was an error creating the user.' };
          }
        }
      };
  
      return {
        name,
        email,
        password,
        flash,
        createUser
      };
    }
  }
  </script>
  
<style scoped>
@media (max-width: 768px) {
  .py-12 {
    padding-top: 6px;
    padding-bottom: 6px;
  }

  .max-w-7xl {
    max-width: 95%;
  }

  .p-6 {
    padding: 3px;
  }

  h2, h3 {
    font-size: 1.25rem; /* Smaller headings on mobile */
  }

  .text-sm {
    font-size: 0.875rem; /* Adjust text size for smaller screens */
  }

  input[type="text"],
  input[type="email"],
  input[type="password"] {
    padding: 10px; /* Larger padding for easier interaction */
    font-size: 0.875rem; /* Larger font size for readability */
  }

  .bg-blue-500:hover,
  .bg-blue-700:hover,
  .bg-blue-500,
  .bg-blue-700 {
    padding: 12px 24px; /* Larger buttons for easier interaction */
    font-size: 0.875rem; /* Adjust button text size */
  }
}

/* General styling for larger screens can remain the same or be adjusted as needed */
</style>
  