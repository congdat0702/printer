<template>
    <div class="contact-list">
      <h3>Danh bạ</h3>
      <input 
        type="text" 
        :value="searchQuery" 
        @input="$emit('filterContacts', $event.target.value)" 
        placeholder="Tìm kiếm liên hệ..." 
      />
      <ul>
        <li 
          v-for="contact in filteredContacts" 
          :key="contact.id" 
          @click="$emit('contactSelected', contact)"
        >
          {{ contact.name }} - {{ contact.phone }}
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      contacts: { type: Array, required: true },
      searchQuery: { type: String, required: true },
      filteredContacts: { type: Array, required: true },
    },
  };
  </script>
  
  <style scoped>
  .contact-list {
    flex: 3 1 30%;
    padding: 20px;
  }
  
  .contact-list h3 {
    font-size: 18px;
    margin-bottom: 10px;
  }
  
  .contact-list ul {
    list-style: none;
    padding: 0;
  }
  
  .contact-list li {
    cursor: pointer;
    padding: 8px;
    border-bottom: 1px solid #ddd;
  }
  
  .contact-list li:hover {
    background-color: #f0f0f0;
  }
  
  .contact-list input {
    margin-bottom: 10px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
    font-size: 14px;
  }
  </style>