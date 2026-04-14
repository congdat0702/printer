<template>
  <form @submit.prevent="handleSubmit" class="form-container">
    <fieldset>
      <legend>Thông tin Người Nhận</legend>
      <div class="field">
        <label for="name">Tên:</label>
        <input type="text" id="name" v-model="form.name">
      </div>
      <div class="field">
        <label for="phone">Số điện thoại:</label>
        <input type="text" id="phone" v-model="form.phone">
      </div>
      <div class="field">
        <label for="address">Địa chỉ:</label>
        <input type="text" id="address" v-model="form.address">
      </div>
      <div class="field">
        <label for="company">Đơn vị vận chuyển:</label>
        <input type="text" id="company" v-model="form.company">
      </div>
      <div class="field">
        <label for="cod">COD:</label>
        <input type="text" id="cod" v-model="form.cod">
      </div>
      <div class="field field-row">
        <div class="field-item" style="flex:2; min-width: 120px;">
          <label for="item">Tên hàng hóa:</label>
          <input type="text" id="item" v-model="form.item">
        </div>
        <div class="field-item" style="flex:1; min-width: 80px; margin-left: 12px;">
          <label for="quantity">Số lượng bill:</label>
          <input type="number" id="quantity" v-model="form.quantity" min="1" style="width: 70px; display:inline-block;" />
        </div>
      </div>
      <div class="field">
        <label>Người trả phí:</label>
        <input type="radio" id="sender" value="Người gửi trả" v-model="form.payer"> Người gửi trả
        <input type="radio" id="receiver" value="Người nhận trả" v-model="form.payer"> Người nhận trả
      </div>
      <button type="submit">Submit</button>
    </fieldset>
    <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
    <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
  </form>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ShipmentForm',
  props: {
    form: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      errorMessage: '',
      successMessage: ''
    };
  },
  methods: {
    handleSubmit() {
      axios.post('/recipients', this.form)
        .then(response => {
          this.successMessage = 'Người nhận đã được lưu thành công!';
          this.errorMessage = '';
          this.$emit('submitted');
          setTimeout(() => {
            this.successMessage = '';
          }, 3000); // Hide the success message after 3 seconds
        })
        .catch(error => {
          this.errorMessage = error.response.data.message || 'Có lỗi xảy ra khi lưu người nhận.';
          this.successMessage = '';
        });
    }
  }
}
</script>

<style scoped>
.form-container {
  max-width: 500px;
  margin: auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 5px;
  background-color: #f9f9f9;
}

fieldset {
  border: 1px solid #ddd;
  padding: 10px;
}

legend {
  padding: 0 10px;
  font-weight: bold;
}

.field {
  margin-bottom: 15px;
}

label {
  display: block;
  margin-bottom: 5px;
}

input[type="text"],
input[type="radio"] {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
}

input[type="radio"] {
  width: auto;
}

button {
  width: 100%;
  padding: 10px;
  background-color: #4CAF50;
  color: white;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: #45a049;
}

.error-message {
  color: red;
  font-weight: bold;
}

.success-message {
  color: green;
  font-weight: bold;
}

.field-row {
  display: flex;
  align-items: flex-end;
  gap: 0;
}
.field-item label {
  margin-bottom: 5px;
  display: block;
}
@media (max-width: 600px) {
  .field-row {
    flex-direction: column;
    gap: 0;
  }
  .field-item {
    margin-left: 0 !important;
    width: 100%;
  }
}
</style>
