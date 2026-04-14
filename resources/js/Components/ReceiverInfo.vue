<template>
    <div class="receiver-info">
      <p v-if="formData.name"><strong class="highlight">Tên:</strong> <span class="highlight">{{ formData.name }}</span></p>
      <p v-if="formData.phone"><strong class="highlight">SĐT:</strong> <span class="highlight">{{ formData.phone }}</span></p>
      <p v-if="formData.address"><strong class="highlight">Địa chỉ:</strong> <span class="highlight">{{ formData.address }}</span></p>
      <p v-if="formData.company"><strong>ĐVVC:</strong> {{ formData.company }}</p>
      <p v-if="formData.cod"><strong>COD:</strong> {{ formData.cod }}</p>
      <p v-if="formData.item"><strong>Tên hàng hóa:</strong> {{ formData.item }}</p>
      <p v-if="formData.payer"><strong>Người trả phí:</strong> {{ formData.payer }}</p>
      <button @click="$emit('printContent')" class="print-button">In</button>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      formData: { type: Object, required: true },
    },
  };
  </script>
  
  <style scoped>
  .receiver-info {
    margin-bottom: 20px;
  }
  
  .receiver-info p {
    margin: 3px 0;
  }
  
  .highlight {
    font-size: 16px;
    font-weight: bold;
  }
  
  .print-button {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
  }
  
  .print-button:hover {
    background-color: #45a049;
  }
  </style>