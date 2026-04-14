<template>
    <p v-if="message" class="success-message">{{ message }}</p>
  </template>
  
  <script>
  export default {
    props: {
      message: { type: String, required: false },
    },
  };
  </script>
  
  <style scoped> 
  .success-message {
    color: green;
    font-weight: bold;
    margin-top: 10px;
  }
  </style>